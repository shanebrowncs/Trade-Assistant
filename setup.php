<html>

<head>
    <title>Trade Assistant Setup</title>
    <link rel="stylesheet" type="text/css" href="layout.css"/>
    <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
	<meta content="utf-8" http-equiv="encoding">
    <style>
    h1{
        margin-bottom: 10px;
    }
    span{
        color: white;
    }
    p{
        margin-bottom: 10px;
    }
    </style>
</head>

<body>
    <h1>Trade Assistant Setup</h1>
    <p>Welcome to the trade assistant setup. This setup will generate a database for integration with the trade assist making the user's experience much faster. Start by entering the user and password you want the Trade Assistant to use to setup the database. It is highly recommended that you do not run this setup on a live server, the tool will not attempt to store database info without first denying access to users, but tools like this can fail and we suggest you pay close attention to what you are and are not hiding from the outside world. Please pay very close attention to any output from this script.</p>
    <form action="setup.php" method='POST'>
        <span>MySQL Host ( If you don't know what this is it is likely <b>localhost</b> ):</span><br/>
        <input name="host" type="text"/><br/>
        <span>MySQL User:</span><br/>
        <input name="user" type="text"/>
        <br/><span>MySQL Password:</span><br/>
        <input name="pass" type="password"/><br/>
        <span>Re-enter Password:</span><br/>
        <input name="pass_confirm" type="password"/><br/>
        <input type="submit" value="Submit"/>
    </form>
</body>

<?php

    function connectToSQL($host, $user, $password){
        $sqlConn = new mysqli($host, $user, $password);

        if($sqlConn->connect_errno){
            echo "<p>Error connecting to MySQL: " . $sqlConn->connect_errno . " : " . $sqlConn->connect_error . "</p>";
            return false;
        }
        return $sqlConn;
    }

    function populateSQL($sqlConn, $db){
        //$dbQuery = "CREATE DATABASE ? CHARACTER SET utf8 COLLATE utf8_general_ci";
        $dbQuery = "CREATE DATABASE " . $db;
        if(!$result = $sqlConn->query($dbQuery)){
            echo '<p>MySQL Error: ' . mysqli_error($sqlConn) . '</p>';
            return false;
        }

        echo '<p>Database Created.</p>';

        // Select DB before creating table
        $sqlConn->select_db($db);

        $tableQuery = "CREATE TABLE `items` ( `name` VARCHAR(50) NOT NULL , `current` DECIMAL(5, 2) NOT NULL , `median` DECIMAL(5, 2) NOT NULL , `market` DECIMAL(5, 2) NOT NULL , `volume` INT(11) NOT NULL )";

        if(!$sqlConn->query($tableQuery)){
            echo '<p>MySQL Error: ' . mysqli_error($sqlConn) . '</p>';
            return false;
        }

        echo '<p>Table Created.</p>';
        return true;
    }

    // User hasn't entered values yet
    if(!isset($_POST['user'])){
        return;
    }

    if(empty($_POST['pass']) || empty($_POST['pass_confirm']) || empty($_POST['host'])){
        echo "<p>Field/s Empty. Please ensure to fill out the whole form.</p>";
        return;
    }

    $host = $_POST['host'];
    $user = $_POST['user'];
    $password = $_POST['pass'];
    $password_confirm = $_POST['pass_confirm'];

    if(strcmp($password, $password_confirm) !== 0){
        echo "<p>Passwords do not match.</p>";
        return;
    }

    $sqlConn = connectToSQL($host, $user, $password);
    if($sqlConn == FALSE){
        return;
    }


    // Hardcoded for now :/, can easily edit settings.ini
    $db = "tradeassist";

    if(!populateSQL($sqlConn, $db)){
        return;
    }

    if(!file_exists("server/.htaccess")){
        if(!file_put_contents("server/.htaccess", "Deny from all")){
            echo "<p>ERROR: Couldn't write to server/.htaccess. Ensure non-owners have write permissions for this directory.</p>";
            return;
        }

    }else{
        if(strcmp(file_get_contents("server/.htaccess"), "Deny from all") != 0){
            if(!file_put_contents("server/.htaccess", "Deny from all", FILE_APPEND)){
                echo "<p>ERROR: Could not write to existing file server/.htaccess. Ensure non-owners have write permissions for this directory.</p>";
                return;
            }
        }

    }

    if(file_put_contents("server/settings.ini", "[database]\nhost=" . $host . "\ndb=" . $db . "\nuser=" . $user . "\npass=" . $password)){
        echo "<p>Written server/settings.ini file with database info. <b>Ensure to remove this file from public version control.</b></p>";
    }else{
        echo "<p>ERROR: Could not write to server/settings.ini. Ensure non-owners have write permissions for this directory.</p>";
        return;
    }

    echo "<p>Trade Assistant Successfully Setup.</p>";
    unlink(__FILE__);

?>

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
    <p>Welcome to the trade assistant setup. The trade assistant will run as-is without the need to run this setup. The benefit of the setup is database integration of the trade assist making the user's experience much faster. Start by entering the user and password you want the Trade Assistant to use to setup the database. You can optionally specify a name for the database. Otherwise the default name 'tradeassist' will be used. <b>WARNING: DO NOT SETUP ON LIVE SERVER! It is possible for outside users to view DB passwords while the setup is <i>IN PROGRESS</i>.</b>Please pay very close attention to any output from this script.</p>
    <form action="setup.php" method='POST'>
        <span>MySQL Host( If you don't know what this is it is likely <b>localhost</b> ):</span><br/>
        <input name="host" type="text"/><br/>
        <span>MySQL User:</span><br/>
        <input name="user" type="text"/>
        <br/><span>MySQL Password:</span><br/>
        <input name="pass" type="password"/><br/>
        <span>Re-enter Password:</span><br/>
        <input name="pass_confirm" type="password"/><br/>
        <span>Database Name(Optional):</span><br/>
        <input name="db" type="text"/><br/><br/>
        <input type="submit" value="Submit"/>
    </form>
</body>

<?php

    function connectToSQL($host, $user, $password){
        $sqlConn = mysqli_connect($host, $user, $password);

        if(mysqli_connect_error()){
            echo "<p>Error connecting to MySQL: " . mysqli_connect_errno() . " : " . mysqli_connect_error() . "</p>";
            return false;
        }
        return $sqlConn;
    }

    function populateSQL($sqlConn, $db){
        $dbQuery = "CREATE DATABASE " . $db . " CHARACTER SET utf8 COLLATE utf8_general_ci";
        if(!mysqli_query($sqlConn, $dbQuery)){
            echo '<p>MySQL Error: ' . mysqli_error($sqlConn) . '</p>';
            return false;
        }

        echo '<p>Database Created.</p>';

        // Select DB before creating table
        mysqli_select_db($sqlConn, $db);

        $tableQuery = "CREATE TABLE `" . $db . "`.`items` ( `name` VARCHAR(50) NOT NULL , `current` DECIMAL(5, 2) NOT NULL , `median` DECIMAL(5, 2) NOT NULL , `market` DECIMAL(5, 2) NOT NULL , `volume` INT(11) NOT NULL )";

        if(!mysqli_query($sqlConn, $tableQuery)){
            echo 'MySQL Error: ' . mysqli_error($sqlConn);
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

    if(!empty($_POST['db'])){
        $db = $_POST['db'];
    }else{
        $db = "tradeassist";
    }

    if(strcmp($password, $password_confirm) !== 0){
        echo "<p>Passwords do not match.</p>";
        return;
    }

    $sqlConn = connectToSQL($host, $user, $password);
    if($sqlConn == FALSE){
        return;
    }

    if(!populateSQL($sqlConn, $db)){
        return;
    }

    if(!file_put_contents(".htaccess", "<files settings.ini>\norder allow,deny\ndeny from all\n</files>")){
        echo "<p>Could not write to .htaccess. Ensure non-owners have write permissions to the current directory.</p>";
        return;
    }
    if(file_exists(".htaccess")){
        echo "<p>You already have an htaccess file. We have written permissions to it for the file 'settings.ini' which stores database info. Verify the .htaccess in this directory is set to deny outside access to this file.</p>";
    }else{
        echo "<p>Written Permissions to .htaccess</p>";
    }

    if(file_put_contents("settings.ini", "[database]\nhost=" . $host . "\ndb=" . $db . "\nuser=" . $user . "\npass=" . $password)){
        echo "<p>Written settings.ini file with database info. Ensure to remove this file from public version control.</p>";
    }else{
        echo "<p>Could not write to settings.ini. Ensure non-owners have write permissions to the current directory.</p>";
        return;
    }


    echo "<p>Success! You can now close this page and delete this file.</p>";



?>

<html>

<head>
    <title>Trade Assistant Setup</title>
    <link rel="stylesheet" type="text/css" href="layout.css"/>
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
    <p>Welcome to the trade assistant setup. The trade assistant will run as-is without the need to run this setup. The benefit of the setup is database integration of the trade assist making the user's experience much faster. Start by entering the user and password you want the Trade Assistant to use to setup the database. You can optionally specify a name for the database. Otherwise the default name 'tradeassist' will be used.</p>
    <form action="setup.php" method="POST">
        <span>MySQL User:</span><br/>
        <input name="user" type="text"/>
        <br/><span>MySQL Password:</span><br/>
        <input name="pass" type="password"/><br/>
        <br/><span>Re-enter Password:</span><br/>
        <input name="pass_confirm" type="password"/><br/>
        <span>Database Name(Optional):</span><br/>
        <input name="db" type="text"/><br/><br/>
        <input type="submit" value="Submit"/>
    </form>
</body>

<?php
    function checkForMissedFields(){
        missedFields = "";
        if(!isset($_POST['user'])){
            missedFields += "User\n";
        }
        if(!isset($_POST['pass'])){
            missedFields += "Password\n";
        }
        if(!isset($_POST['pass_confirm'])){
            missedFields += "Password Confirmation\n";
        }

        // Return
        if(strlen(missedFields) > 0){
            return missedFields;
        }

        return TRUE;
    }

    missedFields = checkForMissedFields();

    if(missedFields != TRUE){
        echo "Fields Missed:\n" . missedFields;
    }

    user = $_POST['user'];
    password = $_POST['pass'];
    password_confirm = $_POST['pass_confirm'];

    if(isset($_POST['db'])){
        db = $_POST['db'];
    }else{
        
    }

?>

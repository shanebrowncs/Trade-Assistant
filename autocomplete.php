<?php

function handleSearch(){
    $search = $_GET['search'];

    // Get SQL Info
    if(($settings = AssistantUtility::readSettingsFile("settings.ini")) == FALSE){
        die("");
    }

    // Connect to MySQL
    if(($sqlConn = @mysqli_connect($settings->host, $settings->user, $settings->pass, $settings->db)) == FALSE){
        die("");
    }

    // Protect against SQL injection
    $search = mysqli_real_escape_string($sqlConn, $search);
    $search = preg_split('/\s+/', $search);

    // Build SQL Query from search items
    $sqlQuery = "SELECT `name` FROM `items` WHERE `name` LIKE '%";
    for($i = 0; $i < count($search); $i++){
        $sqlQuery .= $search[$i] . "%";
    }
    $sqlQuery .= "' limit 10";

    // Query database
    $result = mysqli_query($sqlConn, $sqlQuery);
    if($result === FALSE){
        mysqli_close($sqlConn);
        die("");
    }

    // Get array of items from query result
    $data = "";
    while($row = mysqli_fetch_assoc($result)){
        $data .= $row['name'] . "<br />";
    }

    // Remove line ending from the end of the string
    $data = substr($data, 0, strlen($data) - 1);

    // Close SQL and return data
    mysqli_close($sqlConn);
    return $data;
}

function handleItemFetch(){
    if(!isset($_GET['cur']) || empty($_GET['cur'])){
        die("");
    }

    $currency = $_GET['cur'];
    $item = $_GET['getitem'];
    // Get SQL Info
    if(($settings = AssistantUtility::readSettingsFile("settings.ini")) == FALSE){
        die("");
    }

    // Connect to MySQL
    if(($sqlConn = @mysqli_connect($settings->host, $settings->user, $settings->pass, $settings->db)) == FALSE){
        die("");
    }

    // Protect against SQL injection
    $item = mysqli_real_escape_string($sqlConn, $item);

    $sqlQuery = "SELECT * FROM `items` WHERE `name`='" . $item . "'";
    $result = mysqli_query($sqlConn, $sqlQuery);
    if($result === FALSE){
        mysqli_close($sqlConn);
        die("");
    }

    $itemArray = array();
    while($row = mysqli_fetch_array($result)){
        $itemArray[] = $row;
    }
    $itemArray = $itemArray[0];

    $conversion = AssistantUtility::getCurrencyConversion("USD", $currency);

    for ($i=1; $i < 4; $i++) {
        $itemArray[$i] *= $conversion;
        $itemArray[$i] = round($itemArray[$i], 2);
    }

    mysqli_close($sqlConn);
    return json_encode($itemArray);
}

// Required to get SQL info
require 'server/AssistantUtility.php';

// Ensure search field isn't empty or missing
if((!isset($_GET['getitem']) || empty($_GET['getitem'])) && (!isset($_GET['search']) || empty($_GET['search']))){
    die("");
}

if(isset($_GET['search'])){
    echo handleSearch();
}elseif(isset($_GET['getitem'])){
    echo handleItemFetch();
}

?>

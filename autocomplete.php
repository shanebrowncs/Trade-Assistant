<?php

// Required to get SQL info
require 'server/AssistantUtility.php';

// Ensure search field isn't empty or missing
if(!isset($_GET['search']) || empty($_GET['search'])){
    die("");
}

// Declare and split searched items
$search = $_GET['search'];
$search = preg_split('/\s+/', $search);

// Get SQL Info
if(($settings = AssistantUtility::readSettingsFile("settings.ini")) == FALSE){
    die("");
}

// Connect to MySQL
if(($sqlConn = @mysqli_connect($settings->host, $settings->user, $settings->pass, $settings->db)) == FALSE){
    die("");
}

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
echo $data;

?>

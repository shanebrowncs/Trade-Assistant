<?php

function handleSearch(){
    $search = $_GET['search'];

    // Get SQL Info
    if(($settings = AssistantUtility::readSettingsFile("settings.ini")) == FALSE){
        die("");
    }

    // Connect to MySQL
    if(($sqlConn = new mysqli($settings->host, $settings->user, $settings->pass, $settings->db)) == FALSE){
        die("");
    }

    // Add wildcards to search terms
    $search = "%" . str_replace(" ", "%", $search) . "%";

    // Build SQL Query from search items
    $bindParamString = "";
    $sqlQuery = "SELECT name FROM items WHERE name LIKE ? LIMIT 10";

    // Prepared Statement
    if($stmt = $sqlConn->prepare($sqlQuery)){
        $stmt->bind_param("s", $search);
        if($stmt->execute()){
            $result = $stmt->get_result();

            // Get array of items from query result
            $data = "";
            while($row = $result->fetch_assoc()){
                $data .= $row['name'] . "<br />";
            }
            // Remove line ending from the end of the string
            $data = substr($data, 0, strlen($data) - 1);

            $stmt->close();
            $sqlConn->close();

            return $data;
        }
        $stmt->close();
    }
    $sqlConn->close();
}

function handleItemFetch(){
    if(!isset($_GET['cur']) || empty($_GET['cur'])){
        die("");
    }

    $currency = $_GET['cur'];
    $item = $_GET['getitem'];

    $itemResult = AssistantUtility::fetchSqlData($item, $host, $db, $user, $pass, $currencyConversion);

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

<?php
$file = file_get_contents("http://steamcommunity.com/market/priceoverview/?country=US&currency=5&appid=730&market_hash_name=Chroma%202%20Case");

$fileJSON = json_decode($file);

echo "Encoded: " . utf8_encode($fileJSON->lowest_price);
?>

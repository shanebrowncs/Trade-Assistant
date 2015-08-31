<!DOCTYPE HTML>
<html>

<head>
	<title>Title</title>
</head>

<body>

</body>

</html>

<?php

function get_url_contents($url){
        $crl = curl_init();
        $timeout = 5;
        curl_setopt ($crl, CURLOPT_URL,$url);
        curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
        $ret = curl_exec($crl);
        curl_close($crl);
        return $ret;
}

function readMarketPage($pageNum){
	echo 'Page Query: http://steamcommunity.com/market/search/render/?query=&start=' . $pageNum * 100 . '&count=100&search_descriptions=0&sort_column=name&sort_dir=asc&appid=730';

	$json = json_decode(get_url_contents("http://steamcommunity.com/market/search/render/?query=&start=" . $pageNum * 100 . "&count=100&search_descriptions=0&sort_column=name&sort_dir=asc&appid=730"), true);

	while($json["success"] != true){
		echo 'no success';
		$json = json_decode(get_url_contents("http://steamcommunity.com/market/search/render/?query=&start=" . $pageNum * 100 . "&count=100&search_descriptions=0&sort_column=name&sort_dir=asc&appid=730"), true);
	}

	$dom = new DOMDocument;
	$dom->loadHTML($json["results_html"]);

	$dom->preserveWhiteSpace = false;

	//echo $dom->saveHTML();

	$itemResult = $dom->getElementById('result_0_name');

	$itemArray = array();

	if($itemResult === NULL){
		echo "Couldn't get first element, num = " . $pageNum . "itemResult: " . $itemResult;
		return false;
	}

	$secCount = 1;
	while(true){
		$itemResult = $dom->getElementById('result_' . $secCount . '_name');
		if($itemResult != false){
			$itemArray[] = utf8_decode($itemResult->nodeValue);
			echo $itemArray[count($itemArray) - 1] . "<br />";
			echo '<script>window.scrollTo(0,document.body.scrollHeight);</script>';
			$secCount++;
		}else{
			break;
		}
	}

	return $itemArray;
}

function addItemToDatabase($item, $host, $db, $user, $pass){
	$sqlConn = mysqli_connect($host, $user, $pass);
	if(mysqli_connect_errno()){
		echo mysqli_connect_error();
	}

	mysqli_select_db($sqlConn, $db);

	if(isset($item->name)){
		$compQuery = "SELECT `name` FROM `items` WHERE `name`='" . str_replace("'", "\'", $item->name) . "'";
		$compResult = mysqli_query($sqlConn, $compQuery);
		if($compResult === FALSE)
			$rows = 1;
		else
			$rows = $compResult->fetch_assoc();

		if(count($rows) > 0){
			echo 'Updating ' . $item->name . "<br />";
			echo '<script>window.scrollTo(0,document.body.scrollHeight);</script>';

			$updateQuery = "UPDATE `items` SET `current`='" . $item->curPrice . "', `median`='" . $item->medPrice . "', `market`='" . $item->taxPrice . "', `volume`='" . $item->volume . "' WHERE `name`='" . str_replace("'", "\'", $item->name) . "'";
			$updateResult = mysqli_query($sqlConn, $updateQuery);
			if($updateResult === FALSE){
				echo "<br />" . mysqli_error($sqlConn) . "<br />";
			}

		}else{
			echo 'Inserting ' . $item->name . "<br />";
			echo '<script>window.scrollTo(0,document.body.scrollHeight);</script>';
			//$setQuery = "INSERT INTO `items` (`name`, `name`) VALUES ('null', '" . $array[$i]->name . "')";
			$setQuery = "INSERT INTO `items`(`name`, `current`, `median`, `market`, `volume`) VALUES ('" . str_replace("'", "\'", $item->name) . "','" . $item->curPrice . "','" . $item->medPrice . "','" . $item->taxPrice . "','" . $item->volume . "')";
			$setResult = mysqli_query($sqlConn, $setQuery);
		}
	}

	mysqli_close($sqlConn);
}

function grabItemValue($item){
	$obj = new stdClass();
	$obj->name = $item;
	echo 'Grabbing Data for ' . $obj->name . "<br />";
	echo '<script>window.scrollTo(0,document.body.scrollHeight);</script>';
	if(($itemJSON = TradeTranslator::getItemJSON($obj->name)) != FALSE){
		$obj->curPrice = TradeTranslator::getItemCurrentPrice($itemJSON);
		$obj->medPrice = TradeTranslator::getItemMedianPrice($itemJSON);
		$obj->taxPrice = $obj->curPrice - ($obj->curPrice * 0.15);
		$obj->volume = TradeTranslator::getItemVolume($itemJSON);
	}else{
		$obj->curPrice = 0.0;
		$obj->medPrice = 0.0;
		$obj->taxPrice = 0.0;
		$obj->volume = 0;
	}

	return $obj;
}

require 'TradeTranslator.php';
require 'AssistantUtility.php';

ini_set('display_errors',1);
error_reporting(E_ALL);
set_time_limit(0);

$time_pre = microtime(true);

$dataArray = array();

$sqlData = AssistantUtility::readSettingsFile("../settings.ini");
if($sqlData === FALSE){
	$sqlData = new stdClass();

	$sqlData->host = "localhost";
	$sqlData->db = "csgo";
	$sqlData->user = "user";
	$sqlData->pass = "pass";
}

$count = 0;
while(TRUE){
	$temp = readMarketPage($count);

	if($temp === FALSE){
		break;
	}

	for($i = 0; $i < count($temp); $i++){
		addItemToDatabase(grabItemValue($temp[$i]), $sqlData->host, $sqlData->db, $sqlData->user, $sqlData->pass);
	}

	$count++;
}

echo "\nTime: " . $microtime(true) - $time_pre;

?>

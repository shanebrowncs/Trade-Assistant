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
        curl_setopt ($crl, CURLOPT_URL, $url);
        curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
        $ret = curl_exec($crl);
        curl_close($crl);
        return $ret;
}

function readMarketPage($pageNum){
	echo 'Page Query: http://steamcommunity.com/market/search/render/?query=&start=' . $pageNum * 100 . '&count=100&search_descriptions=0&sort_column=name&sort_dir=asc&appid=730';

	//$json = json_decode(get_url_contents("http://steamcommunity.com/market/search/render/?query=&start=" . $pageNum * 100 . "&count=100&search_descriptions=0&sort_column=name&sort_dir=asc&appid=730"), true);

	do{
		echo 'attempting fetch';
		$json = json_decode(get_url_contents("http://steamcommunity.com/market/search/render/?query=&start=" . $pageNum * 100 . "&count=100&search_descriptions=0&sort_column=name&sort_dir=asc&appid=730"), true);
	}while($json["success"] != true);


	$json['results_html'] = str_replace("\\t", "\t", $json['results_html']);
	$json['results_html'] = str_replace("\\r\\n", "\n", $json['results_html']);

	$dom = new DOMDocument;
	$dom->loadHTML($json["results_html"]);

	$dom->preserveWhiteSpace = false;

	file_put_contents("output.html", $dom->saveHTML());

	$itemResult = $dom->getElementById('result_0_name');

	$itemArray = array();

	if($itemResult === NULL){
		echo "Couldn't get first element, num = " . $pageNum . "<br />itemResult: " . $itemResult;
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

function checkItemExistence($item, $sqlConn){
	if($sqlConn->errno){
		echo $sqlConn->error();
	}

	$item->name = str_replace("'", "\'", $item->name);
	$compQuery = "SELECT name FROM items WHERE name=?";
	if($stmt = $sqlConn->prepare($compQuery)){
		$stmt->bind_param('s', $item->name);
		if($stmt->execute()){
			$result = $stmt->get_result();

			if(strcmp($result->fetch_array(MYSQLI_NUM)[0], $item->name) == 0){
				return true;
			}
		}
	}

	return false;
}

function addItemToDatabase($item, $host, $db, $user, $pass){
	$sqlConn = new mysqli($host, $user, $pass, $db);
	if($sqlConn->errno){
		echo $sqlConn->error();
	}

	$update = true;
	if(checkItemExistence($item, $sqlConn)){
		echo 'Updating ' . $item->name . "<br />";
		echo '<script>window.scrollTo(0,document.body.scrollHeight);</script>';
		$setQuery = "UPDATE items SET current=?, median=?, market=?, volume=? WHERE name=?";
		$update = true;

	}else{
		echo 'Inserting ' . $item->name . "<br />";
		echo '<script>window.scrollTo(0,document.body.scrollHeight);</script>';
		$setQuery = "INSERT INTO items(name, current, median, market, volume) VALUES (?, ?, ?, ?, ?)";
		$update = false;
	}

	if($stmt = $sqlConn->prepare($setQuery)){
		if($update){
			$stmt->bind_param('sssss', $item->curPrice, $item->medPrice, $item->taxPrice, $item->volume, $item->name);
		}else{
			$stmt->bind_param('sssss', $item->name, $item->curPrice, $item->medPrice, $item->taxPrice, $item->volume);
		}
		if($stmt->execute()){
			$stmt->close();
			$sqlConn->close();
			return true;
		}
		$stmt->close();
	}

	$sqlConn->close();
	return false;
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

//debug
$longestItem = 0;
while(TRUE){
	$temp = readMarketPage($count);

	if($temp === FALSE){
		break;
	}

	for($i = 0; $i < count($temp); $i++){
		addItemToDatabase(grabItemValue($temp[$i]), $sqlData->host, $sqlData->db, $sqlData->user, $sqlData->pass);
		if(strlen($temp[$i]) > $longestItem){
			$longestItem = strlen($temp[$i]);
		}
	}

	$count++;
}
echo "<br />Longest: " . $longestItem;

?>

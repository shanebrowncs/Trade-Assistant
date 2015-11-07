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
    global $lineBreak;

	echo 'Page Query: http://steamcommunity.com/market/search/render/?query=&start=' . $pageNum * 100 . '&count=100&search_descriptions=0&sort_column=name&sort_dir=asc&appid=730' . $lineBreak;

	//$json = json_decode(get_url_contents("http://steamcommunity.com/market/search/render/?query=&start=" . $pageNum * 100 . "&count=100&search_descriptions=0&sort_column=name&sort_dir=asc&appid=730"), true);

	do{
		echo 'attempting fetch' . $lineBreak;
		$json = json_decode(get_url_contents("http://steamcommunity.com/market/search/render/?query=&start=" . $pageNum * 100 . "&count=100&search_descriptions=0&sort_column=name&sort_dir=asc&appid=730"), true);
	}while($json["success"] != true);


	$json['results_html'] = str_replace("\\t", "\t", $json['results_html']);
	$json['results_html'] = str_replace("\\r\\n", "\n", $json['results_html']);

	$dom = new DOMDocument;
	@$dom->loadHTML($json["results_html"]);

	$dom->preserveWhiteSpace = false;


	$xpath = new DOMXPath($dom);

	$nodes = $xpath->query("//a//div//span[contains(@id, 'result_0_name')]");

	$itemArray = array();

	if($nodes === NULL || $nodes->length <= 0){
		echo "Couldn't get first element, num = " . $pageNum . $lineBreak;
		return false;
	}

	$secCount = 1;
	while(true){
		//$itemResult = $dom->getElementById('result_' . $secCount . '_name');
		$itemResult = $xpath->query("//a//div//span[contains(@id, 'result_" . $secCount . "_name')]");
		if($itemResult != false && count($itemResult) > 0 && isset($itemResult->item(0)->nodeValue)){
			$itemArray[] = utf8_decode($itemResult->item(0)->nodeValue);
			echo "Found " . $itemArray[count($itemArray) - 1] . $lineBreak;
			echo '<script>window.scrollTo(0,document.body.scrollHeight);</script>';
			$secCount++;
		}else{
			break;
		}
	}

	return $itemArray;
}

// TODO: Remove, redundant to AssistantUtility::fetchSqlData()
function checkItemExistence($item, $sqlConn){
	if($sqlConn->errno){
		echo $sqlConn->error();
	}

	$item->name = str_replace("'", "\'", $item->name);
	$compQuery = "SELECT name FROM items WHERE name=?";
	if($stmt = $sqlConn->prepare($compQuery)){
		$stmt->bind_param('s', $item->name);
		if($stmt->execute()){
			//$result = $stmt->get_result();
			$stmt->bind_result($name);

			if($stmt->fetch() === TRUE)
				return true;
		}
	}

	return false;
}

function addItemToDatabase($item, $host, $db, $user, $pass){
    global $lineBreak;

	$sqlConn = new mysqli($host, $user, $pass, $db);
	if($sqlConn->errno){
		echo $sqlConn->error();
	}

	$update = true;
	if(checkItemExistence($item, $sqlConn)){
		echo 'Updating ' . $item->name . $lineBreak;
		echo '<script>window.scrollTo(0,document.body.scrollHeight);</script>';
		$setQuery = "UPDATE items SET current=?, median=?, market=?, volume=? WHERE name=?";
		$update = true;

	}else{
		echo 'Inserting ' . $item->name . $lineBreak;
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
    global $lineBreak;
	$obj = new stdClass();
	$obj->name = $item;
	echo 'Grabbing Data for ' . $obj->name . $lineBreak;;
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

@$fileContent = file_get_contents("page.txt");

if($fileContent != false){
    $count = intval($fileContent) + 1;
    if($count == -1)
        $count = 0;
}



//debug
$lineBreak = "<br/>";
$longestItem = 0;
while(TRUE){
    echo 'Page: ' . $count . $lineBreak;
	$temp = readMarketPage($count);

	if($temp === FALSE){
        file_put_contents("page.txt", "-1");

        $nowDate = new DateTime();

        file_put_contents("datetime.txt", $nowDate->getTimestamp());
		break;
	}

	for($i = 0; $i < count($temp); $i++){
		addItemToDatabase(grabItemValue($temp[$i]), $sqlData->host, $sqlData->db, $sqlData->user, $sqlData->pass);
		if(strlen($temp[$i]) > $longestItem){
			$longestItem = strlen($temp[$i]);
		}
	}

    file_put_contents("page.txt", $count);

	$count++;
}
echo $lineBreak . "Longest: " . $longestItem;

?>

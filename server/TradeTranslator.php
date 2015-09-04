<?php
class TradeTranslator{
	public static function getTrade($url){
		$data = file_get_contents(str_replace("https", "http", $url));

		if($data !== FALSE){
			@$dom = new DomDocument();
			@$dom->loadHTML($data);

			$finder = new DomXPath($dom);

			$leftNode = $finder->query("//form[@class='left']//div[@class='oitm']//div[@class='name']//b");
			$rightNode = $finder->query("//form[@class='right']//div[@class='oitm']//div[@class='name']//b");

			$items = array();

			foreach($leftNode as $node){
				$items[0][] = utf8_decode($node->nodeValue);
			}

			foreach($rightNode as $node){
				$items[1][] = utf8_decode($node->nodeValue);
			}

		}else{
			return false;
		}

		return $items;
	}

	public static function getItemJSON($item){
		$item = str_replace(" ", "%20", $item);
		@$raw = file_get_contents("http://steamcommunity.com/market/priceoverview/?country=US&currency=1&appid=730&market_hash_name=" . $item);
		if($raw === FALSE){
			return FALSE;
		}

		return $raw;
	}

	public static function getItemCurrentPrice($raw){
		$json = json_decode($raw);
		if($json === NULL){
			return 0.0;
		}
		if(!isset($json->lowest_price)){
			return 0.0;
		}
		return floatval(substr($json->lowest_price, 1));
	}

	public static function getItemMedianPrice($raw){
		$json = json_decode($raw);
		if($json === NULL){
			return 0.0;
		}else if(!isset($json->median_price)){
			return 0.0;
		}

		return floatval(substr($json->median_price, 1));
	}

	public static function getItemVolume($raw){
		$json = json_decode($raw);
		if($json === NULL){
			return 0;
		}else if(!isset($json->volume)){
			return 0;
		}

		return floatval(str_replace(",", "", $json->volume));
	}

}
?>

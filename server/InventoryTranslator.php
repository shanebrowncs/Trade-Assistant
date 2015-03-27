<?php

function getInventory($url){
	@$data = file_get_contents($url . "?xml=1");
    if($data !== FALSE){
    	$xml = simplexml_load_string($data);
    	if($xml !== FALSE){
    		$steamID64 = $xml->steamID64;
    		return retrieveInventory($steamID64);
    	}else{
    		return false;
    	}
    }else{
        return false;
    }
}

function getSteamName($url){
    $data = file_get_contents($url . "?xml=1");
    $xml = simplexml_load_string($data);
    if($xml !== FALSE){
        return $xml->steamID;
    }else{
        return false;
    }
}

function retrieveInventory($steamID64){
	$data = file_get_contents("http://steamcommunity.com/profiles/" . $steamID64 . "/inventory/json/730/2");
	$itemArray = array();
    if($data !== FALSE){
		$json = json_decode($data, TRUE);
		if($json !== FALSE){
			$subNodes = $json['rgDescriptions'];
            foreach($subNodes as $node){
                if(substr($node['market_hash_name'], 0, 5) !== "Offer"){
                    $itemArray[] = new stdClass();
                    $itemArray[count($itemArray) - 1]->name = $node['market_hash_name'];
                    $itemArray[count($itemArray) - 1]->count = substr_count($data, $node['classid']) - 2;
                }
            }
            return $itemArray;
		}else{
			return false;
		}
		
	}else{
        return false;
    }
}
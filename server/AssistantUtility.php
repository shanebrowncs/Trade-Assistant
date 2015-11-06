<?php

class AssistantUtility{
    public static function readSettingsFile($filePath){
    	if((@$settings = parse_ini_file($filePath, TRUE)) === FALSE) {
    		$data = FALSE;
    	}else{
    		$data = new stdClass();

    		$data->host = $settings['database']['host'];
    		$data->db = $settings['database']['db'];
    		$data->user = $settings['database']['user'];
    		$data->pass = $settings['database']['pass'];
    	}

    	return $data;
    }

    public static function fetchSqlData($item, $host, $db, $user, $pass, $currencyConversion){
    	$sqlConn = new mysqli($host, $user, $pass, $db);

    	if($sqlConn !== FALSE){
            if($stmt = $sqlConn->prepare('SELECT * FROM `items` WHERE `name`=?')){
                $stmt->bind_param('s', $item);
                if($stmt->execute()){
                    $stmt->bind_result($name, $curPrice, $medPrice, $taxPrice, $volume);

                    $stmt->fetch();

                    if(!empty($name)){
                        $obj = new stdClass();
            			$obj->curPrice = floatval($curPrice) * $currencyConversion;
            			$obj->medPrice = floatval($medPrice) * $currencyConversion;
            			$obj->taxPrice = floatval($taxPrice) * $currencyConversion;
            			$obj->volume = intval($volume);


                        $stmt->close();
                        $sqlConn->close();
                		return $obj;
                    }
                }
                $stmt->close();
            }
            $sqlConn->close();
    	}

        return FALSE;
    }

    public static function getCurrencyConversion($fromCurrency, $toCurrency){
        $url = 'http://www.webservicex.net/CurrencyConvertor.asmx/ConversionRate?FromCurrency=' . $fromCurrency . '&ToCurrency=' . $toCurrency;
        $xml = simpleXML_load_file($url, "SimpleXMLElement", LIBXML_NOCDATA);
        if($xml !=  FALSE){
            return floatval($xml);
        }else {
            return 0.0;
        }
    }

    public static function getUserCurrency(){
        if(isset($_COOKIE['currency']))
            return $_COOKIE['currency'];
        else
            return FALSE;
    }

    public static function getManualFetch(){
        if(isset($_COOKIE['manualprice'])){
            if($_COOKIE['manualprice'] == "true")
                return TRUE;
            else
                return FALSE;
        }
        else
            return FALSE;
    }
}

/*
USD = 1
GBP = 2
EUR = 3
<NIL> = 4
RUB = 5
<NIL> = 6
BRL = 7
JPY = 8
NOK = 9
IDR = 10
MYR = 11
PHP = 12
SGD = 13
THB = 14
<NIL> = 15
<NIL> = 16
TRY = 17
<NIL> = 18
MXN = 19
CAD = 20
<NIL> = 21
NZD = 22*/

?>

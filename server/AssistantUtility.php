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
    	$sqlConn = @mysqli_connect($host, $user, $pass);

    	if($sqlConn !== FALSE){
    		mysqli_select_db($sqlConn, $db);

            $stmt = mysqli_stmt_init($sqlConn);
            if(mysqli_stmt_prepare($stmt, 'SELECT * FROM `items` WHERE `name`=?')){
                mysqli_stmt_bind_param($stmt, 's', $item);
                if(mysqli_stmt_execute($stmt)){
                    mysqli_stmt_bind_result($stmt, $name, $current, $median, $market, $volume);
                    mysqli_stmt_fetch($stmt);

                    $obj = new stdClass();
                    while(mysqli_stmt_fetch($stmt)){
                        echo $current;
            			$obj->curPrice = floatval($current) * $currencyConversion;
            			$obj->medPrice = floatval($current) * $currencyConversion;
            			$obj->taxPrice = floatval($current) * $currencyConversion;
            			$obj->volume = intval($volume);
            		}

            		return $obj;
                }
                mysqli_stmt_close($stmt);
            }
            mysqli_close($sqlConn);
    	}else{
    		return FALSE;
    	}
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

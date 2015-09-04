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

    public static function getCurrencyConversion($fromCurrency, $toCurrency){
        $url = 'http://www.webservicex.net/CurrencyConvertor.asmx/ConversionRate?FromCurrency=' . $fromCurrency . '&ToCurrency=' . $toCurrency;
        $xml = simpleXML_load_file($url, "SimpleXMLElement", LIBXML_NOCDATA);
        if($xml !=  FALSE){
            return $xml;
        }else {
            return 0.0;
        }
    }

    public static function getUserCurrency(){
        $currency = new stdClass();
        if(isset($_COOKIE['currency'])){
            $currency = $_COOKIE['currency'];
            return $currency;
        }else{
            return FALSE;
        }
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

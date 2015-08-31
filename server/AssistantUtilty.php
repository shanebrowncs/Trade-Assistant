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
}

?>

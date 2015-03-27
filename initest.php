<?php
$data = "";

if((@$settings = parse_ini_file("settings.ini", TRUE)) === FALSE) {
	echo 'Could not read from file.';
}else{
	$data .= "Host: " . $settings['database']['host'] . "<br />";
	$data .= "Database: " . $settings['database']['db'] . "<br />";
	$data .= "Username: " . $settings['database']['user'] . "<br />";
	$data .= "Password: " . $settings['database']['pass'];
}

echo 'Data: <br />' . $data;
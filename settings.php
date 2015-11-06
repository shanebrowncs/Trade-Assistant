<?php

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

if(isset($_POST['currency']) && !empty($_POST['currency'])){
	setcookie("currency", $_POST['currency'], time() + (525949 * 60), './', FALSE); // NULL REQUIRED FOR LOCALHOST

	if(isset($_POST['manualprice'])){
		setcookie("manualprice", "true", time() + (525949 * 60), './', FALSE);
	}else{
		setcookie("manualprice", "false", time() + (525949 * 60), './', FALSE);
	}


}
?>

<!DOCTYPE HTML>
<html>

<head>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="index.js"></script>
	<link rel="stylesheet" type="text/css" href="layout.css" />
	<link rel="stylesheet" type="text/css" href="settings.css" />
	<link rel="icon" type="image/png" href="/images/favicon.png" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
	<meta content="utf-8" http-equiv="encoding">

	<title>Settings - Trade Assistant</title>
</head>

<body>


	<div id="navbar">
		<a href="help.html">How To / Info</a>
		<a href="settings.php">Settings</a>
		<a href="calculator.html">Item Calculator</a>
		<a href="index.php">Home</a>
	</div>

	<a id="header" href="index.php"><h1>| TRADE ASSISTANT |</h1></a>
	<img id="header_image" src="images/csgo_logo.png"/>
	<!-- This Credit is a little obnoxious I know, feel free to remove it.
		I would appreciate if you could credit me somewhere but it's not required as long as you do not claim my work is your own. -->
	<p id="credit">Created by <a href="http://shane-brown.ca/" target="_blank">Shane "SajeOne" Brown</a><br /></p>

	<h2>Settings</h2>
	<p>This page includes some settings to help personalize your experience on the Trade Assistant. <b>By clicking "Update Settings" you agree to us creating and using a cookie which contains your personal settings.</b></p>

	<form id="settingsform" action="" method="POST">
		<label for="currency">Currency: </label><br />
		<select name="currency" form="settingsform">
			<option value="USD" <?php if(isset($_COOKIE['currency']) && $_COOKIE['currency'] == "USD") echo "selected"; ?>>USD</option>
			<option value="CAD" <?php if(isset($_COOKIE['currency']) && $_COOKIE['currency'] == "CAD") echo "selected"; ?>>CAD</option>
			<option value="GBP" <?php if(isset($_COOKIE['currency']) && $_COOKIE['currency'] == "GBP") echo "selected"; ?>>GBP</option>
			<option value="EUR" <?php if(isset($_COOKIE['currency']) && $_COOKIE['currency'] == "EUR") echo "selected"; ?>>EUR</option>
			<option value="RUB" <?php if(isset($_COOKIE['currency']) && $_COOKIE['currency'] == "RUB") echo "selected"; ?>>RUB</option>
			<option value="JPY" <?php if(isset($_COOKIE['currency']) && $_COOKIE['currency'] == "JPY") echo "selected"; ?>>JPY</option>
		</select>
		<br />
		<label for="manualprice">Manual Price Grabbing: </label>
		<input type="checkbox" value="true" name="manualprice" <?php if(isset($_COOKIE['manualprice']) && $_COOKIE['manualprice'] == "true") echo "checked"; ?>/><br />
		<input type="submit" value="Update Settings" name="submit"/>
	</form>

	<h2>Info:</h2>
	<p><b>Currency</b> - This allows you to set your preffered currency for prices to be displayed in.</p>
	<p><b>Manual Price Grabbing</b> - This setting will manually fetch the remote prices from steam. This process is slow but more accurate to the period in time you are fetching the prices. Because of the processing power needed to do this you will be required to submit a re-captcha.</p>
</body>

</html>

<?php

if(isset($_POST['currency']) && !empty($_POST['currency']))
	echo '<br/><br/>Submitted New Settings!';

?>

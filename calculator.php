<!DOCTYPE HTML>
<html>

<head>
	<link rel="stylesheet" href="jquery/jquery-ui.min.css">
	<script src="jquery/jquery.js"></script>
	<script src="jquery/jquery-ui.min.js"></script>
	<script src="js/sum.js"></script>
	<script src="js/calculator.js"></script>
	<link rel="stylesheet" type="text/css" href="layout.css" />
	<link rel="stylesheet" type="text/css" href="calculator.css" />
	<link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>Trade Assistant</title>
</head>

<body>
	<div id="navbar">
		<a href="help.html">How To</a>
		<a href="#">Item Calculator</a>
		<a href="#">Settings</a>
		<a href="index.php">Home</a>
	</div>

	<a href="index.php"><h1>Trade Assistant</h1></a>
	<p id="credit">Created by <a href="http://shane-brown.ca/" target="_blank">Shane "SajeOne" Brown</a><br /></p>

	<div id="page_content">
		<h2 id="trader">Trader's Items:</h2> <br />
			<label for="autocomplete">Search: </label>
			<input id="autocomplete"/>
			<table id="left" style="width: 100%;">
				<tr><td>Item Name:</td><td>Current Price:</td><td>Median Price:</td><td>Market Worth:</td><td>Volume:</td></tr>
				<tr><td>Test Item</td><td>$1.23</td><td>$1.19</td><td>$1.02</td><td>29323</td></tr>
			</table>
	</div>
</body>

</html>
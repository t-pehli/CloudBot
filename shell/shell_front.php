<?php 
		file_put_contents("shell/io/output_buffer", "");
		file_put_contents("shell/io/input_buffer", "");
?>
<!DOCTYPE html>
<html>
<head>
	<title>CloudBot OS</title>
	<meta charset="UTF-8">

	<style type="text/css">
		a {
			padding: 4px;
			border: 1px solid black;
			background-color: lightGray;
			cursor: pointer;
		}

		p {
			margin: 2px;
			white-space: pre-wrap;
		}

		body {
			margin: 2px;

			font-family: Arial;
			font-size: 12pt;
			font-weight: normal;
			text-decoration: none;
			color: black;
		}

		#wrapper {
			width: 800px;
			max-width: 100%;
			height: 600px;
			min-height: 200px;

			resize:both;
			
			border: 2px solid black;
			background-color: #F8F8F8;

			position:relative;
		}

		#header {
			margin: 0 auto;
			padding: 10px; 
			border-bottom: 1px solid black;
			background-color: #E0E0E0;
		}

		#main {
			position:absolute;
			overflow-x:hidden;
			overflow-y:auto;

			top:40px;
			bottom:0;
			left:0;

			width:99.5%;
			padding-left: 0.5%;

			margin-bottom: 28px;
		}

		#footer {
			position:absolute;
			bottom:0;
			left:0;
			display: inline-table;
			white-space: nowrap;

			width: 99.5%;
			min-height: 24px;
			max-height: 24px;
			line-height: 24px;
			padding-left: 0.5%;

			border-top: 1px solid black;

		}

		#path {
			display: table-cell;
			width: 1%;
		}

		#cmd {
			display: table-cell;
			width: 100%;
			overflow: hidden;

			border: 0px;
			background: none;

			font-family: Arial;
			font-size: 12pt;
			font-weight: normal;
			text-decoration: none;
			color: black;
		}

	</style>

</head>
<body>
	<div id="wrapper">
		<p id="header">CloudOS</p>
		<div id="main"></div>
		<div id="footer">
			<span id="path">/></span>
			<input type="text" id="cmd" value="">
		</div>
	</div>

</body>

<script type="text/javascript" src="lib/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="shell/io/io_front.js"></script>

</html>
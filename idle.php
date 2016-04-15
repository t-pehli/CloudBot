<html>
<head>
	<title>CloudBot Idle</title>
	<meta charset="UTF-8">

	<style type="text/css">
		a {
			padding: 4px;
			border: 1px solid black;
			background-color: lightGray;
			cursor: pointer;
		}
	</style>

</head>
<body>

	<div style="width:300px; margin: 50 auto; border: 1px solid black; text-align:center">
		<?php
		
			foreach(SYSTEM::$STATUS as $key => $value) {
			
				echo "$key : $value<br>";
			} 
		?>
	</div>

	<div style="width:200px; margin: 50 auto; border: 1px solid black; text-align:center">
		<p style="margin: 0 auto; padding: 10px; border-bottom: 1px solid black;">CloudOS</p>
		<p>
			<?php 
				if( SYSTEM::$STATUS['POWER'] != "ON" ){
				
					foreach(SYSTEM::$PARAMETERS['ENVIRONMENTS'] as $key => $value) {
				
					echo '<form action="/?access=directive" method="post">
							<input type="hidden" name="directive" value="START">
							<input type="hidden" name="environment" value="'.$key.'">
							<input type="submit" value="Start '.$key.'">
						</form>';
					}
				}
				else {

					echo '<form action="/?access=directive" method="post">
							<input type="hidden" name="directive" value="STOP">
							<input type="submit" value="Stop">
						</form>';
				} 
			?>
			
			<p style="min-height:20px"></p>
			<form action="/?access=directive" method="post">
				<input type="hidden" name="directive" value="STATUS">
				<input type="submit" value="Status">
			</form>
		</p>
		
	</div>

</body>
</html>
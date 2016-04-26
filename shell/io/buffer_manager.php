<?php
	$BUFFER_TIMEOUT = 3900;
	$BUFFER_START = $_SERVER['REQUEST_TIME_FLOAT']*1000;

	// ============= Input Handling =============
	if( isset( $_POST['data'] ) && $_POST['data'] !="" ){
		
		file_put_contents("input_buffer", ",\n".$_POST['data'], FILE_APPEND);
	}


	// ============ Output Handling =============
	do{
		usleep( 20000 );
		
		$output = file_get_contents( "output_buffer" );
		file_put_contents( "output_buffer", "" );

		// wait for something in the buffer if possible
	} while ( $output == "" && microtime(true)*1000 < $BUFFER_START + $BUFFER_TIMEOUT );

	if ( $output == "" ){
		echo $_GET['callback'] . "({});";
	}
	else {
	
		echo $_GET['callback'] . "([".substr($output, 2)."]);";
	}
	// =========================================

?>
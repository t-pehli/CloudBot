<?php

	if( isset( $_POST['data'] ) && $_POST['data'] !="" ){
		
		file_put_contents("input_buffer", ",\n".$_POST['data'], FILE_APPEND);
	}

	$output = file_get_contents( "output_buffer" );
	file_put_contents( "output_buffer", "" );

	if ( $output == "" ){
		echo "{}";		
	}
	else {
	
		echo '['.substr($output, 2).']';
	}

?>
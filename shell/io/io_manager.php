<?php


/**
* 
*/
class IO
{

	// ---------------- Constructor ------------------
	public static function start () {

		IO::$bufferCounter = 0;
	}
	// -----------------------------------------------

	// -------- Shell IO Manager Main Loops ----------
	public static function loopStart () {

		// ===== Input Buffer =====
		$serialInput = file_get_contents( "shell/io/input_buffer" );
		// clear external input buffer
		file_put_contents("shell/io/input_buffer", "");
		$input = [];

		// place stuff from the external output buffer (file) to the internal
		if( substr($serialInput, 2) != "" ){
			
			$serialInput = '['.substr($serialInput, 2).']';
			$input = json_decode( $serialInput );
			SYSTEM::logx($serialInput);
			IO::$inputBuffer = array_merge( IO::$inputBuffer, $input );
		}
	}

	public static function loopEnd() {

		// ===== Output Buffer =====
		$dump = "";
		foreach (IO::$outputBuffer as $out) {
			// place stuff from the internal output buffer to the external (file)
			$dump .= ",\n".$out;
		}

		file_put_contents("shell/io/output_buffer", $dump, FILE_APPEND);
		
		// clear internal output buffer
		IO::$outputBuffer = [];
	}

	// -----------------------------------------------

	// -------- Shell IO Management Methods ----------
	public static function printx ( $out ){
		// stringify if not
		if( !is_string($out) ) {
			$out = print_r( $out, true );
		}

		$serialOut = json_encode( 
			array( "id"=>IO::$bufferCounter, "type"=>"msg", "content"=>$out ) );

		IO::$bufferCounter++;
		array_push( IO::$outputBuffer, $serialOut );
	}

	public static function scanx ( $out ){
		// stringify if not
		if( !is_string($out) ) {
			$out = print_r( $out, true );
		}

		$serialOut = json_encode( 
			array( "id"=>IO::$bufferCounter, "type"=>"ask", "content"=>$out ) );

		IO::$bufferCounter++;
		array_push( IO::$outputBuffer, $serialOut );
	}

	public static function returnx (){

		$serialOut = json_encode( 
			array( "id"=>IO::$bufferCounter, "type"=>"path", "content"=>SHELL::$PATH ) );

		IO::$bufferCounter++;
		array_push( IO::$outputBuffer, $serialOut );
	}

	public static function readx (){

		if( !empty( IO::$inputBuffer ) ){

			return array_shift( IO::$inputBuffer );
		}
		else {

			return false;
		}
	}
	// -----------------------------------------------

	// ---------- Shell IO Helper Methods ------------
	public static function yesNo( $input ){

		if( strcasecmp( "yes", $input ) || strcasecmp( "y", $input ) ){

			return 1;
		}
		else if( strcasecmp( "no", $input ) || strcasecmp( "n", $input ) ){

			return 0;
		}
		else{

			return -1;
		}
	}

	public static function is_dir_empty($dir) {
		if (!is_readable($dir)){

			return NULL; 	
		}
		else{

			return ( count( scandir($dir) ) == 2 );	
		}		
	}
	// -----------------------------------------------

	private static $inputBuffer = [];	// internal
	private static $outputBuffer = [];	// internal

	private static $bufferCounter;

}
IO::start();



?>
<?php


/**
* 
*/
class IO
{

	// ---------------- Constructor ------------------
	public static function setup () {

		file_put_contents("os/io/output_buffer",
			json_encode( array( "id"=>"0","type"=>"directive","target"=>"SYSTEM",
				"content"=>array( "ENVIRONMENT"=>SYSTEM::$ENVIRONMENT ) ) ) );
		self::$bufferCounter = 1;

	}
	// -----------------------------------------------

	// ----------- IO Manager Main Loop --------------
	public static function loop () {

		foreach (self::$outputBuffer as $out) {
			// place stuff from the internal output buffer to the external (file)
			file_put_contents("os/io/output_buffer", ",\n".$out, FILE_APPEND);
		}
		// clear internal output buffer
		self::$outputBuffer = [];

		
	}

	// -----------------------------------------------

	// ----------- IO Management Methods -------------
	public static function msg ( $out, $target="SHELL" ){
		// stringify if not
		if( !is_string($out) ) {
			$out = print_r( $out, true );
		}

		$serialOut = json_encode( array("id"=>self::$bufferCounter, "type"=>"message", "target"=>$target, "content"=>$out ) );
		self::$bufferCounter++;
		array_push( self::$outputBuffer, $serialOut );
	}

	// -----------------------------------------------

	public static $inputBuffer = [];	// internal
	public static $outputBuffer = [];	// internal

	public static $bufferCounter;

}
IO::setup();



?>
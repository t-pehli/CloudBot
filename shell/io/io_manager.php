<?php


/**
* 
*/
class IO
{

	// ---------------- Constructor ------------------
	public static function start () {

		self::$bufferCounter = 0;
		file_put_contents("shell/io/output_buffer", "");
		file_put_contents("shell/io/input_buffer", "");
	}
	// -----------------------------------------------

	// -------- Shell IO Manager Main Loop -----------
	public static function loop () {

		// ===== Input Buffer =====
		$serialInput = file_get_contents( "shell/io/input_buffer" );
		file_put_contents("shell/io/input_buffer", "");
		$input = [];

		// place stuff from the external output buffer (file) to the internal
		if( substr($serialInput, 2) != "" ){
			
			$serialInput = '['.substr($serialInput, 2).']';
			$input = json_decode( $serialInput );
			self::$inputBuffer = array_merge( self::$inputBuffer, $input );
		}
		// clear external output buffer
		// =========================
		
		// ===== Output Buffer =====
		$dump = "";
		foreach (self::$outputBuffer as $out) {
			// place stuff from the internal output buffer to the external (file)
			$dump .= ",\n".$out;
		}

		file_put_contents("shell/io/output_buffer", $dump, FILE_APPEND);
		
		// clear internal output buffer
		self::$outputBuffer = [];
		// =========================

		
	}

	// -----------------------------------------------

	// -------- Shell IO Management Methods ----------
	public static function printx ( $out ){
		// stringify if not
		if( !is_string($out) ) {
			$out = print_r( $out, true );
		}

		$serialOut = json_encode( 
			array( "id"=>self::$bufferCounter, "type"=>"msg", "content"=>$out ) );

		self::$bufferCounter++;
		array_push( self::$outputBuffer, $serialOut );
	}

	public static function returnx (){

		$serialOut = json_encode( 
			array( "id"=>self::$bufferCounter, "type"=>"path", "content"=>SHELL::$PATH ) );

		self::$bufferCounter++;
		array_push( self::$outputBuffer, $serialOut );
	}

	public static function readx (){

		if( !empty( self::$inputBuffer ) ){

			return array_shift( self::$inputBuffer );
		}
		else {

			return false;
		}
	}
	// -----------------------------------------------

	private static $inputBuffer = [];	// internal
	private static $outputBuffer = [];	// internal

	private static $bufferCounter;

}
IO::start();



?>
<?php


/**
* 
*/
class IO
{

	// ---------------- Constructor ------------------
	public static function start () {

		IO::$bufferCounter = 0;
		// file_put_contents("shell/io/output_buffer", "");
		// file_put_contents("shell/io/input_buffer", "");
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
			IO::$inputBuffer = array_merge( IO::$inputBuffer, $input );
		}
		// clear external output buffer
		// =========================
		
		// ===== Output Buffer =====
		$dump = "";
		foreach (IO::$outputBuffer as $out) {
			// place stuff from the internal output buffer to the external (file)
			$dump .= ",\n".$out;
		}

		file_put_contents("shell/io/output_buffer", $dump, FILE_APPEND);
		
		// clear internal output buffer
		IO::$outputBuffer = [];
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
			array( "id"=>IO::$bufferCounter, "type"=>"msg", "content"=>$out ) );

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

	private static $inputBuffer = [];	// internal
	private static $outputBuffer = [];	// internal

	private static $bufferCounter;

}
IO::start();



?>
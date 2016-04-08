<?php

/**
* 
*/
class SHELL
{

	// ------------------- Start ---------------------
	public static function start () {

		self::$PATH="/";

		require_once( "shell/io/io_manager.php" );
		IO::start();
		IO::printx( "CloudOS online..." );
		IO::returnx();	
	}
	// -----------------------------------------------

	// ------------------ Resume ---------------------
	public static function resume () {

		require_once( "shell/io/io_manager.php" );
		require_once( "shell/commands/parser.php" );
	}
	// -----------------------------------------------

	// ----------------- Main Loop -------------------
	public static function loop () {

		IO::loop();

		try {
			if( $cmd ){


				IO::printx( $cmd );
				IO::returnx();
			}
		}
		catch( Exception $e ) {

			IO::printx( "PHP Error: "$e->getMessage() );
			IO::returnx();
		}
		
		
	}
	// -----------------------------------------------

	public static $PATH;

}


?>
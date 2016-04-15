<?php

/**
* 
*/
class SHELL
{

	// ------------------- Start ---------------------
	public static function start () {

		SHELL::$STATE = "idle";
		SHELL::$PATH = "/";
		SYSTEM::$MEMORY['SHELL_STATE'] = SHELL::$STATE;
		SYSTEM::$MEMORY['SHELL_PATH'] = SHELL::$PATH;

		require_once( "shell/io/io_manager.php" );
		IO::start();
		IO::printx( "CloudOS online..." );
		// IO::returnx();	
	}
	// -----------------------------------------------

	// ------------------ Resume ---------------------
	public static function resume () {

		require_once( "shell/io/io_manager.php" );

		SHELL::$STATE = SYSTEM::$MEMORY['SHELL_STATE'];
		SHELL::$PATH = SYSTEM::$MEMORY['SHELL_PATH'];

	}
	// -----------------------------------------------

	// --------------- Helper Methods ----------------
	public static function parse ( $cmd ) {

		SHELL::$ARGS = explode(' ',trim($cmd));

		$registry = json_decode( file_get_contents("shell/registry.json"), true);

		if( array_key_exists( SHELL::$ARGS[0], $registry['aliases'] ) ){

			$program = $registry['aliases'][SHELL::$ARGS[0]];
		}
		else {

			$program = SHELL::$ARGS[0];
		}

		if( array_key_exists( $program, $registry ) ){

			include_once( $registry[$program]["path"] );

			if( class_exists( $registry[$program]["class"] )){

				SHELL::$runningClass = $registry[$program]["class"];
				return true;
			}
			else {

				IO::printx( "Error in file ".$registry[$program]["path"] );
				IO::returnx();
				return false;
			}
		}
	}

	public static function returnx () {

		SHELL::$STATE = "idle";
		IO::returnx();
	}
	// -----------------------------------------------

	// ----------------- Main Loop -------------------
	public static function loop () {

		IO::loop();

		if( SHELL::$STATE == "idle" ){

			$cmd = IO::readx();

			if( $cmd != false ){

				if( SHELL::parse( $cmd ) ){

					SHELL::$STATE = "running";

					$MAIN = SHELL::$runningClass;
					$MAIN::start();
				}
				else {

					IO::printx( "No command '".$cmd."' found." );
					IO::returnx();
				}
			}
		}
		else {

			$MAIN = SHELL::$runningClass;
			$MAIN::loop();
		}
		
		SYSTEM::$MEMORY['SHELL_STATE'] = SHELL::$STATE;
		SYSTEM::$MEMORY['SHELL_PATH'] = SHELL::$PATH;
	}
	// -----------------------------------------------

	public static $STATE;
	private static $runningClass;

	public static $PATH;
	public static $ARGS;

}


?>
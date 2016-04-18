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
		SYSTEM::$MEMORY['SHELL_PROGRAM'] = SHELL::$PROGRAM;

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
		SHELL::$PROGRAM = SYSTEM::$MEMORY['SHELL_PROGRAM'];

		if( SHELL::$PROGRAM != "" && method_exists( SHELL::$PROGRAM, "resume") ){

			$program = SHELL::$PROGRAM;
			$program::resume();	
		}

	}
	// -----------------------------------------------

	// ----------------- Main Loop -------------------
	public static function loop () {

		IO::loopStart();
		SYSTEM::logx( SHELL::$STATE );

		if( SHELL::$STATE == "idle" || SHELL::$PROGRAM == "" ){

			$cmd = IO::readx();

			if( $cmd != false ){

				if( SHELL::parse( $cmd ) ){

					SHELL::$STATE = "running";

					$MAIN = SHELL::$PROGRAM;
					$MAIN::start();
				}
				else {

					IO::printx( "No command '".$cmd."' found." );
					IO::returnx();
				}
			}
		}
		else {

			$MAIN = SHELL::$PROGRAM;
			$MAIN::loop();
		}
		
		IO::loopEnd();

	}
	// -----------------------------------------------

	// ------------------ Pause ----------------------
	public static function pause () {

		require_once( "shell/io/io_manager.php" );

		SYSTEM::$MEMORY['SHELL_STATE'] = SHELL::$STATE;
		SYSTEM::$MEMORY['SHELL_PATH'] = SHELL::$PATH;
		SYSTEM::$MEMORY['SHELL_PROGRAM'] = SHELL::$PROGRAM;

		if( SHELL::$PROGRAM != "" && method_exists( SHELL::$PROGRAM, "pause")){

			$program = SHELL::$PROGRAM;
			$program::pause();	
		}

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

				SHELL::$PROGRAM = $registry[$program]["class"];
				return true;
			}
			else {

				IO::printx( "Error in file ".$registry[$program]["path"] );
				IO::returnx();
				return false;
			}
		}
	}

	public static function handleShutdown( $error ){

		IO::printx( "Error: ".$error["message"] );
		IO::printx( "File: ".$error["file"]." line: ".$error["line"] );
		SHELL::returnx();
		IO::loopEnd();
		SYSTEM::$MEMORY['SHELL_STATE'] = "idle";
		SYSTEM::$MEMORY['SHELL_PROGRAM'] = "";
		SYSTEM::$MEMORY['SHELL_PATH'] = SHELL::$PATH;
	}

	public static function returnx () {

		SHELL::$STATE = "idle";
		SHELL::$PROGRAM = "";
		IO::returnx();
	}
	// -----------------------------------------------

	public static $STATE;
	public static $PROGRAM;

	public static $PATH;
	public static $ARGS;

}


?>
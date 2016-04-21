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
	}
	// -----------------------------------------------

	// ------------------ Resume ---------------------
	public static function resume () {

		require_once( "shell/io/io_manager.php" );

		SHELL::$STATE = SYSTEM::$MEMORY['SHELL_STATE'];
		SHELL::$PATH = SYSTEM::$MEMORY['SHELL_PATH'];
		SHELL::$PROGRAM = SYSTEM::$MEMORY['SHELL_PROGRAM'];

		if( SHELL::$STATE == "running" ){


			$registry = json_decode( file_get_contents("shell/registry.json"), true);
			$programArray = array_filter(
				$registry,
				function ($e) {
					if( array_key_exists( "class", $e ) ){

						return $e["class"] == SHELL::$PROGRAM;	
					}
					else{

						return false;
					}					
				}
			);
			$program = array_shift( $programArray );
			include_once( $program['path'] );
		}

		if( SHELL::$PROGRAM != "" && method_exists( SHELL::$PROGRAM, "resume") ){

			$programClass = SHELL::$PROGRAM;
			$programClass::resume();	
		}

	}
	// -----------------------------------------------

	// ----------------- Main Loop -------------------
	public static function loop () {

		IO::loopStart();

		if( SHELL::$STATE == "idle" || SHELL::$PROGRAM == "" ){

			$cmd = IO::readx();

			if( $cmd != false ){

				if( SHELL::parse( $cmd ) ){

					SHELL::$STATE = "running";

					$programClass = SHELL::$PROGRAM;
					if( method_exists( $programClass, "start")){
					
						$programClass::start();
					}
					if( method_exists( $programClass, "resume")){
					
						$programClass::resume();
					}
				}
				else {

					IO::printx( "No command '".$cmd."' found." );
					IO::returnx();
				}
			}
		}
		else {

			$cmd = IO::readx();
			$runningArgs = explode(' ',trim($cmd));

			if( $cmd != false && $runningArgs[0] == "interrrupt" ){

				if( SHELL::$PROGRAM != "" && method_exists( SHELL::$PROGRAM, "stop")){

					$programClass = SHELL::$PROGRAM;
					$programClass::stop();	
				}
				array_shift( $runningArgs );
				IO::printx( "Interrupt: ".implode( " ", $runningArgs ) );
				IO::loopEnd();

				SHELL::$STATE = "idle";
				SHELL::$PROGRAM = "";
				SHELL::returnx();
			}
			else{

				if( $cmd!=false ){

					IO::replyx( $cmd );
				}

				if( SHELL::$PROGRAM != "" && method_exists( SHELL::$PROGRAM, "loop") ){

					$programClass = SHELL::$PROGRAM;
					$programClass::loop();	
				}
			}

		}
		
		IO::loopEnd();

	}
	// -----------------------------------------------

	// ------------------ Pause ----------------------
	public static function pause () {

		if( SHELL::$PROGRAM != "" && method_exists( SHELL::$PROGRAM, "pause")){

			$programClass = SHELL::$PROGRAM;
			$programClass::pause();	
		}
		IO::loopEnd();

		SYSTEM::$MEMORY['SHELL_STATE'] = SHELL::$STATE;
		SYSTEM::$MEMORY['SHELL_PATH'] = SHELL::$PATH;
		SYSTEM::$MEMORY['SHELL_PROGRAM'] = SHELL::$PROGRAM;
	}
	// -----------------------------------------------

	// --------------- Helper Methods ----------------
	public static function parse ( $cmd ) {

		$cmd = trim( preg_replace( '/\s+/', ' ', $cmd ) );

		SHELL::$ARGS = explode(' ', $cmd );

		$registry = json_decode( file_get_contents("shell/registry.json"), true);

		if( array_key_exists( SHELL::$ARGS[0], $registry['aliases'] ) ){

			$programName = $registry['aliases'][SHELL::$ARGS[0]];
		}
		else {

			$programName = SHELL::$ARGS[0];
		}

		if( array_key_exists( $programName, $registry ) ){

			include_once( $registry[$programName]["path"] );

			if( class_exists( $registry[$programName]["class"] )){

				SHELL::$PROGRAM = $registry[$programName]["class"];
				return true;
			}
			else {

				IO::printx( "Error in file ".$registry[$programName]["path"] );
				IO::returnx();
				return false;
			}
		}
	}

	public static function handleShutdown( $error ){

		IO::printx( "Error".$error["type"].": ".$error["message"] );
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
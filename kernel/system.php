<?php

/** 	Helper class that contains all system settings and environment variables
* 	and also access lists for modules to be loaded during initialistaion.
*/
class SYSTEM
{

	// ---------------- Constructor ------------------
	public static function start () {

		SYSTEM::$CYCLE = 0;

		SYSTEM::loadConfiguration();
		SYSTEM::loadStatus();

		SYSTEM::handleShutdown();
	}
	// -----------------------------------------------



	// -------------- Helper Methods -----------------
	public static function loadConfiguration(){

		$conf = json_decode( file_get_contents("kernel/conf.json"), true);
		foreach ($conf as $property => $value) {
			SYSTEM::$PARAMETERS[$property] = $value;
		}
	}

	public static function loadStatus( ){

		SYSTEM::$STATUS = json_decode( file_get_contents("kernel/status.json"), true);
		if( !isset( SYSTEM::$STATUS['POWER'] ) ){

			SYSTEM::$STATUS['POWER'] = "OFF";
		}

		if( !isset( SYSTEM::$STATUS['CONNECTION'] ) ){

			SYSTEM::$STATUS['CONNECTION'] = "OFF";
		}
	}

	public static function saveStatus( ){

		file_put_contents( "kernel/status.json", json_encode(SYSTEM::$STATUS) );
	}

	public static function loadEnvironment(){

		if( 
			isset($_POST['environment']) 
			&& array_key_exists( $_POST['environment'], SYSTEM::$PARAMETERS['ENVIRONMENTS'])
		){
		
			SYSTEM::$ENVIRONMENT = $_POST['environment'];
			SYSTEM::$STATUS['ENVIRONMENT'] = SYSTEM::$ENVIRONMENT;
		}
		else if( isset(SYSTEM::$STATUS['ENVIRONMENT']) ){

			SYSTEM::$ENVIRONMENT = SYSTEM::$STATUS['ENVIRONMENT'];
		}
		else{

			SYSTEM::$ENVIRONMENT = "SHELL";
			SYSTEM::$STATUS['ENVIRONMENT'] = SYSTEM::$ENVIRONMENT;
		}
	
	}

	public static function logx ( $out ){
		// stringify if not
		if( !is_string($out) ) {
			$out = print_r( $out, true );
		}

		if( $out == "CLEAR_LOG" ){

			file_put_contents("log.txt", "CloudOS System Logfile:");
		}
		else {

			file_put_contents("log.txt", "\n".$out, FILE_APPEND);
		}
	}

	public static function handleShutdown(){

		function shutdown_handler() {

			$error = error_get_last();
			
			if ( $error["type"] > 0 ){

				chdir( $_SERVER['DOCUMENT_ROOT'] );

				SYSTEM::logx( "Error ".$error["type"].": ".$error["message"] );
				SYSTEM::logx( "File: ".$error["file"]." line: ".$error["line"] );
			}
			if ( $error['type'] === E_ERROR || $error['type'] === E_PARSE ) {
				// fatal error has occured

				if( isset( SYSTEM::$ENVIRONMENT ) ){

					$MAIN=SYSTEM::$PARAMETERS['ENVIRONMENTS'][SYSTEM::$ENVIRONMENT]['MAIN_CLASS'];
					$MAIN::handleShutdown( $error );
				}

				PULSE::fire( SYSTEM::$PARAMETERS['ADDRESS'] );
			}
		}

		register_shutdown_function('shutdown_handler');
	}
	// -----------------------------------------------

	// ---------- Main Operation Methods -------------
	public static function loop(){

		if( !PULSE::check() ){

			SYSTEM::$CYCLE = -1;
		}
		else {

			SYSTEM::$CYCLE++;
			usleep( 20000 );
		}
		
	}

	public static function runDirective( $directive ){
		
		if( strcasecmp( $directive, "STATUS") == 0 ){

			echo json_encode( SYSTEM::$STATUS );
		}
		else if( strcasecmp( $directive, "START") == 0 ){

			SYSTEM::powerOn();
		}
		else if( strcasecmp( $directive, "STOP") == 0 ){

			SYSTEM::powerOff();
			echo json_encode( SYSTEM::$STATUS );
		}
		
	}


	public static function powerOn(){

			SYSTEM::logx( "CLEAR_LOG" );

		if( SYSTEM::$STATUS['POWER'] != "ON" ){

			SYSTEM::$STATUS['POWER'] = "ON";
			SYSTEM::$STATUS['CONNECTION'] = "OFF";
			SYSTEM::saveStatus();

			require( SYSTEM::$PARAMETERS['ENVIRONMENTS'][SYSTEM::$ENVIRONMENT]['LOCATION_FRONT'] );
			
			require( "kernel/pulse_manager.php" );
			PULSE::$COUNT = 0;
			PULSE::fire( SYSTEM::$PARAMETERS['ADDRESS'] );
		}
		else {

			SYSTEM::logx( "Already online!" );
		}
	}

	public static function powerOff(){

		if( SYSTEM::$STATUS['POWER'] != "OFF" ){

			SYSTEM::$STATUS = [];
			SYSTEM::$STATUS['POWER'] = "OFF";
			SYSTEM::$STATUS['CONNECTION'] = "OFF";
			SYSTEM::saveStatus();
		}
		else {

			SYSTEM::logx( "Already offline!" );
		}
	}
	// -----------------------------------------------


	public static $PARAMETERS = [];
	public static $MEMORY = [];
	public static $STATUS = [];
	public static $ENVIRONMENT = "";
	public static $CYCLE = 0;
	public static $DEBUG = "";


}
SYSTEM::start();

?>
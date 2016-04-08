<?php

/** 	Helper class that contains all system settings and environment variables
* 	and also access lists for modules to be loaded during initialistaion.
*/
class SYSTEM
{

	// ---------------- Constructor ------------------
	public static function start () {

		self::loadConfiguration();
		self::loadStatus();

	}
	// -----------------------------------------------



	// -------------- Helper Methods -----------------
	public static function loadConfiguration(){

		$conf = json_decode( file_get_contents("kernel/conf.json"), true);
		foreach ($conf as $property => $value) {
			self::$PARAMETERS[$property] = $value;
		}
	}

	public static function loadStatus( ){

		self::$STATUS = json_decode( file_get_contents("kernel/status.json"), true);
	}

	public static function loadEnvironment(){

		if( 
			isset($_POST['environment']) 
			&& array_key_exists( $_POST['environment'], self::$PARAMETERS['ENVIRONMENTS'])
		){
		
				self::$ENVIRONMENT = $_POST['environment'];
		}
		else{

			self::$ENVIRONMENT = "SHELL";	
		}
		self::$STATUS['ENVIRONMENT'] = self::$ENVIRONMENT;
	
	}

	public static function runDirective( $directive ){
		
		if( strcasecmp( $directive, "STATUS") == 0 ){

			self::echoStatus();
		}
		else if( strcasecmp( $directive, "START") == 0 ){

			self::powerOn();
		}
		else if( strcasecmp( $directive, "STOP") == 0 ){

			self::powerOff();
			self::echoStatus();
		}
		
	}
	
	public static function echoStatus(){

		echo json_encode( self::$STATUS );
	}
	// -----------------------------------------------

	// ---------- Main Operation Methods -------------
	public static function powerOn(){

		if( self::$STATUS['POWER'] != "ON" ){

			self::$STATUS['POWER'] = "ON";

			self::loadEnvironment();		

			require( self::$PARAMETERS['ENVIRONMENTS'][self::$ENVIRONMENT]['LOCATION_FRONT'] );
			
			require( "kernel/pulse_manager.php" );
			PULSE::$COUNT = 0;
			PULSE::fire( self::$PARAMETERS['ADDRESS'] );

			file_put_contents( "kernel/status.json", json_encode(self::$STATUS) );
		}
		else {

			echo "Already online!";
		}
	}

	public static function powerOff(){

		if( self::$STATUS['POWER'] != "OFF" ){

			self::$STATUS = [];
			self::$STATUS['POWER'] = "OFF";
			file_put_contents("kernel/status.json", json_encode(self::$STATUS) );
		}
		else {

			echo "Already offline!";
		}
	}

	// -----------------------------------------------


	public static $PARAMETERS = [];
	public static $MEMORY = [];
	public static $STATUS = [];
	public static $ENVIRONMENT = "";
	public static $DEBUG = "";


}
SYSTEM::start();

?>
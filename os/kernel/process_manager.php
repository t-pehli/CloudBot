<?php

/**
* 
*/
class PROCESS_MANAGER
{

	// ---------------- Constructor ------------------
	public static function setup () {
		if( SYSTEM::$PARAMETERS['ENVIRONMENT'] == "GUI" ){
			// TODO Window manager
		} else {
			// Initialise shell
			require("os/shell/shell_manager.php");
		}
	}
	// -----------------------------------------------

	// -------- Process Management Methods -----------

	public static function loop(){
		
		// Shell/GUI loop
		if( SYSTEM::$PARAMETERS['ENVIRONMENT'] == "GUI" ){
			// TODO Window manager
		
		} else {
			// SHELL::loop();
		}

		// Loop through active processes and call loop
		foreach ( self::$processes as $process) {
			
			if( $process['status'] == 0 ){
				// process is starting right now, include source and setup
				include( $process['location'] );
				$process['name']::setup();
				$process['status']=1;
			}

			if( $process['status'] == 1 ){
				// process is resuming, load globals if any
				if( array_key_exists( $process['name'], SYSTEM::$MEMORY )){
					$process['name']::MEMORY = SYSTEM::$MEMORY[$process['name']];
				}
				$process['status']=2;
			}

			if( $process['status'] == 2 ){
				// process is running, call loop normally
				$process['name']::loop();	
			
			}
		}

	}

	public static function run(){

	}

	public static function resume(){

	}

	public static function pause(){

	}

	public static function kill(){

	}

	// -----------------------------------------------

	public static $processes = [];
}
PROCESS_MANAGER::setup();




?>
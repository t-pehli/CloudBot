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
		foreach ( self::$PROCESSES as $process) {
			
			// Process status: 0-starting, 1-active, 2-paused

			if( $process['status'] == 1 ){
				// process is running, call loop normally
				$process['name']::loop();
			
			}
		}

	}

	public static function run($process, $scriptmode = 0){
		$process['name'] = strtoupper( $process['name'] );

		if( $scriptmode == 0 && array_key_exists( $process['name'], SYSTEM::$REGISTRY )){
				
			// process is starting right now, include source and setup
			include( SYSTEM::$REGISTRY[$process['name']]['location'] );

			$mainClass = SYSTEM::$REGISTRY[$process['name']]['mainClass'];
			$mainClass::setup();
			$process['status'] = 0;
			self::$PROCESSES[$process['name']]=$process;
			self::resume($process, $scriptmode);

			
		} else if ( $scriptmode == 1 ) {
			// TODO executable script handling, temp memory?
		} else {
			// TODO throw error no such process
		}
	}

	public static function resume( $process, $scriptmode = 0 ){
		$process['name'] = strtoupper( $process['name'] );

		if( $scriptmode == 0 && array_key_exists( $process['name'], self::$PROCESSES )){
				
			// process is resuming, load globals if any
			if( array_key_exists( $process['name'], SYSTEM::$MEMORY ) && isset( $process['name']::$MEMORY )){
				$process['name']::$MEMORY = SYSTEM::$MEMORY[$process['name']];
			}
			self::$PROCESSES[$process['name']]['status'] = 1;
			
		} else if ( $scriptmode == 1 ) {
			// TODO executable script handling, temp memory?
		} else {
			// TODO throw error no such process
		}
	}

	public static function pause( $process, $scriptmode = 0 ){

		if( $scriptmode == 0 && array_key_exists( $process['name'], self::$PROCESSES )){

			// process is pausing, put globals in system memory and update status
			SYSTEM::$MEMORY[$process['name']] = $process['name']::$MEMORY;
			self::$PROCESSES[$process['name']]['status'] = 2;
			
		} else if ( $scriptmode == 1 ) {
			// TODO executable script handling, temp memory?
		} else {
			// TODO throw error no such process running
		}
	}

	public static function kill( $process, $scriptmode = 0 ){
		
		if( $scriptmode == 0 && array_key_exists( $process['name'], self::$PROCESSES )){

			// process is killed, clear globals in system memory and remove from active
			unset ( SYSTEM::$MEMORY[$process['name']] );
			unset ( self::$PROCESSES[$process['name']] );
			
		} else if ( $scriptmode == 1 ) {
			// TODO executable script handling, temp memory?
		} else {
			// TODO throw error no such process running
		}	
	}

	// -----------------------------------------------

	public static $PROCESSES = [];
}
PROCESS_MANAGER::setup();




?>
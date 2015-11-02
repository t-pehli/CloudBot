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
		$process = strtoupper( $process );

		if( $scriptmode == 0 && array_key_exists( $process, SYSTEM::$REGISTRY )){
				
			// process is starting right now, include source and setup
			include( SYSTEM::$REGISTRY[$process]['location'] );

			$mainClass = SYSTEM::$REGISTRY[$process]['mainClass'];
			$mainClass::setup();
			self::$PROCESSES[$process] = array( 'name'=>$process, 'status'=>0 );
			self::resume($process, $scriptmode);

			
		} else if ( $scriptmode == 1 ) {
			// TODO executable script handling, temp memory?
		} else {
			// TODO throw error no such process
		}
	}

	public static function resume( $process, $scriptmode = 0 ){
		$process = strtoupper( $process );

		if( $scriptmode == 0 && array_key_exists( $process, self::$PROCESSES )){
			
			$mainClass = SYSTEM::$REGISTRY[$process]['mainClass'];
			// process is resuming, load globals if any
			if( array_key_exists( $process, SYSTEM::$MEMORY ) && isset( $mainClass::$MEMORY )){
				$mainClass::$MEMORY = SYSTEM::$MEMORY[$process];
			}
			self::$PROCESSES[$process]['status'] = 1;
			
		} else if ( $scriptmode == 1 ) {
			// TODO executable script handling, temp memory?
		} else {
			// TODO throw error no such process
		}
	}

	public static function pause( $process, $scriptmode = 0 ){
		$process = strtoupper( $process );

		if( $scriptmode == 0 && array_key_exists( $process, self::$PROCESSES )){

			$mainClass = SYSTEM::$REGISTRY[$process]['mainClass'];
			// process is pausing, put globals in system memory and update status
			if(isset( $mainClass::$MEMORY )){
				SYSTEM::$MEMORY[$process] = $mainClass::$MEMORY;
			}
			self::$PROCESSES[$process]['status'] = 2;
			
		} else if ( $scriptmode == 1 ) {
			// TODO executable script handling, temp memory?
		} else {
			// TODO throw error no such process running
		}
	}

	public static function kill( $process, $scriptmode = 0 ){
		$process = strtoupper( $process );
		
		if( $scriptmode == 0 && array_key_exists( $process, self::$PROCESSES )){

			// process is killed, clear globals in system memory and remove from active
			unset ( SYSTEM::$MEMORY[$process] );
			unset ( self::$PROCESSES[$process] );
			
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
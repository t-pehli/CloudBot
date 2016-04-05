<?php

/**
* 
*/
class PROCESS
{

	// ---------------- Constructor ------------------
	public static function setup () {
		
	}
	// -----------------------------------------------

	// -------- Process Manager Main Loop ------------

	public static function loop(){
		
		// Shell/GUI loop
		$envClass = SYSTEM::$ENVIRONMENT;
		// $envClass::loop();

		// Loop through active processes and call loop
		foreach ( self::$processList as $process) {
			
			// Process status: 0-starting, 1-active, 2-paused

			if( $process['status'] == 1 ){
				// process is running, call loop normally
				$process['name']::loop();
			
			}
		}

	}
	// -----------------------------------------------

	// -------- Process Management Methods -----------
	public static function run($process, $scriptmode = 0){
		$process = strtoupper( $process );

		if( $scriptmode == 0 && array_key_exists( $process, SYSTEM::$REGISTRY )){
				
			// process is starting right now, include source and setup
			include( SYSTEM::$REGISTRY[$process]['location'] );

			$mainClass = SYSTEM::$REGISTRY[$process]['mainClass'];
			$mainClass::setup();
			self::$processList[$process] = array( 'name'=>$process, 'status'=>0 );
			self::resume($process, $scriptmode);

			
		} else if ( $scriptmode == 1 ) {
			// TODO executable script handling, temp memory?
		} else {
			// TODO throw error no such process
		}
	}

	public static function resume( $process, $scriptmode = 0 ){
		$process = strtoupper( $process );

		if( $scriptmode == 0 && array_key_exists( $process, self::$processList )){
			
			$mainClass = SYSTEM::$REGISTRY[$process]['mainClass'];
			// process is resuming, load globals if any
			if( array_key_exists( $process, SYSTEM::$MEMORY ) && isset( $mainClass::$MEMORY )){
				$mainClass::$MEMORY = SYSTEM::$MEMORY[$process];
			}
			// set as active
			self::$processList[$process]['status'] = 1;
			
		} else if ( $scriptmode == 1 ) {
			// TODO executable script handling, temp memory?
		} else {
			// TODO throw error no such process
		}
	}

	public static function pause( $process, $scriptmode = 0 ){
		$process = strtoupper( $process );

		if( $scriptmode == 0 && array_key_exists( $process, self::$processList )){

			$mainClass = SYSTEM::$REGISTRY[$process]['mainClass'];
			// process is pausing, put globals in system memory and update status
			if(isset( $mainClass::$MEMORY )){
				SYSTEM::$MEMORY[$process] = $mainClass::$MEMORY;
			}
			// set as paused
			self::$processList[$process]['status'] = 2;
			
		} else if ( $scriptmode == 1 ) {
			// TODO executable script handling, temp memory?
		} else {
			// TODO throw error no such process running
		}
	}

	public static function kill( $process, $scriptmode = 0 ){
		$process = strtoupper( $process );
		
		if( $scriptmode == 0 && array_key_exists( $process, self::$processList )){

			// process is killed, clear globals in system memory and remove from active
			unset ( SYSTEM::$MEMORY[$process] );
			unset ( self::$processList[$process] );
			
		} else if ( $scriptmode == 1 ) {
			// TODO executable script handling, temp memory?
		} else {
			// TODO throw error no such process running
		}	
	}

	// -----------------------------------------------

	public static $processList = [];
}
PROCESS::setup();




?>
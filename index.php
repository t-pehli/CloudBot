<?php

	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

	if( isset($_GET['access']) ){
	// incoming directive from user or other cloudbot

		if( $_GET['access'] == 'directive' ){

			require('kernel/system.php');
			SYSTEM::loadEnvironment();
			
			if(isset($_POST['directive'])){

				SYSTEM::runDirective($_POST['directive']);
			}
			else {

				SYSTEM::runDirective("STATUS");
			}
		}

		
		else if( $_GET['access'] == 'pulse' ){
			
			// ========== Load System ===========
			require('kernel/system.php');
			SYSTEM::loadEnvironment();


			require('kernel/pulse_manager.php');
			PULSE::accept();


			require( SYSTEM::$PARAMETERS['ENVIRONMENTS'][SYSTEM::$ENVIRONMENT]['LOCATION_BACK'] );
			$MAIN = SYSTEM::$PARAMETERS['ENVIRONMENTS'][SYSTEM::$ENVIRONMENT]['MAIN_CLASS'];


			// ============= Start ==============
			if( PULSE::$COUNT <= 1 ){

				$MAIN::start();
			}

			// ============= Resume =============
			if( method_exists( $MAIN, "resume")){

				$MAIN::resume();	
			}
			
			// ============= Loop ===============
			while( SYSTEM::$CYCLE != -1 && SYSTEM::$CYCLE<100 ){ // about 600 cycles without limit

				SYSTEM::loop();
				$MAIN::loop();
			}

			// ========= Pause & Pulse ==========
			if( SYSTEM::$STATUS['POWER'] == "ON" ){

				if( method_exists( $MAIN, "pause")){

					$MAIN::pause();	
				}
				SYSTEM::logx( "PULSE ".PULSE::$COUNT );
				PULSE::fire( SYSTEM::$PARAMETERS['ADDRESS'] );
			}
			else{

			// ============= Stop ==============
				if( method_exists( $MAIN, "stop")){

					$MAIN::stop();	
				}
			}
			// ==================================

		} else if ( $_GET['access'] == 'client' ){
			// user connected
			// TODO auth

			require('kernel/system.php');

			if( SYSTEM::$STATUS['POWER']=="ON" && SYSTEM::$STATUS['CONNECTION'] == "OFF"){

				$env = SYSTEM::$STATUS['ENVIRONMENT'];
				require( SYSTEM::$PARAMETERS['ENVIRONMENTS'][$env]['LOCATION_FRONT'] );
			}
			else if( SYSTEM::$STATUS['POWER']=="ON" && SYSTEM::$STATUS['CONNECTION'] == "ON"){

				echo "Another client is connected to CloudOS";
			}
			else{

				require ( "idle.php" );
			}


		}


	} else {
		// incoming connection from unknown party

		// TODO show fake front 
	}




?>
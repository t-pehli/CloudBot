<?php

	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

	if( isset($_GET['access']) ){

		// incoming connection from user or other cloudbot
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
			// incoming pulse

			require('kernel/system.php');
			SYSTEM::loadEnvironment();


			require('kernel/pulse_manager.php');
			PULSE::accept();


			require( SYSTEM::$PARAMETERS['ENVIRONMENTS'][SYSTEM::$ENVIRONMENT]['LOCATION_BACK'] );
			$MAIN = SYSTEM::$PARAMETERS['ENVIRONMENTS'][SYSTEM::$ENVIRONMENT]['MAIN_CLASS'];

			if( PULSE::$COUNT <= 1 ){

				$MAIN::start();
				SYSTEM::logx("START");
			}

			if( method_exists( $MAIN, "resume")){

				$MAIN::resume();	
			}

					
			while( SYSTEM::$CYCLE != -1 && SYSTEM::$CYCLE<100 ){

				SYSTEM::loop();
				$MAIN::loop();
			}

			if( PULSE::$COUNT < 10 ){

				if( method_exists( $MAIN, "pause")){

					$MAIN::pause();	
				}
				SYSTEM::logx( "PULSE ".PULSE::$COUNT );
				PULSE::fire( SYSTEM::$PARAMETERS['ADDRESS'] );
			}
			else{

				SYSTEM::powerOff();
			}


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
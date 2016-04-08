<?php

	if( isset($_GET['access']) ){

		// incoming connection from user or other cloudbot
		if( $_GET['access'] == 'directive' ){

			require('kernel/system.php');
			
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
			$main = SYSTEM::$PARAMETERS['ENVIRONMENTS'][SYSTEM::$ENVIRONMENT]['MAIN_CLASS'];

			if( PULSE::$COUNT <= 1 ){

				$main::start();
			}
			$main::resume();


			$i=0;
					
			while( PULSE::check() && $i<100 ){

				file_put_contents("filename.txt", "\nMain Loop $i :  ", FILE_APPEND);

				$main::loop();
				$i++;
				usleep( 200000 );
			}


		} else if ( $_GET['access'] == 'client' ){
			// user connected
			// TODO auth

			require('kernel/system.php');

			if( SYSTEM::$STATUS['POWER']=="ON" ){

				$env = SYSTEM::$STATUS['ENVIRONMENT'];
				require( SYSTEM::$PARAMETERS['ENVIRONMENTS'][$env]['LOCATION_FRONT'] );
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
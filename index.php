<?php
	if( isset($_GET['access']) ){
		// incoming connection from user or other cloudbot
		if( $_GET['access'] == 'pulse' ){
			// incoming pulse

			require('os/kernel/system.php');
			require('os/kernel/pulse_manager.php');
			require('os/kernel/process_manager.php');
			require('os/io/io_manager.php');

			$i=0;
			
			PROCESS::run("Debug");

			while( PULSE::pulseCheck() && $i<40 ){

				file_put_contents("filename.txt", "\nMain Loop $i :  ", FILE_APPEND);

				IO::loop();
				PROCESS::loop();
				$i++;


			}

		} else if ( $_GET['access'] == 'client' ){
			// user connected
			// TODO auth

			require('os/io/io_front.php');


		}


	} else {
		// incoming connection from unknown party

		// TODO show fake front 
	}

		


?>
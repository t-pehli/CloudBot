<?php

	


	require('os/kernel/system.php');
	require('os/kernel/pulse_manager.php');
	require('os/kernel/process_manager.php');

	$i=0;
	
	PROCESS_MANAGER::run("Debug");



	while( PULSE_MANAGER::pulseCheck() && $i<40 ){

		file_put_contents("filename.txt", "Main Loop $i :  ", FILE_APPEND);

		PROCESS_MANAGER::loop();
		$i++;


	}





		


?>
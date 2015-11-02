<?php

	


	require('os/kernel/system.php');
	require('os/kernel/pulse_manager.php');
	require('os/kernel/process_manager.php');

	$i=0;
	$debugger = ['name'=>"Debug"];
	PROCESS_MANAGER::run($debugger);

	while( PULSE_MANAGER::pulseCheck() && $i<20 ){

		PROCESS_MANAGER::loop();
		$i++;


	}

	
	





		


?>
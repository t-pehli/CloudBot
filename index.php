<?php

	


	require('os/kernel/system.php');
	require('os/kernel/pulse_manager.php');
	require('os/kernel/process_manager.php');

	$i=0;

	sleep(5);
	while( PULSE_MANAGER::pulseCheck() && $i<2000 ){
		file_put_contents("filename.txt", $i."  ".PULSE_MANAGER::$beginTime."  ".PULSE_MANAGER::$endTime."\n", FILE_APPEND);
		$i++;


	}

	
	





		


?>
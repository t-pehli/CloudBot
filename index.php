<?php

	


	require('os/kernel/system.php');
	require('os/kernel/pulser.php');

	$PULSER->pulse();
	// file_put_contents("filename.txt", PULSER::$pulseCounter);
	sleep(3);

	$PULSER->pulse();
	$PULSER->pulse();
	$PULSER->pulse();
	$PULSER->pulse();
	$PULSER->pulse();


	// $PULSER->pulseEnd();





	// $SYSTEM->loadModules();



		


?>
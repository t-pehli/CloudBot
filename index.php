<?php

	file_put_contents("filename.txt", "Test");
	


	require('os/kernel/system.php');
	require('os/kernel/pulser.php');

	$PULSER->pulse();
	file_put_contents("filename.txt", $SYSTEM->TIMEOUT);
	sleep(3);

	$PULSER->pulse();
	$PULSER->pulse();
	$PULSER->pulse();
	$PULSER->pulse();
	$PULSER->pulse();


	// $PULSER->pulseEnd();





	// $SYSTEM->loadModules();



		


?>
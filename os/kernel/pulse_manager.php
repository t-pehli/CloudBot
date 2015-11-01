<?php

/**
* 
*/
class PULSE_MANAGER
{

	// ---------------- Constructor ------------------
	public static function setup() {
		// Pulse is starting
		self::pulseBegin();
	}
	// -----------------------------------------------

	// ----------- Pulse Handlng Methods -------------
	public static function pulseBegin(){

		// get system status and carry globals over if set
		if (isset($_POST['STATE'])){
			SYSTEM::$MEMORY = $_POST['STATE'];
		}
		
		// get the time window of the pulse
		self::$beginTime = $_SERVER['REQUEST_TIME'];
		self::$endTime = SYSTEM::$PARAMETERS['TIMEOUT'] + self::$beginTime;

		// Terminate request and close connection to previous pulser if possible
		if (function_exists("ignore_user_abort")){
			ob_end_clean();
			header("Connection: close\r\n");
			header("Content-Encoding: none\r\n");
			header("Content-Length: 1");
			ignore_user_abort(true);
		}
	}

	public static function pulseEnd(){
		
		//Start a new request to the next pulser
		$nextUrl = "cloudos1.localhost";
		$nextPort = "80";
		$nextIP = "127.0.0.1";

		$ch = curl_init($nextUrl); 
		curl_setopt($ch, CURLOPT_RESOLVE, $nextUrl .":". $nextPort .":". $nextIP);
		curl_setopt($ch, CURLOPT_PARAMETERS['TIMEOUT'], 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 10); 

		curl_setopt($ch, CURLOPT_POSTFIELDS, SYSTEM::$MEMORY);

		curl_exec($ch);  
		curl_close($ch);
	}

	public static function pulseCheck(){

		// Regular pulse. check time
		$timeRemaining = self::$endTime - time();
		
		if( $timeRemaining > 0 && $timeRemaining <= 1 ){
			// self::pulseEnd();
			return false;
		} else {
			return true;
		}
	}


	// -----------------------------------------------

	
	public static $beginTime;
	public static $endTime;

}
PULSE_MANAGER::setup();




?>
<?php

/**
* 
*/
class PULSER
{


	// ---------------- Constructor ------------------
	public function __construct() {

		self::$pulseCounter = 0;
	}
	// -----------------------------------------------

	// ----------- Pulse Handlng Methods -------------
	public function pulseBegin(){

		// get system status and carry globals over if set
		if (isset($_POST['STATE'])){
			$SYSTEM->GLOBAL = $_POST['STATE'];
		}
		
		// get the time window of the pulse
		$this->beginTime = $_SERVER['REQUEST_TIME'];
		$this->endTime = $SYSTEM->TIMEOUT + $this->beginTime;

		// Terminate request and close connection to previous pulser if possible
		if (function_exists("ignore_user_abort")){
			ob_end_clean();
			header("Connection: close\r\n");
			header("Content-Encoding: none\r\n");
			header("Content-Length: 1");
			ignore_user_abort(true);
		}
	}

	public function pulseEnd(){
		
		//Start a new request to the next pulser
		$nextUrl = "cloudos1.localhost";
		$nextPort = "80";
		$nextIP = "127.0.0.1";

		$ch = curl_init($nextUrl); 
		curl_setopt($ch, CURLOPT_RESOLVE, $nextUrl .":". $nextPort .":". $nextIP);
		curl_setopt($ch, CURLOPT_TIMEOUT, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 10); 

		curl_setopt($ch, CURLOPT_POSTFIELDS, $SYSTEM->GLOBAL);

		curl_exec($ch);  
		curl_close($ch);
	}

	public function pulse(){

		if (self::$pulseCounter == 0) {
			// Pulse is starting
			$this->pulseBegin();
			self::$pulseCounter ++;
		
		} else {
			// Regular pulse. check time
			$timeRemaining = $this->endTime - time();
			
			if( $timeRemaining > 0 && $timeRemaining <= 1 ){
				// $this->pulseEnd();
			
			} else{
				self::$pulseCounter ++;
			} 
		}

		self::$pulseCounter ++;
	}


	// -----------------------------------------------


	public static $pulseCounter;
	
	public $beginTime;
	public $endTime;

}
$PULSER = new PULSER();




?>
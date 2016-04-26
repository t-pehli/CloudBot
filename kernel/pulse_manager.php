<?php

/**
* 
*/
class PULSE
{

	public static function start(){

		PULSE::$TASKMASTER = SYSTEM::$PARAMETERS['TASKMASTER'];
		require ( PULSE::$TASKMASTER['LOCATION'] );

	}


	// ----------- Pulse Handlng Methods -------------
	public static function accept(){

		// get pulse count and carry globals over if set
		if (isset($_POST['pulseCount'])){

			PULSE::$COUNT = $_POST['pulseCount'] + 1 ;
		}
		else{

			PULSE::$COUNT = 1;
		}

		if (isset($_POST['memory'])){

			SYSTEM::$MEMORY = json_decode( $_POST['memory'], true );
		}
		
		// get the time window of the pulse
		PULSE::$BEGIN_TIME = $_SERVER['REQUEST_TIME_FLOAT']*1000;
		PULSE::$END_TIME = SYSTEM::$PARAMETERS['SERVER_TIMEOUT'] + PULSE::$BEGIN_TIME;

		$tmMain = PULSE::$TASKMASTER['MAIN_CLASS'];
		$tmMain::accept();

		// Terminate request and close connection to previous pulser if possible
		if (function_exists("ignore_user_abort")){
			ob_end_clean();
			header("Connection: close\r\n");
			header("Content-Encoding: none\r\n");
			header("Content-Length: 1");
			ignore_user_abort(true);
		}
	}

	public static function check( $address ){

		$url = $address['URL'];
		$port = $address['PORT'];
		$ip = $address['IP'];

		$ch = curl_init();

		curl_setopt_array($ch, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url."?access=directive",
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => array( "directive" => "STATUS" )
		));

		$result = curl_exec($ch);  
		curl_close($ch);
		return $result;
	}

	public static function next(){

		$tmMain = PULSE::$TASKMASTER['MAIN_CLASS'];
		PULSE::$NEXT = $tmMain::next();
	}


	public static function fire(){

		if( !isset( PULSE::$NEXT) ){

			PULSE::$NEXT = SYSTEM::$PARAMETERS['ADDRESS'];
		}		
		
		//Start a new request to the next pulser
		$url = PULSE::$NEXT['URL'];
		$port = PULSE::$NEXT['PORT'];
		$ip = PULSE::$NEXT['IP'];

		// $status = json_decode( PULSE::check( $address ), true );
		// SYSTEM::logx( $status['POWER'] );

		PULSE::pulseCurl($url, $port, $ip, 
			array(
				"environment"=>SYSTEM::$ENVIRONMENT,
				"pulseCount"=>PULSE::$COUNT,
				"memory"=>json_encode( SYSTEM::$MEMORY )
			) );
		
	}

	public static function pulseCurl( $url, $port, $ip, $data ) {

		$ch = curl_init($url."?access=pulse"); 
		curl_setopt($ch, CURLOPT_RESOLVE, array( $url.":".$port.":".$ip ));
	    
	    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
	    curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
	    curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 10); 

	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		$result = curl_exec($ch);  
		curl_close($ch);
		return $result;
	}

	// not used currently
	public static function pulseFopen( $url, $port, $ip, $data ) {

		$params = array('http' => array(
			'method' => 'POST',
			'content' => $data
		));

		if ($optional_headers !== null) {
			
			$params['http']['header'] = $optional_headers;
		}

		$ctx = stream_context_create( $params );
		$fp = @fopen( $url, 'rb', false, $ctx );

		return $fp;
	}


	// -----------------------------------------------

	
	public static $BEGIN_TIME;
	public static $END_TIME;
	public static $COUNT;
	public static $TASKMASTER;
	public static $NEXT;

}
PULSE::start();


?>
<?php


/**
* 
*/
class SOLO
{
	// -------- Pulse Reaction Methods ----------
	public static function accept(){

		SOLO::$address = SYSTEM::$PARAMETERS['ADDRESS'];
	}


	public static function next(){

		PULSE::$NEXT = SOLO::$address;
	}

	public static $address;
}

?>
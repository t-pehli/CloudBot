<?php

/**
* 
*/
class SHUTDOWN
{
	public static function start(){

		IO::printx( "Shutting down..." );
		SHELL::returnx();	
		SYSTEM::powerOff();
	}

}


/**
* 
*/
class PING
{
	public static function start(){

		SYSTEM::$STATUS['CONNECTION'] = "ON";
		SYSTEM::saveStatus();
		SHELL::returnx();	
	}

}


?>
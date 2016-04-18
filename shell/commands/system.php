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

		if( SHELL::$ARGS[1] =="-s" ){

			SYSTEM::$STATUS['CONNECTION'] = "ON";
			SYSTEM::saveStatus();

			SHELL::returnx();	
		}
		else if ( count( SHELL::$ARGS ) >1 ){

			IO::printx( "Usage: ".SHELL::$ARGS[0]." [-s]");
			SHELL::returnx();
		}
		else{

			IO::printx( "Pinged");
			SHELL::returnx();
		}
	}

}


?>
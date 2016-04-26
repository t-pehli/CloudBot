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
class RESTART
{
	public static function start(){

		IO::printx( "Restarting..." );
		
		PULSE::$COUNT = 0;
		SYSTEM::$CYCLE = -1;
		SYSTEM::$STATUS['POWER'] = "RESTART";
		SYSTEM::saveStatus();
		SHELL::$PATH = "/";
		SHELL::pause();
		
		PULSE::fire();
	}

	public static function resume(){

		SHELL::returnx();
	}

}

/**
* 
*/
class ECHOX
{
	public static function start(){

		 if ( count( SHELL::$ARGS ) >1 ){

		 	array_splice( SHELL::$ARGS, 0, 1 );
		 	try {

		 		$result = eval( "return ".implode( ' ', SHELL::$ARGS ).";" );
		 	} catch ( Exception $e ) {

		 		$result = $e;
		 	}
		 	
			IO::printx( $result );
			SHELL::returnx();
		}
		else{
			IO::printx( "Usage: ".SHELL::$ARGS[0]." <php expression>");
			SHELL::returnx();
		}
	}

}
/**
* 
*/
class PING
{
	public static function start(){

		if( count( SHELL::$ARGS ) > 1 && SHELL::$ARGS[1] =="-s" ){

			SYSTEM::$STATUS['CONNECTION'] = "ON";
			SYSTEM::saveStatus();

			IO::setx( "IO_CLOCK", SYSTEM::$PARAMETERS['IO_CLOCK'] );

			SHELL::returnx();	
		}
		else if( count( SHELL::$ARGS ) > 1 && SHELL::$ARGS[1] =="-curl" ){

			IO::printx( PULSE::check( SYSTEM::$PARAMETERS['ADDRESS'] ) );

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
<?php

/**
* 
*/
class LS
{
	public static function start(){

		IO::printx( "Items in directory: ".SHELL::$PATH );
		IO::printx( implode( "<br>", scandir( $_SERVER['DOCUMENT_ROOT'].SHELL::$PATH ) ) );
		SHELL::returnx();	
	}

}

/**
* 
*/
class RM
{
	public static function start(){

		IO::printx( "Keeping busy RM" );
		unknown();
		SHELL::returnx();	
	}

	public static function loop(){

		IO::printx( "Keeping busy" );
	}

}


?>
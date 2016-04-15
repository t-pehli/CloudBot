<?php

/**
* 
*/
class LS
{
	public static function start(){

		IO::printx( "Items in directory: " );
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
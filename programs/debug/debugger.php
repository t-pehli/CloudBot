<?php

class Debug
{
	
	public static function start () {
		file_put_contents("filename.txt", "Debugger Setup"."\n", FILE_APPEND);
		
	}



	public static function loop () {
		// file_put_contents("filename.txt", "Debugger Loop"."\n", FILE_APPEND);
		IO::msg("Debug Loop");

		IO::msg(SYSTEM::$DEBUG);
		
	}






}




?>
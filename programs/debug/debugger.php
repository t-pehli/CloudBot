<?php

class Debug
{
	
	public static function setup () {
		file_put_contents("filename.txt", "debug"."\n", FILE_APPEND);
		
	}



	public static function loop () {
		file_put_contents("filename.txt", PULSE_MANAGER::$beginTime."  ".PULSE_MANAGER::$endTime."\n", FILE_APPEND);
		
	}






}




?>
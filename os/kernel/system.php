<?php

/** 	Helper class that contains all system settings and environment variables
* 	and also access lists for modules to be loaded during initialistaion.
*/
class SYSTEM
{

	// ---------------- Constructor ------------------
	public static function setup () {

		self::loadConfiguration();
		self::loadRegistry();

		self::$PARAMETERS['VERSION'] ="0.1";
	}
	// -----------------------------------------------



	// --------- Resource Handling Methods -----------
	public static function loadConfiguration(){

		$conf = json_decode( file_get_contents("os/kernel/conf.json"), true);
		foreach ($conf as $property => $value) {
			self::$PARAMETERS[$property] = $value;
		}
	}

	public static function loadRegistry(){
		
		self::$REGISTRY = json_decode( file_get_contents("os/kernel/registry.json"), true);
	}

	public static function addToRegistry($program, $properties){
		
		self::$REGISTRY[$program] = $properties;
		file_put_contents( "os/kernel/registry.json", json_encode($this->REGISTRY) );
	}

	public static function removeFromRegistry($program){
		
		unset(self::$REGISTRY[$program]);
		file_put_contents( "os/kernel/registry.json", json_encode($this->REGISTRY) );
	}
	// -----------------------------------------------


	public static $PARAMETERS = [];
	public static $REGISTRY = [];
	public static $MEMORY = [];


}
SYSTEM::setup();

?>
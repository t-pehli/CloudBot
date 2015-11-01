<?php

/** 	Helper class that contains all system settings and environment variables
* 	and also access lists for modules to be loaded during initialistaion.
*/
class SYSTEM
{

	// ---------------- Constructor ------------------
	public function __construct () {

		$conf = $this->loadConfiguration();
		$this->setConfiguration($conf);
	}
	// -----------------------------------------------



	// ----------- Configuration Methods -------------
	public function setConfiguration($conf){

		foreach ($conf as $property => $value) {
			$this->$property = $value;
		}
	}

	public function loadConfiguration(){

		return json_decode( file_get_contents("os/kernel/conf.json"), true);
	}

	public function storeConfiguration($conf){

		$this->setConfiguration($conf);
		file_put_contents( "os/kernel/conf.json", json_encode($conf) );
	}
	// -----------------------------------------------

	// ---------- Module Loading Methods -------------
	public function loadModules(){
		
		foreach ($this->MODULES as $module => $filename) {
			include($filename);
		}
	}
	// -----------------------------------------------



	public $VERSION ="0.0";

	public $MODULES = array(
			// 'PULSER'=>"system/kernel/pulser.php",
			'IO'=>"os/kernel/io.php"		
		);

	public $GLOBAL = array();


}
$SYSTEM = new SYSTEM();

?>
<?php

/**
* 
*/
class CONF_TASKMASTER
{
	public static function start(){

		if( count( SHELL::$ARGS ) > 1 && in_array( "-set", SHELL::$ARGS ) ){

			$key = array_search( "-set", SHELL::$ARGS );

			if( isset( SHELL::$ARGS[$key+1] ) && isset( SHELL::$ARGS[$key+2] ) ){

				SYSTEM::$PARAMETERS['TASKMASTER']['MAIN_CLASS'] = SHELL::$ARGS[$key+1];
				SYSTEM::$PARAMETERS['TASKMASTER']['LOCATION'] = SHELL::$ARGS[$key+2];

				SYSTEM::saveConfiguration();
				IO::printx( "Taskmaster updated!" );
			}
			else{

				IO::printx( "Usage: ".SHELL::$ARGS[0]." [-set &lt;classname&gt; &lt;location&gt;]");
			}
		}
		else if( count( SHELL::$ARGS ) > 2 && in_array( "-edit", SHELL::$ARGS ) ){

			$key = array_search( "-edit", SHELL::$ARGS );

			$tmMain = PULSE::$TASKMASTER['MAIN_CLASS'];
			if( method_exists( $tmMain, "edit" ) ){

				$args = SHELL::$ARGS;
				$result = $tmMain::edit( array_slice( $args, $key+1 ) );
				IO::printx( $result );
			}

		}
		else if ( count( SHELL::$ARGS ) == 1 ){

			IO::printx( "Current taskmaster: ".SYSTEM::$PARAMETERS['TASKMASTER']['MAIN_CLASS'] );
			IO::printx( "Taskmaster location: ".SYSTEM::$PARAMETERS['TASKMASTER']['LOCATION'] );
		}
		else{

			IO::printx( "Usage: ".SHELL::$ARGS[0].
				" [-set &lt;classname&gt; &lt;location&gt;] [-edit &lt;options$gt;]");
		}

		SHELL::returnx();
	}

}


?>
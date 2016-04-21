<?php

/**
* 
*/
class CHDIR
{
	public static function start(){

		if( count( SHELL::$ARGS ) > 1 ){

			if( substr( SHELL::$ARGS[1] , 0, 1) == "/" ){

				$path = ltrim( SHELL::$ARGS[1], "/" );
			}
			else{

				$path = ltrim( SHELL::$PATH."/".SHELL::$ARGS[1], "/" );
			}

			if( SHELL::$ARGS[1] == ".." && SHELL::$PATH != "/" ){

				SHELL::$PATH = dirname( SHELL::$PATH );
			}
			else if( SHELL::$ARGS[1] == "/" ){

				SHELL::$PATH = "/";
			}	
			else if( SHELL::$ARGS[1] != ".." ){

				if( is_dir( $path ) ){

					SHELL::$PATH = "/".$path;
				}
				else{

					IO::printx( SHELL::$ARGS[1]." is not a directory!");
				}

			}
			SHELL::returnx();
		}
		else {
			
			IO::printx( "Usage: ".SHELL::$ARGS[0]." <directory name>");
			SHELL::returnx();
		}
	}

}


/**
* 
*/
class AUTOCOMPLETE
{
	public static function start(){

		$results = [];

		if( count( SHELL::$ARGS ) == 2 ){
			// Autocomplete command
			
			if ( !isset( $registry ) ){

				$registry = json_decode( file_get_contents("shell/registry.json"), true);
			}

			$commands = array_merge( array_keys( $registry["aliases"] ), array_keys( $registry ) );

			if( ( $key = array_search( "aliases", $commands ) ) !== false ) {
				array_splice( $commands, $key, 1 );
			}
			if( ( $key = array_search( "autocomplete", $commands ) ) !== false ) {
				array_splice( $commands, $key, 1 );
			}

			foreach( $commands as $value ){

				if( strpos( $value, SHELL::$ARGS[1] ) === 0 ){

					array_push( $results, $value );
				}
			}

			if( count( $results ) == 1 ){

				IO::autox( $results[0] );
			}
			else if( count( $results ) > 1 ){

				IO::printx( implode( "        ", $results ) );
				IO::autox( "" );
			}
			else{
		
				IO::autox( "" );
			}
		}
		else{
			// Autocomplete path

			end( SHELL::$ARGS );
			$lastArg = key( SHELL::$ARGS );
			reset( SHELL::$ARGS );

			if( substr( SHELL::$ARGS[$lastArg] , 0, 1) == "/" ){

				$path = ltrim( SHELL::$ARGS[$lastArg], "/" );
			}
			else{

				$path = ltrim( SHELL::$PATH."/".SHELL::$ARGS[$lastArg], "/" );
			}
			// IO::printx( $path );

			$tail = ltrim( strrchr( $path, "/" ), "/" );
			if ( $tail === "" ){

				$tail = ltrim( $path, "/" );
			}
			$searchPath = preg_replace('/'.$tail.'$/s', '', $path);


			$items = scandir( $_SERVER['DOCUMENT_ROOT']."/".$searchPath );

			if( $items ){

				foreach( $items as $value ){

					if( strpos( $value, $tail ) === 0 ){

						array_push( $results, $value );
					}
				}
			}

			if( count( $results ) == 1 ){

				$cmdArray = SHELL::$ARGS;

				if( $tail == $results[0] ){

					$cmdArray[$lastArg] .= "/";
					$list = scandir( $_SERVER['DOCUMENT_ROOT']."/".$searchPath."/".$tail );

					if( ( $key = array_search( ".", $list ) ) !== false ) {
						array_splice( $list, $key, 1 );
					}
					if( ( $key = array_search( "..", $list ) ) !== false ) {
						array_splice( $list, $key, 1 );
					}

					IO::printx( implode( "        ", $list ) );

					array_splice( $cmdArray, 0, 1 ); //remove autocomplete
					IO::autox( implode( " ", $cmdArray ) );
				}
				else{
					
					$cmdArray[$lastArg] = preg_replace( 
						'/'.$tail.'$/s', $results[0],
						SHELL::$ARGS[$lastArg]
					);

					array_splice( $cmdArray, 0, 1 ); //remove autocomplete
					IO::autox( implode( " ", $cmdArray ) );
				}
				
			}
			else if( count( $results ) > 1 ){

				IO::printx( implode( "        ", $results ) );
				IO::autox( "" );
			}
			else{
		
				IO::autox( "" );
			}
		}
		SHELL::returnx();
	}
	
}


/**
* 
*/
class TEST
{
	public static function loop(){

		IO::printx( "Testloop" );
	}

}



?>
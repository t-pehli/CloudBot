<?php



/**
* 
*/
class LS
{
	public static function start(){

		if( !function_exists( "cmp" ) ){

			function cmp ( $a, $b ){

				if( substr( $a, 0, 1 ) == "/" && substr( $b, 0, 1 ) == "/" ){
				
					return strcasecmp ( $a , $b );
				}
				else if( substr( $a, 0, 1 ) == "/" && substr( $b, 0, 1 ) != "/" ){

					return -1;
				}
				else if( substr( $a, 0, 1 ) != "/" && substr( $b, 0, 1 ) == "/" ){

					return 1;
				}
				else{

					return strcasecmp ( $a , $b );
				}
			}
		}		

		$hiddenFlag = false;
		if( count( SHELL::$ARGS ) > 1 && in_array( "-a", SHELL::$ARGS ) ){

			if( ( $key = array_search( "-a", SHELL::$ARGS ) ) !== false ) {
				array_splice( SHELL::$ARGS, $key, 1 );
			}

			$hiddenFlag = true;
		}

		$path = false;

		if( count( SHELL::$ARGS ) == 1 ){

			$path = $_SERVER['DOCUMENT_ROOT'].SHELL::$PATH;
		}
		else if( count( SHELL::$ARGS ) == 2 && substr( SHELL::$ARGS[1] , 0, 1) == "/" ){

			$path = $_SERVER['DOCUMENT_ROOT'].SHELL::$ARGS[1];
			if ( !is_dir( $path ) ){

				IO::printx( SHELL::$ARGS[1]." is not a directory" );
				$path = false;
			}
		}
		else if ( count( SHELL::$ARGS ) == 2 && substr( SHELL::$ARGS[1] , 0, 1) != "/" ){

			$path = $_SERVER['DOCUMENT_ROOT'].SHELL::$PATH.SHELL::$ARGS[1];
			if ( !is_dir( $path ) ){

				IO::printx( SHELL::$ARGS[1]." is not a directory" );
				$path = false;
			}
		}


		if ( $path !== false ){

			$items = scandir( $path );
			foreach ($items as $key => $item) {
				
				if( !$hiddenFlag && substr( $item, 0, 1 ) == "." ){

					unset( $items[$key] );
				}
				else if( is_dir( $item ) ){

					$items[$key] = "/".$item;
				}
			}
			IO::printx( "Items in directory: ".
				preg_replace( '!^'.$_SERVER['DOCUMENT_ROOT'].'!s', '', $path ) );

			if( !empty( $items ) ){

				usort( $items, "cmp" );
				IO::printx( implode( "        ", $items ) );
			}
			SHELL::returnx();
		}
		else {

			IO::printx( "Usage: ".SHELL::$ARGS[0]." [-a] [&lt;directory&gt;]");
			SHELL::returnx();
		}
	}

}


/**
* 
*/
class MKDIR
{
	public static function start(){

		if( count( SHELL::$ARGS ) > 1 ){

			if( substr( SHELL::$ARGS[1] , 0, 1) == "/" ){

				$path = SHELL::$ARGS[1];
			}
			else{

				$path = ltrim( SHELL::$PATH."/".SHELL::$ARGS[1], "/" );
			}

			if( mkdir( ltrim( $path, "/" ) ) ){

				IO::printx( "Directory: ".SHELL::$ARGS[1]." created succesfully!" );
			}
			else{

				IO::printx( "Error while creating directory: ".SHELL::$ARGS[1] );
			}
			SHELL::returnx();
		}
		else {
			
			IO::printx( "Usage: ".SHELL::$ARGS[0]." &lt;directory&gt;");
			SHELL::returnx();
		}
	}
}


/**
* 
*/
class RM
{
	public static function start(){

		if( count( SHELL::$ARGS ) > 1 && in_array( "-y", SHELL::$ARGS ) ){
			// force delete
			if( ( $key = array_search( "-y", SHELL::$ARGS ) ) !== false ) {
				array_splice( SHELL::$ARGS, $key, 1 );
			}

			if( substr( SHELL::$ARGS[1] , 0, 1) == "/" ){

				$path = ltrim( SHELL::$ARGS[1], "/" );
			}
			else{

				$path = ltrim( SHELL::$PATH."/".SHELL::$ARGS[1], "/" );
			}

			RM::deleteFileDir( $path );
			SHELL::returnx();
		}
		else if( count( SHELL::$ARGS ) > 1 ) {
			//prompt for deletion

			if( substr( SHELL::$ARGS[1] , 0, 1) == "/" ){

				$path = ltrim( SHELL::$ARGS[1], "/" );
			}
			else{

				$path = ltrim( SHELL::$PATH."/".SHELL::$ARGS[1], "/" );
			}

			if( file_exists( $path ) && is_file( $path ) ){

				SYSTEM::$MEMORY['rm_file'] = $path;
				IO::scanx("Are you sure you want to delete file: \""
					.SHELL::$ARGS[1]."\" ?[Y/N]");
			}
			else if ( file_exists( $path ) && is_dir( $path ) ){

				SYSTEM::$MEMORY['rm_file'] = $path;
				IO::scanx("Are you sure you want to delete directory: \""
					.SHELL::$ARGS[1]."\" ?[Y/N]");
			}
			else {

				IO::printx( $path." is not a file or directory!" );
				SHELL::returnx();
			}
			
		}
		else {
			
			IO::printx( "Usage: ".SHELL::$ARGS[0]." &lt;file/directory&gt;");
			SHELL::returnx();
		}

	}

	public static function loop(){

		if( isset( SYSTEM::$MEMORY['rm_file'] ) && SYSTEM::$MEMORY['rm_file'] != "" ){

			$reply = IO::replyx();

			if( $reply && IO::yesNo( $reply ) == 1 ){

				RM::deleteFileDir( SYSTEM::$MEMORY['rm_file'] );
				SYSTEM::$MEMORY['rm_file'] = "";
				SHELL::returnx();
			}
			else if( $reply && IO::yesNo( $reply ) != 1 ){

				SYSTEM::$MEMORY['rm_file'] = "";
				SHELL::returnx();		
			}
		}
		else{

			IO::printx( "Error while deleting..." );
			SHELL::returnx();
		}
	}

	// ------------- Helper Methods -------------
	public static function deleteFileDir( $name ) {

		if( is_file( $name ) && unlink( $name ) ){
			
			IO::printx( "File deleted!" );
		}
		else if ( is_dir( $name ) && IO::is_dir_empty( $name ) && rmdir( $name ) ){

			IO::printx( "Directory deleted!" );
		}
		else if( is_dir( $name ) && RM::deleteDir( $name ) ){

			IO::printx( "Directory deleted!" );
		}
		else {

			rmdir( $name );
			IO::printx( "Error while deleting..." );
		}

			IO::printx( "|".$name."|" );
			IO::printx( "|".SHELL::$PATH."|" );
		if( "/".$name == SHELL::$PATH ){

			SHELL::$PATH = "/";
		}
	}


	public static function deleteDir( $dirPath ) {

		if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
			
			$dirPath .= '/';
		}
		$files = glob($dirPath . '*', GLOB_MARK);
		foreach ($files as $file) {
			
			if (is_dir($file)) {

				RM::deleteDir($file);
			} else {

				unlink($file);
			}
		}
		return rmdir($dirPath);
	}
}

?>
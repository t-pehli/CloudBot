<?php



/**
* 
*/
class LS
{
	public static function start(){

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

		if( count( SHELL::$ARGS ) > 1 && SHELL::$ARGS[1] == "-a" ){

			$items = scandir( $_SERVER['DOCUMENT_ROOT'].SHELL::$PATH );
			foreach ($items as $key => $item) {
				
				if( is_dir( $item ) ){

					$items[$key] = "/".$item;
				}
			}
			usort( $items, "cmp" );
			IO::printx( "Items in directory: ".SHELL::$PATH );
			IO::printx( implode( "        ", $items ) );
			SHELL::returnx();
		}
		else if ( count( SHELL::$ARGS ) > 1 ){

			IO::printx( "Usage: ".SHELL::$ARGS[0]." [-a]");
			SHELL::returnx();
		}
		else{

			$items = scandir( $_SERVER['DOCUMENT_ROOT'].SHELL::$PATH );
			foreach ($items as $key => $item) {
				
				if( substr( $item, 0, 1 ) == "." ){

					unset( $items[$key] );
				}
				else if( is_dir( $item ) ){

					$items[$key] = "/".$item;
				}
			}
			usort( $items, "cmp" );
			IO::printx( "Items in directory: ".SHELL::$PATH );
			IO::printx( implode( "        ", $items ) );
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

		if( count( SHELL::$ARGS ) > 1 ){

			if( file_exists( SHELL::$ARGS[1] ) ){

				if( is_file( SHELL::$ARGS[1] ) ){

					SYSTEM::$MEMORY['rm_file'] = SHELL::$ARGS[1];
					IO::scanx("Are you sure you want to delete file: \""
						.SHELL::$ARGS[1]."\" ?[Y/N]");
				}
				else if ( is_dir( SHELL::$ARGS[1] ) ){

					SYSTEM::$MEMORY['rm_file'] = SHELL::$ARGS[1];
					IO::scanx("Are you sure you want to delete directory: \""
						.SHELL::$ARGS[1]."\" ?[Y/N]");
				}
			}
			else {

				IO::printx( SHELL::$ARGS[1]." is not a file or directory!" );
				SHELL::returnx();
			}
			
		}
		else {

			IO::printx( "Usage: ".SHELL::$ARGS[0]." <file/directory>");
			SHELL::returnx();
		}

	}

	public static function loop(){

		if( isset( SYSTEM::$MEMORY['rm_file'] ) && SYSTEM::$MEMORY['rm_file'] != "" ){

			$reply = IO::readx();

			if( $reply && IO::yesNo( $reply ) == 1 ){

				if( is_file( SYSTEM::$MEMORY['rm_file'] ) ){
					
					unlink( SYSTEM::$MEMORY['rm_file'] );
					IO::printx( "File deleted!" );
				}
				else if ( is_dir( SYSTEM::$MEMORY['rm_file'] ) 
					&& IO::is_dir_empty( SYSTEM::$MEMORY['rm_file'] ) ){

					if( rmdir( SYSTEM::$MEMORY['rm_file'] ) ){}
				}
				else {

					RM::deleteDir( SYSTEM::$MEMORY['rm_file'] );
				}

				if( !file_exists( SYSTEM::$MEMORY['rm_file'] ) ){

					IO::printx( "Deleted successfully!" );
				}
				else {

					IO::printx( "Error while deleting..." );
				}
				SYSTEM::$MEMORY['rm_file'] = "";
				SHELL::returnx();		
			}
			else if( $reply && IO::yesNo( $reply ) == 0 ){

				SYSTEM::$MEMORY['rm_file'] = "";
				SHELL::returnx();		
			}
		}
		else{

			IO::printx( "Error while deleting..." );
			SHELL::returnx();
		}
	}

	// -------------- Helper Method -------------
	public static function deleteDir($dirPath) {

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
		rmdir($dirPath);
	}
}

?>
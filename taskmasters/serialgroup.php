<?php


/**
* 
*/
class SERIALGROUP
{
	// -------- Pulse Reaction Methods ----------
	public static function accept(){

		SERIALGROUP::$address = SYSTEM::$PARAMETERS['ADDRESS'];

		if( isset( SYSTEM::$MEMORY['tm_addressList'] ) ){

			SERIALGROUP::$addressList = SYSTEM::$MEMORY['tm_addressList'];
		}

		if( !in_array( SERIALGROUP::$address, SERIALGROUP::$addressList ) ){

			array_push( SERIALGROUP::$addressList, SERIALGROUP::$address );
		}
	}

	public static function next(){

		$addressKey = array_search( SERIALGROUP::$address, SERIALGROUP::$addressList );

		if( $addressKey + 1 < count( SERIALGROUP::$addressList ) ){

			$nextKey = $addressKey + 1;
		}
		else{

			$nextKey = 0;
		}

		if( array_key_exists( $nextKey, SERIALGROUP::$addressList ) ){

			SYSTEM::$MEMORY['tm_addressList'] = SERIALGROUP::$addressList;
			return SERIALGROUP::$addressList[$nextKey];
		}
		else{

			SYSTEM::logx( "Error finding next pulse target." );
			SYSTEM::logx( SERIALGROUP::$addressList );
			return SYSTEM::$PARAMETERS['ADDRESS'];
		}
	}


	public static function edit( $options ){

		if( $options[0] == "add" 
			// && isset( $options[1] ) 
			// && isset( $options[2] ) 
			// && isset( $options[3] ) ){
			){

			$count = count( SERIALGROUP::$addressList );

			$newAddress = array( "URL"=>"cloudmaster.localhost", "PORT"=>"80", "IP"=>"127.0.0.1" );
			// $newAddress = array( "URL"=>$options[1], "PORT"=>$options[2], "IP"=>$options[3] );
			$newCount = array_push( SERIALGROUP::$addressList, $newAddress );

			if( $newCount > $count ){

				return "Address added successfullly!";
			}
			else{

				return "Error while adding to Address List.";
			}
		}
		else if( $options[0] == "list" || $options[0] == "ls" ){

			return SERIALGROUP::$addressList;
		}
	}
	// tm -edit add cloudmaster.localhost 80 127.0.0.1

	public static $address;
	public static $addressList = [];
}

?>
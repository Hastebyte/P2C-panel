<?php

	// grab a new session

	printf( "[-] logging in..\n" );
	
	$session_id = login( );

	if ( strlen( $session_id ) == 0 )
	{
		printf( "[!] login failed\n" );
		die( );
	}
	
	// update hashed serial list
	
	printf( "[-] session valid\n" );
	
	if ( !resync_glot( $session_id, get_hashed_serials( ) ) )
	{
		printf( "[!] sync failed\n" );
		die( );
	} else {
		printf( "[+] sync successful\n" );
	}
	
	function login( )
	{	
		$data = array(
			'email' 	=> 'xxx@ctemplar.com',
			'password' 	=> 'xxx'
		);
		 	 
		$ch = curl_init( 'https://glot.io/auth/page/simple/login' );
		
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLINFO_HEADER_OUT, true );
		curl_setopt( $ch, CURLOPT_HEADER, 1 );
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );

		$result = curl_exec( $ch );

		printf( "[+] login result: %s\n\n", $result );
		
		preg_match_all( '/^Set-Cookie:\s*([^;]*)/mi', $result, $matches );
		
		 print_r( $matches );		
				
		curl_close( $ch );		
		
		if ( count( $matches ) != 2 )
			return "";
		else
			return $matches[1][0];
	}
	
	function resync_glot( $session, $content )
	{
		$json = '{"language":"plaintext","title":"hash","public":true,"files":[{"name":"hash","content":"' . $content . '"}]}';
		
		$ch = curl_init( );

		curl_setopt( $ch, CURLOPT_URL, 'https://glot.io/snippets/xxxx' );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );

		$headers = array (
			   "Authorization: Token xxxx",
			   "Content-Type: application/json; charset=utf-8",
			   "Content-Length: " . strlen( $json ),  
		   );	

		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );		
		curl_setopt( $ch, CURLOPT_COOKIE, $session );	
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $json );
		
		printf( "[+] connecting..\n" );
		
		$response = curl_exec( $ch );
		
		//printf( "[+] sync result: %s", $response );
		//printf( "[+] sync result length: %d\n", strlen( $response ) );
		
		curl_close( $ch );				
		
		if ( strlen( $response ) == 2 )
			return true;
		else
			return false;
	}

	function get_hashed_serials( )
	{
		$mysqli = connect_mysql( );
		$query = "SELECT * from serials";

		$result = mysqli_query( $mysqli, $query );
		
		if ( !$result )
		{ 
			printf( "failed1\n" );
			$mysqli->close( );
			die( );
		}

		$result = $result->fetch_all( );

		$hashed_collection = "";
		
		$count = 0;
		$last_key = "";
		
		foreach( $result as $row )
		{
			$last_key = $row[1];
			$truncated_serial = substr( $row[1], 0, 20 );
			$hashed = hash( 'sha256', $truncated_serial );
			$hashed_collection .= $hashed . "\n";
			$count++;
		}
		
		printf( " -> found %d keys\n", $count );
		//printf( " -> last key %s\n", $last_key );

		$mysqli->close( );	

		return $hashed_collection;
	}
	
	function connect_mysql( )
	{
		$mysqli = new mysqli( 'localhost', 'gateway', 'xxxx', 'gateway' );
		
		if ( mysqli_connect_errno( ) )
		{
			die( create_response( response_code::failure_database ) );
		}
		
		return $mysqli;
	}
	
?>

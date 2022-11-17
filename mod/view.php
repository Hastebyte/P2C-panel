
<?php
date_default_timezone_set( 'America/Los_Angeles' );

// ---------------------------------------------------------------

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

	//print_r( $result );
	
	foreach( $result as $row )
	{
	   $truncated_serial = substr( $row[1], 0, 20 );
	   $hashed = hash( 'sha256', $truncated_serial );
	   printf( "%s\n", $hashed );
	}
		
	//$id = $result->id;
	//$computerid = trim( $result->computerid );
	//$registered_date = $result->registered;
	//$duration = $result->duration;

	$mysqli->close( );
	die( );
	
function connect_mysql( )
{
	$mysqli = new mysqli( 'localhost', 'gateway', 'GateSQL123!', 'gateway' );
	
	if ( mysqli_connect_errno( ) )
	{
		die( create_response( response_code::failure_database ) );
	}
	
	return $mysqli;
}

?>

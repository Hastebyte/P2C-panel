<?php
	if ( !isset( $_SESSION ) )
	{ 
		session_start( );
	}

	if ( !isset( $_SESSION['logged_in'] ) )
	{
		header('Location: login.php');
		exit( );
	}
	
	function get_game_name( $id )
	{
		switch( $id )
		{
		case 0:
			return "r6 siege";
			break;
		case 1:
			return "dayz";
			break;
		case 2:
			return "rust";
			break;
		case 3:
			return "scum";
			break;
		case 4:
			return "apex";
			break;
		case 5:
			return "fortnite";
			break;
		case 6:
			return "pubg";
			break;	
		case 10:
			return "overwatch";
			break;		
		case 12:
			return "pubglite";
			break;	
		case 13:
			return "tarkov";
			break;	
		case 99:
			return "hwspoof";
			break;	
		default:
			return strval( $id );
		}
	}
	
	function get_duration_name( $duration )
	{
		switch( $duration )
		{
		case 2:
			return "day";
			break;
		case 4:
			return "3day";
			break;
		case 8:
			return "week";
			break;
		case 31:	
			return "month";
			break;
		default:
			return strval( $duration );
		}
	}
?>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<title>modtool</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="custom.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
	<link rel="icon" type="image/png" href="images/database_key.png"/>
 </head>
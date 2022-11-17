<!DOCTYPE html>
<html lang="en">
<head>

	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Clubhouse</title>
	
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>	

	<style>
		body
		{
			padding-top: 10px;
			padding-left: 10px;
			padding-right: 10px;
			padding-bottom: 10px;
		}
	</style>
	
</head>


<body>
	<div class="panel panel-primary">
    <div class="panel-heading"><strong>keygen</strong></div>
	
	<div class="panel-body">
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		
			<div class="form-row">
			  <div class="form-group col-md-6">
				<label for="serial">Game:</label>
				  <select class="form-control" id="gameid" name="gameid">
					<option selected>Choose...</option>
					<option value="0">Siege</option>
					<option value="1">DayZ</option>
					<option value="2">Rust</option>
					<option value="2">Scum</option>
					<option value="2">Apex</option>
					<option value="2">Fortnite</option>
					<option value="2">Pubg</option>
					<option value="2">Overwatch</option>
				  </select>
			  </div>
			  <div class="form-group col-md-6">
				<label for="resellerid">Duration:</label>
				  <select class="form-control" id="duration" name="duration">
					<option selected>Choose...</option>
					<option value="2">Day</option>
					<option value="8">Week</option>
					<option value="31">Month</option>
					<option value="366">Year</option>
					<option value="9999">Lifetime</option>
				  </select>
			  </div>
			</div>	
			<div class="form-row">		
				<div class="form-group col-md-6">
					<label for="resellerid">Count:</label>
					<input type="text" class="form-control" id="count" name="count" placeholder="Count">
				</div>
			  
				<div class="form-group col-md-6">
					<label for="resellerid">Reseller:</label>
					<select class="form-control" id="resellerid" name="resellerid">
					<option selected>Choose...</option>
					<option value="0">None</option>
					<option value="1">Dot</option>
					<option value="4">Huang</option>
					<option value="5">Aaron</option>
					<option value="6">Mar1k</option>
					<option value="7">Epic</option>
					<option value="8">Remidy</option>
					<option value="9">Mae</option>
					<option value="10">Snosh</option>
					
					<option value="11">Eplug (Aaron's Buyer)</option>
					<option value="12">mmbe55</option>
					<option value="13">Sky</option>
					</select>
				</div>		
			</div>		  
			<div class="form-row">		
				<div class="form-group col-md-12">
					<label for="resellerid">Comment:</label>
					<input type="text" class="form-control" id="comment" name="comment" placeholder="Comment">
				</div>
			</div>
				
			<div class="form-row">			
				<button type="submit" name="submit" class="btn btn-primary">Submit</button>
			</div>
			
		</form>
	</div>
		
	<div class="panel-footer">Version 1.0</div>
	</div>	
	
	<?php

		require_once( __DIR__ . '/api/Util/API.php' );
		require_once( __DIR__ . '/api/Util/Format.php' );
		require_once( __DIR__ . '/api/Util/SQL.php' );
		
		if( isset( $_POST['submit'] ) && !empty( $_POST['serial'] ) )
		{

			$serial_number = trim( $_POST['serial'] );
			$reseller_id   = trim( $_POST['resellerid'] );
			
	?>

		<div class="panel panel-primary">
			<div class="panel-heading"><strong>Result</strong></div>		
			<div class="panel-body">
				<?php				
					//$keyArray = array( );
					//$serialManager->createSerials( $count, $duration, $gameid, $resellerid, $comments, $keyArray );
					//print_r( $keyArray );
				?>
			</div>
		</div>	
	
	<?php
		}
						
		function generateSerial( )
		{
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$charactersLength = strlen( $characters );

			$randomString = '';

			for ( $i = 0; $i < 40; $i++ )
			{
				$randomString .= $characters[rand( 0, $charactersLength - 1 )];
			}

			return $randomString;
		}

		function serialExists( $mysqli, $serial )
		{
			$mysqli = \Util\ConnectSQL( );

			if ( !$mysqli )
			{
				return "Service is unavailable (0)";
			}
			
			$serial = \Util\Sanitize( mysqli, $serial );
			$query = "SELECT COUNT(*) AS num_rows from serials WHERE serial = '" . $serial . "'";      
			$result = mysqli_query( mysqli, $query );
		
			if ( !$result )
			{
				$mysqli->close( );
				return false;
			}
			
			$row = $result->fetch_array( MYSQLI_ASSOC );
	 
			if ( $row["num_rows"] == 0 )
			{
				return false;
			}
	  
			return true;
		}

		// curl -d '{"action":"createSerials","count":"10","duration":"8","gameId":"0","resellerId":"0","username":"choose","password":"gateway"}' -H "Content-Type: application/json" -X POST http://167.99.170.199/api/index.php

		function createSerials( $count, $duration, $gameId, $resellerId, $comments, &$resultArray )
		{	
			$mysqli = \Util\ConnectSQL( );

			if ( !$mysqli )
			{
				return "Service is unavailable (0)";
			}
			
			
			$resultArray = array( );
			$keysCreated = 0;

			for ( $i = 0; $i < $count; $i++ )
			{
				$newSerial = generateSerial( );

				// unlikely collision of 40 random characters

				if ( serialExists( $mysqli, $newSerial ) )
				{
					$newSerial = generateSerial( );
				
					// if still a collision, something is wrong with the random character generation

					if ( serialExists( $mysqli, $newSerial ) )
						die( "createSerials" );
				}

				// debug key

				//printf( "[+] key: %s\n", $newSerial );

				// create insert statement

				$query  = "INSERT INTO serials ( serial, duration, created, comments, gameid, resellerid ) ";
				$query .=" VALUES ( '$newSerial', $duration, NOW( ), '$comments', $gameId, $resellerId )";

				if ( mysqli_query( $mysqli, $query ) )
				{
				   //printf( "New record created successfully" );
				   array_push( $resultArray, $newSerial );
				   $keysCreated++;
				} else {
				   //printf( "Error: " . $query . "" . mysqli_error( $mysqli ) );
				   continue;
				}
			}

			return $keysCreated;
		}
	?>
	
</body>
</html>
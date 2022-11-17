<!DOCTYPE html>
<html lang="en">
<?php include 'header.php'; ?>
<body>

  <?php include 'menu.php'; ?>

  <!-- Page Content -->
  <div class="container">
  
 	<!-- Main Form -->
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >

		<table class="table table-sm">
			<tbody>
			<tr>
				<th scope="row">key:</th>
				<td>
				  <div class="form-group">
					<input type="text" class="form-control" id="key" name="key" placeholder="Enter serial">
				  </div>
				</td>
			</tr>
			<tr>
			  <th scope="row"></th>
			  <td>
			  	<button type="submit" name="action" value="query" class="btn btn-primary">Query</button>
				<button type="submit" name="action" value="reset" class="btn btn-primary">Reset</button>
			  </td>
			</tr>
			
			<?php
				if ( $_SESSION['admin'] == TRUE )
				{
			?>

			<tr>
				<th scope="row">change to:</th>
				<td>
					<select class="form-control" value="0" id="gameid" name="gameid">
							<option value="0">Siege</option>
							<option value="1">DayZ</option>
							<option value="2">Rust</option>
							<option value="3">Scum</option>
							<option value="4">Apex</option>
							<option value="5">Fortnite</option>
							<option value="10">Overwatch</option>
							<option value="11">Pubg Lite</option>
							<option value="12">Tarkov</option>
							<option value="14">Arma</option>
							<option value="99">Spoofer</option>
							<option value="98">Spoofer 2</option>
							<option value="100">Test</option>
					</select>
				</td>
			  <td><button type="submit" name="action" value="change_game" class="btn btn-primary">Change</button></td>
			</tr>
					
			<tr>
				<th scope="row">change duration:</th>
				<td>
					<input type="text" class="form-control" id="duration" name="duration" value="">
				</td>
			  <td><button type="submit" name="action" value="change_duration" class="btn btn-primary">Change</button></td>
			</tr>
						
			<?php
				}
			?>
			
			</tbody>
		</table> 	
		
		  
	</form> 
  
	<?php
		require_once( __DIR__ . '/../api/Util/API.php' );
		require_once( __DIR__ . '/../api/Util/Format.php' );
		require_once( __DIR__ . '/../api/Util/SQL.php' );
		require_once( __DIR__ . '/../api/Core/SerialManager.php' );
		
		function dump_key_info( )
		{
			$mysql_link = \Util\ConnectSQL( );

			if ( !$mysql_link )
			{
				return "[!] unable to connect to server";
			}
			
			$serial = \Util\Sanitize( $mysql_link, $_POST['key'] );
			$query = "SELECT * from serials WHERE serial = '" . $serial . "'";  
			
			$result = mysqli_query( $mysql_link, $query );
		
			if ( !$result )
			{
				$mysql_link->close( );
				return "[!] unable to connect to server";
			}
									
			if ( !$row = $result->fetch_object( ) )
			{
				$mysql_link->close( );
				return "[!] key is not found (0)";		
			}
			
			$message  = "key: " 		. $row->serial . "\n";
			$message .= "game: " 		. get_game_name( $row->gameid ) . "\n";
			$message .= "duration: " 	. get_duration_name( $row->duration ) . "\n";
			$message .= "created: " 	. $row->created . "\n";
			$message .= "reset count: " . intval( $row->resetcount ) . "\n";
			$message .= "hardware id: " . $row->computerid . "\n";
			
			if ( empty( $row->registered ) )
				$message .= "registered: no\n";
			else
				$message .= "registered: " 	. $row->registered . "\n";
			
			$message .= "resellerid: " 	. $row->resellerid . "\n";
			
			$mysql_link->close( );
			return $message;
		}
		
		function reset_key( )
		{
			$mysql_link = \Util\ConnectSQL( );

			if ( !$mysql_link )
			{
				return "[!] unable to connect to server";
			}
			
			$serial = \Util\Sanitize( $mysql_link, $_POST['key'] );	
			
			if ( !mysqli_query( $mysql_link, "UPDATE serials SET computerid = NULL WHERE serial =  '" . $serial . "'" ) )
			{
				$mysql_link->close( );
				return "[!] service is unavailable (2)";
			}

			$message = "reset " . $serial . " successfully";
			
			$mysql_link->close( );
			return $message;			
		}
		
		function change_game( )
		{
			if ( $_SESSION['admin'] != TRUE )
				return "invalid permissions";

			$mysql_link = \Util\ConnectSQL( );

			if ( !$mysql_link )
			{
				return "[!] unable to connect to server";
			}
			
			$serial = \Util\Sanitize( $mysql_link, $_POST['key'] );	
			$new_id = \Util\Sanitize( $mysql_link, $_POST['gameid'] );	
			
			if ( !mysqli_query( $mysql_link, "UPDATE serials SET gameid = " . $new_id . " WHERE serial =  '" . $serial . "'" ) )
			{
				$mysql_link->close( );
				return "[!] service is unavailable (2)";
			}
			
			$message = "changed " . $serial . " to game " . get_game_name( $new_id );
			
			$mysql_link->close( );
			return $message;			
		}		
		
		function change_duration( )
		{
			if ( $_SESSION['admin'] != TRUE )
				return "invalid permissions";

			$mysql_link = \Util\ConnectSQL( );

			if ( !$mysql_link )
			{
				return "[!] unable to connect to server";
			}
			
			$serial = \Util\Sanitize( $mysql_link, $_POST['key'] );	
			$new_duration = \Util\Sanitize( $mysql_link, $_POST['duration'] );	
			
			if ( !mysqli_query( $mysql_link, "UPDATE serials SET duration = " . $new_duration . " WHERE serial =  '" . $serial . "'" ) )
			{
				$mysql_link->close( );
				return "[!] service is unavailable (2)";
			}
			
			$message = "changed " . $serial . " to have duration: " . $new_duration;
			
			$mysql_link->close( );
			return $message;			
		}
				
		if( isset( $_POST['action'] ) && $_POST['action'] == "query" )
		{
			$message = dump_key_info( );
		} else if ( isset( $_POST['action'] ) && $_POST['action'] == "reset" ) {
			$message = reset_key( );	
		} else if ( isset( $_POST['action'] ) && $_POST['action'] == "change_game" ) {
			$message = change_game( );	
		} else if ( isset( $_POST['action'] ) && $_POST['action'] == "change_duration" ) {
			$message = change_duration( );
		}
		
		if ( !empty( $message ) )
		{
	?>

		<br />
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Result</strong></div>		
			<div class="panel-body">
				<pre><?php echo $message; ?></pre>

			</div>
		</div>	
	
	<?php 
		}
	?>
	
  </div>
</body>

</html>
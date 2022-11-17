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
			  <th scope="row">cache resync</th>
			  <td><button type="submit" name="action" value="resync" class="btn btn-primary">resync</button></td>
			</tr>
		  </tbody>
		</table> 	
	
		  
	</form> 
  
	<?php
		require_once( __DIR__ . '/../api/Util/API.php' );
		require_once( __DIR__ . '/../api/Util/Format.php' );
		require_once( __DIR__ . '/../api/Util/SQL.php' );
		require_once( __DIR__ . '/../api/Core/SerialManager.php' );

		function login( )
		{	
			$data = array(
				'email' 	=> 'zllswp@ctemplar.com',
				'password' 	=> 'GMA0dZCytfJnmPN'
			);
				 
			$ch = curl_init( 'https://glot.io/auth/page/simple/login' );
			
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLINFO_HEADER_OUT, true );
			curl_setopt( $ch, CURLOPT_HEADER, 1 );
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );

			$result = curl_exec( $ch );

			// printf( "[+] login result: %s\n\n", $result );
			
			preg_match_all( '/^Set-Cookie:\s*([^;]*)/mi', $result, $matches );
			
			// print_r( $matches );		
					
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

			curl_setopt( $ch, CURLOPT_URL, 'https://glot.io/snippets/x' );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );

			$headers = array (
				   "Authorization: Token x",
				   "Content-Type: application/json; charset=utf-8",
				   "Content-Length: " . strlen( $json ),  
			   );	

			curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );		
			curl_setopt( $ch, CURLOPT_COOKIE, $session );	
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $json );
			
			//printf( "[+] connecting..\n" );
			
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
			$mysql_link = \Util\ConnectSQL( );
		
			if ( !$mysql_link )
			{
				die ( "[!] unable to connect to server" );
			}
			
			$query = "SELECT * from serials";

			$result = mysqli_query( $mysql_link, $query );
			
			if ( !$result )
			{ 
				printf( "failed1\n" );
				$mysql_link->close( );
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
			
			//printf( " -> found %d keys\n", $count );
			//printf( " -> last key %s\n", $last_key );

			$mysql_link->close( );	

			return $hashed_collection;
		}
				
		function resync_cache( )
		{
			$session_id = login( );
			$message = "";

			if ( strlen( $session_id ) == 0 )
			{
				$message .= "[!] login failed\n";
				return $message;
			}
			
			// update hashed serial list
			
			$message .= "[-] session valid\n";
			
			if ( !resync_glot( $session_id, get_hashed_serials( ) ) )
			{
				$message .= "[!] sync failed";
				return $message;
			} else {
				$message .= "[+] sync successful";
				return $message;
			}						
		}
				
		if( isset( $_POST['action'] ) && $_POST['action'] == "resync" )
		{
			$message = resync_cache( );
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
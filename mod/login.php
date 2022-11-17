<!DOCTYPE html>
<html lang="en">

<?php
	if ( !isset( $_SESSION ) )
	{ 
		session_start( );
	}

	require_once( __DIR__ . '/../api/Util/API.php' );
	require_once( __DIR__ . '/../api/Util/Format.php' );
	require_once( __DIR__ . '/../api/Util/SQL.php' );
	require_once( __DIR__ . '/../api/Core/SerialManager.php' );	

	function authenticate_user( )
	{
		$mysql_link = \Util\ConnectSQL( );
		
		$username = $_POST['username']; //\Util\Sanitize( $mysql_link, $_POST['username'] );
		$password = $_POST['password']; //\Util\Sanitize( $mysql_link, $_POST['password'] );
		$admin = false;
		$reseller = false;
		$developer = false;

		if ( mysqli_connect_errno( ) )
		{
			return "[!] failed to connect to server";
		}
		
		// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
		
		$statement_object = $mysql_link->prepare( 'SELECT id, password, admin, reseller, developer FROM accounts WHERE username = ?' );
		
		if ( !$statement_object )
		{
			return "[!] failed to query values";
		}
						
		// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
		
		$statement_object->bind_param( 's', $_POST['username'] );
		$statement_object->execute( );
		$statement_object->store_result( );
		
		if ( $statement_object->num_rows <= 0 )
		{
			return "[!] invalid account";
		}
						
		$statement_object->bind_result( $id, $password, $admin, $reseller, $developer );
		$statement_object->fetch( );
		
		// Account exists, now we verify the password.
		// Note: remember to use password_hash in your registration file to store the hashed passwords.
		
		if ( hash( 'sha256', $_POST['password'] ) != $password )
		{
			return "[!] invalid account";
		}		
			
		// Verification success! User has logged in!
		// Create sessions so we know the user is logged in, they basically act like cookies but remember the data on the server.
		
		session_regenerate_id( );
		
		$_SESSION['logged_in'] = TRUE;
		$_SESSION['username'] = $_POST['username'];
		
		// clamp admin accounts to id = 0
		
		if ( $admin )
		{
			$_SESSION['id'] 	= 0;
			$_SESSION['admin'] 	= true;
		} else {
			$_SESSION['id'] 	= $id;
		}
			
		if ( $reseller )
			$_SESSION['reseller'] = true;			
		
		if ( $developer )
			$_SESSION['developer'] = true;
			
			
		return '[+] valid account, id=' . $_SESSION['id'] . ', username=' . $_SESSION['username'];
	}

	if( isset( $_POST['username'] ) && isset( $_POST['password'] ) )
	{
		$message = authenticate_user( );
		
		if ( $_SESSION['logged_in'] == TRUE )
		{
			header( 'Location: index.php' );
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
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
 </head>
<body>
	
	<!-- Page Content -->
	<div class="container">
	
	
	<br/>

	
	<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
		<div class="panel-heading"><strong>Authenticate</strong></div>
		<div class="panel-body">
		
			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
			
				<div class="form-group">
					<label for="username">Username:</label><br/>
					<input type="text" name="username" placeholder="Username" id="username" required>
				</div>
				<div class="form-group">
					<label for="password">Password:</label><br/>
					<input type="password" name="password" placeholder="Password" id="password" required>
				</div>
				<button type="submit" class="btn btn-primary">Login</button>
				
			</form>
			
		</div>
			<?php
			if ( isset( $message ) )
			{
			?>	
				<div class="panel-footer"><pre><?php  echo $message; ?></pre></div>
			<?php
			}
			?>
		</div>	
	</div>
	</div>
	</div>
	
</body>

</html>
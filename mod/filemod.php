<!DOCTYPE html>
<html lang="en">
<?php include 'header.php'; ?>
<body>

	<?php include 'menu.php'; ?>
	<?php

		require_once( __DIR__ . '/../api/Util/API.php' );
		require_once( __DIR__ . '/../api/Util/Format.php' );
		require_once( __DIR__ . '/../api/Util/SQL.php' );
		require_once( __DIR__ . '/../api/Core/SerialManager.php' );
		
		function upload_file( )
		{
				// /var/www/html/builds
		}
		
		function show_file_table( $reseller_id )
		{
			$mysql_link = \Util\ConnectSQL( );

			if ( !$mysql_link )
			{
				return 0;
			}
			
			$query = "SELECT gameid, name, maintenance, image, userfile, kernelfile, modified, developerid FROM games WHERE developerid = 70";
					
			$result = mysqli_query( $mysql_link, $query );
			
			if ( !$result )
			{
				echo "error";
				$mysql_link->close( );
				return 0;
			}
			
			while ( $row = $result->fetch_assoc( ) )
			{
				echo '<tr>';
				echo '	<td><strong>' . $row["name"] . '</strong></td>';
				echo '	<td>' . $row["maintenance"] . '</td>';
				echo '	<td>' . $row["userfile"] . '</td>';
				echo '	<td>' . $row["kernelfile"] . '</td>';
				echo '	<td>' . $row["modified"] . '</td>';
				echo '	<td><button type="submit" name="action" value="Upload Build" class="btn btn-primary">Upload Build</button></td>';
				echo '	<td><button type="submit" name="action" value="Maintenance" class="btn btn-warning">Maintenance</button></td>';
				echo '</tr>';
			}
				
			$mysql_link->close( );
		}	

	?>
		
  <!-- Page Content -->
  <div class="container">
	  
	<!-- Main Form -->
	<form method="post" action="game.php" >
	
			

			<table class="table table-striped table-nonfluid">
			  <thead>
				<tr>
				  <th scope="col">Game</th>
				  <th scope="col">Live</th>
				  <th scope="col">User File</th>
				  <th scope="col">Kernel Driver</th>
				  <th scope="col">Date</th>
				  <th scope="col"></th>
				  <th scope="col"></th>
				</tr>
			  </tr>
			  
			  
			  </thead>
			  <tbody>
			  
				<?php show_file_table( ); ?>
			  
			</tbody>
			</table>	

		
	</form>
	
  </div>
</body>

</html>
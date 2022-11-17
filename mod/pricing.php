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
		
		function show_pricing_table( $reseller_id )
		{
			$mysql_link = \Util\ConnectSQL( );

			if ( !$mysql_link )
			{
				return 0;
			}
			
			$query  = 	"SELECT username, id, accountid, games.name as 'game_name', games.gameid, maintenance, duration, durations.name as 'duration_name', pricing.price as 'price' from gateway.accounts";
			$query .=	" RIGHT JOIN gateway.pricing on gateway.pricing.accountid = gateway.accounts.id";
			$query .=	" LEFT JOIN gateway.durations on gateway.durations.durationid = gateway.pricing.durationid ";
			$query .=	" LEFT JOIN gateway.games on gateway.games.gameid = gateway.pricing.gameid WHERE gateway.accounts.id = 5";
					
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
				echo '	<td><strong>' . $row["game_name"] . ' ' . $row["duration_name"] . '</strong></td>';
				echo '	<td><span class="text-secondary">' . $row["price"] . '</span></td>';
				echo '	<td><input type="text" class="form-control input-sm" size="3" id="count" name="count" placeholder="0"></td>';
				echo '	<td><span class="label label-primary">' . $row["maintenance"] . '</span></td>';
				echo '</tr>';
			}
				
			$mysql_link->close( );
		}	

	?>
		
  <!-- Page Content -->
  <div class="container">
	  
	<!-- Main Form -->
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >

		<table class="table table-nonfluid">
		  <thead>
			<tr>
			  <th scope="col">Select Reseller</th>			  
			</tr>
		  </thead>
		  <tbody>
			<tr>
				<td>
					<select class="form-control" value="0" id="resellerid" name="resellerid">
						<option value="0">ch</option>
						<option value="1">dot</option>
						<option value="4">huang</option>
						<option value="5">aaron</option>
						<option value="7">epic</option>
						<option value="8">remidy</option>
						<option value="9">mae</option>
						<option value="10">snosh</option>
						<option value="11">eplug</option>
						<option value="12">mbe555</option>
						<option value="13">2upad</option>
						<option value="14">ninjaa</option>
						<option value="15">jazzy</option>
						<option value="16">beluka</option>
						<option value="4">xan</option>
						<option value="17">darkby</option>
						<option value="18">tobbo</option>
						<option value="19">sparky/fury</option>
						<option value="20">shark</option>
					</select>	
				</td>
			</tr>
			<tr>	
				<td><button type="submit" class="btn btn-primary mb-2">View Pricing</button></td>
					
			</tr>
		</tbody>
		</table>	
	
	</form>
	
  </div>
</body>

</html>
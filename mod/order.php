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
	
			

			<table class="table table-striped table-nonfluid">
			  <thead>
				<tr>
				  <th scope="col">Game</th>
				  <th scope="col">Price</th>
				  <th scope="col">Amount</th>
				  <th scope="col">Status</th>
				</tr>
			  </tr>
			  
			  
			  </thead>
			  <tbody>
			  
				<?php show_pricing_table( ); ?>
			  
				<!--
				<tr>
					<td><strong>R6 Day</strong></td>
					<td><span class="text-secondary">$2.50</span></td>
					<td><input type="text" class="form-control input-sm" size="3" id="count" name="count" placeholder="0"></td>
					<td><span class="label label-primary">Live</span></td>
				</tr>
				<tr>
					<td class="align-middle"><strong>R6 Week</strong></td>
					<td class="align-middle"><span class="text-secondary">$2.50</span></td>
					<td><input type="text" class="form-control input-sm" size="3" id="count" name="count" placeholder="0"></td>
					<td><span class="label label-primary">Live</span></td>
				</tr>
				<tr>
					<td class="align-middle"><strong>R6 Month</strong></td>
					<td class="align-middle"><span class="text-secondary">$2.50</span></td>
					<td><input type="text" class="form-control input-sm" size="3" id="count" name="count" placeholder="0"></td>
					<td><span class="label label-primary">Live</span></td>
				</tr>
				-->
				
					<td>
						Total: $0.00
					</td>
					<td></td>
					<td></td>				
					<td></td>				
				</tr>
				</tr>
					<td>
						<button type="submit" name="action" value="order" class="btn btn-primary">Order</button>
						<button type="submit" name="action" value="calculate" class="btn btn-secondary">Calculate Total</button>
					</td>
					<td></td>
					<td></td>				
					<td></td>				
				</tr>
			</tbody>
			</table>	

		
	</form>
	
  </div>
</body>

</html>
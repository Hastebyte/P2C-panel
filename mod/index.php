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
			
	function query_stat( $query )
	{
		$mysql_link = \Util\ConnectSQL( );

		if ( !$mysql_link )
		{
			return 0;
		}
		
		$result = mysqli_query( $mysql_link, $query );
		
		if ( !$result )
		{
			$mysql_link->close( );
			return 0;
		}
		
		if ( !$row = $result->fetch_object( ) )
		{
			$mysql_link->close( );
			return 0;	
		}
 
		$total = intval( $row->total );					
		$mysql_link->close( );
		
		return $total;
	}
	
	$key_count 				= query_stat( "SELECT COUNT(*) as total from serials WHERE resellerid = " . $_SESSION['id'] );
	$key_count_unregistered	= query_stat( "SELECT COUNT(*) as total from serials WHERE registered IS NULL AND resellerid = " . $_SESSION['id'] );
	
	$query  = "SELECT COUNT(*) as total from serials WHERE MONTH(created) = MONTH(CURRENT_DATE()) AND YEAR(created) = YEAR(CURRENT_DATE()) AND resellerid = " . $_SESSION['id'];
	
	$key_count_monthly		= query_stat( $query );
	
  ?>
  
  
  
  <!-- Page Content -->
  <div class="container">
  
					<!--
				 <div class="row">
					  <div class="col-md-3">
						<div class="thumbnail">
						  <a href="/w3images/lights.jpg">
							<img src="/w3images/lights.jpg" alt="Lights" style="width:100%">
							<div class="caption">
							  <p></p>
							</div>
						  </a>
						</div>
					  </div>
					  <div class="col-md-3">
						<div class="thumbnail">
						  <a href="/w3images/nature.jpg">
							<img src="/w3images/nature.jpg" alt="Nature" style="width:100%">
							<div class="caption">
							  <p></p>
							</div>
						  </a>
						</div>
					  </div>
					  <div class="col-md-3">
						<div class="thumbnail">
						  <a href="/w3images/fjords.jpg">
							<img src="/w3images/fjords.jpg" alt="Fjords" style="width:100%">
							<div class="caption">
							  <p></p>
							</div>
						  </a>
						</div>
					  </div>
					  <div class="col-md-3">
						<div class="thumbnail">
						  <a href="/w3images/fjords.jpg">
							<img src="/w3images/fjords.jpg" alt="Fjords" style="width:100%">
							<div class="caption">
							  <p></p>
							</div>
						  </a>
						</div>
					  </div>
				</div>
				-->

  
	 <table class="table table-sm">
	  <tbody>
		<tr>
		  <th scope="row">user_id</th>
		  <td><?php echo $_SESSION['id']; ?></td>
		</tr>
		<tr>
		  <th scope="row">username</th>
		  <td><?php echo $_SESSION['username']; ?></td>
		</tr>
		<tr>
		  <th scope="row">admin</th>
		  <td><?php echo $_SESSION['admin']; ?></td>
		</tr>
		<tr>
		  <th scope="row">reseller</th>
		  <td><?php echo $_SESSION['reseller']; ?></td>
		</tr>
		<tr>
		  <th scope="row">developer</th>
		  <td><?php echo $_SESSION['developer']; ?></td>
		</tr>
		<tr>
		  <th scope="row">key_count</th>
		  <td><?php echo $key_count; ?></td>
		</tr>
		<tr>
		  <th scope="row">key_count_unregistered</th>
		  <td><?php echo $key_count_unregistered; ?></td>
		</tr>
		<tr>
		  <th scope="row">keys_created_this_month</th>
		  <td><?php echo $key_count_monthly; ?></td>
		</tr>
	  </tbody>
	</table> 
  
	<?php

	?>
	
  </div>
</body>

</html>
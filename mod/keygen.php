<!DOCTYPE html>
<html lang="en">
<?php include 'header.php'; ?>
<body>

  <?php include 'menu.php'; ?>

  <!-- Page Content -->
  <div class="container">
	  
	<!-- Main Form -->
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" >
	
		<table class="table table-nonfluid">
		  <thead>
			<tr>
			  <th scope="col">Game</th>
			  <th scope="col">Duration</th>
			  <th scope="col">Count</th>
			  <th scope="col">Comment</th>
			  
				<?php
				if ( $_SESSION['admin'] == TRUE )
				{
				?>
				
				<th scope="col">Reseller</th>
				
				<?php
				}
				?>
			  
			</tr>
		  </thead>
		  <tbody>
			<tr>
				<td>
					<select class="form-control" value="0" id="gameid" name="gameid">
						<option value="0">Siege</option>
						<option value="1">DayZ</option>
						<option value="2">Rust</option>
						<option value="3">Scum</option>
						<option value="4">Apex</option>
						<option value="5">Fortnite</option>
						<option value="6">Pubg</option>
						<option value="10">Overwatch</option>
						<option value="12">Pubg Lite</option>
						<option value="13">Tarkov</option>
						<option value="14">Arma</option>
						<option value="99">Spoofer</option>
						<option value="100">Test</option>
					</select>
				</td>
				
				<td>
					<select class="form-control" value="2" id="duration" name="duration">
						<option value="2">Day</option>
						<option value="4">3 Day</option>
						<option value="8">Week</option>
						<option value="31">Month</option>
					</select>
				</td>
				

				<td><input type="text" class="form-control" value="0" id="count" name="count" placeholder="Count"></td>
				<td><input type="text" class="form-control" id="comment" name="comment" placeholder="Comment"></td>
				
				<?php
				
					if ( $_SESSION['admin'] == TRUE )
					{
				?>
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
				
				<?php
					}				
				?>
				
			</tr>
			<tr>	
				<td><button type="submit" class="btn btn-primary mb-2">Create</button></td>
				<td></td>
				<td></td>
				<td></td>	
				
				<?php
				
					if ( $_SESSION['admin'] == TRUE )
					{
				?>
					<td></td>	
					
				<?php
					}				
				?>
				
			</tr>
		</tbody>
		</table>	
			
	</form>
	
	
	
	<?php

		require_once( __DIR__ . '/../api/Util/API.php' );
		require_once( __DIR__ . '/../api/Util/Format.php' );
		require_once( __DIR__ . '/../api/Util/SQL.php' );
		require_once( __DIR__ . '/../api/Core/SerialManager.php' );
		
		function create_keys( )
		{
			$mysql_link = \Util\ConnectSQL( );
			
			$gameid 	= \Util\Sanitize( $mysql_link, $_POST['gameid'] );
			$duration 	= \Util\Sanitize( $mysql_link, $_POST['duration'] );
			$count 		= \Util\Sanitize( $mysql_link, $_POST['count'] );
			$comment 	= \Util\Sanitize( $mysql_link, $_POST['comment'] );	
			
			$gameid		= intval( $gameid );
			$duration	= intval( $duration );
			$count		= intval( $count );
			
			if ( $count > 100 )
			{
				return "[!] error: 100 keys maximum";
			}

			$serialManager = new Core\SerialManager( $mysql_link, 0, 0 ); 

			$key_array = array( );
			
			if ( $_SESSION['admin'] == TRUE )
			{
				$resellerid = \Util\Sanitize( $mysql_link, $_POST['resellerid'] );	
			} else {
				$resellerid = $_SESSION['id'];
			}
			
			$serialManager->createSerials( $count, $duration, $gameid, $resellerid, $comment, $key_array );
			return $key_array;
		}
		
		if( isset( $_POST['count'] ) && intval( $_POST['count'] ) > 0 )
		{
			if ( $_SESSION['admin'] == TRUE || $_SESSION['id'] == 12 )
			{
				$message = create_keys( );
			} else {
				$message = "[!] invalid permissions";
			}
	?>

		<div class="panel panel-default">
			<div class="panel-heading"><strong>Result</strong></div>		
			<div class="panel-body">
				<pre><?php
				echo intval( $_POST['count'] ) . "x " . get_game_name( intval( $_POST['gameid'] ) ) . " " . get_duration_name( intval( $_POST['duration'] ) );
				?></pre>
				<pre><?php print_r( $message ); ?></pre>
			</div>
		</div>	
	
	<?php 
		}
	?>
	
  </div>
</body>

</html>
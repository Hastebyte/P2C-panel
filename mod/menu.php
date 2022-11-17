<?php

	function parse( $str, $start_token, $end_token )
	{
		$start_pos 	= strrpos( $str, $start_token, 0 );
		$end_pos 	= strrpos( $str, $end_token, $start_pos + strlen( $start_token ) );

		$start_cut 	= $start_pos + strlen( $start_token );
		$end_cut 	= $end_pos - $start_cut;

		$substr 	= substr( $str, $start_cut, $end_cut + strlen( $end_token ) );

		return $substr;
	}
?>

<!-- Navigation  -->
<nav class="navbar navbar-default">
  <div class="container-fluid">
	<div class="navbar-header">
	  <a class="navbar-brand" href="#">modtool</a>
	</div>
		<ul class="nav navbar-nav">
	
		<!-- Render Menu -->

		<?php

		function render_menu_item( $url, $image, $title )
		{
			$current_url = $_SERVER['REQUEST_URI']; 
			$current_url = parse( $current_url, "/", ".php" );
			
			if ( $url == $current_url )
				echo '<li class="active">';
			else
				echo '<li>';
			
			echo '<a href="'. $url . '"><img src="'. $image . '">&nbsp;' . $title . '</a></li>';
		}
		
		render_menu_item( "index.php", "images/rosette_blue.png", "dashboard" );
			
		if ( $_SESSION['admin'] == TRUE ||  $_SESSION['id'] == 12 )
			render_menu_item( "keygen.php", "images/key.png", "keygen" );
		
		render_menu_item( "keymod.php", "images/hammer.png", "keymod" );
		
		if ( $_SESSION['developer'] == TRUE )
			render_menu_item( "filemod.php", "images/brackets.png", "filemod" );
		
		if ( $_SESSION['reseller'] == TRUE )
			render_menu_item( "order.php", "images/cart.png", "order" );

		if ( $_SESSION['admin'] == TRUE ||  $_SESSION['id'] == 12 )
			render_menu_item( "cache.php", "images/arrow_refresh_small.png", "cache" );
	
		if ( $_SESSION['admin'] == TRUE )
			render_menu_item( "pricing.php", "images/money_dollar.png", "pricing" );	
		
		render_menu_item( "logout.php", "images/key.png", "logout" );
		
		
		//render_menu_item( "inventory.php", "images/box.png", "inventory" );
		?>
	</ul>
  </div>
</nav>

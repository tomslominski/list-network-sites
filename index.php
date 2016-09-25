<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title><?php echo get_site_option('site_name'); ?> | <?php _e( 'Site list', 'list-network-sites' ); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<?php
	$sorting_method_cookie = key_exists( 'lnsSortingMethod', $_COOKIE ) ? $_COOKIE[ 'lnsSortingMethod' ] : null;
	$sorting_method_setting = get_theme_mod( 'ls_sorting_method' );

	if( !empty( $sorting_method_cookie ) ) {
		$sorting_method = $sorting_method_cookie;
	} elseif( !empty( $sorting_method_setting ) ) {
		$sorting_method = $sorting_method_setting;
	} else {
		$sorting_method = 'id';
	}

	$sorting_order_cookie = key_exists( 'lnsSortingOrder', $_COOKIE ) ? $_COOKIE[ 'lnsSortingOrder' ] : null;
	$sorting_order_setting = get_theme_mod( 'ls_sorting_order' );

	if( !empty( $sorting_order_cookie ) ) {
		$sorting_order = $sorting_order_cookie;
	} elseif( !empty( $sorting_order_setting ) ) {
		$sorting_order = $sorting_order_setting;
	} else {
		$sorting_order = 'ascending';
	}
?>

<body <?php body_class(); ?> data-lns-sorting-method="<?php echo $sorting_method; ?>" data-lns-sorting-order="<?php echo $sorting_order; ?>">

	<div class="container">

		<header>

			<h1 class="site-name"><?php echo get_site_option('site_name'); ?></h1>

			<div class="tools">
				<div class="links">
					<h2><?php _e( 'Network Management', 'list-network-sites' ); ?></h2>
					<a href="<?php echo network_admin_url( 'site-new.php' ); ?>" class="link"><?php _e( 'Add New Site', 'list-network-sites' ); ?></a>
					<a href="<?php echo network_admin_url( 'users.php' ); ?>" class="link"><?php _e( 'All Users', 'list-network-sites' ); ?></a>
					<a href="<?php echo network_admin_url( 'themes.php' ); ?>" class="link"><?php _e( 'Themes', 'list-network-sites' ); ?></a>
					<a href="<?php echo network_admin_url( 'plugins.php' ); ?>" class="link"><?php _e( 'Plugins', 'list-network-sites' ); ?></a>
					<a href="<?php echo network_admin_url( 'settings.php' ); ?>" class="link"><?php _e( 'Network Settings', 'list-network-sites' ); ?></a>
				</div>

				<div class="sorting-method">
					<h2><?php _e( 'Sorting Method', 'list-network-sites' ); ?></h2>

					<select id="sorting-method">
						<option value="alphabetical" <?php selected( 'alphabetical', $sorting_method ); ?>><?php _e( 'Alphabetical', 'list-network-sites' ); ?></a>
						<option value="date_registered" <?php selected( 'date_registered', $sorting_method ); ?>><?php _e( 'Date Registered', 'list-network-sites' ); ?></a>
						<option value="date_updated" <?php selected( 'date_updated', $sorting_method ); ?>><?php _e( 'Date Updated', 'list-network-sites' ); ?></a>
						<option value="post_count" <?php selected( 'post_count', $sorting_method ); ?>><?php _e( 'Post Count', 'list-network-sites' ); ?></a>
						<option value="id" <?php selected( 'id', $sorting_method ); ?>><?php _e( 'ID', 'list-network-sites' ); ?></a>
					</select>
				</div>

				<div class="sorting-order">
					<h2><?php _e( 'Sorting Order', 'list-network-sites' ); ?></h2>

					<select id="sorting-method">
						<option value="ascending" <?php selected( 'ascending', $sorting_order ); ?>><?php _e( 'Ascending', 'list-network-sites' ); ?></a>
						<option value="descending" <?php selected( 'descending', $sorting_order ); ?>><?php _e( 'Descending', 'list-network-sites' ); ?></a>
					</select>
				</div>
			</div>

			<input type="search" placeholder="<?php _e( 'Search sites', 'list-network-sites' ); ?>" id="filter-field" class="search" autofocus>

		</header>

		<div class="items-wrapper">

			<div class="items-container">
				<?php
					$site_query = new List_Network_Sites( array(
						'sorting' => $sorting_method,
						'order' => $sorting_order,
						'page' => get_query_var( 'sites_page' ) ? absint( get_query_var( 'sites_page' ) ) : 1,
					) );

					echo $site_query->get_html();
				?>
			</div>

			<div class="items-overlay hide">
				<span class="loader">
			</div>

		</div>

		<footer>

			<p><?php _e( '<a href="https://github.com/tomslominski/wp-list-network-sites" target="_blank">List Network Sites</a> theme by <a href="http://tomslominski.net" target="_blank">Tom Slominski</a>.', 'list-network-sites' ); ?></p>

		</footer>

	</div>

	<?php wp_footer(); ?>

</body>

</html>

<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title><?php echo get_site_option('site_name'); ?> | <?php _e( 'Site list', 'list-network-sites' ); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<div class="container">

		<header>

			<h1 class="site-name"><?php echo get_site_option('site_name'); ?></h1>

			<div class="tools">
				<h2><?php _e( 'Network Management', 'list-network-sites' ); ?></h2>
				<a href="<?php echo network_admin_url( 'site-new.php' ); ?>" class="link"><?php _e( 'Add New Site', 'list-network-sites' ); ?></a>
				<a href="<?php echo network_admin_url( 'users.php' ); ?>" class="link"><?php _e( 'All Users', 'list-network-sites' ); ?></a>
				<a href="<?php echo network_admin_url( 'themes.php' ); ?>" class="link"><?php _e( 'Themes', 'list-network-sites' ); ?></a>
				<a href="<?php echo network_admin_url( 'plugins.php' ); ?>" class="link"><?php _e( 'Plugins', 'list-network-sites' ); ?></a>
				<a href="<?php echo network_admin_url( 'settings.php' ); ?>" class="link"><?php _e( 'Network Settings', 'list-network-sites' ); ?></a>
			</div>

			<input type="search" placeholder="<?php _e( 'Search sites', 'list-network-sites' ); ?>" id="filter-field" class="search" autofocus>

		</header>

		<div class="items-wrapper">

			<div class="items-container">
				<?php
					$site_query = new List_Network_Sites( array(
						'sorting' => get_theme_mod( 'ls_sorting_method' ),
						'order' => get_theme_mod( 'ls_sorting_order' ),
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

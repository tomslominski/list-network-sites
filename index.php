<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title><?php echo get_site_option('site_name'); ?> | <?php _e( 'Site list', 'list-network-sites' ); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body>

	<div class="container">

		<header>

			<h1 class="site-name"><?php echo get_site_option('site_name'); ?></h1>

			<input type="search" placeholder="<?php _e( 'Search sites', 'list-network-sites' ); ?>" id="filter-field" class="search" autofocus>

		</header>

		<div class="items">

			<?php

				$sites = wp_get_sites();

				foreach ($sites as $site) :

					$site = get_blog_details( $site['blog_id'] );

				?>
					<section class="item" data-name="<?php echo $site->blogname; ?>">

						<h2><?php echo $site->blogname; ?></h2>

						<div class="links">
							<a href="<?php echo get_admin_url( $site->blog_id ); ?>" class="link admin"><?php _e( 'Admin', 'list-network-sites' ); ?></a>
							<a href="<?php echo $site->siteurl; ?>" class="link site"><?php _e( 'Site', 'list-network-sites' ); ?></a>
						</div>

					</section>

				<?php

				endforeach;

			?>

			<p class="hide no-results"><?php _e( 'No results. Sorry.', 'list-network-sites' ); ?></p>

		</div>

		<footer>

			<p><?php _e( '<a href="https://github.com/tomslominski/wp-list-network-sites" target="_blank">List Network Sites</a> theme by <a href="http://tomslominski.net" target="_blank">Tom Slominski</a>.', 'list-network-sites' ); ?></p>

		</footer>

	</div>

	<?php wp_footer(); ?>

</body>

</html>

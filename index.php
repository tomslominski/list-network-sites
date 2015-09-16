<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Site list</title>
	<?php wp_head(); ?>
</head>

<body>

	<div class="container">

		<header>

			<h1><?php echo get_site_option('site_name'); ?></h1>

			<input type="search" placeholder="Search sites" id="filter-field" class="search">

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
							<a href="<?php echo get_admin_url( $site->blog_id ); ?>" class="link admin">Admin</a>
							<a href="<?php echo $site->siteurl; ?>" class="link site">Site</a>
						</div>

					</section>

				<?php

				endforeach;

			?>

			<p class="hide no-results">No results. Sorry.</p>

		</div>

		<footer>

			<p><a href="https://github.com/tomslominski/wp-list-network-sites" target="_blank">List Network Sites</a> theme by <a href="http://tomslominski.net" target="_blank">Tom Slominski</a>.</p>

		</footer>

	</div>

	<?php wp_footer(); ?>

</body>

</html>

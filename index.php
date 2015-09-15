<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Site list</title>
	<?php wp_head(); ?>
</head>

<body>

	<div class="container">

		<?php

			$sites = wp_get_sites();

			foreach ($sites as $site) :

				$site = get_blog_details( $site['blog_id'] );

			?>
				<div class="item">

					<h2><?php echo $site->blogname; ?></h2>
					
					<div class="links">
						<a href="<?php echo get_admin_url( $site->blog_id ); ?>" class="link admin">Admin</a>
						<a href="<?php echo $site->siteurl; ?>" class="link site">Site</a>
					</div>

				</div>

			<?php

			endforeach;

		?>

	</div>

	<?php wp_footer(); ?>

</body>

</html>

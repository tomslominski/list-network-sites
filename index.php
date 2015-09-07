<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Site list</title>
</head>

<body>

	<div class="container">

		<?php

			$sites = wp_get_sites();

			foreach ($sites as $site) :

				$site = get_blog_details( $site['blog_id'] );

			?>
				
				<h2><?php echo $site->blogname; ?></h2>
				<a href="<?php echo get_admin_url( $site->blog_id ); ?>">Admin</a>
				<a href="<?php echo $site->siteurl; ?>">Site</a>

			<?php

			endforeach;

		?>

	</div>

</body>

</html>

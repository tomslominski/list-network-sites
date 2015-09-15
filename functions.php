<?php

if( !function_exists( 'ls_load_styles' ) ) {

	function ls_load_styles() {

		wp_enqueue_style( 'ls_style', get_stylesheet_uri(), array( 'normalize', 'fonts' ) );

		wp_enqueue_style( 'normalize', 'https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.min.css' );

		wp_enqueue_style( 'fonts', 'https://fonts.googleapis.com/css?family=Open+Sans' );

	}

	add_action( 'wp_enqueue_scripts', 'ls_load_styles' );

}



?>

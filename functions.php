<?php

if( !function_exists( 'ls_load_styles' ) ) {

	function ls_load_styles() {

		wp_enqueue_style( 'ls_style', get_stylesheet_uri(), array( 'normalize', 'fonts' ) );

		wp_enqueue_style( 'normalize', 'https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.min.css' );

		wp_enqueue_style( 'fonts', 'https://fonts.googleapis.com/css?family=Open+Sans' );

	}

	add_action( 'wp_enqueue_scripts', 'ls_load_styles' );

}

if( !function_exists( 'ls_load_scripts' ) ) {

	function ls_load_scripts() {

		wp_enqueue_style( 'jquery' );

		wp_enqueue_script( 'ls_app', get_template_directory_uri() . '/app.js', array( 'jquery' ), false, true );

	}

	add_action( 'wp_enqueue_scripts', 'ls_load_scripts' );

}

if( !function_exists( 'ls_init' ) ) {

	function ls_init() {

		load_theme_textdomain( 'list-network-sites', get_template_directory() . '/languages' );

	}

	add_action( 'after_setup_theme', 'ls_init' );

}

if( !function_exists( 'ls_customizer' ) ) {

	/**
	 * Register settings to be displayed in the WP customiser
	 *
	 * @since 1.1
	 * @param \WP_Customize_Manager $wp_customize Instance of WP Customizer
	 */
	function ls_customizer( $wp_customize ) {

		// Add section for settings
		$wp_customize->add_section( 'ls_main' , array(
		    'title'      => __( 'List Network Sites', 'list-network-sites' ),
		) );

		// Add settings
		$wp_customize->add_setting( 'ls_sorting_method' , array(
			'default'     => 'alphabetical'
		) );

		$wp_customize->add_setting( 'ls_sorting_order' , array(
			'default'     => 'ascending'
		) );

		// Add controls
		$wp_customize->add_control( 'ls_sorting_method_control', 
			array(
				'label'    => __( 'Sorting method', 'list-network-sites' ),
				'section'  => 'ls_main',
				'settings' => 'ls_sorting_method',
				'type'     => 'select',
				'choices'  => array(
					'alphabetical'  => __( 'Alphabetical', 'list-network-sites' ),
					'date_registered' => __( 'Date registered', 'list-network-sites' ),
					'date_updated' => __( 'Date updated', 'list-network-sites' ),
					'post_count' => __( 'Post count', 'list-network-sites' ),
					'id' => __( 'ID', 'list-network-sites' ),
				),
			)
		);

		$wp_customize->add_control( 'ls_sorting_order_control', 
			array(
				'label'    => __( 'Sorting order', 'list-network-sites' ),
				'section'  => 'ls_main',
				'settings' => 'ls_sorting_order',
				'type'     => 'radio',
				'choices'  => array(
					'ascending'  => __( 'Ascending', 'list-network-sites' ),
					'descending' => __( 'Descending', 'list-network-sites' ),
				),
			)
		);

	}

	add_action( 'customize_register', 'ls_customizer' );

}

if( !function_exists( 'ls_get_sites' ) ) {

	/**
	 * Get a list of WordPress blogs for the current site
	 *
	 * @since 1.1
	 */
	function ls_get_sites() {

		$sites = wp_get_sites(); // Get array of sites
		$sites_detailed = array(); // Define a new array for sites

		// Build a new array with more useful information about the sites
		foreach ($sites as $site) {
			$sites_detailed[] = get_blog_details( $site['blog_id'] );
		}

		// Overwrite the original array
		$sites = $sites_detailed;

		// Get rid of the now useless array
		unset( $sites_detailed );

		return $sites;

	}

}

if( !function_exists( 'ls_sort_sites' ) ) {

	/**
	 * Sort a list of WordPress blogs
	 *
	 * @since 1.1
	 * @param array $sites List of blogs from {@see ls_get_sites()}
	 */
	function ls_sort_sites( $sites, $sorting_method, $sorting_order ) {

		switch ( $sorting_method ) {

			case 'alphabetical':

				if ( $sorting_order == 'descending' ) {
					usort( $sites, function( $a, $b ) {
						return strnatcasecmp( $b->blogname, $a->blogname );
					} );
				} elseif ( $sorting_order == 'ascending' ) {
					usort( $sites, function( $a, $b ) {
						return strnatcasecmp( $a->blogname, $b->blogname );
					} );
				}

				break;

			case 'date_registered':

				if ( $sorting_order == 'descending' ) {
					usort( $sites, function($a, $b) {
						return strtotime( $b->registered ) - strtotime( $a->registered );

					} );
				} elseif ( $sorting_order == 'ascending' ) {
					usort( $sites, function($a, $b) {
						return strtotime( $a->registered ) - strtotime( $b->registered );
					} );
				}

				break;

			case 'date_updated':

				if ( $sorting_order == 'descending' ) {
					usort( $sites, function($a, $b) {
						return strtotime( $b->last_updated ) - strtotime( $a->last_updated );
					} );
				} elseif ( $sorting_order == 'ascending' ) {
					usort( $sites, function( $a, $b ) {
						return strtotime( $a->last_updated ) - strtotime( $b->last_updated );
					} );
				}

				break;

			case 'post_count':

				if ( $sorting_order == 'descending' ) {
					usort( $sites, function($a, $b) {
						return $b->post_count - $a->post_count;
					} );
				} elseif ( $sorting_order == 'ascending' ) {
					usort( $sites, function($a, $b) {
						return $a->post_count - $b->post_count;
					} );
				}

				break;

			case 'id':
			default:

				if ( $sorting_order == 'descending' ) {
					usort( $sites, function($a, $b) {
						return $b->blog_id - $a->blog_id;
					} );
				} elseif ( $sorting_order == 'ascending' ) {
					usort( $sites, function($a, $b) {
						return $a->blog_id - $b->blog_id;
					} );
				}

				break;

		}

		return $sites;

	}

}

?>

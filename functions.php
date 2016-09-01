<?php

if( !function_exists( 'ls_load_styles' ) ) {

	/**
	 * Load CSS styles which the theme depends on
	 *
	 * @since 1.0
	 */
	function ls_load_styles() {
		wp_enqueue_style( 'ls_style', get_stylesheet_uri() );
	}

	add_action( 'wp_enqueue_scripts', 'ls_load_styles' );

}

if( !function_exists( 'ls_load_scripts' ) ) {

	/**
	 * Load JS scripts which the theme depends on
	 *
	 * @since 1.0
	 */
	function ls_load_scripts() {

		wp_enqueue_style( 'jquery' );

		wp_enqueue_script( 'ls_app', get_template_directory_uri() . '/app.js', array( 'jquery' ), false, true );

	}

	add_action( 'wp_enqueue_scripts', 'ls_load_scripts' );

}

if( !function_exists( 'ls_init' ) ) {

	/**
	 * Initialise other parts of the theme - textdomain etc.
	 *
	 * @since 1.0
	 */
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

		if( function_exists( 'get_sites' ) ) {
			$sites = get_sites(); // Get array of sites
		} else {
			$sites = wp_get_sites(); // Get array of sites
		}

		$sites_detailed = array(); // Define a new array for sites

		// Build a new array with more useful information about the sites
		foreach ($sites as $site) {
			if( function_exists( 'get_sites' ) ) {
				$sites_detailed[] = get_blog_details( $site->blog_id );
			} else {
				$sites_detailed[] = get_blog_details( $site['blog_id'] );
			}
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
	 * @param string $sorting_method Data to sort by (alphabetical/date_registered/date_updated/post_count/id)
	 * @param string $shorting_order Order to sort by (descending/ascending)
	 */
	function ls_sort_sites( $sites, $sorting_method = 'id', $sorting_order = 'descending' ) {

		switch ( $sorting_method ) {

			case 'alphabetical':

				if ( $sorting_order == 'descending' ) {
					usort( $sites, 'ls_sort_sites_alphabetical_descending' );
				} elseif ( $sorting_order == 'ascending' ) {
					usort( $sites, 'ls_sort_sites_alphabetical_ascending' );
				}

				break;

			case 'date_registered':

				if ( $sorting_order == 'descending' ) {
					usort( $sites, 'ls_sort_sites_date_registered_descending' );
				} elseif ( $sorting_order == 'ascending' ) {
					usort( $sites, 'ls_sort_sites_date_registered_ascending' );
				}

				break;

			case 'date_updated':

				if ( $sorting_order == 'descending' ) {
					usort( $sites, 'ls_sort_sites_date_updated_descending' );
				} elseif ( $sorting_order == 'ascending' ) {
					usort( $sites, 'ls_sort_sites_date_updated_ascending' );
				}

				break;

			case 'post_count':

				if ( $sorting_order == 'descending' ) {
					usort( $sites, 'ls_sort_sites_post_count_descending' );
				} elseif ( $sorting_order == 'ascending' ) {
					usort( $sites, 'ls_sort_sites_post_count_ascending' );
				}

				break;

			case 'id':
			default:

				if ( $sorting_order == 'descending' ) {
					usort( $sites, 'ls_sort_sites_id_descending' );
				} elseif ( $sorting_order == 'ascending' ) {
					usort( $sites, 'ls_sort_sites_id_ascending' );
				}

				break;

		}

		return $sites;

	}

	/**
	 * usort() function for sorting sites alphabetically in descending order.
	 * To be used in {@see ls_sort_sites()}.
	 *
	 * @since 1.2
	 * @param string $a First value to be compared
	 * @param string $b Second value to be compared
	 */
	function ls_sort_sites_alphabetical_descending( $a, $b ) {
		return strnatcasecmp( $b->blogname, $a->blogname );
	}

	/**
	 * usort() function for sorting sites alphabetically in ascending order.
	 * To be used in {@see ls_sort_sites()}.
	 *
	 * @since 1.2
	 * @param string $a First value to be compared
	 * @param string $b Second value to be compared
	 */
	function ls_sort_sites_alphabetical_ascending( $a, $b ) {
		return strnatcasecmp( $a->blogname, $b->blogname );
	}

	/**
	 * usort() function for sorting sites by date registered in descending order.
	 * To be used in {@see ls_sort_sites()}.
	 *
	 * @since 1.2
	 * @param string $a First value to be compared
	 * @param string $b Second value to be compared
	 */
	function ls_sort_sites_date_registered_descending($a, $b) {
		return strtotime( $b->registered ) - strtotime( $a->registered );
	}

	/**
	 * usort() function for sorting sites by date registered in ascending order.
	 * To be used in {@see ls_sort_sites()}.
	 *
	 * @since 1.2
	 * @param string $a First value to be compared
	 * @param string $b Second value to be compared
	 */
	function ls_sort_sites_date_registered_ascending($a, $b) {
		return strtotime( $a->registered ) - strtotime( $b->registered );
	}

	/**
	 * usort() function for sorting sites by date updated in descending order.
	 * To be used in {@see ls_sort_sites()}.
	 *
	 * @since 1.2
	 * @param string $a First value to be compared
	 * @param string $b Second value to be compared
	 */
	function ls_sort_sites_date_updated_descending($a, $b) {
		return strtotime( $b->last_updated ) - strtotime( $a->last_updated );
	}

	/**
	 * usort() function for sorting sites by date updated in ascending order.
	 * To be used in {@see ls_sort_sites()}.
	 *
	 * @since 1.2
	 * @param string $a First value to be compared
	 * @param string $b Second value to be compared
	 */
	function ls_sort_sites_date_updated_ascending( $a, $b ) {
		return strtotime( $a->last_updated ) - strtotime( $b->last_updated );
	}

	/**
	 * usort() function for sorting sites by post count in descending order.
	 * To be used in {@see ls_sort_sites()}.
	 *
	 * @since 1.2
	 * @param string $a First value to be compared
	 * @param string $b Second value to be compared
	 */
	function ls_sort_sites_post_count_descending($a, $b) {
		return $b->post_count - $a->post_count;
	}

	/**
	 * usort() function for sorting sites by post count in ascending order.
	 * To be used in {@see ls_sort_sites()}.
	 *
	 * @since 1.2
	 * @param string $a First value to be compared
	 * @param string $b Second value to be compared
	 */
	function ls_sort_sites_post_count_ascending($a, $b) {
		return $a->post_count - $b->post_count;
	}

	/**
	 * usort() function for sorting sites by site ID in descending order.
	 * To be used in {@see ls_sort_sites()}.
	 *
	 * @since 1.2
	 * @param string $a First value to be compared
	 * @param string $b Second value to be compared
	 */
	function ls_sort_sites_id_descending($a, $b) {
		return $b->blog_id - $a->blog_id;
	}

	/**
	 * usort() function for sorting sites by site ID in ascending order.
	 * To be used in {@see ls_sort_sites()}.
	 *
	 * @since 1.2
	 * @param string $a First value to be compared
	 * @param string $b Second value to be compared
	 */
	function ls_sort_sites_id_ascending($a, $b) {
		return $a->blog_id - $b->blog_id;
	}

}

?>

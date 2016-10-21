<?php

require_once( 'class-list-network-sites.php' );

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

		wp_register_script( 'lns_utils', get_template_directory_uri() . '/assets/js/utils.js', array( 'jquery' ), false, true );

		wp_register_script( 'lns_app', get_template_directory_uri() . '/assets/js/app.js', array( 'jquery', 'lns_utils' ), false, true );
		$data = array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'siteUrl' => trailingslashit( site_url() )
		);
		wp_localize_script( 'lns_app', 'lnsi18n', $data );
		wp_enqueue_script( 'lns_app' );

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
	 * @since 2.0
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

		$wp_customize->add_setting( 'ls_sites_per_page' , array(
			'default'     => 10
		) );

		$wp_customize->add_setting( 'ls_display_network_links' , array(
			'default'     => 'display'
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
				'type'     => 'select',
				'choices'  => array(
					'ascending'  => __( 'Ascending', 'list-network-sites' ),
					'descending' => __( 'Descending', 'list-network-sites' ),
				),
			)
		);

		$wp_customize->add_control( 'ls_sites_per_page_control',
			array(
				'label'    => __( 'Sites per page', 'list-network-sites' ),
				'section'  => 'ls_main',
				'settings' => 'ls_sites_per_page',
				'type'     => 'number',
			)
		);

		$wp_customize->add_control( 'ls_display_network_links_control',
			array(
				'label'    => __( '"Network Management" links', 'list-network-sites' ),
				'section'  => 'ls_main',
				'settings' => 'ls_display_network_links',
				'type'     => 'select',
				'choices'  => array(
					'on'  => __( 'Display', 'list-network-sites' ),
					'off' => __( 'Hide', 'list-network-sites' ),
				)
			)
		);

	}

	add_action( 'customize_register', 'ls_customizer' );

}

if( !function_exists( 'lns_filter_query_vars' ) ) {

	/**
	 * Filter query variables to enable pagination.
	 *
	 * @param array $vars Array of existing query variables.
	 * @since 2.0
	 */
	function lns_filter_query_vars( $vars ) {

		$vars[] = 'sites_page';
		$vars[] = 'sites_sorting_method';
		$vars[] = 'sites_sorting_order';
		$vars[] = 'sites_search';
		return $vars;

	}

	add_filter( 'query_vars', 'lns_filter_query_vars' );

}

if( !function_exists( 'lns_populate_query_vars' ) ) {

	/**
	 * Set up pretty permalinks for the sites pagination. The structure
	 * is: example.com/sites/[sorting_method]/[sorting_order]/[page]/[search]
	 *
	 * @since 2.0
	 */
	function lns_populate_query_vars() {

		add_rewrite_rule( 'sites\/(alphabetical|date_registered|date_updated|post_count|id)\/(ascending|descending)\/([0-9]{1,})\/([^\/]+)', 'index.php?&sites_sorting_method=$matches[1]&sites_sorting_order=$matches[2]&sites_page=$matches[3]&sites_search=$matches[4]', 'top' );
		add_rewrite_rule( 'sites\/(alphabetical|date_registered|date_updated|post_count|id)\/(ascending|descending)\/([0-9]{1,})', 'index.php?&sites_sorting_method=$matches[1]&sites_sorting_order=$matches[2]&sites_page=$matches[3]', 'top' );
		add_rewrite_rule( 'sites\/(alphabetical|date_registered|date_updated|post_count|id)\/(ascending|descending)', 'index.php?&sites_sorting_method=$matches[1]&sites_sorting_order=$matches[2]', 'top' );
		add_rewrite_rule( 'sites\/(alphabetical|date_registered|date_updated|post_count|id)', 'index.php?&sites_sorting_method=$matches[1]', 'top' );

	}

	add_filter( 'init', 'lns_populate_query_vars' );

}

if( !function_exists( 'lns_js_get_sites' ) ) {

	/**
	 * WP AJAX function for getting the HTML of the list of sites.
	 * Called every time the user filters the list of sites.
	 *
	 * @since 2.0
	 */
	function lns_js_get_sites() {

		$site_query = new List_Network_Sites( array(
			'sorting' => !empty( $_POST['sorting'] ) ? $_POST['sorting'] : 'alphabetical',
			'order' => !empty( $_POST['order'] ) ? $_POST['order'] : 'ascending',
			'page' => !empty( $_POST['page'] ) ? $_POST['page'] : 1,
			'search' => isset( $_POST['search_value'] ) ? $_POST['search_value'] : false,
		) );

		echo $site_query->get_html();

		wp_die();

	}

	add_action( 'wp_ajax_lns_get_sites', 'lns_js_get_sites' );
	add_action( 'wp_ajax_nopriv_lns_get_sites', 'lns_js_get_sites' );

}

if( !function_exists( 'lns_redirect_post' ) ) {

	/**
     * Function which redirects non-JS users to the correct
	 * static sites list page.
     *
     * @param int $current The current page of sites being displayed.
     * @return string Pagination HTML.
     * @since 2.0
     */
	function lns_redirect_post() {

		$url = lns_generate_url( $_POST );
	    wp_redirect( $url );
		exit;

	}

	add_action( 'admin_post_nopriv_lns_sites_form', 'lns_redirect_post' );
	add_action( 'admin_post_lns_sites_form', 'lns_redirect_post' );

}

if( !function_exists( 'lns_generate_url' ) ) {

	/**
     * Function for generating a URL to a list of sites, including
	 * filtering options, paging and search options.
     *
     * @param int $current The current page of sites being displayed.
     * @return string Pagination HTML.
     * @since 2.0
     */
	function lns_generate_url( $input_params = array() ) {

		$arrays = array(
			'static_defaults' => array(
				'sorting_method' => 'alphabetical',
				'sorting_order' => 'ascending',
				'page' => 1,
			),
			'site_defaults' => array(
				'sorting_method' => get_theme_mod( 'ls_sorting_method' ),
				'sorting_order' => get_theme_mod( 'ls_sorting_order' ),
			),
			'cookie_defaults' => array(
				'sorting_method' => key_exists( 'lnsSortingMethod', $_COOKIE ) ? $_COOKIE[ 'lnsSortingMethod' ] : null,
				'sorting_order' => key_exists( 'lnsSortingOrder', $_COOKIE ) ? $_COOKIE[ 'lnsSortingOrder' ] : null,
			),
			'query_vars' => array(
				'sorting_method' => get_query_var( 'sites_sorting_method' ),
				'sorting_order' => get_query_var( 'sites_sorting_order' ),
				'page' => get_query_var( 'sites_page' ),
				'search' => get_query_var( 'sites_search' )
			),
			'input_params' => $input_params
		);

		foreach( $arrays as $array ) {
			if( is_array( $array ) ) {
				foreach( $array as $param_name => $param_value ) {
					// Only add valid values to the $params array
					if( !in_array( $param_name, array( 'sorting_method', 'sorting_order', 'page', 'search' ) ) ) {
						continue;
					}

					// Overwrite values of lesser importance if the array value is not empty
					if( !empty( $param_value ) ) {
						$params[$param_name] = $param_value;
					}
				}
			}
		}

		$url = trailingslashit( get_site_url() ) . 'sites/';

		foreach ( $params as $param => $value ) {
			// Fill values with existing values of empty
			if( empty( $value ) ) {
				$params[$param] = get_query_var( $param );
			}

			// Special case
			if( $param == 'page' && $value == 1 && empty( $params['search'] ) ) {
				continue;
			}

			// Build URL
			if( !empty( $value ) ) {
				$url .= $value . '/';
			}
		}

		return $url;

	}

}

if( !function_exists( 'lns_activation' ) ) {

	/**
     * Function to be run when the theme is activated. Resets
	 * rewrite rules.
     *
     * @since 2.0
     */
	function lns_activation() {
		flush_rewrite_rules();
	}

	add_action( 'after_switch_theme', 'lns_activation' );

}
?>

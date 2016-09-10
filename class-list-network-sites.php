<?php

/**
 * Class for generating an array of sites in a network, with options
 * to sort the array. Also provides supporting functions and pagination.
 *
 * @since 1.1
 */
class List_Network_Sites {

    /**
     * Arguments used to create the site query.
     *
     * @var array $args
     * @since 1.1
     */
    public $args = null;

    /**
     * Sites found by the site query.
     *
     * @var array $sites
     * @since 1.1
     */
    public $sites = null;

    /**
     * Sites found by the site query.
     *
     * @var int $total_sites
     * @since 1.1
     */
    public $total_sites = null;

    /**
     * Constructor which sets object variables and automatically
     * gets the requested sites.
     *
     * @param array $args Arguments which describe what sites should be queried.
     * @since 1.1
     */
    public function __construct( $args = array() ) {

        // Merge with defaults
        $this->args = array_merge( array(
            'offset' => 0,
            'sorting' => 'id',
            'order' => 'descending',
            'include_primary' => false,
            'posts_per_page' => 5,
            'paged' => 1
        ), $args );

        // Error checking
        if( !in_array( $this->args['sorting'], array( 'alphabetical', 'date_registered', 'date_updated', 'post_count', 'id' ) ) ) {
            throw new Exception( 'Sorting type ' . $this->args['sorting'] . ' unavailable.' );
        }

        if( !in_array( $this->args['order'], array( 'descending', 'ascending' ) ) ) {
            throw new Exception( 'Sorting order ' . $this->args['order'] . ' unavailable.' );
        }

        $this->get_sites();

    }

    /**
     * Gets sites, filters them, sorts them and deals with paging.
     *
     * @return array An array of sites.
     * @since 1.1
     */
    public function get_sites() {

        // Pick function depending on which version of WP is used
        if( function_exists( 'get_sites' ) ) {
			$this->sites = get_sites();
		} else {
			$this->sites = wp_get_sites();
		}

        // Filter the sites to remove the primary site
        if( $this->args['include_primary'] == false ) {
            foreach ( $this->sites as $id => $site ) {
                if( $site->blog_id == BLOG_ID_CURRENT_SITE ) {
                    unset( $this->sites[$id] );
                }
            }
        }

        // Write vars
        $this->total_sites = count( $this->sites );

        // Sort sites
        usort( $this->sites, array( $this, 'sorting_' . $this->args['sorting'] . '_' . $this->args['order'] ) );

        // Paging
        $offset = $this->args['posts_per_page'] * $this->args['paged'] - $this->args['posts_per_page'];
        $this->sites = array_slice( $this->sites, $offset, 5 );

		return $this->sites;

    }

    /**
     * Returns true if this query has found any sites.
     *
     * @return bool Whether this query has found any sites.
     * @since 1.1
     */
    public function has_sites() {

        if( count( $this->sites ) > 0 ) {
            return true;
        }

        return false;

    }

    /**
     * Returns the sites array, usually for iterating over and
     * displaying in the theme.
     *
     * @return array The sites array.
     * @since 1.1
     */
    public function return_sites() {
        return $this->sites;
    }

    /**
     * Maximum possible number of pages for the amount of pages found
     * in the query. Used for pagination.
     *
     * @return int Number of pages.
     * @since 1.1
     */
    public function get_max_num_pages() {
        return ceil( $this->total_sites / $this->args['posts_per_page'] );
    }

    /**
	 * usort() function for sorting sites alphabetically in descending order.
	 * To be used in {@see List_Network_Sites::get_sites()}.
	 *
	 * @since 1.1
	 * @param string $a First value to be compared
	 * @param string $b Second value to be compared
	 */
	function sorting_alphabetical_descending( $a, $b ) {
		return strnatcasecmp( $b->blogname, $a->blogname );
	}

	/**
	 * usort() function for sorting sites alphabetically in ascending order.
	 * To be used in {@see List_Network_Sites::get_sites()}.
	 *
	 * @since 1.1
	 * @param string $a First value to be compared
	 * @param string $b Second value to be compared
	 */
	function sorting_alphabetical_ascending( $a, $b ) {
		return strnatcasecmp( $a->blogname, $b->blogname );
	}

	/**
	 * usort() function for sorting sites by date registered in descending order.
	 * To be used in {@see List_Network_Sites::get_sites()}.
	 *
	 * @since 1.1
	 * @param string $a First value to be compared
	 * @param string $b Second value to be compared
	 */
	function sorting_date_registered_descending($a, $b) {
		return strtotime( $b->registered ) - strtotime( $a->registered );
	}

	/**
	 * usort() function for sorting sites by date registered in ascending order.
	 * To be used in {@see List_Network_Sites::get_sites()}.
	 *
	 * @since 1.1
	 * @param string $a First value to be compared
	 * @param string $b Second value to be compared
	 */
	function sorting_date_registered_ascending($a, $b) {
		return strtotime( $a->registered ) - strtotime( $b->registered );
	}

	/**
	 * usort() function for sorting sites by date updated in descending order.
	 * To be used in {@see List_Network_Sites::get_sites()}.
	 *
	 * @since 1.1
	 * @param string $a First value to be compared
	 * @param string $b Second value to be compared
	 */
	function sorting_date_updated_descending($a, $b) {
		return strtotime( $b->last_updated ) - strtotime( $a->last_updated );
	}

	/**
	 * usort() function for sorting sites by date updated in ascending order.
	 * To be used in {@see List_Network_Sites::get_sites()}.
	 *
	 * @since 1.1
	 * @param string $a First value to be compared
	 * @param string $b Second value to be compared
	 */
	function sorting_date_updated_ascending( $a, $b ) {
		return strtotime( $a->last_updated ) - strtotime( $b->last_updated );
	}

	/**
	 * usort() function for sorting sites by post count in descending order.
	 * To be used in {@see List_Network_Sites::get_sites()}.
	 *
	 * @since 1.1
	 * @param string $a First value to be compared
	 * @param string $b Second value to be compared
	 */
	function sorting_post_count_descending($a, $b) {
		return $b->post_count - $a->post_count;
	}

	/**
	 * usort() function for sorting sites by post count in ascending order.
	 * To be used in {@see List_Network_Sites::get_sites()}.
	 *
	 * @since 1.1
	 * @param string $a First value to be compared
	 * @param string $b Second value to be compared
	 */
	function sorting_post_count_ascending($a, $b) {
		return $a->post_count - $b->post_count;
	}

	/**
	 * usort() function for sorting sites by site ID in descending order.
	 * To be used in {@see List_Network_Sites::get_sites()}.
	 *
	 * @since 1.1
	 * @param string $a First value to be compared
	 * @param string $b Second value to be compared
	 */
	function sorting_id_descending($a, $b) {
		return $b->blog_id - $a->blog_id;
	}

	/**
	 * usort() function for sorting sites by site ID in ascending order.
	 * To be used in {@see List_Network_Sites::get_sites()}.
	 *
	 * @since 1.1
	 * @param string $a First value to be compared
	 * @param string $b Second value to be compared
	 */
	function sorting_id_ascending($a, $b) {
		return $a->blog_id - $b->blog_id;
	}

}

?>

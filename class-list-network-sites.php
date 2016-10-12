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

        $per_page = get_theme_mod( 'ls_sites_per_page' );

        // Merge with defaults
        $this->args = array_merge( array(
            'offset' => 0,
            'sorting' => 'id',
            'order' => 'descending',
            'include_primary' => true,
            'posts_per_page' => !empty( $per_page ) ? $per_page : 10,
            'page' => 1,
            'search' => false
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

        // Backwards compatibility
        foreach ( $this->sites as $id => $site ) {
            if( function_exists( 'get_sites' ) ) {
    			$this->sites[$id] = get_blog_details( $site->blog_id );
    		} else {
    			$this->sites[$id] = get_blog_details( $site['blog_id'] );
    		}
        }

        // Filter the sites
        foreach ( $this->sites as $id => $site ) {
            // Remove primary site
            if( $this->args['include_primary'] == false && $site->blog_id == BLOG_ID_CURRENT_SITE ) {
                unset( $this->sites[$id] );
            }

            // Filter by search term
            if( $this->args['search'] != false ) {
                $name = strtolower( $site->blogname );
                $search = strtolower( $this->args['search'] );

                if( !strstr( $name, $search ) ) {
                    unset( $this->sites[$id] );
                }
            }
        }

        // Write vars
        $this->total_sites = count( $this->sites );

        // Sort sites
        usort( $this->sites, array( $this, 'sorting_' . $this->args['sorting'] . '_' . $this->args['order'] ) );

        // Paging
        $offset = $this->args['posts_per_page'] * $this->args['page'] - $this->args['posts_per_page'];
        $this->sites = array_slice( $this->sites, $offset, $this->args['posts_per_page'] );

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
     * Returns the HTML for the site list, including the pagination
     * {@see List_Network_Sites::get_pagination()}.
     *
     * @return string Site list HTML.
     * @since 1.1
     */
    public function get_html() {

    	ob_start();

        if( empty( $this->sites ) ) : ?>
            <p class="no-results"><?php _e( 'No results. Sorry.', 'list-network-sites' ); ?></p>
        <?php endif;
    	?>

    	<div class="items">
    		<?php foreach ($this->sites as $site) : ?>
                <?php switch_to_blog( $site->blog_id ); ?>

    			<section class="item" data-name="<?php echo get_bloginfo( 'name' ); ?>">

    				<h2><?php echo get_bloginfo( 'name' ); ?></h2>

    				<div class="links">
    					<a href="<?php echo get_admin_url( get_current_blog_id() ); ?>" class="link admin"><?php _e( 'Admin', 'list-network-sites' ); ?></a>
                        <a href="<?php echo get_bloginfo( 'url' ); ?>" class="link site"><?php _e( 'Site', 'list-network-sites' ); ?></a>
                        <?php do_action( 'lns_print_site_links', get_current_blog_id() ); ?>
    				</div>

    			</section>

        		<?php restore_current_blog(); ?>
            <?php endforeach; ?>
    	</div>

    	<?php

        echo $this->get_pagination( $this->args['page'] );

    	return ob_get_clean();
    }

    /**
     * Returns the HTML for pagination used below the site list.
     *
     * @param int $current The current page of sites being displayed.
     * @return string Pagination HTML.
     * @since 1.1
     */
    public function get_pagination( $current = 1 ) {

        if( $this->get_max_num_pages() < 2 ) {
            return false;
        }

        $maximum = $this->get_max_num_pages();

        $previous = $current - 1;
        $next = $current + 1;

        ob_start();

        ?>

            <div class="pagination">

                <?php if( $current > 1 ) : ?>
                    <div class="section back-buttons">
                        <a href="<?php echo lns_generate_url( array( 'page' => 1 ) ); ?>" data-page="1" class="button first" title="<?php _e( 'Go to the first page', 'list-network-sites' ); ?>">&laquo;</a>
                        <a href="<?php echo lns_generate_url( array( 'page' => $previous ) ); ?>" data-page="<?php echo $previous; ?>" class="button previous" title="<?php printf( __( 'Go to the page %d', 'list-network-sites' ), $current - 1 ); ?>">&lsaquo;</a>
                    </div>
                <?php endif; ?>

                <div class="section pager">
                    <form class="pager-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                        <input type="hidden" name="action" value="lns_sites_form">

                        <?php
                            $input = '<input type="number" min="1" max="' . $maximum . '" value="' . $current . '" name="page">';

                            printf( __( 'Page %1$s of %2$d', 'list-network-sites' ), $input, $maximum );
                        ?>
                    </form>
                </div>

                <?php if( $current < $maximum ) : ?>
                    <div class="section next-buttons">
                        <a href="<?php echo lns_generate_url( array( 'page' => $next ) ); ?>" data-page="<?php echo $next; ?>" class="button next" title="<?php printf( __( 'Go to the page %d', 'list-network-sites' ), $current + 1 ); ?>">&rsaquo;</a>
                        <a href="<?php echo lns_generate_url( array( 'page' => $maximum ) ); ?>" data-page="<?php echo $maximum; ?>" class="button last" title="<?php _e( 'Go to the last page', 'list-network-sites' ); ?>">&raquo;</a>
                    </div>
                <?php endif; ?>

            </div>

        <?php

        return ob_get_clean();

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

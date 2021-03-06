/* ============================================================ *\
	List Network Sites by Tom Slominski
	-----------------------------------
	Main site scripts.

	https://github.com/tomslominski/wp-list-network-sites/
\* ============================================================ */
jQuery(document).ready(function( $ ) {

	var LNS = {

		/**
		 * Main function for getting the HTML of a list of sites
		 * based on search criteria entered on the page.
		 */
		getSites: function() {

			var data = {
				action: 'lns_get_sites',
				search_value: $( 'body' ).data( 'lns-search' ),
				page: $( 'body' ).data( 'lns-page' ),
				sorting: $( 'body' ).data( 'lns-sorting-method' ),
				order: $( 'body' ).data( 'lns-sorting-order' )
			}

			$.ajax({
				dataType: 'html',
				data: data,
				method: 'POST',
				url: lnsi18n.ajaxUrl,
				beforeSend: function() {
					$( '.items-overlay' ).removeClass( 'hide' );
					$( '.items-overlay' ).addClass( 'show' );
				},
				success: function( response ) {
					$( '.items-container' ).html( response );
					$( '.items-overlay' ).addClass( 'hide' );
					$( '.items-overlay' ).removeClass( 'show' );

					$('html, body').animate( {
	                    scrollTop: $( '.items-container' ).offset().top - $( '#wpadminbar' ).height() - 10
	                }, 300);

					LNS.updateUrl();
				}
			});

		},

		/**
		 * Function for generating a site URL based on the current
		 * search criteria. It also updates the site URL at the end.
		 */
		updateUrl: function() {

			var data = {
				sorting_method: $( 'body' ).data( 'lns-sorting-method' ),
				sorting_order: $( 'body' ).data( 'lns-sorting-order' ),
				page: $( 'body' ).data( 'lns-page' ),
				search: $( 'body' ).data( 'lns-search' )
			}

			var url = lnsi18n.siteUrl + 'sites/';

			$.each( data, function( param, value ) {
				if( param == 'page' && value == 1 && !data.search ) { // SC
					return true;
				} else if( value ) {
					url += value + '/';
				}
			} );

			history.pushState( 'data', '', url );

		},

		handle: {

			/**
			 * Handler for the sorting method dropdown.
			 */
			sortingMethod: function() {

				var value = $( '#sorting-method' ).val();
				createCookie( 'lnsSortingMethod', value );
				$( 'body' ).data( 'lns-sorting-method', value );
				$( 'body' ).data( 'lns-page', 1 );

				LNS.getSites();

			},

			/**
			 * Handler for the sorting order dropdown.
			 */
			sortingOrder: function() {

				var value = $( '#sorting-order' ).val();
				createCookie( 'lnsSortingOrder', value );
				$( 'body' ).data( 'lns-sorting-order', value );
				$( 'body' ).data( 'lns-page', 1 );

				LNS.getSites();

			},

			/**
			 * Handler for the search field - delayed
			 */
			search: function() {

				var value = $( '#search-field' ).val();
				$( 'body' ).data( 'lns-search', value );
				$( 'body' ).data( 'lns-page', 1 );

				LNS.getSites();

			},

			/**
			 * When the form is submitted but JS is available
			 * there is no need to reload the page
			 */
			formSubmit: function( e ) {
				e.preventDefault();
			},

			/**
			 * Handler for the freeform page number input.
			 */
			pageInput: function() {

				event.preventDefault();

				var value = $( 'input', this ).val();
				$( 'body' ).data( 'lns-page', value );

				LNS.getSites();

			},

			/**
			 * Handler for the page buttons.
			 */
			pageButtons: function() {

				event.preventDefault();

				var value = $( this ).data( 'page' );
				$( 'body' ).data( 'lns-page', value );

				LNS.getSites();

			}

		}

	}

	/* ============================================
	  Event handlers
	============================================ */
	$( '#search-field' ).keyup(function() {
		delay( LNS.handle.search, 500 );
	});

	$( 'header .filtering' ).on( 'search', '#search-field', this, LNS.handle.search );
	$( '.container' ).on( 'submit', '.filtering-form', this, LNS.handle.formSubmit );
	$( '.items-wrapper' ).on( 'click', '.pagination .button', this, LNS.handle.pageButtons );
	$( '.items-wrapper' ).on( 'submit', '.pagination .pager-form', this, LNS.handle.pageInput );
	$( 'header .filtering' ).on( 'change', '#sorting-method', this, LNS.handle.sortingMethod );
	$( 'header .filtering' ).on( 'change', '#sorting-order', this, LNS.handle.sortingOrder );

});

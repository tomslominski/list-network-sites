jQuery(document).ready(function( $ ) {

	function getSites( args ) {

		var data = {
			action: 'lns_get_sites',
			search_value: $( '#filter-field' ).val(),
			page: typeof args != 'undefined' ? args.page : 1
		}

		$.ajax({
			dataType: 'html',
			data: data,
			method: 'POST',
			url: i18n.ajaxUrl,
			success: function( response ) {
				$( '.items-wrapper' ).html( response );
			}
		});

	}

	function paginationGetSites( eventData ) {

		event.preventDefault();

		if( $( eventData.target ).hasClass( 'pager-form' ) ) {
			var args = {
				page: $( eventData.target ).find( 'input' ).val()
			}
		} else if( $( eventData.target ).hasClass( 'button' ) ) {
			var args = $( eventData.target ).data();
		}

		getSites( args );
		history.pushState( 'data', '', i18n.siteUrl + '/sites_page/' + args.page + '/' );

	}

	// Source: http://stackoverflow.com/a/1909508
	var delay = (function(){
		var timer = 0;
		return function(callback, ms){
			clearTimeout (timer);
			timer = setTimeout(callback, ms);
		};
	})();

	$( '#filter-field' ).keyup(function() {
		delay( getSites, 500 );
	});

	$( '.container header' ).on( 'search', '#filter-field', this, getSites );
	$( '.items-wrapper' ).on( 'click', '.pagination .button', this, paginationGetSites );
	$( '.items-wrapper' ).on( 'submit', '.pagination .pager-form', this, paginationGetSites );
	$( '.container' ).on( 'click', '#ajax', this, getSites );

});

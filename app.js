jQuery(document).ready(function( $ ) {

	function getSites() {

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

				updateUrl();
			}
		});

	}

	function updateUrl() {

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
		
	}

	function handleSortingMethod( eventData ) {
		var value = $( eventData.target ).val();
		createCookie( 'lnsSortingMethod', value );
		$( 'body' ).data( 'lns-sorting-method', value );

		getSites();
	}

	function handleSortingOrder( eventData ) {
		var value = $( eventData.target ).val();
		createCookie( 'lnsSortingOrder', value );
		$( 'body' ).data( 'lns-sorting-order', value );

		getSites();
	}

	function handleSearch() {
		var value = $( '#filter-field' ).val();
		$( 'body' ).data( 'lns-search', value );
		$( 'body' ).data( 'lns-page', 1 );

		getSites();
	}

	function handlePageInput() {
		event.preventDefault();

		var value = $( 'input', this ).val();
		$( 'body' ).data( 'lns-page', value );

		getSites();
	}

	function handlePageButtons() {
		event.preventDefault();

		var value = $( this ).data( 'page' );
		$( 'body' ).data( 'lns-page', value );

		getSites();
	}

	// Scripts from QuirksMode
	// http://www.quirksmode.org/js/cookies.html#script
	function createCookie(name,value,days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires+"; path=/";
	}

	function readCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}

	function eraseCookie(name) {
		createCookie(name,"",-1);
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
		delay( handleSearch, 500 );
	});

	$( '.container header' ).on( 'search', '#filter-field', this, handleSearch );
	$( '.items-wrapper' ).on( 'click', '.pagination .button', this, handlePageButtons );
	$( '.items-wrapper' ).on( 'submit', '.pagination .pager-form', this, handlePageInput );
	$( '.container .tools' ).on( 'change', '.sorting-method', this, handleSortingMethod );
	$( '.container .tools' ).on( 'change', '.sorting-order', this, handleSortingOrder );

});

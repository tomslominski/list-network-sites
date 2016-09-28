jQuery(document).ready(function( $ ) {

	function getSites( args ) {

		var data = {
			action: 'lns_get_sites',
			search_value: $( '#filter-field' ).val(),
			page: typeof args != 'undefined' ? args.page : 1,
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
		history.pushState( 'data', '', lnsi18n.siteUrl + '/sites_page/' + args.page + '/' );

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

	// console.log( readCookie( 'wp-settings-time-1' ) );

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
	// $( '.container' ).on( 'click', '#ajax', this, getSites );
	$( '.container .tools' ).on( 'change', '.sorting-method', this, handleSortingMethod );
	$( '.container .tools' ).on( 'change', '.sorting-order', this, handleSortingOrder );

});

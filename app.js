jQuery(document).ready(function( $ ) {

	function filterElements() {

		var filterCriteria = $( '#filter-field' ).val();
		var regex = new RegExp( filterCriteria, 'gi' );

		if( filterCriteria.length > 0 ) {

			$( '.items .item' ).each( function() {
				if( $( this ).data('name').match( regex ) !== null ) {
					$( this ).addClass( 'show' );
				} else {
					$( this ).removeClass( 'show' );
					$( this ).addClass( 'hide' );
				}
			});

			if( $( '.items .item' ).hasClass('show') === false ) {
				$( '.items .no-results' ).addClass( 'show' );
			} else {
				$( '.items .no-results' ).removeClass( 'show' );
			}

		} else {

			$( '.items .item' ).removeClass( 'show hide' );

		}

	}

	$( '#filter-field' ).on( 'keyup', filterElements );
	
});

/** Enable dynamic webcomic navigation. */
jQuery( function( $ ) {
	/**
	 * Update any anchors with a webcomic-dynamic data attribute inside
	 * of a container with a webcomic-container data attribute.
	 */
	function update_hyperlinks( c ) {
		var container = typeof c !== 'undefined' ? '[data-webcomic-container="' + c + '"]' : '[data-webcomic-container]';
		
		$( container + ' [data-webcomic-dynamic][href]' ).each( function ( i, e ) {
			var hash = $( e ).data( 'webcomic-dynamic' ) + '/' + $( e ).parents( container ).data( 'webcomic-container' );
			
			$( e ).data( 'webcomic-permalink', $( e ).attr( 'href' ) );
			$( e ).attr( 'href', $.param.fragment( window.location.href, hash, 2 ) ).attr( 'data-webcomic-dynamic', hash );
		} );
	} update_hyperlinks();
	
	/** Handle dynamic webcomic navigation. */
	$( window ).on( 'hashchange', function() {
		if ( $.param.fragment() && 0 !== $.param.fragment().indexOf( '0' ) ) {
			var container = $.param.fragment().substr( $.param.fragment().lastIndexOf( '/' ) + 1, $.param.fragment().length );
			
			$.get( $( 'a[data-webcomic-dynamic="' + $.param.fragment() + '"]' ).data( 'webcomic-permalink' ), { webcomic_dynamic: container }, function( data ) {
				$( '[data-webcomic-container="' + container + '"]' ).html( data ).fadeIn( 'fast' );
				
				update_hyperlinks( container );
			} );
		}
	} ).trigger( 'hashchange' );
} );
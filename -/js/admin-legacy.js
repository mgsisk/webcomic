/** Automatically continue legacy upgrades. */
jQuery( function( $ ) {
	setTimeout( function() {
		$( '.webcomic-auto #upgrade_legacy' ).trigger( 'click' );
	}, 5000 );
	
	$( '.webcomic-auto #upgrade_legacy' ).on( 'click', function() {
		$( this ).hide();
		
		$( '.webcomic-auto-message' ).html( $( '[data-webcomic-upgrade-continue]' ).data( 'webcomic-upgrade-continue' ) );
	} );
} );
/** Automatically continue legacy upgrades. */
function webcomic_auto_upgrade( c ) {
	jQuery( function( $ ) {
		setTimeout( function() {
			$( '.webcomic-auto #upgrade_legacy' ).trigger( 'click' );
		}, 5000 );
		
		$( '.webcomic-auto #upgrade_legacy' ).on( 'click', function() {
			$( this ).hide();
			
			$( '.webcomic-auto-message' ).html( c );
		} );
	} );
}
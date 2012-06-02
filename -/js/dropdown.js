/** Enable webcomic dropdown navigation. */
jQuery( function( $ ) {
	$( 'select.webcomic-terms,select.webcomic-collections,select.webcomic-transcript-terms' ).on( 'change', function() {
		var url = $( this ).find( 'option:selected' ).data( 'webcomic-url' );
		
		if ( url ) {
			window.location.href = $( this ).find( 'option:selected' ).data( 'webcomic-url' );
		}
	} );
} );
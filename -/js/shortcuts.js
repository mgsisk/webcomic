/** Handle keyboard shortcut navigation. */
jQuery( function( $ ) {
	$.hotkeys.add( 'right', function() { if ( $( '.next-webcomic-link' ).attr( 'href' ) ) { window.location.href = $( '.next-webcomic-link' ).attr( 'href' ); } } );
	$.hotkeys.add( 'left', function() { if ( $( '.previous-webcomic-link' ).attr( 'href' ) ) { window.location.href = $( '.previous-webcomic-link' ).attr( 'href' ); } } );
	$.hotkeys.add( 'shift+right', function() { if ( $( '.last-webcomic-link' ).attr( 'href' ) ) { window.location.href = $( '.last-webcomic-link' ).attr( 'href' ); } } );
	$.hotkeys.add( 'shift+left', function() { if ( $( '.first-webcomic-link' ).attr( 'href' ) ) { window.location.href = $( '.first-webcomic-link' ).attr( 'href' ); } } );
	$.hotkeys.add( 'shift+up', function() { if ( $( '.purchase-webcomic-link' ).attr( 'href' ) ) { window.location.href = $( '.purchase-webcomic-link' ).attr( 'href' ); } } );
	$.hotkeys.add( 'shift+down', function() { if ( $( '.random-webcomic-link' ).attr( 'href' ) ) { window.location.href = $( '.random-webcomic-link' ).attr( 'href' ); } } );
} );
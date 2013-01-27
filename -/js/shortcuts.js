/** Handle keyboard shortcut navigation. */
jQuery( function( $ ) {
	$.hotkeys.add( 'right', function() { if ( $( '.next-webcomic-link' ).attr( 'href' ) ) { $( '.next-webcomic-link' ).first().trigger( 'click' ); } } );
	$.hotkeys.add( 'left', function() { if ( $( '.previous-webcomic-link' ).attr( 'href' ) ) { $( '.previous-webcomic-link' ).first().trigger( 'click' ); } } );
	$.hotkeys.add( 'shift+right', function() { if ( $( '.last-webcomic-link' ).attr( 'href' ) ) { $( '.last-webcomic-link' ).first().trigger( 'click' ); } } );
	$.hotkeys.add( 'shift+left', function() { if ( $( '.first-webcomic-link' ).attr( 'href' ) ) { $( '.first-webcomic-link' ).first().trigger( 'click' ); } } );
	$.hotkeys.add( 'shift+up', function() { if ( $( '.purchase-webcomic-link' ).attr( 'href' ) ) { $( '.purchase-webcomic-link' ).first().trigger( 'click' ); } } );
	$.hotkeys.add( 'shift+down', function() { if ( $( '.random-webcomic-link' ).attr( 'href' ) ) { $( '.random-webcomic-link' ).first().trigger( 'click' ); } } );
} );
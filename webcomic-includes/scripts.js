/**
 * This document contains javascript necessary to enhance certain Webcomic functions.
 * 
 * @package webcomic
 * @since 3
 */
jQuery( document ) . ready( function( $ ) {
	/** Load transcripts for improvement */
	$( 'select[name=webcomic_transcript_language]' ) . change( 
		function() {
			$( this ) . parents( 'form' ) . find( 'input[name=webcomic_ajax]' ) . val( 1 );
			var params = $( this ) . parents( 'form' ) . serialize();
			$( this ) . parents( 'form' ) . find( 'textarea[name=webcomic_transcript_text]' ) . load( window.location.pathname, params );
			$( this ) . parents( 'form' ) . find( 'input[name=webcomic_ajax]' ) . val( 0 );
		}
	) . change();
	
	/** Enable keyboard shortcuts */
	$( '.webcomic-kbd-shortcut' ) . click( function() { if ( $( this ) . attr( 'href' ) ) window . location = $( this ) . attr( 'href' ); } );
	$.hotkeys.add( 'right', { disableInInput: true }, function() { $( '.next-webcomic-link.webcomic-kbd-shortcut' ) . click(); } );
	$.hotkeys.add( 'left', { disableInInput: true }, function() { $( '.previous-webcomic-link.webcomic-kbd-shortcut' ) . click(); } );
	$.hotkeys.add( 'shift+left', { disableInInput: true }, function() { $( '.first-webcomic-link.webcomic-kbd-shortcut' ) . click(); } );
	$.hotkeys.add( 'shift+right', { disableInInput: true }, function() { $( '.last-webcomic-link.webcomic-kbd-shortcut' ) . click(); } );
	$.hotkeys.add( 'shift+up', { disableInInput: true }, function() { $( '.purchase-webcomic-link.webcomic-kbd-shortcut' ) . click(); } );
	$.hotkeys.add( 'shift+down', { disableInInput: true }, function() { $( '.random-webcomic-link.webcomic-kbd-shortcut' ) . click(); } );
	$.hotkeys.add( 'ctrl+shift+up', { disableInInput: true }, function() { $( '.remove-webcomic-link.webcomic-kbd-shortcut' ) . click(); } );
	$.hotkeys.add( 'ctrl+shift+left', { disableInInput: true }, function() { $( '.return-webcomic-link.webcomic-kbd-shortcut' ) . click(); } );
	$.hotkeys.add( 'ctrl+shift+down', { disableInInput: true }, function() { $( '.bookmark-webcomic-link.webcomic-kbd-shortcut' ) . click(); } );
	
	/** Jump to the selected url */
	$( '.webcomic-posts,.related-webcomic-posts,.webcomic-post-terms,.webcomic-terms,.webcomic-archive' ) . change ( function() { if ( $( this ) . attr( 'value' ) != 0 ) window . location = $( this ) . attr( 'value' ); } );
} );
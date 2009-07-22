/**
 * This document contains javascript necessary to enhance certain WebComic administrative functions.
 * 
 * @package WebComic
 * @since 2.1.0
 */

jQuery( document ) . ready( function( $ ) {
	$( '.orphans' ) . change ( function() {
		if ( $( this ) . attr( 'value' ) == 'comic_post' )
			$( '#orphan_post_controls' ) . show( 0 );
		else
			$( '#orphan_post_controls' ) . hide( 0 );
	} );
		
	$( '.comic' ) . change ( function() {
		if ( $( this ) . attr( 'value' ) == 'publish' )
			$( '#comic_post_controls' ) . show( 0 );
		else
			$( '#comic_post_controls' ) . hide( 0 );
	} );
} );
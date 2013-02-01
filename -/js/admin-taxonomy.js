/**
 * Modify the add/edit tag forms to disable AJAX submission and
 * accept file uploads. Also disable AJAX deletion of storylines.
 */
jQuery( function( $ ) {
	$( '#addtag,#edittag' ).attr( 'enctype', 'multipart/form-data' );
	$( '#submit' ).attr( 'id', 'webcomic-submit' );
	
	if ( 'storyline' === $( '[data-webcomic-taxonomy]' ).data( 'webcomic-taoxnomy' ) ) {
		$( 'a.delete-tag' ).removeClass( 'delete-tag' ).addClass( 'webcomic-delete-term' );
	}
} );
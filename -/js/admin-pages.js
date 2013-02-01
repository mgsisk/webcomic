jQuery( function( $ ) {
	var url = $( '[data-webcomic-admin-url]' ).data( 'webcomic-admin-url' );
	
	/** Update meta data values in the quick edit box for pages. */
	$( document ).on( 'click', 'a.editinline', function() {
		$.getJSON( url, {
			post: $( this ).parents( 'tr' ).attr( 'id' ).substr( 5, $( this ).parents( 'tr' ).attr( 'id' ).length ),
			webcomic_admin_ajax: 'WebcomicPages::ajax_quick_edit'
		}, function ( data ) {
			$( '#webcomic_page_collection' ).val( data.collection );
		} );
	} );

	/** Save meta data values from the quick edit box for pages. */
	$( '.save' ).on( 'click', function() {
		$.get( url, {
			post: $( this ).parents( 'tr' ).attr( 'id' ).substr( 5, $( this ).parents( 'tr' ).attr( 'id' ).length ),
			collection: $( '#webcomic_page_collection' ).val(),
			webcomic_page_inline_save: $( '#webcomic_page_inline_save' ).val(),
			webcomic_admin_ajax: 'WebcomicPages::ajax_quick_save'
		} );
	} );
} );
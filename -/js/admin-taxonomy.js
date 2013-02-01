jQuery( function( $ ) {
	/** Prevent AJAX actions for storylines. */
	if ( 'storyline' === $( '[data-webcomic-taxonomy]' ).data( 'webcomic-taxonomy' ) ) {
		$( '#submit' ).attr( 'id', 'webcomic-submit' );
		$( '.delete-tag' ).removeClass( 'delete-tag' ).addClass( 'webcomic-delete-term' );
		$( '.wp-list-table tbody' ).sortable();
	}
	
	/** Remove the term image. */
	$( document ).on( 'click', '.webcomic-term-image-x', function() {
		$.get( $( '[data-webcomic-admin-url]' ).data( 'webcomic-admin-url' ), {
			id: 0,
			taxonomy: $( '[data-webcomic-taxonomy]' ).data( 'webcomic-taxonomy' ),
			term: $( 'data-webcomic-term' ).data( 'webcomic-term' ),
			webcomic_admin_ajax: 'WebcomicTaxonomy::ajax_term_image'
		}, function( data ) {
			$( '#webcomic_term_image' ).html( data );
		} );
	} );
} );

/** Enable fancy taxonomy images. */
( function( $ ) {
	var frame;
	
	$( function() {
		$( document ). on( 'click', '.webcomic-term-image', function( e ) {
			var $e = $( this );
			
			e.preventDefault();
			
			if ( frame ) {
				frame.open();
				
				return;
			}
			
			frame = wp.media.frames.webcomicTermImage = wp.media( {
				title: $e.data( 'title' ),
				library: {
					type: 'image'
				},
				button: {
					text: $e.data( 'update' )
				}
			} );
			
			frame.on( 'select', function() {
				$.get( $( '[data-webcomic-admin-url]' ).data( 'webcomic-admin-url' ), {
					id: frame.state().get( 'selection' ).first().id,
					taxonomy: $( '[data-webcomic-taxonomy]' ).data( 'webcomic-taxonomy' ),
					term: $( 'data-webcomic-term' ).data( 'webcomic-term' ),
					webcomic_admin_ajax: 'WebcomicTaxonomy::ajax_term_image'
				}, function( data ) {
					$( '#webcomic_term_image' ).html( data );
				} );
			} );
			
			frame.open();
		} );
	} );
}( jQuery ) );
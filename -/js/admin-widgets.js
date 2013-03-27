jQuery( function( $ ) {
	var url = $( '[data-webcomic-admin-url]' ).data( 'webcomic-admin-url' );
	
	/** Remove the webcomic image. */
	$( document ).on( 'click', '.webcomic-image-x', function() {
		$e = $( this );
		
		$.get( url, {
			id: 0,
			field: $e.data( 'field' ),
			webcomic_admin_ajax: $e.data( 'callback' )
		}, function( data ) {
			$( $e.data( 'target' ) ).html( data );
		} );
	} );
} );

/** Enable fancy image selection. */
( function( $ ) {
	var frame;
	
	$( function() {
		$( document ).on( 'click', '.webcomic-image', function( e ) {
			var $e = $( this );
			
			e.preventDefault();
			
			if ( frame ) {
				frame.open();
				
				return;
			}
			
			frame = wp.media.frames.webcomicPoster = wp.media( {
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
					field: $e.data( 'field' ),
					webcomic_admin_ajax: $e.data( 'callback' )
				}, function( data ) {
					$( $e.data( 'target' ) ).html( data );
				} );
			} );
			
			frame.open();
		} );
	} );
}( jQuery ) );
jQuery( function( $ ) {
	var url = $( '[data-webcomic-admin-url]' ).data( 'webcomic-admin-url' );
	
	/** Remove the webcomic image. */
	$( document ).on( 'click', '.webcomic-image-x', function() {
		var $e = $( this );
		
		$.get( url, {
			id: 0,
			name: $e.data( 'name' ),
			target: $e.data( 'target' ),
			webcomic_admin_ajax: $e.data( 'callback' )
		}, function( data ) {
			$( $e.closest( $e.data( 'target' ) ) ).html( data );
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
			
			frame = wp.media.frames.webcomicWidgetImage = wp.media( {
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
					name: $e.data( 'name' ),
					target: $e.data( 'target' ),
					webcomic_admin_ajax: $e.data( 'callback' )
				}, function( data ) {
					$( $e.closest( $e.data( 'target' ) ) ).html( data );
				} );
			} );
			
			frame.open();
		} );
	} );
}( jQuery ) );
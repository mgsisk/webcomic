jQuery( function( $ ) {
	var url = $( '[data-webcomic-admin-url]' ).data( 'webcomic-admin-url' );
	
	/** Update collection slug previews. */
	$( '#webcomic_slugs_archive,#webcomic_slugs_webcomic,#webcomic_slugs_storyline,#webcomic_slugs_character' ).on( 'change', function() {
		$.getJSON( url, {
			slug: $( this ).val(),
			preview: '#' + $( this ).attr( 'id' ),
			collection: $( '[name=webcomic_collection]' ).val(),
			webcomic_admin_ajax: 'WebcomicConfig::ajax_slug_preview'
		}, function( data ) {
			$( data.container ).val( data.slug ).siblings( '.description' ).children( 'b' ).html( data.slug )
		} );
	} );
	
	/** Enable or disable print defaults. */
	$( '#webcomic_commerce_business' ).on( 'change', function() {
		$.getJSON( url, {
			business: $( this ).val(),
			webcomic_admin_ajax: 'WebcomicConfig::ajax_commerce_defaults'
		}, function ( data ) {
			if ( data.clear ) {
				$( '#webcomic_commerce_prints,#webcomic_commerce_originals' ).removeAttr( 'disabled' ).siblings( 'span' ).removeClass( 'description' );
			} else {
				$( '#webcomic_commerce_prints,#webcomic_commerce_originals' ).removeAttr( 'checked' ).attr( 'disabled', true ).siblings( 'span' ).addClass( 'description' );
			}
		} );
	} ).trigger( 'change' );
	
	/** Update Twitter authorized account. */
	$( '#webcomic_twitter_consumer_key,#webcomic_twitter_consumer_secret' ).on( 'change', function() {
		$.get( url, {
			consumer_key: $( '#webcomic_twitter_consumer_key' ).val(),
			consumer_secret: $( '#webcomic_twitter_consumer_secret' ).val(),
			collection: $( '[name=webcomic_collection]' ).val(),
			webcomic_admin_ajax: 'WebcomicConfig::ajax_twitter_account'
		}, function ( data ) {
			$( '#webcomic_twitter_account' ).html( data );
		} );
	} );
	
	/** Remove the collection poster. */
	$( document ).on( 'click', '.webcomic-collection-poster-remove', function() {
		$.get( url, {
			id: 0,
			collection: $( '[name=webcomic_collection]' ).val(),
			webcomic_admin_ajax: 'WebcomicConfig::ajax_collection_image'
		}, function( data ) {
			$( '#webcomic_collection_image' ).html( data );
		} );
	} );
	
	/** Toggle collection setting sections. */
	$( '.wrap h3' ).css( {
		'border-top': 'thin solid #dfdfdf',
		color: '#21759b',
		cursor: 'pointer',
		padding: '.5em 0 0'
	} ).nextAll( 'table' ).hide();
	
	$( '.wrap h3' ).on( 'mouseenter', function() { $( this ).css( 'color', '#d54e21' ); } );
	$( '.wrap h3' ).on( 'mouseleave', function() { $( this ).css( 'color', '#21759b' ); } );
	
	$( '.wrap h3' ).on( 'click', function() {
		$( this ).nextAll( 'table:first' ).toggle();
	} );
	
	$( '.wrap h3:first' ).trigger( 'click' );
} );

/** Enable fancy collection posters. */
( function( $ ) {
	var frame;
	
	$( function() {
		$( document ). on( 'click', '.webcomic-collection-poster', function( e ) {
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
					collection: $( '[name=webcomic_collection]' ).val(),
					webcomic_admin_ajax: 'WebcomicConfig::ajax_collection_image'
				}, function( data ) {
					$( '#webcomic_collection_image' ).html( data );
				} );
			} );
			
			frame.open();
		} );
	} );
}( jQuery ) );

var wpActiveEditor;

jQuery( function( $ ) {
	
	/** Dynamically update the price preview for prints. */
	$( "#webcomic_commerce_adjust_prices_domestic,#webcomic_commerce_adjust_shipping_domestic" ).on( "change", function() {
		calculate_total( "domestic" );
	} );
	
	$( "#webcomic_commerce_adjust_prices_international,#webcomic_commerce_adjust_shipping_international" ).on( "change", function() {
		calculate_total( "international" );
	} );
	
	$( "#webcomic_commerce_adjust_prices_original,#webcomic_commerce_adjust_shipping_original" ).on( "change", function() {
		calculate_total( "original" );
	} );
	
	$( "#webcomic_commerce_original_available" ).on( "click", function() {
		calculate_total( "original" );
	} );
	
	/** Calculate and update print prices. */
	function calculate_total( id ) {
		var total;
		
		if ( "original" === id && !$( "#webcomic_commerce_original_available" ).attr( "checked" ) ) {
			$( "#webcomic_original_total" ).html( $( "[data-webcomic-original]" ).data( "webcomic-original" ) );
		} else {
			total = ( parseFloat( $( "#webcomic_commerce_" + id + "_price" ).html() ) * ( 1 + ( parseFloat( $( "#webcomic_commerce_adjust_prices_" + id ).val() ) * .01 ) ) ) + ( parseFloat( $( "#webcomic_commerce_" + id + "_shipping" ).html() ) * ( 1 + ( parseFloat( $( "#webcomic_commerce_adjust_shipping_" + id ).val() ) * .01 ) ) );
			
			$( "#webcomic_" + id + "_total" ).html( total.toFixed( 2 ) + " " + $( "[data-webcomic-currency]" ).data( "webcomic-currency" ) );
		}
	}

	/**  Media MetaBox handling. */

	var _ = window._;
	var wp = window.wp;
	var frame;
	var updating = false;

	var disableMetaBox = function () {
		$( '#webcomic_media_action .button' )
			.addClass( 'button-disabled' )
			.attr('disabled', true);
		$( '#webcomic_media_action .spinner' )
			.addClass( 'is-active' );
	};

	var enableMetaBox = function () {
		$( '#webcomic_media_action .button' )
			.removeClass( 'button-disabled' )
			.attr('disabled', false);
		$( '#webcomic_media_action .spinner' )
			.removeClass( 'is-active' );
	}

	var updateMetaBox = function () {
		if (updating) return;
		updating = true;
		disableMetaBox();
		$.get( $( "[data-webcomic-admin-url]" ).data( "webcomic-admin-url" ), {
			post: $( "#post_ID" ).val(),
			webcomic_admin_ajax: "WebcomicPosts::ajax_media_preview"
		}, function( data ) {
			updating = false;
			enableMetaBox();
			$( "#webcomic_media_preview" ).html( data );
		} );
	}

	var detachMedia = function ( mediaId ) {
		if (updating) return;
		updating = true;
		disableMetaBox();
		$.post( $( "[data-webcomic-admin-url]" ).data( "webcomic-admin-url" ), {
			postId: $( "#post_ID" ).val(),
			mediaId: mediaId,
			webcomic_admin_ajax: "WebcomicPosts::ajax_detach_media"
		}, function( data ) {
			updating = false;
			enableMetaBox();
			$( "#webcomic_media_preview" ).html( data );
		} )
	}

	var webcomicMediaFrameInit = function () {
		frame = wp.media.frames.webcomicMedia = new wp.media.view.MediaFrame.Select({
			title: 'Webcomic Media',
			multiple: false,
			library: {
				order: 'ASC',
				type: 'image',
				uploadedTo: wp.media.view.settings.post.id
			},
			button: {
				text: 'Add Media'
			}
		});

		frame.on('close', updateMetaBox);
		frame.on('select', function () {
			updateMetaBox();
			frame.close();
		});
	}

	$('#webcomic_media_action .open-frame').on('click', function (e) {
		e.preventDefault();
		if (!frame) webcomicMediaFrameInit();
		frame.open();
	});

	$('#webcomic_media_preview').on('click', '.detach-media', function (e) {
		e.preventDefault();
		var id = $(e.target).parent('.webcomic_media_image').data('attachment-id');
		detachMedia(id);
	});

} );

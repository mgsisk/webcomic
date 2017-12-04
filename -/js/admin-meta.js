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
	
	$( "#webcomic_media_preview" ).sortable( {
		update: function() {
			disableMetaBox();
			$.post( $( "[data-webcomic-admin-url]" ).data( "webcomic-admin-url" ), {
				ids: $( "[name='webcomic_media_ids[]']" ).serializeArray(),
				webcomic_admin_ajax: "WebcomicMedia::ajax_sort_media"
			}, function( data ) {
				enableMetaBox();
			} );
		}

	var disableMetaBox = function () {
		updating = true;
		$( '#webcomic-media button' )
			.addClass( 'button-disabled' )
			.attr('disabled', true);
		$( '#webcomic_media_action .spinner' )
			.addClass( 'is-active' );
	};

	var enableMetaBox = function () {
		updating = false;
		$( '#webcomic-media button' )
			.removeClass( 'button-disabled' )
			.attr('disabled', false);
		$( '#webcomic_media_action .spinner' )
			.removeClass( 'is-active' );
		
		$( "#webcomic_media_preview" ).sortable( {
			update: function() {
				disableMetaBox();
				$.post( $( "[data-webcomic-admin-url]" ).data( "webcomic-admin-url" ), {
					ids: $( "[name='webcomic_media_ids[]']" ).serializeArray(),
					webcomic_admin_ajax: "WebcomicMedia::ajax_sort_media"
				}, function( data ) {
					enableMetaBox();
				} );
			}
		} );
	}

	var updateMetaBox = function () {
		if (updating) return;
		disableMetaBox();
		$.get( $( "[data-webcomic-admin-url]" ).data( "webcomic-admin-url" ), {
			post: $( "#post_ID" ).val(),
			webcomic_admin_ajax: "WebcomicPosts::ajax_media_preview"
		}, function( data ) {
			enableMetaBox();
			$( "#webcomic_media_preview" ).html( data );
		} );
	}

	var attachMedia = function ( mediaIds ) {
		if (updating) return;
		disableMetaBox();
		$.post( $( "[data-webcomic-admin-url]" ).data( "webcomic-admin-url" ), {
			postId: $( "#post_ID" ).val(),
			mediaIds: mediaIds,
			webcomic_admin_ajax: "WebcomicPosts::ajax_attach_media"
		}, function( data ) {
			enableMetaBox();
			$( "#webcomic_media_preview" ).html( data );
		} );
	}

	var detachMedia = function ( mediaId ) {
		if (updating) return;
		disableMetaBox();
		$.post( $( "[data-webcomic-admin-url]" ).data( "webcomic-admin-url" ), {
			postId: $( "#post_ID" ).val(),
			mediaId: mediaId,
			webcomic_admin_ajax: "WebcomicPosts::ajax_detach_media"
		}, function( data ) {
			enableMetaBox();
			$( "#webcomic_media_preview" ).html( data );
		} );
	}

	var webcomicMediaFrameInit = function () {
		frame = wp.media.frames.webcomicMedia = wp.media({
			title: 'Webcomic Media',
			multiple: true,
			library: {
				type: 'image',
				// Only show unattached images.
				uploadedTo: 0
			},
			button: {
				text: 'Add Media'
			}
		});

		frame.on('select', function () {
			var attachments = frame.state().get('selection').models;
			var ids = _.map( attachments, function (attachment) {
				return attachment.get('id');
			} );
			attachMedia( ids );
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

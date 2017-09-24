var wpActiveEditor;

jQuery( function( $ ) {

	/** DEPRECIATED: see the new approach below. **/

	/** Refresh the webcomic media meta box. */
	// $( document ).on( "mousedown", ".media-modal-close,.media-modal-backdrop,.media-button-insert", function() {
		
	// 	setTimeout( function() {
	// 		$.get( $( "[data-webcomic-admin-url]" ).data( "webcomic-admin-url" ), {
	// 			post: $( "#post_ID" ).val(),
	// 			webcomic_admin_ajax: "WebcomicPosts::ajax_media_preview"
	// 		}, function( data ) {
	// 			$( "#webcomic_media_preview" ).html( data );
	// 		} );
	// 	}, 500 );
	// } );
	
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
} );

!(function (root) {

	var _ = root._
  var wp = root.wp
  var Backbone = root.Backbone
  var webcomic = root.webcomic = root.webcomic || {};

	/**
	 * New meta-box that uses `wp.media.view.MediaFrame.Select` to attach
	 * images. With this method, you can actually click `Add Media` in the
	 * modal view rather than having to click on the `x`, which is a more 
	 * user-friendly experience. Additionally, ONLY media uploaded to the
	 * current post is visible, which is the behavior we want.
	 *
	 * This is only a proof-of-concept, but it seems to work fine.
	 */
	var WebcomicMediaMetabox = Backbone.View.extend({

		events: {
			'click [data-webcomic-action="open-media"]': 'openMediaFrame'
		},

		initialize: function () {
			var self = this;
			this.frame = new wp.media.view.MediaFrame.Select({
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
			this.frame.on('select', _.bind(this.onImageSelected, this));
		},

		openMediaFrame : function(e) {
			e.preventDefault();
			this.frame.open();
		},

		onImageSelected : function() {
			var attachments = this.frame.state().get('selection').models;
			var out = '';
			_.each(attachments, _.bind(function (attachment) {
				out += '<img src="' + attachment.get('sizes').thumbnail.url + '" />';
			}, this));
			this.$('#webcomic_media_preview').html(out);
			this.frame.close();
		}

	});

	root.jQuery(function () {
		webcomic.mediaMetabox = new WebcomicMediaMetabox({ el: '#webcomic-media' }); 
	});

})(window);

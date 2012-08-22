/** Refresh the webcomic media meta box. */
function webcomic_media_meta( url ) {
	jQuery( function( $ ) {
		$( document ).on( 'mousedown', '#TB_overlay,#TB_closeWindowButton', function() {
			
			setTimeout( function() {
				$.get( url, {
					post: $( '#post_ID' ).val(),
					webcomic_admin_ajax: 'WebcomicPosts::ajax_media_preview'
				}, function( data ) {
					$( '#webcomic_media_preview' ).html( data );
				} );
			}, 500 );
		} );
	} );
}

/** Dynamically update the price preview for prints. */
function webcomic_prints_meta( currency, sold ) {
	jQuery( function( $ ) {
		$( '#webcomic_commerce_adjust_prices_domestic,#webcomic_commerce_adjust_shipping_domestic' ).on( 'change', function() {
			calculate_total( 'domestic' );
		} );
		
		$( '#webcomic_commerce_adjust_prices_international,#webcomic_commerce_adjust_shipping_international' ).on( 'change', function() {
			calculate_total( 'international' );
		} );
		
		$( '#webcomic_commerce_adjust_prices_original,#webcomic_commerce_adjust_shipping_original' ).on( 'change', function() {
			calculate_total( 'original' );
		} );
		
		$( '#webcomic_commerce_original_available' ).on( 'click', function() {
			calculate_total( 'original' );
		} );
		
		function calculate_total( id ) {
			var total;
			
			if ( 'original' === id && !$( '#webcomic_commerce_original_available' ).attr( 'checked' ) ) {
				$( '#webcomic_original_total' ).html( sold );
			} else {
				total = ( parseFloat( $( '#webcomic_commerce_' + id + '_price' ).html() ) * ( 1 + ( parseFloat( $( '#webcomic_commerce_adjust_prices_' + id ).val() ) * .01 ) ) ) + ( parseFloat( $( '#webcomic_commerce_' + id + '_shipping' ).html() ) * ( 1 + ( parseFloat( $( '#webcomic_commerce_adjust_shipping_' + id ).val() ) * .01 ) ) );
				
				$( '#webcomic_' + id + '_total' ).html( total.toFixed( 2 ) + ' ' + currency );
			}
		}
	} );
}
<script type="text/javascript">
jQuery( document ).ready( function( $ ) {
	// Enable file uploads during post publish/save.
	$( 'form#post' ).attr( 'enctype', 'multipart/form-data' ).attr( 'encoding', 'multipart/form-data' );
	
	// Enable dynamic character/storyline loading.
	$( 'select#webcomic_collection' ).change( function() {
		$( 'input[name=webcomic_ajax]' ).val( 'collection' );
		
		var params = $( 'form' ).serialize();
		
		$( '#webcomic_terms' ).load( '<?php echo $_SERVER[ 'PHP_SELF' ]; ?>', params );
		$( 'input[name=webcomic_ajax]' ).val( 'orphans' );
		
		var params = $( 'form' ).serialize();
		
		$( '#webcomic_orphans' ).load( '<?php echo $_SERVER[ 'PHP_SELF' ]; ?>', params );
		$( 'input[name="webcomic_ajax"]' ).val(0);
	} ).change();
	
	// Enable multiple individual file uploads.
	$( '#add_webcomic_file' ).click( function() {
		$( '#webcomic_files' ).append( '<br><input type="file" name="webcomic_file[]">' );
	} );
	
	// Enable multiple transcript languages.
	$( '#webcomic_transcript_language' ).change( function() {
		var x=$(this).val();
		$('[id*="webcomic_lang_"]').hide();
		$('[id*="[webcomic_lang_'+x+']"]').show();
	} ).change();
} );

// Paypal price function.
jQuery('select[name*=webcomic_paypal],input[name*=webcomic_paypal]').change(function(){
	var i,x,p,m,f,a,t,s,c,cost_d,cost_i,cost_o,ship_d,ship_i,ship_o,currency;
	
	i        = jQuery( this ) . attr( 'name' ) . lastIndexOf( '_price' );
	cost_d   = <?php echo $this->price( $this->option( 'paypal_price_d' ), array( $wc->webcomic_paypal[ 'price_d' ], $price_d ) ); ?>;
	cost_i   = <?php echo $this->price( $this->option( 'paypal_price_i' ), array( $wc->webcomic_paypal[ 'price_i' ], $price_i ) ); ?>;
	cost_o   = <?php echo $this->price( $this->option( 'paypal_price_o' ), array( $wc->webcomic_paypal[ 'price_o' ], $price_o ) ); ?>;
	ship_d   = <?php echo $this->price( $this->option( 'paypal_shipping_d' ), array( $wc->webcomic_paypal[ 'shipping_d' ], $shipping_d ) ); ?>;
	ship_i   = <?php echo $this->price( $this->option( 'paypal_shipping_i' ), array( $wc->webcomic_paypal[ 'shipping_i' ], $shipping_i ) ); ?>;
	ship_o   = <?php echo $this->price( $this->option( 'paypal_shipping_o' ), array( $wc->webcomic_paypal[ 'shipping_o' ], $shipping_o ) ); ?>;
	currency = ' <?php echo $this->option( 'paypal_currency' ); ?>';
	
	if ( 0 < jQuery(this).attr('name').lastIndexOf('_d') ) {
		x = 'd';
		c = cost_d;
		s = ship_d;
	} else if ( 0 < jQuery( this ).attr( 'name' ).lastIndexOf( '_i' ) ) {
		x = 'i';
		c = cost_i;
		s = ship_i;
	} else {
		x = 'o';
		c = cost_o;
		s = ship_o;
	}
	
	a = ( i > 0 ) ? 'webcomic_paypal_price_' + x : 'webcomic_paypal_shipping_' + x;
	t = ( i > 0 ) ? 'webcomic_paypal_price_type_' + x : 'webcomic_paypal_shipping_type_' + x;
	p = ( i > 0 ) ? c : s;
	m = p * ( Math.abs( jQuery( 'input[name=' + a + ']' ).val() ) / 100 );
	f = ( 'sub' == jQuery( 'select[name=' + t + ']' ).val() ) ? p - m : p + m;
	f = ( f <= .01 ) ? '<span class="error">!</span>' : f.toFixed( 2 ) + currency;
	
	jQuery( '.' + a ).html( f );
} );
</script>
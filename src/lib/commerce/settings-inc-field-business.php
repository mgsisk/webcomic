<?php
/**
 * Business field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Commerce;

if ( ! isset( $args ) ) {
	return;
}

?>

<input type="email" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="<?php echo esc_attr( $args['option'] ); ?>" class="regular-text">
<p class="description">
	<?php
	// Translators: Paypal account hyperlink.
	printf( esc_html__( 'The %s transactions will go to.', 'webcomic' ), $args['paypal'] ); // WPCS: xss ok.
	?>
</p>

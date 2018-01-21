<?php
/**
 * Paypal redirect template
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Commerce;

if ( ! isset( $args ) ) {
	return;
}

?>

<form action="<?php echo esc_url( $args['action'] ); ?>" method="post">
	<?php foreach ( $args['fields'] as $name => $value ) : ?>
		<input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">
	<?php endforeach; ?>
	<noscript>
		<button type="submit"><?php esc_html_e( 'Continue to PayPal', 'webcomic' ); ?></button>
	</noscript>
</form>
<script>document.forms[0].submit()</script>

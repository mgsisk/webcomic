<?php
/**
 * Currency field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Commerce;

if ( ! isset( $args ) ) {
	return;
}

?>

<select id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>">
	<?php foreach ( $args['currencies'] as $currency => $label ) : ?>
		<option value="<?php echo esc_attr( $currency ); ?>" <?php selected( $currency, $args['option'] ); ?>><?php echo esc_html( $label ); ?></option>
	<?php endforeach; ?>
</select>
<p class="description"><?php esc_html_e( 'The currency transactions will use.', 'webcomic' ); ?></p>

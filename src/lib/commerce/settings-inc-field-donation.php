<?php
/**
 * Donation field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Commerce;

if ( ! isset( $args ) ) {
	return;
}

?>

<input type="number" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="<?php echo esc_attr( $args['option'] ); ?>" min="0" step="0.01" class="small-text">
<p class="description"><?php esc_html_e( 'The amount users must donate; 0 allows users to specify their own donation amount.', 'webcomic' ); ?></p>

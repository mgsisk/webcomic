<?php
/**
 * Age field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Restrict;

if ( ! isset( $args ) ) {
	return;
}

?>

<input type="number" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="<?php echo esc_attr( $args['option'] ); ?>" min="0" class="small-text">
<p class="description"><?php esc_html_e( 'The minimum age required to view new comics.', 'webcomic' ); ?></p>

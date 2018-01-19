<?php
/**
 * Post custom field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

if ( ! isset( $args ) ) {
	return;
}

?>

<input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="<?php echo esc_attr( $args['option'] ); ?>">
<p class="description"><?php esc_html_e( 'The custom field key to use for comparison.', 'webcomic' ); ?></p>

<?php
/**
 * Slug field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Settings;

if ( ! isset( $args ) ) {
	return;
}

?>

<input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="<?php echo esc_attr( $args['option'] ); ?>" class="regular-text">
<p class="description"><?php esc_html_e( 'The slug is the URL-friendly text used for archives. It can contain lowercase letters, numbers, and hyphens.', 'webcomic' ); ?></p>

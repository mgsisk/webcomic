<?php
/**
 * Update field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Twitter;

if ( ! isset( $args ) ) {
	return;
}

?>

<input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="<?php echo esc_attr( $args['option'] ); ?>" class="regular-text">
<p class="description"><?php esc_html_e( 'The status to update with when publishing new comics.', 'webcomic' ); ?></p><br>
<div data-input="<?php echo esc_attr( $args['label_media'] ); ?>" data-webcomic-media-manager>
	<?php ( $args['option_media'] ) && printf( '%s', wp_get_attachment_image( $args['option_media'], 'medium' ) ); ?>
	<noscript><?php esc_html_e( 'Media management requires JavaScript.', 'webcomic' ); ?></noscript>
</div>
<input type="hidden" id="<?php echo esc_attr( $args['label_media'] ); ?>" name="<?php echo esc_attr( $args['label_media'] ); ?>" value="<?php echo esc_attr( $args['option_media'] ); ?>">
<p class="description"><?php esc_html_e( 'An alternative image to include in place of comic media for status updates.', 'webcomic' ); ?></p>

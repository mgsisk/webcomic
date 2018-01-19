<?php
/**
 * Roles media field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Restrict;

if ( ! isset( $args ) ) {
	return;
}

?>

<div data-input="<?php echo esc_attr( $args['label_for'] ); ?>" data-webcomic-media-manager>
	<?php $args['option'] && printf( '%s', wp_get_attachment_image( $args['option'], 'medium' ) ); ?>
	<noscript><?php esc_html_e( 'Media management requires JavaScript.', 'webcomic' ); ?></noscript>
</div>
<input type="hidden" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="<?php echo esc_attr( $args['option'] ); ?>">
<p class="description"><?php esc_html_e( 'An alternative image to display in place of comic media for role-restricted comics.', 'webcomic' ); ?></p>

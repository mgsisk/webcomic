<?php
/**
 * Taxonomy add media field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Taxonomy\Shared;

if ( ! isset( $args ) ) {
	return;
}

wp_nonce_field( $args['nonce'], $args['nonce'] );

?>

<div class="form-field">
	<label>
		<?php esc_html_e( 'Image', 'webcomic' ); ?>
	</label>
	<div data-input="webcomic_media" data-webcomic-media-manager>
		<noscript><?php esc_html_e( 'Media management requires JavaScript.', 'webcomic' ); ?></noscript>
	</div>
	<input type="hidden" name="webcomic_media">
	<p><?php esc_html_e( 'A representative image that can be displayed on your site.', 'webcomic' ); ?></p>
</div>

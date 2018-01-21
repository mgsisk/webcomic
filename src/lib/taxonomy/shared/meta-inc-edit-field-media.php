<?php
/**
 * Taxonomy edit media field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Taxonomy\Shared;

if ( ! isset( $args ) ) {
	return;
}

wp_nonce_field( $args['nonce'], $args['nonce'] );

?>

<tr class="form-field">
	<th>
		<label>
			<?php esc_html_e( 'Image', 'webcomic' ); ?>
		</label>
	</th>
	<td>
		<div data-input="webcomic_media" data-webcomic-media-manager>
			<noscript><?php esc_html_e( 'Media management requires JavaScript.', 'webcomic' ); ?></noscript>
		</div>
		<input type="hidden" name="webcomic_media" value="<?php echo esc_attr( $args['option'] ); ?>">
		<p><?php esc_html_e( 'A representative image that can be displayed on your site.', 'webcomic' ); ?></p>
	</td>
</tr>

<?php
/**
 * Age meta box
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Restrict\MetaBox;

if ( ! isset( $args ) ) {
	return;
}

wp_nonce_field( $args['nonce'], $args['nonce'] );

?>

<p class="post-attributes-label-wrapper">
	<label for="webcomic_restrict_age" class="post-attributes-label"><?php esc_html_e( 'Minimum Age', 'webcomic' ); ?></label>
</p>
<p><input type="number" id="webcomic_restrict_age" name="webcomic_restrict_age" value="<?php echo esc_attr( $args['option_age'] ); ?>" min="0" class="small-text"></p>
<p class="post-attributes-label-wrapper">
	<label class="post-attributes-label"><?php esc_html_e( 'Age-Restricted Media', 'webcomic' ); ?></label>
</p>
<div data-input="webcomic_restrict_age_media" data-webcomic-media-manager>
	<?php $args['option_media'] && printf( '%s', wp_get_attachment_image( $args['option_media'], 'medium' ) ); ?>
	<noscript><?php esc_html_e( 'Media management requires JavaScript.', 'webcomic' ); ?></noscript>
</div>
<input type="hidden" name="webcomic_restrict_age_media" value="<?php echo esc_attr( $args['option_media'] ); ?>">

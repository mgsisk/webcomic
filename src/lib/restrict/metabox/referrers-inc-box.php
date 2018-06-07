<?php
/**
 * Referrers meta box
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
	<label for="webcomic_restrict_referrers" class="post-attributes-label"><?php esc_html_e( 'Allowed Referrers', 'webcomic' ); ?></label>
</p>
<p>
	<textarea id="webcomic_restrict_referrers" name="webcomic_restrict_referrers" rows="7" class="large-text"><?php echo esc_textarea( $args['option_referrers'] ); ?></textarea>
	<br><span class="howto"><?php esc_html_e( 'Enter one web address per line.', 'webcomic' ); ?></span>
</p>
<p class="post-attributes-label-wrapper">
	<label class="post-attributes-label"><?php esc_html_e( 'Referrer-Restricted Media', 'webcomic' ); ?></label>
</p>
<div data-input="webcomic_restrict_referrers_media" data-webcomic-media-manager></div>
<input type="hidden" name="webcomic_restrict_referrers_media" value="<?php echo esc_attr( $args['option_media'] ); ?>">

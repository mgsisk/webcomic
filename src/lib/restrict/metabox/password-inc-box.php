<?php
/**
 * Password meta box
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Restrict\MetaBox;

if ( ! isset( $args ) ) {
	return;
}

wp_nonce_field( $args['nonce'], $args['nonce'] );

?>

<p class="howto"><?php esc_html_e( 'Posts can be password-protected using the Visibility settings in the Publish box.', 'webcomic' ); ?></p>
<p class="post-attributes-label-wrapper">
	<label class="post-attributes-label"><?php esc_html_e( 'Password-Restricted Media', 'webcomic' ); ?></label>
</p>
<div data-input="webcomic_restrict_password_media" data-webcomic-media-manager></div>
<input type="hidden" name="webcomic_restrict_password_media" value="<?php echo esc_attr( $args['option'] ); ?>">

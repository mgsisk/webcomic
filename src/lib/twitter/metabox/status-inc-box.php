<?php
/**
 * Status meta box
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Twitter\MetaBox;

if ( ! isset( $args ) ) {
	return;
}

wp_nonce_field( $args['nonce'], $args['nonce'] );

?>

<input type="hidden" name="webcomic_twitter_update">
<input type="hidden" name="webcomic_twitter_update_media">
<input type="hidden" name="webcomic_twitter_update_sensitive">
<p class="post-attributes-label-wrapper">
	<label class="post-attributes-label"><?php esc_html_e( 'Account', 'webcomic' ); ?></label>
</p>
<?php
echo $args['account']; // WPCS: xss ok.
?>
<p>
	<label class="selectit">
		<input type="checkbox" name="webcomic_twitter_update" value="1" <?php checked( $args['option_update'] ); ?>>
		<?php esc_html_e( 'Update status on publish', 'webcomic' ); ?>
	</label><br>
	<label class="selectit">
		<input type="checkbox" name="webcomic_twitter_update_media" value="1" <?php checked( $args['option_update_media'] ); ?>>
		<?php esc_html_e( 'Include media with update', 'webcomic' ); ?>
	</label><br>
	<label class="selectit">
		<input type="checkbox" name="webcomic_twitter_update_sensitive" value="1" <?php checked( $args['option_update_sensitive'] ); ?>>
		<?php esc_html_e( 'Media may be sensitive', 'webcomic' ); ?>
	</label>
</p>
<p class="post-attributes-label-wrapper">
	<label for="webcomic_twitter_status" class="post-attributes-label"><?php esc_html_e( 'Status', 'webcomic' ); ?></label>
</p>
<p><input type="text" id="webcomic_twitter_status" name="webcomic_twitter_status" value="<?php echo esc_attr( $args['option_status'] ); ?>" style="width:100%;"></p>
<div data-input="webcomic_twitter_status_media" data-webcomic-media-manager>
	<?php $args['option_status_media'] && printf( '%s', wp_get_attachment_image( $args['option_status_media'], 'medium' ) ); ?>
	<noscript><?php esc_html_e( 'Media management requires JavaScript.', 'webcomic' ); ?></noscript>
</div>
<input type="hidden" name="webcomic_twitter_status_media" value="<?php echo esc_attr( $args['option_status_media'] ); ?>">

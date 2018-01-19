<?php
/**
 * Status edit field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Twitter\MetaBox;

if ( ! isset( $args ) ) {
	return;
}

wp_nonce_field( $args['nonce'], $args['nonce'] );

?>

<fieldset class="inline-edit-col-left">
	<div class="inline-edit-col">
		<?php if ( $args['bulk'] ) : ?>
			<label class="alignleft">
				<span class="title"><?php esc_html_e( 'Twitter Update', 'webcomic' ); ?></span>
				<select name="webcomic_twitter_update">
					<option value="no-change"><?php esc_html_e( '&mdash; No Change &mdash;', 'webcomic' ); ?></option>
					<option value="1"><?php esc_html_e( 'Update on publish', 'webcomic' ); ?></option>
					<option value=""><?php esc_html_e( 'Do not update on publish', 'webcomic' ); ?></option>
				</select>
			</label>
			<label class="alignleft">
				<span class="title"><?php esc_html_e( 'Twitter Update Media', 'webcomic' ); ?></span>
				<select name="webcomic_twitter_update_media">
					<option value="no-change"><?php esc_html_e( '&mdash; No Change &mdash;', 'webcomic' ); ?></option>
					<option value="1"><?php esc_html_e( 'Include media', 'webcomic' ); ?></option>
					<option value=""><?php esc_html_e( 'Do not include media', 'webcomic' ); ?></option>
				</select>
			</label>
			<label class="alignleft">
				<span class="title"><?php esc_html_e( 'Twitter Update Sensitive', 'webcomic' ); ?></span>
				<select name="webcomic_twitter_update_sensitive">
					<option value="no-change"><?php esc_html_e( '&mdash; No Change &mdash;', 'webcomic' ); ?></option>
					<option value="1"><?php esc_html_e( 'Media may be sensitive', 'webcomic' ); ?></option>
					<option value=""><?php esc_html_e( 'Media is not sensitive', 'webcomic' ); ?></option>
				</select>
			</label>
		<?php else : ?>
			<label>
				<input type="hidden" name="webcomic_twitter_update">
				<input type="checkbox" name="webcomic_twitter_update" value="1">
				<span class="checkbox-title"><?php esc_html_e( 'Update Twitter status on publish', 'webcomic' ); ?></span>
			</label>
			<label>
				<input type="hidden" name="webcomic_twitter_update_media">
				<input type="checkbox" name="webcomic_twitter_update_media" value="1">
				<span class="checkbox-title"><?php esc_html_e( 'Include media with Twitter status updates', 'webcomic' ); ?></span>
			</label>
			<label>
				<input type="hidden" name="webcomic_twitter_update_sensitive">
				<input type="checkbox" name="webcomic_twitter_update_sensitive" value="1">
				<span class="checkbox-title"><?php esc_html_e( 'Media may be sensitive', 'webcomic' ); ?></span>
			</label>
		<?php endif; ?>
	</div>
	<input type="hidden" name="webcomic_twitter_status_quick_edit" value="1">
</fieldset>

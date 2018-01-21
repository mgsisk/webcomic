<?php
/**
 * Transcripts edit field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe\MetaBox;

if ( ! isset( $args ) ) {
	return;
}

wp_nonce_field( $args['nonce'], $args['nonce'] );

?>

<fieldset class="inline-edit-col-left">
	<div class="inline-edit-col">
		<?php if ( $args['bulk'] ) : ?>
			<label class="alignleft">
				<span class="title"><?php esc_html_e( 'Transcripts', 'webcomic' ); ?></span>
				<select name="webcomic_transcribe">
					<option value="no-change"><?php esc_html_e( '&mdash; No Change &mdash;', 'webcomic' ); ?></option>
					<option value="1"><?php esc_html_e( 'Allow', 'webcomic' ); ?></option>
					<option value=""><?php esc_html_e( 'Do not allow', 'webcomic' ); ?></option>
				</select>
			</label>
		<?php else : ?>
			<label>
				<input type="hidden" name="webcomic_transcribe">
				<input type="checkbox" name="webcomic_transcribe" value="1">
				<span class="checkbox-title"><?php esc_html_e( 'Allow transcripts', 'webcomic' ); ?></span>
			</label>
		<?php endif; ?>
	</div>
</fieldset>

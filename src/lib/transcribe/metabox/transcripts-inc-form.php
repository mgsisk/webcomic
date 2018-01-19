<?php
/**
 * Transcripts metabox form
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe\MetaBox;

if ( ! isset( $args ) ) {
	return;
}

wp_nonce_field( $args['nonce'], $args['nonce'] );

?>

<fieldset class="webcomic-transcript-form" data-id="<?php echo esc_attr( $args['post'] ); ?>">
	<legend><?php echo esc_html( $args['form_title'] ); ?></legend>
	<div class="webcomic-transcript-editor">
		<?php
		wp_editor(
			$args['post_content'],
			'webcomic_transcript_content', [
				'tinymce'       => false,
				'media_buttons' => false,
			]
		);
		?>
		<div class="webcomic-transcript-languages">
			<?php foreach ( $args['languages'] as $language ) : ?>
				<label><input type="checkbox" value="<?php echo esc_attr( $language->term_id ); ?>" <?php checked( in_array( $language->term_id, $args['post_languages'], true ) ); ?>><?php echo esc_html( $language->name ); ?></label>
			<?php endforeach; ?>

			<?php if ( ! $args['languages'] ) : ?>
				<span class="description"><?php esc_html_e( 'No languages found.', 'webcomic' ); ?></span>
			<?php endif; ?>
		</div>
	</div>
	<div class="webcomic-media">
		<?php
		echo $args['post_media']; // WPCS: xss ok.

		if ( ! $args['post_media'] ) {
			echo '<p>' . esc_html__( 'No comic media found.', 'webcomic' ) . '</p>';
		}
		?>
	</div>
	<p>
		<a href="#" class="button button-secondary"><?php esc_html_e( 'Cancel', 'webcomic' ); ?></a>
		<span class="error"></span>
		<span>
			<select class="status">
				<option value="draft" <?php selected( $args['post_status'], 'draft' ); ?>><?php esc_html_e( 'Save Draft', 'webcomic' ); ?></option>
				<option value="pending" <?php selected( $args['post_status'], 'pending' ); ?>><?php esc_html_e( 'Pending Review', 'webcomic' ); ?></option>
				<option value="publish" <?php selected( $args['post_status'], 'publish' ); ?>><?php esc_html_e( 'Publish', 'webcomic' ); ?></option>
				<option value="private" <?php selected( $args['post_status'], 'private' ); ?>><?php esc_html_e( 'Private', 'webcomic' ); ?></option>
			</select>
			<a href="#" class="button button-primary"><?php echo esc_html( $args['form_submit'] ); ?></a>
		</span>
	</p>
</fieldset>

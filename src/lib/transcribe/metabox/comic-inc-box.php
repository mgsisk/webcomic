<?php
/**
 * Parent meta box
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe\MetaBox;

if ( ! isset( $args ) ) {
	return;
}

wp_nonce_field( $args['nonce'], $args['nonce'] );

?>

<?php if ( $args['option'] ) : ?>
	<p>
		<label class="selectit">
			<input type="hidden" name="<?php echo esc_attr( $args['label_transcribe'] ); ?>">
			<input type="checkbox" name="<?php echo esc_attr( $args['label_transcribe'] ); ?>" value="1" <?php checked( $args['option_transcribe'] ); ?>>
			<?php esc_html_e( 'Allow transcripts', 'webcomic' ); ?>
		</label>
	</p>
<?php endif; ?>
<div data-input="post_parent" data-size="full" data-webcomic-search>
	<?php if ( $args['option'] ) : ?>
		<p><strong><?php echo esc_html( get_the_title( $args['option'] ) ); ?></strong></p>
		<?php webcomic_media( 'full', $args['option'] ); ?>
	<?php endif; ?>
	<noscript><?php esc_html_e( 'Comic search requires JavaScript.', 'webcomic' ); ?></noscript>
</div>
<input type="hidden" name="post_parent" value="<?php echo esc_attr( $args['option'] ); ?>">

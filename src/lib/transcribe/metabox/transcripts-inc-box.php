<?php
/**
 * Transcripts metabox
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe\MetaBox;

if ( ! isset( $args ) ) {
	return;
}

wp_nonce_field( $args['nonce'], $args['nonce'] );

?>

<input type="hidden" name="webcomic_transcribe">
<p data-post-status="<?php echo esc_attr( $post->post_status ); ?>">
	<label class="selectit">
		<input type="checkbox" name="webcomic_transcribe" value="1" <?php checked( $args['option'] ); ?>>
		<?php esc_html_e( 'Allow transcripts', 'webcomic' ); ?>
	</label>
</p>

<?php
if ( 'auto-draft' === $post->post_status ) {
	return;
}
?>

<div class="webcomic-transcripts">
	<table class="widefat striped">
		<tbody>
			<?php
			foreach ( $args['transcripts'] as $transcript ) {
				require __DIR__ . '/transcripts-inc-row.php';
			}

			if ( ! $args['transcripts'] ) :
			?>
			<tr class="none">
				<td><?php esc_html_e( 'No transcripts yet.', 'webcomic' ); ?></td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>

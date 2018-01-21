<?php
/**
 * Comics setting field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe;

if ( ! isset( $args ) ) {
	return;
}

?>

<fieldset>
	<input type="hidden" name="<?php echo esc_attr( $args['label_for'] ); ?>">
	<label>
		<input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked( $args['option'] ); ?>>
		<span><?php esc_html_e( 'Allow people to transcribe new comics', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( $args['label_close_check'] ); ?>" value="1" <?php checked( 0 < $args['option_close'] ); ?>>
		<span><?php esc_html_e( 'Automatically close transcripts on comics older than', 'webcomic' ); ?></span>
	</label>
	<label>
		<input type="number" name="<?php echo esc_attr( $args['label_close'] ); ?>" value="<?php echo esc_attr( $args['option_close'] ); ?>" min="0" class="small-text">
		<span><?php esc_html_e( 'days', 'webcomic' ); ?></span>
	</label>
</fieldset>

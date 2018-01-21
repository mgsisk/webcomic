<?php
/**
 * Notifications settings field
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
	<input type="hidden" name="<?php echo esc_attr( $args['label_mod'] ); ?>">
	<label>
		<input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked( $args['option'] ); ?>>
		<span><?php esc_html_e( 'Send an email whenever a transcript is published', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" id="<?php echo esc_attr( $args['label_mod'] ); ?>" name="<?php echo esc_attr( $args['label_mod'] ); ?>" value="1" <?php checked( $args['option_mod'] ); ?>>
		<span><?php esc_html_e( 'Send an email whenever a transcript is held for moderation', 'webcomic' ); ?></span>
	</label>
</fieldset>

<?php
/**
 * Update field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Twitter;

if ( ! isset( $args ) ) {
	return;
}

?>

<fieldset>
	<input type="hidden" name="<?php echo esc_attr( $args['label_for'] ); ?>">
	<input type="hidden" name="<?php echo esc_attr( $args['label_media'] ); ?>">
	<input type="hidden" name="<?php echo esc_attr( $args['label_sensitive'] ); ?>">
	<label>
		<input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked( $args['option'] ); ?>>
		<span><?php esc_html_e( 'Update status when publishing new comics', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" id="<?php echo esc_attr( $args['label_media'] ); ?>" name="<?php echo esc_attr( $args['label_media'] ); ?>" value="1" <?php checked( $args['option_media'] ); ?>>
		<span><?php esc_html_e( 'Include comic media with status updates', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" id="<?php echo esc_attr( $args['label_sensitive'] ); ?>" name="<?php echo esc_attr( $args['label_sensitive'] ); ?>" value="1" <?php checked( $args['option_sensitive'] ); ?>>
		<span><?php esc_html_e( 'Comic media may contain sensitive content', 'webcomic' ); ?></span>
	</label>
</fieldset>

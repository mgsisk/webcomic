<?php
/**
 * Syndicate setting field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Settings;

if ( ! isset( $args ) ) {
	return;
}

?>

<fieldset>
	<input type="hidden" name="<?php echo esc_attr( $args['label_for'] ); ?>">
	<input type="hidden" name="<?php echo esc_attr( $args['label_preview'] ); ?>">
	<label>
		<input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked( $args['option'] ); ?>>
		<span><?php esc_html_e( 'Include in the main syndication feed', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" id="<?php echo esc_attr( $args['label_preview'] ); ?>" name="<?php echo esc_attr( $args['label_preview'] ); ?>" value="1" <?php checked( $args['option_preview'] ); ?>>
		<span><?php esc_html_e( 'Include comic previews in syndication feeds', 'webcomic' ); ?></span>
	</label>
</fieldset>

<?php
/**
 * Uninstall field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Plugin;

if ( ! isset( $args ) ) {
	return;
}

?>

<fieldset>
	<label>
		<input type="hidden" name="<?php echo esc_attr( $args['label_for'] ); ?>">
		<input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked( $args['option'] ); ?>>
		<span><?php esc_html_e( 'Delete data when components are disabled or the plugin is deactivated', 'webcomic' ); ?></span>
	</label>
</fieldset>

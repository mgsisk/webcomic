<?php
/**
 * Archives setting field
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
	<input type="hidden" name="<?php echo esc_attr( $args['label_sort'] ); ?>">
	<label>
		<input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked( $args['option'] ); ?>>
		<span><?php esc_html_e( 'Include in standard date archives', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" id="<?php echo esc_attr( $args['label_sort'] ); ?>" name="<?php echo esc_attr( $args['label_sort'] ); ?>" value="1" <?php checked( $args['option_sort'] ); ?>>
		<span><?php esc_html_e( 'Sort comics in chronological order', 'webcomic' ); ?></span>
	</label><br>
</fieldset>

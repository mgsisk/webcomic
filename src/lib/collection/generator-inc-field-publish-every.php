<?php
/**
 * Publish every field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

if ( ! isset( $args ) ) {
	return;
}

?>

<input type="hidden" name="<?php echo esc_attr( $args['label_for'] ); ?>[]">
<label>
	<input type="checkbox" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="1" <?php checked( in_array( 1, $args['option'], true ) ); ?>>
	<span><?php esc_html_e( 'Monday', 'webcomic' ); ?></span>
</label>
<label>
	<input type="checkbox" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="2" <?php checked( in_array( 2, $args['option'], true ) ); ?>>
	<span><?php esc_html_e( 'Tuesday', 'webcomic' ); ?></span>
</label>
<label>
	<input type="checkbox" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="3" <?php checked( in_array( 3, $args['option'], true ) ); ?>>
	<span><?php esc_html_e( 'Wednesday', 'webcomic' ); ?></span>
</label>
<label>
	<input type="checkbox" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="4" <?php checked( in_array( 4, $args['option'], true ) ); ?>>
	<span><?php esc_html_e( 'Thursday', 'webcomic' ); ?></span>
</label>
<label>
	<input type="checkbox" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="5" <?php checked( in_array( 5, $args['option'], true ) ); ?>>
	<span><?php esc_html_e( 'Friday', 'webcomic' ); ?></span>
</label>
<label>
	<input type="checkbox" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="6" <?php checked( in_array( 6, $args['option'], true ) ); ?>>
	<span><?php esc_html_e( 'Saturday', 'webcomic' ); ?></span>
</label>
<label>
	<input type="checkbox" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="7" <?php checked( in_array( 7, $args['option'], true ) ); ?>>
	<span><?php esc_html_e( 'Sunday', 'webcomic' ); ?></span>
</label>

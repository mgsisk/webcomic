<?php
/**
 * Debug field
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
		<span><?php esc_html_e( 'Enable debugging', 'webcomic' ); ?></span>
	</label>
</fieldset>
<p class="description"><?php esc_html_e( "This could break your site; make sure you know what you're doing.", 'webcomic' ); ?></p>

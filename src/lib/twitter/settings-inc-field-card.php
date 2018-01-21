<?php
/**
 * Card field
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
	<label>
		<input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked( $args['option'] ); ?>>
		<span><?php esc_html_e( 'Add card meta tags to comic pages', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" id="<?php echo esc_attr( $args['label_media'] ); ?>" name="<?php echo esc_attr( $args['label_media'] ); ?>" value="1" <?php checked( $args['option_media'] ); ?>>
		<span><?php esc_html_e( 'Include comic media card meta tags', 'webcomic' ); ?></span>
	</label>
</fieldset>
<p class="description">
	<?php
	// Translators: Twitter Card Validator hyperlink.
	printf( esc_html__( 'You must validate a URL with the %s to use Twitter Cards.', 'webcomic' ), $args['twitter'] ); // WPCS: xss ok.
	?>
</p>

<?php
/**
 * Description field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Settings;

if ( ! isset( $args ) ) {
	return;
}

?>

<textarea id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>" rows="5" cols="50" class="large-text"><?php echo esc_textarea( $args['option'] ); ?></textarea>
<p class="description"><?php esc_html_e( 'The description is not prominent by default; some themes may show it, though.', 'webcomic' ); ?></p>

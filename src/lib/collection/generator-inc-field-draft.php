<?php
/**
 * Draft field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

if ( ! isset( $args ) ) {
	return;
}

?>

<input type="hidden" name="<?php echo esc_attr( $args['label_for'] ); ?>">
<label>
	<input type="checkbox" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked( $args['option'] ); ?>>
	<span><?php esc_html_e( 'Save comics as drafts', 'webcomic' ); ?></span>
</label><br>

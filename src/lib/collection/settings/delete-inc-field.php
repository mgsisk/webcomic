<?php
/**
 * Delete field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Settings;

if ( ! isset( $args ) ) {
	return;
}

?>

<input type="text" name="<?php echo esc_attr( $args['label_for'] ); ?>" class="regular-text" id="<?php echo esc_attr( $args['label_for'] ); ?>">
<p class="description"><?php esc_html_e( "Type the collection name here to confirm that you would like to delete this collection and all of it's associated data. Uploaded media will not be deleted.", 'webcomic' ); ?></p>

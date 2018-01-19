<?php
/**
 * Collection field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

if ( ! isset( $args ) ) {
	return;
}

?>

<select id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>">
	<?php foreach ( webcomic( 'option.collections' ) as $collection ) : ?>
		<option value="<?php echo esc_attr( $collection ); ?>" <?php selected( $collection, $args['option'] ); ?>><?php echo esc_html( get_webcomic_collection_title( $collection ) ); ?></option>
	<?php endforeach; ?>
</select>

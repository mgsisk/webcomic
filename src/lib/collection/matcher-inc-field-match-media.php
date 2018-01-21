<?php
/**
 * Match media field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

if ( ! isset( $args ) ) {
	return;
}

?>

<select id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>">
	<option value="post_title" <?php selected( 'post_title', $args['option'] ); ?>><?php esc_html_e( 'Title', 'webcomic' ); ?></option>
	<option value="post_name" <?php selected( 'post_name', $args['option'] ); ?>><?php esc_html_e( 'Slug', 'webcomic' ); ?></option>
	<option value="post_date" <?php selected( 'post_date', $args['option'] ); ?>><?php esc_html_e( 'Date', 'webcomic' ); ?></option>
	<option value="guid" <?php selected( 'guid', $args['option'] ); ?>><?php esc_html_e( 'Filename', 'webcomic' ); ?></option>
</select>

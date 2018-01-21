<?php
/**
 * Comics list widget settings
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Widget;

if ( ! isset( $this, $instance ) ) {
	return;
}

?>

<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'webcomic' ); ?></label>
	<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat">
</p>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'posts_per_page' ) ); ?>"><?php esc_html_e( 'Number of comics to show:', 'webcomic' ); ?></label>
	<input type="number" id="<?php echo esc_attr( $this->get_field_id( 'posts_per_page' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'posts_per_page' ) ); ?>" value="<?php echo esc_attr( $instance['posts_per_page'] ); ?>" class="tiny-text" min="-1" size="3">
</p>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>"><?php esc_html_e( 'Link:', 'webcomic' ); ?></label>
	<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link' ) ); ?>" value="<?php echo esc_attr( $instance['link'] ); ?>" class="widefat">
</p>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'format' ) ); ?>"><?php esc_html_e( 'Format:', 'webcomic' ); ?></label>
	<select id="<?php echo esc_attr( $this->get_field_id( 'format' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'format' ) ); ?>" class="widefat">
		<option value=""><?php esc_html_e( 'None', 'webcomic' ); ?></option>
		<option value="cloud" <?php selected( 'cloud', $instance['format'] ); ?>><?php esc_html_e( 'Cloud', 'webcomic' ); ?></option>
		<option value="ordered" <?php selected( 'ordered', $instance['format'] ); ?>><?php esc_html_e( 'Ordered', 'webcomic' ); ?></option>
		<option value="plain" <?php selected( 'plain', $instance['format'] ); ?>><?php esc_html_e( 'Plain', 'webcomic' ); ?></option>
		<option value="select" <?php selected( 'select', $instance['format'] ); ?>><?php esc_html_e( 'Select', 'webcomic' ); ?></option>
		<option value="unordered" <?php selected( 'unordered', $instance['format'] ); ?>><?php esc_html_e( 'Unordered', 'webcomic' ); ?></option>
	</select>
</p>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'post_type' ) ); ?>"><?php esc_html_e( 'Related to:', 'webcomic' ); ?></label>
	<select id="<?php echo esc_attr( $this->get_field_id( 'post_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_type' ) ); ?>" class="widefat">
		<option value=""><?php esc_html_e( '(current collection)', 'webcomic' ); ?></option>
		<option value="webcomic" <?php selected( 'webcomic', $instance['post_type'] ); ?>><?php esc_html_e( '(current comic)', 'webcomic' ); ?></option>
		<?php foreach ( webcomic( 'option.collections' ) as $collection ) : ?>
			<option value="<?php echo esc_attr( $collection ); ?>" <?php selected( $collection, $instance['post_type'] ); ?>><?php echo esc_html( get_webcomic_collection_title( $collection ) ); ?></option>
		<?php endforeach; ?>
	</select>
</p>
<hr>
<p class="description"><small><?php esc_html_e( "(current collection) can't always be determined.", 'webcomic' ); ?></small></p>

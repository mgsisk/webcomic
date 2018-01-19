<?php
/**
 * Collections list widget settings
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
	<label for="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>"><?php esc_html_e( 'Number of collections to show:', 'webcomic' ); ?></label>
	<input type="number" id="<?php echo esc_attr( $this->get_field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'limit' ) ); ?>" value="<?php echo esc_attr( $instance['limit'] ); ?>" class="tiny-text" min="0" size="3">
</p>
<p>
	<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'related' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'related' ) ); ?>" value="1" <?php checked( $instance['related'] ); ?>>
	<label for="<?php echo esc_attr( $this->get_field_id( 'related' ) ); ?>"><?php esc_html_e( 'Related to the current comic', 'webcomic' ); ?></label>
</p>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>"><?php esc_html_e( 'Link:', 'webcomic' ); ?></label>
	<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link' ) ); ?>" value="<?php echo esc_attr( $instance['link'] ); ?>" class="widefat">
</p>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'link_relation' ) ); ?>"><?php esc_html_e( 'Link to:', 'webcomic' ); ?></label>
	<select id="<?php echo esc_attr( $this->get_field_id( 'link_relation' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link_relation' ) ); ?>" class="widefat">
		<option value=""><?php esc_html_e( 'Archive page', 'webcomic' ); ?></option>
		<option value="first" <?php selected( 'first', $instance['link_relation'] ); ?>><?php esc_html_e( 'First comic', 'webcomic' ); ?></option>
		<option value="last" <?php selected( 'last', $instance['link_relation'] ); ?>><?php esc_html_e( 'Last comic', 'webcomic' ); ?></option>
		<option value="random" <?php selected( 'random', $instance['link_relation'] ); ?>><?php esc_html_e( 'Random comic', 'webcomic' ); ?></option>
	</select>
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
	<label for="<?php echo esc_attr( $this->get_field_id( 'webcomics' ) ); ?>"><?php esc_html_e( 'Comic link:', 'webcomic' ); ?></label>
	<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'webcomics' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'webcomics' ) ); ?>" value="<?php echo esc_attr( $instance['webcomics'] ); ?>" class="widefat">
	<small><?php esc_html_e( 'Include comic links in the list by providing comic link text. Some formats never list comics.', 'webcomic' ); ?></small>
</p>

<?php
/**
 * Comic prints list widget settings
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Commerce\Widget;

if ( ! isset( $this, $instance ) ) {
	return;
}

?>

<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( 'post_title' ) ); ?>">
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'webcomic' ); ?></label>
	<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat">
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
<label for="<?php echo esc_attr( $this->get_field_id( 'post_title' ) ); ?>"><?php esc_html_e( 'Comic:', 'webcomic' ); ?></label>
<div data-input="<?php echo esc_attr( $this->get_field_name( 'post' ) ); ?>" data-size="medium" data-webcomic-search="<?php echo esc_attr( $this->get_field_id( 'post_title' ) ); ?>">
	<?php if ( $instance['post'] ) : ?>
		<strong><?php echo esc_html( get_the_title( $instance['post'] ) ); ?></strong><br>
		<?php webcomic_media( 'medium', $instance['post'] ); ?>
	<?php endif; ?>
	<noscript><?php esc_html_e( 'Comic search requires JavaScript.', 'webcomic' ); ?></noscript>
</div>
<input type="hidden" id="<?php echo esc_attr( $this->get_field_id( 'post' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post' ) ); ?>" value="<?php echo esc_attr( $instance['post'] ); ?>">
<br>

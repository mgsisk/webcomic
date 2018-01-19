<?php
/**
 * Comic transcripts list widget settings
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe\Widget;

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
	<label for="<?php echo esc_attr( $this->get_field_id( 'posts_per_page' ) ); ?>"><?php esc_html_e( 'Number of transcripts to show:', 'webcomic' ); ?></label>
	<input type="number" id="<?php echo esc_attr( $this->get_field_id( 'posts_per_page' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'posts_per_page' ) ); ?>" value="<?php echo esc_attr( $instance['posts_per_page'] ); ?>" class="tiny-text" min="-1" size="3">
</p>
<p>
	<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'pending' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'pending' ) ); ?>" value="1" <?php checked( $instance['pending'] ); ?>>
	<label for="<?php echo esc_attr( $this->get_field_id( 'pending' ) ); ?>"><?php esc_html_e( 'Include pending transcripts', 'webcomic' ); ?></label>
</p>
<p>
	<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'languages' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'languages' ) ); ?>" value="1" <?php checked( $instance['languages'] ); ?>>
	<label for="<?php echo esc_attr( $this->get_field_id( 'languages' ) ); ?>"><?php esc_html_e( 'Include language selector', 'webcomic' ); ?></label>
</p>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'item' ) ); ?>"><?php esc_html_e( 'Item:', 'webcomic' ); ?></label>
	<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'item' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'item' ) ); ?>" value="<?php echo esc_attr( $instance['item'] ); ?>" class="widefat">
</p>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'format' ) ); ?>"><?php esc_html_e( 'Format:', 'webcomic' ); ?></label>
	<select id="<?php echo esc_attr( $this->get_field_id( 'format' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'format' ) ); ?>" class="widefat">
		<option value=""><?php esc_html_e( 'None', 'webcomic' ); ?></option>
		<option value="ordered" <?php selected( 'ordered', $instance['format'] ); ?>><?php esc_html_e( 'Ordered', 'webcomic' ); ?></option>
		<option value="plain" <?php selected( 'plain', $instance['format'] ); ?>><?php esc_html_e( 'Plain', 'webcomic' ); ?></option>
		<option value="unordered" <?php selected( 'unordered', $instance['format'] ); ?>><?php esc_html_e( 'Unordered', 'webcomic' ); ?></option>
	</select>
</p>
<label for="<?php echo esc_attr( $this->get_field_id( 'post_title' ) ); ?>"><?php esc_html_e( 'Comic:', 'webcomic' ); ?></label>
<div data-input="<?php echo esc_attr( $this->get_field_name( 'post_parent' ) ); ?>" data-size="medium" data-webcomic-search="<?php echo esc_attr( $this->get_field_id( 'post_title' ) ); ?>">
	<?php if ( $instance['post_parent'] ) : ?>
		<strong><?php echo esc_html( get_the_title( $instance['post_parent'] ) ); ?></strong><br>
		<?php webcomic_media( 'medium', $instance['post_parent'] ); ?>
	<?php endif; ?>
	<noscript><?php esc_html_e( 'Comic search requires JavaScript.', 'webcomic' ); ?></noscript>
</div>
<input type="hidden" id="<?php echo esc_attr( $this->get_field_id( 'post_parent' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_parent' ) ); ?>" value="<?php echo esc_attr( $instance['post_parent'] ); ?>">
<br>

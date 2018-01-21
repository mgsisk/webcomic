<?php
/**
 * Comic media widget settings
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Widget;

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
	<label for="<?php echo esc_attr( $this->get_field_id( 'format' ) ); ?>"><?php esc_html_e( 'Format:', 'webcomic' ); ?></label>
	<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'format' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'format' ) ); ?>" value="<?php echo esc_attr( $instance['format'] ); ?>" class="widefat">
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
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>"><?php esc_html_e( 'Link to:', 'webcomic' ); ?></label>
	<select id="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link' ) ); ?>" class="widefat">
		<option value=""><?php esc_html_e( 'None', 'webcomic' ); ?></option>
		<option value="self" <?php selected( 'self', $instance['link'] ); ?>><?php esc_html_e( 'Current comic', 'webcomic' ); ?></option>
		<option value="first" <?php selected( 'first', $instance['link'] ); ?>><?php esc_html_e( 'First comic', 'webcomic' ); ?></option>
		<option value="previous" <?php selected( 'previous', $instance['link'] ); ?>><?php esc_html_e( 'Previous comic', 'webcomic' ); ?></option>
		<option value="next" <?php selected( 'next', $instance['link'] ); ?>><?php esc_html_e( 'Next comic', 'webcomic' ); ?></option>
		<option value="last" <?php selected( 'last', $instance['link'] ); ?>><?php esc_html_e( 'Last comic', 'webcomic' ); ?></option>
		<option value="random" <?php selected( 'random', $instance['link'] ); ?>><?php esc_html_e( 'Random comic', 'webcomic' ); ?></option>
	</select>
</p>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'related_by' ) ); ?>"><?php esc_html_e( 'Related by:', 'webcomic' ); ?></label>
	<select id="<?php echo esc_attr( $this->get_field_id( 'related_by' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'related_by' ) ); ?>" class="widefat">
		<option value=""><?php esc_html_e( '(current collection)', 'webcomic' ); ?></option>
		<optgroup label="<?php esc_attr_e( 'Collections', 'webcomic' ); ?>">
			<?php foreach ( $instance['collections'] as $collection ) : ?>
				<option value="<?php echo esc_attr( $collection ); ?>" <?php selected( $collection, $instance['related_by'] ); ?>><?php echo esc_html( get_webcomic_collection_title( $collection ) ); ?></option>
			<?php endforeach; ?>
		</optgroup>
		<optgroup label="<?php esc_attr_e( 'Taxonomies', 'webcomic' ); ?>">
			<?php foreach ( $instance['taxonomies'] as $taxonomy ) : ?>
				<option value="<?php echo esc_attr( $taxonomy->name ); ?>" <?php selected( $taxonomy->name, $instance['related_by'] ); ?>><?php echo esc_html( $taxonomy->labels->name ); ?></option>
			<?php endforeach; ?>
		</optgroup>
	</select>
</p>

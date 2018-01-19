<?php
/**
 * Comic link widget settings
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
<label for="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>"><?php esc_html_e( 'Link:', 'webcomic' ); ?></label>
<div data-input="<?php echo esc_attr( $this->get_field_name( 'media' ) ); ?>" data-webcomic-media-manager>
	<?php $instance['media'] && printf( '%s', wp_get_attachment_image( $instance['media'], 'medium' ) ); ?>
	<noscript><?php esc_html_e( 'Media management requires JavaScript.', 'webcomic' ); ?></noscript>
</div>
<input type="hidden" id="<?php echo esc_attr( $this->get_field_id( 'media' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'media' ) ); ?>" value="<?php echo esc_attr( $instance['media'] ); ?>">
<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link' ) ); ?>" value="<?php echo esc_attr( $instance['link'] ); ?>" class="widefat">
<br>
<?php if ( ! $this->relation ) : ?>
	<br>
	<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( 'post_type' ) ); ?>">
	<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( 'related_by' ) ); ?>">
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
<?php
	return;
endif;
?>
<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( 'post' ) ); ?>" value="0">
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'post_type' ) ); ?>"><?php esc_html_e( 'Collection:', 'webcomic' ); ?></label>
	<select id="<?php echo esc_attr( $this->get_field_id( 'post_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_type' ) ); ?>" class="widefat">
		<option value=""><?php esc_html_e( '(current collection)', 'webcomic' ); ?></option>
		<?php foreach ( $instance['collections'] as $collection ) : ?>
			<option value="<?php echo esc_attr( $collection ); ?>" <?php selected( $collection, $instance['post_type'] ); ?>><?php echo esc_html( get_webcomic_collection_title( $collection ) ); ?></option>
		<?php endforeach; ?>
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
<hr>
<p class="description"><small><?php esc_html_e( "(current collection) can't always be determined.", 'webcomic' ); ?></small></p>

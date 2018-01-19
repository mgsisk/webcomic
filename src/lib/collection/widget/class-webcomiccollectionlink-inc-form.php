<?php
/**
 * Collection link widget settings
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Widget;

if ( ! isset( $this, $instance ) ) {
	return;
}

?>

<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( 'query_url' ) ); ?>" value="0">
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'webcomic' ); ?></label>
	<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat">
</p>
<label for="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>"><?php esc_html_e( 'Link:', 'webcomic' ); ?></label>
<div data-input="<?php echo esc_attr( $this->get_field_name( 'media' ) ); ?>" data-value="<?php echo esc_attr( $instance['media'] ); ?>" data-webcomic-media-manager>
	<?php $instance['media'] && printf( '%s', wp_get_attachment_image( $instance['media'], 'medium' ) ); ?>
	<noscript><?php esc_html_e( 'Media management requires JavaScript.', 'webcomic' ); ?></noscript>
</div>
<input type="hidden" id="<?php echo esc_attr( $this->get_field_id( 'media' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'media' ) ); ?>" value="<?php echo esc_attr( $instance['media'] ); ?>">
<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link' ) ); ?>" value="<?php echo esc_attr( $instance['link'] ); ?>" class="widefat">
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'relation' ) ); ?>"><?php esc_html_e( 'Link to:', 'webcomic' ); ?></label>
	<select id="<?php echo esc_attr( $this->get_field_id( 'relation' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'relation' ) ); ?>" class="widefat">
		<option value=""><?php esc_html_e( 'Archive page', 'webcomic' ); ?></option>
		<option value="first" <?php selected( 'first', $instance['relation'] ); ?>><?php esc_html_e( 'First comic', 'webcomic' ); ?></option>
		<option value="last" <?php selected( 'last', $instance['relation'] ); ?>><?php esc_html_e( 'Last comic', 'webcomic' ); ?></option>
		<option value="random" <?php selected( 'random', $instance['relation'] ); ?>><?php esc_html_e( 'Random comic', 'webcomic' ); ?></option>
	</select>
</p>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'collection' ) ); ?>"><?php esc_html_e( 'Collection:', 'webcomic' ); ?></label>
	<select id="<?php echo esc_attr( $this->get_field_id( 'collection' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'collection' ) ); ?>" class="widefat">
		<option value=""><?php esc_html_e( '(current collection)', 'webcomic' ); ?></option>
		<?php foreach ( webcomic( 'option.collections' ) as $collection ) : ?>
			<option value="<?php echo esc_attr( $collection ); ?>" <?php selected( $collection, $instance['collection'] ); ?>><?php echo esc_html( get_webcomic_collection_title( $collection ) ); ?></option>
		<?php endforeach; ?>
	</select>
</p>
<hr>
<p class="description"><small><?php esc_html_e( "(current collection) can't always be determined.", 'webcomic' ); ?></small></p>

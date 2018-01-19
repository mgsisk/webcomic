<?php
/**
 * Comic term link widget settings
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Taxonomy\MetaBox;

if ( ! isset( $this, $instance ) ) {
	return;
}

?>

<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( 'term_title' ) ); ?>">
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'webcomic' ); ?></label>
	<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat">
</p>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>"><?php esc_html_e( 'Link:', 'webcomic' ); ?></label>
	<div data-input="<?php echo esc_attr( $this->get_field_name( 'media' ) ); ?>" data-webcomic-media-manager>
		<?php $instance['media'] && printf( '%s', wp_get_attachment_image( $instance['media'], 'medium' ) ); ?>
		<noscript><?php esc_html_e( 'Media management requires JavaScript.', 'webcomic' ); ?></noscript>
	</div>
	<input type="hidden" id="<?php echo esc_attr( $this->get_field_id( 'media' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'media' ) ); ?>" value="<?php echo esc_attr( $instance['media'] ); ?>">
	<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link' ) ); ?>" value="<?php echo esc_attr( $instance['link'] ); ?>" class="widefat">
</p>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'link_post_relation' ) ); ?>"><?php esc_html_e( 'Link to:', 'webcomic' ); ?></label>
	<select id="<?php echo esc_attr( $this->get_field_id( 'link_post_relation' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link_post_relation' ) ); ?>" class="widefat">
		<option value=""><?php esc_html_e( 'Archive page', 'webcomic' ); ?></option>
		<option value="first" <?php selected( 'first', $instance['link_post_relation'] ); ?>><?php esc_html_e( 'First comic', 'webcomic' ); ?></option>
		<option value="last" <?php selected( 'last', $instance['link_post_relation'] ); ?>><?php esc_html_e( 'Last comic', 'webcomic' ); ?></option>
		<option value="random" <?php selected( 'random', $instance['link_post_relation'] ); ?>><?php esc_html_e( 'Random comic', 'webcomic' ); ?></option>
	</select>
</p>
<?php if ( ! $this->relation ) : ?>
	<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( 'collection' ) ); ?>">
	<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( 'link_post_relation' ) ); ?>">
	<label for="<?php echo esc_attr( $this->get_field_id( 'term_title' ) ); ?>"><?php echo esc_html( $instance['term_search_label'] ); ?></label>
	<div
		data-input="<?php echo esc_attr( $this->get_field_name( 'term' ) ); ?>"
		data-webcomic-term-search="<?php echo esc_attr( $this->get_field_id( 'term_title' ) ); ?>"
		data-webcomic-term-taxonomy="<?php echo esc_attr( $instance['taxonomy_type'] ); ?>"
		data-webcomic-term-search-info="<?php echo esc_attr( $instance['term_search_info'] ); ?>"
		data-webcomic-term-search-null="<?php echo esc_attr( $instance['term_search_null'] ); ?>"
		data-webcomic-term-search-search="<?php echo esc_attr( $instance['term_search_search'] ); ?>"
		data-webcomic-term-search-remove="<?php echo esc_attr( $instance['term_search_remove'] ); ?>"
	>
		<?php if ( $instance['term'] ) : ?>
			<strong><?php echo esc_html( get_the_title( $instance['post'] ) ); ?></strong><br>
			<?php webcomic_term_media( 'medium', $instance['term'] ); ?>
		<?php endif; ?>
		<noscript><?php esc_html_e( 'Comic term search requires JavaScript.', 'webcomic' ); ?></noscript>
	</div>
	<input type="hidden" id="<?php echo esc_attr( $this->get_field_id( 'term' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'term' ) ); ?>" value="<?php echo esc_attr( $instance['term'] ); ?>">
	<br>
<?php
	return;
endif;
?>
<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( 'term' ) ); ?>" value="0">
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

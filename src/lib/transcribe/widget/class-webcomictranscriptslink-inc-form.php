<?php
/**
 * Comic transcripts link widget settings
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
<label for="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>"><?php esc_html_e( 'Link:', 'webcomic' ); ?></label>
<div data-input="<?php echo esc_attr( $this->get_field_name( 'media' ) ); ?>" data-webcomic-media-manager>
	<?php $instance['media'] && printf( '%s', wp_get_attachment_image( $instance['media'], 'medium' ) ); ?>
	<noscript><?php esc_html_e( 'Media management requires JavaScript.', 'webcomic' ); ?></noscript>
</div>
<input type="hidden" id="<?php echo esc_attr( $this->get_field_id( 'media' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'media' ) ); ?>" value="<?php echo esc_attr( $instance['media'] ); ?>">
<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link' ) ); ?>" value="<?php echo esc_attr( $instance['link'] ); ?>" class="widefat">
<p>
	<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'transcribe' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'transcribe' ) ); ?>" value="1" <?php checked( $instance['transcribe'] ); ?>>
	<label for="<?php echo esc_attr( $this->get_field_id( 'transcribe' ) ); ?>"><?php esc_html_e( 'Link to the transcript form', 'webcomic' ); ?></label>
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

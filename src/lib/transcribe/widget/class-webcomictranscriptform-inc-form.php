<?php
/**
 * Comic transcript form widget settings
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
	<label for="<?php echo esc_attr( $this->get_field_id( 'transcript_notes_after' ) ); ?>"><?php esc_html_e( 'Notes:', 'webcomic' ); ?></label>
	<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'transcript_notes_after' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'transcript_notes_after' ) ); ?>" value="<?php echo esc_attr( $instance['transcript_notes_after'] ); ?>" class="widefat">
	<small><?php esc_html_e( 'Notes are displayed just below the transcript field.', 'webcomic' ); ?></small>
</p>
<p>
	<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'url_field' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'url_field' ) ); ?>" value="1" <?php checked( $instance['url_field'] ); ?>>
	<label for="<?php echo esc_attr( $this->get_field_id( 'url_field' ) ); ?>"><?php esc_html_e( 'Show the author URL field', 'webcomic' ); ?></label>
</p>
<p>
	<input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'languages_field' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'languages_field' ) ); ?>" value="1" <?php checked( $instance['languages_field'] ); ?>>
	<label for="<?php echo esc_attr( $this->get_field_id( 'languages_field' ) ); ?>"><?php esc_html_e( 'Show the transcript language field', 'webcomic' ); ?></label>
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

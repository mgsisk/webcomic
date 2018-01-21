<?php
/**
 * Deprecated widget form
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Compat\Widget;

if ( ! isset( $this, $instance ) ) {
	return;
}

?>

<p style="color:#d98500;font-weight:600">
	<?php
	// Translators: Alternative widget name.
	printf( esc_html__( 'This widget is deprecated; use %s instead.', 'webcomic' ), esc_html( $instance['use_instead'] ) );
	?>
</p>

<?php foreach ( $instance as $key => $value ) : ?>
	<input type="hidden" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" value="<?php echo esc_attr( $value ); ?>">
<?php endforeach; ?>

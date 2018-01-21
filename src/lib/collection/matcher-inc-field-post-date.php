<?php
/**
 * Post date field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

if ( ! isset( $args ) ) {
	return;
}

?>

<input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="<?php echo esc_attr( $args['option'] ); ?>">
<p class="description">
	<?php
	printf(
		// Translators: Hyperlink to PHP date format documentation.
		esc_html__( 'The %s to use for comparison.', 'webcomic' ),
		'<a href="https://php.net/manual/en/function.date.php" target="_blank">' . esc_html__( 'date format', 'webcomic' ) . '</a>'
	);
	?>
</p>

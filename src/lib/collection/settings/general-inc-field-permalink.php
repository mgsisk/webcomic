<?php
/**
 * Permalink field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Settings;

if ( ! isset( $args ) ) {
	return;
}

?>

<input type="text" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="<?php echo esc_attr( $args['option'] ); ?>" class="regular-text">
<p class="description">
	<?php
	// Translators: An example collection permalink.
	printf( esc_html__( 'The permalink is the URL-friendly text used to build links to your comics, like %s', 'webcomic' ), esc_html( get_site_url() ) );

	echo '/<strong>' . esc_html( $args['option'] ) . '</strong>/comic-post';
	?>
</p>

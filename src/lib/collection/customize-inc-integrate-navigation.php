<?php
/**
 * Comic navigation integration template
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

$classes = '';

if ( get_theme_mod( 'webcomic_integrate_navigation_keyboard' ) ) {
	$classes .= 'webcomic-keyboard ';
}

if ( get_theme_mod( 'webcomic_integrate_navigation_gestures' ) ) {
	$classes .= 'webcomic-gestures ';
}

?>

<div class="widget-area <?php echo esc_attr( $classes ); ?>webcomic-navigation">
	<?php
	if ( ! dynamic_sidebar( get_post_type() . '-navigation' ) && ! dynamic_sidebar( 'webcomic-navigation' ) ) {
		the_widget( 'Mgsisk\Webcomic\Collection\Widget\FirstWebcomicLink' );
		the_widget( 'Mgsisk\Webcomic\Collection\Widget\PreviousWebcomicLink' );
		the_widget( 'Mgsisk\Webcomic\Collection\Widget\RandomWebcomicLink' );
		the_widget( 'Mgsisk\Webcomic\Collection\Widget\NextWebcomicLink' );
		the_widget( 'Mgsisk\Webcomic\Collection\Widget\LastWebcomicLink' );
	}
	?>
</div>

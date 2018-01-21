<?php
/**
 * Comic meta integration template
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

?>

<div class="widget-area webcomic-meta">
	<?php
	if ( ! dynamic_sidebar( get_post_type() . '-meta' ) && ! dynamic_sidebar( 'webcomic-meta' ) ) {
		/**
		 * Integrate comic meta data defaults.
		 *
		 * This action provides a way for hooks to add default widgets to the
		 * Webcomic Meta widget area.
		 */
		do_action( 'webcomic_integrate_meta_default' );
	}
	?>
</div>

<?php
/**
 * Comic media integration template
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

?>

<div class="widget-area webcomic-media">
	<?php
	if ( ! dynamic_sidebar( get_post_type() . '-media' ) && ! dynamic_sidebar( 'webcomic-media' ) ) {
		the_widget( 'Mgsisk\Webcomic\Collection\Widget\WebcomicMedia' );
	}
	?>
</div>

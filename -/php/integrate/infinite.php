<?php
/**
 * Automagic integration for infinite requests.
 * 
 * Unlike the other integration templates this file will be loaded
 * for infinite scroll requests regardless of the integration setting if no
 * infinite scroll template exists.
 * 
 * @package Webcomic
 * @uses the_webcomic()
 */
?>

<div class="integrated-webcomic">
	<div class="webcomic-img"><?php the_webcomic( "full" ); ?></div><!-- .webcomic-img -->
</div><!-- .integrated-webcomic -->
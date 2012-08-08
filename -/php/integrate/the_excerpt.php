<?php
/** Automagic integration for the_excerpt.
 * 
 * @package Webcomic
 * @uses WebcomicTag::the_webcomic()
 */
$prepend = sprintf( '<div class="integrated-webcomic"><div class="webcomic-img">%s</div><!-- .webcomic-img --></div><!-- .integrated-webcomic -->', WebcomicTag::the_webcomic( 'medium', 'self' ) );
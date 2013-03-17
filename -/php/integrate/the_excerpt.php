<?php
/** Automagic integration for the_excerpt.
 * 
 * @package Webcomic
 * @uses WebcomicTag::the_webcomic()
 */
$prepend = '<div class="integrated-webcomic"><div class="webcomic-img">' . WebcomicTag::the_webcomic( 'medium', 'self' ) . '</div><!-- .webcomic-img --></div><!-- .integrated-webcomic -->';
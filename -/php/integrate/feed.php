<?php
/** Basic feed integration.
 * 
 * @package Webcomic
 */
global $post;

$prepend = '<p><a href="' . get_permalink( $post->ID ) . '">';

foreach ( $attachments as $attachment ) {
	$prepend .= wp_get_attachment_image( $attachment->ID, $feed_size );
}

$prepend .= '</a></p>';
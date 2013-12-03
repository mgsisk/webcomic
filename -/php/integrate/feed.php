<?php
/**
 * Basic feed integration.
 * 
 * @package Webcomic
 */
global $post;

$link = get_permalink( $post->ID );
$images = "";

foreach ( $attachments as $attachment ) {
	$images .= wp_get_attachment_image( $attachment->ID, $feed_size );
}

$prepend = "<p><a href='{$link}'>{$images}</a></p>";
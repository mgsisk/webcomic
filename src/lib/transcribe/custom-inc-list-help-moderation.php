<?php
/**
 * Comic transcript status help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe;

?>

<p><?php esc_html_e( 'Comic transcript status is indicated by colored bars along the left side of the list:', 'webcomic' ); ?></p>
<ul>
	<li><?php esc_html_e( 'A red bar means the comic transcript is a draft and is waiting for you to moderate it.', 'webcomic' ); ?></li>
	<li><?php esc_html_e( "A blue bar means the comic transcript is pending review. Comic transcripts pending review may be edited and resubmitted as drafts if the transcript's parent comic allows new transcripts.", 'webcomic' ); ?></li>
	<li><?php esc_html_e( 'A black bar means the comic transcript is private. A black bar means the comic transcript is private. Only users with the correct capabilities can view private transcripts.', 'webcomic' ); ?></li>
</ul>
<p><?php esc_html_e( 'You can also narrow the list of transcripts to a particular IP address by clicking an IP address in the Authors column. Only the first author will have their full details listed here if a transcript has more than one author.', 'webcomic' ); ?></p>

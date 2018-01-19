<?php
/**
 * Comic transcript list help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe;

?>

<p><?php esc_html_e( "This page provides access to all comic transcripts. You can customize the display of this page's content in a number of ways:", 'webcomic' ); ?></p>
<ul>
	<li><?php esc_html_e( 'You can hide/display columns based on your needs and decide how many transcripts to list per screen using the Screen Options tab.', 'webcomic' ); ?></li>
	<li><?php esc_html_e( 'You can filter the list of transcripts by post status using the text links above the transcripts list to only show transcripts with that status. The default view is to show all transcripts. Orphaned transcripts are comic transcripts with no parent comic.', 'webcomic' ); ?></li>
	<li><?php esc_html_e( 'You can refine the list to show only transcripts from a specific month, transcripts assigned to comics in a certain collection, or transcripts tagged with a certain language by using the dropdown menus above the transcripts list. Click the Filter button after making your selection.', 'webcomic' ); ?></li>
</ul>

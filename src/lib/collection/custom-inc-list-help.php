<?php
/**
 * Comic list help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

?>

<p><?php esc_html_e( "This page provides access to all of the comics in this collection. You can customize the display of this page's content in a number of ways:", 'webcomic' ); ?></p>
<ul>
	<li><?php esc_html_e( 'You can hide/display columns based on your needs and decide how many comics to list per screen using the Screen Options tab.', 'webcomic' ); ?></li>
	<li><?php esc_html_e( 'You can filter the list of comics by post status using the text links above the comics list to only show comics with that status. The default view is to show all comics. Orphaned comics are comics with no comic media.', 'webcomic' ); ?></li>
	<li><?php esc_html_e( 'You can view comics in a simple title list or with an excerpt using the Screen Options tab.', 'webcomic' ); ?></li>
	<li><?php esc_html_e( 'You can refine the list to show only comics from a specific month or &ndash; depending on your collection settings &ndash; in a certain taxonomy by using the dropdown menus above the posts list. Click the Filter button after making your selection. You also can refine the list by clicking on the comic author or a taxonomy term in the comics list.', 'webcomic' ); ?></li>
</ul>

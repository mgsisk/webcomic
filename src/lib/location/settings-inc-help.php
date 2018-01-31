<?php
/**
 * Location settings help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Location;

?>

<p><?php esc_html_e( 'These settings allow you to change some of the basic features related to locations:', 'webcomic' ); ?></p>
<ul>
	<li>
		<b><?php esc_html_e( 'Slug', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; The slug is the URL-friendly text used for archives. It can contain lowercase letters, numbers, and hyphens. This setting has no effect with Plain Permalinks.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Behavior', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; These settings control how locations in this collection behave.', 'webcomic' ); ?><br><br>
		<?php esc_html_e( 'Allow hierarchical organization will allow you to assign locations to other locations, creating a parent-child relationship.', 'webcomic' ); ?><br><br>
		<?php esc_html_e( 'When browsing hierarchical locations using previous/next links, it can be difficult to determine which location is previous or next. For example, if a location named Bastion has three child locations &ndash; Citadel, Dive, and East Ward &ndash; and a user is looking at Citadel, the previous location is normally Bastion (the parent of Citadel). Likewise, if a user is looking at Bastion, the next location is normally Citadel (the first child of Bastion).', 'webcomic' ); ?><br><br>
		<?php esc_html_e( "Skip redundant terms while browsing changes this default behavior so that a previous location can't be an ancestor of the current location, and a next location can't be a descendant of the current location. Using our previous example, let's say Bastion has two siblings &ndash; Anchorage and 2Fort (ordered before and after Bastion, respectively). If a user is looking at Citadel, the previous location will be Anchorage (the first location that isn't an ancestor of Citadel). Likewise, if a user is looking at Bastion, the next location will be 2Fort (the first location that isn't a descendent of Bastion).", 'webcomic' ); ?><br><br>
		<?php esc_html_e( 'Sort terms in custom order by default will force locations to list in your custom-defined order by default. You can change the actual sorting order on the location management screen.', 'webcomic' ); ?><br><br>
		<?php esc_html_e( "Include crossover comics on archive pages will cause any comic from any collection tagged with a location from this collection to show up in that location's WordPress taxonomy archive page. Otherwise, only comics from this collection will appear in the archive. You can view an archive of crossover comics for any location by adding /crossover to the end of it's archive URL.", 'webcomic' ); ?><br><br>
	</li>
</ul>

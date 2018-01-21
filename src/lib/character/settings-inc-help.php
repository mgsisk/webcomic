<?php
/**
 * Character settings help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Character;

?>

<p><?php esc_html_e( 'These settings allow you to change some of the basic features related to characters:', 'webcomic' ); ?></p>
<ul>
	<li>
		<b><?php esc_html_e( 'Slug', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; The slug is the URL-friendly text used for archives. It can contain lowercase letters, numbers, and hyphens. This setting has no effect with Plain Permalinks.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Behavior', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; These settings control how characters in this collection behave.', 'webcomic' ); ?><br><br>
		<?php esc_html_e( 'Allow hierarchical organization will allow you to assign characters to other characters, creating a parent-child relationship.', 'webcomic' ); ?><br><br>
		<?php esc_html_e( 'When browsing hierarchical characters using previous/next links, it can be difficult to determine which character is previous or next. For example, if a character named Birdie has three child characters &ndash; Charlie, Dan, and Eagle &ndash; and a user is looking at Charlie, the previous character is normally Birdie (the parent of Charlie). Likewise, if a user is looking at Birdie, the next character is normally Charlie (the first child of Birdie).', 'webcomic' ); ?><br><br>
		<?php esc_html_e( "Skip redundant terms while browsing changes this default behavior so that a previous character can't be an ancestor of the current character, and a next character can't be a descendant of the current character. Using our previous example, let's say Birdie has two siblings &ndash; Adon and Furiosa (ordered before and after Birdie, respectively). If a user is looking at Charlie, the previous character will be Adon (the first character that isn't an ancestor of Charlie). Likewise, if a user is looking at Birdie, the next character will be Furiosa (the first character that isn't a descendent of Birdie).", 'webcomic' ); ?><br><br>
		<?php esc_html_e( 'Sort terms in custom order by default will force characters to list in your custom-defined order by default. You can change the actual sorting order on the character management screen.', 'webcomic' ); ?><br><br>
		<?php esc_html_e( "Include crossover comics on archive pages will cause any comic from any collection tagged with a character from this collection to show up in that character's WordPress taxonomy archive page. Otherwise, only comics from this collection will appear in the archive. You can view an archive of crossover comics for any character by adding /crossover to the end of it's archive URL.", 'webcomic' ); ?><br><br>
	</li>
</ul>

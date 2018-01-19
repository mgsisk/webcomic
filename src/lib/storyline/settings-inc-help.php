<?php
/**
 * Storyline settings help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Storyline;

?>

<p><?php esc_html_e( 'These settings allow you to change some of the basic features related to storylines:', 'webcomic' ); ?></p>
<ul>
	<li>
		<b><?php esc_html_e( 'Slug', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; The slug is the URL-friendly text used for archives. It can contain lowercase letters, numbers, and hyphens. This setting has no effect with Plain Permalinks.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Behavior', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; These settings control how storylines in this collection behave.', 'webcomic' ); ?><br><br>
		<?php esc_html_e( 'Allow hierarchical organization will allow you to assign storylines to other storylines, creating a parent-child relationship.', 'webcomic' ); ?><br><br>
		<?php esc_html_e( 'When browsing hierarchical storylines using previous/next links, it can be difficult to determine which storyline is previous or next. For example, if a storyline named Part II has three child storylines &ndash; Chapter 1, Chapter 2, and Chapter 3 &ndash; and a user is looking at Chapter 1, the previous storyline is normally Part II (the parent of Chapter 1). Likewise, if a user is looking at Part II, the next storyline is normally Chapter 1 (the first child of Part II).', 'webcomic' ); ?><br><br>
		<?php esc_html_e( "Skip redundant terms while browsing changes this default behavior so that a previous storyline can't be an ancestor of the current storyline, and a next storyline can't be a descendant of the current storyline. Using our previous example, let's say Part II has two siblings &ndash; Part I and Part III (ordered before and after Part II, respectively). If a user is looking at Chapter 1, the previous storyline will be Part I (the first storyline that isn't an ancestor of Chapter 1). Likewise, if a user is looking at Part II, the next storyline will be Part III (the first storyline that isn't a descendent of Part II).", 'webcomic' ); ?><br><br>
		<?php esc_html_e( 'Sort terms in custom order by default will force storylines to list in your custom-defined order by default. You can change the actual sorting order on the storyline management screen.', 'webcomic' ); ?><br><br>
		<?php esc_html_e( "Include crossover comics on archive pages will cause any comic from any collection tagged with a storyline from this collection to show up in that storyline's WordPress taxonomy archive page. Otherwise, only comics from this collection will appear in the archive. You can view an archive of crossover comics for any storyline by adding /crossover to the end of it's archive URL.", 'webcomic' ); ?><br><br>
	</li>
</ul>

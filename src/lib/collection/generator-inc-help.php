<?php
/**
 * Comic generator help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

?>

<p><?php esc_html_e( "This tool is useful if you have a lot of uploaded media that you'd like to automatically generate comics for. Posts created by the generator will use the media title for the post title.", 'webcomic' ); ?></p>
<ul>
	<li>
		<b><?php esc_html_e( 'Collection', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; The collection generated comics will belong to.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Start on&hellip;', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; The date to begin publishing comics for the selected media. The generator will publish the first selected media item on this date.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Publish every&hellip;', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; The days of the week to publish the following comics. The generator works through the selected media from top to bottom, publishing on the days you select. You can change the publish order by dragging the media items in the list.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Save comics as drafts', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; When checked, the generator will save comics as drafts instead of publishing them. These comics will not appear on your site until you publish them.', 'webcomic' ); ?>
	</li>
</ul>

<?php
/**
 * General settings help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Settings;

?>

<p><?php esc_html_e( 'These settings allow you to change some of the basic information and features related to your collection:', 'webcomic' ); ?></p>
<ul>
	<li>
		<b><?php esc_html_e( 'Name', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; The name of your comic, as it will appear on your site.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Slug', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; The slug is the URL-friendly text used for archives. It can contain lowercase letters, numbers, and hyphens. This setting has no effect with Plain Permalinks.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Permalink', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; The permalink is the URL-friendly text used to build links to your comics. Be careful with this setting; comics may be unavailable if you use a setting that is not unique to your collection. This setting has no effect with Plain Permalinks.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Description', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; The description is not prominent by default; some themes may show it, though', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Image', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; A representative image to display on your site.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Archives', 'webcomic' ); ?></b>
		<?php esc_html_e( " &mdash; These settings control the WordPress archive page options for your collection. Most WordPress archive pages will include comics by default, but date archives don't include comics by default. Include in standard date archives will add comics from this collection to the standard WordPress date archives. Sort comics in chronological order will force WordPress archive pages for this collection to sort in chronological order, instead of the standard reverse-chronological order.", 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Syndication', 'webcomic' ); ?></b>
		<?php esc_html_e( " &mdash; These settings control the syndication options for your collection. Include in the main syndication feed will add comics from your collection to the main site syndication feed, which doesn't include comics by default. Include comic previews in syndication feeds will include a small comic preview along with comics in all syndication feeds; you can customize this preview using an appropriate widget area on the widgets management page.", 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Supports', 'webcomic' ); ?></b>
		<?php esc_html_e( " &mdash; These settings control the basic features and taxonomies of your collection. Disabling a feature or taxonomy will remove it's associated box from the Add New Comic and Edit Comic screens.", 'webcomic' ); ?>
		<br><br>
		<?php esc_html_e( "Widget Areas allow you to enable collection-specific widget areas for customizable comic features. For example, you can customize syndication previews using the Webcomic Syndication widget area. If you want this collection to have a unique appearance in syndication feeds, you can enable the Syndication widget area and add widgets to this collection's Syndication widget area, which Webcomic will use instead of the generic Webcomic Syndication widget area.", 'webcomic' ); ?>
	</li>
</ul>

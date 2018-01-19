<?php
/**
 * Media matcher help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

?>

<p><?php esc_html_e( "This tool is useful if you have a lot of uploaded media that you'd like to automatically assign to existing comics.", 'webcomic' ); ?></p>
<ul>
	<li>
		<b><?php esc_html_e( 'Collection', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; The collection to check for orphaned comics.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Match comic&hellip;', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; The comic attribute to match media against.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Date Format', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; The date format to use when matching comic dates.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Custom Field', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; The custom field to use when matching comic custom fields.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'With media&hellip;', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; The media attribute to match orphaned comics against.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Values must match exactly', 'webcomic' ); ?></b>
		<?php
		// Translators: 1: Example date format.
		printf( esc_html__( ' &mdash; When checked, orphaned comics and media will only match if the values are exactly the same. For example, a post date of %1$s will not match a media filename of %1$s-comic.jpg with this option.', 'webcomic' ), esc_html( date( 'Y-m-d' ) ) );
		?>
	</li>
</ul>
<p><?php esc_html_e( 'After selecting your criteria, click Find Matches and the matcher will compare all orphaned comics in the selected collection to all unassigned media in the media library. You can save any matches by checking them and clicking Save Selected Matches.', 'webcomic' ); ?></p>

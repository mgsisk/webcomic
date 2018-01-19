<?php
/**
 * Restrict settings help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Restrict;

?>

<p><?php esc_html_e( 'These settings allow you to set the default restriction settings for new comics in your collection. You can adjust these settings on a per-comic basis; changes made here will only affect newly created comics.', 'webcomic' ); ?></p>
<ul>
	<li>
		<b><?php esc_html_e( 'Minimum Age', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; The minimum age required to view the post content and comic media of newly created comics. Users will have to confirm their age before viewing the post content and comic media of comics with a minimum age greater than zero.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Accessible Roles', 'webcomic' ); ?></b>
		<?php esc_html_e( " &mdash; The user roles allowed to view the post content and comic media of newly created comics. Selecting Any registered user will allow any registered user to view the post content and comic media once they've logged in, overriding any other role selections.", 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Age-Restricted Media', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; Alternative media to display to age-restricted users in place of the normal comic media.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Role-Restricted Media', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; Alternative media to display to role-restricted users in place of the normal comic media.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Password-Restricted Media', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; Alternative media to display to password-restricted users in place of the normal comic media.', 'webcomic' ); ?>
	</li>
</ul>

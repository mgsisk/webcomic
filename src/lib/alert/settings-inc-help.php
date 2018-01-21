<?php
/**
 * Alert settings help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Alert;

?>

<p><?php esc_html_e( "These settings allow you to setup automated email alerts for your collection to remind you of important milestones. One alert is sent for each milestone; you'll want to setup more than one alert if you'd like more than one reminder.", 'webcomic' ); ?></p>
<ul>
	<li>
		<b><?php esc_html_e( 'Buffer', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; These alerts send email reminders when the number of scheduled comics in your collection reaches a certain point. Specify how many comics should be scheduled, then add emails that should receive alerts. Separate multiple emails with commas.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Hiatus', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; These alerts send email reminders when a certain number of days have passed since the last published comic in your collection. Specify how many days it should be since the last comic was published, then add emails that should receive alerts. Separate multiple emails with commas.', 'webcomic' ); ?>
	</li>
</ul>

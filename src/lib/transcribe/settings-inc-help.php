<?php
/**
 * Transcribe settings help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe;

?>

<p><?php esc_html_e( 'These settings allow you to change how comic transcription works in your collection:', 'webcomic' ); ?></p>
<ul>
	<li>
		<b><?php esc_html_e( 'Comics', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; These settings control how transcripts interact with comics. Allow people to transcribe new comics will enable transcription for any newly-published comics. You can also enable or disable transcription on a per-comic basis. Automatically close transcripts on comics older than X days will close transcription &ndash; both the submission of new transcripts and the improvement of pending transcripts &ndash; on any comic that was published more than X days ago.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Permissions', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; These settings control who can transcribe comics and what happens when they submit a transcript. By default, anyone can transcribe a comic, and transcripts are always saved as drafts when submitted. Transcript authors must&hellip; requires transcript authors to either provide a name and email address or to register and login before they can submit a transcript. Publish transcripts from authors that&hellip; will automatically publish transcripts submitted by authors that either provide a name and email address or register and login before they submit a transcript.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Notifications', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; These settings allow you to receive email notifications when transcripts are submitted. Send an email whenever a transcript is published will send an email to the comic author whenever a transcript is published. Send an email whenever a transcript is held for moderation will send an email to the site email address &ndash; and the comic author, if they can publish transcripts &ndash; whenever a transcript has been submitted and needs to be reviewed.', 'webcomic' ); ?>
	</li>
</ul>

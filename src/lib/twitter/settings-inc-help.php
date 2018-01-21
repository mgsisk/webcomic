<?php
/**
 * Twitter settings help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Twitter;

?>

<p><?php esc_html_e( 'These settings allow you to send status updates to a Twitter account whenever you publish a comic:', 'webcomic' ); ?></p>
<ul>
	<li>
		<b><?php esc_html_e( 'Account', 'webcomic' ); ?></b>
		<?php
		// Translators: Site URL.
		printf( esc_html__( " &mdash; The account that will update when you publish a comic. To connect your collection to a Twitter account, you'll need to create a Twitter Application. Set the optional Callback URL for your Twitter Application to your site URL, %s", 'webcomic' ), esc_html( home_url( '/' ) ) );
		?>
		<br><br>
		<?php esc_html_e( 'Once created, go to the Permissions page for your Twitter Application and select Read and Write for Access. Then, go to the Keys and Access Tokens page, click Regenerate Consumer Key and Secret (to ensure your app permissions take effect), and copy the Consumer Key (API Key) and Consumer Secret (API Secret) into their respective fields on this page. A Sign in with Twitter button should appear.', 'webcomic' ); ?><br><br>
		<?php esc_html_e( "When you've completed the authorization process you'll see the details for your Twitter account here. Clicking Deauthorize will clear the collection's authorization information and disable status updates until you've authorized a new account.", 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Update', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; These settings control status updates for new comics. Update status when publishing new comics will enable status updates to your authorized Twitter account for any newly-published comics. Include comic media with status updates will attempt to attach comic media when making a status update. Comic media may contain sensitive content tells Twitter that the media attached to your update may contain nudity, violence, or other sensitive content. You can adjust these settings on a per-comic basis.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Status', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; These settings control the status update format for new comics. The status field accepts a variety of tokens; refer to the User Guide for a complete list. The media selected here will replace any post thumbnails or comic media when making a status update. You can adjust these settings on a per-comic basis.', 'webcomic' ); ?>
	</li>
	<li>
		<b><?php esc_html_e( 'Cards', 'webcomic' ); ?></b>
		<?php esc_html_e( ' &mdash; These settings allow you to add Twitter Card meta data to your comic pages. Twitter Cards attach rich media to status updates and make tweets with links to your comic more prominent. Add card meta tags to comic pages will add the necessary meta tags for basic card support to pages related to this collection. Include comic media card meta tags will add extra meta tags with links to comic media.', 'webcomic' ); ?><br><br>
		<?php esc_html_e( "If you don't see Twitter Cards in your status updates, try running a comic page URL through the Twitter Card Validator. Twitter requires that you validate one URL for each card type you want to use with your site; depending on the options selected here, a page will use Summary Cards or Summary Cards with Large Image. Once validated, any page with that Twitter Card type should work; you don't have to validate every page on your site.", 'webcomic' ); ?>
	</li>
</ul>

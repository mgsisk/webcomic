<?php
/**
 * Twitter meta box help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Twitter\MetaBox;

?>

<p><?php esc_html_e( "This box lets you customize Twitter account updates for your comic. You should see the account authorized for your collection here. Update status on publish will update that accounts Twitter status whenever you publish your comic, even if it's already published; uncheck this to prevent status updates.", 'webcomic' ); ?></p>
<p><?php esc_html_e( 'Include media with update will attempt to include either the comic media, post thumbnail, or status-specific image you select in this box with any status updates. Check Media may be sensitive if the media may contain nudity, violence, or other sensitive content.', 'webcomic' ); ?></p>
<p><?php esc_html_e( 'The status field lets you customize the update format for your comic. It accepts several tokens; refer to the User Guide for a complete list.', 'webcomic' ); ?></p>

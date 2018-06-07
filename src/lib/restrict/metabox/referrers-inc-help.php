<?php
/**
 * Referrers meta box help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Restrict\MetaBox;

?>

<p><?php esc_html_e( "This box lets you set one or more web addresses that users must be referred by to view your comic. Users will not be able to view your comic's content, comments, and media unless they have been referred by one of the specified web addresses. Full URL's (e.g. http://example.com/) must match the referrer exactly. Partial URL's (e.g. example.com) will match any referrer beginning with the specified address. You can also select alternative media to display when users can't view the normal comic media.", 'webcomic' ); ?></p>

<?php
/**
 * Roles meta box help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Restrict\MetaBox;

?>

<p><?php esc_html_e( "This box lets you specify what roles users must have to view your comic. Users will not be able to view your comic's content, comments, and media unless they login and have one of the selected roles. You can also select alternative media to display when users can't view the normal comic media.", 'webcomic' ); ?></p>
<p><?php esc_html_e( "Selecting Any registered user will allow any registered user to view your comic content, comments, and media once they've logged in, overriding any other role selections. Also note that any user that has permission to edit your comic will be able to see your comic content, comments, and media in the dashboard, regardless of the selected role restrictions.", 'webcomic' ); ?></p>

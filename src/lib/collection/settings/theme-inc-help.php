<?php
/**
 * Theme settings help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Settings;

?>

<p><?php esc_html_e( "This section allows you to select a theme to use for pages related to this collection. You can customize collection themes, but WordPress doesn't support more than one active theme so some customizations &ndash; changing widgets, for example &ndash; may not work or may affect the active site theme.", 'webcomic' ); ?></p>
<p><?php esc_html_e( "Keep in mind that Webcomic can't always determine which collection a page belongs to, in which case the active site theme will take over. A good example is the standard, non-static front page; even if you choose to display your comic or comics on this page, Webcomic can't determine the current collection for this page and will default to using the active site theme.", 'webcomic' ); ?></p>

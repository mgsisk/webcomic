<?php
/**
 * Overview settings help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Plugin;

?>

<p><?php esc_html_e( "These settings control the components that make up Webcomic. Enabling a component adds that component's functionality to your site, including any extra settings, shortcodes, template tags, and widgets the component provides. Disabling a component removes that component's functionality from your site.", 'webcomic' ); ?></p>
<ul>
	<li>
		<b><?php esc_html_e( 'Uninstall', 'webcomic' ); ?></b>
		<?php esc_html_e( " &mdash; Deletes data associated with Webcomic when disabling components or deactivating the plugin. For example, disabling the Character component with Uninstall enabled will delete any characters you've created and remove all Character-specific settings from all your collections. Deactivating the plugin with Uninstall enabled will delete all Webcomic data from your site, including comics and settings. Uploaded media will not be deleted.", 'webcomic' ); ?>
	</li>
</ul>

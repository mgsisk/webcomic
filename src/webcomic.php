<?php
/**
 * Plugin Name: Webcomic
 * Plugin URI: https://github.com/mgsisk/webcomic
 * Description: Comic publishing power for the web. Turn your WordPress-powered site into a comic publishing platform with Webcomic.
 * Version: 5.0.2
 * Author: Michael Sisk
 * Author URI: https://mgsisk.com/
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: webcomic
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic;

if ( version_compare( PHP_VERSION, '7', '<' ) ) {
	return add_filter( 'admin_notices', function() {
		echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'Webcomic requires PHP 7 or later.', 'webcomic' ) . '</p></div>';

		deactivate_plugins( [ plugin_basename( __FILE__ ) ] );
	} );
} elseif ( version_compare( get_bloginfo( 'version' ), '4.7', '<' ) ) {
	return add_filter( 'admin_notices', function() {
		echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'Webcomic requires WordPress 4.7 or later.', 'webcomic' ) . '</p></div>';

		deactivate_plugins( [ plugin_basename( __FILE__ ) ] );
	} );
} elseif ( function_exists( 'webcomic' ) ) {
	return; // NOTE For compatibility with Inkblot 4.
}

require __DIR__ . '/lib/plugin/globals.php';

webcomic_init( '5.0.2' );
webcomic_load( webcomic( 'option.components' ) );

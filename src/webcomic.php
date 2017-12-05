<?php
/**
 * Plugin Name: Webcomic
 * Plugin URI: https://github.com/mgsisk/webcomic
 * Description: Comic publishing power for the web. Turn your WordPress-powered site into a comic publishing platform with Webcomic.
 * Version: 5.0.0
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
	throw new \Exception( esc_html__( 'Webcomic requires PHP 7 or later.', 'webcomic' ) );
} elseif ( version_compare( get_bloginfo( 'version' ), '4.7', '<' ) ) {
	throw new \Exception( esc_html__( 'Webcomic requires WordPress 4.7 or later.', 'webcomic' ) );
} elseif ( function_exists( 'webcomic' ) ) {
	return; // NOTE For compatibility with Inkblot 4.
}

require __DIR__ . '/lib/plugin/globals.php';

webcomic_init( '5.0.0' );
webcomic_load( webcomic( 'option.components' ) );

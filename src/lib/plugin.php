<?php
/**
 * Plugin component
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic;

/**
 * Initilize the plugin.
 *
 * @return void
 */
function plugin() {
	if ( ! is_admin() ) {
		return;
	}

	require __DIR__ . '/plugin/settings.php';

	Plugin\settings();
}

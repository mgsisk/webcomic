<?php
/**
 * Compat upgrade functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Compat;

/**
 * Upgrade Webcomic 1-4 to 5.0.0.
 *
 * @param string $version The version being upgraded.
 * @return void
 */
function v5_0_0( string $version ) {
	$upgrader = __DIR__ . "/upgrade/v{$version[0]}.php";

	if ( ! is_readable( $upgrader ) ) {
		return;
	}

	require $upgrader;

	$callback = __NAMESPACE__ . '\Upgrade\v' . $version[0];

	$callback();
}

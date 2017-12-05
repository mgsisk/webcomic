<?php
/**
 * Plugin upgrade functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Plugin;

/**
 * Add plugin options.
 *
 * @param string $version The version being upgraded.
 * @return void
 */
function v0_0_0( string $version ) {
	add_option(
		'webcomic', [
			'version'     => $version,
			'upgrade'     => $version,
			'debug'       => false,
			'uninstall'   => false,
			'components'  => [],
			'collections' => [],
			'install'     => true,
		]
	);
}

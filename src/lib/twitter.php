<?php
/**
 * Twitter component
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic;

/**
 * Initialize the Twitter component.
 *
 * @return void
 */
function twitter() {
	require __DIR__ . '/twitter/common.php';

	Twitter\common();

	if ( is_admin() ) {
		require __DIR__ . '/twitter/settings.php';
		require __DIR__ . '/twitter/metabox/status.php';

		Twitter\settings();
		Twitter\MetaBox\status();
	}
}

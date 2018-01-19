<?php
/**
 * Alert component
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic;

use WP_Post;

/**
 * Initialize the alert component.
 *
 * @return void
 */
function alert() {
	require __DIR__ . '/alert/common.php';

	Alert\common();

	if ( is_admin() ) {
		require __DIR__ . '/alert/settings.php';

		Alert\settings();
	}
}

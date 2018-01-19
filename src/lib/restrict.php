<?php
/**
 * Restrict component
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic;

/**
 * Initialize the restrict component.
 *
 * @return void
 */
function restrict() {
	require __DIR__ . '/restrict/common.php';
	require __DIR__ . '/restrict/filters.php';
	require __DIR__ . '/restrict/globals.php';
	require __DIR__ . '/restrict/shortcodes.php';

	Restrict\common();
	Restrict\filters();
	Restrict\shortcodes();

	if ( is_admin() ) {
		require __DIR__ . '/restrict/settings.php';
		require __DIR__ . '/restrict/metabox/age.php';
		require __DIR__ . '/restrict/metabox/password.php';
		require __DIR__ . '/restrict/metabox/roles.php';

		Restrict\settings();
		Restrict\MetaBox\age();
		Restrict\MetaBox\password();
		Restrict\MetaBox\roles();
	}
}

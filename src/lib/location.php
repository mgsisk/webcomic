<?php
/**
 * Location component
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic;

/**
 * Initialize the location component.
 *
 * @return void
 */
function location() {
	if ( ! function_exists( __NAMESPACE__ . '\taxonomy' ) ) {
		require __DIR__ . '/taxonomy.php';

		taxonomy();
	}

	require __DIR__ . '/location/common.php';
	require __DIR__ . '/location/customize.php';
	require __DIR__ . '/location/globals.php';
	require __DIR__ . '/location/shortcodes.php';
	require __DIR__ . '/location/widget/class-firstwebcomiclocationlink.php';
	require __DIR__ . '/location/widget/class-lastwebcomiclocationlink.php';
	require __DIR__ . '/location/widget/class-nextwebcomiclocationlink.php';
	require __DIR__ . '/location/widget/class-previouswebcomiclocationlink.php';
	require __DIR__ . '/location/widget/class-randomwebcomiclocationlink.php';
	require __DIR__ . '/location/widget/class-webcomiclocationslist.php';
	require __DIR__ . '/location/widget/class-webcomiclocationlink.php';

	Location\common();
	Location\customize();
	Location\shortcodes();

	if ( is_admin() ) {
		require __DIR__ . '/location/meta.php';
		require __DIR__ . '/location/settings.php';

		Location\meta();
		Location\settings();
	}
}

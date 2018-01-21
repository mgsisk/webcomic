<?php
/**
 * Character component
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic;

/**
 * Initialize the character component.
 *
 * @return void
 */
function character() {
	if ( ! function_exists( __NAMESPACE__ . '\taxonomy' ) ) {
		require __DIR__ . '/taxonomy.php';

		taxonomy();
	}

	require __DIR__ . '/character/common.php';
	require __DIR__ . '/character/customize.php';
	require __DIR__ . '/character/globals.php';
	require __DIR__ . '/character/shortcodes.php';
	require __DIR__ . '/character/widget/class-firstwebcomiccharacterlink.php';
	require __DIR__ . '/character/widget/class-lastwebcomiccharacterlink.php';
	require __DIR__ . '/character/widget/class-nextwebcomiccharacterlink.php';
	require __DIR__ . '/character/widget/class-previouswebcomiccharacterlink.php';
	require __DIR__ . '/character/widget/class-randomwebcomiccharacterlink.php';
	require __DIR__ . '/character/widget/class-webcomiccharacterslist.php';
	require __DIR__ . '/character/widget/class-webcomiccharacterlink.php';

	Character\common();
	Character\customize();
	Character\shortcodes();

	if ( is_admin() ) {
		require __DIR__ . '/character/meta.php';
		require __DIR__ . '/character/settings.php';

		Character\meta();
		Character\settings();
	}
}

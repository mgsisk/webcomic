<?php
/**
 * Storyline component
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic;

/**
 * Initialize the storyline component.
 *
 * @return void
 */
function storyline() {
	if ( ! function_exists( __NAMESPACE__ . '\taxonomy' ) ) {
		require __DIR__ . '/taxonomy.php';

		taxonomy();
	}

	require __DIR__ . '/storyline/common.php';
	require __DIR__ . '/storyline/customize.php';
	require __DIR__ . '/storyline/globals.php';
	require __DIR__ . '/storyline/shortcodes.php';
	require __DIR__ . '/storyline/widget/class-firstwebcomicstorylinelink.php';
	require __DIR__ . '/storyline/widget/class-lastwebcomicstorylinelink.php';
	require __DIR__ . '/storyline/widget/class-nextwebcomicstorylinelink.php';
	require __DIR__ . '/storyline/widget/class-previouswebcomicstorylinelink.php';
	require __DIR__ . '/storyline/widget/class-randomwebcomicstorylinelink.php';
	require __DIR__ . '/storyline/widget/class-webcomicstorylineslist.php';
	require __DIR__ . '/storyline/widget/class-webcomicstorylinelink.php';

	Storyline\common();
	Storyline\customize();
	Storyline\shortcodes();

	if ( is_admin() ) {
		require __DIR__ . '/storyline/meta.php';
		require __DIR__ . '/storyline/settings.php';

		Storyline\meta();
		Storyline\settings();
	}
}

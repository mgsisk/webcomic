<?php
/**
 * Taxonomy component
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic;

/**
 * Initialize the taxonomy component.
 *
 * @return void
 */
function taxonomy() {
	require __DIR__ . '/taxonomy/common.php';
	require __DIR__ . '/taxonomy/customize.php';
	require __DIR__ . '/taxonomy/filters.php';
	require __DIR__ . '/taxonomy/globals.php';
	require __DIR__ . '/taxonomy/shortcodes.php';
	require __DIR__ . '/taxonomy/shared/common.php';
	require __DIR__ . '/taxonomy/walker/class-termlister.php';
	require __DIR__ . '/taxonomy/widget/class-webcomictermlink.php';
	require __DIR__ . '/taxonomy/widget/class-webcomictermslist.php';

	Taxonomy\common();
	Taxonomy\customize();
	Taxonomy\filters();
	Taxonomy\shortcodes();

	if ( is_admin() ) {
		require __DIR__ . '/taxonomy/media.php';
		require __DIR__ . '/taxonomy/settings.php';
		require __DIR__ . '/taxonomy/search.php';
		require __DIR__ . '/taxonomy/sorter.php';
		require __DIR__ . '/taxonomy/shared/meta.php';
		require __DIR__ . '/taxonomy/shared/settings.php';
		require __DIR__ . '/taxonomy/walker/class-termsorter.php';

		Taxonomy\media();
		Taxonomy\search();
		Taxonomy\settings();
		Taxonomy\sorter();
	}
}

<?php
/**
 * Compat component
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic;

/**
 * Initialize the compat component.
 *
 * @return void
 */
function compat() {
	require __DIR__ . '/compat/class-webcomictag.php';
	require __DIR__ . '/compat/common.php';
	require __DIR__ . '/compat/filters.php';
	require __DIR__ . '/compat/globals.php';
	require __DIR__ . '/compat/shortcodes.php';
	require __DIR__ . '/compat/widget/class-widget-purchasewebcomiclink.php';
	require __DIR__ . '/compat/widget/class-widget-recentwebcomics.php';
	require __DIR__ . '/compat/widget/class-widget-scheduledwebcomics.php';
	require __DIR__ . '/compat/widget/class-widget-webcomiccharacterlink.php';
	require __DIR__ . '/compat/widget/class-widget-webcomiccharacters.php';
	require __DIR__ . '/compat/widget/class-widget-webcomiccollectionlink.php';
	require __DIR__ . '/compat/widget/class-widget-webcomiccollections.php';
	require __DIR__ . '/compat/widget/class-widget-webcomicdonation.php';
	require __DIR__ . '/compat/widget/class-widget-webcomiclink.php';
	require __DIR__ . '/compat/widget/class-widget-webcomicprint.php';
	require __DIR__ . '/compat/widget/class-widget-webcomicstorylinelink.php';
	require __DIR__ . '/compat/widget/class-widget-webcomicstorylines.php';
	require __DIR__ . '/compat/widget/class-widget-webcomictranscriptslink.php';

	Compat\common();
	Compat\filters();
	Compat\shortcodes();

	if ( is_admin() ) {
		require __DIR__ . '/compat/settings.php';

		Compat\settings();
	}
}

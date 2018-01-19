<?php
/**
 * Commerce component
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic;

use WP_Post;

/**
 * Initialize the commerce component.
 *
 * @return void
 */
function commerce() {
	require __DIR__ . '/commerce/common.php';
	require __DIR__ . '/commerce/customize.php';
	require __DIR__ . '/commerce/filters.php';
	require __DIR__ . '/commerce/globals.php';
	require __DIR__ . '/commerce/shortcodes.php';
	require __DIR__ . '/commerce/walker/class-printlister.php';
	require __DIR__ . '/commerce/widget/class-webcomiccollectioncartlink.php';
	require __DIR__ . '/commerce/widget/class-webcomiccollectiondonationlink.php';
	require __DIR__ . '/commerce/widget/class-webcomicprintslist.php';

	Commerce\common();
	Commerce\customize();
	Commerce\filters();
	Commerce\shortcodes();

	if ( is_admin() ) {
		require __DIR__ . '/commerce/logger.php';
		require __DIR__ . '/commerce/settings.php';
		require __DIR__ . '/commerce/metabox/prints.php';

		Commerce\logger();
		Commerce\settings();
		Commerce\MetaBox\prints();
	}
}

<?php
/**
 * Mock functions for webcomic_init() tests
 *
 * @package webcomic
 */

namespace Mgsisk\Webcomic\Plugin;

/**
 * This upgrade function should never run.
 *
 * @return void
 */
function v998() {
	update_option(
		'webcomic', [
			'mock' => true,
		]
	);
}

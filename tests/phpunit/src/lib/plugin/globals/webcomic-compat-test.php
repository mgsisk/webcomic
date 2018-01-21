<?php
/**
 * Test webcomic_compat()
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Test\Plugin\Globals;

use WP_UnitTestCase;

/**
 * Test webcomic_compat() functionality.
 *
 * @group plugin
 * @covers ::webcomic_compat()
 */
class WebcomicCompat extends WP_UnitTestCase {
	/**
	 * Setup the test environment.
	 */
	public function setup() {
		parent::setup();

		require_once dirname( __DIR__, 6 ) . '/src/lib/plugin/globals.php';
	}

	/**
	 * It should return a default value when WebcomicTag doesn't exist.
	 *
	 * @test
	 */
	public function it_returns_a_default_value_without_compat_class() {
		self::assertTrue( webcomic_compat( 'webcomic', [], true ) );
	}

	/**
	 * It should call a method when WebcomicTag does exist.
	 *
	 * @test
	 */
	public function it_calls_a_method_with_compat_class() {
		require_once dirname( __DIR__, 6 ) . '/src/lib/compat/class-webcomictag.php';

		self::assertTrue( webcomic_compat( 'webcomic', [] ) );
	}
}

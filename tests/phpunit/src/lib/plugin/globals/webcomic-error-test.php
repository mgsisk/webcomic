<?php
/**
 * Test webcomic_error()
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Test\Plugin\Globals;

use WP_UnitTestCase;

/**
 * Test webcomic_error() functionality.
 *
 * @group plugin
 * @covers ::webcomic_error()
 */
class WebcomicError extends WP_UnitTestCase {
	/**
	 * Setup the test environment.
	 */
	public function setup() {
		parent::setup();

		require_once dirname( __DIR__, 6 ) . '/src/lib/plugin/globals.php';
	}

	/**
	 * It should return false when debug is falsy.
	 *
	 * @test
	 */
	public function it_returns_false_when_debug_is_falsy() {
		add_option(
			'webcomic', [
				'debug' => false,
			]
		);

		self::assertFalse( webcomic_error( '.' ) );
	}

	/**
	 * It should trigger an error when debug is truthy.
	 *
	 * @test
	 */
	public function it_triggers_an_error_when_debug_is_truthy() {
		add_option(
			'webcomic', [
				'debug' => true,
			]
		);

		try {
			self::assertTrue( webcomic_error( '.' ) );
		} catch ( \Exception $exception ) {
			self::assertInstanceOf( \Exception::class, $exception );
			self::assertSame( '.', $exception->getMessage() );
		}
	}
}

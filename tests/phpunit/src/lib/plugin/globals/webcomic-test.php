<?php
/**
 * Test webcomic()
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Test\Plugin\Globals;

use WP_UnitTestCase;

/**
 * Test webcomic() functionality.
 *
 * @group plugin
 * @covers ::webcomic()
 */
class Webcomic extends WP_UnitTestCase {
	/**
	 * Setup the test environment.
	 */
	public function setup() {
		parent::setup();

		require_once dirname( __DIR__, 6 ) . '/src/lib/plugin/globals.php';

		add_option(
			'webcomic', [
				'version' => '999',
			]
		);

		add_option(
			'webcomic1', [
				'id' => 'webcomic1',
			]
		);
	}

	/**
	 * It should return true when no argument is provided, for compatibility.
	 *
	 * @test
	 */
	public function it_returns_true_with_no_argument() {
		self::assertTrue( webcomic() );
	}

	/**
	 * It should return the absolute path to the plugin file.
	 *
	 * @test
	 */
	public function it_returns_the_absolute_path_to_the_plugin_file() {
		self::assertSame( dirname( __DIR__, 6 ) . '/src/webcomic.php', webcomic( 'file' ) );
	}

	/**
	 * It should return null for unknown arguments.
	 *
	 * @test
	 */
	public function it_returns_null_for_unknown_arguments() {
		self::assertNull( webcomic( 'bogus' ) );
	}

	/**
	 * It should return global values.
	 *
	 * @test
	 * @SuppressWarnings(PHPMD.Superglobals) - We're purposely changing global vlaues for testing.
	 */
	public function it_returns_global_values() {
		$GLOBALS['test_globals'] = true;

		self::assertTrue( webcomic( 'GLOBALS.test_globals' ) );

		unset( $GLOBALS['test_globals'] );
	}

	/**
	 * It should return null for unknown global values.
	 *
	 * @test
	 */
	public function it_returns_null_for_unknown_global_values() {
		self::assertNull( webcomic( 'GLOBALS.bogus' ) );
	}

	/**
	 * It should return plugin option values.
	 *
	 * @test
	 */
	public function it_returns_plugin_option_values() {
		self::assertSame( '999', webcomic( 'option.version' ) );
	}

	/**
	 * It should return null for unknown option values.
	 *
	 * @test
	 */
	public function it_returns_null_for_unknown_plugin_option_values() {
		self::assertNull( webcomic( 'option.bogus' ) );
	}

	/**
	 * It should return collection option values.
	 *
	 * @test
	 */
	public function it_returns_collection_option_values() {
		self::assertSame( 'webcomic1', webcomic( 'option.webcomic1.id' ) );
	}

	/**
	 * It should return null for unknown collection option values.
	 *
	 * @test
	 */
	public function it_returns_null_for_unknown_collection_option_values() {
		self::assertNull( webcomic( 'option.webcomic1.bogus' ) );
	}
}

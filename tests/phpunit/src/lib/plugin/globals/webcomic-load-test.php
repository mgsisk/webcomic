<?php
/**
 * Test webcomic_load()
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Test\Plugin\Globals;

use WP_UnitTestCase;

/**
 * Test webcomic_load() functionality.
 *
 * @group plugin
 * @group isolated
 * @covers ::webcomic_load()
 * @preserveGlobalState disabled
 * @runTestsInSeparateProcesses
 */
class WebcomicLoad extends WP_UnitTestCase {
	/**
	 * Setup the test environment.
	 */
	public function setup() {
		parent::setup();

		require_once dirname( __DIR__, 6 ) . '/src/lib/plugin/globals.php';

		add_option(
			'webcomic', [
				'version' => '5.0.6',
			]
		);
	}

	/**
	 * It should always load the plugin component.
	 *
	 * @test
	 */
	public function it_always_loads_the_plugin_component() {
		webcomic_load( [] );

		self::assertTrue( function_exists( 'Mgsisk\Webcomic\plugin' ) );
	}

	/**
	 * It should ignore unknown components.
	 *
	 * @test
	 */
	public function it_ignores_unknown_components() {
		webcomic_load( [ 'test' ] );

		self::assertFalse( function_exists( 'Mgsisk\Webcomic\test' ) );
	}

	/**
	 * It should ignore previously loaded components.
	 *
	 * @test
	 */
	public function it_ignores_previously_loaded_components() {
		webcomic_load( [] );

		self::assertTrue( function_exists( 'Mgsisk\Webcomic\plugin' ) );

		webcomic_load( [] );
	}
}

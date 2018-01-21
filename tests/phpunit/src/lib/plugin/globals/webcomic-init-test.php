<?php
/**
 * Test webcomic_init()
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Test\Plugin\Globals;

use WP_UnitTestCase;

/**
 * Test webcomic_init() functionality.
 *
 * @group plugin
 * @group isolated
 * @covers ::webcomic_init()
 * @preserveGlobalState disabled
 * @runTestsInSeparateProcesses
 */
class WebcomicInit extends WP_UnitTestCase {
	/**
	 * Setup the test environment.
	 */
	public function setup() {
		parent::setup();

		require_once dirname( __DIR__, 6 ) . '/src/lib/plugin/globals.php';
	}

	/**
	 * It should upgrade plugin components.
	 *
	 * @test
	 */
	public function it_upgrades_plugin_components() {
		webcomic_init( '999' );

		self::assertSame( '999', get_option( 'webcomic' )['version'] );
	}

	/**
	 * It should check for Webcomic 1.x and 2.x data.
	 *
	 * @test
	 */
	public function it_upgrades_v1v2_installations() {
		add_option( 'webcomic_version', '998' );

		webcomic_init( '999' );

		self::assertSame( '998', get_option( 'webcomic' )['upgrade'] );
	}

	/**
	 * It should check for Webcomic 3.x and 4.x data.
	 *
	 * @test
	 */
	public function it_upgrades_v3v4_installations() {
		add_option(
			'webcomic_options', [
				'version' => '998',
			]
		);

		webcomic_init( '999' );

		self::assertSame( '998', get_option( 'webcomic' )['upgrade'] );
	}

	/**
	 * It should ignore outdated upgrade functions.
	 *
	 * @test
	 */
	public function it_ignores_outdated_upgrade_functions() {
		add_option(
			'webcomic', [
				'version' => '998',
			]
		);

		webcomic_init( '999' );

		self::assertSame( '998', get_option( 'webcomic' )['upgrade'] );
	}

	/**
	 * It should ignore unnecessary upgrade functions.
	 *
	 * @test
	 */
	public function it_ignores_unnecessary_upgrade_functions() {
		require_once __DIR__ . '/webcomic-init-test-mock.php';

		webcomic_init( '999' );

		self::assertFalse( isset( get_option( 'webcomic' )['mock'] ) );
	}

	/**
	 * It should do nothing if the plugin is fully updated.
	 *
	 * @test
	 */
	public function it_does_nothing_when_the_plugin_is_updated() {
		add_option(
			'webcomic', [
				'version' => '999',
			]
		);

		webcomic_init( '1' );

		self::assertSame( '999', get_option( 'webcomic' )['version'] );
	}
}

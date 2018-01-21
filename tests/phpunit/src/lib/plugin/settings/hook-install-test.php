<?php
/**
 * Test Mgsisk\Webcomic\Plugin\hook_install()
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Test\Plugin\Settings;

use WP_UnitTestCase;
use function Mgsisk\Webcomic\Plugin\hook_install;

/**
 * Test Mgsisk\Webcomic\Plugin\hook_install() functionality.
 *
 * @group plugin
 * @covers ::Mgsisk\Webcomic\Plugin\hook_install()
 */
class HookInstall extends WP_UnitTestCase {
	/**
	 * Setup the test environment.
	 */
	public function setup() {
		parent::setup();

		require_once dirname( __DIR__, 6 ) . '/src/lib/plugin/settings.php';
	}

	/**
	 * It should do nothing when install is falsy.
	 *
	 * @test
	 */
	public function it_does_nothing_when_install_option_is_falsy() {
		hook_install();

		self::assertFalse( get_option( 'webcomic' ) );
	}

	/**
	 * It should update plugin options when install is truthy.
	 *
	 * @test
	 */
	public function it_updates_plugin_options_when_install_option_is_truthy() {
		add_option(
			'webcomic', [
				'install' => true,
			]
		);

		hook_install();

		self::assertSame( [], get_option( 'webcomic' ) );
	}
}

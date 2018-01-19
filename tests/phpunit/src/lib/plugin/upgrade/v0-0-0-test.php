<?php
/**
 * Test Mgsisk\Webcomic\Plugin\v000()
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Test\Plugin\Upgrade;

use WP_UnitTestCase;
use function Mgsisk\Webcomic\Plugin\v0_0_0;

/**
 * Test Mgsisk\Webcomic\Plugin\v000() functionality.
 *
 * @group plugin
 * @covers ::Mgsisk\Webcomic\Plugin\v0_0_0()
 */
class V000 extends WP_UnitTestCase {
	/**
	 * Setup the test environment.
	 */
	public function setup() {
		parent::setup();

		require_once dirname( __DIR__, 6 ) . '/src/lib/plugin/upgrade.php';
	}

	/**
	 * It should add the default plugin options.
	 *
	 * @test
	 */
	public function it_adds_default_plugin_options() {
		v0_0_0( '999' );

		$options = get_option( 'webcomic' );

		self::assertSame(
			[
				'version'     => '999',
				'upgrade'     => '999',
				'debug'       => false,
				'uninstall'   => false,
				'components'  => [],
				'collections' => [],
				'install'     => true,
			], $options
		);
	}

	/**
	 * It should not overwrite existing options.
	 *
	 * @test
	 */
	public function it_does_not_overwrite_existing_options() {
		add_option(
			'webcomic', [
				'version' => '999',
			]
		);

		$options = get_option( 'webcomic' );

		self::assertSame(
			[
				'version' => '999',
			], $options
		);
	}
}

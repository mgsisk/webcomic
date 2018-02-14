<?php
/**
 * Test webcomic_help()
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Test\Plugin\Globals;

use WP_UnitTestCase;

/**
 * Test webcomic_help() functionality.
 *
 * @group plugin
 * @covers ::webcomic_help()
 */
class WebcomicHelp extends WP_UnitTestCase {
	/**
	 * Setup the test environment.
	 */
	public function setup() {
		parent::setup();

		require_once dirname( __DIR__, 6 ) . '/src/lib/plugin/globals.php';

		add_option(
			'webcomic', [
				'version' => '5.0.5',
			]
		);
	}

	/**
	 * It should return an empty string outside of the admin dashboard.
	 *
	 * @test
	 */
	public function it_returns_an_empty_string_outside_the_admin_dashboard() {
		self::assertSame( '', webcomic_help() );
	}

	/**
	 * It should return help sidebar text in the admin dashboard.
	 *
	 * @test
	 * @SuppressWarnings(PHPMD.Superglobals) - We're purposely changing global vlaues for testing.
	 */
	public function it_returns_help_text_inside_the_admin_dashboard() {
		set_current_screen( 'plugin.php' );

		$expected  = '<p><b>For more information:</b></p>';
		$expected .= '<ul>';
		$expected .= '<li><a href="https://github.com/mgsisk/webcomic/wiki" target="_blank">User Guide</a></li>';
		$expected .= '<li><a href="https://wordpress.org/support/plugin/webcomic" target="_blank">Support Forum</a></li>';
		$expected .= '<li><a href="https://discord.gg/TNTfzzg" target="_blank">Discord Server</a></li>';
		$expected .= '<li><a href="https://github.com/mgsisk/webcomic/issues" target="_blank">Issue Tracker</a></li>';
		$expected .= '<li><a href="mailto:help@mgsisk.com?subject=Webcomic%20Help">Contact Mike</a></li>';
		$expected .= '</ul>';
		$expected .= '<p><a href="https://mgsisk.com/#support" target="_blank" class="button button-primary button-small">Support Webcomic</a></p>';
		$expected .= '<p><small>Thank you for creating with Webcomic 5.0.5</small></p>';

		self::assertSame( $expected, webcomic_help() );

		unset( $GLOBALS['current_screen'] );
	}
}

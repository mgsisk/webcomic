<?php
/**
 * Test webcomic_notice()
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Test\Plugin\Globals;

use WP_UnitTestCase;

/**
 * Test webcomic_notice() functionality.
 *
 * @group plugin
 * @covers ::webcomic_notice()
 */
class WebcomicNotice extends WP_UnitTestCase {
	/**
	 * Setup the test environment.
	 */
	public function setup() {
		parent::setup();

		require_once dirname( __DIR__, 6 ) . '/src/lib/plugin/globals.php';
	}

	/**
	 * It should return false outside of the admin dashboard.
	 *
	 * @test
	 * @SuppressWarnings(PHPMD.Superglobals) - We're purposely changing global vlaues for testing.
	 */
	public function it_returns_false_outside_the_admin_dashboard() {
		unset( $GLOBALS['current_screen'] );

		self::assertFalse( webcomic_notice( '.' ) );
	}

	/**
	 * It should return true in the admin dashboard.
	 *
	 * @test
	 * @SuppressWarnings(PHPMD.Superglobals) - We're purposely changing global vlaues for testing.
	 */
	public function it_returns_true_inside_the_admin_dashboard() {
		set_current_screen( 'plugin.php' );

		self::assertTrue( webcomic_notice( '.' ) );

		unset( $GLOBALS['current_screen'] );
	}

	/**
	 * It should set a transient on success.
	 *
	 * @test
	 * @SuppressWarnings(PHPMD.Superglobals) - We're purposely changing global vlaues for testing.
	 */
	public function it_sets_a_transient() {
		set_current_screen( 'plugin.php' );

		webcomic_notice( '.', 'info' );

		self::assertSame( 'info|.', get_transient( 'webcomic_admin_notices' )[0] );

		unset( $GLOBALS['current_screen'] );
	}
}

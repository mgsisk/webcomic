<?php
/**
 * Test Mgsisk\Webcomic\Plugin\settings()
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Test\Plugin\Settings;

use WP_UnitTestCase;
use function Mgsisk\Webcomic\Plugin\settings;

/**
 * Test Mgsisk\Webcomic\Plugin\settings() functionality.
 *
 * @group plugin
 * @covers ::Mgsisk\Webcomic\Plugin\settings()
 */
class Settings extends WP_UnitTestCase {
	/**
	 * Setup the test environment.
	 */
	public function setup() {
		parent::setup();

		require_once dirname( __DIR__, 6 ) . '/src/lib/plugin/settings.php';

		settings();
	}

	/**
	 * It should set a deactivation hook.
	 *
	 * @test
	 */
	public function it_sets_deactivation_hook() {
		self::assertInternalType( 'int', has_filter( 'deactivate_' . plugin_basename( dirname( __DIR__, 6 ) . '/src/webcomic.php' ), 'Mgsisk\Webcomic\Plugin\hook_uninstall' ) );
	}

	/**
	 * It should set plugins_loaded hooks.
	 *
	 * @test
	 */
	public function it_sets_plugins_loaded_hook() {
		self::assertInternalType( 'int', has_filter( 'plugins_loaded', 'Mgsisk\Webcomic\Plugin\hook_install' ) );
	}

	/**
	 * It should set admin_menu hooks.
	 *
	 * @test
	 */
	public function it_sets_admin_menu_hook() {
		self::assertInternalType( 'int', has_filter( 'admin_menu', 'Mgsisk\Webcomic\Plugin\hook_add_settings_page' ) );
	}

	/**
	 * It should set admin_init hooks.
	 *
	 * @test
	 */
	public function it_sets_admin_init_hooks() {
		self::assertInternalType( 'int', has_filter( 'admin_init', 'Mgsisk\Webcomic\Plugin\hook_register_options' ) );
		self::assertInternalType( 'int', has_filter( 'admin_init', 'Mgsisk\Webcomic\Plugin\hook_add_settings_section' ) );
		self::assertInternalType( 'int', has_filter( 'admin_init', 'Mgsisk\Webcomic\Plugin\hook_add_field_uninstall' ) );
		self::assertInternalType( 'int', has_filter( 'admin_init', 'Mgsisk\Webcomic\Plugin\hook_add_field_debug' ) );
	}

	/**
	 * It should set admin_head hooks.
	 *
	 * @test
	 */
	public function it_sets_admin_head_hooks() {
		self::assertInternalType( 'int', has_filter( 'admin_head', 'Mgsisk\Webcomic\Plugin\hook_add_help_sidebar' ) );
		self::assertInternalType( 'int', has_filter( 'admin_head', 'Mgsisk\Webcomic\Plugin\hook_add_help_overview' ) );
		self::assertInternalType( 'int', has_filter( 'admin_head', 'Mgsisk\Webcomic\Plugin\hook_add_help_details' ) );
	}

	/**
	 * It should set admin_notices hooks.
	 *
	 * @test
	 */
	public function it_sets_admin_notices_hooks() {
		self::assertInternalType( 'int', has_filter( 'admin_notices', 'Mgsisk\Webcomic\Plugin\hook_inkblot_component_alert' ) );
		self::assertInternalType( 'int', has_filter( 'admin_notices', 'Mgsisk\Webcomic\Plugin\hook_uninstall_alert' ) );
	}

	/**
	 * It should set network_admin_notices hooks.
	 *
	 * @test
	 */
	public function it_sets_network_admin_notices_hooks() {
		self::assertInternalType( 'int', has_filter( 'network_admin_notices', 'Mgsisk\Webcomic\Plugin\hook_uninstall_network_alert' ) );
	}

	/**
	 * It should set all_admin_notices hooks.
	 *
	 * @test
	 */
	public function it_sets_all_admin_notices_hooks() {
		self::assertInternalType( 'int', has_filter( 'all_admin_notices', 'Mgsisk\Webcomic\Plugin\hook_display_admin_notices' ) );
	}

	/**
	 * It should set sanitize_option_webcomic hooks.
	 *
	 * @test
	 */
	public function it_sets_sanitize_option_webcomic_hooks() {
		self::assertInternalType( 'int', has_filter( 'sanitize_option_webcomic', 'Mgsisk\Webcomic\Plugin\hook_sanitize_options' ) );
		self::assertInternalType( 'int', has_filter( 'sanitize_option_webcomic', 'Mgsisk\Webcomic\Plugin\hook_sanitize_components' ) );
		self::assertInternalType( 'int', has_filter( 'sanitize_option_webcomic', 'Mgsisk\Webcomic\Plugin\hook_sanitize_uninstall' ) );
		self::assertInternalType( 'int', has_filter( 'sanitize_option_webcomic', 'Mgsisk\Webcomic\Plugin\hook_sanitize_debug' ) );
	}

	/**
	 * It should set plugin_row_meta hooks.
	 *
	 * @test
	 */
	public function it_sets_plugin_row_meta_hooks() {
		self::assertInternalType( 'int', has_filter( 'plugin_row_meta', 'Mgsisk\Webcomic\Plugin\hook_add_plugin_links' ) );
	}
}

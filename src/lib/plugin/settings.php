<?php
/**
 * Plugin settings functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Plugin;

use WP_Screen;

/**
 * Add plugin settings hooks.
 *
 * @return void
 */
function settings() {
	register_deactivation_hook( webcomic( 'file' ), __NAMESPACE__ . '\hook_uninstall' );
	add_filter( 'plugins_loaded', __NAMESPACE__ . '\hook_install' );
	add_filter( 'admin_menu', __NAMESPACE__ . '\hook_add_settings_page' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_options' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_settings_section' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_uninstall', 999 );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_debug', 999 );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_help_sidebar' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_help_overview' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_help_details' );
	add_filter( 'admin_notices', __NAMESPACE__ . '\hook_inkblot_component_alert' ); // NOTE For compatibility with Inkblot 4.
	add_filter( 'admin_notices', __NAMESPACE__ . '\hook_uninstall_alert' );
	add_filter( 'network_admin_notices', __NAMESPACE__ . '\hook_uninstall_network_alert' );
	add_filter( 'all_admin_notices', __NAMESPACE__ . '\hook_display_admin_notices' );
	add_filter( 'sanitize_option_webcomic', __NAMESPACE__ . '\hook_sanitize_options' );
	add_filter( 'sanitize_option_webcomic', __NAMESPACE__ . '\hook_sanitize_components' );
	add_filter( 'sanitize_option_webcomic', __NAMESPACE__ . '\hook_sanitize_uninstall' );
	add_filter( 'sanitize_option_webcomic', __NAMESPACE__ . '\hook_sanitize_debug' );
	add_filter( 'plugin_row_meta', __NAMESPACE__ . '\hook_add_plugin_links', 10, 4 );
}

/**
 * Uninstall the plugin.
 *
 * @param bool $network Whether this is a network deactivation.
 * @param int  $site The site the plugin is being deactivated on.
 * @return void
 */
function hook_uninstall( bool $network, int $site = 0 ) {
	if ( $network ) {
		$sites = get_sites(
			[
				'fields' => 'ids',
			]
		);

		foreach ( $sites as $site ) {
			hook_uninstall( false, $site );
		}
	} elseif ( is_multisite() ) {
		if ( ! $site ) {
			$site = get_current_blog_id();
		}

		switch_to_blog( $site );
	}

	if ( webcomic( 'option.uninstall' ) ) {
		$path       = dirname( __DIR__ );
		$components = array_reverse( array_map( 'basename', array_unique( array_merge( [ $path . '/collection', $path . '/compat' ], glob( $path . '/*', GLOB_ONLYDIR ) ) ) ) );

		foreach ( $components as $component ) {
			webcomic_load( [ $component ] );

			/* This action is documented in Mgsisk\Webcomic\Plugin\hook_sanitize_components() */
			do_action( 'webcomic_deactivate_' . $component );
		}

		delete_option( 'webcomic' );
	}

	if ( is_multisite() ) {
		restore_current_blog();
	}
}

/**
 * Install the plugin.
 *
 * @return void
 */
function hook_install() {
	if ( ! webcomic( 'option.install' ) ) {
		return;
	}

	update_option( 'webcomic', [] );
}

/**
 * Add the settings page.
 *
 * @return void
 */
function hook_add_settings_page() {
	add_options_page(
		__( 'Webcomic Settings', 'webcomic' ),
		__( 'Webcomic', 'webcomic' ),
		'manage_options',
		'webcomic_options',
		function() {
			require __DIR__ . '/settings-inc-page.php';
		}
	);
}

/**
 * Register the plugin options.
 *
 * @return void
 */
function hook_register_options() {
	register_setting( 'webcomic', 'webcomic' );
}

/**
 * Add the settings section.
 *
 * @return void
 */
function hook_add_settings_section() {
	add_settings_section(
		'webcomic_general',
		'',
		function() {
			$args = [
				'file'      => __DIR__ . '/settings-inc-section.php',
				'option'    => webcomic( 'option.components' ),
				'label_for' => 'webcomic[components]',
				'css_class' => [ 'inactive', 'active' ],
				'uninstall' => '',
			];

			if ( webcomic( 'option.uninstall' ) ) {
				$args['uninstall'] = __( 'Deactivating a component will delete all data related to it.', 'webcomic' );
			}

			require $args['file'];
		},
		'webcomic_options'
	);
}

/**
 * Add the uninstall setting field.
 *
 * @return void
 */
function hook_add_field_uninstall() {
	add_settings_field(
		'webcomic_uninstall',
		__( 'Uninstall', 'webcomic' ),
		function( $args ) {
			require $args['file'];
		},
		'webcomic_options',
		'webcomic_general', [
			'file'      => __DIR__ . '/settings-inc-field-uninstall.php',
			'option'    => webcomic( 'option.uninstall' ),
			'label_for' => 'webcomic[uninstall]',
		]
	);
}

/**
 * Add the debug setting field.
 *
 * @return void
 */
function hook_add_field_debug() {
	if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
		return;
	}

	add_settings_field(
		'webcomic_debug',
		__( 'Debug', 'webcomic' ),
		function( $args ) {
			require $args['file'];
		},
		'webcomic_options',
		'webcomic_general', [
			'file'      => __DIR__ . '/settings-inc-field-debug.php',
			'option'    => webcomic( 'option.debug' ),
			'label_for' => 'webcomic[debug]',
		]
	);
}

/**
 * Add the help sidebar.
 *
 * @return void
 */
function hook_add_help_sidebar() {
	$screen = get_current_screen();

	if ( 'settings_page_webcomic_options' !== $screen->id ) {
		return;
	}

	$screen->set_help_sidebar( webcomic_help() );
}

/**
 * Add the overview help tab.
 *
 * @return void
 */
function hook_add_help_overview() {
	$screen = get_current_screen();

	if ( 'settings_page_webcomic_options' !== $screen->id ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'overview',
			'title'    => __( 'Overview', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/settings-inc-help-overview.php';
			},
		]
	);
}

/**
 * Add the details help tab.
 *
 * @return void
 */
function hook_add_help_details() {
	$screen = get_current_screen();

	if ( 'settings_page_webcomic_options' !== $screen->id ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'details',
			'title'    => __( 'Details', 'webcomic' ),
			'callback' => function() {
				$args = [
					'file'   => __DIR__ . '/settings-inc-help-details.php',
					'option' => webcomic( 'option' ),
				];

				ksort( $args['option'] );

				require $args['file'];
			},
		]
	);
}

/**
 * Add a component alert message if Inkblot 4 is the active theme.
 *
 * @return void
 */
function hook_inkblot_component_alert() {
	$theme = wp_get_theme();

	if ( 'inkblot' !== strtolower( $theme->name ) ) {
		$theme = wp_get_theme( $theme->template );
	}

	if ( 'inkblot' !== strtolower( $theme->name ) || '4' !== $theme->version[0] ) {
		return;
	}

	$missing = array_diff( [ 'character', 'collection', 'commerce', 'compat', 'restrict', 'storyline', 'transcribe' ], webcomic( 'option.components' ) );

	if ( ! $missing ) {
		return;
	}

	// Translators: 1: Theme name. 2: Theme major version. 3: Disabled plugin components.
	webcomic_notice( sprintf( __( '%1$s %2$s requires these disabled Webcomic components: %3$s', 'webcomic' ), $theme->name, $theme->version[0], ucwords( implode( ', ', $missing ) ) ), 'warning' );
}

/**
 * Add an uninstall alert message to the Plugins screen.
 *
 * @return void
 */
function hook_uninstall_alert() {
	if ( 'plugins' !== get_current_screen()->id || ! webcomic( 'option.uninstall' ) ) {
		return;
	}

	webcomic_notice( __( 'Deactivating Webcomic will delete all data related to it.', 'webcomic' ), 'warning' );
}

/**
 * Add an uninstall alert message to the network Plugins screen.
 *
 * @return void
 */
function hook_uninstall_network_alert() {
	if ( 'plugins-network' !== get_current_screen()->id ) {
		return;
	}

	$uninstall = 0;
	$sites     = get_sites(
		[
			'fields' => 'ids',
		]
	);

	foreach ( $sites as $site ) {
		$options = get_blog_option( $site, 'webcomic', [] );

		if ( ! $options['uninstall'] ) {
			continue;
		}

		$uninstall++;
	}

	if ( ! $uninstall ) {
		return;
	}

	// Translators: The number of sites with the uninstall option enabled.
	webcomic_notice( sprintf( _n( 'Deactivating Webcomic will delete all data related to it on %s site.', 'Deactivating Webcomic will delete all data related to it on %s sites.', $uninstall, 'webcomic' ), number_format_i18n( $uninstall ) ), 'warning' );
}

/**
 * Display administrative notifications.
 *
 * @return void
 */
function hook_display_admin_notices() {
	$notices = get_transient( 'webcomic_admin_notices' );

	if ( ! $notices ) {
		return;
	}

	delete_transient( 'webcomic_admin_notices' );

	foreach ( $notices as $notice ) {
		list( $class, $message ) = explode( '|', $notice, 2 );

		$class = esc_attr( $class );

		echo "<div class='notice notice-{$class} is-dismissible'><p>{$message}</p></div>"; // WPCS: xss ok.
	}
}

/**
 * Ensure only allowed options are set.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_options( array $options ) : array {
	$options += webcomic( 'option' );

	return array_intersect_key( $options, array_flip( [ 'version', 'upgrade', 'debug', 'uninstall', 'components', 'collections', 'compat' ] ) );
}

/**
 * Sanitize the component field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_components( array $options ) : array {
	$old_components        = webcomic( 'option.components' );
	$options['components'] = array_merge(
		[ 'collection' ],
		array_intersect( $options['components'], [ 'alert', 'character', 'commerce', 'compat', 'integrate', 'restrict', 'storyline', 'transcribe', 'twitter' ] )
	);

	foreach ( array_diff( $old_components, $options['components'] ) as $component ) {
		/**
		 * Deactivate a component.
		 *
		 * This action provides a way for components to perform component-specific
		 * cleanup and other actions before deactivation.
		 */
		do_action( 'webcomic_deactivate_' . $component );
	}

	foreach ( array_diff( $options['components'], $old_components ) as $component ) {
		webcomic_load( [ $component ] );

		/**
		 * Activate a component.
		 *
		 * This action provides a way for components to perform component-specific
		 * setup and other actions before activation.
		 */
		do_action( 'webcomic_activate_' . $component );
	}

	return $options;
}

/**
 * Sanitize the uninstall field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_uninstall( array $options ) : array {
	$options['uninstall'] = (bool) $options['uninstall'];

	return $options;
}

/**
 * Sanitize the debug field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_debug( array $options ) : array {
	$options['debug'] = ( defined( 'WP_DEBUG' ) && WP_DEBUG && $options['debug'] );

	return $options;
}

/**
 * Display additional plugin links.
 *
 * @param array  $meta The current plugin meta data.
 * @param string $file The current plugin file.
 * @param array  $data The current plugin metadata.
 * @param string $context The current plugin context.
 * @return array
 */
function hook_add_plugin_links( array $meta, string $file, array $data, string $context ) : array {
	if ( false === strpos( $file, '/webcomic.php' ) ) {
		return $meta;
	}

	$settings_url     = esc_url(
		add_query_arg(
			[
				'page' => 'webcomic_options',
			], admin_url( 'options-general.php' )
		)
	);
	$meta['settings'] = "<a href='{$settings_url}'>" . esc_html__( 'Settings', 'webcomic' ) . '</a>';
	$meta['support']  = '<a href="https://mgsisk.com/#support" target="_blank">' . esc_html__( 'Support', 'webcomic' ) . '</a>';

	return $meta;
}

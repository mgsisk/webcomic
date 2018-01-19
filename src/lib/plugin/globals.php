<?php
/**
 * Global plugin functions
 *
 * @package Webcomic
 */

/**
 * Get plugin data.
 *
 * @param string $key The data to get.
 * @return mixed|null
 * @SuppressWarnings(PHPMD.Superglobals) - $GLOBALS is purposely being used here to provide consistent access to global data.
 */
function webcomic( string $key = '' ) {
	$match = [];

	if ( ! $key ) {
		webcomic_error( __( 'The classic behavior of webcomic() is deprecated; please refer to the webcomic() documentation for updated usage information.', 'webcomic' ) );

		return true;
	} elseif ( 'file' === $key ) {
		return dirname( __DIR__, 2 ) . '/webcomic.php';
	} elseif ( ! preg_match( '/^(?:GLOBALS|option(?:\.(webcomic\d+))?)\.?/', $key, $match ) ) {
		return;
	}

	$value = $GLOBALS;

	if ( 0 !== strpos( $match[0], 'GLOBALS' ) ) {
		$match[] = 'webcomic';
		$value   = get_option( $match[1], [] );
	}

	foreach ( array_filter( explode( '.', str_replace( $match[0], '', $key ) ) ) as $k ) {
		if ( ! is_array( $value ) || ! array_key_exists( $k, $value ) ) {
			return;
		}

		$value = $value[ $k ];
	}

	if ( 0 === strpos( $match[0], 'GLOBALS' ) ) {
		return wp_unslash( $value );
	}

	return $value;
}

/**
 * Attempt to invoke a compatibility method.
 *
 * @param string $method The method to invoke, if it's available.
 * @param array  $args Optional arguments to pass to the method.
 * @param mixed  $default Optional value to return if $method does not exist.
 * @return mixed
 */
function webcomic_compat( string $method, array $args = [], $default = null ) {
	if ( ! class_exists( 'WebcomicTag' ) ) {
		return $default;
	}

	return call_user_func_array( [ 'WebcomicTag', $method ], $args );
}

/**
 * Trigger an error.
 *
 * @param string $message The error message.
 * @param int    $type Optional error type; one of E_USER_DEPRECATED,
 * E_USER_NOTICE, E_USER_WARNING, or E_USER_ERROR.
 * @return bool
 */
function webcomic_error( string $message, int $type = E_USER_DEPRECATED ) : bool {
	if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG || ! webcomic( 'option.debug' ) ) {
		return false;
	}

	// @codingStandardsIgnoreLine WordPress.PHP.DevelopmentFunctions - We're purposely using trigger_error().
	return trigger_error( esc_html( $message ), $type );
}

/**
 * Return help content.
 *
 * @return string
 */
function webcomic_help() : string {
	if ( ! is_admin() ) {
		return '';
	}

	$output  = '<p><b>' . esc_html__( 'For more information:', 'webcomic' ) . '</b></p>';
	$output .= '<ul>';
	$output .= '<li><a href="https://github.com/mgsisk/webcomic/wiki" target="_blank">' . esc_html__( 'User Guide', 'webcomic' ) . '</a></li>';
	$output .= '<li><a href="https://wordpress.org/support/plugin/webcomic" target="_blank">' . esc_html__( 'Support Forum', 'webcomic' ) . '</a></li>';
	$output .= '<li><a href="https://discord.gg/TNTfzzg" target="_blank">' . esc_html__( 'Discord Server', 'webcomic' ) . '</a></li>';
	$output .= '<li><a href="https://github.com/mgsisk/webcomic/issues" target="_blank">' . esc_html__( 'Issue Tracker', 'webcomic' ) . '</a></li>';
	$output .= '<li><a href="mailto:help@mgsisk.com?subject=Webcomic%20Help">' . esc_html__( 'Contact Mike', 'webcomic' ) . '</a></li>';
	$output .= '</ul>';
	$output .= '<p><a href="https://mgsisk.com/#support" target="_blank" class="button button-primary button-small">' . esc_html__( 'Support Webcomic', 'webcomic' ) . '</a></p>';
	$output .= '<p><small>';
	// Translators: Plugin version.
	$output .= sprintf( esc_html__( 'Thank you for creating with Webcomic %s', 'webcomic' ), webcomic( 'option.version' ) );
	$output .= '</small></p>';

	return $output;
}

/**
 * Set an administrative notification.
 *
 * @param string $message The notification message.
 * @param string $class Optional notification type; one of success, error,
 * warning, or info.
 * @return bool
 */
function webcomic_notice( string $message, string $class = 'success' ) : bool {
	if ( ! is_admin() ) {
		return false;
	}

	$message    = wp_kses_post( $message );
	$messages   = get_transient( 'webcomic_admin_notices' );
	$messages[] = "{$class}|{$message}";

	return set_transient( 'webcomic_admin_notices', array_unique( $messages ), 1 );
}

/**
 * Initialize the plugin, upgrading components as necessary.
 *
 * @param string $version The version to upgrade to.
 * @return void
 * @codingStandardsIgnoreStart Generic.Metrics.CyclomaticComplexity.TooHigh - Required for compatibility.
 */
function webcomic_init( string $version ) {
	$path    = dirname( __DIR__ );
	$options = get_option(
		'webcomic', [
			'version' => '0.0.0-new',
		]
	);

	if ( version_compare( $version, $options['version'], '<=' ) ) {
		return;
	} elseif ( '0.0.0-new' === $options['version'] && isset( get_option( 'webcomic_options', [] )['version'] ) ) {
		$options['version'] = (string) get_option( 'webcomic_options' )['version']; // NOTE Webcomic 3.x and 4.x store version information in webcomic_options.
	} elseif ( '0.0.0-new' === $options['version'] && get_option( 'webcomic_version' ) ) {
		$options['version'] = (string) get_option( 'webcomic_version' ); // NOTE Webcomic 1.x and 2.x store version information in webcomic_version.
	}

	$components = array_unique( array_merge( [ $path . '/compat', $path . '/plugin', $path . '/collection' ], glob( $path . '/*', GLOB_ONLYDIR ) ) );

	foreach ( $components as $component ) {
		if ( ! file_exists( $component. '/upgrade.php' ) ) {
			continue;
		}

		require $component . '/upgrade.php';

		$namespace = 'mgsisk\webcomic\\' . basename( $component );
		$functions = array_filter(
			get_defined_functions()['user'], function( $function ) use ( $namespace ) {
				return 0 === strpos( $function, "{$namespace}\\v" );
			}
		);

		sort( $functions, SORT_NATURAL );

		foreach ( $functions as $function ) {
			$upgrade_version = str_replace( [ "{$namespace}\\v", '_' ], [ '', '.' ], $function );

			if ( version_compare( $upgrade_version, $options['version'], '<=' ) ) {
				continue;
			} elseif ( '0.0.0-new' === $options['version'] && '0.0.0' !== $upgrade_version ) {
				break;
			}

			$function( $options['version'] );
		}
	}

	$options = [
		'version' => $version,
		'upgrade' => $options['version'],
	] + get_option( 'webcomic', [] );

	update_option( 'webcomic', $options );

	$learn = '<a href="https://github.com/mgsisk/webcomic/releases/latest/" target="_blank">' . esc_html__( 'Learn more', 'webcomic' ) . '</a>';

	// Translators: Plugin version.
	webcomic_notice( '<strong>' . sprintf( __( 'Thank you for creating with Webcomic %s', 'webcomic' ), webcomic( 'option.version' ) ) . "</strong> {$learn}", 'info' );
} // @codingStandardsIgnoreEnd Generic.Metrics.CyclomaticComplexity.TooHigh

/**
 * Load plugin components.
 *
 * @param array $components The components to load.
 * @return void
 */
function webcomic_load( array $components ) {
	$path       = dirname( __DIR__ );
	$components = array_merge( [ 'plugin' ], $components );

	foreach ( $components as $component ) {
		$callback = 'Mgsisk\Webcomic\\' . $component;

		if ( function_exists( $callback ) || ! file_exists( "{$path}/{$component}.php" ) ) {
			continue;
		}

		require "{$path}/{$component}.php";

		$callback();
	}
}

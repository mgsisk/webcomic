<?php
/**
 * Alert settings functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Alert;

use WP_Screen;

/**
 * Add settings hooks.
 *
 * @return void
 */
function settings() {
	add_filter( 'webcomic_activate_alert', __NAMESPACE__ . '\hook_activate' );
	add_filter( 'webcomic_deactivate_alert', __NAMESPACE__ . '\hook_deactivate' );
	add_filter( 'webcomic_collection_allowed_options', __NAMESPACE__ . '\hook_add_allowed_options' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_alert_schedule' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_settings_section' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_buffer' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_hiatus' );
	add_filter( 'current_screen', __NAMESPACE__ . '\hook_enqueue_record_scripts' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_help_tab' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_buffer' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_hiatus' );
	}
}

/**
 * Activate the alert component.
 *
 * @return void
 */
function hook_activate() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		if ( null !== webcomic( "option.{$collection}.alert_buffer" ) ) {
			continue;
		}

		update_option(
			$collection, [
				'alert_buffer' => [],
				'alert_hiatus' => [],
			]
		);
	}
}

/**
 * Deactivate the alert component.
 *
 * @return void
 */
function hook_deactivate() {
	if ( ! webcomic( 'option.uninstall' ) ) {
		return;
	}

	wp_unschedule_event( wp_next_scheduled( 'webcomic_alert' ), 'webcomic_alert' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter(
			"sanitize_option_{$collection}", function( array $options ) use ( $collection ) {
				remove_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_buffer' );
				remove_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_hiatus' );

				unset(
					$options['alert_buffer'],
					$options['alert_hiatus']
				);

				return $options;
			}, 99
		);

		update_option(
			$collection, [
				'alert_buffer' => [],
				'alert_hiatus' => [],
			]
		);
	}
}

/**
 * Add the alert allowed collection options.
 *
 * @param array $allowed The allowed options.
 * @return array
 */
function hook_add_allowed_options( array $allowed ) : array {
	return array_merge( $allowed, [ 'alert_buffer', 'alert_hiatus' ] );
}

/**
 * Add the webcomic_alert scheduled event.
 *
 * @return void
 */
function hook_add_alert_schedule() {
	if ( wp_get_schedule( 'webcomic_alert' ) ) {
		return;
	}

	wp_schedule_event( time(), 'daily', 'webcomic_alert' );
}

/**
 * Add the settings section.
 *
 * @return void
 */
function hook_add_settings_section() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_section(
			"{$collection}_alert",
			'<span class="dashicons dashicons-megaphone"></span> ' . esc_html__( 'Alerts', 'webcomic' ),
			function() {
				echo '<div></div>';
			},
			"{$collection}_settings"
		);
	}
}

/**
 * Add the buffer setting field.
 *
 * @return void
 */
function hook_add_field_buffer() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_buffer",
			__( 'Buffer', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_alert", [
				'file'      => __DIR__ . '/settings-inc-field-buffer.php',
				'option'    => webcomic( "option.{$collection}.alert_buffer" ),
				'label_for' => "{$collection}[alert_buffer]",
			]
		);
	}
}

/**
 * Add the hiatus setting field.
 *
 * @return void
 */
function hook_add_field_hiatus() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_hiatus",
			__( 'Hiatus', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_alert", [
				'file'      => __DIR__ . '/settings-inc-field-hiatus.php',
				'option'    => webcomic( "option.{$collection}.alert_hiatus" ),
				'label_for' => "{$collection}[alert_hiatus]",
			]
		);
	}
}

/**
 * Enqueue the table record scripts.
 *
 * @param WP_Screen $screen The current screen.
 * @return void
 */
function hook_enqueue_record_scripts( WP_Screen $screen ) {
	if ( ! preg_match( '/^webcomic\d+_page_webcomic\d+_options$/', $screen->id ) ) {
		return;
	}

	add_filter( 'webcomic_enqueue_table_record', '__return_true' );
}

/**
 * Add the help tab.
 *
 * @return void
 */
function hook_add_help_tab() {
	$screen = get_current_screen();

	if ( ! preg_match( '/^webcomic\d+_page_webcomic\d+_options$/', $screen->id ) ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'alert',
			'title'    => __( 'Alerts', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/settings-inc-help.php';
			},
		]
	);
}

/* ===== Collection Hooks =================================================== */

/**
 * Sanitize the alert_buffer field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_buffer( array $options ) : array {
	if ( isset( $options['alert_buffer']['comics'], $options['alert_buffer']['emails'] ) ) {
		$alerts = [];

		foreach ( $options['alert_buffer']['comics'] as $key => $comics ) {
			if ( ! $comics || ! $options['alert_buffer']['emails'][ $key ] ) {
				continue;
			} elseif ( empty( $alerts[ $comics ] ) ) {
				$alerts[ $comics ] = [];
			}

			$alerts[ $comics ] = array_merge( $alerts[ $comics ], explode( ',', $options['alert_buffer']['emails'][ $key ] ) );
		}

		$alerts = array_map(
			function( $value ) {
					return implode( ',', $value );
			}, $alerts
		);

		$options['alert_buffer'] = $alerts;
	}

	foreach ( array_keys( $options['alert_buffer'] ) as $comics ) {
		$options['alert_buffer'][ $comics ] = explode( ',', $options['alert_buffer'][ $comics ] );
		$options['alert_buffer'][ $comics ] = array_map( 'trim', $options['alert_buffer'][ $comics ] );
		$options['alert_buffer'][ $comics ] = array_unique( $options['alert_buffer'][ $comics ] );
		$options['alert_buffer'][ $comics ] = array_filter(
			$options['alert_buffer'][ $comics ], function( $email ) {
				return is_email( $email );
			}
		);

		if ( ! $options['alert_buffer'][ $comics ] ) {
			unset( $options['alert_buffer'][ $comics ] );

			continue;
		}

		$options['alert_buffer'][ $comics ] = implode( ',', $options['alert_buffer'][ $comics ] );
	}

	krsort( $options['alert_buffer'] );

	return $options;
}

/**
 * Sanitize the alert_hiatus field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_hiatus( array $options ) : array {
	if ( isset( $options['alert_hiatus']['days'], $options['alert_hiatus']['emails'] ) ) {
		$alerts = [];

		foreach ( $options['alert_hiatus']['days'] as $key => $days ) {
			if ( ! $days || ! $options['alert_hiatus']['emails'][ $key ] ) {
				continue;
			} elseif ( empty( $alerts[ $days ] ) ) {
				$alerts[ $days ] = [];
			}

			$alerts[ $days ] = array_merge( $alerts[ $days ], explode( ',', $options['alert_hiatus']['emails'][ $key ] ) );
		}

		$alerts = array_map(
			function( $value ) {
					return implode( ',', $value );
			}, $alerts
		);

		krsort( $alerts );

		$options['alert_hiatus'] = $alerts;
	}

	foreach ( array_keys( $options['alert_hiatus'] ) as $days ) {
		$options['alert_hiatus'][ $days ] = explode( ',', $options['alert_hiatus'][ $days ] );
		$options['alert_hiatus'][ $days ] = array_map( 'trim', $options['alert_hiatus'][ $days ] );
		$options['alert_hiatus'][ $days ] = array_unique( $options['alert_hiatus'][ $days ] );
		$options['alert_hiatus'][ $days ] = array_filter(
			$options['alert_hiatus'][ $days ], function( $email ) {
				return is_email( $email );
			}
		);

		if ( ! $options['alert_hiatus'][ $days ] ) {
			unset( $options['alert_hiatus'][ $days ] );

			continue;
		}

		$options['alert_hiatus'][ $days ] = implode( ',', $options['alert_hiatus'][ $days ] );
	}

	krsort( $options['alert_hiatus'] );

	return $options;
}

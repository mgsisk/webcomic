<?php
/**
 * Transcribe settings functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe;

/**
 * Add settings hooks.
 *
 * @return void
 */
function settings() {
	add_filter( 'webcomic_activate_transcribe', __NAMESPACE__ . '\hook_activate' );
	add_filter( 'webcomic_deactivate_transcribe', __NAMESPACE__ . '\hook_deactivate' );
	add_filter( 'webcomic_collection_allowed_options', __NAMESPACE__ . '\hook_add_allowed_options' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_settings_section' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_comics' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_permissions' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_notifications' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_help_tab' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_comics' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_permissions' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_notifications' );
	}
}

/**
 * Activate the transcribe component.
 *
 * @return void
 */
function hook_activate() {
	set_transient( 'webcomic_flush_rewrite_rules', true, 1 );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		if ( null !== webcomic( "option.{$collection}.transcribe_comic" ) ) {
			continue;
		}

		update_option(
			$collection, [
				'transcribe_comic'     => false,
				'transcribe_close'     => 0,
				'transcribe_require'   => 'loggedin',
				'transcribe_publish'   => '',
				'transcribe_alert_pub' => false,
				'transcribe_alert_mod' => false,
			]
		);
	}
}

/**
 * Deactivate the transcribe component.
 *
 * @return void
 * @suppress PhanAccessMethodInternal - get_terms() incorrectly triggers this.
 */
function hook_deactivate() {
	set_transient( 'webcomic_flush_rewrite_rules', true, 1 );

	if ( ! webcomic( 'option.uninstall' ) ) {
		return;
	}

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter(
			"sanitize_option_{$collection}", function( array $options ) use ( $collection ) {
				remove_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_comics' );
				remove_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_permissions' );
				remove_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_notifications' );

				unset(
					$options['transcribe_comic'],
					$options['transcribe_close'],
					$options['transcribe_require'],
					$options['transcribe_publish'],
					$options['transcribe_alert_pub'],
					$options['transcribe_alert_mod']
				);

				return $options;
			}, 99
		);

		update_option(
			$collection, [
				'transcribe_comic'     => false,
				'transcribe_close'     => 0,
				'transcribe_require'   => 'loggedin',
				'transcribe_publish'   => '',
				'transcribe_alert_pub' => false,
				'transcribe_alert_mod' => false,
			]
		);
	}

	$table = webcomic( 'GLOBALS.wpdb' )->options;

	webcomic( 'GLOBALS.wpdb' )->query( "DELETE from {$table} where option_name like 'widget_mgsisk_webcomic_transcribe_%'" );

	$transcripts = get_posts(
		[
			'fields'         => 'ids',
			'post_type'      => 'webcomic_transcript',
			'post_status'    => 'any',
			'posts_per_page' => -1,
		]
	);

	foreach ( $transcripts as $transcript ) {
		wp_delete_post( $transcript, true );
	}

	$languages = get_terms(
		[
			'fields'     => 'ids',
			'taxonomy'   => 'webcomic_transcript_language',
			'hide_empty' => false,
		]
	);

	foreach ( $languages as $language ) {
		wp_delete_term( $language, 'webcomic_transcript_language' );
	}

	delete_metadata( 'post', 0, 'webcomic_transcribe', null, true );
}

/**
 * Add the transcribe allowed collection options.
 *
 * @param array $allowed The allowed options.
 * @return array
 */
function hook_add_allowed_options( array $allowed ) : array {
	return array_merge( $allowed, [ 'transcribe_comic', 'transcribe_close', 'transcribe_close_check', 'transcribe_require', 'transcribe_require_check', 'transcribe_publish', 'transcribe_publish_check', 'transcribe_alert_pub', 'transcribe_alert_mod' ] );
}

/**
 * Add the settings section.
 *
 * @return void
 */
function hook_add_settings_section() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_section(
			"{$collection}_transcribe",
			'<span class="dashicons dashicons-testimonial"></span> ' . esc_html__( 'Transcripts', 'webcomic' ),
			function() {
				echo '<div></div>';
			},
			"{$collection}_settings"
		);
	}
}

/**
 * Add the comics setting field.
 *
 * @return void
 */
function hook_add_field_comics() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_comics",
			__( 'Comics', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_transcribe", [
				'file'              => __DIR__ . '/settings-inc-field-comics.php',
				'option'            => webcomic( "option.{$collection}.transcribe_comic" ),
				'option_close'      => webcomic( "option.{$collection}.transcribe_close" ),
				'label_for'         => "{$collection}[transcribe_comic]",
				'label_close'       => "{$collection}[transcribe_close]",
				'label_close_check' => "{$collection}[transcribe_close_check]",
			]
		);
	}
}

/**
 * Add the permissions setting field.
 *
 * @return void
 */
function hook_add_field_permissions() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_permissions",
			__( 'Permissions', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_transcribe", [
				'file'                => __DIR__ . '/settings-inc-field-permissions.php',
				'option'              => webcomic( "option.{$collection}.transcribe_require" ),
				'option_publish'      => webcomic( "option.{$collection}.transcribe_publish" ),
				'label_for'           => "{$collection}[transcribe_require_check]",
				'label_option'        => "{$collection}[transcribe_require]",
				'label_publish'       => "{$collection}[transcribe_publish]",
				'label_publish_check' => "{$collection}[transcribe_publish_check]",
			]
		);
	}
}

/**
 * Add the notifications setting field.
 *
 * @return void
 */
function hook_add_field_notifications() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_donation",
			__( 'Notifications', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_transcribe", [
				'file'       => __DIR__ . '/settings-inc-field-notifications.php',
				'option'     => webcomic( "option.{$collection}.transcribe_alert_pub" ),
				'option_mod' => webcomic( "option.{$collection}.transcribe_alert_mod" ),
				'label_for'  => "{$collection}[transcribe_alert_pub]",
				'label_mod'  => "{$collection}[transcribe_alert_mod]",
			]
		);
	}
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
			'id'       => 'transcribe',
			'title'    => __( 'Transcripts', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/settings-inc-help.php';
			},
		]
	);
}

/* ===== Collection Hooks =================================================== */

/**
 * Sanitize the comics field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_comics( array $options ) : array {
	$options['transcribe_comic'] = (bool) $options['transcribe_comic'];
	$close                       = 0;

	if ( isset( $options['transcribe_close_check'] ) ) {
		$close = abs( (int) $options['transcribe_close'] );
	}

	$options['transcribe_close'] = $close;

	unset( $options['transcribe_close_check'] );

	return $options;
}

/**
 * Sanitize the permissions field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_permissions( array $options ) : array {
	$require = '';
	$publish = '';

	if ( isset( $options['transcribe_require_check'] ) && in_array( $options['transcribe_require'], [ '', 'name_email', 'loggedin' ], true ) ) {
		$require = $options['transcribe_require'];
	}

	if ( isset( $options['transcribe_publish_check'] ) && in_array( $options['transcribe_publish'], [ '', 'name_email', 'loggedin' ], true ) ) {
		$publish = $options['transcribe_publish'];
	}

	unset( $options['transcribe_require_check'], $options['transcribe_publish_check'] );

	$options['transcribe_require'] = $require;
	$options['transcribe_publish'] = $publish;

	return $options;
}

/**
 * Sanitize the notifications field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_notifications( array $options ) : array {
	$options['transcribe_alert_pub'] = (bool) $options['transcribe_alert_pub'];
	$options['transcribe_alert_mod'] = (bool) $options['transcribe_alert_mod'];

	return $options;
}

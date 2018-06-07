<?php
/**
 * Restrict settings functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Restrict;

use WP_Screen;

/**
 * Add settings hooks.
 *
 * @return void
 */
function settings() {
	add_filter( 'webcomic_activate_restrict', __NAMESPACE__ . '\hook_activate' );
	add_filter( 'webcomic_deactivate_restrict', __NAMESPACE__ . '\hook_deactivate' );
	add_filter( 'webcomic_collection_allowed_options', __NAMESPACE__ . '\hook_add_allowed_options' );
	add_filter( 'webcomic_new_collection', __NAMESPACE__ . '\hook_new_collection_restrict' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_settings_section' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_age' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_age_media' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_password_media' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_referrers' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_referrers_media' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_roles' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_roles_media' );
	add_filter( 'current_screen', __NAMESPACE__ . '\hook_enqueue_record_scripts' );
	add_filter( 'admin_head', __NAMESPACE__ . '\add_help_tab' );
	add_filter( 'delete_attachment', __NAMESPACE__ . '\hook_delete_age_media' );
	add_filter( 'delete_attachment', __NAMESPACE__ . '\hook_delete_password_media' );
	add_filter( 'delete_attachment', __NAMESPACE__ . '\hook_delete_referrers_media' );
	add_filter( 'delete_attachment', __NAMESPACE__ . '\hook_delete_roles_media' );
	add_filter( 'display_media_states', __NAMESPACE__ . '\hook_add_age_media_state' );
	add_filter( 'display_media_states', __NAMESPACE__ . '\hook_add_password_media_state' );
	add_filter( 'display_media_states', __NAMESPACE__ . '\hook_add_referrers_media_state' );
	add_filter( 'display_media_states', __NAMESPACE__ . '\hook_add_roles_media_state' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_age' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_age_media' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_password_media' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_referrers' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_referrers_media' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_roles' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_roles_media' );
	}
}

/**
 * Activate the restrict component.
 *
 * @return void
 */
function hook_activate() {
	set_transient( 'webcomic_flush_rewrite_rules', true, 1 );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		if ( null !== webcomic( "option.{$collection}.restrict_age" ) ) {
			continue;
		}

		update_option(
			$collection, [
				'restrict_age'             => 0,
				'restrict_age_media'       => 0,
				'restrict_password_media'  => 0,
				'restrict_referrers'       => [],
				'restrict_referrers_media' => 0,
				'restrict_roles'           => [],
				'restrict_roles_media'     => 0,
			]
		);
	}
}

/**
 * Deactivate the restrict component.
 *
 * @return void
 */
function hook_deactivate() {
	set_transient( 'webcomic_flush_rewrite_rules', true, 1 );

	if ( ! webcomic( 'option.uninstall' ) ) {
		return;
	}

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter(
			"sanitize_option_{$collection}", function( array $options ) use ( $collection ) {
				remove_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_age' );
				remove_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_age_media' );
				remove_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_password_media' );
				remove_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_referrers' );
				remove_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_referrers_media' );
				remove_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_roles' );
				remove_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_roles_media' );

				unset(
					$options['restrict_age'],
					$options['restrict_age_media'],
					$options['restrict_password_media'],
					$options['restrict_referrers'],
					$options['restrict_referrers_media'],
					$options['restrict_roles'],
					$options['restrict_roles_media']
				);

				return $options;
			}, 99
		);

		update_option(
			$collection, [
				'restrict_age'             => 0,
				'restrict_age_media'       => 0,
				'restrict_password_media'  => 0,
				'restrict_referrers'       => [],
				'restrict_referrers_media' => 0,
				'restrict_roles'           => [],
				'restrict_roles_media'     => 0,
			]
		);
	}

	delete_metadata( 'post', 0, 'webcomic_restrict_age', '', true );
	delete_metadata( 'post', 0, 'webcomic_restrict_password_media', '', true );
	delete_metadata( 'post', 0, 'webcomic_restrict_age_media', '', true );
	delete_metadata( 'post', 0, 'webcomic_restrict_referrers', '', true );
	delete_metadata( 'post', 0, 'webcomic_restrict_referrers_media', '', true );
	delete_metadata( 'post', 0, 'webcomic_restrict_roles', '', true );
	delete_metadata( 'post', 0, 'webcomic_restrict_roles_media', '', true );
}

/**
 * Add the restrict allowed collection options.
 *
 * @param array $allowed The allowed options.
 * @return array
 */
function hook_add_allowed_options( array $allowed ) : array {
	return array_merge( $allowed, [ 'restrict_age', 'restrict_age_media', 'restrict_password_media', 'restrict_referrers', 'restrict_referrers_media', 'restrict_roles', 'restrict_roles_media' ] );
}

/**
 * Clear the new collection's alternative media settings.
 *
 * @param array $defaults The default settings of the new collection.
 * @return array
 */
function hook_new_collection_restrict( array $defaults ) : array {
	$defaults['restrict_age_media']       = 0;
	$defaults['restrict_referrers_media'] = 0;
	$defaults['restrict_roles_media']     = 0;
	$defaults['restrict_password_media']  = 0;

	return $defaults;
}

/**
 * Add the settings section.
 *
 * @return void
 */
function hook_add_settings_section() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_section(
			"{$collection}_restrict",
			'<span class="dashicons dashicons-lock"></span> ' . esc_html__( 'Restrictions', 'webcomic' ),
			function() {
				echo '<div></div>';
			},
			"{$collection}_settings"
		);
	}
}

/**
 * Add the age setting field.
 *
 * @return void
 */
function hook_add_field_age() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_restrict_age",
			__( 'Minimum Age', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_restrict", [
				'file'      => __DIR__ . '/settings-inc-field-age.php',
				'option'    => webcomic( "option.{$collection}.restrict_age" ),
				'label_for' => "{$collection}[restrict_age]",
			]
		);
	}
}

/**
 * Add the age media setting field.
 *
 * @return void
 */
function hook_add_field_age_media() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_restrict_age_media",
			__( 'Age-Restricted Media', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_restrict", [
				'file'      => __DIR__ . '/settings-inc-field-age-media.php',
				'option'    => webcomic( "option.{$collection}.restrict_age_media" ),
				'label_for' => "{$collection}[restrict_age_media]",
			]
		);
	}
}

/**
 * Add the password media setting field.
 *
 * @return void
 */
function hook_add_field_password_media() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_restrict_password_media",
			__( 'Password-Restricted Media', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_restrict", [
				'file'      => __DIR__ . '/settings-inc-field-password-media.php',
				'option'    => webcomic( "option.{$collection}.restrict_password_media" ),
				'label_for' => "{$collection}[restrict_password_media]",
			]
		);
	}
}

/**
 * Add the referrers setting field.
 *
 * @return void
 */
function hook_add_field_referrers() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_restrict_referrers",
			__( 'Allowed Referrers', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_restrict", [
				'file'      => __DIR__ . '/settings-inc-field-referrers.php',
				'option'    => webcomic( "option.{$collection}.restrict_referrers" ),
				'label_for' => "{$collection}[restrict_referrers]",
			]
		);
	}
}

/**
 * Add the referrers media setting field.
 *
 * @return void
 */
function hook_add_field_referrers_media() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_restrict_referrers_media",
			__( 'Referrer-Restricted Media', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_restrict", [
				'file'      => __DIR__ . '/settings-inc-field-referrers-media.php',
				'option'    => webcomic( "option.{$collection}.restrict_referrers_media" ),
				'label_for' => "{$collection}[restrict_referrers_media]",
			]
		);
	}
}

/**
 * Add the roles setting field.
 *
 * @return void
 */
function hook_add_field_roles() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_restrict_roles",
			__( 'Accessible Roles', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_restrict", [
				'file'      => __DIR__ . '/settings-inc-field-roles.php',
				'option'    => webcomic( "option.{$collection}.restrict_roles" ),
				'label_for' => "{$collection}[restrict_roles]",
			]
		);
	}
}

/**
 * Add the roles media setting field.
 *
 * @return void
 */
function hook_add_field_roles_media() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_restrict_roles_media",
			__( 'Role-Restricted Media', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_restrict", [
				'file'      => __DIR__ . '/settings-inc-field-roles-media.php',
				'option'    => webcomic( "option.{$collection}.restrict_roles_media" ),
				'label_for' => "{$collection}[restrict_roles_media]",
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
function add_help_tab() {
	$screen = get_current_screen();

	if ( ! preg_match( '/^webcomic\d+_page_webcomic\d+_options$/', $screen->id ) ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'restrict',
			'title'    => __( 'Restrictions', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/settings-inc-help.php';
			},
		]
	);
}

/**
 * Delete the age media setting when a post is deleted.
 *
 * @param mixed $id The post ID.
 * @return void
 */
function hook_delete_age_media( $id ) {
	$contexts = array_unique( get_post_meta( $id, 'webcomic_restrict_age_collection' ) );

	foreach ( $contexts as $collection ) {
		if ( ! webcomic_collection_exists( $collection ) ) {
			continue;
		}

		update_option(
			$collection, [
				'restrict_age_media' => 0,
			]
		);
	}
}

/**
 * Delete the password media setting when a post is deleted.
 *
 * @param mixed $id The post ID.
 * @return void
 */
function hook_delete_password_media( $id ) {
	$contexts = array_unique( get_post_meta( $id, 'webcomic_restrict_password_collection' ) );

	foreach ( $contexts as $collection ) {
		if ( ! webcomic_collection_exists( $collection ) ) {
			continue;
		}

		update_option(
			$collection, [
				'restrict_password_media' => 0,
			]
		);
	}
}

/**
 * Delete the referrers media setting when a post is deleted.
 *
 * @param mixed $id The post ID.
 * @return void
 */
function hook_delete_referrers_media( $id ) {
	$contexts = array_unique( get_post_meta( $id, 'webcomic_restrict_referrers_collection' ) );

	foreach ( $contexts as $collection ) {
		if ( ! webcomic_collection_exists( $collection ) ) {
			continue;
		}

		update_option(
			$collection, [
				'restrict_referrers_media' => 0,
			]
		);
	}
}

/**
 * Delete the roles media setting when a post is deleted.
 *
 * @param mixed $id The post ID.
 * @return void
 */
function hook_delete_roles_media( $id ) {
	$contexts = array_unique( get_post_meta( $id, 'webcomic_restrict_roles_collection' ) );

	foreach ( $contexts as $collection ) {
		if ( ! webcomic_collection_exists( $collection ) ) {
			continue;
		}

		update_option(
			$collection, [
				'restrict_roles_media' => 0,
			]
		);
	}
}

/**
 * Add media state for user age media.
 *
 * @param array $states The media states for the current object.
 * @return array
 */
function hook_add_age_media_state( array $states ) : array {
	$contexts = array_unique( get_post_meta( get_the_ID(), 'webcomic_restrict_age_collection' ) );

	if ( ! $contexts ) {
		return $states;
	}

	foreach ( $contexts as $collection ) {
		// Translators: Post type name.
		$states[] = sprintf( __( '%s Age-Restricted Image', 'webcomic' ), webcomic( "option.{$collection}.name" ) );
	}

	return $states;
}

/**
 * Add media state for referrers media.
 *
 * @param array $states The media states for the current object.
 * @return array
 */
function hook_add_referrers_media_state( array $states ) : array {
	$contexts = array_unique( get_post_meta( get_the_ID(), 'webcomic_restrict_referrers_collection' ) );

	if ( ! $contexts ) {
		return $states;
	}

	foreach ( $contexts as $collection ) {
		// Translators: Post type name.
		$states[] = sprintf( __( '%s Referrer-Restricted Image', 'webcomic' ), webcomic( "option.{$collection}.name" ) );
	}

	return $states;
}

/**
 * Add media state for user roles media.
 *
 * @param array $states The media states for the current object.
 * @return array
 */
function hook_add_roles_media_state( array $states ) : array {
	$contexts = array_unique( get_post_meta( get_the_ID(), 'webcomic_restrict_roles_collection' ) );

	if ( ! $contexts ) {
		return $states;
	}

	foreach ( $contexts as $collection ) {
		// Translators: Post type name.
		$states[] = sprintf( __( '%s Role-Restricted Image', 'webcomic' ), webcomic( "option.{$collection}.name" ) );
	}

	return $states;
}

/**
 * Add media state for user password media.
 *
 * @param array $states The media states for the current object.
 * @return array
 */
function hook_add_password_media_state( array $states ) : array {
	$contexts = array_unique( get_post_meta( get_the_ID(), 'webcomic_restrict_password_collection' ) );

	if ( ! $contexts ) {
		return $states;
	}

	foreach ( $contexts as $collection ) {
		// Translators: Post type name.
		$states[] = sprintf( __( '%s Password-Restricted Image', 'webcomic' ), webcomic( "option.{$collection}.name" ) );
	}

	return $states;
}

/* ===== Collection Hooks =================================================== */

/**
 * Sanitize the age field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_age( array $options ) : array {
	$options['restrict_age'] = abs( (int) $options['restrict_age'] );

	return $options;
}

/**
 * Sanitize the age media field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_age_media( array $options ) : array {
	$options['restrict_age_media'] = abs( (int) $options['restrict_age_media'] );
	$old_media                     = webcomic( "option.{$options['id']}.restrict_age_media" );

	if ( $options['restrict_age_media'] !== $old_media ) {
		if ( $options['restrict_age_media'] ) {
			add_post_meta( $options['restrict_age_media'], 'webcomic_restrict_age_collection', $options['id'] );
		}

		if ( $old_media ) {
			delete_post_meta( $old_media, 'webcomic_restrict_age_collection', $options['id'] );
		}
	}

	return $options;
}

/**
 * Sanitize the password media field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_password_media( array $options ) : array {
	$options['restrict_password_media'] = abs( (int) $options['restrict_password_media'] );
	$old_media                          = webcomic( "option.{$options['id']}.restrict_password_media" );

	if ( $options['restrict_password_media'] !== $old_media ) {
		if ( $options['restrict_password_media'] ) {
			add_post_meta( $options['restrict_password_media'], 'webcomic_restrict_password_collection', $options['id'] );
		}

		if ( $old_media ) {
			delete_post_meta( $old_media, 'webcomic_restrict_password_collection', $options['id'] );
		}
	}

	return $options;
}

/**
 * Sanitize the roles field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_roles( array $options ) : array {
	$options['restrict_roles'] = array_intersect( $options['restrict_roles'], array_merge( [ '~loggedin~' ], array_keys( get_editable_roles() ) ) );

	if ( in_array( '~loggedin~', $options['restrict_roles'], true ) ) {
		$options['restrict_roles'] = [ '~loggedin~' ];
	}

	return $options;
}

/**
 * Sanitize the referrers media field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_referrers_media( array $options ) : array {
	$options['restrict_referrers_media'] = abs( (int) $options['restrict_referrers_media'] );
	$old_media                           = webcomic( "option.{$options['id']}.restrict_referrers_media" );

	if ( $options['restrict_referrers_media'] !== $old_media ) {
		if ( $options['restrict_referrers_media'] ) {
			add_post_meta( $options['restrict_referrers_media'], 'webcomic_restrict_referrers_collection', $options['id'] );
		}

		if ( $old_media ) {
			delete_post_meta( $old_media, 'webcomic_restrict_referrers_collection', $options['id'] );
		}
	}

	return $options;
}

/**
 * Sanitize the referrers field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_referrers( array $options ) : array {
	$options['restrict_referrers'] = array_map( 'trim', $options['restrict_referrers'] );

	foreach ( $options['restrict_referrers'] as $key => $referrer ) {
		if ( filter_var( $referrer, FILTER_VALIDATE_URL ) || filter_var( "http://{$referrer}", FILTER_VALIDATE_URL ) ) {
			continue;
		}

		unset( $options['restrict_referrers'][ $key ] );
	}

	sort( $options['restrict_referrers'] );

	return $options;
}

/**
 * Sanitize the roles media field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_roles_media( array $options ) : array {
	$options['restrict_roles_media'] = abs( (int) $options['restrict_roles_media'] );
	$old_media                       = webcomic( "option.{$options['id']}.restrict_roles_media" );

	if ( $options['restrict_roles_media'] !== $old_media ) {
		if ( $options['restrict_roles_media'] ) {
			add_post_meta( $options['restrict_roles_media'], 'webcomic_restrict_roles_collection', $options['id'] );
		}

		if ( $old_media ) {
			delete_post_meta( $old_media, 'webcomic_restrict_roles_collection', $options['id'] );
		}
	}

	return $options;
}

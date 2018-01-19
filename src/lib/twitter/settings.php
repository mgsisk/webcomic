<?php
/**
 * Twitter settings functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Twitter;

/**
 * Add settings hooks.
 *
 * @return void
 */
function settings() {
	add_filter( 'webcomic_activate_twitter', __NAMESPACE__ . '\hook_activate' );
	add_filter( 'webcomic_deactivate_twitter', __NAMESPACE__ . '\hook_deactivate' );
	add_filter( 'webcomic_collection_allowed_options', __NAMESPACE__ . '\hook_add_allowed_options' );
	add_filter( 'webcomic_new_collection', __NAMESPACE__ . '\hook_new_collection_restrict' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_settings_section' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_account' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_update' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_status' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_card' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_delete_twitter_tokens' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_settings_styles' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_settings_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_settings_styles' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_settings_scripts' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_help_tab' );
	add_filter( 'delete_attachment', __NAMESPACE__ . '\hook_delete_twitter_media' );
	add_filter( 'display_media_states', __NAMESPACE__ . '\hook_add_twitter_media_state' );
	add_filter( 'wp_ajax_webcomic_twitter_account', __NAMESPACE__ . '\hook_get_account_details' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_account' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_update' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_status' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_card' );
	}
}

/**
 * Activate the Twitter component.
 *
 * @return void
 */
function hook_activate() {
	set_transient( 'webcomic_flush_rewrite_rules', true, 1 );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		if ( null !== webcomic( "option.{$collection}.twitter_update" ) ) {
			continue;
		}

		update_option(
			$collection, [
				'twitter_oauth'            => [
					'oauth_consumer_key'    => '',
					'oauth_consumer_secret' => '',
					'oauth_token'           => '',
					'oauth_token_secret'    => '',
				],
				'twitter_user'             => '',
				'twitter_update'           => true,
				'twitter_update_media'     => true,
				'twitter_update_sensitive' => false,
				// @codingStandardsIgnoreLine WordPress.WP.I18n.UnorderedPlaceholdersText - Tokens are being mistaken for translation placeholders.
				'twitter_status'           => __( '%collection has updated! %site-url', 'webcomic' ),
				'twitter_status_media'     => 0,
				'twitter_card'             => false,
				'twitter_card_media'       => false,
			]
		);
	}
}

/**
 * Deactivate the Twitter component.
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
				remove_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_account' );
				remove_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_update' );
				remove_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_status' );
				remove_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_card' );

				unset(
					$options['twitter_oauth'],
					$options['twitter_user'],
					$options['twitter_update'],
					$options['twitter_update_media'],
					$options['twitter_update_sensitive'],
					$options['twitter_status'],
					$options['twitter_status_media'],
					$options['twitter_card'],
					$options['twitter_card_media']
				);

				return $options;
			}, 99
		);

		update_option(
			$collection, [
				'twitter_oauth'            => [
					'oauth_consumer_key'    => '',
					'oauth_consumer_secret' => '',
					'oauth_token'           => '',
					'oauth_token_secret'    => '',
				],
				'twitter_user'             => '',
				'twitter_update'           => true,
				'twitter_update_media'     => true,
				'twitter_update_sensitive' => false,
				// @codingStandardsIgnoreLine WordPress.WP.I18n.UnorderedPlaceholdersText - Tokens are being mistaken for translation placeholders.
				'twitter_status'           => __( '%collection has updated! %site-url', 'webcomic' ),
				'twitter_status_media'     => 0,
				'twitter_card'             => false,
				'twitter_card_media'       => false,
			]
		);
	}

	delete_metadata( 'post', 0, 'webcomic_twitter_update', null, true );
	delete_metadata( 'post', 0, 'webcomic_twitter_update_media', null, true );
	delete_metadata( 'post', 0, 'webcomic_twitter_update_sensitive', null, true );
	delete_metadata( 'post', 0, 'webcomic_twitter_status', null, true );
	delete_metadata( 'post', 0, 'webcomic_twitter_status_media', null, true );
}

/**
 * Add the Twitter allowed collection options.
 *
 * @param array $allowed The allowed options.
 * @return array
 */
function hook_add_allowed_options( array $allowed ) : array {
	return array_merge( $allowed, [ 'twitter_oauth', 'twitter_user', 'twitter_update', 'twitter_update_media', 'twitter_update_sensitive', 'twitter_status', 'twitter_status_media', 'twitter_card', 'twitter_card_media' ] );
}

/**
 * Clear the new collection's twitter status media setting.
 *
 * @param array $defaults The default settings of the new collection.
 * @return array
 */
function hook_new_collection_restrict( array $defaults ) : array {
	$defaults['twitter_status_media'] = 0;

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
			"{$collection}_twitter",
			'<span class="dashicons dashicons-twitter"></span> ' . esc_html__( 'Twitter', 'webcomic' ),
			function() {
				echo '<div></div>';
			},
			"{$collection}_settings"
		);
	}
}

/**
 * Add the account setting field.
 *
 * @return void
 */
function hook_add_field_account() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_oauth",
			__( 'Account', 'webcomic' ),
			function( $args ) {
				$args['account'] = util_get_account_details( $args['collection'], $args['option'] );

				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_twitter", [
				'file'               => __DIR__ . '/settings-inc-field-account.php',
				'option'             => webcomic( "option.{$collection}.twitter_oauth" ),
				'label_for'          => "{$collection}[twitter_oauth][oauth_consumer_key]",
				'label_secret'       => "{$collection}[twitter_oauth][oauth_consumer_secret]",
				'label_token'        => "{$collection}[twitter_oauth][oauth_token]",
				'label_token_secret' => "{$collection}[twitter_oauth][oauth_token_secret]",
				'collection'         => $collection,
			]
		);
	}
}

/**
 * Add the update setting field.
 *
 * @return void
 */
function hook_add_field_update() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_media",
			__( 'Update', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_twitter", [
				'file'             => __DIR__ . '/settings-inc-field-update.php',
				'option'           => webcomic( "option.{$collection}.twitter_update" ),
				'option_media'     => webcomic( "option.{$collection}.twitter_update_media" ),
				'option_sensitive' => webcomic( "option.{$collection}.twitter_update_sensitive" ),
				'label_for'        => "{$collection}[twitter_update]",
				'label_media'      => "{$collection}[twitter_update_media]",
				'label_sensitive'  => "{$collection}[twitter_update_sensitive]",
			]
		);
	}
}

/**
 * Add the status setting field.
 *
 * @return void
 */
function hook_add_field_status() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_status",
			__( 'Status', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_twitter", [
				'file'         => __DIR__ . '/settings-inc-field-status.php',
				'option'       => webcomic( "option.{$collection}.twitter_status" ),
				'option_media' => webcomic( "option.{$collection}.twitter_status_media" ),
				'label_for'    => "{$collection}[twitter_status]",
				'label_media'  => "{$collection}[twitter_status_media]",
			]
		);
	}
}

/**
 * Add the card setting field.
 *
 * @return void
 */
function hook_add_field_card() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_card",
			__( 'Cards', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_twitter", [
				'file'         => __DIR__ . '/settings-inc-field-card.php',
				'option'       => webcomic( "option.{$collection}.twitter_card" ),
				'option_media' => webcomic( "option.{$collection}.twitter_card_media" ),
				'label_for'    => "{$collection}[twitter_card]",
				'label_media'  => "{$collection}[twitter_card_media]",
				'twitter'      => '<a href="https://cards-dev.twitter.com/validator" target="_blank">' . esc_html__( 'Twitter Card Validator', 'webcomic' ) . '</a>',
			]
		);
	}
}

/**
 * Delete Twitter OAuth tokens.
 *
 * @return void
 */
function hook_delete_twitter_tokens() {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'DeauthorizeNonce' ) ), __NAMESPACE__ . 'DeauthorizeNonce' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$collection = sanitize_key( webcomic( 'GLOBALS._REQUEST.webcomic_twitter_delete_tokens' ) );

	if ( ! $collection || ! webcomic_collection_exists( $collection ) ) {
		return;
	}

	delete_transient( "{$collection}_twitter_oauth_token" );
	delete_transient( "{$collection}_twitter_oauth_token_secret" );

	$options                                        = webcomic( "option.{$collection}" );
	$options['twitter_oauth']['oauth_token']        = '';
	$options['twitter_oauth']['oauth_token_secret'] = '';

	update_option( $collection, $options );

	$redirect_url = add_query_arg(
		[
			'post_type' => $collection,
			'page'      => "{$collection}_options",
		], admin_url( 'edit.php' )
	);

	webcomic_notice( '<strong>' . __( 'Twitter authorization tokens removed.', 'webcomic' ) . '</strong>' );

	define( 'DOING_AJAX', true ) && wp_safe_redirect( $redirect_url ) && wp_die();
}

/**
 * Register settings stylesheets.
 *
 * @return void
 */
function hook_register_settings_styles() {
	wp_register_style(
		__NAMESPACE__ . 'SettingsCSS',
		plugins_url( 'srv/twitter/settings.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);
}

/**
 * Register settings javascript.
 *
 * @return void
 */
function hook_register_settings_scripts() {
	wp_register_script(
		__NAMESPACE__ . 'SettingsJS',
		plugins_url( 'srv/twitter/settings.js', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' ),
		true
	);
}

/**
 * Enqueue settings stylesheets.
 *
 * @return void
 */
function hook_enqueue_settings_styles() {
	if ( ! preg_match( '/^webcomic\d+_page_webcomic\d+_options$/', get_current_screen()->id ) && ! webcomic_collection_exists( get_current_screen()->id ) ) {
		return;
	}

	wp_enqueue_style( __NAMESPACE__ . 'SettingsCSS' );
}

/**
 * Enqueue settings javascript.
 *
 * @return void
 */
function hook_enqueue_settings_scripts() {
	if ( ! preg_match( '/^webcomic\d+_page_webcomic\d+_options$/', get_current_screen()->id ) ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'SettingsJS' );
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
			'id'       => 'twitter',
			'title'    => __( 'Twitter', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/settings-inc-help.php';
			},
		]
	);
}

/**
 * Delete the twitter_status_media setting when a post is deleted.
 *
 * @param mixed $id The post ID.
 * @return void
 */
function hook_delete_twitter_media( $id ) {
	$contexts = array_unique( get_post_meta( $id, 'webcomic_twitter_collection' ) );

	foreach ( $contexts as $collection ) {
		if ( ! webcomic_collection_exists( $collection ) ) {
			continue;
		}

		update_option(
			$collection, [
				'twitter_status_media' => 0,
			]
		);
	}
}

/**
 * Add media state for Twitter media.
 *
 * @param array $states The media states for the current object.
 * @return array
 */
function hook_add_twitter_media_state( array $states ) : array {
	$contexts = array_unique( get_post_meta( get_the_ID(), 'webcomic_twitter_collection' ) );

	if ( ! $contexts ) {
		return $states;
	}

	foreach ( $contexts as $collection ) {
		// Translators: Post type name.
		$states[] = sprintf( __( '%s Twitter Status Image', 'webcomic' ), webcomic( "option.{$collection}.name" ) );
	}

	return $states;
}

/* ===== Collection Hooks =================================================== */

/**
 * Sanitize the account field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_account( array $options ) : array {
	$options['twitter_oauth'] = array_map(
		'sanitize_text_field', array_filter(
			$options['twitter_oauth'], function( $key ) {
				return in_array( $key, [ 'oauth_consumer_key', 'oauth_consumer_secret', 'oauth_token', 'oauth_token_secret' ], true );
			}, ARRAY_FILTER_USE_KEY
		)
	);

	return $options;
}

/**
 * Sanitize the update field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_update( array $options ) : array {
	$options['twitter_update']           = (bool) $options['twitter_update'];
	$options['twitter_update_media']     = (bool) $options['twitter_update_media'];
	$options['twitter_update_sensitive'] = (bool) $options['twitter_update_sensitive'];

	return $options;
}

/**
 * Sanitize the status field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_status( array $options ) : array {
	$options['twitter_status']       = sanitize_text_field( $options['twitter_status'] );
	$options['twitter_status_media'] = abs( (int) $options['twitter_status_media'] );
	$old_media                       = webcomic( "option.{$options['id']}.twitter_status_media" );

	if ( $options['twitter_status_media'] !== $old_media ) {
		if ( $options['twitter_status_media'] ) {
			add_post_meta( $options['twitter_status_media'], 'webcomic_twitter_collection', $options['id'] );
		}

		if ( $old_media ) {
			delete_post_meta( $old_media, 'webcomic_twitter_collection', $options['id'] );
		}
	}

	return $options;
}

/**
 * Sanitize the card field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_card( array $options ) : array {
	$options['twitter_card']       = (bool) $options['twitter_card'];
	$options['twitter_card_media'] = (bool) $options['twitter_card_media'];

	return $options;
}

/**
 * Handle consumer token update requests.
 *
 * @return void
 */
function hook_get_account_details() {
	if ( ! webcomic( 'GLOBALS._REQUEST.collection' ) ) {
		wp_die();
	}

	$collection = webcomic( 'GLOBALS._REQUEST.collection' );

	if ( ! in_array( $collection, webcomic( 'option.collections' ), true ) ) {
		wp_die();
	}

	$oauth = [
		'oauth_consumer_key'    => (string) webcomic( 'GLOBALS._REQUEST.consumer_key' ),
		'oauth_consumer_secret' => (string) webcomic( 'GLOBALS._REQUEST.consumer_secret' ),
	] + webcomic( "option.{$collection}.twitter_oauth" );

	update_option(
		$collection, [
			'twitter_oauth' => $oauth,
		]
	);

	echo util_get_account_details( $collection, $oauth ); // WPCS: xss ok.

	wp_die();
}

/* ===== Utility Functions ================================================== */

/**
 * Get Twitter account details.
 *
 * @param string $collection The collection to get account details for.
 * @param array  $oauth The OAuth arguments to use for API requests.
 * @return string
 * @internal For hook_add_field_account(), hook_get_account_details().
 */
function util_get_account_details( string $collection, array $oauth ) : string {
	// Translators: Twitter Application.
	$output      = '<p>' . sprintf( esc_html__( 'Enter your %s consumer key and consumer secret below.', 'webcomic' ), '<a href="https://apps.twitter.com/app/new" target="_blank">' . esc_html__( 'Twitter Application', 'webcomic' ) . '</a>' ) . '</p>';
	$oauth_token = '';
	$delete_url  = add_query_arg(
		[
			'webcomic_twitter_delete_tokens' => $collection,
		], admin_url( 'edit.php' )
	);
	$delete_link = '<a href="' . wp_nonce_url( esc_url( $delete_url ), __NAMESPACE__ . 'DeauthorizeNonce', __NAMESPACE__ . 'DeauthorizeNonce' ) . '" class="button button-secondary"><span class="dashicons dashicons-no"></span> ' . esc_html__( 'Deauthorize', 'webcomic' ) . '</a>';
	$response    = [
		'errors' => [],
	];

	if ( $oauth['oauth_consumer_key'] && $oauth['oauth_consumer_secret'] && $oauth['oauth_token'] && $oauth['oauth_token_secret'] ) {
		$response = util_api_request( 'GET', 'account/verify_credentials.json', $oauth );

		if ( isset( $response['body']['screen_name'] ) ) {
			if ( webcomic( "option.{$collection}.twitter_user" ) !== $response['body']['screen_name'] ) {
				update_option(
					$collection, [
						'twitter_user' => sanitize_text_field( $response['body']['screen_name'] ),
					]
				);
			}

			$output  = '<a href="https://twitter.com/' . esc_attr( $response['body']['screen_name'] ) . '" target="_blank" class="webcomic-twitter-account">';
			$output .= '<img src="' . esc_attr( str_replace( '_normal', '_200x200', $response['body']['profile_image_url'] ) ) . '">';
			$output .= '<b>' . esc_html( $response['body']['name'] ) . '</b><br>';
			$output .= '@' . esc_html( $response['body']['screen_name'] );
			$output .= '</a>';
			$output .= "<p>{$delete_link}</p>";
			$output .= '<p class="description">' . esc_html__( 'The account status updates will be sent to.', 'webcomic' ) . '</p>';
		}
	} elseif ( $oauth['oauth_consumer_key'] && $oauth['oauth_consumer_secret'] ) {
		$output   = '<a href="https://api.twitter.com/oauth/authorize?oauth_token=%webcomic-token" class="button button-primary"><span class="dashicons dashicons-twitter"></span> ' . esc_html__( 'Sign in with Twitter', 'webcomic' ) . '</a>';
		$callback = esc_url( util_oauth_get_callback( $collection ) );
		$response = util_api_request(
			'POST', 'oauth/request_token', [
				'oauth_callback' => $callback,
			] + $oauth
		);
	}

	if ( $response['errors'] ) {
		$output = '<p class="webcomic-twitter-error">' . implode( '<br>', array_map( 'esc_html', $response['errors'] ) ) . '</p>';

		if ( $oauth['oauth_token'] && $oauth['oauth_token_secret'] ) {
			$output .= $delete_link;
		}
	} elseif ( isset( $response['body']['oauth_token'], $response['body']['oauth_token_secret'] ) ) {
		$oauth_token = $response['body']['oauth_token'];

		set_transient( "{$collection}_twitter_oauth_token", $response['body']['oauth_token'] );
		set_transient( "{$collection}_twitter_oauth_token_secret", $response['body']['oauth_token_secret'] );
	}

	return str_replace( '%webcomic-token', $oauth_token, $output );
}

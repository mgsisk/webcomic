<?php
/**
 * Status metabox functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Twitter\MetaBox;

use function Mgsisk\Webcomic\Twitter\util_api_request;

/**
 * Add status metabox hooks.
 *
 * @return void
 */
function status() {
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_status_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_status_scripts' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_box_status_help' );
	add_filter( 'add_meta_boxes', __NAMESPACE__ . '\hook_add_box_status' );
	add_filter( 'bulk_edit_custom_box', __NAMESPACE__ . '\hook_add_quick_edit_status', 10, 2 );
	add_filter( 'quick_edit_custom_box', __NAMESPACE__ . '\hook_add_quick_edit_status', 10, 2 );
	add_filter( 'get_post_metadata', __NAMESPACE__ . '\hook_get_post_meta_default_update', 10, 4 );
	add_filter( 'get_post_metadata', __NAMESPACE__ . '\hook_get_post_meta_default_update_media', 10, 4 );
	add_filter( 'get_post_metadata', __NAMESPACE__ . '\hook_get_post_meta_default_update_sensitive', 10, 4 );
	add_filter( 'get_post_metadata', __NAMESPACE__ . '\hook_get_post_meta_default_status', 10, 4 );
	add_filter( 'get_post_metadata', __NAMESPACE__ . '\hook_get_post_meta_default_status_media', 10, 4 );
	add_filter( 'update_post_metadata', __NAMESPACE__ . '\hook_update_status_media_context', 10, 5 );
	add_filter( 'delete_post_meta', __NAMESPACE__ . '\hook_delete_status_media_context', 10, 4 );
	add_filter( 'delete_attachment', __NAMESPACE__ . '\hook_delete_alernative_status_media' );
	add_filter( 'display_media_states', __NAMESPACE__ . '\hook_add_status_media_states' );
	add_filter( 'wp_ajax_webcomic_twitter_status_quick_edit', __NAMESPACE__ . '\hook_quick_edit_status' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "save_post_{$collection}", __NAMESPACE__ . '\hook_update_post_meta_update' );
		add_filter( "save_post_{$collection}", __NAMESPACE__ . '\hook_update_post_meta_update_media' );
		add_filter( "save_post_{$collection}", __NAMESPACE__ . '\hook_update_post_meta_update_sensitive' );
		add_filter( "save_post_{$collection}", __NAMESPACE__ . '\hook_update_post_meta_status' );
		add_filter( "save_post_{$collection}", __NAMESPACE__ . '\hook_update_post_meta_status_media' );
	}
}

/**
 * Register javascript.
 *
 * @return void
 */
function hook_register_status_scripts() {
	wp_register_script(
		__NAMESPACE__ . 'StatusQuickEditJS',
		plugins_url( 'srv/twitter/quick-edit-status.js', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' ),
		true
	);
}

/**
 * Enqueue javascript.
 *
 * @return void
 */
function hook_enqueue_status_scripts() {
	if ( ! preg_match( '/^edit-webcomic\d+$/', get_current_screen()->id ) ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'StatusQuickEditJS' );
}

/**
 * Add the help tab.
 *
 * @return void
 */
function hook_add_box_status_help() {
	$screen = get_current_screen();

	if ( ! webcomic_collection_exists( $screen->id ) ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'webcomic_twitter_status',
			'title'    => __( 'Webcomic Twitter Status', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/status-inc-help.php';
			},
		]
	);
}

/**
 * Display the status meta box.
 *
 * @param string $type The current post type.
 * @return void
 */
function hook_add_box_status( string $type ) {
	if ( ! webcomic_collection_exists( $type ) ) {
		return;
	}

	add_meta_box(
		str_replace( '\\', '_', __NAMESPACE__ ) . 'Status',
		__( 'Webcomic Twitter Status', 'webcomic' ),
		/**
		 * Meta box closure.
		 *
		 * @suppress PhanAccessMethodInternal - util_api_request() is allowed within Mgsisk\Webcomic\Twitter.
		 */
		function( $post ) {
			$args  = [
				'file'                    => __DIR__ . '/status-inc-box.php',
				'nonce'                   => __NAMESPACE__ . 'StatusNonce',
				'account'                 => '<p class="webcomic-twitter-alert">' . esc_html__( 'No account has been authorized for this collection.', 'webcomic' ) . '</p>',
				'option_update'           => (bool) get_post_meta( $post->ID, 'webcomic_twitter_update', true ),
				'option_update_media'     => (bool) get_post_meta( $post->ID, 'webcomic_twitter_update_media', true ),
				'option_update_sensitive' => (bool) get_post_meta( $post->ID, 'webcomic_twitter_update_sensitive', true ),
				'option_status'           => get_post_meta( $post->ID, 'webcomic_twitter_status', true ),
				'option_status_media'     => implode( ',', get_post_meta( $post->ID, 'webcomic_twitter_status_media' ) ),
			];
			$oauth = array_filter( webcomic( "option.{$post->post_type}.twitter_oauth" ) );

			if ( 4 === count( $oauth ) ) {
				$response = util_api_request( 'GET', 'account/verify_credentials.json', $oauth );

				if ( $response['errors'] ) {
					$args['account'] = '<p class="webcomic-twitter-error">' . implode( '<br>', array_map( 'esc_html', $response['errors'] ) ) . '</p>';
				} elseif ( isset( $response['body']['screen_name'] ) ) {
					$args['account'] = '<a href="https://twitter.com/' . esc_attr( $response['body']['screen_name'] ) . '" target="_blank" class="webcomic-twitter-account"><img src="' . esc_attr( str_replace( '_normal', '_200x200', $response['body']['profile_image_url'] ) ) . '"><b>' . esc_html( $response['body']['name'] ) . '</b><br>@' . esc_html( $response['body']['screen_name'] ) . '</a>';
				}
			}

			require $args['file'];
		},
		$type,
		'side'
	);
}

/**
 * Add the status quick edit field.
 *
 * @param string $column The current column.
 * @param string $type The current post type.
 * @return void
 */
function hook_add_quick_edit_status( string $column, string $type ) {
	if ( 'webcomic_media' !== $column || ! webcomic_collection_exists( $type ) ) {
		return;
	}

	$args = [
		'file'  => __DIR__ . '/status-inc-quick-edit.php',
		'bulk'  => false !== strpos( current_filter(), 'bulk' ),
		'nonce' => __NAMESPACE__ . 'StatusNonce',
	];

	require $args['file'];
}

/**
 * Get the default update for new posts.
 *
 * @param mixed  $meta The meta value.
 * @param int    $id The post ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return only the first $key value.
 * @return mixed
 */
function hook_get_post_meta_default_update( $meta, $id, $key, $single ) {
	if ( 'webcomic_twitter_update' !== $key || 'auto-draft' !== get_post_status( $id ) ) {
		return $meta;
	}

	$collection = get_post_type( $id );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return $meta;
	}

	return [ webcomic( "option.{$collection}.twitter_update" ) ];
}

/**
 * Get the default update media for new posts.
 *
 * @param mixed  $meta The meta value.
 * @param int    $id The post ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return only the first $key value.
 * @return mixed
 */
function hook_get_post_meta_default_update_media( $meta, $id, $key, $single ) {
	if ( 'webcomic_twitter_update_media' !== $key || 'auto-draft' !== get_post_status( $id ) ) {
		return $meta;
	}

	$collection = get_post_type( $id );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return $meta;
	}

	return [ webcomic( "option.{$collection}.twitter_update_media" ) ];
}

/**
 * Get the default update sensitivity for new posts.
 *
 * @param mixed  $meta The meta value.
 * @param int    $id The post ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return only the first $key value.
 * @return mixed
 */
function hook_get_post_meta_default_update_sensitive( $meta, $id, $key, $single ) {
	if ( 'webcomic_twitter_update_sensitive' !== $key || 'auto-draft' !== get_post_status( $id ) ) {
		return $meta;
	}

	$collection = get_post_type( $id );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return $meta;
	}

	return [ webcomic( "option.{$collection}.twitter_update_sensitive" ) ];
}

/**
 * Get the default status for new posts.
 *
 * @param mixed  $meta The meta value.
 * @param int    $id The post ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return only the first $key value.
 * @return mixed
 */
function hook_get_post_meta_default_status( $meta, $id, $key, $single ) {
	if ( 'webcomic_twitter_status' !== $key || 'auto-draft' !== get_post_status( $id ) ) {
		return $meta;
	}

	$collection = get_post_type( $id );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return $meta;
	}

	return [ webcomic( "option.{$collection}.twitter_status" ) ];
}

/**
 * Get the default status media for new posts.
 *
 * @param mixed  $meta The meta value.
 * @param int    $id The post ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return only the first $key value.
 * @return mixed
 */
function hook_get_post_meta_default_status_media( $meta, $id, $key, $single ) {
	if ( 'webcomic_twitter_status_media' !== $key || 'auto-draft' !== get_post_status( $id ) ) {
		return $meta;
	}

	$collection = get_post_type( $id );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return $meta;
	}

	return [ webcomic( "option.{$collection}.twitter_status_media" ) ];
}

/**
 * Update media post meta when adding webcomic_restrict_age_media meta data.
 *
 * @param mixed  $check Whether to short-circuit the update process.
 * @param int    $post The post being updated.
 * @param string $key The meta data key being added.
 * @param mixed  $value The meta data value being added.
 * @param mixed  $previous The previous meta data value.
 * @return void
 */
function hook_update_status_media_context( $check, int $post, string $key, $value, $previous ) {
	if ( 'webcomic_twitter_status_media' !== $key || ! is_a_webcomic( $post ) ) {
		return;
	}

	delete_post_meta( $previous, 'webcomic_twitter_status_post', $post );
	add_post_meta( $value, 'webcomic_twitter_status_post', $post );
}

/**
 * Delete media contexts when deleting Twitter media meta data.
 *
 * @param array  $meta The meta entries being deleted.
 * @param int    $post The post being updated.
 * @param string $key The meta data key being delete.
 * @param mixed  $value The meta data value being deleted.
 * @return void
 */
function hook_delete_status_media_context( array $meta, int $post, string $key, $value ) {
	if ( 'webcomic_twitter_status_media' !== $key || ! is_a_webcomic( $post ) ) {
		return;
	}

	delete_post_meta( $value, 'webcomic_twitter_status_post', $post );
}

/**
 * Delete alternative Twitter status media when a media item is deleted.
 *
 * @param int $post The post being deleted.
 * @return void
 */
function hook_delete_alernative_status_media( int $post ) {
	foreach ( get_post_meta( $post, 'webcomic_twitter_status_post' ) as $comic ) {
		delete_post_meta( $comic, 'webcomic_twitter_status_media', $post );
	}
}

/**
 * Add media states for Twitter status alternative media.
 *
 * @param array $states The media states for the current object.
 * @return array
 */
function hook_add_status_media_states( array $states ) : array {
	$comics = get_post_meta( get_the_ID(), 'webcomic_twitter_status_post' );

	foreach ( $comics as $comic ) {
		$collection = get_post_type( $comic );

		if ( isset( $states[ "{$collection}-twitter" ] ) ) {
			continue;
		} elseif ( ! webcomic_collection_exists( $collection ) ) {
			delete_post_meta( get_the_ID(), 'webcomic_twitter_status_post', $comic );

			continue;
		}

		// Translators: Post type name.
		$states[ "{$collection}-twitter" ] = sprintf( __( '%s Twitter Status Comic Image', 'webcomic' ), webcomic( "option.{$collection}.name" ) );
	}

	return $states;
}

/**
 * Handle status quick edit requests.
 *
 * @return void
 */
function hook_quick_edit_status() {
	if ( ! webcomic( 'GLOBALS._REQUEST.post' ) ) {
		wp_die();
	}

	$post   = abs( (int) webcomic( 'GLOBALS._REQUEST.post' ) );
	$output = [
		(bool) get_post_meta( $post, 'webcomic_twitter_update', true ),
		(bool) get_post_meta( $post, 'webcomic_twitter_update_media', true ),
	];

	wp_send_json( $output );
}

/* ===== Collection Hooks =================================================== */

/**
 * Update webcomic_twitter_update meta data.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_post_meta_update( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'StatusNonce' ) ), __NAMESPACE__ . 'StatusNonce' ) ) {
		return;
	} elseif ( ! webcomic( 'GLOBALS._REQUEST.webcomic_twitter_update' ) ) {
		delete_post_meta( $id, 'webcomic_twitter_update' );

		return;
	}

	$update = abs( (int) webcomic( 'GLOBALS._REQUEST.webcomic_twitter_update' ) );

	if ( ! $update ) {
		return;
	}

	update_post_meta( $id, 'webcomic_twitter_update', true );
}

/**
 * Update webcomic_twitter_update_media meta data.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_post_meta_update_media( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'StatusNonce' ) ), __NAMESPACE__ . 'StatusNonce' ) ) {
		return;
	} elseif ( ! webcomic( 'GLOBALS._REQUEST.webcomic_twitter_update_media' ) ) {
		delete_post_meta( $id, 'webcomic_twitter_update_media' );

		return;
	}

	$update_media = abs( (int) webcomic( 'GLOBALS._REQUEST.webcomic_twitter_update_media' ) );

	if ( ! $update_media ) {
		return;
	}

	update_post_meta( $id, 'webcomic_twitter_update_media', true );
}

/**
 * Update webcomic_twitter_update meta data.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_post_meta_update_sensitive( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'StatusNonce' ) ), __NAMESPACE__ . 'StatusNonce' ) ) {
		return;
	} elseif ( ! webcomic( 'GLOBALS._REQUEST.webcomic_twitter_update_sensitive' ) ) {
		delete_post_meta( $id, 'webcomic_twitter_update_sensitive' );

		return;
	}

	$update = abs( (int) webcomic( 'GLOBALS._REQUEST.webcomic_twitter_update_sensitive' ) );

	if ( ! $update ) {
		return;
	}

	update_post_meta( $id, 'webcomic_twitter_update_sensitive', true );
}

/**
 * Update webcomic_twitter_status meta data.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_post_meta_status( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'StatusNonce' ) ), __NAMESPACE__ . 'StatusNonce' ) || webcomic( 'GLOBALS._REQUEST.webcomic_twitter_status_quick_edit' ) ) {
		return;
	}

	$status = sanitize_text_field( webcomic( 'GLOBALS._REQUEST.webcomic_twitter_status' ) );

	if ( ! $status ) {
		delete_post_meta( $id, 'webcomic_twitter_status' );

		return;
	}

	update_post_meta( $id, 'webcomic_twitter_status', $status );
}

/**
 * Update webcomic_twitter_status_media meta data.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_post_meta_status_media( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'StatusNonce' ) ), __NAMESPACE__ . 'StatusNonce' ) || webcomic( 'GLOBALS._REQUEST.webcomic_twitter_status_quick_edit' ) ) {
		return;
	}

	$media = abs( (int) webcomic( 'GLOBALS._REQUEST.webcomic_twitter_status_media' ) );

	if ( ! $media ) {
		delete_post_meta( $id, 'webcomic_twitter_status_media' );

		return;
	}

	update_post_meta( $id, 'webcomic_twitter_status_media', $media );
}

<?php
/**
 * Password metabox functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Restrict\MetaBox;

/**
 * Add password metabox hooks.
 *
 * @return void
 */
function password() {
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_box_password_help' );
	add_filter( 'add_meta_boxes', __NAMESPACE__ . '\hook_add_box_password' );
	add_filter( 'get_post_metadata', __NAMESPACE__ . '\hook_get_post_meta_default_password', 10, 4 );
	add_filter( 'update_post_metadata', __NAMESPACE__ . '\hook_update_password_media_context', 10, 5 );
	add_filter( 'delete_post_meta', __NAMESPACE__ . '\hook_delete_password_media_context', 10, 4 );
	add_filter( 'delete_attachment', __NAMESPACE__ . '\hook_delete_alernative_password_media' );
	add_filter( 'display_media_states', __NAMESPACE__ . '\hook_add_password_media_states' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "save_post_{$collection}", __NAMESPACE__ . '\hook_update_post_meta_password_media' );
	}
}

/**
 * Add the help tab.
 *
 * @return void
 */
function hook_add_box_password_help() {
	$screen = get_current_screen();

	if ( ! webcomic_collection_exists( $screen->id ) ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'webcomic_password',
			'title'    => __( 'Webcomic Password Restrictions', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/password-inc-help.php';
			},
		]
	);
}

/**
 * Display the password meta box.
 *
 * @param string $type The current post type.
 * @return void
 */
function hook_add_box_password( string $type ) {
	if ( ! webcomic_collection_exists( $type ) ) {
		return;
	}

	add_meta_box(
		str_replace( '\\', '_', __NAMESPACE__ ) . 'Password',
		__( 'Webcomic Password Restrictions', 'webcomic' ),
		function( $post ) {
			$args = [
				'file'   => __DIR__ . '/password-inc-box.php',
				'nonce'  => __NAMESPACE__ . 'PasswordNonce',
				'option' => implode( ',', get_post_meta( $post->ID, 'webcomic_restrict_password_media' ) ),
			];

			require $args['file'];
		},
		$type,
		'side'
	);
}

/**
 * Get the default password media for new posts.
 *
 * @param mixed  $meta The meta value.
 * @param int    $id The post ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return only the first $key value.
 * @return mixed
 */
function hook_get_post_meta_default_password( $meta, $id, $key, $single ) {
	if ( 'webcomic_restrict_password_media' !== $key || 'auto-draft' !== get_post_status( $id ) ) {
		return $meta;
	}

	$collection = get_post_type( $id );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return $meta;
	}

	return [ webcomic( "option.{$collection}.restrict_password_media" ) ];
}

/**
 * Update media post meta when adding webcomic_restrict_password_media meta
 * data.
 *
 * @param mixed  $check Whether to short-circuit the update process.
 * @param int    $post The post being updated.
 * @param string $key The meta data key being added.
 * @param mixed  $value The meta data value being added.
 * @param mixed  $previous The previous meta data value.
 * @return void
 */
function hook_update_password_media_context( $check, int $post, string $key, $value, $previous ) {
	if ( 'webcomic_restrict_password_media' !== $key || ! is_a_webcomic( $post ) ) {
		return;
	}

	delete_post_meta( $previous, 'webcomic_restrict_password_post', $post );
	add_post_meta( $value, 'webcomic_restrict_password_post', $post );
}

/**
 * Delete media contexts when deleting password media meta data.
 *
 * @param array  $meta The meta entries being deleted.
 * @param int    $post The post being updated.
 * @param string $key The meta data key being delete.
 * @param mixed  $value The meta data value being deleted.
 * @return void
 */
function hook_delete_password_media_context( array $meta, int $post, string $key, $value ) {
	if ( 'webcomic_restrict_password_media' !== $key || ! is_a_webcomic( $post ) ) {
		return;
	}

	delete_post_meta( $value, 'webcomic_restrict_password_post', $post );
}

/**
 * Delete alternative comic media when a media item is deleted.
 *
 * @param int $post The post being deleted.
 * @return void
 */
function hook_delete_alernative_password_media( int $post ) {
	foreach ( get_post_meta( $post, 'webcomic_restrict_password_post' ) as $comic ) {
		delete_post_meta( $comic, 'webcomic_restrict_password_media', $post );
	}
}

/**
 * Add media states for password alternative comic media.
 *
 * @param array $states The media states for the current object.
 * @return array
 */
function hook_add_password_media_states( array $states ) : array {
	$comics = get_post_meta( get_the_ID(), 'webcomic_restrict_password_post' );

	foreach ( $comics as $comic ) {
		$collection = get_post_type( $comic );

		if ( isset( $states[ "{$collection}-password" ] ) ) {
			continue;
		} elseif ( ! webcomic_collection_exists( $collection ) ) {
			delete_post_meta( get_the_ID(), 'webcomic_restrict_password_post', $comic );

			continue;
		}

		// Translators: Post type name.
		$states[ "{$collection}-password" ] = sprintf( __( '%s Password-Restricted Comic Image', 'webcomic' ), webcomic( "option.{$collection}.name" ) );
	}

	return $states;
}

/* ===== Collection Hooks =================================================== */

/**
 * Update webcomic_restrict_password_media meta data.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_post_meta_password_media( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'PasswordNonce' ) ), __NAMESPACE__ . 'PasswordNonce' ) ) {
		return;
	}

	$media = abs( (int) webcomic( 'GLOBALS._REQUEST.webcomic_restrict_password_media' ) );

	if ( ! $media ) {
		delete_post_meta( $id, 'webcomic_restrict_password_media' );

		return;
	}

	update_post_meta( $id, 'webcomic_restrict_password_media', $media );
}

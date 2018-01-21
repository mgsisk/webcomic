<?php
/**
 * Age metabox functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Restrict\MetaBox;

use WP_Post;

/**
 * Add age metabox hooks.
 *
 * @return void
 */
function age() {
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_age_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_age_scripts' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_box_age_help' );
	add_filter( 'add_meta_boxes', __NAMESPACE__ . '\hook_add_box_age' );
	add_filter( 'bulk_edit_custom_box', __NAMESPACE__ . '\hook_add_quick_edit_age', 10, 2 );
	add_filter( 'quick_edit_custom_box', __NAMESPACE__ . '\hook_add_quick_edit_age', 10, 2 );
	add_filter( 'get_post_metadata', __NAMESPACE__ . '\hook_get_post_meta_default_age', 10, 4 );
	add_filter( 'get_post_metadata', __NAMESPACE__ . '\hook_get_post_meta_default_age_media', 10, 4 );
	add_filter( 'update_post_metadata', __NAMESPACE__ . '\hook_update_age_media_context', 10, 5 );
	add_filter( 'delete_post_meta', __NAMESPACE__ . '\hook_delete_age_media_context', 10, 4 );
	add_filter( 'delete_attachment', __NAMESPACE__ . '\hook_delete_alernative_age_media' );
	add_filter( 'display_media_states', __NAMESPACE__ . '\hook_add_age_media_states' );
	add_filter( 'display_post_states', __NAMESPACE__ . '\hook_comic_age_state', 10, 2 );
	add_filter( 'wp_ajax_webcomic_restrict_age_quick_edit', __NAMESPACE__ . '\hook_quick_edit_age' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "save_post_{$collection}", __NAMESPACE__ . '\hook_update_post_meta_age' );
		add_filter( "save_post_{$collection}", __NAMESPACE__ . '\hook_update_post_meta_age_media' );
	}
}

/**
 * Register javascript.
 *
 * @return void
 */
function hook_register_age_scripts() {
	wp_register_script(
		__NAMESPACE__ . 'AgeQuickEditJS',
		plugins_url( 'srv/restrict/quick-edit-age.js', webcomic( 'file' ) ),
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
function hook_enqueue_age_scripts() {
	if ( ! preg_match( '/^edit-webcomic\d+$/', get_current_screen()->id ) ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'AgeQuickEditJS' );
}

/**
 * Add the help tab.
 *
 * @return void
 */
function hook_add_box_age_help() {
	$screen = get_current_screen();

	if ( ! webcomic_collection_exists( $screen->id ) ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'webcomic_restrict_age',
			'title'    => __( 'Webcomic Age Restrictions', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/age-inc-help.php';
			},
		]
	);
}

/**
 * Display the age meta box.
 *
 * @param string $type The current post type.
 * @return void
 */
function hook_add_box_age( string $type ) {
	if ( ! webcomic_collection_exists( $type ) ) {
		return;
	}

	add_meta_box(
		str_replace( '\\', '_', __NAMESPACE__ ) . 'Age',
		__( 'Webcomic Age Restrictions', 'webcomic' ),
		function( $post ) {
			$args = [
				'file'         => __DIR__ . '/age-inc-box.php',
				'nonce'        => __NAMESPACE__ . 'AgeNonce',
				'option_age'   => (int) get_post_meta( $post->ID, 'webcomic_restrict_age', true ),
				'option_media' => implode( ',', get_post_meta( $post->ID, 'webcomic_restrict_age_media' ) ),
			];

			require $args['file'];
		},
		$type,
		'side'
	);
}

/**
 * Add the age quick edit field.
 *
 * @param string $column The current column.
 * @param string $type The current post type.
 * @return void
 */
function hook_add_quick_edit_age( string $column, string $type ) {
	if ( 'webcomic_media' !== $column || ! webcomic_collection_exists( $type ) ) {
		return;
	}

	$args = [
		'file'  => __DIR__ . '/age-inc-quick-edit.php',
		'bulk'  => false !== strpos( current_filter(), 'bulk' ),
		'nonce' => __NAMESPACE__ . 'AgeNonce',
	];

	require $args['file'];
}

/**
 * Get the default age for new posts.
 *
 * @param mixed  $meta The meta value.
 * @param int    $id The post ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return only the first $key value.
 * @return mixed
 */
function hook_get_post_meta_default_age( $meta, $id, $key, $single ) {
	if ( 'webcomic_restrict_age' !== $key || 'auto-draft' !== get_post_status( $id ) ) {
		return $meta;
	}

	$collection = get_post_type( $id );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return $meta;
	}

	return [ webcomic( "option.{$collection}.restrict_age" ) ];
}

/**
 * Get the default age media for new posts.
 *
 * @param mixed  $meta The meta value.
 * @param int    $id The post ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return only the first $key value.
 * @return mixed
 */
function hook_get_post_meta_default_age_media( $meta, $id, $key, $single ) {
	if ( 'webcomic_restrict_age_media' !== $key || 'auto-draft' !== get_post_status( $id ) ) {
		return $meta;
	}

	$collection = get_post_type( $id );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return $meta;
	}

	return [ webcomic( "option.{$collection}.restrict_age_media" ) ];
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
function hook_update_age_media_context( $check, int $post, string $key, $value, $previous ) {
	if ( 'webcomic_restrict_age_media' !== $key || ! is_a_webcomic( $post ) ) {
		return;
	}

	delete_post_meta( $previous, 'webcomic_restrict_age_post', $post );
	add_post_meta( $value, 'webcomic_restrict_age_post', $post );
}

/**
 * Delete media contexts when deleting age media meta data.
 *
 * @param array  $meta The meta entries being deleted.
 * @param int    $post The post being updated.
 * @param string $key The meta data key being delete.
 * @param mixed  $value The meta data value being deleted.
 * @return void
 */
function hook_delete_age_media_context( array $meta, int $post, string $key, $value ) {
	if ( 'webcomic_restrict_age_media' !== $key || ! is_a_webcomic( $post ) ) {
		return;
	}

	delete_post_meta( $value, 'webcomic_restrict_age_post', $post );
}

/**
 * Delete alternative comic media when a media item is deleted.
 *
 * @param int $post The post being deleted.
 * @return void
 */
function hook_delete_alernative_age_media( int $post ) {
	foreach ( get_post_meta( $post, 'webcomic_restrict_age_post' ) as $comic ) {
		delete_post_meta( $comic, 'webcomic_restrict_age_media', $post );
	}
}

/**
 * Add media states for age alternative comic media.
 *
 * @param array $states The media states for the current object.
 * @return array
 */
function hook_add_age_media_states( array $states ) : array {
	$comics = get_post_meta( get_the_ID(), 'webcomic_restrict_age_post' );

	foreach ( $comics as $comic ) {
		$collection = get_post_type( $comic );

		if ( isset( $states[ "{$collection}-age" ] ) ) {
			continue;
		} elseif ( ! webcomic_collection_exists( $collection ) ) {
			delete_post_meta( get_the_ID(), 'webcomic_restrict_age_post', $comic );

			continue;
		}

		// Translators: Post type name.
		$states[ "{$collection}-age" ] = sprintf( __( '%s Age-Restricted Comic Image', 'webcomic' ), webcomic( "option.{$collection}.name" ) );
	}

	return $states;
}

/**
 * Display restricted post states.
 *
 * @param array   $states The current post states.
 * @param WP_Post $post The current post.
 * @return array
 */
function hook_comic_age_state( array $states, WP_Post $post ) : array {
	if ( ! webcomic_collection_exists( $post->post_type ) ) {
		return $states;
	} elseif ( get_post_meta( $post->ID, 'webcomic_restrict_age', true ) ) {
		$states[] = __( 'Age restricted', 'webcomic' );
	}

	return $states;
}

/**
 * Handle age quick edit requests.
 *
 * @return void
 */
function hook_quick_edit_age() {
	if ( ! webcomic( 'GLOBALS._REQUEST.post' ) ) {
		wp_die();
	}

	$output = [
		(int) get_post_meta( abs( (int) webcomic( 'GLOBALS._REQUEST.post' ) ), 'webcomic_restrict_age', true ),
	];

	wp_send_json( $output );
}

/* ===== Collection Hooks =================================================== */

/**
 * Update webcomic_restrict_age meta data.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_post_meta_age( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'AgeNonce' ) ), __NAMESPACE__ . 'AgeNonce' ) ) {
		return;
	} elseif ( ! webcomic( 'GLOBALS._REQUEST.webcomic_restrict_age' ) ) {
		delete_post_meta( $id, 'webcomic_restrict_age' );

		return;
	}

	$age = abs( (int) webcomic( 'GLOBALS._REQUEST.webcomic_restrict_age' ) );

	if ( ! $age ) {
		return;
	}

	update_post_meta( $id, 'webcomic_restrict_age', $age );
}

/**
 * Update webcomic_restrict_age_media meta data.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_post_meta_age_media( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'AgeNonce' ) ), __NAMESPACE__ . 'AgeNonce' ) || webcomic( 'GLOBALS._REQUEST.webcomic_restrict_age_quick_edit' ) ) {
		return;
	}

	$media = abs( (int) webcomic( 'GLOBALS._REQUEST.webcomic_restrict_age_media' ) );

	if ( ! $media ) {
		delete_post_meta( $id, 'webcomic_restrict_age_media' );

		return;
	}

	update_post_meta( $id, 'webcomic_restrict_age_media', $media );
}

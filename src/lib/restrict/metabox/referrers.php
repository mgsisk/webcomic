<?php
/**
 * Referrers metabox functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Restrict\MetaBox;

use WP_Post;

/**
 * Add referrers metabox hooks.
 *
 * @return void
 */
function referrers() {
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_referrers_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_referrers_scripts' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_box_referrers_help' );
	add_filter( 'add_meta_boxes', __NAMESPACE__ . '\hook_add_box_referrers' );
	add_filter( 'bulk_edit_custom_box', __NAMESPACE__ . '\hook_add_quick_edit_referrers', 10, 2 );
	add_filter( 'quick_edit_custom_box', __NAMESPACE__ . '\hook_add_quick_edit_referrers', 10, 2 );
	add_filter( 'get_post_metadata', __NAMESPACE__ . '\hook_get_post_meta_default_referrers', 10, 4 );
	add_filter( 'get_post_metadata', __NAMESPACE__ . '\hook_get_post_meta_default_referrers_media', 10, 4 );
	add_filter( 'update_post_metadata', __NAMESPACE__ . '\hook_update_referrers_media_context', 10, 5 );
	add_filter( 'delete_post_meta', __NAMESPACE__ . '\hook_delete_referrers_media_context', 10, 4 );
	add_filter( 'delete_attachment', __NAMESPACE__ . '\hook_delete_alernative_referrers_media' );
	add_filter( 'display_media_states', __NAMESPACE__ . '\hook_add_referrers_media_states' );
	add_filter( 'display_post_states', __NAMESPACE__ . '\hook_comic_referrers_state', 10, 2 );
	add_filter( 'wp_ajax_webcomic_restrict_referrers_quick_edit', __NAMESPACE__ . '\hook_quick_edit_referrers' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "save_post_{$collection}", __NAMESPACE__ . '\hook_update_post_meta_referrers' );
		add_filter( "save_post_{$collection}", __NAMESPACE__ . '\hook_update_post_meta_referrers_media' );
	}
}

/**
 * Register javascript.
 *
 * @return void
 */
function hook_register_referrers_scripts() {
	wp_register_script(
		__NAMESPACE__ . 'ReferrersQuickEditJS',
		plugins_url( 'srv/restrict/quick-edit-referrers.js', webcomic( 'file' ) ),
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
function hook_enqueue_referrers_scripts() {
	if ( ! preg_match( '/^edit-webcomic\d+$/', get_current_screen()->id ) ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'ReferrersQuickEditJS' );
}

/**
 * Add the help tab.
 *
 * @return void
 */
function hook_add_box_referrers_help() {
	$screen = get_current_screen();

	if ( ! webcomic_collection_exists( $screen->id ) ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'webcomic_restrict_referrers',
			'title'    => __( 'Webcomic Referrer Restrictions', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/referrers-inc-help.php';
			},
		]
	);
}

/**
 * Display the referrers meta box.
 *
 * @param string $type The current post type.
 * @return void
 */
function hook_add_box_referrers( string $type ) {
	if ( ! webcomic_collection_exists( $type ) ) {
		return;
	}

	add_meta_box(
		str_replace( '\\', '_', __NAMESPACE__ ) . 'Referrers',
		__( 'Webcomic Referrer Restrictions', 'webcomic' ),
		function( $post ) {
			$args = [
				'file'             => __DIR__ . '/referrers-inc-box.php',
				'nonce'            => __NAMESPACE__ . 'ReferrersNonce',
				'option_referrers' => implode( "\n", get_post_meta( $post->ID, 'webcomic_restrict_referrers' ) ),
				'option_media'     => implode( ',', get_post_meta( $post->ID, 'webcomic_restrict_referrers_media' ) ),
			];

			require $args['file'];
		},
		$type,
		'side'
	);
}

/**
 * Add the referrers quick edit field.
 *
 * @param string $column The current column.
 * @param string $type The current post type.
 * @return void
 */
function hook_add_quick_edit_referrers( string $column, string $type ) {
	if ( 'webcomic_media' !== $column || ! webcomic_collection_exists( $type ) ) {
		return;
	}

	$args = [
		'file'  => __DIR__ . '/referrers-inc-quick-edit.php',
		'bulk'  => false !== strpos( current_filter(), 'bulk' ),
		'title' => __( 'Referrers (separate with commas)', 'webcomic' ),
		'nonce' => __NAMESPACE__ . 'ReferrersNonce',
	];

	if ( $args['bulk'] ) {
		$args['title'] = __( 'Referrers to Add (separate with commas)', 'webcomic' );
	}

	require $args['file'];
}

/**
 * Get the default referrers for new posts.
 *
 * @param mixed  $meta The meta value.
 * @param int    $id The post ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return only the first $key value.
 * @return mixed
 */
function hook_get_post_meta_default_referrers( $meta, $id, $key, $single ) {
	if ( 'webcomic_restrict_referrers' !== $key || 'auto-draft' !== get_post_status( $id ) ) {
		return $meta;
	}

	$collection = get_post_type( $id );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return $meta;
	}

	return webcomic( "option.{$collection}.restrict_referrers" );
}

/**
 * Get the default referrers media for new posts.
 *
 * @param mixed  $meta The meta value.
 * @param int    $id The post ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return only the first $key value.
 * @return mixed
 */
function hook_get_post_meta_default_referrers_media( $meta, $id, $key, $single ) {
	if ( 'webcomic_restrict_referrers_media' !== $key || 'auto-draft' !== get_post_status( $id ) ) {
		return $meta;
	}

	$collection = get_post_type( $id );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return $meta;
	}

	return [ webcomic( "option.{$collection}.restrict_referrers_media" ) ];
}

/**
 * Update media post meta when adding webcomic_restrict_referrers_media meta data.
 *
 * @param mixed  $check Whether to short-circuit the update process.
 * @param int    $post The post being updated.
 * @param string $key The meta data key being added.
 * @param mixed  $value The meta data value being added.
 * @param mixed  $previous The previous meta data value.
 * @return void
 */
function hook_update_referrers_media_context( $check, int $post, string $key, $value, $previous ) {
	if ( 'webcomic_restrict_referrers_media' !== $key || ! is_a_webcomic( $post ) ) {
		return;
	}

	delete_post_meta( $previous, 'webcomic_restrict_referrers_post', $post );
	add_post_meta( $value, 'webcomic_restrict_referrers_post', $post );
}

/**
 * Delete media contexts when deleting referrers media meta data.
 *
 * @param array  $meta The meta entries being deleted.
 * @param int    $post The post being updated.
 * @param string $key The meta data key being delete.
 * @param mixed  $value The meta data value being deleted.
 * @return void
 */
function hook_delete_referrers_media_context( array $meta, int $post, string $key, $value ) {
	if ( 'webcomic_restrict_referrers_media' !== $key || ! is_a_webcomic( $post ) ) {
		return;
	}

	delete_post_meta( $value, 'webcomic_restrict_referrers_post', $post );
}

/**
 * Delete alternative comic media when a media item is deleted.
 *
 * @param int $post The post being deleted.
 * @return void
 */
function hook_delete_alernative_referrers_media( int $post ) {
	foreach ( get_post_meta( $post, 'webcomic_restrict_referrers_post' ) as $comic ) {
		delete_post_meta( $comic, 'webcomic_restrict_referrers_media', $post );
	}
}

/**
 * Add media states for referrers alternative comic media.
 *
 * @param array $states The media states for the current object.
 * @return array
 */
function hook_add_referrers_media_states( array $states ) : array {
	$comics = get_post_meta( get_the_ID(), 'webcomic_restrict_referrers_post' );

	foreach ( $comics as $comic ) {
		$collection = get_post_type( $comic );

		if ( isset( $states[ "{$collection}-referrers" ] ) ) {
			continue;
		} elseif ( ! webcomic_collection_exists( $collection ) ) {
			delete_post_meta( get_the_ID(), 'webcomic_restrict_referrers_post', $comic );

			continue;
		}

		// Translators: Post type name.
		$states[ "{$collection}-referrers" ] = sprintf( __( '%s Referrer-Restricted Comic Image', 'webcomic' ), webcomic( "option.{$collection}.name" ) );
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
function hook_comic_referrers_state( array $states, WP_Post $post ) : array {
	if ( ! webcomic_collection_exists( $post->post_type ) ) {
		return $states;
	} elseif ( get_post_meta( $post->ID, 'webcomic_restrict_referrers' ) ) {
		$states[] = __( 'Referrer restricted', 'webcomic' );
	}

	return $states;
}

/**
 * Handle quick edit requests.
 *
 * @return void
 */
function hook_quick_edit_referrers() {
	if ( ! webcomic( 'GLOBALS._REQUEST.post' ) ) {
		wp_die();
	}

	$output = get_post_meta( abs( (int) webcomic( 'GLOBALS._REQUEST.post' ) ), 'webcomic_restrict_referrers' );

	wp_send_json( implode( ', ', $output ) );
}

/* ===== Collection Hooks =================================================== */

/**
 * Update webcomic_restrict_referrers meta data.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_post_meta_referrers( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'ReferrersNonce' ) ), __NAMESPACE__ . 'ReferrersNonce' ) ) {
		return;
	}

	$old_referrers = get_post_meta( $id, 'webcomic_restrict_referrers' );
	$new_referrers = array_map( 'trim', explode( "\n", preg_replace( '/\s*,\s*/', "\n", webcomic( 'GLOBALS._REQUEST.webcomic_restrict_referrers' ) ) ) );

	foreach ( $new_referrers as $key => $referrer ) {
		if ( filter_var( $referrer, FILTER_VALIDATE_URL ) || filter_var( "http://{$referrer}", FILTER_VALIDATE_URL ) ) {
			continue;
		}

		unset( $new_referrers[ $key ] );
	}

	sort( $new_referrers );

	if ( $old_referrers === $new_referrers ) {
		return;
	} elseif ( null === webcomic( 'GLOBALS._REQUEST.webcomic_restrict_referrers_bulk' ) ) {
		foreach ( array_diff( $old_referrers, $new_referrers ) as $referrer ) {
			delete_post_meta( $id, 'webcomic_restrict_referrers', $referrer );
		}
	}

	foreach ( array_diff( $new_referrers, $old_referrers ) as $referrer ) {
		add_post_meta( $id, 'webcomic_restrict_referrers', $referrer );
	}
}

/**
 * Update webcomic_restrict_referrers_media meta data.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_post_meta_referrers_media( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'ReferrersNonce' ) ), __NAMESPACE__ . 'ReferrersNonce' ) || webcomic( 'GLOBALS._REQUEST.webcomic_restrict_referrers_quick_edit' ) ) {
		return;
	}

	$media = abs( (int) webcomic( 'GLOBALS._REQUEST.webcomic_restrict_referrers_media' ) );

	if ( ! $media ) {
		delete_post_meta( $id, 'webcomic_restrict_referrers_media' );

		return;
	}

	update_post_meta( $id, 'webcomic_restrict_referrers_media', $media );
}

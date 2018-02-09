<?php
/**
 * Roles metabox functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Restrict\MetaBox;

use WP_Post;

/**
 * Add roles metabox hooks.
 *
 * @return void
 */
function roles() {
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_roles_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_roles_scripts' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_box_roles_help' );
	add_filter( 'add_meta_boxes', __NAMESPACE__ . '\hook_add_box_roles' );
	add_filter( 'bulk_edit_custom_box', __NAMESPACE__ . '\hook_add_quick_edit_roles', 10, 2 );
	add_filter( 'quick_edit_custom_box', __NAMESPACE__ . '\hook_add_quick_edit_roles', 10, 2 );
	add_filter( 'get_post_metadata', __NAMESPACE__ . '\hook_get_post_meta_default_roles', 10, 4 );
	add_filter( 'get_post_metadata', __NAMESPACE__ . '\hook_get_post_meta_default_roles_media', 10, 4 );
	add_filter( 'update_post_metadata', __NAMESPACE__ . '\hook_update_roles_media_context', 10, 5 );
	add_filter( 'delete_post_meta', __NAMESPACE__ . '\hook_delete_roles_media_context', 10, 4 );
	add_filter( 'delete_attachment', __NAMESPACE__ . '\hook_delete_alernative_roles_media' );
	add_filter( 'display_media_states', __NAMESPACE__ . '\hook_add_roles_media_states' );
	add_filter( 'display_post_states', __NAMESPACE__ . '\hook_comic_roles_state', 10, 2 );
	add_filter( 'wp_ajax_webcomic_restrict_roles_quick_edit', __NAMESPACE__ . '\hook_quick_edit_roles' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "save_post_{$collection}", __NAMESPACE__ . '\hook_update_post_meta_roles' );
		add_filter( "save_post_{$collection}", __NAMESPACE__ . '\hook_update_post_meta_roles_media' );
	}
}

/**
 * Register javascript.
 *
 * @return void
 */
function hook_register_roles_scripts() {
	wp_register_script(
		__NAMESPACE__ . 'RolesQuickEditJS',
		plugins_url( 'srv/restrict/quick-edit-roles.js', webcomic( 'file' ) ),
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
function hook_enqueue_roles_scripts() {
	if ( ! preg_match( '/^edit-webcomic\d+$/', get_current_screen()->id ) ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'RolesQuickEditJS' );
}

/**
 * Add the help tab.
 *
 * @return void
 */
function hook_add_box_roles_help() {
	$screen = get_current_screen();

	if ( ! webcomic_collection_exists( $screen->id ) ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'webcomic_restrict_roles',
			'title'    => __( 'Webcomic Role Restrictions', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/roles-inc-help.php';
			},
		]
	);
}

/**
 * Display the roles meta box.
 *
 * @param string $type The current post type.
 * @return void
 */
function hook_add_box_roles( string $type ) {
	if ( ! webcomic_collection_exists( $type ) ) {
		return;
	}

	add_meta_box(
		str_replace( '\\', '_', __NAMESPACE__ ) . 'Roles',
		__( 'Webcomic Role Restrictions', 'webcomic' ),
		function( $post ) {
			$args = [
				'file'         => __DIR__ . '/roles-inc-box.php',
				'nonce'        => __NAMESPACE__ . 'RolesNonce',
				'option_roles' => get_post_meta( $post->ID, 'webcomic_restrict_roles' ),
				'option_media' => implode( ',', get_post_meta( $post->ID, 'webcomic_restrict_roles_media' ) ),
			];

			require $args['file'];
		},
		$type,
		'side'
	);
}

/**
 * Add the roles quick edit field.
 *
 * @param string $column The current column.
 * @param string $type The current post type.
 * @return void
 */
function hook_add_quick_edit_roles( string $column, string $type ) {
	if ( 'webcomic_media' !== $column || ! webcomic_collection_exists( $type ) ) {
		return;
	}

	$args = [
		'file'  => __DIR__ . '/roles-inc-quick-edit.php',
		'bulk'  => false !== strpos( current_filter(), 'bulk' ),
		'title' => __( 'Roles', 'webcomic' ),
		'nonce' => __NAMESPACE__ . 'RolesNonce',
	];

	if ( $args['bulk'] ) {
		$args['title'] = __( 'Roles to Add', 'webcomic' );
	}

	require $args['file'];
}

/**
 * Get the default roles for new posts.
 *
 * @param mixed  $meta The meta value.
 * @param int    $id The post ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return only the first $key value.
 * @return mixed
 */
function hook_get_post_meta_default_roles( $meta, $id, $key, $single ) {
	if ( 'webcomic_restrict_roles' !== $key || 'auto-draft' !== get_post_status( $id ) ) {
		return $meta;
	}

	$collection = get_post_type( $id );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return $meta;
	}

	return webcomic( "option.{$collection}.restrict_roles" );
}

/**
 * Get the default roles media for new posts.
 *
 * @param mixed  $meta The meta value.
 * @param int    $id The post ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return only the first $key value.
 * @return mixed
 */
function hook_get_post_meta_default_roles_media( $meta, $id, $key, $single ) {
	if ( 'webcomic_restrict_roles_media' !== $key || 'auto-draft' !== get_post_status( $id ) ) {
		return $meta;
	}

	$collection = get_post_type( $id );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return $meta;
	}

	return [ webcomic( "option.{$collection}.restrict_roles_media" ) ];
}

/**
 * Update media post meta when adding webcomic_restrict_roles_media meta data.
 *
 * @param mixed  $check Whether to short-circuit the update process.
 * @param int    $post The post being updated.
 * @param string $key The meta data key being added.
 * @param mixed  $value The meta data value being added.
 * @param mixed  $previous The previous meta data value.
 * @return void
 */
function hook_update_roles_media_context( $check, int $post, string $key, $value, $previous ) {
	if ( 'webcomic_restrict_roles_media' !== $key || ! is_a_webcomic( $post ) ) {
		return;
	}

	delete_post_meta( $previous, 'webcomic_restrict_roles_post', $post );
	add_post_meta( $value, 'webcomic_restrict_roles_post', $post );
}

/**
 * Delete media contexts when deleting role media meta data.
 *
 * @param array  $meta The meta entries being deleted.
 * @param int    $post The post being updated.
 * @param string $key The meta data key being delete.
 * @param mixed  $value The meta data value being deleted.
 * @return void
 */
function hook_delete_roles_media_context( array $meta, int $post, string $key, $value ) {
	if ( 'webcomic_restrict_roles_media' !== $key || ! is_a_webcomic( $post ) ) {
		return;
	}

	delete_post_meta( $value, 'webcomic_restrict_roles_post', $post );
}

/**
 * Delete alternative comic media when a media item is deleted.
 *
 * @param int $post The post being deleted.
 * @return void
 */
function hook_delete_alernative_roles_media( int $post ) {
	foreach ( get_post_meta( $post, 'webcomic_restrict_roles_post' ) as $comic ) {
		delete_post_meta( $comic, 'webcomic_restrict_roles_media', $post );
	}
}

/**
 * Add media states for role alternative comic media.
 *
 * @param array $states The media states for the current object.
 * @return array
 */
function hook_add_roles_media_states( array $states ) : array {
	$comics = get_post_meta( get_the_ID(), 'webcomic_restrict_roles_post' );

	foreach ( $comics as $comic ) {
		$collection = get_post_type( $comic );

		if ( isset( $states[ "{$collection}-roles" ] ) ) {
			continue;
		} elseif ( ! webcomic_collection_exists( $collection ) ) {
			delete_post_meta( get_the_ID(), 'webcomic_restrict_roles_post', $comic );

			continue;
		}

		// Translators: Post type name.
		$states[ "{$collection}-roles" ] = sprintf( __( '%s Role-Restricted Comic Image', 'webcomic' ), webcomic( "option.{$collection}.name" ) );
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
function hook_comic_roles_state( array $states, WP_Post $post ) : array {
	if ( ! webcomic_collection_exists( $post->post_type ) ) {
		return $states;
	} elseif ( get_post_meta( $post->ID, 'webcomic_restrict_roles' ) ) {
		$states[] = __( 'Role restricted', 'webcomic' );
	}

	return $states;
}

/**
 * Handle quick edit requests.
 *
 * @return void
 */
function hook_quick_edit_roles() {
	if ( ! webcomic( 'GLOBALS._REQUEST.post' ) ) {
		wp_die();
	}

	$output = get_post_meta( abs( (int) webcomic( 'GLOBALS._REQUEST.post' ) ), 'webcomic_restrict_roles' );

	wp_send_json( $output );
}

/* ===== Collection Hooks =================================================== */

/**
 * Update webcomic_restrict_roles meta data.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_post_meta_roles( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'RolesNonce' ) ), __NAMESPACE__ . 'RolesNonce' ) ) {
		return;
	}

	$old_roles = get_post_meta( $id, 'webcomic_restrict_roles' );
	$new_roles = array_filter( webcomic( 'GLOBALS._REQUEST.webcomic_restrict_roles' ) );

	if ( $old_roles === $new_roles ) {
		return;
	} elseif ( in_array( '~loggedin~', $new_roles, true ) ) {
		$new_roles = [ '~loggedin~' ];
		$old_roles = [];

		delete_post_meta( $id, 'webcomic_restrict_roles' );
	}

	if ( null === webcomic( 'GLOBALS._REQUEST.webcomic_restrict_roles_bulk' ) ) {
		foreach ( $old_roles as $role ) {
			delete_post_meta( $id, 'webcomic_restrict_roles', $role );
		}
	}

	foreach ( $new_roles as $role ) {
		if ( webcomic( 'GLOBALS._REQUEST.webcomic_restrict_roles_bulk' ) && in_array( $role, $old_roles, true ) ) {
			continue;
		}

		add_post_meta( $id, 'webcomic_restrict_roles', $role );
	}
}

/**
 * Update webcomic_restrict_roles_media meta data.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_post_meta_roles_media( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'RolesNonce' ) ), __NAMESPACE__ . 'RolesNonce' ) || webcomic( 'GLOBALS._REQUEST.webcomic_restrict_roles_quick_edit' ) ) {
		return;
	}

	$media = abs( (int) webcomic( 'GLOBALS._REQUEST.webcomic_restrict_roles_media' ) );

	if ( ! $media ) {
		delete_post_meta( $id, 'webcomic_restrict_roles_media' );

		return;
	}

	update_post_meta( $id, 'webcomic_restrict_roles_media', $media );
}

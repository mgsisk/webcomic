<?php
/**
 * Comic media metabox functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\MetaBox;

use WP_Screen;

/**
 * Add media metabox hooks.
 *
 * @return void
 */
function media() {
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_box_media_help' );
	add_filter( 'current_screen', __NAMESPACE__ . '\hook_enqueue_media_manager' );
	add_filter( 'add_meta_boxes', __NAMESPACE__ . '\hook_add_box_media' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "save_post_{$collection}", __NAMESPACE__ . '\hook_update_post_meta_media' );
	}
}

/**
 * Add the help tab.
 *
 * @return void
 */
function hook_add_box_media_help() {
	$screen = get_current_screen();

	if ( ! webcomic_collection_exists( $screen->id ) ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'webcomic_media',
			'title'    => __( 'Webcomic Media', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/media-inc-help.php';
			},
		]
	);
}

/**
 * Enqueue the media manager.
 *
 * @param WP_Screen $screen The current screen.
 * @return void
 */
function hook_enqueue_media_manager( WP_Screen $screen ) {
	if ( ! webcomic_collection_exists( $screen->id ) ) {
		return;
	}

	add_filter( 'webcomic_enqueue_media_manager', '__return_true' );
}

/**
 * Display the media meta box.
 *
 * @param string $type The current post type.
 * @return void
 */
function hook_add_box_media( string $type ) {
	if ( ! webcomic_collection_exists( $type ) ) {
		return;
	}

	add_meta_box(
		str_replace( '\\', '_', __NAMESPACE__ ) . 'Media',
		__( 'Webcomic Media', 'webcomic' ),
		function( $post ) {
			$args = [
				'file'  => __DIR__ . '/media-inc-box.php',
				'nonce' => __NAMESPACE__ . 'MetaBoxNonce',
				'media' => implode( ',', get_post_meta( $post->ID, 'webcomic_media' ) ),
			];

			require $args['file'];
		},
		$type,
		'side',
		'high'
	);
}

/* ===== Collection Hooks =================================================== */

/**
 * Update webcomic_media meta data.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_post_meta_media( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'MetaBoxNonce' ) ), __NAMESPACE__ . 'MetaBoxNonce' ) ) {
		return;
	}

	$old_media = get_post_meta( $id, 'webcomic_media' );
	$new_media = array_filter( explode( ',', webcomic( 'GLOBALS._REQUEST.webcomic_media' ) ) );

	if ( $old_media === $new_media ) {
		return;
	}

	foreach ( $old_media as $media ) {
		delete_post_meta( $id, 'webcomic_media', (int) $media );
	}

	foreach ( $new_media as $media ) {
		add_post_meta( $id, 'webcomic_media', (int) $media );
	}
}

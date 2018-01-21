<?php
/**
 * Prints metabox functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Commerce\MetaBox;

use WP_Post;

/**
 * Add prints metabox hooks.
 *
 * @return void
 */
function prints() {
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_prints_styles' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_prints_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_prints_styles' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_box_prints_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_quick_prints_scripts' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_box_prints_help' );
	add_filter( 'add_meta_boxes', __NAMESPACE__ . '\hook_add_box_prints' );
	add_filter( 'bulk_edit_custom_box', __NAMESPACE__ . '\hook_add_quick_edit_prints', 10, 2 );
	add_filter( 'quick_edit_custom_box', __NAMESPACE__ . '\hook_add_quick_edit_prints', 10, 2 );
	add_filter( 'get_post_metadata', __NAMESPACE__ . '\hook_get_post_meta_default_prints', 10, 4 );
	add_filter( 'wp_ajax_webcomic_commerce_prints_quick_edit', __NAMESPACE__ . '\hook_quick_edit_prints' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "save_post_{$collection}", __NAMESPACE__ . '\hook_update_post_meta_prints' );
		add_filter( "save_post_{$collection}", __NAMESPACE__ . '\hook_update_post_meta_prints_adjust' );
	}
}

/**
 * Register prints stylesheets.
 *
 * @return void
 */
function hook_register_prints_styles() {
	wp_register_style(
		__NAMESPACE__ . 'PrintsCSS',
		plugins_url( 'srv/commerce/metabox-prints.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);
}

/**
 * Register prints javascript.
 *
 * @return void
 */
function hook_register_prints_scripts() {
	wp_register_script(
		__NAMESPACE__ . 'PrintsJS',
		plugins_url( 'srv/commerce/metabox-prints.js', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' ),
		true
	);

	wp_register_script(
		__NAMESPACE__ . 'PrintsQuickEditJS',
		plugins_url( 'srv/commerce/quick-edit-prints.js', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' ),
		true
	);
}

/**
 * Enqueue stylesheets.
 *
 * @return void
 */
function hook_enqueue_prints_styles() {
	if ( ! webcomic_collection_exists( get_current_screen()->id ) ) {
		return;
	}

	wp_enqueue_style( __NAMESPACE__ . 'PrintsCSS' );
}

/**
 * Enqueue prints meta box javascript.
 *
 * @return void
 */
function hook_enqueue_box_prints_scripts() {
	if ( ! webcomic_collection_exists( get_current_screen()->id ) ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'PrintsJS' );
}

/**
 * Enqueue prints quick edit javascript.
 *
 * @return void
 */
function hook_enqueue_quick_prints_scripts() {
	if ( ! preg_match( '/^edit-webcomic\d+$/', get_current_screen()->id ) ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'PrintsQuickEditJS' );
}

/**
 * Add the help tab.
 *
 * @return void
 */
function hook_add_box_prints_help() {
	$screen = get_current_screen();

	if ( ! webcomic_collection_exists( $screen->id ) ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'webcomic_prints',
			'title'    => __( 'Webcomic Prints', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/prints-inc-help.php';
			},
		]
	);
}

/**
 * Display the prints meta box.
 *
 * @param string $type The current post type.
 * @return void
 */
function hook_add_box_prints( string $type ) {
	if ( ! webcomic_collection_exists( $type ) ) {
		return;
	}

	add_meta_box(
		str_replace( '\\', '_', __NAMESPACE__ ) . 'Prints',
		__( 'Webcomic Prints', 'webcomic' ),
		function( $post ) {
			$args    = [
				'file'         => __DIR__ . '/prints-inc-box.php',
				'nonce'        => __NAMESPACE__ . 'PrintsNonce',
				'option'       => get_post_meta( $post->ID, 'webcomic_commerce_prints' ),
				'prints'       => webcomic( "option.{$post->post_type}.commerce_prints" ),
				'currency'     => webcomic( "option.{$post->post_type}.commerce_currency" ),
				'all_prints'   => false,
				'label_adjust' => 'webcomic_commerce_prints[%s]',
			];
			$checked = 0;

			foreach ( $args['prints'] as $key => $print ) {
				$sold = (int) get_post_meta( get_the_ID(), "webcomic_commerce_prints_sold_{$key}", true );

				if ( ( $print['stock'] && $sold >= $print['stock'] ) || ! in_array( $key, $args['option'], true ) ) {
					continue;
				}

				$checked++;
			}

			$args['all_prints'] = ( count( $args['prints'] ) === $checked );

			require $args['file'];
		},
		$type
	);
}

/**
 * Add the prints quick edit field.
 *
 * @param string $column The current column.
 * @param string $type The current post type.
 * @return void
 */
function hook_add_quick_edit_prints( string $column, string $type ) {
	if ( 'webcomic_media' !== $column || ! webcomic_collection_exists( $type ) ) {
		return;
	}

	$args = [
		'file'   => __DIR__ . '/prints-inc-quick-edit.php',
		'bulk'   => false !== strpos( current_filter(), 'bulk' ),
		'nonce'  => __NAMESPACE__ . 'PrintsNonce',
		'prints' => webcomic( "option.{$type}.commerce_prints" ),
	];

	require $args['file'];
}

/**
 * Get the default prints setting for new posts.
 *
 * @param mixed  $meta The meta value.
 * @param int    $id The post ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return only the first $key value.
 * @return mixed
 */
function hook_get_post_meta_default_prints( $meta, $id, $key, $single ) {
	if ( 'webcomic_commerce_prints' !== $key || 'auto-draft' !== get_post_status( $id ) ) {
		return $meta;
	}

	$post_type = get_post_type( $id );
	$defaults  = [];

	foreach ( webcomic( "option.{$post_type}.commerce_prints" ) as $print ) {
		if ( ! $print['default'] ) {
			continue;
		}

		$defaults[] = $print['slug'];
	}

	return $defaults;
}

/**
 * Handle quick edit requests.
 *
 * @return void
 */
function hook_quick_edit_prints() {
	if ( ! webcomic( 'GLOBALS._REQUEST.post' ) ) {
		wp_die();
	}

	$output = get_post_meta( abs( (int) webcomic( 'GLOBALS._REQUEST.post' ) ), 'webcomic_commerce_prints' );

	wp_send_json( $output );
}

/* ===== Collection Hooks =================================================== */

/**
 * Update webcomic_commerce_prints meta data.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_post_meta_prints( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'PrintsNonce' ) ), __NAMESPACE__ . 'PrintsNonce' ) ) {
		return;
	}

	$post_type  = get_post_type( $id );
	$prints     = array_keys( webcomic( "option.{$post_type}.commerce_prints" ) );
	$old_prints = array_filter( array_intersect( (array) get_post_meta( $id, 'webcomic_commerce_prints' ), $prints ) );
	$new_prints = array_filter( array_intersect( webcomic( 'GLOBALS._REQUEST.webcomic_commerce_prints' ), $prints ) );

	if ( $old_prints === $new_prints ) {
		return;
	}

	if ( null === webcomic( 'GLOBALS._REQUEST.webcomic_commerce_prints_bulk' ) ) {
		foreach ( $old_prints as $print ) {
			delete_post_meta( $id, 'webcomic_commerce_prints', $print );
		}
	}

	foreach ( $new_prints as $print ) {
		$sold  = (int) get_post_meta( $id, "webcomic_commerce_prints_sold_{$print}", true );
		$stock = webcomic( "option.{$post_type}.commerce_prints.{$print}.stock" );

		if ( $stock && $sold >= $stock ) {
			continue;
		}

		add_post_meta( $id, 'webcomic_commerce_prints', $print );
	}
}

/**
 * Update webcomic_commerce_prints_adjust meta data.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_post_meta_prints_adjust( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'PrintsNonce' ) ), __NAMESPACE__ . 'PrintsNonce' ) || webcomic( 'GLOBALS._REQUEST.webcomic_commerce_prints_quick_edit' ) ) {
		return;
	}

	$post_type = get_post_type( $id );
	$prints    = array_keys( webcomic( "option.{$post_type}.commerce_prints" ) );

	foreach ( $prints as $print ) {
		$adjust = (int) webcomic( "GLOBALS._REQUEST.webcomic_commerce_prints_adjust.{$print}" );

		if ( -100 > $adjust ) {
			$adjust = -100;
		} elseif ( ! $adjust ) {
			delete_post_meta( $id, "webcomic_commerce_prints_adjust_{$print}" );

			continue;
		}

		update_post_meta( $id, "webcomic_commerce_prints_adjust_{$print}", $adjust );
	}
}

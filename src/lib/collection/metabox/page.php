<?php
/**
 * Page collection metabox functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\MetaBox;

/**
 * Add page metabox hooks.
 *
 * @return void
 */
function page() {
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_page_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_box_page_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_quick_page_scripts' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_box_page_help' );
	add_filter( 'add_meta_boxes', __NAMESPACE__ . '\hook_add_box_page' );
	add_filter( 'bulk_edit_custom_box', __NAMESPACE__ . '\hook_add_quick_edit_page', 10, 2 );
	add_filter( 'quick_edit_custom_box', __NAMESPACE__ . '\hook_add_quick_edit_page', 10, 2 );
	add_filter( 'save_post', __NAMESPACE__ . '\hook_update_page_meta_collection' );
	add_filter( 'save_post', __NAMESPACE__ . '\hook_update_page_meta_integrate_landing_page_order' );
	add_filter( 'save_post', __NAMESPACE__ . '\hook_update_page_meta_integrate_landing_page_content' );
	add_filter( 'save_post', __NAMESPACE__ . '\hook_update_page_meta_integrate_landing_page_meta' );
	add_filter( 'save_post', __NAMESPACE__ . '\hook_update_page_meta_integrate_landing_page_comments' );
	add_filter( 'save_post', __NAMESPACE__ . '\hook_update_page_meta_integrate_infinite_order' );
	add_filter( 'wp_ajax_webcomic_page_quick_edit', __NAMESPACE__ . '\hook_page_quick_edit' );
}

/**
 * Register javascript.
 *
 * @return void
 */
function hook_register_page_scripts() {
	wp_register_script(
		__NAMESPACE__ . 'PageJS',
		plugins_url( 'srv/collection/metabox-page.js', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' ),
		true
	);

	wp_register_script(
		__NAMESPACE__ . 'PageQuickEditJS',
		plugins_url( 'srv/collection/quick-edit-page.js', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' ),
		true
	);
}

/**
 * Enqueue page meta box javascript.
 *
 * @return void
 */
function hook_enqueue_box_page_scripts() {
	if ( 'page' !== get_current_screen()->id ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'PageJS' );
}

/**
 * Enqueue page quick edit javascript.
 *
 * @return void
 */
function hook_enqueue_quick_page_scripts() {
	if ( 'edit-page' !== get_current_screen()->id ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'PageQuickEditJS' );
}

/**
 * Add the help tab.
 *
 * @return void
 */
function hook_add_box_page_help() {
	$screen = get_current_screen();

	if ( 'page' !== $screen->id ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'webcomic_settings',
			'title'    => __( 'Webcomic Settings', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/page-inc-help.php';
			},
		]
	);
}

/**
 * Display the collection meta box.
 *
 * @param string $type The current post type.
 * @return void
 */
function hook_add_box_page( string $type ) {
	if ( 'page' !== $type ) {
		return;
	}

	add_meta_box(
		str_replace( '\\', '_', __NAMESPACE__ ) . 'Page',
		__( 'Webcomic Settings', 'webcomic' ),
		function( $page ) {
			$args = [
				'file'              => __DIR__ . '/page-inc-box.php',
				'nonce'             => __NAMESPACE__ . 'PageNonce',
				'option_collection' => get_post_meta( $page->ID, 'webcomic_collection', true ),
				'option_comments'   => get_post_meta( $page->ID, 'webcomic_integrate_landing_page_comments', true ),
				'option_content'    => get_post_meta( $page->ID, 'webcomic_integrate_landing_page_content', true ),
				'option_meta'       => get_post_meta( $page->ID, 'webcomic_integrate_landing_page_meta', true ),
				'option_order'      => get_post_meta( $page->ID, 'webcomic_integrate_landing_page_order', true ),
				'option_infinite'   => get_post_meta( $page->ID, 'webcomic_integrate_infinite_order', true ),
				'label_template'    => 'webcomic_page_template',
				'label_collection'  => 'webcomic_page_collection',
				'label_comments'    => 'webcomic_page_integrate_landing_page_comments',
				'label_content'     => 'webcomic_page_integrate_landing_page_content',
				'label_meta'        => 'webcomic_page_integrate_landing_page_meta',
				'label_order'       => 'webcomic_page_integrate_landing_page_order',
				'label_infinite'    => 'webcomic_page_integrate_infinite_order',
				'customize_url'     => 'https://github.com/mgsisk/webcomic/wiki',
			];

			if ( current_user_can( 'customize' ) ) {
				$args['customize_url'] = add_query_arg(
					[
						'autofocus[section]' => 'webcomic',
					], admin_url( 'customize.php' )
				);
			}

			require $args['file'];
		},
		$type,
		'side'
	);
}

/**
 * Add the collection quick edit field.
 *
 * @param string $column The current column.
 * @param string $type The current post type.
 * @return void
 */
function hook_add_quick_edit_page( string $column, string $type ) {
	if ( 'webcomic_collection' !== $column || 'page' !== $type ) {
		return;
	}

	$args = [
		'file'  => __DIR__ . '/page-inc-quick-edit.php',
		'bulk'  => false !== strpos( current_filter(), 'bulk' ),
		'nonce' => __NAMESPACE__ . 'PageNonce',
	];

	require $args['file'];
}

/**
 * Update webcomic_collection meta data.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_page_meta_collection( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'PageNonce' ) ), __NAMESPACE__ . 'PageNonce' ) ) {
		return;
	} elseif ( ! webcomic( 'GLOBALS._REQUEST.webcomic_page_collection' ) ) {
		delete_post_meta( $id, 'webcomic_collection' );

		return;
	}

	$collection = sanitize_key( webcomic( 'GLOBALS._REQUEST.webcomic_page_collection' ) );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return;
	}

	update_post_meta( $id, 'webcomic_collection', $collection );
}

/**
 * Update page integrate_order meta data.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_page_meta_integrate_landing_page_order( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'PageNonce' ) ), __NAMESPACE__ . 'PageNonce' ) || webcomic( 'GLOBALS._REQUEST.webcomic_page_collection_quick_edit' ) ) {
		return;
	}

	$order = sanitize_key( webcomic( 'GLOBALS._REQUEST.webcomic_page_integrate_landing_page_order' ) );

	if ( 'landing' !== webcomic( 'GLOBALS._REQUEST.webcomic_page_template' ) || ! in_array( $order, [ 'asc', 'desc' ], true ) ) {
		delete_post_meta( $id, 'webcomic_integrate_landing_page_order' );

		return;
	}

	update_post_meta( $id, 'webcomic_integrate_landing_page_order', $order );
}

/**
 * Update page integrate_content meta data.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_page_meta_integrate_landing_page_content( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'PageNonce' ) ), __NAMESPACE__ . 'PageNonce' ) || webcomic( 'GLOBALS._REQUEST.webcomic_page_collection_quick_edit' ) ) {
		return;
	}

	$content = (bool) webcomic( 'GLOBALS._REQUEST.webcomic_page_integrate_landing_page_content' );

	if ( ! $content || 'landing' !== webcomic( 'GLOBALS._REQUEST.webcomic_page_template' ) ) {
		delete_post_meta( $id, 'webcomic_integrate_landing_page_content' );

		return;
	}

	update_post_meta( $id, 'webcomic_integrate_landing_page_content', $content );
}

/**
 * Update page integrate_meta meta data.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_page_meta_integrate_landing_page_meta( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'PageNonce' ) ), __NAMESPACE__ . 'PageNonce' ) || webcomic( 'GLOBALS._REQUEST.webcomic_page_collection_quick_edit' ) ) {
		return;
	}

	$meta = (bool) webcomic( 'GLOBALS._REQUEST.webcomic_page_integrate_landing_page_meta' );

	if ( ! $meta || 'landing' !== webcomic( 'GLOBALS._REQUEST.webcomic_page_template' ) ) {
		delete_post_meta( $id, 'webcomic_integrate_landing_page_meta' );

		return;
	}

	update_post_meta( $id, 'webcomic_integrate_landing_page_meta', $meta );
}

/**
 * Update page integrate_comments meta data.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_page_meta_integrate_landing_page_comments( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'PageNonce' ) ), __NAMESPACE__ . 'PageNonce' ) || webcomic( 'GLOBALS._REQUEST.webcomic_page_collection_quick_edit' ) ) {
		return;
	}

	$comments = (bool) webcomic( 'GLOBALS._REQUEST.webcomic_page_integrate_landing_page_comments' );

	if ( ! $comments || 'landing' !== webcomic( 'GLOBALS._REQUEST.webcomic_page_template' ) ) {
		delete_post_meta( $id, 'webcomic_integrate_landing_page_comments' );

		return;
	}

	update_post_meta( $id, 'webcomic_integrate_landing_page_comments', $comments );
}

/**
 * Update page integrate_infinite_order meta data.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_page_meta_integrate_infinite_order( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'PageNonce' ) ), __NAMESPACE__ . 'PageNonce' ) || webcomic( 'GLOBALS._REQUEST.webcomic_page_collection_quick_edit' ) ) {
		return;
	}

	$order = sanitize_key( webcomic( 'GLOBALS._REQUEST.webcomic_page_integrate_infinite_order' ) );

	if ( 'infinite' !== webcomic( 'GLOBALS._REQUEST.webcomic_page_template' ) || ! in_array( $order, [ 'asc', 'desc' ], true ) ) {
		delete_post_meta( $id, 'webcomic_integrate_infinite_order' );

		return;
	}

	update_post_meta( $id, 'webcomic_integrate_infinite_order', $order );
}

/**
 * Handle quick edit requests.
 *
 * @return void
 */
function hook_page_quick_edit() {
	if ( ! webcomic( 'GLOBALS._REQUEST.post' ) ) {
		wp_die();
	}

	$output = [
		get_post_meta( abs( (int) webcomic( 'GLOBALS._REQUEST.post' ) ), 'webcomic_collection', true ),
	];

	wp_send_json( $output );
}

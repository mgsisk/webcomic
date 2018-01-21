<?php
/**
 * Comic search functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

/**
 * Add comic search hooks.
 *
 * @return void
 */
function search() {
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_search_styles' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_search_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_localize_search_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_search_styles' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_search_scripts' );
	add_filter( 'wp_ajax_webcomic_update_comic_search', __NAMESPACE__ . '\hook_comic_search' );
}

/**
 * Register search stylesheets.
 *
 * @return void
 */
function hook_register_search_styles() {
	wp_register_style(
		__NAMESPACE__ . 'SearchCSS',
		plugins_url( 'srv/collection/search.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);
}

/**
 * Register search scripts.
 *
 * @return void
 */
function hook_register_search_scripts() {
	wp_register_script(
		__NAMESPACE__ . 'SearchJS',
		plugins_url( 'srv/collection/search.js', webcomic( 'file' ) ),
		[ 'jquery' ],
		webcomic( 'option.version' ),
		true
	);
}

/**
 * Localize search scripts.
 *
 * @return void
 */
function hook_localize_search_scripts() {
	/**
	 * Alter the comic search info.
	 *
	 * This filter allows hooks to alter the search help info displayed along
	 * with the comic search box.
	 *
	 * @param string $info The comic search info.
	 */
	$info             = apply_filters( 'webcomic_search_info', __( "The current page's comic, if any, will be used.", 'webcomic' ) );
	$post_type_object = get_post_type_object( 'webcomic1' );

	wp_localize_script(
		__NAMESPACE__ . 'SearchJS',
		'webcomicSearchL10n',
		[
			'info'   => $info,
			'null'   => $post_type_object->labels->not_found,
			'search' => $post_type_object->labels->search_items,
			'remove' => __( 'Remove Comic', 'webcomic' ),
		]
	);
}

/**
 * Enqueue search stylesheets.
 *
 * @return void
 */
function hook_enqueue_search_styles() {
	/**
	 * Alter comic search enqueing.
	 *
	 * This filter allows hooks to enqueue the shared comic search functionality.
	 *
	 * @param bool $enqueue Wether to enqueue the comic search.
	 */
	$enqueue = apply_filters( 'webcomic_enqueue_comic_search', false );

	if ( ! $enqueue ) {
		return;
	}

	wp_enqueue_style( __NAMESPACE__ . 'SearchCSS' );
}

/**
 * Enqueue search scripts.
 *
 * @return void
 */
function hook_enqueue_search_scripts() {
	/* This filter is documented in Mgsisk\Webcomic\Collection::hook_enqueue_search_styles() */
	if ( ! apply_filters( 'webcomic_enqueue_comic_search', false ) ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'SearchJS' );
}

/**
 * Handle comic search requests.
 *
 * @return void
 */
function hook_comic_search() {
	if ( null === webcomic( 'GLOBALS._REQUEST.post' ) && null === webcomic( 'GLOBALS._REQUEST.query' ) ) {
		wp_die();
	} elseif ( webcomic( 'GLOBALS._REQUEST.post' ) ) {
		wp_send_json(
			[
				'title' => get_the_title( webcomic( 'GLOBALS._REQUEST.post' ) ),
				'media' => get_webcomic_media( webcomic( 'GLOBALS._REQUEST.size' ), webcomic( 'GLOBALS._REQUEST.post' ) ),
			]
		);
	} elseif ( ! webcomic( 'GLOBALS._REQUEST.query' ) ) {
		wp_send_json( [] );
	}

	$output = get_posts(
		[
			's'              => esc_sql( webcomic( 'GLOBALS._REQUEST.query' ) ),
			'post_type'      => webcomic( 'option.collections' ),
			'post_status'    => 'any',
			'posts_per_page' => 10,
		]
	);

	foreach ( $output as $key => $post ) {
		$media = get_webcomic_media( 'thumbnail', $post );

		if ( ! $media ) {
			$media = '&mdash;';
		}

		$output[ $key ]->status_label   = get_post_status_object( $post->post_status )->label;
		$output[ $key ]->webcomic_media = $media;
	}

	wp_send_json( $output );
}

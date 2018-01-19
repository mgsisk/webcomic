<?php
/**
 * Comic term search functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Taxonomy;

/**
 * Add comic search hooks.
 *
 * @return void
 */
function search() {
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_search_styles' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_search_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_search_styles' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_search_scripts' );
	add_filter( 'wp_ajax_webcomic_update_comic_term_search', __NAMESPACE__ . '\hook_comic_term_search' );
}

/**
 * Register search stylesheets.
 *
 * @return void
 */
function hook_register_search_styles() {
	wp_register_style(
		__NAMESPACE__ . 'SearchCSS',
		plugins_url( 'srv/taxonomy/search.css', webcomic( 'file' ) ),
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
		plugins_url( 'srv/taxonomy/search.js', webcomic( 'file' ) ),
		[ 'jquery' ],
		webcomic( 'option.version' ),
		true
	);
}

/**
 * Enqueue search stylesheets.
 *
 * @return void
 */
function hook_enqueue_search_styles() {
	/**
	 * Alter comic term search enqueing.
	 *
	 * This filter allows hooks to enqueue the shared comicterm  search
	 * functionality.
	 *
	 * @param bool $enqueue Wether to enqueue the comic term search.
	 */
	$enqueue = apply_filters( 'webcomic_enqueue_comic_term_search', false );

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
	/* This filter is documented in Mgsisk\Webcomic\Taxonomy::hook_enqueue_search_styles() */
	if ( ! apply_filters( 'webcomic_enqueue_comic_term_search', false ) ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'SearchJS' );
}

/**
 * Handle comic term search requests.
 *
 * @return void
 * @suppress PhanAccessMethodInternal - get_terms() incorrectly triggers this.
 */
function hook_comic_term_search() {
	if ( null === webcomic( 'GLOBALS._REQUEST.term' ) && null === webcomic( 'GLOBALS._REQUEST.query' ) && null === webcomic( 'GLOBALS._REQUEST.taxonomy' ) ) {
		wp_die();
	} elseif ( webcomic( 'GLOBALS._REQUEST.term' ) ) {
		$term = get_term( webcomic( 'GLOBALS._REQUEST.term' ) );

		if ( ! $term ) {
			wp_send_json(
				[
					'title' => esc_html__( 'Error', 'webcomic' ),
					'media' => '',
				]
			);
		}

		wp_send_json(
			[
				'title' => $term->name,
				'media' => get_webcomic_term_media( 'medium', $term ),
			]
		);
	} elseif ( ! webcomic( 'GLOBALS._REQUEST.query' ) ) {
		wp_send_json( [] );
	}

	$taxonomies = [];

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		$taxonomies[] = "{$collection}_" . webcomic( 'GLOBALS._REQUEST.taxonomy' );
	}

	$output = get_terms(
		[
			'search'     => esc_sql( webcomic( 'GLOBALS._REQUEST.query' ) ),
			'number'     => 10,
			'taxonomy'   => $taxonomies,
			'hide_empty' => false,
		]
	);

	foreach ( $output as $key => $term ) {
		$media = get_webcomic_term_media( 'thumbnail', $term );

		if ( ! $media ) {
			$media = '&mdash;';
		}

		$output[ $key ]->webcomic_term_media = $media;
	}

	wp_send_json( $output );
}

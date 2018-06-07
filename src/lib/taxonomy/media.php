<?php
/**
 * Taxonomy media functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Taxonomy;

use WP_Screen;

/**
 * Add media hooks.
 *
 * @return void
 */
function media() {
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_media_styles' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_media_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_media_styles' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_media_scripts' );
	add_filter( 'current_screen', __NAMESPACE__ . '\hook_enqueue_media_manager' );
	add_filter( 'update_term_metadata', __NAMESPACE__ . '\hook_update_term_media_context', 10, 5 );
	add_filter( 'delete_term_meta', __NAMESPACE__ . '\hook_delete_term_media_context', 10, 4 );
	add_filter( 'delete_attachment', __NAMESPACE__ . '\hook_delete_term_media' );
	add_filter( 'display_media_states', __NAMESPACE__ . '\hook_add_media_states' );
}

/**
 * Register stylesheets.
 *
 * @return void
 */
function hook_register_media_styles() {
	wp_register_style(
		__NAMESPACE__ . 'ColumnCSS',
		plugins_url( 'srv/taxonomy/media-column.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);
}

/**
 * Register javascript.
 *
 * @return void
 */
function hook_register_media_scripts() {
	wp_register_script(
		__NAMESPACE__ . 'ManagerJS',
		plugins_url( 'srv/taxonomy/manager.js', webcomic( 'file' ) ),
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
function hook_enqueue_media_styles() {
	if ( ! preg_match( '/^edit-webcomic\d+_/', get_current_screen()->id ) ) {
		return;
	}

	wp_enqueue_style( __NAMESPACE__ . 'ColumnCSS' );
}

/**
 * Enqueue scripts.
 *
 * @return void
 */
function hook_enqueue_media_scripts() {
	if ( ! preg_match( '/^edit-webcomic\d+_/', get_current_screen()->id ) ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'ManagerJS' );
}

/**
 * Enqueue the media manager.
 *
 * @param WP_Screen $screen The current screen.
 * @return void
 */
function hook_enqueue_media_manager( WP_Screen $screen ) {
	if ( ! preg_match( '/^edit-webcomic\d+_/', $screen->id ) ) {
		return;
	}

	add_filter( 'webcomic_enqueue_media_manager', '__return_true' );
}

/**
 * Update media term meta when adding webcomic_media term meta data.
 *
 * @param mixed  $check Whether to short-circuit the update process.
 * @param int    $term The post being updated.
 * @param string $key The meta data key being added.
 * @param mixed  $value The meta data value being added.
 * @param mixed  $previous The previous meta data value.
 * @return void
 */
function hook_update_term_media_context( $check, int $term, string $key, $value, $previous ) {
	if ( 'webcomic_media' !== $key || ! is_a_webcomic_term( $term ) ) {
		return;
	}

	delete_post_meta( $previous, 'webcomic_term', $term );
	add_post_meta( $value, 'webcomic_term', $term );
}

/**
 * Delete media contexts when deleting term media meta data.
 *
 * @param array  $meta The meta entries being deleted.
 * @param int    $term The post being updated.
 * @param string $key The meta data key being delete.
 * @param mixed  $value The meta data value being deleted.
 * @return void
 */
function hook_delete_term_media_context( array $meta, int $term, string $key, $value ) {
	if ( 'webcomic_media' !== $key || ! is_a_webcomic_term( $term ) ) {
		return;
	}

	delete_post_meta( $value, 'webcomic_term', $term );
}

/**
 * Delete comic term media when a media item is deleted.
 *
 * @param int $post The post being deleted.
 * @return void
 */
function hook_delete_term_media( int $post ) {
	foreach ( get_post_meta( $post, 'webcomic_term' ) as $term ) {
		delete_term_meta( $term, 'webcomic_media', $post );
	}
}

/**
 * Add media states for comic term media.
 *
 * @param array $states The media states for the current object.
 * @return array
 */
function hook_add_media_states( array $states ) : array {
	$comic_terms = get_post_meta( get_the_ID(), 'webcomic_term' );

	foreach ( $comic_terms as $comic_term ) {
		$comic_term = get_term( $comic_term );

		if ( ! $comic_term || is_wp_error( $comic_term ) ) {
			delete_post_meta( get_the_ID(), 'webcomic_term', $comic_term );

			continue;
		} elseif ( ! webcomic_taxonomy_exists( $comic_term->taxonomy ) ) {
			delete_post_meta( get_the_ID(), 'webcomic_term', $comic_term->term_id );

			continue;
		} elseif ( isset( $states[ $comic_term->taxonomy ] ) ) {
			continue;
		}

		preg_match( '/^(webcomic\d+)/', $comic_term->taxonomy, $match );

		// Translators: 1: Custom post-type name. 2: Custom taxonomy singular name.
		$states[ $comic_term->taxonomy ] = sprintf( __( '%1$s %2$s Image', 'webcomic' ), webcomic( "option.{$match[1]}.name" ), get_taxonomy( $comic_term->taxonomy )->labels->singular_name );
	}

	return $states;
}

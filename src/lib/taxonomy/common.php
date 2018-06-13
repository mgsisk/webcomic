<?php
/**
 * Common taxonomy functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Taxonomy;

use WP_Post;
use WP_Query;

/**
 * Add common hooks.
 *
 * @return void
 */
function common() {
	if ( ! defined( __NAMESPACE__ . '\ENDPOINT' ) ) {
		define( __NAMESPACE__ . '\ENDPOINT', 17592186044416 ); // NOTE Endpoint is 2 ^ 44.
	}

	add_filter( 'webcomic_permalink_tokens', __NAMESPACE__ . '\hook_add_permalink_tokens', 10, 3 );
	add_filter( 'init', __NAMESPACE__ . '\hook_add_rewrite_endpoint' );
	add_filter( 'init', __NAMESPACE__ . '\hook_redirect_webcomic_term_url', 999 );
	add_filter( 'set_object_terms', __NAMESPACE__ . '\hook_set_term_updated_datetime', 10, 6 );
	add_filter( 'deleted_term_relationships', __NAMESPACE__ . '\hook_change_term_updated_datetime', 10, 3 );
	add_filter( 'transition_post_status', __NAMESPACE__ . '\hook_post_term_updated_datetime', 10, 3 );
	add_filter( 'pre_get_posts', __NAMESPACE__ . '\hook_term_archive' );
	add_filter( 'pre_get_posts', __NAMESPACE__ . '\hook_crossover_archive' );
	add_filter( 'get_terms_defaults', __NAMESPACE__ . '\hook_sort_term_defaults', 10, 2 );
	add_filter( 'post_class', __NAMESPACE__ . '\hook_add_post_classes', 10, 3 );
}

/**
 * Add permalink tokens.
 *
 * @param array   $tokens The permalink tokens.
 * @param string  $url The current post URL.
 * @param WP_Post $post The current post object.
 * @return array
 */
function hook_add_permalink_tokens( array $tokens, string $url, WP_Post $post ) : array {
	foreach ( get_object_taxonomies( $post ) as $taxonomy ) {
		if ( false === strpos( $taxonomy, $post->post_type ) || false === strpos( $url, "%{$taxonomy}%" ) ) {
			continue;
		}

		$terms = wp_get_object_terms(
			$post->ID, $taxonomy, [
				'order'    => 'desc',
				'orderby'  => 'meta_value_num',
				'meta_key' => 'webcomic_order',
			]
		);

		if ( ! $terms ) {
			continue;
		} elseif ( $terms[0]->parent ) {
			$tree = [
				$terms[0]->slug,
			];

			foreach ( get_ancestors( $terms[0]->term_id, $taxonomy, 'taxonomy' ) as $term ) {
				$term   = get_term( $term );
				$tree[] = $term->slug;
			}

			$terms[0]->slug = implode( '/', array_reverse( $tree ) );
		}

		$tokens[ "%{$taxonomy}%" ] = $terms[0]->slug;
	}

	return $tokens;
}

/**
 * Add the crossover rewrite endpoint for comic terms.
 *
 * @return void
 * @suppress PhanUndeclaredConstant - ENDPOINT incorrectly triggers this.
 */
function hook_add_rewrite_endpoint() {
	add_rewrite_endpoint( 'crossover', ENDPOINT );
}

/**
 * Redirect webcomic_term_url requests.
 *
 * @return void
 */
function hook_redirect_webcomic_term_url() {
	if ( null === webcomic( 'GLOBALS._REQUEST.webcomic_term_url' ) ) {
		return;
	}

	$args = array_map(
		function( $value ) {
				return maybe_unserialize( rawurldecode( $value ) );
		}, array_pad( explode( '-', webcomic( 'GLOBALS._REQUEST.webcomic_term_url' ), 6 ), 6, '' )
	);

	if ( ! is_array( $args[3] ) ) {
		$args[3] = [];
	}

	if ( $args[0] ) {
		$args[3]['taxonomy'] = $args[0];
	}

	if ( $args[1] ) {
		$args[3]['relation'] = $args[1];
	}

	if ( ! is_array( $args[5] ) ) {
		$args[5] = [];
	}

	add_filter( 'wp_doing_ajax', '__return_true' ) && wp_safe_redirect( get_webcomic_term_url( $args[2], $args[3], $args[4], $args[5] ) ) && wp_die();
}

/**
 * Set term updated meta when updating post terms.
 *
 * @param int    $post The post being updated.
 * @param array  $terms The term objects added to the post.
 * @param array  $term_ids The term ID's added to the post.
 * @param string $taxonomy The taxonomy the terms belong to.
 * @param bool   $append Whether the terms are being appended or not.
 * @param array  $old_ids The old term ID's that were assigned to the post.
 * @return void
 */
function hook_set_term_updated_datetime( int $post, array $terms, array $term_ids, string $taxonomy, bool $append, array $old_ids ) {
	if ( ! preg_match( '/^webcomic\d+_/', $taxonomy ) ) {
		return;
	}

	clean_post_cache( $post );

	$terms = array_merge( $term_ids, $old_ids );

	foreach ( $terms as $id ) {
		$latest = get_posts(
			[
				'posts_per_page' => 1,
				'post_status'    => 'publish',
				'post_type'      => 'any',
				'tax_query'      => [
					[
						'taxonomy' => $taxonomy,
						'terms'    => (int) $id,
					],
				],
			]
		);

		if ( ! $latest ) {
			delete_term_meta( $id, 'webcomic_updated' );

			continue;
		}

		update_term_meta( $id, 'webcomic_updated', $latest[0]->post_date );
	}
}

/**
 * Set term updated meta when deleting post terms.
 *
 * @param int    $post The post being updated.
 * @param array  $terms The terms removed from the post.
 * @param string $taxonomy The taxonomy the terms belong to.
 * @return void
 */
function hook_change_term_updated_datetime( int $post, array $terms, string $taxonomy ) {
	hook_set_term_updated_datetime( $post, [], $terms, $taxonomy, false, [] );
}

/**
 * Set term updated meta when a comic transitions status.
 *
 * @param string  $new_status The new post status.
 * @param string  $old_status The old post status.
 * @param WP_Post $post The post being updated.
 * @return void
 */
function hook_post_term_updated_datetime( string $new_status, string $old_status, WP_Post $post ) {
	if ( is_a_webcomic( $post ) ) {
		return;
	}

	foreach ( get_object_taxonomies( $post ) as $taxonomy ) {
		$terms = wp_get_object_terms(
			$post->ID, $taxonomy, [
				'fields' => 'ids',
			]
		);

		if ( ! $terms ) {
			continue;
		}

		hook_set_term_updated_datetime( $post->ID, [], $terms, $taxonomy, false, [] );
	}
}

/**
 * Modify the main query for taxonomy archive requests.
 *
 * @param WP_Query $query The query to modify.
 * @return void
 */
function hook_term_archive( WP_Query $query ) {
	if ( $query->is_admin() || ! $query->is_main_query() || ! $query->is_tax() || isset( $query->query['crossover'] ) || ! $query->queried_object ) {
		return;
	}

	preg_match( '/^(webcomic\d+)_(.+)$/', $query->queried_object->taxonomy, $match );

	if ( ! $match || ! webcomic( "option.{$match[1]}" ) || webcomic( "option.{$match[1]}.{$match[2]}_crossovers" ) ) {
		return;
	}

	$query->set( 'post_type', $match[1] );
}

/**
 * Modify the main query for crossover taxonomy archive requests.
 *
 * @param WP_Query $query The query to modify.
 * @return void
 */
function hook_crossover_archive( WP_Query $query ) {
	$args = [
		'crossover'  => true,
		'hide_empty' => false,
	];

	if ( $query->is_admin() || ! $query->is_main_query() || ! is_webcomic_tax( null, null, null, $args ) ) {
		return;
	}

	$collections = get_webcomic_collections( $args );

	if ( ! $collections ) {
		$collections = 'webcomic0'; // NOTE Force empty results with a bogus post type.
	}

	$query->set( 'posts_per_page', -1 ); // NOTE Force all results onto one page, as paging doesn't work on crossover archives.
	$query->set( 'post_type', $collections );
}

/**
 * Update get_terms() defaults based on collection settings.
 *
 * @param array $args The default get_terms() arguments.
 * @param array $taxonomies The taxonomies to get terms for.
 * @return array
 */
function hook_sort_term_defaults( array $args, $taxonomies ) : array {
	if ( ! $taxonomies || 1 < count( $taxonomies ) || ! preg_match( '/^(webcomic\d+)_(.+)/', $taxonomies[0], $match ) || ! webcomic( "option.{$match[1]}.{$match[2]}_sort" ) ) {
		return $args;
	}

	$args['orderby']  = 'meta_value_num';
	$args['meta_key'] = 'webcomic_order';

	return $args;
}

/**
 * Add CSS classes to post elements.
 *
 * @param array $classes The current list of classes.
 * @param array $class A list of additional classes.
 * @param int   $post The current post ID.
 * @return array
 */
function hook_add_post_classes( array $classes, array $class, int $post ) : array {
	$collections = get_webcomic_collections(
		[
			'hide_empty'     => false,
			'not_related_by' => get_post_type( $post ),
			'related_to'     => get_webcomic( $post ),
		]
	);

	if ( ! $collections ) {
		return $classes;
	}

	$classes[] = 'webcomic-crossover';

	foreach ( $collections as $collection ) {
		$classes[] = "{$collection}-crossover";
	}

	return $classes;
}

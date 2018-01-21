<?php
/**
 * Taxonomy filters
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Taxonomy;

use WP_Post;
use WP_Term;
use function Mgsisk\Webcomic\Collection\util_get_media_tokens;

/**
 * Add filters.
 *
 * @return void
 */
function filters() {
	add_filter( 'get_webcomic_term', __NAMESPACE__ . '\hook_get_webcomic_term', 0, 2 );
	add_filter( 'get_webcomic_adjacent_term', __NAMESPACE__ . '\hook_get_webcomic_adjacent_term', 10, 5 );
	add_filter( 'get_webcomic_term_args', __NAMESPACE__ . '\hook_get_webcomic_term_args', 0 );
	add_filter( 'get_webcomic_term_url', __NAMESPACE__ . '\hook_get_webcomic_term_url', 10, 6 );
	add_filter( 'get_webcomic_term_link_tokens', __NAMESPACE__ . '\hook_get_webcomic_term_link_tokens', 0, 3 );
	add_filter( 'get_webcomic_terms_args', __NAMESPACE__ . '\hook_get_webcomic_terms_args', 0 );
	add_filter( 'get_webcomic_terms_list_args', __NAMESPACE__ . '\hook_get_webcomic_terms_list_args', 0, 2 );
	add_filter( 'get_webcomic_collection', __NAMESPACE__ . '\hook_get_webcomic_collection', 10, 2 );
	add_filter( 'get_webcomic_collections_args', __NAMESPACE__ . '\hook_get_webcomic_collections_args', 10, 2 );
}

/**
 * Handle getting the current term for get_webcomic_term().
 *
 * @param mixed $term Optional term to get.
 * @param array $args Optional arguments.
 * @return mixed
 */
function hook_get_webcomic_term( $term, array $args ) {
	if ( $term ) {
		return $term;
	}

	$object = get_queried_object();

	if ( $object instanceof WP_Term && ( empty( $args['taxonomy'] ) || $object->taxonomy === $args['taxonomy'] ) ) {
		return $object->term_id;
	} elseif ( ! $object instanceof WP_Post ) {
		$object = get_webcomic();
	}

	if ( $object instanceof WP_Post && isset( $args['taxonomy'] ) && webcomic_taxonomy_exists( $args['taxonomy'] ) && is_a_webcomic( $object ) ) {
		$terms = wp_get_object_terms(
			$object->ID, $args['taxonomy'], [
				'fields' => 'ids',
			]
		);

		if ( $terms || ! is_wp_error( $terms ) ) {
			return current( $terms );
		}
	}

	return $term;
}

/**
 * Handle hierarchical skip arguments for get_webcomic_term().
 *
 * @param int     $id The adjacent term ID.
 * @param array   $terms The term ID's.
 * @param WP_Term $reference The reference term.
 * @param array   $args Optional arguments.
 * @param mixed   $term Optional term to get.
 * @return int
 */
function hook_get_webcomic_adjacent_term( int $id, array $terms, WP_Term $reference, array $args, $term ) : int {
	if ( isset( $args['hierarchical'] ) && false === $args['hierarchical'] ) {
		return $id;
	}

	preg_match( '/^(webcomic\d+)_(.+)/', $reference->taxonomy, $match );

	$args += [
		'hierarchical_skip' => webcomic( "option.{$match[1]}.{$match[2]}_hierarchical_skip" ),
	];

	if ( ! $args['hierarchical_skip'] ) {
		return $id;
	}

	$key           = array_search( $id, $terms, true );
	$tree_function = 'get_term_children';

	if ( 'next' === $args['relation'] ) {
		$tree_function = 'get_ancestors';
	}

	while ( array_key_exists( --$key, $terms ) && in_array( $reference->term_id, $tree_function( $id, $reference->taxonomy ), true ) ) {
		$id = $terms[ $key ];
	}

	return $id;
}

/**
 * Handle type arguments for get_webcomic_term().
 *
 * @param array $args Optional arguments.
 * @return mixed
 */
function hook_get_webcomic_term_args( array $args ) {
	if ( empty( $args['type'] ) ) {
		return $args;
	}

	if ( empty( $args['collection'] ) ) {
		$args['collection'] = get_webcomic_collection();
	}

	$args['taxonomy'] = "{$args['collection']}_{$args['type']}";

	return $args;
}

/**
 * Handle crossover arguments for get_webcomic_term_url().
 *
 * @param string  $url The comic term URL.
 * @param WP_Term $comic_term The comic term the URL points to.
 * @param array   $post_args Optional post arguments.
 * @param mixed   $post Optional reference post.
 * @param array   $args Optional arguments.
 * @param mixed   $term Optional reference term.
 * @return string
 */
function hook_get_webcomic_term_url( $url, WP_Term $comic_term, array $post_args, $post, array $args, $term ) : string {
	if ( $post || $post_args || empty( $args['crossover'] ) ) {
		return $url;
	}

	$args['crossover'] = preg_grep( '/^webcomic\d+$/', (array) $args['crossover'] );

	foreach ( $args['crossover'] as $key => $collection ) {
		if ( 0 === strpos( $comic_term->taxonomy, $collection ) ) {
			unset( $args['crossover'][ $key ] );

			continue;
		}

		$args['crossover'][ $key ] = webcomic( "option.{$collection}.slug" );
	}

	$args['crossover'] = implode( '/', $args['crossover'] );

	if ( get_option( 'rewrite_rules' ) ) {
		return user_trailingslashit( trailingslashit( $url ) . 'crossover/' . $args['crossover'], $comic_term->taxonomy );
	}

	return esc_url(
		add_query_arg(
			[
				'crossover' => $args['crossover'],
			], $url
		)
	);
}

/**
 * Handle default webcomic term link tokens.
 *
 * @param array   $tokens The token values.
 * @param string  $link The link text to search for tokens.
 * @param WP_Term $comic_term The comic term the link is for.
 * @return array
 */
function hook_get_webcomic_term_link_tokens( array $tokens, string $link, WP_Term $comic_term ) : array {
	if ( ! preg_match( '/%\S/', $link ) ) {
		return $tokens;
	}

	/* This filter is documented in Mgsisk\Webcomic\Collection::hook_get_media_tokens() */
	$tokens = util_get_media_tokens( get_term_meta( $comic_term->term_id, 'webcomic_media' ) );

	if ( false !== strpos( $link, '%date' ) ) {
		$tokens['%date'] = get_webcomic_term_updated( get_option( 'date_format' ), $comic_term );
	}

	if ( false !== strpos( $link, '%time' ) ) {
		$tokens['%time'] = get_webcomic_term_updated( get_option( 'time_format' ), $comic_term );
	}

	if ( false !== strpos( $link, '%title' ) ) {
		$tokens['%title'] = get_webcomic_term_title( $comic_term );
	}

	if ( false !== strpos( $link, '%count' ) ) {
		$tokens['%count'] = get_webcomic_term_count( $comic_term );
	}

	if ( false !== strpos( $link, '%description' ) ) {
		$tokens['%description'] = get_webcomic_term_description( $comic_term );
	}

	return $tokens;
}

/**
 * Handle type and collection arguments for get_webcomic_terms().
 *
 * @param array $args Optional arguments.
 * @return mixed
 */
function hook_get_webcomic_terms_args( array $args ) {
	$args += [
		'collection' => '',
		'type'       => '',
	];

	if ( empty( $args['type'] ) ) {
		return $args;
	} elseif ( isset( $args['object_ids'] ) && is_int( $args['object_ids'] ) ) {
		$args['taxonomy'] = [];
		$collection_args  = [
			'hide_empty' => false,
		];

		if ( 'own' === $args['collection'] ) {
			$collection_args['id__in'] = [ get_post_type( $args['object_ids'] ) ];
		} elseif ( 'crossover' === $args['collection'] ) {
			$collection_args['id__not_in'] = [ get_post_type( $args['object_ids'] ) ];
		}

		foreach ( get_webcomic_collections( $collection_args ) as $collection ) {
			$args['taxonomy'][] = "{$collection}_{$args['type']}";
		}

		return $args;
	} elseif ( empty( $args['collection'] ) ) {
		$args['collection'] = get_webcomic_collection();
	}

	$args['taxonomy'] = "{$args['collection']}_{$args['type']}";

	return $args;
}

/**
 * Set default arguments for get_webcomic_terms_list().
 *
 * @param array $args The arguments to filter.
 * @param array $terms The comics included in the list.
 * @return array
 */
function hook_get_webcomic_terms_list_args( array $args, array $terms ) : array {
	$args = [
		'cloud_count' => [],
		'cloud_floor' => 0,
		'cloud_step'  => 0,
		'options'     => false,
	] + $args + [
		'cloud_max'       => 0,
		'cloud_min'       => 0,
		'current'         => [],
		'depth'           => 0,
		'end_el'          => '',
		'end_lvl'         => '',
		'end'             => '',
		'feed_type'       => 'atom',
		'feed'            => '',
		'format'          => ', ',
		'link_args'       => [],
		'link_post_args'  => [],
		'link_post'       => null,
		'link'            => '%title',
		'start_el'        => '',
		'start_lvl'       => '',
		'start'           => '',
		'taxonomy'        => [],
		'walker'          => '',
		'webcomics'       => [],
		'webcomics_depth' => null,
	];

	if ( ! array_key_exists( 'hierarchical', $args ) ) {
		$args['hierarchical'] = is_taxonomy_hierarchical( current( (array) $args['taxonomy'] ) );
	}

	if ( $args['hierarchical'] ) {
		preg_match( '/^(.*){{(.*)}}(.*)$/s', $args['format'], $match );

		$match        += [ '', '', '', '' ];
		$start_end     = array_replace( [ $match[1], $match[3] ], array_filter( [ $args['start'], $args['end'] ] ) );
		$args['start'] = $start_end[0];
		$args['end']   = $start_end[1];
	}

	if ( preg_match( '/<(?:select|optgroup).*?>.*{{.*}}/', $args['format'] ) ) {
		$args['feed']      = '';
		$args['options']   = true;
		$args['cloud_min'] = 0;
		$args['cloud_max'] = 0;
	}

	if ( $args['cloud_min'] && $args['cloud_max'] && $args['cloud_min'] < $args['cloud_max'] ) {
		foreach ( $terms as $term ) {
			$args['cloud_count'][ $term->term_id ] = (int) $term->count;
		}

		$args['cloud_floor'] = min( $args['cloud_count'] );
		$args['cloud_step']  = ( $args['cloud_max'] - $args['cloud_min'] ) / max( 1, max( $args['cloud_count'] ) - $args['cloud_floor'] );
	}

	if ( $args['webcomics'] && empty( $args['webcomics']['post_type'] ) ) {
		$args['webcomics']['post_type'] = 'any';
	}

	if ( ! class_exists( $args['walker'] ) ) {
		$args['walker'] = __NAMESPACE__ . '\Walker\TermLister';
	}

	return $args;
}

/**
 * Handle crossover collection arguments for get_webcomic_collection().
 *
 * @param string $collection The collection ID.
 * @param mixed  $type Optional type of collection to get.
 * @return string
 */
function hook_get_webcomic_collection( string $collection, $type ) : string {
	if ( ! is_string( $type ) || 0 !== strpos( $type, 'crossover' ) ) {
		return $collection;
	}

	preg_match( '/\d+$/', $type, $match );

	$collections = get_webcomic_collections(
		[
			'crossover'  => true,
			'hide_empty' => false,
		]
	);

	if ( ! $collections || ( $match && empty( $collections[ $match[0] ] ) ) ) {
		return '';
	} elseif ( ! $match ) {
		$match = [ 0 ];
	}

	return $collections[ $match[0] ];
}

/**
 * Handle crossover arguments for get_webcomic_collections().
 *
 * @param array $args The get_webcomic_collections() arguments.
 * @return array
 */
function hook_get_webcomic_collections_args( array $args ) : array {
	if ( empty( $args['crossover'] ) ) {
		return $args;
	}

	$crossovers      = explode( '/', get_query_var( 'crossover' ) );
	$crossover_ids   = [];
	$crossover_slugs = [];

	if ( ! $crossovers[0] ) {
		$crossovers    = [];
		$crossover_ids = webcomic( 'option.collections' );
	}

	foreach ( $crossovers as $crossover ) {
		$crossover_slugs[] = $crossover;
	}

	$args['id__in']     = $crossover_ids;
	$args['slug__in']   = $crossover_slugs;
	$args['id__not_in'] = [ get_webcomic_collection() ];

	return $args;
}

<?php
/**
 * Collection filters
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

use WP_Post;
use WP_Term;

/**
 * Add filters.
 *
 * @return void
 */
function filters() {
	add_filter( 'get_webcomic_collection_link_tokens', __NAMESPACE__ . '\hook_get_webcomic_collection_link_tokens', 0, 3 );
	add_filter( 'get_webcomic_collections', __NAMESPACE__ . '\hook_get_webcomic_collections_hide_empty', 0, 2 );
	add_filter( 'get_webcomic_collections', __NAMESPACE__ . '\hook_get_webcomic_collections_in_not_in', 0, 2 );
	add_filter( 'get_webcomic_collections', __NAMESPACE__ . '\hook_get_webcomic_collections_related', 0, 2 );
	add_filter( 'get_webcomic_collections_list_args', __NAMESPACE__ . '\hook_get_webcomic_collections_list_args', 0, 2 );
	add_filter( 'get_webcomic_link_tokens', __NAMESPACE__ . '\hook_get_webcomic_link_tokens', 0, 3 );
	add_filter( 'get_webcomics_args', __NAMESPACE__ . '\hook_get_webcomics_args' );
	add_filter( 'get_webcomics_list_args', __NAMESPACE__ . '\hook_get_webcomics_list_args', 0, 2 );
}

/**
 * Handle default webcomic collection link tokens.
 *
 * @param array  $tokens The token values.
 * @param string $link The link text to search for tokens.
 * @param string $collection The collection the link is for.
 * @return array
 */
function hook_get_webcomic_collection_link_tokens( array $tokens, string $link, string $collection ) : array {
	if ( ! preg_match( '/%\S/', $link ) ) {
		return $tokens;
	}

	$tokens = util_get_media_tokens( [ webcomic( "option.{$collection}.media" ) ] );

	if ( false !== strpos( $link, '%date' ) ) {
		$tokens['%date'] = get_webcomic_collection_updated( get_option( 'date_format' ), $collection );
	}

	if ( false !== strpos( $link, '%time' ) ) {
		$tokens['%time'] = get_webcomic_collection_updated( get_option( 'time_format' ), $collection );
	}

	if ( false !== strpos( $link, '%title' ) ) {
		$tokens['%title'] = get_webcomic_collection_title( $collection );
	}

	if ( false !== strpos( $link, '%count' ) ) {
		$tokens['%count'] = get_webcomic_collection_count( $collection );
	}

	if ( false !== strpos( $link, '%description' ) ) {
		$tokens['%description'] = get_webcomic_collection_description( $collection );
	}

	return $tokens;
}

/**
 * Handle the hide_empty argument for get_webcomic_collections().
 *
 * @param array $collections The collections to filter.
 * @param array $args The get_webcomic_collections() arguments.
 * @return array
 */
function hook_get_webcomic_collections_hide_empty( array $collections, array $args ) : array {
	if ( empty( $args['hide_empty'] ) ) {
		return $collections;
	}

	foreach ( $collections as $key => $collection ) {
		if ( get_webcomic_collection_count( $collection ) ) {
			continue;
		}

		unset( $collections[ $key ] );
	}

	return $collections;
}

/**
 * Handle *__in and *__not_in arguments for get_webcomic_collections().
 *
 * @param array $collections The collections to filter.
 * @param array $args The get_webcomic_collections() arguments.
 * @return array
 */
function hook_get_webcomic_collections_in_not_in( array $collections, array $args ) : array {
	if ( empty( $args['id__in'] ) && empty( $args['id__not_in'] ) && empty( $args['slug__in'] ) && empty( $args['slug__not_in'] ) ) {
		return $collections;
	}

	$args       += [
		'id__in'       => [],
		'id__not_in'   => [],
		'slug__in'     => [],
		'slug__not_in' => [],
	];
	$collections = [];

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		if ( $args['id__in'] && ! in_array( $collection, $args['id__in'], true ) ) {
			continue;
		} elseif ( $args['id__not_in'] && in_array( $collection, $args['id__not_in'], true ) ) {
			continue;
		} elseif ( $args['slug__in'] && ! in_array( webcomic( "option.{$collection}.slug" ), $args['slug__in'], true ) ) {
			continue;
		} elseif ( $args['slug__not_in'] && in_array( webcomic( "option.{$collection}.slug" ), $args['slug__not_in'], true ) ) {
			continue;
		}

		$collections[] = $collection;
	}

	return array_unique( $collections );
}

/**
 * Handle relational arguments for get_webcomic_collections().
 *
 * @param array $collections The collections to filter.
 * @param array $args The get_webcomic_collections() arguments.
 * @return array
 */
function hook_get_webcomic_collections_related( array $collections, array $args ) {
	if ( empty( $args['related_to'] ) && empty( $args['related_by'] ) && empty( $args['not_related_by'] ) ) {
		return $collections;
	}

	$args                  += [
		'related_to'     => null,
		'related_by'     => [],
		'not_related_by' => [],
	];
	$collections            = [];
	$args['related_by']     = preg_replace( '/(\||^)(webcomic\d+)(\||$)/', '$1$2_$3', implode( '|', (array) $args['related_by'] ) );
	$args['not_related_by'] = preg_replace( '/(\||^)(webcomic\d+)(\||$)/', '$1$2_$3', implode( '|', (array) $args['not_related_by'] ) );

	if ( ! $args['related_to'] ) {
		$args['related_to'] = get_webcomic_collection();
	}

	foreach ( util_get_taxonomies( $args['related_to'] ) as $taxonomy ) {
		$match = [];

		if ( ! preg_match( '/^(webcomic\d+)/', $taxonomy, $match ) ) {
			continue;
		} elseif ( $args['related_by'] && ! preg_match( "/{$args['related_by']}/", $taxonomy ) ) {
			continue;
		} elseif ( $args['not_related_by'] && preg_match( "/{$args['not_related_by']}/", $taxonomy ) ) {
			continue;
		}

		$collections[] = $match[1];
	}

	return array_unique( $collections );
}

/**
 * Set default arguments for get_webcomic_collections_list().
 *
 * @param array $args The arguments to filter.
 * @param array $collections The collections included in the list.
 * @return array
 */
function hook_get_webcomic_collections_list_args( array $args, array $collections ) : array {
	$args = [
		'cloud_count' => [],
		'cloud_floor' => 0,
		'cloud_step'  => 0,
		'options'     => false,
	] + $args + [
		'cloud_max' => 0,
		'cloud_min' => 0,
		'current'   => '',
		'feed_type' => 'atom',
		'feed'      => '',
		'format'    => ', ',
		'link_args' => [],
		'link_post' => null,
		'link'      => '%title',
		'walker'    => '',
		'webcomics' => [],
	];

	if ( preg_match( '/<(?:select|optgroup).*?>.*{{.*}}/', $args['format'] ) ) {
		$args['feed']      = '';
		$args['options']   = true;
		$args['cloud_min'] = 0;
		$args['cloud_max'] = 0;
	}

	if ( $args['cloud_min'] && $args['cloud_max'] && $args['cloud_min'] < $args['cloud_max'] ) {
		foreach ( $collections as $collection ) {
			$args['cloud_count'][ $collection ] = get_webcomic_collection_count( $collection );
		}

		$args['cloud_floor'] = min( $args['cloud_count'] );
		$args['cloud_step']  = ( $args['cloud_max'] - $args['cloud_min'] ) / max( 1, max( $args['cloud_count'] ) - $args['cloud_floor'] );
	}

	if ( ! class_exists( $args['walker'] ) ) {
		$args['walker'] = __NAMESPACE__ . '\Walker\CollectionLister';
	}

	return $args;
}

/**
 * Handle default webcomic link tokens.
 *
 * @param array   $tokens The token values.
 * @param string  $link The link text to search for tokens.
 * @param WP_Post $comic The comic the link is for.
 * @return array
 */
function hook_get_webcomic_link_tokens( array $tokens, string $link, WP_Post $comic ) : array {
	if ( ! preg_match( '/%\S/', $link ) ) {
		return $tokens;
	}

	$tokens = util_get_media_tokens(
		[
			get_post_meta( $comic->ID, 'webcomic_media' ),
			'wfi-' => get_post_thumbnail_id( $comic->ID ),
		]
	);

	if ( false !== strpos( $link, '%date' ) ) {
		$tokens['%date'] = get_the_date( '', $comic->ID );
	}

	if ( false !== strpos( $link, '%time' ) ) {
		$tokens['%time'] = get_the_date( get_option( 'time_format' ), $comic->ID );
	}

	if ( false !== strpos( $link, '%title' ) ) {
		$tokens['%title'] = get_the_title( $comic );
	}

	return $tokens;
}

/**
 * Handle relational arguments for get_webcomics().
 *
 * @param array $args The get_webcomics() arguments.
 * @return array
 */
function hook_get_webcomics_args( array $args ) : array {
	if ( empty( $args['related_to'] ) && empty( $args['related_by'] ) && empty( $args['not_related_by'] ) ) {
		return $args;
	}

	$args                  += [
		'related_to'     => null,
		'related_by'     => [],
		'not_related_by' => [],
		'tax_query'      => [],
	];
	$args['related_to']     = get_webcomic( $args['related_to'] );
	$args['related_by']     = preg_replace( '/(\||^)(webcomic\d+)(\||$)/', '$1$2_$3', implode( '|', (array) $args['related_by'] ) );
	$args['not_related_by'] = preg_replace( '/(\||^)(webcomic\d+)(\||$)/', '$1$2_$3', implode( '|', (array) $args['not_related_by'] ) );
	$args['tax_query']     += [
		'relation' => 'OR',
	];

	foreach ( get_object_taxonomies( $args['related_to'] ) as $taxonomy ) {
		if ( $args['related_by'] && ! preg_match( "/{$args['related_by']}/", $taxonomy ) ) {
			continue;
		} elseif ( $args['not_related_by'] && preg_match( "/{$args['not_related_by']}/", $taxonomy ) ) {
			continue;
		}

		$terms = wp_get_object_terms(
			$args['related_to']->ID, $taxonomy, [
				'fields' => 'ids',
			]
		);

		if ( ! $terms ) {
			continue;
		}

		$args['tax_query'][] = [
			'taxonomy' => $taxonomy,
			'field'    => 'term_id',
			'terms'    => $terms,
		];
	}

	if ( 1 === count( $args['tax_query'] ) ) {
		$args['include'] = -1; // NOTE Force empty results with a bogus include.
	}

	return $args;
}

/**
 * Set default arguments for get_webcomics_list().
 *
 * @param array $args The arguments to filter.
 * @param array $comics The comics included in the list.
 * @return array
 */
function hook_get_webcomics_list_args( array $args, array $comics ) : array {
	$args = [
		'cloud_count' => [],
		'cloud_floor' => 0,
		'cloud_step'  => 0,
		'options'     => false,
	] + $args + [
		'cloud_max' => 0,
		'cloud_min' => 0,
		'current'   => 0,
		'format'    => ', ',
		'link_args' => [],
		'link'      => '%title',
		'walker'    => '',
	];

	if ( preg_match( '/<(?:select|optgroup).*?>.*{{.*}}/', $args['format'] ) ) {
		$args['feed']      = '';
		$args['options']   = true;
		$args['cloud_min'] = 0;
		$args['cloud_max'] = 0;
	}

	if ( $args['cloud_min'] && $args['cloud_max'] && $args['cloud_min'] < $args['cloud_max'] ) {
		foreach ( $comics as $comic ) {
			$args['cloud_count'][ $comic ] = wp_count_comments( $comic )->approved;
		}

		$args['cloud_floor'] = min( $args['cloud_count'] );
		$args['cloud_step']  = ( $args['cloud_max'] - $args['cloud_min'] ) / max( 1, max( $args['cloud_count'] ) - $args['cloud_floor'] );
	}

	if ( ! class_exists( $args['walker'] ) ) {
		$args['walker'] = __NAMESPACE__ . '\Walker\ComicLister';
	}

	return $args;
}

/* ===== Utility Functions ================================================== */

/**
 * Get object media tokens.
 *
 * @param array $objects The objects to get media tokens for.
 * @return array
 */
function util_get_media_tokens( array $objects ) : array {
	$tokens = [];
	$sizes  = array_merge( [ 'full' ], get_intermediate_image_sizes() );

	foreach ( $objects as $prefix => $object ) {
		if ( ! $object ) {
			continue;
		} elseif ( ! is_string( $prefix ) ) {
			$prefix = '';
		}

		$object = (array) $object;

		foreach ( $sizes as $size ) {
			$tokens[ "%{$prefix}{$size}" ] = '';

			foreach ( $object as $obj ) {
				$tokens[ "%{$prefix}{$size}" ] .= wp_get_attachment_image( $obj, $size );
			}
		}
	}

	return $tokens;
}

/**
 * Get taxonomies for a get_webcomic_collections() related_to argument.
 *
 * @param mixed $related_to The related to argument; may be a collection ID,
 * post object, or term object.
 * @return array
 * @internal For hook_get_webcomic_collections().
 */
function util_get_taxonomies( $related_to ) : array {
	$taxonomies = [];

	if ( is_string( $related_to ) && webcomic_collection_exists( $related_to ) ) {
		$taxonomies = get_object_taxonomies( $related_to );
	} elseif ( $related_to instanceof WP_Post ) {
		$taxonomies = get_object_taxonomies( $related_to );

		foreach ( $taxonomies as $key => $taxonomy ) {
			if ( false !== strpos( $taxonomy, "{$related_to->post_type}_" ) || count( wp_get_object_terms( $related_to->ID, $taxonomy ) ) ) {
				continue;
			}

			unset( $taxonomies[ $key ] );
		}

		array_unshift( $taxonomies, "{$related_to->post_type}_" );
	} elseif ( $related_to instanceof WP_Term ) {
		$taxonomies = [ substr( $related_to->taxonomy, 0, strpos( $related_to->taxonomy, '_' ) + 1 ) ];

		foreach ( get_objects_in_term( $related_to->term_id, $related_to->taxonomy ) as $object ) {
			$taxonomies[] = get_post_type( $object ) . '_';
		}
	}

	return array_unique( $taxonomies );
}

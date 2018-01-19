<?php
/**
 * Commerce filters
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Commerce;

use WP_Post;

/**
 * Add filters.
 *
 * @return void
 */
function filters() {
	add_filter( 'get_webcomic_prints', __NAMESPACE__ . '\hook_get_webcomic_prints_hide_empty_infinite', 0, 2 );
	add_filter( 'get_webcomic_prints', __NAMESPACE__ . '\hook_get_webcomic_prints_in_not_in', 0, 2 );
	add_filter( 'get_webcomic_prints', __NAMESPACE__ . '\hook_get_webcomic_prints_adjust_min_max', 0, 2 );
	add_filter( 'get_webcomic_prints', __NAMESPACE__ . '\hook_get_webcomic_prints_base_min_max', 0, 2 );
	add_filter( 'get_webcomic_prints', __NAMESPACE__ . '\hook_get_webcomic_prints_left_min_max', 0, 2 );
	add_filter( 'get_webcomic_prints', __NAMESPACE__ . '\hook_get_webcomic_prints_price_min_max', 0, 2 );
	add_filter( 'get_webcomic_prints', __NAMESPACE__ . '\hook_get_webcomic_prints_sold_min_max', 0, 2 );
	add_filter( 'get_webcomic_prints', __NAMESPACE__ . '\hook_get_webcomic_prints_stock_min_max', 0, 2 );
	add_filter( 'get_webcomic_prints_list_args', __NAMESPACE__ . '\hook_get_webcomic_prints_list_args', 0, 2 );
	add_filter( 'get_webcomic_collection_link_tokens', __NAMESPACE__ . '\hook_get_webcomic_collection_link_tokens', 10, 3 );
	add_filter( 'get_webcomic_url', __NAMESPACE__ . '\hook_get_webcomic_url', 10, 4 );
	add_filter( 'get_webcomic_link_tokens', __NAMESPACE__ . '\hook_get_webcomic_link_tokens', 10, 3 );
	add_filter( 'get_webcomic_link_class', __NAMESPACE__ . '\hook_get_webcomic_link_class', 10, 3 );
}

/**
 * Handle hide_empty and hide_infinite arguments for get_webcomic_prints.
 *
 * @param array $prints The prints to filter.
 * @param array $args The get_webcomic_prints() arguments.
 * @return array
 */
function hook_get_webcomic_prints_hide_empty_infinite( array $prints, array $args ) {
	if ( empty( $args['hide_empty'] ) && empty( $args['hide_infinite'] ) ) {
		return $prints;
	}

	foreach ( $prints as $slug => $print ) {
		if ( $args['hide_empty'] && $print['stock'] && ! $print['left'] ) {
			unset( $prints[ $slug ] );
		} elseif ( $args['hide_infinite'] && ! $print['stock'] ) {
			unset( $prints[ $slug ] );
		}
	}

	return $prints;
}

/**
 * Handle *__in and *__not_in arguments for get_webcomic_prints.
 *
 * @param array $prints The prints to filter.
 * @param array $args The get_webcomic_prints() arguments.
 * @return array
 */
function hook_get_webcomic_prints_in_not_in( array $prints, array $args ) : array {
	if ( empty( $args['slug__in'] ) && empty( $args['slug__not_in'] ) ) {
		return $prints;
	}

	$args  += [
		'slug__in'     => [],
		'slug__not_in' => [],
	];
	$output = [];

	foreach ( $prints as $print ) {
		if ( $args['slug__in'] && ! in_array( $print['slug'], $args['slug__in'], true ) ) {
			continue;
		} elseif ( $args['slug__not_in'] && in_array( $print['slug'], $args['slug__not_in'], true ) ) {
			continue;
		}

		$output[ $print['slug'] ] = $print;
	}

	return $output;
}

/**
 * Handle adjust_min and adjust_max arguments for get_webcomic_prints.
 *
 * @param array $prints The prints to filter.
 * @param array $args The get_webcomic_prints() arguments.
 * @return array
 */
function hook_get_webcomic_prints_adjust_min_max( array $prints, array $args ) : array {
	if ( ! ( isset( $args['adjust_min'] ) || isset( $args['adjust_max'] ) ) ) {
		return $prints;
	}

	$args  += [
		'adjust_min' => -100,
		'adjust_max' => null,
	];
	$output = [];

	foreach ( $prints as $print ) {
		if ( $print['adjust'] < $args['adjust_min'] ) {
			continue;
		} elseif ( null !== $args['adjust_max'] && $print['adjust'] > $args['adjust_max'] ) {
			continue;
		}

		$output[ $print['slug'] ] = $print;
	}

	return $output;
}

/**
 * Handle base_min and base_max arguments for get_webcomic_prints.
 *
 * @param array $prints The prints to filter.
 * @param array $args The get_webcomic_prints() arguments.
 * @return array
 */
function hook_get_webcomic_prints_base_min_max( array $prints, array $args ) : array {
	if ( ! ( isset( $args['base_min'] ) || isset( $args['base_max'] ) ) ) {
		return $prints;
	}

	$args  += [
		'base_min' => 0,
		'base_max' => null,
	];
	$output = [];

	foreach ( $prints as $print ) {
		if ( $print['base'] < $args['base_min'] ) {
			continue;
		} elseif ( null !== $args['base_max'] && $print['base'] > $args['base_max'] ) {
			continue;
		}

		$output[ $print['slug'] ] = $print;
	}

	return $output;
}

/**
 * Handle left_min and left_max arguments for get_webcomic_prints.
 *
 * @param array $prints The prints to filter.
 * @param array $args The get_webcomic_prints() arguments.
 * @return array
 */
function hook_get_webcomic_prints_left_min_max( array $prints, array $args ) : array {
	if ( ! ( isset( $args['left_min'] ) || isset( $args['left_max'] ) ) ) {
		return $prints;
	}

	$args  += [
		'left_min' => 0,
		'left_max' => null,
	];
	$output = [];

	foreach ( $prints as $print ) {
		if ( $print['stock'] && $print['left'] < $args['left_min'] ) {
			continue;
		} elseif ( $print['stock'] && null !== $args['left_max'] && $print['left'] > $args['left_max'] ) {
			continue;
		}

		$output[ $print['slug'] ] = $print;
	}

	return $output;
}

/**
 * Handle price_min and price_max arguments for get_webcomic_prints.
 *
 * @param array $prints The prints to filter.
 * @param array $args The get_webcomic_prints() arguments.
 * @return array
 */
function hook_get_webcomic_prints_price_min_max( array $prints, array $args ) : array {
	if ( ! ( isset( $args['price_min'] ) || isset( $args['price_max'] ) ) ) {
		return $prints;
	}

	$args  += [
		'price_min' => 0,
		'price_max' => null,
	];
	$output = [];

	foreach ( $prints as $print ) {
		if ( $print['price'] < $args['price_min'] ) {
			continue;
		} elseif ( null !== $args['price_max'] && $print['price'] > $args['price_max'] ) {
			continue;
		}

		$output[ $print['slug'] ] = $print;
	}

	return $output;
}

/**
 * Handle sold_min and sold_max arguments for get_webcomic_prints.
 *
 * @param array $prints The prints to filter.
 * @param array $args The get_webcomic_prints() arguments.
 * @return array
 */
function hook_get_webcomic_prints_sold_min_max( array $prints, array $args ) : array {
	if ( ! ( isset( $args['sold_min'] ) || isset( $args['sold_max'] ) ) ) {
		return $prints;
	}

	$args  += [
		'sold_min' => 0,
		'sold_max' => null,
	];
	$output = [];

	foreach ( $prints as $print ) {
		if ( $print['sold'] < $args['sold_min'] ) {
			continue;
		} elseif ( null !== $args['sold_max'] && $print['sold'] > $args['sold_max'] ) {
			continue;
		}

		$output[ $print['slug'] ] = $print;
	}

	return $output;
}

/**
 * Handle stock_min and stock_max arguments for get_webcomic_prints.
 *
 * @param array $prints The prints to filter.
 * @param array $args The get_webcomic_prints() arguments.
 * @return array
 */
function hook_get_webcomic_prints_stock_min_max( array $prints, array $args ) : array {
	if ( ! ( isset( $args['stock_min'] ) || isset( $args['stock_max'] ) ) ) {
		return $prints;
	}

	$args  += [
		'stock_min' => 0,
		'stock_max' => null,
	];
	$output = [];

	foreach ( $prints as $print ) {
		if ( $print['stock'] && $print['stock'] < $args['stock_min'] ) {
			continue;
		} elseif ( null !== $args['stock_max'] && $print['stock'] > $args['stock_max'] ) {
			continue;
		}

		$output[ $print['slug'] ] = $print;
	}

	return $output;
}

/**
 * Set default arguments for get_webcomic_prints_list().
 *
 * @param array $args The arguments to filter.
 * @param array $prints The prints included in the list.
 * @return array
 */
function hook_get_webcomic_prints_list_args( array $args, array $prints ) : array {
	$args = [
		'cloud_count' => [],
		'cloud_floor' => 0,
		'cloud_step'  => 0,
		'options'     => false,
	] + $args + [
		'cloud_max' => 0,
		'cloud_min' => 0,
		'cloud_val' => 'sold',
		'format'    => ', ',
		'link_args' => [],
		'link'      => '%print-name - %print-price %print-currency',
		'post'      => null,
		'walker'    => '',
	];

	if ( preg_match( '/<(?:select|optgroup).*?>.*{{.*}}/', $args['format'] ) ) {
		$args['options']   = true;
		$args['cloud_min'] = 0;
		$args['cloud_max'] = 0;
	}

	if ( $args['cloud_min'] && $args['cloud_max'] && $args['cloud_min'] < $args['cloud_max'] && isset( current( $prints )[ $args['cloud_val'] ] ) ) {
		foreach ( $prints as $print ) {
			$args['cloud_count'][ $print['slug'] ] = $print[ $args['cloud_val'] ];
		}

		$args['cloud_floor'] = min( $args['cloud_count'] );
		$args['cloud_step']  = ( $args['cloud_max'] - $args['cloud_min'] ) / max( 1, max( $args['cloud_count'] ) - $args['cloud_floor'] );
	}

	if ( ! class_exists( $args['walker'] ) ) {
		$args['walker'] = __NAMESPACE__ . '\Walker\PrintLister';
	}

	return $args;
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
	} elseif ( false !== strpos( $link, '%print-currency' ) ) {
		$tokens['%print-currency'] = webcomic( "option.{$collection}.commerce_currency" );
	}

	$prints = array_keys( webcomic( "option.{$collection}.commerce_prints" ) );

	foreach ( $prints as $print ) {
		if ( false !== strpos( $link, "%{$print}-print-price" ) ) {
			$tokens[ "%{$print}-print-price" ] = get_webcomic_collection_print_price( $print, $collection );
		}

		if ( false !== strpos( $link, "%{$print}-print-name" ) ) {
			$tokens[ "%{$print}-print-name" ] = get_webcomic_collection_print_name( $print, $collection );
		}

		if ( false !== strpos( $link, "%{$print}-print-stock" ) ) {
			$tokens[ "%{$print}-print-stock" ] = get_webcomic_collection_print_stock( $print, $collection );
		}
	}

	return $tokens;
}

/**
 * Add a print parameter to a comic URL.
 *
 * @param string  $url The comic URL.
 * @param WP_Post $comic The comic the URL points to.
 * @param array   $args Optional arguments.
 * @param mixed   $post Optional reference post.
 * @return string
 */
function hook_get_webcomic_url( string $url, WP_Post $comic, array $args, $post ) : string {
	if ( empty( $args['print'] ) ) {
		return $url;
	} elseif ( ! has_webcomic_print( $args['print'], $comic ) ) {
		return '';
	}

	if ( get_option( 'rewrite_rules' ) ) {
		return trailingslashit( $url ) . "print/{$args['print']}";
	}

	return add_query_arg(
		[
			'print' => $args['print'],
		], $url
	);
}

/**
 * Handle default webcomic commerce link tokens.
 *
 * @param array   $tokens The token values.
 * @param string  $link The link text to search for tokens.
 * @param WP_Post $comic The comic the link is for.
 * @return array
 * @SuppressWarnings(PHPMD.NPathComplexity) - We're purposely using a lot of conditionals here to avoid executing functions we don't need to.
 * @codingStandardsIgnoreStart Generic.Metrics.CyclomaticComplexity.TooHigh - We're purposely using a lot of conditionals here to avoid executing functions we don't need to.
 */
function hook_get_webcomic_link_tokens( array $tokens, string $link, WP_Post $comic ) : array {
	if ( ! preg_match( '/%\S/', $link ) ) {
		return $tokens;
	} elseif ( false !== strpos( $link, '%print-currency' ) ) {
		$tokens['%print-currency'] = webcomic( "option.{$comic->post_type}.commerce_currency" );
	}

	$prints = get_post_meta( $comic->ID, 'webcomic_commerce_prints' );

	foreach ( $prints as $print ) {
		if ( false !== strpos( $link, "%{$print}-print-adjust" ) ) {
			$tokens[ "%{$print}-print-adjust" ] = get_webcomic_print_adjust( $print, $comic );
		}

		if ( false !== strpos( $link, "%{$print}-print-base" ) ) {
			$tokens[ "%{$print}-print-base" ] = get_webcomic_collection_print_price( $print, $comic->post_type );
		}

		if ( false !== strpos( $link, "%{$print}-print-left" ) ) {
			$tokens[ "%{$print}-print-left" ] = get_webcomic_print_left( $print, $comic );
		}

		if ( false !== strpos( $link, "%{$print}-print-name" ) ) {
			$tokens[ "%{$print}-print-name" ] = get_webcomic_collection_print_name( $print, $comic->post_type );
		}

		if ( false !== strpos( $link, "%{$print}-print-price" ) ) {
			$tokens[ "%{$print}-print-price" ] = get_webcomic_print_price( $print, $comic );
		}

		if ( false !== strpos( $link, "%{$print}-print-sold" ) ) {
			$tokens[ "%{$print}-print-sold" ] = get_webcomic_print_sold( $print, $comic );
		}

		if ( false !== strpos( $link, "%{$print}-print-stock" ) ) {
			$tokens[ "%{$print}-print-stock" ] = get_webcomic_collection_print_stock( $print, $comic->post_type );

			if ( ! $tokens[ "%{$print}-stock" ] ) {
				$tokens[ "%{$print}-print-stock" ] = '&infin;';
			}
		}
	}

	return $tokens;
} // @codingStandardsIgnoreEnd Generic.Metrics.CyclomaticComplexity.TooHigh

/**
 * Add webcomic commerce link classes.
 *
 * @param array   $class The CSS classes.
 * @param array   $args Optional arguments.
 * @param WP_Post $comic The comic the link is for.
 * @return array
 */
function hook_get_webcomic_link_class( array $class, array $args, WP_Post $comic ) : array {
	if ( empty( $args['print'] ) ) {
		return $class;
	} elseif ( in_array( 'current-webcomic', $class, true ) ) {
		$key = array_search( 'current-webcomic', $class, true );

		unset( $class[ $key ], $class[ $key + 1 ] );
	}

	$class[] = 'webcomic-print-link';
	$class[] = "{$comic->post_type}-print-link";

	return $class;
}

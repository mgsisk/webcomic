<?php
/**
 * Deprecated filters
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Compat;

use WP_Post;

/**
 * Add filters.
 *
 * @return void
 */
function filters() {
	add_filter( 'get_webcomic_url', __NAMESPACE__ . '\hook_get_webcomic_url', 10, 4 );
}

/**
 * Add a prints parameter to a comic URL.
 *
 * @param string  $url The comic URL.
 * @param WP_Post $comic The comic the URL points to.
 * @param array   $args Optional arguments.
 * @param mixed   $post Optional reference post.
 * @return string
 */
function hook_get_webcomic_url( string $url, WP_Post $comic, array $args, $post ) : string {
	if ( empty( $args['prints'] ) ) {
		return $url;
	}

	if ( get_option( 'rewrite_rules' ) ) {
		return trailingslashit( $url ) . 'prints';
	}

	return add_query_arg(
		[
			'prints' => '',
		], $url
	);
}

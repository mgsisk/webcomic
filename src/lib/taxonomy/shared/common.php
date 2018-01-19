<?php
/**
 * Shared taxonomy common functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Taxonomy\Shared;

use const Mgsisk\Webcomic\Taxonomy\ENDPOINT;

/**
 * Add rewrite tokens.
 *
 * @param string $type The taxonomy type.
 * @param array  $tokens The rewrite tokens.
 * @return array
 */
function add_rewrite_tokens( string $type, array $tokens ) : array {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		$tokens[ "%{$collection}_{$type}%" ] = '(.+)';
	}

	return $tokens;
}

/**
 * Register taxonomies.
 *
 * @param string $type The taxonomy type.
 * @param array  $args The taxonomy arguments.
 * @return void
 * @suppress PhanUndeclaredConstant - ENDPOINT incorrectly triggers this.
 */
function register_taxonomy( string $type, array $args ) {
	$name = $args['labels']['name'];

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		// Translators: 1: Custom post-type name. 2: Plural taxonomy name.
		$args['labels']['name']    = sprintf( __( '%1$s %2$s', 'webcomic' ), webcomic( "option.{$collection}.name" ), $name );
		$args['show_admin_column'] = true;
		$args['hierarchical']      = webcomic( "option.{$collection}.{$type}_hierarchical" );
		$args['rewrite']           = [
			'slug'         => webcomic( "option.{$collection}.{$type}_slug" ),
			'with_front'   => false,
			'hierarchical' => webcomic( "option.{$collection}.{$type}_hierarchical" ),
			'ep_mask'      => ENDPOINT,
		];

		\register_taxonomy( "{$collection}_{$type}", [], $args );
	}
}

<?php
/**
 * Page functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

use WP;

/**
 * Add page hooks.
 *
 * @return void
 */
function page() {
	add_filter( 'parse_request', __NAMESPACE__ . '\hook_filter_pages_by_collection' );
	add_filter( 'restrict_manage_posts', __NAMESPACE__ . '\hook_add_page_collection_restriction' );
	add_filter( 'manage_pages_columns', __NAMESPACE__ . '\hook_add_page_collection_column' );
	add_filter( 'manage_pages_custom_column', __NAMESPACE__ . '\hook_display_page_collection_column', 10, 2 );
}

/**
 * Filter the pages list admin page to show only pages in a collection.
 *
 * @param WP $request A list of request paramters.
 * @return object
 */
function hook_filter_pages_by_collection( WP $request ) {
	if ( empty( $request->query_vars['post_type'] ) || 'page' !== $request->query_vars['post_type'] || 'webcomic_collection' !== webcomic( 'GLOBALS._REQUEST.meta_key' ) || ! webcomic( 'GLOBALS._REQUEST.meta_value' ) ) {
		return $request;
	}

	$request->query_vars['meta_query'][] = [
		'key'   => 'webcomic_collection',
		'value' => sanitize_key( webcomic( 'GLOBALS._REQUEST.meta_value' ) ),
	];

	return $request;
}

/**
 * Add a collection select for the pages list admin page navigation.
 *
 * @param string $type The current post type.
 * @return void
 */
function hook_add_page_collection_restriction( string $type ) {
	if ( 'page' !== $type ) {
		return;
	}

	$all = esc_html__( 'All Webcomic Collections', 'webcomic' );

	webcomic_collections_list(
		[
			'format'     => "<select name='meta_value'><option value=''>{$all}</option>{{}}</select>",
			'hide_empty' => false,
		]
	);

	echo '<input type="hidden" name="meta_key" value="webcomic_collection">'; // WPCS: xss ok.
}

/**
 * Add the collection column to the pages list admin page.
 *
 * @param array $columns The posts list columns.
 * @return array
 */
function hook_add_page_collection_column( array $columns ) : array {
	$pre                        = array_slice( $columns, 0, 3 );
	$pre['webcomic_collection'] = __( 'Webcomic Collection', 'webcomic' );

	return $pre + $columns;
}

/**
 * Display the page collection column.
 *
 * @param string $column The column currently being displayed.
 * @param int    $post The current post ID.
 * @return void
 */
function hook_display_page_collection_column( string $column, int $post ) {
	if ( 'webcomic_collection' !== $column ) {
		return;
	}

	$output     = '&mdash;';
	$collection = get_post_meta( $post, 'webcomic_collection', true );

	if ( $collection ) {
		$url    = esc_url(
			add_query_arg(
				[
					'meta_key'   => 'webcomic_collection',
					'post_type'  => 'page',
					'meta_value' => $collection,
				], admin_url( 'edit.php' )
			)
		);
		$name   = esc_html( webcomic( "option.{$collection}.name" ) );
		$output = "<a href='{$url}'>{$name}</a>";
	}

	echo $output; // WPCS: xss ok.
}

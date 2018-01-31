<?php
/**
 * Common location functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Location;

use function Mgsisk\Webcomic\Taxonomy\Shared\add_rewrite_tokens;
use function Mgsisk\Webcomic\Taxonomy\Shared\register_taxonomy;

/**
 * Add common hooks.
 *
 * @return void
 */
function common() {
	add_filter( 'webcomic_rewrite_tokens', __NAMESPACE__ . '\hook_add_rewrite_tokens' );
	add_filter( 'init', __NAMESPACE__ . '\hook_register_taxonomy' );
	add_filter( 'widgets_init', __NAMESPACE__ . '\hook_register_widgets' );
}

/**
 * Add rewrite tokens.
 *
 * @param array $tokens The rewrite tokens.
 * @return array
 */
function hook_add_rewrite_tokens( array $tokens ) : array {
	return add_rewrite_tokens( 'location', $tokens );
}

/**
 * Register taxonomies.
 *
 * @return void
 */
function hook_register_taxonomy() {
	register_taxonomy(
		'location', [
			'labels'      => [
				'name'                       => __( 'Locations', 'webcomic' ),
				'singular_name'              => __( 'Location', 'webcomic' ),
				'menu_name'                  => __( 'Locations', 'webcomic' ),
				'all_items'                  => __( 'All Locations', 'webcomic' ),
				'edit_item'                  => __( 'Edit Location', 'webcomic' ),
				'view_item'                  => __( 'View Location', 'webcomic' ),
				'update_item'                => __( 'Update Location', 'webcomic' ),
				'add_new_item'               => __( 'Add New Location', 'webcomic' ),
				'new_item_name'              => __( 'New Location Name', 'webcomic' ),
				'search_items'               => __( 'Search Locations', 'webcomic' ),
				'popular_items'              => __( 'Popular Locations', 'webcomic' ),
				'separate_items_with_commas' => __( 'Separate locations with commas', 'webcomic' ),
				'add_or_remove_items'        => __( 'Add or remove locations', 'webcomic' ),
				'choose_from_most_used'      => __( 'Choose from the most used locations', 'webcomic' ),
				'not_found'                  => __( 'No locations found.', 'webcomic' ),
			],
			'description' => __( 'Allows you to assign locations to comics.', 'webcomic' ),
		]
	);
}

/**
 * Register location widgets.
 *
 * @return void
 */
function hook_register_widgets() {
	register_widget( __NAMESPACE__ . '\Widget\FirstWebcomicLocationLink' );
	register_widget( __NAMESPACE__ . '\Widget\LastWebcomicLocationLink' );
	register_widget( __NAMESPACE__ . '\Widget\NextWebcomicLocationLink' );
	register_widget( __NAMESPACE__ . '\Widget\PreviousWebcomicLocationLink' );
	register_widget( __NAMESPACE__ . '\Widget\RandomWebcomicLocationLink' );
	register_widget( __NAMESPACE__ . '\Widget\WebcomicLocationLink' );
	register_widget( __NAMESPACE__ . '\Widget\WebcomicLocationsList' );
}

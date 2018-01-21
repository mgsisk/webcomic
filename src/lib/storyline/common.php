<?php
/**
 * Common storyline functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Storyline;

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
	return add_rewrite_tokens( 'storyline', $tokens );
}

/**
 * Register taxonomies.
 *
 * @return void
 */
function hook_register_taxonomy() {
	register_taxonomy(
		'storyline', [
			'labels'      => [
				'name'                       => __( 'Storylines', 'webcomic' ),
				'singular_name'              => __( 'Storyline', 'webcomic' ),
				'menu_name'                  => __( 'Storylines', 'webcomic' ),
				'all_items'                  => __( 'All Storylines', 'webcomic' ),
				'edit_item'                  => __( 'Edit Storyline', 'webcomic' ),
				'view_item'                  => __( 'View Storyline', 'webcomic' ),
				'update_item'                => __( 'Update Storyline', 'webcomic' ),
				'add_new_item'               => __( 'Add New Storyline', 'webcomic' ),
				'new_item_name'              => __( 'New Storyline Name', 'webcomic' ),
				'search_items'               => __( 'Search Storylines', 'webcomic' ),
				'popular_items'              => __( 'Popular Storylines', 'webcomic' ),
				'separate_items_with_commas' => __( 'Separate storylines with commas', 'webcomic' ),
				'add_or_remove_items'        => __( 'Add or remove storylines', 'webcomic' ),
				'choose_from_most_used'      => __( 'Choose from the most used storylines', 'webcomic' ),
				'not_found'                  => __( 'No storylines found.', 'webcomic' ),
			],
			'description' => __( 'Allows you to assign storylines to comics.', 'webcomic' ),
		]
	);
}

/**
 * Register storyline widgets.
 *
 * @return void
 */
function hook_register_widgets() {
	register_widget( __NAMESPACE__ . '\Widget\FirstWebcomicStorylineLink' );
	register_widget( __NAMESPACE__ . '\Widget\LastWebcomicStorylineLink' );
	register_widget( __NAMESPACE__ . '\Widget\NextWebcomicStorylineLink' );
	register_widget( __NAMESPACE__ . '\Widget\PreviousWebcomicStorylineLink' );
	register_widget( __NAMESPACE__ . '\Widget\RandomWebcomicStorylineLink' );
	register_widget( __NAMESPACE__ . '\Widget\WebcomicStorylineLink' );
	register_widget( __NAMESPACE__ . '\Widget\WebcomicStorylinesList' );
}

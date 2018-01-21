<?php
/**
 * Common character functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Character;

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
	return add_rewrite_tokens( 'character', $tokens );
}

/**
 * Register taxonomies.
 *
 * @return void
 */
function hook_register_taxonomy() {
	register_taxonomy(
		'character', [
			'labels'      => [
				'name'                       => __( 'Characters', 'webcomic' ),
				'singular_name'              => __( 'Character', 'webcomic' ),
				'menu_name'                  => __( 'Characters', 'webcomic' ),
				'all_items'                  => __( 'All Characters', 'webcomic' ),
				'edit_item'                  => __( 'Edit Character', 'webcomic' ),
				'view_item'                  => __( 'View Character', 'webcomic' ),
				'update_item'                => __( 'Update Character', 'webcomic' ),
				'add_new_item'               => __( 'Add New Character', 'webcomic' ),
				'new_item_name'              => __( 'New Character Name', 'webcomic' ),
				'search_items'               => __( 'Search Characters', 'webcomic' ),
				'popular_items'              => __( 'Popular Characters', 'webcomic' ),
				'separate_items_with_commas' => __( 'Separate characters with commas', 'webcomic' ),
				'add_or_remove_items'        => __( 'Add or remove characters', 'webcomic' ),
				'choose_from_most_used'      => __( 'Choose from the most used characters', 'webcomic' ),
				'not_found'                  => __( 'No characters found.', 'webcomic' ),
			],
			'description' => __( 'Allows you to assign characters to comics.', 'webcomic' ),
		]
	);
}

/**
 * Register character widgets.
 *
 * @return void
 */
function hook_register_widgets() {
	register_widget( __NAMESPACE__ . '\Widget\FirstWebcomicCharacterLink' );
	register_widget( __NAMESPACE__ . '\Widget\LastWebcomicCharacterLink' );
	register_widget( __NAMESPACE__ . '\Widget\NextWebcomicCharacterLink' );
	register_widget( __NAMESPACE__ . '\Widget\PreviousWebcomicCharacterLink' );
	register_widget( __NAMESPACE__ . '\Widget\RandomWebcomicCharacterLink' );
	register_widget( __NAMESPACE__ . '\Widget\WebcomicCharacterLink' );
	register_widget( __NAMESPACE__ . '\Widget\WebcomicCharactersList' );
}

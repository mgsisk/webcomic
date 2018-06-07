<?php
/**
 * Location meta functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Location;

use function Mgsisk\Webcomic\Taxonomy\Shared\add_taxonomy_field;

/**
 * Add meta hooks.
 *
 * @return void
 */
function meta() {
	$namespace = str_replace( 'Location', 'Taxonomy\Shared', __NAMESPACE__ );

	add_filter( 'restrict_manage_posts', __NAMESPACE__ . '\hook_add_taxonomy_field', 10, 2 );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "create_{$collection}_location", "{$namespace}\hook_add_media_meta", 10, 2 );
		add_filter( "create_{$collection}_location", "{$namespace}\hook_add_order_meta", 10, 2 );
		add_filter( "edit_{$collection}_location", "{$namespace}\hook_update_media_meta", 10, 2 );
		add_filter( "{$collection}_location_add_form_fields", "{$namespace}\hook_add_media_field" );
		add_filter( "{$collection}_location_edit_form_fields", "{$namespace}\hook_edit_media_field", 10, 2 );
		add_filter( "manage_edit-{$collection}_location_columns", "{$namespace}\hook_add_media_column" );
		add_filter( "manage_{$collection}_location_custom_column", "{$namespace}\hook_display_media_column", 10, 3 );
	}
}

/**
 * Add a taxonomy select for comic list admin page navigation.
 *
 * @param string $post_type The current post type.
 * @return void
 */
function hook_add_taxonomy_field( string $post_type ) {
	add_taxonomy_field( 'location', $post_type );
}

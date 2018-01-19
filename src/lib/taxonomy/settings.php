<?php
/**
 * Taxonomy settings functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Taxonomy;

/**
 * Add settings hooks.
 *
 * @return void
 */
function settings() {
	add_filter( 'admin_menu', __NAMESPACE__ . '\hook_remove_admin_pages', 999 );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_help_sidebar' );
}

/**
 * Remove extraneous taxonomy page links.
 *
 * The submenu for a collection could easily get unwiedly with several
 * taxonomies enabled, so we remove any that aren't for taxonomies with the
 * collection's prefix.
 *
 * @return void
 */
function hook_remove_admin_pages() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		foreach ( webcomic( "option.{$collection}.taxonomies" ) as $taxonomy ) {
			if ( 0 === strpos( $taxonomy, $collection ) ) {
				continue;
			}

			remove_submenu_page( "edit.php?post_type={$collection}", "edit-tags.php?taxonomy={$taxonomy}&amp;post_type={$collection}" );
		}
	}
}

/**
 * Add the help sidebar.
 *
 * @return void
 */
function hook_add_help_sidebar() {
	$screen = get_current_screen();

	if ( ! preg_match( '/^edit-webcomic\d+_/', $screen->id ) ) {
		return;
	}

	$screen->set_help_sidebar( webcomic_help() );
}

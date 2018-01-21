<?php
/**
 * Character settings functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Character;

use function Mgsisk\Webcomic\Taxonomy\Shared\activate;
use function Mgsisk\Webcomic\Taxonomy\Shared\deactivate;
use function Mgsisk\Webcomic\Taxonomy\Shared\add_allowed_options;
use function Mgsisk\Webcomic\Taxonomy\Shared\new_collection_taxonomy;
use function Mgsisk\Webcomic\Taxonomy\Shared\add_sorter_page;
use function Mgsisk\Webcomic\Taxonomy\Shared\add_settings_section;
use function Mgsisk\Webcomic\Taxonomy\Shared\add_field_slug;
use function Mgsisk\Webcomic\Taxonomy\Shared\add_field_behavior;
use function Mgsisk\Webcomic\Taxonomy\Shared\remove_sorter_submenu_page;
use function Mgsisk\Webcomic\Taxonomy\Shared\add_overview_help;
use function Mgsisk\Webcomic\Taxonomy\Shared\add_settings_help;
use function Mgsisk\Webcomic\Taxonomy\Shared\sanitize_field_slug;
use function Mgsisk\Webcomic\Taxonomy\Shared\sanitize_field_behavior;

/**
 * Add settings hooks.
 *
 * @return void
 */
function settings() {
	$namespace = str_replace( 'Character', 'Taxonomy\Shared', __NAMESPACE__ );

	add_filter( 'webcomic_activate_character', __NAMESPACE__ . '\hook_activate' );
	add_filter( 'webcomic_deactivate_character', __NAMESPACE__ . '\hook_deactivate' );
	add_filter( 'webcomic_collection_allowed_options', __NAMESPACE__ . '\hook_add_allowed_options' );
	add_filter( 'webcomic_new_collection', __NAMESPACE__ . '\hook_new_collection_taxonomy' );
	add_filter( 'admin_menu', __NAMESPACE__ . '\hook_add_sorter_page' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_settings_section' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_slug' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_behavior' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_remove_sorter_submenu_page' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_overview_help' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_settings_help' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_slug' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_behavior' );
		add_filter( "{$collection}_character_pre_add_form", "{$namespace}\hook_add_sorter_link" );
	}
}

/**
 * Activate a taxonomy component.
 *
 * @return void
 */
function hook_activate() {
	activate( 'character' );
}

/**
 * Deactivate a taxonomy component.
 *
 * @return void
 */
function hook_deactivate() {
	deactivate( 'character' );
}

/**
 * Add the taxonomy allowed collection options.
 *
 * @param array $allowed The allowed options.
 * @return array
 */
function hook_add_allowed_options( array $allowed ) : array {
	return add_allowed_options( 'character', $allowed );
}

/**
 * Update new collection's chara settings.
 *
 * @param array $defaults The default settings of the new collection.
 * @return array
 */
function hook_new_collection_taxonomy( array $defaults ) : array {
	return new_collection_taxonomy( 'character', $defaults );
}

/**
 * Add the sorting page.
 *
 * @return void
 */
function hook_add_sorter_page() {
	add_sorter_page( 'character' );
}

/**
 * Add the settings section.
 *
 * @return void
 */
function hook_add_settings_section() {
	add_settings_section( 'character' );
}

/**
 * Add the slug setting field.
 *
 * @return void
 */
function hook_add_field_slug() {
	add_field_slug( 'character' );
}

/**
 * Add the behavior setting field.
 *
 * @return void
 */
function hook_add_field_behavior() {
	add_field_behavior( 'character' );
}

/**
 * Remove extraneous taxonomy sorting page links.
 *
 * @return void
 */
function hook_remove_sorter_submenu_page() {
	remove_sorter_submenu_page( 'character' );
}

/**
 * Add the overview help tab.
 *
 * @return void
 */
function hook_add_overview_help() {
	add_overview_help( 'character', __NAMESPACE__ );
}

/**
 * Add the settings help tab.
 *
 * @return void
 */
function hook_add_settings_help() {
	add_settings_help( 'character', __NAMESPACE__ );
}

/* ===== Collection Hooks =================================================== */

/**
 * Sanitize the slug field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_slug( array $options ) : array {
	return sanitize_field_slug( 'character', $options );
}

/**
 * Sanitize the behavior field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_behavior( array $options ) : array {
	return sanitize_field_behavior( 'character', $options );
}

/* ===== Callbacks ========================================================== */

/**
 * Require help overview.
 *
 * @return void
 */
function call_add_overview_help() {
	require __DIR__ . '/settings-inc-help-overview.php';
}

/**
 * Require settings help.
 *
 * @return void
 */
function call_add_settings_help() {
	require __DIR__ . '/settings-inc-help.php';
}

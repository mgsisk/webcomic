<?php
/**
 * Table record functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

/**
 * Add record hooks.
 *
 * @return void
 */
function record() {
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_record_styles' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_record_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_localize_record_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_record_styles' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_record_scripts' );
}

/**
 * Register record stylesheets.
 *
 * @return void
 */
function hook_register_record_styles() {
	wp_register_style(
		__NAMESPACE__ . 'RecordCSS',
		plugins_url( 'srv/collection/record.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);
}

/**
 * Register record scripts.
 *
 * @return void
 */
function hook_register_record_scripts() {
	wp_register_script(
		__NAMESPACE__ . 'RecordJS',
		plugins_url( 'srv/collection/record.js', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' ),
		true
	);
}

/**
 * Localize search scripts.
 *
 * @return void
 */
function hook_localize_record_scripts() {
	wp_localize_script(
		__NAMESPACE__ . 'RecordJS',
		'webcomicRecordL10n',
		[
			'add'    => __( 'Add new item', 'webcomic' ),
			'delete' => __( 'Delete item', 'webcomic' ),
		]
	);
}

/**
 * Enqueue record styles.
 *
 * @return void
 */
function hook_enqueue_record_styles() {
	/**
	 * Alter table record enqueing.
	 *
	 * This filter allows hooks to enqueue the shared table record functionality.
	 *
	 * @param bool $enqueue Wether to enqueue the table record functionality.
	 */
	$enqueue = apply_filters( 'webcomic_enqueue_table_record', false );

	if ( ! $enqueue ) {
		return;
	}

	wp_enqueue_style( __NAMESPACE__ . 'RecordCSS' );
}

/**
 * Enqueue record scripts.
 *
 * @return void
 */
function hook_enqueue_record_scripts() {
	/* This filter is documented in Mgsisk\Webcomic\Collection\hook_enqueue_record_styles() */
	if ( ! apply_filters( 'webcomic_enqueue_table_record', false ) ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'RecordJS' );
}

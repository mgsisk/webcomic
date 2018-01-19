<?php
/**
 * Comic term sorter functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Taxonomy;

/**
 * Add sorter hooks.
 *
 * @return void
 */
function sorter() {
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_sort_terms' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_sorter_styles' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_sorter_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_sorter_styles' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_sorter_scripts' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_sorter_help_sidebar' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_sorter_help' );
}

/**
 * Sort comic terms.
 *
 * @return void
 */
function hook_sort_terms() {
	if ( null === webcomic( 'GLOBALS._REQUEST.webcomic_sorter.sort' ) || null === webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . '\SharedSorterNonce' ) ) {
		return;
	}

	check_admin_referer( __NAMESPACE__ . '\SharedSorterNonce', __NAMESPACE__ . '\SharedSorterNonce' );

	$error = 0;

	foreach ( (array) webcomic( 'GLOBALS._REQUEST.webcomic_term_order' ) as $term => $order ) {
		$term   = abs( (int) $term );
		$order  = abs( (int) $order );
		$update = wp_update_term(
			$term, get_term( $term )->taxonomy, [
				'parent' => abs( (int) webcomic( "GLOBALS._REQUEST.webcomic_term_parent.{$term}" ) ),
			]
		);

		if ( is_wp_error( $update ) ) {
			$error++;

			continue;
		}

		$update = update_term_meta( $term, 'webcomic_order', $order );

		if ( is_wp_error( $update ) ) {
			$error++;
		}
	}

	webcomic_notice( __( 'Sorting saved.', 'webcomic' ), 'success' );

	if ( $error ) {
		// Translators: The number of terms not sorted.
		webcomic_notice( sprintf( _n( '%s term not updated.', '%s terms not updated.', $error, 'webcomic' ), number_format_i18n( $error ) ), 'error' );
	}
}

/**
 * Register stylesheets.
 *
 * @return void
 */
function hook_register_sorter_styles() {
	wp_register_style(
		__NAMESPACE__ . 'SorterCSS',
		plugins_url( 'srv/taxonomy/sorter.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);
}

/**
 * Register javascript.
 *
 * @return void
 */
function hook_register_sorter_scripts() {
	wp_register_script(
		'jquery-mjs-nestedsortable',
		plugins_url( 'srv/taxonomy/jquery.mjs.nestedSortable.js', webcomic( 'file' ) ),
		[ 'jquery-ui-sortable' ],
		webcomic( 'option.version' ),
		true
	);

	wp_register_script(
		__NAMESPACE__ . 'SorterJS',
		plugins_url( 'srv/taxonomy/sorter.js', webcomic( 'file' ) ),
		[ 'jquery-mjs-nestedsortable' ],
		webcomic( 'option.version' ),
		true
	);
}

/**
 * Enqueue sorting stylesheets.
 *
 * @return void
 */
function hook_enqueue_sorter_styles() {
	if ( ! preg_match( '/^webcomic\d+_page_sort_/', get_current_screen()->id ) ) {
		return;
	}

	wp_enqueue_style( __NAMESPACE__ . 'SorterCSS' );
}

/**
 * Enqueue sorting scripts.
 *
 * @return void
 */
function hook_enqueue_sorter_scripts() {
	if ( ! preg_match( '/^webcomic\d+_page_sort_/', get_current_screen()->id ) ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'SorterJS' );
}

/**
 * Add the help sidebar.
 *
 * @return void
 */
function hook_add_sorter_help_sidebar() {
	$screen = get_current_screen();

	if ( ! preg_match( '/^webcomic\d+_page_sort_/', $screen->id ) ) {
		return;
	}

	$screen->set_help_sidebar( webcomic_help() );
}

/**
 * Add the sorter help.
 *
 * @return void
 */
function hook_add_sorter_help() {
	$screen = get_current_screen();

	if ( ! preg_match( '/^webcomic\d+_page_sort_/', $screen->id ) ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'overview',
			'title'    => __( 'Overview', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/sorter-inc-help.php';
			},
		]
	);
}

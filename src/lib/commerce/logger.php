<?php
/**
 * Comic IPN logger functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Commerce;

/**
 * Add comic IPN logger hooks.
 *
 * @return void
 */
function logger() {
	add_filter( 'admin_menu', __NAMESPACE__ . '\hook_add_logger_page' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_empty_logger' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_logger_styles' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_logger_styles' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_logger_help_sidebar' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_logger_help' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_logger_tool_help' );
	add_filter( 'tool_box', __NAMESPACE__ . '\hook_add_logger_tool' );
}

/**
 * Add the IPN logger page.
 *
 * @return void
 */
function hook_add_logger_page() {
	add_management_page(
		__( 'Webcomic IPN Log', 'webcomic' ),
		__( 'Webcomic IPN Log', 'webcomic' ),
		'manage_options',
		'webcomic_commerce_logger',
		function() {
			$old_ipns = get_posts(
				[
					'fields'         => 'ids',
					'post_status'    => 'any',
					'post_type'      => 'webcomic_commerce',
					'posts_per_page' => -1,
				]
			);

			if ( $old_ipns ) {
				foreach ( $old_ipns as $old_ipn ) {
					wp_update_post(
						[
							'ID'        => $old_ipn,
							'post_type' => 'webcomic_ipn',
						]
					);
				}
			}

			$args = [
				'file'   => __DIR__ . '/logger-inc-page.php',
				'posts'  => get_posts(
					[
						'fields'         => 'ids',
						'post_status'    => 'any',
						'post_type'      => 'webcomic_ipn',
						'posts_per_page' => -1,
					]
				),
				'submit' => get_submit_button(
					__( 'Empty IPN Log', 'webcomic' ), 'secondary', 'webcomic_commerce_logger[empty]', false, [
						'form' => 'webcomic_commerce_logger',
					]
				),
			];

			require $args['file'];
		}
	);
}

/**
 * Empty comic IPN logger.
 *
 * @return void
 */
function hook_empty_logger() {
	if ( ! webcomic( 'GLOBALS._REQUEST.webcomic_commerce_logger.empty' ) ) {
		return;
	}

	check_admin_referer( __NAMESPACE__ . 'LoggerNonce', __NAMESPACE__ . 'LoggerNonce' );

	$error = 0;
	$posts = get_posts(
		[
			'fields'         => 'ids',
			'post_status'    => 'any',
			'post_type'      => 'webcomic_ipn',
			'posts_per_page' => -1,
		]
	);

	foreach ( $posts as $post ) {
		if ( ! wp_delete_post( $post, true ) ) {
			$error++;
		}
	}

	if ( $error ) {
		// Translators: The number of IPN logs not deleted.
		webcomic_notice( sprintf( _n( '%s log could not be deleted.', '%s logs could not be deleted.', $error, 'webcomic' ), number_format_i18n( $error ) ), 'error' );
	}

	$count = count( $posts ) - $error;

	// Translators: The number of IPN logs deleted.
	webcomic_notice( sprintf( _n( '%s log deleted.', '%s logs deleted.', $count, 'webcomic' ), number_format_i18n( $count ) ) );
}

/**
 * Register comic IPN logger stylesheets.
 *
 * @return void
 */
function hook_register_logger_styles() {
	wp_register_style(
		__NAMESPACE__ . 'LoggerCSS',
		plugins_url( 'srv/commerce/logger.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);
}

/**
 * Enqueue stylesheets.
 *
 * @return void
 */
function hook_enqueue_logger_styles() {
	if ( 'tools_page_webcomic_commerce_logger' !== get_current_screen()->id ) {
		return;
	}

	wp_enqueue_style( __NAMESPACE__ . 'LoggerCSS' );
}

/**
 * Add the help sidebar.
 *
 * @return void
 */
function hook_add_logger_help_sidebar() {
	$screen = get_current_screen();

	if ( 'tools_page_webcomic_commerce_logger' !== $screen->id ) {
		return;
	}

	$screen->set_help_sidebar( webcomic_help() );
}

/**
 * Add the comic IPN logger help.
 *
 * @return void
 */
function hook_add_logger_help() {
	$screen = get_current_screen();

	if ( 'tools_page_webcomic_commerce_logger' !== $screen->id ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'overview',
			'title'    => __( 'Overview', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/logger-inc-help.php';
			},
		]
	);
}

/**
 * Add the comic IPN logger tool box help.
 *
 * @return void
 */
function hook_add_logger_tool_help() {
	$screen = get_current_screen();

	if ( 'tools' !== $screen->id ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'webcomic-logger',
			'title'    => __( 'Webcomic IPN Log', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/logger-inc-tool-help.php';
			},
		]
	);
}

/**
 * Add the comic IPN logger tool box.
 *
 * @return void
 */
function hook_add_logger_tool() {
	require __DIR__ . '/logger-inc-tool.php';
}

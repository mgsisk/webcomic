<?php
/**
 * Comic media matcher functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

/**
 * Add comic media matcher hooks.
 *
 * @return void
 */
function matcher() {
	add_filter( 'admin_menu', __NAMESPACE__ . '\hook_add_matcher_page' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_matcher_settings_section' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_matcher_field_collection' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_matcher_field_match_post' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_matcher_field_post_date' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_matcher_field_post_custom' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_matcher_field_match_media' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_matcher_field_exact_match' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_save_matches' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_matcher_styles' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_matcher_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_matcher_styles' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_matcher_scripts' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_matcher_help_sidebar' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_matcher_help' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_matcher_tool_help' );
	add_filter( 'tool_box', __NAMESPACE__ . '\hook_add_matcher_tool' );
}

/**
 * Add the media matcher page.
 *
 * @return void
 */
function hook_add_matcher_page() {
	add_management_page(
		__( 'Webcomic Matcher', 'webcomic' ),
		__( 'Webcomic Matcher', 'webcomic' ),
		'manage_options',
		'webcomic_matcher',
		function() {
			$args = [
				'file'        => __DIR__ . '/matcher-inc-page.php',
				'post_date'   => webcomic( 'GLOBALS._REQUEST.webcomic_matcher.post_date' ),
				'collection'  => webcomic( 'GLOBALS._REQUEST.webcomic_matcher.collection' ),
				'match_post'  => webcomic( 'GLOBALS._REQUEST.webcomic_matcher.match_post' ),
				'match_media' => webcomic( 'GLOBALS._REQUEST.webcomic_matcher.match_media' ),
				'post_custom' => webcomic( 'GLOBALS._REQUEST.webcomic_matcher.post_custom' ),
				'exact_match' => webcomic( 'GLOBALS._REQUEST.webcomic_matcher.exact_match' ),
			];

			if ( ! $args['post_date'] ) {
				$args['post_date'] = 'Y-m-d';
			}

			require $args['file'];
		}
	);
}

/**
 * Add the settings section.
 *
 * @return void
 */
function hook_add_matcher_settings_section() {
	add_settings_section(
		'webcomic_matcher',
		'',
		function() {
			echo '<div>';

			wp_nonce_field( __NAMESPACE__ . 'MatcherNonce', __NAMESPACE__ . 'MatcherNonce' );

			echo '</div>';
		},
		'webcomic_matcher'
	);
}

/**
 * Add the matcher collection field.
 *
 * @return void
 */
function hook_add_matcher_field_collection() {
	add_settings_field(
		'webcomic_matcher_collection',
		__( 'Collection', 'webcomic' ),
		function( $args ) {
			require $args['file'];
		},
		'webcomic_matcher',
		'webcomic_matcher', [
			'file'      => __DIR__ . '/matcher-inc-field-collection.php',
			'option'    => webcomic( 'GLOBALS._REQUEST.webcomic_matcher.collection' ),
			'label_for' => 'webcomic_matcher[collection]',
		]
	);
}

/**
 * Add the matcher match post field.
 *
 * @return void
 */
function hook_add_matcher_field_match_post() {
	add_settings_field(
		'webcomic_matcher_match_post',
		__( 'Match comic&hellip;', 'webcomic' ),
		function( $args ) {
			require $args['file'];
		},
		'webcomic_matcher',
		'webcomic_matcher', [
			'file'      => __DIR__ . '/matcher-inc-field-match-post.php',
			'option'    => webcomic( 'GLOBALS._REQUEST.webcomic_matcher.match_post' ),
			'label_for' => 'webcomic_matcher[match_post]',
		]
	);
}

/**
 * Add the matcher post date field.
 *
 * @return void
 */
function hook_add_matcher_field_post_date() {
	$option = webcomic( 'GLOBALS._REQUEST.webcomic_matcher.post_date' );

	if ( ! $option ) {
		$option = get_option( 'date_format' );
	}

	add_settings_field(
		'webcomic_matcher_post_date',
		__( 'Date Format', 'webcomic' ),
		function( $args ) {
			require $args['file'];
		},
		'webcomic_matcher',
		'webcomic_matcher', [
			'file'      => __DIR__ . '/matcher-inc-field-post-date.php',
			'option'    => $option,
			'label_for' => 'webcomic_matcher[post_date]',
		]
	);
}

/**
 * Add the matcher post custom field.
 *
 * @return void
 */
function hook_add_matcher_field_post_custom() {
	add_settings_field(
		'webcomic_matcher_post_custom',
		__( 'Custom Field', 'webcomic' ),
		function( $args ) {
			require $args['file'];
		},
		'webcomic_matcher',
		'webcomic_matcher', [
			'file'      => __DIR__ . '/matcher-inc-field-post-custom.php',
			'option'    => webcomic( 'GLOBALS._REQUEST.webcomic_matcher.post_custom' ),
			'label_for' => 'webcomic_matcher[post_custom]',
		]
	);
}

/**
 * Add the matcher match media field.
 *
 * @return void
 */
function hook_add_matcher_field_match_media() {
	add_settings_field(
		'webcomic_matcher_match_media',
		__( 'With media&hellip;', 'webcomic' ),
		function( $args ) {
			require $args['file'];
		},
		'webcomic_matcher',
		'webcomic_matcher', [
			'file'      => __DIR__ . '/matcher-inc-field-match-media.php',
			'option'    => webcomic( 'GLOBALS._REQUEST.webcomic_matcher.match_media' ),
			'label_for' => 'webcomic_matcher[match_media]',
		]
	);
}

/**
 * Add the matcher exact match field.
 *
 * @return void
 */
function hook_add_matcher_field_exact_match() {
	add_settings_field(
		'webcomic_matcher_exact_match',
		'',
		function( $args ) {
			require $args['file'];
		},
		'webcomic_matcher',
		'webcomic_matcher', [
			'file'      => __DIR__ . '/matcher-inc-field-exact-match.php',
			'option'    => webcomic( 'GLOBALS._REQUEST.webcomic_matcher.exact_match' ),
			'label_for' => 'webcomic_matcher[exact_match]',
		]
	);
}

/**
 * Save media matches.
 *
 * @return void
 */
function hook_save_matches() {
	if ( ! webcomic( 'GLOBALS._REQUEST.webcomic_matcher.save' ) ) {
		return;
	}

	check_admin_referer( __NAMESPACE__ . 'MatcherNonce', __NAMESPACE__ . 'MatcherNonce' );

	if ( ! webcomic( 'GLOBALS._REQUEST.webcomic_match' ) ) {
		webcomic_notice( __( 'Please select at least one match to save.', 'webcomic' ), 'warning' );

		return;
	}

	$count = 0;
	$error = 0;

	foreach ( webcomic( 'GLOBALS._REQUEST.webcomic_match' ) as $match ) {
		list( $post, $media ) = array_map( 'intval', explode( '-', $match ) );

		if ( ! add_post_meta( $post, 'webcomic_media', $media ) ) {
			$error++;

			continue;
		}

		$count++;
	}

	if ( $error ) {
		// Translators: The number of comic/media matches not saved.
		webcomic_notice( sprintf( _n( '%s match not saved.', '%s matches not saved.', $error, 'webcomic' ), number_format_i18n( $error ) ), 'error' );
	}

	// Translators: The number of comic/media matches saved.
	webcomic_notice( sprintf( _n( '%s match saved.', '%s matches saved.', $count, 'webcomic' ), number_format_i18n( $count ) ) );
}

/**
 * Register matcher stylesheets.
 *
 * @return void
 */
function hook_register_matcher_styles() {
	wp_register_style(
		__NAMESPACE__ . 'MatcherCSS',
		plugins_url( 'srv/collection/matcher.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);
}

/**
 * Register javascript.
 *
 * @return void
 */
function hook_register_matcher_scripts() {
	wp_register_script(
		__NAMESPACE__ . 'MatcherJS',
		plugins_url( 'srv/collection/matcher.js', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' ),
		true
	);
}

/**
 * Enqueue stylesheets.
 *
 * @return void
 */
function hook_enqueue_matcher_styles() {
	if ( 'tools_page_webcomic_matcher' !== get_current_screen()->id ) {
		return;
	}

	wp_enqueue_style( __NAMESPACE__ . 'MatcherCSS' );
}

/**
 * Enqueue javascript.
 *
 * @return void
 */
function hook_enqueue_matcher_scripts() {
	if ( 'tools_page_webcomic_matcher' !== get_current_screen()->id ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'MatcherJS' );
}

/**
 * Add the help sidebar.
 *
 * @return void
 */
function hook_add_matcher_help_sidebar() {
	$screen = get_current_screen();

	if ( 'tools_page_webcomic_matcher' !== $screen->id ) {
		return;
	}

	$screen->set_help_sidebar( webcomic_help() );
}

/**
 * Add the media matcher help.
 *
 * @return void
 */
function hook_add_matcher_help() {
	$screen = get_current_screen();

	if ( 'tools_page_webcomic_matcher' !== $screen->id ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'overview',
			'title'    => __( 'Overview', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/matcher-inc-help.php';
			},
		]
	);
}

/**
 * Add the media matcher tool box help.
 *
 * @return void
 */
function hook_add_matcher_tool_help() {
	$screen = get_current_screen();

	if ( 'tools' !== $screen->id ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'webcomic-matcher',
			'title'    => __( 'Webcomic Matcher', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/matcher-inc-tool-help.php';
			},
		]
	);
}

/**
 * Add the media matcher tool box.
 *
 * @return void
 */
function hook_add_matcher_tool() {
	require __DIR__ . '/matcher-inc-tool.php';
}

<?php
/**
 * Authors metabox functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe\MetaBox;

use WP_Screen;

/**
 * Add authors metabox hooks.
 *
 * @return void
 */
function authors() {
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_authors_styles' );
	add_filter( 'current_screen', __NAMESPACE__ . '\hook_enqueue_record_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_authors_styles' );
	add_filter( 'save_post_webcomic_transcript', __NAMESPACE__ . '\hook_update_post_meta_authors' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_box_authors_help' );
	add_filter( 'add_meta_boxes_webcomic_transcript', __NAMESPACE__ . '\hook_add_box_authors' );
}

/**
 * Register authors stylesheets.
 *
 * @return void
 */
function hook_register_authors_styles() {
	wp_register_style(
		__NAMESPACE__ . 'AuthorsCSS',
		plugins_url( 'srv/transcribe/metabox-authors.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);
}

/**
 * Enqueue the table record scripts.
 *
 * @param WP_Screen $screen The current screen.
 * @return void
 */
function hook_enqueue_record_scripts( WP_Screen $screen ) {
	if ( 'webcomic_transcript' !== $screen->id ) {
		return;
	}

	add_filter( 'webcomic_enqueue_table_record', '__return_true' );
}

/**
 * Enqueue authors meta box stylesheets.
 *
 * @return void
 */
function hook_enqueue_authors_styles() {
	if ( 'webcomic_transcript' !== get_current_screen()->id ) {
		return;
	}

	wp_enqueue_style( __NAMESPACE__ . 'AuthorsCSS' );
}

/**
 * Update post authors meta.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_post_meta_authors( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'AuthorsNonce' ) ), __NAMESPACE__ . 'AuthorsNonce' ) ) {
		return;
	}

	$new_authors = [];
	$old_authors = get_post_meta( $id, 'webcomic_transcript_authors' );
	$now         = (int) current_time( 'timestamp' );
	$names       = array_map( 'sanitize_text_field', webcomic( 'GLOBALS._REQUEST.webcomic_transcript_authors.name' ) );
	$emails      = array_map( 'sanitize_email', webcomic( 'GLOBALS._REQUEST.webcomic_transcript_authors.email' ) );
	$urls        = array_map( 'esc_url_raw', webcomic( 'GLOBALS._REQUEST.webcomic_transcript_authors.url' ) );
	$times       = array_map( 'strtotime', webcomic( 'GLOBALS._REQUEST.webcomic_transcript_authors.time' ) );
	$ips         = array_map(
		function( $value ) {
				return filter_var( $value, FILTER_VALIDATE_IP );
		}, webcomic( 'GLOBALS._REQUEST.webcomic_transcript_authors.ip' )
	);

	foreach ( $names as $key => $name ) {
		if ( ! $name ) {
			continue;
		} elseif ( ! $times[ $key ] ) {
			$times[ $key ] = $now;
		}

		$author = [
			'name'  => $name,
			'email' => $emails[ $key ],
			'url'   => $urls[ $key ],
			'time'  => date( 'Y-m-d H:i:s', $times[ $key ] ),
			'ip'    => $ips[ $key ],
		];

		$new_authors[ crc32( wp_json_encode( $author ) ) ] = $author;
	}

	if ( util_compare_transcript_authors( $old_authors, $new_authors ) ) {
		return;
	}

	foreach ( $old_authors as $author ) {
		delete_post_meta( $id, 'webcomic_transcript_authors', $author );
	}

	foreach ( $new_authors as $author ) {
		add_post_meta( $id, 'webcomic_transcript_authors', $author );
	}
}

/**
 * Add the help tab.
 *
 * @return void
 */
function hook_add_box_authors_help() {
	$screen = get_current_screen();

	if ( 'webcomic_transcript' !== $screen->id ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'webcomic_authors',
			'title'    => __( 'Authors', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/authors-inc-help.php';
			},
		]
	);
}

/**
 * Display the authors meta box.
 *
 * @return void
 */
function hook_add_box_authors() {
	add_meta_box(
		str_replace( '\\', '_', __NAMESPACE__ ) . 'Authors',
		__( 'Authors', 'webcomic' ),
		function( $post ) {
			$user = wp_get_current_user();
			$args = [
				'file'       => __DIR__ . '/authors-inc-box.php',
				'nonce'      => __NAMESPACE__ . 'AuthorsNonce',
				'option'     => get_post_meta( $post->ID, 'webcomic_transcript_authors' ),
				'user_name'  => $user->display_name,
				'user_email' => $user->user_email,
				'user_url'   => $user->user_url,
			];

			require $args['file'];
		},
		'webcomic_transcript',
		'normal',
		'high'
	);
}

/* ===== Utility Functions ================================================== */

/**
 * Compare transcript authors.
 *
 * @param array $old_authors The old transcript authors.
 * @param array $new_authors The new transcript authors.
 * @return bool
 * @internal For hook_update_post_meta_authors().
 */
function util_compare_transcript_authors( $old_authors, $new_authors ) : bool {
	$authors = [];

	foreach ( $old_authors as $author ) {
		$authors[ crc32( wp_json_encode( $author ) ) ] = true;
	}

	foreach ( $new_authors as $author ) {
		$key = crc32( wp_json_encode( $author ) );

		if ( isset( $authors[ $key ] ) ) {
			unset( $authors[ $key ] );

			continue;
		}

		$authors[ $key ] = true;
	}

	return ! $authors;
}

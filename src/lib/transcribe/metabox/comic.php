<?php
/**
 * Comic metabox functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe\MetaBox;

use WP_Screen;

/**
 * Add comic metabox hooks.
 *
 * @return void
 */
function comic() {
	add_filter( 'webcomic_search_info', __NAMESPACE__ . '\hook_comic_search_info' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_comic_styles' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_comic_scripts' );
	add_filter( 'current_screen', __NAMESPACE__ . '\hook_enqueue_comic_search' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_localize_comic_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_comic_styles' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_comic_scripts' );
	add_filter( 'save_post_webcomic_transcript', __NAMESPACE__ . '\hook_update_comic_transcribe' );
	add_filter( 'delete_post', __NAMESPACE__ . '\hook_delete_comic' );
	add_filter( 'delete_post', __NAMESPACE__ . '\hook_update_transcript_counts' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_box_comic_help' );
	add_filter( 'add_meta_boxes_webcomic_transcript', __NAMESPACE__ . '\hook_add_box_comic' );
	add_filter( 'wp_ajax_webcomic_transcript_comic_search', __NAMESPACE__ . '\hook_transcript_comic_search' );
}

/**
 * Alter the comic search info.
 *
 * @param string $info The comic search info.
 * @return string
 */
function hook_comic_search_info( string $info ) : string {
	if ( 'webcomic_transcript' !== get_current_screen()->id ) {
		return $info;
	}

	return __( 'Search for and select a comic to assign this transcript to it.', 'webcomic' );
}

/**
 * Register comic stylesheets.
 *
 * @return void
 */
function hook_register_comic_styles() {
	wp_register_style(
		__NAMESPACE__ . 'ComicCSS',
		plugins_url( 'srv/transcribe/metabox-comic.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);
}

/**
 * Register comic javascript.
 *
 * @return void
 */
function hook_register_comic_scripts() {
	wp_register_script(
		__NAMESPACE__ . 'ComicJS',
		plugins_url( 'srv/transcribe/metabox-comic.js', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' ),
		true
	);
}

/**
 * Enqueue the comic search tool.
 *
 * @param WP_Screen $screen The current screen.
 * @return void
 */
function hook_enqueue_comic_search( WP_Screen $screen ) {
	if ( 'webcomic_transcript' !== $screen->id ) {
		return;
	}

	add_filter( 'webcomic_enqueue_comic_search', '__return_true' );
}

/**
 * Localize comic scripts.
 *
 * @return void
 */
function hook_localize_comic_scripts() {
	wp_localize_script(
		__NAMESPACE__ . 'ComicJS',
		'webcomicTranscriptL10n',
		[
			'allow' => __( 'Allow transcripts', 'webcomic' ),
		]
	);
}

/**
 * Enqueue comic meta box stylesheets.
 *
 * @return void
 */
function hook_enqueue_comic_styles() {
	if ( 'webcomic_transcript' !== get_current_screen()->id ) {
		return;
	}

	wp_enqueue_style( __NAMESPACE__ . 'ComicCSS' );
}

/**
 * Enqueue comic meta box javascript.
 *
 * @return void
 */
function hook_enqueue_comic_scripts() {
	if ( 'webcomic_transcript' !== get_current_screen()->id ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'ComicJS' );
}

/**
 * Update parent comic transcribe setting.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_comic_transcribe( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'ComicNonce' ) ), __NAMESPACE__ . 'ComicNonce' ) ) {
		return;
	}

	$comic = wp_get_post_parent_id( $id );

	if ( ! $comic ) {
		return;
	} elseif ( ! webcomic( 'GLOBALS._REQUEST.webcomic_transcribe' ) ) {
		delete_post_meta( $comic, 'webcomic_transcribe' );

		return;
	}

	update_post_meta( $comic, 'webcomic_transcribe', true );
}

/**
 * Delete transcripts when their parent comic is deleted.
 *
 * @param int $post The post being delete.
 * @return void
 */
function hook_delete_comic( int $post ) {
	if ( ! is_a_webcomic( $post ) ) {
		return;
	}

	$transcripts = get_posts(
		[
			'fields'      => 'ids',
			'post_type'   => 'webcomic_transcript',
			'post_parent' => $post,
			'post_status' => 'any',
		]
	);

	foreach ( $transcripts as $transcript ) {
		wp_delete_post( $transcript, true );
	}
}

/**
 * Update transcript counts for comics when a transcript is deleted.
 *
 * @param int $id The post being deleted.
 * @return void
 */
function hook_update_transcript_counts( int $id ) {
	if ( ! is_a_webcomic_transcript( $id ) ) {
		return;
	}

	$comic = wp_get_post_parent_id( $id );

	if ( ! $comic ) {
		return;
	}

	$status = get_post_status( $id );
	$count  = (int) get_post_meta( $comic, "webcomic_transcribe_count_{$status}", true ) - 1;

	update_post_meta( $comic, "webcomic_transcribe_count_{$status}", $count );

	if ( 0 >= $count ) {
		delete_post_meta( $comic, "webcomic_transcribe_count_{$status}" );
	}
}

/**
 * Add the help tab.
 *
 * @return void
 */
function hook_add_box_comic_help() {
	$screen = get_current_screen();

	if ( 'webcomic_transcript' !== $screen->id ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'webcomic_parent',
			'title'    => __( 'Webcomic', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/comic-inc-help.php';
			},
		]
	);
}

/**
 * Display the comic meta box.
 *
 * @return void
 */
function hook_add_box_comic() {
	add_meta_box(
		str_replace( '\\', '_', __NAMESPACE__ ) . 'Comic',
		__( 'Webcomic', 'webcomic' ),
		function( $post ) {
			$args = [
				'file'              => __DIR__ . '/comic-inc-box.php',
				'nonce'             => __NAMESPACE__ . 'ComicNonce',
				'option'            => $post->post_parent,
				'option_transcribe' => (bool) get_post_meta( $post->post_parent, 'webcomic_transcribe', true ),
				'label_transcribe'  => 'webcomic_transcribe',
			];

			require $args['file'];
		},
		'webcomic_transcript',
		'side',
		'high'
	);
}

/**
 * Handle comic transcript search requests.
 *
 * @return void
 */
function hook_transcript_comic_search() {
	if ( null === webcomic( 'GLOBALS._REQUEST.post' ) ) {
		wp_die();
	}

	$output = (bool) get_post_meta( abs( (int) webcomic( 'GLOBALS._REQUEST.post' ) ), 'webcomic_transcribe', true );

	wp_send_json( $output );
}

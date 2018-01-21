<?php
/**
 * Transcribe collection functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe;

use WP;

/**
 * Add collection post type hooks.
 *
 * @return void
 */
function collection() {
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_transcripts_styles' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_transcripts_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_transcripts_styles' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_transcripts_script' );
	add_filter( 'parse_request', __NAMESPACE__ . '\hook_filter_posts_by_transcripts' );
	add_filter( 'parse_request', __NAMESPACE__ . '\hook_sort_posts_by_transcripts' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "views_edit-{$collection}", __NAMESPACE__ . '\hook_add_untranscribe_view' );
		add_filter( "manage_{$collection}_posts_columns", __NAMESPACE__ . '\hook_add_transcripts_column' );
		add_filter( "manage_{$collection}_posts_custom_column", __NAMESPACE__ . '\hook_display_transcripts_column', 10, 2 );
		add_filter( "manage_edit-{$collection}_sortable_columns", __NAMESPACE__ . '\hook_add_sortable_transcripts_column', 10, 4 );
	}
}

/**
 * Register transcripts column stylesheets.
 *
 * @return void
 */
function hook_register_transcripts_styles() {
	wp_register_style(
		__NAMESPACE__ . 'TranscriptsColumnCSS',
		plugins_url( 'srv/transcribe/column-transcripts.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);

	wp_register_style(
		__NAMESPACE__ . 'TranscriptsColumnCSSrtl',
		plugins_url( 'srv/transcribe/column-transcripts-rtl.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);
}

/**
 * Register transcripts column scripts.
 *
 * @return void
 */
function hook_register_transcripts_scripts() {
	wp_register_script(
		__NAMESPACE__ . 'TranscriptsColumnJS',
		plugins_url( 'srv/transcribe/column-transcripts.js', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' ),
		true
	);
}

/**
 * Enqueue transcripts column styles.
 *
 * @return void
 */
function hook_enqueue_transcripts_styles() {
	if ( ! preg_match( '/^edit-webcomic\d+$/', get_current_screen()->id ) ) {
		return;
	}

	wp_enqueue_style( __NAMESPACE__ . 'TranscriptsColumnCSS' );

	if ( is_rtl() ) {
		wp_enqueue_style( __NAMESPACE__ . 'TranscriptsColumnCSSrtl' );
	}
}

/**
 * Enqueue transcripts column scripts.
 *
 * @return void
 */
function hook_enqueue_transcripts_script() {
	if ( ! preg_match( '/^edit-webcomic\d+$/', get_current_screen()->id ) ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'TranscriptsColumnJS' );
}

/**
 * Filter the posts list admin page to show only untranscribed comics.
 *
 * @param WP $request A list of request paramters.
 * @return object
 */
function hook_filter_posts_by_transcripts( WP $request ) {
	if ( empty( $request->query_vars['post_type'] ) || ! webcomic( 'GLOBALS._REQUEST.webcomic_untranscribed' ) ) {
		return $request;
	}

	foreach ( get_post_stati() as $status ) {
		$request->query_vars['meta_query'][] = [
			'key'     => "webcomic_transcribe_count_{$status}",
			'compare' => 'NOT EXISTS',
		];
	}

	return $request;
}

/**
 * Sort the comics list admin page by transcripts.
 *
 * @param WP $request A list of request paramters.
 * @return object
 */
function hook_sort_posts_by_transcripts( WP $request ) {
	if ( empty( $request->query_vars['post_type'] ) || ! webcomic_collection_exists( $request->query_vars['post_type'] ) || 'webcomic_transcribe_count_publish' !== webcomic( 'GLOBALS._REQUEST.orderby' ) ) {
		return $request;
	}

	$request->query_vars['orderby']    = 'meta_value_num ID';
	$request->query_vars['meta_type']  = 'SIGNED';
	$request->query_vars['meta_query'] = [
		'relation' => 'OR',
		[
			'key'     => 'webcomic_transcribe_count_publish',
			'compare' => 'EXISTS',
		],
		[
			'key'     => 'webcomic_transcribe_count_publish',
			'compare' => 'NOT EXISTS',
		],
	];

	return $request;
}

/**
 * Add a hidden untranscribed comics field for posts list admin page navigation.
 *
 * @param string $type The current post type.
 * @return void
 */
function hook_add_untranscribed_field( string $type ) {
	if ( ! webcomic_collection_exists( $type ) || ! webcomic( 'GLOBALS._REQUEST.webcomic_untranscribed' ) ) {
		return;
	}

	echo '<input type="hidden" name="webcomic_untranscribed" vlaue="1">';
}

/* ===== Collection Hooks =================================================== */

/**
 * Add an untranscribed comics view to the posts list admin page.
 *
 * @param array $views The current view links.
 * @return array
 */
function hook_add_untranscribe_view( array $views ) : array {
	$class  = '';
	$screen = get_current_screen();
	$args   = [
		'fields'         => 'ids',
		'post_type'      => $screen->post_type,
		'meta_query'     => [
			'relation' => 'AND',
		],
		'post_status'    => 'any',
		'posts_per_page' => -1,
	];

	foreach ( get_post_stati() as $status ) {
		$args['meta_query'][] = [
			'key'     => "webcomic_transcribe_count_{$status}",
			'compare' => 'NOT EXISTS',
		];
	}

	$posts = get_posts( $args );

	if ( ! $posts ) {
		return $views;
	} elseif ( webcomic( 'GLOBALS._REQUEST.webcomic_untranscribed' ) ) {
		$class = 'current';
	}

	$url   = esc_url(
		add_query_arg(
			[
				'post_type'              => $screen->post_type,
				'post_status'            => 'any',
				'webcomic_untranscribed' => true,
			], admin_url( 'edit.php' )
		)
	);
	$count = count( $posts );

	$views['webcomic_untranscribed'] = sprintf( "<a href='{$url}' class='{$class}'>%s <span class='count'>({$count})</span></a>", __( 'Untranscribed', 'webcomic' ) );

	return $views;
}

/**
 * Add the transcripts column to the comics list admin page.
 *
 * @param array $columns The posts list columns.
 * @return array
 */
function hook_add_transcripts_column( array $columns ) : array {
	$pos = 0;

	foreach ( array_keys( $columns ) as $key ) {
		if ( 'date' === $key ) {
			break;
		}

		$pos++;
	}

	return array_slice( $columns, 0, $pos ) + [
		'webcomic_transcripts' => '<span class="dashicons dashicons-testimonial"><span class="screen-reader-text">' . __( 'Transcripts', 'webcomic' ) . '</span></span>',
	] + array_slice( $columns, $pos );
}

/**
 * Display the transcripts column.
 *
 * @param string $column The column currently being displayed.
 * @param int    $post The current post ID.
 * @return void
 */
function hook_display_transcripts_column( string $column, int $post ) {
	if ( 'webcomic_transcripts' !== $column ) {
		return;
	}

	$output = util_transcripts_bubble( $post );

	echo "<div class='post-com-count-wrapper'>{$output}</div>"; // WPCS: xss ok.
}

/**
 * Add transcripts to the sortable columns for the comics list admin page.
 *
 * @param array $columns The posts list sortable columns.
 * @return array
 */
function hook_add_sortable_transcripts_column( array $columns ) : array {
	$columns['webcomic_transcripts'] = 'webcomic_transcribe_count_publish';

	return $columns;
}

<?php
/**
 * Collection custom post type functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

use WP;

/**
 * Add custom post type hooks.
 *
 * @return void
 */
function custom() {
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_list_help_sidebar' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_list_help' );
	add_filter( 'parse_request', __NAMESPACE__ . '\hook_filter_posts_by_orphans' );
	add_filter( 'restrict_manage_posts', __NAMESPACE__ . '\hook_add_orphaned_field' );
	add_filter( 'bulk_post_updated_messages', __NAMESPACE__ . '\hook_bulk_comic_messages', 10, 2 );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "views_edit-{$collection}", __NAMESPACE__ . '\hook_add_orphans_view' );
	}
}

/**
 * Add the help sidebar.
 *
 * @return void
 */
function hook_add_list_help_sidebar() {
	$screen = get_current_screen();

	if ( ! preg_match( '/^edit-webcomic\d+$/', $screen->id ) ) {
		return;
	}

	$screen->set_help_sidebar( webcomic_help() );
}

/**
 * Add the comic list help.
 *
 * @return void
 */
function hook_add_list_help() {
	$screen = get_current_screen();

	if ( ! preg_match( '/^edit-webcomic\d+$/', $screen->id ) ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'overview',
			'title'    => __( 'Overview', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/custom-inc-list-help.php';
			},
		]
	);
}

/**
 * Filter the posts list admin page to show only orphans.
 *
 * @param WP $request A list of request paramters.
 * @return object
 */
function hook_filter_posts_by_orphans( WP $request ) {
	if ( empty( $request->query_vars['post_type'] ) || ! webcomic( 'GLOBALS._REQUEST.webcomic_orphaned' ) ) {
		return $request;
	}

	$request->query_vars['meta_query'][] = [
		'key'     => 'webcomic_media',
		'compare' => 'NOT EXISTS',
	];

	return $request;
}

/**
 * Add a hidden orphaned comics field for posts list admin page navigation.
 *
 * @param string $type The current post type.
 * @return void
 */
function hook_add_orphaned_field( string $type ) {
	if ( ! webcomic( 'GLOBALS._REQUEST.webcomic_orphaned' ) || ! webcomic_collection_exists( $type ) ) {
		return;
	}

	echo '<input type="hidden" name="webcomic_orphaned" vlaue="1">';
}

/**
 * Update bulk messages for comics.
 *
 * @param array $messages The list of bulk messages.
 * @param array $counts The bulk edited counts.
 * @return array
 */
function hook_bulk_comic_messages( array $messages, array $counts ) {
	if ( ! preg_match( '/^edit-webcomic\d+$/', get_current_screen()->id ) ) {
		return $messages;
	}

	$messages['post'] = [
		// Translators: The number of comics updated.
		'updated'   => _n( '%s comic updated.', '%s comics updated.', $counts['updated'], 'webcomic' ),
		// Translators: The number of comics not updated.
		'locked'    => _n( '%s comic not updated, somebody is editing it.', '%s comics not updated, somebody is editing them.', $counts['locked'], 'webcomic' ),
		// Translators: The number of comics permanently deleted.
		'deleted'   => _n( '%s comic permanently deleted.', '%s comics permanently deleted.', $counts['deleted'], 'webcomic' ),
		// Translators: The number of comics moved to the Trash.
		'trashed'   => _n( '%s comic moved to the Trash.', '%s comics moved to the Trash.', $counts['trashed'], 'webcomic' ),
		// Translators: The number of comics restored from the Trash.
		'untrashed' => _n( '%s comic restored from the Trash.', '%s comics restored from the Trash.', $counts['untrashed'], 'webcomic' ),
	];

	return $messages;
}

/* ===== Collection Hooks =================================================== */

/**
 * Add an orphaned comics view to the posts list admin page.
 *
 * @param array $views The current view links.
 * @return array
 */
function hook_add_orphans_view( array $views ) : array {
	$class  = '';
	$screen = get_current_screen();
	$posts  = get_posts(
		[
			'fields'         => 'ids',
			'post_type'      => $screen->post_type,
			'meta_query'     => [
				[
					'key'     => 'webcomic_media',
					'compare' => 'NOT EXISTS',
				],
			],
			'post_status'    => 'any',
			'posts_per_page' => -1,
		]
	);

	if ( ! $posts ) {
		return $views;
	} elseif ( webcomic( 'GLOBALS._REQUEST.webcomic_orphaned' ) ) {
		$class = 'current';
	}

	$url   = esc_url(
		add_query_arg(
			[
				'post_type'         => $screen->post_type,
				'post_status'       => 'any',
				'webcomic_orphaned' => true,
			], admin_url( 'edit.php' )
		)
	);
	$count = count( $posts );

	$views['webcomic_orphaned'] = sprintf( "<a href='{$url}' class='{$class}'>%s <span class='count'>({$count})</span></a>", __( 'Orphaned', 'webcomic' ) );

	return $views;
}

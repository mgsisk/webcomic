<?php
/**
 * Webcomic 2 upgrade functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Compat\Upgrade;

use WP_Term;

/**
 * Convert Webcomic 2 options to Webcomic 5 equivalents.
 *
 * @return void
 */
function v2() {
	$options = v2_options();

	update_option( 'thumbnail_size_w', $options['comic_thumb_size_w'] );
	update_option( 'thumbnail_size_h', $options['comic_thumb_size_h'] );
	update_option( 'thumbnail_crop', $options['comic_thumb_crop'] );
	update_option( 'medium_size_w', $options['comic_medium_size_w'] );
	update_option( 'medium_size_h', $options['comic_medium_size_h'] );
	update_option( 'large_size_w', $options['comic_large_size_w'] );
	update_option( 'large_size_h', $options['comic_large_size_h'] );

	update_option(
		'webcomic', [
			'version'     => $options['webcomic_version'],
			'upgrade'     => $options['webcomic_version'],
			'debug'       => false,
			'uninstall'   => false,
			'components'  => [ 'collection', 'alert', 'compat', 'storyline', 'transcribe' ],
			'collections' => array_values( $options['comic_category'] ),
			'compat'      => [
				'version' => 2,
				'upgrade' => $options,
			],
		]
	);

	v2_collections( $options );

	set_transient( 'webcomic_flush_rewrite_rules', true, 1 );
}

/**
 * Get an array of Webcomic 2 options.
 *
 * @return array
 */
function v2_options() : array {
	$increment  = 1;
	$categories = [];

	foreach ( (array) get_option( 'comic_category' ) as $category ) {
		$categories[ (int) $category ] = "webcomic{$increment}";

		$increment++;
	}

	return [
		'comic_buffer_alert'         => (int) get_option( 'comic_buffer_alert' ),
		'comic_buffer'               => (bool) get_option( 'comic_buffer' ),
		'comic_category'             => $categories,
		'comic_feed_size'            => (bool) get_option( 'comic_feed_size' ),
		'comic_feed'                 => (bool) get_option( 'comic_feed' ),
		'comic_keyboard_shortcuts'   => (bool) get_option( 'comic_keyboard_shortcuts' ),
		'comic_large_size_h'         => (int) get_option( 'comic_large_size_h' ),
		'comic_large_size_w'         => (int) get_option( 'comic_large_size_w' ),
		'comic_medium_size_h'        => (int) get_option( 'comic_medium_size_h' ),
		'comic_medium_size_w'        => (int) get_option( 'comic_medium_size_w' ),
		'comic_thumb_crop'           => (bool) get_option( 'comic_thumb_crop' ),
		'comic_thumb_size_h'         => (int) get_option( 'comic_thumb_size_h' ),
		'comic_thumb_size_w'         => (int) get_option( 'comic_thumb_size_w' ),
		'comic_transcripts_allowed'  => (bool) get_option( 'comic_transcripts_allowed' ),
		'comic_transcripts_loggedin' => (bool) get_option( 'comic_transcripts_loggedin' ),
		'comic_transcripts_required' => (bool) get_option( 'comic_transcripts_required' ),
		'webcomic_version'           => (string) get_option( 'webcomic_version' ),
	];
}

/**
 * Convert Webcomic 2 collections.
 *
 * @param array $options The options array.
 * @return void
 */
function v2_collections( array $options ) {
	foreach ( $options['comic_category'] as $category => $collection ) {
		$term               = get_term( $category );
		$collection_options = [
			'id'                => $collection,
			'name'              => $term->name,
			'slug'              => $term->slug,
			'permalink'         => $term->slug,
			'description'       => $term->description,
			'media'             => 0,
			'theme'             => '',
			'archive_date'      => true,
			'archive_reverse'   => false,
			'syndicate'         => $options['comic_feed'],
			'syndicate_preview' => $options['comic_feed_size'],
			'supports'          => [ 'title', 'editor', 'author', 'excerpt', 'comments', 'revisions', 'trackbacks', 'custom-fields' ],
			'taxonomies'        => [ 'category', 'post_tag', "{$collection}_storyline" ],
			'sidebars'          => [],
			'updated'           => '',
		];

		v2_posts( $collection_options, $collection, $category );
		v2_alert( $collection_options, $options );
		v2_storyline( $collection_options, $collection, $term );
		v2_transcribe( $collection_options, $options );

		update_option( $collection, $collection_options );
	}
}

/**
 * Convert Webcomic 2 posts to a valid collection type.
 *
 * @param array  $options The Webcomic 5 collection options array.
 * @param string $collection The collection to convert posts to.
 * @param int    $category The category to look for posts in.
 * @return void
 */
function v2_posts( array &$options, string $collection, int $category ) {
	$posts = get_posts(
		[
			'fields'         => 'ids',
			'post_status'    => get_post_stati(),
			'post_type'      => 'post',
			'posts_per_page' => -1,
			'tax_query'      => [
				[
					'taxonomy' => 'category',
					'terms'    => $category,
				],
			],
		]
	);

	if ( ! $posts || ! preg_match( '/^webcomic\d+$/', $collection ) ) {
		return;
	}

	$table = webcomic( 'GLOBALS.wpdb' )->posts;
	$posts = implode( ',', array_filter( array_map( 'intval', $posts ) ) );

	webcomic( 'GLOBALS.wpdb' )->query( "UPDATE {$table} set post_type = '{$collection}' where ID in ({$posts})" );

	$updated = webcomic( 'GLOBALS.wpdb' )->get_results( "SELECT post_date from {$table} where ID in ({$posts}) and post_status = 'publish' order by post_date desc limit 1" );

	if ( $updated ) {
		$options['updated'] = $updated;
	}
}

/**
 * Update alert options.
 *
 * @param array $options The Webcomic 5 collection options array.
 * @param array $plugin The plugin options array.
 * @return void
 */
function v2_alert( array &$options, array $plugin ) {
	$options += [
		'alert_buffer' => [],
		'alert_hiatus' => [],
	];

	if ( $plugin['comic_buffer'] && $plugin['comic_buffer_alert'] ) {
		$options['alert_buffer'][ (int) $plugin['comic_buffer_alert'] ] = get_option( 'admin_email' );
	}
}

/**
 * Update storyline options and convert Webcomic 2 chapters.
 *
 * @param array   $options The Webcomic 5 collection options array.
 * @param string  $collection The collection to check for storylines.
 * @param WP_Term $category The category collection object.
 * @return void
 */
function v2_storyline( array &$options, string $collection, WP_Term $category ) {
	$options += [
		'storyline_slug'              => "{$category->slug}-storyline",
		'storyline_sort'              => true,
		'storyline_crossovers'        => false,
		'storyline_hierarchical'      => true,
		'storyline_hierarchical_skip' => false,
	];

	$table = webcomic( 'GLOBALS.wpdb' )->term_taxonomy;
	$terms = webcomic( 'GLOBALS.wpdb' )->get_col( "SELECT term_id from {$table} where taxonomy = 'chapter' and parent = {$category->term_id}" );
	$order = 1;

	webcomic( 'GLOBALS.wpdb' )->query( "UPDATE {$table} set taxonomy = '{$collection}_storyline', parent = 0 where taxonomy = 'chapter' and parent = {$category->term_id}" );

	foreach ( $terms as $term ) {
		update_term_meta( $term, 'webcomic_order', $order );

		$order++;
	}
}

/**
 * Update transcribe options.
 *
 * @param array $options The Webcomic 5 collection options array.
 * @param array $plugin The plugin options array.
 * @return void
 */
function v2_transcribe( array &$options, array $plugin ) {
	$require = '';

	if ( $plugin['comic_transcripts_loggedin'] ) {
		$require = 'name_email';
	} elseif ( $plugin['comic_transcripts_required'] ) {
		$require = 'loggedin';
	}

	$options += [
		'transcribe_comic'     => $plugin['comic_transcripts_allowed'],
		'transcribe_close'     => 0,
		'transcribe_require'   => $require,
		'transcribe_publish'   => '',
		'transcribe_alert_pub' => $plugin['comic_transcripts_allowed'],
		'transcribe_alert_mod' => $plugin['comic_transcripts_allowed'],
	];
}

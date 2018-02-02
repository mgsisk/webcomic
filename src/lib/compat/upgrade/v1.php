<?php
/**
 * Webcomic 1 upgrade functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Compat\Upgrade;

use WP_Term;

/**
 * Convert Webcomic 1 options to Webcomic 5 equivalents.
 *
 * @return void
 */
function v1() {
	$options = v1_options();

	update_option( 'thumbnail_size_w', $options['comic_thumbnail_size_w'] );
	update_option( 'thumbnail_size_h', $options['comic_thumbnail_size_h'] );
	update_option( 'thumbnail_crop', $options['comic_thumbnail_crop'] );
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
			'components'  => [ 'collection', 'compat', 'storyline', 'transcribe' ],
			'collections' => array_values( $options['comic_category'] ),
			'compat'      => [
				'version' => 1,
				'upgrade' => $options,
			],
		]
	);

	v1_collections( $options );

	set_transient( 'webcomic_flush_rewrite_rules', true, 1 );
}

/**
 * Get an array of Webcomic 1 options.
 *
 * @return array
 */
function v1_options() : array {
	$increment  = 1;
	$categories = [];

	foreach ( (array) get_option( 'comic_category' ) as $category ) {
		$categories[ (int) $category ] = "webcomic{$increment}";

		$increment++;
	}

	return [
		'comic_category'         => $categories,
		'comic_feed_size'        => (bool) get_option( 'comic_feed_size' ),
		'comic_feed'             => (bool) get_option( 'comic_feed' ),
		'comic_large_size_h'     => (int) get_option( 'comic_large_size_h' ),
		'comic_large_size_w'     => (int) get_option( 'comic_large_size_w' ),
		'comic_medium_size_h'    => (int) get_option( 'comic_medium_size_h' ),
		'comic_medium_size_w'    => (int) get_option( 'comic_medium_size_w' ),
		'comic_thumbnail_crop'   => (bool) get_option( 'comic_thumbnail_crop' ),
		'comic_thumbnail_size_h' => (int) get_option( 'comic_thumbnail_size_h' ),
		'comic_thumbnail_size_w' => (int) get_option( 'comic_thumbnail_size_w' ),
		'comic_transcript_email' => (bool) get_option( 'comic_transcript_email' ),
		'webcomic_version'       => (string) get_option( 'webcomic_version' ),
	];
}

/**
 * Convert Webcomic 1 collections.
 *
 * @param array $options The options array.
 * @return void
 */
function v1_collections( array $options ) {
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

		v1_posts( $collection_options, $collection, $category );
		v1_storyline( $collection_options, $collection, $term );
		v1_transcribe( $collection_options, $options );

		update_option( $collection, $collection_options );
	}
}

/**
 * Convert Webcomic 1 posts to a valid collection type.
 *
 * @param array  $options The Webcomic 5 collection options array.
 * @param string $collection The collection to convert posts to.
 * @param int    $category The category to look for posts in.
 * @return void
 */
function v1_posts( array &$options, string $collection, int $category ) {
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
 * Update storyline options and convert Webcomic 1 chapters.
 *
 * @param array   $options The Webcomic 5 collection options array.
 * @param string  $collection The collection to check for storylines.
 * @param WP_Term $category The category collection object.
 * @return void
 */
function v1_storyline( array &$options, string $collection, WP_Term $category ) {
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
function v1_transcribe( array &$options, array $plugin ) {
	$options += [
		'transcribe_comic'     => $plugin['comic_transcript_email'],
		'transcribe_close'     => 0,
		'transcribe_require'   => 'name_email',
		'transcribe_publish'   => '',
		'transcribe_alert_pub' => $plugin['comic_transcript_email'],
		'transcribe_alert_mod' => $plugin['comic_transcript_email'],
	];
}

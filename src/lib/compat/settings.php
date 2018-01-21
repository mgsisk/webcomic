<?php
/**
 * Deprecated settings functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Compat;

/**
 * Add settings hooks.
 *
 * @return void
 */
function settings() {
	add_filter( 'webcomic_activate_compat', __NAMESPACE__ . '\hook_activate' );
	add_filter( 'webcomic_deactivate_compat', __NAMESPACE__ . '\hook_deactivate' );

	if ( webcomic( 'option.compat.upgrade' ) ) {
		add_filter( 'init', __NAMESPACE__ . '\hook_upgrade_v1_v2', 0 );
		add_filter( 'init', __NAMESPACE__ . '\hook_post_type_taxonomies_v3', 0 );
		add_filter( 'admin_init', __NAMESPACE__ . '\hook_upgrade_v3', 0 );
		add_filter( 'admin_init', __NAMESPACE__ . '\hook_upgrade_v4', 0 );
		add_filter( 'admin_init', __NAMESPACE__ . '\hook_upgrade', 0 );
	}

	if ( ! webcomic( 'option.compat.sizes' ) ) {
		return;
	}

	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_media_settings_section' );
	add_filter( 'whitelist_options', __NAMESPACE__ . '\hook_whitelist_media_options' );
	add_filter( 'sanitize_option_webcomic', __NAMESPACE__ . '\hook_sanitize_image_sizes' );
	add_filter( 'image_size_names_choose', __NAMESPACE__ . '\hook_add_choosable_image_sizes' );
}

/**
 * Activate the compat component.
 *
 * @return void
 */
function hook_activate() {
	set_transient( 'webcomic_flush_rewrite_rules', true, 1 );
}

/**
 * Deactivate the compat component.
 *
 * @return void
 */
function hook_deactivate() {
	set_transient( 'webcomic_flush_rewrite_rules', true, 1 );

	$table = webcomic( 'GLOBALS.wpdb' )->options;

	webcomic( 'GLOBALS.wpdb' )->query( "DELETE from {$table} where option_name like 'widget_webcomic_%' or option_name like 'widget_purchasewebcomiclink' or option_name like 'widget_recentwebcomics' or like 'widget_scheduledwebcomics'" );
}

/**
 * Finalize Webcomic 1 and Webcomic 2 upgrades.
 *
 * @return void
 * @suppress PhanAccessMethodInternal - get_terms() incorrectly triggers this.
 */
function hook_upgrade_v1_v2() {
	if ( 1 !== webcomic( 'option.compat.version' ) && 2 !== webcomic( 'option.compat.version' ) ) {
		return;
	}

	register_taxonomy(
		'chapter', 'post', [
			'hierarchical' => true,
		]
	);

	foreach ( webcomic( 'option.compat.upgrade.comic_category' ) as $category => $collection ) {
		$storylines = get_terms(
			'chapter', [
				'child_of' => $category,
				'fields'   => 'ids',
				'get'      => 'all',
			]
		);

		if ( ! $storylines ) {
			continue;
		}

		$order = 0;

		foreach ( $storylines as $storyline ) {
			update_term_meta( $storyline, 'webcomic_order', $order++ );
		}

		$table      = webcomic( 'GLOBALS.wpdb' )->term_taxonomy;
		$storylines = implode( ',', array_filter( array_map( 'intval', $storylines ) ) );
		$parents    = "0,{$storylines},{$category}";

		webcomic( 'GLOBALS.wpdb' )->query( "UPDATE {$table} set parent = 0, taxonomy = '{$collection}_storyline' where taxonomy = 'chapter' and parent in ({$parents}) and term_id in ({$storylines})" );
	}
}

/**
 * Register the Webcomic 3 post type and taxonomies.
 *
 * @return void
 */
function hook_post_type_taxonomies_v3() {
	if ( 3 !== webcomic( 'option.compat.version' ) ) {
		return;
	}

	register_post_type( 'webcomic_post' );

	register_taxonomy( 'webcomic_collection', 'webcomic_post' );

	register_taxonomy(
		'webcomic_character', 'webcomic_post', [
			'hierarchical' => true,
		]
	);

	register_taxonomy(
		'webcomic_storyline', 'webcomic_post', [
			'hierarchical' => true,
		]
	);
}

/**
 * Finalize Webcomic 3 upgrades.
 *
 * @return void
 * @suppress PhanAccessMethodInternal - get_terms() incorrectly triggers this.
 */
function hook_upgrade_v3() {
	if ( 3 !== webcomic( 'option.compat.version' ) ) {
		return;
	}

	$increment = 1;

	foreach ( array_keys( webcomic( 'option.compat.upgrade.term_meta.collection' ) ) as $id ) {
		$term               = get_term( (int) $id );
		$collection         = "webcomic{$increment}";
		$collection_options = [
			'name'        => $term->name,
			'slug'        => $term->slug,
			'permalink'   => $term->slug,
			'description' => $term->description,
		];

		update_option( $collection, $collection_options );

		util_upgrade_posts_v3( $collection, (int) $id );
		util_upgrade_terms_v3( $collection, 'character', (int) $id );
		util_upgrade_terms_v3( $collection, 'storyline', (int) $id );

		$increment++;
	}
}

/**
 * Finalize Webcomic 4 upgrades.
 *
 * @return void
 * @suppress PhanAccessMethodInternal - get_terms() incorrectly triggers this.
 */
function hook_upgrade_v4() {
	if ( 4 !== webcomic( 'option.compat.version' ) ) {
		return;
	}

	$args = [
		'fields' => 'ids',
		'get'    => 'all',
	];

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		$args['taxonomy'] = "{$collection}_character";

		$characters = get_terms( $args );

		if ( $characters ) {
			$order = 0;

			foreach ( $characters as $character ) {
				update_term_meta( $character, 'webcomic_order', $order++ );
			}
		}

		$args['taxonomy'] = "{$collection}_storyline";

		$storylines = get_terms( $args );

		if ( $storylines ) {
			$order = 0;

			foreach ( $storylines as $storyline ) {
				update_term_meta( $storyline, 'webcomic_order', $order++ );
			}
		}

		update_option(
			$collection, [
				'storyline_sort' => true,
			]
		);
	}
}

/**
 * Finalize Webcomic 1, 2, 3, and 4 upgrades.
 *
 * @return void
 */
function hook_upgrade() {
	$compat = webcomic( 'option.compat' );

	unset( $compat['upgrade'] );

	update_option(
		'webcomic', [
			'compat' => $compat,
		]
	);
}

/**
 * Add the image sizes settings section.
 *
 * @return void
 */
function hook_add_media_settings_section() {
	add_settings_section(
		'webcomic_image_sizes',
		__( 'Webcomic Image Sizes', 'webcomic' ),
		function() {
			$args = [
				'file'   => __DIR__ . '/settings-inc-section-media.php',
				'nonce'  => __NAMESPACE__ . 'MediaNonce',
				'option' => webcomic( 'option.compat.sizes' ),
			];

			require $args['file'];
		},
		'media'
	);
}

/**
 * Whitelist plugin options on the media settings page.
 *
 * @param array $options The options whitelist.
 * @return array
 */
function hook_whitelist_media_options( array $options ) : array {
	$options['media'][] = 'webcomic';

	return $options;
}

/**
 * Update the media size settings.
 *
 * @param array $options The submitted option.
 * @return array
 */
function hook_sanitize_image_sizes( $options ) {
	if ( ! webcomic( 'option.compat.sizes' ) || ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'MediaNonce' ) ), __NAMESPACE__ . 'MediaNonce' ) ) {
		return $options;
	}

	foreach ( array_filter( $options['compat']['image_size_delete'] ) as $size ) {
		unset( $options['compat']['sizes'][ $size ] );
	}

	if ( ! $options['compat']['sizes'] ) {
		unset( $options['compat']['sizes'] );
	}

	unset( $options['compat']['image_size_delete'] );

	return $options;
}

/**
 * Add additional sizes to the list of available sizes in the Media Library.
 *
 * @param array $sizes The available image sizes.
 * @return array
 */
function hook_add_choosable_image_sizes( $sizes ) {
	foreach ( array_keys( webcomic( 'option.compat.sizes' ) ) as $size ) {
		$sizes[ $size ] = ucwords( str_replace( [ '-', '_' ], ' ', $size ) );
	}

	return $sizes;
}

/* ===== Utility Functions ================================================== */

/**
 * Upgrade Webcomic 3 posts.
 *
 * @param string $collection The collection ID.
 * @param int    $id The collection term ID.
 * @return void
 * @internal For hook_upgrade_v3().
 */
function util_upgrade_posts_v3( string $collection, int $id ) {
	if ( ! preg_match( '/^webcomic\d+$/', $collection ) ) {
		return;
	}

	$posts = get_posts(
		[
			'fields'         => 'ids',
			'post_status'    => get_post_stati(),
			'post_type'      => 'webcomic_post',
			'posts_per_page' => -1,
			'tax_query'      => [
				[
					'taxonomy' => 'webcomic_collection',
					'terms'    => $id,
				],
			],
		]
	);

	if ( ! $posts ) {
		return;
	}

	$table = webcomic( 'GLOBALS.wpdb' )->posts;
	$posts = implode( ',', array_filter( array_map( 'intval', $posts ) ) );

	webcomic( 'GLOBALS.wpdb' )->query( "UPDATE {$table} set post_type = '{$collection}' where ID in ({$posts})" );

	$updated = webcomic( 'GLOBALS.wpdb' )->get_results( "SELECT post_date from {$table} where ID in ({$posts}) and post_status = 'publish' order by post_date desc limit 1" );

	if ( $updated ) {
		update_option(
			$collection, [
				'updated' => $updated,
			]
		);
	}
}

/**
 * Upgrade Webcomic 3 terms.
 *
 * @param string $collection The collection ID.
 * @param string $type The type of terms being updated.
 * @param int    $id The collection term ID.
 * @return void
 * @internal For hook_upgrade_v3().
 * @suppress PhanAccessMethodInternal - get_terms() incorrectly triggers this.
 */
function util_upgrade_terms_v3( string $collection, string $type, int $id ) {
	if ( ! preg_match( '/^webcomic\d+$/', $collection ) || ! in_array( $type, [ 'character', 'storyline' ], true ) ) {
		return;
	}

	$table = webcomic( 'GLOBALS.wpdb' )->term_taxonomy;
	$terms = get_terms(
		"webcomic_{$type}", [
			'get'    => 'all',
			'fields' => 'ids',
		]
	);

	if ( ! $terms ) {
		return;
	}

	$order = 0;

	foreach ( $terms as $key => $term ) {
		if ( get_term_field( 'term_group', $term ) !== $id ) {
			unset( $terms[ $key ] );

			continue;
		}

		update_term_meta( $term, 'webcomic_order', $order++ );
	}

	$terms = implode( ',', array_filter( array_map( 'intval', $terms ) ) );

	webcomic( 'GLOBALS.wpdb' )->query( "UPDATE {$table} set taxonomy = '{$collection}_{$type}' where taxonomy = 'webcomic_{$type}' and term_id in ({$terms})" );
}

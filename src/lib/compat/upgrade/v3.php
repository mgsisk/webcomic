<?php
/**
 * Webcomic 3 upgrade functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Compat\Upgrade;

use WP_Term;

/**
 * Convert Webcomic 3 options to Webcomic 5 equivalents.
 *
 * @return void
 */
function v3() {
	$options     = get_option( 'webcomic_options' );
	$collections = [];
	$increment   = count( $options['term_meta']['collection'] );

	while ( $increment ) {
		$collections[] = "webcomic{$increment}";

		$increment--;
	}

	update_option( 'thumbnail_size_w', $options['small_w'] );
	update_option( 'thumbnail_size_h', $options['small_h'] );
	update_option( 'medium_size_w', $options['medium_w'] );
	update_option( 'medium_size_h', $options['medium_h'] );
	update_option( 'large_size_w', $options['large_w'] );
	update_option( 'large_size_h', $options['large_h'] );

	update_option(
		'webcomic', [
			'version'     => $options['version'],
			'upgrade'     => $options['version'],
			'debug'       => false,
			'uninstall'   => false,
			'components'  => [ 'collection', 'alert', 'character', 'commerce', 'compat', 'restrict', 'storyline', 'transcribe' ],
			'collections' => array_reverse( $collections ),
			'compat'      => [
				'version' => 3,
				'upgrade' => $options,
			],
		]
	);

	v3_collections( $options );

	set_transient( 'webcomic_flush_rewrite_rules', true, 1 );
}

/**
 * Update Webcomic 3 collections.
 *
 * @param array $options The options array.
 * @return void
 */
function v3_collections( array $options ) {
	register_taxonomy( 'webcomic_collection', 'post' );

	$increment   = 1;
	$collections = [];

	foreach ( $options['term_meta']['collection'] as $id => $meta ) {
		$term = get_term( $id, 'webcomic_collection' );

		$collection         = "webcomic{$increment}";
		$collections[]      = $collection;
		$collection_options = [
			'id'                => $collection,
			'name'              => $term->name,
			'slug'              => $term->slug,
			'permalink'         => $term->slug,
			'description'       => $term->description,
			'media'             => 0,
			'theme'             => $meta['theme'] . '|' . $meta['theme'],
			'archive_date'      => true,
			'archive_reverse'   => false,
			'syndicate'         => $options['feed_toggle'],
			'syndicate_preview' => $options['feed_size'],
			'supports'          => [ 'title', 'editor', 'author', 'excerpt', 'comments', 'revisions', 'trackbacks', 'custom-fields' ],
			'taxonomies'        => [ 'categories', 'tags', "{$collection}_character", "{$collection}_storyline" ],
			'sidebars'          => [],
			'updated'           => '',
		];

		v3_alert( $collection_options, $options );
		v3_character( $collection_options, $collection, $term );
		v3_commerce( $collection_options, $options, $meta['paypal'] );
		v3_restrict( $collection_options, $options, $term, (bool) $meta['restrict'] );
		v3_storyline( $collection_options, $collection, $term );
		v3_transcribe( $collection_options, $options );

		update_option( $collection, $collection_options );

		$increment++;
	}
}

/**
 * Update alert options.
 *
 * @param array $options The Webcomic 5 collection options array.
 * @param array $plugin The plugin options array.
 * @return void
 */
function v3_alert( array &$options, array $plugin ) {
	$options += [
		'alert_buffer' => [],
		'alert_hiatus' => [],
	];

	if ( $plugin['buffer_toggle'] && $plugin['buffer_size'] ) {
		$options['alert_buffer'][ (int) $plugin['buffer_size'] ] = get_option( 'admin_email' );
	}
}

/**
 * Update character options and add Webcomci 3 character conversion filters.
 *
 * @param array   $options The Webcomic 5 collection options array.
 * @param string  $collection The collection to check for characters.
 * @param WP_Term $collection_term The collection object.
 * @return void
 */
function v3_character( array &$options, string $collection, WP_Term $collection_term ) {
	$options += [
		'character_slug'              => "{$collection_term->slug}-character",
		'character_sort'              => true,
		'character_crossovers'        => false,
		'character_hierarchical'      => true,
		'character_hierarchical_skip' => false,
	];

	$table_terms    = webcomic( 'GLOBALS.wpdb' )->terms;
	$table_taxonomy = webcomic( 'GLOBALS.wpdb' )->term_taxonomy;
	$terms          = webcomic( 'GLOBALS.wpdb' )->get_col( "SELECT term_id from {$table_terms} join {$table_taxonomy} on {$table_terms}.term_id = {$table_taxonomy}.term_id and {$table_taxonomy}.taxonomy = 'webcomic_character' where term_group = {$collection_term->term_id}" );
	$term_ids       = implode( ',', array_filter( array_map( 'intval', $terms ) ) );
	$order          = 1;

	webcomic( 'GLOBALS.wpdb' )->query( "UPDATE {$table_terms} set term_group = 0 where term_id in ({$term_ids})" );
	webcomic( 'GLOBALS.wpdb' )->query( "UPDATE {$table_taxonomy} set taxonomy = '{$collection}_character' where taxonomy = 'webcomic_character' and term_id in ({$term_ids})" );

	foreach ( $terms as $term ) {
		update_term_meta( $term, 'webcomic_order', $order );

		$order++;
	}
}

/**
 * Update commerce options.
 *
 * @param array $options The Webcomic 5 collection options array.
 * @param array $plugin The plugin options array.
 * @param array $collection The collection paypal options array.
 * @return void
 */
function v3_commerce( array &$options, array $plugin, array $collection ) {
	$price_d    = $plugin['paypal_price_d'] * ( 1 + 0.01 * $collection['price_d'] );
	$shipping_d = $plugin['paypal_shipping_d'] * ( 1 + 0.01 * $collection['shipping_d'] );
	$price_i    = $plugin['paypal_price_i'] * ( 1 + 0.01 * $collection['price_i'] );
	$shipping_i = $plugin['paypal_shipping_i'] * ( 1 + 0.01 * $collection['shipping_i'] );
	$price_o    = $plugin['paypal_price_o'] * ( 1 + 0.01 * $collection['price_o'] );
	$shipping_o = $plugin['paypal_shipping_o'] * ( 1 + 0.01 * $collection['shipping_o'] );

	$options += [
		'commerce_business' => $plugin['paypal_business'],
		'commerce_cart'     => ( '_cart' === $plugin['paypal_method'] ),
		'commerce_currency' => $plugin['paypal_currency'],
		'commerce_donation' => round( abs( $plugin['paypal_donation'] ), 2 ),
		'commerce_prints'   => [
			'domestic'      => [
				'name'    => __( 'Domestice', 'webcomic' ),
				'slug'    => 'domestic',
				'price'   => round( abs( $price_d + $shipping_d ), 2 ),
				'stock'   => 0,
				'default' => ( $plugin['paypal_prints'] && $collection['prints'] ),
			],
			'international' => [
				'name'    => __( 'International', 'webcomic' ),
				'slug'    => 'international',
				'price'   => round( abs( $price_i + $shipping_i ), 2 ),
				'stock'   => 0,
				'default' => ( $plugin['paypal_prints'] && $collection['prints'] ),
			],
			'original'      => [
				'name'    => __( 'Original', 'webcomic' ),
				'slug'    => 'original',
				'price'   => round( abs( $price_o + $shipping_o ), 2 ),
				'stock'   => 1,
				'default' => ( $plugin['paypal_prints'] && $collection['prints'] ),
			],
		],
	];
}

/**
 * Update restrict options.
 *
 * @param array   $options The Webcomic 5 collection options array.
 * @param array   $plugin The plugin options array.
 * @param WP_Term $collection The current collection object.
 * @param bool    $restrict_roles Wether to require users to login to view posts.
 * @return void
 */
function v3_restrict( array &$options, array $plugin, WP_Term $collection, bool $restrict_roles ) {
	$table    = webcomic( 'GLOBALS.wpdb' )->terms;
	$options += [
		'restrict_age'            => (int) webcomic( 'GLOBALS.wpdb' )->get_col( "SELECT term_group from {$table} where term_id = {$collection->term_id}" ),
		'restrict_age_media'      => 0,
		'restrict_roles'          => [],
		'restrict_roles_media'    => 0,
		'restrict_password_media' => 0,
	];

	if ( ! $plugin['age_toggle'] || $plugin['age_size'] > $options['restrict_age'] ) {
		$options['restrict_age'] = 0;
	}

	if ( $restrict_roles ) {
		$options['restrict_roles'] = [ '~loggedin~' ];
	}
}

/**
 * Update storyline options and add Webcomci 3 storyline conversion filters.
 *
 * @param array   $options The Webcomic 5 collection options array.
 * @param string  $collection The collection to check for characters.
 * @param WP_Term $collection_term The collection object.
 * @return void
 */
function v3_storyline( array &$options, string $collection, WP_Term $collection_term ) {
	$options += [
		'storyline_slug'              => "{$collection_term->slug}-storyline",
		'storyline_sort'              => true,
		'storyline_crossovers'        => false,
		'storyline_hierarchical'      => true,
		'storyline_hierarchical_skip' => false,
	];

	$table_terms    = webcomic( 'GLOBALS.wpdb' )->terms;
	$table_taxonomy = webcomic( 'GLOBALS.wpdb' )->term_taxonomy;
	$terms          = webcomic( 'GLOBALS.wpdb' )->get_col( "SELECT term_id from {$table_terms} join {$table_taxonomy} on {$table_terms}.term_id = {$table_taxonomy}.term_id and {$table_taxonomy}.taxonomy = 'webcomic_storyline' where term_group = {$collection_term->term_id}" );
	$term_ids       = implode( ',', array_filter( array_map( 'intval', $terms ) ) );
	$order          = 1;

	webcomic( 'GLOBALS.wpdb' )->query( "UPDATE {$table_terms} set term_group = 0 where term_id in ({$term_ids})" );
	webcomic( 'GLOBALS.wpdb' )->query( "UPDATE {$table_taxonomy} set taxonomy = '{$collection}_storyline' where taxonomy = 'webcomic_storyline' and term_id in ({$term_ids})" );

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
function v3_transcribe( array &$options, array $plugin ) {
	$options += [
		'transcribe_comic'     => $plugin['transcribe_toggle'],
		'transcribe_close'     => 0,
		'transcribe_require'   => str_replace( [ 'login', 'selfid', 'anyone' ], [ 'loggedin', 'name_email', '' ], $plugin['transcribe_restrict'] ),
		'transcribe_publish'   => '',
		'transcribe_alert_pub' => $plugin['transcribe_toggle'],
		'transcribe_alert_mod' => $plugin['transcribe_toggle'],
	];
}

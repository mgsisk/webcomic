<?php
/**
 * Webcomic 4 upgrade functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Compat\Upgrade;

/**
 * Convert Webcomic 4 options to Webcomic 5 equivalents.
 *
 * @return void
 */
function v4() {
	$options = get_option( 'webcomic_options' );

	update_option(
		'webcomic', [
			'version'     => $options['version'],
			'upgrade'     => $options['version'],
			'debug'       => false,
			'uninstall'   => $options['uninstall'],
			'components'  => [ 'collection', 'alert', 'character', 'commerce', 'compat', 'restrict', 'storyline', 'transcribe', 'twitter' ],
			'collections' => array_keys( $options['collections'] ),
			'compat'      => [
				'version' => 4,
				'upgrade' => $options,
				'sizes'   => $options['sizes'],
				'terms'   => $options['terms'],
			],
		]
	);

	v4_collections( $options );

	webcomic( 'GLOBALS.wpdb' )->update(
		webcomic( 'GLOBALS.wpdb' )->term_taxonomy, [
			'taxonomy' => 'webcomic_transcript_language',
		], [
			'taxonomy' => 'webcomic_language',
		]
	);
}

/**
 * Convert Webcomic 4 collections.
 *
 * @param array $options The options array.
 * @return void
 */
function v4_collections( array $options ) {
	foreach ( $options['collections'] as $collection ) {
		$collection_options = [
			'id'                => $collection['id'],
			'name'              => $collection['name'],
			'slug'              => $collection['slugs']['archive'],
			'permalink'         => $collection['slugs']['webcomic'],
			'description'       => $collection['description'],
			'media'             => $collection['image'],
			'theme'             => $collection['theme'],
			'archive_date'      => true,
			'archive_reverse'   => false,
			'syndicate'         => $collection['feeds']['main'],
			'syndicate_preview' => $collection['feeds']['hook'],
			'supports'          => $collection['supports'],
			'taxonomies'        => array_merge( [ "{$collection['id']}_character", "{$collection['id']}_storyline" ], $collection['taxonomies'] ),
			'sidebars'          => [],
			'updated'           => '',
		];

		if ( $collection['updated'] ) {
			$collection_options['updated'] = date( 'Y-m-d H:i:s', $collection['updated'] );
		}

		v4_alert( $collection_options, $collection );
		v4_character( $collection_options, $collection );
		v4_commerce( $collection_options, $collection );
		v4_restrict( $collection_options, $collection );
		v4_storyline( $collection_options, $collection );
		v4_transcribe( $collection_options, $collection );
		v4_twitter( $collection_options, $collection );

		update_option( $collection['id'], $collection_options );
	}
}

/**
 * Update alert options.
 *
 * @param array $options The Webcomic 5 collection options array.
 * @param array $collection The collection options array.
 * @return void
 */
function v4_alert( array &$options, array $collection ) {
	$options += [
		'alert_buffer' => [],
		'alert_hiatus' => [],
	];

	if ( $collection['buffer']['hook'] ) {
		$options['alert_buffer'][ $collection['buffer']['days'] ] = $collection['buffer']['email'];
	}
}

/**
 * Update character options.
 *
 * @param array $options The Webcomic 5 collection options array.
 * @param array $collection The collection options array.
 * @return void
 */
function v4_character( array &$options, array $collection ) {
	$options += [
		'character_slug'              => $collection['slugs']['character'],
		'character_sort'              => false,
		'character_crossovers'        => false,
		'character_hierarchical'      => false,
		'character_hierarchical_skip' => false,
	];
}

/**
 * Update commerce options.
 *
 * @param array $options The Webcomic 5 collection options array.
 * @param array $collection The collection options array.
 * @return void
 */
function v4_commerce( array &$options, array $collection ) {
	$options += [
		'commerce_business' => $collection['commerce']['business'],
		'commerce_cart'     => ( '_cart' === $collection['commerce']['method'] ),
		'commerce_currency' => $collection['commerce']['currency'],
		'commerce_donation' => $collection['commerce']['donation'],
		'commerce_prints'   => [
			'domestic'      => [
				'name'    => __( 'Domestice', 'webcomic' ),
				'slug'    => 'domestic',
				'price'   => $collection['commerce']['total']['domestic'],
				'stock'   => 0,
				'default' => $collection['commerce']['prints'],
			],
			'international' => [
				'name'    => __( 'International', 'webcomic' ),
				'slug'    => 'international',
				'price'   => $collection['commerce']['total']['international'],
				'stock'   => 0,
				'default' => $collection['commerce']['prints'],
			],
			'original'      => [
				'name'    => __( 'Original', 'webcomic' ),
				'slug'    => 'original',
				'price'   => $collection['commerce']['total']['original'],
				'stock'   => 1,
				'default' => $collection['commerce']['originals'],
			],
		],
	];
}

/**
 * Update restrict options.
 *
 * @param array $options The Webcomic 5 collection options array.
 * @param array $collection The collection options array.
 * @return void
 */
function v4_restrict( array &$options, array $collection ) {
	$options += [
		'restrict_age'            => $collection['access']['age'],
		'restrict_age_media'      => 0,
		'restrict_roles'          => $collection['access']['roles'],
		'restrict_roles_media'    => 0,
		'restrict_password_media' => 0,
	];

	if ( ! $collection['access']['byage'] ) {
		$options['restrict_age'] = 0;
	}

	if ( ! $collection['access']['byrole'] ) {
		$options['restrict_roles'] = [];
	} elseif ( '!' === $collection['access']['roles'] ) {
		$options['restrict_roles'] = [ '~loggedin~' ];
	}
}

/**
 * Update storyline options.
 *
 * @param array $options The Webcomic 5 collection options array.
 * @param array $collection The collection options array.
 * @return void
 */
function v4_storyline( array &$options, array $collection ) {
	$options += [
		'storyline_slug'              => $collection['slugs']['storyline'],
		'storyline_sort'              => false,
		'storyline_crossovers'        => false,
		'storyline_hierarchical'      => true,
		'storyline_hierarchical_skip' => false,
	];
}

/**
 * Update transcribe options.
 *
 * @param array $options The Webcomic 5 collection options array.
 * @param array $collection The collection options array.
 * @return void
 */
function v4_transcribe( array &$options, array $collection ) {
	$options += [
		'transcribe_comic'     => $collection['transcripts']['open'],
		'transcribe_close'     => 0,
		'transcribe_require'   => str_replace( [ 'register', 'identify', 'everyone' ], [ 'loggedin', 'name_email', '' ], $collection['transcripts']['notify']['hook'] ),
		'transcribe_publish'   => '',
		'transcribe_alert_pub' => $collection['transcripts']['notify']['hook'],
		'transcribe_alert_mod' => $collection['transcripts']['notify']['hook'],
	];
}

/**
 * Update Twitter options.
 *
 * @param array $options The Webcomic 5 collection options array.
 * @param array $collection The collection options array.
 * @return void
 */
function v4_twitter( array &$options, array $collection ) {
	$options += [
		'twitter_oauth'            => [
			'oauth_consumer_key'    => $collection['twitter']['consumer_key'],
			'oauth_consumer_secret' => $collection['twitter']['consumer_secret'],
			'oauth_token'           => $collection['twitter']['oauth_token'],
			'oauth_token_secret'    => $collection['twitter']['oauth_secret'],
		],
		'twitter_user'             => '',
		'twitter_update'           => true,
		'twitter_update_media'     => (bool) $collection['twitter']['media'],
		'twitter_update_sensitive' => false,
		'twitter_status'           => $collection['twitter']['format'],
		'twitter_status_media'     => 0,
		'twitter_card'             => true,
		'twitter_card_media'       => true,
	];
}

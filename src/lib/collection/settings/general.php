<?php
/**
 * General settings functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Settings;

use WP_Screen;

/**
 * Add general settings hooks.
 *
 * @return void
 */
function general() {
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_section_general' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_name' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_slug' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_permalink' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_description' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_media' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_archive' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_syndicate' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_supports' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_help_general' );
	add_filter( 'current_screen', __NAMESPACE__ . '\hook_enqueue_media_manager' );
	add_filter( 'delete_attachment', __NAMESPACE__ . '\hook_delete_collection_media' );
	add_filter( 'display_media_states', __NAMESPACE__ . '\hook_add_collection_media_state' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_name' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_slug' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_permalink' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_description' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_media' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_archive' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_syndicate' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_supports' );
	}
}

/**
 * Add the settings section.
 *
 * @return void
 */
function hook_add_section_general() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_section(
			"{$collection}_general",
			'<span class="dashicons dashicons-admin-settings"></span> ' . esc_html__( 'General', 'webcomic' ),
			function() {
				echo '<div></div>';
			},
			"{$collection}_settings"
		);
	}
}

/**
 * Add the name setting field.
 *
 * @return void
 */
function hook_add_field_name() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_name",
			__( 'Name', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_general", [
				'file'      => __DIR__ . '/general-inc-field-name.php',
				'option'    => webcomic( "option.{$collection}.name" ),
				'label_for' => "{$collection}[name]",
			]
		);
	}
}

/**
 * Add the slug setting field.
 *
 * @return void
 */
function hook_add_field_slug() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_slug",
			__( 'Slug', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_general", [
				'file'      => __DIR__ . '/general-inc-field-slug.php',
				'option'    => webcomic( "option.{$collection}.slug" ),
				'label_for' => "{$collection}[slug]",
			]
		);
	}
}

/**
 * Add the permalink setting field.
 *
 * @return void
 */
function hook_add_field_permalink() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_permalink",
			__( 'Permalink', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_general", [
				'file'      => __DIR__ . '/general-inc-field-permalink.php',
				'option'    => webcomic( "option.{$collection}.permalink" ),
				'label_for' => "{$collection}[permalink]",
			]
		);
	}
}

/**
 * Add the description setting field.
 *
 * @return void
 */
function hook_add_field_description() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_description",
			__( 'Description', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_general", [
				'file'      => __DIR__ . '/general-inc-field-description.php',
				'option'    => webcomic( "option.{$collection}.description" ),
				'label_for' => "{$collection}[description]",
			]
		);
	}
}

/**
 * Add the media setting field.
 *
 * @return void
 */
function hook_add_field_media() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_media",
			__( 'Image', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_general", [
				'file'      => __DIR__ . '/general-inc-field-media.php',
				'option'    => webcomic( "option.{$collection}.media" ),
				'label_for' => "{$collection}[media]",
			]
		);
	}
}

/**
 * Add the archives setting field.
 *
 * @return void
 */
function hook_add_field_archive() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_archives",
			__( 'Archives', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_general", [
				'file'        => __DIR__ . '/general-inc-field-archives.php',
				'option'      => webcomic( "option.{$collection}.archive_date" ),
				'option_sort' => webcomic( "option.{$collection}.archive_reverse" ),
				'label_for'   => "{$collection}[archive_date]",
				'label_sort'  => "{$collection}[archive_reverse]",
			]
		);
	}
}

/**
 * Add the syndicate setting field.
 *
 * @return void
 */
function hook_add_field_syndicate() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_syndicate",
			__( 'Syndication', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_general", [
				'file'            => __DIR__ . '/general-inc-field-syndicate.php',
				'option'          => webcomic( "option.{$collection}.syndicate" ),
				'option_preview'  => webcomic( "option.{$collection}.syndicate_preview" ),
				'label_for'       => "{$collection}[syndicate]",
				'label_preview'   => "{$collection}[syndicate_preview]",
				'collection_name' => webcomic( "option.{$collection}.name" ),
			]
		);
	}
}

/**
 * Add the supports setting field.
 *
 * @return void
 */
function hook_add_field_supports() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_supports",
			__( 'Supports', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_general", [
				'file'        => __DIR__ . '/general-inc-field-supports.php',
				'option'      => webcomic( "option.{$collection}.supports" ),
				'option_tax'  => webcomic( "option.{$collection}.taxonomies" ),
				'option_area' => webcomic( "option.{$collection}.sidebars" ),
				'label_for'   => "{$collection}[supports]",
				'label_tax'   => "{$collection}[taxonomies]",
				'label_area'  => "{$collection}[sidebars]",
				'taxonomies'  => get_taxonomies(
					[
						'public'  => true,
						'show_ui' => true,
						'rewrite' => true,
					], 'objects'
				),
			]
		);
	}
}

/**
 * Add the general help tab.
 *
 * @return void
 */
function hook_add_help_general() {
	$screen = get_current_screen();

	if ( ! preg_match( '/^webcomic\d+_page_webcomic\d+_options$/', $screen->id ) ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'general',
			'title'    => __( 'General', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/general-inc-help.php';
			},
		]
	);
}

/**
 * Enqueue the media manager.
 *
 * @param WP_Screen $screen The current screen.
 * @return void
 */
function hook_enqueue_media_manager( WP_Screen $screen ) {
	if ( ! preg_match( '/^webcomic\d+_page_webcomic\d+_options$/', $screen->id ) ) {
		return;
	}

	add_filter( 'webcomic_enqueue_media_manager', '__return_true' );
}

/**
 * Delete collection media setting when a post is deleted.
 *
 * @param mixed $id The post ID.
 * @return void
 */
function hook_delete_collection_media( $id ) {
	$contexts = array_unique( get_post_meta( $id, 'webcomic_collection' ) );

	foreach ( $contexts as $collection ) {
		if ( ! webcomic_collection_exists( $collection ) ) {
			continue;
		}

		update_option(
			$collection, [
				'media' => 0,
			]
		);
	}
}


/**
 * Add media state for collection media.
 *
 * @param array $states The media states for the current object.
 * @return array
 */
function hook_add_collection_media_state( array $states ) : array {
	$contexts = array_unique( get_post_meta( get_the_ID(), 'webcomic_collection' ) );

	if ( ! $contexts ) {
		return $states;
	}

	foreach ( $contexts as $collection ) {
		// Translators: Post type name.
		$states[] = sprintf( __( '%s Image', 'webcomic' ), webcomic( "option.{$collection}.name" ) );
	}

	return $states;
}

/* ===== Collection Hooks =================================================== */

/**
 * Sanitize the name field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_name( array $options ) : array {
	$options['name'] = sanitize_text_field( $options['name'] );

	if ( ! $options['name'] ) {
		$options['name'] = webcomic( "option.{$options['id']}.name" );
	}

	return $options;
}

/**
 * Sanitize the slug field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_slug( array $options ) : array {
	$options['slug'] = sanitize_title( $options['slug'] );

	if ( ! $options['slug'] ) {
		$options['slug'] = sanitize_title( $options['name'] );
	}

	if ( webcomic( "option.{$options['id']}.slug" ) !== $options['slug'] ) {
		set_transient( 'webcomic_flush_rewrite_rules', true, 1 );
	}

	return $options;
}

/**
 * Sanitize the permalink field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_permalink( array $options ) : array {
	/* This filter is documented in Mgsisk\Webcomic\hook_add_collection_rewrite_rules() */
	$tokens    = array_keys( apply_filters( 'webcomic_rewrite_tokens', [], $options['id'] ) );
	$permalink = [];

	foreach ( explode( '/', $options['permalink'] ) as $piece ) {
		if ( ! in_array( $piece, $tokens, true ) ) {
			$piece = sanitize_title( $piece );
		}

		$permalink[] = $piece;
	}

	$permalink = implode( '/', array_filter( $permalink ) );

	if ( ! $permalink ) {
		$permalink = sanitize_title( $options['name'] );
	}

	$options['permalink'] = $permalink;

	if ( webcomic( "option.{$options['id']}.permalink" ) !== $options['permalink'] ) {
		set_transient( 'webcomic_flush_rewrite_rules', true, 1 );
	}

	return $options;
}

/**
 * Sanitize the description field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_description( array $options ) : array {
	$options['description'] = wp_kses_post( trim( $options['description'] ) );

	return $options;
}

/**
 * Sanitize the media field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_media( array $options ) : array {
	$options['media'] = (int) $options['media'];
	$old_media        = webcomic( "option.{$options['id']}.media" );

	if ( $options['media'] !== $old_media ) {
		if ( $options['media'] ) {
			add_post_meta( $options['media'], 'webcomic_collection', $options['id'] );
		}

		if ( $old_media ) {
			delete_post_meta( $old_media, 'webcomic_collection', $options['id'] );
		}
	}

	return $options;
}

/**
 * Sanitize the archives field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_archive( array $options ) : array {
	$options['archive_date']    = (bool) $options['archive_date'];
	$options['archive_reverse'] = (bool) $options['archive_reverse'];

	return $options;
}

/**
 * Sanitize the syndicate field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_syndicate( array $options ) : array {
	$options['syndicate']         = (bool) $options['syndicate'];
	$options['syndicate_preview'] = (bool) $options['syndicate_preview'];

	return $options;
}

/**
 * Sanitize the supports field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_supports( array $options ) : array {
	$taxonomies            = get_taxonomies(
		[
			'public'  => true,
			'show_ui' => true,
		]
	);
	$options['supports']   = array_intersect( $options['supports'], [ 'title', 'author', 'editor', 'excerpt', 'comments', 'thumbnail', 'revisions', 'trackbacks', 'post-formats', 'custom-fields' ] );
	$options['taxonomies'] = array_intersect( $options['taxonomies'], $taxonomies );
	$options['sidebars']   = array_intersect( $options['sidebars'], [ 'infinite', 'media', 'meta', 'navigation', 'syndicate' ] );

	return $options;
}

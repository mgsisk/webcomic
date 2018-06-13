<?php
/**
 * Collection settings functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

/**
 * Add settings hooks.
 *
 * @return void
 */
function settings() {
	add_filter( 'webcomic_activate_collection', __NAMESPACE__ . '\hook_activate' );
	add_filter( 'webcomic_deactivate_collection', __NAMESPACE__ . '\hook_deactivate' );
	add_filter( 'webcomic_collection_allowed_options', __NAMESPACE__ . '\hook_add_allowed_options' );
	add_filter( 'admin_menu', __NAMESPACE__ . '\hook_add_settings_page' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_create_new_collection' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_shutdown_flush_rules' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_options' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_settings_styles' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_settings_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_settings_styles' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_settings_scripts' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_help_sidebar' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_settings_help_overview' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_settings_help_details', 999 );
	add_filter( 'wp_insert_post_data', __NAMESPACE__ . '\hook_update_post_data', 10, 2 );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_options' );
	}
}

/**
 * Activate the collection component.
 *
 * @return void
 */
function hook_activate() {
	if ( get_option( 'webcomic1' ) ) {
		return;
	}

	add_option(
		'webcomic1', [
			'id'                => 'webcomic1',
			'name'              => 'Untitled Comic',
			'slug'              => 'untitled-comic',
			'permalink'         => 'untitled-comic',
			'description'       => '',
			'media'             => 0,
			'theme'             => '',
			'archive_date'      => false,
			'archive_reverse'   => false,
			'syndicate'         => true,
			'syndicate_preview' => true,
			'supports'          => [ 'title', 'editor', 'author', 'excerpt', 'comments', 'revisions', 'trackbacks' ],
			'taxonomies'        => [],
			'sidebars'          => [],
			'updated'           => '',
		]
	);

	add_filter(
		'init', function() {
			update_option(
				'webcomic', [
					'collections' => [ 'webcomic1' ],
				]
			);
		}
	);

	set_transient( 'webcomic_flush_rewrite_rules', true, 1 );
}

/**
 * Deactivate the collection component.
 *
 * @return void
 */
function hook_deactivate() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		/* This action is documented in Mgsisk\Webcomic\Collection\hook_sanitize_delete_field() */
		do_action( 'webcomic_delete_collection', $collection );
	}

	$table = webcomic( 'GLOBALS.wpdb' )->options;

	webcomic( 'GLOBALS.wpdb' )->query( "DELETE from {$table} where option_name like 'widget_mgsisk_webcomic_collection_%'" );
}

/**
 * Add the default allowed collection options.
 *
 * @param array $allowed The allowed options.
 * @return array
 */
function hook_add_allowed_options( array $allowed ) : array {
	return array_merge( $allowed, [ 'id', 'name', 'slug', 'permalink', 'description', 'media', 'theme', 'archive_date', 'archive_reverse', 'syndicate', 'syndicate_preview', 'supports', 'taxonomies', 'sidebars', 'updated', 'delete' ] );
}

/**
 * Add the settings page.
 *
 * @return void
 */
function hook_add_settings_page() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_submenu_page(
			"edit.php?post_type={$collection}",
			// Translators: The post type name.
			sprintf( __( '%s Settings', 'webcomic' ), webcomic( "option.{$collection}.name" ) ),
			__( 'Settings', 'webcomic' ),
			'manage_options',
			"{$collection}_options",
			function() use ( $collection ) {
				$url  = add_query_arg(
					[
						'webcomic_new_collection' => $collection,
					], admin_url()
				);
				$args = [
					'file'       => __DIR__ . '/settings-inc-page.php',
					'new_link'   => wp_nonce_url( esc_url( $url ), __NAMESPACE__ . 'NewNonce', __NAMESPACE__ . 'NewNonce' ),
					'collection' => $collection,
				];

				require $args['file'];
			}
		);
	}
}

/**
 * Create a new collection.
 *
 * @return void
 * @suppress PhanTypeArraySuspicious - Using $options incorrectly triggers this.
 */
function hook_create_new_collection() {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'NewNonce' ) ), __NAMESPACE__ . 'NewNonce' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$base = webcomic( 'option.' . sanitize_key( webcomic( 'GLOBALS._REQUEST.webcomic_new_collection' ) ) );

	if ( ! $base ) {
		return;
	}

	$id = count( webcomic( 'option.collections' ) ) + 1;

	while ( in_array( "webcomic{$id}", webcomic( 'option.collections' ), true ) ) {
		$id++;
	}

	/**
	 * Alter the default collection settings.
	 *
	 * This filter allows hooks to alter the default settings of new a
	 * collection. Any settings that remain unset will be copied from whatever
	 * collection is being used as the $base collection.
	 *
	 * @param array $defaults The default collection settings.
	 */
	$options                         = apply_filters(
		'webcomic_new_collection', [
			'id'          => "webcomic{$id}",
			// Translators: Custom post type increment number.
			'name'        => sprintf( __( 'Untitled Comic %s', 'webcomic' ), $id ),
			'slug'        => "untitled-comic-{$id}",
			'permalink'   => "untitled-comic-{$id}",
			'description' => '',
			'media'       => 0,
			'updated'     => '',
		]
	) + $base;
	$plugin_options                  = webcomic( 'option' );
	$plugin_options['collections'][] = $options['id'];

	add_option( $options['id'], $options );
	update_option( 'webcomic', $plugin_options );

	$options_url = esc_url(
		add_query_arg(
			[
				'post_type' => $options['id'],
				'page'      => "{$options['id']}_options",
			], admin_url( 'edit.php' )
		)
	);
	$settings    = "<a href='{$options_url}'>" . __( 'Update Settings', 'webcomic' ) . '</a>';

	set_transient( 'webcomic_flush_rewrite_rules', true, 1 );

	webcomic_notice( '<strong>' . __( 'New collection created.', 'webcomic' ) . "</strong> {$settings}" );

	add_filter( 'wp_doing_ajax', '__return_true' ) && wp_safe_redirect( admin_url() ) && wp_die();
}

/**
 * Flush rewrite rules.
 *
 * @return void
 */
function hook_add_shutdown_flush_rules() {
	if ( ! get_transient( 'webcomic_flush_rewrite_rules' ) ) {
		return;
	}

	add_filter( 'shutdown', 'flush_rewrite_rules' );
}

/**
 * Register collection options.
 *
 * @return void
 */
function hook_register_options() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		register_setting( $collection, $collection );
	}
}

/**
 * Register settings stylesheets.
 *
 * @return void
 */
function hook_register_settings_styles() {
	wp_register_style(
		__NAMESPACE__ . 'SettingsCSS',
		plugins_url( 'srv/collection/settings.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);
}

/**
 * Register settings javascript.
 *
 * @return void
 */
function hook_register_settings_scripts() {
	wp_register_script(
		__NAMESPACE__ . 'SettingsJS',
		plugins_url( 'srv/collection/settings-section.js', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' ),
		true
	);
}

/**
 * Enqueue settings stylesheets.
 *
 * @return void
 */
function hook_enqueue_settings_styles() {
	if ( ! preg_match( '/^webcomic\d+_page_webcomic\d+_options$/', get_current_screen()->id ) ) {
		return;
	}

	wp_enqueue_style( __NAMESPACE__ . 'SettingsCSS' );
}

/**
 * Enqueue settings javascript.
 *
 * @return void
 */
function hook_enqueue_settings_scripts() {
	if ( ! preg_match( '/^webcomic\d+_page_webcomic\d+_options$/', get_current_screen()->id ) ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'SettingsJS' );
}

/**
 * Add the help sidebar.
 *
 * @return void
 */
function hook_add_help_sidebar() {
	$screen = get_current_screen();

	if ( ! preg_match( '/^(edit-)?webcomic\d+(_page_webcomic\d+_options)?$/', $screen->id ) ) {
		return;
	}

	$screen->set_help_sidebar( webcomic_help() );
}

/**
 * Add the overview help tab.
 *
 * @return void
 */
function hook_add_settings_help_overview() {
	$screen = get_current_screen();

	if ( ! preg_match( '/^webcomic\d+_page_webcomic\d+_options$/', $screen->id ) ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'overview',
			'title'    => __( 'Overview', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/settings-inc-help-overview.php';
			},
		]
	);
}

/**
 * Add the details help tab.
 *
 * @return void
 */
function hook_add_settings_help_details() {
	$match  = [];
	$screen = get_current_screen();

	if ( ! preg_match( '/^(webcomic\d+)_page_webcomic\d+_options$/', $screen->id, $match ) ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'details',
			'title'    => __( 'Details', 'webcomic' ),
			'callback' => function() use ( $match ) {
				$args = [
					'file'   => __DIR__ . '/settings-inc-help-details.php',
					'option' => webcomic( "option.{$match[1]}" ),
				];

				ksort( $args['option'] );

				require $args['file'];
			},
		]
	);
}

/**
 * Update post data based on currently supported features.
 *
 * @param array $data The slashed post data.
 * @param array $post The sanitized, unslashed post data.
 * @return array
 */
function hook_update_post_data( array $data, array $post ) : array {
	if ( ! $post['ID'] || ! $post['post_type'] || ! webcomic_collection_exists( $post['post_type'] ) ) {
		return $data;
	}

	if ( ! post_type_supports( $post['post_type'], 'title' ) ) {
		$data['post_name']  = $post['ID'];
		$data['post_title'] = '';
	}

	if ( ! post_type_supports( $post['post_type'], 'editor' ) ) {
		$data['post_content'] = '&nbsp;';
	}

	return $data;
}

/* ===== Collection Hooks =================================================== */

/**
 * Ensure only allowed options are set.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_options( $options ) : array {
	preg_match( '(webcomic\d+)', current_filter(), $match );

	$options['id'] = $match[0];
	$options      += webcomic( "option.{$options['id']}" );

	/**
	 * Alter the allowed options.
	 *
	 * This filter allows hooks to alter the allowed collection options.
	 *
	 * @param array $allowed The allowed options.
	 */
	$allowed = apply_filters( 'webcomic_collection_allowed_options', [] );

	return array_intersect_key( $options, array_flip( $allowed ) );
}

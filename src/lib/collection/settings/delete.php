<?php
/**
 * Delete settings functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Settings;

/**
 * Add delete settings hooks.
 *
 * @return void
 */
function delete() {
	add_filter( 'webcomic_delete_collection', __NAMESPACE__ . '\hook_delete_collection_posts' );
	add_filter( 'webcomic_delete_collection', __NAMESPACE__ . '\hook_delete_collection_options', 999 );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_section_delete', 999 );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_delete' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_help_delete', 99 );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_delete', 99 );
	}
}

/**
 * Delete collection posts and page metadata.
 *
 * @param string $collection The collection ID being deleted.
 * @return void
 */
function hook_delete_collection_posts( string $collection ) {
	$comics = get_posts(
		[
			'fields'         => 'ids',
			'post_type'      => $collection,
			'post_status'    => 'any',
			'posts_per_page' => -1,
		]
	);

	foreach ( $comics as $comic ) {
		wp_delete_post( $comic, true );
	}

	delete_metadata( 'page', 0, 'webcomic_collection', $collection, true );
}

/**
 * Delete collection options.
 *
 * @param string $collection The collection ID being deleted.
 * @return void
 */
function hook_delete_collection_options( string $collection ) {
	add_filter(
		"sanitize_option_{$collection}", function( array $options ) use ( $collection ) {
			remove_filter( "sanitize_option_{$collection}", str_replace( 'Settings', '', __NAMESPACE__ ) . 'hook_sanitize_options' );

			$options = [];

			return $options;
		}, 99
	);

	update_option(
		$collection, [
			'id'                => $collection,
			'name'              => '',
			'slug'              => '',
			'permalink'         => '',
			'description'       => '',
			'media'             => 0,
			'theme'             => '',
			'archive_date'      => false,
			'archive_reverse'   => false,
			'syndicate'         => true,
			'syndicate_preview' => true,
			'supports'          => [],
			'taxonomies'        => [],
			'sidebars'          => [],
			'updated'           => '',
		]
	);

	delete_option( $collection );
}

/**
 * Add the delete settings section.
 *
 * @return void
 */
function hook_add_section_delete() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		if ( 'webcomic1' === $collection ) {
			continue;
		}

		add_settings_section(
			"{$collection}_delete",
			'<span class="dashicons dashicons-trash"></span> ' . esc_html__( 'Delete', 'webcomic' ),
			function() {
				echo '<div></div>';
			},
			"{$collection}_settings"
		);
	}
}

/**
 * Add the delete setting field.
 *
 * @return void
 */
function hook_add_field_delete() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		if ( 'webcomic1' === $collection ) {
			continue;
		}

		add_settings_field(
			"{$collection}_delete",
			__( 'Confirm', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_delete", [
				'file'      => __DIR__ . '/delete-inc-field.php',
				'label_for' => "{$collection}[delete]",
			]
		);
	}
}

/**
 * Add the delete help tab.
 *
 * @return void
 */
function hook_add_help_delete() {
	$match  = [];
	$screen = get_current_screen();

	if ( ! preg_match( '/^(webcomic\d+)_page_webcomic\d+_options/', $screen->id, $match ) || 'webcomic1' === $match[1] ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'delete',
			'title'    => __( 'Delete', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/delete-inc-help.php';
			},
		]
	);
}

/* ===== Collection Hooks =================================================== */

/**
 * Sanitize the delete field.
 *
 * @param array $options The submitted options.
 * @return array|void
 */
function hook_sanitize_delete( array $options ) {
	$name   = webcomic( "option.{$options['id']}.name" );
	$delete = '';

	if ( isset( $options['delete'] ) ) {
		$delete = $options['delete'];

		unset( $options['delete'] );
	}

	if ( ! $delete || 'webcomic1' === $options['id'] ) {
		return $options;
	} elseif ( $name !== $delete ) {
		add_settings_error( $options['id'], 'settings_updated', __( 'Settings saved.', 'webcomic' ), 'updated' );
		add_settings_error( $options['id'], 'bad-collection-name', __( 'Incorrect collection name, nothing deleted.', 'webcomic' ), 'error' );

		return $options;
	}

	/**
	 * Delete a collection.
	 *
	 * This action provides a way for hooks to perform collection-specific cleanup
	 * and other actions before deleting the collection settings.
	 *
	 * @param string $id The ID of the deleted collection.
	 */
	do_action( 'webcomic_delete_collection', $options['id'] );

	$plugin_options = webcomic( 'option' );

	unset( $plugin_options['collections'][ array_search( $options['id'], $plugin_options['collections'], true ) ] );

	update_option( 'webcomic', $plugin_options );
	set_transient( 'webcomic_flush_rewrite_rules', true, 1 );

	// Translators: The post type name.
	webcomic_notice( '<strong>' . sprintf( __( '%s has been deleted.', 'webcomic' ), esc_html( $name ) ) . '</strong>' );

	add_filter( 'wp_doing_ajax', '__return_true' ) && wp_safe_redirect( admin_url() ) && wp_die();
}

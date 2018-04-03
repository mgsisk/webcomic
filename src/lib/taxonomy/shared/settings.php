<?php
/**
 * Shared taxonomy settings functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Taxonomy\Shared;

/**
 * Activate a taxonomy component.
 *
 * @param string $type The taxonomy type.
 * @return void
 */
function activate( string $type ) {
	set_transient( 'webcomic_flush_rewrite_rules', true, 1 );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		if ( null !== webcomic( "option.{$collection}.{$type}_sort" ) ) {
			continue;
		}

		\register_taxonomy( "{$collection}_{$type}", [] );

		update_option(
			$collection, [
				'taxonomies'                => array_merge( [ "{$collection}_{$type}" ], webcomic( "option.{$collection}.taxonomies" ) ),
				"{$type}_slug"              => webcomic( "option.{$collection}.slug" ) . "-{$type}",
				"{$type}_sort"              => false,
				"{$type}_crossovers"        => true,
				"{$type}_hierarchical"      => false,
				"{$type}_hierarchical_skip" => false,
			]
		);
	}
}

/**
 * Deactivate a taxonomy component.
 *
 * @param string $type The taxonomy type.
 * @return void
 * @suppress PhanAccessMethodInternal - get_terms() incorrectly triggers this.
 */
function deactivate( string $type ) {
	set_transient( 'webcomic_flush_rewrite_rules', true, 1 );

	if ( ! webcomic( 'option.uninstall' ) ) {
		return;
	}

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter(
			"sanitize_option_{$collection}", function( array $options ) use ( $collection, $type ) {
				remove_filter( "sanitize_option_{$collection}", str_replace( 'Taxonomy\Shared', ucwords( $type ), __NAMESPACE__ ) . '\hook_sanitize_field_slug' );
				remove_filter( "sanitize_option_{$collection}", str_replace( 'Taxonomy\Shared', ucwords( $type ), __NAMESPACE__ ) . '\hook_sanitize_field_behavior' );

				unset(
					$options[ "{$type}_slug" ],
					$options[ "{$type}_sort" ],
					$options[ "{$type}_crossovers" ],
					$options[ "{$type}_hierarchical" ],
					$options[ "{$type}_hierarchical_skip" ]
				);

				return $options;
			}, 99
		);

		$taxonomies   = webcomic( "option.{$collection}.taxonomies" );
		$taxonomy_key = array_search( "{$collection}_{$type}", $taxonomies, true );

		if ( false !== $taxonomy_key ) {
			unset( $taxonomies[ $taxonomy_key ] );
		}

		update_option(
			$collection, [
				'taxonomies'                => $taxonomies,
				"{$type}_slug"              => webcomic( "option.{$collection}.slug" ) . "-{$type}",
				"{$type}_sort"              => false,
				"{$type}_crossovers"        => true,
				"{$type}_hierarchical"      => false,
				"{$type}_hierarchical_skip" => false,
			]
		);

		$terms = get_terms(
			[
				'get'      => 'all',
				'fields'   => 'ids',
				'taxonomy' => "{$collection}_{$type}",
			]
		);

		foreach ( $terms as $term ) {
			wp_delete_term( $term, "{$collection}_{$type}" );
		}
	}// End foreach().

	$type  = sanitize_key( $type );
	$table = webcomic( 'GLOBALS.wpdb' )->options;

	webcomic( 'GLOBALS.wpdb' )->query( "DELETE from {$table} where option_name like 'widget_mgsisk_webcomic_{$type}_%'" );
}

/**
 * Add the taxonomy allowed collection options.
 *
 * @param string $type The taxonomy type.
 * @param array  $allowed The allowed options.
 * @return array
 */
function add_allowed_options( string $type, array $allowed ) : array {
	return array_merge( $allowed, [ "{$type}_slug", "{$type}_sort", "{$type}_crossovers", "{$type}_hierarchical", "{$type}_hierarchical_skip" ] );
}

/**
 * Update new collection's taxonomy settings.
 *
 * @param string $type The taxonomy type.
 * @param array  $defaults The default settings of the new collection.
 * @return array
 */
function new_collection_taxonomy( string $type, array $defaults ) : array {
	$defaults['taxonomies'][]   = "{$defaults['id']}_{$type}";
	$defaults[ "{$type}_slug" ] = "{$defaults['slug']}-{$type}";

	return $defaults;
}

/**
 * Add the sorting page.
 *
 * @param string $type The taxonomy type.
 * @return void
 */
function add_sorter_page( string $type ) {
	set_query_var( 'orderby', 'meta_value_num' );
	set_query_var( 'meta_key', 'webcomic_order' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		$taxonomy = get_taxonomy( "{$collection}_{$type}" );

		add_submenu_page(
			"edit.php?post_type={$collection}",
			// Translators: The post type name.
			sprintf( __( 'Sort %s', 'webcomic' ), $taxonomy->labels->name ),
			// Translators: The post type name.
			sprintf( __( 'Sort %s', 'webcomic' ), $taxonomy->labels->name ),
			'manage_categories',
			"sort_{$type}",
			function() use ( $collection, $taxonomy ) {
				$hierarchical = ' data-webcomic-terms-hierarchical';

				if ( ! $taxonomy->hierarchical ) {
					$hierarchical = '';
				}

				$args = [
					'file'         => __DIR__ . '/settings-inc-sorter-page.php',
					'help'         => __( 'Drag-and-drop terms to change their order, then click Save Changes.', 'webcomic' ),
					'nonce'        => __NAMESPACE__ . 'SorterNonce',
					'taxonomy'     => $taxonomy->name,
					'admin_url'    => esc_url(
						add_query_arg(
							[
								'taxonomy'  => $taxonomy->name,
								'post_type' => $collection,
							], admin_url( 'edit-tags.php' )
						)
					),
					'collection'   => $collection,
					'hierarchical' => $hierarchical,
				];

				if ( ! wp_count_terms( $args['taxonomy'] ) ) {
					$args['help'] = __( "It looks like you haven't created any terms yet. Create some terms on the previous page, then return here to sort them.", 'webcomic' );
				}

				require $args['file'];
			}
		);
	}// End foreach().
}

/**
 * Add the settings section.
 *
 * @param string $type The taxonomy type.
 * @return void
 */
function add_settings_section( string $type ) {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		$taxonomy = get_taxonomy( "{$collection}_{$type}" );

		\add_settings_section(
			"{$collection}_{$type}",
			'<span class="dashicons dashicons-tag"></span> ' . esc_html( $taxonomy->labels->menu_name ),
			function() {
				echo '<div></div>';
			},
			"{$collection}_settings"
		);
	}
}

/**
 * Add the slug setting field.
 *
 * @param string $type The taxonomy type.
 * @return void
 */
function add_field_slug( string $type ) {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_{$type}_slug",
			__( 'Slug', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_{$type}", [
				'file'      => __DIR__ . '/settings-inc-field-slug.php',
				'option'    => webcomic( "option.{$collection}.{$type}_slug" ),
				'label_for' => "{$collection}[{$type}_slug]",
			]
		);
	}
}

/**
 * Add the behavior setting field.
 *
 * @param string $type The taxonomy type.
 * @return void
 */
function add_field_behavior( string $type ) {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_{$type}_behavior",
			__( 'Behavior', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_{$type}", [
				'file'              => __DIR__ . '/settings-inc-field-behavior.php',
				'option'            => webcomic( "option.{$collection}.{$type}_hierarchical" ),
				'option_skip'       => webcomic( "option.{$collection}.{$type}_hierarchical_skip" ),
				'option_sort'       => webcomic( "option.{$collection}.{$type}_sort" ),
				'option_crossovers' => webcomic( "option.{$collection}.{$type}_crossovers" ),
				'label_for'         => "{$collection}[{$type}_hierarchical]",
				'label_skip'        => "{$collection}[{$type}_hierarchical_skip]",
				'label_sort'        => "{$collection}[{$type}_sort]",
				'label_crossovers'  => "{$collection}[{$type}_crossovers]",
			]
		);
	}
}

/**
 * Remove extraneous taxonomy sorting page links.
 *
 * @param string $type The taxonomy type.
 * @return void
 */
function remove_sorter_submenu_page( string $type ) {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		remove_submenu_page( "edit.php?post_type={$collection}", "sort_{$type}" );
	}
}

/**
 * Add the overview help tab.
 *
 * @param string $type The taxonomy type.
 * @param string $namespace The taxonomy type namespace.
 * @return void
 */
function add_overview_help( string $type, string $namespace ) {
	$screen = get_current_screen();

	if ( ! preg_match( "/^edit-webcomic\d+_{$type}$/", $screen->id ) ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => $type,
			'title'    => __( 'Overview', 'webcomic' ),
			'callback' => "{$namespace}\\call_add_overview_help",
		]
	);
}

/**
 * Add the settings help tab.
 *
 * @param string $type The taxonomy type.
 * @param string $namespace The taxonomy type namespace.
 * @return void
 */
function add_settings_help( string $type, string $namespace ) {
	$match  = [];
	$screen = get_current_screen();

	if ( ! preg_match( '/^(webcomic\d+)_page_webcomic\d+_options$/', $screen->id, $match ) ) {
		return;
	}

	$taxonomy = get_taxonomy( "{$match[1]}_{$type}" );

	$screen->add_help_tab(
		[
			'id'       => $type,
			'title'    => $taxonomy->labels->menu_name,
			'callback' => "{$namespace}\\call_add_settings_help",
		]
	);
}

/* ===== Collection Hooks =================================================== */

/**
 * Sanitize the slug field.
 *
 * @param string $type The taxonomy type.
 * @param array  $options The submitted options.
 * @return array
 */
function sanitize_field_slug( string $type, array $options ) : array {
	$options[ "{$type}_slug" ] = sanitize_title( $options[ "{$type}_slug" ] );

	if ( ! $options[ "{$type}_slug" ] ) {
		$options[ "{$type}_slug" ] = sanitize_title( "{$options['slug']}-{$type}" );
	}

	if ( webcomic( "option.{$options['id']}.{$type}_slug" ) !== $options[ "{$type}_slug" ] ) {
		set_transient( 'webcomic_flush_rewrite_rules', true, 1 );
	}

	return $options;
}

/**
 * Sanitize the behavior field.
 *
 * @param string $type The taxonomy type.
 * @param array  $options The submitted options.
 * @return array
 */
function sanitize_field_behavior( string $type, array $options ) : array {
	$options[ "{$type}_sort" ]              = (bool) $options[ "{$type}_sort" ];
	$options[ "{$type}_crossovers" ]        = (bool) $options[ "{$type}_crossovers" ];
	$options[ "{$type}_hierarchical" ]      = (bool) $options[ "{$type}_hierarchical" ];
	$options[ "{$type}_hierarchical_skip" ] = (bool) $options[ "{$type}_hierarchical_skip" ];

	return $options;
}

/**
 * Add the comic term sorter link.
 *
 * @param string $taxonomy The current taxonomy.
 */
function hook_add_sorter_link( string $taxonomy ) {
	$url = esc_url(
		add_query_arg(
			[
				'page' => 'sort' . substr( $taxonomy, strpos( $taxonomy, '_' ) ),
			], admin_url( get_current_screen()->parent_file )
		)
	);
	// Translators: Custom taxonomy menu name.
	$label = sprintf( esc_html__( 'Sort %s', 'webcomic' ), get_taxonomy( $taxonomy )->labels->menu_name );

	echo "<p><a href='{$url}' class='button button-large'>{$label}</a></p>"; // WPCS: xss ok.
}

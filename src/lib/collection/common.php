<?php
/**
 * Common collection functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

use WP_Post;
use WP_Query;
use const Mgsisk\Webcomic\Collection\ENDPOINT;

/**
 * Add common hooks.
 *
 * @return void
 */
function common() {
	if ( ! defined( __NAMESPACE__ . '\ENDPOINT' ) ) {
		define( __NAMESPACE__ . '\ENDPOINT', 4398046511104 ); // NOTE Endpoint is 2 ^ 42.
	}

	add_filter( 'webcomic_permalink_tokens', __NAMESPACE__ . '\hook_add_permalink_tokens', 10, 3 );
	add_filter( 'webcomic_rewrite_rules', __NAMESPACE__ . '\hook_add_rewrite_rules' );
	add_filter( 'webcomic_rewrite_tokens', __NAMESPACE__ . '\hook_add_rewrite_tokens' );
	add_filter( 'webcomic_current_collection', __NAMESPACE__ . '\hook_set_current_collection_page', 10, 2 );
	add_filter( 'setup_theme', __NAMESPACE__ . '\hook_get_current_collection' );
	add_filter( 'init', __NAMESPACE__ . '\hook_register_post_types', 99 );
	add_filter( 'init', __NAMESPACE__ . '\hook_redirect_webcomic_url_requests', 999 );
	add_filter( 'init', __NAMESPACE__ . '\hook_register_scripts' );
	add_filter( 'widgets_init', __NAMESPACE__ . '\hook_register_widgets' );
	add_filter( 'widgets_init', __NAMESPACE__ . '\hook_register_sidebars_infinite', 999 );
	add_filter( 'widgets_init', __NAMESPACE__ . '\hook_register_sidebars_syndicate', 999 );
	add_filter( 'wp_enqueue_scripts', __NAMESPACE__ . '\hook_localize_scripts' );
	add_filter( 'wp_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_scripts' );
	add_filter( 'pre_option_template', __NAMESPACE__ . '\hook_get_theme' );
	add_filter( 'pre_option_stylesheet', __NAMESPACE__ . '\hook_get_theme' );
	add_filter( 'transition_post_status', __NAMESPACE__ . '\hook_set_updated_datetime', 10, 3 );
	add_filter( 'pre_get_posts', __NAMESPACE__ . '\hook_author_archive' );
	add_filter( 'pre_get_posts', __NAMESPACE__ . '\hook_category_archive' );
	add_filter( 'pre_get_posts', __NAMESPACE__ . '\hook_date_archive' );
	add_filter( 'pre_get_posts', __NAMESPACE__ . '\hook_archive_reverse' );
	add_filter( 'pre_get_posts', __NAMESPACE__ . '\hook_feeds' );
	add_filter( 'body_class', __NAMESPACE__ . '\hook_add_body_classes' );
	add_filter( 'post_class', __NAMESPACE__ . '\hook_add_post_classes', 10, 3 );
	add_filter( 'the_content_feed', __NAMESPACE__ . '\hook_add_feed_preview', 10, 2 );
	add_filter( 'wp_get_attachment_image_attributes', __NAMESPACE__ . '\hook_add_media_attributes', 10, 3 );
	add_filter( 'post_type_link', __NAMESPACE__ . '\hook_parse_post_permalinks', 10, 2 );
	add_filter( 'attachment_link', __NAMESPACE__ . '\hook_parse_post_permalinks', 10, 2 );
	add_filter( 'wp_doing_ajax', __NAMESPACE__ . '\hook_dynamic_request' );
	add_filter( 'wp_ajax_webcomic_infinite', __NAMESPACE__ . '\hook_infinite_scroll' );
	add_filter( 'wp_ajax_nopriv_webcomic_infinite', __NAMESPACE__ . '\hook_infinite_scroll' );
}

/**
 * Add permalink tokens.
 *
 * @param array   $tokens The permalink tokens.
 * @param string  $url The current post URL.
 * @param WP_Post $post The current post object.
 * @return array
 */
function hook_add_permalink_tokens( array $tokens, string $url, WP_Post $post ) : array {
	$time                 = strtotime( $post->post_date );
	$tokens['%day%']      = date( 'd', $time );
	$tokens['%year%']     = date( 'Y', $time );
	$tokens['%hour%']     = date( 'H', $time );
	$tokens['%minute%']   = date( 'i', $time );
	$tokens['%second%']   = date( 's', $time );
	$tokens['%author%']   = get_the_author_meta( 'nicename', $post->post_author );
	$tokens['%post_id%']  = $post->ID;
	$tokens['%monthnum%'] = date( 'm', $time );

	return $tokens;
}

/**
 * Add rewrite rules.
 *
 * @param array $rules The rewrite rules.
 * @return array
 */
function hook_add_rewrite_rules( array $rules ) : array {
	/**
	 * Alter rewrite tokens.
	 *
	 * This filter allows hooks to provide token => regex pairs for handling
	 * comic permalink rewrites.
	 *
	 * @param array $rewrite The list of token => regex rewrites.
	 */
	$tokens = apply_filters( 'webcomic_rewrite_tokens', [] );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		$rules[ webcomic( "option.{$collection}.slug" ) ] = $collection;
		$rules[ str_replace( array_keys( $tokens ), $tokens, webcomic( "option.{$collection}.permalink" ) ) ] = $collection;
		$rules[ str_replace( array_merge( array_keys( $tokens ), [ '(', ')' ] ), array_merge( $tokens, [ '' ] ), webcomic( "option.{$collection}.permalink" ) ) ] = $collection;
	}

	return $rules;
}

/**
 * Add rewrite tokens.
 *
 * @param array $tokens The rewrite tokens.
 * @return array
 */
function hook_add_rewrite_tokens( array $tokens ) : array {
	$tokens['%day%']      = '([0-9]{1,2})';
	$tokens['%year%']     = '([0-9]{4})';
	$tokens['%hour%']     = '([0-9]{1,2})';
	$tokens['%minute%']   = '([0-9]{1,2})';
	$tokens['%second%']   = '([0-9]{1,2})';
	$tokens['%author%']   = '([^/]+)';
	$tokens['%post_id%']  = '([0-9]+)';
	$tokens['%monthnum%'] = '([0-9]{1,2})';

	return $tokens;
}

/**
 * Get the current collection based on a page request.
 *
 * @param string $collection The current collection.
 * @param string $url The requested URL.
 * @return string
 */
function hook_set_current_collection_page( string $collection, string $url ) : string {
	if ( $collection ) {
		return $collection;
	} elseif ( ! $url ) {
		return get_post_meta( get_option( 'page_on_front' ), 'webcomic_collection', true );
	}

	$page = url_to_postid( $url );

	if ( ! $page || 'page' !== get_post_type( $page ) ) {
		return $collection;
	}

	return get_post_meta( $page, 'webcomic_collection', true );
}

/**
 * Get the current collection based on the page request.
 *
 * @return void
 */
function hook_get_current_collection() {
	preg_match( '/webcomic\d+/', webcomic( 'GLOBALS._SERVER.REQUEST_URI' ), $match );

	$url    = explode( '?', explode( '#', webcomic( 'GLOBALS._SERVER.REQUEST_URI' ) )[0] )[0];
	$domain = wp_parse_url( home_url( '/' ) );

	if ( isset( $domain['path'] ) && 0 === strpos( $url, $domain['path'] ) ) {
		$url = substr_replace( $url, '', 0, strlen( $domain['path'] ) );
	}

	$url = trim( $url, '/' );

	if ( ! $match && get_option( 'rewrite_rules' ) ) {
		foreach ( get_option( 'rewrite_rules' ) as $regex => $query ) {
			if ( ! preg_match( "~^{$regex}~", $url ) ) {
				continue;
			}

			preg_match( '/webcomic\d+/', $query, $match );

			break;
		}

		if ( ! $match ) {
			/**
			 * Alter comic rewrite rules.
			 *
			 * This filter allows hooks to provide URL => collection mappings for
			 * identifying pages that belong to a specific Webcomic collection.
			 *
			 * @param array $rules The list of URL => collection mappings.
			 */
			$rules = apply_filters( 'webcomic_rewrite_rules', [] );

			foreach ( $rules as $query => $id ) {
				if ( 0 !== strpos( $regex, $query ) ) {
					continue;
				}

				$match[0] = $id;

				break;
			}
		}
	}

	$match[] = '';

	/**
	 * Alter the current comic collection.
	 *
	 * This filter allows hooks to provide additional matching features for
	 * identifying the current collection. As a constant, this value cannot be
	 * changed once it has been set.
	 *
	 * @param string $collection The current collection.
	 * @param string $url The requested URL.
	 */
	$collection = apply_filters( 'webcomic_current_collection', $match[0], $url );

	/**
	 * Current collection.
	 *
	 * @var string
	 */
	define( __NAMESPACE__ . '\COLLECTION', $collection );
}

/**
 * Register custom post types.
 *
 * @return void
 * @suppress PhanUndeclaredConstant - ENDPOINT incorrectly triggers this.
 */
function hook_register_post_types() {
	$labels = [
		'add_new'               => __( 'Add New', 'webcomic' ),
		'add_new_item'          => __( 'Add New Comic', 'webcomic' ),
		'edit_item'             => __( 'Edit Comic', 'webcomic' ),
		'new_item'              => __( 'New Comic', 'webcomic' ),
		'view_item'             => __( 'View Comic', 'webcomic' ),
		'view_items'            => __( 'View Comics', 'webcomic' ),
		'search_items'          => __( 'Search Comics', 'webcomic' ),
		'not_found'             => __( 'No comics found.', 'webcomic' ),
		'not_found_in_trash'    => __( 'No comics found in Trash.', 'webcomic' ),
		'all_items'             => __( 'All Comics', 'webcomic' ),
		'attributes'            => __( 'Comic Attributes', 'webcomic' ),
		'insert_into_item'      => __( 'Insert into comic', 'webcomic' ),
		'uploaded_to_this_item' => __( 'Uploaded to this comic', 'webcomic' ),
	];

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		$supports = webcomic( "option.{$collection}.supports" );

		if ( ! $supports ) {
			$supports = false;
		}

		$labels['name'] = webcomic( "option.{$collection}.name" );
		// Translators: The post type name.
		$labels['archives']      = sprintf( __( '%s Archive', 'webcomic' ), webcomic( "option.{$collection}.name" ) );
		$labels['singular_name'] = webcomic( "option.{$collection}.name" );
		// Translators: The post type name.
		$labels['name_admin_bar'] = sprintf( __( '%s Comic', 'webcomic' ), webcomic( "option.{$collection}.name" ) );

		register_post_type(
			$collection,
			[
				'labels'      => $labels,
				'public'      => true,
				'supports'    => $supports,
				'menu_icon'   => 'dashicons-images-alt2',
				'taxonomies'  => webcomic( "option.{$collection}.taxonomies" ),
				'description' => webcomic( "option.{$collection}.description" ),
				'has_archive' => webcomic( "option.{$collection}.slug" ),
				'rewrite'     => [
					'slug'       => webcomic( "option.{$collection}.permalink" ),
					'ep_mask'    => ENDPOINT | EP_PERMALINK,
					'with_front' => false,
				],
			]
		);
	}
}

/**
 * Redirect webcomic_url requests.
 *
 * @return void
 */
function hook_redirect_webcomic_url_requests() {
	if ( null === webcomic( 'GLOBALS._REQUEST.webcomic_url' ) ) {
		return;
	}

	$args = array_map(
		function( $value ) {
				return maybe_unserialize( rawurldecode( $value ) );
		}, array_pad( explode( '-', webcomic( 'GLOBALS._REQUEST.webcomic_url' ), 4 ), 4, '' )
	);

	if ( ! is_array( $args[3] ) ) {
		$args[3] = [];
	}

	if ( $args[0] ) {
		$args[3]['post_type'] = $args[0];
	}

	if ( $args[1] ) {
		$args[3]['relation'] = $args[1];
	}

	$url = get_webcomic_url( $args[2], $args[3] );

	if ( 'webcomic_dynamic' === webcomic( 'GLOBALS._REQUEST.action' ) ) {
		$url = esc_url(
			add_query_arg(
				[
					'action' => 'webcomic_dynamic',
				], $url
			)
		);
	}

	add_filter( 'wp_doing_ajax', '__return_true' ) && wp_safe_redirect( $url ) && wp_die();
}

/**
 * Register common scripts.
 *
 * @return void
 */
function hook_register_scripts() {
	wp_register_script(
		__NAMESPACE__ . 'CommonJS',
		plugins_url( 'srv/collection/common.js', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' ),
		true
	);
}

/**
 * Register collection widgets.
 *
 * @return void
 */
function hook_register_widgets() {
	register_widget( __NAMESPACE__ . '\Widget\FirstWebcomicLink' );
	register_widget( __NAMESPACE__ . '\Widget\LastWebcomicLink' );
	register_widget( __NAMESPACE__ . '\Widget\NextWebcomicLink' );
	register_widget( __NAMESPACE__ . '\Widget\PreviousWebcomicLink' );
	register_widget( __NAMESPACE__ . '\Widget\RandomWebcomicLink' );
	register_widget( __NAMESPACE__ . '\Widget\WebcomicCollectionLink' );
	register_widget( __NAMESPACE__ . '\Widget\WebcomicCollectionsList' );
	register_widget( __NAMESPACE__ . '\Widget\WebcomicLink' );
	register_widget( __NAMESPACE__ . '\Widget\WebcomicMedia' );
	register_widget( __NAMESPACE__ . '\Widget\WebcomicsList' );
}

/**
 * Register infinite sidebars.
 *
 * @return void
 */
function hook_register_sidebars_infinite() {
	register_sidebar(
		[
			'id'            => 'webcomic-infinite',
			'after_widget'  => '</div>',
			'before_widget' => '<div class="widget %2$s">',
			'name'          => __( 'Webcomic Infinite', 'webcomic' ),
			'description'   => __( 'Add widgets here to change how comics look when displayed in an infinitely-scrolling container.', 'webcomic' ),
		]
	);

	$args = [
		'after_widget'  => '</div>',
		'before_widget' => '<div class="widget %2$s">',
		// Translators: Custom post type name.
		'name'          => __( '%s Infinite', 'webcomic' ),
		// Translators: Custom post type name.
		'description'   => __( 'Add widgets here to change how %s comics look when displayed in an infinitely-scrolling container.', 'webcomic' ),
	];

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		if ( ! in_array( 'infinite', webcomic( "option.{$collection}.sidebars" ), true ) ) {
			continue;
		}

		$sidebar_args                = $args;
		$sidebar_args['id']          = "{$collection}-infinite";
		$sidebar_args['name']        = sprintf( $args['name'], webcomic( "option.{$collection}.name" ) );
		$sidebar_args['description'] = sprintf( $args['description'], webcomic( "option.{$collection}.name" ) );

		register_sidebar( $sidebar_args );
	}
}

/**
 * Register syndication sidebars.
 *
 * @return void
 */
function hook_register_sidebars_syndicate() {
	register_sidebar(
		[
			'id'            => 'webcomic-syndicate',
			'after_widget'  => '</div>',
			'before_widget' => '<div class="widget %2$s">',
			'name'          => __( 'Webcomic Syndication', 'webcomic' ),
			'description'   => __( 'Add widgets here to change how comic previews look in site syndication feeds.', 'webcomic' ),
		]
	);

	$args = [
		'after_widget'  => '</div>',
		'before_widget' => '<div class="widget %2$s">',
		// Translators: Custom post type name.
		'name'          => __( '%s Syndication', 'webcomic' ),
		// Translators: Custom post type name.
		'description'   => __( 'Add widgets here to change how %s comic previews look in site syndication feeds.', 'webcomic' ),
	];

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		if ( ! webcomic( "option.{$collection}.syndicate_preview" ) || ! in_array( 'syndicate', webcomic( "option.{$collection}.sidebars" ), true ) ) {
			continue;
		}

		$sidebar_args                = $args;
		$sidebar_args['id']          = "{$collection}-syndicate";
		$sidebar_args['name']        = sprintf( $args['name'], webcomic( "option.{$collection}.name" ) );
		$sidebar_args['description'] = sprintf( $args['description'], webcomic( "option.{$collection}.name" ) );

		register_sidebar( $sidebar_args );
	}
}

/**
 * Localze common scripts.
 *
 * @return void
 */
function hook_localize_scripts() {
	wp_localize_script(
		__NAMESPACE__ . 'CommonJS',
		'webcomicCommonJS',
		[
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		]
	);
}

/**
 * Enqueue common scripts.
 *
 * @return void
 */
function hook_enqueue_scripts() {
	if ( is_admin() ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'CommonJS' );
}

/**
 * Get the requested collection's theme and stylesheet.
 *
 * @param mixed $template The current template.
 * @return mixed
 * @suppress PhanUndeclaredConstant - COLLECTION incorrectly triggers this.
 */
function hook_get_theme( $template ) {
	if ( is_admin() || ! defined( __NAMESPACE__ . '\COLLECTION' ) ) {
		return $template;
	}

	$collection = COLLECTION;

	if ( ! $collection || ! webcomic( "option.{$collection}.theme" ) ) {
		return $template;
	}

	list( $theme, $stylesheet ) = explode( '|', webcomic( "option.{$collection}.theme" ) );

	if ( ! is_readable( trailingslashit( get_theme_root() ) . $theme ) ) {
		return $template;
	} elseif ( 'pre_option_stylesheet' === current_filter() ) {
		return $stylesheet;
	}

	return $theme;
}

/**
 * Set collection update timestamp.
 *
 * @param string  $new_status The new post status.
 * @param string  $old_status The old post status.
 * @param WP_Post $post The post being updated.
 * @return void
 */
function hook_set_updated_datetime( string $new_status, string $old_status, WP_Post $post ) {
	if ( ! is_a_webcomic( $post ) ) {
		return;
	}

	$latest  = get_posts(
		[
			'post_type'      => $post->post_type,
			'posts_per_page' => 1,
		]
	);
	$updated = 0;

	if ( $latest ) {
		$updated = $latest[0]->post_date;
	}

	if ( webcomic( "option.{$post->post_type}.updated" ) === $latest ) {
		return;
	}

	$options            = webcomic( "option.{$post->post_type}" );
	$options['updated'] = $updated;

	update_option( $post->post_type, $options );
}

/**
 * Include comics on author archive pages.
 *
 * @param WP_Query $query The query to modify.
 * @return WP_Query
 */
function hook_author_archive( WP_Query $query ) : WP_Query {
	if ( $query->is_admin() || ! $query->is_main_query() || ! $query->is_author() || $query->get( 'post_type' ) ) {
		return $query;
	}

	$post_types = [ 'post' ];

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		if ( ! in_array( 'author', webcomic( "option.{$collection}.supports" ), true ) ) {
			continue;
		}

		$post_types[] = $collection;
	}

	$query->set( 'post_type', $post_types );

	return $query;
}

/**
 * Include comics on category archive pages.
 *
 * @param WP_Query $query The query to modify.
 * @return WP_Query
 */
function hook_category_archive( WP_Query $query ) : WP_Query {
	if ( $query->is_admin() || ! $query->is_main_query() || ! $query->is_category() || $query->get( 'post_type' ) ) {
		return $query;
	}

	$post_types = [ 'post' ];

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		if ( ! in_array( 'category', webcomic( "option.{$collection}.taxonomies" ), true ) ) {
			continue;
		}

		$post_types[] = $collection;
	}

	$query->set( 'post_type', $post_types );

	return $query;
}

/**
 * Include comics on date archive pages.
 *
 * @param WP_Query $query The query to modify.
 * @return WP_Query
 */
function hook_date_archive( WP_Query $query ) : WP_Query {
	if ( $query->is_admin() || ! $query->is_main_query() || ! $query->is_date() || $query->get( 'post_type' ) ) {
		return $query;
	}

	$collections = [];

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		if ( ! webcomic( "option.{$collection}.archive_date" ) ) {
			continue;
		}

		$collections[] = $collection;
	}

	$query->set( 'post_type', array_merge( [ 'post' ], $collections ) );

	return $query;
}

/**
 * Reverse comic archive ordering.
 *
 * @param WP_Query $query The query to modify.
 * @return WP_Query
 */
function hook_archive_reverse( WP_Query $query ) : WP_Query {
	if ( is_admin() || ! $query->is_main_query() || ! is_archive() ) {
		return $query;
	}

	$post_type = (array) $query->get( 'post_type' );

	if ( 1 < count( $post_type ) || ( $post_type[0] && ! webcomic_collection_exists( $post_type[0] ) ) ) {
		return $query;
	}

	$collection = get_webcomic_collection();

	if ( ! webcomic( "option.{$collection}.archive_reverse" ) ) {
		return $query;
	}

	$query->set( 'order', 'asc' );

	return $query;
}

/**
 * Include comics in the main site feed.
 *
 * @param WP_Query $query The query to modify.
 * @return WP_Query
 */
function hook_feeds( WP_Query $query ) : WP_Query {
	if ( $query->is_admin() || ! $query->is_main_query() || ! $query->is_feed() || 1 !== count( $query->query ) ) {
		return $query;
	}

	$post_types = [ 'post' ];

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		if ( ! webcomic( "option.{$collection}.syndicate" ) ) {
			continue;
		}

		$post_types[] = $collection;
	}

	$query->set( 'post_type', array_merge( [ 'post' ], $post_types ) );

	return $query;
}

/**
 * Add CSS classes to the body element.
 *
 * @param array $classes The current list of CSS classes.
 * @return array
 */
function hook_add_body_classes( array $classes ) : array {
	$collection = get_webcomic_collection();

	if ( ! $collection ) {
		return $classes;
	}

	$classes[] = 'webcomic';
	$classes[] = $collection;
	$classes[] = 'webcomic-' . webcomic( "option.{$collection}.slug" );

	return $classes;
}

/**
 * Add CSS classes to post elements.
 *
 * @param array $classes The current list of classes.
 * @param array $class A list of additional classes.
 * @param int   $post The current post ID.
 * @return array
 */
function hook_add_post_classes( array $classes, array $class, int $post ) : array {
	$post_type = get_post_type( $post );

	if ( ! webcomic( "option.{$post_type}" ) ) {
		return $classes;
	}

	$count     = count( get_post_meta( $post, 'webcomic_media' ) );
	$classes[] = "webcomic-media-{$count}";

	return $classes;
}

/**
 * Add comic previews to syndication feeds.
 *
 * @param string $content The post content.
 * @param string $type The current feed type.
 */
function hook_add_feed_preview( string $content, string $type ) {
	$collection = get_post_type();

	if ( ! webcomic( "option.{$collection}.syndicate_preview" ) ) {
		return $content;
	}

	ob_start();

	if ( ! dynamic_sidebar( "{$collection}-syndicate" ) && ! dynamic_sidebar( 'webcomic-syndicate' ) ) {
		the_widget( __NAMESPACE__ . '\Widget\WebcomicMedia' );
	}

	$prefix = ob_get_clean();

	return $prefix . $content;
}

/**
 * Add additional comic media attributes.
 *
 * @param array   $attr The media attributes.
 * @param WP_Post $media The media item.
 * @param mixed   $size The media size.
 */
function hook_add_media_attributes( array $attr, WP_Post $media, $size ) {
	if ( is_a_webcomic_media( $media ) && $media->post_excerpt ) {
		$attr['title'] = esc_attr( wp_strip_all_tags( $media->post_excerpt ) );
	}

	return $attr;
}

/**
 * Parse collection permalinks.
 *
 * @param string $url The current post URL.
 * @param mixed  $post The current post.
 * @return string
 */
function hook_parse_post_permalinks( string $url, $post ) : string {
	if ( false === strpos( $url, '%' ) || ! is_a_webcomic( $post ) ) {
		return $url;
	} elseif ( ! $post instanceof WP_Post ) {
		$post = get_webcomic( $post );
	}

	/**
	 * Alter comic permalink tokens.
	 *
	 * This filter allows hooks to provide specific token => value pairs for
	 * rewriting comic permalinks.
	 *
	 * ## Core tokens
	 *
	 * |Token     |Value                           |Example |
	 * |----------|--------------------------------|--------|
	 * |%author%  |The comic author's username.    |username|
	 * |%day%     |The publish day of the comic.   |01      |
	 * |%hour%    |The publish hour of the comic.  |16      |
	 * |%minute%  |The publish minute of the comic.|52      |
	 * |%monthnum%|The publish month of the comic. |05      |
	 * |%post_id% |The comic's post ID.            |9       |
	 * |%second%  |The publish second of the comic.|22      |
	 * |%year%    |The publish year of the comic.  |2099    |
	 *
	 * ## Taxonomy tokens
	 *
	 * |Token                |Value                  |Example       |
	 * |---------------------|-----------------------|--------------|
	 * |%webcomic*_character%|The comic's characters.|character-slug|
	 * |%webcomic*_storyline%|The comic's storylines.|storyline-slug|
	 *
	 * The `*` in these tokens is a placeholder for the collection ID number, like
	 * `webcomic1_character` or `webcomic42_storyline`.
	 *
	 * @param array $tokens The list of token => value pairs.
	 * @param string $url The URL being rewritten.
	 * @param WP_Post $post The post the URL is being rewritten for.
	 */
	$tokens = apply_filters( 'webcomic_permalink_tokens', [], $url, $post );

	return str_replace( array_keys( $tokens ), $tokens, $url );
}

/**
 * Handle dynamic comic requests.
 *
 * @param bool $ajax Whether we're doing an AJAX request.
 * @return bool
 */
function hook_dynamic_request( bool $ajax ) : bool {
	if ( 'webcomic_dynamic' !== webcomic( 'GLOBALS._REQUEST.action' ) ) {
		return $ajax;
	}

	return true;
}

/**
 * Handle infinite scroll requests.
 *
 * @return void
 */
function hook_infinite_scroll() {
	if ( ! webcomic( 'GLOBALS._REQUEST.args' ) ) {
		return;
	}

	parse_str( htmlspecialchars_decode( webcomic( 'GLOBALS._REQUEST.args' ) ), $args );

	$args = [
		'fields' => 'ids',
	] + $args + [
		'post_type'      => webcomic( 'option.collections' ),
		'posts_per_page' => 1,
	];

	if ( $args['posts_per_page'] < 1 ) {
		$args['posts_per_page'] = 1;
	}

	foreach ( get_webcomics( $args ) as $comic ) {
		webcomic_setup_postdata( $comic );

		echo '<div class="widget-area webcomic-infinite">';

		if ( ! dynamic_sidebar( get_post_type() . '-infinite' ) && ! dynamic_sidebar( 'webcomic-infinite' ) ) {
			the_widget( __NAMESPACE__ . '\Widget\WebcomicMedia' );
		}

		echo '</div>';

		webcomic_reset_postdata();
	}

	$args['offset'] += $args['posts_per_page'];

	if ( ! get_webcomics( $args ) ) {
		echo '<wbr class="finished">';
	}

	wp_die();
}

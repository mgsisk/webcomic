<?php
/**
 * Collection shortcodes
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

/**
 * Add shortcodes.
 *
 * @return void
 */
function shortcodes() {
	add_shortcode( 'webcomic_collection_count', __NAMESPACE__ . '\webcomic_collection_count_shortcode' );
	add_shortcode( 'webcomic_collection_description', __NAMESPACE__ . '\webcomic_collection_description_shortcode' );
	add_shortcode( 'webcomic_collection_media', __NAMESPACE__ . '\webcomic_collection_media_shortcode' );
	add_shortcode( 'webcomic_collection_title', __NAMESPACE__ . '\webcomic_collection_title_shortcode' );
	add_shortcode( 'webcomic_collection_updated', __NAMESPACE__ . '\webcomic_collection_updated_shortcode' );
	add_shortcode( 'webcomic_collection_link', __NAMESPACE__ . '\webcomic_collection_link_shortcode' );
	add_shortcode( 'webcomic_collections_list', __NAMESPACE__ . '\webcomic_collections_list_shortcode' );
	add_shortcode( 'webcomic_infinite_link', __NAMESPACE__ . '\webcomic_infinite_link_shortcode' );
	add_shortcode( 'webcomic_link', __NAMESPACE__ . '\webcomic_link_shortcode' );
	add_shortcode( 'first_webcomic_link', __NAMESPACE__ . '\webcomic_link_shortcode' );
	add_shortcode( 'previous_webcomic_link', __NAMESPACE__ . '\webcomic_link_shortcode' );
	add_shortcode( 'next_webcomic_link', __NAMESPACE__ . '\webcomic_link_shortcode' );
	add_shortcode( 'last_webcomic_link', __NAMESPACE__ . '\webcomic_link_shortcode' );
	add_shortcode( 'random_webcomic_link', __NAMESPACE__ . '\webcomic_link_shortcode' );
	add_shortcode( 'webcomic_media', __NAMESPACE__ . '\webcomic_media_shortcode' );
	add_shortcode( 'has_webcomic_media', __NAMESPACE__ . '\has_webcomic_media_shortcode' );
	add_shortcode( 'is_a_webcomic', __NAMESPACE__ . '\is_a_webcomic_shortcode' );
	add_shortcode( 'is_a_first_webcomic', __NAMESPACE__ . '\is_a_webcomic_shortcode' );
	add_shortcode( 'is_a_previous_webcomic', __NAMESPACE__ . '\is_a_webcomic_shortcode' );
	add_shortcode( 'is_a_next_webcomic', __NAMESPACE__ . '\is_a_webcomic_shortcode' );
	add_shortcode( 'is_a_last_webcomic', __NAMESPACE__ . '\is_a_webcomic_shortcode' );
	add_shortcode( 'is_a_webcomic_media', __NAMESPACE__ . '\is_a_webcomic_media_shortcode' );
	add_shortcode( 'is_a_webcomic_page', __NAMESPACE__ . '\is_a_webcomic_page_shortcode' );
	add_shortcode( 'is_webcomic', __NAMESPACE__ . '\is_webcomic_shortcode' );
	add_shortcode( 'is_first_webcomic', __NAMESPACE__ . '\is_webcomic_shortcode' );
	add_shortcode( 'is_previous_webcomic', __NAMESPACE__ . '\is_webcomic_shortcode' );
	add_shortcode( 'is_next_webcomic', __NAMESPACE__ . '\is_webcomic_shortcode' );
	add_shortcode( 'is_last_webcomic', __NAMESPACE__ . '\is_webcomic_shortcode' );
	add_shortcode( 'is_webcomic_media', __NAMESPACE__ . '\is_webcomic_media_shortcode' );
	add_shortcode( 'is_webcomic_page', __NAMESPACE__ . '\is_webcomic_page_shortcode' );
	add_shortcode( 'webcomics_list', __NAMESPACE__ . '\webcomics_list_shortcode' );
}

/**
 * Display a collection count.
 *
 * @uses get_webcomic_collection_count()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed $collection Optional collection to display a comic count for.
 * }
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_collection_count_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'collection' => null,
		], $atts, $name
	);

	return (string) get_webcomic_collection_count( $args['collection'] );
}

/**
 * Display a collection description.
 *
 * @uses get_webcomic_collection_description()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed $collection Optional collection to display a description for.
 * }
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_collection_description_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'collection' => null,
		], $atts, $name
	);

	return get_webcomic_collection_description( $args['collection'] );
}

/**
 * Display a collection image.
 *
 * @uses get_webcomic_collection_media()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type string $size Optional media size.
 *     @type mixed  $collection Optional collection to display an image for.
 * }
 * @param string $content Optional shortcode content; mapped to $args['size'].
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_collection_media_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'size'       => 'full',
			'collection' => null,
		], $atts, $name
	);

	if ( $content ) {
		$args['size'] = $content;
	}

	return get_webcomic_collection_media( $args['size'], $args['collection'] );
}

/**
 * Display a collection title.
 *
 * @uses get_webcomic_collection_title()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed $collection Optional collection to display a title for.
 * }
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_collection_title_shortcode( $atts, string $content, string $name ) : string {
	if ( isset( $atts['prefix'] ) ) {
		webcomic_error( __( 'The classic behavior of [webcomic_collection_title] is deprecated; please refer to the webcomic_collection_title_shortcode() documentation for updated usage information.', 'webcomic' ) );

		return webcomic_compat( 'webcomic_collection_title_shortcode_', [ $atts, $content, $name ], '' );
	}

	$args = shortcode_atts(
		[
			'collection' => null,
		], $atts, $name
	);

	return get_webcomic_collection_title( $args['collection'] );
}

/**
 * Display a collection updated time.
 *
 * @uses get_webcomic_collection_updated()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type string $format Optional datetime format.
 *     @type mixed  $collection Optional collection to display an updated
 *                              datetime for.
 * }
 * @param string $content Optional shortcode content; mapped to $args['format'].
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_collection_updated_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'format'     => '',
			'collection' => null,
		], $atts, $name
	);

	if ( $content ) {
		$args['format'] = do_shortcode( htmlspecialchars_decode( $content ) );
	}

	return get_webcomic_collection_updated( $args['format'], $args['collection'] );
}

/**
 * Display a collection link.
 *
 * @uses get_webcomic_collection_link()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type string $link Optional link text, like 'before{{text}}after'.
 *     @type mixed  $collection Optional collection to display a link for.
 *     @type mixed  $post Optional reference post.
 *     @type array  $args Optional arguments.
 * }
 * @param string $content Optional shortcode content; mapped to $args['link'].
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_collection_link_shortcode( $atts, string $content, string $name ) : string {
	if ( isset( $atts['format'] ) ) {
		webcomic_error( __( 'The classic behavior of [webcomic_collection_link] is deprecated; please refer to the webcomic_collection_link_shortcode() documentation for updated usage information.', 'webcomic' ) );

		return webcomic_compat( 'webcomic_collection_link_shortcode_', [ $atts, $content, $name ], '' );
	}

	$args = shortcode_atts(
		[
			'link'       => '%title',
			'collection' => null,
			'post'       => null,
			'args'       => [],
		], $atts, $name
	);

	if ( $content ) {
		$args['link'] = do_shortcode( htmlspecialchars_decode( $content ) );
	}

	$args['link'] = htmlspecialchars_decode( $args['link'] );

	if ( is_string( $args['args'] ) ) {
		parse_str( htmlspecialchars_decode( $args['args'] ), $args['args'] );
	}

	return get_webcomic_collection_link( $args['link'], $args['collection'], $args['post'], $args['args'] );
}

/**
 * Display a list of collections.
 *
 * @uses get_webcomic_collections_list()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type string $format Optional list format, like before{{join}}after.
 *                          Including `<select>` or `<optgroup>` elements will
 *                          convert links to `<option>` elements. Using
 *                          'webcomics_optgroup' as the join will replace
 *                          collection links with a list of comic `<option>`
 *                          elements wrapped in an `<optgroup>`.
 *     @type string $link Optional link text, like before{{text}}after.
 *     @type mixed  $link_post Optional reference post for collection links.
 *     @type array  $link_args Optional link arguments.
 *     @type string $feed Optional collection feed link text.
 *     @type string $feed_type Optional collection feed type; one of atom,
 *                             rss, or rss2.
 *     @type int    $cloud_min Optional weighted list minimum font size.
 *     @type int    $cloud_max Optional weighted list maximum font size.
 *     @type string $current Optional collection ID of the current collection.
 *     @type string $walker Optional custom Walker class to use instead of
 *                          Mgsisk\Webcomic\Collection\Walker\CollectionLister.
 *     @type array  $webcomics Optional get_webcomics_list() arguments.
 *     @type bool   $hide_empty Whether to include or exclude empty
 *                              collections.
 *     @type int    $limit Optional maximum number of collections to return.
 *     @type mixed  $not_related_by Optional taxonomies the collections must
 *                                  not be related by.
 *     @type string $order Optional collection sort order; one of asc or desc.
 *     @type string $orderby Optional collection sort field; one of name,
 *                           slug, count, updated, or rand.
 *     @type mixed  $related_to Optional post object, term object, or
 *                              collection ID the collections must be related
 *                              to.
 *     @type mixed  $related_by Optional taxonomies the collections must be
 *                              related by.
 * }
 * @param string $content Optional shortcode content; mapped to $args['link'].
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_collections_list_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'cloud_max'      => 0,
			'cloud_min'      => 0,
			'current'        => '',
			'feed_type'      => 'atom',
			'feed'           => '',
			'format'         => ', ',
			'hide_empty'     => true,
			'limit'          => 0,
			'link_args'      => [],
			'link_post'      => null,
			'link'           => '%title',
			'not_related_by' => [],
			'order'          => 'asc',
			'orderby'        => 'name',
			'related_by'     => [],
			'related_to'     => null,
			'walker'         => '',
			'webcomics'      => [],
		], $atts, $name
	);

	$args['format']    = htmlspecialchars_decode( $args['format'] );
	$args['cloud_min'] = (int) $args['cloud_min'];
	$args['cloud_max'] = (int) $args['cloud_max'];

	if ( $content ) {
		$args['link'] = do_shortcode( htmlspecialchars_decode( $content ) );
	}

	$args['link'] = htmlspecialchars_decode( $args['link'] );

	if ( is_string( $args['link_args'] ) ) {
		parse_str( htmlspecialchars_decode( $args['link_args'] ), $args['link_args'] );
	}

	if ( is_string( $args['webcomics'] ) ) {
		parse_str( htmlspecialchars_decode( $args['webcomics'] ), $args['webcomics'] );
	}

	if ( is_string( $args['related_to'] && ! webcomic_collection_exists( $args['related_to'] ) ) ) {
		$args['related_to'] = (int) $args['related_to'];
	}

	return get_webcomic_collections_list( $args );
}

/**
 * Display an infinite comic link.
 *
 * @uses get_webcomic_infinite_link()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type string $link Optional link text, like before{{text}}after.
 * }
 * @param string $content Optional shortcode content; mapped to $args['link'].
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_infinite_link_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'link' => '',
		], $atts, $name
	);

	if ( $content ) {
		$args['link'] = do_shortcode( htmlspecialchars_decode( $content ) );
	}

	$args['link'] = htmlspecialchars_decode( $args['link'] );

	return get_webcomic_infinite_link( $args['link'] );
}

/**
 * Display a link to a comic.
 *
 * @uses get_webcomic_link()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type string $link Optional link text, like before{{text}}after.
 *     @type mixed  $post Optional post to get a link for.
 *     @type array  $args Optional arguments. The shortcode name determines the
 *                        value of the relation argument.
 * }
 * @param string $content Optional shortcode content; mapped to $args['link'].
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_link_shortcode( $atts, string $content, string $name ) : string {
	if ( isset( $atts['format'] ) || isset( $atts['in_same_term'] ) || isset( $atts['excluded_terms'] ) || isset( $atts['taxonomy'] ) || isset( $atts['collection'] ) || isset( $atts['the_post'] ) || isset( $atts['cache'] ) ) {
		// Translators: The shortcode name.
		webcomic_error( sprintf( __( 'The classic behavior of [%s] is deprecated; please refer to the webcomic_link_shortcode() documentation for updated usage information.', 'webcomic' ), $name ) );

		return webcomic_compat( 'webcomic_link_shortcode_', [ $atts, $content, $name ], '' );
	}

	$args = shortcode_atts(
		[
			'link' => [
				'webcomic' => '%title',
				'first'    => '&laquo;',
				'previous' => '&lsaquo;',
				'next'     => '&rsaquo;',
				'last'     => '&raquo;',
				'random'   => '&infin;',
			],
			'post' => null,
			'args' => [],
		], $atts, $name
	);

	if ( is_string( $args['args'] ) ) {
		parse_str( htmlspecialchars_decode( $args['args'] ), $args['args'] );
	}

	$args['args']['relation'] = substr( $name, 0, strpos( $name, '_' ) );

	if ( $content ) {
		$args['link'] = do_shortcode( htmlspecialchars_decode( $content ) );
	} elseif ( is_array( $args['link'] ) ) {
		$args['link'] = $args['link'][ $args['args']['relation'] ];
	}

	$args['link'] = htmlspecialchars_decode( $args['link'] );

	if ( 'webcomic' === $args['args']['relation'] ) {
		unset( $args['args']['relation'] );
	}

	return get_webcomic_link( $args['link'], $args['post'], $args['args'] );
}

/**
 * Display comic media.
 *
 * @uses get_webcomic_media()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type string $format Optional media format, like
 *                           before{{join}}after{size}. Size may be be any valid
 *                           image size or a comma-separated list of width and
 *                           height pixel values (in that order), and may be
 *                           specified without the rest of the format arguments.
 *     @type mixed  $post Optional post to get comic media for.
 *     @type array  $args Optional arguments.
 * }
 * @param string $content Optional shortcode content, mapped to $atts['format'].
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_media_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'format' => 'full',
			'post'   => null,
			'args'   => [],
		], $atts, $name
	);

	if ( $content ) {
		$args['format'] = do_shortcode( htmlspecialchars_decode( $content ) );
	}

	if ( is_string( $args['args'] ) ) {
		parse_str( htmlspecialchars_decode( $args['args'] ), $args['args'] );
	}

	return get_webcomic_media( $args['format'], $args['post'], $args['args'] );
}

/**
 * Display content if the post has comic media.
 *
 * @uses has_webcomic_media()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed $post Optional post to check for comic media.
 *     @type mixed $count Optional media count. May be an integer or a
 *                        comparison argument, like `< 3`. Valid comparison
 *                        operatorsare `<`, `lt`, `<=`, `le`, `>`, `gt`, `>=`,
 *                        `ge`, `==`, `=`, `eq`, `!=`, `<>`, and `ne`.
 * }
 * @param string $content Content to display if the post has comic media.
 * @param string $name Shortcode name.
 * @return string
 */
function has_webcomic_media_shortcode( $atts, string $content, string $name ) : string {
	if ( ! $content ) {
		return '';
	}

	$args  = shortcode_atts(
		[
			'post'  => null,
			'count' => '',
		], $atts, $name
	);
	$match = [];

	if ( ! has_webcomic_media( $args['post'] ) ) {
		return '';
	} elseif ( preg_match( '/^(<|lt|<=|le|>|gt|>=|ge|==|=|eq|!=|<>|ne)\s*(\d+)$/', htmlspecialchars_decode( $args['count'] ), $match ) && ! version_compare( (string) count( get_post_meta( $args['post'], 'webcomic_media' ) ), $match[2], $match[1] ) ) {
		return '';
	}

	return do_shortcode( $content );
}

/**
 * Display content if the post is a comic.
 *
 * @uses is_a_webcomic()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed $post Optional post to check.
 *     @type mixed $relative Optional reference post.
 *     @type array $args Optional arguments. The shortcode name determines the
 *                       value of the relation argument.
 * }
 * @param string $content Content to display if the post is a comic.
 * @param string $name Shortcode name.
 * @return string
 */
function is_a_webcomic_shortcode( $atts, string $content, string $name ) : string {
	if ( ! $content ) {
		return '';
	}

	$args = shortcode_atts(
		[
			'post'     => null,
			'relative' => null,
			'args'     => [],
		], $atts, $name
	);

	if ( is_string( $args['args'] ) ) {
		parse_str( htmlspecialchars_decode( $args['args'] ), $args['args'] );
	}

	if ( empty( $args['args']['relation'] ) ) {
		$args['args']['relation'] = substr( $name, 5, strpos( $name, '_', 5 ) - 5 );

		if ( 'web' === $args['args']['relation'] ) {
			$args['args']['relation'] = '';
		}
	}

	if ( ! is_a_webcomic( $args['post'], $args['relative'], $args['args'] ) ) {
		return '';
	}

	return do_shortcode( $content );
}

/**
 * Display content if the post is comic media.
 *
 * @uses is_a_webcomic_media()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed $post Optional post to check.
 *     @type array $collections Optional collections to check for.
 * }
 * @param string $content Content to display if the post is comic media.
 * @param string $name Shortcode name.
 * @return string
 */
function is_a_webcomic_media_shortcode( $atts, string $content, string $name ) : string {
	if ( ! $content ) {
		return '';
	}

	$args = shortcode_atts(
		[
			'post'        => null,
			'collections' => [],
		], $atts, $name
	);

	if ( ! is_array( $args['collections'] ) ) {
		$args['collections'] = explode( ',', $args['collections'] );
	}

	if ( ! is_a_webcomic_media( $args['post'], $args['collections'] ) ) {
		return '';
	}

	return do_shortcode( $content );
}

/**
 * Display content if the page is related to a comic collection.
 *
 * @uses is_a_webcomic_page()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed $page Optional page to check.
 *     @type mixed $collections Optional collections to check for.
 * }
 * @param string $content Content to display if the page is related to a comic
 * collection.
 * @param string $name Shortcode name.
 * @return string
 */
function is_a_webcomic_page_shortcode( $atts, string $content, string $name ) : string {
	if ( ! $content ) {
		return '';
	}

	$args = shortcode_atts(
		[
			'page'        => null,
			'collections' => null,
		], $atts, $name
	);

	if ( ! is_a_webcomic_page( $args['page'], $args['collections'] ) ) {
		return '';
	}

	return do_shortcode( $content );
}

/**
 * Display content if the query is for a comic.
 *
 * @uses is_webcomic()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed $collections Optional collections to check for.
 *     @type mixed $posts Optional posts to check for.
 *     @type mixed $relative Optional reference post.
 *     @type array $args Optional arguments. The shortcode name determines the
 *                       value of the relation argument.
 * }
 * @param string $content Content to display if the query is for a comic.
 * @param string $name Shortcode name.
 * @return string
 */
function is_webcomic_shortcode( $atts, string $content, string $name ) : string {
	if ( ! $content ) {
		return '';
	}

	$args = shortcode_atts(
		[
			'collections' => null,
			'posts'       => null,
			'relative'    => null,
			'args'        => [],
		], $atts, $name
	);

	if ( is_string( $args['args'] ) ) {
		parse_str( htmlspecialchars_decode( $args['args'] ), $args['args'] );
	}

	if ( empty( $args['args']['relation'] ) ) {
		$args['args']['relation'] = substr( $name, 3, strpos( $name, '_', 3 ) - 3 );

		if ( 'webco' === $args['args']['relation'] ) {
			$args['args']['relation'] = '';
		}
	}

	if ( ! is_webcomic( $args['collections'], $args['posts'], $args['relative'], $args['args'] ) ) {
		return '';
	}

	return do_shortcode( $content );
}

/**
 * Display content if the query is for comic media.
 *
 * @uses is_webcomic_media()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type array $collections Optional collections to check for.
 * }
 * @param string $content Content to display if the query is for comic media.
 * @param string $name Shortcode name.
 * @return string
 */
function is_webcomic_media_shortcode( $atts, string $content, string $name ) : string {
	if ( ! $content ) {
		return '';
	}

	$args = shortcode_atts(
		[
			'collections' => [],
		], $atts, $name
	);

	if ( ! is_array( $args['collections'] ) ) {
		$args['collections'] = explode( ',', $args['collections'] );
	}

	if ( ! is_webcomic_media( $args['collections'] ) ) {
		return '';
	}

	return do_shortcode( $content );
}

/**
 * Display content if the query is for a page related to a comic collection.
 *
 * @uses is_webcomic_page()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed $collections Optional collections to check for.
 *     @type mixed $pages Optional pages to check for.
 * }
 * @param string $content Content to display if the query is for a page related
 * to a comic collection.
 * @param string $name Shortcode name.
 * @return string
 */
function is_webcomic_page_shortcode( $atts, string $content, string $name ) : string {
	if ( ! $content ) {
		return '';
	}

	$args = shortcode_atts(
		[
			'collections' => null,
			'pages'       => null,
		], $atts, $name
	);

	if ( ! is_webcomic_page( $args['collections'], $args['pages'] ) ) {
		return '';
	}

	return do_shortcode( $content );
}

/**
 * Display a list of comics.
 *
 * @uses get_webcomics_list()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type string $format Optional list format, like before{{join}}after.
 *                          Including `<select>` or `<optgroup>` elements will
 *                          convert links to `<option>` elements.
 *     @type string $link Optional link text, like before{{text}}after.
 *     @type array  $link_args Optional link arguments.
 *     @type int    $cloud_min Optional weighted list minimum font size.
 *     @type int    $cloud_max Optional weighted list maximum font size.
 *     @type int    $current Optional post ID of the current comic.
 *     @type string $walker Optional custom Walker class to use instead of
 *                          Mgsisk\Webcomic\Collection\Walker\ComicLister.
 *     @type mixed  $related_to Optional post the comics must be related to.
 *     @type mixed  $related_by Optional taxonomies the comics must be related
 *                             by.
 *     @type mixed  $not_related_by Optional taxonomies the comics must not be
 *                                 related by.
 *     @type string $order Optional post sort order; one of asc or desc.
 *     @type string $orderby Optional post sort field; one of date, none, name,
 *                           author, title, modified, menu_order, parent, ID,
 *                           rand, relevance, or comment_count.
 *     @type int    $posts_per_page Optional number of posts to retrieve.
 * }
 * @param string $content Optional shortcode content; mapped to $args['link'].
 * @param string $name Shortcode name.
 * @return string
 */
function webcomics_list_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'cloud_max'      => 0,
			'cloud_min'      => 0,
			'current'        => 0,
			'format'         => ', ',
			'link_args'      => [],
			'link'           => '%title',
			'not_related_by' => [],
			'order'          => 'ASC',
			'orderby'        => 'date',
			'posts_per_page' => -1,
			'related_by'     => [],
			'related_to'     => null,
			'walker'         => '',
		], $atts, $name
	);

	$args['link']      = htmlspecialchars_decode( $args['link'] );
	$args['format']    = htmlspecialchars_decode( $args['format'] );
	$args['cloud_min'] = (int) $args['cloud_min'];
	$args['cloud_max'] = (int) $args['cloud_max'];

	if ( $content ) {
		$args['link'] = do_shortcode( htmlspecialchars_decode( $content ) );
	}

	if ( is_string( $args['link_args'] ) ) {
		parse_str( htmlspecialchars_decode( $args['link_args'] ), $args['link_args'] );
	}

	if ( ! is_array( $args['orderby'] ) ) {
		$args['orderby'] = explode( ',', $args['orderby'] );
	}

	return get_webcomics_list( $args );
}

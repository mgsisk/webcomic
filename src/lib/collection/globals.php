<?php
/**
 * Global collection functions
 *
 * @package Webcomic
 */

use const Mgsisk\Webcomic\Collection\COLLECTION;

/**
 * Get a comic collection ID.
 *
 * @param mixed $type Optional type of collection ID to get. May be a collection
 * ID, a collection keyword (like crossover), a post ID, a post object, or
 * empty (to use the requested collection).
 * @return string
 * @suppress PhanUndeclaredConstant - COLLECTION incorrectly triggers this.
 */
function get_webcomic_collection( $type = null ) {
	if ( is_bool( $type ) ) {
		webcomic_error( __( 'The classic behavior of get_webcomic_collection() is deprecated; please refer to the get_webcomic_collection() documentation for updated usage information.', 'webcomic' ) );

		return webcomic_compat( 'get_webcomic_collection_', func_get_args(), '' );
	}

	$collection = '';

	if ( defined( 'Mgsisk\Webcomic\Collection\COLLECTION' ) ) {
		$collection = COLLECTION;
	}

	if ( ( ! $collection && ! $type ) || is_int( $type ) || $type instanceof WP_Post ) {
		$type = get_post_type( $type );
	}

	if ( is_string( $type ) && webcomic_collection_exists( $type ) ) {
		$collection = $type;
	}

	/**
	 * Alter the collection.
	 *
	 * This filter allows hooks to alter the returned collection.
	 *
	 * @param string $collection The collection ID.
	 * @param mixed  $type Optional type of collection to get.
	 */
	$collection = apply_filters( 'get_webcomic_collection', $collection, $type );

	return (string) $collection;
}

/**
 * Get a collection comic count.
 *
 * @param mixed $collection Optional collection to get a post count for.
 * @return int
 */
function get_webcomic_collection_count( $collection = null ) : int {
	$collection = get_webcomic_collection( $collection );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return 0;
	}

	$counts = wp_count_posts( $collection );

	return (int) $counts->publish;
}

/**
 * Get a collection description.
 *
 * @param mixed $collection Optional collection to get a description for.
 * @return string
 */
function get_webcomic_collection_description( $collection = null ) : string {
	$collection = get_webcomic_collection( $collection );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return '';
	}

	return webcomic( "option.{$collection}.description" );
}

/**
 * Get a collection image.
 *
 * @see https://developer.wordpress.org/reference/functions/wp_get_attachment_image/
 * @param string $size Optional media size.
 * @param mixed  $collection Optional collection to get an image for.
 * @return string
 */
function get_webcomic_collection_media( string $size = 'full', $collection = null ) : string {
	$collection = get_webcomic_collection( $collection );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return '';
	}

	return wp_get_attachment_image( webcomic( "option.{$collection}.media" ), $size );
}

/**
 * Get a collection post type object.
 *
 * @see https://developer.wordpress.org/reference/functions/get_post_type_object/
 * @param mixed $collection Optional collection to get a post type object for.
 * @return mixed
 */
function get_webcomic_collection_object( $collection = null ) {
	$collection = get_webcomic_collection( $collection );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return;
	}

	return get_post_type_object( $collection );
}

/**
 * Get collection options.
 *
 * @param mixed $collection Optional collection to get options for.
 * @return array
 */
function get_webcomic_collection_options( $collection = null ) : array {
	$collection = get_webcomic_collection( $collection );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return [];
	}

	return webcomic( "option.{$collection}" );
}

/**
 * Get a collection title.
 *
 * @param mixed $collection Optional collection to get a title for.
 * @return string
 */
function get_webcomic_collection_title( $collection = null ) : string {
	$collection = get_webcomic_collection( $collection );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return '';
	}

	return webcomic( "option.{$collection}.name" );
}

/**
 * Get a collection updated datetime.
 *
 * @param string $format Optional datetime format.
 * @param mixed  $collection Optional collection to get an updated datetime for.
 * @return string
 */
function get_webcomic_collection_updated( string $format = '', $collection = null ) : string {
	$collection = get_webcomic_collection( $collection );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return '';
	}

	return (string) mysql2date( $format, webcomic( "option.{$collection}.updated" ) );
}

/**
 * Get a collection URL.
 *
 * @param mixed $collection Optional collection to get a URL for.
 * @param mixed $post Optional reference post.
 * @param array $args Optional arguments.
 * @return string
 */
function get_webcomic_collection_url( $collection = null, $post = null, array $args = [] ) : string {
	$collection = get_webcomic_collection( $collection );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return '';
	} elseif ( ! $post && ! $args ) {
		return get_post_type_archive_link( $collection );
	}

	$args['post_type'] = $collection;

	return get_webcomic_url( $post, $args );
}

/**
 * Get a collection link.
 *
 * @param string $link Optional link text, like 'before{{text}}after'.
 * @param mixed  $collection Optional collection to get a link for.
 * @param mixed  $post Optional reference post.
 * @param array  $args Optional arguments.
 * @return string
 */
function get_webcomic_collection_link( string $link = '%title', $collection = null, $post = null, array $args = [] ) : string {
	$collection = get_webcomic_collection( $collection );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return '';
	}

	$url = get_webcomic_collection_url( $collection, $post, $args );

	if ( ! $url ) {
		return '';
	}

	preg_match( '/^(.*){{(.*)}}(.*)$/s', $link, $match );

	$match += [ '', '', $link, '' ];
	$class  = [ 'webcomic-collection-link', "{$collection}-collection-link" ];

	/**
	 * Alter the link tokens.
	 *
	 * This filter allows hooks to alter the replaceable link text tokens and
	 * their values.
	 *
	 * ## Core tokens
	 *
	 * |Token        |Value                                    |Example       |
	 * |-------------|-----------------------------------------|--------------|
	 * |%date        |The collection's update date.            |May 1, 2099   |
	 * |%count       |The number of comics in the collection.  |42            |
	 * |%description |The collection's description.            |It's exciting!|
	 * |%full        |The collection's full-size media.        |              |
	 * |%large       |The collection's large-size media.       |              |
	 * |%medium      |The collection's medium-size media.      |              |
	 * |%medium_large|The collection's medium_large-size media.|              |
	 * |%thumbnail   |The collection's thumbnail-size media.   |              |
	 * |%time        |The collection's updated time.           |4:52 pm       |
	 * |%title       |The collection's title.                  |Page 1        |
	 *
	 * ## Commerce tokens
	 *
	 * |Token          |Value                                          |Example |
	 * |---------------|-----------------------------------------------|--------|
	 * |%print-currency|The collection's print currency.               |USD     |
	 * |%*-print-name  |The collection's print name.                   |Domestic|
	 * |%*-print-price |The collection's print price.                  |10      |
	 * |%*-print-stock |The number of prints available.                |50      |
	 *
	 * The `*` in these tokens is a placeholder for the print ID. To see a comic's
	 * print base price for a print with an ID of `domestic`, you would use the
	 * token `%domestic-print-price`.
	 *
	 * @param array  $tokens The token values.
	 * @param string $link The link text to search for tokens.
	 * @param string $collection The collection the link is for.
	 */
	$tokens = apply_filters( 'get_webcomic_collection_link_tokens', [], $match[2], $collection );
	$anchor = str_replace( array_keys( $tokens ), $tokens, $match[2] );

	if ( get_webcomic_collection() === $collection ) {
		$class[] = 'current-webcomic-collection';
	}

	/**
	 * Alter the link class.
	 *
	 * This filter allows hooks to alter the CSS classes assigned to the link.
	 *
	 * @param array  $class The CSS classes.
	 * @param array  $args Optional arguments.
	 * @param string $collection The collection the link is for.
	 */
	$class = apply_filters( 'get_webcomic_collection_link_class', $class, $args, $collection );
	$class = implode( ' ', array_unique( array_map( 'esc_attr', $class ) ) );

	return "{$match[1]}<a href='{$url}' class='{$class}'>{$anchor}</a>{$match[3]}";
}

/**
 * Get collections.
 *
 * @param array $args {
 *     Optional arguments.
 *
 *     @type bool   $crossover Whether to get collections based on the current
 *                             taxonomy crossover query, if any (taxonomy
 *                             components only).
 *     @type string $fields Optional collection fields to get; one of ids,
 *                          options, or objects.
 *     @type bool   $hide_empty Whether to include or exclude empty
 *                              collections.
 *     @type array  $id__in Optional ID's the collections must match.
 *     @type array  $id__not_in Optional ID's the collections must not match.
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
 *     @type array  $slug__in Optional slugs the collections must match.
 *     @type array  $slug__not_in Optional slugs the collections must not
 *                                match.
 * }
 * @return array
 * @SuppressWarnings(PHPMD.NPathComplexity) - Required for compatibility.
 */
function get_webcomic_collections( $args = [] ) : array {
	if ( ! is_array( $args ) ) {
		webcomic_error( __( 'The classic behavior of get_webcomic_collections() is deprecated; please refer to the get_webcomic_collections() documentation for updated usage information.', 'webcomic' ) );

		return webcomic_compat( 'get_webcomic_collections_', func_get_args(), [] );
	}

	/**
	 * Alter the get_webcomic_collections() arguments.
	 *
	 * This filter allows hooks to alter the arguments used by
	 * get_webcomic_collections().
	 *
	 * @param array $args The arguments to filter.
	 */
	$args = apply_filters(
		'get_webcomic_collections_args', $args + [
			'fields'     => 'ids',
			'hide_empty' => true,
			'limit'      => 0,
			'order'      => 'asc',
			'orderby'    => 'name',
		]
	);

	/**
	 * Alter the collections.
	 *
	 * This filter allows hooks to alter the list of collection ID's before
	 * standard arguments are applied to the collections array.
	 *
	 * @param array $collections The collection ID's.
	 * @param array $args Optional arguments.
	 */
	$collections = apply_filters( 'get_webcomic_collections', webcomic( 'option.collections' ), $args );

	if ( 'rand' === $args['orderby'] ) {
		shuffle( $collections );
	} elseif ( function_exists( "sort_webcomic_collections_{$args['orderby']}" ) ) {
		usort( $collections, "sort_webcomic_collections_{$args['orderby']}" );
	}

	if ( 'desc' === $args['order'] ) {
		$collections = array_reverse( $collections );
	}

	if ( 0 < $args['limit'] ) {
		$collections = array_slice( $collections, 0, $args['limit'] );
	}

	if ( in_array( $args['fields'], [ 'objects', 'options' ], true ) ) {
		$callback = 'get_webcomic_collection_' . str_replace( 'objects', 'object', $args['fields'] );

		foreach ( $collections as $key => $collection ) {
			$collections[ $key ] = $callback( $collection );
		}
	}

	return $collections;
}

/**
 * Get a list of collections.
 *
 * @uses get_webcomic_collections() The fields argument is always set to `ids`.
 * @param array $args {
 *     Optional arguments.
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
 * }
 * @return string
 */
function get_webcomic_collections_list( array $args = [] ) : string {
	$args['fields'] = 'ids';
	$collections    = get_webcomic_collections( $args );

	if ( ! $collections ) {
		return '';
	}

	/**
	 * Alter the get_webcomic_collections_list() arguments.
	 *
	 * This filter allows hooks to alter the arguments used by
	 * get_webcomic_collections_list().
	 *
	 * @param array $args The arguments to filter.
	 * @param array $collections The collections included in the list.
	 */
	$args = apply_filters( 'get_webcomic_collections_list_args', $args, $collections );
	// @codingStandardsIgnoreLine WordPress.Classes.ClassInstantiation.MissingParenthesis - Incorrectly triggered.
	$walker = new $args['walker']();
	$items  = [];

	foreach ( $collections as $collection ) {
		$output = '';

		$walker->start_el( $output, get_post_type_object( $collection ), 0, $args, (int) str_replace( 'webcomic', '', $collection ) );

		$items[] = $output;
	}

	preg_match( '/^(.*){{(.*)}}(.*)$/s', $args['format'], $match );

	$match += [ '', '', $args['format'], '' ];

	return $match[1] . implode( $match[2], $items ) . $match[3];
}

/**
 * Get an infinite comic URL.
 *
 * @return string
 */
function get_webcomic_infinite_url() : string {
	if ( ! webcomic( 'GLOBALS._REQUEST.url' ) || ! webcomic( 'GLOBALS._REQUEST.args' ) || ! webcomic( 'GLOBALS._REQUEST.action' ) || 'webcomic_infinite' !== webcomic( 'GLOBALS._REQUEST.action' ) ) {
		return '';
	}

	$url = esc_url( webcomic( 'GLOBALS._REQUEST.url' ) );

	if ( ! $url ) {
		return '';
	}

	parse_str( htmlspecialchars_decode( webcomic( 'GLOBALS._REQUEST.args' ) ), $args );

	if ( empty( $args['offset'] ) ) {
		return $url;
	}

	return esc_url(
		add_query_arg(
			[
				'wi' => abs( (int) $args['offset'] ),
			], webcomic( 'GLOBALS._REQUEST.url' )
		)
	);
}

/**
 * Get an infinite comic link.
 *
 * @param string $link Optional link text, like before{{text}}after.
 * @return string
 */
function get_webcomic_infinite_link( string $link = '' ) : string {
	$url = get_webcomic_infinite_url();

	if ( ! $url ) {
		return '';
	} elseif ( ! $link ) {
		$link = esc_html__( 'Bookmark', 'webcomic' );
	}

	preg_match( '/^(.*){{(.*)}}(.*)$/s', $link, $match );

	$match += [ '', '', $link, '' ];

	return "{$match[1]}<a href='{$url}' class='webcomic-infinite-link'>{$match[2]}</a>{$match[3]}";
}

/**
 * Get a comic.
 *
 * @see https://developer.wordpress.org/reference/functions/get_post/
 * @param mixed $post Optional post to get.
 * @param array $args {
 *     Optional arguments.
 *
 *     @type string $relation Optional relational post to get; one of first,
 *                            previous, next, last, or random. When set, $post
 *                            becomes the point of reference for determining
 *                            the related post to get.
 * }
 * @return mixed
 * @codingStandardsIgnoreStart Generic.Metrics.CyclomaticComplexity.TooHigh - More refactoring would be counterproductive.
 */
function get_webcomic( $post = null, array $args = [] ) {
	$comic = get_post( $post );

	if ( ! $comic || ! webcomic_collection_exists( $comic->post_type ) ) {
		$comic = null;
	}

	if ( empty( $args['relation'] ) || ( ! $comic && in_array( $args['relation'], [ 'previous', 'next' ], true ) ) ) {
		return $comic;
	} elseif ( $comic && empty( $args['post_type'] ) ) {
		$args['post_type'] = $comic->post_type;
	}

	if ( 'random' === $args['relation'] ) {
		$args['orderby'] = 'rand';
	} elseif ( in_array( $args['relation'], [ 'first', 'previous' ], true ) ) {
		$args['order'] = 'asc';
	}

	$args['fields']              = 'ids';
	$args['ignore_sticky_posts'] = true;
	$comics                      = get_webcomics( $args );

	if ( ! $comics ) {
		return;
	} elseif ( in_array( $args['relation'], [ 'previous', 'next' ], true ) ) {
		$key = (int) array_search( $comic->ID, $comics, true ) - 1;

		if ( array_key_exists( $key, $comics ) ) {
			$comics[0] = $comics[ $key ];
		}
	}

	return get_post( $comics[0] );
} // @codingStandardsIgnoreEnd

/**
 * Get comic media.
 *
 * @param string $format Optional media format, like before{{join}}after{size}.
 *                       Size may be be any valid image size or a
 *                       comma-separated list of width and height pixel values
 *                       (in that order), and may be specified without the rest
 *                       of the format arguments.
 * @param mixed  $post Optional post to get comic media for.
 * @param array  $args {
 *     Optional arguments.
 *
 *     @type array $attr Optional attributes for the media markup.
 *     @type int   $length Optional length of the media array slice to use.
 *     @type int   $offset Optional zero-based media index to use.
 * }
 * @return string
 */
function get_webcomic_media( $format = 'full', $post = null, array $args = [] ) : string {
	$comic = get_webcomic( $post, $args );

	if ( ! $comic ) {
		return '';
	}

	$media = get_post_meta( $comic->ID, 'webcomic_media' );

	if ( ! $media ) {
		return '';
	}

	preg_match( '/^(.*){{(.*)}}(.*?)(?:{(.+?)})?$/s', $format, $match );

	if ( empty( $match ) ) {
		$match = [ '', '', '', '', $format ];
	} elseif ( 5 !== count( $match ) ) {
		$match += [ '', '', '', '', 'full' ];
	}

	$args += [
		'attr'   => [],
		'offset' => null,
		'length' => null,
	];
	$items = [];

	if ( preg_match( '/^\s*(\d+)(?:\s*,\s*(\d+))?\s*$/', $match[4], $size ) ) {
		$size    += [ '', $size[1], $size[1] ];
		$match[4] = [ (int) $size[1], (int) $size[2] ];
	}

	if ( is_int( $args['offset'] ) ) {
		$media = array_slice( $media, $args['offset'], $args['length'] );
	}

	foreach ( $media as $item ) {
		$items[] = wp_get_attachment_image( $item, $match[4], false, $args['attr'] );
	}

	return $match[1] . implode( $match[2], $items ) . $match[3];
}

/**
 * Get a comic URL.
 *
 * @param mixed $post Optional post to get a URL for.
 * @param array $args {
 *     Optional arguments.
 *
 *     @type bool   $query_url Whether to use query parameters for the URL.
 *     @type bool   $confirm_age Whether to set an age confirmation parameter
 *                               (Restrict component only).
 *     @type string $print Optinal print slug to use for a purchase comic print
 *                         url (Commerce component only).
 *     @type string $transcribe Optional transcript ID to edit (Transcribe
 *                              component only).
 *     @type string $transcripts Optional transcripts list ID (Transcribe
 *                               component only).
 * }
 * @return string
 */
function get_webcomic_url( $post = null, array $args = [] ) : string {
	$comic = get_webcomic( $post, $args );

	if ( ! $comic ) {
		return '';
	} elseif ( empty( $args['query_url'] ) ) {
		/**
		 * Alter the comic URL.
		 *
		 * This filter allows hooks to alter the requested comic's URL. Note that
		 * this filter does not run if the $query_url argument has been specified;
		 * it will run during the query URL redirect.
		 *
		 * @param string $url The comic URL.
		 * @param WP_Post $comic The comic the URL points to.
		 * @param array  $args Optional arguments.
		 * @param mixed  $post Optional reference post.
		 */
		$url = apply_filters( 'get_webcomic_url', get_permalink( $comic ), $comic, $args, $post );

		return esc_url( $url );
	}

	$relation = '';

	if ( isset( $args['relation'] ) ) {
		$relation = $args['relation'];
	}

	unset( $args['query_url'], $args['relation'], $args['post_type'] );

	$query = [ $comic->post_type, $relation, $comic->ID, str_replace( 'a:0:{}', '', maybe_serialize( $args ) ) ];

	return esc_url(
		add_query_arg(
			[
				'webcomic_url' => rawurlencode( rtrim( implode( '-', $query ), '-' ) ),
			], home_url( '/' )
		)
	);
}

/**
 * Get a comic link.
 *
 * @param string $link Optional link text, like before{{text}}after.
 * @param mixed  $post Optional post to get a link for.
 * @param array  $args Optional arguments.
 * @return string
 */
function get_webcomic_link( string $link = '%title', $post = null, array $args = [] ) : string {
	$url = get_webcomic_url( $post, $args );

	if ( ! $url ) {
		return '';
	}

	preg_match( '/^(.*){{(.*)}}(.*)$/s', $link, $match );

	$match   += [ '', '', $link, '' ];
	$comic    = get_webcomic( $post, $args );
	$relation = 'self';

	if ( isset( $args['relation'] ) ) {
		$relation = $args['relation'];
	}

	/**
	 * Alter the link tokens.
	 *
	 * This filter allows hooks to alter the replaceable link text tokens and
	 * their values.
	 *
	 * ## Core tokens
	 *
	 * |Token            |Value                                   |Example    |
	 * |-----------------|----------------------------------------|-----------|
	 * |%date            |The comic's publish date.               |May 1, 2099|
	 * |%full            |The comic's full media.                 |           |
	 * |%large           |The comic's large media.                |           |
	 * |%medium          |The comic's medium media.               |           |
	 * |%medium_large    |The comic's medium_large media.         |           |
	 * |%thumbnail       |The comic's thumbnail media.            |           |
	 * |%time            |The comic's publish time.               |4:52 pm    |
	 * |%title           |The comic's title.                      |Page 1     |
	 * |%wfi-full        |The comic's full featured image.        |           |
	 * |%wfi-large       |The comic's large featured image.       |           |
	 * |%wfi-medium      |The comic's medium featured image.      |           |
	 * |%wfi-medium_large|The comic's medium_large featured image.|           |
	 * |%wfi-thumbnail   |The comic's thumbnail featured image.   |           |
	 *
	 * ## Commerce tokens
	 *
	 * |Token          |Value                                        |Example   |
	 * |---------------|---------------------------------------------|----------|
	 * |%*-print-adjust|The comic's print adjustment.                |-25       |
	 * |%*-print-base  |The collection's print price.                |10.00     |
	 * |%*-print-left  |The number of prints left for the comic.     |42        |
	 * |%*-print-name  |The collection's print name.                 |Domestic  |
	 * |%*-print-price |The comic's print price.                     |7.50      |
	 * |%*-print-sold  |The number of prints sold for the comic.     |8         |
	 * |%*-print-stock |The number of prints available for the comic.|50        |
	 * |%print-currency|The collection's print currency.             |USD       |
	 *
	 * The `*` in these tokens is a placeholder for the print ID. To see a comic's
	 * print base price for a print with an ID of `domestic`, you would use the
	 * token `%domestic-print-price`.
	 *
	 * @param array   $tokens The token values.
	 * @param string  $link The link text to search for tokens.
	 * @param WP_Post $collection The comic the link is for.
	 */
	$tokens  = apply_filters( 'get_webcomic_link_tokens', [], $match[2], $comic );
	$anchor  = str_replace( array_keys( $tokens ), $tokens, $match[2] );
	$current = get_webcomic();
	$class   = [ 'webcomic-link', "{$comic->post_type}-link", "{$relation}-webcomic-link", "{$relation}-{$comic->post_type}-link" ];

	if ( 'random' !== $relation && $current && $comic->ID === $current->ID ) {
		$class[] = 'current-webcomic';
		$class[] = "current-{$comic->post_type}";
	}

	/**
	 * Alter the link class.
	 *
	 * This filter allows hooks to alter the CSS classes assigned to the link.
	 *
	 * @param array   $class The CSS classes.
	 * @param array   $args Optional arguments.
	 * @param WP_Post $comic The comic the link is for.
	 */
	$class = apply_filters( 'get_webcomic_link_class', $class, $args, $comic );
	$class = implode( ' ', array_unique( array_map( 'esc_attr', $class ) ) );

	return "{$match[1]}<a href='{$url}' class='{$class}'>{$anchor}</a>{$match[3]}";
}

/**
 * Get comics.
 *
 * @see https://developer.wordpress.org/reference/functions/get_posts/ Accepts
 * get_posts() arguments as well.
 * @param array $args {
 *     Optional arguments.
 *
 *     @type mixed $post_type Optional post type. May be a collection ID, an
 *                            array of collection ID's, or all.
 *     @type mixed $related_to Optional post the comics must be related to.
 *     @type mixed $related_by Optional taxonomies the comics must be related
 *                             by.
 *     @type mixed $not_related_by Optional taxonomies the comics must not be
 *                                 related by.
 * }
 * @return array
 */
function get_webcomics( array $args = [] ) : array {
	$args += [
		'posts_per_page' => -1,
	];

	if ( empty( $args['post_type'] ) ) {
		$args['post_type'] = get_webcomic_collection();
	} elseif ( 'any' === $args['post_type'] ) {
		$args['post_type'] = webcomic( 'option.collections' );
	}

	/**
	 * Alter the get_webcomics() arguments.
	 *
	 * This filter allows hooks to alter the get_webcomics() arguments before
	 * they're passed to get_posts().
	 *
	 * @param array $args Optional arguments.
	 */
	$args              = apply_filters( 'get_webcomics_args', $args );
	$args['post_type'] = preg_grep( '/^webcomic\d+$/', (array) $args['post_type'] );

	if ( ! $args['post_type'] ) {
		return [];
	}

	return get_posts( $args );
}

/**
 * Get a list of comics.
 *
 * @uses get_webcomics() The fields argument is always set to `ids`.
 * @param array $args {
 *     Optional arguments.
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
 * }
 * @return string
 */
function get_webcomics_list( array $args = [] ) : string {
	$args['fields'] = 'ids';
	$comics         = get_webcomics( $args );

	if ( ! $comics ) {
		return '';
	}

	/**
	 * Alter the get_webcomics_list arguments.
	 *
	 * This filter allows hooks to alter the arguments used by
	 * get_webcomics_list.
	 *
	 * @param array $args The arguments to filter.
	 * @param array $comics The comics included in the list.
	 */
	$args = apply_filters( 'get_webcomics_list_args', $args, $comics );
	// @codingStandardsIgnoreLine WordPress.Classes.ClassInstantiation.MissingParenthesis - Incorrectly triggered.
	$walker = new $args['walker']();
	$items  = [];

	foreach ( $comics as $comic ) {
		$output = '';

		$walker->start_el( $output, get_webcomic( $comic ), 0, $args, $comic );

		$items[] = $output;
	}

	preg_match( '/^(.*){{(.*)}}(.*)$/s', $args['format'], $match );

	$match += [ '', '', $args['format'], '' ];

	return $match[1] . implode( $match[2], $items ) . $match[3];
}

/**
 * Does the comic have any media?
 *
 * @param mixed $post Optional post to check for comic media.
 * @param array $args Optional arguments.
 * @return bool
 */
function has_webcomic_media( $post = null, array $args = [] ) : bool {
	$comic = get_webcomic( $post, $args );

	if ( ! $comic ) {
		return false;
	}

	return (bool) get_post_meta( $comic->ID, 'webcomic_media' );
}

/**
 * Is the post a comic?
 *
 * @param mixed $post Optional post to check.
 * @param mixed $relative Optional reference post.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_a_webcomic( $post = null, $relative = null, array $args = [] ) : bool {
	$comic = get_webcomic( $post );

	if ( ! $comic ) {
		return false;
	} elseif ( ! $relative && ! $args ) {
		return true;
	}

	$comic_relative = get_webcomic( $relative, $args );

	if ( ! $comic_relative ) {
		return false;
	}

	return $comic->ID === $comic_relative->ID;
}

/**
 * Is the post the first comic?
 *
 * @uses is_a_webcomic() The relation argument is always set to `first`.
 * @param mixed $post Optional post to check.
 * @param mixed $relative Optional reference post.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_a_first_webcomic( $post = null, $relative = null, array $args = [] ) : bool {
	$args['relation'] = 'first';

	return is_a_webcomic( $post, $relative, $args );
}

/**
 * Is the post the previous comic?
 *
 * @uses is_a_webcomic() The relation argument is always set to `previous`.
 * @param mixed $post Optional post to check.
 * @param mixed $relative Optional reference post.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_a_previous_webcomic( $post = null, $relative = null, array $args = [] ) : bool {
	$args['relation'] = 'previous';

	return is_a_webcomic( $post, $relative, $args );
}

/**
 * Is the post the next comic?
 *
 * @uses is_a_webcomic() The relation argument is always set to `next`.
 * @param mixed $post Optional post to check.
 * @param mixed $relative Optional reference post.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_a_next_webcomic( $post = null, $relative = null, array $args = [] ) : bool {
	$args['relation'] = 'next';

	return is_a_webcomic( $post, $relative, $args );
}

/**
 * Is the post the last comic?
 *
 * @uses is_a_webcomic() The relation argument is always set to `last`.
 * @param mixed $post Optional post to check.
 * @param mixed $relative Optional reference post.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_a_last_webcomic( $post = null, $relative = null, array $args = [] ) : bool {
	$args['relation'] = 'last';

	return is_a_webcomic( $post, $relative, $args );
}

/**
 * Is the post comic media?
 *
 * @param mixed $post Optional post to check.
 * @param array $collections Optional collections to check for.
 * @return bool
 */
function is_a_webcomic_media( $post = null, array $collections = [] ) : bool {
	$media = get_post( $post );

	if ( ! $media || ! $media->post_mime_type ) {
		return false;
	} elseif ( ! $collections ) {
		$collections = webcomic( 'option.collections' );
	}

	$comics = get_post_meta( $media->ID, 'webcomic_post' );

	foreach ( $comics as $key => $comic ) {
		$comics[ $key ] = get_post_type( $comic );
	}

	return (bool) array_intersect( $collections, array_unique( $comics ) );
}

/**
 * Is the page related to a comic collection?
 *
 * @param mixed $page Optional page to check.
 * @param mixed $collections Optional collections to check for.
 * @return bool
 */
function is_a_webcomic_page( $page = null, $collections = null ) : bool {
	$page = get_post( $page );

	if ( ! $page || 'page' !== $page->post_type ) {
		return false;
	}

	$collection = get_post_meta( $page->ID, 'webcomic_collection', true );

	if ( ! $collection ) {
		return false;
	} elseif ( ! $collections ) {
		$collections = webcomic( 'option.collections' );
	}

	return in_array( $collection, (array) $collections, true );
}

/**
 * Is the query for a comic?
 *
 * @see https://developer.wordpress.org/reference/functions/is_single/
 * @see https://developer.wordpress.org/reference/functions/is_singular/
 * @uses is_a_webcomic() The post argument is always set to the queried object.
 * @param mixed $collections Optional collections to check for.
 * @param mixed $posts Optional posts to check for.
 * @param mixed $relative Optional reference post.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_webcomic( $collections = null, $posts = null, $relative = null, array $args = [] ) : bool {
	if ( is_bool( $collections ) ) {
		webcomic_error( __( 'The classic behavior of is_webcomic() is deprecated; please refer to the is_webcomic() documentation for updated usage information.', 'webcomic' ) );

		return webcomic_compat( 'is_webcomic_', func_get_args(), false );
	}

	if ( ! is_single( $posts ) || ! is_a_webcomic( get_queried_object(), $relative, $args ) ) {
		return false;
	} elseif ( ! $collections ) {
		$collections = webcomic( 'option.collections' );
	}

	if ( ! is_singular( $collections ) ) {
		return false;
	}

	return true;
}

/**
 * Is the query for the first comic?
 *
 * @uses is_webcomic() The relation argument is always set to `first`.
 * @param mixed $collections Optional collections to check for.
 * @param mixed $posts Optional posts to check for.
 * @param mixed $relative Optional reference post.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_first_webcomic( $collections = null, $posts = null, $relative = null, $args = [] ) : bool {
	if ( is_bool( $collections ) || is_bool( $posts ) || is_string( $relative ) || ! is_array( $args ) ) {
		webcomic_error( __( 'The classic behavior of is_first_webcomic() is deprecated; please refer to the is_first_webcomic() documentation for updated usage information.', 'webcomic' ) );

		return webcomic_compat( 'is_relative_webcomic_', array_merge( [ 'first' ], func_get_args() ), false );
	}

	$args['relation'] = 'first';

	return is_webcomic( $collections, $posts, $relative, $args );
}

/**
 * Is the query for the previous comic?
 *
 * @uses is_webcomic() The relation argument is always set to `previous`.
 * @param mixed $collections Optional collections to check for.
 * @param mixed $posts Optional posts to check for.
 * @param mixed $relative Optional reference post.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_previous_webcomic( $collections = null, $posts = null, $relative = null, array $args = [] ) : bool {
	$args['relation'] = 'previous';

	return is_webcomic( $collections, $posts, $relative, $args );
}

/**
 * Is the query for the next comic?
 *
 * @uses is_webcomic() The relation argument is always set to `next`.
 * @param mixed $collections Optional collections to check for.
 * @param mixed $posts Optional posts to check for.
 * @param mixed $relative Optional reference post.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_next_webcomic( $collections = null, $posts = null, $relative = null, array $args = [] ) : bool {
	$args['relation'] = 'next';

	return is_webcomic( $collections, $posts, $relative, $args );
}

/**
 * Is the query for the last comic?
 *
 * @uses is_webcomic() The relation argument is always set to `last`.
 * @param mixed $collections Optional collections to check for.
 * @param mixed $posts Optional post to check.
 * @param mixed $relative Optional reference post.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_last_webcomic( $collections = null, $posts = null, $relative = null, $args = [] ) : bool {
	if ( is_bool( $collections ) || is_bool( $posts ) || is_string( $relative ) || ! is_array( $args ) ) {
		webcomic_error( __( 'The classic behavior of is_last_webcomic() is deprecated; please refer to the is_last_webcomic() documentation for updated usage information.', 'webcomic' ) );

		return webcomic_compat( 'is_relative_webcomic_', array_merge( [ 'last' ], func_get_args() ), false );
	}

	$args['relation'] = 'last';

	return is_webcomic( $collections, $posts, $relative, $args );
}

/**
 * Is the query for a collection archive?
 *
 * @see https://developer.wordpress.org/reference/functions/is_post_type_archive/
 * @param mixed $collections Optional collections to check for.
 * @return bool
 */
function is_webcomic_collection( $collections = null ) : bool {
	$collections = preg_grep( '/^webcomic\d+$/', (array) $collections );

	if ( ! $collections ) {
		$collections = webcomic( 'option.collections' );
	}

	return is_post_type_archive( $collections );
}

/**
 * Is the query for comic media?
 *
 * @see https://developer.wordpress.org/reference/functions/is_attachment/
 * @uses is_webcomic_media() The post argument is always set to the queried
 * object.
 * @param array $collections Optional collections to check for.
 * @return bool
 */
function is_webcomic_media( array $collections = [] ) : bool {
	if ( ! is_attachment() ) {
		return false;
	}

	return is_a_webcomic_media( get_queried_object(), $collections );
}

/**
 * Is the query for a page related to a comic collection?
 *
 * @see https://developer.wordpress.org/reference/functions/is_page/
 * @uses is_a_webcomic_page() The page argument is always set to the queried
 * object.
 * @param mixed $collections Optional collections to check for.
 * @param mixed $pages Optional pages to check for.
 * @return bool
 */
function is_webcomic_page( $collections = null, $pages = null ) : bool {
	if ( is_int( $collections ) || is_object( $collections ) || ( is_string( $pages ) && preg_match( '/^webcomic\d+$/', $pages ) ) ) {
		webcomic_error( __( 'The classic behavior of is_webcomic_page() is deprecated; please refer to the is_webcomic_page() documentation for updated usage information.', 'webcomic' ) );

		return webcomic_compat( 'is_webcomic_page_', func_get_args(), false );
	}

	if ( ! is_page( $pages ) ) {
		return false;
	}

	return is_a_webcomic_page( get_queried_object(), $collections );
}

/**
 * Does the collection exist?
 *
 * @param string $collection The collection to check.
 * @return bool
 */
function webcomic_collection_exists( string $collection ) : bool {
	if ( ! post_type_exists( $collection ) ) {
		return false;
	}

	return in_array( $collection, webcomic( 'option.collections' ), true );
}

/**
 * Restore global post data from a backup.
 *
 * @see https://developer.wordpress.org/reference/functions/setup_postdata/
 * @return bool
 * @SuppressWarnings(PHPMD.Superglobals) - WordPress requires a specific global variable for setup_postdata().
 */
function webcomic_reset_postdata() : bool {
	$backups = webcomic( 'GLOBALS.webcomic_backup_posts' );

	if ( ! $backups ) {
		return false;
	}

	end( $backups );

	$index = key( $backups );
	$post  = get_post( $backups[ $index ] );

	if ( ! $post ) {
		return false;
	}

	// @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited - We're purposely changing the $post global.
	$GLOBALS['post'] = $post;

	unset( $backups[ $index ] );

	$GLOBALS['webcomic_backup_posts'] = $backups;

	return setup_postdata( webcomic( 'GLOBALS.post' ) );
}

/**
 * Setup global post data.
 *
 * @see https://developer.wordpress.org/reference/functions/setup_postdata/
 * @param mixed $post The post to setup.
 * @return bool
 * @SuppressWarnings(PHPMD.Superglobals) - WordPress requires a specific global variable for setup_postdata().
 */
function webcomic_setup_postdata( $post ) : bool {
	$post = get_post( $post );

	if ( ! $post ) {
		return false;
	} elseif ( empty( $GLOBALS['webcomic_backup_posts'] ) ) {
		$GLOBALS['webcomic_backup_posts'] = [];
	}

	$GLOBALS['webcomic_backup_posts'][] = $GLOBALS['post']->ID;
	// @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited - We're purposely changing the $post global.
	$GLOBALS['post']                    = $post;

	return setup_postdata( $post );
}

/*
===== Display Functions ======================================================

The following template tags directly display content returned by some of the
template tags found above.
*/

/**
 * Display a collection comic count.
 *
 * @uses get_webcomic_collection_count()
 * @param mixed $collection Optional collection to display a comic count for.
 * @return void
 */
function webcomic_collection_count( $collection = null ) {
	echo get_webcomic_collection_count( $collection ); // WPCS: xss ok.
}

/**
 * Display a collection description.
 *
 * @uses get_webcomic_collection_description()
 * @param mixed $collection Optional collection to display a description for.
 * @return void
 */
function webcomic_collection_description( $collection = null ) {
	echo get_webcomic_collection_description( $collection ); // WPCS: xss ok.
}

/**
 * Display a collection image.
 *
 * @uses get_webcomic_collection_media()
 * @param string $size Optional media size.
 * @param mixed  $collection Optional collection to display an image for.
 * @return void
 */
function webcomic_collection_media( string $size = 'full', $collection = null ) {
	echo get_webcomic_collection_media( $size, $collection ); // WPCS: xss ok.
}

/**
 * Display a collection title.
 *
 * @uses get_webcomic_collection_title()
 * @param mixed $collection Optional collection to display a title for.
 * @return void
 */
function webcomic_collection_title( $collection = null ) {
	if ( 1 < func_num_args() || ( is_string( $collection ) && ! preg_match( '/^(webcomic\d+|crossover(-\d+)?)$/', $collection ) ) ) {
		webcomic_error( __( 'The classic behavior of webcomic_collection_title() is deprecated; please refer to the webcomic_collection_title() documentation for updated usage information.', 'webcomic' ) );

		echo webcomic_compat( 'webcomic_collection_title_', func_get_args(), '' ); // WPCS: xss ok.

		return;
	}

	echo get_webcomic_collection_title( $collection ); // WPCS: xss ok.
}

/**
 * Display a collection updated datetime.
 *
 * @uses get_webcomic_collection_updated()
 * @param string $format Optional datetime format.
 * @param mixed  $collection Optional collection to display an updated
 * datetime for.
 * @return void
 */
function webcomic_collection_updated( string $format = '', $collection = null ) {
	echo get_webcomic_collection_updated( $format, $collection ); // WPCS: xss ok.
}

/**
 * Display a collection link.
 *
 * @uses get_webcomic_collection_link()
 * @param string $link Optional link text, like 'before{{text}}after'.
 * @param mixed  $collection Optional collection to display a link for.
 * @param mixed  $post Optional reference post.
 * @param array  $args Optional arguments.
 * @return void
 */
function webcomic_collection_link( string $link = '%title', $collection = null, $post = null, array $args = [] ) {
	if ( false !== strpos( $link, '%link' ) || ( is_string( $collection ) && ! preg_match( '/^(webcomic\d+|crossover(-\d+)?)$/', $collection ) ) || is_string( $post ) ) {
		webcomic_error( __( 'The classic behavior of webcomic_collection_link() is deprecated; please refer to the webcomic_collection_link() documentation for updated usage information.', 'webcomic' ) );

		echo webcomic_compat( 'webcomic_collection_link_', func_get_args(), '' ); // WPCS: xss ok.

		return;
	}

	echo get_webcomic_collection_link( $link, $collection, $post, $args ); // WPCS: xss ok.
}

/**
 * Display a list of collections.
 *
 * @uses get_webcomic_collections_list()
 * @param array $args Optional arguments.
 * @return void
 */
function webcomic_collections_list( array $args = [] ) {
	echo get_webcomic_collections_list( $args ); // WPCS: xss ok.
}

/**
 * Display an infninite comic link.
 *
 * @uses get_webcomic_infinite_link()
 * @param string $link Optional link text, like before{{text}}after.
 * @return void
 */
function webcomic_infinite_link( string $link = '' ) {
	echo get_webcomic_infinite_link( $link ); // WPCS:: xss ok.
}

/**
 * Display comic media.
 *
 * @uses get_webcomic_media()
 * @param string $format Optional media format, like before{{join}}after{size}.
 *                       Size may be be any valid image size or a
 *                       comma-separated list of width and height values in
 *                       pixels (in that order), and may be specified without
 *                       the rest of the format arguments.
 * @param mixed  $post Optional post to get comic media for.
 * @param array  $args Optional arguments.
 * @return void
 */
function webcomic_media( string $format = 'full', $post = null, array $args = [] ) {
	echo get_webcomic_media( $format, $post, $args ); // WPCS: xss ok.
}

/**
 * Display a comic link.
 *
 * @uses get_webcomic_link()
 * @param string $link Optional link text, like before{{text}}after.
 * @param mixed  $post Optional post to get a link for.
 * @param array  $args Optional arguments.
 * @return void
 */
function webcomic_link( string $link = '%title', $post = null, array $args = [] ) {
	echo get_webcomic_link( $link, $post, $args ); // WPCS: xss ok.
}

/**
 * Display a link to the first comic.
 *
 * @uses get_webcomic_link() The relation argument is always set to `first`.
 * @param string $link Optional link text, like before{{text}}after.
 * @param mixed  $post Optional reference post.
 * @param array  $args Optional arguments.
 * @return void
 */
function first_webcomic_link( string $link = '&laquo;', $post = null, $args = [] ) {
	if ( 3 < func_num_args() || false !== strpos( $link, '%link' ) || is_string( $post ) || ! is_array( $args ) || isset( $args[0] ) ) {
		webcomic_error( __( 'The classic behavior of first_webcomic_link() is deprecated; please refer to the first_webcomic_link() documentation for updated usage information.', 'webcomic' ) );

		$args = func_get_args() + [ '%link', '&laquo;', false, false, 'storyline', '', true ];

		array_splice( $args, 2, 0, 'first' );

		if ( false === $args[7] ) {
			$args[2] = 'first-nocache';
		}

		echo webcomic_compat( 'relative_webcomic_link_', $args, '' ); // WPCS: xss ok.

		return;
	}

	$args['relation'] = 'first';

	echo get_webcomic_link( $link, $post, $args ); // WPCS: xss ok.
}

/**
 * Display a link to the previous comic.
 *
 * @uses get_webcomic_link() The relation argument is always set to `previous`.
 * @param string $link Optional link text, like before{{text}}after.
 * @param mixed  $post Optional reference post.
 * @param array  $args Optional arguments.
 * @return void
 */
function previous_webcomic_link( string $link = '&lsaquo;', $post = null, $args = [] ) {
	if ( 3 < func_num_args() || false !== strpos( $link, '%link' ) || is_string( $post ) || ! is_array( $args ) || isset( $args[0] ) ) {
		webcomic_error( __( 'The classic behavior of previous_webcomic_link() is deprecated; please refer to the previous_webcomic_link() documentation for updated usage information.', 'webcomic' ) );

		$args = func_get_args() + [ '%link', '&lsaquo;', false, false, 'storyline' ];

		array_splice( $args, 2, 0, 'previous' );

		echo webcomic_compat( 'relative_webcomic_link_', $args, '' ); // WPCS: xss ok.

		return;
	}

	$args['relation'] = 'previous';

	echo get_webcomic_link( $link, $post, $args ); // WPCS: xss ok.
}

/**
 * Display a link to the next comic.
 *
 * @uses get_webcomic_link() The relation argument is always set to `next`.
 * @param string $link Optional link text, like before{{text}}after.
 * @param mixed  $post Optional reference post.
 * @param array  $args Optional arguments.
 * @return void
 */
function next_webcomic_link( string $link = '&rsaquo;', $post = null, $args = [] ) {
	if ( 3 < func_num_args() || false !== strpos( $link, '%link' ) || is_string( $post ) || ! is_array( $args ) || isset( $args[0] ) ) {
		webcomic_error( __( 'The classic behavior of next_webcomic_link() is deprecated; please refer to the next_webcomic_link() documentation for updated usage information.', 'webcomic' ) );

		$args = func_get_args() + [ '%link', '&rsaquo;', false, false, 'storyline' ];

		array_splice( $args, 2, 0, 'next' );

		echo webcomic_compat( 'relative_webcomic_link_', $args, '' ); // WPCS: xss ok.

		return;
	}

	$args['relation'] = 'next';

	echo get_webcomic_link( $link, $post, $args ); // WPCS: xss ok.
}

/**
 * Display a link to the last comic.
 *
 * @uses get_webcomic_link() The relation argument is always set to `last`.
 * @param string $link Optional link text, like before{{text}}after.
 * @param mixed  $post Optional reference post.
 * @param array  $args Optional arguments.
 * @return void
 */
function last_webcomic_link( string $link = '&raquo;', $post = null, $args = [] ) {
	if ( 3 < func_num_args() || false !== strpos( $link, '%link' ) || is_string( $post ) || ! is_array( $args ) || isset( $args[0] ) ) {
		webcomic_error( __( 'The classic behavior of last_webcomic_link() is deprecated; please refer to the last_webcomic_link() documentation for updated usage information.', 'webcomic' ) );

		$args = func_get_args() + [ '%link', '&raquo;', false, false, 'storyline', '', true ];

		array_splice( $args, 2, 0, 'last' );

		if ( false === $args[7] ) {
			$args[2] = 'last-nocache';
		}

		echo webcomic_compat( 'relative_webcomic_link_', $args, '' ); // WPCS: xss ok.

		return;
	}

	$args['relation'] = 'last';

	echo get_webcomic_link( $link, $post, $args ); // WPCS: xss ok.
}

/**
 * Display a link to a random comic.
 *
 * @uses get_webcomic_link() The relation argument is always set to `random`.
 * @param string $link Optional link text, like before{{text}}after.
 * @param mixed  $post Optional reference post.
 * @param array  $args Optional arguments.
 * @return void
 */
function random_webcomic_link( string $link = '&infin;', $post = null, $args = [] ) {
	if ( 3 < func_num_args() || false !== strpos( $link, '%link' ) || is_string( $post ) || ! is_array( $args ) || isset( $args[0] ) ) {
		webcomic_error( __( 'The classic behavior of random_webcomic_link() is deprecated; please refer to the random_webcomic_link() documentation for updated usage information.', 'webcomic' ) );

		$args = func_get_args() + [ '%link', '&infin;', false, false, 'storyline', '', true ];

		array_splice( $args, 2, 0, 'random' );

		if ( false === $args[7] ) {
			$args[2] = 'random-nocache';
		}

		echo webcomic_compat( 'relative_webcomic_link_', $args, '' ); // WPCS: xss ok.

		return;
	}

	$args['relation'] = 'random';

	echo get_webcomic_link( $link, $post, $args ); // WPCS: xss ok.
}

/**
 * Display a list of comics.
 *
 * @uses get_webcomics_list()
 * @param array $args Optional arguments.
 * @return void
 */
function webcomics_list( array $args = [] ) {
	echo get_webcomics_list( $args ); // WPCS: xss ok.
}

/* ===== Utility Functions ================================================== */

/**
 * Sort collections by count.
 *
 * @param string $collection_one The first collection to compare.
 * @param string $collection_two The second collection to compare.
 * @return int
 * @internal For get_webcomic_collections().
 */
function sort_webcomic_collections_count( $collection_one, $collection_two ) : int {
	$count_one = get_webcomic_collection_count( $collection_one );
	$count_two = get_webcomic_collection_count( $collection_two );

	if ( $count_one === $count_two ) {
		return 0;
	} elseif ( $count_one < $count_two ) {
		return -1;
	}

	return 1;
}

/**
 * Sort collections by name.
 *
 * @param string $collection_one The first collection to compare.
 * @param string $collection_two The second collection to compare.
 * @return int
 * @internal For get_webcomic_collections().
 */
function sort_webcomic_collections_name( $collection_one, $collection_two ) : int {
	return strcmp(
		get_post_type_object( $collection_one )->labels->name,
		get_post_type_object( $collection_two )->labels->name
	);
}

/**
 * Sort collections by slug.
 *
 * @param string $collection_one The first collection to compare.
 * @param string $collection_two The second collection to compare.
 * @return int
 * @internal For get_webcomic_collections().
 */
function sort_webcomic_collections_slug( $collection_one, $collection_two ) : int {
	return strcmp( get_post_type_object( $collection_one )->has_archive, get_post_type_object( $collection_two )->has_archive );
}

/**
 * Sort collections by update time.
 *
 * @param string $collection_one The first collection to compare.
 * @param string $collection_two The second collection to compare.
 * @return int
 * @internal For get_webcomic_collections().
 */
function sort_webcomic_collections_updated( $collection_one, $collection_two ) : int {
	$time_one = (int) get_webcomic_collection_updated( 'U', $collection_one );
	$time_two = (int) get_webcomic_collection_updated( 'U', $collection_two );

	if ( $time_one === $time_two ) {
		return 0;
	} elseif ( $time_one < $time_two ) {
		return -1;
	}

	return 1;
}

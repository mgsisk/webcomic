<?php
/**
 * Taxonomy shortcodes
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Taxonomy;

/**
 * Add shortcodes.
 *
 * @return void
 */
function shortcodes() {
	add_shortcode( 'webcomic_term_count', __NAMESPACE__ . '\webcomic_term_count_shortcode' );
	add_shortcode( 'webcomic_term_description', __NAMESPACE__ . '\webcomic_term_description_shortcode' );
	add_shortcode( 'webcomic_term_media', __NAMESPACE__ . '\webcomic_term_media_shortcode' );
	add_shortcode( 'webcomic_term_title', __NAMESPACE__ . '\webcomic_term_title_shortcode' );
	add_shortcode( 'webcomic_term_updated', __NAMESPACE__ . '\webcomic_term_updated_shortcode' );
	add_shortcode( 'webcomic_term_link', __NAMESPACE__ . '\webcomic_term_link_shortcode' );
	add_shortcode( 'first_webcomic_term_link', __NAMESPACE__ . '\webcomic_term_link_shortcode' );
	add_shortcode( 'previous_webcomic_term_link', __NAMESPACE__ . '\webcomic_term_link_shortcode' );
	add_shortcode( 'next_webcomic_term_link', __NAMESPACE__ . '\webcomic_term_link_shortcode' );
	add_shortcode( 'last_webcomic_term_link', __NAMESPACE__ . '\webcomic_term_link_shortcode' );
	add_shortcode( 'random_webcomic_term_link', __NAMESPACE__ . '\webcomic_term_link_shortcode' );
	add_shortcode( 'has_webcomic_term', __NAMESPACE__ . '\has_webcomic_term_shortcode' );
	add_shortcode( 'is_a_webcomic_term', __NAMESPACE__ . '\is_a_webcomic_term_shortcode' );
	add_shortcode( 'is_a_first_webcomic_term', __NAMESPACE__ . '\is_a_webcomic_term_shortcode' );
	add_shortcode( 'is_a_previous_webcomic_term', __NAMESPACE__ . '\is_a_webcomic_term_shortcode' );
	add_shortcode( 'is_a_next_webcomic_term', __NAMESPACE__ . '\is_a_webcomic_term_shortcode' );
	add_shortcode( 'is_a_last_webcomic_term', __NAMESPACE__ . '\is_a_webcomic_term_shortcode' );
	add_shortcode( 'is_webcomic_tax', __NAMESPACE__ . '\is_webcomic_tax_shortcode' );
	add_shortcode( 'is_first_webcomic_tax', __NAMESPACE__ . '\is_webcomic_tax_shortcode' );
	add_shortcode( 'is_previous_webcomic_tax', __NAMESPACE__ . '\is_webcomic_tax_shortcode' );
	add_shortcode( 'is_next_webcomic_tax', __NAMESPACE__ . '\is_webcomic_tax_shortcode' );
	add_shortcode( 'is_last_webcomic_tax', __NAMESPACE__ . '\is_webcomic_tax_shortcode' );
	add_shortcode( 'webcomic_terms_list', __NAMESPACE__ . '\webcomic_terms_list_shortcode' );
}

/**
 * Display a comic term count.
 *
 * @uses get_webcomic_term_count()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed $term Optional term to get a post count for.
 *     @type array $args Optional arguments.
 * }
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_term_count_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'term' => null,
			'args' => [],
		], $atts, $name
	);

	if ( is_string( $args['args'] ) ) {
		parse_str( htmlspecialchars_decode( $args['args'] ), $args['args'] );
	}

	return (string) get_webcomic_term_count( $args['term'], $args['args'] );
}

/**
 * Display a comic term description.
 *
 * @uses get_webcomic_term_description()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed $term Optional term to get a description for.
 *     @type array $args Optional arguments.
 * }
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_term_description_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'term' => null,
			'args' => [],
		], $atts, $name
	);

	if ( is_string( $args['args'] ) ) {
		parse_str( htmlspecialchars_decode( $args['args'] ), $args['args'] );
	}

	return get_webcomic_term_description( $args['collection'] );
}

/**
 * Display a comic term image.
 *
 * @uses get_webcomic_term_media()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type string $size Optional media size.
 *     @type mixed  $term Optional term to get an image for.
 *     @type array  $args Optional arguments.
 * }
 * @param string $content Optional shortcode content; mapped to $args['size'].
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_term_media_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'size' => 'full',
			'term' => null,
			'args' => [],
		], $atts, $name
	);

	if ( $content ) {
		$args['size'] = $content;
	}

	if ( is_string( $args['args'] ) ) {
		parse_str( htmlspecialchars_decode( $args['args'] ), $args['args'] );
	}

	return get_webcomic_term_media( $args['size'], $args['term'], $args['args'] );
}

/**
 * Display a comic term title.
 *
 * @uses get_webcomic_term_title()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed $term Optional term to get a title for.
 *     @type array $args Optional arguments.
 * }
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_term_title_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'term' => null,
			'args' => [],
		], $atts, $name
	);

	if ( is_string( $args['args'] ) ) {
		parse_str( htmlspecialchars_decode( $args['args'] ), $args['args'] );
	}

	return get_webcomic_term_title( $args['term'], $args['args'] );
}

/**
 * Display a comic term updated time.
 *
 * @uses get_webcomic_term_updated()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type string $format Optional datetime format.
 *     @type mixed  $term Optional term to get an updated datetime for.
 *     @type array  $args Optional arguments.
 * }
 * @param string $content Optional shortcode content; mapped to $args['format'].
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_term_updated_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'format' => '',
			'term'   => null,
			'args'   => [],
		], $atts, $name
	);

	if ( $content ) {
		$args['format'] = do_shortcode( htmlspecialchars_decode( $content ) );
	}

	if ( is_string( $args['args'] ) ) {
		parse_str( htmlspecialchars_decode( $args['args'] ), $args['args'] );
	}

	return get_webcomic_term_updated( $args['format'], $args['term'], $args['args'] );
}

/**
 * Display a comic term link.
 *
 * @uses get_webcomic_term_link()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type string $link Optional link text, like before{{text}}after.
 *     @type mixed  $term Optional reference term.
 *     @type array  $args Optional arguments. The shortcode name determines the
 *                        value of the relation argument.
 *     @type mixed  $post Optional reference post.
 *     @type array  $post_args Optional post arguments.
 * }
 * @param string $content Optional shortcode content; mapped to $args['link'].
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_term_link_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'link'      => '%title',
			'term'      => null,
			'args'      => [],
			'post'      => null,
			'post_args' => [],
		], $atts, $name
	);

	if ( is_string( $args['args'] ) ) {
		parse_str( htmlspecialchars_decode( $args['args'] ), $args['args'] );
	}

	if ( is_string( $args['post_args'] ) ) {
		parse_str( htmlspecialchars_decode( $args['post_args'] ), $args['post_args'] );
	}

	$args['args']['relation'] = substr( $name, 0, strpos( $name, '_' ) );

	if ( $content ) {
		$args['link'] = do_shortcode( htmlspecialchars_decode( $content ) );
	}

	$args['link'] = htmlspecialchars_decode( $args['link'] );

	if ( 'webcomic' === $args['args']['relation'] ) {
		unset( $args['args']['relation'] );
	}

	return get_webcomic_term_link( $args['link'], $args['term'], $args['args'], $args['post'], $args['post_args'] );
}

/**
 * Display content if the post has a comic term.
 *
 * @uses has_webcomic_term()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type string $taxonomy Optional taxonomy to check. May be a collection ID
 *     (like webcomic1), a type of taxonomy (like character), a type of taxonomy
 *     prefixed with a scope keyword (like own_character or
 *     crossover_character), or empty.
 *     @type mixed  $term Optional term to check.
 *     @type mixed  $post Optional post to check.
 * }
 * @param string $content Content to display if the post has a comic term.
 * @param string $name Shortcode name.
 * @return string
 */
function has_webcomic_term_shortcode( $atts, string $content, string $name ) : string {
	if ( ! $content ) {
		return '';
	}

	$args = shortcode_atts(
		[
			'taxonomy' => '',
			'term'     => null,
			'post'     => null,
		], $atts, $name
	);

	if ( ! has_webcomic_term( $args['taxonomy'], $args['term'], $args['post'] ) ) {
		return '';
	}

	return do_shortcode( $content );
}

/**
 * Display content if the term is a comic term.
 *
 * @uses is_a_webcomic_term()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed $term Optional term to check.
 *     @type mixed $relative Optional reference term.
 *     @type array $args Optional arguments. The shortcode name determines the
 *                       value of the relation argument.
 * }
 * @param string $content Content to display if the term is a comic term.
 * @param string $name Shortcode name.
 * @return string
 */
function is_a_webcomic_term_shortcode( $atts, string $content, string $name ) : string {
	if ( ! $content ) {
		return '';
	}

	$args = shortcode_atts(
		[
			'term'     => null,
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

	if ( ! is_a_webcomic_term( $args['term'], $args['relative'], $args['args'] ) ) {
		return '';
	}

	return do_shortcode( $content );
}

/**
 * Display content if the query is for a comic taxonomy archive.
 *
 * @uses is_webcomic_tax()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed $taxonomies Optional taxonomies to check for.
 *     @type mixed $terms Optional terms to check for.
 *     @type mixed $relative Optional reference term.
 *     @type array $args Optional arguments. The shortcode name determines the
 *                       value of the relation argument.
 * }
 * @param string $content Content to display if the query is for a comic
 * taxonomy archive.
 * @param string $name Shortcode name.
 * @return string
 */
function is_webcomic_tax_shortcode( $atts, string $content, string $name ) : string {
	if ( ! $content ) {
		return '';
	}

	$args = shortcode_atts(
		[
			'taxonomies' => null,
			'terms'      => null,
			'relative'   => null,
			'args'       => [],
		], $atts, $name
	);

	if ( is_string( $args['taxonomies'] ) ) {
		$args['taxonomies'] = preg_split( '/&|,/', $args['taxonomies'] );
	}

	if ( is_string( $args['terms'] ) ) {
		$args['terms'] = preg_split( '/&|,/', $args['terms'] );
	}

	if ( is_string( $args['args'] ) ) {
		parse_str( htmlspecialchars_decode( $args['args'] ), $args['args'] );
	}

	if ( empty( $args['args']['relation'] ) ) {
		$args['args']['relation'] = substr( $name, 3, strpos( $name, '_', 3 ) - 3 );

		if ( 'webco' === $args['args']['relation'] ) {
			$args['args']['relation'] = '';
		}
	}

	if ( ! is_webcomic_tax( $args['taxonomies'], $args['terms'], $args['relative'], $args['args'] ) ) {
		return '';
	}

	return do_shortcode( $content );
}

/**
 * Display a list of comic terms.
 *
 * @uses get_webcomic_terms_list()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type int    $cloud_max Optional weighted list maximum font size.
 *     @type int    $cloud_min Optional weighted list minimum font size.
 *     @type array  $current Optional term ID of the current term or terms.
 *     @type string $feed_type Optional term feed type; one of atom, rss, or
 *                             rss2.
 *     @type string $feed Optional term feed link text.
 *     @type string $format Optional flat list format, like before{{join}}after.
 *                          Including `<select>` or `<optgroup>` elements will
 *                          convert links to `<option>` elements. Using
 *                          webcomics_optgroup as a join will replace collection
 *                          links with a list of comic `<option>` elements
 *                          wrapped in an `<optgroup>`. When $hierarchical is
 *                          true, before and after are mapped to the $start and
 *                          $end arguments.
 *     @type string $end_el Optional text to append to list items when
 *                          $hierarchical is true.
 *     @type string $end_lvl Optional text to append to a list level when
 *                           $hierarchical is true.
 *     @type string $end Optional text to append to the list when $hierarchical
 *                       is true.
 *     @type array  $link_args Optional arguments for term links.
 *     @type mixed  $link_post Optional reference post for term links.
 *     @type array  $link_post_args Optional post arguments for term links.
 *     @type string $link Optional link text, like before{{text}}after.
 *     @type string $start_el Optional text to prepend to list items when
 *                            $hierarchical is true.
 *     @type string $start_lvl Optional text to prepend to a list level when
 *                             $hierarchical is true.
 *     @type string $start Optional text to prepend to the list when
 *                         $hierarchical is true.
 *     @type string $walker Optional custom Walker class to use instead of
 *                          Mgsisk\Webcomic\Taxonomy\Walker\TermLister.
 *     @type array  $webcomics Optional get_webcomics_list() arguments.
 *     @type int    $webcomics_depth Optional depth to list comics at.
 *     @type string $collection Optional collection ID; combined with $type to
 *                              produce a taxonomy when $type is specified.
 *                              If no $collection is specified, the requested
 *                              collection (if any) will be used.
 *
 *                              This behavior changes if an integer
 *                              $object_ids has been specified; in this case,
 *                              $collections will be determined as follows:
 *                              - If $collection is empty, $collection will
 *                              include all collections.
 *                              - If $collection is own, $collection will be
 *                              the $object_ids collection.
 *                              - If $collection is crossover, $collection will
 *                              be all collections except the $object_ids
 *                              collection.
 *     @type string $type Optional taxonomy type, like character or storyline.
 *                        Specifying a $type overrides any specified
 *                        $taxonomy; it will be combined with the $collection
 *                        argument to produce a taxonomy.
 *     @type mixed $taxonomy Taxonomy name, or array of taxonomies, to which
 *                           results should be limited.
 *     @type bool   $hide_empty Whether to hide terms not assigned to any posts.
 *     @type mixed  $object_ids Optional object ID or an array of object IDs.
 *                              Results will be limited to terms associated with
 *                              these objects.
 *     @type string $order Whether to order terms in ascending or descending
 *                         order. Accepts 'ASC' (ascending) or 'DESC'
 *                         (descending).
 *     @type string $orderby Field to order terms by. Accepts term fields (
 *                           'name', 'slug', 'term_group', 'term_id', 'id',
 *                           'description', 'parent') and 'count' for term
 *                           taxonomy count.
 *     @type int    $depth The maximum hierarchical depth.
 * }
 * @param string $content Optional shortcode content; mapped to $args['link'].
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_terms_list_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'cloud_max'       => 0,
			'cloud_min'       => 0,
			'collection'      => '',
			'current'         => 0,
			'depth'           => 0,
			'end_el'          => '',
			'end_lvl'         => '',
			'end'             => '',
			'feed_type'       => 'atom',
			'feed'            => '',
			'format'          => ', ',
			'hide_empty'      => true,
			'link_args'       => [],
			'link_post_args'  => [],
			'link_post'       => null,
			'link'            => '%title',
			'object_ids'      => null,
			'order'           => 'asc',
			'orderby'         => 'name',
			'start_el'        => '',
			'start_lvl'       => '',
			'start'           => '',
			'taxonomy'        => [],
			'type'            => '',
			'walker'          => '',
			'webcomics'       => [],
			'webcomics_depth' => null,
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

	if ( is_string( $args['link_post_args'] ) ) {
		parse_str( htmlspecialchars_decode( $args['link_post_args'] ), $args['link_post_args'] );
	}

	if ( is_string( $args['taxonomy'] ) ) {
		parse_str( htmlspecialchars_decode( $args['taxonomy'] ), $args['taxonomy'] );
	}

	if ( is_string( $args['webcomics'] ) ) {
		parse_str( htmlspecialchars_decode( $args['webcomics'] ), $args['webcomics'] );
	}

	return get_webcomic_terms_list( $args );
}

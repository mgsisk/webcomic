<?php
/**
 * Global taxonomy functions
 *
 * @package Webcomic
 */

/**
 * Get a comic term.
 *
 * @see https://developer.wordpress.org/reference/functions/get_term/
 * @see https://developer.wordpress.org/reference/functions/get_terms/ Accepts
 * get_terms() arguments as well.
 * @param mixed $term Optional term to get.
 * @param array $args {
 *     Optional arguments.
 *
 *     @type string $collection Optional collection ID; combined with $type to
 *                              produce a taxonomy when $type is specified.
 *                              If no $collection is specified, the requested
 *                              collection (if any) will be used.
 *     @type bool   $hierarchical_skip Whether to skip over nested terms when
 *                                     finding a previous or next term in a
 *                                     hierarchical taxonomy. When true, a
 *                                     previous term cannot be an ancestor of
 *                                     the reference term, and a next term
 *                                     cannot be a child of the reference term.
 *     @type string $relation Optional relational term to get; one of first,
 *                            previous, next, last, or random. When set, $term
 *                            becomes the point of reference for determining the
 *                            related term to get.
 *     @type string $type Optional taxonomy type, like character or storyline.
 *                        Specifying a $type overrides any specified $taxonomy;
 *                        it will be combined with the $collection argument to
 *                        produce a taxonomy.
 * }
 * @return mixed
 * @codingStandardsIgnoreStart Generic.Metrics.CyclomaticComplexity.TooHigh - More refactoring would be counterproductive.
 */
function get_webcomic_term( $term = null, array $args = [] ) {
	/**
	 * Alter the get_webcomic_term() arguments.
	 *
	 * This filter allows hooks to alter the get_webcomic_term() arguments
	 * before they're passed to get_term().
	 *
	 * @param array $args Optional arguments.
	 * @param mixed $term Optional term to get.
	 */
	$args = apply_filters( 'get_webcomic_term_args', $args, $term );

	/**
	 * Alter the get_webcomic_term() term.
	 *
	 * This filter allows hooks to alter the get_webcomic_term() term, mostly to
	 * allow guessing at the current term when no term has been specified.
	 *
	 * @param mixed $term Optional term to get.
	 * @param array $args Optional arguments.
	 */
	$comic_term = apply_filters( 'get_webcomic_term', $term, $args );
	$comic_term = get_term( $comic_term );

	if ( ! $comic_term instanceof WP_Term || ! webcomic_taxonomy_exists( $comic_term->taxonomy ) ) {
		$comic_term = null;
	}

	if ( empty( $args['relation'] ) || ( ! $comic_term && in_array( $args['relation'], [ 'previous', 'next' ], true ) ) ) {
		return $comic_term;
	} elseif ( $comic_term && empty( $args['taxonomy'] ) ) {
		$args['taxonomy'] = $comic_term->taxonomy;
	}

	if ( 'random' === $args['relation'] ) {
		$args['orderby'] = 'rand';
	} elseif ( in_array( $args['relation'], [ 'next', 'last' ], true ) ) {
		$args['order'] = 'desc';
	}

	$args['fields'] = 'ids';
	$comic_terms    = get_webcomic_terms( $args );

	if ( ! $comic_terms ) {
		return;
	} elseif ( in_array( $args['relation'], [ 'previous', 'next' ], true ) ) {
		$key = (int) array_search( $comic_term->term_id, $comic_terms, true ) - 1;

		if ( array_key_exists( $key, $comic_terms ) ) {
			/**
			 * Alter the adjacent term.
			 *
			 * This filter allows hooks to alter which term is the adjacent term.
			 *
			 * @param int     $id The adjacent term ID.
			 * @param array   $terms The term ID's.
			 * @param WP_Term $reference The reference term.
			 * @param array   $args Optional arguments.
			 * @param mixed   $term Optional term to get.
			 */
			$adjacent_term  = apply_filters( 'get_webcomic_adjacent_term', $comic_terms[ $key ], $comic_terms, $comic_term, $args, $term );
			$comic_terms[0] = $adjacent_term;
		}
	}

	return get_webcomic_term(
		$comic_terms[0], [
			'taxonomy' => $args['taxonomy'],
		]
	);
}// @codingStandardsIgnoreEnd

/**
 * Get a term comic count.
 *
 * @param mixed $term Optional term to get a post count for.
 * @param array $args Optional arguments.
 * @return int
 */
function get_webcomic_term_count( $term = null, array $args = [] ) : int {
	$comic_term = get_webcomic_term( $term, $args );

	if ( ! $comic_term ) {
		return 0;
	}

	return $comic_term->count;
}

/**
 * Get a comic term description.
 *
 * @param mixed $term Optional term to get a description for.
 * @param array $args Optional arguments.
 * @return string
 */
function get_webcomic_term_description( $term = null, array $args = [] ) : string {
	$comic_term = get_webcomic_term( $term, $args );

	if ( ! $comic_term ) {
		return '';
	}

	return $comic_term->description;
}

/**
 * Get a comic term image.
 *
 * @see https://developer.wordpress.org/reference/functions/wp_get_attachment_image/
 * @param string $size Optional media size.
 * @param mixed  $term Optional term to get an image for.
 * @param array  $args Optional arguments.
 * @return string
 */
function get_webcomic_term_media( string $size = 'full', $term = null, array $args = [] ) : string {
	$comic_term = get_webcomic_term( $term, $args );

	if ( ! $comic_term ) {
		return '';
	}

	$media = get_term_meta( $comic_term->term_id, 'webcomic_media', true );

	return wp_get_attachment_image( $media, $size );
}

/**
 * Get a comic term title.
 *
 * @param mixed $term Optional term to get a title for.
 * @param array $args Optional arguments.
 * @return string
 */
function get_webcomic_term_title( $term = null, array $args = [] ) : string {
	$comic_term = get_webcomic_term( $term, $args );

	if ( ! $comic_term ) {
		return '';
	}

	return $comic_term->name;
}

/**
 * Get a comic term updated datetime.
 *
 * @param string $format Optional datetime format.
 * @param mixed  $term Optional term to get an updated datetime for.
 * @param array  $args Optional arguments.
 * @return string
 */
function get_webcomic_term_updated( string $format = '', $term = null, array $args = [] ) : string {
	$comic_term = get_webcomic_term( $term, $args );

	if ( ! $comic_term ) {
		return '';
	}

	return (string) mysql2date( $format, get_term_meta( $comic_term->term_id, 'webcomic_updated', true ) );
}

/**
 * Get a comic term URL.
 *
 * @param mixed $term Optional reference term.
 * @param array $args {
 *     Optional arguments.
 *
 *     @type mixed $crossover Whether to link to the terms crossover archive.
 *                            May be boolean, a collection ID, or an array of
 *                            collection ID's.
 * }
 * @param mixed $post Optional reference post.
 * @param array $post_args Optional post arguments.
 * @return string
 */
function get_webcomic_term_url( $term = null, array $args = [], $post = null, array $post_args = [] ) : string {
	$comic_term = get_webcomic_term( $term, $args );

	if ( ! $comic_term ) {
		return '';
	} elseif ( empty( $args['query_url'] ) && empty( $post_args ) ) {
		/**
		 * Alter the comic term URL.
		 *
		 * This filter allows hooks to alter the requested term's URL. Note that
		 * this filter does not run if the $query_url argument has been specified;
		 * it will run during the query URL redirect.
		 *
		 * @param string $url The comic term URL.
		 * @param WP_Term $comic_term The comic term the URL points to.
		 * @param array  $post_args Optional post arguments.
		 * @param mixed  $post Optional reference post.
		 * @param array  $args Optional arguments.
		 * @param mixed  $term Optional reference term.
		 */
		$url = apply_filters( 'get_webcomic_term_url', get_term_link( $comic_term ), $comic_term, $post_args, $post, $args, $term );

		return esc_url( $url );
	} elseif ( empty( $args['query_url'] ) ) {
		$post_args['tax_query'][] = [
			'taxonomy' => $comic_term->taxonomy,
			'field'    => 'term_id',
			'terms'    => $comic_term->term_id,
		];

		return get_webcomic_url( $post, $post_args );
	}

	$relation = '';

	if ( isset( $args['relation'] ) ) {
		$relation = $args['relation'];
	}

	unset( $args['post_type'], $args['query_url'], $args['relation'], $args['taxonomy'], $post_args['query_url'] );

	$query = [ $comic_term->taxonomy, $relation, $comic_term->term_id, str_replace( 'a:0:{}', '', maybe_serialize( $args ) ), $post, str_replace( 'a:0:{}', '', maybe_serialize( $post_args ) ) ];

	if ( is_object( $query[4] ) ) {
		$query[4] = $query[4]->ID;
	}

	return esc_url(
		add_query_arg(
			[
				'webcomic_term_url' => rawurlencode( rtrim( implode( '-', $query ), '-' ) ),
			], home_url( '/' )
		)
	);
}

/**
 * Get a comic term link.
 *
 * @param string $link Optional link text, like before{{text}}after.
 * @param mixed  $term Optional reference term.
 * @param array  $args Optional arguments.
 * @param mixed  $post Optional reference post.
 * @param array  $post_args Optional post arguments.
 * @return string
 */
function get_webcomic_term_link( string $link = '%title', $term = null, array $args = [], $post = null, $post_args = [] ) : string {
	$url = get_webcomic_term_url( $term, $args, $post, $post_args );

	if ( ! $url ) {
		return '';
	}

	preg_match( '/^(.*){{(.*)}}(.*)$/s', $link, $match );

	$match     += [ '', '', $link, '' ];
	$comic_term = get_webcomic_term( $term, $args );
	$relation   = 'self';

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
	 * |Token        |Value                                    |Example       |
	 * |-------------|-----------------------------------------|--------------|
	 * |%count       |The number of comics in the collection.  |42            |
	 * |%date        |The collection's update date.            |May 1, 2099   |
	 * |%description |The collection's description.            |It's exciting!|
	 * |%full        |The collection's full-size media.        |              |
	 * |%large       |The collection's large-size media.       |              |
	 * |%medium      |The collection's medium-size media.      |              |
	 * |%medium_large|The collection's medium_large-size media.|              |
	 * |%thumbnail   |The collection's thumbnail-size media.   |              |
	 * |%time        |The collection's updated time.           |4:52 pm       |
	 * |%title       |The collection's title.                  |Chapter 1     |
	 *
	 * @param array   $tokens The token values.
	 * @param string  $link The link text to search for tokens.
	 * @param WP_Term $comic_term The comic term the link is for.
	 */
	$tokens  = apply_filters( 'get_webcomic_term_link_tokens', [], $match[2], $comic_term );
	$anchor  = str_replace( array_keys( $tokens ), $tokens, $match[2] );
	$current = get_webcomic_term();
	$class   = [ 'webcomic-term-link', "{$comic_term->taxonomy}-link", "{$relation}-webcomic-term-link", "{$relation}-{$comic_term->taxonomy}-link" ];

	if ( 'random' !== $relation && $current && $comic_term->term_id === $current->term_id ) {
		$class[] = 'current-webcomic-term';
		$class[] = "current-{$comic_term->taxonomy}";
	}

	/**
	 * Alter the link class.
	 *
	 * This filter allows hooks to alter the CSS classes assigned to the link.
	 *
	 * @param array   $class The CSS classes.
	 * @param array   $args Optional arguments.
	 * @param WP_Term $comic_term The comic term the link is for.
	 */
	$class = apply_filters( 'get_webcomic_term_link_class', $class, $args, $comic_term );
	$class = implode( ' ', array_unique( array_map( 'esc_attr', $class ) ) );

	return "{$match[1]}<a href='{$url}' class='{$class}'>{$anchor}</a>{$match[3]}";
}

/**
 * Get comic terms.
 *
 * @see https://developer.wordpress.org/reference/functions/get_terms/ Accepts
 * get_terms() arguments as well.
 * @param array $args {
 *     Optional arguments.
 *
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
 * }
 * @return array
 * @suppress PhanAccessMethodInternal - get_terms() incorrectly triggers this.
 */
function get_webcomic_terms( array $args = [] ) : array {
	/**
	 * Alter the get_webcomic_terms() arguments.
	 *
	 * This filter allows hooks to alter the get_webcomic_terms() arguments
	 * before they're passed to get_terms().
	 *
	 * @param array $args Optional arguments.
	 */
	$args             = apply_filters( 'get_webcomic_terms_args', $args );
	$args['taxonomy'] = preg_grep( '/^webcomic\d+_.+/', (array) $args['taxonomy'] );

	if ( ! $args['taxonomy'] ) {
		return [];
	}

	$comic_terms = get_terms( $args );

	if ( isset( $args['object_ids'] ) && is_int( $args['object_ids'] ) && $args['object_ids'] && 1 === count( $args['taxonomy'] ) ) {
		$comic_terms = apply_filters( 'get_the_terms', $comic_terms, $args['object_ids'], $args['taxonomy'] );
	}

	if ( ! $comic_terms || is_wp_error( $comic_terms ) ) {
		return [];
	} elseif ( isset( $args['orderby'] ) && 'rand' === $args['orderby'] ) {
		shuffle( $comic_terms );
	}

	return $comic_terms;
}

/**
 * Get a list of comic terms.
 *
 * @uses get_webcomic_terms() The fields argument is always set to `all`.
 * @param array $args {
 *     Optional arguments.
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
 * }
 * @return string
 */
function get_webcomic_terms_list( array $args = [] ) : string {
	$args['fields'] = 'all';
	$comic_terms    = get_webcomic_terms( $args );

	if ( ! $comic_terms ) {
		return '';
	}

	/**
	 * Alter the get_webcomic_terms_list() arguments.
	 *
	 * This filter allows hooks to alter the arguments used by
	 * get_webcomic_terms_list().
	 *
	 * @param array $args The arguments to filter.
	 * @param array $comics The terms included in the list.
	 */
	$args = apply_filters(
		'get_webcomic_terms_list_args',
		/* This filter is documented in get_webcomic_terms() */
		apply_filters( 'get_webcomic_terms_args', $args ),
		$comic_terms
	);
	// @codingStandardsIgnoreLine WordPress.Classes.ClassInstantiation.MissingParenthesis - Incorrectly triggered.
	$walker = new $args['walker']();

	if ( $args['hierarchical'] ) {
		return $args['start'] . $walker->walk( $comic_terms, $args['depth'], $args ) . $args['end'];
	}

	$items = [];

	foreach ( $comic_terms as $comic_term ) {
		$output = '';

		$walker->start_el( $output, $comic_term, 0, $args, $comic_term->term_id );

		$items[] = $output;
	}

	preg_match( '/^(.*){{(.*)}}(.*)$/s', $args['format'], $match );

	$match += [ '', '', $args['format'], '' ];

	return $match[1] . implode( $match[2], $items ) . $match[3];
}

/**
 * Does the post have a comic term?
 *
 * @see https://developer.wordpress.org/reference/functions/has_term/
 * @param string $taxonomy Optional taxonomy to check. May be a collection ID
 * (like webcomic1), a type of taxonomy (like character), a type of taxonomy
 * prefixed with a scope keyword (like own_character or crossover_character), or
 * empty.
 * @param mixed  $term Optional term to check.
 * @param mixed  $post Optional post to check.
 * @return bool
 * @codingStandardsIgnoreStart Generic.Metrics.CyclomaticComplexity.TooHigh - More refactoring would be counterproductive.
 */
function has_webcomic_term( string $taxonomy = '', $term = null, $post = null ) : bool {
	$regex = "webcomic\d+_{$taxonomy}";
	$comic = get_webcomic( $post );

	if ( ! $comic ) {
		return false;
	} elseif ( $term instanceof WP_Term ) {
		$term = $term->term_id;
	}

	if ( webcomic_taxonomy_exists( $taxonomy ) ) {
		return has_term( $term, $taxonomy, $comic );
	} elseif ( webcomic_collection_exists( $taxonomy ) ) {
		$regex = "{$taxonomy}_";
	} elseif ( 0 === strpos( $taxonomy, 'own' ) ) {
		$regex = "{$comic->post_type}_" . substr( $taxonomy, 4 );
	} elseif ( 0 === strpos( $taxonomy, 'crossover' ) ) {
		$regex = "(?!{$comic->post_type}_)webcomic\d+_" . substr( $taxonomy, 10 );
	}

	foreach ( preg_grep( "/^{$regex}/", get_object_taxonomies( $comic ) ) as $taxonomy ) {
		if ( has_term( $term, $taxonomy, $comic ) ) {
			return true;
		}
	}

	return false;
}// @codingStandardsIgnoreEnd

/**
 * Is the term a comic term?
 *
 * @param mixed $term Optional term to check.
 * @param mixed $relative Optional reference term.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_a_webcomic_term( $term = null, $relative = null, array $args = [] ) : bool {
	$comic_term = get_webcomic_term( $term );

	if ( ! $comic_term ) {
		return false;
	} elseif ( ! $relative && ! $args ) {
		return true;
	}

	$comic_term_relative = get_webcomic_term( $relative, $args );

	if ( ! $comic_term_relative ) {
		return false;
	}

	return $comic_term->term_id === $comic_term_relative->term_id;
}

/**
 * Is the term the first comic term?
 *
 * @uses is_a_webcomic_term() The relation argument is always set to `first`.
 * @param mixed $term Optional term to check.
 * @param mixed $relative Optional reference term.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_a_first_webcomic_term( $term = null, $relative = null, array $args = [] ) : bool {
	$args['relation'] = 'first';

	return is_a_webcomic_term( $term, $relative, $args );
}

/**
 * Is the term the previous comic term?
 *
 * @uses is_a_webcomic_term() The relation argument is always set to `previous`.
 * @param mixed $term Optional term to check.
 * @param mixed $relative Optional reference term.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_a_previous_webcomic_term( $term = null, $relative = null, array $args = [] ) : bool {
	$args['relation'] = 'previous';

	return is_a_webcomic_term( $term, $relative, $args );
}

/**
 * Is the term the next comic term?
 *
 * @uses is_a_webcomic_term() The relation argument is always set to `next`.
 * @param mixed $term Optional term to check.
 * @param mixed $relative Optional reference term.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_a_next_webcomic_term( $term = null, $relative = null, array $args = [] ) : bool {
	$args['relation'] = 'next';

	return is_a_webcomic_term( $term, $relative, $args );
}

/**
 * Is the term the last comic term?
 *
 * @uses is_a_webcomic_term() The relation argument is always set to `last`.
 * @param mixed $term Optional term to check.
 * @param mixed $relative Optional reference term.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_a_last_webcomic_term( $term = null, $relative = null, array $args = [] ) : bool {
	$args['relation'] = 'last';

	return is_a_webcomic_term( $term, $relative, $args );
}

/**
 * Is the query for a comic taxonomy archive?
 *
 * @uses is_a_webcomic_term() The term argument is always set to the queried
 * object.
 * @param mixed $taxonomies Optional taxonomies to check for.
 * @param mixed $terms Optional terms to check for.
 * @param mixed $relative Optional reference term.
 * @param array $args {
 *     Optional arguments.
 *
 *     @type mixed $crossover Whether to check for a taxonomy crossover archive.
 *                            May be boolean, a collection ID, or an array of
 *                            collection ID's.
 * }
 * @return bool
 * @suppress PhanTypeInvalidDimOffset - $args keys incorrectly trigger this.
 */
function is_webcomic_tax( $taxonomies = null, $terms = null, $relative = null, array $args = [] ) : bool {
	$taxonomies = preg_grep( '/^webcomic\d+_.+/', (array) $taxonomies );

	if ( ! is_tax( $taxonomies, $terms ) || ! is_a_webcomic_term( get_queried_object(), $relative, $args ) ) {
		return false;
	} elseif ( empty( $args['crossover'] ) ) {
		return true;
	} elseif ( null === get_query_var( 'crossover', null ) ) {
		return false;
	}

	$args['crossover'] = preg_grep( '/^webcomic\d+$/', (array) $args['crossover'] );

	if ( ! $args['crossover'] ) {
		return true;
	}

	$collections = get_webcomic_collections(
		[
			'crossover'  => true,
			'hide_empty' => false,
		]
	);

	return (bool) array_intersect( $collections, $args['crossover'] );
}

/**
 * Is the query for the first comic taxonomy archive?
 *
 * @uses is_webcomic_tax() The relation argument is always set to `first`.
 * @param mixed $taxonomies Optional taxonomies to check for.
 * @param mixed $terms Optional terms to check for.
 * @param mixed $relative Optional reference term.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_first_webcomic_tax( $taxonomies = null, $terms = null, $relative = null, array $args = [] ) : bool {
	$args['relation'] = 'first';

	return is_webcomic_tax( $taxonomies, $terms, $relative, $args );
}

/**
 * Is the query for the previous comic taxonomy archive?
 *
 * @uses is_webcomic_tax() The relation argument is always set to `previous`.
 * @param mixed $taxonomies Optional taxonomies to check for.
 * @param mixed $terms Optional terms to check for.
 * @param mixed $relative Optional reference term.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_previous_webcomic_tax( $taxonomies = null, $terms = null, $relative = null, array $args = [] ) : bool {
	$args['relation'] = 'previous';

	return is_webcomic_tax( $taxonomies, $terms, $relative, $args );
}

/**
 * Is the query for the next comic taxonomy archive?
 *
 * @uses is_webcomic_tax() The relation argument is always set to `next`.
 * @param mixed $taxonomies Optional taxonomies to check for.
 * @param mixed $terms Optional terms to check for.
 * @param mixed $relative Optional reference term.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_next_webcomic_tax( $taxonomies = null, $terms = null, $relative = null, array $args = [] ) : bool {
	$args['relation'] = 'next';

	return is_webcomic_tax( $taxonomies, $terms, $relative, $args );
}

/**
 * Is the query for the last comic taxonomy archive?
 *
 * @uses is_webcomic_tax() The relation argument is always set to `last`.
 * @param mixed $taxonomies Optional taxonomies to check for.
 * @param mixed $terms Optional terms to check for.
 * @param mixed $relative Optional reference term.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_last_webcomic_tax( $taxonomies = null, $terms = null, $relative = null, array $args = [] ) : bool {
	$args['relation'] = 'last';

	return is_webcomic_tax( $taxonomies, $terms, $relative, $args );
}

/**
 * Does the taxonomy exist?
 *
 * @see https://developer.wordpress.org/reference/functions/taxonomy_exists/
 * @param string $taxonomy The taxonomy to check.
 * @return bool
 */
function webcomic_taxonomy_exists( string $taxonomy ) : bool {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return false;
	}

	$collections = implode( '|', webcomic( 'option.collections' ) );

	return (bool) preg_match( "/^({$collections})_.+/", $taxonomy );
}

/* ===== Display Functions ================================================ */

/**
 * Display a comic term count.
 *
 * @uses get_webcomic_term_count()
 * @param mixed $term Optional term to display a post count for.
 * @param array $args Optional arguments.
 * @return void
 */
function webcomic_term_count( $term = null, array $args = [] ) {
	echo get_webcomic_term_count( $term, $args ); // WPCS: xss ok.
}

/**
 * Display a comic term description.
 *
 * @uses get_webcomic_term_description()
 * @param mixed $term Optional term to display a description for.
 * @param array $args Optional arguments.
 * @return void
 */
function webcomic_term_description( $term = null, array $args = [] ) {
	echo get_webcomic_term_description( $term, $args ); // WPCS: xss ok.
}

/**
 * Display a comic term image.
 *
 * @uses get_webcomic_term_media()
 * @param string $size Optional media size.
 * @param mixed  $term Optional term to display an image for.
 * @param array  $args Optional arguments.
 * @return void
 */
function webcomic_term_media( string $size = 'full', $term = null, array $args = [] ) {
	echo get_webcomic_term_media( $size, $term, $args ); // WPCS: xss ok.
}

/**
 * Display a comic term title.
 *
 * @uses get_webcomic_term_title()
 * @param mixed $term Optional term to display a title for.
 * @param array $args Optional arguments.
 * @return void
 */
function webcomic_term_title( $term = null, array $args = [] ) {
	echo get_webcomic_term_title( $term, $args ); // WPCS: xss ok.
}

/**
 * Display a comic term updated datetime.
 *
 * @uses get_webcomic_term_updated()
 * @param string $format Optional datetime format.
 * @param mixed  $term Optional term to display an updated datetime for.
 * @param array  $args Optional arguments.
 * @return void
 */
function webcomic_term_updated( string $format = '', $term = null, array $args = [] ) {
	echo get_webcomic_term_updated( $format, $term, $args ); // WPCS: xss ok.
}

/**
 * Display a comic term link.
 *
 * @uses get_webcomic_term_link()
 * @param string $link Optional link text, like before{{text}}after.
 * @param mixed  $term Optional reference term.
 * @param array  $args Optional arguments.
 * @param mixed  $post Optional reference post.
 * @param array  $post_args Optional post arguments.
 * @return void
 */
function webcomic_term_link( string $link = '%title', $term = null, array $args = [], $post = null, $post_args = [] ) {
	echo get_webcomic_term_link( $link, $term, $args, $post, $post_args ); // WPCS: xss ok.
}

/**
 * Display a link to the first comic term.
 *
 * @uses get_webcomic_term_link() The relation argument is always set to
 * `first`.
 * @param string $link Optional link text, like before{{text}}after.
 * @param mixed  $term Optional reference term.
 * @param array  $args Optional arguments.
 * @param mixed  $post Optional reference post.
 * @param array  $post_args Optional post arguments.
 * @return void
 */
function first_webcomic_term_link( string $link = '&laquo; %title', $term = null, array $args = [], $post = null, $post_args = [] ) {
	$args['relation'] = 'first';

	echo get_webcomic_term_link( $link, $term, $args, $post, $post_args ); // WPCS: xss ok.
}

/**
 * Display a link to the previous comic term.
 *
 * @uses get_webcomic_term_link() The relation argument is always set to
 * `previous`.
 * @param string $link Optional link text, like before{{text}}after.
 * @param mixed  $term Optional reference term.
 * @param array  $args Optional arguments.
 * @param mixed  $post Optional reference post.
 * @param array  $post_args Optional post arguments.
 * @return void
 */
function previous_webcomic_term_link( string $link = '&lsaquo; %title', $term = null, array $args = [], $post = null, $post_args = [] ) {
	$args['relation'] = 'previous';

	echo get_webcomic_term_link( $link, $term, $args, $post, $post_args ); // WPCS: xss ok.
}

/**
 * Display a link to the next comic term.
 *
 * @uses get_webcomic_term_link() The relation argument is always set to
 * `next`.
 * @param string $link Optional link text, like before{{text}}after.
 * @param mixed  $term Optional reference term.
 * @param array  $args Optional arguments.
 * @param mixed  $post Optional reference post.
 * @param array  $post_args Optional post arguments.
 * @return void
 */
function next_webcomic_term_link( string $link = '%title &rsaquo;', $term = null, array $args = [], $post = null, $post_args = [] ) {
	$args['relation'] = 'next';

	echo get_webcomic_term_link( $link, $term, $args, $post, $post_args ); // WPCS: xss ok.
}

/**
 * Display a link to the last comic term.
 *
 * @uses get_webcomic_term_link() The relation argument is always set to
 * `last`.
 * @param string $link Optional link text, like before{{text}}after.
 * @param mixed  $term Optional reference term.
 * @param array  $args Optional arguments.
 * @param mixed  $post Optional reference post.
 * @param array  $post_args Optional post arguments.
 * @return void
 */
function last_webcomic_term_link( string $link = '%title &raquo;', $term = null, array $args = [], $post = null, $post_args = [] ) {
	$args['relation'] = 'last';

	echo get_webcomic_term_link( $link, $term, $args, $post, $post_args ); // WPCS: xss ok.
}

/**
 * Display a link to a random comic term.
 *
 * @uses get_webcomic_term_link() The relation argument is always set to
 * `random`.
 * @param string $link Optional link text, like before{{text}}after.
 * @param mixed  $term Optional reference term.
 * @param array  $args Optional arguments.
 * @param mixed  $post Optional reference post.
 * @param array  $post_args Optional post arguments.
 * @return void
 */
function random_webcomic_term_link( string $link = '%title', $term = null, array $args = [], $post = null, $post_args = [] ) {
	$args['relation'] = 'random';

	echo get_webcomic_term_link( $link, $term, $args, $post, $post_args ); // WPCS: xss ok.
}

/**
 * Display a list of comic terms.
 *
 * @uses get_webcomic_terms_list()
 * @param array $args Optional arguments.
 * @return void
 */
function webcomic_terms_list( array $args = [] ) {
	echo get_webcomic_terms_list( $args ); // WPCS: xss ok.
}

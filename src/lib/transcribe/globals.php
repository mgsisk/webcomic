<?php
/**
 * Global transcribe functions
 *
 * @package Webcomic
 */

/**
 * Get a comic transcript.
 *
 * @see https://developer.wordpress.org/reference/functions/get_post/
 * @param mixed $post Optional post to get.
 * @return mixed
 */
function get_webcomic_transcript( $post = null ) {
	$transcript = get_post( $post );

	if ( ! $transcript || 'webcomic_transcript' !== $transcript->post_type ) {
		return;
	}

	return $transcript;
}

/**
 * Get comic transcript authors.
 *
 * @param array $args {
 *     Optional arguments.
 *
 *     @type string $hide_duplicates Optional author field to check for
 *                                   duplicate values; one of name, email,
 *                                   url, time, ip, or an empty string to not
 *                                   hide duplicates.
 *     @type int    $limit Optional maximum number of authors to return.
 *     @type string $order Optional author sort order; one of asc or desc.
 *     @type string $orderby Optional author sort field; one of name, email,
 *                           url, time, or ip.
 *     @type mixed  $post Optional post to get transcript authors for.
 *     @type string $prefer_duplicate Optional duplicate priority to use when
 *                                    $hide_duplicates is not empty; one of
 *                                    first or last.
 * }
 * @return array
 * @suppress PhanAccessMethodInternal - util_webcomic_transcript_authors_dedupe() incorrectly triggers this.
 */
function get_webcomic_transcript_authors( array $args = [] ) : array {
	$args      += [
		'post' => null,
	];
	$transcript = get_webcomic_transcript( $args['post'] );

	if ( ! $transcript ) {
		return [];
	}

	$authors = get_post_meta( $transcript->ID, 'webcomic_transcript_authors' );

	if ( ! $authors ) {
		return [];
	}

	/**
	 * Alter the get_webcomic_transcript_authors() arguments.
	 *
	 * This filter allows hooks to alter the arguments used by
	 * get_webcomic_transcript_authors().
	 *
	 * @param array $args Optional arguments.
	 */
	$args = apply_filters(
		'get_webcomic_transcript_authors_args',
		$args + [
			'prefer_duplicate' => 'first',
			'hide_duplicates'  => 'name',
			'limit'            => 0,
			'order'            => 'asc',
			'orderby'          => 'time',
		]
	);

	if ( $args['hide_duplicates'] && isset( $authors[0][ $args['hide_duplicates'] ] ) ) {
		$authors = util_webcomic_transcript_authors_dedupe( $authors, $args );
	}

	/**
	 * Alter the transcript authors.
	 *
	 * This filter allows hooks to alter the list of transcript authors before
	 * standard arguments are applied to the authors array.
	 *
	 * @param array $authors The list of authors.
	 * @param array $args Optional arguments.
	 */
	$authors = apply_filters( 'get_webcomic_transcript_authors', $authors, $args );

	if ( 'rand' === $args['orderby'] ) {
		shuffle( $authors );
	} elseif ( function_exists( "sort_webcomic_transcript_authors_{$args['orderby']}" ) ) {
		usort( $authors, "sort_webcomic_transcript_authors_{$args['orderby']}" );
	}

	if ( 'desc' === $args['order'] ) {
		$authors = array_reverse( $authors );
	}

	if ( 0 < $args['limit'] ) {
		$authors = array_slice( $authors, 0, $args['limit'] );
	}

	return $authors;
}

/**
 * Get a list of comic transcript authors.
 *
 * @uses get_webcomic_transcript_authors()
 * @param array $args {
 *     Optional arguments.
 *
 *     @type string $format Optional list format, like before{{join}}after.
 *                          Including `<select>` or `<optgroup>` elements will
 *                          convert links to `<option>` elements.
 *     @type string $link_rel Optional link rel attribute value; may be a
 *                            space-separated list of valid link types or an
 *                            empty string to remove the href attribute.
 *     @type string $link   Optional link text, like before{{text}}after.
 *     @type string $walker Optional custom Walker class to use instead of
 *                          Mgsisk\Webcomic\Transcribe\Walker\AuthorLister.
 * }
 * @return string
 */
function get_webcomic_transcript_authors_list( array $args = [] ) : string {
	$authors = get_webcomic_transcript_authors( $args );

	if ( ! $authors ) {
		return '';
	}

	/**
	 * Alter the get_webcomic_transcript_authors_list() arguments.
	 *
	 * This filter allows hooks to alter the arguments used by
	 * get_webcomic_transcript_authors_list().
	 *
	 * @param array $args The arguments to filter.
	 * @param array $authors The authors included in the list.
	 */
	$args = apply_filters( 'get_webcomic_transcript_authors_list_args', $args, $authors );
	// @codingStandardsIgnoreLine WordPress.Classes.ClassInstantiation.MissingParenthesis - Incorrectly triggered.
	$walker = new $args['walker']();
	$items  = [];

	foreach ( $authors as $author ) {
		$output = '';

		$walker->start_el( $output, (object) $author, 0, $args, $author['ip'] );

		$items[] = $output;
	}

	preg_match( '/^(.*){{(.*)}}(.*)$/s', $args['format'], $match );

	$match += [ '', '', $args['format'], '' ];

	return $match[1] . implode( $match[2], $items ) . $match[3];
}

/**
 * Get a comic transcript form.
 *
 * @param array $args {
 *     Optional arguments.
 *
 *     @type string $cancel_link Optional cancel link text. Should not contain
 *                               block-level elements, as the link will be
 *                               wrapped in a `<span>`.
 *     @type string $cancel_link_hash Optional cancel link URL hash.
 *     @type array  $fields Optional form fields.
 *     @type string $form Optional form structure, like before{{fields}}after.
 *                        Should include the %fields token.
 *     @type string $format Optional form format, like before{{form}}after.
 *                          Should include the %cancel-link and %form tokens.
 *     @type string $logged_in_as Optional message to display to logged-in
 *                                users.
 *     @type string $must_log_in Optional message to display to anonymous
 *                               users if they must login to transcribe.
 *     @type mixed  $post_parent Optional comic the form belongs to.
 *     @type mixed  $post Optional comic transcript ID the form is for.
 *     @type string $transcript_notes_after Optional message to display after
 *                                          the transcript field.
 *     @type string $transcript_notes_before Optional message to display
 *                                           before the the form fields.
 * }
 * @return string
 * @suppress PhanTypeExpectedObjectPropAccess - Incorrectly triggered.
 */
function get_webcomic_transcript_form( array $args = [] ) : string {
	$args += [
		'post_parent' => null,
	];

	$args['post_parent'] = get_webcomic( $args['post_parent'] );

	if ( ! $args['post_parent'] || ! webcomic_transcripts_open( $args['post_parent'] ) ) {
		return '';
	}

	/**
	 * Alter the get_webcomic_transcript_form() arguments.
	 *
	 * This filter allows hooks to alter the arguments used by
	 * get_webcomic_transcript_form().
	 *
	 * @param array $args The arguments to filter.
	 */
	$args = apply_filters( 'get_webcomic_transcript_form_args', $args );

	preg_match( '/^(.*){{(.*)}}(.*)$/s', $args['format'], $match );

	$match    += [ '', '', $args['format'], '' ];
	$comic_url = apply_filters( 'the_permalink', get_permalink( $args['post_parent']->ID ) );

	if ( 'loggedin' === webcomic( "option.{$args['post_parent']->post_type}.transcribe_require" ) && ! is_user_logged_in() ) {
		return $match[1] . str_replace( [ '%cancel-link', '%form' ], [ '', $match[2] ], sprintf( $args['must_log_in'], wp_login_url( $comic_url ) ) ) . $match[3];
	} elseif ( $args['post'] instanceof WP_Post ) {
		$args['post'] = $args['post']->ID;
	}

	if ( ! is_a_webcomic_transcript( $args['post'], 'pending' ) || wp_get_post_parent_id( $args['post'] ) !== $args['post_parent']->ID ) {
		$args['post'] = 0;
	}

	if ( is_user_logged_in() ) {
		$user = wp_get_current_user();
		// Translators: User display name.
		$args['transcript_notes_before'] = sprintf( (string) $args['logged_in_as'], get_edit_user_link(), esc_attr( sprintf( __( 'Logged in as %s. Edit your profile.', 'webcomic' ), $user->display_name ) ), $user->display_name, wp_logout_url( $comic_url ) );
	}

	preg_match( '/^(.*){{(.*)}}(.*)$/s', $args['form'], $parts );

	$parts += [ '', '', $args['form'], '' ];

	/**
	 * Alter the get_webcomic_transcript_form() fields.
	 *
	 * This filter allows hooks to alter the fields passed to
	 * get_webcomic_transcript_form().
	 *
	 * @param array $fields The fields to filter.
	 * @param array $args The get_webcomic_transcript_form() arguments.
	 * @param array $commenter The current commenter.
	 */
	$fields      = apply_filters( 'get_webcomic_transcript_form_fields', $args['fields'], $args, wp_get_current_commenter() );
	$form        = sprintf( $parts[1], $comic_url ) . $args['transcript_notes_before'] . str_replace( '%fields', implode( '', $fields ), $parts[2] ) . $parts[3];
	$cancel_link = get_webcomic_link(
		'<span class="webcomic-transcribe-cancel-link">{{' . $args['cancel_link'] . '}}</span>', $args['post_parent'], [
			'transcribe' => $args['cancel_link_hash'],
		]
	);

	return $match[1] . str_replace( [ '%cancel-link', '%form' ], [ $cancel_link, $form ], $match[2] ) . $match[3];
}

/**
 * Get comic transcript languages.
 *
 * @uses get_webcomic_transcript_terms() The type argument is always set to
 * `language`.
 * @param array $args Optional arguments.
 * @return array
 */
function get_webcomic_transcript_languages( array $args = [] ) : array {
	$args['type'] = 'language';

	return get_webcomic_transcript_terms( $args );
}

/**
 * Get a list of comic transcript languages.
 *
 * @uses get_webcomic_transcript_terms_list() The type argument is always set to
 * `language`.
 * @param array $args Optional arguments.
 * @return string
 */
function get_webcomic_transcript_languages_list( array $args = [] ) : string {
	$args['type'] = 'language';

	return get_webcomic_transcript_terms_list( $args );
}

/**
 * Get comic transcript terms.
 *
 * @see https://developer.wordpress.org/reference/functions/get_terms/ Accepts
 * get_terms() arguments as well.
 * @param array $args {
 *     Optional arguments.
 *
 *     @type array  $related Optional arguments to use when retrieving post
 *                           transcripts to determine what terms are related
 *                           to a post.
 *     @type string $type Optional taxonomy type, like character or storyline.
 *                        Specifying a $type overrides any specified $taxonomy;
 *                        it will be combined with the $collection argument to
 *                        produce a taxonomy.
 * }
 * @return array
 * @suppress PhanAccessMethodInternal - get_terms() incorrectly triggers this.
 */
function get_webcomic_transcript_terms( array $args = [] ) : array {
	/**
	 * Alter the get_webcomic_transcript_terms() arguments.
	 *
	 * This filter allows hooks to alter the get_webcomic_transcript_terms()
	 * arguments before they're passed to get_terms().
	 *
	 * @param array $args Optional arguments.
	 */
	$args             = apply_filters( 'get_webcomic_transcript_terms_args', $args );
	$args['taxonomy'] = preg_grep( '/^webcomic_transcript_.+/', (array) $args['taxonomy'] );

	if ( ! $args['taxonomy'] ) {
		return [];
	}

	$comic_transcript_terms = get_terms( $args );

	if ( isset( $args['object_ids'] ) && is_int( $args['object_ids'] ) && $args['object_ids'] && 1 === count( $args['taxonomy'] ) ) {
		$comic_transcript_terms = apply_filters( 'get_the_terms', $comic_transcript_terms, $args['object_ids'], $args['taxonomy'] );
	}

	if ( ! $comic_transcript_terms || is_wp_error( $comic_transcript_terms ) ) {
		return [];
	} elseif ( isset( $args['orderby'] ) && 'rand' === $args['orderby'] ) {
		shuffle( $comic_transcript_terms );
	}

	return $comic_transcript_terms;
}

/**
 * Get a list of comic transcript terms.
 *
 * @uses get_webcomic_transcript_terms() The fields argument is always set to
 * `all`.
 * @param array $args {
 *     Optional arguments.
 *
 *     @type array  $current Optional term ID of the current term or terms.
 *     @type string $format Optional flat list format, like before{{join}}after.
 *                          Including `<select>` or `<optgroup>` elements will
 *                          convert links to `<option>` elements. Using
 *                          webcomics_optgroup as a join will replace collection
 *                          links with a list of comic `<option>` elements
 *                          wrapped in an `<optgroup>`. When $hierarchical is
 *                          true, before and after are mapped to the $start and
 *                          $end arguments.
 *     @type string $start Optional text to prepend to the list when
 *                         $hierarchical is true.
 *     @type string $start_lvl Optional text to prepend to a list level when
 *                             $hierarchical is true.
 *     @type string $start_el Optional text to prepend to list items when
 *                            $hierarchical is true.
 *     @type string $end_el Optional text to append to list items when
 *                          $hierarchical is true.
 *     @type string $end_lvl Optional text to append to a list level when
 *                           $hierarchical is true.
 *     @type string $end Optional text to append to the list when $hierarchical
 *                       is true.
 *     @type string $walker Optional custom Walker class to use instead of
 *                          Mgsisk\Webcomic\Transcribe\TermLister.
 * }
 * @return string
 */
function get_webcomic_transcript_terms_list( array $args = [] ) : string {
	$args['fields']         = 'all';
	$comic_transcript_terms = get_webcomic_transcript_terms( $args );

	if ( ! $comic_transcript_terms ) {
		return '';
	}

	/**
	 * Alter the get_webcomic_transcript_terms_list() arguments.
	 *
	 * This filter allows hooks to alter the arguments used by
	 * get_webcomic_transcript_terms_list().
	 *
	 * @param array $args The arguments to filter.
	 * @param array $comics The terms included in the list.
	 */
	$args = apply_filters( 'get_webcomic_transcript_terms_list_args', $args, $comic_transcript_terms );
	// @codingStandardsIgnoreLine WordPress.Classes.ClassInstantiation.MissingParenthesis - Incorrectly triggered.
	$walker = new $args['walker']();

	if ( $args['hierarchical'] ) {
		return $args['start'] . $walker->walk( $comic_transcript_terms, $args['depth'], $args ) . $args['end'];
	}

	$items = [];

	foreach ( $comic_transcript_terms as $comic_transcript_term ) {
		$output = '';

		$walker->start_el( $output, $comic_transcript_term, 0, $args, $comic_transcript_term->term_id );

		$items[] = $output;
	}

	preg_match( '/^(.*){{(.*)}}(.*)$/s', $args['format'], $match );

	$match += [ '', '', $args['format'], '' ];

	return $match[1] . implode( $match[2], $items ) . $match[3];
}

/**
 * Get comic transcripts.
 *
 * @see https://developer.wordpress.org/reference/functions/get_posts/ Accepts
 * get_posts() arguments as well.
 * @param array $args Optional arguments.
 * @return array
 */
function get_webcomic_transcripts( $args = [] ) : array {
	if ( 1 < func_num_args() || ! is_array( $args ) ) {
		webcomic_error( __( 'The classic behavior of get_webcomic_transcripts() is deprecated; please refer to the get_webcomic_transcripts() documentation for updated usage information.', 'webcomic' ) );

		return webcomic_compat( 'get_webcomic_transcripts_', func_get_args(), [] );
	}

	$args += [
		'post_type'      => 'webcomic_transcript',
		'posts_per_page' => -1,
	];

	if ( ! array_key_exists( 'post_parent', $args ) ) {
		$comic = get_webcomic();

		if ( ! $comic ) {
			return [];
		}

		$args['post_parent'] = $comic->ID;
	}

	/**
	 * Alter the get_webcomic_transcripts() arguments.
	 *
	 * This filter allows hooks to alter the get_webcomic_transcripts()
	 * arguments before they're passed to get_posts().
	 *
	 * @param array $args Optional arguments.
	 */
	$args              = apply_filters( 'get_webcomic_transcripts_args', $args );
	$args['post_type'] = 'webcomic_transcript';

	return get_posts( $args );
}

/**
 * Get a list of comic transcripts.
 *
 * @uses get_webcomic_transcripts() The fields argument is always set to 'ids'.
 * @param array $args {
 *     Optional arguments.
 *
 *     @type array  $authors_list Optional arguments for
 *                                get_webcomic_transcript_authors_list(); only
 *                                used when $item contains the %authors token.
 *     @type string $edit_link Optional edit link format, like
 *                             before{{text}}after.
 *     @type string $format Optional list format, like before{{join}}after.
 *     @type string $item Optional item format, like before{{item}}after. The
 *                        before text should include two sprintf() tokens,
 *                        which will be replaced with the transcript ID and
 *                        CSS class names, respectively.
 *     @type array  $languages_list Optional arguments for
 *                                get_webcomic_transcript_languages_list();
 *                                only used when $item contains the %languages
 *                                token.
 *     @type string $parent_link Optional parent link format, like
 *                               before{{text}}after.
 *     @type string $walker Optional custom Walker class to use instead of
 *                         Mgsisk\Webcomic\Transcribe\Walker\TranscriptLister.
 * }
 * @return string
 */
function get_webcomic_transcripts_list( array $args = [] ) : string {
	$args['fields'] = 'ids';
	$transcripts    = get_webcomic_transcripts( $args );

	if ( ! $transcripts ) {
		return '';
	}

	/**
	 * Alter the get_webcomic_transcripts_list() arguments.
	 *
	 * This filter allows hooks to alter the arguments used by
	 * get_webcomic_transcripts_list().
	 *
	 * @param array $args The arguments to filter.
	 * @param array $transcripts The post transcripts included in the list.
	 */
	$args = apply_filters( 'get_webcomic_transcripts_list_args', $args, $transcripts );
	// @codingStandardsIgnoreLine WordPress.Classes.ClassInstantiation.MissingParenthesis - Incorrectly triggered.
	$walker = new $args['walker']();
	$items  = [];

	foreach ( $transcripts as $transcript ) {
		$output = '';

		$walker->start_el( $output, get_webcomic_transcript( $transcript ), 0, $args, $transcript );

		$items[] = $output;
	}

	preg_match( '/^(.*){{(.*)}}(.*)$/s', $args['format'], $match );

	$match += [ '', '', $args['format'], '' ];

	return $match[1] . implode( $match[2], $items ) . $match[3];
}

/**
 * Does the post have a comic transcript language?
 *
 * @uses has_webcomic_transcript_term() The taxonomy argument is always set to
 * `language`.
 * @param mixed $term Optional term to check.
 * @param mixed $post Optional post to check.
 * @return bool
 */
function has_webcomic_transcript_language( $term = null, $post = null ) : bool {
	return has_webcomic_transcript_term( 'language', $term, $post );
}

/**
 * Does the post have a comic transcript term?
 *
 * @param string $taxonomy Optional taxonomy to check. May be a type of
 * taxonomy (like language) or empty.
 * @param mixed  $term Optional term to check.
 * @param mixed  $post Optional post to check.
 * @return bool
 */
function has_webcomic_transcript_term( string $taxonomy = '', $term = null, $post = null ) : bool {
	$regex      = "webcomic_transcript_{$taxonomy}";
	$transcript = get_webcomic_transcript( $post );

	if ( ! $transcript ) {
		return false;
	} elseif ( $term instanceof WP_Term ) {
		$term = $term->term_id;
	}

	if ( webcomic_transcript_taxonomy_exists( $taxonomy ) ) {
		return has_term( $term, $taxonomy, $transcript );
	}

	$taxonomies = preg_grep( "/^{$regex}/", get_object_taxonomies( $transcript ) );

	foreach ( $taxonomies as $taxonomy ) {
		if ( ! has_term( $term, $taxonomy, $transcript ) ) {
			continue;
		}

		return true;
	}

	return false;
}

/**
 * Does the comic have any transcripts?
 *
 * @uses get_webcomic_transcripts()
 * @param mixed $post Optional post to check for transcripts.
 * @param array $args Optional arguments.
 * @return bool
 */
function has_webcomic_transcripts( $post = null, array $args = [] ) : bool {
	$args = [
		'fields'         => 'ids',
		'post_parent'    => get_webcomic( $post ),
		'posts_per_page' => 1,
	] + $args;

	if ( $args['post_parent'] ) {
		$args['post_parent'] = $args['post_parent']->ID;
	}

	return (bool) get_webcomic_transcripts( $args );
}

/**
 * Is the post a comic transcript?
 *
 * @param mixed  $post Optional post to check.
 * @param string $status Optional post status to check.
 * @return boolean
 */
function is_a_webcomic_transcript( $post = null, $status = '' ) : bool {
	$transcript = get_webcomic_transcript( $post );

	if ( ! $transcript ) {
		return false;
	} elseif ( ! $status ) {
		return true;
	}

	return $transcript->post_status === $status;
}

/**
 * Does the transcript taxonomy exist?
 *
 * @see https://developer.wordpress.org/reference/functions/taxonomy_exists/
 * @param string $taxonomy The taxonomy to check.
 * @return bool
 */
function webcomic_transcript_taxonomy_exists( string $taxonomy ) : bool {
	if ( ! taxonomy_exists( $taxonomy ) || 0 !== strpos( $taxonomy, 'webcomic_transcript_' ) ) {
		return false;
	}

	return true;
}

/**
 * Does the comic allow transcribing?
 *
 * @param mixed $post Optional comic to check.
 * @return bool
 */
function webcomic_transcripts_open( $post = null ) : bool {
	$comic = get_webcomic( $post );

	if ( ! $comic || ! get_post_meta( $comic->ID, 'webcomic_transcribe', true ) ) {
		return false;
	}

	return true;
}

/*
===== Display Functions ======================================================

The following template tags directly display content returned by some of the
template tags found above.
*/

/**
 * Display a list of comic transcript authors.
 *
 * @uses get_webcomic_transcript_authors_list()
 * @param array $args Optional arguments.
 * @return void
 */
function webcomic_transcript_authors_list( array $args = [] ) {
	echo get_webcomic_transcript_authors_list( $args ); // WPCS: xss ok.
}

/**
 * Display a comic transcript form.
 *
 * @uses get_webcomic_transcript_form()
 * @param array $args Optional arguments.
 * @return void
 */
function webcomic_transcript_form( $args = [] ) {
	if ( 1 < func_num_args() || array_intersect( [ 'language_field', 'transcript_field', 'transcript_notes_success', 'transcript_notes_failure', 'wysiwyg_editor', 'id_form', 'label_submit', 'title_submit' ], array_flip( $args ) ) ) {
		webcomic_error( __( 'The classic behavior of webcomic_transcript_form() is deprecated; please refer to the webcomic_transcript_form() documentation for updated usage information.', 'webcomic' ) );

		echo webcomic_compat( 'webcomic_transcript_form_', func_get_args(), '' ); // WPCS: xss ok.

		return;
	}

	echo get_webcomic_transcript_form( $args ); // WPCS: xss ok.
}

/**
 * Display a list of comic transcript languages.
 *
 * @uses get_webcomic_transcript_languages_list()
 * @param array $args Optional arguments.
 * @return void
 */
function webcomic_transcript_languages_list( array $args = [] ) {
	echo get_webcomic_transcript_languages_list( $args ); // WPCS: xss ok.
}

/**
 * Display a list of comic transcript terms.
 *
 * @uses get_webcomic_transcript_terms_list()
 * @param array $args Optional arguments.
 * @return void
 */
function webcomic_transcript_term_list( array $args = [] ) {
	echo get_webcomic_transcript_terms_list( $args ); // WPCS: xss ok.
}

/**
 * Display a list of comic transcripts.
 *
 * @uses get_webcomic_transcripts_list()
 * @param array $args Optional arguments.
 * @return void
 */
function webcomic_transcripts_list( array $args = [] ) {
	echo get_webcomic_transcripts_list( $args ); // WPCS: xss ok.
}

/* ===== Utility Functions ================================================== */

/**
 * Sort authors by email.
 *
 * @param array $author_one The first author to compare.
 * @param array $author_two The second author to compare.
 * @return int
 * @internal For get_webcomic_transcript_authors().
 */
function sort_webcomic_transcript_authors_email( array $author_one, array $author_two ) : int {
	return strcmp( $author_one['email'], $author_two['email'] );
}

/**
 * Sort authors by ip.
 *
 * @param array $author_one The first author to compare.
 * @param array $author_two The second author to compare.
 * @return int
 * @internal For get_webcomic_transcript_authors().
 */
function sort_webcomic_transcript_authors_ip( array $author_one, array $author_two ) : int {
	return strcmp( $author_one['ip'], $author_two['ip'] );
}

/**
 * Sort authors by name.
 *
 * @param array $author_one The first author to compare.
 * @param array $author_two The second author to compare.
 * @return int
 * @internal For get_webcomic_transcript_authors().
 */
function sort_webcomic_transcript_authors_name( array $author_one, array $author_two ) : int {
	return strcmp( $author_one['name'], $author_two['name'] );
}

/**
 * Sort authors by time.
 *
 * @param array $author_one The first author to compare.
 * @param array $author_two The second author to compare.
 * @return int
 * @internal For get_webcomic_transcript_authors().
 */
function sort_webcomic_transcript_authors_time( array $author_one, array $author_two ) : int {
	$time_one = mysql2date( 'U', $author_one['time'] );
	$time_two = mysql2date( 'U', $author_two['time'] );

	if ( $time_one === $time_two ) {
		return 0;
	} elseif ( $time_one < $time_two ) {
		return -1;
	}

	return 1;
}

/**
 * Sort authors by url.
 *
 * @param array $author_one The first author to compare.
 * @param array $author_two The second author to compare.
 * @return int
 * @internal For get_webcomic_transcript_authors().
 */
function sort_webcomic_transcript_authors_url( array $author_one, array $author_two ) : int {
	return strcmp( $author_one['url'], $author_two['url'] );
}

/**
 * Remove duplciate authors from a set of comic transcript authors.
 *
 * @param array $authors The authors to dedupe.
 * @param array $args The get_webcomic_transcript_authors arguments.
 * @return array
 * @internal For get_webcomic_transcript_authors().
 */
function util_webcomic_transcript_authors_dedupe( array $authors, array $args ) : array {
	$deduped = [];

	foreach ( $authors as $author ) {
		if ( 'first' === $args['prefer_duplicate'] && isset( $deduped[ $author[ $args['hide_duplicates'] ] ] ) ) {
			continue;
		}

		$deduped[ $author[ $args['hide_duplicates'] ] ] = $author;
	}

	return array_values( $deduped );
}

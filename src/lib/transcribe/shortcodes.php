<?php
/**
 * Transcribe shortcodes
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe;

/**
 * Add shortcodes.
 *
 * @return void
 */
function shortcodes() {
	add_shortcode( 'has_webcomic_transcripts', __NAMESPACE__ . '\has_webcomic_transcripts_shortcode' );
	add_shortcode( 'is_a_webcomic_transcript', __NAMESPACE__ . '\is_a_webcomic_transcript_shortcode' );
	add_shortcode( 'webcomic_transcript_form', __NAMESPACE__ . '\webcomic_transcript_form_shortcode' );
	add_shortcode( 'webcomic_transcript_languages_list', __NAMESPACE__ . '\webcomic_transcript_languages_list_shortcode' );
	add_shortcode( 'webcomic_transcript_terms_list', __NAMESPACE__ . '\webcomic_transcript_terms_list_shortcode' );
	add_shortcode( 'webcomic_transcripts_list', __NAMESPACE__ . '\webcomic_transcripts_list_shortcode' );
	add_shortcode( 'webcomic_transcripts_open', __NAMESPACE__ . '\webcomic_transcripts_open_shortcode' );
}

/**
 * Display content if the comic has transcripts.
 *
 * @uses has_webcomic_transcripts()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed $post Optional post to check for transcripts.
 *     @type mixed $post_status Optional post status or statuses to check.
 * }
 * @param string $content Content to display if the comic has transcripts.
 * @param string $name Shortcode name.
 * @return string
 */
function has_webcomic_transcripts_shortcode( $atts, string $content, string $name ) : string {
	if ( ! $content ) {
		return '';
	}

	$args = shortcode_atts(
		[
			'post'        => null,
			'post_status' => null,
		], $atts, $name
	);
	$post = null;

	if ( $args['post'] ) {
		$post = $args['post'];
	}

	unset( $args['post'] );

	if ( is_string( $args['post_status'] ) ) {
		parse_str( htmlspecialchars_decode( $args['post_status'] ), $args['post_status'] );

		$args['post_status'] = array_keys( $args['post_status'] );
	}

	if ( ! has_webcomic_transcripts( $post, $args ) ) {
		return '';
	}

	return do_shortcode( $content );
}

/**
 * Display content if the post is a comic transcript.
 *
 * @uses is_a_webcomic_transcript()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed  $post Optional post to check.
 *     @type string $status Optional post status to check.
 * }
 * @param string $content Content to display if the post is a comic transcript.
 * @param string $name Shortcode name.
 * @return string
 */
function is_a_webcomic_transcript_shortcode( $atts, string $content, string $name ) : string {
	if ( ! $content ) {
		return '';
	}

	$args = shortcode_atts(
		[
			'post'   => null,
			'status' => '',
		], $atts, $name
	);

	if ( ! is_a_webcomic_transcript( $args['post'], $args['status'] ) ) {
		return '';
	}

	return do_shortcode( $content );
}

/**
 * Display a comic transcript form.
 *
 * NOTE Because of the complexity of this functions arguments, we set all of the
 * defaults to null and then remove arguments from the list that the user didn't
 * set themselves, allowing the function defaults to take over without having to
 * repeat them here.
 *
 * @uses get_webcomic_transcript_form()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type array  $fields Optional form fields.
 *     @type string $form Optional form structure, like before{{fields}}after.
 *                        Should include the %fields token.
 *     @type string $format Optional form format, like before{{form}}after.
 *                          Should include the %cancel-link and %form tokens.
 *     @type string $logged_in_as Optional message to display to logged-in
 *                                users.
 *     @type string $must_log_in Optional message to display to anonymous
 *                               users if they must login to transcribe.
 *     @type mixed  $post Optional comic transcript ID the form is for.
 *     @type string $transcript_notes_after Optional message to display after
 *                                          the transcript field.
 *     @type string $transcript_notes_before Optional message to display
 *                                           before the the form fields.
 * }
 * @param string $content Optional shortcode content; mapped to $args['item'].
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_transcript_form_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'fields'                  => null,
			'form'                    => null,
			'format'                  => null,
			'logged_in_as'            => null,
			'must_log_in'             => null,
			'post'                    => null,
			'transcript_notes_after'  => null,
			'transcript_notes_before' => null,
		], $atts, $name
	);

	foreach ( $args as $key => $arg ) {
		if ( null === $arg ) {
			unset( $args[ $key ] );

			continue;
		}

		$args[ $key ] = htmlspecialchars_decode( $arg );

		if ( 'fields' === $key ) {
			parse_str( $args[ $key ], $args[ $key ] );
		}
	}

	return get_webcomic_transcript_form( $args );
}

/**
 * Display a list of comic transcript languages.
 *
 * @uses [webcomic_transcript_terms_list] The type argument is always set to
 * `language`.
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_transcript_languages_list_shortcode( $atts, string $content, string $name ) : string {
	if ( ! $atts ) {
		$atts = [];
	}

	$atts['type'] = 'language';

	return webcomic_transcript_terms_list_shortcode( $atts, $content, $name );
}

/**
 * Display a list of comic transcript languages.
 *
 * @uses get_webcomic_transcript_terms_list()
 * @param array  $atts {
 *     Optional attributes.
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
 *     @type array  $related Optional arguments to use when retrieving post
 *                           transcripts to determine what terms are related
 *                           to a post.
 *     @type string $type Optional taxonomy type, like character or storyline.
 *                        Specifying a $type overrides any specified $taxonomy;
 *                        it will be combined with the $collection argument to
 *                        produce a taxonomy.
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
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_transcript_terms_list_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'current'    => [],
			'depth'      => 0,
			'end_el'     => '',
			'end_lvl'    => '',
			'end'        => '',
			'format'     => ', ',
			'hide_empty' => true,
			'object_ids' => null,
			'order'      => 'asc',
			'orderby'    => 'name',
			'related'    => null,
			'start_el'   => '',
			'start_lvl'  => '',
			'start'      => '',
			'taxonomy'   => null,
			'type'       => '',
			'walker'     => '',
		], $atts, $name
	);

	$args['format'] = htmlspecialchars_decode( $args['format'] );

	if ( is_string( $args['current'] ) ) {
		parse_str( htmlspecialchars_decode( $args['current'] ), $args['current'] );
	}

	if ( is_null( $args['related'] ) ) {
		unset( $args['related'] );
	} elseif ( is_string( $args['related'] ) && false !== strpos( $args['related'], '=' ) ) {
		parse_str( htmlspecialchars_decode( $args['related'] ), $args['related'] );
	}

	if ( is_string( $args['taxonomy'] ) ) {
		parse_str( htmlspecialchars_decode( $args['taxonomy'] ), $args['taxonomy'] );
	}

	return get_webcomic_transcript_terms_list( $args );
}

/**
 * Display a list of comic transcripts.
 *
 * NOTE Because of the complexity of this functions arguments, we set all of the
 * defaults to null and then remove arguments from the list that the user didn't
 * set themselves, allowing the function defaults to take over without having to
 * repeat them here.
 *
 * @uses get_webcomic_transcripts_list()
 * @param array  $atts {
 *     Optional attributes.
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
 *     @type string $order Optional post sort order; one of asc or desc.
 *     @type string $orderby Optional post sort field; one of date, none, name,
 *                           author, title, modified, menu_order, parent, ID,
 *                           rand, relevance, or comment_count.
 *     @type int    $posts_per_page Optional number of posts to retrieve.
 *     @type mixed  $post_status Optional post status or statuses to check.
 * }
 * @param string $content Optional shortcode content; mapped to $args['item'].
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_transcripts_list_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'authors_list'   => null,
			'edit_link'      => null,
			'format'         => null,
			'item'           => null,
			'languages_list' => null,
			'order'          => null,
			'orderby'        => null,
			'parent_link'    => null,
			'post_status'    => null,
			'posts_per_page' => null,
			'walker'         => null,
		], $atts, $name
	);

	foreach ( $args as $key => $arg ) {
		if ( null === $arg ) {
			unset( $args[ $key ] );

			continue;
		}

		$args[ $key ] = htmlspecialchars_decode( $arg );

		if ( in_array( $key, [ 'authors_list', 'languages_list' ], true ) ) {
			parse_str( $args[ $key ], $args[ $key ] );
		}
	}

	if ( $content ) {
		$args['item'] = do_shortcode( htmlspecialchars_decode( $content ) );
	}

	return get_webcomic_transcripts_list( $args );
}

/**
 * Display content if the comic allows transcribing.
 *
 * @uses webcomic_transcripts_open()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed $post Optional comic to check.
 * }
 * @param string $content Content to display if the comic allows transcribing.
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_transcripts_open_shortcode( $atts, string $content, string $name ) : string {
	if ( ! $content ) {
		return '';
	}

	$args = shortcode_atts(
		[
			'post' => null,
		], $atts, $name
	);

	if ( ! webcomic_transcripts_open( $args['post'] ) ) {
		return '';
	}

	return do_shortcode( $content );
}

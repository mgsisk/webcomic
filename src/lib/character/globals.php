<?php
/**
 * Global character functions
 *
 * @package Webcomic
 */

/**
 * Get a comic character.
 *
 * @uses get_webcomic_term() The type argument is always set to `character`.
 * @param mixed $term Optional term to get.
 * @param array $args Optional arguments.
 * @return mixed
 */
function get_webcomic_character( $term = null, array $args = [] ) {
	$args['type'] = 'character';

	return get_webcomic_term( $term, $args );
}

/**
 * Get a term comic count.
 *
 * @uses get_webcomic_term_count() The type argument is always set to
 * `character`.
 * @param mixed $term Optional term to get a post count for.
 * @param array $args Optional arguments.
 * @return int
 */
function get_webcomic_character_count( $term = null, array $args = [] ) : int {
	$args['type'] = 'character';

	return get_webcomic_term_count( $term, $args );
}

/**
 * Get a comic character description.
 *
 * @uses get_webcomic_term_description() The type argument is always set to
 * `character`.
 * @param mixed $term Optional term to get a description for.
 * @param array $args Optional arguments.
 * @return string
 */
function get_webcomic_character_description( $term = null, array $args = [] ) : string {
	$args['type'] = 'character';

	return get_webcomic_term_description( $term, $args );
}

/**
 * Get a comic character image.
 *
 * @uses get_webcomic_term_media() The type argument is always set to
 * `character`.
 * @param string $size Optional media size.
 * @param mixed  $term Optional term to get an image for.
 * @param array  $args Optional arguments.
 * @return string
 */
function get_webcomic_character_media( string $size = 'full', $term = null, array $args = [] ) : string {
	$args['type'] = 'character';

	return get_webcomic_term_media( $size, $term, $args );
}

/**
 * Get a comic character title.
 *
 * @uses get_webcomic_term_title() The type argument is always set to
 * `character`.
 * @param mixed $term Optional term to get a title for.
 * @param array $args Optional arguments.
 * @return string
 */
function get_webcomic_character_title( $term = null, array $args = [] ) : string {
	$args['type'] = 'character';

	return get_webcomic_term_title( $term, $args );
}

/**
 * Get a comic character updated datetime.
 *
 * @uses get_webcomic_term_updated() The type argument is always set to
 * `character`.
 * @param string $format Optional datetime format.
 * @param mixed  $term Optional term to get an updated datetime for.
 * @param array  $args Optional arguments.
 * @return string
 */
function get_webcomic_character_updated( string $format = '', $term = null, array $args = [] ) : string {
	$args['type'] = 'character';

	return get_webcomic_term_updated( $format, $term, $args );
}

/**
 * Get a comic character URL.
 *
 * @uses get_webcomic_term_url() The type argument is always set to `character`.
 * @param mixed $term Optional reference term.
 * @param array $args Optional arguments.
 * @param mixed $post Optional reference post.
 * @param array $post_args Optional post arguments.
 * @return string
 */
function get_webcomic_character_url( $term = null, array $args = [], $post = null, array $post_args = [] ) : string {
	$args['type'] = 'character';

	return get_webcomic_term_url( $term, $args, $post, $post_args );
}

/**
 * Get a comic character link.
 *
 * @uses get_webcomic_term_link() The type argument is always set to
 * `character`.
 * @param string $link Optional link text, like before{{text}}after.
 * @param mixed  $term Optional reference term.
 * @param array  $args Optional arguments.
 * @param mixed  $post Optional reference post.
 * @param array  $post_args Optional post arguments.
 * @return string
 */
function get_webcomic_character_link( string $link = '%title', $term = null, array $args = [], $post = null, $post_args = [] ) : string {
	$args['type'] = 'character';

	return get_webcomic_term_link( $link, $term, $args, $post, $post_args );
}

/**
 * Get comic characters.
 *
 * @uses get_webcomic_terms() The type argument is always set to `character`.
 * @param array $args Optional arguments.
 * @return array
 */
function get_webcomic_characters( array $args = [] ) : array {
	$args['type'] = 'character';

	return get_webcomic_terms( $args );
}

/**
 * Get a list of comic characters.
 *
 * @uses get_webcomic_terms_list() The type argument is always set to
 * `character`.
 * @param array $args Optional arguments.
 * @return string
 */
function get_webcomic_characters_list( array $args = [] ) : string {
	$args['type'] = 'character';

	return get_webcomic_terms_list( $args );
}

/**
 * Does the post have a comic character?
 *
 * @uses has_webcomic_term() The taxonomy argument is always set to
 * `character` or `{$scope}_character`.
 * @param string $scope Optional scope to check. May be a collection ID (like
 * webcomic1), a scope keyword (like own or crossover), or empty.
 * @param mixed  $term Optional term to check.
 * @param mixed  $post Optional post to check.
 * @return bool
 */
function has_webcomic_character( $scope = '', $term = null, $post = null ) : bool {
	if ( ! is_string( $scope ) || ! preg_match( '/^own|crossover|webcomic\d+$/', $scope ) || ( is_object( $term ) && ! $term instanceof WP_Term ) ) {
		webcomic_error( __( 'The classic behavior of has_webcomic_character() is deprecated; please refer to the is_webcomic_character() documentation for updated usage information.', 'webcomic' ) );

		$comic = get_webcomic( $term );

		if ( ! $comic ) {
			return false;
		}

		return has_term( $scope, "{$comic->post_type}_character", $comic );
	}

	$taxonomy = 'character';

	if ( $scope ) {
		$taxonomy = "{$scope}_character";
	}

	return has_webcomic_term( $taxonomy, $term, $post );
}

/**
 * Is the term a comic character?
 *
 * @uses is_a_webcomic_term()
 * @param mixed $term Optional term to check.
 * @param mixed $relative Optional reference term.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_a_webcomic_character( $term = null, $relative = null, array $args = [] ) : bool {
	if ( ! is_a_webcomic_term( $term, $relative, $args ) ) {
		return false;
	}

	$comic_term = get_webcomic_term( $term );

	return (bool) preg_match( '/^webcomic\d+_character/', $comic_term->taxonomy );
}

/**
 * Is the term the first comic character?
 *
 * @uses is_a_webcomic_character() The relation argument is always set to
 * `first`.
 * @param mixed $term Optional term to check.
 * @param mixed $relative Optional reference term.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_a_first_webcomic_character( $term = null, $relative = null, array $args = [] ) : bool {
	$args['relation'] = 'first';

	return is_a_webcomic_character( $term, $relative, $args );
}

/**
 * Is the term the previous comic character?
 *
 * @uses is_a_webcomic_character() The relation argument is always set to
 * `previous`.
 * @param mixed $term Optional term to check.
 * @param mixed $relative Optional reference term.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_a_previous_webcomic_character( $term = null, $relative = null, array $args = [] ) : bool {
	$args['relation'] = 'previous';

	return is_a_webcomic_character( $term, $relative, $args );
}

/**
 * Is the term the next comic character?
 *
 * @uses is_a_webcomic_character() The relation argument is always set to
 * `next`.
 * @param mixed $term Optional term to check.
 * @param mixed $relative Optional reference term.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_a_next_webcomic_character( $term = null, $relative = null, array $args = [] ) : bool {
	$args['relation'] = 'next';

	return is_a_webcomic_character( $term, $relative, $args );
}

/**
 * Is the term the last comic character?
 *
 * @uses is_a_webcomic_character() The relation argument is always set to
 * `last`.
 * @param mixed $term Optional term to check.
 * @param mixed $relative Optional reference term.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_a_last_webcomic_character( $term = null, $relative = null, array $args = [] ) : bool {
	$args['relation'] = 'last';

	return is_a_webcomic_character( $term, $relative, $args );
}

/**
 * Is the query for a comic taxonomy archive?
 *
 * @uses is_webcomic_tax() The taxonomies argument is always set to a character
 * taxonomy or character taxonomies.
 * @param mixed $collections Optional collections to check for.
 * @param mixed $terms Optional terms to check for.
 * @param mixed $relative Optional reference term.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_webcomic_character( $collections = null, $terms = null, $relative = null, array $args = [] ) : bool {
	if ( is_int( $collections ) || ( is_string( $collections ) && ! webcomic_collection_exists( $collections ) ) || ( is_array( $collections ) && ! array_filter(
		$collections, function( $value ) {
			return webcomic_collection_exists( $value );
		}
	) ) ) {
		webcomic_error( __( 'The classic behavior of is_webcomic_character() is deprecated; please refer to the is_webcomic_character() documentation for updated usage information.', 'webcomic' ) );

		return webcomic_compat( 'is_webcomic_tax_', array_merge( [ 'character' ], func_get_args() ), false );
	}

	$taxonomies  = [];
	$collections = (array) $collections;

	if ( ! $collections ) {
		$collections = webcomic( 'option.collections' );
	}

	foreach ( $collections as $collection ) {
		$taxonomies[] = "{$collection}_character";
	}

	return is_webcomic_tax( $taxonomies, $terms, $relative, $args );
}

/**
 * Is the query for the first comic taxonomy archive?
 *
 * @uses is_webcomic_character() The relation argument is always set to `first`.
 * @param mixed $collections Optional collections to check for.
 * @param mixed $terms Optional terms to check for.
 * @param mixed $relative Optional reference term.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_first_webcomic_character( $collections = null, $terms = null, $relative = null, array $args = [] ) : bool {
	$args['relation'] = 'first';

	return is_webcomic_character( $collections, $terms, $relative, $args );
}

/**
 * Is the query for the previous comic taxonomy archive?
 *
 * @uses is_webcomic_character() The relation argument is always set to
 * `previous`.
 * @param mixed $collections Optional collections to check for.
 * @param mixed $terms Optional terms to check for.
 * @param mixed $relative Optional reference term.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_previous_webcomic_character( $collections = null, $terms = null, $relative = null, array $args = [] ) : bool {
	$args['relation'] = 'previous';

	return is_webcomic_character( $collections, $terms, $relative, $args );
}

/**
 * Is the query for the next comic taxonomy archive?
 *
 * @uses is_webcomic_character() The relation argument is always set to `next`.
 * @param mixed $collections Optional collections to check for.
 * @param mixed $terms Optional terms to check for.
 * @param mixed $relative Optional reference term.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_next_webcomic_character( $collections = null, $terms = null, $relative = null, array $args = [] ) : bool {
	$args['relation'] = 'next';

	return is_webcomic_character( $collections, $terms, $relative, $args );
}

/**
 * Is the query for the last comic taxonomy archive?
 *
 * @uses is_webcomic_character() The relation argument is always set to `last`.
 * @param mixed $collections Optional collections to check for.
 * @param mixed $terms Optional terms to check for.
 * @param mixed $relative Optional reference term.
 * @param array $args Optional arguments.
 * @return bool
 */
function is_last_webcomic_character( $collections = null, $terms = null, $relative = null, array $args = [] ) : bool {
	$args['relation'] = 'last';

	return is_webcomic_character( $collections, $terms, $relative, $args );
}

/*
===== Display Functions ======================================================

The following template tags directly display content returned by some of the
template tags found above.
*/

/**
 * Display a comic character count.
 *
 * @uses get_webcomic_character_count()
 * @param mixed $term Optional term to display a post count for.
 * @param array $args Optional arguments.
 * @return void
 */
function webcomic_character_count( $term = null, array $args = [] ) {
	echo get_webcomic_character_count( $term, $args ); // WPCS: xss ok.
}

/**
 * Display a comic character description.
 *
 * @uses get_webcomic_character_description()
 * @param mixed $term Optional term to display a description for.
 * @param array $args Optional arguments.
 * @return void
 */
function webcomic_character_description( $term = null, $args = [] ) {
	if ( ! is_array( $args ) ) {
		webcomic_error( __( 'The classic behavior of webcomic_character_description() is deprecated; please refer to the webcomic_character_description() documentation for updated usage information.', 'webcomic' ) );

		echo webcomic_compat( 'webcomic_term_description_', func_get_args(), '' ); // WPCS: xss ok.

		return;
	}

	echo get_webcomic_character_description( $term, $args ); // WPCS: xss ok.
}

/**
 * Display a comic character image.
 *
 * @uses get_webcomic_character_media()
 * @param string $size Optional media size.
 * @param mixed  $term Optional term to display an image for.
 * @param array  $args Optional arguments.
 * @return void
 */
function webcomic_character_media( string $size = 'full', $term = null, array $args = [] ) {
	echo get_webcomic_character_media( $size, $term, $args ); // WPCS: xss ok.
}

/**
 * Display a comic character title.
 *
 * @uses get_webcomic_character_title()
 * @param mixed $term Optional term to display a title for.
 * @param array $args Optional arguments.
 * @return void
 */
function webcomic_character_title( $term = null, $args = [] ) {
	if ( 2 < func_num_args() || is_string( $term ) || ! is_array( $args ) ) {
		webcomic_error( __( 'The classic behavior of webcomic_character_title() is deprecated; please refer to the webcomic_character_title() documentation for updated usage information.', 'webcomic' ) );

		echo webcomic_compat( 'webcomic_term_title_', func_get_args(), '' ); // WPCS: xss ok.

		return;
	}

	echo get_webcomic_character_title( $term, $args ); // WPCS: xss ok.
}

/**
 * Display a comic character updated datetime.
 *
 * @uses get_webcomic_character_updated()
 * @param string $format Optional datetime format.
 * @param mixed  $term Optional term to display an updated datetime for.
 * @param array  $args Optional arguments.
 * @return void
 */
function webcomic_character_updated( string $format = '', $term = null, array $args = [] ) {
	echo get_webcomic_character_updated( $format, $term, $args ); // WPCS: xss ok.
}

/**
 * Display a comic character link.
 *
 * @uses get_webcomic_character_link()
 * @param string $link Optional link text, like before{{text}}after.
 * @param mixed  $term Optional reference term.
 * @param array  $args Optional arguments.
 * @param mixed  $post Optional reference post.
 * @param array  $post_args Optional post arguments.
 * @return void
 */
function webcomic_character_link( string $link = '%title', $term = null, array $args = [], $post = null, $post_args = [] ) {
	echo get_webcomic_character_link( $link, $term, $args, $post, $post_args ); // WPCS: xss ok.
}

/**
 * Display a link to the first comic character.
 *
 * @uses get_webcomic_character_link() The relation argument is always set to
 * `first`.
 * @param string $link Optional link text, like before{{text}}after.
 * @param mixed  $term Optional reference term.
 * @param array  $args Optional arguments.
 * @param mixed  $post Optional reference post.
 * @param array  $post_args Optional post arguments.
 * @return void
 */
function first_webcomic_character_link( string $link = '&laquo; %title', $term = null, $args = [], $post = null, $post_args = [] ) {
	if ( 5 < func_num_args() || false !== strpos( $link, '%link' ) || is_string( $term ) || ! is_array( $args ) || is_array( $post ) || ! is_array( $post_args ) ) {
		webcomic_error( __( 'The classic behavior of first_webcomic_character_link() is deprecated; please refer to the first_webcomic_character_link() documentation for updated usage information.', 'webcomic' ) );

		$args = func_get_args() + [ '%link', '%title', 'archive', [], get_webcomic_collection(), true ];

		array_splice( $args, 3, 0, 'first' );

		if ( false === $args[6] ) {
			$args[2] = 'first-nocache';
		}

		$args[6] = $args[4];
		$args[4] = "{$args[5]}_character";
		$args[5] = $args[6];

		echo webcomic_compat( 'relative_webcomic_term_link_', $args, '' ); // WPCS: xss ok.

		return;
	}

	$args['relation'] = 'first';

	echo get_webcomic_character_link( $link, $term, $args, $post, $post_args ); // WPCS: xss ok.
}

/**
 * Display a link to the previous comic character.
 *
 * @uses get_webcomic_character_link() The relation argument is always set to
 * `previous`.
 * @param string $link Optional link text, like before{{text}}after.
 * @param mixed  $term Optional reference term.
 * @param array  $args Optional arguments.
 * @param mixed  $post Optional reference post.
 * @param array  $post_args Optional post arguments.
 * @return void
 */
function previous_webcomic_character_link( string $link = '&lsaquo; %title', $term = null, array $args = [], $post = null, $post_args = [] ) {
	if ( false !== strpos( $link, '%link' ) || is_string( $term ) || ! is_array( $args ) || is_array( $post ) ) {
		webcomic_error( __( 'The classic behavior of previous_webcomic_character_link() is deprecated; please refer to the previous_webcomic_character_link() documentation for updated usage information.', 'webcomic' ) );

		$args = func_get_args() + [ '%link', '%title', 'archive', [], get_webcomic_collection() ];

		array_splice( $args, 3, 0, 'previous' );

		$args[6] = $args[4];
		$args[4] = "{$args[5]}_character";
		$args[5] = $args[6];

		echo webcomic_compat( 'relative_webcomic_term_link_', $args, '' ); // WPCS: xss ok.

		return;
	}

	$args['relation'] = 'previous';

	echo get_webcomic_character_link( $link, $term, $args, $post, $post_args ); // WPCS: xss ok.
}

/**
 * Display a link to the next comic character.
 *
 * @uses get_webcomic_character_link() The relation argument is always set to
 * `next`.
 * @uses get_webcomic_character_link()
 * @param string $link Optional link text, like before{{text}}after.
 * @param mixed  $term Optional reference term.
 * @param array  $args Optional arguments.
 * @param mixed  $post Optional reference post.
 * @param array  $post_args Optional post arguments.
 * @return void
 */
function next_webcomic_character_link( string $link = '%title &rsaquo;', $term = null, array $args = [], $post = null, $post_args = [] ) {
	if ( false !== strpos( $link, '%link' ) || is_string( $term ) || ! is_array( $args ) || is_array( $post ) ) {
		webcomic_error( __( 'The classic behavior of next_webcomic_character_link() is deprecated; please refer to the next_webcomic_character_link() documentation for updated usage information.', 'webcomic' ) );

		$args = func_get_args() + [ '%link', '%title', 'archive', [], get_webcomic_collection() ];

		array_splice( $args, 3, 0, 'next' );

		$args[6] = $args[4];
		$args[4] = "{$args[5]}_character";
		$args[5] = $args[6];

		echo webcomic_compat( 'relative_webcomic_term_link_', $args, '' ); // WPCS: xss ok.

		return;
	}

	$args['relation'] = 'next';

	echo get_webcomic_character_link( $link, $term, $args, $post, $post_args ); // WPCS: xss ok.
}

/**
 * Display a link to the last comic character.
 *
 * @uses get_webcomic_character_link() The relation argument is always set to
 * `last`.
 * @param string $link Optional link text, like before{{text}}after.
 * @param mixed  $term Optional reference term.
 * @param array  $args Optional arguments.
 * @param mixed  $post Optional reference post.
 * @param array  $post_args Optional post arguments.
 * @return void
 */
function last_webcomic_character_link( string $link = '%title &raquo;', $term = null, array $args = [], $post = null, $post_args = [] ) {
	if ( 5 < func_num_args() || false !== strpos( $link, '%link' ) || is_string( $term ) || ! is_array( $args ) || is_array( $post ) || ! is_array( $post_args ) ) {
		webcomic_error( __( 'The classic behavior of last_webcomic_character_link() is deprecated; please refer to the last_webcomic_character_link() documentation for updated usage information.', 'webcomic' ) );

		$args = func_get_args() + [ '%link', '%title', 'archive', [], get_webcomic_collection(), true ];

		array_splice( $args, 3, 0, 'last' );

		if ( false === $args[6] ) {
			$args[2] = 'last-nocache';
		}

		$args[6] = $args[4];
		$args[4] = "{$args[5]}_character";
		$args[5] = $args[6];

		echo webcomic_compat( 'relative_webcomic_term_link_', $args, '' ); // WPCS: xss ok.

		return;
	}

	$args['relation'] = 'last';

	echo get_webcomic_character_link( $link, $term, $args, $post, $post_args ); // WPCS: xss ok.
}

/**
 * Display a link to a random comic character.
 *
 * @uses get_webcomic_character_link() The relation argument is always set to
 * `random`.
 * @param string $link Optional link text, like before{{text}}after.
 * @param mixed  $term Optional reference term.
 * @param array  $args Optional arguments.
 * @param mixed  $post Optional reference post.
 * @param array  $post_args Optional post arguments.
 * @return void
 */
function random_webcomic_character_link( string $link = '%title', $term = null, array $args = [], $post = null, $post_args = [] ) {
	if ( 5 < func_num_args() || false !== strpos( $link, '%link' ) || is_string( $term ) || ! is_array( $args ) || is_array( $post ) || ! is_array( $post_args ) ) {
		webcomic_error( __( 'The classic behavior of random_webcomic_character_link() is deprecated; please refer to the random_webcomic_character_link() documentation for updated usage information.', 'webcomic' ) );

		$args = func_get_args() + [ '%link', '%title', 'archive', [], get_webcomic_collection(), true ];

		array_splice( $args, 3, 0, 'random' );

		if ( false === $args[6] ) {
			$args[2] = 'random-nocache';
		}

		$args[6] = $args[4];
		$args[4] = "{$args[5]}_character";
		$args[5] = $args[6];

		echo webcomic_compat( 'relative_webcomic_term_link_', $args, '' ); // WPCS: xss ok.

		return;
	}

	$args['relation'] = 'random';

	echo get_webcomic_character_link( $link, $term, $args, $post, $post_args ); // WPCS: xss ok.
}

/**
 * Display a list of comic characters.
 *
 * @uses get_webcomic_characters_list()
 * @param array $args Optional arguments.
 * @return void
 */
function webcomic_characters_list( array $args = [] ) {
	echo get_webcomic_characters_list( $args ); // WPCS: xss ok.
}

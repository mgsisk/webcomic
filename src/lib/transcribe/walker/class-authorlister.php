<?php
/**
 * Mgsisk\Webcomic\Transcribe\Walker\AuthorLister class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe\Walker;

use Walker;

/**
 * Standard walker for get_webcomic_transcript_authors_list().
 */
class AuthorLister extends Walker {
	/**
	 * Walker type.
	 *
	 * @var string
	 */
	public $tree_type = 'webcomic_transcript_author';

	/**
	 * Start element output.
	 *
	 * @param string $output The current output.
	 * @param object $comic_transcript_author The current transcript author.
	 * @param int    $depth The current depth.
	 * @param array  $args The walker arguments.
	 * @param int    $id The current transcript author ID.
	 * @return void
	 */
	public function start_el( &$output, $comic_transcript_author, $depth = 0, $args = [], $id = 0 ) {
		preg_match( '/^(.*){{(.*)}}(.*)$/s', $args['link'], $match );

		$match += [ '', '', $args['link'], '' ];

		/**
		 * Alter the link tokens.
		 *
		 * This filter allows hooks to alter the replaceable link text tokens and
		 * their values.
		 *
		 * ## Core tokens
		 *
		 * |Token    |Value                                     |Example    |
		 * |---------|------------------------------------------|-----------|
		 * |%avatar  |The author's avatar                       |           |
		 * |%avatar-*|The author's avatar at the specified size.|           |
		 * |%name    |The transcript author's name`.            |Angie      |
		 * |%url     |The transcript author's URL.              |example.com|
		 *
		 * The `*` in these tokens is a placeholder for a specific avatar pixel
		 * size, like `%avatar-48`.
		 *
		 * @param array  $tokens The token values.
		 * @param string $link The link text to search for tokens.
		 * @param array  $author The transcript author the link is for.
		 */
		$tokens = apply_filters( 'get_webcomic_transcript_author_link_tokens', [], $match[2], (array) $comic_transcript_author );
		$anchor = str_replace( array_keys( $tokens ), $tokens, $match[2] );
		$atts   = str_replace( [ "href='{$comic_transcript_author->url}' rel='' ", "href='' rel='{$args['link_rel']}' " ], '', "href='{$comic_transcript_author->url}' rel='{$args['link_rel']}' " );
		$output = "{$match[1]}<a {$atts}class='webcomic-transcript-author'>{$anchor}</a>{$match[3]}";

		if ( $args['options'] ) {
			$output = str_replace( [ '<a ', 'href=', '</a>' ], [ '<option value="' . esc_attr( $comic_transcript_author->name ) . '" ', 'data-webcomic-url=', '</option>' ], $output );
			$output = preg_replace( "/ rel='.+?'/", '', $output );
		}
	}
}

<?php
/**
 * Mgsisk\Webcomic\Transcribe\Walker\TermLister class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe\Walker;

use Walker;
use WP_Term;

/**
 * Standard walker for get_webcomic_transcript_terms_list().
 */
class TermLister extends Walker {
	/**
	 * Walker type.
	 *
	 * @var string
	 */
	public $tree_type = 'webcomic_transcript_taxonomy';

	/**
	 * Database fields.
	 *
	 * @var array
	 */
	public $db_fields = [
		'id'     => 'term_id',
		'parent' => 'parent',
	];

	/**
	 * Start level output.
	 *
	 * @param string $output The current output.
	 * @param int    $depth The current depth.
	 * @param array  $args The walker arguments.
	 * @return void
	 */
	public function start_lvl( &$output, $depth = 0, $args = [] ) {
		if ( $args['options'] ) {
			return;
		}

		$output .= $args['start_lvl'];
	}

	/**
	 * End level output.
	 *
	 * @param string $output The current output.
	 * @param int    $depth The current depth.
	 * @param array  $args The walker arguments.
	 * @return void
	 */
	public function end_lvl( &$output, $depth = 0, $args = [] ) {
		if ( $args['options'] ) {
			return;
		}

		$output .= $args['end_lvl'];
	}

	/**
	 * Start element output.
	 *
	 * @param string  $output The current output.
	 * @param WP_Term $comic_transcript_term The current term.
	 * @param int     $depth The current depth.
	 * @param array   $args The walker arguments.
	 * @param int     $id The current term ID.
	 * @return void
	 */
	public function start_el( &$output, $comic_transcript_term, $depth = 0, $args = [], $id = 0 ) {
		if ( $args['options'] ) {
			$comic_transcript_term->name = str_repeat( '&nbsp;', $depth * 4 ) . $comic_transcript_term->name;
		}

		$item = "<a class='webcomic-transcript-term {$comic_transcript_term->taxonomy} {$comic_transcript_term->taxonomy}-{$comic_transcript_term->slug}'>{$comic_transcript_term->name}</a>";

		if ( in_array( $comic_transcript_term->term_id, (array) $args['current'], true ) ) {
			$item = str_replace( " {$comic_transcript_term->taxonomy}-{$comic_transcript_term->slug}", " {$comic_transcript_term->taxonomy}-{$comic_transcript_term->slug} current-webcomic-transcript-term current-{$comic_transcript_term->taxonomy}", $item );
		}

		if ( $args['options'] ) {
			$item = str_replace( [ '<a ', " current-webcomic-transcript-term current-{$comic_transcript_term->taxonomy}'>", '</a>' ], [ "<option value='{$comic_transcript_term->term_id}' ", " current-webcomic-term current-{$comic_transcript_term->taxonomy}' selected>", '</option>' ], $item );
		}

		if ( $args['hierarchical'] ) {
			$output .= $args['start_el'] . $item;

			return;
		}

		$output = $item;
	}

	/**
	 * End element output.
	 *
	 * @param string  $output The current output.
	 * @param WP_Term $comic_transcript_term The current term.
	 * @param int     $depth The current depth.
	 * @param array   $args The walker arguments.
	 * @return void
	 */
	public function end_el( &$output, $comic_transcript_term, $depth = 0, $args = [] ) {
		if ( $args['options'] ) {
			return;
		}

		$output .= $args['end_el'];
	}
}

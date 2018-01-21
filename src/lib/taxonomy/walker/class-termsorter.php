<?php
/**
 * Mgsisk\Webcomic\Taxonomy\Walker\TermSorter class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Taxonomy\Walker;

use Walker;
use WP_Term;

/**
 * Sort walker for administering hierarchical taxonomies.
 */
class TermSorter extends Walker {
	/**
	 * Walker type.
	 *
	 * @var string
	 */
	public $tree_type = 'webcomic_taxonomy';

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
		$output .= '<ol>';
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
		$output .= '</ol>';
	}

	/**
	 * Start element output.
	 *
	 * @param string  $output The current output.
	 * @param WP_Term $comic_term The current term.
	 * @param int     $depth The current depth.
	 * @param array   $args The walker arguments.
	 * @param int     $id The current term ID.
	 * @return void
	 */
	public function start_el( &$output, $comic_term, $depth = 0, $args = [], $id = 0 ) {
		$order = esc_attr( (int) get_term_meta( $comic_term->term_id, 'webcomic_order', true ) );
		$media = wp_get_attachment_image( get_term_meta( $comic_term->term_id, 'webcomic_media', true ), 'thumbnail' );
		$item  = "<li id='term-{$comic_term->term_id}'>
			<label>
				<input type='hidden' name='webcomic_term_parent[{$comic_term->term_id}]' value='{$comic_term->parent}' form='webcomic_sorter'>
				<input type='number' name='webcomic_term_order[{$comic_term->term_id}]' value='{$order}' min='0' class='small-text' form='webcomic_sorter'>
					<div class='media'>{$media}</div>
					{$comic_term->name}
			</label>";

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
	 * @param WP_Term $comic_term The current term.
	 * @param int     $depth The current depth.
	 * @param array   $args The walker arguments.
	 * @return void
	 */
	public function end_el( &$output, $comic_term, $depth = 0, $args = [] ) {
		$output .= '</li>';
	}
}

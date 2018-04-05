<?php
/**
 * Mgsisk\Webcomic\Taxonomy\Walker\TermLister class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Taxonomy\Walker;

use Walker;
use WP_Term;

/**
 * Standard walker for get_webcomic_terms_list().
 */
class TermLister extends Walker {
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
	 * @param WP_Term $comic_term The current term.
	 * @param int     $depth The current depth.
	 * @param array   $args The walker arguments.
	 * @param int     $id The current term ID.
	 * @return void
	 */
	public function start_el( &$output, $comic_term, $depth = 0, $args = [], $id = 0 ) {
		$item = get_webcomic_term_link( $this->get_padded_link_text( $depth, $args ), $comic_term, $args['link_args'], $args['link_post'], $args['link_post_args'] );

		if ( $args['current'] && ! in_array( $comic_term->term_id, (array) $args['current'], true ) ) {
			$item = str_replace( " current-webcomic-term current-{$comic_term->taxonomy}", '', $item );
		} elseif ( in_array( $comic_term->term_id, (array) $args['current'], true ) && false === strpos( $item, 'current-webcomic-term' ) ) {
			$item = str_replace( "-{$comic_term->taxonomy}-link", "-{$comic_term->taxonomy}-link current-webcomic-term current-{$comic_term->taxonomy}", $item );
		}

		if ( $args['options'] ) {
			$item = str_replace( [ '<a ', 'href=', " current-webcomic-term current-{$comic_term->taxonomy}'>", '</a>' ], [ "<option value='{$comic_term->term_id}' ", 'data-webcomic-url=', " current-webcomic-term current-{$comic_term->taxonomy}' selected>", '</option>' ], $item );
		} elseif ( isset( $args['cloud_count'][ $comic_term->term_id ] ) ) {
			$item = $this->get_cloud_item( $item, $comic_term, $args );
		}

		if ( ! $args['options'] && $args['feed'] ) {
			$feed_link = get_term_feed_link( $comic_term->term_id, $comic_term->taxonomy, $args['feed_type'] );
			$item     .= " <a href='{$feed_link}' class='webcomic-term-feed'>{$args['feed']}</a>";
		}

		if ( $args['webcomics'] ) {
			$item = $this->get_webcomics_list( $item, $comic_term, $args, $depth );
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
	 * @param WP_Term $comic_term The current term.
	 * @param int     $depth The current depth.
	 * @param array   $args The walker arguments.
	 * @return void
	 */
	public function end_el( &$output, $comic_term, $depth = 0, $args = [] ) {
		if ( $args['options'] ) {
			return;
		}

		$output .= $args['end_el'];
	}

	/**
	 * Get padded link text.
	 *
	 * @param int   $depth The current depth.
	 * @param array $args The walker arguments.
	 * @return string
	 */
	protected function get_padded_link_text( int $depth, array $args ) : string {
		if ( ! $args['options'] ) {
			return $args['link'];
		}

		return str_repeat( '&nbsp;', $depth * 4 ) . $args['link'];
	}

	/**
	 * Get a cloud-styled list item.
	 *
	 * @param string  $item The current term list item.
	 * @param WP_Term $comic_term The current term.
	 * @param array   $args The list arguments.
	 * @return string
	 */
	protected function get_cloud_item( string $item, WP_Term $comic_term, array $args ) : string {
		$font_size = $args['cloud_min'] + ( $args['cloud_step'] * ( $args['cloud_count'][ $comic_term->term_id ] - $args['cloud_floor'] ) );
		$item      = str_replace( '<a ', "<a style='font-size:{$font_size}%' ", $item );

		if ( preg_match( '/width="(\d+)" height="(\d+)"/', $item, $match ) ) {
			$match[1] *= ( $font_size / 100 );
			$match[2] *= ( $font_size / 100 );
			$item      = preg_replace( "/{$match[0]}/", "width='{$match[1]}' height='{$match[2]}'", $item );
		}

		return $item;
	}

	/**
	 * Handle webcomics arguments for term lists.
	 *
	 * @param string  $item The current term list item.
	 * @param WP_Term $comic_term The current term.
	 * @param array   $args The list arguments.
	 * @param int     $depth The current list depth.
	 * @return string
	 * @suppress PhanTypeComparisonToArray - $args['webcomics']['post_type'] incorrectly triggers this.
	 * @suppress PhanTypeInvalidDimOffset - $args['webcomics']['post_type'] incorrectly triggers this.
	 * @suppress PhanTypeMismatchArgument - The first argument passed to get_term_children() incorrectly triggers this for WP 4.8-
	 */
	protected function get_webcomics_list( string $item, WP_Term $comic_term, array $args, int $depth ) : string {
		if ( -1 < $args['webcomics_depth'] && $depth !== $args['webcomics_depth'] ) {
			return $item;
		} elseif ( -1 === $args['webcomics_depth'] && get_term_children( $comic_term->term_id, $comic_term->taxonomy ) ) {
			return $item;
		}

		$args['webcomics']['tax_query'][] = [
			'taxonomy'         => $comic_term->taxonomy,
			'field'            => 'term_id',
			'terms'            => $comic_term->term_id,
			'include_children' => false,
		];

		if ( 'own' === $args['webcomics']['post_type'] ) {
			$args['webcomics']['post_type'] = substr( $comic_term->taxonomy, 0, strpos( $comic_term->taxonomy, '_' ) );
		} elseif ( 'crossover' === $args['webcomics']['post_type'] ) {
			$args['webcomics']['post_type'] = get_webcomic_collections(
				[
					'hide_empty' => false,
					'id__not_in' => [ substr( $comic_term->taxonomy, 0, strpos( $comic_term->taxonomy, '_' ) ) ],
				]
			);
		}

		if ( false !== strpos( $args['format'], '{{webcomics_optgroup}}' ) ) {
			$args['webcomics']['format'] = '<optgroup label="' . esc_attr( get_webcomic_term_title( $comic_term ) ) . '">{{}}</optgroup>';
			$item                        = '';
		}

		return $item . get_webcomics_list( $args['webcomics'] );
	}
}

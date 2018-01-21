<?php
/**
 * Mgsisk\Webcomic\Collection\Walker\ComicLister class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Walker;

use Walker;
use WP_Post;

/**
 * Standard walker for get_webcomics_list().
 */
class ComicLister extends Walker {
	/**
	 * Walker type.
	 *
	 * @var string
	 */
	public $tree_type = 'webcomic';

	/**
	 * Database fields.
	 *
	 * @var array
	 */
	public $db_fields = [
		'id'     => 'ID',
		'parent' => 'post_parent',
	];

	/**
	 * Start element output.
	 *
	 * @param string  $output The current output.
	 * @param WP_Post $comic The current comic.
	 * @param int     $depth The current depth.
	 * @param array   $args The walker arguments.
	 * @param int     $id The current comic ID.
	 * @return void
	 */
	public function start_el( &$output, $comic, $depth = 0, $args = [], $id = 0 ) {
		$output = get_webcomic_link( $args['link'], $id, $args['link_args'] );

		if ( $args['current'] && $args['current'] !== $id ) {
			$output = str_replace( " current-webcomic current-{$comic->post_type}", '', $output );
		} elseif ( $args['current'] === $id && false === strpos( $output, 'current-webcomic-collection' ) ) {
			$output = str_replace( "-{$comic->post_type}-link", "-{$comic->post_type}-link current-webcomic current-{$comic->post_type}", $output );
		}

		if ( $args['options'] ) {
			$output = str_replace( [ '<a ', 'href=', " current-webcomic current-{$comic->post_type}'>", '</a>' ], [ "<option value='{$id}' ", 'data-webcomic-url=', " current-webcomic current-{$comic->post_type}' selected>", '</option>' ], $output );
		}

		if ( isset( $args['cloud_count'][ $id ] ) ) {
			$font_size = $args['cloud_min'] + ( $args['cloud_step'] * ( $args['cloud_count'][ $id ] - $args['cloud_floor'] ) );
			$output    = str_replace( '<a ', "<a style='font-size:{$font_size}%' ", $output );

			if ( preg_match( '/width="(\d+)" height="(\d+)"/', $output, $match ) ) {
				$match[1] *= ( $font_size / 100 );
				$match[2] *= ( $font_size / 100 );
				$output    = preg_replace( "/{$match[0]}/", "width='{$match[1]}' height='{$match[2]}'", $output );
			}
		}
	}
}

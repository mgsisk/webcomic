<?php
/**
 * Mgsisk\Webcomic\Commerce\Walker\PrintLister class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Commerce\Walker;

use Walker;

/**
 * Standard walker for get_webcomic_prints_list().
 */
class PrintLister extends Walker {
	/**
	 * Walker type.
	 *
	 * @var string
	 */
	public $tree_type = 'webcomic_print';

	/**
	 * Start element output.
	 *
	 * @param string $output The current output.
	 * @param object $comic_print The current comic print.
	 * @param int    $depth The current depth.
	 * @param array  $args The walker arguments.
	 * @param int    $id The current comic print ID.
	 * @return void
	 */
	public function start_el( &$output, $comic_print, $depth = 0, $args = [], $id = 0 ) {
		$link      = preg_replace( '/%print-(?!currency)(\S+)/', "%{$comic_print->slug}-print-$1", $args['link'] );
		$link_args = [
			'print' => $comic_print->slug,
		] + $args['link_args'];
		$output    = get_webcomic_link( $link, $args['post'], $link_args );

		if ( $args['options'] ) {
			$output = str_replace( [ '<a ', 'href=', '</a>' ], [ "<option value='{$comic_print->slug}' ", 'data-webcomic-url=', '</option>' ], $output );
		} elseif ( isset( $args['cloud_count'][ $comic_print->slug ] ) ) {
			$font_size = $args['cloud_min'] + ( $args['cloud_step'] * ( $args['cloud_count'][ $comic_print->slug ] - $args['cloud_floor'] ) );
			$output    = str_replace( '<a ', "<a style='font-size:{$font_size}%' ", $output );

			if ( preg_match( '/width="(\d+)" height="(\d+)"/', $output, $match ) ) {
				$match[1] *= ( $font_size / 100 );
				$match[2] *= ( $font_size / 100 );
				$output    = preg_replace( "/{$match[0]}/", "width='{$match[1]}' height='{$match[2]}'", $output );
			}
		}
	}
}

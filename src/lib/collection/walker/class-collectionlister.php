<?php
/**
 * Mgsisk\Webcomic\Collection\Walker\CollectionLister class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Walker;

use Walker;
use WP_Post_Type;

/**
 * Standard walker for get_webcomic_collections_list().
 */
class CollectionLister extends Walker {
	/**
	 * Walker type.
	 *
	 * @var string
	 */
	public $tree_type = 'webcomic_collection';

	/**
	 * Start element output.
	 *
	 * @param string       $output The current output.
	 * @param WP_Post_Type $collection The current collection.
	 * @param int          $depth The current depth.
	 * @param array        $args The walker arguments.
	 * @param int          $id The current collection increment.
	 * @return void
	 */
	public function start_el( &$output, $collection, $depth = 0, $args = [], $id = 0 ) {
		$output = get_webcomic_collection_link( $args['link'], $collection->name, $args['link_post'], $args['link_args'] );

		if ( $args['current'] && $args['current'] !== $collection->name ) {
			$output = str_replace( ' current-webcomic-collection', '', $output );
		} elseif ( $args['current'] === $collection->name && false === strpos( $output, 'current-webcomic-collection' ) ) {
			$output = str_replace( "{$collection->name}-collection-link", "{$collection->name}-collection-link current-webcomic-collection", $output );
		}

		if ( $args['options'] ) {
			$output = str_replace( [ '<a ', 'href=', " current-webcomic-collection'>", '</a>' ], [ "<option value='{$collection->name}' ", 'data-webcomic-url=', " current-webcomic-collection' selected>", '</option>' ], $output );
		} elseif ( isset( $args['cloud_count'][ $collection->name ] ) ) {
			$font_size = $args['cloud_min'] + ( $args['cloud_step'] * ( $args['cloud_count'][ $collection->name ] - $args['cloud_floor'] ) );
			$output    = str_replace( '<a ', "<a style='font-size:{$font_size}%' ", $output );

			if ( preg_match( '/width="(\d+)" height="(\d+)"/', $output, $match ) ) {
				$match[1] *= ( $font_size / 100 );
				$match[2] *= ( $font_size / 100 );
				$output    = preg_replace( "/{$match[0]}/", "width='{$match[1]}' height='{$match[2]}'", $output );
			}
		}

		if ( ! $args['options'] && $args['feed'] ) {
			$feed_link = get_post_type_archive_feed_link( $collection->name, $args['feed_type'] );
			$output   .= " <a href='{$feed_link}' class='webcomic-collection-feed'>{$args['feed']}</a>";
		}

		if ( $args['webcomics'] ) {
			$output = $this->get_webcomics_list( $output, $collection, $args );
		}
	}

	/**
	 * Handle webcomics arguments for collection lists.
	 *
	 * @param string       $output The current list output.
	 * @param WP_Post_Type $collection The current collection.
	 * @param array        $args The walker arguments.
	 * @return string
	 */
	protected function get_webcomics_list( string $output, WP_Post_Type $collection, array $args ) : string {
		$list_args              = $args['webcomics'];
		$list_args['post_type'] = $collection->name;

		if ( false !== strpos( $args['format'], '{{webcomics_optgroup}}' ) ) {
			$list_args['format'] = '<optgroup label="' . esc_attr( get_webcomic_collection_title( $collection->name ) ) . '">{{}}</optgroup>';
			$output              = '';
		}

		return $output . get_webcomics_list( $list_args );
	}
}

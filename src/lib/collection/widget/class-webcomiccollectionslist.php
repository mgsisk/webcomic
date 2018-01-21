<?php
/**
 * Mgsisk\Webcomic\Collection\Widget\WebcomicCollectionsList class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Widget;

use WP_Widget;

/**
 * Comic collections list widget implementation.
 *
 * @name Webcomic Collections List
 * @summary Display a list of comic collections.
 * @option Title: Optional widget title.
 * @option Number of collections to show: Optional number of collections to
 * show; 0 shows all collections.
 * @option Related to the current comic: When checked, limits the list to
 * collections related to the current comic (if any).
 * @option Link: Link text; accepts
 * [a variety of tokens](get_webcomic_collection_link_tokens).
 * @option Link to: Where the link goes; one of Archive page, First comic, Last
 * comic, or Random comic.
 * @option Format: List format; one of None, Cloud, Ordered, Plain, Select, or
 * Unordered.
 * @option Comic link: Optional comic link text; accepts
 * [a variety of tokens](get_webcomic_link_tokens). Some formats never list
 * comics.
 */
class WebcomicCollectionsList extends WP_Widget {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		parent::__construct(
			str_replace( '\\', '_', static::class ),
			__( 'Webcomic Collections List', 'webcomic' ),
			[
				'description' => __( 'Display a list of comic collections.', 'webcomic' ),
			]
		);
	}

	/**
	 * Display the widget.
	 *
	 * @param array $args The widget arguments.
	 * @param array $instance The widget instance configuration.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$instance += $this->get_instance_defaults();
		$list_args = [
			'format'    => '<span>{{</span><span>}}</span>',
			'limit'     => $instance['limit'],
			'related'   => $instance['related'],
			'link_args' => [],
			'link'      => $instance['link'],
			'orderby'   => 'name',
			'webcomics' => [],
		];

		if ( $instance['related'] ) {
			$comic = get_webcomic();

			if ( ! $comic ) {
				return;
			}

			$list_args['related_to'] = $comic;
		}

		if ( $instance['link_relation'] ) {
			$list_args['link_args']['relation'] = $instance['link_relation'];
		}

		$list_args = $this->get_format_args( $instance, $list_args );

		if ( $instance['webcomics'] && ! in_array( $instance['format'], [ '', 'cloud' ], true ) ) {
			$list_args = $this->get_webcomics_args( $instance, $list_args );
		}

		$output = get_webcomic_collections_list( $list_args );

		if ( ! $output ) {
			return;
		} elseif ( $instance['title'] ) {
			$instance['title'] = $args['before_title'] . $instance['title'] . $args['after_title'];
		}

		echo $args['before_widget'] . $instance['title'] . $output . $args['after_widget']; // WPCS: xss ok.
	}

	/**
	 * Update an instance of the widget.
	 *
	 * @param array $new The new widget instance configuration.
	 * @param array $old The old widget instance configuration.
	 * @return array
	 */
	public function update( $new, $old ) {
		if ( ! in_array( $new['format'], [ '', 'cloud', 'ordered', 'plain', 'unordered', 'select' ], true ) ) {
			$new['format'] = '';
		}

		if ( ! in_array( $new['link_relation'], [ '', 'first', 'last', 'random' ], true ) ) {
			$new['link_relation'] = '';
		}

		return [
			'title'         => sanitize_text_field( $new['title'] ),
			'limit'         => (int) $new['limit'],
			'related'       => (bool) $new['related'],
			'link'          => wp_kses_post( $new['link'] ),
			'link_relation' => $new['link_relation'],
			'format'        => $new['format'],
			'webcomics'     => wp_kses_post( $new['webcomics'] ),
		];
	}

	/**
	 * Display widget settings form.
	 *
	 * @param array $instance The widget instance configuration.
	 * @return string
	 */
	public function form( $instance ) {
		$instance += $this->get_instance_defaults();

		require __DIR__ . '/class-webcomiccollectionslist-inc-form.php';

		return __CLASS__;
	}

	/**
	 * Get the default widget settings.
	 *
	 * @return array
	 */
	protected function get_instance_defaults() : array {
		return [
			'title'         => '',
			'limit'         => 0,
			'related'       => false,
			'link'          => '%title',
			'link_relation' => '',
			'format'        => '',
			'webcomics'     => '',
		];
	}

	/**
	 * Get list format arguments.
	 *
	 * @param array $instance The widget instance configuration.
	 * @param array $list_args The get_webcomic_collections_list() arguments.
	 * @return array
	 */
	protected function get_format_args( array $instance, array $list_args ) : array {
		if ( 'cloud' === $instance['format'] ) {
			$list_args['format']    = '<span>{{</span> <span>}}</span>';
			$list_args['cloud_min'] = 80;
			$list_args['cloud_max'] = 160;
			$list_args['orderby']   = 'rand';
		} elseif ( 'ordered' === $instance['format'] ) {
			$list_args['format'] = '<ol><li>{{</li><li>}}</li></ol>';
			$list_args['order']  = 'asc';
		} elseif ( 'plain' === $instance['format'] ) {
			$list_args['format'] = '<div>{{</div><div>}}</div>';
		} elseif ( 'select' === $instance['format'] ) {
			$list_args['format'] = '<select name="webcomic_collection"><option value="">' . esc_html__( 'Select a Collection', 'webcomic' ) . '</option>{{}}</select>';
		} elseif ( 'unordered' === $instance['format'] ) {
			$list_args['format'] = '<ul><li>{{</li><li>}}</li></ul>';
		}

		return $list_args;
	}

	/**
	 * Get webcomics list arguments.
	 *
	 * @param array $instance The widget instance configuration.
	 * @param array $list_args The get_webcomic_collections_list() arguments.
	 * @return array
	 */
	protected function get_webcomics_args( array $instance, array $list_args ) : array {
		$list_args['webcomics']['link']   = $instance['webcomics'];
		$list_args['webcomics']['order']  = 'desc';
		$list_args['webcomics']['format'] = '<div>{{</div><div>}}</div>';

		if ( 'ordered' === $instance['format'] ) {
			$list_args['webcomics']['format'] = '<ol><li>{{</li><li>}}</li></ol>';
			$list_args['webcomics']['order']  = 'asc';
		} elseif ( 'select' === $instance['format'] ) {
			$list_args['format'] = preg_replace( '/<option value="">.+?<\/option>/', '<option value="">' . esc_html__( 'Select a Comic', 'webcomic' ) . '</option>', $list_args['format'] );
			$list_args['format'] = str_replace( [ "name='webcomic_collection'", '{{}}' ], [ "name='webcomic'", '{{webcomics_optgroup}}' ], $list_args['format'] );
		} elseif ( 'unordered' === $instance['format'] ) {
			$list_args['webcomics']['format'] = '<ul><li>{{</li><li>}}</li></ul>';
		}

		return $list_args;
	}
}

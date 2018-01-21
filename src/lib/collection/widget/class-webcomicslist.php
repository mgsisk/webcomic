<?php
/**
 * Mgsisk\Webcomic\Collection\Widget\WebcomicsList class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Widget;

use WP_Widget;

/**
 * Comics list widget implementation.
 *
 * @name Webcomics List
 * @summary Display a list of comic collections.
 * @option Title: Optional widget title.
 * @option Number of comics to show: Optional number of comics to show; -1 shows
 * all comics.
 * @option Link: Link text; accepts
 * [a variety of tokens](get_webcomic_link_tokens).
 * @option Format: List format; one of None, Cloud, Ordered, Plain, Select, or
 * Unordered.
 * @option Related to: The collection or comic the comics must be related to.
 * The (current collection) can't always be determined.
 */
class WebcomicsList extends WP_Widget {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		add_filter( 'webcomic_delete_collection', [ $this, 'hook_delete_widget_collection' ] );

		parent::__construct(
			str_replace( '\\', '_', static::class ),
			__( 'Webcomics List', 'webcomic' ),
			[
				'description' => __( 'Display a list of comics.', 'webcomic' ),
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
			'link'           => $instance['link'],
			'format'         => '<span>{{</span><span>}}</span>',
			'post_type'      => $instance['post_type'],
			'posts_per_page' => $instance['posts_per_page'],
		];

		if ( 'webcomic' === $list_args['post_type'] ) {
			$comic = get_webcomic();

			if ( ! $comic ) {
				return;
			}

			$list_args['exclude']    = $comic->ID;
			$list_args['post_type']  = '';
			$list_args['related_to'] = $comic;
		}

		$list_args = $this->get_format_args( $instance, $list_args );
		$output    = get_webcomics_list( $list_args );

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

		if ( $new['post_type'] && ! preg_match( '/^webcomic(\d+)?$/', $new['post_type'] ) ) {
			$new['post_type'] = '';
		}

		return [
			'title'          => sanitize_text_field( $new['title'] ),
			'link'           => wp_kses_post( $new['link'] ),
			'format'         => $new['format'],
			'post_type'      => $new['post_type'],
			'posts_per_page' => (int) $new['posts_per_page'],
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

		require __DIR__ . '/class-webcomicslist-inc-form.php';

		return __CLASS__;
	}

	/**
	 * Delete widget collection setting when a collection is deleted.
	 *
	 * @param string $collection The collection being delete.
	 * @return void
	 */
	public function hook_delete_widget_collection( string $collection ) {
		$widgets = get_option( $this->option_name, [] );

		foreach ( $widgets as $key => $widget ) {
			if ( $collection !== $widget['post_type'] ) {
				continue;
			}

			$widgets[ $key ]['post_type'] = '';
		}

		update_option( $this->option_name, $widgets );
	}

	/**
	 * Get the default widget settings.
	 *
	 * @return array
	 */
	protected function get_instance_defaults() : array {
		return [
			'title'          => '',
			'link'           => '%title',
			'format'         => '',
			'post_type'      => '',
			'posts_per_page' => -1,
		];
	}

	/**
	 * Get list format arguments.
	 *
	 * @param array $instance The widget instance configuration.
	 * @param array $list_args The get_webcomics_list() arguments.
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
			$list_args['format'] = '<select name="webcomic"><option value="">' . esc_html__( 'Select a Comic', 'webcomic' ) . '</option>{{}}</select>';
		} elseif ( 'unordered' === $instance['format'] ) {
			$list_args['format'] = '<ul><li>{{</li><li>}}</li></ul>';
		}

		return $list_args;
	}
}

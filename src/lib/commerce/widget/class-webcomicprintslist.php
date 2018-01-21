<?php
/**
 * Mgsisk\Webcomic\Commerce\Widget\WebcomicPrintsList class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Commerce\Widget;

use WP_Screen;
use WP_Widget;

/**
 * Comic prints list widget implementation.
 *
 * @name Webcomic Prints List
 * @summary Display a list of comic prints.
 * @option Title: Optional widget title.
 * @option Link: Link text; accepts
 * [a variety of tokens](get_webcomic_collection_link_tokens). Will be used as
 * alternate text if an image is selected.
 * @option Format: List format; one of None, Cloud, Ordered, Plain, Select, or
 * Unordered.
 * @option Comic: The comic to list prints for, or the current page's comic (if
 * any).
 */
class WebcomicPrintsList extends WP_Widget {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		add_filter( 'current_screen', [ $this, 'hook_enqueue_comic_search' ] );
		add_filter( 'delete_post', [ $this, 'hook_delete_widget_post' ] );
		add_filter( 'wp_trash_post', [ $this, 'hook_delete_widget_post' ] );

		parent::__construct(
			str_replace( '\\', '_', static::class ),
			__( 'Webcomic Prints List', 'webcomic' ),
			[
				'description' => __( 'Display a list of comic prints.', 'webcomic' ),
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
			'link'   => $instance['link'],
			'format' => '<span>{{</span><span>}}</span>',
			'post'   => $instance['post'],
		];

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
			$list_args['format'] = '<select name="webcomic_prints"><option value="">' . esc_html__( 'Buy a Comic Print', 'webcomic' ) . '</option>{{}}</select>';
		} elseif ( 'unordered' === $instance['format'] ) {
			$list_args['format'] = '<ul><li>{{</li><li>}}</li></ul>';
		}

		$output = get_webcomic_prints_list( $list_args );

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

		return [
			'title'      => sanitize_text_field( $new['title'] ),
			'link'       => wp_kses_post( $new['link'] ),
			'format'     => $new['format'],
			'post'       => (int) $new['post'],
			'post_title' => sanitize_text_field( $new['post_title'] ),
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

		require __DIR__ . '/class-webcomicprintslist-inc-form.php';

		return __CLASS__;
	}

	/**
	 * Enqueue the comic search tool.
	 *
	 * @param WP_Screen $screen The current screen.
	 * @return void
	 */
	public function hook_enqueue_comic_search( WP_Screen $screen ) {
		if ( ! in_array( $screen->id, [ 'customize', 'widgets' ], true ) ) {
			return;
		}

		add_filter( 'webcomic_enqueue_comic_search', '__return_true' );
	}

	/**
	 * Delete widget post setting when a post is trashed or deleted.
	 *
	 * @param int $post The post being delete.
	 * @return void
	 */
	public function hook_delete_widget_post( int $post ) {
		if ( ! is_a_webcomic( $post ) ) {
			return;
		}

		$widgets = get_option( $this->option_name, [] );

		foreach ( $widgets as $key => $widget ) {
			if ( $post !== $widget['post'] ) {
				continue;
			}

			$widgets[ $key ]['post'] = null;
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
			'title'      => '',
			'link'       => '%print-name - %print-price %print-currency',
			'format'     => '',
			'post'       => null,
			'post_title' => '',
		];
	}
}

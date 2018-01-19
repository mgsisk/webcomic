<?php
/**
 * Mgsisk\Webcomic\Collection\Widget\WebcomicMedia class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Widget;

use WP_Screen;
use WP_Widget;

/**
 * Comic media widget implementation.
 */
class WebcomicMedia extends WP_Widget {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		add_filter( 'webcomic_delete_collection', [ $this, 'hook_delete_widget_collection' ] );
		add_filter( 'webcomic_deactivate_character', [ $this, 'hook_deactivate_widget_component' ] );
		add_filter( 'webcomic_deactivate_storyline', [ $this, 'hook_deactivate_widget_component' ] );
		add_filter( 'current_screen', [ $this, 'hook_enqueue_comic_search' ] );
		add_filter( 'wp_trash_post', [ $this, 'hook_delete_widget_post' ] );
		add_filter( 'delte_post', [ $this, 'hook_delete_widget_post' ] );

		parent::__construct(
			str_replace( '\\', '_', static::class ), __( 'Webcomic Media', 'webcomic' ), [
				'description' => __( 'Display comic media.', 'webcomic' ),
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

		$output = get_webcomic_media( $instance['format'], $instance['post'] );

		if ( $instance['link'] ) {
			$link_args = [
				'relation' => str_replace( 'self', '', $instance['link'] ),
			];

			if ( $instance['related_by'] && ( webcomic_collection_exists( $instance['related_by'] ) || webcomic_taxonomy_exists( $instance['related_by'] ) || webcomic_taxonomy_exists( "webcomic1_{$instance['related_by']}" ) ) ) {
				$link_args['related_by'] = $instance['related_by'];
			}

			$output = get_webcomic_link( $output, $instance['post'], $link_args );
		}

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
		if ( ! in_array( $new['link'], [ '', 'self', 'first', 'previous', 'next', 'last', 'random' ], true ) ) {
			$new['link'] = '';
		}

		if ( $new['related_by'] && ! webcomic_collection_exists( $new['related_by'] ) && ! taxonomy_exists( $new['related_by'] ) && ! taxonomy_exists( "webcomic1_{$new['related_by']}" ) ) {
			$new['related_by'] = '';
		}

		return [
			'title'      => sanitize_text_field( $new['title'] ),
			'format'     => wp_kses_post( $new['format'] ),
			'post'       => (int) $new['post'],
			'link'       => $new['link'],
			'related_by' => $new['related_by'],
		];
	}

	/**
	 * Display widget settings form.
	 *
	 * @param array $instance The widget instance configuration.
	 * @return string
	 */
	public function form( $instance ) {
		$instance               += $this->get_instance_defaults();
		$general_taxonomies      = [];
		$instance['collections'] = webcomic( 'option.collections' );
		$instance['taxonomies']  = get_taxonomies(
			[
				'public'  => true,
				'show_ui' => true,
			], 'objects'
		);

		foreach ( $instance['taxonomies'] as $key => $taxonomy ) {
			if ( preg_match( '/webcomic\d+_(.+)/', $taxonomy->name, $match ) ) {
				if ( isset( $general_taxonomies[ $match[1] ] ) ) {
					continue;
				}

				$general_taxonomies[ $match[1] ] = (object) [
					'name'   => $match[1],
					'labels' => (object) [
						'name' => $taxonomy->labels->menu_name,
					],
				];

				continue;
			}

			unset( $instance['taxonomies'][ $key ] );
		}

		$instance['taxonomies'] = $general_taxonomies + $instance['taxonomies'];

		require __DIR__ . '/class-webcomicmedia-inc-form.php';

		return __CLASS__;
	}

	/**
	 * Delete widget settings when a collection is deleted.
	 *
	 * @param string $collection The collection being delete.
	 * @return void
	 */
	public function hook_delete_widget_collection( string $collection ) {
		$widgets = get_option( $this->option_name, [] );

		foreach ( $widgets as $key => $widget ) {
			if ( false === strpos( $widget['related_by'], $collection ) ) {
				continue;
			}

			$widgets[ $key ]['related_by'] = '';
		}

		update_option( $this->option_name, $widgets );
	}

	/**
	 * Delete widget settings when a component is deactivated.
	 *
	 * @return void
	 */
	public function hook_deactivate_widget_component() {
		preg_match( '/^webcomic_deactivate_(.+)$/', current_filter(), $match );

		if ( empty( $match[1] ) ) {
			return;
		}

		$widgets = get_option( $this->option_name, [] );

		foreach ( $widgets as $key => $widget ) {
			if ( false === strpos( $widget['related_by'], $match[1] ) ) {
				continue;
			}

			$widgets[ $key ]['related_by'] = '';
		}

		update_option( $this->option_name, $widgets );
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
			'format'     => '',
			'post'       => null,
			'link'       => '',
			'related_by' => '',
		];
	}
}

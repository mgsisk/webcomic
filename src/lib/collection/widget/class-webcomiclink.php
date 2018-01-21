<?php
/**
 * Mgsisk\Webcomic\Collection\Widget\WebcomicLink class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Widget;

use WP_Screen;
use WP_Widget;

/**
 * Comic link widget implementation.
 *
 * @name Webcomic Link
 * @summary Display a link to a comic.
 * @option Title: Optional widget title.
 * @option Link: Link text; accepts
 * [a variety of tokens](get_webcomic_term_link_tokens). Will be used as
 * alternate text if an image is selected.
 * @option Comic: The comic to link to, or the current page's comic (if any).
 */
class WebcomicLink extends WP_Widget {
	/**
	 * Widget description.
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Media context.
	 *
	 * @var string
	 */
	protected $context;

	/**
	 * Link relation.
	 *
	 * @var string
	 */
	protected $relation;

	/**
	 * Relation label.
	 *
	 * @var string
	 */
	protected $relation_label;

	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		// Translators: Link relation; one of First, Previous, Next, Last, or Random.
		$this->context = ltrim( sprintf( __( '%s Webcomic Link Widget Image', 'webcomic' ), $this->relation_label ) );

		if ( ! $this->description ) {
			$this->description = __( 'Display a link to a comic.', 'webcomic' );
		}

		add_filter( 'webcomic_delete_collection', [ $this, 'hook_delete_widget_collection' ] );
		add_filter( 'webcomic_deactivate_character', [ $this, 'hook_deactivate_widget_component' ] );
		add_filter( 'webcomic_deactivate_storyline', [ $this, 'hook_deactivate_widget_component' ] );
		add_filter( 'current_screen', [ $this, 'hook_enqueue_comic_search' ] );
		add_filter( 'current_screen', [ $this, 'hook_enqueue_media_manager' ] );
		add_filter( 'delete_attachment', [ $this, 'hook_delete_widget_media' ] );
		add_filter( 'delete_post', [ $this, 'hook_delete_widget_post' ] );
		add_filter( 'wp_trash_post', [ $this, 'hook_delete_widget_post' ] );
		add_filter( 'delete_widget', [ $this, 'hook_delete_media_state' ] );
		add_filter( 'display_media_states', [ $this, 'hook_add_media_state' ] );

		parent::__construct(
			str_replace( '\\', '_', static::class ),
			// Translators: Link relation; one of First, Previous, Next, Last, or Random.
			ltrim( sprintf( __( '%s Webcomic Link', 'webcomic' ), $this->relation_label ) ),
			[
				'description' => $this->description,
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
		$link_args = [];

		if ( $this->relation ) {
			$link_args['relation'] = $this->relation;
		}

		if ( $instance['post_type'] ) {
			$link_args['post_type'] = $instance['post_type'];
		}

		if ( $instance['related_by'] && ( webcomic_collection_exists( $instance['related_by'] ) || webcomic_taxonomy_exists( $instance['related_by'] ) || webcomic_taxonomy_exists( "webcomic1_{$instance['related_by']}" ) ) ) {
			$link_args['related_by'] = $instance['related_by'];
		}

		if ( $instance['media'] ) {
			preg_match( '/^(.*){{(.*)}}(.*)$/s', $instance['link'], $match );

			$match           += [ '', '', $instance['link'], '' ];
			$media            = wp_get_attachment_image( $instance['media'], 'full' );
			$match[2]         = preg_replace( '/alt=".+?"/', 'alt="' . esc_attr( $match[2] ) . '"', $media );
			$instance['link'] = "$match[1]{{{$match[2]}}}{$match[3]}";
		}

		$output = get_webcomic_link( $instance['link'], $instance['post'], $link_args );

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
		if ( $new['post_type'] && ! webcomic_collection_exists( $new['post_type'] ) ) {
			$new['post_type'] = '';
		}

		if ( $new['related_by'] && ! webcomic_collection_exists( $new['related_by'] ) && ! taxonomy_exists( $new['related_by'] ) && ! taxonomy_exists( "webcomic1_{$new['related_by']}" ) ) {
			$new['related_by'] = '';
		}

		$new['media'] = (int) $new['media'];

		if ( $new['media'] !== $old['media'] ) {
			if ( $new['media'] ) {
				add_post_meta( $new['media'], str_replace( '\\', '_', static::class ), $this->number );
			}

			if ( $old['media'] ) {
				delete_post_meta( $old['media'], str_replace( '\\', '_', static::class ), $this->number );
			}
		}

		return [
			'title'      => sanitize_text_field( $new['title'] ),
			'media'      => (int) $new['media'],
			'link'       => wp_kses_post( $new['link'] ),
			'post'       => (int) $new['post'],
			'post_title' => sanitize_text_field( $new['post_title'] ),
			'post_type'  => $new['post_type'],
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

		require __DIR__ . '/class-webcomiclink-inc-form.php';

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
			if ( $collection === $widget['post_type'] ) {
				$widgets[ $key ]['post_type'] = '';
			}

			if ( false !== strpos( $widget['related_by'], $collection ) ) {
				$widgets[ $key ]['related_by'] = '';
			}
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
	 * Enqueue the media manager.
	 *
	 * @param WP_Screen $screen The current screen.
	 * @return void
	 */
	public function hook_enqueue_media_manager( WP_Screen $screen ) {
		if ( ! in_array( $screen->id, [ 'customize', 'widgets' ], true ) ) {
			return;
		}

		add_filter( 'webcomic_enqueue_media_manager', '__return_true' );
	}

	/**
	 * Delete widget media setting when an attachment is deleted.
	 *
	 * @param int $post The attachment being delete.
	 * @return void
	 */
	public function hook_delete_widget_media( int $post ) {
		$widgets = get_option( $this->option_name, [] );

		foreach ( $widgets as $key => $widget ) {
			if ( $post !== $widget['media'] ) {
				continue;
			}

			$widgets[ $key ]['media'] = 0;
		}

		update_option( $this->option_name, $widgets );
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
	 * Delete media state for widget media.
	 *
	 * @param string $id The ID of the widget being deleted.
	 * @return void
	 */
	public function hook_delete_media_state( $id ) {
		$match = [];

		if ( ! preg_match( "/^{$this->id_base}-(\d+)$/", $id, $match ) ) {
			return;
		}

		$options  = get_option( $this->option_name );
		$instance = (int) $match[1];

		if ( ! $options[ $instance ]['media'] ) {
			return;
		}

		delete_post_meta( $options[ $instance ]['media'], str_replace( '\\', '_', static::class ), $this->number );
	}

	/**
	 * Add media state for widget media.
	 *
	 * @param array $states The media states for the current object.
	 * @return array
	 */
	public function hook_add_media_state( array $states ) : array {
		if ( ! get_post_meta( get_the_ID(), str_replace( '\\', '_', static::class ) ) ) {
			return $states;
		}

		$states[] = $this->context;

		return $states;
	}

	/**
	 * Get the default widget settings.
	 *
	 * @return array
	 */
	protected function get_instance_defaults() : array {
		$link = '%title';

		if ( 'first' === $this->relation ) {
			$link = '&laquo;';
		} elseif ( 'previous' === $this->relation ) {
			$link = '&lsaquo;';
		} elseif ( 'random' === $this->relation ) {
			$link = '&infin;';
		} elseif ( 'next' === $this->relation ) {
			$link = '&rsaquo;';
		} elseif ( 'last' === $this->relation ) {
			$link = '&raquo;';
		}

		return [
			'title'      => '',
			'media'      => 0,
			'link'       => $link,
			'post'       => null,
			'post_title' => '',
			'post_type'  => '',
			'related_by' => '',
		];
	}
}

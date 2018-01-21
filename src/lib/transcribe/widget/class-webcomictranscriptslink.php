<?php
/**
 * Mgsisk\Webcomic\Transcribe\Widget\WebcomicTranscriptsLink class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe\Widget;

use WP_Screen;
use WP_Widget;

/**
 * Comic transcript link widget implementation.
 *
 * @name Webcomic Transcripts Link
 * @summary Display a comic transcripts link.
 * @option Title: Optional widget title.
 * @option Link: Link text; accepts
 * [a variety of tokens](get_webcomic_link_tokens). Will be used as alternate
 * text if an image is selected.
 * @option Link to the transcript form: Link to the transcript form instead of
 * the transcripts list.
 * @option Comic: The comic to link to, or the current page's comic (if any).
 */
class WebcomicTranscriptsLink extends WP_Widget {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		add_filter( 'current_screen', [ $this, 'hook_enqueue_comic_search' ] );
		add_filter( 'current_screen', [ $this, 'hook_enqueue_media_manager' ] );
		add_filter( 'delete_attachment', [ $this, 'hook_delete_widget_media' ] );
		add_filter( 'delete_post', [ $this, 'hook_delete_widget_post' ] );
		add_filter( 'wp_trash_post', [ $this, 'hook_delete_widget_post' ] );
		add_filter( 'delete_widget', [ $this, 'hook_delete_media_state' ] );
		add_filter( 'display_media_states', [ $this, 'hook_add_media_state' ] );

		parent::__construct(
			str_replace( '\\', '_', static::class ),
			__( 'Webcomic Transcripts Link', 'webcomic' ),
			[
				'description' => __( 'Display a comic transcripts link.', 'webcomic' ),
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
		$key       = 'transcripts';

		if ( $instance['transcribe'] ) {
			$key = 'transcribe';
		}

		if ( $instance['media'] ) {
			preg_match( '/^(.*){{(.*)}}(.*)$/s', $instance['link'], $match );

			$match           += [ '', '', $instance['link'], '' ];
			$media            = wp_get_attachment_image( $instance['media'], 'full' );
			$match[2]         = preg_replace( '/alt=".+?"/', 'alt="' . esc_attr( $match[2] ) . '"', $media );
			$instance['link'] = "$match[1]{{{$match[2]}}}{$match[3]}";
		}

		$output = get_webcomic_link(
			$instance['link'], $instance['post'], [
				$key => 'true',
			]
		);

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
			'media'      => $new['media'],
			'link'       => wp_kses_post( $new['link'] ),
			'transcribe' => (bool) $new['transcribe'],
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

		require __DIR__ . '/class-webcomictranscriptslink-inc-form.php';

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

		$states[] = __( 'Webcomic Transcripts Link Widget Image', 'webcomic' );

		return $states;
	}

	/**
	 * Get the default widget settings.
	 *
	 * @return array
	 */
	protected function get_instance_defaults() : array {
		return [
			'title'      => '',
			'media'      => 0,
			'link'       => '%title',
			'transcribe' => false,
			'post'       => null,
			'post_title' => '',
		];
	}
}

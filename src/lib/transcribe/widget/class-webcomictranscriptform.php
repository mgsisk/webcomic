<?php
/**
 * Mgsisk\Webcomic\Transcribe\Widget\WebcomicTranscriptForm class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe\Widget;

use WP_Screen;
use WP_Widget;

/**
 * Comic transcript form widget implementation.
 *
 * @name Webcomic Transcript Form
 * @summary Display a comic transcript form.
 * @option Title: Optional widget title.
 * @option Notes: Optional text to display just below the transcript field.
 * @option Show the author URL field: When checked, shows the optional author
 * URL field.
 * @option Show the transcript language field: When checked, shows the optional
 * language selection field.
 * @option Comic: The comic the transcript form is for, or the current page's
 * comic (if any).
 */
class WebcomicTranscriptForm extends WP_Widget {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		add_filter( 'current_screen', [ $this, 'hook_enqueue_comic_search' ] );
		add_filter( 'delete_post', [ $this, 'hook_delete_widget_post' ] );
		add_filter( 'wp_trash_post', [ $this, 'hook_delete_widget_post' ] );

		parent::__construct(
			str_replace( '\\', '_', static::class ),
			__( 'Webcomic Transcript Form', 'webcomic' ),
			[
				'description' => __( 'Display a comic transcript form.', 'webcomic' ),
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
		$form_args = [
			'format'                 => '<div id="webcomic-transcribe" class="webcomic-transcribe">{{%form}}</div>',
			'post_parent'            => $instance['post_parent'],
			'transcript_notes_after' => $instance['transcript_notes_after'],
		];

		if ( ! $instance['languages_field'] ) {
			$form_args['fields']['languages'] = '';
		}

		if ( ! $instance['url_field'] ) {
			$form_args['fields']['url'] = '';
		}

		$output = get_webcomic_transcript_form( $form_args );

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
		return [
			'title'                  => sanitize_text_field( $new['title'] ),
			'transcript_notes_after' => wp_kses_post( $new['transcript_notes_after'] ),
			'url_field'              => (bool) $new['url_field'],
			'languages_field'        => (bool) $new['languages_field'],
			'post_parent'            => (int) $new['post_parent'],
			'post_title'             => sanitize_text_field( $new['post_title'] ),
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

		require __DIR__ . '/class-webcomictranscriptform-inc-form.php';

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
			if ( $post !== $widget['post_parent'] ) {
				continue;
			}

			$widgets[ $key ]['post_parent'] = null;
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
			'title'                  => '',
			'transcript_notes_after' => '',
			'url_field'              => true,
			'languages_field'        => true,
			'post_parent'            => 0,
			'post_title'             => '',
		];
	}
}

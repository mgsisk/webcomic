<?php
/**
 * Mgsisk\Webcomic\Transcribe\Widget\WebcomicTranscriptsList class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe\Widget;

use WP_Screen;
use WP_Widget;

/**
 * Comic transcripts list widget implementation.
 *
 * @name Webcomic Transcripts List
 * @summary Display a list of comic transcripts.
 * @option Title: Optional widget title.
 * @option Number of transcripts to show: Optional number of transcripts to
 * show; -1 shows all transcripts.
 * @option Include pending transcripts: When checked, includes pending
 * transcripts in the list. Pending transcripts may be edited and resubmited,
 * depending on your collection settings.
 * @option Include language selector: When checked, includes a select element to
 * filter the list of transcripts by language.
 * @option Item: Transcript item format; accepts
 * [a variety of tokens](get_webcomic_transcripts_list_item_tokens).
 * @option Format: List format; one of None, Ordered, Plain, or Unordered.
 * @option Comic: The comic to list transcripts for, or the current page's comic
 * (if any).
 */
class WebcomicTranscriptsList extends WP_Widget {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		add_filter( 'current_screen', [ $this, 'hook_enqueue_comic_search' ] );
		add_filter( 'delete_post', [ $this, 'hook_delete_widget_post' ] );
		add_filter( 'wp_trash_post', [ $this, 'hook_delete_widget_post' ] );

		parent::__construct(
			str_replace( '\\', '_', static::class ),
			__( 'Webcomic Transcripts List', 'webcomic' ),
			[
				'description' => __( 'Display a list of comic transcripts.', 'webcomic' ),
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
			'format'         => '',
			'item'           => $instance['item'],
			'post_parent'    => get_webcomic(),
			'posts_per_page' => $instance['posts_per_page'],
		];

		if ( $instance['pending'] ) {
			$list_args['post_status'] = [ 'publish', 'pending' ];
		}

		if ( $instance['post_parent'] ) {
			$list_args['post_parent'] = $instance['post_parent'];
		} elseif ( $list_args['post_parent'] ) {
			$list_args['post_parent'] = $list_args['post_parent']->ID;
		}

		$list_args = $this->get_format_args( $instance, $list_args );
		$output    = get_webcomic_transcripts_list( $list_args );

		if ( ! $output ) {
			return;
		} elseif ( $instance['title'] ) {
			$instance['title'] = $args['before_title'] . $instance['title'] . $args['after_title'];
		}

		if ( $instance['languages'] ) {
			$languages = get_webcomic_transcript_languages_list(
				[
					'format'  => '<p><select class="webcomic-transcripts-toggler"><option value="">' . esc_html__( 'Comic Transcripts', 'webcomic' ) . '</option>{{}}</select></p>',
					'related' => get_the_ID(),
				]
			);
			$output    = '<div class="webcomic-transcripts-toggle">' . $languages . $output . '</div>';
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
		preg_match( '/^(.*){{(.*)}}(.*)$/s', $new['item'], $match );

		$match += [ '', '', $new['item'], '' ];

		if ( ! in_array( $new['format'], [ '', 'ordered', 'plain', 'unordered' ], true ) ) {
			$new['format'] = '';
		}

		return [
			'title'          => sanitize_text_field( $new['title'] ),
			'posts_per_page' => (int) $new['posts_per_page'],
			'pending'        => (bool) $new['pending'],
			'languages'      => (bool) $new['languages'],
			'item'           => wp_kses_post( $match[2] ),
			'format'         => $new['format'],
			'post_parent'    => (int) $new['post_parent'],
			'post_title'     => sanitize_text_field( $new['post_title'] ),
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

		require __DIR__ . '/class-webcomictranscriptslist-inc-form.php';

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
			'title'          => '',
			'posts_per_page' => -1,
			'pending'        => true,
			'languages'      => true,
			// @codingStandardsIgnoreLine WordPress.WP.I18n.UnorderedPlaceholdersText - Tokens are being mistaken for translation placeholders.
			'item'           => __( '%content %parent-link transcribed by %authors in %languages', 'webcomic' ),
			'format'         => '',
			'post_parent'    => 0,
			'post_title'     => '',
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
		if ( 'ordered' === $instance['format'] ) {
			$list_args['format'] = '<ol>{{}}</ol>';
			$list_args['item']   = '<li class="%2$s">{{' . $list_args['item'] . '}}</li>';
			$list_args['order']  = 'asc';
		} elseif ( 'plain' === $instance['format'] ) {
			$list_args['format'] = '<div>{{}}</div>';
			$list_args['item']   = '<div class="%2$s">{{' . $list_args['item'] . '}}</div>';
		} elseif ( 'unordered' === $instance['format'] ) {
			$list_args['format'] = '<ul>{{}}</ul>';
			$list_args['item']   = '<li class="%2$s">{{' . $list_args['item'] . '}}</li>';
		} elseif ( ! $instance['format'] && $instance['languages'] ) {
			$list_args['item'] = '<div class="%2$s">{{' . $list_args['item'] . '}}</div>';
		}

		return $list_args;
	}
}

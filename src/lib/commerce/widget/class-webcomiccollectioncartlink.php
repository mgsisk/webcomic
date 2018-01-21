<?php
/**
 * Mgsisk\Webcomic\Commerce\Widget\WebcomicCollectionCartLink class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Commerce\Widget;

use WP_Screen;
use WP_Widget;

/**
 * Collection cart link widget implementation.
 *
 * @name Webcomic Collection Cart Link
 * @summary Display a link to a comic collection cart.
 * @option Title: Optional widget title.
 * @option Link: Link text; accepts
 * [a variety of tokens](get_webcomic_collection_link_tokens). Will be used as
 * alternate text if an image is selected.
 * @option Collection: The collection to link to. The (current collection) can't
 * always be determined.
 */
class WebcomicCollectionCartLink extends WP_Widget {
	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		add_filter( 'webcomic_delete_collection', [ $this, 'hook_delete_widget_collection' ] );
		add_filter( 'current_screen', [ $this, 'hook_enqueue_media_manager' ] );
		add_filter( 'delete_attachment', [ $this, 'hook_delete_widget_media' ] );
		add_filter( 'delete_widget', [ $this, 'hook_delete_media_state' ] );
		add_filter( 'display_media_states', [ $this, 'hook_add_media_state' ] );

		parent::__construct(
			str_replace( '\\', '_', static::class ),
			__( 'Webcomic Collection Cart Link', 'webcomic' ),
			[
				'description' => __( 'Display a link to a comic collection cart.', 'webcomic' ),
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

		if ( $instance['media'] ) {
			preg_match( '/^(.*){{(.*)}}(.*)$/s', $instance['link'], $match );

			$media            = wp_get_attachment_image( $instance['media'], 'full' );
			$match           += [ '', '', $instance['link'], '' ];
			$match[2]         = preg_replace( '/alt=".+?"/', 'alt="' . esc_attr( $match[2] ) . '"', $media );
			$instance['link'] = "$match[1]{{{$match[2]}}}{$match[3]}";
		}

		$output = get_webcomic_collection_cart_link( $instance['link'], $instance['collection'] );

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
		if ( ! webcomic_collection_exists( $new['collection'] ) ) {
			$new['collection'] = '';
		}

		if ( $new['media'] ) {
			add_post_meta( $new['media'], str_replace( '\\', '_', static::class ), $this->number );
		}

		if ( $old['media'] && $old['media'] !== $new['media'] ) {
			delete_post_meta( $old['media'], str_replace( '\\', '_', static::class ), $this->number );
		}

		return [
			'title'      => sanitize_text_field( $new['title'] ),
			'media'      => (int) $new['media'],
			'link'       => wp_kses_post( $new['link'] ),
			'collection' => $new['collection'],
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

		require __DIR__ . '/class-webcomiccollectioncartlink-inc-form.php';

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
			if ( $collection !== $widget['collection'] ) {
				continue;
			}

			$widgets[ $key ]['collection'] = '';
		}

		update_option( $this->option_name, $widgets );
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

		$states[] = __( 'Webcomic Collection Cart Link Widget Image', 'webcomic' );

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
			'link'       => esc_html__( 'View Cart', 'webcomic' ),
			'collection' => '',
		];
	}
}

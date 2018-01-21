<?php
/**
 * Mgsisk\Webcomic\Taxonomy\Widget\WebcomicTermLink class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Taxonomy\Widget;

use WP_Screen;
use WP_Widget;

/**
 * Comic link widget implementation.
 */
abstract class WebcomicTermLink extends WP_Widget {
	/**
	 * Taxonomy type.
	 *
	 * @var string
	 */
	protected $taxonomy_type;

	/**
	 * Singular taxonomy label.
	 *
	 * @var string
	 */
	protected $taxonomy_label;

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
		// Translators: 1: Link relation; one of First, Previous, Next, Last, or Random. 2: Taxonomy type name.
		$this->context = ltrim( sprintf( __( '%1$s Webcomic %2$s Link Widget Image', 'webcomic' ), $this->relation_label, $this->taxonomy_label ) );

		add_filter( 'webcomic_delete_collection', [ $this, 'hook_delete_widget_collection' ] );
		add_filter( 'current_screen', [ $this, 'hook_enqueue_comic_term_search' ] );
		add_filter( 'current_screen', [ $this, 'hook_enqueue_media_manager' ] );
		add_filter( 'delete_attachment', [ $this, 'hook_delete_widget_media' ] );
		add_filter( 'delete_widget', [ $this, 'hook_delete_media_state' ] );
		add_filter( 'display_media_states', [ $this, 'hook_add_media_state' ] );

		foreach ( webcomic( 'option.collections' ) as $collection ) {
			add_filter( "delete_{$collection}_{$this->taxonomy_type}", [ $this, 'hook_delete_widget_term' ] );
		}

		parent::__construct(
			str_replace( '\\', '_', static::class ),
			// Translators: 1: Link relation; one of First, Previous, Next, Last, or Random. 2: Taxonomy type name.
			ltrim( sprintf( __( '%1$s Webcomic %2$s Link', 'webcomic' ), $this->relation_label, $this->taxonomy_label ) ),
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
		$instance      += $this->get_instance_defaults();
		$link_args      = [
			'type'       => $this->taxonomy_type,
			'collection' => get_webcomic_collection(),
		];
		$link_post_args = [];

		if ( $this->relation ) {
			$link_args['relation'] = $this->relation;
		}

		if ( $instance['collection'] ) {
			$link_args['collection'] = $instance['collection'];
		}

		if ( $instance['link_post_relation'] ) {
			$link_post_args['post_type'] = $instance['collection'];
			$link_post_args['relation']  = $instance['link_post_relation'];
		}

		if ( $instance['media'] ) {
			preg_match( '/^(.*){{(.*)}}(.*)$/s', $instance['link'], $match );

			$match           += [ '', '', $instance['link'], '' ];
			$media            = wp_get_attachment_image( $instance['media'], 'full' );
			$match[2]         = preg_replace( '/alt=".+?"/', 'alt="' . esc_attr( $match[2] ) . '"', $media );
			$instance['link'] = "$match[1]{{{$match[2]}}}{$match[3]}";
		}

		$output = get_webcomic_term_link( $instance['link'], $instance['term'], $link_args, null, $link_post_args );

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
		if ( ! in_array( $new['link_post_relation'], [ '', 'first', 'last', 'random' ], true ) ) {
			$new['link_post_relation'] = '';
		}

		if ( $new['collection'] && ! webcomic_collection_exists( $new['collection'] ) ) {
			$new['collection'] = '';
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
			'title'              => sanitize_text_field( $new['title'] ),
			'media'              => (int) $new['media'],
			'link'               => wp_kses_post( $new['link'] ),
			'link_post_relation' => $new['link_post_relation'],
			'term'               => (int) $new['term'],
			'term_title'         => sanitize_text_field( $new['term_title'] ),
			'collection'         => $new['collection'],
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

		if ( ! $this->relation ) {
			$taxonomy                      = get_taxonomy( "webcomic1_{$this->taxonomy_type}" );
			$instance['taxonomy_type']     = $this->taxonomy_type;
			$instance['term_search_label'] = "{$taxonomy->labels->singular_name}:";
			// Translators: Taxonomy singular name.
			$instance['term_search_info']   = sprintf( __( "The current page's %s, if any, will be used.", 'webcomic' ), $taxonomy->labels->singular_name );
			$instance['term_search_null']   = $taxonomy->labels->not_found;
			$instance['term_search_search'] = $taxonomy->labels->search_items;
			// Translators: Taxonomy singular name.
			$instance['term_search_remove'] = sprintf( __( 'Remove %s', 'webcomic' ), $taxonomy->labels->singular_name );
		}

		require __DIR__ . '/class-webcomictermlink-inc-form.php';

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
	 * Enqueue the comic term search tool.
	 *
	 * @param WP_Screen $screen The current screen.
	 * @return void
	 */
	public function hook_enqueue_comic_term_search( WP_Screen $screen ) {
		if ( ! in_array( $screen->id, [ 'customize', 'widgets' ], true ) ) {
			return;
		}

		add_filter( 'webcomic_enqueue_comic_term_search', '__return_true' );
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
	 * Delete widget term setting when a term is deleted.
	 *
	 * @param int $term The term being delete.
	 * @return void
	 */
	public function hook_delete_widget_term( int $term ) {
		$widgets = get_option( $this->option_name, [] );

		foreach ( $widgets as $key => $widget ) {
			if ( $term !== $widget['term'] ) {
				continue;
			}

			$widgets[ $key ]['term'] = null;
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
			$link = '&laquo; %title';
		} elseif ( 'previous' === $this->relation ) {
			$link = '&lsaquo; %title';
		} elseif ( 'next' === $this->relation ) {
			$link = '%title &rsaquo;';
		} elseif ( 'last' === $this->relation ) {
			$link = '%title &raquo;';
		}

		return [
			'title'              => '',
			'media'              => 0,
			'link'               => $link,
			'link_post_relation' => '',
			'term'               => null,
			'collection'         => '',
			'term_title'         => '',
		];
	}
}

<?php
/**
 * Mgsisk\Webcomic\Taxonomy\Widget\WebcomicTermsList class
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Taxonomy\Widget;

use WP_Widget;

/**
 * Comic terms list widget implementation.
 */
abstract class WebcomicTermsList extends WP_Widget {
	/**
	 * Taxonomy type.
	 *
	 * @var string
	 */
	protected $taxonomy_type;

	/**
	 * Taxonomy name.
	 *
	 * @var string
	 */
	protected $taxonomy_label;

	/**
	 * Taxonomy singular name.
	 *
	 * @var string
	 */
	protected $taxonomy_singular_label;

	/**
	 * Instantiate the class.
	 */
	public function __construct() {
		parent::__construct(
			str_replace( '\\', '_', static::class ),
			// Translators: Taxonomy type singular name.
			sprintf( __( 'Webcomic %s List', 'webcomic' ), $this->taxonomy_label ),
			[
				// Translators: Taxonomy type name.
				'description' => sprintf( __( 'Display a list of comic %s.', 'webcomic' ), $this->taxonomy_label ),
			]
		);

		add_filter( 'webcomic_delete_collection', [ $this, 'hook_delete_widget_collection' ] );
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
			'type'            => $this->taxonomy_type,
			'collection'      => '',
			'link'            => $instance['link'],
			'number'          => $instance['number'],
			'format'          => '<span>{{</span><span>}}</span>',
			'webcomics'       => [],
			'webcomics_depth' => -1,
		];

		if ( 'webcomic' === $instance['collection'] ) {
			$comic = get_webcomic();

			if ( ! $comic ) {
				return;
			}

			$list_args['collection'] = '';
			$list_args['object_ids'] = $comic->ID;
		} elseif ( $instance['collection'] ) {
			$list_args['collection'] = $instance['collection'];
		}

		if ( $instance['link_post_relation'] ) {
			$list_args['link_post_args']['post_type'] = $list_args['collection'];
			$list_args['link_post_args']['relation']  = $instance['link_post_relation'];
		}

		$list_args = $this->get_format_args( $instance, $list_args );

		if ( $instance['webcomics'] && ! in_array( $instance['format'], [ '', 'cloud' ], true ) ) {
			$list_args = $this->get_webcomics_args( $instance, $list_args );
		}

		$output = get_webcomic_terms_list( $list_args );

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

		if ( ! in_array( $new['link_post_relation'], [ '', 'first', 'last', 'random' ], true ) ) {
			$new['link_post_relation'] = '';
		}

		if ( $new['collection'] && ! preg_match( '/^webcomic(\d+)?$/', $new['collection'] ) ) {
			$new['collection'] = '';
		}

		return [
			'title'              => sanitize_text_field( $new['title'] ),
			'number'             => (int) $new['number'],
			'link'               => wp_kses_post( $new['link'] ),
			'link_post_relation' => $new['link_post_relation'],
			'format'             => $new['format'],
			'collection'         => $new['collection'],
			'webcomics'          => wp_kses_post( $new['webcomics'] ),
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

		require __DIR__ . '/class-webcomictermslist-inc-form.php';

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
	 * Get the default widget settings.
	 *
	 * @return array
	 */
	protected function get_instance_defaults() : array {
		return [
			'title'              => '',
			'number'             => 0,
			'link'               => '%title',
			'link_post_relation' => '',
			'format'             => '',
			'collection'         => '',
			'webcomics'          => '',
		];
	}

	/**
	 * Get list format arguments.
	 *
	 * @param array $instance The widget instance configuration.
	 * @param array $list_args The webcomic_terms_list() arguments.
	 * @return array
	 */
	protected function get_format_args( array $instance, array $list_args ) : array {
		if ( ! $instance['format'] ) {
			$list_args['hierarchical'] = false;
		} elseif ( 'cloud' === $instance['format'] ) {
			$list_args['format']       = '<span>{{</span> <span>}}</span>';
			$list_args['cloud_min']    = 80;
			$list_args['cloud_max']    = 160;
			$list_args['orderby']      = 'rand';
			$list_args['hierarchical'] = false;
		} elseif ( 'ordered' === $instance['format'] ) {
			$list_args['format']    = '<ol><li>{{</li><li>}}</li></ol>';
			$list_args['order']     = 'asc';
			$list_args['start']     = '<ol>';
			$list_args['start_lvl'] = '<ol>';
			$list_args['start_el']  = '<li>';
			$list_args['end_el']    = '</li>';
			$list_args['end_lvl']   = '</ol>';
			$list_args['end']       = '</ol>';
		} elseif ( 'plain' === $instance['format'] ) {
			$list_args['format']    = '<div>{{</div><div>}}</div>';
			$list_args['start']     = '<div>';
			$list_args['start_lvl'] = '<div>';
			$list_args['start_el']  = '<div>';
			$list_args['end_el']    = '</div>';
			$list_args['end_lvl']   = '</div>';
			$list_args['end']       = '</div>';
		} elseif ( 'select' === $instance['format'] ) {
			// Translators: Taxonomy type singular name.
			$list_args['format'] = '<select name="' . esc_attr( "{$list_args['collection']}_{$list_args['type']}" ) . '"><option value="">' . sprintf( esc_html__( 'Select a %s', 'webcomic' ), $this->taxonomy_singular_label ) . '</option>{{}}</select>';
		} elseif ( 'unordered' === $instance['format'] ) {
			$list_args['format']    = '<ul><li>{{</li><li>}}</li></ul>';
			$list_args['start']     = '<ul>';
			$list_args['start_lvl'] = '<ul>';
			$list_args['start_el']  = '<li>';
			$list_args['end_el']    = '</li>';
			$list_args['end_lvl']   = '</ul>';
			$list_args['end']       = '</ul>';
		}

		return $list_args;
	}

	/**
	 * Get webcomics list arguments.
	 *
	 * @param array $instance The widget instance configuration.
	 * @param array $list_args The webcomic_terms_list() arguments.
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
			$list_args['format'] = preg_replace( '/name="webcomic\d+_.+?"/', "name='webcomic'", $list_args['format'] );
			$list_args['format'] = preg_replace( '/<option value="">.+?<\/option>/', '<option value="">' . esc_html__( 'Select a Comic', 'webcomic' ) . '</option>', $list_args['format'] );
			$list_args['format'] = str_replace( '{{}}', '{{webcomics_optgroup}}', $list_args['format'] );
		} elseif ( 'unordered' === $instance['format'] ) {
			$list_args['webcomics']['format'] = '<ul><li>{{</li><li>}}</li></ul>';
		}

		return $list_args;
	}
}

<?php
/**
 * Deprecated class
 *
 * @package Webcomic
 */

/**
 * Deprecated widget.
 *
 * @deprecated
 */
class Widget_WebcomicCollections extends WP_Widget {
	/**
	 * Deprecated method.
	 */
	public function __construct() {
		parent::__construct(
			'',
			__( '[Webcomic 4] Collections', 'webcomic' ),
			[
				'description' => __( 'This widget is deprecated; use the Webcomic Collections List widget instead.', 'webcomic' ),
			]
		);
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed $args Deprecated parameter.
	 * @param mixed $instance Deprecated parameter.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$instance += $this->get_instance_defaults();
		$list_args = [
			'target'           => $instance['target'],
			'webcomics'        => $instance['webcomics'],
			'show_image'       => $instance['show_image'],
			'show_count'       => $instance['show_count'],
			'webcomic_image'   => $instance['webcomic_image'],
			'orderby'          => 'name',
			'show_option_none' => __( 'Select Collection', 'webcomic' ),
		];

		$callback = 'WebcomicTag::webcomic_list_collections_';

		if ( 'dropdown' === $instance['format'] ) {
			$callback = 'WebcomicTag::webcomic_dropdown_collections_';
		} elseif ( 'cloud' === $instance['format'] ) {
			$callback = 'WebcomicTag::webcomic_collection_cloud_';
		}

		$output = $callback( $list_args );

		if ( ! $output ) {
			return;
		} elseif ( $instance['title'] ) {
			$instance['title'] = $args['before_title'] . $instance['title'] . $args['after_title'];
		}

		echo $args['before_widget'] . $instance['title'] . $output . $args['after_widget']; // WPCS: xss ok.
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed $instance Deprecated parameter.
	 * @return string
	 */
	public function form( $instance ) {
		$instance               += $this->get_instance_defaults();
		$instance['use_instead'] = __( 'the Webcomic Collections List widget', 'webcomic' );

		return __CLASS__;
	}

	/**
	 * Get the default widget settings.
	 *
	 * @return array
	 */
	protected function get_instance_defaults() : array {
		return [
			'title'          => '',
			'format'         => 'list',
			'target'         => 'self',
			'webcomics'      => false,
			'show_image'     => '',
			'show_count'     => false,
			'webcomic_image' => '',
		];
	}
}

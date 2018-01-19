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
class Widget_RecentWebcomics extends WP_Widget {
	/**
	 * Deprecated method.
	 */
	public function __construct() {
		parent::__construct(
			'',
			__( '[Webcomic 4] Recent', 'webcomic' ),
			[
				'description' => __( 'This widget is deprecated; use the Webcomics List widget instead.', 'webcomic' ),
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
		$link      = '%title';

		if ( $instance['image'] ) {
			$link = "%{$instance['image']}";
		}

		if ( ! $instance['collection'] ) {
			$instance['collection'] = get_webcomic_collection();
		} elseif ( -1 === (int) $instance['collection'] ) {
			$instance['collection'] = webcomic( 'option.collections' );
		}

		$output = get_webcomics_list(
			[
				'format'         => '<ul class="recent-webcomics"><li>{{</li><li>}}</li></ul>',
				'link'           => $link,
				'post_type'      => $instance['collection'],
				'posts_per_page' => $instance['numberposts'],
				'order'          => 'desc',
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
	 * Deprecated method.
	 *
	 * @param mixed $instance Deprecated parameter.
	 * @return string
	 */
	public function form( $instance ) {
		$instance               += $this->get_instance_defaults();
		$instance['use_instead'] = __( 'the Webcomics List widget', 'webcomic' );

		require __DIR__ . '/class-widget-inc-form.php';

		return __CLASS__;
	}

	/**
	 * Get the default widget settings.
	 *
	 * @return array
	 */
	protected function get_instance_defaults() : array {
		return [
			'title'       => '',
			'collection'  => '',
			'numberposts' => 5,
			'image'       => 0,
		];
	}
}

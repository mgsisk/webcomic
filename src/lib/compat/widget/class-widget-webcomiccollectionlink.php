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
class Widget_WebcomicCollectionLink extends WP_Widget {
	/**
	 * Deprecated method.
	 */
	public function __construct() {
		parent::__construct(
			'',
			__( '[Webcomic 4] Collection Link', 'webcomic' ),
			[
				'description' => __( 'This widget is deprecated; use the Webcomic Collection Link widget instead.', 'webcomic' ),
			]
		);
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed $args Deprecated parameter.
	 * @param mixed $instance Deprecated parameter.
	 * @return void
	 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
	 */
	public function widget( $args, $instance ) {
		$instance += $this->get_instance_defaults();

		if ( ! $instance['collection'] ) {
			$instance['collection'] = get_webcomic_collection();
		}

		if ( $instance['image'] ) {
			$image            = wp_get_attachment_image( $instance['image'], 'full' );
			$instance['link'] = preg_replace( '/alt=".+?"/', 'alt="' . esc_attr( $instance['link'] ) . '"', $image );
		}

		$output = WebcomicTag::webcomic_collection_link_( '%link', $instance['link'], $instance['collection'] );

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
		$instance['use_instead'] = __( 'the Webcomic Collection Link widget', 'webcomic' );

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
			'title'      => '',
			'collection' => '',
			'link'       => '%title',
		];
	}
}

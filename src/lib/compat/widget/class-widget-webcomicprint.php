<?php
/**
 * Deprecated class
 *
 * @package Webcomic
 */

/**
 * Deprecated class.
 *
 * @deprecated
 */
class Widget_WebcomicPrint extends WP_Widget {
	/**
	 * Deprecated method.
	 */
	public function __construct() {
		parent::__construct(
			'',
			__( '[Webcomic 4] Print', 'webcomic' ),
			[
				'description' => __( 'This widget is deprecated; use the Webcomic Prints List widget instead.', 'webcomic' ),
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

		if ( $instance['image'] ) {
			$image             = wp_get_attachment_image( $instance['image'], 'full' );
			$instance['label'] = preg_replace( '/alt=".+?"/', 'alt="' . esc_attr( $instance['labe'] ) . '"', $image );
		}

		$output = WebcomicTag::webcomic_print_form_( $instance['type'], $instance['label'] );

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
		$instance['use_instead'] = __( 'the Webcomic Prints List widget', 'webcomic' );

		return __CLASS__;
	}

	/**
	 * Get the default widget settings.
	 *
	 * @return array
	 */
	protected function get_instance_defaults() : array {
		return [
			'title' => '',
			'type'  => '',
			'image' => 0,
			'label' => '',
		];
	}
}

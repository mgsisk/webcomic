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
class Widget_WebcomicCharacterLink extends WP_Widget {
	/**
	 * Deprecated method.
	 */
	public function __construct() {
		parent::__construct(
			'',
			__( '[Webcomic 4] Character Link', 'webcomic' ),
			[
				'description' => __( 'This widget is deprecated; use one of the new Webcomic Character Link widgets instead.', 'webcomic' ),
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

		if ( $instance['cache'] ) {
			$instance['relative'] = "{$instance['relative']}-nocache";
		}

		if ( ! $instance['collection'] ) {
			$instance['collection'] = get_webcomic_collection();
		}

		if ( $instance['image'] ) {
			$image            = wp_get_attachment_image( $instance['image'], 'full' );
			$instance['link'] = preg_replace( '/alt=".+?"/', 'alt="' . esc_attr( $instance['link'] ) . '"', $image );
		}

		$output = WebcomicTag::relative_webcomic_term_link_( '%link', $instance['link'], $instance['link'], $instance['target'], $instance['relative'], "{$instance['collection']}_character" );

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
		$instance['use_instead'] = __( 'one of the Webcomic Collection Link widgets', 'webcomic' );

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
			'link'       => '%title',
			'image'      => 0,
			'target'     => '',
			'relative'   => 'random',
			'collection' => '',
		];
	}
}

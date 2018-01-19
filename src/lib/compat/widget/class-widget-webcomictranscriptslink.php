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
class Widget_WebcomicTranscriptsLink extends WP_Widget {
	/**
	 * Deprecated method.
	 */
	public function __construct() {
		parent::__construct(
			'',
			__( '[Webcomic 4] Transcripts Link', 'webcomic' ),
			[
				'description' => __( 'This widget is deprecated; use the Webcomic Transcripts Link widget instead.', 'webcomic' ),
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

		if ( $instance['none_image'] ) {
			$image            = wp_get_attachment_image( $instance['none_image'], 'full' );
			$instance['none'] = preg_replace( '/alt=".+?"/', 'alt="' . esc_attr( $instance['none'] ) . '"', $image );
		}

		if ( $instance['some_image'] ) {
			$image            = wp_get_attachment_image( $instance['some_image'], 'full' );
			$instance['some'] = preg_replace( '/alt=".+?"/', 'alt="' . esc_attr( $instance['some'] ) . '"', $image );
		}

		if ( $instance['off_image'] ) {
			$image           = wp_get_attachment_image( $instance['off_image'], 'full' );
			$instance['off'] = preg_replace( '/alt=".+?"/', 'alt="' . esc_attr( $instance['off'] ) . '"', $image );
		}

		$output = WebcomicTag::webcomic_transcripts_link_( '%link', $instance['none'], $instance['some'], $instance['off'] );

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
			'title'          => '',
			'format'         => 'list',
			'target'         => 'self',
			'collection'     => '',
			'webcomics'      => false,
			'show_image'     => '',
			'show_count'     => false,
			'webcomic_image' => '',
		];
	}
}

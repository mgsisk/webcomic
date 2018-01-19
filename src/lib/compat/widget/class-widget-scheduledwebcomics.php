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
class Widget_ScheduledWebcomics extends WP_Widget {
	/**
	 * Deprecated method.
	 */
	public function __construct() {
		parent::__construct(
			'',
			__( '[Webcomic 4] Scheduled', 'webcomic' ),
			[
				'description' => __( 'This widget is deprecated.', 'webcomic' ),
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
		$link      = '<figure>%title</figure>';

		if ( $instance['image'] ) {
			$link = "<figure>%{$instance['image']}</figure>";
		}

		if ( ! $instance['collection'] ) {
			$instance['collection'] = get_webcomic_collection();
		} elseif ( -1 === (int) $instance['collection'] ) {
			$instance['collection'] = webcomic( 'option.collections' );
		}

		if ( $instance['date'] ) {
			$link = str_replace( '</figure>', '<figcaption>%date</figcaption></figure>', $link );
		}

		$output = get_webcomics_list(
			[
				'format'         => '<ul class="recent-webcomics"><li>{{</li><li>}}</li></ul>',
				'link'           => $link,
				'post_type'      => $instance['collection'],
				'posts_per_page' => $instance['numberposts'],
				'order'          => 'desc',
				'post_status'    => 'future',
			]
		);

		if ( ! $output ) {
			return;
		} elseif ( $instance['title'] ) {
			$instance['title'] = $args['before_title'] . $instance['title'] . $args['after_title'];
		}

		$output = preg_replace( '/<\/?a.*?>/', '', $output );

		echo $args['before_widget'] . $instance['title'] . $output . $args['after_widget']; // WPCS: xss ok.
	}

	/**
	 * Deprecated method.
	 *
	 * @param mixed $instance Deprecated parameter.
	 * @return string
	 */
	public function form( $instance ) {
		$instance += $this->get_instance_defaults();

		foreach ( $instance as $key => $value ) {
			echo '<input type="hidden" name="' . esc_attr( $this->get_field_name( $key ) ) . '" value="' . esc_attr( $value ) . '"';
		}

		echo '<p style="color:#d98500;font-weight:600">';

		esc_html_e( 'This widget is deprecated.', 'webcomic' );

		echo '</p>';

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
			'date'        => false,
		];
	}
}

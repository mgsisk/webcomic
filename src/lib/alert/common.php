<?php
/**
 * Standard alert functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Alert;

use WP_Post;

/**
 * Add common hooks.
 *
 * @return void
 */
function common() {
	add_filter( 'webcomic_alert', __NAMESPACE__ . '\hook_alert_hiatus' );
	add_filter( 'future_to_publish', __NAMESPACE__ . '\hook_alert_buffer' );
}

/**
 * Send a hiatus alert.
 *
 * @return void
 */
function hook_alert_hiatus() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		if ( ! webcomic( "option.{$collection}.alert_hiatus" ) ) {
			return;
		}

		$posts = wp_count_posts( $collection );

		if ( $posts->future ) {
			return;
		}

		$post = current(
			get_posts(
				[
					'post_type'      => $collection,
					'posts_per_page' => 1,
				]
			)
		);

		if ( ! $post ) {
			return;
		}

		$days = (int) floor( ( (int) current_time( 'timestamp' ) - strtotime( $post->post_date ) ) / DAY_IN_SECONDS );

		if ( ! webcomic( "option.{$collection}.alert_hiatus.{$days}" ) ) {
			return;
		}

		$from = 'webcomic@' . preg_replace( '/^www\./', '', strtolower( webcomic( 'GLOBALS._SERVER.SERVER_NAME' ) ) );
		// Translators: The number of days since the last comic was published.
		$since = sprintf( _n( '%s Day', '%s Days', $days, 'webcomic' ), number_format_i18n( $days ) );
		// Translators: 1: The plugin name. 2: Custom post type name. 3: Days since the last post was published, like 'X days'.
		$subject = sprintf( '[%1$s] %2$s Hiatus Alert - %3$s Since Last Comic', 'Webcomic', webcomic( "option.{$collection}.name" ), $since );
		// Translators: 1: Plugin name. 2: Custom post-type name. 3: Cstom post-type posts scheduled.
		$message = sprintf( __( "This is an automated reminder from %1\$s that %2\$s hasn't updated in %3\$s.", 'webcomic' ), 'Webcomic', webcomic( "option.{$post->post_type}.name" ), strtolower( $since ) ) . "\n\n";
		// Translators: Login url.
		$message .= sprintf( __( 'Publish more comics or disable these alerts by logging into your site at %s', 'webcomic' ), wp_login_url() );
		$headers  = [
			'from'         => "Webcomic <{$from}>",
			'content-type' => 'text/plain',
		];

		wp_mail( webcomic( "option.{$collection}.alert_hiatus.{$days}" ), $subject, $message, $headers );
	}//end foreach()
}

/**
 * Send a buffer alert.
 *
 * @param WP_Post $post The post being published.
 * @return void
 */
function hook_alert_buffer( WP_Post $post ) {
	if ( ! webcomic( "option.{$post->post_type}.alert_buffer" ) ) {
		return;
	}

	$posts = wp_count_posts( $post->post_type );

	if ( ! $posts->future || ! webcomic( "option.{$post->post_type}.alert_buffer.{$posts->future}" ) ) {
		return;
	}

	$from = 'webcomic@' . preg_replace( '/^www\./', '', strtolower( webcomic( 'GLOBALS._SERVER.SERVER_NAME' ) ) );
	// Translators: Custom post-type posts scheduled.
	$left = sprintf( _n( '%s Comic', '%s Comics', $posts->future, 'webcomic' ), number_format_i18n( $posts->future ) );
	// Translators: 1: The plugin name. 2: Custom post type name. 3: Posts left, like 'X posts'.
	$subject = sprintf( '[%1$s] %2$s Buffer Alert - %3$s Left', 'Webcomic', webcomic( "option.{$post->post_type}.name" ), $left );
	// Translators: 1: Post title. 2: Post permalink.
	$message = sprintf( __( '"%1$s" was just published: %2$s', 'webcomic' ), $post->post_title, get_permalink( $post->ID ) ) . "\n\n";
	// Translators: 1: Plugin name. 2: Custom post-type name. 3: Cstom post-type posts scheduled.
	$message .= sprintf( __( 'This is an automated reminder from %1$s that %2$s has %3$s left before the buffer runs out.', 'webcomic' ), 'Webcomic', webcomic( "option.{$post->post_type}.name" ), strtolower( $left ) ) . "\n\n";
	// Translators: Login url.
	$message .= sprintf( __( 'Schedule more comics or disable these alerts by logging into your site at %s', 'webcomic' ), wp_login_url() );
	$headers  = [
		'from'         => "Webcomic <{$from}>",
		'content-type' => 'text/plain',
	];

	wp_mail( webcomic( "option.{$post->post_type}.alert_buffer.{$posts->future}" ), $subject, $message, $headers );
}

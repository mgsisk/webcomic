<?php
/**
 * User shortcodes
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Restrict;

/**
 * Add restrict shortcodes.
 *
 * @return void
 */
function shortcodes() {
	add_shortcode( 'webcomic_age', __NAMESPACE__ . '\webcomic_age_shortcode' );
	add_shortcode( 'webcomic_age_required', __NAMESPACE__ . '\webcomic_age_required_shortcode' );
	add_shortcode( 'webcomic_roles_required', __NAMESPACE__ . '\webcomic_roles_required_shortcode' );
}

/**
 * Display the minimum age required to view a comic.
 *
 * @uses get_webcomic_age()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed $post Optional post to get a minimum age for.
 *     @type array $args Optional arguments.
 * }
 * @param string $content Optional shortcode content.
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_age_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'post' => null,
			'args' => [],
		], $atts, $name
	);

	if ( is_string( $args['args'] ) ) {
		parse_str( htmlspecialchars_decode( $args['args'] ), $args['args'] );
	}

	return (string) get_webcomic_age( $args['post'], $args['args'] );
}

/**
 * Display content if a user has verified their age.
 *
 * @uses webcomic_age_required()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed $post Optional post to check.
 *     @type array $args Optional arguments.
 * }
 * @param string $content Content to display if a user has verified their age.
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_age_required_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'post' => null,
			'args' => [],
		], $atts, $name
	);

	if ( is_string( $args['args'] ) ) {
		parse_str( htmlspecialchars_decode( $args['args'] ), $args['args'] );
	}

	if ( webcomic_age_required( $args['post'], $args['args'] ) ) {
		return '';
	}

	return do_shortcode( $content );
}

/**
 * Display content if a user has a required role.
 *
 * @uses webcomic_roles_required()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed $post Optional post to check.
 *     @type array $args Optional arguments.
 * }
 * @param string $content Content to display if a user has a required role.
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_roles_required_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'post' => null,
			'args' => [],
		], $atts, $name
	);

	if ( is_string( $args['args'] ) ) {
		parse_str( htmlspecialchars_decode( $args['args'] ), $args['args'] );
	}

	if ( webcomic_roles_required( $args['post'], $args['args'] ) ) {
		return '';
	}

	return do_shortcode( $content );
}

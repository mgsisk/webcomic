<?php
/**
 * Restrict filters
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Restrict;

use WP_Post;

/**
 * Add filters.
 *
 * @return void
 */
function filters() {
	add_filter( 'get_webcomic_url', __NAMESPACE__ . '\hook_get_webcomic_url', 10, 4 );
	add_filter( 'get_post_metadata', __NAMESPACE__ . '\hook_get_post_meta_restrict_media', 10, 4 );
}

/**
 * Add an age confirmation parameter to a comic URL.
 *
 * @param string  $url The comic URL.
 * @param WP_Post $comic The comic the URL points to.
 * @param array   $args Optional arguments.
 * @param mixed   $post Optional reference post.
 * @return string
 */
function hook_get_webcomic_url( string $url, WP_Post $comic, array $args, $post ) : string {
	if ( empty( $args['confirm_age'] ) ) {
		return $url;
	} elseif ( ! get_option( 'rewrite_rules' ) ) {
		return esc_url(
			add_query_arg(
				[
					'confirm-age' => '',
				], $url
			)
		);
	}

	return esc_url( trailingslashit( $url ) . 'confirm-age' );
}

/**
 * Alter comic media based on user access.
 *
 * @param mixed  $meta The meta value.
 * @param int    $id The post ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return only the first $key value.
 * @return mixed
 */
function hook_get_post_meta_restrict_media( $meta, $id, $key, $single ) {
	if ( 'webcomic_media' !== $key || ! is_a_webcomic( $id ) ) {
		return $meta;
	} elseif ( webcomic_referrers_required( $id ) ) {
		return get_post_meta( $id, 'webcomic_restrict_referrers_media' );
	} elseif ( webcomic_roles_required( $id ) ) {
		return get_post_meta( $id, 'webcomic_restrict_roles_media' );
	} elseif ( webcomic_age_required( $id ) ) {
		return get_post_meta( $id, 'webcomic_restrict_age_media' );
	} elseif ( post_password_required( $id ) && ! ( is_admin() && current_user_can( 'edit_post', $id ) ) ) {
		return get_post_meta( $id, 'webcomic_restrict_password_media' );
	}

	return $meta;
}

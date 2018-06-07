<?php
/**
 * Global restrict functions
 *
 * @package Webcomic
 */

/**
 * Get the minimum age required to view a comic.
 *
 * @param mixed $post Optional post to get a minimum age for.
 * @param array $args Optional arguments.
 * @return int
 */
function get_webcomic_age( $post = null, array $args = [] ) : int {
	$comic = get_webcomic( $post, $args );

	if ( ! $comic ) {
		return 0;
	}

	return (int) get_post_meta( $comic->ID, 'webcomic_restrict_age', true );
}

/**
 * Get the referrers allowed to view a comic.
 *
 * @param mixed $post Optional post to get referrers for.
 * @param array $args Optional arguments.
 * @return array
 */
function get_webcomic_referrers( $post = null, array $args = [] ) : array {
	$comic = get_webcomic( $post, $args );

	if ( ! $comic ) {
		return [];
	}

	return get_post_meta( $comic->ID, 'webcomic_restrict_referrers' );
}

/**
 * Get the roles allowed to view a comic.
 *
 * @param mixed $post Optional post to get required roles for.
 * @param array $args Optional arguments.
 * @return array
 */
function get_webcomic_roles( $post = null, array $args = [] ) : array {
	$comic = get_webcomic( $post, $args );

	if ( ! $comic ) {
		return [];
	}

	return get_post_meta( $comic->ID, 'webcomic_restrict_roles' );
}

/**
 * Does viewing a comic require age confirmation?
 *
 * @param mixed $post Optional post to check.
 * @param array $args Optional arguments.
 * @return bool
 */
function webcomic_age_required( $post = null, array $args = [] ) : bool {
	$comic = get_webcomic( $post, $args );

	if ( ! $comic ) {
		return false;
	}

	$age = get_webcomic_age( $comic );

	if ( ! $age || ( is_admin() && current_user_can( 'edit_post', $comic->ID ) ) ) {
		return false;
	}

	$user_age = (int) webcomic( "GLOBALS._COOKIE.{$comic->post_type}_age_" . COOKIEHASH );

	return $user_age < $age;
}

/**
 * Does viewing a comic require a specific referrer?
 *
 * @param mixed $post Optional post to check.
 * @param array $args Optional arguments.
 * @return bool
 */
function webcomic_referrers_required( $post = null, array $args = [] ) : bool {
	$comic = get_webcomic( $post, $args );

	if ( ! $comic ) {
		return false;
	}

	$referrers = get_webcomic_referrers( $comic );

	if ( ! $referrers || ( is_admin() && current_user_can( 'edit_post', $comic->ID ) ) ) {
		return false;
	}

	$referer = wp_get_raw_referer();

	if ( ! $referer ) {
		return true;
	}

	foreach ( $referrers as $referrer ) {
		if ( $referer === $referrer ) {
			return false;
		} elseif ( false !== strpos( $referrer, '://' ) ) {
			continue;
		}

		$scheme   = wp_parse_url( $referer, PHP_URL_SCHEME );
		$referrer = "{$scheme}://{$referrer}";

		if ( 0 === strpos( $referer, $referrer ) ) {
			return false;
		}
	}

	return true;
}

/**
 * Does viewing a comic require a particular role?
 *
 * @param mixed $post Optional post to check.
 * @param array $args Optional arguments.
 * @return bool
 */
function webcomic_roles_required( $post = null, array $args = [] ) : bool {
	$comic = get_webcomic( $post, $args );

	if ( ! $comic ) {
		return false;
	}

	$roles = get_webcomic_roles( $comic );

	if ( ! $roles || ( is_admin() && current_user_can( 'edit_post', $comic->ID ) ) ) {
		return false;
	} elseif ( '~loggedin~' === $roles[0] && is_user_logged_in() ) {
		return true;
	}

	$user_roles = wp_get_current_user()->roles;

	return ! ! ! array_intersect( $user_roles, $roles );
}

/*
===== Display Functions ======================================================

The following template tags directly display content returned by some of the
template tags found above.
*/

/**
 * Display the minimum age required to view a comic.
 *
 * @uses get_webcomic_age()
 * @param mixed $post Optional post to get a minimum age for.
 * @param array $args Optional arguments.
 * @return void
 */
function webcomic_age( $post = null, array $args = [] ) {
	echo get_webcomic_age( $post, $args ); // WPCS: xss ok.
}

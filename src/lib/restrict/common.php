<?php
/**
 * Standard restrict functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Restrict;

use const Mgsisk\Webcomic\Collection\ENDPOINT;

/**
 * Add common hooks.
 *
 * @return void
 */
function common() {
	add_filter( 'init', __NAMESPACE__ . '\hook_add_rewrite_endpoint' );
	add_filter( 'template_include', __NAMESPACE__ . '\hook_confirm_age' );
	add_filter( 'post_class', __NAMESPACE__ . '\hook_add_post_classes', 10, 3 );
	add_filter( 'the_title', __NAMESPACE__ . '\hook_comic_title', 10, 2 );
	add_filter( 'the_content', __NAMESPACE__ . '\hook_comic_content' );
	add_filter( 'comments_template', __NAMESPACE__ . '\hook_comic_comments' );
}

/**
 * Add the confirm-age rewrite endpoint for comics.
 *
 * @return void
 * @suppress PhanUndeclaredConstant - ENDPOINT incorrectly triggers this.
 */
function hook_add_rewrite_endpoint() {
	add_rewrite_endpoint( 'confirm-age', ENDPOINT );
}

/**
 * Set age confirmation cookie and redirect.
 *
 * @param string $template The requested template.
 * @return string|null
 */
function hook_confirm_age( string $template ) {
	if ( null === get_query_var( 'confirm-age', null ) || ! is_webcomic() ) {
		return $template;
	} elseif ( get_query_var( 'confirm-age' ) ) {
		return locate_template( '404.php' );
	}

	$post_type = get_post_type();
	$age       = get_webcomic_age();
	$time      = (int) current_time( 'timestamp' ) + 604800;
	$user_age  = (int) webcomic( "GLOBALS._COOKIE.{$post_type}_age_" . COOKIEHASH );

	if ( $age && $age > $user_age ) {
		setcookie( "{$post_type}_age_" . COOKIEHASH, (string) $age, $time, COOKIEPATH );
	}

	define( 'DOING_AJAX', true ) && wp_safe_redirect( get_webcomic_url() ) && wp_die();
}

/**
 * Add CSS classes to post elements.
 *
 * @param array $classes The current list of classes.
 * @param array $class A list of additional classes.
 * @param int   $post The current post ID.
 * @return array
 */
function hook_add_post_classes( array $classes, array $class, int $post ) : array {
	if ( ! is_a_webcomic( $post ) ) {
		return $classes;
	} elseif ( get_post_meta( $post, 'webcomic_restrict_age', true ) ) {
		$classes[] = 'webcomic-age-protected';

		if ( webcomic_age_required( $post ) ) {
			$classes[] = 'webcomic-age-required';
		}
	}

	if ( get_post_meta( $post, 'webcomic_restrict_roles' ) ) {
		$classes[] = 'webcomic-roles-protected';

		if ( webcomic_roles_required( $post ) ) {
			$classes[] = 'webcomic-roles-required';
		}
	}

	return $classes;
}

/**
 * Modify comic title based on age and role settings.
 *
 * @param string $title The post content.
 * @param int    $id The post ID.
 * @return string
 */
function hook_comic_title( string $title, int $id ) : string {
	if ( is_a_webcomic( $id ) && ( webcomic_roles_required( $id ) || webcomic_age_required( $id ) ) ) {
		// Translators: Post title.
		return sprintf( __( 'Restricted: %s', 'webcomic' ), $title );
	}

	return $title;
}

/**
 * Restrict comic content based on age and role settings.
 *
 * @param string $content The post content.
 * @return string
 * @suppress PhanPluginAlwaysReturnFunction - Incorrectly triggered.
 */
function hook_comic_content( string $content ) : string {
	$id = get_the_ID();

	if ( ! is_a_webcomic( $id ) ) {
		return $content;
	} elseif ( webcomic_roles_required( $id ) ) {
		/**
		 * Alter the standard role restricted content.
		 *
		 * This filter allows hooks to alter the standard content returned when
		 * a user does not have the required role or roles to view the comic.
		 *
		 * @param string $content The content to return in place of the normal
		 *                        post content.
		 * @param int    $id The post ID.
		 */
		$content = apply_filters(
			'webcomic_roles_required_content',
			'<p>' . esc_html__( "You don't have permission to view this comic.", 'webcomic' ) . '</p>',
			$id
		);

		return (string) $content;
	} elseif ( webcomic_age_required( $id ) ) {
		$link = get_webcomic_link(
			// Translators: Minimum age required to view content.
			sprintf( esc_html__( "confirm that you're at least %s years old or older", 'webcomic' ), get_webcomic_age( $id ) ), $id, [
				'confirm_age' => true,
			]
		);

		/**
		 * Alter the standard age restricted content.
		 *
		 * This filter allows hooks to alter the standard content returned when
		 * a user has not confirmed their age for an age-restricted comic.
		 *
		 * @param string $content The content to return in place of the normal
		 *                        post content.
		 * @param int    $id The post ID.
		 */
		$content = apply_filters(
			'webcomic_age_required_content',
			// Translators: Age confirmation link.
			'<p>' . sprintf( esc_html__( 'You must %s to view this comic.', 'webcomic' ), $link ) . '</p>',
			$id
		);

		return (string) $content;
	}// End if().

	return $content;
}

/**
 * Restrict comic comments based on age and role settings.
 *
 * We have to have an actual comment template to load for restricted comics
 * because comments_template() will load a theme-compat template if the provided
 * template path doesn't exist. The default template loaded here is empty,
 * hiding comments on age and role-restricted comics.
 *
 * @param string $template The comic template to use.
 * @return string
 * @suppress PhanPluginAlwaysReturnFunction - Incorrectly triggered.
 */
function hook_comic_comments( string $template ) : string {
	$id = get_the_ID();

	if ( ! is_a_webcomic( $id ) ) {
		return $template;
	} elseif ( webcomic_roles_required( $id ) ) {
		/**
		 * Alter the role restricted comments template path.
		 *
		 * This filter allows hooks to alter the template used when a user does not
		 * have the required role or roles to view the comic.
		 *
		 * @param string $restricted The restricted comment template.
		 * @param string $template The original comment template.
		 */
		$template = apply_filters( 'webcomic_roles_required_comments', __DIR__ . '/common-inc-comments.php', $template );

		return (string) $template;
	} elseif ( webcomic_age_required( $id ) ) {
		/**
		 * Alter the age restricted comments template path.
		 *
		 * This filter allows hooks to alter the template used when a user has not
		 * confirmed their age for an age-restricted comic.
		 *
		 * @param string $restricted The restricted comment template.
		 * @param string $template The original comment template.
		 */
		$template = apply_filters( 'webcomic_age_required_comments', __DIR__ . '/common-inc-comments.php', $template );

		return (string) $template;
	}

	return $template;
}

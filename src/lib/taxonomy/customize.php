<?php
/**
 * Customize API functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Taxonomy;

use WP_Query;

/**
 * Add customize hooks.
 *
 * @return void
 */
function customize() {
	add_filter( 'loop_start', __NAMESPACE__ . '\hook_integrate_universal_archive_media' );
}

/**
 * Integrate comics with the front page.
 *
 * @param WP_Query $query The query to modify.
 * @return WP_Query
 */
function hook_integrate_universal_archive_media( WP_Query $query ) : WP_Query {
	if ( 'universal' !== get_theme_mod( 'webcomic_integrate' ) || $query->is_admin() || ! $query->is_main_query() || ! $query->is_tax() || ! is_webcomic_tax() || ! get_webcomic_term_media() ) {
		return $query;
	}

	echo '<div class="webcomic-term-media">';

	webcomic_term_media();

	echo '</div>';

	return $query;
}

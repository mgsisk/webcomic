<?php
/**
 * Deprecated shortcodes
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Compat;

use WebcomicTag;
use function Mgsisk\Webcomic\Collection\has_webcomic_media_shortcode;

/**
 * Add deprecated shortcodes.
 *
 * @return void
 */
function shortcodes() {
	add_shortcode( 'the_webcomic_collections', __NAMESPACE__ . '\the_webcomic_collections_shortcode' );
	add_shortcode( 'the_related_webcomics', __NAMESPACE__ . '\the_related_webcomics_shortcode' );
	add_shortcode( 'webcomic_collection_poster', __NAMESPACE__ . '\webcomic_collection_poster_shortcode' );
	add_shortcode( 'webcomic_dropdown_collections', __NAMESPACE__ . '\webcomic_dropdown_collections_shortcode' );
	add_shortcode( 'webcomic_list_collections', __NAMESPACE__ . '\webcomic_list_collections_shortcode' );
	add_shortcode( 'webcomic_collection_cloud', __NAMESPACE__ . '\webcomic_collection_cloud_shortcode' );
	add_shortcode( 'the_webcomic', __NAMESPACE__ . '\the_webcomic_shortcode' );
	add_shortcode( 'webcomic_count', __NAMESPACE__ . '\webcomic_count_shortcode' );
	add_shortcode( 'verify_webcomic_age', __NAMESPACE__ . '\verify_webcomic_age_shortcode' );
	add_shortcode( 'verify_webcomic_role', __NAMESPACE__ . '\verify_webcomic_role_shortcode' );
	add_shortcode( 'the_webcomic_characters', __NAMESPACE__ . '\the_webcomic_terms_shortcode' );
	add_shortcode( 'the_webcomic_storylines', __NAMESPACE__ . '\the_webcomic_terms_shortcode' );
	add_shortcode( 'webcomic_character_crossovers', __NAMESPACE__ . '\webcomic_term_crossovers_shortcode' );
	add_shortcode( 'webcomic_storyline_crossovers', __NAMESPACE__ . '\webcomic_term_crossovers_shortcode' );
	add_shortcode( 'webcomic_character_avatar', __NAMESPACE__ . '\webcomic_term_media_shortcode' );
	add_shortcode( 'webcomic_storyline_cover', __NAMESPACE__ . '\webcomic_term_media_shortcode' );
	add_shortcode( 'webcomic_character_cloud', __NAMESPACE__ . '\webcomic_term_cloud_shortcode' );
	add_shortcode( 'webcomic_storyline_cloud', __NAMESPACE__ . '\webcomic_term_cloud_shortcode' );
	add_shortcode( 'webcomic_dropdown_characters', __NAMESPACE__ . '\webcomic_dropdown_terms_shortcode' );
	add_shortcode( 'webcomic_dropdown_storylines', __NAMESPACE__ . '\webcomic_dropdown_terms_shortcode' );
	add_shortcode( 'webcomic_list_characters', __NAMESPACE__ . '\webcomic_list_terms_shortcode' );
	add_shortcode( 'webcomic_list_storylines', __NAMESPACE__ . '\webcomic_list_terms_shortcode' );
	add_shortcode( 'webcomic_crossover_description', __NAMESPACE__ . '\webcomic_crossover_description_shortcode' );
	add_shortcode( 'webcomic_crossover_poster', __NAMESPACE__ . '\webcomic_crossover_poster_shortcode' );
	add_shortcode( 'webcomic_crossover_title', __NAMESPACE__ . '\webcomic_crossover_title_shortcode' );
	add_shortcode( 'webcomic_collection_crossovers', __NAMESPACE__ . '\webcomic_collection_crossovers_shortcode' );
	add_shortcode( 'purchase_webcomic_link', __NAMESPACE__ . '\purchase_webcomic_link_shortcode' );
	add_shortcode( 'webcomic_collection_print_amount', __NAMESPACE__ . '\webcomic_collection_print_amount_shortcode' );
	add_shortcode( 'webcomic_donation_amount', __NAMESPACE__ . '\webcomic_donation_amount_shortcode' );
	add_shortcode( 'webcomic_donation_form', __NAMESPACE__ . '\webcomic_donation_form_shortcode' );
	add_shortcode( 'webcomic_print_amount', __NAMESPACE__ . '\webcomic_print_amount_shortcode' );
	add_shortcode( 'webcomic_print_adjustment', __NAMESPACE__ . '\webcomic_print_adjustment_shortcode' );
	add_shortcode( 'webcomic_print_form', __NAMESPACE__ . '\webcomic_print_form_shortcode' );
	add_shortcode( 'webcomic_transcripts_link', __NAMESPACE__ . '\webcomic_transcripts_link_shortcode' );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function the_webcomic_collections_shortcode( $atts, string $content, string $name ) : string {
	webcomic_error( __( '[the_webcomic_collections] is deprecated; use [webcomic_collections_list] instead.', 'webcomic' ) );

	$args = shortcode_atts(
		[
			'id'        => 0,
			'before'    => '',
			'sep'       => ', ',
			'after'     => '',
			'target'    => 'self',
			'image'     => '',
			'crossover' => true,
		], $atts, $name
	);

	return WebcomicTag::get_the_webcomic_collection_list_( $args['id'], $args['before'], $args['sep'], $args['after'], $args['target'], $args['image'], $args['crossover'] );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function the_related_webcomics_shortcode( $atts, string $content, string $name ) : string {
	webcomic_error( __( '[the_related_webcomics] is deprecated; use [webcomics_list] instead.', 'webcomic' ) );

	$args = shortcode_atts(
		[
			'before'     => '',
			'sep'        => ', ',
			'after'      => '',
			'image'      => '',
			'limit'      => 5,
			'storylines' => true,
			'characters' => true,
			'the_post'   => false,
		], $atts, $name
	);

	return WebcomicTag::the_related_webcomics_( $args['before'], $args['sep'], $args['after'], $args['image'], $args['limit'], $args['storylines'], $args['characters'], $args['the_post'] );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_collection_poster_shortcode( $atts, string $content, string $name ) : string {
	webcomic_error( __( '[webcomic_collection_poster] is deprecated; use [webcomic_collection_media] instead.', 'webcomic' ) );

	$args = shortcode_atts(
		[
			'size'       => 'full',
			'collection' => '',
		], $atts, $name
	);

	return get_webcomic_collection_media( $args['size'], $args['collection'] );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_dropdown_collections_shortcode( $atts, string $content, string $name ) : string {
	webcomic_error( __( '[webcomic_dropdown_collections] is deprecated; use [webcomic_collections_list] instead.', 'webcomic' ) );

	$args = shortcode_atts(
		[
			'select_name'      => 'webcomic_collections',
			'id'               => '',
			'class'            => '',
			'show_option_all'  => '',
			'show_option_none' => '',
			'hide_empty'       => true,
			'hide_if_empty'    => true,
			'collection'       => '',
			'orderby'          => '',
			'callback'         => '',
			'webcomics'        => false,
			'show_count'       => false,
			'target'           => 'self',
			'selected'         => '',
		], $atts, $name
	);

	return WebcomicTag::webcomic_dropdown_collections_( $args );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_list_collections_shortcode( $atts, string $content, string $name ) : string {
	webcomic_error( __( '[webcomic_list_collections] is deprecated; use [webcomic_collections_list] instead.', 'webcomic' ) );

	$args = shortcode_atts(
		[
			'id'               => '',
			'class'            => '',
			'before'           => '',
			'after'            => '',
			'hide_empty'       => true,
			'ordered'          => '',
			'collection'       => '',
			'order'            => 'asc',
			'orderby'          => '',
			'callback'         => false,
			'feed'             => '',
			'feed_type'        => 'rss2',
			'webcomics'        => false,
			'webcomic_order'   => 'asc',
			'webcomic_orderby' => 'date',
			'webcomic_image'   => '',
			'show_count'       => false,
			'show_description' => false,
			'show_image'       => '',
			'target'           => 'self',
			'selected'         => 0,
		], $atts, $name
	);

	return WebcomicTag::webcomic_list_collections_( $args );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_collection_cloud_shortcode( $atts, string $content, string $name ) : string {
	webcomic_error( __( '[webcomic_collection_cloud] is deprecated; use [webcomic_collections_list] instead.', 'webcomic' ) );

	$args = shortcode_atts(
		[
			'id'         => '',
			'class'      => '',
			'smallest'   => 75,
			'largest'    => 150,
			'unit'       => '%',
			'image'      => '',
			'before'     => '',
			'after'      => '',
			'sep'        => ' ',
			'orderby'    => '',
			'order'      => 'rand',
			'callback'   => '',
			'show_count' => false,
			'target'     => 'self',
			'selected'   => 0,
		], $atts, $name
	);

	return WebcomicTag::webcomic_collection_cloud_( $args );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function the_webcomic_shortcode( $atts, string $content, string $name ) : string {
	webcomic_error( __( '[the_webcomic] is deprecated; use [webcomic_media] instead.', 'webcomic' ) );

	$args = shortcode_atts(
		[
			'size'           => 'full',
			'relative'       => '',
			'in_same_term'   => false,
			'excluded_terms' => '',
			'taxonomy'       => 'storyline',
			'the_post'       => false,
		], $atts, $name
	);

	return WebcomicTag::the_webcomic_( $args['size'], $args['relative'], $args['in_same_term'], $args['excluded_terms'], $args['taxonomy'], $args['the_post'] );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_count_shortcode( $atts, string $content, string $name ) : string {
	webcomic_error( __( '[webcomic_count] is deprecated; use [has_webcomic_media] instead.', 'webcomic' ) );

	$args = shortcode_atts(
		[
			'if'       => '',
			'the_post' => null,
		], $atts, $name
	);

	return has_webcomic_media_shortcode(
		[
			'post'  => $args['the_post'],
			'count' => $args['if'],
		], $content, $name
	);
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function verify_webcomic_age_shortcode( $atts, string $content, string $name ) : string {
	webcomic_error( __( '[verify_webcomic_age] is deprecated; use [webcomic_age_required] instead.', 'webcomic' ) );

	$args = shortcode_atts(
		[
			'collection' => '',
			'age'        => 0,
		], $atts, $name
	);

	if ( ! WebcomicTag::verify_webcomic_age_( $args['collection'], null, $args['age'] ) ) {
		return '';
	}

	return do_shortcode( $content );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function verify_webcomic_role_shortcode( $atts, string $content, string $name ) : string {
	webcomic_error( __( '[verify_webcomic_role] is deprecated; use [webcomic_roles_required] instead.', 'webcomic' ) );

	$args = shortcode_atts(
		[
			'collection' => '',
			'roles'      => [],
		], $atts, $name
	);

	if ( ! is_array( $args['roles'] ) ) {
		parse_str( htmlspecialchars_decode( $args['roles'] ), $args['roles'] );
	}

	if ( ! WebcomicTag::verify_webcomic_role_( $args['collection'], null, array_keys( $args['roles'] ) ) ) {
		return '';
	}

	return do_shortcode( $content );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function the_webcomic_terms_shortcode( $atts, string $content, string $name ) : string {
	// Translators: The shortcode name.
	webcomic_error( sprintf( __( '[%s] is deprecated; use [webcomic_terms_list] instead.', 'webcomic' ), $name ) );

	$args = shortcode_atts(
		[
			'after'    => '',
			'before'   => '',
			'id'       => 0,
			'image'    => '',
			'sep'      => ', ',
			'target'   => 'archive',
			'taxonomy' => '',
		], $atts, $name
	);

	preg_match( '/storyline|character/', $name, $match );

	if ( $match ) {
		$args['taxonomy'] = $match[0];
	}

	return WebcomicTag::get_the_webcomic_term_list_( $args['id'], $args['taxonomy'], $args['before'], $args['sep'], $args['after'], $args['target'], $args['image'] );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_term_crossovers_shortcode( $atts, string $content, string $name ) : string {
	// Translators: The shortcode name.
	webcomic_error( sprintf( __( '[%s] is deprecated; use [webcomic_terms_list] instead.', 'webcomic' ), $name ) );

	$args = shortcode_atts(
		[
			'after'      => '',
			'before'     => '',
			'collection' => get_webcomic_collection(),
			'image'      => '',
			'sep'        => ', ',
			'target'     => 'archive',
			'term'       => 0,
		], $atts, $name
	);

	preg_match( '/storyline|character/', $name, $match );

	if ( $match ) {
		$args['collection'] = "{$args['collection']}_{$match[0]}";
	}

	return WebcomicTag::webcomic_term_crossovers_( $args['term'], $args['collection'], $args['before'], $args['sep'], $args['after'], $args['target'], $args['image'] );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_term_media_shortcode( $atts, string $content, string $name ) : string {
	// Translators: The shortcode name.
	webcomic_error( sprintf( __( '[%s] is deprecated; use [webcomic_term_media] instead.', 'webcomic' ), $name ) );

	$args = shortcode_atts(
		[
			'size'       => 'full',
			'term'       => 0,
			'collection' => get_webcomic_collection(),
		], $atts, $name
	);

	preg_match( '/storyline|character/', $name, $match );

	if ( $match ) {
		$args['collection'] = "{$args['collection']}_{$match[0]}";
	}

	return WebcomicTag::webcomic_term_media_( $args['size'], $args['term'], $args['collection'] );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_term_cloud_shortcode( $atts, string $content, string $name ) : string {
	// Translators: The shortcode name.
	webcomic_error( sprintf( __( '[%s] is deprecated; use [webcomic_terms_list] instead.', 'webcomic' ), $name ) );

	$args = shortcode_atts(
		[
			'after'      => '',
			'before'     => '',
			'callback'   => '',
			'class'      => '',
			'collection' => get_webcomic_collection(),
			'id'         => '',
			'image'      => '',
			'largest'    => 150,
			'orderby'    => 'rand',
			'selected'   => 0,
			'sep'        => ' ',
			'show_count' => false,
			'smallest'   => 75,
			'target'     => 'archive',
		], $atts, $name
	);

	preg_match( '/storyline|character/', $name, $match );

	if ( ! $match || ! taxonomy_exists( "{$args['collection']}_{$match[0]}" ) ) {
		return '';
	}

	$args['taxonomy'] = "{$args['collection']}_{$match[0]}";

	return WebcomicTag::webcomic_term_cloud_( $args );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_dropdown_terms_shortcode( $atts, string $content, string $name ) : string {
	// Translators: The shortcode name.
	webcomic_error( sprintf( __( '[%s] is deprecated; use [webcomic_terms_list] instead.', 'webcomic' ), $name ) );

	$args = shortcode_atts(
		[
			'class'            => '',
			'collection'       => get_webcomic_collection(),
			'depth'            => 0,
			'hide_if_empty'    => true,
			'hierarchical'     => true,
			'id'               => '',
			'order_by'         => 'term_group',
			'select_name'      => 'webcomic_terms',
			'selected'         => 0,
			'show_count'       => false,
			'show_option_all'  => '',
			'show_option_none' => '',
			'target'           => 'archive',
			'walker'           => false,
			'webcomics'        => false,
		], $atts, $name
	);

	preg_match( '/storyline|character/', $name, $match );

	if ( ! $match || ! taxonomy_exists( "{$args['collection']}_{$match[0]}" ) ) {
		return '';
	}

	$args['taxonomy'] = "{$args['collection']}_{$match[0]}";

	return WebcomicTag::webcomic_dropdown_terms_( $args );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_list_terms_shortcode( $atts, string $content, string $name ) : string {
	// Translators: The shortcode name.
	webcomic_error( sprintf( __( '[%s] is deprecated; use [webcomic_terms_list] instead.', 'webcomic' ), $name ) );

	$args = shortcode_atts(
		[
			'after'            => '',
			'before'           => '',
			'class'            => '',
			'collection'       => get_webcomic_collection(),
			'depth'            => 0,
			'feed_type'        => 'rss2',
			'feed'             => '',
			'hierarchical'     => true,
			'id'               => '',
			'orderby'          => '',
			'ordered'          => '',
			'selected'         => 0,
			'sep'              => '',
			'show_count'       => false,
			'show_description' => false,
			'show_image'       => '',
			'target'           => 'archive',
			'walker'           => false,
			'webcomic_image'   => '',
			'webcomics'        => false,
		], $atts, $name
	);

	preg_match( '/storyline|character/', $name, $match );

	if ( ! $match || ! taxonomy_exists( "{$args['collection']}_{$match[0]}" ) ) {
		return '';
	}

	$args['taxonomy'] = "{$args['collection']}_{$match[0]}";

	return WebcomicTag::webcomic_list_terms_( $args );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_crossover_description_shortcode( $atts, string $content, string $name ) : string {
	webcomic_error( __( '[webcomic_crossover_description] is deprecated; use [webcomic_collection_description] instead.', 'webcomic' ) );

	return WebcomicTag::webcomic_crossover_description_();
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_crossover_poster_shortcode( $atts, string $content, string $name ) : string {
	webcomic_error( __( '[webcomic_crossover_poster] is deprecated; use [webcomic_collection_media] instead.', 'webcomic' ) );

	$args = shortcode_atts(
		[
			'size' => 'full',
		], $atts, $name
	);

	return WebcomicTag::webcomic_crossover_media_( $args['size'] );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_crossover_title_shortcode( $atts, string $content, string $name ) : string {
	webcomic_error( __( '[webcomic_crossover_title] is deprecated; use [webcomic_collection_title] instead.', 'webcomic' ) );

	$args = shortcode_atts(
		[
			'prefix' => '',
		], $atts, $name
	);

	return WebcomicTag::webcomic_crossover_title_( $args['prefix'] );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_collection_crossovers_shortcode( $atts, string $content, string $name ) {
	webcomic_error( __( '[webcomic_collection_crossovers] is deprecated; use [webcomic_collections_list] instead.', 'webcomic' ) );

	$args = shortcode_atts(
		[
			'before'     => '',
			'sep'        => ', ',
			'after'      => '',
			'target'     => 'archive',
			'image'      => '',
			'collection' => '',
		], $atts, $name
	);

	return WebcomicTag::webcomic_collection_crossovers_( $args['before'], $args['join'], $args['after'], $args['target'], $args['image'], $args['collection'] );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function purchase_webcomic_link_shortcode( $atts, string $content, string $name ) {
	webcomic_error( __( '[purchase_webcomic_link] is deprecated; use [webcomic_link] instead.', 'webcomic' ) );

	$args = shortcode_atts(
		[
			'format'   => '%link',
			'link'     => '&curren;',
			'the_post' => null,
		], $atts, $name
	);

	return WebcomicTag::purchase_webcomic_link_( $args['format'], $args['link'], $args['the_post'] );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_collection_print_amount_shortcode( $atts, string $content, string $name ) {
	webcomic_error( __( '[webcomic_collection_print_amount] is deprecated; use [webcomic_collection_print_price] instead.', 'webcomic' ) );

	$args = shortcode_atts(
		[
			'type'       => '',
			'dec'        => '.',
			'sep'        => ',',
			'collection' => '',
		], $atts, $name
	);

	return WebcomicTag::webcomic_collection_print_amount_( $args['type'], $args['dec'], $args['sep'], $args['collection'] );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_donation_amount_shortcode( $atts, string $content, string $name ) {
	webcomic_error( __( '[webcomic_donation_amount] is deprecated; use [webcomic_collection_donation] instead.', 'webcomic' ) );

	$args = shortcode_atts(
		[
			'dec'        => '.',
			'sep'        => ',',
			'collection' => '',
		], $atts, $name
	);

	return WebcomicTag::webcomic_donation_amount_( $args['dec'], $args['sep'], $args['collection'] );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_donation_form_shortcode( $atts, string $content, string $name ) {
	webcomic_error( __( '[webcomic_donation_form] is deprecated; use [webcomic_link] instead.', 'webcomic' ) );

	$args = shortcode_atts(
		[
			'dec'        => '.',
			'sep'        => ',',
			'collection' => '',
		], $atts, $name
	);

	return WebcomicTag::webcomic_donation_amount_( $args['dec'], $args['sep'], $args['collection'] );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_print_amount_shortcode( $atts, string $content, string $name ) {
	webcomic_error( __( '[webcomic_print_amount] is deprecated; use [webcomic_print_price] instead.', 'webcomic' ) );

	$args = shortcode_atts(
		[
			'type'     => '',
			'dec'      => '.',
			'sep'      => ',',
			'the_post' => null,
		], $atts, $name
	);

	return WebcomicTag::webcomic_print_amount_( $args['type'], $args['dec'], $args['sep'], $args['the_post'] );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_print_adjustment_shortcode( $atts, string $content, string $name ) {
	webcomic_error( __( '[webcomic_print_adjustment] is deprecated; use [webcomic_print_adjust] instead.', 'webcomic' ) );

	$args = shortcode_atts(
		[
			'type'     => '',
			'the_post' => null,
		], $atts, $name
	);

	return WebcomicTag::webcomic_print_adjustment_( $args['type'], $args['the_post'] );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_print_form_shortcode( $atts, string $content, string $name ) {
	webcomic_error( __( '[webcomic_print_form] is deprecated; use [webcomic_link] instead.', 'webcomic' ) );

	$args = shortcode_atts(
		[
			'type'     => '',
			'label'    => '',
			'the_post' => null,
		], $atts, $name
	);

	return WebcomicTag::webcomic_print_form_( $args['type'], $args['label'], $args['the_post'] );
}

/**
 * Deprecated shortcode.
 *
 * @param array  $atts Optional attributes.
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function webcomic_transcripts_link_shortcode( $atts, string $content, string $name ) {
	webcomic_error( __( '[webcomic_transcripts_link] is deprecated; use [webcomic_link] instead.', 'webcomic' ) );

	$args = shortcode_atts(
		[
			'format'   => '%link',
			'none'     => '',
			'some'     => '',
			'off'      => '',
			'language' => false,
			'the_post' => false,
		], $atts, $name
	);

	return WebcomicTag::webcomic_transcripts_link_( $args['format'], $args['none'], $args['some'], $args['off'], $args['language'], $args['the_post'] );
}

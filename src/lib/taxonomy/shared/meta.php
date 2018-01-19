<?php
/**
 * Share taxonomy meta functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Taxonomy\Shared;

/**
 * Add a taxonomy select for comic list admin page navigation.
 *
 * @param string $type The taxonomy type.
 * @param string $post_type The current post type.
 * @return void
 */
function add_taxonomy_field( string $type, string $post_type ) {
	if ( ! webcomic( "option.{$post_type}" ) || in_array( "taxonomy-{$post_type}_{$type}", get_hidden_columns( get_current_screen() ), true ) ) {
		return;
	}

	$terms = get_webcomic_terms(
		[
			'taxonomy' => "{$post_type}_{$type}",
		]
	);

	if ( ! $terms ) {
		return;
	}

	// Translators: Taxonomy name.
	$all      = sprintf( esc_html__( 'All %s', 'webcomic' ), get_taxonomy( "{$post_type}_{$type}" )->labels->menu_name );
	$dropdown = get_webcomic_terms_list(
		[
			'format'   => "<select name='{{$post_type}_{$type}}'><option value=''>{$all}</option>{{}}</select>",
			'taxonomy' => "{$post_type}_{$type}",
		]
	);

	foreach ( $terms as $term ) {
		$dropdown = preg_replace( "/ value='{$term->term_id}'/", " value='{$term->slug}'", $dropdown );
	}

	echo $dropdown; // WPCS: xss ok.
}

/* ===== Collection Hooks =================================================== */

/**
 * Add media post meta when creating a new term.
 *
 * @param int $id The term ID.
 * @param int $taxonomy_id The term taxonomy ID.
 * @return void
 */
function hook_add_media_meta( int $id, int $taxonomy_id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'MediaNonce' ) ), __NAMESPACE__ . 'MediaNonce' ) || ! webcomic( 'GLOBALS._REQUEST.webcomic_media' ) ) {
		return;
	}

	update_term_meta( $id, 'webcomic_media', abs( (int) webcomic( 'GLOBALS._REQUEST.webcomic_media' ) ) );
}

/**
 * Add order meta when creating a new term.
 *
 * @param int $id The term ID.
 * @param int $taxonomy_id The term taxonomy ID.
 * @return void
 */
function hook_add_order_meta( int $id, int $taxonomy_id ) {
	update_term_meta( $id, 'webcomic_order', wp_count_terms( get_term( $id )->taxonomy ) );
}

/**
 * Update media post meta when updating a term.
 *
 * @param int $id The term ID.
 * @param int $taxonomy_id The term taxonomy ID.
 * @return void
 */
function hook_update_media_meta( int $id, int $taxonomy_id ) {
	if ( null === webcomic( 'GLOBALS._REQUEST.webcomic_media' ) || ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'MediaNonce' ) ), __NAMESPACE__ . 'MediaNonce' ) ) {
		return;
	} elseif ( ! webcomic( 'GLOBALS._REQUEST.webcomic_media' ) ) {
		delete_term_meta( $id, 'webcomic_media', get_term_meta( $id, 'webcomic_media', true ) );

		return;
	}

	$media = abs( (int) webcomic( 'GLOBALS._REQUEST.webcomic_media' ) );

	if ( ! $media ) {
		return;
	}

	update_term_meta( $id, 'webcomic_media', $media, get_term_meta( $id, 'webcomic_media', true ) );
}

/**
 * Add the media field to Add Term pages.
 *
 * @param string $taxonomy The current taxonomy.
 * @return void
 */
function hook_add_media_field( string $taxonomy ) {
	$args = [
		'file'  => __DIR__ . '/meta-inc-add-field-media.php',
		'nonce' => __NAMESPACE__ . 'MediaNonce',
	];

	require $args['file'];
}

/**
 * Add the media field to Edit Term pages.
 *
 * @param mixed  $term The term being edited.
 * @param string $taxonomy The current taxonomy.
 * @return void
 */
function hook_edit_media_field( $term, $taxonomy ) {
	$args = [
		'file'   => __DIR__ . '/meta-inc-edit-field-media.php',
		'nonce'  => __NAMESPACE__ . 'MediaNonce',
		'option' => implode( ',', get_term_meta( $term->term_id, 'webcomic_media' ) ),
	];

	require $args['file'];
}

/**
 * Add the media column to the terms list admin page.
 *
 * @param array $columns The terms list columns.
 * @return array
 */
function hook_add_media_column( array $columns ) : array {
	$pre                   = array_slice( $columns, 0, 1 );
	$pre['webcomic_media'] = __( 'Media', 'webcomic' );

	return $pre + $columns;
}

/**
 * Display the media column.
 *
 * @param mixed  $value The current column value.
 * @param string $column The column currently being displayed.
 * @param int    $term The current post ID.
 * @return void
 */
function hook_display_media_column( $value, string $column, int $term ) {
	if ( 'webcomic_media' !== $column ) {
		return;
	}

	$output = wp_get_attachment_image( get_term_meta( $term, 'webcomic_media', true ), 'medium' );

	if ( ! $output ) {
		$output = '&mdash;';
	}

	echo $output; // WPCS: xss ok.
}

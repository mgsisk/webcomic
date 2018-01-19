<?php
/**
 * Media management functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

/**
 * Add media management hooks.
 *
 * @return void
 */
function media() {
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_media_styles' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_media_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_localize_media_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_media_styles' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_media_scripts' );
	add_filter( 'add_post_meta', __NAMESPACE__ . '\hook_add_post_media_context', 10, 3 );
	add_filter( 'delete_post_meta', __NAMESPACE__ . '\hook_delete_post_media_context', 10, 4 );
	add_filter( 'delete_attachment', __NAMESPACE__ . '\hook_delete_comic_media' );
	add_filter( 'display_media_states', __NAMESPACE__ . '\hook_add_post_media_states' );
	add_filter( 'wp_ajax_webcomic_update_media_manager', __NAMESPACE__ . '\hook_update_media_manager' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "manage_{$collection}_posts_columns", __NAMESPACE__ . '\hook_add_media_column' );
		add_filter( "manage_{$collection}_posts_custom_column", __NAMESPACE__ . '\hook_display_media_column', 10, 2 );
	}
}

/**
 * Register media stylesheets.
 *
 * @return void
 */
function hook_register_media_styles() {
	wp_register_style(
		__NAMESPACE__ . 'MediaColumnCSS',
		plugins_url( 'srv/collection/column-media.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);

	wp_register_style(
		__NAMESPACE__ . 'ManagerCSS',
		plugins_url( 'srv/collection/manager.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);
}

/**
 * Register media javascript.
 *
 * @return void
 */
function hook_register_media_scripts() {
	wp_register_script(
		__NAMESPACE__ . 'ManagerJS',
		plugins_url( 'srv/collection/manager.js', webcomic( 'file' ) ),
		[ 'jquery-ui-sortable' ],
		webcomic( 'option.version' ),
		true
	);
}

/**
 * Localize media scripts.
 *
 * @return void
 */
function hook_localize_media_scripts() {
	wp_localize_script(
		__NAMESPACE__ . 'ManagerJS',
		'webcomicMediaManagerL10n',
		[
			'add'    => __( 'Add Media', 'webcomic' ),
			'drag'   => __( 'Drag media to change the display order.', 'webcomic' ),
			'title'  => __( 'Select Media', 'webcomic' ),
			'change' => __( 'Change Media', 'webcomic' ),
			'update' => __( 'Update', 'webcomic' ),
			'remove' => __( 'Remove', 'webcomic' ),

		]
	);
}

/**
 * Enqueue media stylesheets.
 *
 * @return void
 */
function hook_enqueue_media_styles() {
	if ( ! preg_match( '/^edit-webcomic\d+$/', get_current_screen()->id ) ) {
		return;
	}

	wp_enqueue_style( __NAMESPACE__ . 'MediaColumnCSS' );
}

/**
 * Enqueue media scripts.
 *
 * @return void
 */
function hook_enqueue_media_scripts() {
	/**
	 * Alter media manager enqueing.
	 *
	 * This filter allows hooks to enqueue the shared media manager functionality.
	 *
	 * @param bool $enqueue Wether to enqueue the media manager.
	 */
	$enqueue = apply_filters( 'webcomic_enqueue_media_manager', false );

	if ( ! $enqueue ) {
		return;
	}

	$screen  = get_current_screen();
	$options = [];

	if ( webcomic_collection_exists( $screen->id ) && ! in_array( 'content', webcomic( "option.{$screen->id}.supports" ), true ) ) {
		$options = [
			'post' => get_the_ID(),
		];
	}

	wp_enqueue_media( $options );
	wp_enqueue_style( __NAMESPACE__ . 'ManagerCSS' );
	wp_enqueue_script( __NAMESPACE__ . 'ManagerJS' );
}

/**
 * Add media contexts when adding webcomic_media meta data.
 *
 * @param int    $post The post being updated.
 * @param string $key The meta data key being added.
 * @param mixed  $value The meta data value being added.
 * @return void
 */
function hook_add_post_media_context( int $post, string $key, $value ) {
	if ( 'webcomic_media' !== $key || ! is_a_webcomic( $post ) ) {
		return;
	}

	add_post_meta( $value, 'webcomic_post', $post );
}

/**
 * Delete media contexts when deleting webcomic_media meta data.
 *
 * @param array  $meta The meta entries being deleted.
 * @param int    $post The post being updated.
 * @param string $key The meta data key being delete.
 * @param mixed  $value The meta data value being deleted.
 * @return void
 */
function hook_delete_post_media_context( array $meta, int $post, string $key, $value ) {
	if ( 'webcomic_media' !== $key || ! is_a_webcomic( $post ) ) {
		return;
	}

	delete_post_meta( $value, 'webcomic_post', $post );
}

/**
 * Delete comic media when a media item is deleted.
 *
 * @param int $post The post being deleted.
 * @return void
 */
function hook_delete_comic_media( int $post ) {
	foreach ( get_post_meta( $post, 'webcomic_post' ) as $comic ) {
		delete_post_meta( $comic, 'webcomic_media', $post );
	}
}

/**
 * Add media states for comic media.
 *
 * @param array $states The media states for the current object.
 * @return array
 */
function hook_add_post_media_states( array $states ) : array {
	$comics = get_post_meta( get_the_ID(), 'webcomic_post' );

	foreach ( $comics as $comic ) {
		$collection = get_post_type( $comic );

		if ( isset( $states[ $collection ] ) ) {
			continue;
		} elseif ( ! webcomic_collection_exists( $collection ) ) {
			delete_post_meta( get_the_ID(), 'webcomic_post', $comic );

			continue;
		}

		// Translators: Post type name.
		$states[ $collection ] = sprintf( __( '%s Comic Image', 'webcomic' ), webcomic( "option.{$collection}.name" ) );
	}

	return $states;
}

/**
 * Handle media manager requests.
 *
 * @return void
 */
function hook_update_media_manager() {
	if ( null === webcomic( 'GLOBALS._REQUEST.media' ) ) {
		wp_die();
	}

	$output = [];

	foreach ( explode( ',', webcomic( 'GLOBALS._REQUEST.media' ) ) as $media ) {
		if ( ! $media ) {
			continue;
		}

		$output[] = [
			'id'    => (int) $media,
			'media' => wp_get_attachment_image( (int) $media, 'medium' ),
		];
	}

	wp_send_json( $output );
}

/* ===== Collection Hooks =================================================== */

/**
 * Add the media column to the posts list admin page.
 *
 * @param array $columns The posts list columns.
 * @return array
 */
function hook_add_media_column( array $columns ) : array {
	return array_slice( $columns, 0, 1 ) + [
		'webcomic_media' => __( 'Media', 'webcomic' ),
	] + array_slice( $columns, 1 );
}

/**
 * Display the media column.
 *
 * @param string $column The column currently being displayed.
 * @param int    $post The current post ID.
 * @return void
 */
function hook_display_media_column( string $column, int $post ) {
	if ( 'webcomic_media' !== $column ) {
		return;
	}

	$output = [ '&mdash;' ];

	foreach ( get_post_meta( $post, 'webcomic_media' ) as $media ) {
		$image = wp_get_attachment_image( $media, 'medium' );

		if ( current_user_can( 'edit_post', $media ) ) {
			$url   = esc_url(
				add_query_arg(
					[
						'post'   => $media,
						'action' => 'edit',
					], admin_url( 'post.php' )
				)
			);
			$image = "<a href='{$url}'>{$image}</a>";
		}

		$output[] = $image;
	}

	if ( 1 < count( $output ) ) {
		unset( $output[0] );
	}

	echo implode( '<br>', $output ); // WPCS: xss ok.
}

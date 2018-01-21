<?php
/**
 * Standard transcribe functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe;

use const Mgsisk\Webcomic\Collection\ENDPOINT;
use WP_Query;
use WP_Post;

/**
 * Add common hooks.
 *
 * @return void
 */
function common() {
	add_filter( 'init', __NAMESPACE__ . '\hook_add_rewrite_endpoint' );
	add_filter( 'init', __NAMESPACE__ . '\hook_register_taxonomy', 99 );
	add_filter( 'init', __NAMESPACE__ . '\hook_register_post_type', 99 );
	add_filter( 'init', __NAMESPACE__ . '\hook_save_transcript', 99 );
	add_filter( 'init', __NAMESPACE__ . '\hook_register_scripts' );
	add_filter( 'widgets_init', __NAMESPACE__ . '\hook_register_widgets' );
	add_filter( 'template_include', __NAMESPACE__ . '\hook_edit_transcript' );
	add_filter( 'wp_enqueue_scripts', __NAMESPACE__ . '\hook_localize_scripts' );
	add_filter( 'wp_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_scripts' );
	add_filter( 'post_updated', __NAMESPACE__ . '\hook_update_transcript_counts', 10, 3 );
	add_filter( 'save_post', __NAMESPACE__ . '\hook_increment_transcript_counts', 10, 3 );
	add_filter( 'transition_post_status', __NAMESPACE__ . '\hook_notify_comic_author', 10, 3 );
	add_filter( 'the_posts', __NAMESPACE__ . '\hook_search_transcript_comics', 10, 2 );
	add_filter( 'the_title', __NAMESPACE__ . '\hook_get_transcript_title', 10, 2 );
	add_filter( 'get_post_metadata', __NAMESPACE__ . '\hook_get_post_meta_close_transcripts', 10, 4 );
	add_filter( 'wp_ajax_webcomic_transcribe', __NAMESPACE__ . '\hook_get_editable_transcript' );
	add_filter( 'wp_ajax_nopriv_webcomic_transcribe', __NAMESPACE__ . '\hook_get_editable_transcript' );
}

/**
 * Add the transcribe rewrite endpoint for comics.
 *
 * @return void
 * @suppress PhanUndeclaredConstant - ENDPOINT incorrectly triggers this.
 */
function hook_add_rewrite_endpoint() {
	add_rewrite_endpoint( 'transcribe', ENDPOINT );
}

/**
 * Register transcript language taxonomy.
 *
 * @return void
 */
function hook_register_taxonomy() {
	register_taxonomy(
		'webcomic_transcript_language', [], [
			'labels'            => [
				'name'                       => __( 'Languages', 'webcomic' ),
				'singular_name'              => __( 'Language', 'webcomic' ),
				'menu_name'                  => __( 'Languages', 'webcomic' ),
				'all_items'                  => __( 'All Languages', 'webcomic' ),
				'edit_item'                  => __( 'Edit Language', 'webcomic' ),
				'view_item'                  => __( 'View Language', 'webcomic' ),
				'update_item'                => __( 'Update Language', 'webcomic' ),
				'add_new_item'               => __( 'Add New Language', 'webcomic' ),
				'new_item_name'              => __( 'New Language Name', 'webcomic' ),
				'search_items'               => __( 'Search Languages', 'webcomic' ),
				'popular_items'              => __( 'Popular Languages', 'webcomic' ),
				'separate_items_with_commas' => __( 'Separate languages with commas', 'webcomic' ),
				'add_or_remove_items'        => __( 'Add or remove languages', 'webcomic' ),
				'choose_from_most_used'      => __( 'Choose from the most used languages', 'webcomic' ),
				'not_found'                  => __( 'No languages found.', 'webcomic' ),
			],
			'description'       => __( 'Allows you to assign languages to comic transcripts.', 'webcomic' ),
			'rewrite'           => false,
			'show_in_nav_menus' => false,
			'show_admin_column' => true,
		]
	);
}

/**
 * Register transcript custom post type.
 *
 * @return void
 */
function hook_register_post_type() {
	register_post_type(
		'webcomic_transcript', [
			'labels'              => [
				'name'                  => __( 'Comic Transcripts', 'webcomic' ),
				'singular_name'         => __( 'Comic Transcript', 'webcomic' ),
				'add_new'               => __( 'Add New', 'webcomic' ),
				'add_new_item'          => __( 'Add New Comic Transcript', 'webcomic' ),
				'edit_item'             => __( 'Edit Comic Transcript', 'webcomic' ),
				'new_item'              => __( 'New Comic Transcript', 'webcomic' ),
				'view_item'             => __( 'View Comic Transcript', 'webcomic' ),
				'search_items'          => __( 'Search Comic Transcripts', 'webcomic' ),
				'not_found'             => __( 'No comic transcripts found.', 'webcomic' ),
				'not_found_in_trash'    => __( 'No comic transcripts found in Trash.', 'webcomic' ),
				'all_items'             => __( 'All Comic Transcripts', 'webcomic' ),
				'archives'              => __( 'Comic Transcript Archives', 'webcomic' ),
				'insert_into_item'      => __( 'Insert into comic transcript', 'webcomic' ),
				'uploaded_to_this_item' => __( 'Uploaded to this comic transcript', 'webcomic' ),
			],
			'public'              => false,
			'show_ui'             => true,
			'exclude_from_search' => false,
			'supports'            => [ 'editor', 'revisions' ],
			'taxonomies'          => [ 'webcomic_transcript_language' ],
			'menu_icon'           => 'dashicons-testimonial',
			'description'         => __( 'Provides transcription functionality for Webcomic post types.', 'webcomic' ),
		]
	);
}

/**
 * Handle user transcript submissions.
 *
 * @return void
 */
function hook_save_transcript() {
	if ( ! webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'Nonce' ) ) {
		return;
	} elseif ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'Nonce' ) ), __NAMESPACE__ . 'Nonce' ) ) {
		wp_die( esc_html__( 'Are you sure you want to do this?', 'webcomic' ), '', 'back_link=1' );
	}

	$author_data = util_get_transcript_author();
	$post_data   = util_get_transcript_data( $author_data );
	$error       = util_validate_transcript_data( $post_data, $author_data );

	if ( $error ) {
		wp_die( esc_html( $error ), '', 'back_link=1' );
	} elseif ( ! $author_data['name'] ) {
		$author_data['name'] = sanitize_text_field( __( 'Anonymous', 'webcomic' ) );
	}

	$post = wp_insert_post( $post_data, true );

	if ( is_wp_error( $post ) ) {
		// Translators: WP_Error error message.
		wp_die( sprintf( esc_html__( 'ERROR: %s', 'webcomic' ), esc_html( $post->get_error_message() ) ), '', 'back_link=1' );
	}

	util_set_transcript_terms( $post, $post_data['tax_input'] );

	add_post_meta( $post, 'webcomic_transcript_authors', $author_data );

	if ( 'draft' === $post_data['post_status'] ) {
		util_notify_transcript_moderator( $post );
	}
}

/**
 * Register common scripts.
 *
 * @return void
 */
function hook_register_scripts() {
	wp_register_script(
		__NAMESPACE__ . 'CommonJS',
		plugins_url( 'srv/transcribe/common.js', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' ),
		true
	);
}

/**
 * Register transcribe widgets.
 *
 * @return void
 */
function hook_register_widgets() {
	register_widget( __NAMESPACE__ . '\Widget\WebcomicTranscriptForm' );
	register_widget( __NAMESPACE__ . '\Widget\WebcomicTranscriptsLink' );
	register_widget( __NAMESPACE__ . '\Widget\WebcomicTranscriptsList' );
}

/**
 * Handle /transcribe endpoints for comics.
 *
 * @param string $template The template to load.
 * @return string
 */
function hook_edit_transcript( string $template ) : string {
	if ( null === get_query_var( 'transcribe', null ) || ! is_webcomic() ) {
		return $template;
	}

	$transcript = abs( (int) get_query_var( 'transcribe' ) );

	if ( ! is_a_webcomic_transcript( $transcript, 'pending' ) || wp_get_post_parent_id( $transcript ) !== get_the_ID() ) {
		return locate_template( '404.php' );
	}

	return $template;
}

/**
 * Localze common scripts.
 *
 * @return void
 */
function hook_localize_scripts() {
	wp_localize_script(
		__NAMESPACE__ . 'CommonJS',
		'webcomicCommonJS',
		[
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		]
	);
}

/**
 * Enqueue common scripts.
 *
 * @return void
 */
function hook_enqueue_scripts() {
	if ( is_admin() ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'CommonJS' );
}

/**
 * Update transcript counts for comics.
 *
 * @param int     $id The post ID.
 * @param WP_Post $new_post The new post object.
 * @param WP_Post $old_post The old post object.
 * @return void
 */
function hook_update_transcript_counts( int $id, WP_Post $new_post, WP_Post $old_post ) {
	if ( ! is_a_webcomic_transcript( $new_post ) ) {
		return;
	} elseif ( ! $new_post->post_parent && ! $old_post->post_parent ) {
		return;
	} elseif ( $new_post->post_parent === $old_post->post_parent && $new_post->post_status === $old_post->post_status ) {
		return;
	}

	$old_count = (int) get_post_meta( $old_post->post_parent, "webcomic_transcribe_count_{$old_post->post_status}", true ) - 1;
	$new_count = (int) get_post_meta( $new_post->post_parent, "webcomic_transcribe_count_{$new_post->post_status}", true ) + 1;

	if ( 0 >= $old_count ) {
		delete_post_meta( $old_post->post_parent, "webcomic_transcribe_count_{$old_post->post_status}" );
	} elseif ( $old_post->post_parent ) {
		update_post_meta( $old_post->post_parent, "webcomic_transcribe_count_{$old_post->post_status}", $old_count );
	}

	if ( $new_post->post_parent ) {
		update_post_meta( $new_post->post_parent, "webcomic_transcribe_count_{$new_post->post_status}", $new_count );
	}
}

/**
 * Update transcript counts when a new transcript is saved.
 *
 * @param int     $id The post ID.
 * @param WP_Post $post The new post object.
 * @param bool    $updated Wether the post was updated or not.
 * @return void
 */
function hook_increment_transcript_counts( int $id, WP_Post $post, bool $updated ) {
	if ( $updated || ! $post->post_parent || wp_is_post_revision( $id ) ) {
		return;
	}

	$count = (int) get_post_meta( $post->post_parent, "webcomic_transcribe_count_{$post->post_status}", true ) + 1;

	update_post_meta( $post->post_parent, "webcomic_transcribe_count_{$post->post_status}", $count );
}

/**
 * Notify comic authors of published transcripts.
 *
 * @param string  $new_status The new post status.
 * @param string  $old_status The old post status.
 * @param WP_Post $post The post being updated.
 * @return void
 */
function hook_notify_comic_author( string $new_status, string $old_status, WP_Post $post ) {
	if ( 'publish' !== $new_status || $new_status === $old_status || ! is_a_webcomic_transcript( $post ) || ! is_a_webcomic( $post->post_parent ) ) {
		return;
	}

	$comic = get_webcomic( $post->post_parent );

	if ( ! webcomic( "option.{$comic->post_type}.transcribe_alert_pub" ) || ! user_can( $comic->post_author, 'read_post', $comic->ID ) ) {
		return;
	}

	$email   = get_userdata( $comic->post_author )->user_email;
	$authors = get_webcomic_transcript_authors(
		[
			'hide_duplicates' => 'email',
			'post'            => $post,
		]
	);

	if ( ! $authors ) {
		$authors = [ util_get_transcript_author() ];
	}

	if ( ! $email || 0 === strcasecmp( $email, $authors[0]['email'] ) ) {
		return;
	}

	// Translators: 1: Plugin name. 2: Custom post type name. 3: Post title.
	$subject = sprintf( '[%1$s] Transcript: %2$s - "%3$s"', 'Webcomic', webcomic( "option.{$comic->post_type}.name" ), $comic->post_title );
	$headers = [
		'from'         => 'Webcomic <webcomic@' . preg_replace( '/^www\./', '', strtolower( webcomic( 'GLOBALS._SERVER.SERVER_NAME' ) ) ) . '>',
		'content-type' => 'text/plain',
	];

	wp_mail( $email, $subject, util_get_notify_comic_author_message( $post, $comic, $authors ), $headers );
}

/**
 * Replace transcripts with their parent comic in searches.
 *
 * @param array    $posts The list of posts.
 * @param WP_Query $query The current query.
 * @return array
 */
function hook_search_transcript_comics( array $posts, WP_Query $query ) : array {
	if ( $query->is_admin() || ! $query->is_search() || ! $query->is_main_query() ) {
		return $posts;
	}

	$modified = false;

	foreach ( $posts as $key => $post ) {
		if ( ! is_a_webcomic_transcript( $post ) ) {
			continue;
		} elseif ( ! $post->post_parent || ! current_user_can( 'read_post', $post->post_parent ) ) {
			$modified = true;

			unset( $posts[ $key ] );

			continue;
		}

		$modified      = true;
		$posts[ $key ] = get_webcomic( $post->post_parent );
	}

	if ( ! $modified ) {
		return $posts;
	}

	return array_values( array_unique( $posts, SORT_REGULAR ) );
}

/**
 * Build a transcript title based on the current parent comic.
 *
 * @param string $title The post title.
 * @param int    $id The post ID.
 * @return string
 */
function hook_get_transcript_title( string $title, int $id ) : string {
	if ( ! is_a_webcomic_transcript( $id ) ) {
		return $title;
	}

	$parent = wp_get_post_parent_id( $id );

	if ( ! $parent || ! get_post_type( $parent ) ) {
		return __( 'Orphaned Comic Transcript', 'webcomic' );
	}

	$collection = get_post_type( $parent );

	// Translators: 1: Custom post type name. 2: Parent post title.
	return sprintf( __( '%1$s - %2$s Transcript', 'webcomic' ), webcomic( "option.{$collection}.name" ), get_the_title( $parent ) );
}

/**
 * Close comic transcription.
 *
 * @param mixed  $meta The meta value.
 * @param int    $id The post ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return only the first $key value.
 * @return mixed
 * @suppress PhanUndeclaredFunctionInCallable - Incorrectly triggered.
 */
function hook_get_post_meta_close_transcripts( $meta, $id, $key, $single ) {
	if ( 'webcomic_transcribe' !== $key || 'publish' !== get_post_status( $id ) ) {
		return $meta;
	}

	remove_filter( 'get_post_metadata', __FUNCTION__ );

	$collection = get_post_type( $id );

	if ( get_post_meta( $id, 'webcomic_transcribe', true ) && webcomic_collection_exists( $collection ) ) {
		$close_day = webcomic( "option.{$collection}.transcribe_close" ) * DAY_IN_SECONDS;

		if ( $close_day ) {
			$post_age = (int) current_time( 'timestamp' ) - (int) get_post_time( 'U', true, $id );

			if ( $post_age > $close_day ) {
				delete_post_meta( $id, $key );
			}
		}
	}

	add_filter( 'get_post_metadata', __FUNCTION__, 10, 4 );

	return $meta;
}

/**
 * Handle edit transcript requests.
 *
 * @return void
 */
function hook_get_editable_transcript() {
	if ( ! webcomic( 'GLOBALS._REQUEST.post' ) || ! webcomic( 'GLOBALS._REQUEST.parent' ) ) {
		return;
	}

	$post   = abs( (int) webcomic( 'GLOBALS._REQUEST.post' ) );
	$parent = abs( (int) webcomic( 'GLOBALS._REQUEST.parent' ) );

	if ( ! is_a_webcomic_transcript( $post, 'pending' ) || wp_get_post_parent_id( $post ) !== $parent ) {
		wp_die();
	}

	$output = [
		'id'        => $post,
		'content'   => esc_textarea( get_post_field( 'post_content', $post, 'edit' ) ),
		'languages' => wp_get_object_terms(
			$post, 'webcomic_transcript_language', [
				'fields' => 'ids',
			]
		),
	];

	wp_send_json( $output );
}

/* ===== Utility Functions ================================================== */

/**
 * Get author data for a user-submitted transcript.
 *
 * @return array
 * @internal For hook_save_transcript() and hook_notify_comic_author().
 */
function util_get_transcript_author() : array {
	$user = wp_get_current_user();
	$data = [
		'name'  => sanitize_text_field( webcomic( 'GLOBALS._REQUEST.webcomic_transcript_author' ) ),
		'email' => sanitize_email( webcomic( 'GLOBALS._REQUEST.webcomic_transcript_author_email' ) ),
		'url'   => esc_url_raw( webcomic( 'GLOBALS._REQUEST.webcomic_transcript_author_url' ) ),
		'time'  => current_time( 'mysql' ),
		'ip'    => webcomic( 'GLOBALS._SERVER.REMOTE_ADDR' ),
	];

	if ( $user->ID ) {
		$data['name']  = $user->display_name;
		$data['email'] = $user->user_email;
		$data['url']   = $user->user_url;
	}

	return $data;
}

/**
 * Get user-submitted transcript post data.
 *
 * @param array $author The transcript author info.
 * @return array
 * @internal For hook_save_transcript().
 */
function util_get_transcript_data( array $author ) : array {
	$data       = [
		'ID'           => abs( (int) webcomic( 'GLOBALS._REQUEST.webcomic_transcript_id' ) ),
		'post_type'    => 'webcomic_transcript',
		'post_parent'  => abs( (int) webcomic( 'GLOBALS._REQUEST.webcomic_transcript_parent' ) ),
		'post_status'  => 'draft',
		'post_content' => wp_kses_post( webcomic( 'GLOBALS._REQUEST.webcomic_transcript' ) ),
		'tax_input'    => [],
	];
	$language   = webcomic( 'GLOBALS._REQUEST.webcomic_transcript_language' );
	$collection = get_post_type( $data['post_parent'] );

	if ( $language ) {
		$data['tax_input']['webcomic_transcript_language'] = array_filter( array_map( 'intval', $language ) );
	}

	if ( ( 'loggedin' === webcomic( "option.{$collection}.transcribe_publish" ) && is_user_logged_in() ) || ( 'name_email' === webcomic( "option.{$collection}.transcribe_publish" ) && $author['name'] && $author['email'] ) ) {
		$data['post_status'] = 'publish';
	}

	$data['tax_input'] = array_filter( $data['tax_input'] );

	return $data;
}

/**
 * Get a comic author transcript notification message.
 *
 * @param WP_Post $transcript The transcript triggering the notification.
 * @param WP_Post $comic The comic the transcript belongs to.
 * @param array   $authors The athors that have contributed to the transcript.
 * @return string
 * @internal For hook_notify_comic_author().
 */
function util_get_notify_comic_author_message( WP_Post $transcript, WP_Post $comic, array $authors ) : string {
	// Translators: 1: Custom post type name. 2: Post title.
	$message  = sprintf( __( 'New transcript on %1$s - "%2$s"', 'webcomic' ), webcomic( "option.{$comic->post_type}.name" ), $comic->post_title ) . "\n";
	$message .= get_permalink( $comic->ID ) . "\n\n";
	// Translators: 1: Transcript author name. 2: Transcript author IP address.
	$message .= sprintf( __( 'Author: %1$s (%2$s)', 'webcomic' ), $authors[0]['name'], $authors[0]['ip'] ) . "\n";
	// Translators: Transcript author email address.
	$message .= sprintf( __( 'Email: %s', 'webcomic' ), $authors[0]['email'] ) . "\n";
	// Translators: Transcript author URL.
	$message .= sprintf( __( 'URL: %s', 'webcomic' ), $authors[0]['url'] ) . "\n";

	if ( 1 < count( $authors ) ) {
		// Translators: Number of other contributing authors.
		$message .= sprintf( _n( 'Other: %s other author contributed to this transcript.', 'Others: %s other authors contributed to this transcript.', count( $authors ) - 1, 'webcomic' ), number_format_i18n( count( $authors ) - 1 ) ) . "\n";
	}

	$message .= __( 'Transcript:', 'webcomic' ) . "\n" . $transcript->post_content . "\n\n";

	if ( user_can( $comic->post_author, 'edit_post', $transcript->ID ) ) {
		$query_args = [
			'post'   => $transcript->ID,
			'action' => 'edit',
		];

		// Translators: Edit post link.
		$message .= sprintf( 'Edit it: %s', esc_url( add_query_arg( $query_args, admin_url( 'post.php' ) ) ) ) . "\n";
	}

	return $message;
}

/**
 * Send comic transcript submission notification emails.
 *
 * @param int $post The submitted transcript ID.
 * @return void
 * @internal For hook_save_transcript().
 */
function util_notify_transcript_moderator( int $post ) {
	if ( ! is_a_webcomic_transcript( $post, 'draft' ) ) {
		return;
	}

	$comic = get_webcomic( wp_get_post_parent_id( $post ) );

	if ( ! $comic || ! webcomic( "option.{$comic->post_type}.transcribe_alert_mod" ) ) {
		return;
	}

	$query_args = [
		'post'   => $post,
		'action' => 'edit',
	];
	$email      = get_userdata( $comic->post_author )->user_email;
	$emails     = [ get_option( 'admin_email' ) ];
	$author     = current( get_post_meta( $post, 'webcomic_transcript_authors' ) );
	// Translators: 1: Plugin name. 2: Custom post type name. 3: Post title.
	$subject = sprintf( '[%1$s] Please moderate: Transcript for %2$s - "%3$s"', 'Webcomic', webcomic( "option.{$comic->post_type}.name" ), $comic->post_title );
	// Translators: 1: Post title. 2: Custom post type name.
	$message  = sprintf( __( 'A new transcript on %1$s - "%2$s" is waiting for your approval.', 'webcomic' ), webcomic( "option.{$comic->post_type}.name" ), $comic->post_title ) . "\n";
	$message .= get_permalink( $comic->ID ) . "\n\n";
	// Translators: 1: Transcript author name. 2: Transcript author IP address.
	$message .= sprintf( __( 'Author: %1$s (%2$s)', 'webcomic' ), $author['name'], $author['ip'] ) . "\n";
	// Translators: Transcript author email address.
	$message .= sprintf( __( 'Email: %s', 'webcomic' ), $author['email'] ) . "\n";
	// Translators: Transcript author URL.
	$message .= sprintf( __( 'URL: %s', 'webcomic' ), $author['url'] ) . "\n";
	$message .= __( 'Transcript:', 'webcomic' ) . "\n" . get_post_field( 'post_content', $post ) . "\n\n";
	// Translators: Publish post link.
	$message .= sprintf( 'Edit it: %s', esc_url( add_query_arg( $query_args, admin_url( 'post.php' ) ) ) );
	$headers  = [
		'from'         => "{$author['name']} <{$author['email']}>",
		'content-type' => 'text/plain',
	];

	if ( $email && user_can( $comic->post_author, 'edit_post', $post ) ) {
		$emails[] = $email;
	}

	wp_mail( $emails, $subject, $message, $headers );
}

/**
 * Set transcript post terms.
 *
 * @param int   $post The post ID to set terms for.
 * @param array $tax_input The taxonomy terms to set.
 * @return void
 * @internal For hook_save_transcript().
 * @suppress PhanTypeExpectedObjectPropAccess - Incorrectly triggered.
 */
function util_set_transcript_terms( int $post, array $tax_input ) {
	foreach ( $tax_input as $taxonomy => $terms ) {
		if ( current_user_can( get_taxonomy( $taxonomy )->cap->assign_terms ) ) {
			continue;
		}

		wp_set_object_terms( $post, $terms, $taxonomy );
	}
}

/**
 * Validate submitted transcript data.
 *
 * @param array $post The transcript data to validate.
 * @param array $author The author data to validate.
 * @return string
 * @internal For hook_save_transcript().
 */
function util_validate_transcript_data( array $post, array $author ) : string {
	$collection = get_post_type( $post['post_parent'] );
	$name_email = ( $author['name'] && $author['email'] );

	if ( ! $post['post_parent'] || ! is_a_webcomic( $post['post_parent'] ) ) {
		return __( 'ERROR: Invalid comic.', 'webcomic' );
	} elseif ( ! get_post_meta( $post['post_parent'], 'webcomic_transcribe', true ) ) {
		return __( 'ERROR: This comic cannot be transcribed.', 'webcomic' );
	} elseif ( 'loggedin' === webcomic( "option.{$collection}.transcribe_require" ) && ! is_user_logged_in() ) {
		return __( 'ERROR: You must be logged in to transcribe this comic.', 'webcomic' );
	} elseif ( ! $post['post_content'] ) {
		return __( 'ERROR: Please type a transcript.', 'webcomic' );
	} elseif ( 'name_email' === webcomic( "option.{$collection}.transcribe_require" ) && ! $name_email ) {
		return __( 'ERROR: You must provide a name and valid email address to transcribe this comic.', 'webcomic' );
	} elseif ( $post['ID'] && ! is_a_webcomic_transcript( $post['ID'], 'pending' ) ) {
		return __( 'ERROR: Invalid comic transcript.', 'webcomic' );
	}

	return '';
}

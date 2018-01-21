<?php
/**
 * Transcripts metabox functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe\MetaBox;

use WP_Screen;

/**
 * Add transcripts metabox hooks.
 *
 * @return void
 */
function transcripts() {
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_transcripts_styles' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_transcripts_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_localize_transcripts_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_transcripts_styles' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_transcripts_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_quick_transcripts_scripts' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_box_transcripts_help' );
	add_filter( 'add_meta_boxes', __NAMESPACE__ . '\hook_add_box_transcripts' );
	add_filter( 'bulk_edit_custom_box', __NAMESPACE__ . '\hook_add_quick_edit_transcripts', 10, 2 );
	add_filter( 'quick_edit_custom_box', __NAMESPACE__ . '\hook_add_quick_edit_transcripts', 10, 2 );
	add_filter( 'get_post_metadata', __NAMESPACE__ . '\hook_get_post_meta_default_transcripts', 10, 4 );
	add_filter( 'wp_ajax_webcomic_transcribe_quick_edit', __NAMESPACE__ . '\hook_quick_edit_transcripts' );
	add_filter( 'wp_ajax_webcomic_transcribe_form', __NAMESPACE__ . '\hook_get_transcript_form' );
	add_filter( 'wp_ajax_webcomic_transcribe_submit', __NAMESPACE__ . '\hook_submit_transcript' );
	add_filter( 'wp_ajax_webcomic_transcribe_trash', __NAMESPACE__ . '\hook_trash_transcript' );
	add_filter( 'wp_ajax_webcomic_transcribe_untrash', __NAMESPACE__ . '\hook_untrash_transcript' );
	add_filter( 'wp_ajax_webcomic_transcribe_row', __NAMESPACE__ . '\hook_get_transcript_row' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "save_post_{$collection}", __NAMESPACE__ . '\hook_update_post_meta_transcribe' );
	}
}

/**
 * Register transcripts stylesheets.
 *
 * @return void
 */
function hook_register_transcripts_styles() {
	wp_register_style(
		__NAMESPACE__ . 'TranscriptsCSS',
		plugins_url( 'srv/transcribe/metabox-transcripts.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);

	wp_register_style(
		__NAMESPACE__ . 'TranscriptsCSSrtl',
		plugins_url( 'srv/transcribe/metabox-transcripts-rtl.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);
}

/**
 * Register transcripts scripts.
 *
 * @return void
 */
function hook_register_transcripts_scripts() {
	wp_register_script(
		__NAMESPACE__ . 'TranscriptsJS',
		plugins_url( 'srv/transcribe/metabox-transcripts.js', webcomic( 'file' ) ),
		[ 'quicktags' ],
		webcomic( 'option.version' ),
		true
	);

	wp_register_script(
		__NAMESPACE__ . 'TranscriptsQuickEditJS',
		plugins_url( 'srv/transcribe/quick-edit-transcripts.js', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' ),
		true
	);
}

/**
 * Localize transcripts scripts.
 *
 * @return void
 */
function hook_localize_transcripts_scripts() {
	wp_localize_script(
		__NAMESPACE__ . 'TranscriptsJS',
		'webcomicTranscriptL10n',
		[
			'add'   => __( 'Add transcript', 'webcomic' ),
			'warn'  => __( "Are you sure you want to do this?\nThe transcript changes you made will be lost.", 'webcomic' ),
			'comic' => __( 'comic', 'webcomic' ),
		]
	);
}

/**
 * Enqueue transcripts meta box stylesheets.
 *
 * @return void
 */
function hook_enqueue_transcripts_styles() {
	if ( ! webcomic_collection_exists( get_current_screen()->id ) ) {
		return;
	}

	wp_enqueue_style( __NAMESPACE__ . 'TranscriptsCSS' );

	if ( is_rtl() ) {
		wp_enqueue_style( __NAMESPACE__ . 'TranscriptsCSSrtl' );
	}
}

/**
 * Enqueue transcripts meta box scripts.
 *
 * @return void
 */
function hook_enqueue_transcripts_scripts() {
	if ( ! webcomic_collection_exists( get_current_screen()->id ) || 'auto-draft' === get_post_status() ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'TranscriptsJS' );
}

/**
 * Enqueue transcripts quick edit scripts.
 *
 * @return void
 */
function hook_enqueue_quick_transcripts_scripts() {
	if ( ! preg_match( '/^edit-webcomic\d+$/', get_current_screen()->id ) ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'TranscriptsQuickEditJS' );
}

/**
 * Add the help tab.
 *
 * @return void
 */
function hook_add_box_transcripts_help() {
	$screen = get_current_screen();

	if ( ! webcomic_collection_exists( $screen->id ) ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'webcomic_transcripts',
			'title'    => __( 'Webcomic Transcripts', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/transcripts-inc-help.php';
			},
		]
	);
}

/**
 * Display the transcripts meta box.
 *
 * @param string $type The current post type.
 * @return void
 */
function hook_add_box_transcripts( string $type ) {
	if ( ! webcomic_collection_exists( $type ) ) {
		return;
	}

	add_meta_box(
		str_replace( '\\', '_', __NAMESPACE__ ) . 'Transcripts',
		__( 'Webcomic Transcripts', 'webcomic' ),
		function( $post ) {
			$args = [
				'file'        => __DIR__ . '/transcripts-inc-box.php',
				'nonce'       => __NAMESPACE__ . 'TranscriptsNonce',
				'option'      => (bool) get_post_meta( $post->ID, 'webcomic_transcribe', true ),
				'trash_nonce' => __NAMESPACE__ . 'TrashTranscriptNonce',
				'transcripts' => get_posts(
					[
						'post_type'      => 'webcomic_transcript',
						'post_parent'    => $post->ID,
						'post_status'    => 'any',
						'posts_per_page' => -1,
					]
				),
			];

			require $args['file'];
		},
		$type
	);
}

/**
 * Add the transcripts quick edit field.
 *
 * @param string $column The current column.
 * @param string $type The current post type.
 * @return void
 */
function hook_add_quick_edit_transcripts( string $column, string $type ) {
	if ( 'webcomic_media' !== $column || ! webcomic_collection_exists( $type ) ) {
		return;
	}

	$args = [
		'file'  => __DIR__ . '/transcripts-inc-quick-edit.php',
		'bulk'  => false !== strpos( current_filter(), 'bulk' ),
		'nonce' => __NAMESPACE__ . 'TranscriptsNonce',
	];

	require $args['file'];
}

/**
 * Get the default transcripts setting for new posts.
 *
 * @param mixed  $meta The meta value.
 * @param int    $id The post ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return only the first $key value.
 * @return mixed
 */
function hook_get_post_meta_default_transcripts( $meta, $id, $key, $single ) {
	if ( 'webcomic_transcribe' !== $key || 'auto-draft' !== get_post_status( $id ) ) {
		return $meta;
	}

	$post_type = get_post_type( $id );

	return webcomic( "option.{$post_type}.transcribe_comic" );
}

/**
 * Handle quick edit requests.
 *
 * @return void
 */
function hook_quick_edit_transcripts() {
	if ( ! webcomic( 'GLOBALS._REQUEST.post' ) ) {
		wp_die();
	}

	$output = (bool) get_post_meta( abs( (int) webcomic( 'GLOBALS._REQUEST.post' ) ), 'webcomic_transcribe', true );

	wp_send_json( $output );
}

/**
 * Handle new transcript form requests.
 *
 * @return void
 * @suppress PhanAccessMethodInternal - get_terms() incorrectly triggers this.
 */
function hook_get_transcript_form() {
	if ( ! webcomic( 'GLOBALS._REQUEST.parent' ) ) {
		wp_die();
	}

	$transcript = get_webcomic_transcript( abs( (int) webcomic( 'GLOBALS._REQUEST.post' ) ) );
	$parent     = abs( (int) webcomic( 'GLOBALS._REQUEST.parent' ) );
	$args       = [
		'file'           => __DIR__ . '/transcripts-inc-form.php',
		'nonce'          => __NAMESPACE__ . 'SubmitTranscriptNonce',
		'post'           => 0,
		'post_status'    => 'publish',
		'post_content'   => '',
		'post_parent'    => $parent,
		'post_media'     => get_webcomic_media( 'full', $parent ),
		'post_languages' => [],
		'form_title'     => __( 'Add new Transcript', 'webcomic' ),
		'form_submit'    => __( 'Add Transcript', 'webcomic' ),
		'languages'      => get_terms(
			[
				'taxonomy'   => 'webcomic_transcript_language',
				'hide_empty' => false,
			]
		),
	];

	if ( $transcript ) {
		$args['post']           = $transcript->ID;
		$args['post_status']    = $transcript->post_status;
		$args['post_content']   = $transcript->post_content;
		$args['post_languages'] = wp_get_object_terms(
			$transcript->ID, 'webcomic_transcript_language', [
				'fields' => 'ids',
			]
		);
		$args['form_title']     = __( 'Edit Transcript', 'webcomic' );
		$args['form_submit']    = __( 'Save Transcript', 'webcomic' );
	}

	require $args['file'];

	wp_die();
}

/**
 * Handle new transcript submission requests.
 *
 * @return void
 */
function hook_submit_transcript() {
	check_admin_referer( __NAMESPACE__ . 'SubmitTranscriptNonce', __NAMESPACE__ . 'SubmitTranscriptNonce' );

	$post_data = [
		'ID'           => abs( (int) webcomic( 'GLOBALS._REQUEST.post' ) ),
		'post_type'    => 'webcomic_transcript',
		'post_parent'  => abs( (int) webcomic( 'GLOBALS._REQUEST.post_parent' ) ),
		'post_status'  => sanitize_key( webcomic( 'GLOBALS._REQUEST.post_status' ) ),
		'post_content' => wp_kses_post( webcomic( 'GLOBALS._REQUEST.post_content' ) ),
		'tax_input'    => [
			'webcomic_transcript_language' => array_map( 'intval', explode( ',', webcomic( 'GLOBALS._REQUEST.post_languages' ) ) ),
		],
	];

	$error = util_validate_admin_transcript_data( $post_data );

	if ( $error ) {
		wp_die( esc_html( $error ) );
	} elseif ( ! $post_data['ID'] ) {
		unset( $post_data['ID'] );

		$user = wp_get_current_user();
		$post_data['meta_input']['webcomic_transcript_authors'] = [
			'name'  => $user->display_name,
			'email' => $user->user_email,
			'url'   => $user->user_url,
			'time'  => current_time( 'mysql' ),
			'ip'    => webcomic( 'GLOBALS._SERVER.REMOTE_ADDR' ),
		];
	}

	$post = wp_insert_post( $post_data, true );

	if ( is_wp_error( $post ) ) {
		// Translators: WP_Error error message.
		wp_die( sprintf( esc_html__( 'ERROR: %s', 'webcomic' ), esc_html( $post->get_error_message() ) ) );
	}

	wp_send_json( $post );
}

/**
 * Handle transcript trash requests.
 *
 * @return void
 */
function hook_trash_transcript() {
	check_admin_referer();

	$transcript = abs( (int) webcomic( 'GLOBALS._REQUEST.post' ) );

	if ( ! is_a_webcomic_transcript( $transcript ) ) {
		wp_die( esc_html__( 'ERROR: Invalid post.', 'webcomic' ) );
	} elseif ( ! current_user_can( 'delete_post', $transcript ) ) {
		wp_die( esc_html__( "ERROR: You don't have permission to trash this comic transcript.", 'webcomic' ) );
	} elseif ( ! wp_trash_post( $transcript ) ) {
		wp_die( esc_html__( 'ERROR: An unknown error occurred while trying to trash the comic transcript.', 'webcomic' ) );
	}

	$url = esc_url( wp_nonce_url( '#' ) );

	echo '<td colspan="3">';

	esc_html_e( 'Transcript moved to trash.', 'webcomic' );

	printf( " <a href='{$url}'>%s</a>", esc_html__( 'Undo', 'webcomic' ) ); // WPCS: xss ok.

	echo ' <span class="js-error"></span></td>';

	wp_die();
}

/**
 * Handle transcript untrash requests.
 *
 * @return void
 */
function hook_untrash_transcript() {
	check_admin_referer();

	$transcript = get_webcomic_transcript( abs( (int) webcomic( 'GLOBALS._REQUEST.post' ) ) );

	if ( ! $transcript ) {
		wp_die( esc_html__( 'ERROR: Invalid post.', 'webcomic' ) );
	} elseif ( ! current_user_can( 'delete_post', $transcript ) ) {
		wp_die( esc_html__( "ERROR: You don't have permission to untrash this comic transcript.", 'webcomic' ) );
	} elseif ( ! wp_untrash_post( $transcript->ID ) ) {
		wp_die( esc_html__( 'ERROR: An unknown error occurred while trying to trash the comic transcript.', 'webcomic' ) );
	}

	require __DIR__ . '/transcripts-inc-row.php';

	wp_die();
}

/**
 * Get a transcript table row.
 *
 * @return void
 */
function hook_get_transcript_row() {
	if ( ! webcomic( 'GLOBALS._REQUEST.post' ) ) {
		wp_die();
	}

	$transcript = get_webcomic_transcript( abs( (int) webcomic( 'GLOBALS._REQUEST.post' ) ) );

	if ( ! $transcript ) {
		wp_die( esc_html__( 'ERROR: Invalid post.', 'webcomic' ) );
	} elseif ( ! current_user_can( 'read_post', $transcript ) ) {
		wp_die( esc_html__( "ERROR: You don't have permission to view this comic transcript.", 'webcomic' ) );
	}

	require __DIR__ . '/transcripts-inc-row.php';

	wp_die();
}

/* ===== Collection Hooks =================================================== */

/**
 * Update webcomic_transcribe meta data.
 *
 * @param int $id The post to update.
 * @return void
 */
function hook_update_post_meta_transcribe( int $id ) {
	if ( ! wp_verify_nonce( sanitize_key( webcomic( 'GLOBALS._REQUEST.' . __NAMESPACE__ . 'TranscriptsNonce' ) ), __NAMESPACE__ . 'TranscriptsNonce' ) ) {
		return;
	} elseif ( ! webcomic( 'GLOBALS._REQUEST.webcomic_transcribe' ) ) {
		delete_post_meta( $id, 'webcomic_transcribe' );

		return;
	}

	$transcribe = abs( (int) webcomic( 'GLOBALS._REQUEST.webcomic_transcribe' ) );

	if ( ! $transcribe ) {
		return;
	}

	update_post_meta( $id, 'webcomic_transcribe', true );
}

/* ===== Utility Functions ================================================== */

/**
 * Validate submitted transcript data.
 *
 * @param array $transcript The transcript data to validate.
 * @return string
 */
function util_validate_admin_transcript_data( array $transcript ) : string {
	if ( $transcript['ID'] && ! is_a_webcomic_transcript( $transcript['ID'] ) ) {
		return __( 'ERROR: Invalid comic transcript.', 'webcomic' );
	} elseif ( $transcript['ID'] && ! current_user_can( 'edit_post', $transcript['ID'] ) ) {
		return __( "ERROR: You don't have permission to edit this comic transcript.", 'webcomic' );
	} elseif ( ! $transcript['post_parent'] || ! is_a_webcomic( $transcript['post_parent'] ) ) {
		return __( 'ERROR: Invalid comic.', 'webcomic' );
	} elseif ( 'publish' === $transcript['post_status'] && ! current_user_can( 'publish_posts' ) ) {
		return __( "ERROR: You don't have permission to publish comic transcripts.", 'webcomic' );
	} elseif ( ! in_array( $transcript['post_status'], [ 'private', 'publish', 'pending', 'draft' ], true ) ) {
		return __( 'ERROR: Invalid post status.', 'webcomic' );
	} elseif ( ! $transcript['post_content'] ) {
		return __( 'ERROR: Please type a transcript.', 'webcomic' );
	}

	return '';
}

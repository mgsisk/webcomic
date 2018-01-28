<?php
/**
 * Transcribe custom post type functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe;

use WP;

/**
 * Add custom post type hooks.
 *
 * @return void
 */
function custom() {
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_transcribe_styles' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_transcribe_scripts' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_transcribe_styles' );
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_transcribe_parent_script' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_list_help_sidebar' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_list_help' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_list_moderation_help' );
	add_filter( 'parse_request', __NAMESPACE__ . '\hook_filter_posts_by_ip' );
	add_filter( 'parse_request', __NAMESPACE__ . '\hook_filter_posts_by_parent' );
	add_filter( 'parse_request', __NAMESPACE__ . '\hook_filter_posts_by_collection' );
	add_filter( 'parse_request', __NAMESPACE__ . '\hook_filter_posts_by_orphans' );
	add_filter( 'parse_request', __NAMESPACE__ . '\hook_sort_posts_by_authors' );
	add_filter( 'list_table_primary_column', __NAMESPACE__ . '\hook_set_primary_column', 10, 2 );
	add_filter( 'manage_webcomic_transcript_posts_columns', __NAMESPACE__ . '\hook_add_meta_columns' );
	add_filter( 'manage_edit-webcomic_transcript_sortable_columns', __NAMESPACE__ . '\hook_add_sortable_columns' );
	add_filter( 'manage_webcomic_transcript_posts_custom_column', __NAMESPACE__ . '\hook_display_content_column', 10, 2 );
	add_filter( 'manage_webcomic_transcript_posts_custom_column', __NAMESPACE__ . '\hook_display_parent_column', 10, 2 );
	add_filter( 'manage_webcomic_transcript_posts_custom_column', __NAMESPACE__ . '\hook_display_authors_column', 10, 2 );
	add_filter( 'restrict_manage_posts', __NAMESPACE__ . '\hook_add_transcript_collection_restriction' );
	add_filter( 'restrict_manage_posts', __NAMESPACE__ . '\hook_add_ip_field' );
	add_filter( 'restrict_manage_posts', __NAMESPACE__ . '\hook_add_parent_field' );
	add_filter( 'restrict_manage_posts', __NAMESPACE__ . '\hook_add_orphaned_field' );
	add_filter( 'restrict_manage_posts', __NAMESPACE__ . '\hook_add_authors_field' );
	add_filter( 'views_edit-webcomic_transcript', __NAMESPACE__ . '\hook_add_orphans_view' );
	add_filter( 'bulk_post_updated_messages', __NAMESPACE__ . '\hook_bulk_transcript_messages', 10, 2 );
}

/**
 * Register transcribe stylesheets.
 *
 * @return void
 */
function hook_register_transcribe_styles() {
	wp_register_style(
		__NAMESPACE__ . 'ListCSS',
		plugins_url( 'srv/transcribe/list.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);

	wp_register_style(
		__NAMESPACE__ . 'ListCSSrtl',
		plugins_url( 'srv/transcribe/list-rtl.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);

	wp_register_style(
		__NAMESPACE__ . 'AuthorColumnCSS',
		plugins_url( 'srv/transcribe/column-authors.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);

	wp_register_style(
		__NAMESPACE__ . 'AuthorColumnCSSrtl',
		plugins_url( 'srv/transcribe/column-authors-rtl.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);

	wp_register_style(
		__NAMESPACE__ . 'ParentColumnCSS',
		plugins_url( 'srv/transcribe/column-comic.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);

	wp_register_style(
		__NAMESPACE__ . 'ParentColumnCSSrtl',
		plugins_url( 'srv/transcribe/column-comic-rtl.css', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' )
	);
}

/**
 * Register transcribe scripts.
 *
 * @return void
 */
function hook_register_transcribe_scripts() {
	wp_register_script(
		__NAMESPACE__ . 'ParentColumnJS',
		plugins_url( 'srv/transcribe/column-comic.js', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' ),
		true
	);
}

/**
 * Enqueue transcribe stylesheets.
 *
 * @return void
 */
function hook_enqueue_transcribe_styles() {
	if ( 'edit-webcomic_transcript' !== get_current_screen()->id ) {
		return;
	}

	wp_enqueue_style( __NAMESPACE__ . 'ListCSS' );
	wp_enqueue_style( __NAMESPACE__ . 'AuthorColumnCSS' );
	wp_enqueue_style( __NAMESPACE__ . 'ParentColumnCSS' );

	if ( is_rtl() ) {
		wp_enqueue_style( __NAMESPACE__ . 'ListCSSrtl' );
		wp_enqueue_style( __NAMESPACE__ . 'AuthorColumnCSSrtl' );
		wp_enqueue_style( __NAMESPACE__ . 'ParentColumnCSSrtl' );
	}
}

/**
 * Enqueue parent column script.
 *
 * @return void
 */
function hook_enqueue_transcribe_parent_script() {
	if ( 'edit-webcomic_transcript' !== get_current_screen()->id ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'ParentColumnJS' );
}

/**
 * Add the help sidebar.
 *
 * @return void
 */
function hook_add_list_help_sidebar() {
	$screen = get_current_screen();

	if ( false === strpos( $screen->id, 'webcomic_transcript' ) ) {
		return;
	}

	$screen->set_help_sidebar( webcomic_help() );
}

/**
 * Add the transcripts list help.
 *
 * @return void
 */
function hook_add_list_help() {
	$screen = get_current_screen();

	if ( 'edit-webcomic_transcript' !== $screen->id ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'overview',
			'title'    => __( 'Overview', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/custom-inc-list-help-overview.php';
			},
		]
	);
}

/**
 * Add the transcripts moderation list help.
 *
 * @return void
 */
function hook_add_list_moderation_help() {
	$screen = get_current_screen();

	if ( 'edit-webcomic_transcript' !== $screen->id ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'moderation',
			'title'    => __( 'Moderation', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/custom-inc-list-help-moderation.php';
			},
		]
	);
}

/**
 * Filter the transcripts list admin page to show transcripts by author IP.
 *
 * @param WP $request A list of request paramters.
 * @return object
 */
function hook_filter_posts_by_ip( WP $request ) : WP {
	if ( empty( $request->query_vars['post_type'] ) || 'webcomic_transcript' !== $request->query_vars['post_type'] || ! webcomic( 'GLOBALS._REQUEST.ip' ) ) {
		return $request;
	}

	$request->query_vars['meta_query'][] = [
		'key'     => 'webcomic_transcript_authors',
		'value'   => sprintf( '"ip";s:12:"%s";', filter_var( webcomic( 'GLOBALS._REQUEST.ip' ), FILTER_VALIDATE_IP ) ),
		'compare' => 'LIKE',
	];

	return $request;
}

/**
 * Filter the transcripts list admin page to show only child transcripts.
 *
 * @param WP $request A list of request paramters.
 * @return object
 */
function hook_filter_posts_by_parent( WP $request ) : WP {
	if ( empty( $request->query_vars['post_type'] ) || 'webcomic_transcript' !== $request->query_vars['post_type'] || ! webcomic( 'GLOBALS._REQUEST.post_parent' ) ) {
		return $request;
	}

	$request->query_vars['post_parent'] = abs( (int) webcomic( 'GLOBALS._REQUEST.post_parent' ) );

	return $request;
}

/**
 * Filter the transcripts list to show only transcripts for a collection.
 *
 * @param WP $request A list of request paramters.
 * @return object
 */
function hook_filter_posts_by_collection( WP $request ) : WP {
	if ( empty( $request->query_vars['post_type'] ) || 'webcomic_transcript' !== $request->query_vars['post_type'] || ! webcomic_collection_exists( (string) webcomic( 'GLOBALS._REQUEST.post_parent_type' ) ) ) {
		return $request;
	}

	$request->query_vars['post_parent__in'] = get_posts(
		[
			'fields'      => 'ids',
			'post_type'   => webcomic( 'GLOBALS._REQUEST.post_parent_type' ),
			'post_status' => 'any',
		]
	);

	return $request;
}

/**
 * Filter the transcripts list admin page to show only orphans.
 *
 * @param WP $request A list of request paramters.
 * @return object
 */
function hook_filter_posts_by_orphans( WP $request ) {
	if ( empty( $request->query_vars['post_type'] ) || 'webcomic_transcript' !== $request->query_vars['post_type'] || ! webcomic( 'GLOBALS._REQUEST.webcomic_transcript_orphans' ) ) {
		return $request;
	}

	$request->query_vars['post_parent'] = 0;

	return $request;
}

/**
 * Sort the transcripts list admin page by authors.
 *
 * @param WP $request A list of request paramters.
 * @return object
 */
function hook_sort_posts_by_authors( WP $request ) {
	if ( empty( $request->query_vars['post_type'] ) || 'webcomic_transcript' !== $request->query_vars['post_type'] || ! webcomic( 'GLOBALS._REQUEST.authors' ) ) {
		return $request;
	}

	$request->query_vars['meta_key'] = 'webcomic_transcript_authors';
	$request->query_vars['orderby']  = 'meta_value';

	return $request;
}

/**
 * Set the primary column for transcripts list.
 *
 * @param string $default The current primary column.
 * @param string $screen The current screen ID.
 * @return string
 */
function hook_set_primary_column( string $default, string $screen ) : string {
	if ( 'edit-webcomic_transcript' !== $screen ) {
		return $default;
	}

	return 'webcomic_transcript';
}

/**
 * Add the meta columns to the transcripts list admin page.
 *
 * @param array $columns The posts list columns.
 * @return array
 */
function hook_add_meta_columns( array $columns ) : array {
	unset( $columns['title'] );

	return array_slice( $columns, 0, 1 ) + [
		'webcomic_transcript_comic'   => __( 'Comic', 'webcomic' ),
		'webcomic_transcript'         => __( 'Transcript', 'webcomic' ),
		'webcomic_transcript_authors' => __( 'Authors', 'webcomic' ),
	] + array_slice( $columns, 1 );
}

/**
 * Add post_parent to the sortable columns for the transcripts list admin page.
 *
 * @param array $columns The posts list sortable columns.
 * @return array
 */
function hook_add_sortable_columns( array $columns ) : array {
	$columns['webcomic_transcript_comic']   = 'post_parent';
	$columns['webcomic_transcript_authors'] = 'authors';

	return $columns;
}

/**
 * Display the webcomic_transcript column.
 *
 * @param string $column The column currently being displayed.
 * @param int    $post The current post ID.
 * @return void
 */
function hook_display_content_column( string $column, int $post ) {
	if ( 'webcomic_transcript' !== $column ) {
		return;
	}

	$status = get_post_status( $post );

	if ( 'publish' !== $status ) {
		$stati = get_post_stati( [], 'objects' );

		if ( empty( $stati[ $status ] ) ) {
			$stati[ $status ] = (object) [
				'label' => $status,
			];
		}

		echo '<p class="screen-reader-text">' . esc_html( $stati[ $status ]->label ) . '</p>';
	}

	echo wp_kses_post( apply_filters( 'the_content', get_post_field( 'post_content', $post ) ) );

	get_inline_data( get_webcomic_transcript( $post ) );
}

/**
 * Display the webcomic_transcript_comic column.
 *
 * @param string $column The column currently being displayed.
 * @param int    $post The current post ID.
 * @return void
 */
function hook_display_parent_column( string $column, int $post ) {
	if ( 'webcomic_transcript_comic' !== $column ) {
		return;
	}

	$output = '&mdash;';
	$parent = wp_get_post_parent_id( $post );

	if ( $parent && get_post_type( $parent ) ) {
		$title  = esc_html( get_the_title( $parent ) );
		$output = "<strong>{$title}</strong>";

		if ( current_user_can( 'edit_post', $parent ) ) {
			$url    = esc_url(
				add_query_arg(
					[
						'post'   => $parent,
						'action' => 'edit',
					],
					admin_url( 'post.php' )
				)
			);
			$output = "<a href='{$url}' class='comments-edit-item-link'>{$title}</a>";
		}

		$comic = [];

		foreach ( get_post_meta( $parent, 'webcomic_media' ) as $media ) {
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

			$comic[] = $image;
		}

		$url     = esc_url( get_permalink( $parent ) );
		$output  = implode( '', $comic ) . $output;
		$output .= sprintf( "<a href='{$url}' class='comments-view-item-link'>%s</a>", __( 'View Comic', 'webcomic' ) );
		$output .= "<span class='post-com-count-wrapper'>" . util_transcripts_bubble( $parent ) . '</span>';
	}

	echo "<div class='response-links' data-comic='{$parent}'>{$output}</div>"; // WPCS: xss ok.
}

/**
 * Display the authors column.
 *
 * @param string $column The column currently being displayed.
 * @param int    $post The current post ID.
 * @return void
 */
function hook_display_authors_column( string $column, int $post ) {
	if ( 'webcomic_transcript_authors' !== $column ) {
		return;
	}

	$output  = '&mdash;';
	$authors = get_webcomic_transcript_authors(
		[
			'hide_duplicates' => '',
			'post'            => $post,
		]
	);

	if ( $authors ) {
		$avatar = get_avatar( $authors[0]['email'], 32, '', $authors[0]['name'] );
		$output = "<strong>{$avatar} {$authors[0]['name']}</strong>";

		if ( $authors[0]['url'] ) {
			$output .= "<br><a href='{$authors[0]['url']}'>{$authors[0]['url']}</a>";
		}

		if ( $authors[0]['email'] ) {
			$output .= "<br><a href='mailto:{$authors[0]['email']}'>{$authors[0]['email']}</a>";
		}

		$url     = esc_url(
			add_query_arg(
				[
					'post_type' => 'webcomic_transcript',
					'ip'        => $authors[0]['ip'],
				], admin_url( 'edit.php' )
			)
		);
		$output .= "<br><a href='{$url}'>{$authors[0]['ip']}</a>";

		if ( 1 < count( $authors ) ) {
			$url = esc_url(
				add_query_arg(
					[
						'post'   => $post,
						'action' => 'edit',
					], admin_url( 'post.php' )
				) . '#' . str_replace( '\\', '_', __NAMESPACE__ ) . '_MetaBoxAuthors'
			);
			// Translators: The number of additional transcript authors.
			$label   = sprintf( _n( '%s More Author', '%s More Authors', count( $authors ) - 1, 'webcomic' ), number_format_i18n( count( $authors ) - 1 ) );
			$output .= "<br><a href='{$url}'><span class='dashicons dashicons-groups' aria-hidden='true'></span> {$label}</a>";
		}
	}

	echo $output; // WPCS: xss ok.
}


/**
 * Add a hidden orphans field for transcripts list admin page navigation.
 *
 * @param string $type The current post type.
 * @return void
 */
function hook_add_transcript_collection_restriction( string $type ) {
	if ( 'webcomic_transcript' !== $type ) {
		return;
	}

	$all = esc_html__( 'All Collections', 'webcomic' );

	webcomic_collections_list(
		[
			'format'     => "<select name='post_parent_type'><option value=''>{$all}</option>{{}}</select>",
			'hide_empty' => false,
		]
	);
}

/**
 * Add a hidden IP field for transcripts list admin page navigation.
 *
 * @param string $type The current post type.
 * @return void
 */
function hook_add_ip_field( string $type ) {
	if ( 'webcomic_transcript' !== $type || ! webcomic( 'GLOBALS._REQUEST.ip' ) ) {
		return;
	}

	$ip = esc_attr( filter_var( webcomic( 'GLOBALS._REQUEST.ip' ), FILTER_VALIDATE_IP ) );

	echo "<input type='hidden' name='ip' value='{$ip}'>"; // WPCS: xss ok.
}

/**
 * Add a hidden post_parent field for transcripts list admin page navigation.
 *
 * @param string $type The current post type.
 * @return void
 */
function hook_add_parent_field( string $type ) {
	if ( 'webcomic_transcript' !== $type || ! webcomic( 'GLOBALS._REQUEST.post_parent' ) ) {
		return;
	}

	$post = esc_attr( abs( (int) webcomic( 'GLOBALS._REQUEST.post_parent' ) ) );

	echo "<input type='hidden' name='post_parent' value='{$post}'>"; // WPCS: xss ok.
}

/**
 * Add a hidden orphans field for transcripts list admin page navigation.
 *
 * @param string $type The current post type.
 * @return void
 */
function hook_add_orphaned_field( string $type ) {
	if ( 'webcomic_transcript' !== $type || ! webcomic( 'GLOBALS._REQUEST.webcomic_transcript_orphans' ) ) {
		return;
	}

	echo '<input type="hidden" name="webcomic_transcript_orphans" vlaue="1">';
}

/**
 * Add a hidden orphans field for transcripts list admin page navigation.
 *
 * @param string $type The current post type.
 * @return void
 */
function hook_add_authors_field( string $type ) {
	if ( 'webcomic_transcript' !== $type || ! webcomic( 'GLOBALS._REQUEST.authors' ) ) {
		return;
	}

	echo '<input type="hidden" name="authors" vlaue="1">';
}

/**
 * Add an orphaned transcripts view to the transcripts list admin page.
 *
 * @param array $views The current view links.
 * @return array
 */
function hook_add_orphans_view( array $views ) : array {
	$class  = '';
	$screen = get_current_screen();
	$posts  = get_posts(
		[
			'fields'         => 'ids',
			'post_type'      => $screen->post_type,
			'post_parent'    => 0,
			'post_status'    => 'any',
			'posts_per_page' => -1,
		]
	);

	if ( ! $posts ) {
		return $views;
	} elseif ( webcomic( 'GLOBALS._REQUEST.webcomic_transcript_orphans' ) ) {
		$class = 'current';
	}

	$url   = esc_url(
		add_query_arg(
			[
				'post_type'                   => $screen->post_type,
				'post_status'                 => 'any',
				'webcomic_transcript_orphans' => true,
			],
			admin_url( 'edit.php' )
		)
	);
	$count = count( $posts );

	$views['webcomic_transcript_orphans'] = sprintf( "<a href='{$url}' class='{$class}'>%s <span class='count'>({$count})</span></a>", __( 'Orphaned', 'webcomic' ) );

	return $views;
}

/**
 * Update bulk messages for comic transcripts.
 *
 * @param array $messages The list of bulk messages.
 * @param array $counts The bulk edited counts.
 * @return array
 */
function hook_bulk_transcript_messages( array $messages, array $counts ) {
	if ( 'edit-webcomic_transcript' !== get_current_screen()->id ) {
		return $messages;
	}

	$messages['post'] = [
		// Translators: The number of transcripts updated.
		'updated'   => _n( '%s transcript updated.', '%s transcripts updated.', $counts['updated'], 'webcomic' ),
		// Translators: The number of transcripts not updated.
		'locked'    => _n( '%s transcript not updated, somebody is editing it.', '%s transcripts not updated, somebody is editing them.', $counts['locked'], 'webcomic' ),
		// Translators: The number of transcripts permanently deleted.
		'deleted'   => _n( '%s transcript permanently deleted.', '%s transcripts permanently deleted.', $counts['deleted'], 'webcomic' ),
		// Translators: The number of transcripts moved to the Trash.
		'trashed'   => _n( '%s transcript moved to the Trash.', '%s transcripts moved to the Trash.', $counts['trashed'], 'webcomic' ),
		// Translators: The number of transcripts restored from the Trash..
		'untrashed' => _n( '%s transcript restored from the Trash.', '%s transcripts restored from the Trash.', $counts['untrashed'], 'webcomic' ),
	];

	return $messages;
}

/* ===== Utility Functions ================================================== */

/**
 * Construct transcript count bubble output.
 *
 * @param int $post The post to get transcript counts for.
 * @return string
 * @internal For hook_display_parent_column().
 */
function util_transcripts_bubble( int $post ) : string {
	$publish        = (int) get_post_meta( $post, 'webcomic_transcribe_count_publish', true );
	$pending        = (int) get_post_meta( $post, 'webcomic_transcribe_count_pending', true );
	$drafted        = (int) get_post_meta( $post, 'webcomic_transcribe_count_draft', true );
	$publish_number = number_format_i18n( $publish );
	$pending_number = number_format_i18n( $pending );
	$drafted_number = number_format_i18n( $drafted );

	$output = sprintf(
		'<span aria-hidden="true">&mdash;</span><span class="screen-reader-text">%s</span>',
		__( 'No transcripts', 'webcomic' )
	);

	if ( $publish ) {
		$output = sprintf(
			'<a href="%s" class="post-com-count post-com-count-approved"><span class="comment-count-approved" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a>',
			esc_url( util_transcripts_bubble_url( $post, 'publish' ) ),
			$publish_number,
			// Translators: The number of published transcripts.
			sprintf( _n( '%s published transcript', '%s published transcripts', $publish, 'webcomic' ), $publish_number )
		);
	} elseif ( $drafted || $pending ) {
		$output = sprintf(
			'<span class="post-com-count post-com-count-no-comments"><span class="comment-count comment-count-no-comments" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></span>',
			$publish_number,
			__( 'No published transcripts', 'webcomic' )
		);
	}

	if ( $drafted ) {
		$output .= sprintf(
			'<a href="%s" class="post-com-count post-com-count-pending"><span class="comment-count-pending" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a>',
			esc_url( util_transcripts_bubble_url( $post, 'draft' ) ),
			$drafted_number,
			// Translators: The number of draft transcripts.
			sprintf( _n( '%s draft transcript', '%s draft transcripts', $drafted, 'webcomic' ), $drafted_number )
		);
	} elseif ( $publish || $pending ) {
		$output .= sprintf(
			'<span class="post-com-count post-com-count-pending post-com-count-no-pending"><span class="comment-count comment-count-no-pending" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></span>',
			$drafted_number,
			__( 'No draft transcripts', 'webcomic' )
		);
	}

	if ( $pending ) {
		$output .= sprintf(
			'<a href="%s" class="post-com-count post-com-count-pending transcript"><span class="comment-count-pending" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a>',
			esc_url( util_transcripts_bubble_url( $post, 'pending' ) ),
			$pending_number,
			// Translators: The number of pending transcripts.
			sprintf( _n( '%s pending transcript', '%s pending transcripts', $pending, 'webcomic' ), $pending_number )
		);
	} elseif ( $publish || $drafted ) {
		$output .= sprintf(
			'<span class="post-com-count post-com-count-pending post-com-count-no-pending"><span class="comment-count comment-count-no-pending" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></span>',
			$pending_number,
			__( 'No pending transcripts', 'webcomic' )
		);
	}

	return $output;
}

/**
 * Get a transcript bubble URL.
 *
 * @param int    $parent The post parent.
 * @param string $status The URL status.
 * @return string
 * @internal For util_transcripts_bubble().
 */
function util_transcripts_bubble_url( int $parent, string $status ) : string {
	return add_query_arg(
		[
			'post_type'   => 'webcomic_transcript',
			'post_parent' => $parent,
			'post_status' => $status,
		], admin_url( 'edit.php' )
	);
}

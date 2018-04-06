<?php
/**
 * Transcribe filters
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe;

use WP_Post;

/**
 * Add filters.
 *
 * @return void
 */
function filters() {
	add_filter( 'get_webcomic_transcript_author_link_tokens', __NAMESPACE__ . '\hook_get_webcomic_transcript_author_link_tokens', 0, 3 );
	add_filter( 'get_webcomic_transcript_authors_list_args', __NAMESPACE__ . '\hook_get_webcomic_transcript_authors_list_args', 0, 2 );
	add_filter( 'get_webcomic_transcript_form_args', __NAMESPACE__ . '\hook_get_webcomic_transcript_form_args', 0 );
	add_filter( 'get_webcomic_transcript_form_fields', __NAMESPACE__ . '\hook_get_webcomic_transcript_form_fields', 0, 3 );
	add_filter( 'get_webcomic_transcripts_list_item_tokens', __NAMESPACE__ . '\hook_get_webcomic_transcripts_list_item_tokens', 0, 4 );
	add_filter( 'get_webcomic_transcript_terms_args', __NAMESPACE__ . '\hook_get_webcomic_transcript_terms_args', 0 );
	add_filter( 'get_webcomic_transcript_terms_args', __NAMESPACE__ . '\hook_get_webcomic_transcript_terms_args_related', 0 );
	add_filter( 'get_webcomic_transcript_terms_list_args', __NAMESPACE__ . '\hook_get_webcomic_transcript_terms_list_args', 0, 2 );
	add_filter( 'get_webcomic_transcripts_list_args', __NAMESPACE__ . '\hook_get_webcomic_transcripts_list_args', 0, 2 );
	add_filter( 'get_webcomic_url', __NAMESPACE__ . '\hook_get_webcomic_url_transcribe', 10, 4 );
	add_filter( 'get_webcomic_url', __NAMESPACE__ . '\hook_get_webcomic_url_transcripts', 10, 4 );
	add_filter( 'get_webcomic_link_class', __NAMESPACE__ . '\hook_get_webcomic_link_class_transcribe', 10, 3 );
	add_filter( 'get_webcomic_link_class', __NAMESPACE__ . '\hook_get_webcomic_link_class_transcripts', 10, 3 );
}

/**
 * Handle default webcomic transcript author link tokens.
 *
 * @param array  $tokens The token values.
 * @param string $link The link text to search for tokens.
 * @param array  $author The transcript author the link is for.
 * @return array
 */
function hook_get_webcomic_transcript_author_link_tokens( array $tokens, string $link, array $author ) : array {
	if ( ! preg_match( '/%\S/', $link ) ) {
		return $tokens;
	}

	if ( false !== strpos( $link, '%name' ) ) {
		$tokens['%name'] = $author['name'];
	}

	if ( false !== strpos( $link, '%url' ) ) {
		$tokens['%url'] = $author['url'];
	}

	if ( preg_match( '/%avatar(?:-(\d+))?/', $link, $match ) ) {
		$match += [ 96 ];

		$tokens[ $match[0] ] = get_avatar( $author['email'], (int) $match[1] );
	}

	return $tokens;
}

/**
 * Set default arguments for get_webcomic_transcript_authors_list().
 *
 * @param array $args The arguments to filter.
 * @param array $authors The authors included in the list.
 * @return array
 */
function hook_get_webcomic_transcript_authors_list_args( array $args, array $authors ) : array {
	$args = [
		'options' => false,
	] + $args + [
		'format'   => ', ',
		'link_rel' => 'external nofollow',
		'link'     => '%name',
		'walker'   => '',
	];

	if ( preg_match( '/<(?:select|optgroup).*?>.*{{.*}}/', $args['format'] ) ) {
		$args['options'] = true;
	}

	if ( ! class_exists( $args['walker'] ) ) {
		$args['walker'] = __NAMESPACE__ . '\Walker\AuthorLister';
	}

	return $args;
}

/**
 * Set default arguments for get_webcomic_transcript_form().
 *
 * @param array $args The arguments to filter.
 * @return array
 */
function hook_get_webcomic_transcript_form_args( array $args ) : array {
	$required      = '';
	$required_attr = '';
	$required_text = '';

	if ( 'name_email' === webcomic( "option.{$args['post_parent']->post_type}.transcribe_require" ) ) {
		$required      = ' <span class="required">*</span>';
		$required_attr = ' aria-required="true" required';
		// Translators: Required indicator.
		$required_text = sprintf( __( 'Required fields are marked %s', 'webcomic' ), '<span class="required">*</span>' );
	}

	$args += [
		'cancel_link'             => '<small>' . __( 'Cancel', 'webcomic' ) . '</small>',
		'cancel_link_hash'        => '#',
		'fields'                  => [],
		'form'                    => '<form method="post" action="%s" id="webcomic-transcript-form" class="webcomic-transcript-form" novalidate>{{%fields}}</form>',
		'format'                  => '<div id="webcomic-transcribe" class="webcomic-transcribe">{{<h3 class="webcomic-transcript-form-title">' . esc_html__( 'Transcribe Comic', 'webcomic' ) . ' %cancel-link</h3>%form}}</div>',
		'logged_in_as'            => '<p class="logged-in-as"><a href="%1$s" aria-label="%2$s">' . esc_html__( 'Logged in as', 'webcomic' ) . ' %3$s</a>. <a href="%4$s">' . esc_html__( 'Log out?', 'webcomic' ) . '</a></p>',
		// Translators: Login URL.
		'must_log_in'             => "<p class='must-log-in'>" . __( 'You must be <a href="%s">logged in</a> to transcribe.', 'webcomic' ) . '</p>',
		'post'                    => (int) get_query_var( 'transcribe', 0 ),
		'transcript_notes_after'  => '',
		'transcript_notes_before' => '<p class="webcomic-transcript-notes"><span id="webcomic-transcript-email-notes">' . __( 'Your email address will not be published.', 'webcomic' ) . "</span> {$required_text}</p>",
	];

	$args['fields'] += [
		'transcript' => "<p class='webcomic-transcript-form-transcript'><label for='webcomic_transcript'>" . __( 'Transcript', 'webcomic' ) . "</label> <textarea id='webcomic_transcript' name='webcomic_transcript' cols='45' rows='8' maxlength='65525' aria-required='true' required>%s</textarea></p>",
		'languages'  => "<p class='webcomic-transcript-form-language'><label for='webcomic_transcript_language'>" . __( 'Language', 'webcomic' ) . "</label> <select id='webcomic_transcript_language' name='webcomic_transcript_language[]'><option value=''></option>{{}}</select></p>",
		'author'     => "<p class='webcomic-transcript-form-author'><label for='webcomic_transcript_author'>" . __( 'Name', 'webcomic' ) . "{$required}</label> <input type='text' id='webcomic_transcript_author' name='webcomic_transcript_author' value='%s' size='30' maxlength='245'{$required_attr}></p>",
		'email'      => "<p class='webcomic-transcript-form-author-email'><label for='webcomic_transcript_author_email'>" . __( 'Email', 'webcomic' ) . "{$required}</label> <input type='text' id='webcomic_transcript_author_email' name='webcomic_transcript_author_email' value='%s' size='30' maxlength='100' aria-describedby='webcomic-transcript-email-notes'{$required_attr}></p>",
		'url'        => "<p class='webcomic-transcript-form-author-url'><label for='webcomic_transcript_author_url'>" . __( 'Website', 'webcomic' ) . "</label> <input type='url' id='webcomic_transcript_author_url' name='webcomic_transcript_author_url' value='%s' size='30' maxlength='200'></p>",
		'submit'     => "<p class='form-submit'><button type='submit'>" . esc_html__( 'Submit Transcript', 'webcomic' ) . '</button></p>',
		'id'         => "<input type='hidden' name='webcomic_transcript_id' value='%s'>",
		'parent'     => "<input type='hidden' name='webcomic_transcript_parent' value='%s'>",
		'nonce'      => wp_nonce_field( __NAMESPACE__ . 'Nonce', __NAMESPACE__ . 'Nonce', true, false ),
	];

	return $args;
}

/**
 * Handle default field filtering for get_webcomic_transcript_form().
 *
 * @param array $fields The fields to filter.
 * @param array $args The get_webcomic_transcript_form() arguments.
 * @param array $commenter The current commenter.
 * @return array
 * @codingStandardsIgnoreStart Generic.Metrics.CyclomaticComplexity.TooHigh - We're purposely using a lot of conditionals here to process all default fields in one pass.
 */
function hook_get_webcomic_transcript_form_fields( array $fields, array $args, array $commenter ) : array {
	if ( is_user_logged_in() ) {
		unset( $fields['author'], $fields['email'], $fields['url'] );
	}

	foreach ( $fields as $name => $field ) {
		if ( ! $field ) {
			continue;
		} elseif ( 'transcript' === $name ) {
			$content = '';

			if ( $args['post'] ) {
				$content = get_post_field( 'post_content', $args['post'], 'edit' );
			}

			$fields[ $name ] = sprintf( $field, esc_textarea( $content ) ) . $args['transcript_notes_after'];
		} elseif ( 'languages' === $name ) {
			$languages_args = [
				'format'     => $field,
				'hide_empty' => false,
			];

			if ( $args['post'] ) {
				$languages_args['current'] = get_webcomic_transcript_languages(
					[
						'fields'     => 'ids',
						'object_ids' => $args['post'],
					]
				);
			}

			$fields[ $name ] = get_webcomic_transcript_languages_list( $languages_args );
		} elseif ( 'author' === $name ) {
			$fields[ $name ] = sprintf( $field, esc_attr( $commenter['comment_author'] ) );
		} elseif ( 'email' === $name ) {
			$fields[ $name ] = sprintf( $field, esc_attr( $commenter['comment_author_email'] ) );
		} elseif ( 'url' === $name ) {
			$fields[ $name ] = sprintf( $field, esc_attr( $commenter['comment_author_url'] ) );
		} elseif ( 'id' === $name ) {
			$fields[ $name ] = sprintf( $field, esc_attr( $args['post'] ) );
		} elseif ( 'parent' === $name ) {
			$fields[ $name ] = sprintf( $field, esc_attr( $args['post_parent']->ID ) );
		}
	}

	return $fields;
}// @codingStandardsIgnoreEnd Generic.Metrics.CyclomaticComplexity.TooHigh

/**
 * Handle default webcomic transcript list item tokens.
 *
 * @param array   $tokens The token values.
 * @param string  $item The list item text to search for tokens.
 * @param WP_Post $transcript The transcript the list item is for.
 * @param array   $args The transcript list arguments.
 * @return array
 * @SuppressWarnings(PHPMD.NPathComplexity) - We're purposely using a lot of conditionals here to avoid executing functions we don't need to.
 * @codingStandardsIgnoreStart Generic.Metrics.CyclomaticComplexity.TooHigh - We're purposely using a lot of conditionals here to avoid executing functions we don't need to.
 */
function hook_get_webcomic_transcripts_list_item_tokens( array $tokens, string $item, WP_Post $transcript, array $args ) : array {
	if ( ! preg_match( '/%\S/', $item ) ) {
		return $tokens;
	}

	if ( false !== strpos( $item, '%authors' ) ) {
		$tokens['%authors'] = get_webcomic_transcript_authors_list(
			[
				'post' => $transcript->ID,
			] + $args['authors_list']
		);
	}

	if ( false !== strpos( $item, '%content' ) ) {
		webcomic_setup_postdata( $transcript );

		$tokens['%content'] = apply_filters( 'the_content', $transcript->post_content );

		webcomic_reset_postdata();
	}

	if ( false !== strpos( $item, '%date' ) ) {
		$tokens['%date'] = get_the_date( '', $transcript->ID );
	}

	if ( false !== strpos( $item, '%edit-link' ) ) {
		$tokens['%edit-link'] = get_webcomic_link(
			$args['edit_link'], $transcript->post_parent, [
				'transcribe' => $transcript->ID,
			]
		);
	}

	if ( false !== strpos( $item, '%languages' ) ) {
		$tokens['%languages'] = get_webcomic_transcript_languages_list(
			[
				'object_ids' => $transcript->ID,
			] + $args['languages_list']
		);
	}

	if ( false !== strpos( $item, '%parent-link' ) ) {
		$tokens['%parent-link'] = get_webcomic_link( $args['parent_link'], $transcript->post_parent );
	}

	if ( false !== strpos( $item, '%parent-title' ) ) {
		$tokens['%parent-title'] = get_the_title( $transcript->post_parent );
	}

	if ( false !== strpos( $item, '%time' ) ) {
		$tokens['%time'] = get_the_date( get_option( 'time_format' ), $transcript->ID );
	}

	return $tokens;
}// @codingStandardsIgnoreEnd Generic.Metrics.CyclomaticComplexity.TooHigh

/**
 * Handle type arguments for get_webcomic_transcript_terms().
 *
 * @param array $args The get_webcomic_transcript_terms() arguments.
 * @return mixed
 */
function hook_get_webcomic_transcript_terms_args( array $args ) {
	if ( empty( $args['type'] ) ) {
		return $args;
	}

	$args['taxonomy'] = "webcomic_transcript_{$args['type']}";

	return $args;
}

/**
 * Handle related arguments for get_webcomic_transcript_terms().
 *
 * @param array $args The get_webcomic_transcript_terms() arguments.
 * @return mixed
 */
function hook_get_webcomic_transcript_terms_args_related( array $args ) {
	if ( empty( $args['related'] ) ) {
		return $args;
	}

	$related = $args['related'];

	if ( ! is_array( $related ) ) {
		$related = [];
	}

	$transcripts = get_webcomic_transcripts(
		[
			'fields' => 'ids',
		] + $related
	);

	if ( ! $transcripts ) {
		$transcripts = -1; // NOTE Force empty results with a bogus object ID.
	}

	$terms = wp_get_object_terms(
		$transcripts, $args['taxonomy'], [
			'fields' => 'ids',
		]
	);

	if ( ! $terms || is_wp_error( $terms ) ) {
		$terms = -1; // NOTE Force empty results with a bogus include.
	}

	$args['include'] = $terms;

	return $args;
}

/**
 * Set default arguments for get_webcomic_transcript_terms_list().
 *
 * @param array $args The arguments to filter.
 * @param array $terms The post transcript terms included in the list.
 * @return array
 */
function hook_get_webcomic_transcript_terms_list_args( array $args, array $terms ) : array {
	$args = [
		'options' => false,
	] + $args + [
		'current'   => [],
		'depth'     => 0,
		'end_el'    => '',
		'end_lvl'   => '',
		'end'       => '',
		'format'    => ', ',
		'related'   => [],
		'start_el'  => '',
		'start_lvl' => '',
		'start'     => '',
		'taxonomy'  => [],
		'walker'    => '',
	];

	if ( ! array_key_exists( 'hierarchical', $args ) ) {
		$args['hierarchical'] = is_taxonomy_hierarchical( current( (array) $args['taxonomy'] ) );
	}

	if ( $args['hierarchical'] ) {
		preg_match( '/^(.*){{(.*)}}(.*)$/s', $args['format'], $match );

		$match        += [ '', '', '', '' ];
		$start_end     = array_replace( [ $match[1], $match[3] ], array_filter( [ $args['start'], $args['end'] ] ) );
		$args['start'] = $start_end[0];
		$args['end']   = $start_end[1];
	}

	if ( preg_match( '/<(?:select|optgroup).*?>.*{{.*}}/', $args['format'] ) ) {
		$args['options'] = true;
	}

	if ( ! class_exists( $args['walker'] ) ) {
		$args['walker'] = __NAMESPACE__ . '\Walker\TermLister';
	}

	return $args;
}

/**
 * Set default arguments for get_webcomic_transcripts_list().
 *
 * @param array $args The arguments to filter.
 * @param array $terms The post transcript terms included in the list.
 * @return array
 */
function hook_get_webcomic_transcripts_list_args( array $args, array $terms ) : array {
	$args += [
		'authors_list'   => [
			'format' => '<span class="webcomic-transcript-authors"><span class="screen-reader-text">' . esc_html__( 'Comic Transcript Authors', 'webcomic' ) . '</span>{{, }}</span>',
		],
		'edit_link'      => '<span>{{' . esc_html__( 'Edit This', 'webcomic' ) . '}}</span>',
		'format'         => '<div id="webcomic-transcripts" class="webcomic-transcripts"><h2 class="webcomic-transcripts-title">' . esc_html__( 'Comic Transcripts', 'webcomic' ) . '</h2>{{}}</div>',
		'item'           => '<article id="post-%1$s" class="%2$s">{{<div class="webcomic-transcript-content">%content</div><footer class="webcomic-transcript-meta">%authors %languages %edit-link</footer>}}</article>',
		'languages_list' => [
			'format' => '<span class="webcomic-transcript-languages"><span class="screen-reader-text">' . esc_html__( 'Comic Transcript Languages', 'webcomic' ) . '</span>{{, }}</span>',
		],
		'parent_link'    => '%title',
		'walker'         => '',
	];

	if ( ! class_exists( $args['walker'] ) ) {
		$args['walker'] = __NAMESPACE__ . '\Walker\TranscriptLister';
	}

	return $args;
}

/**
 * Add a transcribe parameter to a comic URL.
 *
 * @param string  $url The comic URL.
 * @param WP_Post $comic The comic the URL points to.
 * @param array   $args Optional arguments.
 * @param mixed   $post Optional reference post.
 * @return string
 */
function hook_get_webcomic_url_transcribe( string $url, WP_Post $comic, array $args, $post ) : string {
	if ( empty( $args['transcribe'] ) ) {
		return $url;
	} elseif ( ! webcomic_transcripts_open( $comic ) ) {
		return '';
	}

	preg_match( '/^(\d+)?(#.+)?$/', (string) $args['transcribe'], $match );

	$match   += [ '', 0, '#webcomic-transcribe' ];
	$match[1] = (int) $match[1];
	$suffix   = "transcribe/{$match[1]}/{$match[2]}";
	$query    = [
		'transcribe' => $match[1],
	];

	if ( $match[1] && ( ! is_a_webcomic_transcript( $match[1], 'pending' ) || wp_get_post_parent_id( $match[1] ) !== $comic->ID ) ) {
		return '';
	} elseif ( ! $match[1] ) {
		$suffix = $match{2};
		$query  = [];
	}

	if ( get_option( 'rewrite_rules' ) ) {
		return trailingslashit( $url ) . $suffix;
	}

	return esc_url( add_query_arg( $query, $url ) . $suffix );
}

/**
 * Add a transcripts anchor to a comic URL.
 *
 * @param string  $url The comic URL.
 * @param WP_Post $comic The comic the URL points to.
 * @param array   $args Optional arguments.
 * @param mixed   $post Optional reference post.
 * @return string
 */
function hook_get_webcomic_url_transcripts( string $url, WP_Post $comic, array $args, $post ) : string {
	if ( empty( $args['transcripts'] ) ) {
		return $url;
	} elseif ( ! webcomic_transcripts_open( $comic ) && ! has_webcomic_transcripts( $comic ) ) {
		return '';
	}

	$anchor = (string) $args['transcripts'];

	if ( '#' !== $anchor[0] ) {
		$anchor = '#webcomic-transcripts';
	}

	if ( get_option( 'rewrite_rules' ) ) {
		return trailingslashit( $url ) . $anchor;
	}

	return $url . $anchor;
}

/**
 * Add webcomic transcribe link classes.
 *
 * @param array   $class The CSS classes.
 * @param array   $args Optional arguments.
 * @param WP_Post $comic The comic the link is for.
 * @return array
 */
function hook_get_webcomic_link_class_transcribe( array $class, array $args, WP_Post $comic ) : array {
	if ( empty( $args['transcribe'] ) || ! webcomic_transcripts_open( $comic ) ) {
		return $class;
	} elseif ( in_array( 'current-webcomic', $class, true ) ) {
		$key = array_search( 'current-webcomic', $class, true );

		unset( $class[ $key ], $class[ $key + 1 ] );
	}

	$class[] = 'webcomic-transcribe-link';
	$class[] = "{$comic->post_type}-transcribe-link";
	$class[] = "webcomic-transcribe-parent-{$comic->ID}";

	return $class;
}

/**
 * Add webcomic transcripts link classes.
 *
 * @param array   $class The CSS classes.
 * @param array   $args Optional arguments.
 * @param WP_Post $comic The comic the link is for.
 * @return array
 */
function hook_get_webcomic_link_class_transcripts( array $class, array $args, WP_Post $comic ) : array {
	if ( empty( $args['transcripts'] ) || ( ! webcomic_transcripts_open( $comic ) && ! has_webcomic_transcripts( $comic ) ) ) {
		return $class;
	} elseif ( in_array( 'current-webcomic', $class, true ) ) {
		$key = array_search( 'current-webcomic', $class, true );

		unset( $class[ $key ], $class[ $key + 1 ] );
	}

	$class[] = 'webcomic-transcripts-link';
	$class[] = "{$comic->post_type}-transcripts-link";

	return $class;
}

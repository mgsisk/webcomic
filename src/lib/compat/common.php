<?php
/**
 * Deprecated common functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Compat;

use WebcomicTag;
use WP_Customize_Manager;
use WP_Post;
use const Mgsisk\Webcomic\Collection\ENDPOINT;

/**
 * Add common hooks.
 *
 * @return void
 */
function common() {
	add_filter( 'webcomic_twitter_status_tokens', __NAMESPACE__ . '\hook_add_status_tokens', 10, 3 );
	add_filter( 'init', __NAMESPACE__ . '\hook_add_rewrite_endpoints' );
	add_filter( 'init', __NAMESPACE__ . '\hook_redirect_webcomic_url', 999 );
	add_filter( 'init', __NAMESPACE__ . '\hook_redirect_webcomic_term_url', 999 );
	add_filter( 'init', __NAMESPACE__ . '\hook_confirm_webcomic_age', 999 );
	add_filter( 'init', __NAMESPACE__ . '\hook_add_image_sizes' );
	add_filter( 'widgets_init', __NAMESPACE__ . '\hook_register_widgets' );
	add_filter( 'template_include', __NAMESPACE__ . '\hook_home_template' );
	add_filter( 'template_include', __NAMESPACE__ . '\hook_archive_template' );
	add_filter( 'template_include', __NAMESPACE__ . '\hook_taxonomy_template' );
	add_filter( 'template_include', __NAMESPACE__ . '\hook_page_template' );
	add_filter( 'template_include', __NAMESPACE__ . '\hook_single_template' );
	add_filter( 'template_include', __NAMESPACE__ . '\hook_attachment_template' );
	add_filter( 'template_include', __NAMESPACE__ . '\hook_redirect_restricted_collections' );
	add_filter( 'get_post_metadata', __NAMESPACE__ . '\hook_get_post_meta_v3', 10, 4 );
	add_filter( 'get_post_metadata', __NAMESPACE__ . '\hook_get_post_media_v4', 10, 4 );
	add_filter( 'get_post_metadata', __NAMESPACE__ . '\hook_get_post_prints_v4', 10, 4 );
	add_filter( 'get_post_metadata', __NAMESPACE__ . '\hook_get_post_transcribe_v4', 10, 4 );
	add_filter( 'get_post_metadata', __NAMESPACE__ . '\hook_get_transcript_authors_v4', 10, 4 );
	add_filter( 'get_term_metadata', __NAMESPACE__ . '\hook_get_term_media_v4', 10, 4 );
	add_filter( 'customize_register', __NAMESPACE__ . '\hook_inkblot_customizer_panel', 99 );
}

/**
 * Add compatibility status tokens.
 *
 * @param array   $tokens The status tokens.
 * @param string  $format The status format.
 * @param WP_Post $post The current post object.
 * @return array
 * @SuppressWarnings(PHPMD.NPathComplexity) - Required for compatibility.
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength) - Required for compatibility.
 * @codingStandardsIgnoreStart Generic.Metrics.NestingLevel.TooHigh, Generic.Metrics.CyclomaticComplexity.MaxExceeded - Required for compatibility.
 */
function hook_add_status_tokens( array $tokens, string $format, WP_Post $post ) {
	if ( ! preg_match( '/%\S/', $format ) ) {
		return $tokens;
	} elseif ( false !== strpos( $format, '%collection-name' ) ) {
		$tokens['%collection-name'] = webcomic( "option.{$post->post_type}.name" );
	}

	if ( false !== strpos( $format, '%collections' ) ) {
		$tokens['%collections'][ $post->post_type ] = str_replace( '-', '', preg_replace( '/^#$/', '', '#' . sanitize_title( webcomic( "option.{$post->post_type}.name" ), '', 'save' ) ) );
	}

	if ( false !== strpos( $format, '%collection-names' ) ) {
		$tokens['%collection-names'][ $post->post_type ] = webcomic( "option.{$post->post_type}.name" );
	}

	foreach ( get_object_taxonomies( $post ) as $taxonomy ) {
		if ( false !== strpos( $format, "%{$taxonomy}-names" ) ) {
			$format = str_replace( "%{$taxonomy}-names", "%{$taxonomy}", $format );

			$terms                        = wp_get_object_terms( $post->ID, $taxonomy );
			$tokens[ "%{$taxonomy}-names" ] = [];

			foreach ( $terms as $term ) {
				$tokens[ "%{$taxonomy}-names" ][] = $term->name;
			}

			$tokens[ "%{$taxonomy}-names" ] = implode( ' ', array_filter( $tokens[ "%{$taxonomy}-names" ] ) );
		}

		if ( preg_match_all( '/%x?collection(?:-name)?s/', $format, $keys ) && preg_match( '/^(webcomic\d+)_/', $taxonomy, $match ) && ! isset( $tokens[ $keys[0][0] ][ $match[1] ] ) ) {
			foreach ( $keys[0] as $key ) {
				if ( false !== strpos( $key, '!' ) && false === strpos( $taxonomy, $post->post_type ) ) {
					break;
				} elseif ( false !== strpos( $key, 'x' ) && false !== strpos( $taxonomy, $post->post_type ) ) {
					break;
				}

				$value = webcomic( "option.{$match[1]}.name" );

				if ( false === strpos( $key, '-names' ) ) {
					$value = str_replace( '-', '', preg_replace( '/^#$/', '', '#' . sanitize_title( webcomic( "option.{$match[1]}.name" ), '', 'save' ) ) );
				}

				$tokens[ $key ][ $match[1] ] = $value;
			}
		}

		if ( preg_match_all( '/%(?:!|x)?(character|storyline)(?:-name)?s/', $format, $keys ) && preg_match( '/^webcomic\d+_(character|storyline)/', $taxonomy ) && ! isset( $tokens[ $keys[0][0] ][ $taxonomy ] ) ) {
			$terms = wp_get_object_terms( $post->ID, $taxonomy );

			foreach ( $keys[0] as $index => $key ) {
				if ( false !== strpos( $key, '!' ) && false === strpos( $taxonomy, $post->post_type ) ) {
					break;
				} elseif ( false !== strpos( $key, 'x' ) && false !== strpos( $taxonomy, $post->post_type ) ) {
					break;
				} elseif ( false === strpos( $taxonomy, $keys[1][ $index ] ) ) {
					continue;
				}

				$tokens[ $key ][ $taxonomy ] = [];

				foreach ( $terms as $term ) {
					$value = $term->name;

					if ( false === strpos( $key, '-names' ) ) {
						$value = str_replace( '-', '', "#{$term->slug}" );
					}

					$tokens[ $key ][ $taxonomy ][] = $value;
				}

				$tokens[ $key ][ $taxonomy ] = implode( ' ', array_unique( array_filter( $tokens[ $key ][ $taxonomy ] ) ) );
			}
		}
	}

	foreach ( $tokens as $key => $token ) {
		if ( ! is_array( $token ) ) {
			continue;
		}

		$tokens[ $key ] = implode( ' ', array_unique( array_filter( $tokens[ $key ] ) ) );
	}

	return $tokens;
} // @codingStandardsIgnoreEnd Generic.Metrics.NestingLevel.TooHigh, Generic.Metrics.CyclomaticComplexity.MaxExceeded

/**
 * Add deprecated rewrite endpoints.
 *
 * @return void
 * @suppress PhanUndeclaredConstant - ENDPOINT incorrectly triggers this.
 */
function hook_add_rewrite_endpoints() {
	add_rewrite_endpoint( 'prints', ENDPOINT );
}

/**
 * Redirect old webcomic URL parameterized requests.
 *
 * @return void
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 * @codingStandardsIgnoreStart Generic.Metrics.CyclomaticComplexity.TooHigh - Required for compatibility.
 */
function hook_redirect_webcomic_url() {
	$relation   = '';
	$collection = '';

	if ( webcomic( 'GLOBALS._REQUEST.first_webcomic' ) ) {
		$relation   = 'first';
		$collection = webcomic( 'GLOBALS._REQUEST.first_webcomic' );
	} elseif ( webcomic( 'GLOBALS._REQUEST.last_webcomic' ) ) {
		$relation   = 'last';
		$collection = webcomic( 'GLOBALS._REQUEST.last_webcomic' );
	} elseif ( webcomic( 'GLOBALS._REQUEST.random_webcomic' ) ) {
		$relation   = 'random';
		$collection = webcomic( 'GLOBALS._REQUEST.random_webcomic' );
	}

	if ( ! $relation ) {
		return;
	} elseif ( ! $collection ) {
		$collection = array_rand( array_flip( webcomic( 'option.collections' ) ) );
	}

	$in_same_term   = [];
	$excluded_terms = [];
	$taxonomy       = '';

	if ( webcomic( 'GLOBALS._REQUEST.in_same_term' ) ) {
		$in_same_term = maybe_unserialize( rawurldecode( webcomic( 'GLOBALS._REQUEST.in_same_term' ) ) );
	}

	if ( webcomic( 'GLOBALS._REQUEST.excluded_terms' ) ) {
		$excluded_terms = maybe_unserialize( rawurldecode( webcomic( 'GLOBALS._REQUEST.excluded_terms' ) ) );
	}

	if ( webcomic( 'GLOBALS._REQUEST.taxonomy' ) ) {
		$taxonomy = maybe_unserialize( rawurldecode( webcomic( 'GLOBALS._REQUEST.taxonomy' ) ) );
	}

	$url = WebcomicTag::get_relative_webcomic_link_( $relation, $in_same_term, $excluded_terms, $taxonomy, $collection );

	define( 'DOING_AJAX', true ) && wp_safe_redirect( $url ) && wp_die();
} // @codingStandardsIgnoreEnd Generic.Metrics.CyclomaticComplexity.TooHigh

/**
 * Redirect old webcomic term URL parameterized requests.
 *
 * @return void
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function hook_redirect_webcomic_term_url() {
	$relation = '';
	$taxonomy = '';

	if ( webcomic( 'GLOBALS._REQUEST.first_webcomic_term' ) ) {
		$relation = 'first';
		$taxonomy = webcomic( 'GLOBALS._REQUEST.first_webcomic_term' );
	} elseif ( webcomic( 'GLOBALS._REQUEST.last_webcomic_term' ) ) {
		$relation = 'last';
		$taxonomy = webcomic( 'GLOBALS._REQUEST.last_webcomic_term' );
	} elseif ( webcomic( 'GLOBALS._REQUEST.random_webcomic_term' ) ) {
		$relation = 'random';
		$taxonomy = webcomic( 'GLOBALS._REQUEST.random_webcomic_term' );
	}

	if ( ! $relation ) {
		return;
	} elseif ( ! $taxonomy ) {
		$taxonomy = array_rand( array_flip( webcomic( 'option.collections' ) ) ) . '_' . array_rand(
			[
				'character' => true,
				'storyline' => true,
			]
		);
	}

	$args   = [];
	$target = 'archive';

	if ( null !== webcomic( 'GLOBALS._REQUEST.target' ) && 'archive' !== webcomic( 'GLOBALS._REQUEST.target' ) ) {
		$target = webcomic( 'GLOBALS._REQUEST.target' );
	}

	if ( webcomic( 'GLOBALS._REQUEST.args' ) ) {
		$args = maybe_unserialize( rawurldecode( webcomic( 'GLOBALS._REQUEST.args' ) ) );
	}

	$url = WebcomicTag::get_relative_webcomic_term_link_( $target, $relation, $taxonomy, $args );

	define( 'DOING_AJAX', true ) && wp_safe_redirect( $url ) && wp_die();
}

/**
 * Set age confirmation cookie and redirect for collection age restrictions.
 *
 * @return void
 */
function hook_confirm_webcomic_age() {
	if ( ! webcomic( 'GLOBALS._REQUEST.webcomic_birthday' ) ) {
		return;
	}

	$collection = get_webcomic_collection();
	$age        = webcomic( "option.{$collection}.restrict_age" );

	if ( ! $age ) {
		return;
	}

	$user_age = (int) webcomic( "GLOBALS._COOKIE.{$collection}_age_" . COOKIEHASH );

	if ( $user_age >= $age ) {
		return;
	}

	setcookie( "{$collection}_age_" . COOKIEHASH, (string) $age, (int) current_time( 'timestamp' ) + 604800, COOKIEPATH );

	header( 'Refresh:0' );

	define( 'DOING_AJAX', true ) && wp_die();
}

/**
 * Add image sizes.
 *
 * @return void
 */
function hook_add_image_sizes() {
	if ( ! webcomic( 'option.compat.sizes' ) ) {
		return;
	}

	foreach ( webcomic( 'option.compat.sizes' ) as $size => $attr ) {
		add_image_size( $size, $attr['width'], $attr['height'], $attr['crop'] );
	}
}

/**
 * Register compat widgets.
 *
 * @return void
 */
function hook_register_widgets() {
	register_widget( 'Widget_PurchaseWebcomicLink' );
	register_widget( 'Widget_RecentWebcomics' );
	register_widget( 'Widget_ScheduledWebcomics' );
	register_widget( 'Widget_WebcomicCharacterLink' );
	register_widget( 'Widget_WebcomicCharacters' );
	register_widget( 'Widget_WebcomicCollectionLink' );
	register_widget( 'Widget_WebcomicCollections' );
	register_widget( 'Widget_WebcomicDonation' );
	register_widget( 'Widget_WebcomicLink' );
	register_widget( 'Widget_WebcomicPrint' );
	register_widget( 'Widget_WebcomicStorylineLink' );
	register_widget( 'Widget_WebcomicStorylines' );
	register_widget( 'Widget_WebcomicTranscriptsLink' );
}

/**
 * Include old home comic templates.
 *
 * @param string $template The template to load.
 * @return string
 */
function hook_home_template( string $template ) : string {
	if ( ! is_front_page() || ! is_home() ) {
		return $template;
	}

	$collection    = get_webcomic_collection();
	$templates     = [ "webcomic/home-{$collection}.php" ];
	$templates[]   = 'webcomic/home.php';
	$home_template = locate_template( $templates );

	if ( $home_template ) {
		return $home_template;
	}

	return $template;
}

/**
 * Include old comic archive templates.
 *
 * @param string $template The template to load.
 * @return string
 */
function hook_archive_template( string $template ) : string {
	if ( ! is_webcomic_collection() ) {
		return $template;
	}

	$collection       = get_webcomic_collection();
	$templates        = [ "archive-{$collection}.php" ];
	$templates[]      = 'webcomic/collection.php';
	$templates[]      = 'webcomic/archive.php';
	$archive_template = locate_template( $templates );

	if ( $archive_template ) {
		return $archive_template;
	}

	return $template;
}

/**
 * Include old comic taxonomy templates.
 *
 * @param string $template The template to load.
 * @return string
 */
function hook_taxonomy_template( string $template ) : string {
	if ( ! function_exists( 'is_webcomic_tax' ) || ! is_webcomic_tax() ) {
		return $template;
	}

	$templates = [];
	$object    = get_queried_object();
	$type      = preg_replace( '/^webcomic\d+_/', '', $object->taxonomy );

	if ( is_webcomic_tax(
		null, null, null, [
			'crossover' => true,
		]
	) ) {
		$templates[] = "webcomic/crossover-{$object->taxonomy}-{$object->slug}.php";
		$templates[] = "webcomic/crossover-{$object->taxonomy}.php";
		$templates[] = "webcomic/crossover-{$type}.php";
		$templates[] = 'webcomic/crossover.php';
	}

	$templates[]       = "taxonomy-{$object->taxonomy}-{$object->slug}.php";
	$templates[]       = "taxonomy-{$object->taxonomy}.php";
	$templates[]       = "webcomic/{$type}.php";
	$templates[]       = 'webcomic/taxonomy.php';
	$templates[]       = 'taxonomy.php';
	$templates[]       = 'webcomic/archive.php';
	$taxonomy_template = locate_template( $templates );

	if ( $taxonomy_template ) {
		return $taxonomy_template;
	}

	return $template;
}

/**
 * Include old comic page templates.
 *
 * @param string $template The template to load.
 * @return string
 */
function hook_page_template( string $template ) : string {
	if ( ! is_webcomic_page() ) {
		return $template;
	}

	$collection    = get_webcomic_collection();
	$templates     = [ "webcomic/page-{$collection}.php" ];
	$templates[]   = 'webcomic/page.php';
	$page_template = locate_template( $templates );

	if ( $page_template ) {
		return $page_template;
	}

	return $template;
}

/**
 * Include old single-comic templates.
 *
 * @param string $template The template to load.
 * @return string
 */
function hook_single_template( string $template ) : string {
	if ( ! is_webcomic() ) {
		return $template;
	}

	$templates  = [];
	$post       = get_post();
	$collection = get_webcomic_collection();

	if ( null !== get_query_var( 'prints', null ) ) {
		$templates[] = "webcomic/prints-{$collection}-{$post->post_name}.php";
		$templates[] = "webcomic/prints-{$collection}.php";
		$templates[] = 'webcomic/prints.php';
	}

	$templates[]     = "single-{$collection}-{$post->post_name}.php";
	$templates[]     = "single-{$collection}.php";
	$templates[]     = 'webcomic/single.php';
	$single_template = locate_template( $templates );

	if ( $single_template ) {
		return $single_template;
	}

	return $template;
}

/**
 * Include old attachment comic templates.
 *
 * @param string $template The template to load.
 * @return string
 */
function hook_attachment_template( string $template ) : string {
	if ( ! is_webcomic_media() ) {
		return $template;
	}

	$post                = get_post();
	$mimetype            = explode( '/', $post->post_mime_type );
	$collection          = get_webcomic_collection();
	$templates           = [ "webcomic/{$mimetype[0]}-{$collection}.php" ];
	$templates[]         = "webcomic/{$mimetype[0]}.php";
	$templates[]         = "webcomic/{$mimetype[1]}-{$collection}.php";
	$templates[]         = "webcomic/{$mimetype[1]}.php";
	$templates[]         = "webcomic/{$mimetype[0]}_{$mimetype[1]}-{$collection}.php";
	$templates[]         = "webcomic/{$mimetype[0]}_{$mimetype[1]}.php";
	$templates[]         = "webcomic/attachment-{$collection}.php";
	$templates[]         = 'webcomic/attachment.php';
	$attachment_template = locate_template( $templates );

	if ( $attachment_template ) {
		return $attachment_template;
	}

	return $template;
}

/**
 * Redirect to old restricted templates.
 *
 * @param string $template The template to include.
 * @return string
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 */
function hook_redirect_restricted_collections( string $template ) : string {
	$collection = get_webcomic_collection();

	if ( ! $collection ) {
		return $template;
	}

	$compat_template = '';

	if ( ! WebcomicTag::verify_webcomic_role_( $collection ) ) {
		$compat_template = locate_template( [ "webcomic/restricted-role-{$collection}.php", 'webcomic/restricted-role.php', 'webcomic/restricted.php' ] );
	} elseif ( ! WebcomicTag::verify_webcomic_age_( $collection ) ) {
		$compat_template = locate_template( [ "webcomic/restricted-age-{$collection}.php", 'webcomic/restricted-age.php', 'webcomic/restricted.php' ] );
	}

	if ( $compat_template ) {
		return $compat_template;
	}

	return $template;
}

/**
 * Convert Webcomic 3 meta data.
 *
 * @param mixed  $meta The meta value.
 * @param int    $id The post ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return only the first $key value.
 * @return mixed
 * @suppress PhanUndeclaredFunctionInCallable - Incorrectly triggered.
 */
function hook_get_post_meta_v3( $meta, $id, $key, $single ) {
	if ( ! preg_match( '/^webcomic_media|webcomic_transcribe(_count_.+)?|webcomic_commerce_prints(_(sold|adjust)_.+)?$/', $key ) || 3 !== webcomic( 'option.compat.version' ) || ! metadata_exists( 'post', $id, 'webcomic' ) || ! is_a_webcomic( $id ) ) {
		return $meta;
	}

	remove_filter( 'get_post_metadata', __FUNCTION__ );

	$meta = (array) get_post_meta( $id, 'webcomic', true );

	util_get_post_media_v3( $id, $meta );
	util_get_post_prints_v3( $id, $meta );
	util_get_post_transcribe_v3( $id, $meta );

	delete_post_meta( $id, 'webcomic' );

	add_filter( 'get_post_metadata', __FUNCTION__, 10, 4 );

	return $meta;
}

/**
 * Convert Webcomic 4 media attachments.
 *
 * @param mixed  $meta The meta value.
 * @param int    $id The post ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return only the first $key value.
 * @return mixed
 * @SuppressWarnings(PHPMD.StaticAccess) - Required for compatibility.
 * @suppress PhanUndeclaredFunctionInCallable - Incorrectly triggered.
 */
function hook_get_post_media_v4( $meta, $id, $key, $single ) {
	if ( 'webcomic_media' !== $key || 4 !== webcomic( 'option.compat.version' ) || ! is_a_webcomic( $id ) ) {
		return $meta;
	}

	remove_filter( 'get_post_metadata', __FUNCTION__ );

	if ( ! metadata_exists( 'post', $id, $key ) ) {
		$media = WebcomicTag::get_attachments_( $id );

		foreach ( $media as $item ) {
			add_post_meta( $id, 'webcomic_media', $item );
			add_post_meta( $item, 'webcomic_post', $id );
		}
	}

	add_filter( 'get_post_metadata', __FUNCTION__, 10, 4 );

	return $meta;
}

/**
 * Convert Webcomic 4 commerce data.
 *
 * @param mixed  $meta The meta value.
 * @param int    $id The post ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return only the first $key value.
 * @return mixed
 * @SuppressWarnings(PHPMD.NPathComplexity) - Required for compatibility.
 * @codingStandardsIgnoreStart Generic.Metrics.CyclomaticComplexity.TooHigh - Required for compatibility.
 */
function hook_get_post_prints_v4( $meta, $id, $key, $single ) {
	if ( ! preg_match( '/^webcomic_commerce_prints(_(sold|adjust)_.+)?$/', $key ) || 4 !== webcomic( 'option.compat.version' ) || ! metadata_exists( 'post', $id, 'webcomic_commerce' ) || ! is_a_webcomic( $id ) ) {
		return $meta;
	}

	remove_filter( 'get_post_metadata', __FUNCTION__ );

	$commerce      = webcomic( 'option.' . get_post_type( $id ) . '.commerce_prints' );
	$post_commerce = get_post_meta( $id, 'webcomic_commerce', true );

	if ( isset( $commerce['domestic'], $post_commerce['total']['domestic'] ) ) {
		$adjust = ( $post_commerce['total']['domestic'] / $commerce['domestic']['price'] - 1 ) * 100;

		if ( $adjust ) {
			update_post_meta( $id, 'webcomic_commerce_prints_adjust_domestic', $adjust );
		}
	}

	if ( isset( $commerce['international'], $post_commerce['total']['international'] ) ) {
		$adjust = ( $post_commerce['total']['international'] / $commerce['international']['price'] - 1 ) * 100;

		if ( $adjust ) {
			update_post_meta( $id, 'webcomic_commerce_prints_adjust_international', $adjust );
		}
	}

	if ( isset( $commerce['original'], $post_commerce['total']['original'] ) ) {
		$adjust = ( $post_commerce['total']['original'] / $commerce['original']['price'] - 1 ) * 100;

		if ( $adjust ) {
			update_post_meta( $id, 'webcomic_commerce_prints_adjust_original', $adjust );
		}
	}

	if ( get_post_meta( $id, 'webcomic_prints', true ) ) {
		$prints = [ 'domestic', 'international' ];

		if ( get_post_meta( $id, 'webcomic_original', true ) ) {
			$prints[] = 'original';
		}

		foreach ( $prints as $print ) {
			add_post_meta( $id, 'webcomic_commerce_prints', $print );
		}
	}

	delete_post_meta( $id, 'webcomic_prints' );
	delete_post_meta( $id, 'webcomic_original' );
	delete_post_meta( $id, 'webcomic_commerce' );

	add_filter( 'get_post_metadata', __FUNCTION__ );

	return $meta;
} // @codingStandardsIgnoreEnd Generic.Metrics.CyclomaticComplexity.TooHigh

/**
 * Convert Webcomic 4 transcript data.
 *
 * @param mixed  $meta The meta value.
 * @param int    $id The post ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return only the first $key value.
 * @return mixed
 * @suppress PhanUndeclaredFunctionInCallable - Incorrectly triggered.
 */
function hook_get_post_transcribe_v4( $meta, $id, $key, $single ) {
	if ( ! preg_match( '/^webcomic_transcribe(_count_.+)?$/', $key ) || 4 !== webcomic( 'option.compat.version' ) || ! metadata_exists( 'post', $id, 'webcomic_transcripts' ) || ! is_a_webcomic( $id ) ) {
		return $meta;
	}

	remove_filter( 'get_post_metadata', __FUNCTION__ );

	if ( get_post_meta( $id, 'webcomic_transcripts', true ) ) {
		update_post_meta( $id, 'webcomic_transcribe', true );
	}

	$counts      = [];
	$transcripts = get_posts(
		[
			'fields'      => 'ids',
			'post_type'   => 'webcomic_transcript',
			'post_status' => 'any',
			'post_parent' => $id,
		]
	);

	foreach ( $transcripts as $transcript ) {
		$index = get_post_status( $transcript );

		if ( empty( $counts[ $index ] ) ) {
			$counts[ $index ] = 0;
		}

		$counts[ $index ]++;
	}

	foreach ( $counts as $status => $count ) {
		if ( ! $count ) {
			continue;
		}

		update_post_meta( $id, "webcomic_transcribe_count_{$status}", $count );
	}

	delete_post_meta( $id, 'webcomic_transcripts' );

	add_filter( 'get_post_metadata', __FUNCTION__, 10, 4 );

	return $meta;
}

/**
 * Convert Webcomic 4 transcript author data.
 *
 * @param mixed  $meta The meta value.
 * @param int    $id The post ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return only the first $key value.
 * @return mixed
 */
function hook_get_transcript_authors_v4( $meta, $id, $key, $single ) {
	if ( 'webcomic_transcript_authors' !== $key || 4 !== webcomic( 'option.compat.version' ) || ! metadata_exists( 'post', $id, 'webcomic_author' ) || ! is_a_webcomic_transcript( $id ) ) {
		return $meta;
	}

	$old_authors = get_post_meta( $id, 'webcomic_author' );
	$post_author = get_userdata( (int) get_post_field( 'post_author', $id ) );
	$new_authors = [
		[
			'name'  => $post_author->display_name,
			'email' => $post_author->user_email,
			'url'   => $post_author->user_url,
			'time'  => get_post_field( 'post_date', $id ),
			'ip'    => '',
		],
	];

	foreach ( $old_authors as $author ) {
		$author['time'] = date( 'Y-m-d H:i:s', $author['time'] );
		$new_authors[]  = $author;
	}

	foreach ( $new_authors as $author ) {
		add_post_meta( $id, 'webcomic_transcript_authors', $author );
	}

	delete_post_meta( $id, 'webcomic_author' );

	return $meta;
}

/**
 * Convert Webcomic 4 term media data.
 *
 * @param mixed  $meta The meta value.
 * @param int    $id The post ID.
 * @param string $key The meta key.
 * @param bool   $single Whether to return only the first $key value.
 * @return mixed
 */
function hook_get_term_media_v4( $meta, $id, $key, $single ) {
	if ( 'webcomic_media' !== $key || 4 !== webcomic( 'option.compat.version' ) || ! is_a_webcomic_term( $id ) ) {
		return $meta;
	}

	$options = webcomic( 'option' );

	if ( empty( $options['compat']['terms'][ $id ]['image'] ) ) {
		return $meta;
	}

	add_term_meta( $id, 'webcomic_media', $options['compat']['terms'][ $id ]['image'] );

	unset( $options['compat']['terms'][ $id ] );

	if ( empty( $options['compat']['terms'] ) ) {
		unset( $options['compat']['terms'] );
	}

	update_option( 'webcomic', $options );

	return $meta;
}

/**
 * Rename Inkblot 4 Webcomic customizer panel.
 *
 * @param WP_Customize_Manager $customizer The customizer object.
 * @return void
 */
function hook_inkblot_customizer_panel( WP_Customize_Manager $customizer ) {
	$theme = $customizer->theme();

	if ( 'inkblot' !== strtolower( $theme->name ) ) {
		$theme = wp_get_theme( $theme->template );
	}

	if ( 'inkblot' !== strtolower( $theme ) || '4' !== $theme->version[0] ) {
		return;
	}

	$panel = $customizer->get_panel( 'webcomic' );

	if ( ! $panel ) {
		return;
	}

	$panel->id          = 'webcomic_inkblot';
	$panel->title       = __( 'Webcomic and Inkblot 4', 'webcomic' );
	$panel->description = __( 'These settings affect Webcomic-specific features in the Inkblot 4 theme.', 'webcomic' );

	$customizer->remove_panel( 'webcomic' );
	$customizer->add_panel( $panel );

	foreach ( $customizer->sections() as $section ) {
		if ( 'webcomic' !== $section->panel ) {
			continue;
		}

		$customizer->remove_section( $section->id );

		$section->id    = str_replace( 'webcomic_', 'webcomic_inkblot_', $section->id );
		$section->panel = 'webcomic_inkblot';

		$customizer->add_section( $section );
	}

	foreach ( $customizer->controls() as $control ) {
		if ( false === strpos( $control->section, 'webcomic_' ) ) {
			continue;
		}

		$customizer->remove_control( $control->id );

		$control->id      = str_replace( 'webcomic_', 'webcomic_inkblot_', $control->id );
		$control->section = str_replace( 'webcomic_', 'webcomic_inkblot_', $control->section );

		$customizer->add_control( $control );
	}
}

/* ===== Utility Functions ================================================== */

/**
 * Split Webcomic 3 file meta data into descriptive custom fields.
 *
 * @param int   $id The post ID.
 * @param array $meta The old meta data to update.
 * @return void
 * @internal For hook_get_post_meta_v3().
 */
function util_get_post_media_v3( $id, array $meta ) {
	if ( ! isset( $meta['files']['full'] ) ) {
		return;
	}

	foreach ( $meta['files']['full'] as $key => $file ) {
		update_post_meta( $id, "webcomic3_file_{$key}", $file );

		if ( $meta['alternate'][ $key ] ) {
			update_post_meta( $id, "webcomic3_file_{$key}_alternate", $file );
		}

		if ( $meta['description'][ $key ] ) {
			update_post_meta( $id, "webcomic3_file_{$key}_description", $file );
		}
	}
}

/**
 * Convert Webcomic 3 commerce data.
 *
 * @param int   $id The post ID.
 * @param array $meta The old meta data to update.
 * @return void
 * @internal For hook_get_post_meta_v3().
 */
function util_get_post_prints_v3( $id, array $meta ) {
	$commerce = webcomic( 'option.' . get_post_type( $id ) . '.commerce_prints' );

	if ( ! $commerce ) {
		return;
	} elseif ( $meta['paypal']['prints'] && isset( $commerce['prints']['domestic'], $meta['paypal']['price_d'], $meta['paypal']['shipping_d'] ) ) {
		add_post_meta( $id, 'webcomic_commerce_prints', 'domestic' );

		$adjust = $meta['paypal']['price_d'] + $meta['paypal']['shipping_d'];

		if ( $adjust ) {
			update_post_meta( $id, 'webcomic_commerce_prints_adjust_domestic', $adjust );
		}
	} elseif ( $meta['paypal']['prints'] && isset( $commerce['prints']['international'], $meta['paypal']['price_i'], $meta['paypal']['shipping_i'] ) ) {
		add_post_meta( $id, 'webcomic_commerce_prints', 'international' );

		$adjust = $meta['paypal']['price_i'] + $meta['paypal']['shipping_i'];

		if ( $adjust ) {
			update_post_meta( $id, 'webcomic_commerce_prints_adjust_international', $adjust );
		}
	} elseif ( $meta['paypal']['original'] && isset( $commerce['prints']['original'], $meta['paypal']['price_o'], $meta['paypal']['shipping_o'] ) ) {
		add_post_meta( $id, 'webcomic_commerce_prints', 'original' );

		$adjust = $meta['paypal']['price_o'] + $meta['paypal']['shipping_o'];

		if ( $adjust ) {
			update_post_meta( $id, 'webcomic_commerce_prints_adjust_original', $adjust );
		}
	}
}

/**
 * Split Webcomic 3 transcript data into descriptive custom fields.
 *
 * @param int   $id The post ID.
 * @param array $meta The old meta data to update.
 * @return void
 * @internal For hook_get_post_meta_v3().
 */
function util_get_post_transcribe_v3( $id, array $meta ) {
	if ( $meta['transcribe_toggle'] ) {
		update_post_meta( $id, 'webcomic_transcribe', true );
	} elseif ( empty( $meta['transcripts'] ) ) {
		return;
	}

	foreach ( $meta['transcripts'] as $key => $transcript ) {
		update_post_meta( $id, "webcomic3_transcript_{$key}", $transcript['text'] );
		update_post_meta( $id, "webcomic3_transcript_{$key}_time", date( 'Y-m-d H:i:s', $transcript['time'] ) );
		update_post_meta( $id, "webcomic3_transcript_{$key}_author", $transcript['author'] );
		update_post_meta( $id, "webcomic3_transcript_{$key}_status", $transcript['status'] );
		update_post_meta( $id, "webcomic3_transcript_{$key}_language", $transcript['language'] );
	}
}

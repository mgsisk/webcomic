<?php
/**
 * Common Twitter functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Twitter;

use WP_Post;

/**
 * Add common hooks.
 *
 * @return void
 */
function common() {
	add_filter( 'webcomic_twitter_card_data', __NAMESPACE__ . '\hook_add_card_data_basic', 0, 2 );
	add_filter( 'webcomic_twitter_status_tokens', __NAMESPACE__ . '\hook_add_status_tokens_basic', 0, 3 );
	add_filter( 'webcomic_twitter_status_tokens', __NAMESPACE__ . '\hook_add_status_tokens_fields', 0, 3 );
	add_filter( 'webcomic_twitter_status_tokens', __NAMESPACE__ . '\hook_add_status_tokens_taxonomies', 0, 3 );
	add_filter( 'init', __NAMESPACE__ . '\hook_add_rewrite_endpoint' );
	add_filter( 'transition_post_status', __NAMESPACE__ . '\hook_set_status_update', 10, 3 );
	add_filter( 'template_include', __NAMESPACE__ . '\hook_oauth_authorization', 99 );
	add_filter( 'wp_head', __NAMESPACE__ . '\hook_cards' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "save_post_{$collection}", __NAMESPACE__ . '\hook_update_status', 99 );
	}
}

/**
 * Add basic card data.
 *
 * @param array  $data Test.
 * @param string $collection Test.
 * @return array
 */
function hook_add_card_data_basic( array $data, string $collection ) : array {
	$data += [
		'card'        => 'summary',
		'site'        => preg_replace( '/^@$/', '', '@' . webcomic( "option.{$collection}.twitter_user" ) ),
		'title'       => wp_get_document_title(),
		'description' => [
			get_bloginfo( 'description' ),
			wp_strip_all_tags( webcomic( "option.{$collection}.description" ), true ),
		],
	];
	$media = [];

	if ( webcomic( "option.{$collection}.twitter_card_media" ) ) {
		$media[] = webcomic( "option.{$collection}.media" );
	}

	if ( is_singular() ) {
		$data['description'][] = wp_strip_all_tags( get_the_excerpt(), true );

		if ( $media ) {
			$media[] = current( (array) get_post_meta( get_the_ID(), 'webcomic_media' ) );
			$media[] = get_post_thumbnail_id();
			$media[] = get_post_meta( get_the_ID(), 'webcomic_twitter_status_media', true );
		}
	} elseif ( is_tax() ) {
		$data['description'][] = wp_strip_all_tags( get_queried_object()->description, true );

		if ( $media ) {
			$media[] = get_term_meta( get_queried_object()->term_id, 'webcomic_media' );
		}
	}

	$media               = (int) current( array_filter( array_reverse( $media ) ) );
	$data['description'] = (string) current( array_filter( array_reverse( $data['description'] ) ) );

	if ( $media ) {
		$data['card']      = 'summary_large_image';
		$data['image']     = wp_get_attachment_image_url( $media, 'full' );
		$data['image:alt'] = wp_strip_all_tags( get_post_field( 'post_excerpt', $media ), true );
	}

	return $data;
}

/**
 * Add basic status tokens.
 *
 * @param array   $tokens The status tokens.
 * @param string  $format The status format.
 * @param WP_Post $post The current post object.
 * @return array
 * @SuppressWarnings(PHPMD.NPathComplexity) - We're purposely using a lot of conditionals here to avoid executing functions we don't need to.
 * @codingStandardsIgnoreStart Generic.Metrics.CyclomaticComplexity.TooHigh - We're purposely using a lot of conditionals here to avoid executing functions we don't need to.
 */
function hook_add_status_tokens_basic( array $tokens, string $format, WP_Post $post ) {
	if ( ! preg_match( '/%\S/', $format ) ) {
		return $tokens;
	} elseif ( false !== strpos( $format, '%author' ) ) {
		$tokens['%author'] = get_userdata( $post->post_author )->user_nicename;
	}

	if ( false !== strpos( $format, '%collection' ) ) {
		$tokens['%collection'] = webcomic( "option.{$post->post_type}.name" );
	}

	if ( false !== strpos( $format, '%#collection' ) ) {
		$tokens['%#collection'] = str_replace( '-', '', preg_replace( '/^#$/', '', '#' . sanitize_title( webcomic( "option.{$post->post_type}.name" ), '', 'save' ) ) );
	}

	if ( false !== strpos( $format, '%date' ) ) {
		$tokens['%date'] = get_the_date( '', $post->ID );
	}

	if ( false !== strpos( $format, '%shortlink' ) ) {
		$tokens['%shortlink'] = wp_get_shortlink( $post->ID );
	}

	if ( false !== strpos( $format, '%site' ) ) {
		$tokens['%site-name'] = get_bloginfo( 'name' );
	}

	if ( false !== strpos( $format, '%site-url' ) ) {
		$tokens['%site-url'] = home_url();
	}

	if ( false !== strpos( $format, '%time' ) ) {
		$tokens['%time'] = get_the_date( get_option( 'time_format' ), $post->ID );
	}

	if ( false !== strpos( $format, '%title' ) ) {
		$tokens['%title'] = get_the_title( $post );
	}

	if ( false !== strpos( $format, '%url' ) ) {
		$tokens['%url'] = get_permalink( $post->ID );
	}

	return $tokens;
} // @codingStandardsIgnoreEnd Generic.Metrics.CyclomaticComplexity.TooHigh

/**
 * Add custom field status tokens.
 *
 * @param array   $tokens The status tokens.
 * @param string  $format The status format.
 * @param WP_Post $post The current post object.
 * @return array
 */
function hook_add_status_tokens_fields( array $tokens, string $format, WP_Post $post ) {
	if ( ! preg_match( '/%\S/', $format ) ) {
		return $tokens;
	} elseif ( preg_match_all( '/%field:{.+?}/', $format, $match ) ) {
		foreach ( $match[0] as $field ) {
			if ( isset( $tokens[ $field ] ) ) {
				continue;
			}

			$tokens[ $field ] = (string) get_post_meta( $post->ID, str_replace( [ '%field:{', '}' ], '', $field ), true );
		}
	}

	return $tokens;
}

/**
 * Add taxonomy status tokens.
 *
 * @param array   $tokens The status tokens.
 * @param string  $format The status format.
 * @param WP_Post $post The current post object.
 * @return array
 */
function hook_add_status_tokens_taxonomies( array $tokens, string $format, WP_Post $post ) {
	if ( ! preg_match( '/%\S/', $format ) ) {
		return $tokens;
	}

	foreach ( get_object_taxonomies( $post ) as $taxonomy ) {
		if ( false === strpos( $format, "%{$taxonomy}" ) && false === strpos( $format, "%#{$taxonomy}" ) ) {
			continue;
		}

		$terms                     = wp_get_object_terms( $post->ID, $taxonomy );
		$tokens[ "%{$taxonomy}" ]  = [];
		$tokens[ "%#{$taxonomy}" ] = [];

		foreach ( $terms as $term ) {
			if ( false !== strpos( $format, "%{$taxonomy}" ) ) {
				$tokens[ "%{$taxonomy}" ][] = $term->name;
			}

			if ( false !== strpos( $format, "%#{$taxonomy}" ) ) {
				$tokens[ "%#{$taxonomy}" ][] = str_replace( '-', '', "#{$term->slug}" );
			}
		}

		$tokens[ "%{$taxonomy}" ]  = implode( ' ', array_filter( $tokens[ "%{$taxonomy}" ] ) );
		$tokens[ "%#{$taxonomy}" ] = implode( ' ', array_filter( $tokens[ "%#{$taxonomy}" ] ) );
	}

	return $tokens;
}

/**
 * Add Twitter rewrite endpoints.
 *
 * @return void
 */
function hook_add_rewrite_endpoint() {
	add_rewrite_endpoint( 'webcomic-twitter-oauth', EP_ROOT );
}

/**
 * Update Twitter status when a comic is published.
 *
 * @param string  $new_status The new post status.
 * @param string  $old_status The old post status.
 * @param WP_Post $post The post being updated.
 * @return void
 */
function hook_set_status_update( string $new_status, string $old_status, WP_Post $post ) {
	if ( 'publish' !== $new_status || ! is_a_webcomic( $post ) ) {
		return;
	}

	add_filter( 'webcomic_twitter_update_status', '__return_true' );
}

/**
 * Handle Twitter account authorization.
 *
 * @param string $template The template to load.
 * @return string|null
 */
function hook_oauth_authorization( string $template ) {
	$collection = get_query_var( 'webcomic-twitter-oauth', null );

	if ( null === $collection ) {
		return $template;
	} elseif ( ! webcomic_collection_exists( $collection ) || ! current_user_can( 'manage_options' ) || ! webcomic( 'GLOBALS._REQUEST' ) ) {
		return locate_template( '404.php' );
	}

	$url = esc_url(
		add_query_arg(
			[
				'post_type' => $collection,
				'page'      => "{$collection}_options",
			], admin_url( 'edit.php' )
		)
	);
	// Translators: Custom post type name.
	$return = "<p><a href='{$url}'>" . sprintf( esc_html__( '&laquo; Return to %s Settings', 'webcomic' ), esc_html( webcomic( "option.{$collection}.name" ) ) ) . '</a></p>';

	if ( webcomic( 'GLOBALS._REQUEST.denied' ) ) {
		wp_die( esc_html__( 'Authorization was denied.', 'webcomic' ) . $return, esc_html__( 'Twitter Authorization Denied', 'webcomic' ) ); // WPCS: xss ok.
	}

	$oauth                       = webcomic( "option.{$collection}.twitter_oauth" );
	$oauth['oauth_token']        = (string) get_transient( "{$collection}_twitter_oauth_token" );
	$oauth['oauth_token_secret'] = (string) get_transient( "{$collection}_twitter_oauth_token_secret" );
	$oauth['oauth_verifier']     = webcomic( 'GLOBALS._REQUEST.oauth_verifier' );
	$response                    = util_api_request( 'POST', 'oauth/access_token', $oauth );

	if ( $response['errors'] ) {
		wp_die( implode( '<br>', array_map( 'esc_html', $response['errors'] ) ) . $return, esc_html__( 'Twitter Authorization Error', 'webcomic' ) ); // WPCS: xss ok.
	}

	unset( $oauth['oauth_verifier'] );

	delete_transient( "{$collection}_twitter_oauth_token" );
	delete_transient( "{$collection}_twitter_oauth_token_secret" );

	$oauth['oauth_token']        = $response['body']['oauth_token'];
	$oauth['oauth_token_secret'] = $response['body']['oauth_token_secret'];
	$response                    = util_api_request( 'GET', 'account/verify_credentials.json', $oauth );

	if ( $response['errors'] ) {
		wp_die( implode( '<br>', array_map( 'esc_html', $response['errors'] ) ) . $return, esc_html__( 'Twitter Authorization Error', 'webcomic' ) ); // WPCS: xss ok.
	}

	$options                  = webcomic( "option.{$collection}" );
	$options['twitter_oauth'] = $oauth;

	update_option( $collection, $options );

	// Translators: Twitter screen name.
	wp_die( sprintf( esc_html__( 'Twitter authorization for @%s complete.', 'webcomic' ), esc_html( $response['body']['screen_name'] ) ) . $return, esc_html__( 'Twitter Authorization Complete', 'webcomic' ) ); // WPCS: xss ok.
}

/**
 * Add Twitter Card meta data.
 *
 * @return void
 */
function hook_cards() {
	$collection = get_webcomic_collection();

	if ( ! $collection || ! webcomic( "option.{$collection}.twitter_card" ) ) {
		return;
	}

	/**
	 * Alter card meta data.
	 *
	 * This filter allows hooks to provide name => content pairs for generating
	 * Twitter Card meta data tags.
	 *
	 * @param array  $rules The list of name => content meta data pairs.
	 * @param string $collection The current collection.
	 */
	$data = apply_filters( 'webcomic_twitter_card_data', [], $collection );
	$data = array_filter( $data );

	uksort( $data, 'strcmp' );

	foreach ( $data as $key => $value ) {
		printf( "<meta name='twitter:%s' content='%s'>\n", esc_attr( $key ), esc_attr( $value ) );
	}
}

/* ===== Collection Hooks =================================================== */

/**
 * Update Twitter status when a comic is published.
 *
 * @param int $id The post to send status updates.
 * @return void
 */
function hook_update_status( int $id ) {
	/**
	 * Alter Twitter status updating.
	 *
	 * This filter allows hooks to force or disallow status updates when a comic
	 * is published.
	 *
	 * @param bool $tweet Wether to update status when a comic is published.
	 */
	$tweet = apply_filters( 'webcomic_twitter_update_status', false );

	if ( ! $tweet ) {
		return;
	}

	$comic = get_webcomic( $id );

	if ( ! $comic || 'publish' !== $comic->post_status ) {
		return;
	}

	$oauth  = array_filter( webcomic( "option.{$comic->post_type}.twitter_oauth" ) );
	$media  = 0;
	$status = util_get_status( get_post_meta( $id, 'webcomic_twitter_status', true ), $comic );

	if ( 4 !== count( $oauth ) || ! $status || ! get_post_meta( $id, 'webcomic_twitter_update' ) ) {
		return;
	} elseif ( get_post_meta( $id, 'webcomic_twitter_update_media' ) ) {
		$media = [
			get_post_meta( $id, 'webcomic_twitter_status_media', true ),
			get_post_thumbnail_id( $id ),
			current( (array) get_post_meta( $id, 'webcomic_media' ) ),
		];

		$media = wp_get_attachment_url( (int) current( array_filter( $media ) ) );
	}

	if ( $media ) {
		$oauth['media'] = $media;
		$response       = util_api_request( 'POST', 'media/upload.json', $oauth );

		unset( $oauth['media'] );

		if ( ! $response['errors'] ) {
			$oauth['media_ids']          = $response['body']['media_id'];
			$oauth['possibly_sensitive'] = (bool) get_post_meta( $id, 'webcomic_twitter_update_sensitive', true );
		}
	}

	$oauth['status']             = $status;
	$oauth['enable_dm_commands'] = false;

	$response = util_api_request( 'POST', 'statuses/update.json', $oauth );

	if ( $response['errors'] ) {
		// Translators: Custom post type name.
		webcomic_notice( sprintf( '<strong>' . __( '%s Twitter Error', 'webcomic' ) . '</strong><br>%s', webcomic( "option.{$comic->post_type}.name" ), implode( '<br>', array_map( 'esc_html', $response['errors'] ) ) ), 'error' );
	}
}

/* ===== Utility Functions ================================================== */

/**
 * Get a Twitter status message for a given post based on the provided format.
 *
 * @param string  $format The status format to use.
 * @param WP_Post $post The post the status message is for.
 * @return string
 * @internal For hook_update_status().
 */
function util_get_status( string $format, WP_Post $post ) : string {
	if ( ! $format ) {
		return $format;
	}

	/**
	 * Alter status tokens.
	 *
	 * This filter allows hooks to provide specific token => value pairs for
	 * Twitter status updates.
	 *
	 * ## Core tokens
	 *
	 * |Token       |Value                                    |Example         |
	 * |------------|-----------------------------------------|----------------|
	 * |%author     |The comic's author.                      |Angie           |
	 * |%collection |The comic's collection.                  |Numina          |
	 * |%#collection|The comic's collection as a hashtag.     |#numina         |
	 * |%date       |The comic's publish date.                |May 1, 2099     |
	 * |%field{name}|The value of the `name` custom field.    |custom value    |
	 * |%shortlink  |The shortlinke to the comic.             |example.com?p=42|
	 * |%site-name  |The site name.                           |Example Site    |
	 * |%site-url   |The site URL.                            |example.com     |
	 * |%taxonomy   |The comic's `taxonomy` terms.            |tag1, tag2      |
	 * |%#taxonomy  |The comic's `taxonomy` terms as hashtags.|#tag1, #tag2    |
	 * |%time       |The comic's publish time.                |4:52 pm         |
	 * |%title      |The comic's title.                       |Page 1          |
	 * |%url        |The permalink to the comic.              |example.com/post|
	 *
	 * ### %field{name}
	 *
	 * When using the `%field{name}` token, `name` is the name of a custom field.
	 * To show the value of a custom field named `special_value`, for example, you
	 * would use `%field{special_value}`.
	 *
	 * ### %taxonomy and %#taxonomy
	 *
	 * When using the `%taxonomy` or `$#taxonomy` tokens, `taxonomy` is the ID of
	 * the terms you want to display. For Tags, use `post_tag`; for Categories,
	 * use `category`. Webcomic taxonomy ID's are always `collectionID_taxonomy`
	 * (e.g. `webcomic1_character` or `webcomic42_storyline`). To show the
	 * characters from a comic collection with an ID of `webcomic1`, you would use
	 * %webcomic1_character (or %#webcomic1_character to show them as hashtags).
	 *
	 * @param array $tokens The list of token => value pairs.
	 * @param string $url The URL being rewritten.
	 * @param WP_Post $post The post the URL is being rewritten for.
	 */
	$tokens = apply_filters( 'webcomic_twitter_status_tokens', [], $format, $post );
	$status = str_replace( array_keys( $tokens ), $tokens, preg_replace( '/^\s+|\s+$/u', '', $format ) );

	if ( function_exists( 'normalizer_normalize' ) ) {
		$status = normalizer_normalize( $status );
	}

	$status = preg_split( '/ /u', $status );
	$links  = [];

	foreach ( $status as $key => $value ) {
		if ( ! $value || ! filter_var( $value, FILTER_VALIDATE_URL ) || ! filter_var( "http://{$value}", FILTER_VALIDATE_URL ) ) {
			continue;
		}

		$tco            = substr( md5( $value ), 0, 23 );
		$links[ $tco ]  = $value;
		$status[ $key ] = $tco;
	}

	$status = implode( ' ', $status );

	if ( 280 < mb_strlen( $status, 'UTF-8' ) ) {
		$status = preg_replace( '/\s+/u', ' ', $status );

		while ( 280 < mb_strlen( $status, 'UTF-8' ) ) {
			$status = preg_split( '/ /u', $status );

			array_pop( $status );

			$status = implode( ' ', $status );
		}
	}

	return html_entity_decode( str_replace( array_keys( $links ), $links, $status ), ENT_QUOTES, 'UTF-8' );
}

/**
 * Make a Twitter API request.
 *
 * @param string $method The method to use, like GET or POST.
 * @param string $endpoint The endpoint to submit the request to.
 * @param array  $args The request arguments.
 * @return array
 * @internal For hook_oauth_authorization() and hook_update_status().
 */
function util_api_request( string $method, string $endpoint, array $args ) : array {
	$args       += [
		'oauth_consumer_key'     => '',
		'oauth_consumer_secret'  => '',
		'oauth_nonce'            => wp_create_nonce( 'webcomic-twitter-api-' . uniqid() ),
		'oauth_signature_method' => 'HMAC-SHA1',
		'oauth_timestamp'        => current_time( 'timestamp' ),
		'oauth_token_secret'     => '',
		'oauth_version'          => '1.0',
	];
	$request_url = str_replace( '1.1/oauth', 'oauth', "https://api.twitter.com/1.1/{$endpoint}" );
	$signing_key = implode( '&', array_map( 'rawurlencode', [ $args['oauth_consumer_secret'], $args['oauth_token_secret'] ] ) );

	unset( $args['oauth_consumer_secret'], $args['oauth_token_secret'] );

	if ( isset( $args['media'] ) ) {
		$request_url = str_replace( '//api.', '//upload.', $request_url );
	}

	$args['oauth_signature'] = util_oauth_get_signature( $signing_key, $method, $request_url, $args );
	$request_query           = util_oauth_get_query( $args );
	$request_args            = [
		'body'       => $request_query,
		'headers'    => [
			'Authorization' => util_oauth_get_header( $args ),
			'Content-Type'  => 'application/x-www-form-urlencoded',
		],
		'method'     => strtoupper( $method ),
		'sslverify'  => true,
		'timeout'    => 60,
		'user-agent' => 'Webcomic ' . webcomic( 'option.version' ) . ' - https://github.com/mgsisk/webcomic/',
	];

	if ( isset( $args['media'] ) ) {
		$content_boundary                        = wp_create_nonce( 'webcomic-twitter-media-' . uniqid() );
		$request_args['body']                    = util_oauth_get_media( $content_boundary, $args );
		$request_args['headers']['Content-Type'] = "multipart/form-data;boundary={$content_boundary}";
	} elseif ( ! in_array( strtoupper( $method ), [ 'PUT', 'POST' ], true ) ) {
		unset( $request_args['body'], $request_args['headers']['Content-Type'] );

		$request_url .= "?{$request_query}";
	}

	return util_api_response( wp_remote_request( $request_url, $request_args ) );
}

/**
 * Parse a Twitter API response.
 *
 * @param array $response The response to parse.
 * @return array
 * @internal For util_api_request().
 */
function util_api_response( array $response ) : array {
	$output = [
		'code'    => (int) wp_remote_retrieve_response_code( $response ),
		'message' => wp_remote_retrieve_response_message( $response ),
		'headers' => wp_remote_retrieve_headers( $response ),
		'body'    => wp_remote_retrieve_body( $response ),
		'errors'  => [],
	];
	$body   = json_decode( $output['body'], true );

	if ( 200 !== $output['code'] ) {
		// Translators: 1: Error code. 2: Error message.
		$output['errors'][] = sprintf( __( 'HTTP Error %1$s: %2$s', 'webcomic' ), $output['code'], $output['message'] );
	}

	if ( $body || ! $output['body'] ) {
		$output['body'] = $body;

		if ( isset( $output['body']['errors'] ) ) {
			foreach ( $output['body']['errors'] as $error ) {
				// Translators: 1: Error code. 2: Error message.
				$output['errors'][] = sprintf( __( 'API Error %1$s: %2$s', 'webcomic' ), $error['code'], $error['message'] );
			}
		}

		return $output;
	}

	$body = [];

	foreach ( explode( '&', $response['body'] ) as $parts ) {
		if ( false === strpos( $parts, '=' ) ) {
			continue;
		}

		list( $key, $value ) = explode( '=', $parts );

		$body[ rawurldecode( $key ) ] = rawurldecode( $value );
	}

	$output['body'] = $body;

	return $output;
}

/**
 * Get an OAuth callback URL.
 *
 * @param string $collection The collection the callback is for.
 * @return string
 * @internal For util_get_account_details().
 */
function util_oauth_get_callback( string $collection ) : string {
	$collection = get_webcomic_collection( $collection );

	if ( ! webcomic_collection_exists( $collection ) ) {
		return '';
	}

	$url = home_url( '/' );

	if ( get_option( 'rewrite_rules' ) ) {
		return "{$url}webcomic-twitter-oauth/{$collection}";
	}

	return add_query_arg(
		[
			'webcomic-twitter-oauth' => $collection,
		], $url
	);
}

/**
 * Get an OAuth request header.
 *
 * @param array $args The request arguments.
 * @return string
 * @internal For util_api_request().
 */
function util_oauth_get_header( array $args ) : string {
	$header = [];

	foreach ( $args as $key => $value ) {
		if ( in_array( $key, [ 'oauth_consumer_secret', '' ], true ) || 0 !== strpos( $key, 'oauth_' ) ) {
			continue;
		}

		$header[] = sprintf( '%1$s="%2$s"', rawurlencode( $key ), rawurlencode( $value ) );
	}

	usort( $header, 'strcmp' );

	return 'OAuth ' . implode( ',', $header );
}

/**
 * Get an OAuth media request.
 *
 * @param string $content_boundary The request content boundary.
 * @param array  $args The request arguments.
 * @return string
 * @internal For util_api_request().
 */
function util_oauth_get_media( string $content_boundary, array $args ) : string {
	$request = [
		"--{$content_boundary}",
		sprintf( 'Content-Disposition: form-data; name="media"; filename="%s"', $args['media'] ),
		"Content-Type: application/octet-stream\r\n",
		// @codingStandardsIgnoreLine WordPress.WP.AlternativeFunctions - file_get_contents() is probably the best option here; see https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/943
		file_get_contents( $args['media'] ),
		"--{$content_boundary}--",
	];

	return implode( "\r\n", $request );
}

/**
 * Get an OAuth request query.
 *
 * @param array $args The request arguments.
 * @return string
 * @internal For util_api_request().
 */
function util_oauth_get_query( array $args ) : string {
	$query = [];

	foreach ( $args as $key => $value ) {
		if ( 'media' === $key || 0 === strpos( $key, 'oauth_' ) ) {
			continue;
		}

		$query[] = sprintf( '%1$s=%2$s', rawurlencode( $key ), rawurlencode( $value ) );
	}

	usort( $query, 'strcmp' );

	return implode( '&', $query );
}

/**
 * Get an OAuth request signature.
 *
 * @param string $signing_key The signing key to use.
 * @param string $method The request method.
 * @param string $request_url The complete request URL.
 * @param array  $args The request arguments.
 * @return string
 * @internal For util_api_request().
 */
function util_oauth_get_signature( string $signing_key, string $method, string $request_url, array $args ) : string {
	$parameters = [];

	foreach ( $args as $key => $value ) {
		if ( in_array( $key, [ 'oauth_consumer_secret', 'oauth_signature', 'oauth_token_secret', 'realm' ], true ) || ( isset( $args['media'] ) && 0 !== strpos( $key, 'oauth_' ) ) ) {
			continue;
		}

		$parameters[] = sprintf( '%1$s=%2$s', rawurlencode( $key ), rawurlencode( $value ) );
	}

	usort( $parameters, 'strcmp' );

	$base_string = implode( '&', array_map( 'rawurlencode', [ strtoupper( $method ), $request_url, implode( '&', $parameters ) ] ) );

	return base64_encode( hash_hmac( 'sha1', $base_string, $signing_key, true ) );
}

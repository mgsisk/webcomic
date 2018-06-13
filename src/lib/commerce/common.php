<?php
/**
 * Standard commerce functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Commerce;

use const Mgsisk\Webcomic\Collection\ENDPOINT;

/**
 * Add common hooks.
 *
 * @return void
 */
function common() {
	add_filter( 'init', __NAMESPACE__ . '\hook_register_post_type' );
	add_filter( 'init', __NAMESPACE__ . '\hook_add_rewrite_endpoints' );
	add_filter( 'init', __NAMESPACE__ . '\hook_capture_ipn', 99 );
	add_filter( 'widgets_init', __NAMESPACE__ . '\hook_register_widgets' );
	add_filter( 'template_include', __NAMESPACE__ . '\hook_prints' );
	add_filter( 'template_include', __NAMESPACE__ . '\hook_cart' );
	add_filter( 'template_include', __NAMESPACE__ . '\hook_donate' );
}

/**
 * Register IPN custom post type.
 *
 * @return void
 */
function hook_register_post_type() {
	register_post_type(
		'webcomic_ipn', [
			'public'   => false,
			'supports' => false,
		]
	);
}

/**
 * Add commerce rewrite endpoints.
 *
 * @return void
 * @suppress PhanUndeclaredConstant - ENDPOINT incorrectly triggers this.
 */
function hook_add_rewrite_endpoints() {
	add_rewrite_endpoint( 'print', ENDPOINT );
	add_rewrite_endpoint( 'webcomic-cart', EP_ROOT );
	add_rewrite_endpoint( 'webcomic-donation', EP_ROOT );
}

/**
 * Capture IPN events.
 *
 * @return void
 */
function hook_capture_ipn() {
	if ( ! webcomic( 'GLOBALS._REQUEST.webcomic_ipn' ) || ! webcomic( 'GLOBALS._POST' ) ) {
		return;
	}

	$url  = 'https://www.paypal.com/cgi-bin/webscr';
	$host = 'www.paypal.com';
	$body = 'cmd=' . rawurlencode( '_notify-validate' );

	foreach ( webcomic( 'GLOBALS._POST' ) as $key => $value ) {
		$body .= "&{$key}=" . rawurlencode( $value );
	}

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG && webcomic( 'options.debug' ) ) {
		$url  = str_replace( 'www.', 'www.sandbox.', $url );
		$host = str_replace( 'www.', 'www.sandbox.', $host );
	}

	$request = wp_remote_post(
		$url, [
			'body'      => $body,
			'headers'   => [
				'Host' => $host,
			],
			'sslverify' => true,
		]
	);

	util_process_ipn( $request, wp_remote_retrieve_body( $request ), webcomic( 'GLOBALS._POST' ) );
}

/**
 * Register commerce widgets.
 *
 * @return void
 */
function hook_register_widgets() {
	register_widget( __NAMESPACE__ . '\Widget\WebcomicCollectionCartLink' );
	register_widget( __NAMESPACE__ . '\Widget\WebcomicCollectionDonationLink' );
	register_widget( __NAMESPACE__ . '\Widget\WebcomicPrintsList' );
}

/**
 * Handle /prints endpoints for comics.
 *
 * @param string $template The template to load.
 * @return string|null
 */
function hook_prints( string $template ) {
	if ( null === get_query_var( 'print', null ) || ! is_webcomic() ) {
		return $template;
	}

	$print = get_query_var( 'print' );
	$comic = get_webcomic();

	if ( ! has_webcomic_print( $print, $comic ) ) {
		return locate_template( '404.php' );
	}

	$args = [
		'file'   => __DIR__ . '/common-inc-redirect.php',
		'action' => 'https://www.paypal.com/cgi-bin/webscr',
		'fields' => [
			'amount'        => (float) get_webcomic_print_price( $print, $comic ),
			'business'      => webcomic( "option.{$comic->post_type}.commerce_business" ),
			'cmd'           => '_xclick',
			'currency_code' => webcomic( "option.{$comic->post_type}.commerce_currency" ),
			'item_name'     => substr( get_the_title(), 0, 127 ),
			'notify_url'    => esc_url(
				add_query_arg(
					[
						'webcomic_ipn' => true,
					], home_url( '/' )
				)
			),
			'item_number'   => substr( "{$comic->ID}-{$print}", 0, 127 ),
			'return'        => get_permalink(),
		],
	];

	if ( webcomic( "option.{$comic->post_type}.commerce_cart" ) ) {
		$args['fields']['add']          = 1;
		$args['fields']['cmd']          = '_cart';
		$args['fields']['shoppint_url'] = get_permalink();
	}

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG && webcomic( 'options.debug' ) ) {
		$args['action'] = str_replace( 'www.', 'www.sandbox.', $args['action'] );
	}

	require $args['file'];

	add_filter( 'wp_doing_ajax', '__return_true' ) && wp_die();
}

/**
 * Handle /webcomic-cart endpoints for the site front page.
 *
 * @param string $template The template to load.
 * @return string|null
 */
function hook_cart( string $template ) {
	if ( null === get_query_var( 'webcomic-cart', null ) || ! is_front_page() ) {
		return $template;
	} elseif ( ! get_query_var( 'webcomic-cart' ) || ! webcomic_collection_exists( get_query_var( 'webcomic-cart' ) ) ) {
		return locate_template( '404.php' );
	}

	$collection = get_query_var( 'webcomic-cart' );

	if ( ! webcomic( "option.{$collection}.commerce_cart" ) ) {
		return locate_template( '404.php' );
	}

	$args = [
		'file'   => __DIR__ . '/common-inc-redirect.php',
		'action' => 'https://www.paypal.com/cgi-bin/webscr',
		'fields' => [
			'business'     => webcomic( "option.{$collection}.commerce_business" ),
			'cmd'          => '_cart',
			'display'      => 1,
			'shopping_url' => home_url( '/' ),
		],
	];

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG && webcomic( 'options.debug' ) ) {
		$args['action'] = str_replace( 'www.', 'www.sandbox.', $args['action'] );
	}

	require $args['file'];

	add_filter( 'wp_doing_ajax', '__return_true' ) && wp_die();
}

/**
 * Handle /webcomic-donation endpoints for the site front page.
 *
 * @param string $template The template to load.
 * @return string|null
 */
function hook_donate( string $template ) {
	if ( null === get_query_var( 'webcomic-donation', null ) || ! is_front_page() ) {
		return $template;
	} elseif ( ! get_query_var( 'webcomic-donation' ) || ! webcomic_collection_exists( get_query_var( 'webcomic-donation' ) ) ) {
		return locate_template( '404.php' );
	}

	$collection = get_query_var( 'webcomic-donation' );
	$args       = [
		'file'   => __DIR__ . '/common-inc-redirect.php',
		'action' => 'https://www.paypal.com/cgi-bin/webscr',
		'fields' => [
			'business'      => webcomic( "option.{$collection}.commerce_business" ),
			'cmd'           => '_donations',
			'currency_code' => webcomic( "option.{$collection}.commerce_currency" ),
			'item_name'     => substr( webcomic( "option.{$collection}.name" ), 0, 127 ),
			'notify_url'    => esc_url(
				add_query_arg(
					[
						'webcomic_ipn' => true,
					], home_url( '/' )
				)
			),
			'item_number'   => substr( $collection, 0, 127 ),
			'return'        => home_url( '/' ),
		],
	];

	if ( webcomic( "option.{$collection}.commerce_donation" ) ) {
		$args['fields']['amount'] = webcomic( "option.{$collection}.commerce_donation" );
	}

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG && webcomic( 'options.debug' ) ) {
		$args['action'] = str_replace( 'www.', 'www.sandbox.', $args['action'] );
	}

	require $args['file'];

	add_filter( 'wp_doing_ajax', '__return_true' ) && wp_die();
}

/* ===== Utility Functions ================================================== */

/**
 * Log an IPN event.
 *
 * @param string $title The IPN transaction ID.
 * @param string $date The IPN date.
 * @param string $excerpt The IPN excerpt.
 * @param string $content The IPN content.
 * @return void
 * @internal For hook_capture_ipn().
 */
function util_log_ipn( string $title, string $date, string $excerpt, string $content ) {
	wp_insert_post(
		[
			'post_author'   => 1,
			'post_content'  => $content,
			'post_date_gmt' => get_gmt_from_date( $date ),
			'post_date'     => $date,
			'post_excerpt'  => $excerpt,
			'post_name'     => sanitize_title( $title ),
			'post_status'   => 'publish',
			'post_title'    => $title,
			'post_type'     => 'webcomic_ipn',
		],
		true
	);
}

/**
 * Process an instant payment notification.
 *
 * @param array  $request The IPN request.
 * @param string $response The IPN response.
 * @param array  $ipn The IPN data to process.
 * @return void
 * @internal For hook_capture_ipn().
 */
function util_process_ipn( array $request, string $response, array $ipn ) {
	$date = date( 'Y-m-d H:i:s', strtotime( $ipn['payment_date'] ) );

	if ( 200 !== wp_remote_retrieve_response_code( $request ) ) {
		// Translators: 1: HTTP response code. 2: HTTP response message.
		util_log_ipn( $ipn['txn_id'], $date, $ipn['txn_id'], sprintf( __( 'HTTP ERROR %1$s - %2$s', 'webcomic' ), wp_remote_retrieve_response_code( $request ), wp_remote_retrieve_response_message( $request ) ) );

		return;
	} elseif ( false !== strpos( $response, 'INVALID' ) ) {
		util_log_ipn( $ipn['txn_id'], $date, $ipn['txn_id'], __( 'IPN ERROR - Invalid response.', 'webcomic' ) );

		return;
	} elseif ( false === strpos( $response, 'VERIFIED' ) ) {
		util_log_ipn( $ipn['txn_id'], $date, $ipn['txn_id'], __( 'IPN ERROR - Unrecognized response.', 'webcomic' ) );

		return;
	} elseif ( 'Completed' !== $ipn['payment_status'] ) {
		util_log_ipn( $ipn['txn_id'], $date, $ipn['txn_id'], __( 'TXN ERROR - Transaction incomplete.', 'webcomic' ) );

		return;
	} elseif ( get_page_by_title( $ipn['txn_id'], OBJECT, 'webcomic_ipn' ) ) {
		util_log_ipn( $ipn['txn_id'], $date, $ipn['txn_id'], __( 'TXN ERROR - Transaction already completed.', 'webcomic' ) );

		return;
	} elseif ( 'cart' === $ipn['txn_type'] ) {
		util_log_ipn( $ipn['txn_id'], $date, __( 'Shopping Cart', 'webcomic' ), util_process_cart_ipn( $ipn ) );

		return;
	} elseif ( preg_match( '/^\d+/', $ipn['item_number'] ) ) {
		util_log_ipn( $ipn['txn_id'], $date, $ipn['item_number'], util_process_print_ipn( $ipn ) );

		return;
	}

	util_log_ipn( $ipn['txn_id'], $date, $ipn['item_number'], util_process_donation_ipn( $ipn ) );
}

/**
 * Process shopping cart IPN's.
 *
 * @param array $ipn The IPN data to process.
 * @return string
 * @internal For hook_capture_ipn().
 */
function util_process_cart_ipn( array $ipn ) : string {
	$item   = 1;
	$output = '<dl>';

	while ( isset( $ipn[ "item_number{$item}" ] ) ) {
		$sub_ipn = [
			'item_number'    => $ipn[ "item_number{$item}" ],
			'mc_currency'    => $ipn['mc_currency'],
			'mc_gross'       => $ipn[ "mc_gross_{$item}" ],
			'quantity'       => $ipn[ "quantity{$item}" ],
			'receiver_email' => $ipn['receiver_email'],
		];

		$message = util_process_print_ipn( $sub_ipn );
		$output .= "<dt>{$sub_ipn['item_number']}</dt><dd>{$message}</dd>";
		$item++;
	}

	return $output . '</dl>';
}

/**
 * Process single print IPN's.
 *
 * @param array $ipn The IPN data to process.
 * @return string
 * @internal For hook_capture_ipn().
 */
function util_process_print_ipn( array $ipn ) : string {
	$item  = explode( '-', $ipn['item_number'], 2 );
	$comic = get_webcomic( $item[0] );

	if ( ! $comic ) {
		// Translators: Post ID.
		return sprintf( __( 'TXN ERROR - Invalid comic ID %s', 'webcomic' ), $item[0] );
	} elseif ( webcomic( "option.{$comic->post_type}.commerce_business" ) !== $ipn['receiver_email'] ) {
		// Translators: 1: Incorrect business email. 2: Correct business email.
		return sprintf( __( 'TXN ERROR - Incorrect business email %1$s; expected %2$s.', 'webcomic' ), $ipn['receiver_email'], webcomic( "option.{$comic->post_type}.commerce_business" ) );
	} elseif ( webcomic( "option.{$comic->post_type}.commerce_currency" ) !== $ipn['mc_currency'] ) {
		// Translators: 1: Incorrect currency code. 2: Correct currency code.
		return sprintf( __( 'TXN ERROR - Incorrect currency %1$s; expected %2$s.', 'webcomic' ), $ipn['mc_currency'], webcomic( "option.{$comic->post_type}.commerce_currency" ) );
	}

	$price = get_webcomic_print_price( $item[1], $comic );
	$gross = number_format_i18n( $ipn['mc_gross'], 2 );

	if ( $gross !== $price ) {
		// Translators: 1: Incorrect currency code. 2: Correct currency code.
		return sprintf( __( 'TXN ERROR - Incorrect price %1$s; expected %2$s (%3$1 &times; %4$s).', 'webcomic' ), $gross, $price, number_format_i18n( $price / $ipn['quantity'], 2 ), $ipn['quantity'] );
	}

	$sold = (int) get_post_meta( $comic->ID, "webcomic_commerce_prints_sold_{$item[1]}", true ) + 1;

	update_post_meta( $comic->ID, "webcomic_commerce_prints_sold_{$item[1]}", $sold );

	if ( webcomic( "option.{$comic->post_type}.commerce_prints.{$item[1]}.stock" ) && webcomic( "option.{$comic->post_type}.commerce_prints.{$item[1]}.stock" ) <= $sold ) {
		delete_post_meta( $comic->ID, 'webcomic_commerce_prints', $item[1] );
	}

	return __( 'Sale Get!', 'webcomic' );
}

/**
 * Process donation IPN's.
 *
 * @param array $ipn The IPN data to process.
 * @return string
 * @internal For hook_capture_ipn().
 */
function util_process_donation_ipn( array $ipn ) : string {
	if ( ! webcomic_collection_exists( $ipn['item_number'] ) ) {
		// Translators: Post ID.
		return sprintf( __( 'TXN ERROR - Invalid comic collection %s', 'webcomic' ), $ipn['item_number'] );
	} elseif ( webcomic( "option.{$ipn['item_number']}.commerce_business" ) !== $ipn['receiver_email'] ) {
		// Translators: 1: Incorrect business email. 2: Correct business email.
		return sprintf( __( 'TXN ERROR - Incorrect business email %1$s; expected %2$s.', 'webcomic' ), $ipn['receiver_email'], webcomic( "option.{$ipn['item_number']}.commerce_business" ) );
	} elseif ( webcomic( "option.{$ipn['item_number']}.commerce_currency" ) !== $ipn['mc_currency'] ) {
		// Translators: 1: Incorrect currency code. 2: Correct currency code.
		return sprintf( __( 'TXN ERROR - Incorrect currency %1$s; expected %2$s.', 'webcomic' ), $ipn['mc_currency'], webcomic( "option.{$ipn['item_number']}.commerce_currency" ) );
	}

	$donation = webcomic( "option.{$ipn['item_number']}.commerce_donation" );
	$gross    = number_format_i18n( $ipn['mc_gross'], 2 );

	if ( $donation && number_format_i18n( $donation, 2 ) !== $gross ) {
		// Translators: 1: Incorrect currency code. 2: Correct currency code.
		return sprintf( __( 'TXN ERROR - Incorrect donation %1$s; expected %2$s.', 'webcomic' ), $gross, $donation );
	}

	return __( 'Donation Get!', 'webcomic' );
}

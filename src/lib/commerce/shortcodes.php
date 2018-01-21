<?php
/**
 * Commerce shortcodes
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Commerce;

/**
 * Add shortcodes.
 *
 * @return void
 */
function shortcodes() {
	add_shortcode( 'webcomic_collection_cart_link', __NAMESPACE__ . '\webcomic_collection_cart_link_shortcode' );
	add_shortcode( 'webcomic_collection_currency', __NAMESPACE__ . '\webcomic_collection_currency_shortcode' );
	add_shortcode( 'webcomic_collection_donation', __NAMESPACE__ . '\webcomic_collection_donation_shortcode' );
	add_shortcode( 'webcomic_collection_donation_link', __NAMESPACE__ . '\webcomic_collection_donation_link_shortcode' );
	add_shortcode( 'webcomic_collection_print_name', __NAMESPACE__ . '\webcomic_collection_print_name_shortcode' );
	add_shortcode( 'webcomic_collection_print_price', __NAMESPACE__ . '\webcomic_collection_print_price_shortcode' );
	add_shortcode( 'webcomic_collection_print_stock', __NAMESPACE__ . '\webcomic_collection_print_stock_shortcode' );
	add_shortcode( 'webcomic_print_adjust', __NAMESPACE__ . '\webcomic_print_adjust_shortcode' );
	add_shortcode( 'webcomic_print_left', __NAMESPACE__ . '\webcomic_print_left_shortcode' );
	add_shortcode( 'webcomic_print_price', __NAMESPACE__ . '\webcomic_print_price_shortcode' );
	add_shortcode( 'webcomic_print_sold', __NAMESPACE__ . '\webcomic_print_sold_shortcode' );
	add_shortcode( 'webcomic_prints_list', __NAMESPACE__ . '\webcomic_prints_list_shortcode' );
	add_shortcode( 'has_webcomic_print', __NAMESPACE__ . '\has_webcomic_print_shortcode' );
}

/**
 * Display a collection cart link.
 *
 * @uses get_webcomic_collection_cart_link()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type string $link Optional link text, like 'before{{text}}after'.
 *     @type mixed  $collection Optional collection to get a cart link for.
 * }
 * @param string $content Optional shortcode content; mapped to $args['link'].
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_collection_cart_link_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'link'       => '',
			'collection' => null,
		], $atts, $name
	);

	if ( $content ) {
		$args['link'] = do_shortcode( htmlspecialchars_decode( $content ) );
	}

	$args['link'] = htmlspecialchars_decode( $args['link'] );

	return get_webcomic_collection_cart_link( $args['link'], $args['collection'] );
}

/**
 * Display the collection currency.
 *
 * @uses get_webcomic_collection_currency()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed $collection Optional collection to get the currency for.
 * }
 * @param string $content Optional shortcode content; mapped to $args['link'].
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_collection_currency_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'collection' => null,
		], $atts, $name
	);

	return get_webcomic_collection_currency( $args['collection'] );
}

/**
 * Display a collection donation amount.
 *
 * @uses get_webcomic_collection_donation()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed $collection Optional collection to get a donation amount for.
 * }
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_collection_donation_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'collection' => null,
		], $atts, $name
	);

	return get_webcomic_collection_donation( $args['collection'] );
}

/**
 * Display a collection donation link.
 *
 * @uses get_webcomic_collection_donation_link()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type string $link Optional link text, like 'before{{text}}after'.
 *     @type mixed  $collection Optional collection to get a donation link for.
 * }
 * @param string $content Optional shortcode content; mapped to $args['link'].
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_collection_donation_link_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'link'       => '',
			'collection' => null,
		], $atts, $name
	);

	if ( $content ) {
		$args['link'] = do_shortcode( htmlspecialchars_decode( $content ) );
	}

	$args['link'] = htmlspecialchars_decode( $args['link'] );

	return get_webcomic_collection_donation_link( $args['link'], $args['collection'] );
}

/**
 * Display a collection print name.
 *
 * @uses get_webcomic_collection_print_name()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type string $type The print name to get.
 *     @type mixed  $collection Optional collection to get the print name from.
 * }
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_collection_print_name_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'type'       => '',
			'collection' => null,
		], $atts, $name
	);

	return get_webcomic_collection_print_name( $args['type'], $args['collection'] );
}

/**
 * Display a collection print price.
 *
 * @uses get_webcomic_collection_print_price()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type string $type The print price to get.
 *     @type mixed  $collection Optional collection to get the print price from.
 * }
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_collection_print_price_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'type'       => '',
			'collection' => null,
		], $atts, $name
	);

	return get_webcomic_collection_print_price( $args['type'], $args['collection'] );
}

/**
 * Display a collection print stock.
 *
 * @uses get_webcomic_collection_print_stock()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type string $type The print stock to get.
 *     @type mixed  $collection Optional collection to get the print stock from.
 * }
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_collection_print_stock_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'type'       => '',
			'collection' => null,
		], $atts, $name
	);

	return get_webcomic_collection_print_stock( $args['type'], $args['collection'] );
}

/**
 * Display a comic print adjustment.
 *
 * @uses get_webcomic_print_adjust()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type string $type The print price adjustment to get.
 *     @type mixed  $post Optional post to get the price adjustment from.
 * }
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_print_adjust_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'type' => '',
			'post' => null,
		], $atts, $name
	);

	return (string) get_webcomic_print_adjust( $args['type'], $args['post'] );
}

/**
 * Display a comic print left count.
 *
 * @uses get_webcomic_print_left()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type string $type The print left count to get.
 *     @type mixed  $post Optional post to get the left count from.
 * }
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_print_left_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'type' => '',
			'post' => null,
		], $atts, $name
	);

	return get_webcomic_print_left( $args['type'], $args['post'] );
}

/**
 * Display a comic print price.
 *
 * @uses get_webcomic_print_price()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type string $type The print price to get.
 *     @type mixed  $post Optional post to get the price from.
 * }
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_print_price_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'type' => '',
			'post' => null,
		], $atts, $name
	);

	return get_webcomic_print_price( $args['type'], $args['post'] );
}

/**
 * Display a comic print adjustment.
 *
 * @uses get_webcomic_print_sold()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type string $type The print sold count to get.
 *     @type mixed  $post Optional post to get the sold count from.
 * }
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_print_sold_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'type' => '',
			'post' => null,
		], $atts, $name
	);

	return (string) get_webcomic_print_sold( $args['type'], $args['post'] );
}

/**
 * Display a list of comic prints.
 *
 * @uses get_webcomic_prints_list()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type string $format Optional list format, like before{{join}}after.
 *                          Including `<select>` or `<optgroup>` elements will
 *                          convert links to `<option>` elements.
 *     @type string $link Optional link text, like before{{text}}after.
 *     @type mixed  $link_post Optional reference post for print links.
 *     @type array  $link_args Optional arguments for print links.
 *     @type int    $cloud_min Optional weighted list minimum font size.
 *     @type int    $cloud_max Optional weighted list maximum font size.
 *     @type string $cloud_val Optional print value to use for cloud
 *                             calculations; one of sold, adjust, base, left,
 *                             price, or stock.
 *     @type string $walker Optional custom Walker class to use instead of
 *                          Mgsisk\Webcomic\Commerce\Walker\PrintLister.
 *     @type mixed  $post Optional post to get prints for.
 *     @type string $order How to sort prints; one of asc or desc.
 *     @type string $limit Whether to limit the number of prints returned.
 *     @type string $orderby What to sort prints by; one of name, adjust,
 *                           base, left, price, sold, slug, or stock.
 *     @type bool   $hide_empty Whether to include or exclude sold out prints.
 *     @type bool   $hide_infinite Whether to include or exclude prints with
 *                                 infinite stock.
 *     @type int    $adjust_min Optional minimum price adjustment prints must
 *                              have.
 *     @type int    $adjust_max Optional maximum price adjustment prints must
 *                              have.
 *     @type float  $base_min Optional minimum base price prints must have.
 *     @type float  $base_max Optional maximum base price prints must have.
 *     @type int    $left_min Optional minimum left count prints must have.
 *     @type int    $left_max Optional maximum left count prints must have.
 *     @type float  $price_min Optional minimum price prints must have.
 *     @type float  $price_max Optional maximum price prints must have.
 *     @type int    $sold_min Optional minimum sold count prints must have.
 *     @type int    $sold_max Optional maximum sold count prints must have.
 *     @type int    $stock_min Optional minimum stock prints must have.
 *     @type int    $stock_max Optional maximum stock prints must have.
 *     @type array  $slug__in Optional slugs the collections must match.
 *     @type array  $slug__not_in Optional slugs the collections must not
 *                                match.
 * }
 * @param string $content Unused shortcode content.
 * @param string $name Shortcode name.
 * @return string
 */
function webcomic_prints_list_shortcode( $atts, string $content, string $name ) : string {
	$args = shortcode_atts(
		[
			'adjust_max'    => null,
			'adjust_min'    => null,
			'base_max'      => null,
			'base_min'      => null,
			'cloud_max'     => null,
			'cloud_min'     => null,
			'cloud_val'     => 'sold',
			'format'        => ', ',
			'hide_empty'    => true,
			'hide_infinite' => false,
			'left_max'      => null,
			'left_min'      => null,
			'limit'         => 0,
			'link_args'     => [],
			'link_post'     => null,
			'link'          => '%print-name - %print-price %print-currency',
			'order'         => 'asc',
			'orderby'       => 'name',
			'post'          => null,
			'price_max'     => null,
			'price_min'     => null,
			'slug__in'      => '',
			'slug__not_in'  => '',
			'sold_max'      => null,
			'sold_min'      => null,
			'stock_max'     => null,
			'stock_min'     => null,
			'walker'        => '',
		], $atts, $name
	);

	foreach ( $args as $key => $value ) {
		if ( false === strpos( $key, '_min' ) && false === strpos( $key, '_max' ) ) {
			continue;
		} elseif ( null === $value ) {
			unset( $args[ $key ] );

			continue;
		}

		$args[ $key ] = (int) $value;
	}

	$args['format']    = htmlspecialchars_decode( $args['format'] );
	$args['cloud_min'] = (int) $args['cloud_min'];
	$args['cloud_max'] = (int) $args['cloud_max'];

	if ( $content ) {
		$args['link'] = do_shortcode( htmlspecialchars_decode( $content ) );
	}

	$args['link'] = htmlspecialchars_decode( $args['link'] );

	if ( is_string( $args['link_args'] ) ) {
		parse_str( htmlspecialchars_decode( $args['link_args'] ), $args['link_args'] );
	}

	return get_webcomic_prints_list( $args );
}

/**
 * Display content if the post has a comic print.
 *
 * @uses has_webcomic_print()
 * @param array  $atts {
 *     Optional attributes.
 *
 *     @type mixed $type Optional print to check for.
 *     @type mixed $post Optional post to check for prints.
 * }
 * @param string $content Content to display if the post has a comic print.
 * @param string $name Shortcode name.
 * @return string
 */
function has_webcomic_print_shortcode( $atts, string $content, string $name ) : string {
	if ( ! $content ) {
		return '';
	}

	$args = shortcode_atts(
		[
			'type' => null,
			'post' => null,
		], $atts, $name
	);

	if ( ! has_webcomic_print( $args['type'], $args['post'] ) ) {
		return '';
	}

	return do_shortcode( $content );
}

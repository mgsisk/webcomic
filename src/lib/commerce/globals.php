<?php
/**
 * Global commerce functions
 *
 * @package Webcomic
 */

/**
 * Get a collection cart URL.
 *
 * @param mixed $collection Optional collection to get a cart URL for.
 * @return string
 */
function get_webcomic_collection_cart_url( $collection = null ) : string {
	$collection = get_webcomic_collection( $collection );

	if ( ! webcomic( "option.{$collection}.commerce_cart" ) ) {
		return '';
	}

	$url = home_url( '/' );

	if ( get_option( 'rewrite_rules' ) ) {
		return "{$url}webcomic-cart/{$collection}";
	}

	return add_query_arg(
		[
			'webcomic-cart' => $collection,
		], $url
	);
}

/**
 * Get a collection cart link.
 *
 * @param string $link Optional link text, like 'before{{text}}after'.
 * @param mixed  $collection Optional collection to get a cart link for.
 * @return string
 */
function get_webcomic_collection_cart_link( string $link = '', $collection = null ) : string {
	$url = get_webcomic_collection_cart_url( $collection );

	if ( ! $url ) {
		return '';
	} elseif ( ! $link ) {
		$link = esc_html__( 'View Cart', 'webcomic' );
	}

	preg_match( '/^(.*){{(.*)}}(.*)$/s', $link, $match );

	$match     += [ '', '', $link, '' ];
	$collection = get_webcomic_collection( $collection );

	/**
	 * Alter the link class.
	 *
	 * This filter allows hooks to alter the CSS classes assigned to the link.
	 *
	 * @param array  $class The CSS classes.
	 * @param string $collection The collection the link is for.
	 */
	$class = apply_filters( 'get_webcomic_collection_cart_link_class', [ 'webcomic-collection-cart-link', "{$collection}-collection-cart-link" ], $collection );
	$class = implode( ' ', array_unique( array_map( 'esc_attr', $class ) ) );
	/* This filter is documented in Mgsisk\Webcomic\Collection::get_webcomic_collection_link() */
	$tokens = apply_filters( 'get_webcomic_collection_link_tokens', [], $match[2], $collection );
	$anchor = str_replace( array_keys( $tokens ), $tokens, $match[2] );

	return "{$match[1]}<a href='{$url}' class='{$class}'>{$anchor}</a>{$match[3]}";
}

/**
 * Get the collection currency.
 *
 * @param mixed $collection Optional collection to get the currency for.
 * @return string
 */
function get_webcomic_collection_currency( $collection = null ) : string {
	$collection = get_webcomic_collection( $collection );

	if ( ! $collection ) {
		return '';
	}

	return webcomic( "option.{$collection}.commerce_currency" );
}

/**
 * Get a collection donation amount.
 *
 * @param mixed $collection Optional collection to get a donation amount for.
 * @return string
 */
function get_webcomic_collection_donation( $collection = null ) : string {
	$collection = get_webcomic_collection( $collection );

	if ( ! $collection ) {
		return '';
	}

	$donation = webcomic( "option.{$collection}.commerce_donation" );

	if ( ! $donation ) {
		return '';
	}

	return number_format_i18n( $donation, 2 );
}

/**
 * Get a collection donation URL.
 *
 * @param mixed $collection Optional collection to get a donation URL for.
 * @return string
 */
function get_webcomic_collection_donation_url( $collection = null ) : string {
	$collection = get_webcomic_collection( $collection );

	if ( ! $collection ) {
		return '';
	}

	$url = home_url( '/' );

	if ( get_option( 'rewrite_rules' ) ) {
		return "{$url}webcomic-donation/{$collection}";
	}

	return add_query_arg(
		[
			'webcomic-donation' => $collection,
		], $url
	);
}

/**
 * Get a collection donation link.
 *
 * @param string $link Optional link text, like 'before{{text}}after'.
 * @param mixed  $collection Optional collection to get a donation link for.
 * @return string
 */
function get_webcomic_collection_donation_link( string $link = '', $collection = null ) : string {
	$url = get_webcomic_collection_donation_url( $collection );

	if ( ! $url ) {
		return '';
	} elseif ( ! $link ) {
		$link = esc_html__( 'Donate to %title', 'webcomic' );
	}

	preg_match( '/^(.*){{(.*)}}(.*)$/s', $link, $match );

	$match     += [ '', '', $link, '' ];
	$collection = get_webcomic_collection( $collection );

	/**
	 * Alter the link class.
	 *
	 * This filter allows hooks to alter the CSS classes assigned to the link.
	 *
	 * @param array  $class The CSS classes.
	 * @param string $collection The collection the link is for.
	 */
	$class = apply_filters( 'get_webcomic_collection_donation_link_class', [ 'webcomic-collection-donation-link', "{$collection}-collection-donation-link" ], $collection );
	$class = implode( ' ', array_unique( array_map( 'esc_attr', $class ) ) );
	/* This filter is documented in Mgsisk\Webcomic\Collection::get_webcomic_collection_link() */
	$tokens = apply_filters( 'get_webcomic_collection_link_tokens', [], $match[2], $collection );
	$anchor = str_replace( array_keys( $tokens ), $tokens, $match[2] );

	return "{$match[1]}<a href='{$url}' class='{$class}'>{$anchor}</a>{$match[3]}";
}

/**
 * Get collection print data.
 *
 * @param string $type The print data to get.
 * @param mixed  $collection Optional collection to get print data for.
 * @return array
 */
function get_webcomic_collection_print( string $type, $collection = null ) : array {
	$collection = get_webcomic_collection( $collection );

	return webcomic( "option.{$collection}.commerce_prints.{$type}" );
}

/**
 * Get a collection print name.
 *
 * @param string $type The print name to get.
 * @param mixed  $collection Optional collection to get the print name from.
 * @return string
 */
function get_webcomic_collection_print_name( string $type, $collection = null ) : string {
	$collection = get_webcomic_collection( $collection );

	if ( ! $collection ) {
		return '';
	}

	$name = webcomic( "option.{$collection}.commerce_prints.{$type}.name" );

	return (string) $name;
}

/**
 * Get a collection print price.
 *
 * @param string $type The print price to get.
 * @param mixed  $collection Optional collection to get the print price from.
 * @return string
 */
function get_webcomic_collection_print_price( string $type, $collection = null ) : string {
	$collection = get_webcomic_collection( $collection );

	if ( ! $collection ) {
		return '';
	}

	$price = webcomic( "option.{$collection}.commerce_prints.{$type}.price" );

	if ( ! $price ) {
		return '';
	}

	return number_format_i18n( $price, 2 );
}

/**
 * Get a collection print stock.
 *
 * @param string $type The print stock to get.
 * @param mixed  $collection Optional collection to get the print stock from.
 * @return string
 */
function get_webcomic_collection_print_stock( string $type, $collection = null ) : string {
	$collection = get_webcomic_collection( $collection );

	if ( ! $collection ) {
		return '';
	}

	$stock = webcomic( "option.{$collection}.commerce_prints.{$type}.stock" );

	if ( null === $stock ) {
		return '';
	} elseif ( ! $stock ) {
		return '&infin;';
	}

	return (string) $stock;
}

/**
 * Get collection prints data.
 *
 * @param mixed $collection Optional collection to get print data for.
 * @return array
 */
function get_webcomic_collection_prints( $collection = null ) : array {
	$collection = get_webcomic_collection( $collection );

	return webcomic( "option.{$collection}.commerce_prints" );
}

/**
 * Get a comic print price adjustment.
 *
 * @param string $type The print price adjustment to get.
 * @param mixed  $post Optional post to get the price adjustment from.
 * @return int
 */
function get_webcomic_print_adjust( string $type, $post = null ) : int {
	$comic = get_webcomic( $post );

	if ( ! $comic ) {
		return 0;
	}

	$adjust = (int) get_post_meta( $comic->ID, "webcomic_commerce_prints_adjust_{$type}", true );

	return $adjust;
}

/**
 * Get a comic print left count.
 *
 * @param string $type The print left count to get.
 * @param mixed  $post Optional post to get the left count from.
 * @return string
 */
function get_webcomic_print_left( string $type, $post = null ) : string {
	$comic = get_webcomic( $post );

	if ( ! $comic ) {
		return '';
	}

	$stock = webcomic( "option.{$comic->post_type}.commerce_prints.{$type}.stock" );

	if ( null === $stock ) {
		return '';
	} elseif ( ! $stock ) {
		return '&infin;';
	}

	$sold = (int) get_post_meta( $comic->ID, "webcomic_commerce_prints_sold_{$type}", true );
	$left = $stock - $sold;

	return (string) $left;
}

/**
 * Get a comic print price.
 *
 * @param string $type The print price to get.
 * @param mixed  $post Optional post to get the price from.
 * @return string
 */
function get_webcomic_print_price( string $type, $post = null ) : string {
	$comic = get_webcomic( $post );

	if ( ! $comic ) {
		return '0';
	}

	$base   = webcomic( "option.{$comic->post_type}.commerce_prints.{$type}.price" );
	$adjust = (int) get_post_meta( $comic->ID, "webcomic_commerce_prints_adjust_{$type}", true );

	return number_format_i18n( $base * ( $adjust / 100 + 1 ), 2 );
}

/**
 * Get a comic print sold count.
 *
 * @param string $type The print sold count to get.
 * @param mixed  $post Optional post to get the sold count from.
 * @return int
 */
function get_webcomic_print_sold( string $type, $post = null ) : int {
	$comic = get_webcomic( $post );

	if ( ! $comic ) {
		return 0;
	}

	$sold = (int) get_post_meta( $comic->ID, "webcomic_commerce_prints_sold_{$type}", true );

	return $sold;
}

/**
 * Get comic prints data.
 *
 * @param array $args {
 *     Optional arguments.
 *
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
 * @return array
 */
function get_webcomic_prints( array $args = [] ) : array {
	$args += [
		'post' => null,
	];
	$comic = get_webcomic( $args['post'] );

	if ( ! $comic ) {
		return [];
	}

	/**
	 * Alter the get_webcomic_prints() arguments.
	 *
	 * This filter allows hooks to alter the arguments used by
	 * get_webcomic_prints().
	 *
	 * @param array $args The arguments to filter.
	 */
	$args         = apply_filters(
		'get_webcomic_prints_args', $args + [
			'order'         => 'asc',
			'limit'         => 0,
			'orderby'       => 'name',
			'hide_empty'    => true,
			'hide_infinite' => false,
		]
	);
	$prints       = [];
	$comic_prints = get_post_meta( $comic->ID, 'webcomic_commerce_prints' );

	foreach ( get_webcomic_collection_prints( $comic ) as $slug => $print ) {
		if ( ! in_array( $slug, $comic_prints, true ) ) {
			continue;
		}

		$prints[ $slug ]          = $print + [
			'adjust' => (int) get_post_meta( $comic->ID, "webcomic_commerce_prints_adjust_{$slug}", true ),
			'sold'   => (int) get_post_meta( $comic->ID, "webcomic_commerce_prints_sold_{$slug}", true ),
		];
		$prints[ $slug ]['base']  = $prints[ $slug ]['price'];
		$prints[ $slug ]['left']  = $prints[ $slug ]['stock'] - $prints[ $slug ]['sold'];
		$prints[ $slug ]['price'] = round( $prints[ $slug ]['price'] * ( $prints[ $slug ]['adjust'] / 100 + 1 ), 2 );
	}

	/**
	 * Alter the prints.
	 *
	 * This filter allows hooks to alter the list of prints before standard
	 * arguments are applied to the prints array.
	 *
	 * @param array $prints The prints array.
	 * @param array $args Optional arguments.
	 */
	$prints = apply_filters( 'get_webcomic_prints', $prints, $args );

	if ( 'rand' === $args['orderby'] ) {
		shuffle( $prints );
	} elseif ( function_exists( "sort_webcomic_prints_{$args['orderby']}" ) ) {
		usort( $prints, "sort_webcomic_prints_{$args['orderby']}" );
	}

	if ( 'desc' === $args['order'] ) {
		$prints = array_reverse( $prints );
	}

	if ( 0 < $args['limit'] ) {
		$prints = array_slice( $prints, 0, $args['limit'] );
	}

	return $prints;
}

/**
 * Get a list of comic prints.
 *
 * @uses get_webcomic_prints()
 * @param array $args {
 *     Optional arguments.
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
 * }
 * @return string
 */
function get_webcomic_prints_list( array $args = [] ) : string {
	$prints = get_webcomic_prints( $args );

	if ( ! $prints ) {
		return '';
	}

	/**
	 * Alter the get_webcomic_prints_list() arguments.
	 *
	 * This filter allows hooks to alter the arguments used by
	 * get_webcomic_prints_list().
	 *
	 * @param array $args The arguments to filter.
	 * @param array $prints The prints included in the list.
	 */
	$args = apply_filters( 'get_webcomic_prints_list_args', $args, $prints );
	// @codingStandardsIgnoreLine WordPress.Classes.ClassInstantiation.MissingParenthesis - Incorrectly triggered.
	$walker = new $args['walker']();
	$items  = [];

	foreach ( $prints as $print ) {
		$output = '';

		$walker->start_el( $output, (object) $print, 0, $args, $print['slug'] );

		$items[] = $output;
	}

	preg_match( '/^(.*){{(.*)}}(.*)$/s', $args['format'], $match );

	$match += [ '', '', $args['format'], '' ];

	return $match[1] . implode( $match[2], $items ) . $match[3];
}

/**
 * Does the comic have any prints?
 *
 * @param mixed $type Optional print to check for.
 * @param mixed $post Optional post to check for prints.
 * @return bool
 */
function has_webcomic_print( $type = null, $post = null ) : bool {
	$comic = get_webcomic( $post );

	if ( ! $comic ) {
		return false;
	}

	$prints = get_post_meta( $comic->ID, 'webcomic_commerce_prints' );

	if ( ! $prints ) {
		return false;
	} elseif ( ! $type ) {
		return true;
	}

	return (bool) array_intersect( (array) $type, $prints );
}

/*
===== Display Functions ======================================================

The following template tags directly display content returned by some of the
template tags found above.
*/

/**
 * Display a collection cart link.
 *
 * @uses get_webcomic_collection_cart_link()
 * @param string $link Optional link text, like 'before{{text}}after'.
 * @param mixed  $collection Optional collection to display a cart link for.
 * @return void
 */
function webcomic_collection_cart_link( string $link = '', $collection = null ) {
	echo get_webcomic_collection_cart_link( $link, $collection ); // WPCS: xss ok.
}

/**
 * Display the collection currency.
 *
 * @uses get_webcomic_collection_currency()
 * @param mixed $collection Optional collection to display a currency for.
 * @return void
 */
function webcomic_collection_currency( $collection = null ) {
	echo get_webcomic_collection_currency( $collection ); // WPCS: xss ok.
}

/**
 * Display a collection donation amount.
 *
 * @uses get_webcomic_collection_donation()
 * @param mixed $collection Optional collection to display a donation amount
 * for.
 * @return void
 */
function webcomic_collection_donation( $collection = null ) {
	echo get_webcomic_collection_donation( $collection ); // WPCS: xss ok.
}

/**
 * Display a collection donation link.
 *
 * @uses get_webcomic_collection_donation_link()
 * @param string $link Optional link text, like 'before{{text}}after'.
 * @param mixed  $collection Optional collection to display a donation link
 * for.
 * @return void
 */
function webcomic_collection_donation_link( string $link = '', $collection = null ) {
	echo get_webcomic_collection_donation_link( $link, $collection ); // WPCS: xss ok.
}

/**
 * Display a collection print name.
 *
 * @uses get_webcomic_collection_print_name()
 * @param string $type The print name to display.
 * @param mixed  $collection Optional collection to get the print name from.
 * @return void
 */
function webcomic_collection_print_name( string $type, $collection = null ) {
	echo get_webcomic_collection_print_name( $type, $collection ); // WPCS: xss ok.
}

/**
 * Display a collection print price.
 *
 * @uses get_webcomic_collection_print_price()
 * @param string $type The print price to display.
 * @param mixed  $collection Optional collection to get the print price from.
 * @return void
 */
function webcomic_collection_print_price( string $type, $collection = null ) {
	echo get_webcomic_collection_print_price( $type, $collection ); // WPCS: xss ok.
}

/**
 * Display a collection print stock.
 *
 * @uses get_webcomic_collection_print_stock()
 * @param string $type The print stock to display.
 * @param mixed  $collection Optional collection to get the print stock from.
 * @return void
 */
function webcomic_collection_print_stock( string $type, $collection = null ) {
	echo get_webcomic_collection_print_stock( $type, $collection ); // WPCS: xss ok.
}

/**
 * Display a comic print price adjustment.
 *
 * @uses get_webcomic_print_adjust()
 * @param string $type The print price adjustment to display.
 * @param mixed  $post Optional post to get the price adjustment from.
 * @return void
 */
function webcomic_print_adjust( string $type, $post = null ) {
	echo get_webcomic_print_adjust( $type, $post ); // WPCS: xss ok.
}

/**
 * Display a comic print left count.
 *
 * @uses get_webcomic_print_left()
 * @param string $type The print left count.
 * @param mixed  $post Optional post to get the left count from.
 * @return void
 */
function webcomic_print_left( string $type, $post = null ) {
	echo get_webcomic_print_left( $type, $post ); // WPCS: xss ok.
}

/**
 * Display a comic print price.
 *
 * @uses get_webcomic_print_price()
 * @param string $type The print price to display.
 * @param mixed  $post Optional post to get the price from.
 * @return void
 */
function webcomic_print_price( string $type, $post = null ) {
	echo get_webcomic_print_price( $type, $post ); // WPCS: xss ok.
}

/**
 * Display a comic print sold count.
 *
 * @uses get_webcomic_print_sold()
 * @param string $type The print sold count to display.
 * @param mixed  $post Optional post to get the sold count from.
 * @return void
 */
function webcomic_print_sold( string $type, $post = null ) {
	echo get_webcomic_print_sold( $type, $post ); // WPCS: xss ok.
}

/**
 * Display a list of comic prints.
 *
 * @uses get_webcomic_prints_list()
 * @param array $args Optional arguments.
 * @return void
 */
function webcomic_prints_list( array $args = [] ) {
	echo get_webcomic_prints_list( $args ); // WPCS: xss ok.
}

/* ===== Utility Functions ================================================== */

/**
 * Sort comic prints by price adjustment.
 *
 * @param array $print_one The first print to compare.
 * @param array $print_two The second print to compare.
 * @return int
 * @internal For get_webcomic_prints().
 */
function sort_webcomic_prints_adjust( $print_one, $print_two ) : int {
	$count_one = $print_one['adjust'];
	$count_two = $print_two['adjust'];

	if ( $count_one === $count_two ) {
		return 0;
	} elseif ( $count_one < $count_two ) {
		return -1;
	}

	return 1;
}

/**
 * Sort comic prints by base price.
 *
 * @param array $print_one The first print to compare.
 * @param array $print_two The second print to compare.
 * @return int
 * @internal For get_webcomic_prints().
 */
function sort_webcomic_prints_base( $print_one, $print_two ) : int {
	$count_one = $print_one['base'];
	$count_two = $print_two['base'];

	if ( $count_one === $count_two ) {
		return 0;
	} elseif ( $count_one < $count_two ) {
		return -1;
	}

	return 1;
}

/**
 * Sort comic prints by left count.
 *
 * @param array $print_one The first print to compare.
 * @param array $print_two The second print to compare.
 * @return int
 * @internal For get_webcomic_prints().
 */
function sort_webcomic_prints_left( $print_one, $print_two ) : int {
	$count_one = $print_one['left'];
	$count_two = $print_two['left'];

	if ( $count_one === $count_two ) {
		return 0;
	} elseif ( $count_one < $count_two ) {
		return -1;
	}

	return 1;
}

/**
 * Sort comic prints by name.
 *
 * @param array $print_one The first print to compare.
 * @param array $print_two The second print to compare.
 * @return int
 * @internal For get_webcomic_prints().
 */
function sort_webcomic_prints_name( $print_one, $print_two ) : int {
	return strcmp( $print_one['name'], $print_two['name'] );
}

/**
 * Sort comic prints by price.
 *
 * @param array $print_one The first print to compare.
 * @param array $print_two The second print to compare.
 * @return int
 * @internal For get_webcomic_prints().
 */
function sort_webcomic_prints_price( $print_one, $print_two ) : int {
	$count_one = $print_one['price'];
	$count_two = $print_two['price'];

	if ( $count_one === $count_two ) {
		return 0;
	} elseif ( $count_one < $count_two ) {
		return -1;
	}

	return 1;
}

/**
 * Sort comic prints by sold count.
 *
 * @param array $print_one The first print to compare.
 * @param array $print_two The second print to compare.
 * @return int
 * @internal For get_webcomic_prints().
 */
function sort_webcomic_prints_sold( $print_one, $print_two ) : int {
	$count_one = $print_one['sold'];
	$count_two = $print_two['sold'];

	if ( $count_one === $count_two ) {
		return 0;
	} elseif ( $count_one < $count_two ) {
		return -1;
	}

	return 1;
}

/**
 * Sort comic prints by slug.
 *
 * @param array $print_one The first print to compare.
 * @param array $print_two The second print to compare.
 * @return int
 * @internal For get_webcomic_prints().
 */
function sort_webcomic_prints_slug( $print_one, $print_two ) : int {
	return strcmp( $print_one['slug'], $print_two['slug'] );
}

/**
 * Sort comic prints by stock.
 *
 * @param array $print_one The first print to compare.
 * @param array $print_two The second print to compare.
 * @return int
 * @internal For get_webcomic_prints().
 */
function sort_webcomic_prints_stock( $print_one, $print_two ) : int {
	$count_one = $print_one['stock'];
	$count_two = $print_two['stock'];

	if ( $count_one === $count_two ) {
		return 0;
	} elseif ( $count_one < $count_two ) {
		return -1;
	}

	return 1;
}

<?php
/*
Text Domain: webcomic
Plugin Name: Webcomic
Plugin URI: http://webcomic.nu
Description: Comic publishing power for the web.
Version: 4.0.9
Author: Michael Sisk
Author URI: http://mgsisk.com
License: GPL2

Copyright 2008 - 2013 Michael Sisk (contact@mgsisk.com)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License as
published by the Free Software Foundation; either version 2 of
the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA
02110-1301 USA
*/

/** Comic Publishing Power for the Web
 * 
 * @todo core.trac.wordpress.org/ticket/16031
 * 
 * @package Webcomic
 * @copyright 2008 - 2013 Michael Sisk
 * @license //gnu.org/licenses/gpl-2.0.html GPL2
 * @version 4.0.9
 * @link http://webcomic.nu
 */

/** Initialize the plugin.
 * 
 * @package Webcomic
 */
class Webcomic {
	/** Internal version number.
	 * @var string
	 */
	protected static $version = '4.0.9';
	
	/** Absolute path to the Webcomic directory.
	 * @var string
	 */
	protected static $dir = '';
	
	/** URL to the Webcomic directory.
	 * @var string
	 */
	protected static $url = '';
	
	/** Stores error notifications.
	 * @var array
	 */
	protected static $error = array();
	
	/** Stores success notifications.
	 * @var array
	 */
	protected static $notice = array();
	
	/** Stores the configuration.
	 * @var array
	 */
	protected static $config = array();
	
	/** Whether to attempt integration with the active theme.
	 * @var boolean
	 */
	protected static $integrate = false;
	
	/** Stores the collection the current page is related to.
	 * @var string
	 */
	protected static $collection = '';
	
	/** Set class properties and register hooks.
	 * 
	 * @uses Webcomic::$dir
	 * @uses Webcomic::$url
	 * @uses Webcomic::$config
	 * @uses Webcomic::init()
	 * @uses Webcomic::log_ipn()
	 * @uses Webcomic::wp_head()
	 * @uses Webcomic::twitter_oauth()
	 * @uses Webcomic::save_transcript()
	 * @uses Webcomic::webcomic_redirect()
	 * @uses Webcomic::setup_theme()
	 * @uses Webcomic::the_post()
	 * @uses Webcomic::pre_get_posts()
	 * @uses Webcomic::buffer_alert()
	 * @uses Webcomic::wp_enqueue_scripts()
	 * @uses Webcomic::tweet_webcomic()
	 * @uses Webcomic::request()
	 * @uses Webcomic::get_term()
	 * @uses Webcomic::template()
	 * @uses Webcomic::the_posts()
	 * @uses Webcomic::get_terms()
	 * @uses Webcomic::stylesheet()
	 * @uses Webcomic::body_class()
	 * @uses Webcomic::post_class()
	 * @uses Webcomic::get_the_terms()
	 * @uses Webcomic::post_type_link()
	 * @uses Webcomic::template_include()
	 * @uses Webcomic::the_content_feed()
	 * @uses Webcomic::wp_get_object_terms()
	 * @uses Webcomic::extra_theme_headers()
	 * @uses Webcomic::wp_get_attachment_image_attributes()
	 * @uses Webcomic::loop_end()
	 * @uses Webcomic::loop_start()
	 * @uses Webcomic::the_excerpt()
	 * @uses Webcomic::the_content()
	 * @uses Webcomic::integrate_sort_asc()
	 * @uses WebcomicWidgets
	 * @uses WebcomicShortcodes
	 */
	public function __construct() {
		self::$dir    = plugin_dir_path( __FILE__ );
		self::$url    = plugin_dir_url( __FILE__ );
		self::$config = get_option( 'webcomic_options' );
		
		if ( self::$config and version_compare( self::$config[ 'version' ], '4x', '>=' ) ) {
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'init', array( $this, 'log_ipn' ) );
			add_action( 'wp_head', array( $this, 'wp_head' ), 1 );
			add_action( 'init', array( $this, 'twitter_oauth' ) );
			add_action( 'init', array( $this, 'save_transcript' ) );
			add_action( 'init', array( $this, 'dynamic_defaults' ) );
			add_action( 'init', array( $this, 'webcomic_redirect' ) );
			add_action( 'setup_theme', array( $this, 'setup_theme' ) );
			add_action( 'the_post', array( $this, 'the_post' ), 10, 1 );
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), 10, 1 );
			add_action( 'webcomic_buffer_alert', array( $this, 'buffer_alert' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
			add_action( 'transition_post_status', array( $this, 'tweet_webcomic' ), 10, 3 );
			
			add_filter( 'request', array( $this, 'request' ), 10, 1 );
			add_filter( 'get_term', array( $this, 'get_term' ), 10, 2 );
			add_filter( 'template', array( $this, 'template' ), 10, 1 );
			add_filter( 'the_posts', array( $this, 'the_posts' ), 10, 2 );
			add_filter( 'get_terms', array( $this, 'get_terms' ), 10, 3 );
			add_filter( 'stylesheet', array( $this, 'stylesheet' ), 10, 1 );
			add_filter( 'body_class', array( $this, 'body_class' ), 10, 2 );
			add_filter( 'post_class', array( $this, 'post_class' ), 10, 3 );
			add_filter( 'get_the_terms', array( $this, 'get_the_terms' ), 10, 3 );
			add_action( 'post_type_link', array( $this, 'post_type_link' ), 10, 4 );
			add_filter( 'template_include', array( $this, 'template_include' ), 10, 1 );
			add_filter( 'the_content_feed', array( $this, 'the_content_feed' ), 10, 1 );
			add_filter( 'wp_get_object_terms', array( $this, 'wp_get_object_terms' ), 10, 4 );
			add_filter( 'extra_theme_headers', array( $this, 'extra_theme_headers' ), 10, 1 );
			add_filter( 'wp_get_attachment_image_attributes', array( $this, 'wp_get_attachment_image_attributes' ), 10, 2 );
			
			if ( self::$config[ 'integrate' ] ) {
				add_action( 'loop_end', array( $this, 'loop_end' ), 10, 1 );
				add_action( 'loop_start', array( $this, 'loop_start' ), 10, 1 );
				add_action( 'the_excerpt', array( $this, 'the_excerpt' ), 10, 1 );
				add_action( 'the_content', array( $this, 'the_content' ), 10, 1 );
				add_action( 'pre_get_posts', array( $this, 'integrate_sort_asc' ), 10, 1 );
			}
			
			require_once self::$dir . '-/php/tags.php';
			require_once self::$dir . '-/php/widgets.php';    new WebcomicWidgets;
			require_once self::$dir . '-/php/shortcodes.php'; new WebcomicShortcode;
		}
	}
	
	/** Initialize major plugin features.
	 * 
	 * This hook performs a number of important functions, including
	 * loading the plugin text domain, defining the 'prints',
	 * 'transcripts', and 'crossover' rewrite endpoints, and
	 * registering custom image sizes, post types, and taxonomies.
	 * 
	 * @uses Webcomic::$dir
	 * @uses Webcomic::$url
	 * @uses Webcomic::$config
	 * @hook init
	 */
	public function init() {
		load_plugin_textdomain( 'webcomic', '', self::$dir . '-/i18n/' );
		
		define( 'EP_WEBCOMIC', 1099511627776 ); // 2^40
		define( 'EP_WEBCOMIC_TERM', 2199023255552 ); // 2^41
		
		add_rewrite_endpoint( 'prints', EP_WEBCOMIC );
		add_rewrite_endpoint( 'transcripts', EP_WEBCOMIC );
		add_rewrite_endpoint( 'crossover', EP_WEBCOMIC_TERM );
		
		foreach ( self::$config[ 'sizes' ] as $k => $v ) {
			add_image_size( $k, $v[ 'width' ], $v[ 'height' ], $v[ 'crop' ] );
		}
		
		foreach ( self::$config[ 'collections' ] as $k => $v ) {
			register_post_type( $k, array(
				'labels' => array(
					'name'               => $v[ 'name' ],
					'singular_name'      => sprintf( __( '%s Webcomic', 'webcomic' ), esc_html( $v[ 'name' ] ) ),
					'add_new'            => __( 'Add New', 'webcomic' ),
					'add_new_item'       => __( 'Add New Webcomic', 'webcomic' ),
					'edit_item'          => __( 'Edit Webcomic', 'webcomic' ),
					'new_item'           => __( 'New Webcomic', 'webcomic' ),
					'all_items'          => __( 'All Webcomics', 'webcomic' ),
					'view_item'          => __( 'View Webcomic', 'webcomic' ),
					'search_items'       => __( 'Search Webcomics', 'webcomic' ),
					'not_found'          => __( 'No webcomics found.', 'webcomic' ),
					'not_found_in_trash' => __( 'No webcomics found in Trash.', 'webcomic' )
				),
				'description' => esc_html( $v[ 'description' ] ),
				'public'      => true,
				'menu_icon'   => self::$url . '-/img/webcomic-small.png',
				'supports'    => $v[ 'supports' ],
				'taxonomies'  => $v[ 'taxonomies' ],
				'has_archive' => $v[ 'slugs' ][ 'archive' ],
				'rewrite' => array(
					'slug'       => $v[ 'slugs' ][ 'webcomic' ],
					'ep_mask'    => EP_WEBCOMIC | EP_PERMALINK,
					'with_front' => false
				)
			) );
			
			register_taxonomy( "{$k}_storyline", $k, array(
				'labels' => array(
					'name'              => sprintf( __( '%s Storylines', 'webcomic' ), esc_html( $v[ 'name' ] ) ),
					'singular_name'     => __( 'Storyline', 'webcomic' ),
					'search_items'      => __( 'Search Storylines', 'webcomic' ),
					'popular_items'     => __( 'Popular Storylines', 'webcomic' ),
					'all_items'         => __( 'All Storylines', 'webcomic' ),
					'parent_item'       => __( 'Parent Storyline', 'webcomic' ),
					'parent_item_colon' => __( 'Parent Storyline:', 'webcomic' ),
					'edit_item'         => __( 'Edit Storyline', 'webcomic' ), 
					'update_item'       => __( 'Update Storyline', 'webcomic' ),
					'add_new_item'      => __( 'Add New Storyline', 'webcomic' ),
					'new_item_name'     => __( 'New Storyline Name', 'webcomic' ),
					'menu_name'         => __( 'Storylines', 'webcomic' )
				),
				'hierarchical' => true,
				'show_admin_column' => true,
				'rewrite' => array(
					'slug'         => $v[ 'slugs' ][ 'storyline' ],
					'with_front'   => false,
					'hierarchical' => true,
					'ep_mask'      => EP_WEBCOMIC_TERM
				)
			) );
			
			register_taxonomy( "{$k}_character", $k, array(
				'labels' => array(
					'name'                       => sprintf( __( '%s Characters', 'webcomic' ), esc_html( $v[ 'name' ] ) ),
					'singular_name'              => __( 'Character', 'webcomic' ),
					'search_items'               => __( 'Search Characters', 'webcomic' ),
					'popular_items'              => __( 'Popular Characters', 'webcomic' ),
					'all_items'                  => __( 'All Characters', 'webcomic' ),
					'edit_item'                  => __( 'Edit Character', 'webcomic' ), 
					'update_item'                => __( 'Update Character', 'webcomic' ),
					'add_new_item'               => __( 'Add New Character', 'webcomic' ),
					'new_item_name'              => __( 'New Character Name', 'webcomic' ),
					'separate_items_with_commas' => __( 'Separate characters with commas', 'webcomic' ),
					'add_or_remove_items'        => __( 'Add or remove characters', 'webcomic' ),
					'choose_from_most_used'      => __( 'Choose from the most used characters', 'webcomic' ),
					'menu_name'                  => __( 'Characters', 'webcomic' )
				),
				'show_admin_column' => true,
				'rewrite' => array(
					'slug'       => $v[ 'slugs' ][ 'character' ],
					'with_front' => false,
					'ep_mask'      => EP_WEBCOMIC_TERM
				)
			) );
		}
		
		foreach ( self::$config[ 'collections' ] as $k => $v ) {
			foreach ( $v[ 'taxonomies' ] as $taxonomy ) {
				register_taxonomy_for_object_type( $taxonomy, $k );
			}
		}
		
		register_post_type( 'webcomic_transcript', array(
			'labels' => array(
				'name'               => __( 'Webcomic Transcripts', 'webcomic' ),
				'singular_name'      => __( 'Webcomic Transcript', 'webcomic' ),
				'add_new'            => __( 'Add New', 'webcomic' ),
				'add_new_item'       => __( 'Add New Transcript', 'webcomic' ),
				'edit_item'          => __( 'Edit Transcript', 'webcomic' ),
				'new_item'           => __( 'New Transcript', 'webcomic' ),
				'all_items'          => __( 'All Transcripts', 'webcomic' ),
				'view_item'          => __( 'View Transcript', 'webcomic' ),
				'search_items'       => __( 'Search Transcripts', 'webcomic' ),
				'not_found'          => __( 'No transcripts found.', 'webcomic' ),
				'not_found_in_trash' => __( 'No transcripts found in Trash.', 'webcomic' )
			),
			'public'    => false,
			'show_ui'   => true,
			'exclude_from_search' => false,
			'supports'  => array( 'editor', 'author', 'revisions' ),
			'menu_icon' => self::$url . '-/img/transcript-small.png'
		) );
		
		register_taxonomy( 'webcomic_language', 'webcomic_transcript', array(
			'labels' => array(
				'name'                       => __( 'Webcomic Transcript Languages', 'webcomic' ),
				'singular_name'              => __( 'Language', 'webcomic' ),
				'search_items'               => __( 'Search Languages', 'webcomic' ),
				'popular_items'              => __( 'Popular Lanuages', 'webcomic' ),
				'all_items'                  => __( 'All Languages', 'webcomic' ),
				'edit_item'                  => __( 'Edit Language', 'webcomic' ), 
				'update_item'                => __( 'Update Language', 'webcomic' ),
				'add_new_item'               => __( 'Add New Language', 'webcomic' ),
				'new_item_name'              => __( 'New Language Name', 'webcomic' ),
				'separate_items_with_commas' => __( 'Separate languages with commas', 'webcomic' ),
				'add_or_remove_items'        => __( 'Add or remove languages', 'webcomic' ),
				'choose_from_most_used'      => __( 'Choose from the most used languages', 'webcomic' ),
				'menu_name'                  => __( 'Languages', 'webcomic' )
			),
			'rewrite'           => false,
			'show_in_nav_menus' => false,
			'show_admin_column' => true
		) );
	}
	
	/** Handle PayPal IPN's.
	 * 
	 * Logs instant payment notifications and updates the original print
	 * availability of webcomics as necessary. Logs are saved to
	 * /webcomic/-/logs/ipn-{$blog_id}.php
	 * 
	 * https://www.paypal.com/cgi-bin/webscr | https://www.sandbox.paypal.com/cgi-bin/webscr
	 * 
	 * @uses Webcomic::$config
	 * @action webcomic_ipn Triggered prior to processing a Paypal IPN request.
	 * @hook init
	 */
	public function log_ipn() {
		global $blog_id;
		
		if ( isset( $_GET[ 'webcomic_commerce_ipn' ] ) and !empty( $_POST ) ) {
			do_action( 'webcomic_ipn' );
			
			$output  = array();
			$header  = $message = $error = '';
			$logfile = self::$dir . sprintf( '-/log/ipn-%s.php', $blog_id ? $blog_id : 1 );
			$request = 'cmd=' . urlencode( '_notify-validate' );
			
			foreach ( $_POST as $k => $v ) {
				$value    = urlencode( stripslashes( $v ) );
				$request .= "&{$k}={$value}";
			}
			
			if ( $curl = curl_init() ) {
				curl_setopt( $curl, CURLOPT_URL, 'https://www.paypal.com/cgi-bin/webscr' );
				curl_setopt( $curl, CURLOPT_HEADER, 0 );
				curl_setopt( $curl, CURLOPT_POST, 1 );
				curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $curl, CURLOPT_POSTFIELDS, $request );
				curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 1 );
				curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 2 );
				curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Host: www.paypal.com' ) );
				
				$log      = is_readable( $logfile ) ? file_get_contents( $logfile ) : '';
				$response = curl_exec( $curl );
				
				if ( 0 === strcmp( $response, 'VERIFIED' ) ) {
					if ( 'Completed' !== $_POST[ 'payment_status' ] ) {
						$error   = true;
						$message = __( 'Incomplete transaction', 'webcomic' );
					} elseif ( preg_match( sprintf( '/^%s/', $_POST[ 'txn_id' ] ), $log ) ) {
						$error   = true;
						$message = __( 'Transaction already completed', 'webcomic' );
					} elseif ( 'cart' === $_POST[ 'txn_type' ] ) {
						$i = 1;
						$e = 0;
						
						while ( isset( $_POST[ "item_number{$i}" ] ) ) {
							$item     = explode( '-', $_POST[ "item_number{$i}" ] );
							$commerce = get_post_meta( $item[ 0 ], 'webcomic_commerce', true );
							
							if ( empty( self::$config[ 'collections' ][ $item[ 1 ] ] ) ) {
								$e++;
								$error   = true;
								$message = sprintf( __( 'Invalid collection %s', 'webcomic' ), $item[ 1 ] );
							} elseif ( self::$config[ 'collections' ][ $item[ 1 ] ][ 'commerce' ][ 'business' ] !== $_POST[ 'receiver_email' ] ) {
								$e++;
								$error   = true;
								$message = sprintf( __( 'Incorrect business email %s', 'webcomic' ), $_POST[ 'receiver_email' ] );
							} elseif ( $_POST[ "quantity{$i}" ] and number_format( $commerce[ 'total' ][ $item[ 2 ] ], 2 ) !== number_format( $_POST[ "mc_gross_{$i}" ] / $_POST[ "quantity{$i}" ], 2 ) ) {
								$e++;
								$error   = true;
								$message = sprintf( __( 'Incorrect price %s', 'webcomic' ), number_format( $_POST[ "mc_gross_{$i}" ] / $_POST[ "quantity{$i}" ], 2 ) );
							} elseif ( self::$config[ 'collections' ][ $item[ 1 ] ][ 'commerce' ][ 'currency' ] !== $_POST[ 'mc_currency' ] ) {
								$e++;
								$error   = true;
								$message = sprintf( __( 'Incorrect currency %s', 'webcomic' ), $_POST[ 'mc_currency' ] );
							} else {
								if ( 'original' === $item[ 2 ] ) {
									update_post_meta( $item[ 0 ], 'webcomic_original', false );
								}
								
								$message = __( 'Good', 'webcomic' );
							}
							
							$output[] = sprintf( "\t\t%s\t%s\t%s", $_POST[ "item_number{$i}" ], $message, $error ? 'x' : '' );
							$error = false;
							
							$i++;
						}
						
						array_unshift( $output, sprintf( "%s\t%s\t%s\t%s\t%s", $_POST[ 'txn_id' ], $_POST[ 'payment_date' ], '', $e ? sprintf( _n( '%s Error', '%s Errors', $e, 'webcomic' ), $e ) : __( 'Sale Get!', 'webcomic' ) ), $e ? 'x' : '' );
					} elseif ( 'donation' === $_GET[ 'webcomic_commerce_ipn' ] ) {
						if ( empty( self::$config[ 'collections' ][ $_POST[ 'item_number' ] ] ) ) {
							$error   = true;
							$message = sprintf( __( 'Invalid collection %s', 'webcomic' ), $_POST[ 'item_number' ] );
						} elseif ( self::$config[ 'collections' ][ $_POST[ 'item_number' ] ][ 'commerce' ][ 'business' ] !== $_POST[ 'receiver_email' ] ) {
							$error   = true;
							$message = sprintf( __( 'Incorrect business email %s', 'webcomic' ), $_POST[ 'receiver_email' ] );
						} elseif ( self::$config[ 'collections' ][ $_POST[ 'item_number' ] ][ 'commerce' ][ 'donation' ] and ( number_format( self::$config[ 'collections' ][ $_POST[ 'item_number' ] ][ 'commerce' ][ 'donation' ], 2 ) !== number_format( $_POST[ 'mc_gross' ], 2 ) ) ) {
							$error   = true;
							$message = sprintf( __( 'Incorrect price %s', 'webcomic' ), number_format( $_POST[ 'mc_gross' ], 2 ) );
						} elseif ( self::$config[ 'collections' ][ $_POST[ 'item_number' ] ][ 'commerce' ][ 'currency' ] !== $_POST[ 'mc_currency' ] ) {
							$error   = true;
							$message = sprintf( __( 'Incorrect currency %s', 'webcomic' ), $_POST[ 'mc_currency' ] );
						} else {
							$message = __( 'Donation Get!', 'webcomic' );
						}
					} else {
						$item     = explode( '-', $_POST[ 'item_number' ] );
						$commerce = get_post_meta( $item[ 0 ], 'webcomic_commerce', true );
						
						if ( empty( self::$config[ 'collections' ][ $item[ 1 ] ] ) ) {
							$error   = true;
							$message = sprintf( __( 'Invalid collection %s', 'webcomic' ), $item[ 1 ] );
						} elseif ( self::$config[ 'collections' ][ $item[ 1 ] ][ 'commerce' ][ 'business' ] !== $_POST[ 'receiver_email' ] ) {
							$error   = true;
							$message = sprintf( __( 'Incorrect business email %s', 'webcomic' ), $_POST[ 'receiver_email' ] );
						} elseif ( $_POST[ 'quantity' ] and number_format( $commerce[ 'total' ][ $item[ 2 ] ], 2 ) !== number_format( $_POST[ 'mc_gross' ] / $_POST[ 'quantity' ], 2 ) ) {
							$error   = true;
							$message = sprintf( __( 'Incorrect price %s', 'webcomic' ), number_format( $_POST[ 'mc_gross' ] / $_POST[ 'quantity' ], 2 ) );
						} elseif ( self::$config[ 'collections' ][ $item[ 1 ] ][ 'commerce' ][ 'currency' ] !== $_POST[ 'mc_currency' ] ) {
							$error   = true;
							$message = sprintf( __( 'Incorrect currency %s', 'webcomic' ), $_POST[ 'mc_currency' ] );
						} else {
							if ( 'original' === $item[ 2 ] ) {
								update_post_meta( $item[ 0 ], 'webcomic_original', false );
							}
							
							$message = __( 'Sale Get!', 'webcomic' );
						}
					}
				} elseif ( 0 === strcmp( $response, 'INVALID' ) ) {
					$error   = true;
					$message = __( 'Invalid response', 'webcomic' );
				}
				
				curl_close( $curl );
			} else {
				$message = __( 'HTTP Error', 'webcomic' );
			}
			
			if ( !$output ) {
				$output[] = sprintf( "%s\t%s\t%s\t%s\t%s", $_POST[ 'txn_id' ], $_POST[ 'payment_date' ], $_POST[ 'item_number' ], $message, $error ? 'x' : '' );
			}
			
			if ( file_exists( $logfile ) and is_writable( $logfile ) ) {
				file_put_contents( $logfile, join( "\n", $output ) . "\n", FILE_APPEND );
			} elseif ( is_writable( dirname( $logfile ) ) ) {
				file_put_contents( $logfile, "<?php die; ?>\n" . join( "\n", $output ) . "\n" );
			}
		}
	}
	
	/** Add Open Graph metadata for Webcomic-related pages.
	 * 
	 * Use of the 'property' attribute is obnoxious but intentional; see
	 * [ogp.me](http://ogp.me/) for details on the Open Graph protocol.
	 * 
	 * @uses Webcomic::$config
	 * @uses Webcomic::$collection
	 * @uses Webcomic::get_attachments()
	 * @hook wp_head
	 * @filter array webcomic_opengraph Filters the Open Graph data Webcomic adds to all Webcomic-related pages.
	 */
	public function wp_head() {
		global $wp_query, $post;
		
		$object = $wp_query->get_queried_object();
		$output = array(
			'generator' => sprintf( 'Webcomic %s', self::$version )
		);
		
		if ( self::$collection and !is_404() ) {
			$output[ 'og:type' ]        = empty( $object->post_type ) ? 'website' : 'article';
			$output[ 'twitter:card' ]   = is_singular() ? 'photo' : 'summary';
			$output[ 'og:site_name' ]   = get_bloginfo( 'name' );
			$output[ 'og:description' ] = get_bloginfo( 'description' );
			
			if ( is_singular() ) {
				setup_postdata( $post );
				
				$output[ 'og:url' ]                 = get_permalink();
				$output[ 'og:title' ]               = esc_attr( get_the_title() );
				$output[ 'article:author' ]         = esc_url( get_author_posts_url( $object->post_author ) );
				$output[ 'article:published_time' ] = get_the_time( 'c' );
				$output[ 'article:modified_time' ]  = get_the_modified_time( 'c' );
				$output[ 'article:section' ]        = esc_attr( self::$config[ 'collections' ][ self::$collection ][ 'name' ] );
				$output[ 'article:tag' ]            = array();
				
				if ( $description = esc_attr( strip_tags( get_the_excerpt() ) ) ) {
					$output[ 'og:description' ] = $description;
				}
				
				if ( isset( self::$config[ 'collections' ][ $object->post_type ] ) ) {
					foreach ( array_merge( ( array ) get_the_terms( $object->ID, "{$object->post_type}_storyline" ), ( array ) get_the_terms( $object->ID, "{$object->post_type}_character" ) ) as $term ) {
						if ( isset( $term->name ) ) {
							$output[ 'article:tag' ][] = esc_attr( $term->name );
						}
					}
				}
			} elseif ( is_tax() ) {
				$output[ 'og:url' ]         = get_term_link( ( integer ) $object->term_id, $object->taxonomy );
				$output[ 'og:title' ]       = esc_attr( single_term_title( '', false ) );
				
				if ( $description = esc_attr( strip_tags( term_description( $object->term_id, $object->taxonomy ) ) ) ) {
					$output[ 'og:description' ] = $description;
				}
			} else {
				$output[ 'og:url' ]         = get_post_type_archive_link( $object->name );
				$output[ 'og:title' ]       = esc_attr( self::$config[ 'collections' ][ $object->name ][ 'name' ] );
				
				if ( $description = esc_attr( strip_tags( self::$config[ 'collections' ][ $object->name ][ 'description' ] ) ) ) {
					$output[ 'og:description' ] = $description;
				}
			}
				
			if ( is_singular() and isset( self::$config[ 'collections' ][ $object->post_type ] ) and $attachments = self::get_attachments( $object->ID ) ) {
				$output[ 'og:image' ] = array();
				
				foreach ( $attachments as $attachment ) {
					$attributes = wp_get_attachment_image_src( $attachment->ID, 'full' );
					$output[ 'og:image' ][] = array(
						$attributes[ 0 ],
						':width'  => $attributes[ 1 ],
						':height' => $attributes[ 2 ]
					);
				}
			} elseif ( is_tax() and isset( self::$config[ 'terms' ][ $object->term_id ][ 'image' ] ) ) {
				$attributes = wp_get_attachment_image_src( self::$config[ 'terms' ][ $object->term_id ][ 'image' ], 'full' );
				$output[ 'og:image' ]        = $attributes[ 0 ];
				$output[ 'og:image:width' ]  = $attributes[ 1 ];
				$output[ 'og:image:height' ] = $attributes[ 2 ];
			} elseif ( self::$config[ 'collections' ][ self::$collection ][ 'image' ] ) {
				$attributes = wp_get_attachment_image_src( self::$config[ 'collections' ][ self::$collection ][ 'image' ], 'full' );
				$output[ 'og:image' ]        = $attributes[ 0 ];
				$output[ 'og:image:width' ]  = $attributes[ 1 ];
				$output[ 'og:image:height' ] = $attributes[ 2 ];
			}
		}
		
		$output = apply_filters( 'webcomic_opengraph', $output, $object, self::$collection );
		
		foreach ( ( array ) $output as $k => $v ) {
			if ( 'generator' === $k ) {
				echo sprintf( '<meta name="%s" content="%s">', $k, $v ), "\n";
			} elseif ( is_array( $v ) ) {
				foreach( $v as $x ) {
					if ( is_array( $x ) ) {
						foreach ( $x as $a => $b ) {
							echo sprintf( '<meta %s="%s%s" content="%s">', 0 === strpos( $k, 'twitter' ) ? 'name' : 'property', $k, is_string( $a ) ? $a : '', $b ), "\n";
						}
					} else {
						echo sprintf( '<meta %s="%s" content="%s">', 0 === strpos( $k, 'twitter' ) ? 'name' : 'property', $k, $x ), "\n";
					}
				}
			} else {
				echo sprintf( '<meta %s="%s" content="%s">', ( 0 === strpos( $k, 'twitter' ) or 'generator' === $k ) ? 'name' : 'property', $k, $v ), "\n";
			}
		}
	}
	
	/** Handle Twitter OAuth authentication.
	 * 
	 * @uses Webcomic::$config
	 * @hook init
	 */
	public function twitter_oauth() {
		if ( isset( $_GET[ 'webcomic_twitter_oauth' ] ) ) {
			if ( !class_exists( 'tmhOAuth' ) ) {
				require_once self::$dir . '-/lib/twitter.php';
			}
			
			$admin_url = add_query_arg( array( 'post_type' => $_GET[ 'webcomic_collection' ], 'page' => "{$_GET[ 'webcomic_collection' ]}-options" ), admin_url( 'edit.php' ) );
			
			if ( isset( $_GET[ 'denied' ] ) ) {
				wp_die( sprintf( __( 'Authorization was denied. <a href="%1$s">Return to %2$s Settings</a>', 'webcomic' ), $admin_url, self::$config[ 'collections' ][ $_GET[ 'webcomic_collection' ] ][ 'name' ] ), __( 'Twitter Authorization Denied | Webcomic', 'webcomic' ), array( 'response' => 200 ) );
			} else {
				$oauth = new tmhOAuth( array(
					'consumer_key'    => self::$config[ 'collections' ][ $_GET[ 'webcomic_collection' ] ][ 'twitter' ][ 'consumer_key' ],
					'consumer_secret' => self::$config[ 'collections' ][ $_GET[ 'webcomic_collection' ] ][ 'twitter' ][ 'consumer_secret' ],
					'user_token'      => self::$config[ 'collections' ][ $_GET[ 'webcomic_collection' ] ][ 'twitter' ][ 'request_token' ],
					'user_secret'     => self::$config[ 'collections' ][ $_GET[ 'webcomic_collection' ] ][ 'twitter' ][ 'request_secret' ]
				) );
				
				$code = $oauth->request( 'POST', str_replace( '.json', '', $oauth->url( 'oauth/access_token', '' ) ), array(
					'oauth_verifier' => $_GET[ 'oauth_verifier' ]
				) );
				
				if ( 200 === intval( $code ) ) {
					$response = $oauth->extract_params( $oauth->response[ 'response' ] );
					
					self::$config[ 'collections' ][ $_GET[ 'webcomic_collection' ] ][ 'twitter' ][ 'oauth_token' ]   = $response[ 'oauth_token' ];
					self::$config[ 'collections' ][ $_GET[ 'webcomic_collection' ] ][ 'twitter' ][ 'oauth_secret' ]  = $response[ 'oauth_token_secret' ];
					self::$config[ 'collections' ][ $_GET[ 'webcomic_collection' ] ][ 'twitter' ][ 'request_token' ] = self::$config[ 'collections' ][ $_GET[ 'webcomic_collection' ] ][ 'twitter' ][ 'request_secret' ] = '';
					
					update_option( 'webcomic_options', self::$config );
					
					$oauth->config[ 'user_token' ]  = $response[ 'oauth_token' ];
					$oauth->config[ 'user_secret' ] = $response[ 'oauth_token_secret' ];
					
					$code = $oauth->request( 'GET', $oauth->url( '1/account/verify_credentials' ) );
					
					if ( 200 === intval( $code ) ) {
						$response = json_decode( $oauth->response[ 'response' ] );
						
						wp_die( sprintf( __( 'Newly published %1$s webcomics will be tweeted to <a href="%2$s">%3$s</a>. <a href="%4$s">Return to %1s Settings</a>', 'webcomic' ),
							self::$config[ 'collections' ][ $_GET[ 'webcomic_collection' ] ][ 'name' ],
							"http://twitter.com/{$response->screen_name}",
							$response->screen_name,
							$admin_url
						), __( 'Twitter Authorization Complete | Webcomic', 'webcomic' ), array( 'response' => 200 ) );
					} else {
						wp_die( sprintf( __( 'Your credentials could not be verified. Please ensure that your <b>consumer key</b> and <b>consumer secret</b> were entered correctly and <a href="%s">try again.</a>', 'webcomic' ), $admin_url ), __( 'Twitter Authorization Failed | Webcomic', 'webcomic' ), array( 'response' => 200 ) );
					}
				} else {
					wp_die( sprintf( __( 'Your credentials could not be verified. Please ensure that your <b>consumer key</b> and <b>consumer secret</b> were entered correctly and <a href="%s">try again.</a>', 'webcomic' ), $admin_url ), __( 'Twitter Authorization Failed | Webcomic', 'webcomic' ), array( 'response' => 200 ) );
				}
			}
		}
	}
	
	/** Handle transcript submissions.
	 * 
	 * @uses Webcomic::$config
	 * @action webcomic_transcript_submit Triggered prior to processing a user-submitted transcript.
	 * @action webcomic_transcript_submitted Triggered after processing a user-submitted transcript.
	 * @hook init
	 */
	public function save_transcript() {
		if ( isset( $_POST[ 'webcomic_user_transcript' ] ) and wp_verify_nonce( $_POST[ 'webcomic_user_transcript' ], 'webcomic_user_transcript' ) ) {
			do_action( 'webcomic_transcript_submit' );
			
			$anonymous = false;
			
			if ( !$the_post = get_post( $_POST[ 'webcomic_transcript_post' ] ) or empty( self::$config[ 'collections' ][ $the_post->post_type ] ) ) {
				wp_die( __( 'Transcripts can only be submitted for webcomics.', 'webcomic' ), __( 'Error | Webcomic', 'webcomic' ) );
			} elseif ( !get_post_meta( $_POST[ 'webcomic_transcript_post' ], 'webcomic_transcripts', true ) ) {
				wp_die( __( 'This webcomic cannot be transcribed.', 'webcomic' ), __( 'Transcribing Not Allowed | Webcomic', 'webcomic' ) );
			} else {
				$user = wp_get_current_user();
				
				if ( $user->ID ) {
					$_POST[ 'webcomic_transcript_url' ]    = $user->user_url;
					$_POST[ 'webcomic_transcript_email' ]  = $user->user_email;
					$_POST[ 'webcomic_transcript_author' ] = $user->display_name;
				} elseif ( empty( $_POST[ 'webcomic_transcript_author' ] ) ) {
					$_POST[ 'webcomic_transcript_author' ] = $anonymous = __( 'an anonymous user', 'webcomic' );
				}
				
				if ( 'register' === self::$config[ 'collections' ][ $the_post->post_type ][ 'transcripts' ][ 'permission' ] and empty( $user->ID ) ) {
					wp_die( sprintf( __( 'You must be <a href="%s">logged in</a> to transcribe this webcomic.', 'webcomic' ), wp_login_url( get_permalink( $the_post->ID ) ) ), __( 'Unauthorized Transcriber | Webcomic', 'webcomic' ), 401 );
				} elseif ( 'identify' === self::$config[ 'collections' ][ $the_post->post_type ][ 'transcripts' ][ 'permission' ] and ( empty( $_POST[ 'webcomic_transcript_author' ] ) or empty( $_POST[ 'webcomic_transcript_email' ] ) or !filter_var( $_POST[ 'webcomic_transcript_email' ], FILTER_VALIDATE_EMAIL ) ) ) {
					wp_die( __( 'You must provide a name and valid email address to transcribe this webcomic.', 'webcomic' ), __( 'Unauthorized Transcriber | Webcomic', 'webcomic' ), 401 );
				}  elseif ( empty( $_POST[ 'webcomic_transcript_content' ] ) ) {
					wp_die( __( 'Please write a transcript.', 'webcomic' ), __( 'Error | Webcomic', 'webcomic' ) );
				}
				
				if ( empty( $_POST[ 'webcomic_transcript_update' ] ) ) {
					$author = empty( $user->ID ) ? 1 : $user->ID;
				} elseif ( $update_post = get_post( $_POST[ 'webcomic_transcript_update' ] ) ) {
					$author = ( int ) $update_post->post_author;
				} else {
					$author = 0;
				}
				
				$date  = current_time( 'mysql' );
				$title = sprintf( __( '%s Transcript', 'webcomic' ), get_the_title( $_POST[ 'webcomic_transcript_post' ] ) );
				
				if ( $new_post = wp_insert_post( array(
					'ID'            => empty( $_POST[ 'webcomic_transcript_update' ] ) ? 0 : $_POST[ 'webcomic_transcript_update' ],
					'post_name'     => sanitize_title( $title ),
					'post_type'     => 'webcomic_transcript',
					'post_date'     => $date,
					'post_title'    => $title,
					'post_author'   => $author,
					'post_parent'   => $_POST[ 'webcomic_transcript_post' ],
					'post_status'   => 'draft',
					'post_content'  => $_POST[ 'webcomic_transcript_content' ],
					'post_date_gmt' => get_gmt_from_date( $date )
				) ) and !is_wp_error( $new_post ) ) {
					if ( !$anonymous and ( int ) $user->ID !== $author ) {
						add_post_meta( $new_post, 'webcomic_author', array(
							'name'  => $_POST[ 'webcomic_transcript_author' ],
							'email' => $_POST[ 'webcomic_transcript_email' ],
							'url'   => $_POST[ 'webcomic_transcript_url' ],
							'ip'    => preg_replace( '/[^0-9a-fA-F:., ]/', '',$_SERVER[ 'REMOTE_ADDR' ] ),
							'time'  => ( integer ) current_time( 'timestamp' )
						) );
					}
					
					if ( $_POST[ 'webcomic_transcript_language' ] ) {
						wp_set_post_terms( $new_post, $_POST[ 'webcomic_transcript_language' ], 'webcomic_language' );
					}
					
					if ( self::$config[ 'collections' ][ $the_post->post_type ][ 'transcripts' ][ 'notify' ][ 'hook' ] ) {
						wp_mail(
							self::$config[ 'collections' ][ $the_post->post_type ][ 'transcripts' ][ 'notify' ][ 'email' ],
							sprintf( __( '[%1$s] %2$s Transcript Submitted', 'webcomic' ), get_bloginfo( 'name' ), $the_post->post_title ),
							sprintf( __( 'This is an automated notification that %1$s%2$s has <a href="%3$s">%4$s</a> for %5$s.', 'webcomic' ),
								$_POST[ 'webcomic_transcript_author' ],
								$_POST[ 'webcomic_transcript_email' ] ? " &lt;{$_POST[ 'webcomic_transcript_email' ]}&gt;" : '',
								esc_url( admin_url( "post.php?post={$new_post}&action=edit" ) ),
								$_POST[ 'webcomic_transcript_update' ] ? __( 'improved a transcript', 'webcomic' ) : __( 'submitted a transcript', 'webcomic' ),
								sprintf( '<a href="%s">%s</a> - <a href="%s">%s</a>', esc_url( admin_url( "edit.php?post_type={$the_post->post_type}" ) ), self::$config[ 'collections' ][ $the_post->post_type ][ 'name' ], esc_url( admin_url( "post.php?post={$the_post->ID}&action=edit" ) ), $the_post->post_title )
							),
							'content-type: text/html'
						);
					}
					
					$_POST[ 'webcomic_transcript_submission' ] = true;
				} else {
					$_POST[ 'webcomic_transcript_submission' ] = false;
				}
			}
			
			do_action( 'webcomic_transcript_submitted' );
		}
	}
	
	/** Get default dynamic webcomic container URL's.
	 * 
	 * @hook init
	 */
	public function dynamic_defaults() {
		if ( isset( $_GET[ 'webcomic_dynamic_defaults' ] ) and 'xmlhttprequest' === strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) ) {
			$output = array();
			
			foreach ( $_GET[ 'webcomic_dynamic_defaults' ] as $default ) {
				$output[] = array(
					'url' => get_permalink( $default[ 'parent' ] ),
					'container' => $default[ 'container' ]
				);
			}
			
			echo json_encode( $output );
			
			die;
		}
	}
	
	/** Handle parameterized webcomic URL's.
	 * 
	 * @uses WebcomicTag::get_relative_webcomic_term_link()
	 * @uses WebcomicTag::get_relative_webcomic_link()
	 * @hook init
	 */
	public function webcomic_redirect() {
		$link = '';
		
		if ( isset( $_GET[ 'first_webcomic' ] ) or isset( $_GET[ 'last_webcomic' ] ) or isset( $_GET[ 'random_webcomic' ] ) ) {
			if ( isset( $_GET[ 'first_webcomic' ] ) ) {
				$relative = 'first';
			} elseif ( isset( $_GET[ 'last_webcomic' ] ) ) {
				$relative = 'last';
			} else {
				$relative = 'random';
			}
			
			$in_same_term   = empty( $_GET[ 'in_same_term' ] ) ? false : maybe_unserialize( stripslashes( urldecode( $_GET[ 'in_same_term' ] ) ) );
			$excluded_terms = empty( $_GET[ 'excluded_terms' ] ) ? false : maybe_unserialize( stripslashes( urldecode( $_GET[ 'excluded_terms' ] ) ) );
			$taxonomy       = empty( $_GET[ 'taxonomy' ] ) ? false : maybe_unserialize( stripslashes( urldecode( $_GET[ 'taxonomy' ] ) ) );
			$collection     = empty( $_GET[ "{$relative}_webcomic" ] ) ? array_rand( self::$config[ 'collections' ] ) : $_GET[ "{$relative}_webcomic" ];
			
			$link = WebcomicTag::get_relative_webcomic_link( $relative, $in_same_term, $excluded_terms, $taxonomy, $collection );
		} elseif ( isset( $_GET[ 'first_webcomic_term' ] ) or isset( $_GET[ 'last_webcomic_term' ] ) or isset( $_GET[ 'random_webcomic_term' ] ) ) {
			$target = empty( $_GET[ 'target' ] ) ? 'archive' : $_GET[ 'target' ];
			
			if ( isset( $_GET[ 'first_webcomic_term' ] ) ) {
				$relative = 'first';
			} elseif ( isset( $_GET[ 'last_webcomic_term' ] ) ) {
				$relative = 'last';
			} else {
				$relative = 'random';
			}
			
			if ( empty( $_GET[ "{$relative}_webcomic_term" ] ) ) {
				$taxonomy = sprintf( '%s_%s',
					array_rand( self::$config[ 'collections' ] ),
					array_rand( array( 'storyline' => true, 'character' => true ) )
				);
			} else {
				$taxonomy = $_GET[ "{$relative}_webcomic_term" ];
			}
			
			$args = empty( $_GET[ 'args' ] ) ? false : maybe_unserialize( stripslashes( urldecode( $_GET[ 'args' ] ) ) );
			$link = WebcomicTag::get_relative_webcomic_term_link( $target, $relative, $taxonomy, $args );
		}
		
		if ( $link ) {
			wp_redirect( $link );
			
			die;
		}
	}
	
	/** Check to see if the current page is webcomic-related.
	 * 
	 * We have to do this as early as possible to ensure that the
	 * correct template can be set if the collection is using a custom
	 * theme.
	 * 
	 * @uses Webcomic::$config
	 * @uses Webcomic::$integrate
	 * @uses Webcomic::$collection
	 * @hook setup_theme
	 */
	public function setup_theme() {
		global $wp_rewrite;
		
		$match = $permalinks = array();
		
		if ( $wp_rewrite->using_permalinks() ) {
			$tokens = array(
				'%year%'     => '(?P<year>\d{4})',
				'%monthnum%' => '(?P<monthnum>0[1-9]|1[012])',
				'%day%'      => '(?P<day>0[1-9]|[12][0-9]|3[01])',
				'%hour%'     => '(?P<hour>0[0-9]|1[0-9]|2[0-3])',
				'%minute%'   => '(?P<minute>0[1-9]|[1-5][0-9])',
				'%second%'   => '(?P<second>0[1-9]|[1-5][0-9])',
				'%post_id%'  => '(?P<post_id>\d+)',
				'%author%'   => '(?P<author>[\w-]+)'
			);
			
			foreach ( self::$config[ 'collections' ] as $k => $v ) {
				$tokens[ "%{$k}_storyline%" ]   = "(?P<{$k}_storyline>([\w-]+/?)+)";
				$permalinks[ "{$k}_archive" ]   = $v[ 'slugs' ][ 'archive' ];
				$permalinks[ "{$k}_webcomic" ]  = str_replace( array_keys( $tokens ), $tokens, $v[ 'slugs' ][ 'webcomic' ] );
				$permalinks[ "{$k}_storyline" ] = $v[ 'slugs' ][ 'storyline' ];
				$permalinks[ "{$k}_character" ] = $v[ 'slugs' ][ 'character' ];
			}
		}
		
		if (
				(
					preg_match( '/webcomic\d+(_(storyline|character))?/', join( ' ', array_keys( $_GET ) ), $match )
					or ( isset( $_GET[ 'post_type' ] ) and isset( self::$config[ 'collections' ][ $_GET[ 'post_type' ] ] ) and $match[ 0 ] = $_GET[ 'post_type' ] )
					or ( $wp_rewrite->using_permalinks() and preg_match( sprintf( '{/(%s)/}', join( '|', $permalinks ) ), $_SERVER[ 'REQUEST_URI' ], $match ) )
					or ( $id = url_to_postid( $_SERVER[ 'REQUEST_URI' ] ) and $match[ 0 ] = get_post_meta( $id, 'webcomic_collection', true ) and isset( self::$config[ 'collections' ][ $match[ 0 ] ] ) )
				)
			and $match
		) {
			if ( 2 < count( $match ) ) {
				foreach ( $permalinks as $k => $v ) {
					if ( false !== strpos( $k, '_webcomic' ) and preg_match( sprintf( '{%s}', $v ), $match[ 1 ] ) ) {
						self::$collection = str_replace( '_webcomic', '', $k );
						break;
					}
				}
			} else {
				$match[ 0 ]       = preg_replace( '/_(storyline|character)$/', '', $match[ 0 ] );
				self::$collection = empty( self::$config[ 'collections' ][ $match[ 0 ] ] ) ? preg_replace( '/_(archive|webcomic|storyline|character)$/', '', array_search( $match[ 1 ], $permalinks ) ) : $match[ 0 ];
			}
		}
		
		$active_theme    = new WP_Theme( get_stylesheet_directory(), '' );
		self::$integrate = !$active_theme->get( 'Webcomic' );
	}
	
	/** Filter titles and content for restricted webcomics.
	 * 
	 * @param object $post The global post object.
	 * @uses WebcomicTag::verify_webcomic_age()
	 * @uses WebcomicTag::verify_webcomic_role()
	 * @hook the_post
	 */
	public function the_post( $post ) {
		global $pages;
		
		if ( isset( self::$config[ 'collections' ][ $post->post_type ] ) ) {
			if ( !current_user_can( 'edit_post', $post->ID ) ) {
				if ( !WebcomicTag::verify_webcomic_role( $post->post_type ) ) {
					$post->post_title = __( 'Restricted Content', 'webcomic' );
					$pages            = array( is_user_logged_in() ? __( "You don't have permission to view this content.", 'webcomic' ) : sprintf( __( 'You must <a href="%s">log in</a> to view this content.', 'webcomic' ), wp_login_url( get_permalink( $post->ID ) ) ) );
					$post->content    = $pages[ 0 ];
					$post->ID         = 0;
				} elseif ( !$clear = WebcomicTag::verify_webcomic_age( $post->post_type ) ) {
					$post->post_title = __( 'Restricted Content', 'webcomic' );
					$pages            = array( is_null( $clear ) ? sprintf( __( 'Please <a href="%s">verify your age</a> to view this content.', 'webcomic' ), get_permalink( $post->ID ) ) : __( "You don't have permission to view this content.", 'webcomic' ) );
					$post->content    = $pages[ 0 ];
					$post->ID         = 0;
				}
			}
		}
	}
	
	/** Include webcomic posts on standard archive pages.
	 * 
	 * Also handles the crossover ep_mask for term crossover archives.
	 * 
	 * @param object $query The query object for the current page.
	 * @return object
	 * @uses Webcomic::$config
	 * @hook pre_get_posts
	 */
	public function pre_get_posts( $query ) {
		if ( $query->is_main_query() and is_tax() and !empty( $query->tax_query->queries[ 0 ][ 'taxonomy' ] ) and preg_match( '/^webcomic\d+_(storyline|character)$/', $query->tax_query->queries[ 0 ][ 'taxonomy' ] ) ) {
			if ( isset( $query->query_vars[ 'crossover' ] ) ) {
				$parts = explode( '/', $query->get( 'crossover' ) );
				
				if ( empty( $parts ) or 'page' === $parts[ 0 ] ) {
					$collections = array_keys( self::$config[ 'collections' ] );
					$key = array_search( str_replace( array( '_storyline', '_character' ), '', $query->tax_query->queries[ 0 ][ 'taxonomy' ] ), $collections );
					
					unset( $collections[ $key ] );
					
					$query->set( 'post_type', $collections );
					
					if ( isset( $parts[ 1 ] ) ) {
						$query->set( 'paged', $parts[ 1 ] );
					}
				} else {
					$collection = '';
					
					foreach ( self::$config[ 'collections' ] as $k => $v ) {
						if ( false === strpos( $query->tax_query->queries[ 0 ][ 'taxonomy' ], $k ) and $parts[ 0 ] === $v[ 'slugs' ][ 'name' ] ) {
							$collection = $k;
							
							break;
						}
					}
					
					if ( $collection ) {
						$query->set( 'post_type', array( $collection ) );
						
						if ( isset( $parts[ 2 ] ) ) {
							$query->set( 'paged', $parts[ 2 ] );
						}
					} else {
						$query->set( 'post_type', $parts[ 0 ] );
					}
				}
			} elseif ( !$query->get( 'post_type' ) ) {
				$query->set( 'post_type', array( str_replace( array( '_storyline', '_character' ), '', $query->tax_query->queries[ 0 ][ 'taxonomy' ] ) ) );
			}
		}
		
		if ( $query->is_main_query() and is_archive() and !$query->get( 'post_type' ) ) {
			$query->set( 'post_type', array_merge( array( 'post' ), array_keys( self::$config[ 'collections' ] ) ) );
		}
		
		return $query;
	}
	
	/** Email buffer alert notifications.
	 * 
	 * Hooks into the webcomic_buffer_alert event scheduled during
	 * installation. As with all WordPress scheduled events this
	 * requires someone actually visiting the site to trigger the event
	 * and send a buffer alert email.
	 * 
	 * @uses Webcomic::$config
	 * @hook webcomic_buffer_alert
	 */
	public function buffer_alert() {
		global $wpdb;
		
		$now = ( integer ) current_time( 'timestamp' );
		
		foreach ( self::$config[ 'collections' ] as $k => $v ) {
			if ( $v[ 'buffer' ][ 'hook' ] and $buffer = strtotime( $wpdb->get_var( $wpdb->prepare( "SELECT post_date FROM $wpdb->posts WHERE post_type = '%s' AND post_status = 'future' ORDER BY post_date DESC", $k ) ) ) and $eta = floor( ( $buffer - $now ) / 86400 ) and $eta <= $v[ 'buffer' ][ 'days' ] ) {
				wp_mail(
					$v[ 'buffer' ][ 'email' ],
					sprintf( _n( '[%s] %s Buffer Alert - %s Day Left', '[%s] %s Buffer Alert - %s Days Left', $eta, 'webcomic' ), get_bloginfo( 'name' ), $v[ 'name' ], $eta ),
					sprintf( __( 'This is an automated reminder that the buffer for %1$s expires on %2%s.', 'webcomic' ),
						sprintf( '<a href="%s">%s</a>', esc_url( admin_url( "edit.php?post_type={$k}" ) ), $v[ 'name' ] ),
						date( 'j F Y', $buffer )
					),
					'content-type: text/html'
				);
			}
		}
	}
	
	/** Register and enqueue javascript.
	 * 
	 * @uses Webcomic::$config
	 * @hook wp_enqueue_scripts
	 */
	public function wp_enqueue_scripts() {
		if ( self::$config[ 'shortcuts' ] ) {
			wp_register_script( 'webcomic-shortcuts', self::$url . '-/js/shortcuts.js', array( 'jquery', 'jquery-hotkeys' ), false, true );
			
			wp_enqueue_script( 'webcomic-shortcuts' );
		}
		
		if ( self::$config[ 'dynamic' ] ) {
			wp_register_script( 'webcomic-dynamic', self::$url . '-/js/dynamic.js', array( 'jquery' ), false, true );
			
			wp_enqueue_script( 'webcomic-dynamic' );
		}
		
		if ( self::$config[ 'gestures' ] ) {
			wp_register_script( 'webcomic-gestures', self::$url . '-/js/gestures.js', array( 'jquery' ), false, true );
			
			wp_enqueue_script( 'webcomic-gestures' );
		}
		
		wp_register_script( 'webcomic-dropdown', self::$url . '-/js/dropdown.js', array( 'jquery' ), false, true );
		wp_enqueue_script( 'webcomic-dropdown' );
	}
	
	/** Auto tweet on webcomic publish.
	 * 
	 * @param string $new New post status.
	 * @param string $old Old post status.
	 * @param string $post Post object to update.
	 * @uses Webcomic::$config
	 * @hook transition_post_status
	 * @filter string webcomic_tweet Filters the tweet text pushed to Twitter whenever a webcomic is published. Defaults to the collection-specific tweet format.
	 */
	public function tweet_webcomic( $new, $old, $post ) {
		if ( 'publish' === $new and 'publish' !== $old and !empty( self::$config[ 'collections' ][ $post->post_type ][ 'twitter' ][ 'format' ] ) and !empty( self::$config[ 'collections' ][ $post->post_type ][ 'twitter' ][ 'oauth_token' ] ) and !empty( self::$config[ 'collections' ][ $post->post_type ][ 'twitter' ][ 'oauth_secret' ] ) ) {
			$status      = self::$config[ 'collections' ][ $post->post_type ][ 'twitter' ][ 'format' ];
			$attachments = self::get_attachments( $post->ID );
			$attachment  = current( $attachments );
			
			if ( false !== strpos( self::$config[ 'collections' ][ $post->post_type ][ 'twitter' ][ 'format' ], '%' ) ) {
				$link   = wp_get_shortlink( $post->ID );
				$tokens = array_merge( array(
					'%url'               => $link ? $link : get_permalink( $post->ID ),
					'%date'              => get_the_time( get_option( 'date_format' ), $post ),
					'%time'              => get_the_time( get_option( 'time_format' ), $post ),
					'%title'             => get_the_title( $post->ID ),
					'%author'            => get_the_author_meta( 'display_name', $post->post_author ),
					'%site-url'          => home_url(),
					'%permalink'         => get_permalink( $post->ID ),
					'%site-name'         => get_bloginfo( 'name' ),
					'%collection-names'  => self::$config[ 'collections' ][ $post->post_type ][ 'name' ],
					'%collection-name'   => self::$config[ 'collections' ][ $post->post_type ][ 'name' ],
					'%collections'       => '#' . str_replace( array( '_', '-' ), '', self::$config[ 'collections' ][ $post->post_type ][ 'slugs' ][ 'name' ] ),
					'%collection'        => '#' . str_replace( array( '_', '-' ), '', self::$config[ 'collections' ][ $post->post_type ][ 'slugs' ][ 'name' ] )
				) );
				
				foreach ( array_merge( get_intermediate_image_sizes(), array( 'full' ) ) as $size ) {
					$tokens[ "%{$size}" ] = ( false !== strpos( $status, "%{$size}" ) and $image = wp_get_attachment_image_src( $attachment->ID, $size ) ) ? $image[ 0 ] : '';
				}
				
				if ( preg_match( sprintf( '/%%%s/', join( '|%', array_merge( array( 'storyline', 'character' ), self::$config[ 'collections' ][ $post->post_type ][ 'taxonomies' ] ) ) ), self::$config[ 'collections' ][ $post->post_type ][ 'twitter' ][ 'format' ] ) and $terms = wp_get_object_terms( $post->ID, array_merge( array( "{$post->post_type}_storyline", "{$post->post_type}_character" ), self::$config[ 'collections' ][ $post->post_type ][ 'taxonomies' ] ) ) and !is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
						$hash = str_replace( array( '_', '-' ), '', "#{$term->slug}" );
					
						if ( preg_match( '/^webcomic\d+_(storyline|character)$/', $term->taxonomy ) ) {
							$parts = explode( '_', $term->taxonomy );
						
							if ( false === strpos( $tokens[ 'collection-names' ], self::$config[ 'collections' ][ $parts[ 0 ] ][ 'name' ] ) ) {
								$tokens[ '%collections' ]       .= ' #' . str_replace( array( '_', '-' ), '', self::$config[ 'collections' ][ $parts[ 0 ] ][ 'slugs' ][ 'name' ] );
								$tokens[ '%collection-names' ]  .= ' ' . self::$config[ 'collections' ][ $parts[ 0 ] ][ 'name' ];
								$tokens[ '%xcollections' ]      .= empty( $tokens[ '%xcollection-names' ] ) ? '#' . str_replace( array( '_', '-' ), '', self::$config[ 'collections' ][ $parts[ 0 ] ][ 'slugs' ][ 'name' ] ) : ' #' . str_replace( array( '_', '-' ), '', self::$config[ 'collections' ][ $parts[ 0 ] ][ 'slugs' ][ 'name' ] );
								$tokens[ '%xcollection-names' ] .= empty( $tokens[ '%xcollection-names' ] ) ? self::$config[ 'collections' ][ $parts[ 0 ] ][ 'name' ] : ' ' . self::$config[ 'collections' ][ $parts[ 0 ] ][ 'name' ];
							}
						
							$tokens[ "%{$parts[ 1 ]}s" ]      .= empty( $tokens[ '%{$parts[ 1 ]}s' ] ) ? "{$hash}" : " {$hash}";
							$tokens[ "%{$parts[ 1 ]}-names" ] .= empty( $tokens[ '%{$parts[ 1 ]}-names' ] ) ? "{$term->name}" : " {$term->name}";
						
							if ( 0 === strpos( $term->taxonomy, $post->post_type ) ) {
								$tokens[ '%!{$parts[ 1 ]}s' ]      .= empty( $tokens[ '%!{$parts[ 1 ]}s' ] ) ? "{$hash}" : " {$hash}";
								$tokens[ '%!{$parts[ 1 ]}-names' ] .= empty( $tokens[ '%!{$parts[ 1 ]}-names' ] ) ? "{$term->name}" : " {$term->name}";
							} else {
								$tokens[ '%x{$parts[ 1 ]}s' ]      .= empty( $tokens[ '%x{$parts[ 1 ]}s' ] ) ? "{$hash}" : " {$hash}";
								$tokens[ '%x{$parts[ 1 ]}-names' ] .= empty( $tokens[ '%x{$parts[ 1 ]}-names' ] ) ? "{$term->name}" : " {$term->name}";
							}
						} else {
							$tokens[ "%{$term->taxonomy}-names" ] = isset( $tokens[ "%{$term->taxonomy}-names" ] ) ? $tokens[ "%{$term->taxonomy}-names" ] . " {$term->name}" : "{$term->name}";
							$tokens[ "%{$term->taxonomy}" ]       = isset( $tokens[ "%{$term->taxonomy}" ] ) ? $tokens[ "%{$term->taxonomy}" ]  . " {$hash}" : "{$hash}";
						}
					}
				}
				
				if ( preg_match_all( '/%field:(?(?=\{).+?\}|\S+)/', self::$config[ 'collections' ][ $post->post_type ][ 'twitter' ][ 'format' ], $matches ) ) {
					foreach ( $matches[ 0 ] as $match ) {
						if ( empty( $m[ $match ] ) ) {
							$m[ $match ] = get_post_meta( $post->ID, str_replace( array( '%field:', '{', '}' ), '', $match ), true );
						}
					}
				}
				
				$status = apply_filters( 'webcomic_tweet', str_replace( array_keys( $tokens ), $tokens, $status ), $post );
			}
			
			if ( $status ) {
				if ( !class_exists( 'tmhOAuth' ) ) {
					require_once self::$dir . '-/lib/twitter.php';
				}
				
				$oauth = new tmhOAuth( array(
					'consumer_key'    => self::$config[ 'collections' ][ $post->post_type ][ 'twitter' ][ 'consumer_key' ],
					'consumer_secret' => self::$config[ 'collections' ][ $post->post_type ][ 'twitter' ][ 'consumer_secret' ],
					'user_token'      => self::$config[ 'collections' ][ $post->post_type ][ 'twitter' ][ 'oauth_token' ],
					'user_secret'     => self::$config[ 'collections' ][ $post->post_type ][ 'twitter' ][ 'oauth_secret' ]
				) );
				
				if ( self::$config[ 'collections' ][ $post->post_type ][ 'twitter' ][ 'media' ] and $attachment ) {
					$file = get_attached_file( $attachment->ID );
					$name = basename( $file );
					$code = $oauth->request( 'POST', $oauth->url( '1.1/statuses/update_with_media' ), array(
						'media[]' => "@{$file};type={$attachment->post_mime_type};filename={$name}",
						'status'  => substr( strip_tags( $status ), 0, 140 )
					), true, true );
				} else {
					$code = $oauth->request( 'POST', $oauth->url( '1/statuses/update' ), array(
						'status'  => substr( strip_tags( $status ), 0, 140 )
					) );
				}
				
				if ( 200 !== intval( $code ) ) {
					$errors = get_transient( 'webcomic_error' );
					
					set_transient( 'webcomic_error', array_merge( array( sprintf( __( '<b>Twitter Error: %s</b>', 'webcomic' ), $oauth->response[ 'response' ] ? $oauth->response[ 'response' ] : $oauth->response[ 'error' ] ) ), $errors ? $errors : array() ), 1 );
				}
			}
		}
	}
	
	/** Integrate webcomics into the main site feed.
	 * 
	 * @param array $query Post request query.
	 * @return array
	 * @uses Webcomic::$collection
	 * @hook request
	 */
	public function request( $query ) {
		if ( isset( $query[ 'feed' ] ) and 1 === count( $query ) ) {
			$post_type = array( 'post' );
			
			foreach ( array_keys( self::$config[ 'collections' ] ) as $k ) {
				if ( self::$config[ 'collections' ][ $k ][ 'feeds' ][ 'main' ] ) {
					$post_type[] = $k;
				}
			}
			
			if ( 1 < count( $post_type ) ) {
				$query[ 'post_type' ] = $post_type;
			}
		}
		
		return $query;
	}
	
	/** Add a webcomic_image property to term objects.
	 * 
	 * @param object $term Retrieved term.
	 * @param string $taxonomy Taxonomy the term belongs to.
	 * @return object
	 * @uses Webcomic::$config
	 * @hook get_term
	 */
	public function get_term( $term, $taxonomy ) {
		if ( preg_match( '/^webcomic\d+_(storyline|character)$/', $taxonomy ) ) {
			$term->webcomic_image = empty( self::$config[ 'terms' ][ $term->term_id ][ 'image' ] ) ? 0 : self::$config[ 'terms' ][ $term->term_id ][ 'image' ];
		}
		
		return $term;
	}
	
	/** Return the appropriate theme ID for custom collection themes.
	 * 
	 * @param string $theme Name of the current theme.
	 * @return string
	 * @uses Webcomic::$config
	 * @uses Webcomic::$collection
	 * @hook template
	 */
	public function template( $theme ) {
		return ( self::$collection and self::$config[ 'collections' ][ self::$collection ][ 'theme' ] and $template = substr( self::$config[ 'collections' ][ self::$collection ][ 'theme' ], 0, strpos( self::$config[ 'collections' ][ self::$collection ][ 'theme' ], '|' ) ) and is_readable( get_theme_root() . "/{$template}" ) ) ? $template : $theme;
	}
	
	/** Display webcomics in place of transcripts in searches.
	 * 
	 * @param array $posts Posts array.
	 * @param object $query WP_Query object.
	 * @return array
	 * @hook the_posts
	 */
	public function the_posts( $posts, $query ) {
		if ( is_search() and $query->is_main_query() ) {
			$stati = get_post_stati( array( 'public' => true ) );
			
			foreach ( $posts as $k => $v ) {
				if ( 'webcomic_transcript' === $v->post_type ) {
					if ( $v->post_parent and in_array( get_post_status( $v->post_parent ), $stati ) ) {
						$posts[ $k ] = get_post( $v->post_parent );
					} else {
						unset( $posts[ $k ] );
					}
				}
			}
			
			return array_values( array_unique( $posts, SORT_REGULAR ) );
		}
		
		return $posts;
	}
	
	/** Add a webcomic_image property to term objects.
	 * 
	 * @param array $terms Array of retrieved terms.
	 * @param array $taxonomies Array of taxonomies the terms belong to.
	 * @param array $args Additional arguments passed to get_terms().
	 * @return array
	 * @uses Webcomic::$config
	 * @hook get_terms
	 */
	public function get_terms( $terms, $taxonomies, $args ) {
		if ( preg_match( '/webcomic\d+_(storyline|character)/', join( ' ', ( array ) $taxonomies ) ) ) {
			foreach ( $terms as $k => $v ) {
				if ( isset( $v->taxonomy ) and preg_match( '/^webcomic\d+_(storyline|character)$/', $v->taxonomy ) ) {
					$terms[ $k ]->webcomic_image = empty( self::$config[ 'terms' ][ $v->term_id ][ 'image' ] ) ? 0 : self::$config[ 'terms' ][ $v->term_id ][ 'image' ];
				}
			}
		}
		
		return $terms;
	}
	
	/** Return the appropriate theme ID for custom collection themes.
	 * 
	 * @param string $theme Name of the current theme.
	 * @return string
	 * @uses Webcomic::$config
	 * @uses Webcomic::$collection
	 * @hook stylesheet
	 */
	public function stylesheet( $theme ) {
		return ( self::$collection and self::$config[ 'collections' ][ self::$collection ][ 'theme' ] and $stylesheet = substr( self::$config[ 'collections' ][ self::$collection ][ 'theme' ], strpos( self::$config[ 'collections' ][ self::$collection ][ 'theme' ], '|' ) + 1 ) and is_readable( get_theme_root() . "/{$stylesheet}" ) ) ? $stylesheet : $theme;
	}
	
	/** Add webcomic classes to the body tag.
	 * 
	 * @param array $classes Array of body classes.
	 * @param mixed $class Additional classes passed to body_class().
	 * @return array
	 * @uses Webcomic::$config
	 * @uses Webcomic::$collection
	 * @hook body_class
	 */
	public function body_class( $classes, $class ) {
		if ( self::$collection ) {
			$classes[] = 'webcomic';
			$classes[] = esc_attr( sprintf( 'webcomic-%s', self::$config[ 'collections' ][ self::$collection ][ 'slugs' ][ 'name' ] ) );
		}
		
		return $classes;
	}
	
	/** Add webcomic classes to posts.
	 * 
	 * @param array $classes Array of post classes.
	 * @param mixed $class Additional classes passed to post_class().
	 * @param integer $id Post ID.
	 * @return array
	 * @uses Webcomic::get_attachments()
	 * @hook post_class
	 */
	public function post_class( $classes, $class, $id ) {
		$post_type = get_post_type( $id );
		
		if ( preg_match( '/^webcomic\d+$/', $post_type ) and $attachments = self::get_attachments( $id ) ) {
			$classes[] = sprintf( 'webcomic-attachments-%s', count( $attachments ) );
		}
		
		if ( $taxonomies = get_object_taxonomies( get_post( $id ) ) ) {
			$crossovers = array();
			
			foreach ( $taxonomies as $taxonomy ) {
				if ( $taxonomy !== "{$post_type}_storyline" and $taxonomy !== "{$post_type}_character" and preg_match( '/^webcomic\d+_(storyline|character)$/', $taxonomy ) and wp_get_object_terms( $id, $taxonomy, array( 'fields' => 'ids' ) ) ) {
					$crossovers[] = 'webcomic-crossover';
					$crossovers[] = str_replace( array( '_storyline', '_character' ), '', "$taxonomy-crossover" );
					$crossovers[] = "{$taxonomy}-crossover";
				}
			}
			
			if ( $crossovers = array_unique( $crossovers ) ) {
				$classes[] = join( ' ', $crossovers );
			}
		}
		
		return $classes;
	}
	
	/** Add a webcomic_image property to term objects.
	 * 
	 * @param array $terms Array of retrieved terms.
	 * @param integer $id Object ID the terms are related to.
	 * @param string $taxonomy Taxonomy the terms belong to.
	 * @return array
	 * @uses Webcomic::$config
	 * @hook get_the_terms
	 */
	public function get_the_terms( $terms, $id, $taxonomy ) {
		if ( preg_match( '/^webcomic\d+_(storyline|character)$/', $taxonomy ) ) {
			foreach ( $terms as $k => $v ) {
				$terms[ $k ]->webcomic_image = empty( self::$config[ 'terms' ][ $v->term_id ][ 'image' ] ) ? 0 : self::$config[ 'terms' ][ $v->term_id ][ 'image' ];
			}
		}
		
		return $terms;
	}
	
	/** Handle custom permalink tokens.
	 * 
	 * @todo Use get_term_parents; see core.trac.wordpress.org/ticket/17069
	 * 
	 * @param string $link Permalink to swap tokens in.
	 * @param object $post Post object.
	 * @param boolean $name Whether to keep post name.
	 * @param boolean $sample Whether this is a sample permalink.
	 * @return string
	 * @uses Webcomic::$config
	 * @hook post_type_link
	 */
	public function post_type_link( $link, $post, $name, $sample ) {
		if ( empty( self::$config[ 'collections' ][ $post->post_type ] ) or false === strpos( $link, '%' ) ) {
			return $link;
		}
		
		if ( false !== strpos( $link, "%{$post->post_type}_storyline%" ) and $storylines = get_the_terms( $post->ID, "{$post->post_type}_storyline" ) and !is_wp_error( $storylines ) ) {
			$storylines = array_reverse( $storylines );
			$storyline  = array( $storylines[ 0 ]->slug );
			
			if ( $parent = $storylines[ 0 ]->parent and $parents = get_ancestors( $storylines[ 0 ]->term_id, $storylines[ 0 ]->taxonomy ) ) {
				foreach ( $parents as $parent ) {
					$the_parent  = get_term( $parent, $storylines[ 0 ]->taxonomy );
					$storyline[] = $the_parent->slug;
				}
			}
			
			$storyline = array_reverse( $storyline );
			$storyline = join( '/', $storyline );
		} else {
			$storyline = '';
		}
		
		$time   = explode( ' ', date( 'Y m d H i s', strtotime( $post->post_date ) ) );
		$tokens = array(
			'%year%'     => $time[ 0 ],
			'%monthnum%' => $time[ 1 ],
			'%day%'      => $time[ 2 ],
			'%hour%'     => $time[ 3 ],
			'%minute%'   => $time[ 4 ],
			'%second%'   => $time[ 5 ],
			'%post_id%'  => $post->ID,
			'%author%'   => false !== strpos( $link, '%author%' ) ? get_userdata( $post->post_author )->user_nicename : '',
			"%{$post->post_type}_storyline%" => $storyline
		);
		
		return str_replace( array_keys( $tokens ), $tokens, $link );
	}
	
	/** Load custom templates for Webcomic-related pages.
	 * 
	 * Changes the template hierarchy when a Webcomic-related page is
	 * found, loadig Webcomic-specific templates from a `webcomic`
	 * directory inside the current them (if they exst).
	 * 
	 * @param string $template The template file to be included.
	 * @return string
	 * @uses Webcomic::$collection
	 * @uses WebcomicTag::verify_webcomic_age()
	 * @uses WebcomicTag::verify_webcomic_role()
	 * @hook template_include
	 */
	public function template_include( $template ) {
		global $wp_query;
		
		$template = str_replace( array( get_stylesheet_directory() . '/', get_template_directory() . '/' ), '', $template );
		
		if ( self::$collection and ( !is_page() or !$page_template = get_page_template_slug() or !validate_file( $page_template ) ) ) {
			$object     = get_queried_object();
			$collection = self::$collection;
			
			if ( isset( $_GET[ 'webcomic_dynamic' ] ) and 'xmlhttprequest' === strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) ) {
				if ( !$template = locate_template( array( "webcomic/dynamic-{$collection}-{$_GET[ 'webcomic_dynamic' ]}.php", "webcomic/dynamic-{$_GET[ 'webcomic_dynamic' ]}.php", "webcomic/dynamic-{$collection}.php", 'webcomic/dynamic.php' ) ) ) {
					$template = self::$dir . '-/php/integrate/dynamic.php';
				}
				
				return $template;
			} elseif ( !WebcomicTag::verify_webcomic_role( $collection ) ) {
				if ( !$template = locate_template( array( "webcomic/restricted-role-{$collection}.php", 'webcomic/restricted-role.php', 'webcomic/restricted.php' ) ) ) {
					$template = self::$dir . '-/php/integrate/restricted-role.php';
				}
				
				return $template;
			} elseif ( !WebcomicTag::verify_webcomic_age( $collection ) ) {
				if ( !$template = locate_template( array( "webcomic/restricted-age-{$collection}.php", 'webcomic/restricted-age.php', 'webcomic/restricted.php' ) ) ) {
					$template = self::$dir . '-/php/integrate/restricted-age.php';
				}
				
				return $template;
			} elseif ( is_front_page() and is_home() ) {
				$template = array( "webcomic/home-{$collection}.php", 'webcomic/home.php', $template );
			} elseif ( is_attachment() ) {
				$mt = explode( '/', $object->post_mime_type );
				$template = array(  "webcomic/{$mt[ 0 ]}-{$collection}.php", "webcomic/{$mt[ 0 ]}.php", "webcomic/{$mt[ 1 ]}-{$collection}.php", "webcomic/{$mt[ 1 ]}.php", "webcomic/{$mt[ 0 ]}_{$mt[ 1 ]}-{$collection}.php", "webcomic/{$mt[ 0 ]}_{$mt[ 1 ]}.php", "webcomic/attachment-{$collection}.php", 'webcomic/attachment.php', $template );
			} elseif ( is_single() ) {
				if ( isset( $wp_query->query_vars[ 'prints' ] ) ) {
					$template = array( "webcomic/prints-{$collection}.php", 'webcomic/prints.php', "single-{$collection}", 'webcomic/single.php', 'single.php', $template );
				} elseif ( false === strpos( $template, $collection ) ) {
					$template = array( 'webcomic/single.php', $template );
				}
			} elseif ( is_page() and false === strpos( $template, $object->post_name ) and false === strpos( $template, $object->ID ) ) {
				$template = array( "webcomic/page-{$collection}.php", 'webcomic/page.php', $template );
			} elseif ( is_tax() ) {
				if ( isset( $wp_query->query_vars[ 'crossover' ] ) ) {
					$template = array( "webcomic/crossover-{$object->taxonomy}-{$object->slug}.php", "webcomic/crossover-{$object->taxonomy}.php", sprintf( 'webcomic/crossover-%s.php', false !== strpos( $object->taxonomy, 'storyline' ) ? 'storyline' : 'character' ), 'webcomic/crossover.php', "taxonomy-{$object->taxonomy}-{$object->slug}.php", "taxonomy-{$object->taxonomy}.php", sprintf( 'webcomic/%s.php', false !== strpos( $object->taxonomy, 'storyline' ) ? 'storyline' : 'character' ), 'webcomic/taxonomy.php', 'taxonomy.php', 'webcomic/archive.php', $template );
				} elseif ( false === strpos( $template, $object->taxonomy ) ) {
					$template = array( sprintf( 'webcomic/%s.php', false !== strpos( $object->taxonomy, 'storyline' ) ? 'storyline' : 'character' ), 'webcomic/taxonomy.php', 'taxonomy.php', 'webcomic/archive.php', $template );
				}
			} elseif ( is_post_type_archive() and false === strpos( $template, $collection ) ) {
				$template = array( 'webcomic/archive.php', $template );
			}
		} elseif ( is_front_page() and is_home() ) {
			$template = array( 'webcomic/home.php', $template );
		}
		
		return locate_template( $template );
	}
	
	/** Add webcomic previews to feed content.
	 * 
	 * @param string $content The post content.
	 * @return string
	 * @uses Webcomic::$config
	 * @uses Webcomic:get_attachments()
	 * @hook the_content_feed
	 * @template feed-{$collectionID}.php, feed.php
	 */
	public function the_content_feed( $content ) {
		global $post;
		
		if ( !empty( self::$config[ 'collections' ][ $post->post_type ][ 'feeds' ][ 'hook' ] ) ) {
			$prepend     = $append = '';
			$feed_size   = self::$config[ 'collections' ][ $post->post_type ][ 'feeds' ][ 'size' ];
			$attachments = self::get_attachments( $post->ID );
			
			if ( $template = locate_template( array( "webcomic/feed-{$post->post_type}.php", 'webcomic/feed.php' ) ) ) {
				require $template;
			} else {
				require self::$dir . '-/php/integrate/feed.php';
			}
			
			$content = $prepend . $content . $append;
		}
		
		return $content;
	}
	
	/** Add a webcomic_image property to term objects.
	 * 
	 * @param array $terms Array of retrieved terms.
	 * @param integer $objects Object ID's the terms are related to.
	 * @param string $taxonomies Taxonomy the terms belong to.
	 * @param array $args Arguments passed to wp_get_object_terms().
	 * @return array
	 * @uses Webcomic::$config
	 * @hook get_the_terms
	 */
	public function wp_get_object_terms( $terms, $objects, $taxonomies, $args ) {
		if ( 'all' === $args[ 'fields' ] and preg_match( '/webcomic\d+_(storyline|character)/', join( ' ', ( array ) $taxonomies ) ) ) {
			foreach ( $terms as $k => $v ) {
				if ( isset( $v->taxonomy ) and preg_match( '/^webcomic\d+_(storyline|character)$/', $v->taxonomy ) ) {
					$terms[ $k ]->webcomic_image = empty( self::$config[ 'terms' ][ $v->term_id ][ 'image' ] ) ? 0 : self::$config[ 'terms' ][ $v->term_id ][ 'image' ];
				}
			}
		}
		
		return $terms;
	}
	
	/** Add the 'Webcomic' key for theme headers.
	 * 
	 * Theme authors can specify a theme as being 'Webcomic ready' by
	 * adding a Webcomic key with the minimum required version of
	 * Webcomic, like:
	 * 
	 * `Webcomic: 4`
	 * 
	 * @param array $extra Extra theme header fields.
	 * @return array
	 * @hook extra_theme_headers
	 */
	public function extra_theme_headers( $extra ) {
		$extra[] = 'Webcomic';
		
		return $extra;
	}
	
	/** Use caption for title of media objects attached to a webcomic.
	 *  
	 * @param array $attributes An array of media attributes.
	 * @param object $attachment The media object.
	 * @return array
	 * @uses Webcomic::$config
	 * @hook wp_get_attachment_image_attributes
	 */
	public function wp_get_attachment_image_attributes( $attributes, $attachment ) {
		if ( $attachment->post_parent and isset( self::$config[ 'collections' ][ get_post_type( $attachment->post_parent ) ] ) ) {
			$attributes[ 'data-webcomic-parent' ] = $attachment->post_parent;
			
			if ( $attachment->post_excerpt ) {
				$attributes[ 'title' ] = esc_attr( trim( strip_tags( $attachment->post_excerpt ) ) );
			}
		}
		
		return $attributes;
	}
	
	/** Automagically integrate basic webcomic functionality.
	 * 
	 * @param object $query WP_Query object for the loop.
	 * @return null
	 * @uses Webcomic::$dir
	 * @uses Webcomic::$config
	 * @uses Webcomic::$integrate
	 * @hook loop_start
	 * @action webcomic_loop_end Triggered during integration just before Webcomic content is appended to the end of a WordPress loop.
	 * @template loop_end-{$collection}.php, loop_end.php
	 */
	public function loop_end( $query ) {
		global $post;
		
		if ( self::$integrate and $query->is_main_query() and is_singular( array_keys( self::$config[ 'collections' ] ) ) ) {
			do_action( 'webcomic_loop_end', self::$collection );
			
			if ( !locate_template( array( 'webcomic/loop_end-' . self::$collection . '.php', 'webcomic/loop_end.php' ), true, false ) ) {
				require self::$dir . '-/php/integrate/loop_end.php';
			}
		}
	}
	
	/** Automagically integrate basic webcomic functionality.
	 * 
	 * @param object $query WP_Query object for the loop.
	 * @return null
	 * @uses Webcomic::$dir
	 * @uses Webcomic::$config
	 * @uses Webcomic::$integrate
	 * @hook loop_start
	 * @action webcomic_loop_start Triggered during integration just before Webcomic content is prepended to the start of a WordPress loop.
	 * @template loop_start-{$collection}.php, loop_start.php
	 */
	public function loop_start( $query ) {
		global $post;
		
		if ( self::$integrate and $query->is_main_query() ) {
			if ( is_front_page() and $webcomic = get_posts( array(
				'numberposts' => 1,
				'post_type'   => array_keys( self::$config[ 'collections' ] )
			) ) ) {
				$post = $webcomic[ 0 ];
				setup_postdata( $webcomic[ 0 ] );
			} elseif ( is_front_page() ) {
				return;
			}
			
			do_action( 'webcomic_loop_start', self::$collection );
			
			if ( !locate_template( array( 'webcomic/loop_start-' . self::$collection . '.php', 'webcomic/loop_start.php' ), true, false ) ) {
				require self::$dir . '-/php/integrate/loop_start.php';
			}
		}
	}
	
	/** Automagically integrate basic webcomic functionality.
	 * 
	 * @param string $excerpt The post excerpt.
	 * @return string
	 * @uses Webcomic::$integrate
	 * @hook the_content
	 * @template the_excerpt-{$collection}.php, the_excerpt.php
	 */
	public function the_excerpt( $excerpt ) {
		global $wp_query, $post;
		
		if ( self::$integrate and in_the_loop() and $wp_query->is_main_query() and !is_feed() and $collection = get_post_type( $post ) and isset( self::$config[ 'collections' ][ $collection ] ) ) {
			$prepend = $append = '';
			
			if ( $template = locate_template( array( "webcomic/the_excerpt-{$collection}.php", 'webcomic/the_excerpt.php' ) ) ) {
				require $template;
			} else {
				require self::$dir . '-/php/integrate/the_excerpt.php';
			}
		}
		
		return $excerpt;
	}
	
	/** Automagically integrate basic webcomic functionality.
	 * 
	 * @param string $content The post content.
	 * @return string
	 * @uses Webcomic::$integrate
	 * @hook the_content
	 * @template the_content-{$collection}.php, the_content.php
	 */
	public function the_content( $content ) {
		global $wp_query, $post;
		
		if ( self::$integrate and in_the_loop() and $wp_query->is_main_query() and !is_feed() and $collection = get_post_type( $post ) and isset( self::$config[ 'collections' ][ $collection ] ) ) {
			$prepend = $append = '';
			
			if ( $template = locate_template( array( "webcomic/the_content-{$collection}.php", 'webcomic/the_content.php' ) ) ) {
				require $template;
			} else {
				require self::$dir . '-/php/integrate/the_content.php';
			}
			
			$content = $prepend . $content . $append;
		}
		
		return $content;
	}
	
	/** Automagically integrate basic webcomic functionality.
	 * 
	 * Reorders webcomics on post type and taxonomy archive pages so
	 * that they appear in chronological order.
	 * 
	 * @param object $query WP_Query object.
	 * @uses WebcomicTag::is_webcomic_archive()
	 * @uses WebcomicTag::is_webcomic_tax()
	 */
	public function integrate_sort_asc( $query ) {
		if ( self::$integrate and $query->is_main_query() and ( WebcomicTag::is_webcomic_archive() or WebcomicTag::is_webcomic_tax() ) ) {
			$query->set( 'order', 'ASC' );
		}
	}
	
	/** Return Webcomic attachments for a post.
	 * 
	 * @param integer $id Post ID to retrieve attachments for.
	 * @return array
	 */
	protected function get_attachments( $id = 0 ) {
		return get_children( array(
			'order'          => 'ASC',
			'orderby'        => 'menu_order',
			'post_type'      => 'attachment',
			'post_parent'    => $id,
			'post_mime_type' => 'image'
		) );
	}
	
	/** Provides access to the plugin directory path.
	 * 
	 * @uses Webcomic::$dir
	 * @return string
	 */
	public static function dir() {
		return self::$dir;
	}
	
	/** Provides access to the plugin URL path.
	 * 
	 * @uses Webcomic::$url
	 * @return string
	 */
	public static function url() {
		return self::$url;
	}
	
	/** Provides access to the plugin configuration.
	 * 
	 * @uses Webcomic::$config
	 * @return array
	 */
	public static function config() {
		return self::$config;
	}
}

if ( is_admin() ) { // Load and instantiate the administrative class.
	require_once dirname( __FILE__ ) . '/-/php/admin.php'; new WebcomicAdmin;
} else { // Instantiate the standard class.
	new Webcomic;
} the standard class.
	new Webcomic;
}
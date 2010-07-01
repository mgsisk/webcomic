<?php
/*
Text Domain: webcomic
Plugin Name: Webcomic
Plugin URI: http://webcomicms.net/
Description: Comic publishing power for WordPress. Create, manage, and share your webcomics like never before.
Version: 3.0.3
Author: Michael Sisk
Author URI: http://maikeruon.com/

Copyright 2008 Michael Sisk (email: mike@maikeruon.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/



/** Load the core */
if ( !class_exists( 'mgs_core' ) ) require_once( 'webcomic-includes/mgs-core.php' );

/**
 * Defines all Webcomic plugin functionality,
 * extending mgs_core. For administrative
 * functions and filters, see the webcomic_admin
 * class in webcomic-includes/admin.php
 * 
 * @package webcomic
 * @since 3
 */
class webcomic extends mgs_core {
	/** Override mgs_core variables */
	protected $name    = 'webcomic';
	protected $version = '3.0.3';
	protected $file    = __FILE__;
	protected $type    = 'plugin';
	
	////
	// Core
	// 
	// These functions define critical features
	// and should never be called directly.
	////
	
	/**
	 * Run-once installation.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function install() {
		$this->domain();
		
		$this->option( array(
			'version'             => $this->version,
			'default_collection'  => false,
			'integrate_toggle'    => false,
			'secure_toggle'       => false,
			'transcribe_toggle'   => true,
			'transcribe_restrict' => 'anyone',
			'transcribe_language' => array( 'en' => __( 'English', 'webcomic' ) ),
			'transcribe_default'  => array( 'en' => __( 'English', 'webcomic' ) ),
			'feed_toggle'         => true,
			'feed_size'           => 'full',
			'buffer_toggle'       => true,
			'buffer_size'         => 7,
			'age_toggle'          => true,
			'age_size'            => 18,
			'shortcut_toggle'     => true,
			'paypal_business'     => '',
			'paypal_currency'     => 'USD',
			'paypal_log'          => false,
			'paypal_prints'       => false,
			'paypal_method'       => '_xclick',
			'paypal_price_d'      => 8,
			'paypal_price_i'      => 11,
			'paypal_price_o'      => 14,
			'paypal_shipping_d'   => 2,
			'paypal_shipping_i'   => 4,
			'paypal_shipping_o'   => 6,
			'paypal_donation'     => 0,
			'large_h'             => get_option( 'large_size_h' ),
			'large_w'             => get_option( 'large_size_w' ),
			'medium_h'            => get_option( 'medium_size_h' ),
			'medium_w'            => get_option( 'medium_size_w' ),
			'small_h'             => get_option( 'thumbnail_size_h' ),
			'small_w'             => get_option( 'thumbnail_size_w' ),
			'term_meta'           => array(
				'collection'      => array(),
				'storyline'       => array(),
				'character'       => array()
			)
		) );
		
		$default = wp_insert_term( __( 'Untitled', 'webcomic' ), 'webcomic_collection' );
		
		$this->option( 'default_collection', $default[ 'term_id' ] );
		
		wp_schedule_event( time(), 'daily', 'webcomic_buffer_alert' );
		
		$a = ( get_option( 'webcomic_version' ) ) ? sprintf( __( "If you're upgrading from a previous version please <a href='%s'>visit the Upgrade Webcomic page</a> now.", "webcomic" ), admin_url( 'admin.php?page=webcomic_tools&subpage=upgrade_webcomic' ) ) : sprintf( __( 'You may want to <a href="%s">adjust some settings</a>, or you can <a href="%s">go to the Library</a> to start publishing webcomics.', 'webcomic' ), admin_url( 'admin.php?page=webcomic_settings' ), admin_url( 'admin.php?page=webcomic_collection' ) );
		
		$this->update[ 'installed' ] = sprintf( __( 'Thanks for choosing Webcomic! %s', 'webcomic' ), $a );
	}
	
	/**
	 * Upgrades older versions.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function upgrade() {
		$this->domain();
		
		$this->option( 'version', $this->version );
		
		if ( !is_array( $this->option( 'term_meta' ) ) )
			$this->option( 'term_meta', array( 'collection' => array(), 'storyline' => array(), 'character' => array() ) );
		
		$this->update[ 'upgraded' ] = sprintf( __( 'Thanks again for choosing Webcomic! Your <a href="%s">support</a> is much appreciated.', 'webcomic' ), 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=R6SH66UF6F9DG' );
	}
	
	/**
	 * Uninstalls the plugin
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function uninstall() {
		global $wpdb;
		
		$this->domain();
		
		$cat = ( int ) get_option( 'default_category' );
		
		if ( $collections = get_terms( 'webcomic_collection', 'get=all&fields=ids' ) )
			foreach ( $collections as $collection )
				wp_delete_term( $collection, 'webcomic_collection' );
		
		if ( $storylines = get_terms( 'webcomic_storyline', 'get=all&fields=ids' ) )
			foreach ( $storylines as $storyline )
				wp_delete_term( $storyline, 'webcomic_storyline' );
		
		if ( $characters = get_terms( 'webcomic_character', 'get=all&fields=ids' ) )
			foreach ( $characters as $character )
				wp_delete_term( $character, 'webcomic_character' );
		
		$posts = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE post_type = 'webcomic_post'" );
		$wpdb->query( "UPDATE $wpdb->posts SET post_type = 'post' WHERE post_type = 'webcomic_post'" );
		$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key IN ( 'webcomic' )" );
		
		foreach ( $posts as $p )
			wp_set_object_terms( $p, $cat, 'category' );
		
		$wp_user_search = new WP_User_Search( null, null, null );
		
		foreach ( $wp_user_search->get_results() as $userid )
			delete_user_meta( $userid, 'webcomic' );
		
		$this->option( array(
			'version'   => $this->version,
			'uninstall' => true
		) );
		
		$this->update[ 'uninstalled' ] = __( 'Information, files, and settings related to Webcomic have been removed.', 'webcomic' );
	}
	
	
	
	////
	// Template Tags
	// 
	// These functions define new template tags
	// to be used when designing WordPress themes.
	////
	
	/**
	 * Shortcut function for retrieving a single webcomic.
	 * 
	 * Since WordPress doesn't have a get_post filter that can
	 * be used to automatically add webcomic metadata to the
	 * post object we're using get_posts with suppress_filters
	 * disabled and 'any' for post_status as a work around.
	 * Hopefully we can trash this later in favor of a hook.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param int $id The post ID.
	 * @return A post object with Webcomic metadata, or false on error.
	 */
	function get_webcomic_post( $id ) {
		return current( get_posts( array( 'p' => $id, 'post_type' => 'webcomic_post', 'post_status' => 'any', 'suppress_filters' => false ) ) );
	}
	
	/**
	 * Retrieves a properly formatted webcomic object for the a specified post or term.
	 * 
	 * @package webcoomic
	 * @since 3
	 * 
	 * @param str $size The size of the object. Must be one of 'full', 'large', 'medium', or 'small'.
	 * @param str $type The type of item to retrieve objects for. Must be one of 'post', 'collection', 'storyline', or 'character'.
	 * @param int $key The file index to return. If specified, only that part of the object will be returned.
	 * @param int $id The post or term ID to retrieve objects for. Only required when $type is one of 'collection', 'storyline', or 'character'.
	 * @param str $format The format webcomic objects should be retrieved in. May be one of 'html', 'shtml', 'bbcode', or 'sbbcode'.
	 * @return A properly formatted webcomic object string, or false on error.
	 */
	function get_webcomic_object( $size, $type, $key = false, $id = false, $format = false ) {
		global $wpdb, $post;
		
		if ( !( 'full' == $size || 'large' == $size || 'medium' == $size || 'small' == $size ) || ( !$id && ( 'collection' == $type || 'storyline' == $type || 'character' == $type ) ) || ( !$id && !$post ) || !$this->verify() )
			return false;
		
		$r  = array();
		
		if ( 'post' == $type )
			$obj = ( $id && $post->ID != $id ) ? $this->get_webcomic_post( $id ) : $post;
		else
			$obj = get_term( $id, "webcomic_$type" );
			
		$secure = $this->option( 'secure_toggle' );
		
		if ( ( $files = $obj->webcomic_files[ $size ] ) ) {
			foreach ( $files as $k => $f ) {
				if ( false !== $key && $key != $k )
					continue;
				
				if ( $format )
					$r[] = $f[ $format ];
				else
					$r[] = ( $secure ) ? $f[ 'shtml' ] : $f[ 'html' ];
			}
		}
		
		return apply_filters( 'webcomic_get_object', implode( '', $r ), $size, $type, $key, $id, $format );
	}
	
	// Display
	
	/**
	 * Retrieves a properly formatted webcomic object.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $size The size of the object. Must be one of 'full', 'large', 'medium', or 'small'.
	 * @param str $link How to link the returned object. May be one of 'random', 'first', 'previous', 'next', 'last', or 'self'.
	 * @param str $taxonomy A taxonomy to limit the linked webcomics to if $link is not false.
	 * @param int|arr $terms A term ID or array of term ID's. Required if $taxonomy is not false.
	 * @param int $key The file index to return. If specified, only that part of the object will be returned.
	 * @param int $id The post ID to retrieve objects for. Only required when used outside of a WordPress loop.
	 * @return The webcomic object, or false on error.
	 */
	function get_the_webcomic_object( $size, $link = false, $taxonomy = false, $terms = false, $key = false, $id = false ) {
		global $post;
		
		$id = ( intval( $id ) > 0 ) ? $id : $post->ID;
		
		if ( !( $obj = $this->get_webcomic_object( $size, 'post', $key, $id ) ) )
			return false;
		
		$kbd = ( $this->option( 'shortcut_toggle' ) ) ? ' webcomic-kbd-shortcut' : '';
		
		if ( ( 'random' == $link || 'first' == $link || 'previous' == $link || 'next' == $link || 'last' == $link ) && ( $ids = $this->get_relative_webcomics( $taxonomy, $terms, $id ) ) )
			$r   = '<a href="' . $this->get_relative_webcomic_url( $link, $taxonomy, $terms, $id ) . '" class="' . $link . '-webcomic-link ' . $link . '-webcomic-link-' . $ids[ $link ] . '" rel="' . $link . '">' . $obj . '</a>';
		elseif ( 'self' == $link )
			$r = '<a href="' . get_permalink( $id ) . '" class="webcomic-link webcomic-link-' . $id . $kbd . '" rel="' . $link . '>' . $obj . '</a>';
		else
			$r = $obj;
		
		return apply_filters( 'webcomic_get_the_object', $r, $size, $key, $id, $link, $taxonomy, $terms );
	}
	
	/**
	 * Retrieves embed code for sharing the specified webcomic.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $format The format webcomic objects should be retrieved in. Must be one of 'html', 'shtml', 'bbcode', or 'sbbcode'.
	 * @param str $size The size of the object. Must be one of 'full', 'large', 'medium', or 'small'.
	 * @param int $key The file index to return. If specified, only that part of the object will be returned.
	 * @param int $id The post ID to retrieve objects for. Only required when used outside of a WordPress loop.
	 * @return Embed webcomic code for sharing, or false on error.
	 */
	function get_webcomic_embed( $format, $size, $key = false, $id = false ) {
		global $post;
		
		$id = ( intval( $id ) > 0 ) ? $id : $post->ID;
		$format = ( $format ) ? $format : 'shtml';
		
		if ( !( $obj = $this->get_webcomic_object( $size, 'post', $key, $id, $format ) ) )
			return false;
		
		$r = ( 'html' == $format || 'shtml' == $format ) ? htmlspecialchars( '<div class="embedded-webcomic"><a href="' . get_permalink( $id ) . '">' . $obj . '</a><br><a href="' . get_bloginfo( 'url' ) . '">' . get_bloginfo( 'name' ) . '</a></div>' ) : "[url=" . get_permalink( $id ) . "]" . $obj . "[/url]\n\n[url=" . get_bloginfo( 'url' ) . "]" . get_bloginfo( 'name' ) . "[/url]";
		
		return apply_filters( 'webcomic_get_embed', $r, $format, $size, $key, $id );
	}
	
	/**
	 * Retrieves embed code form field for sharing the specified webcomic.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $format The format webcomic objects should be retrieved in. Must be one of 'html', 'shtml', 'bbcode', or 'sbbcode'.
	 * @param str $size The size of the object. Must be one of 'full', 'large', 'medium', or 'small'.
	 * @param int $key The file index to return. If specified, only that part of the object will be returned.
	 * @param int $id The post ID to retrieve objects for. Only required when used outside of a WordPress loop.
	 * @return Embed webcomic code in input field for sharing, or false on error.
	 */
	function get_the_webcomic_embed( $format, $size, $key = false, $id = false ) {
		global $post;
		
		$id = ( intval( $id ) > 0 ) ? $id : $post->ID;
		
		if ( !( $code = $this->get_webcomic_embed( $format, $size, $key, $id ) ) )
			return false;
		
		$r = '<input type="text" name="webcomic_embed_code" class="webcomic-embed-code webcomic-embed-code-' . $id . '" value="' . $code . '" readonly>';
		
		return apply_filters( 'webcomic_get_the_embed', $r, $code, $format, $size, $key, $id );
	}
	
	// Navigation
	
	/**
	 * Retrieves webcomic ID's relative to the specified webcomic.
	 * 
	 * This function can also be used outside of the loop
	 * to retrieve first, last, and random webcomics.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $taxonomy A taxonomy to limit the returned ID's to.
	 * @param int|arr $terms A term ID or array of term ID's. Required if $taxonomy is not false.
	 * @param int $id Webcomic ID that returned ID's will be relative to.
	 * @return An array of webcomic ID's, or false on error.
	 */
	function get_relative_webcomics( $taxonomy = false, $terms = false, $id = false, $global = false ) {
		global $wpdb, $post;
		
		if ( !$this->verify() )
			return false;
		
		$id = ( !$id && !empty( $post ) ) ? $post->ID : $id;
		$ck = $id . '_' . hash( 'md5', $taxonomy . implode( ( array ) $terms, '' ) . $global );
		
		if ( $r = wp_cache_get( $ck, 'get_relative_webcomics' ) )
			return $r;
		
		if ( $terms && $taxonomy && ( 'webcomic_collection' == $taxonomy || 'webcomic_storyline' == $taxonomy || 'webcomic_character' == $taxonomy ) ) {
			$terms = ( array ) $terms;
			
			foreach ( $terms as $k => $v )
				$terms[ $k ] = ( int ) $v;
		}
		
		$join = ( $terms ) ? "INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = '$taxonomy' AND tt.term_id IN ( '" . implode( "', '", $terms ) . "' )" : '';
		
		if ( !$global && !$join ) {
			$wc   = current( wp_get_object_terms( $id, 'webcomic_collection' ) );
			$join = "INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'webcomic_collection' AND tt.term_id = '$wc->term_id'";
		}
		
		$query = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts AS p $join WHERE p.post_type = 'webcomic_post' AND p.post_status IN ( 'publish', 'private' ) ORDER BY p.post_date ASC" ) );
		$r     = array();
		
		if ( !empty( $query ) ) {
			$r[ 'random' ] = ( int ) $query[ array_rand( $query, 1 ) ];
			$r[ 'last' ]   = ( int ) end( $query );
			$r[ 'first' ]  = ( int ) reset( $query );
			
			if ( false !== ( $key = array_search( $id, $query ) ) ) {
				$r[ 'previous' ]      = ( int ) ( ( $key - 1 ) > -1 && isset( $query[ $key - 1 ] ) ) ? $query[ $key - 1 ] : $id;
				$r[ 'next' ]          = ( int ) ( ( $key + 1 ) < count( $query ) && isset( $query[ $key + 1 ] ) ) ? $query[ $key + 1 ] : $id;
				$r[ 'first-bookend' ] = $r[ 'previous-bookend' ] = ( int ) ( $wc->webcomic_bookend[ 'first' ] ) ? $wc->webcomic_bookend[ 'first' ] : $id;
				$r[ 'last-bookend' ]  = $r[ 'next-bookend' ]     = ( int ) ( $wc->webcomic_bookend[ 'last' ] ) ? $wc->webcomic_bookend[ 'last' ] : $id;
			}
		}
		
		wp_cache_add( $ck, $r, 'get_relative_webcomics' );
		
		return apply_filters( 'webcomic_get_relative', $r, $id, $taxonomy, $terms );
	}
	
	/**
	 * Retrieves webcomic permalink URL relative to the current webcomic.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $key The releative key ID. Must be one of 'first', 'previous', 'next', 'last', or 'random'.
	 * @param str $taxonomy A taxonomy to limit the returned ID's to.
	 * @param int|arr $terms A term ID or array of term ID's. Required if $taxonomy is not false.
	 * @param int $id Webcomic ID that returned ID's will be relative to.
	 * @param bool $global Uses URL parameters instead of regular permalink if true.
	 * @return The permalink URL, or false on error.
	 */
	function get_relative_webcomic_url( $key, $taxonomy = false, $terms = false, $id = false, $global = false ) {
		if ( !( $ids = $this->get_relative_webcomics( $taxonomy, $terms, $id, $global ) ) || !$ids[ $key ] )
			return false;
		
		global $post;
		
		$end = ( 'random' == $key ) ? $ids[ $key ] : $ids[ $key . '-bookend' ];
		
		if ( $global )
			$url = get_bloginfo( 'url' ) . '?relative_webcomic=' . implode( '/', array( $key, $taxonomy, implode( ',', ( array ) $terms ), $id ) );
		else
			$url = ( !$id && $post->ID == $ids[ $key ] ) ? get_permalink( $end ) : get_permalink( $ids[ $key ] );
		
		return apply_filters( 'webcomic_get_relative_url', $url, $key, $taxonomy, $terms );
	}
	
	/**
	 * Retrieves a properly formatted webcomic link relative to the current webcomic.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $key The releative key ID. Must be one of 'first', 'previous', 'next', 'last', or 'random'.
	 * @param str $format The format of the returned link. Should contain %link token.
	 * @param str $link What to display for the link text. May contain the %label, %title, %date, %thumb-small,-# %thumb-medium-#, %thumb-large-#, and %thumb-full-# tokens.
	 * @param str $taxonomy A taxonomy to limit the returned ID's to.
	 * @param int|arr $terms A term ID or array of term ID's. Required if $taxonomy is not false.
	 * @param int $id Webcomic ID that returned ID's will be relative to.
	 * @param bool $global Uses URL parameters instead of regular permalink if true.
	 * @return Formatted webcomic link, or false on error.
	 */
	function get_relative_webcomic_link( $key, $format, $link, $taxonomy = false, $terms = false, $id = false, $global = false ) {
		if ( !( $ids = $this->get_relative_webcomics( $taxonomy, $terms, $id, $global ) ) || !$ids[ $key ] )
			return false;
		
		global $post;
		
		$this->domain();
		
		switch ( $key ) {
			case 'random'       : $label = '<span>' . __( '&infin; Random &infin;', 'webcomic' ) . '</span>'; break;
			case 'first'        :
			case 'first-bookend': $label = '<span>' . __( '&laquo; First', 'webcomic' ) . '</span>'; break;
			case 'last'         :
			case 'last-bookend' : $label = '<span>' . __( 'Last &raquo;', 'webcomic' ) . '</span>'; break;
			case 'previous'     : $label = '<span>' . __( '&lsaquo; Previous', 'webcomic' ) . '</span>'; break;
			case 'next'         : $label = '<span>' . __( 'Next &rsaquo;', 'webcomic' ) . '</span>'; break;
			default: return false;
		}
		
		$current = ( !$id && !$global && $post->ID == $ids[ $key ] && 'random' != $key ) ? ' current-webcomic' : '';
		$kbd     = ( $this->option( 'shortcut_toggle' ) ) ? ' webcomic-kbd-shortcut' : '';
		$date    = get_the_time( get_option( 'date_format' ), $ids[ $key ] );
		$title   = get_the_title( $ids[ $key ] );
		$match   = false;
		
		if ( preg_match( '/\%thumb-(full|large|medium|small)(?:-(\d+))?/', $link, $match ) )
			$link = preg_replace( "/$match[0]/", $this->get_webcomic_object( $match[ 1 ], 'post', $match[ 2 ], $ids[ $key ] ), $link );
		
		$link   = str_replace( '%label', $label, $link );
		$link   = str_replace( '%title', $title, $link );
		$link   = str_replace( '%date', $date, $link );
		$link   = '<a href="' . get_permalink( $ids[ $key ] ) . '" rel="' . $key . '" class="' . $key . '-webcomic-link ' . $key . '-webcomic-link-' . $ids[ $key ] . $kbd . $current . '">' . $link . '</a>';
		$format = str_replace( '%link', $link, $format );
		
		return apply_filters( 'webcomic_get_relative_link', $format, $link, $key, $taxonomy, $terms );
	}
	
	/**
	 * Retrieves the appropriate URL to the purchase webcomic print template for the specified webcomic.
	 * 
	 * A simple URL parameter redirects to the purchase webcomic print
	 * template for any webcomic post, but this function determines
	 * whether permalinks are being used and appends it to the post
	 * permalink as necessary.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param int $id Webcomic ID to retrieve the purchase print URL for.
	 * @return The purchase URL, or false on error.
	 */
	function get_purchase_webcomic_url( $id = false ) {
		global $wp_rewrite, $post;
		
		if ( ( !$post && !$id ) || !$this->option( 'paypal_business' ) || !$this->verify() )
			return false;
		
		$id  = ( intval( $id ) > 0 ) ? $id : $post->ID;
		$url = get_permalink( $id );
		
		$r = ( is_object( $wp_rewrite ) && $wp_rewrite->using_permalinks() ) ? $url . '?purchase_webcomic_print=' . $id : $url . '&amp;purchase_webcomic_print=' . $id;
		
		return apply_filters( 'webcomic_get_purchase_url', $r, $id );
	}
	
	/**
	 * Retrieves a properly formatted purchase webcomic print link for the specified webcomic.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $format The format of the returned link. Should contain %link token.
	 * @param str $link What to display for the link text. May contain the %label, %title, %date, %thumb-small-#, %thumb-medium-#, %thumb-large-#, and %thumb-full-# tokens.
	 * @param int $id Webcomic ID to retrieve the purchase print URL for.
	 * @return Formatted purchase webcomic link, or false on error.
	 */
	function get_purchase_webcomic_link( $format, $link, $id = false ) {
		global $post;
		
		$this->domain();
		
		if ( !$post && !$id )
			return false;
		
		$id = ( intval( $id ) > 0 ) ? $id : $post->ID;
		
		if ( !( $url = $this->get_purchase_webcomic_url( $id ) ) )
			return false;
		
		$kbd    = ( $this->option( 'shortcut_toggle' ) ) ? ' webcomic-kbd-shortcut' : '';
		$date   = get_the_time( get_option( 'date_format' ), $id );
		$title  = get_the_title( $id );
		$label  = '<span>' . __( 'Purchase Webcomic', 'webcomic' ) . '</span>';
		$closed = ( $this->webcomic_prints_open() ) ? '' : ' purchase-webcomic-link-closed';
		$match  = false;
		
		if ( preg_match( '/\%thumb-(full|large|medium|small)(?:-(\d+))?/', $link, $match ) )
			$link = preg_replace( "/$match[0]/", $this->get_webcomic_object( $match[ 1 ], 'post', $match[ 2 ], $id ), $link );
		
		$link   = str_replace( '%label', $label, $link );
		$link   = str_replace( '%title', $title, $link );
		$link   = str_replace( '%date', $date, $link );
		$link   = '<a href="' . $url . '" rel="purchase" class="purchase-webcomic-link purchase-webcomic-link-' . $id . $kbd . $closed . '">' . $link . '</a>';
		$format = str_replace( '%link', $link, $format );
		
		return apply_filters( 'webcomic_get_purchase_link', $format, $link, $id );
	}
	
	/**
	 * Retrieves the appropriate URL to the purchase webcomic print template for the specified webcomic.
	 * 
	 * A simple URL parameter calls the bookmark webcomic function
	 * for any webcomic post, but this function determines
	 * whether permalinks are being used and appends it to the post
	 * permalink as necessary.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $key The type of bookmark link, one of 'bookmark', 'return', or 'remove'
	 * @param int $id Webcomic ID to retrieve the purchase print URL for.
	 * @return The purchase URL, or false on error.
	 */
	function get_bookmark_webcomic_url( $key, $id = false ) {
		global $wp_rewrite, $post;
		
		if ( ( !$post && !$id ) || !$this->verify() )
			return false;
		
		$id  = ( intval( $id ) > 0 ) ? $id : $post->ID;
		$wc  = current( wp_get_object_terms( $id, 'webcomic_collection' ) );
		$url = get_permalink( $id );
		
		$r = ( is_object( $wp_rewrite ) && $wp_rewrite->using_permalinks() ) ? $url . '?bookmark_webcomic=' . $id . '/' . $key . '/' . $wc->term_id : $url . '&amp;purchase_webcomic_print=' . $id . '/' . $key . '/' . $wc->term_id;
		
		return apply_filters( 'webcomic_get_bookmark_url', $r, $key, $id );
	}
	
	/**
	 * Retrieves a properly formatted bookmark webcomic link for the specified webcomic.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $key The type of bookmark link, one of 'bookmark', 'return', or 'remove'.
	 * @param str $format The format of the returned link. Should contain %link token.
	 * @param str $link What to display for the link text. May contain the %label, %title, %date, %thumb-small-#, %thumb-medium-#, %thumb-large-#, and %thumb-full-# tokens.
	 * @param int $id Webcomic ID to retrieve the bookmark URL for.
	 * @return Formatted bookmark webcomic link, or false on error.
	 */
	function get_bookmark_webcomic_link( $key, $format, $link, $id = false ) {
		global $wp_rewrite, $post;
		
		$wc = current( wp_get_object_terms( $post->ID, 'webcomic_collection' ) );
		
		if ( ( !$post && !$id ) || empty( $wc ) || is_wp_error( $wc ) || !is_singular() || 'webcomic_post' != $post->post_type )
			return false;
		
		$this->domain();
		
		$id = ( intval( $id ) > 0 ) ? $id : $post->ID;
		
		if ( !( $url = $this->get_bookmark_webcomic_url( $key, $id ) ) )
			return false;
		
		switch ( $key ) {
			case 'bookmark': $label = '<span>' . __( 'Bookmark', 'webcomic' ) . '</span>'; break;
			case 'return'  : $label = '<span>' . __( 'Return', 'webcomic' ) . '</span>'; break;
			case 'remove'  : $label = '<span>' . __( 'Remove', 'webcomic' ) . '</span>'; break;
			default: return false;
		}
		
		$kbd   = ( $this->option( 'shortcut_toggle' ) ) ? ' webcomic-kbd-shortcut' : '';
		$date  = get_the_time( get_option( 'date_format' ), $post->ID );
		$title = get_the_title( $post->ID );
		$match = false;
		
		if ( preg_match( '/\%thumb-(full|large|medium|small)(?:-(\d+))?/', $link, $match ) )
			$link = preg_replace( "/$match[0]/", $this->get_webcomic_object( $match[ 1 ], 'post', $match[ 2 ], $id ), $link );
		
		$link   = str_replace( '%label', $label, $link );
		$link   = str_replace( '%title', $title, $link );
		$link   = str_replace( '%date', $date, $link );
		$link   = '<a href="' . $url . '" rel="webcomic_collection-' . $wc->term_id . '" class="' . $key . '-webcomic-link ' . $key . '-webcomic-link-' . $id . $kbd . '">' . $link . '</a>';
		$format = str_replace( '%link', $link, $format );
		
		return apply_filters( 'webcomic_get_bookmark_link', $format, $link, $key );
	}
	
	// Related
	
	/**
	 * Retrieves webcomic ID's related to the specified post by storylines and/or characters.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param bool $storylines Match based on storylines.
	 * @param bool $characters Match based on characters.
	 * @param int $id Webcomic ID to reference.
	 * @return arr Related Webcomic ID's, or false on error.
	 */
	function get_related_webcomics( $storylines = true, $characters = true, $id = false ) {
		global $post;
		
		if ( ( !$post && !$id ) || !$this->verify() )
			return false;
		
		$id = ( intval( $id ) > 0 ) ? $id : $post->ID;
		$storylines = ( $storylines ) ? wp_get_object_terms( $id, 'webcomic_storyline', array( 'fields' => 'ids' ) ) : array();
		$characters = ( $characters ) ? wp_get_object_terms( $id, 'webcomic_character', array( 'fields' => 'ids' ) ) : array();
		
		if ( !empty( $storylines ) && !is_wp_error( $storylines ) )
			$story_posts = get_objects_in_term( $storylines[ array_rand( $storylines ) ], 'webcomic_storyline' );
		
		if ( !empty( $characters ) && !is_wp_error( $characters ) )
			$character_posts = get_objects_in_term( $characters[ array_rand( $characters ) ], 'webcomic_character' );
		
		if ( $story_posts && $character_posts )
			$posts = array_intersect( $story_posts, $character_posts );
		elseif ( !$story_posts && !$character_posts )
			return false;
		else
			$posts = ( !empty( $story_posts ) ) ? $story_posts : $character_posts;
		
		if ( empty( $posts ) || is_wp_error( $posts ) )
			return false;
		
		if ( false !== ( $key = array_search( $id, $posts ) ) )
			unset( $posts[ $key ] );
		
		return apply_filters( 'webcomic_get_related', $posts, $storylines, $characters, $id );
	}
	
	/**
	 * Retrieves a formatted list of related webcomics.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param arr|str $args An array or string of arguments (see below for full details).
	 * @return Formatted list of related webcomics, or false on error.
	 */
	function get_the_related_webcomics( $args = false ) {
		$defaults = array(
			'format'     => false, //str Format to display the terms in. Must be one of 'ulist', 'olist', or 'dropdown'
			'separator'  => false, //str Text string to place between posts when 'format' is false.
			'before'     => false, //str Text string to place before the generated output.
			'after'      => false, //str Text string to place after the generated output.
			'number'     => 5,     //int The number of related posts to return.
			'order'      => 'ASC', //str The order to return posts in, either ASC (oldest first) or DESC (newest first). Sorts by post ID.
			'image'      => false, //str Displays images for post links instead of titles if non-false and 'format' is not 'dropdown'. If set, should be one of 'full', 'large', 'medium', or 'small'.
			'imagekey'   => false, //int The file index to use when 'image' is non-false. If specified, only that part of the image object will be returned.
			'label'      => false, //str Text string to display for the first null option when 'format' is 'dropdown'.
			'storylines' => true,  //bool Match posts based on storylines
			'characters' => true,  //bool Match posts based on characters
			'id'         => false //int ID of the object to retrieve related posts for.
		); $args = wp_parse_args( $args, $defaults ); extract( $args );
		
		if ( !( $posts = $this->get_related_webcomics( $storylines, $characters, $id ) ) )
			return false;
		
		if ( 'DESC' == $order )
			$posts = array_reverse( $posts );
		
		if ( $number > 0 )
			$posts = array_slice( $posts, 0, $number );
		
		if ( $separator )
			$format = false;
		elseif ( !$format )
			$format = 'ulist';
		
		foreach ( $posts as $p ) {
			$t = ( 'dropdown' != $format && ( 'full' == $image || 'large' == $image || 'medium' == $image || 'small' == $image ) ) ? $this->get_webcomic_object( $image, 'post', $imagekey, $p ) : get_the_title( $p );
			$u = get_permalink( $p );
			
			if ( 'ulist' == $format || 'olist' == $format ) {
				$s1 = '<li class="related-webcomic-posts">';
				$s2 = '</li>';
			} elseif ( 'dropdown' == $format ) {
				$s1 = '<option value="' . $u . '">';
				$s2 = '</option>';
			} else {
				$s1 = false;
				$s2 = $separator;
			}
			
			$l[] = $s1 . '<a href="' . $u . '" rel="related">' . $t . '</a>' . $s2;
		}
		
		if ( 'ulist' == $format ) {
			$p1 = '<ul class="related_webcomic_posts">';
			$p2 = '</ul>';
		} elseif ( 'olist' == $format ) {
			$p1 = '<ol class="related_webcomic_posts">';
			$p2 = '</ol>';
		} elseif ( 'dropdown' == $format ) {
			$p1 = '<select name="related_webcomic_posts" class="related-webcomic-posts"><option>' . $label . '</option>';
			$p2 = '</select>';
		}
		
		$l = implode( '', $l );
		$l = ( $separator ) ? substr( $l, 0, strrpos( $l, $separator ) ) : $l;
		
		$r = $before . $p1 . $l . $p2 . $after;
		
		return apply_filters( 'webcomic_get_the_related', $r, $args );
	}
	
	// Buffers
	
	/**
	 * Retrieves webcomic ID's for webcomics schedules to publish in the future.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param arr|int $terms An array of term ID's to limit the search for buffer webcomics by.
	 * @param str $taxonomy The taxonomy the term ID's belong to. Required.
	 * @return An array of webcomic ID's, or false on error.
	 */
	function get_buffer_webcomics( $terms = false, $taxonomy = false ) {
		if ( !$this->verify() )
			return false;
		
		$ck = 'webcomic_buffer_' . hash( 'md5', implode( ( array ) $terms, '' ) . $taxonomy );
		
		if ( $r = wp_cache_get( $ck, 'get_buffer_webcomics' ) )
			return $r;
		
		global $wpdb;
		
		$r = array();
		
		if ( $terms && $taxonomy && ( 'webcomic_collection' == $taxonomy || 'webcomic_storyline' == $taxonomy || 'webcomic_character' == $taxonomy ) ) {
			$terms = ( array ) $terms;
			
			foreach ( $terms as $k => $v )
				$terms[ $k ] = ( int ) $v;
		}
		
		$join  = ( $terms ) ? "INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = '$taxonomy' AND tt.term_id IN ( '" . implode( "', '", $terms ) . "' )" : "";
		$query = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts AS p $join WHERE p.post_type = 'webcomic_post' AND p.post_status = 'future' ORDER BY p.post_date DESC" ) );
		
		if ( empty( $query ) )
			return false;
		
		wp_cache_add( $ck, $query, 'get_buffer_webcomics' );
		
		return apply_filters( 'webcomic_get_buffer', $query, $term, $taxonomy );
	}
	
	/**
	 * Retrieves a formatted list of buffer webcomics.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param arr|str $args An array or string of arguments (see below for full details).
	 * @return Formatted list of related webcomics, or false on error.
	 */
	function get_the_buffer_webcomics( $args = false ) {
		$defaults = array(
			'format'     => false,  //str Format to display the terms in. Must be one of 'ulist' or 'olist'
			'separator'  => false,  //str Text string to place between posts when 'format' is false.
			'before'     => false,  //str Text string to place before the generated output.
			'after'      => false,  //str Text string to place after the generated output.
			'number'     => 1,      //int The number of buffer posts to return.
			'order'      => 'DESC', //str The order to return posts in, either ASC (oldest first) or DESC (newest first). Sorts by post date.
			'image'      => false,  //str Displays images for post links instead of titles if non-false. If set, should be one of 'full', 'large', 'medium', or 'small'.
			'imagekey'   => false,  //int The file index to use when 'image' is non-false. If specified, only that part of the image object will be returned.
			'terms'      => false,  //arr An array of term ID's to limit the search for buffer webcomics to.
			'taxonomy'   => false   //str The taxonomy 'terms' belong to. Required if 'terms' is non-false.
		); $args = wp_parse_args( $args, $defaults ); extract( $args );
		
		if ( !( $posts = $this->get_buffer_webcomics( $terms, $taxonomy ) ) )
			return false;
		
		if ( 'ASC' == $order )
			$posts = array_reverse( $posts );
		
		if ( $number > 0 )
			$posts = array_slice( $posts, 0, $number );
		
		if ( $separator )
			$format = false;
		elseif ( !$format )
			$format = 'ulist';
		
		foreach ( $posts as $p ) {
			$t = ( 'full' == $image || 'large' == $image || 'medium' == $image || 'small' == $image ) ? preg_replace( '/title="*."/', 'title="' . the_title_attribute( 'echo=0' ) . ' (' . get_the_time( get_option( 'date_format' ), $p ) . ' @ ' . get_the_time( get_option( 'time_format' ), $p ) . ')"', $this->get_webcomic_object( $image, 'post', $imagekey, $p ) ) : '<span title="' . get_the_time( get_option( 'date_format' ), $p ) . ' @ ' . get_the_time( get_option( 'time_format' ), $p ) . '">' . get_the_title( $p ) . '</span>';
			
			if ( 'ulist' == $format || 'olist' == $format ) {
				$s1 = '<li class="buffer-webcomic-posts">';
				$s2 = '</li>';
			} else {
				$s1 = false;
				$s2 = $separator;
			}
			
			$l[] = $s1 . $t . $s2;
		}
		
		if ( 'ulist' == $format ) {
			$p1 = '<ul class="buffer_webcomic_posts">';
			$p2 = '</ul>';
		} elseif ( 'olist' == $format ) {
			$p1 = '<ol class="buffer_webcomic_posts">';
			$p2 = '</ol>';
		}
		
		$l = implode( '', $l );
		$l = ( $separator ) ? substr( $l, 0, strrpos( $l, $separator ) ) : $l;
		
		$r = $before . $p1 . $l . $p2 . $after;
		
		return apply_filters( 'webcomic_get_the_buffer', $r, $args );
	}
	
	// Verification
	
	/**
	 * Retrieves a fully formatted age verification form.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $label String label to use for the submit button.
	 * @return Formatted HTML form, or false on error.
	 */
	function get_webcomic_verify_form( $label = false ) {
		global $post;
		
		$this->domain();
		
		$i = 1;
		$day = $year = false;
		
		while ( $i < 32 ) {
			$day .= '<option value="' . $i . '">' . $i . '</option>';
			$i++;
		} $i = $x = intval( date( 'Y' ) );
		
		while ( $i >= $x - 80 ) {
			$year .= '<option value="' . $i . '">' . $i . '</option>';
			$i--;
		} unset( $i, $x );
		
		$r = '<form action="" method="post">' . wp_nonce_field( 'verify_webcomic_age', '_wpnonce', true, false ) . '
				<select name="webcomic_birth_day">' . $day . '</select>
				<select name="webcomic_birth_month">
					<option value="1">' . __( 'January', 'webcomic' ) . '</option>
					<option value="2">' . __( 'February', 'webcomic' ) . '</option>
					<option value="3">' . __( 'March', 'webcomic' ) . '</option>
					<option value="4">' . __( 'April', 'webcomic' ) . '</option>
					<option value="5">' . __( 'May', 'webcomic' ) . '</option>
					<option value="6">' . __( 'June', 'webcomic' ) . '</option>
					<option value="7">' . __( 'July', 'webcomic' ) . '</option>
					<option value="8">' . __( 'August', 'webcomic' ) . '</option>
					<option value="9">' . __( 'September', 'webcomic' ) . '</option>
					<option value="10">' . __( 'October', 'webcomic' ) . '</option>
					<option value="11">' . __( 'November', 'webcomic' ) . '</option>
					<option value="12">' . __( 'December', 'webcomic' ) . '</option>
				</select>
				<select name="webcomic_birth_year">' . $year . '</select>
				<input type="hidden" name="action" value="verify_webcomic_age">
				<input type="hidden" name="redirect" value="' . get_permalink( $post->ID ) . '">
				<input type="submit" value="' . ( ( $label ) ? $label : __( 'Verify Age', 'webcomic' ) ) . '">
			</form>
		';
		
		return apply_filters( 'webcomic_get_verify_form', $r, $label );
	}
	
	/**
	 * Retrieves the minimum age required to access the specified content.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param
	 */
	function get_webcomic_verify_age( $id = false ) {
		global $post;
		
		$id = ( intval( $id ) > 0 ) ? $id : $post->ID;
		$wc = current( wp_get_object_terms( $id, 'webcomic_collection' ) );
		
		if ( empty( $wc ) || is_wp_error( $wc ) )
			return false;
		
		return apply_filters( 'webcomic_get_verify_age', $wc->term_group, $id );
	}
	
	// Donations
	
	/**
	 * Retrieves a formatted donation amount for display.
	 * 
	 * This function formats the donation amount for display purposes.
	 * It should not be used where precise values are required.
	 * 
	 * @param str $dec The character to use as a decimal. Defaults to '.'.
	 * @param str $sep The character to use to separate thousands. Defaults to ','.
	 * @return Formatted donation amount string, or false on error.
	 */
	function get_the_webcomic_donation_amount( $dec = false, $sep = false ) {
		if ( !( $cost = $this->option( 'paypal_donation' ) ) )
			return false;
		
		$dec = ( $dec ) ? $dec : '.';
		$sep = ( $sep ) ? $decimal : ',';
		
		$r = number_format( $cost, 2, $dec, $sep ) . ' ' . $this->option( 'paypal_currency' );
		
		return apply_filters( 'webcomic_get_the_donation_amount', $r, $dec, $sep );
	}
	
	/**
	 * Retrieves necessary form fields for donations.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @return Necessary hidden form fields, or false on error.
	 */
	function get_webcomic_donation_fields() {
		if ( !$this->option( 'paypal_business' ) )
			return false;
		
		$r = '
		<input type="hidden" name="cmd" value="_donations">
		<input type="hidden" name="bn" value="Webcomic_Donate_WPS_US">' . ( ( $this->option( 'paypal_donation' ) ) ? '<input type="hidden" name="amount" value="' . $this->option( 'paypal_donation' ) . '">' : '' ) . '
		<input type="hidden" name="item_name" value="' . substr( get_bloginfo( 'name' ), 0, 127 ) . '">
		<input type="hidden" name="currency_code" value="' . $this->option( 'paypal_currency' ) . '">
		<input type="hidden" name="business" value="' . $this->option( 'paypal_business' ) . '">
		<input type="hidden" name="notify_url" value="' . trailingslashit( get_bloginfo( 'url' ) ) . '?webcomic_paypal_ipn=donation">
		';
		
		return apply_filters( 'webcomic_get_donation_fields', $r );
	}
	
	/**
	 * Retrieves a complete webcomic donation form.
	 * For testing, use https://www.sandbox.paypal.com/cgi-bin/webscr
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $label The label to display for the submit button. Defaults to 'Buy Now' for single-item method and 'Add to Cart' for shopping cart method.
	 * @param int $id Webcomic ID to reference for form data.
	 * @return The complete HTML form, or false on error.
	 */
	function get_webcomic_donation_form( $label = false ) {
		if ( !$this->option( 'paypal_business' ) )
			return false;
		
		$label = ( $label ) ? $label : __( 'Donate', 'webcomic' );
		$r = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" class="webcomic-donation-form"><input type="submit" value="' . $label . '">' . $this->get_webcomic_donation_fields() . '</form>';
		
		return apply_filters( 'webcomic_get_donation_form', $r, $label );
	}
	
	// Prints
	
	/**
	 * Checks to see if prints are being sold for the specified post.
	 * 
	 * @packgae webcomic
	 * @since 3
	 * 
	 * @param int $id Webcomic ID to check print status for.
	 * @param bool $original If true, checks to see if the original is still for sale.
	 * @return True if prints are being sold, false otherwise.
	 */
	function webcomic_prints_open( $id = false, $original = false ) {
		global $post;
		
		if ( !$post && !$id )
			return false;
		
		$id = ( intval( $id ) > 0 ) ? $id : $post->ID;
		$post_meta = current( get_post_meta( $id, 'webcomic' ) );
		
		if ( $original )
			return $post_meta[ 'paypal' ][ 'original' ];
		else
			return $post_meta[ 'paypal' ][ 'prints' ];
	}
	
	/**
	 * Retrieves the specified cost value for a webcomic print.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $cost The value to return. Must be one of 'price', 'collection-price', 'base-price', 'shipping', 'collection-shipping', or 'base-shipping'.
	 * @param str $type The type of value to return. Must be one of 'domestic', 'international', or 'original'.
	 * @param int $id Webcomic ID to retrieve cost for.
	 * @return float The specified cost, or false on error.
	 */
	function get_purchase_webcomic_cost( $cost, $type, $id = false ) {
		global $post;
		
		if ( !$post && !$id )
			return false;
		
		$x    = ( 'domestic' == $type ) ? 'd' : 'i';
		$x    = ( 'original' == $type ) ? 'o' : $x;
		$id   = ( intval( $id ) > 0 ) ? $id : $post->ID;
		$t    = end( explode( '-', $cost ) );
		$wc   = current( wp_get_object_terms( $id, 'webcomic_collection' ) );
		$base = $this->option( 'paypal_' . $t . '_' . $x );
		
		if ( empty( $wc ) || is_wp_error( $wc ) )
			return false;
		
		$post_meta = current( get_post_meta( $id, 'webcomic' ) );
		$amount    = array( $wc->webcomic_paypal[ $t . '_' . $x ], $post_meta[ 'paypal' ][ $t . '_' . $x ] );
		
		if ( 'base-price' == $cost || 'base-shipping' == $cost || 'collection-price' == $cost || 'collection-shipping' == $cost )
			unset( $amount[ 1 ] );
		
		if ( 'base-price' == $cost || 'base-shipping' == $cost )
			unset( $amount[ 0 ] );
		
		$r = $this->price( $base, $amount );
		
		return apply_filters( 'webcomic_get_purchase_cost', $r, $cost, $type, $id );
	}
	
	/**
	 * Retrieves a formatted cost value for display.
	 * 
	 * This function formats the cost value of a webcomic print
	 * for display purposes. It should not be used where precise
	 * values are required.
	 * 
	 * @param str $cost The value to return. Must be one of 'price', 'collection-price', 'base-price', 'shipping', 'collection-shipping', or 'base-shipping'.
	 * @param str $type The type of value to return. Must be one of 'domestic', 'international', or 'original'.
	 * @param str $dec The character to use as a decimal. Defaults to '.'.
	 * @param str $sep The character to use to separate thousands. Defaults to ','.
	 * @param int $id Webcomic ID to retrieve cost for.
	 * @return Formatted cost string, or false on error.
	 */
	function get_the_purchase_webcomic_cost( $cost, $type, $dec = false, $sep = false, $id = false ) {
		if ( !is_float( $cost = $this->get_purchase_webcomic_cost( $cost, $type, $id ) ) )
			return false;
		
		$dec = ( $dec ) ? $dec : '.';
		$sep = ( $sep ) ? $sep : ',';
		
		$r = number_format( $cost, 2, $dec, $sep ) . ' ' . $this->option( 'paypal_currency' );
		
		return apply_filters( 'webcomic_get_the_purchase_cost', $r, $cost, $type, $dec, $sep, $id );
	}
	
	/**
	 * Returns information about the specified adjustment.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $adjustment The adjustment to return, one of 'collection-price', 'collection-shipping', 'post-price', or 'post-shipping'.
	 * @param str $type The type of value to return. Must be one of 'domestic', 'international', or 'original'.
	 * @return arr Array containing adjustment information, or false on error.
	 */
	function get_purchase_webcomic_adjustment( $adjustment, $type, $id = false ) {
		global $post;
		
		if ( !$post && !$id )
			return false;
		
		$id = ( intval( $id ) > 0 ) ? $id : $post->ID;
		
		if ( false !== strpos( $adjustment, 'post' ) ) {
			$post_meta = current( get_post_meta( $id, 'webcomic' ) );
			$paypal    = $post_meta[ 'paypal' ];
		} else {
			$wc     = current( wp_get_object_terms( $id, 'webcomic_collection' ) );
			$paypal = $wc->webcomic_paypal;
		}
		
		if ( !$paypal )
			return false;
		
		$r = array();
		$x = ( 'domestic' == $type ) ? 'd' : 'i';
		$x = ( 'original' == $type ) ? 'o' : $x;
		$t = end( explode( '-', $adjustment ) );
		
		$r = $paypal[ $t . '_' . $x ];
		
		if ( !$r[ 'amount' ] )
			return false;
		
		return apply_filters( 'webcomic_get_purchase_adjustment', $r, $adjustment, $type, $id );
	}
	
	/**
	 * Retrieves a formatted adjustment value for display.
	 * 
	 * This function formats the adjustment value of a webcomic print
	 * for display purposes. It should not be used where precise
	 * values are required.
	 * 
	 * @param str $adjustment The adjustment to return, one of 'collection-price', 'collection-shipping', 'post-price', or 'post-shipping'.
	 * @param str $type The type of value to return. Must be one of 'domestic', 'international', or 'original'.
	 * @param str $dec The character to use as a decimal. Defaults to '.'.
	 * @param str $sep The character to use to separate thousands. Defaults to ','.
	 * @param int $id Webcomic ID to retrieve cost for.
	 * @return Formatted adjustment string, or false on error.
	 */
	function get_the_purchase_webcomic_adjustment( $adjustment, $type, $dec = false, $sep = false, $id = false ) {
		if ( !( $a = $this->get_purchase_webcomic_adjustment( $adjustment, $type, $id ) ) )
			return false;
		
		$dec = ( $dec ) ? $dec : '.';
		$sep = ( $sep ) ? $sep : ',';
		$adj = ( $a > 0 ) ? '+ ' : '- ';
		
		$r = $adj . number_format( $a, 0, $dec, $sep ) . '%';
		
		return apply_filters( 'webcomic_get_the_purchase_adjustment', $r, $cost, $type, $dec, $sep, $id );
	}
	
	/**
	 * Retrieves necessary form fields for the specified type of print form.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $type The type of form, one of 'domestic', 'international', or 'original'.
	 * @param bool $cart If true, displays the cart (does not add item) when paypal_method is _cart.
	 * @param int $id Webcomic ID to reference for form data.
	 * @return Necessary hidden form fields, or false on error.
	 */
	function get_purchase_webcomic_fields( $type, $cart = false, $id = false ) {
		global $post;
		
		if ( ( !$post && !$id ) || !$this->option( 'paypal_business' ) || !$this->verify() )
			return false;
		
		$id = ( intval( $id ) > 0 ) ? $id : $post->ID;
		
		if ( !$this->webcomic_prints_open( $id ) || ( 'original' == $type && !$this->webcomic_prints_open( $id, true ) ) )
			return false;
		
		$bn     = ( '_cart' == $this->option( 'paypal_method' ) && 'original' != $type ) ? 'ShoppingCart' : 'BuyNow';
		$cart   = ( $cart ) ? '<input type="hidden" name="display" value="1">' : '<input type="hidden" name="add" value="1">';
		$method = ( 'original' == $type ) ? '_xclick' : $this->option( 'paypal_method' );
		
		$r = '
		<input type="hidden" name="cmd" value="' . $method . '">
		<input type="hidden" name="bn" value="Webcomic_' . $bn . '_WPS_US">
		<input type="hidden" name="amount" value="' . $this->get_purchase_webcomic_cost( 'price', $type, $id ) . '">
		<input type="hidden" name="item_name" value="' . substr( ucfirst( $type ), 0, 1 ) . ' ' . substr( get_the_title( $id ), 0, 125 ) . '">
		<input type="hidden" name="item_number" value="' . $id . '">
		<input type="hidden" name="shipping" value="' . $this->get_purchase_webcomic_cost( 'shipping', $type, $id ) . '">
		<input type="hidden" name="currency_code" value="' . $this->option( 'paypal_currency' ) . '">
		<input type="hidden" name="business" value="' . $this->option( 'paypal_business' ) . '">
		<input type="hidden" name="notify_url" value="' . trailingslashit( get_bloginfo( 'url' ) ) . '?webcomic_paypal_ipn=' . $type . '">
		';
		
		if ( 'original' != $type )
			$r .= '<input type="hidden" name="undefined_quantity" value="1">';
		
		if ( '_cart' == $method )
			$r .= '<input type="hidden" name="shopping_url" value="' . get_permalink( $id ) . '">' . $cart;
		
		return apply_filters( 'webcomic_get_purchase_fields', $r, $type, $id );
	}
	
	/**
	 * Retrieves a complete purchase webcomic form of the specified type.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $type The type of form, one of 'domestic', 'international', or 'original'.
	 * @param str $label The label to display for the submit button. Defaults to 'Buy Now' for single-item method and 'Add to Cart' for shopping cart method.
	 * @param bool $cart Displays the cart contents for shopping cart method when true.
	 * @param int $id Webcomic ID to reference for form data.
	 * @return The complete HTML form, or false on error.
	 */
	function get_purchase_webcomic_form( $type, $label = false, $cart = false, $id = false ) {
		global $post;
		
		if ( ( !$post && !$id ) || !$this->option( 'paypal_business' ) || !( $fields = $this->get_purchase_webcomic_fields( $type, $id ) ) )
			return false;
		
		$id = ( intval( $id ) > 0 ) ? $id : $post->ID;
		
		if ( !$label && '_cart' == $this->option( 'paypal_method' ) && $cart && 'original' != $type )
			$label = __( 'View Cart', 'webcomic' );
		elseif ( !$label && '_cart' == $this->option( 'paypal_method' ) && 'original' != $type )
			$label = __( 'Add to Cart', 'webcomic' );
		elseif ( !$label )
			$label = __( 'Buy Now', 'webcomic' );
		
		$r = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" class="webcomic-purchase-form webcomic-purchase-form-' . $type . '"><input type="submit" value="' . $label . '">' . $fields . '</form>'; //https://www.sandbox.paypal.com/cgi-bin/webscr
		
		return apply_filters( 'webcomic_get_purchase_form', $r, $type, $label, $id );
	}
	
	// Transcripts
	
	/**
	 * Includes the transcripts template.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $file The filename to retrieve.
	 * @return False on error.
	 */
	function webcomic_transcripts_template( $file = false ) {
		global $with_webcomic_transcripts, $webcomic_transcript, $post, $wpdb, $user_login, $user_ID, $user_identity;
		
		if ( !( is_single() || is_page() || $with_webcomic_transcripts ) || !$post || !$this->verify() )
			return false;
		
		$file = ( $file ) ? $file : 'webcomic_transcripts.php';
		$form = locate_template( array( $file ) ) ? locate_template( array( $file ) ) : $this->dir . 'webcomic-includes/template-transcripts.php';
		
		require_once( $form );
	}
	
	/**
	 * Checks to see if any published transcripts exist.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $status The status to check for. Must be one of 'draft', 'pending', or 'publish'.
	 * @return True or the number of transcripts, false otherwise.
	 */
	function have_webcomic_transcripts( $status = false ) {
		global $post;
		
		if ( !$post )
			return false;
		
		$post_meta = current( get_post_meta( $post->ID, 'webcomic' ) );
		
		$x = 0;
		$r = ( empty( $post_meta[ 'transcripts' ] ) ) ? false : true;
		$s = ( $status ) ? $status : 'publish';
		
		if ( $r ) {
			foreach ( $post_meta[ 'transcripts' ] as $t )
				if ( $s == $t[ 'status' ] )
					$x++;
			
			$r = ( $x ) ? $x : false;
		}
		
		return $r;
	}
	
	/**
	 * Checks to see if transcribing is allowed for the specified post.
	 * 
	 * @packgae webcomic
	 * @since 3
	 * 
	 * @return True if transcribing is allowed, false otherwise.
	 */
	function webcomic_transcripts_open() {
		global $post;
		
		if ( !$post )
			return false;
		
		$i = 0;
		$post_meta = current( get_post_meta( $post->ID, 'webcomic' ) );
		$languages = $this->option( 'transcribe_language' );
		
		foreach ( $languages as $k => $v )
			if ( empty( $post_meta[ 'transcripts' ][ $k ][ 'status' ] ) || ( 'publish' != $post_meta[ 'transcripts' ][ $k ][ 'status' ] && 'draft' != $post_meta[ 'transcripts' ][ $k ][ 'status' ] ) )
				$i++;
		
		return ( $i && $post_meta[ 'transcribe_toggle' ] );
	}
	
	/**
	 * Displays a properly formatted transcribing form.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param arr|str $args Arguments for the generated form (see below).
	 * @param int $id Webcomic ID to display transcribe form for.
	 * @return False if no Webcomic ID can be found.
	 */
	function webcomic_transcribe_form( $args = false ) {
		if ( !$this->webcomic_transcripts_open() ) {
			do_action( 'webcomic_transcribe_form_closed' );
			return false;
		}
		
		global $user_identity, $post, $id;
		
		if ( !$post && !$id )
			return false;
		
		$id = ( intval( $id ) > 0 ) ? $id : $post->ID;
		
		$transcriber = wp_get_current_commenter();
	
		$req = ( 'selfid' == $this->option( 'transcribe_restrict' ) );
		
		$defaults = array(
			'fields' => apply_filters(
				'webcomic_transcribe_default_fields',
				array(
					'author' => '
						<p class="webcomic-transcribe-auther">
							<label for="webcomic_transcript_author">' . __( 'Name', 'webcomic' ) . '</label> ' .
							( $req ? '<span class="required">*</span>' : '' ) .
							'<input type="text" name="webcomic_transcript_author" value="' . esc_attr( $transcriber[ 'comment_author' ] ) . '" id="webcomic_transcript_author">
						</p>',
					'email' => '
						<p class="webcomic-transcribe-email">' .
							'<label for="webcomic_transcript_email">' . __( 'Email', 'webcomic' ) . '</label> ' .
							( $req ? '<span class="required">*</span>' : '' ) .
							'<input type="text" name="webcomic_transcript_author" value="' . esc_attr(  $transcriber['comment_author_email'] ) . '" id="webcomic_transcript_email">
						</p>'
				), $args, $req
			),
			'language' => '
				<p class="webcomic-transcribe-language">
					<label for="webcomic_transcript_language">' . __( 'Language', 'webcomic' ) . '</label>' .
					$this->get_webcomic_transcribe_form_languages() .
				'</p>',
			'transcript' => '
				<p class="webcomic-transcribe-text">
					<label for="webcomic_transcript_text">' . __( 'Transcript', 'webcomic' ) . '</label>
					<textarea name="webcomic_transcript_text" id="webcomic_transcript_text" cols="45" rows="10"></textarea>
				</p>',
			'submit' => '
				<p class="webcomic-transcript-submit">
					<input type="submit" value="' . __( 'Transcribe', 'webcomic' ) . '">' .
					$this->get_webcomic_transcribe_form_fields() .
				'</p>',
			'transcribe_form_title'   => '<h2>' . __( 'Transcribe', 'webcomic' ) . '</h2>',
			'transcript_submitted'    => '<p class="webcomic-transcript-submitted">' . __( 'Thanks! Your transcript has been submitted for approval.', 'webcomic' ) . '</p>',
			'must_log_in'             => '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to transcribe.', 'webcomic' ), wp_login_url( get_permalink( $id ) ) ) . '</p>',
			'logged_in_as'            => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%s">%s</a>. <a href="%s" title="Log out of this account">Log out?</a></p>', 'webcomic' ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( get_permalink( $id ) ) ),
			'transcribe_notes_before' => '<p class="transcribe-before">' . __( 'Your email is <em>never</em> published nor shared.', 'webcomic' ) . ( $req ? __( ' Required fields are marked <span class="required">*</span>', 'webcomic' ) : '' ) . '</p>',
			'transcribe_notes_after'  => '<p class="form-allowed-tags">' . __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: ', 'webcomic' ) . '<code>' . allowed_tags() . '</code></p>'
		); $args = wp_parse_args( $args, apply_filters( 'webcomic_transcribe_defaults', $defaults, $req ) ); extract( $args );
		
		if ( 'login' == $this->option( 'transcribe_restrict' ) && !is_user_logged_in() ) {
			echo apply_filters( 'webcomic_transcribe_form_must_log_in', $must_log_in );
			
			do_action( 'webcomic_transcribe_form_must_log_in_after' );
		} else {
			do_action( 'webcomic_transcribe_form_before' );
		?>
			<form action="" method="post" id="transcribeform">
			<?php
				echo apply_filters( 'webcomic_transcribe_form_title', $transcribe_form_title );
				do_action( 'webcomic_transcribe_form_top' );
				
				if ( !empty( $_REQUEST[ 'webcomic_transcript_status' ] ) )
					echo apply_filters( 'webcomic_transcribe_form_submitted', $transcript_submitted );
				
				if ( is_user_logged_in() ) {
					echo apply_filters( 'webcomic_transcribe_form_logged_in_as', $logged_in_as, $transcriber, $user_identity );
					do_action( 'webcomic_transcribe_form_logged_in_as_after', $transcriber, $user_identity );
				} else {
					echo apply_filters( 'webcomic_transcribe_form_notes_before', $transcribe_notes_before );
					
					do_action( 'webcomic_transcribe_form_fields_before' );
					
					foreach ( ( array ) $fields as $name => $field )
						echo apply_filters( "webcomic_transcribe_form_field_{$name}", $field );
					
					do_action( 'webcomic_transcribe_form_fields_after' );
				}
					
				echo apply_filters( 'webcomic_transcribe_form_field_language', $language );
				echo apply_filters( 'webcomic_transcribe_form_field_transcript', $transcript );
				echo apply_filters( 'webcomic_transcribe_form_notes_after', $transcribe_notes_after );
				echo apply_filters( 'webcomic_transcribe_form_field_submit', $submit );
				
				do_action( 'webcomic_transcribe_form', $id );
			?>
			</form>
		<?php
		}
		
		do_action( 'webcomic_transcribe_form_after' );
	}
	
	/**
	 * Returns necessary transcribe form fields.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function get_webcomic_transcribe_form_fields() {
		global $current_user, $post;
		
		if ( !$post )
			return false;
		
		$r = wp_nonce_field( 'submit_webcomic_transcript', '_wpnonce', true, false ) . '
			<input type="text" name="' . hash( 'md5', 'webcomic_transcript_' . $post->ID ) . '" style="display:none">
			<input type="hidden" name="action" value="submit_webcomic_transcript">
			<input type="hidden" name="webcomic_transcript_post" value="' . $post->ID . '">
			<input type="hidden" name="webcomic_ajax" value="0">
		';
		
		if ( is_user_logged_in() ) $r .= '
			<input type="hidden" name="webcomic_transcript_author" value="' . $current_user->display_name . '">
			<input type="hidden" name="webcomic_transcript_email" value="' . $current_user->user_email . '">
		';
		
		return apply_filters( 'webcomic_get_transcribe_form_fields', $r );
	}
	
	/**
	 * Returns language select field for transcribe form.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function get_webcomic_transcribe_form_languages() {
		global $post;
		
		$post_meta = current( get_post_meta( $post->ID, 'webcomic' ) );
		$default   = $this->option( 'transcribe_default' );
		$languages = $this->option( 'transcribe_language' );
		
		$r = '<select name="webcomic_transcript_language" id="webcomic_transcript_language">';
		
		foreach ( $languages as $k => $v ) {
			if ( isset( $post_meta[ 'transcripts' ][ $k ][ 'status' ] ) && ( 'publish' == $post_meta[ 'transcripts' ][ $k ][ 'status' ] || 'draft' == $post_meta[ 'transcripts' ][ $k ][ 'status' ] ) ) {
				unset( $languages[ $k ] );
				continue;
			}
			
			$r .= '<option value="' . $k . '"' . ( isset( $default[ $k ] ) ? ' selected' : '' ) . '>' . $v . '</option>';
		}
		
		$r .= '</select>';
		
		return apply_filters( 'webcomic_get_transcribe_form_languages', $r, $languages, $default );
	}
	
	/**
	 * Displays the transcripts associated with a post.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param arr|str $args An array or string of arguments (see below for full details).
	 * @return False if no webcomic ID can be found.
	 */
	function list_webcomic_transcripts( $args = array() ) {
		global $post, $in_webcomic_transcript_loop;
		
		if ( !$post )
			return false;
		
		$in_webcomic_transcript_loop = true;
		
		$defaults = array(
			'walker' => null,  //obj The walker class to use for iterating transcripts.
			'callback' => null //str Name of the function that displays each transcript
		); $args = wp_parse_args( $args, $defaults ); extract( $args );
		
		if ( !$walker )
			$walker = new webcomic_Walker_Transcripts;
		
		$post_meta   = current( get_post_meta( $post->ID, 'webcomic' ) );
		$transcripts = array();
		
		foreach ( $post_meta[ 'transcripts' ] as $t )
			if ( 'publish' == $t[ 'status' ] )
				array_push( $transcripts, ( object ) $t );
		
		if ( !$transcripts )
			return false;
		
		$walker->walk( $transcripts, 0, $args );
		
		$in_webcomic_transcript_loop = false;
	}
	
	/**
	 * Retrieves information related to the current transcript.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $i The information to return. One of 'text', 'time', 'the_time', 'the_date', 'language', 'language_code', 'author', or 'id'
	 * @return The specified information, or false on error.
	 */
	function get_webcomic_transcript_info( $i ) {
		global $post, $webcomic_transcript, $in_webcomic_transcript_loop;
		
		if ( !$in_webcomic_transcript_loop )
			return false;
		
		if ( isset( $webcomic_transcript->$i ) )
			$r = $webcomic_transcript->$i;
		elseif ( 'id' == $i )
			$r = $post->ID . '-' . $webcomic_transcript->language_code;
		elseif ( 'the_date' == $i )
			$r = date( get_option( 'date_format' ), $webcomic_transcript->time );
		elseif ( 'the_time' == $i )
			$r = date( get_option( 'time_format' ), $webcomic_transcript->time );
		else
			$r = false;
		
		return apply_filters( 'webcomic_get_transcript_info', $r, $i, $webcomic_transcript );
	}
	
	/**
	 * Retrieves the webcomic transcript classes.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param arr|str $class Additional CSS classes.
	 */
	function get_webcomic_transcript_class( $class = false ) {
		global $post, $webcomic_transcript, $in_webcomic_transcript_loop;
		
		if ( !$in_webcomic_transcript_loop )
			return false;
		
		$user_data = get_userdata( $post->post_author );
		$classes[] = 'webcomic-transcript';
		$classes[] = 'webcomic-transcript-' . $post->ID;
		$classes[] = 'webcomic-transcript-' . $webcomic_transcript->language_code;
		$classes[] = 'webcomic-transcript-' . $post->ID . '-' . $webcomic_transcript->language_code;
		
		if ( $user_data->display_name == $webcomic_transcript->author )
			$classes[] = 'webcomic-transcript-author';
		
		if ( $class )
			foreach ( ( array ) $class as $c )
				$classes[] = $c;
		
		$r = 'class="' . implode( ' ', $classes ). '"';
		
		return apply_filters( 'webcomic_get_transcript_class', $r, $classes, $class );
	}
	
	// Terms
	
	/**
	 * Check if the current post is in specified taxonomy.
	 * 
	 * Shortcut function for WordPress' own is_object_in_term.
	 * Though technically generic, we always return false if the
	 * specified taxonomy is not a Webcomic taxonomy.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function in_webcomic_term( $taxonomy, $terms = false, $id = false ) {
		if ( !( 'webcomic_collection' == $taxonomy || 'webcomic_storyline' == $taxonomy || 'webcomic_character' == $taxonomy ) )
			return false;
		
		if ( $id )
			$_post = get_post( $id );
		else
			$_post =& $GLOBALS[ 'post' ];
		
		if ( !$_post )
			return false;
		
		$r = is_object_in_term( $_post->ID, $taxonomy, $terms );
		
		if ( is_wp_error( $r ) )
			return false;
		
		return $r;
	}
	
	/**
	 * Returns term information based on taxonomy.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $i The information to return.
	 * @parram str $taxonomy The taxonomy to check.
	 * @param str Slug of the term to retrive. Optional; if no slug is provided, will attempt to use query_var.
	 * @return The requested information in it's original format, or false on error.
	 */
	function get_webcomic_term_info( $i, $taxonomy, $term = false ) {
		$term = ( $term ) ? $term : get_query_var( $taxonomy );
		$r = false;
		
		if ( $t = get_term_by( 'slug', $term, $taxonomy ) ) {
			if ( isset( $t->{ $i } ) )
				$r = $t->{ $i };
			elseif ( 'link' == $i )
				$r = get_term_link( ( int ) $t->term_id, $taxonomy );
			elseif ( 'feed' == $i )
				$r = get_term_feed_link( ( int ) $t->term_id, $taxonomy );
			elseif ( 'thumb-small' == $i || 'thumb-medium' == $i || 'thumb-large' == $i || 'thumb-full' == $i ) {
				$s = end( explode( '-', $i ) );
				$x = end( explode( '_', $taxonomy ) );
				$r = $this->get_webcomic_object( $s, $x, false, $t->term_id );
			}
		}
		
		return apply_filters( 'webcomic_get_term_info', $r, $i, $taxonomy, $term );
	}
	
	/**
	 * Returns terms related to the specified post in the specified taxonomy.
	 * 
	 * Although technically generic, we alway return false
	 * if the specified taxonomy is not a Webcomic taxonomy.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function get_webcomic_post_terms( $taxonomy, $id = false ) {
		if ( !( 'webcomic_collection' == $taxonomy || 'webcomic_storyline' == $taxonomy || 'webcomic_character' == $taxonomy ) )
			return false;
		
		global $post;
	
		$id    = ( intval( $id ) > 0 ) ? $id : $post->ID;
		$terms = get_object_term_cache( $id, $taxonomy );
		
		if ( false === $terms ) {
			$terms = wp_get_object_terms( $id, $taxonomy );
			wp_cache_add( $id, $terms, $taxonomy . '_post_relationships' );
		}
		
		if ( !empty( $terms ) ) {
			if ( 'webcomic_storyline' == $taxonomy )
				usort( $terms, array( &$this, 'usort_storylines' ) );
			else
				usort( $terms, '_usort_terms_by_name' );
		} else
			$terms = array();
		
		return apply_filters( 'webcomic_get_post_terms', $terms, $taxonomy, $id );
	}
	
	/**
	 * Returns a formatted list of terms related to the specified object in the specified taxonomy.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @uses get_webcomic_post_terms
	 * 
	 * @param str $taxonomy The taxonomy the retrieved terms belong to. Must be one of 'webcomic_collection', 'webcomic_storyline', or 'webcomic_character'.
	 * @param arr|str $args A string or array of arguments.
	 * @return Formatted list of terms, or false on error.
	 */
	function get_the_webcomic_post_terms( $taxonomy, $args = false ) {
		$defaults = array(
			'format'     => false, //str Format to display the terms in. Must be one of 'ulist', 'olist', 'dropdown', 'cloud', 'grid', or false.
			'separator'  => false, //str Text string to place between terms when 'format' is false.
			'before'     => false, //str Text string to place before the generated output.
			'after'      => false, //str Text string to place after the generated output.
			'image'      => false, //str Displays covers/avatars for term links instead of names if non-false and 'format' is not 'dropdown'. If set, should be one of 'full', 'large', 'medium', or 'small'.
			'label'      => false, //str Text string to display for the first null option when 'format' is 'dropdown'.
			'smallest'   => 8,     //int Smallest size for term clouds. Should be between 1 and 99 when using image clouds.
			'largest'    => 22,    //int Largest for term clouds. Automatically set to 100 for image clouds.
			'unit'       => 'pt',  //str The unit to use for cloud sizes. Automaticaly set to % for image clouds.
			'show_count' => false, //str Displays the total post count for each term.
			'id'         => false  //int ID of the object to retrieve terms for.
		); $args = wp_parse_args( $args, $defaults ); extract( $args );
		
		$terms = $this->get_webcomic_post_terms( $taxonomy, $id );
		
		if ( empty( $terms ) || !$this->verify() )
			return false;
		
		$this->domain();
		
		if ( $separator )
			$format = false;
		elseif ( !$format )
			$format = 'ulist';
		
		if ( !$format || 'ulist' == $format || 'olist' == $format ) {
			$walker = ( 'dropdown' == $format ) ? new webcomic_Walker_TermDropdown() : new webcomic_Walker_TermList();
			
			if ( 'ulist' == $format || 'olist' == $format ) {
				$x  = ( 'ulist' == $format ) ? 'u' : 'o';
				$p1 = '<' . $x . 'l class="webcomic-post-terms ' . $taxonomy . '-post-terms">';
				$p2 = '</' . $x . 'l>';
			} elseif ( 'dropdown' == $format ) {
				$x  = false;
				$p1 = '<select name="' . $taxonomy . '_post_terms" class="webcomic-post-terms ' . str_replace( '_', '-', $taxonomy ) . '-post-terms"><option>' . $label . '</option>';
				$p2 = '</select>';
			} else
				$x = $p1 = $p2 = false;
			
			$args[ 'x' ]        = $x;
			$args[ 'taxonomy' ] = $taxonomy;
			
			$l = $walker->walk( $terms, 0, $args );
			$l = ( !$format ) ? substr( $l, 0, strrpos( $l, $separator ) ) : $l;
		} elseif ( 'cloud' == $format ) {
			$largest  = ( $largest ) ? $largest : 22;
			$smallest = ( $smallest ) ? $smallest : 8;
			$counts   = array();
			
			foreach ( ( array ) $terms as $k => $v )
				$counts[ $k ] = $v->count;
			
			$a = array();
			
			if ( $image ) {
				$largest  = 100;
				$smallest = ceil( $smallest );
				
				$min_count = min( $counts );
				
				if ( ( $spread = max( $counts ) - $min_count ) <= 0 )
					$spread = 1;
				
				if ( ( $dim_spread = $largest - $smalles ) < 0 )
					$dim_spread = 1;
				
				$dim_step = $dim_spread / $spread;
				
				foreach ( $terms as $k => $v ) {
					$count     = $counts[ $k ];
					$size      = ( $smallest + ( ( $count - $min_count ) * $dim_step ) );
					$term_name = $v->name;
					
					if ( $v->webcomic_files[ $image ] ) {
						$term_name = '';
						
						foreach ( $v->webcomic_files[ $image ] as $file ) {
							$f   = ( $this->option( 'secure_toggle' ) ) ? $file[ 'shtml' ] : $file[ 'html' ];
							$new = 'width="' . ceil( ( $size / 100 ) * $file[ 0 ] ) . '" height="' . ceil( ( $size / 100 ) * $file[ 1 ] ) . '"';
							$term_name .= preg_replace( '/(width="\d+" height="\d+")/', $new, $f );
						}
					}
					
					$a[] = '<a href="' . get_term_link( ( int ) $v->term_id, $taxonomy ) . '" class="webcomic-term-item ' . $taxonomy . '-item ' . $taxonomy . '-item-' . $v->term_id . '" title="' . sprintf( __( '%d webcomics', 'webcomic' ), $term->count ) . '" rel="' . $taxonomy . '" style="font-size:' . ( $smallest + ( ( $counts[ $k ] - $min_count ) * $font_step ) ) . $unit . '">' . $term_name . '</a>';
				}
				
				$l = implode( ' ', $a );
			} else {
				$min_count = min( $counts );
				
				if ( ( $spread = max( $counts ) - $min_count ) <= 0 )
					$spread = 1;
				
				if ( ( $font_spread = $largest - $smallest ) < 0 )
					$font_spread = 1;
				
				$font_step = $font_spread / $spread;
				
				foreach ( $terms as $k => $v )
					$a[] = '<a href="' . get_term_link( ( int ) $v->term_id, $taxonomy ) . '" class="webcomic-term-item ' . $taxonomy . '-item ' . $taxonomy . '-item-' . $v->term_id . '" title="' . sprintf( __( '%d webcomics', 'webcomic' ), $v->count ) . '" rel="' . $taxonomy . '" style="font-size:' . ( $smallest + ( ( $counts[ $k ] - $min_count ) * $font_step ) ) . $unit . '">' . $v->name . '</a>';
				
				$l = implode( ' ', $a );
			}
		}
		
		$r  = $before . $p1 . $l . $p2 . $after;
		
		return apply_filters( 'webcomic_get_the_post_terms', $r, $terms, $taxonomy, $args );
	}
	
	// Term Navigation
	
	/**
	 * Retrieves term ID's relative to the specified term.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $taxonomy The taxonomy terms belong to.
	 * @param str|int $term The term slug or ID that acts as an anchor.
	 * @param str $orderby How to order the retrieved terms. Defaults to 'name' for characters and collections and 'custom' for storylines.
	 * @param bool $hide_empty Wether to exclude empty terms. Defaults to true.
	 * @return arr An array of webcomic ID's, or false on error.
	 */
	function get_relative_webcomic_terms( $taxonomy, $term = false, $orderby = false , $hide_empty = true) {
		if ( !( $term = ( $term ) ? $term : get_query_var( $taxonomy ) ) || !$this->verify() )
			return false;
		
		$ck = $taxonomy . '_' . hash( 'md5', implode( ( array ) $term, '' ) . $orderby . $hide_empty . $term->term_group );
		
		if ( $r = wp_cache_get( $ck, 'get_relative_webcomic_terms' ) )
			return $r;
		
		if ( intval( $term ) )
			$term = get_term( $term, $taxonomy );
		else
			$term = get_term_by( 'slug', $term, $taxonomy );
		
		if ( 'webcomic_storyline' == $taxonomy )
			$orderby = ( $orderby ) ? 'orderby=' . $orderby : 'webcomic_order=1';
		else
			$orderby = ( $orderby ) ? 'orderby=' . $orderby : 'orderby=name';
		
		$hide_empty = ( $hide_empty ) ? '' : '&hide_empty=0';
		
		$order = ( 'webcomic_collection' != $taxonomy ) ? str_pad( '&order=ASC', $term->term_group ) : '';
		
		$r = array();
		
		if ( !is_wp_error( $terms = get_terms( $taxonomy, $orderby . '&term_group=' . $term->term_group . $hide_empty . $order ) ) && !empty( $terms ) ) {
			foreach ( array_keys( $terms ) as $k ) {
				if ( $terms[ $k ]->term_id == $term->term_id ) {
					$key = $k;
					break;
				}
			}
			
			if ( false !== $key ) {
				$r[ 'random' ]   = ( int ) $terms[ array_rand( $terms, 1 ) ]->term_id;
				$r[ 'last' ]     = ( int ) end( $terms )->term_id;
				$r[ 'first' ]    = ( int ) reset( $terms )->term_id;
				$r[ 'previous' ] = ( int ) ( ( $key - 1 ) > -1 && isset( $terms[ $key - 1 ] ) ) ? $terms[ $key - 1 ]->term_id : $term->term_id;
				$r[ 'next' ]     = ( int ) ( ( $key + 1 ) < count( $terms ) && isset( $terms[ $key + 1 ] ) ) ? $terms[ $key + 1 ]->term_id : $term->term_id;
			}
		}
		
		wp_cache_add( $ck, $r, 'get_relative_webcomic_terms' );
		
		return apply_filters( 'webcomic_get_relative_terms', $r, $taxonomy, $term, $orderby );
	}
	
	/**
	 * Retrieves term permalink URL's relative to the current term.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $key The releative key ID. Must be one of 'first', 'previous', 'next', 'last', or 'random'.
	 * @param str $taxonomy The taxonomy terms belong to.
	 * @param str $term The term ID or slug that acts as an anchor.
	 * @param str $orderby How to order the terms. May be one of 'name', 'count', 'term_group', 'slug', or 'term_group_name'. Defaults to 'name' for webcomic_character and custom for webcomic_storyline.
	 * @param bool $hide_empty Wether to exclude empty terms. Defaults to true.
	 * @return The permalink URL, or false on error.
	 */
	function get_relative_webcomic_term_url( $key, $taxonomy, $term = false, $orderby = false, $hide_empty = true ) {
		if ( !( $ids = $this->get_relative_webcomic_terms( $taxonomy, $terms, $orderby, $hide_empty ) ) || !$ids[ $key ] )
			return false;
		
		global $post;
		
		$url = get_term_link( $ids[ $key ], $taxonomy );
		
		return apply_filters( 'webcomic_get_relative_term_url', $url, $key, $taxonomy, $terms );
	}
	
	/**
	 * Retrieves a properly formatted term link relative to the current term.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $key The releative key ID. Must be one of 'first', 'previous', 'next', 'last', or 'random'.
	 * @param str $taxonomy The taxonomy terms belong to.
	 * @param str $format The format of the returned link. Should contain %link token.
	 * @param str $link What to display for the link text. May contain the %label, %name, %thumb-small, %thumb-medium, %thumb-large, and %thumb-full tokens.
	 * @param str $term A term ID or array of term ID's. Required if $taxonomy is not false.
	 * @param str $orderby How to order the terms. May be one of 'name', 'count', 'term_group', 'slug', or 'term_group_name'. Defaults to 'name' for webcomic_character and custom for webcomic_storyline.
	 * @param bool $hide_empty Wether to exclude empty terms. Defaults to true.
	 * @return Formatted term link, or false on error.
	 */
	function get_relative_webcomic_term_link( $key, $taxonomy, $format, $link, $term = false, $orderby = false, $hide_empty = true ) {
		if ( !( $term = ( $term ) ? $term : get_query_var( $taxonomy ) ) || !( $ids = $this->get_relative_webcomic_terms( $taxonomy, $term, $orderby, $hide_empty ) ) || !$ids[ $key ] )
			return false;
		
		$this->domain();
		
		if ( 'webcomic_storyline' == $taxonomy )
			$l = __( 'Storyline', 'webcomic' );
		elseif ( 'webcomic_character' == $taxonomy )
			$l = __( 'Character', 'webcomic' );
		elseif ( 'webcomic_collection' == $taxonomy )
			$l = __( 'Collection', 'webcomic' );
		else
			return false;
		
		switch ( $key ) {
			case 'random'  : $label = '<span>' . sprintf( __( 'Random %s', 'webcomic' ), $l ) . '</span>'; break;
			case 'first'   : $label = '<span>' . sprintf( __( '&laquo; First %s', 'webcomic' ), $l ) . '</span>'; break;
			case 'last'    : $label = '<span>' . sprintf( __( 'Last %s &raquo;', 'webcomic' ), $l ) . '</span>'; break;
			case 'previous': $label = '<span>' . sprintf( __( '&lsaquo; Previous %s', 'webcomic' ), $l ) . '</span>'; break;
			case 'next'    : $label = '<span>' . sprintf( __( 'Next %s &rsaquo;', 'webcomic' ), $l ) . '</span>'; break;
		}
		
		$type     = end( explode( '_', $taxonomy ) );
		$term     = get_term_by( 'slug', $term, $taxonomy );
		$adj_term = get_term( $ids[ $key ], $taxonomy );
		$current  = ( $term->term_id == $ids[ $key ] ) ? ' current-' . $type : '';
		$kbd      = ( $this->option( 'shortcut_toggle' ) ) ? ' webcomic-kbd-shortcut' : '';
		$match    = false;
		
		if ( preg_match( '/\%thumb-(full|large|medium|small)(?:-(\d+))?/', $link, $match ) )
			$link = preg_replace( "/$match[0]/", $this->get_webcomic_object( $match[ 1 ], $type, $match[ 2 ], $ids[ $key ] ), $link );
		
		$link   = str_replace( '%label', $label, $link );
		$link   = str_replace( '%name', $adj_term->name, $link );
		$link   = '<a href="' . get_term_link( ( int ) $adj_term->term_id, $taxonomy ) . '" rel="' . $key . '" class="' . $key . '-' . $type . '-link ' . $key . '-' . $type . '-link-' . $adj_term->term_id . $kbd . $current . '">' . $link . '</a>';
		$format = str_replace( '%link', $link, $format );
		
		return apply_filters( 'webcomic_get_relative_term_link', $format, $link, $key, $taxonomy, $term );
	}
	
	/**
	 * Returns a formatted list of terms.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $taxonomy The taxonomy the retrieved terms belong to. Must be one of 'webcomic_collection', 'webcomic_storyline', or 'webcomic_character'.
	 * @param arr|str $args A string or array of arguments.
	 * @return Formatted list of terms, or false on error.
	 */
	function get_the_webcomic_terms( $taxonomy, $args = false ) {
		$defaults = array(
			'format'       => false, //str     Format to display the terms in. Must be one of 'ulist', 'olist', 'dropdown', 'cloud', 'grid', or false.
			'separator'    => false, //str     Text string to place between terms when 'format' is false.
			'before'       => false, //str     Text string to place before the generated output.
			'after'        => false, //str     Text string to place after the generated output.
			'image'        => false, //str     Displays covers/avatars for term links instead of names if non-false and 'format' is not 'dropdown'. If set, should be one of 'full', 'large', 'medium', or 'small'.
			'label'        => false, //str     Text string to display for the first null option when 'format' is 'dropdown'.
			'smallest'     => 8,     //int     Smallest size for term clouds. Should be between 1 and 99 when using image clouds.
			'largest'      => 22,    //int     Largest for term clouds. Automatically set to 100 for image clouds.
			'unit'         => 'pt',  //str     The unit to use for cloud sizes. Automaticaly set to % for image clouds.
			'show_count'   => false, //bool    Show the term post count.
			'selected'     => false, //int     ID of the currently selected term
			'hierarchical' => true,  //bool    Display terms hierarchically.
			'hide_empty'   => true,  //bool    Hide terms with no assigned posts.
			'order'        => 'ASC', //str     Order to return terms in, one of 'ASC' or 'DESC'.
			'term_group'   => false  //int     Term group the returned terms must belong to; necessary for limiting storylines and characters to a specific collection.
		); $args = wp_parse_args( $args, $defaults ); extract( $args );
		
		if ( empty( $args[ 'orderby' ] ) ) {
			$args[ 'orderby' ] = ( 'webcomic_collection' == $taxonomy || 'webcomic_character' == $taxonomy ) ? 'name' : '';
			
			if ( 'webcomic_storyline' == $taxonomy )
				$args[ 'webcomic_order' ] = true;
		}
		
		$args[ 'order' ] = str_pad( $args[ 'order' ], $args[ 'term_group' ] );
		
		if ( !$args[ 'selected' ] && $slug = get_query_var( $taxonomy ) ) {
			$term = get_term_by( 'slug', $slug, $taxonomy );
			
			$args[ 'selected' ] = ( $term && !is_wp_error( $term ) ) ? $term->term_id : false;
		}
		
		$terms = get_terms( $taxonomy, $args );
		
		if ( empty( $terms ) )
			return false;
		
		$this->domain();
		
		if ( $separator )
			$format = false;
		elseif ( !$format )
			$format = 'ulist';
		
		if ( !$format || 'ulist' == $format || 'olist' == $format || 'dropdown' == $format ) {
			$walker = ( 'dropdown' == $format ) ? new webcomic_Walker_TermDropdown() : new webcomic_Walker_TermList();
			
			if ( 'ulist' == $format || 'olist' == $format ) {
				$x  = ( 'ulist' == $format ) ? 'u' : 'o';
				$p1 = '<' . $x . 'l class="webcomic-terms ' . $taxonomy . '-terms">';
				$p2 = '</' . $x . 'l>';
			} elseif ( 'dropdown' == $format ) {
				$x  = false;
				$p1 = '<select name="' . $taxonomy . '_terms" class="webcomic-terms ' . str_replace( '_', '-', $taxonomy ) . '-terms"><option>' . $label . '</option>';
				$p2 = '</select>';
			} else
				$x = $p1 = $p2 = false;
			
			$args[ 'x' ]        = $x;
			$args[ 'taxonomy' ] = $taxonomy;
			
			$l = $walker->walk( $terms, 0, $args );
			$l = ( !$format ) ? substr( $l, 0, strrpos( $l, $separator ) ) : $l;
		} elseif ( 'cloud' == $format ) {
			$largest  = ( $largest ) ? $largest : 22;
			$smallest = ( $smallest ) ? $smallest : 8;
			$counts   = array();
			
			foreach ( ( array ) $terms as $k => $v )
				$counts[ $k ] = $v->count;
			
			$a = array();
			
			if ( $image ) {
				$largest  = 100;
				$smallest = ceil( $smallest );
				
				$min_count = min( $counts );
				
				if ( ( $spread = max( $counts ) - $min_count ) <= 0 )
					$spread = 1;
				
				if ( ( $dim_spread = $largest - $smalles ) < 0 )
					$dim_spread = 1;
				
				$dim_step = $dim_spread / $spread;
				
				foreach ( $terms as $k => $v ) {
					$count     = $counts[ $k ];
					$size      = ( $smallest + ( ( $count - $min_count ) * $dim_step ) );
					$term_name = $v->name;
					
					if ( $v->webcomic_files[ $image ] ) {
						$term_name = '';
						
						foreach ( $v->webcomic_files[ $image ] as $file ) {
							$f   = ( $this->option( 'secure_toggle' ) ) ? $file[ 'shtml' ] : $file[ 'html' ];
							$new = 'width="' . ceil( ( $size / 100 ) * $file[ 0 ] ) . '" height="' . ceil( ( $size / 100 ) * $file[ 1 ] ) . '"';
							$term_name .= preg_replace( '/(width="\d+" height="\d+")/', $new, $f );
						}
					}
					
					$a[] = '<a href="' . get_term_link( ( int ) $v->term_id, $taxonomy ) . '" class="webcomic-term-item ' . $taxonomy . '-item ' . $taxonomy . '-item-' . $v->term_id . '" title="' . sprintf( __( '%d webcomics', 'webcomic' ), $term->count ) . '" rel="' . $taxonomy . '" style="font-size:' . ( $smallest + ( ( $counts[ $k ] - $min_count ) * $font_step ) ) . $unit . '">' . $term_name . '</a>';
				}
				
				$l = implode( ' ', $a );
			} else {
				$min_count = min( $counts );
				
				if ( ( $spread = max( $counts ) - $min_count ) <= 0 )
					$spread = 1;
				
				if ( ( $font_spread = $largest - $smallest ) < 0 )
					$font_spread = 1;
				
				$font_step = $font_spread / $spread;
				
				foreach ( $terms as $k => $v )
					$a[] = '<a href="' . get_term_link( ( int ) $v->term_id, $taxonomy ) . '" class="webcomic-term-item ' . $taxonomy . '-item ' . $taxonomy . '-item-' . $v->term_id . '" title="' . sprintf( __( '%d webcomics', 'webcomic' ), $v->count ) . '" rel="' . $taxonomy . '" style="font-size:' . ( $smallest + ( ( $counts[ $k ] - $min_count ) * $font_step ) ) . $unit . '">' . $v->name . '</a>';
				
				$l = implode( ' ', $a );
			}
		} elseif ( false !== strpos( $format, 'grid' ) ) {
			$i    = 0;
			$cols = ( is_numeric( end( explode( '-', $format ) ) ) ) ? intval( end( explode( '-', $format ) ) ) : 3;
			
			$rows  = ceil ( count( $terms ) / $cols );
			$table = array();
			
			for ( $row = 1; $row <= $rows; $row++ )
				for ( $col = 1; $col <= $cols; $col++ )
					$table[ $row ][ $col ] = array_shift( $terms );
			
			$l = '<table class="webcomic-terms ' . str_replace( '_', '-', $taxonomy ) . '-terms"><tbody>';
			
			foreach ( $table as $row => $cols ) {
				$a  = ( !( $i % 2 ) ) ? ' class="alt"' : '';
				$l .= '<tr' . $a . '>';
				
				foreach ( $cols as $col => $term ) {
					if ( !$term ) {
						$l .= '<td class="empty"></td>';
						continue;
					}
					
					$term_name = $term->name;
					
					if ( $term->webcomic_files[ $image ] ) {
						$term_name = '';
						
						foreach ( $term->webcomic_files[ $image ] as $file )
							$term_name .= ( $this->option( 'secure_toggle' ) ) ? $file[ 'shtml' ] : $file[ 'html' ];
					}
					
					$l .= '<td class="webcomic-term-item ' . $taxonomy . '-item ' . $taxonomy . '-item-' . $term->term_id . '"><a href="' . get_term_link( ( int ) $term->term_id, $taxonomy ) . '">' . $term_name . '</a></td>';
				}
				
				$l .= '</tr>';
				$i++;
			}
			
			$l .= '</tbody></table>';
		}
		
		$r  = $before . $p1 . $l . $p2 . $after;
		
		return apply_filters( 'webcomic_get_the_terms', $r, $terms, $taxonomy, $args );
	}
	
	/**
	 * Displays webcomic posts in the specified format.
	 * 
	 * While this function is relatively flexible, users are
	 * encouraged to expierment with their own archive layouts
	 * and not rely on the ones provided by this template tag.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param arr|str $args A string or array of arguments.
	 * @return Formatted archive of webcomic posts, or false on error.
	 */
	function get_the_webcomic_archive( $args = false ) {
		$defaults = array(
			'format'           => false, //str Format to display the terms in. Must be one of 'ulist', 'olist', 'dropdown', 'grid' (when group is not 'collection', 'storyline', or 'character'), or false.
			'group'            => false, //str How to group the webcomics. May be one of 'day', 'month, 'year', 'collection', 'storyline', or 'character'.
			'separator'        => false, //str Text string to place between terms when 'format' is false.
			'before'           => false, //str Text string to place before the generated output.
			'after'            => false, //str Text string to place after the generated output.
			'image'            => false, //str Displays thumbnails for post links instead of names if non-false and 'format' is not 'dropdown'. If set, should be one of 'full', 'large', 'medium', or 'small'.
			'group_image'      => false, //str Displays thumbnails for term links instead of names if non-false and 'format' is not 'dropdown'. If set, should be one of 'full', 'large', 'medium', or 'small'.
			'label'            => false, //str Text string to display for the first null option when 'format' is 'dropdown'.
			'limit'            => false, //int Limits the number of returned webcomics (total or for each term when groups by 'collection', 'storyline', or 'character').
			'group_limit'      => false, //int Limits the number of returned terms.
			'show_count'       => false, //bool Show the term post count.
			'show_description' => false, //bool Show term descriptions.
			'hierarchical'     => true,  //bool Display terms hierarchically.
			'hide_empty'       => true,  //bool Hide terms with no assigned posts.
			'order'            => 'ASC', //str Order to return posts in, one of 'ASC' or 'DESC'.
			'group_order'      => 'ASC', //str Order to return terms in, one of 'ASC' or 'DESC'.
			'term_group'       => false, //int Term group the returned terms must belong to; necessary for limiting storylines and characters to a specific collection.
			'last_only'        => false  //bool Display posts for the last (bottom-most) terms in the hierarchy.
		); $args = wp_parse_args( $args, $defaults ); extract( $args );
		
		global $wpdb;
		
		$before = $p1 = $l = $p2 = $after = false;
		
		if ( 'collection' == $group || 'storyline' == $group || 'character' == $group ) {
			if ( 'storyline' == $group )
				$args[ 'webcomic_order' ] = 1;
			
			$a  = ( 'collection' == $group || $term_group ) ? get_terms( "webcomic_$group", $args ) : get_terms( 'webcomic_collection', $args );
			$wc = ( 'collection' == $group || $term_group ) ? false : true;
			
			if ( 'DESC' == $group_order )
				$a = array_reverse( $a );
			
			if ( !empty( $group_limit ) )
				$a = array_slice( $a, 0, $group_limit );
			
			if ( false === strpos( $format, 'grid' ) ) {
				if ( 'ulist' == $format || 'olist' == $format ) {
					$x  = ( 'ulist' == $format ) ? 'u' : 'o';
					$p1 = '<' . $x . 'l class="webcomic-archive webcomic-archive-' . $group . 's">';
					$p2 = '</' . $x . 'l>';
				} elseif ( 'dropdown' == $format ) {
					$x  = false;
					$p1 = '<select name="webcomic_archive" class="webcomic-archive webcomic-archive-' . $group . 's"><option>' . $label . '</option>';
					$p2 = '</select>';
				} else
					$x = $p1 = $p2 = false;
				
				$args[ 'x' ]        = $x;
				$args[ 'taxonomy' ] = "webcomic_$group";
				
				if ( $wc ) {
					foreach ( $a as $v ) {
						$args[ 'wc' ] = $v;
						
						if ( 'ulist' == $format || 'olist' == $format )
							$l .= '<li class="webcomic-archive-collection webcomic-archive-collection-' . $v->slug . '"><a href="' . get_term_link( ( int ) $v->term_id, 'webcomic_collection' ) . '"><span>' . $v->name . '</span></a><' . $x . 'l class="webcomic-archive-'. $group .'">';
						elseif ( 'dropdown' == $format )
							$l .= '<optgroup label="' . $v->name . '">';
						
						$b  = get_terms( "webcomic_$group", $args );
						
						if ( 'DESC' == $group_order )
							$b = array_reverse( $b );
						
						if ( !empty( $group_limit ) )
							$b = array_slice( $b, 0, $group_limit );
						
						$w  = ( 'dropdown' == $format ) ? new webcomic_Walker_ArchiveDropdown() : new webcomic_Walker_ArchiveList();
						$l .= $w->walk( $b, 0, $args );
						
						if ( !$format )
							$l .= substr( $l, 0, strrpos( $l, $separator ) );
						else
							$l .= ( 'dropdown' == $format ) ? '</optgroup>' : '</' . $x . 'l></li>';
					}
				} else {
					$w = ( 'dropdown' == $format ) ? new webcomic_Walker_ArchiveDropdown() : new webcomic_Walker_ArchiveList();
					$l = $w->walk( $a, 0, $args );
					$l = ( !$format ) ? substr( $l, 0, strrpos( $l, $separator ) ) : $l;
				}
			}
		} else {
			$order = ( 'ASC' == $order ) ? $order : 'DESC';
			$limit = ( $limit ) ? 'LIMIT ' . intval( $limit ) : '';
			$join  = ( $term_group ) ? $wpdb->prepare( "INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'webcomic_collection' AND tt.term_id IN (%s)", $term_group ) : '';
			$ids   = $wpdb->get_col( "SELECT ID FROM $wpdb->posts AS p $join WHERE post_type = 'webcomic_post' AND post_status IN ('publish','private') ORDER BY post_date $order $limit" );
			$d     = false;
			
			if ( !$ids )
				return false;
			
			if ( $separator )
				$format = false;
			elseif ( !$format )
				$format = 'ulist';
			
			if ( 'year' == $group )
				$f = apply_filters( 'webcomic_archive_year', 'Y' );
			elseif ( 'month' == $group )
				$f = apply_filters( 'webcomic_archive_month', 'F Y' );
			elseif ( 'day' == $group )
				$f = apply_filters( 'webcomic_archive_day', get_option( 'date_format' ) );
			else
				$f = false;
			
			if ( 'ulist' == $format || 'olist' == $format ) {
				$x = ( 'ulist' == $format ) ? 'u' : 'o';
				$l = '<' . $x . 'l class="webcomic-archive webcomic-archive-' . $group . '">';
				
				foreach ( $ids as $id ) {
					if ( $f && $d && ( $d != get_the_time( $f, $id ) ) )
						$l .=  '</' . $x . 'l></li>';
					
					if ( $f && ( $d != get_the_time( $f, $id ) ) ) {
						if ( 'year' == $group )
							$u = get_year_link( get_the_time( 'Y', $id ) );
						elseif ( 'month' == $group )
							$u = get_month_link( get_the_time( 'Y', $id ), get_the_time( 'n', $id ) );
						else
							$u = get_day_link( get_the_time( 'Y', $id ), get_the_time( 'n', $id ), get_the_time( 'j', $id ) );
						
						$l .= '<li class="webcomic-archive-' . $group . ' webcomic-archive-' . $group . '-' . str_replace( ' ', '-', get_the_time( $f, $id ) ) . '"><a href="' . $u . '"><span>' . get_the_time( $f, $id ) . '</span></a><' . $x . 'l class="webcomic-archive-items">';
						$d  = get_the_time( $f, $id );
					} else
						$l .= '</li>';
					
					if ( $image ) {
						$link = $wc = $img = false;
						$wc = current( wp_get_object_terms( $id, 'webcomic_collection' ) );
						
						if ( is_object( $wc ) && ( $img = $this->retrieve( $id, 'post', $wc->slug, true ) ) )
							if ( !empty( $img[ $image ] ) )
								foreach ( $img[ $image ] as $v )
									$link .= ( $this->option( 'secure_toggle' ) ) ? $v[ 'shtml' ] : $v[ 'html' ];
					} else
						$link = get_the_title( $id );
					
					$l .= '<li class="webcomic-archive-item webcomic-archive-item-' . $id . '"><a href="' . get_permalink( $id ) . '">' . $link . '</a></li>';
				}
				
				$l .= ( $d ) ? '</' . $x . 'l></li></' . $x . 'l>' : '</' . $x . 'l>';
			} elseif ( 'dropdown' == $format ) {
				$c = ( $group ) ? ' webcomic-archive-' . $group  : '';
				$l = '<select name="webcomic_archive" class="webcomic-archive' . $c . '"><option>' . $label . '</option>';
				
				foreach ( $ids as $id ) {
					if ( $f && ( $d != get_the_time( $f, $id ) ) ) {
						$l .= ( !$d ) ? '<optgroup label="' . get_the_time( $f, $id ) . '">' : '</optgroup><optgroup label="' . get_the_time( $f, $id ) . '">';
						$d = get_the_time( $f, $id );
					}
					
					$l .= '<option value="' . get_permalink( $id ) . '">' . get_the_title( $id ) . '</option>';
				}
				
				$l .= ( $d ) ? '</optgroup></select>' : '</select>';
			} elseif ( false !== strpos( $format, 'grid' ) ) {
				$i = $x = 0;
				$c = $cols = ( is_numeric( end( explode( '-', $format ) ) ) ) ? intval( end( explode( '-', $format ) ) ) : 3;
				
				if ( !$group ) {
					$rows  = ceil ( count( $ids ) / $cols );
					$table = array();
					
					for ( $row = 1; $row <= $rows; $row++ )
						for ( $col = 1; $col <= $cols; $col++ )
							$table[ $row ][ $col ] = array_shift( $ids );
				} else {
					$date  = false;
					$dates = array();
					
					foreach ( $ids as $id ) {
						if ( $date != get_the_time( $f, $id ) ) {
							$dates[ get_the_time( $f, $id ) ][] = $id;
							$date = get_the_time( $f, $id );
						} else
							$dates[ $date ][] = $id;
					}
					
					$table = array();
					
					foreach ( $dates as $k => $v ) {
						$rows = ceil ( count( $v ) / $cols );
						
						$table[] = current( $v );
						
						$i++;
						
						for( $row = 1; $row <= $rows; $row++ )
							for ( $col = 1; $col <= $cols; $col++ )
								$table[ $k . ' ' . $row ][ $col ] = array_shift( $v );
					}
				}
				
				$class = ( $group ) ? ' webcomic-archive-' . $group : '';
				
				$i = $x = 0;
				$l = '<table class="webcomic-archive' . $class . '"><tbody>';
				
				foreach ( $table as $row => $cols ) {
					$a  = ( !( $i % 2 ) ) ? ' alt' : '';
					$l .= '<tr class="';
					
					if ( !is_array( $cols ) ) {
						$l .= 'webcomic-archive-' . $group . '-' . str_replace( ' ', '-', get_the_time( $f, $cols ) ) . $a . '">';
						
						if ( 'year' == $group )
							$u = get_year_link( get_the_time( 'Y', $cols ) );
						elseif ( 'month' == $group )
							$u = get_month_link( get_the_time( 'Y', $cols ), get_the_time( 'n', $cols ) );
						else
							$u = get_day_link( get_the_time( 'Y', $cols ), get_the_time( 'n', $cols ), get_the_time( 'j', $cols ) );
						
						$l .= '<th colspan="' . $c . '"><a href="' . $u . '">' . get_the_time( $f, $cols ) . '</a></th>';
						$d  = get_the_time( $f, $cols );
					} else {
						$l .= 'webcomic-archive-items' . $a . '">';
						
						foreach ( $cols as $col => $id ) {
							if ( !$id ) {
								$l .= '<td class="webcomic-archive-item webcomic-archive-item-empty"></td>';
								continue;
							}
							
							if ( $image ) {
								$link = $wc = $img = false;
								$wc = current( wp_get_object_terms( $id, 'webcomic_collection' ) );
								
								if ( $img = $this->retrieve( $id, 'post', $wc->slug, true ) )
									foreach ( $img[ $image ] as $v )
										$link .= ( $this->option( 'secure_toggle' ) ) ? $v[ 'shtml' ] : $v[ 'html' ];
							} else
								$link = get_the_title( $id );
							
							$l .= '<td class="webcomic-archive-item webcomic-archive-item-' . $id . '"><a href="' . get_permalink( $id ) . '" class="">' . $link . '</a></td>';
						}
					}
					
					$l .= '</tr>';
					$i++;
				}
				
				$l .= '</tbody></table>';
			} else {
				$l = array();
				
				foreach ( $ids as $id ) {
					if ( $image ) {
						$link = $wc = $img = false;
						$wc = current( wp_get_object_terms( $id, 'webcomic_collection' ) );
						
						if ( $img = $this->retrieve( $id, 'post', $wc->slug, true ) )
							foreach ( $img[ $image ] as $v )
								$link .= ( $this->option( 'secure_toggle' ) ) ? $v[ 'shtml' ] : $v[ 'html' ];
					} else
						$link = get_the_title( $id );
					
					$l[] = '<a href="' . get_permalink( $id ) . '">' . $link . '</a>';
				}
				
				$l = implode( $separator, $l );
			}
		}
		
		
		$r = $before . $p1 . $l . $p2 . $after;
		
		return apply_filters( 'webcomic_get_the_archive', $r, $args );
	}

	
	
	
	////
	// Shortcodes
	//
	// These functions define WordPress shortcodes
	// to be used in post content.
	////
	
	/**
	 * Displays the webcomic related to the current post.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function short_webcomic( $atts, $content = false ) {
		extract( shortcode_atts( array(
			'size'     => 'full',
			'link'     => false,
			'taxonomy' => false,
			'terms'    => false,
			'key'      => false
		), $atts ) );
		
		return $this->get_the_webcomic_object( $size, $link, $taxonomy, $terms, $key );
	}
	
	/**
	 * Displays a link to a random webcomic in the current collection.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function short_random_webcomic_link( $atts, $content = false ) {
		extract( shortcode_atts( array(
			'link'     => '%link',
			'format'   => '%label',
			'taxonomy' => false,
			'terms'    => false
		), $atts ) );
		
		return $this->get_relative_webcomic_link( 'random', $link, $format, $taxonomy, $terms );
	}
	/**
	 * Displays a link to the first webcomic in the current collection.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function short_first_webcomic_link( $atts, $content = false ) {
		extract( shortcode_atts( array(
			'link'     => '%link',
			'format'   => '%label',
			'taxonomy' => false,
			'terms'    => false
		), $atts ) );
		
		return $this->get_relative_webcomic_link( 'first', $link, $format, $taxonomy, $terms );
	}
	
	/**
	 * Displays a link to the previous webcomic in the current collection.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function short_previous_webcomic_link( $atts, $content = false ) {
		extract( shortcode_atts( array(
			'link'     => '%link',
			'format'   => '%label',
			'taxonomy' => false,
			'terms'    => false
		), $atts ) );
		
		return $this->get_relative_webcomic_link( 'previous', $link, $format, $taxonomy, $terms );
	}
	
	/**
	 * Displays a link to the next webcomic in the current collection.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function short_next_webcomic_link( $atts, $content = false ) {
		extract( shortcode_atts( array(
			'link'     => '%link',
			'format'   => '%label',
			'taxonomy' => false,
			'terms'    => false
		), $atts ) );
		
		return $this->get_relative_webcomic_link( 'next', $link, $format, $taxonomy, $terms );
	}
	
	/**
	 * Displays a link to the last webcomic in the current collection.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function short_last_webcomic_link( $atts, $content = false ) {
		extract( shortcode_atts( array(
			'link'     => '%link',
			'format'   => '%label',
			'taxonomy' => false,
			'terms'    => false
		), $atts ) );
		
		return $this->get_relative_webcomic_link( 'last', $link, $format, $taxonomy, $terms );
	}
	
	/**
	 * Displays a link to purchase a print of the current webcomic.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function short_purchase_webcomic_link( $atts, $content = false ) {
		extract( shortcode_atts( array(
			'link'   => '%link',
			'format' => '%label',
			'id'     => false
		), $atts ) );
		
		return $this->get_purchase_webcomic_link( $link, $format, $id );
	}
	
	/**
	 * Displays a formatted list of webcomics related to the current webcomic.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function short_get_related_webcomics( $atts, $content = false ) {
		extract( shortcode_atts( array(
			'format'     => false,
			'separator'  => false,
			'before'     => false,
			'after'      => false,
			'number'     => 5,
			'order'      => 'ASC',
			'image'      => false,
			'imagekey'   => false,
			'label'      => false,
			'storylines' => true,
			'characters' => true
		), $atts ) );
		
		return $this->get_the_related_webcomics( array(
			'format'     => $format,
			'separator'  => $separator,
			'before'     => $before,
			'after'      => $after,
			'number'     => 5,
			'order'      => 'ASC',
			'image'      => $image,
			'imagekey'   => $imagekey,
			'label'      => $label,
			'storylines' => $storylines,
			'characters' => $characters
		) );
	}
	
	/**
	 * Displays a formatted list of collections related to the current webcomic.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function short_webcomic_collections( $atts, $content = false ) {
		extract( shortcode_atts( array(
			'format'    => false,
			'separator' => false,
			'before'    => false,
			'after'     => false,
			'image'     => false,
			'label'     => false,
			'smallest'  => 8,
			'largest'   => 22,
			'unit'      => 'pt'
		), $atts ) );
		
		return $this->get_the_webcomic_post_terms( 'webcomic_collection', array(
			'format'    => $format,
			'separator' => $separator,
			'before'    => $before,
			'after'     => $after,
			'image'     => $image,
			'label'     => $label,
			'smallest'  => $smallest,
			'largest'   => $largest,
			'unit'      => $unit
		) );
	}
	
	/**
	 * Displays a formatted list of storylines related to the current webcomic.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function short_webcomic_storylines( $atts, $content = false ) {
		extract( shortcode_atts( array(
			'format'    => false,
			'separator' => false,
			'before'    => false,
			'after'     => false,
			'image'     => false,
			'label'     => false,
			'smallest'  => 8,
			'largest'   => 22,
			'unit'      => 'pt'
		), $atts ) );
		
		return $this->get_the_webcomic_post_terms( 'webcomic_storyline', array(
			'format'    => $format,
			'separator' => $separator,
			'before'    => $before,
			'after'     => $after,
			'image'     => $image,
			'label'     => $label,
			'smallest'  => $smallest,
			'largest'   => $largest,
			'unit'      => $unit
		) );
	}
	
	/**
	 * Displays a formatted list of characters related to the current webcomic.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function short_webcomic_characters( $atts, $content = false ) {
		extract( shortcode_atts( array(
			'format'    => false,
			'separator' => false,
			'before'    => false,
			'after'     => false,
			'image'     => false,
			'label'     => false,
			'smallest'  => 8,
			'largest'   => 22,
			'unit'      => 'pt'
		), $atts ) );
		
		return $this->get_the_webcomic_post_terms( 'webcomic_character', array(
			'format'    => $format,
			'separator' => $separator,
			'before'    => $before,
			'after'     => $after,
			'image'     => $image,
			'label'     => $label,
			'smallest'  => $smallest,
			'largest'   => $largest,
			'unit'      => $unit
		) );
	}
	
	
	
	////
	// Hooks - Initialization
	// 
	// These functions hook into various WordPress
	// initialization actions and should never be
	// called directly.
	////
	
	/**
	 * 'init' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_init() {
		global $wp_rewrite;
		
		$this->domain();
		
		$rewrite = array();
		
		if ( is_object( $wp_rewrite ) && $wp_rewrite->using_permalinks() ) {
			$rewrite[ 'webcomic' ]   = array( 'slug' => apply_filters( 'webcomic_post_slug', 'archive' ) );
			$rewrite[ 'collection' ] = array( 'slug' => apply_filters( 'webcomic_collection_slug', 'collection' ) );
			$rewrite[ 'storyline' ]  = array( 'slug' => apply_filters( 'webcomic_storyline_slug', 'storyline' ) );
			$rewrite[ 'character' ]  = array( 'slug' => apply_filters( 'webcomic_character_slug', 'character' ) );
		} else
			$rewrite[ 'webcomic' ] = $rewrite[ 'collection' ] = $rewrite[ 'storyline' ] = $rewrite[ 'character' ] = false;
		
		register_post_type( 'webcomic_post', array( 'labels' => array( 'name' => __( 'Webcomics', 'webcomic' ), 'singular_name' => __( 'Webcomic', 'webcomic' ), 'add_new_item' => __( 'Add New Webcomic', 'webcomic' ), 'edit_item' => __( 'Edit Webcomic', 'webcomic' ), 'new_item' => __( 'New Webcomic', 'webcomic' ), 'view_item' => __( 'View Webcomic', 'webcomic' ), 'search_items' => __( 'Search Webcomics', 'webcomic' ), 'not_found' => __( 'No webcomics found', 'webcomic' ), 'not_found_in_trash' => __( 'No webcomics found in trash', 'webcomic' ) ), 'public' => true, 'show_ui' => true, 'rewrite' => $rewrite[ 'webcomic' ], 'query_var' => 'webcomic_post', 'taxonomies' => array( 'category', 'post_tag' ), 'menu_position' => 5, 'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions' ) ) );
		register_taxonomy( 'webcomic_collection', array( 'webcomic_post' ), array( 'labels' => array( 'name' => __( 'Collections', 'webcomic' ), 'singular_name' => __( 'Collection', 'webcomic' ), 'search_items' => __( 'Search Collections', 'webcomic' ), 'popular_items' => __( 'Popular Collections', 'webcomic' ), 'all_items' => __( 'All Collections', 'webcomic' ), 'parent_item' => __( 'Parent Collection', 'webcomic' ), 'parent_item_colon' => __( 'Parent Collection:', 'webcomic' ), 'edit_item' => __( 'Edit Collection', 'webcomic' ), 'update_item' => __( 'Update Collection', 'webcomic' ), 'add_new_item' => __( 'Add New Collection', 'webcomic' ), 'new_item_name' => __( 'New Collection Name', 'webcomic' ) ), 'hierarchical' => true, 'public' => true, 'show_ui' => false, 'rewrite' => $rewrite[ 'collection' ], 'query_var' => 'webcomic_collection', 'update_count_callback' => '_update_post_term_count' ) );
		register_taxonomy( 'webcomic_storyline', array( 'webcomic_post' ), array( 'labels' => array( 'name' => __( 'Storylines', 'webcomic' ), 'singular_name' => __( 'Storyline', 'webcomic' ), 'search_items' => __( 'Search Storylines', 'webcomic' ), 'popular_items' => __( 'Popular Storylines', 'webcomic' ), 'all_items' => __( 'Al Storylines', 'webcomic' ), 'parent_item' => __( 'Parent Storyline', 'webcomic' ), 'parent_item_colon' => __( 'Parent Storyline:', 'webcomic' ), 'edit_item' => __( 'Edit Storyline', 'webcomic' ), 'update_item' => __( 'Update Storyline', 'webcomic' ), 'add_new_item' => __( 'Add New Storyline', 'webcomic' ), 'new_item_name' => __( 'New Storyline Name', 'webcomic' ) ), 'hierarchical' => true, 'public' => true, 'show_ui' => false, 'rewrite' => $rewrite[ 'storyline' ], 'query_var' => 'webcomic_storyline', 'update_count_callback' => '_update_post_term_count' ) );
		register_taxonomy( 'webcomic_character', array( 'webcomic_post' ), array( 'labels' => array( 'name' => __( 'Characters', 'webcomic' ), 'singular_name' => __( 'Character', 'webcomic' ), 'search_items' => __( 'Search Characters', 'webcomic' ), 'popular_items' => __( 'Popular Characters', 'webcomic' ), 'all_items' => __( 'All Characters', 'webcomic' ), 'parent_item' => __( 'Parent Character', 'webcomic' ), 'parent_item_colon' => __( 'Parent Character:', 'webcomic' ), 'edit_item' => __( 'Edit Character', 'webcomic' ), 'update_item' => __( 'Update Character', 'webcomic' ), 'add_new_item' => __( 'Add New Character', 'webcomic' ), 'new_item_name' => __( 'New Character Name', 'webcomic' ) ), 'hierarchical' => true, 'public' => true, 'show_ui' => false, 'rewrite' => $rewrite[ 'character' ], 'query_var' => 'webcomic_character', 'update_count_callback' => '_update_post_term_count' ) );
		
		if ( get_option( 'webcomic_version' ) )
			register_taxonomy( 'chapter', array( 'post', 'webcomic_post' ), array( 'label' => __( 'Chapter', 'webcomic' ), 'hierarchical' => true, 'public' => true, 'show_ui' => false, 'update_count_callback' => '_update_post_term_count' ) );
		
		flush_rewrite_rules();
		
		wp_enqueue_script( 'swfobject', '', '', '', true );
		
		remove_filter( 'pre_term_description', 'wp_filter_kses' );
		
		if ( !empty( $_REQUEST[ 'webcomic_paypal_ipn' ] ) ) {
			global $wpdb;
			
			$id   = ( $wpdb->blog_id ) ? $wpdb->blog_id : 0;
			$text = '';
			$pass = true;
			$type = $_REQUEST[ 'webcomic_paypal_ipn' ];
			$req  = 'cmd=_notify-validate';
			$log  = ( $this->option( 'paypal_log' ) ) ? fopen( $this->dir . 'webcomic-includes/ipnlog-' . $id . '.txt', 'a' ) : false;
			
			foreach ( $_POST as $key => $value ) {
				$value = urlencode( stripslashes( $value ) );
				$req  .= "&$key=$value";
			}
			
			$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
			$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$header .= "Content-Length: " . strlen( $req ) . "\r\n\r\n";
			$fp      = fsockopen( 'ssl://www.paypal.com', 443, $errno, $errstr, 30 ); //ssl://www.sandbox.paypal.com
			
			$txn_id           = $_POST[ 'txn_id' ];
			$item_number      = ( $_POST[ 'num_cart_items' ] ) ? $_POST[ 'num_cart_items' ] . 'SC' : $_POST[ 'item_number' ];
			$payer_email      = $_POST[ 'payer_email' ];
			$receiver_email   = $_POST[ 'receiver_email' ];
			$payment_status   = $_POST[ 'payment_status' ];
			$payment_currency = $_POST[ 'mc_currency' ];
			
			$text = sprintf( "%s %s IPN %s: ", ucfirst( $type ), $item_number, $txn_id );
			
			if ( !$fp )
				$text .= __( "HTTP Error\n", "webcomic" );
			else {
				fputs( $fp, $header . $req );
				
				while ( !feof( $fp ) ) {
					$res  = fgets ( $fp, 1024 );
					$file = file_get_contents( $this->dir . 'webcomic-includes/ipnlog-' . $id . '.txt' );
					
					if ( 0 == strcmp( $res, "VERIFIED" ) ) {
						if ( 'Completed' != $payment_status ) {
							$text .= __( "Incomplete\n", "webcomic" );
							$pass = false;
						}
						
						if ( false !== strpos( $file, $txn_id ) ) {
							$text .= __( "Already processed\n", "webcomic" );
							$pass = false;
						}
						
						if ( strtolower( urldecode( $receiver_email ) ) != $this->option( 'paypal_business' ) ) {
							$text .= sprintf( __( "Bad Email: %s != %s \n", "webcomic" ), strtolower( urldecode( $receiver_email ) ), $this->option( 'paypal_business' ) );
							$pass = false;
						}
						
						if ( $payment_currency != $this->option( 'paypal_currency' ) ) {
							$text .= sprintf( __( "Bad Currency: %s != %s\n", "webcomic" ), $payment_currency, $this->option( 'paypal_currency' ) );
							$pass = false;
						}
						
						if ( $pass ) {
							$text .= sprintf( __( "Completed on %s\n", "webcomic" ), date( get_option( 'date_format' ) . ' @ ' . get_option( 'time_format' ) ) );
						
							if ( 'original' == $type ) {
								$post_meta = current( get_post_meta( ( int ) $item_number, 'webcomic' ) );
								$post_meta[ 'paypal' ][ 'original' ] = false;
								update_post_meta( ( int ) $item_number, 'webcomic', $post_meta );
							}
						}
					} elseif ( 0 == strcmp ( $res, "INVALID" ) )
						$text .= __( "Invalid\n", "webcomic" );
				}
				
				if ( $log ) {
					fwrite( $log, $text );
					fclose( $fp );
				}
				
				fclose( $log );
			}
		}
		
		if ( !empty( $_GET[ 'webcomic_object' ] ) ) {
			do_action( 'webcomic_secure_object' );
			
			$info    = explode( '/', $_GET[ 'webcomic_object' ] );
			$match   = ( 'post' == $info[ 0 ] ) ? true : false;
			$headers = ( function_exists( 'getallheaders' ) ) ? getallheaders() : false;
			
			if ( 'post' == $info[ 0 ] ) {
				$id = current( wp_get_object_terms( $info[ 1 ], 'webcomic_collection' ) );
				$wc = get_term( $id, 'webcomic_collection' );
			} elseif ( 'collection' == $info[ 0 ] )
				$wc = get_term( $info[ 1 ], 'webcomic_collection' );
			else {
				$term = get_term( $info[ 1 ], 'webcomic_' . $info[ 0 ] );
				$wc   = get_term( $term->term_group, 'webcomic_collection' );
			}
			
			if ( $files = $this->retrieve( $info[ 1 ], $info[ 0 ], $wc->slug, $match ) ) {
				if ( !$files[ $info[ 2 ] ][ $info[ 3 ] ] )
					return false;
				
				$img = $files[ $info[ 2 ] ][ $info[ 3 ] ][ 'dirname' ] .'/' . $files[ $info[ 2 ] ][ $info[ 3 ] ][ 'basename' ];
				
				if ( $headers && isset( $headers[ 'If-Modified-Since' ] ) && ( strtotime( $headers[ 'If-Modified-Since' ] ) == filemtime( $img ) ) )
					header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', filemtime( $img ) ) . ' GMT', true, 304 );
				else {
					header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', filemtime( $img ) ) . ' GMT', true, 200 );
					header( 'Content-Length: ' . filesize( $img ) );
					header( 'Content-Type: ' . $files[ $info[ 2 ] ][ $info[ 3 ] ][ 'mime' ] );
				}
				
				die( readfile( $img ) );
			}
		}
		
		if ( isset( $_REQUEST[ 'action' ] ) && 'verify_webcomic_age' == $_REQUEST[ 'action' ] ) {
			check_admin_referer( 'verify_webcomic_age' );
			
			do_action( 'webcomic_verify_age' );
			
			global $current_user;
			
			if ( $_REQUEST[ 'webcomic_birth_year' ] && $_REQUEST[ 'webcomic_birth_month' ] && $_REQUEST[ 'webcomic_birth_day' ] ) {
				$bday = strtotime( $_REQUEST[ 'webcomic_birth_year' ] . '/' . $_REQUEST[ 'webcomic_birth_month' ] . '/' . $_REQUEST[ 'webcomic_birth_day' ] );
				
				if ( $current_user->ID ) {
					$user_meta = current( get_user_meta( $current_user->ID, 'webcomic' ) );
					$user_meta[ 'birthday' ] = $bday;
					update_user_meta( $current_user->ID, 'webcomic', $user_meta );
				} else {
					$time = apply_filters( 'webcomic_verify_lifetime', 30000000 );
					setcookie( 'webcomic_birthday_' . COOKIEHASH, $bday, time() + $time, COOKIEPATH, COOKIE_DOMAIN );
				}
			}
		}
		
		if ( isset( $_REQUEST[ 'action' ] ) && 'submit_webcomic_transcript' == $_REQUEST[ 'action' ] ) {
			check_admin_referer( 'submit_webcomic_transcript' );
			
			do_action( 'webcomic_submit_transcript' );
			
			if ( !$_REQUEST[ hash( 'md5', 'webcomic_transcript_' . $_REQUEST[ 'webcomic_transcript_post' ] ) ] ) {
				$post_meta = current( get_post_meta( $_REQUEST[ 'webcomic_transcript_post' ], 'webcomic' ) );
				
				if ( !empty( $_REQUEST[ 'webcomic_ajax' ] ) ) {
					echo ( isset( $post_meta[ 'transcripts' ][ $_REQUEST[ 'webcomic_transcript_language' ] ][ 'text' ] ) ) ? wp_filter_kses( $post_meta[ 'transcripts' ][ $_REQUEST[ 'webcomic_transcript_language' ] ][ 'text' ] ) : '';
					
					die();
				}
				
				if ( isset( $post_meta[ 'transcripts' ][ $_REQUEST[ 'webcomic_transcript_language' ] ][ 'status' ] ) && ( 'publish' == $post_meta[ 'transcripts' ][ $_REQUEST[ 'webcomic_transcript_language' ] ][ 'status' ] || 'draft' == $post_meta[ 'transcripts' ][ $_REQUEST[ 'webcomic_transcript_language' ] ][ 'status' ] ) )
					wp_die( __( 'Error: no transcripts are being accepted for this webcomic right now.', 'webcomic' ) );
				elseif ( !$_REQUEST[ 'webcomic_transcript_text' ] )
					wp_die( __( 'Error: please type a transcript.', 'webcomic' ) );
				elseif ( 'selfid' == $this->option( 'transcribe_restrict' ) && !( $_REQUEST[ 'webcomic_transcript_author' ] || $_REQUEST[ 'webcomic_transcript_email' ] ) )
					wp_die( __( 'Error: all fields are required.', 'webcomic' ) );
				elseif ( 'selfid' == $this->option( 'transcribe_restrict' ) && !filter_var( $_REQUEST[ 'webcomic_transcript_email' ], FILTER_VALIDATE_EMAIL ) )
					wp_die( __( 'Error: a valid e-mail address is required.', 'webcomic' ) );
				else {
					$_REQUEST[ 'webcomic_transcript_status' ] = true;
					
					$languages  = $this->option( 'transcribe_language' );
					$default    = array_keys( $this->option( 'transcribe_default' ) );
					$lkey       = ( $_REQUEST[ 'webcomic_transcript_language' ] ) ? $_REQUEST[ 'webcomic_transcript_language' ] : $default[ 0 ];
					$author     = ( $_REQUEST[ 'webcomic_transcript_author' ] ) ? stripslashes( $_REQUEST[ 'webcomic_transcript_author' ] ) : __( 'Anonymous', 'webcomc' );
					$email      = ( $_REQUEST[ 'webcomic_transcript_email' ] ) ? $_REQUEST[ 'webcomic_transcript_email' ] : '';
					$title      = '<a href="' . get_permalink( $_REQUEST[ 'webcomic_transcript_post' ] ) . '">' . get_the_title( $_REQUEST[ 'webcomic_transcript_post' ] ) . '</a>';
					$transcript = "\n\n" . wpautop( wp_filter_kses( $_REQUEST[ 'webcomic_transcript_text' ] ) ) . "\n\n";
					$message    = sprintf( __( '%s has submitted a new transcript for %s in %s.%sYou can approve, edit, or delete this transcript by visiting: %s', 'webcomic' ), $author . ' (' . $email . ')', $title, $languages[ $lkey ], $transcript, admin_url( 'post.php?action=edit&post=' . $_REQUEST[ 'webcomic_transcript_post' ] ) );
					
					if ( !empty( $post_meta[ 'transcripts' ][ $lkey ][ 'text' ] ) ) {
						$post_meta[ 'transcripts' ][ $lkey ][ 'backup' ]        = $post_meta[ 'transcripts' ][ $lkey ][ 'text' ];
						$post_meta[ 'transcripts' ][ $lkey ][ 'backup_time' ]   = $post_meta[ 'transcripts' ][ $lkey ][ 'author' ];
						$post_meta[ 'transcripts' ][ $lkey ][ 'backup_author' ] = $post_meta[ 'transcripts' ][ $lkey ][ 'time' ];
					}
					
					$post_meta[ 'transcripts' ][ $lkey ][ 'language_code' ] = $lkey;
					$post_meta[ 'transcripts' ][ $lkey ][ 'language' ]      = $languages[ $lkey ];
					$post_meta[ 'transcripts' ][ $lkey ][ 'status' ]        = 'draft';
					$post_meta[ 'transcripts' ][ $lkey ][ 'author' ]        = $author;
					$post_meta[ 'transcripts' ][ $lkey ][ 'time' ]          = time();
					$post_meta[ 'transcripts' ][ $lkey ][ 'text' ]          = rtrim( $transcript );
					
					update_post_meta( $_REQUEST[ 'webcomic_transcript_post' ], 'webcomic', $post_meta );
					
					@wp_mail( get_option( 'admin_email' ), sprintf( __( '[%s] Transcript Submitted for %s', 'webcomic' ), html_entity_decode( get_option( 'blogname' ) ), html_entity_decode( $title ) ), $message );
				}
			}
		}
	}
	
	/**
	 * 'widgets_init' hook.
	 * 
	 * All widgets are defined in the
	 * /webcomic-includes/widgets.php file.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_widgets_init() {
		register_widget( 'webcomic_Widget_Buffer' );
		register_widget( 'webcomic_Widget_Donation' );
		register_widget( 'webcomic_Widget_Relative' );
		register_widget( 'webcomic_Widget_Bookmark' );
		register_widget( 'webcomic_Widget_Characters' );
		register_widget( 'webcomic_Widget_Storylines' );
		register_widget( 'webcomic_Widget_Collections' );
		register_widget( 'webcomic_Widget_Archive' );
	}
	
	/**
	 * 'template_redirect' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_template_redirect() {
		global $post;
		
		wp_enqueue_script( 'webcomic-scripts', $this->url . 'webcomic-includes/scripts.js', array( 'jquery', 'jquery-hotkeys' ), '', true );
		
		if ( ( $wc = $this->get_collection_by_path() ) && !empty( $wc->webcomic_theme ) && is_dir( get_theme_root() . '/' . $wc->webcomic_theme ) ) {
			global $webcomic_theme;
			
			$webcomic_theme = $wc->webcomic_theme;
			
			add_filter( 'template', create_function( '', 'global $webcomic_theme; return $webcomic_theme;' ) );
			add_filter( 'stylesheet', create_function( '', 'global $webcomic_theme; return $webcomic_theme;' ) );
		}
		
		if ( !$this->verify( 'restrict' ) )
			auth_redirect();
		
		if ( !$this->verify( 'age' ) ) {
			if ( isset( $_REQUEST[ 'action' ] ) && 'verify_webcomic_age' == $_REQUEST[ 'action' ] && $_REQUEST[ 'redirect' ] ) {
				unset( $_REQUEST[ 'action' ] );
				wp_redirect( $_REQUEST[ 'redirect' ] );
			}
				
			$v = apply_filters( 'webcomic_verify_age_template', 'webcomic_verifyage.php' );
			$f = apply_filters( 'webcomic_verify_fail_template', 'webcomic_verifyfail.php' );
			
			$form = ( locate_template( array( $v ) ) ) ? locate_template( array( $v ) ) : $this->dir . 'webcomic-includes/template-verify-age.php';
			$fail = ( locate_template( array( $f ) ) ) ? locate_template( array( $f ) ) : $this->dir . 'webcomic-includes/template-verify-fail.php';
			
			if ( !$this->age() ) {
				require_once( $form );
				die();
			} elseif ( !$this->verify( 'age' ) ) {
				require_once( $fail );
				die();
			}
		}
		
		if ( $this->option( 'paypal_business' ) && !empty( $_REQUEST[ 'purchase_webcomic_print' ] ) ) {
			$v = apply_filters( 'webcomic_purchase_print_template', 'webcomic_purchaseprint.php' );
			
			if ( locate_template( array( $v ) ) )
				$p = locate_template( array( $v ) );
			else
				$p = $this->dir . 'webcomic-includes/template-purchase-print.php';
			
			require_once( $p );
			die();
		}
		
		if ( !empty( $_REQUEST[ 'relative_webcomic' ] ) ) {
			global $wpdb;
			
			$a = explode( '/', $_REQUEST[ 'relative_webcomic' ] );
			
			if ( $url = $this->get_relative_webcomic_url( $a[ 0 ], $a[ 1 ], explode( ',', $a[ 2 ] ), $a[ 3 ] ) )
				wp_redirect( $url );
		}
		
		if ( !empty( $_REQUEST[ 'bookmark_webcomic' ] ) ) {
			$a    = explode( '/', $_REQUEST[ 'bookmark_webcomic' ] );
			$time = ( 'bookmark' == $a[ 1 ] ) ? apply_filters( 'webcomic_bookmark_lifetime', 30000000 ) : -1;
			
			if ( 'bookmark' == $a[ 1 ] )
				setcookie( 'webcomic_bookmark_' . $a[ 2 ], get_permalink( $a[ 0 ] ), time() + $time, COOKIEPATH, COOKIE_DOMAIN );
			elseif ( 'remove' == $a[ 1 ] )
				setcookie( 'webcomic_bookmark_' . $a[ 2 ], get_permalink( $a[ 0 ] ), time() + $time, COOKIEPATH, COOKIE_DOMAIN );
			elseif ( isset( $_COOKIE[ 'webcomic_bookmark_' . $a[ 2 ] ] ) )
				wp_redirect( $_COOKIE[ 'webcomic_bookmark_' . $a[ 2 ] ] );
			else
				return false;
		}
	}
	
	
	
	////
	// Hooks - Posts
	// 
	// These functions hook into various WordPress
	// post actions and should never be called
	// directly.
	////
	
	/**
	 * 'body_class' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_body_class( $classes, $class ) {
		global $post;
		
		if ( is_tax( 'webcomic_collection' ) )
			$classes[] = 'webcomic_collection webcomic_collection-' . get_query_var( 'webcomic_collection' );
		
		if ( is_tax( 'webcomic_storyline' ) ) {
			$st = get_term_by( 'slug', get_query_var( 'webcomic_storyline' ), 'webcomic_storyline' );
			$wc = get_term( $st->term_group, 'webcomic_collection' );
			$classes[] = 'webcomic_collection-' . $wc->slug;
			$classes[] = 'webcomic_storyline webcomic_storyline-' . get_query_var( 'webcomic_storyline' );
		}
		
		if ( is_tax( 'webcomic_character' ) ) {
			$st = get_term_by( 'slug', get_query_var( 'webcomic_character' ), 'webcomic_character' );
			$wc = get_term( $st->term_group, 'webcomic_collection' );
			$classes[] = 'webcomic_collection-' . $wc->slug;
			$classes[] = 'webcomic_character webcomic_character-' . get_query_var( 'webcomic_character' );
		}
				
		if ( ( is_singular( 'webcomic_post' ) || is_page() ) ) {
			if ( $wc = current( wp_get_object_terms( $post->ID, 'webcomic_collection' ) ))
				$classes[] = 'webcomic_collection-' . sanitize_html_class( $wc->slug, $wc->term_id );
			elseif ( $id = current( get_post_meta( $post->ID, 'webcomic_collection' ) ) ) {
				$wc = get_term( $id, 'webcomic_collection' );
				$classes[] = 'webcomic_collection-' . sanitize_html_class( $wc->slug, $wc->term_id );
			}
			
			
			if ( isset( $_REQUEST[ 'purchase_webcomic_print' ] ) )
				$classes[] = 'webcomic-purchase';
			
			if ( !$this->verify( 'age' ) )
				$classes[] = ( $this->age() ) ? 'webcomic-verify-fail' : 'webcomic-verify-age';
			
			if ( !$this->verify( 'restrict' ) )
				$classes[] = 'webcomic-restricted';
		}
		
		return $classes;
	}
	
	/**
	 * 'post_class' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_post_class( $classes, $class, $id ) {
		if ( in_array( 'type-webcomic_post', $classes ) && ( $wc = current( wp_get_object_terms( $id, 'webcomic_collection' ) ) ) ) {
			$classes[] = 'webcomic_collection-' . sanitize_html_class( $wc->slug, $wc->term_id );
			
			if ( $storylines = wp_get_object_terms( $id, 'webcomic_storyline' ) )
				foreach ( $storylines as $storyline )
					$classes[] = 'webcomic_storyline-' . sanitize_html_class( $storyline->slug, $storyline->term_id );
			
			if ( $characters = wp_get_object_terms( $id, 'webcomic_character' ) )
				foreach ( $characters as $character )
					$classes[] = 'webcomic_character-' . sanitize_html_class( $character->slug, $character->term_id );
			
			if ( !$this->verify( 'age' ) )
				$classes[] = ( $this->age() ) ? 'webcomic-verify-fail' : 'webcomic-verify-age';
			
			if ( !$this->verify( 'restrict' ) )
				$classes[] = 'webcomic-restricted';
		}
		
		return $classes;
	}
	
	/**
	 * 'the_post' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_the_post( $post ) {
		if ( !$post || 'webcomic_post' != $post->post_type )
			return $post;
		
		$wc = current( wp_get_object_terms( $post->ID, 'webcomic_collection' ) );
		$post_meta = current( get_post_meta( $post->ID, 'webcomic' ) );
		
		$post->webcomic_files             = ( is_object( $wc ) ) ? $this->retrieve( $post->ID, 'post', $wc->slug, true ) : NULL;
		$post->webcomic_alternate         = ( isset( $post_meta[ 'alternate' ] ) ) ? $post_meta[ 'alternate' ] : NULL;
		$post->webcomic_description       = ( isset( $post_meta[ 'description' ] ) ) ? $post_meta[ 'description' ] : NULL;
		$post->webcomic_transcribe_toggle = ( isset( $post_meta[ 'transcribe_toggle' ] ) ) ? $post_meta[ 'transcribe_toggle' ] : NULL;
		$post->webcomic_transcripts       = ( isset( $post_meta[ 'transcripts' ] ) ) ? $post_meta[ 'transcripts' ] : NULL;
		$post->webcomic_paypal            = ( isset( $post_meta[ 'paypal' ] ) ) ? $post_meta[ 'paypal' ] : NULL;
		
		return $post;
	}
	
	/**
	 * 'the_posts' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_the_posts( $posts ) {
		foreach ( $posts as $post ) {
			if ( 'webcomic_post' != $post->post_type )
				continue;
			
			$wc = current( wp_get_object_terms( $post->ID, 'webcomic_collection' ) );
			$post_meta = current( get_post_meta( $post->ID, 'webcomic' ) );
			
			$post->webcomic_files             = ( is_object( $wc ) ) ? $this->retrieve( $post->ID, 'post', $wc->slug, true ) : NULL;
			$post->webcomic_alternate         = ( isset( $post_meta[ 'alternate' ] ) ) ? $post_meta[ 'alternate' ] : NULL;
			$post->webcomic_description       = ( isset( $post_meta[ 'description' ] ) ) ? $post_meta[ 'description' ] : NULL;
			$post->webcomic_transcribe_toggle = ( isset( $post_meta[ 'transcribe_toggle' ] ) ) ? $post_meta[ 'transcribe_toggle' ] : NULL;
			$post->webcomic_transcripts       = ( isset( $post_meta[ 'transcripts' ] ) ) ? $post_meta[ 'transcripts' ] : NULL;
			$post->webcomic_paypal            = ( isset( $post_meta[ 'paypal' ] ) ) ? $post_meta[ 'paypal' ] : NULL;
		}
		
		return $posts;
	}
	
	/**
	 * 'loop_start' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_loop_start() {
		if ( !$this->option( 'integrate_toggle' ) )
			return false;
		
		global $post;
		
		if ( is_home() || is_front_page() ) {
			$webcomics = new WP_Query( 'post_type=webcomic_post&posts_per_page=1' ); if ( $webcomics->posts ) { $_post = $post; foreach ( $webcomics->posts as $p ) {
				$post = $p;
				?>
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'webcomic' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
					<div class="entry-meta">
						<?php
							printf( __( '<span class="meta-prep meta-prep-author">Posted on </span><a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a> <span class="meta-sep"> by </span> <span class="author vcard"><a class="url fn n" href="%4$s" title="%5$s">%6$s</a></span>', 'webcomic' ),
								get_permalink(),
								esc_attr( get_the_time() ),
								get_the_date(),
								get_author_posts_url( get_the_author_meta( 'ID' ) ),
								sprintf( esc_attr__( 'View all posts by %s', 'webcomic' ), get_the_author() ),
								get_the_author()
							);
						?>
					</div><!-- .entry-meta -->
					
					<div class="entry-content">
						<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'webcomic' ) ); ?>
						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'webcomic' ), 'after' => '</div>' ) ); ?>
					</div><!-- .entry-content -->
		
					<div class="entry-utility">
						<span class="collection-links"><?php echo $this->get_the_webcomic_terms( 'webcomic_collection', array( 'before' => __( 'From ', 'webcomic' ), 'separator' => ', ', 'after' => ' | ' ) ); ?></span>
						<span class="storyline-links"><?php echo $this->get_the_webcomic_terms( 'webcomic_storyline', array( 'before' => __( 'Part of ', 'webcomic' ), 'separator' => ', ', 'after' => ' | ' ) ); ?></span>
						<span class="character-links"><?php echo $this->get_the_webcomic_terms( 'webcomic_character', array( 'before' => __( 'Featuring ', 'webcomic' ), 'separator' => ', ', 'after' => ' | ' ) ); ?></span>
						<span class="cat-links"><span class="entry-utility-prep entry-utility-prep-cat-links"><?php _e( 'Posted in ', 'webcomic' ); ?></span><?php the_category( ', ' ); ?></span>
						<span class="meta-sep"> | </span>
						<?php the_tags( '<span class="tag-links"><span class="entry-utility-prep entry-utility-prep-tag-links">' . __( 'Tagged ', 'webcomic' ) . '</span>', ', ', '<span class="meta-sep"> | </span>' ); ?>
						<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'webcomic' ), __( '1 Comment', 'webcomic' ), __( '% Comments', 'webcomic' ) ); ?></span>
						<?php edit_post_link( __( 'Edit', 'webcomic' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
					</div><!-- #entry-utility -->
				</div><!-- #post-<?php the_ID(); ?> --><hr>
				<?php
			} $post = $_post; }
		}
	}
	
	/**
	 * 'the_content' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_the_content( $content ) {
		$this->domain();
		
		global $post;
		
		if ( 'webcomic_post' == get_post_type( $post ) ) {
			$wc = current( wp_get_object_terms( $post->ID, 'webcomic_collection' ) );
			$ok = true;
			
			if ( !$this->verify( 'restrict' ) ) {
				$content = apply_filters( 'webcomic_content_restrict', sprintf( __( 'Please <a href="%s">login</a> to view this webcomic.', 'webcomic' ), get_permalink( $post->ID ) ), $post, $wc );
				$ok = false;
			} elseif ( !$this->verify( 'age' ) ) {
				if ( !$this->age() ) {
					$content = apply_filters( 'webcomic_content_verify', sprintf( __( 'Please <a href="%s">verify your age</a> to view this webcomic.', 'webcomic' ), get_permalink( $post->ID ) ), $post, $wc );
					$ok = false;
				} else {
					$content = apply_filters( 'webcomic_content_verify_fail', sprintf( __( 'You must be at least %d years old to view this webcomic.', 'webcomic' ), $wc->term_group ), $post, $wc );
					$ok = false;
				}
			}
			
			if ( $this->option( 'feed_toggle' ) && is_feed() && $ok ) {
				$sz = $this->option( 'feed_size' );
				
				if ( $files = $this->retrieve( $post->ID, 'post', $wc->slug, true ) ) {
					foreach ( $files[ 'full' ] as $k => $v ) {
						$file = ( isset( $files[ $sz ][ $k ] ) ) ? $files[ $sz ][ $k ] : $v;
						$img .= ( $this->option( 'secure_toggle' ) ) ? $file[ 'html' ] : $file[ 'shtml' ];
					}
					
					$content = '<p><a href="' . get_permalink( $post->ID ) . '">' . $img . '</a></p>' . $content;
				}
			} elseif ( $this->option( 'integrate_toggle' ) && !is_feed() && !is_trackback() && !is_admin() && $this->in_webcomic_term( 'webcomic_collection' ) && $ok ) {
				if ( is_home() || is_front_page() || ( is_single() && $this->in_webcomic_term( 'webcomic_collection' ) ) ) {
					?>
					<div class="navigation">
						<div class="nav-previous"><?php echo $this->get_relative_webcomic_link( 'first', '%link', '%label' ); ?> | <?php echo $this->get_relative_webcomic_link( 'previous', '%link', '%label' ); ?></div>
						<div class="nav-next"><?php echo $this->get_relative_webcomic_link( 'next', '%link', '%label' ); ?> | <?php echo $this->get_relative_webcomic_link( 'last', '%link', '%label' ); ?></div>
					</div>
					<?php echo $this->get_the_webcomic_object( 'full' ); ?>
					<div class="navigation">
						<div class="nav-previous"><?php echo $this->get_relative_webcomic_link( 'first', '%link', '%label' ); ?> | <?php echo $this->get_relative_webcomic_link( 'previous', '%link', '%label' ); ?></div>
						<div class="nav-next"><?php echo $this->get_relative_webcomic_link( 'next', '%link', '%label' ); ?> | <?php echo $this->get_relative_webcomic_link( 'last', '%link', '%label' ); ?></div>
					</div>
					<?php
					
					if ( is_home() || is_front_page() && !$content )
						$content = $post->post_content;
				} elseif ( is_archive() && $this->in_webcomic_term( 'webcomic_collection' ) ) {
					echo $this->get_the_webcomic_object( 'small' );
				}
			}
		}
		
		return $content;
	}
	
	/**
	 *'request' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_request( $q ) {
		if ( isset( $q[ 'feed' ] ) && !isset( $q[ 'post_type' ] ) )
			$q[ 'post_type' ] = array( 'post', 'webcomic_post' );
		
		return $q;
	}
	
	/**
	 * 'posts_request' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_posts_request( $query ) {
		global $wp_query;
		
		if ( $wp_query->is_search && false === strpos( $query, 'DISTINCT' ) )
			$query = str_replace( 'SELECT', 'SELECT DISTINCT', $query );
		
		return $query;
	}
	
	/**
	 * 'posts_join' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_posts_join( $join ) {
		global $wp_query, $wpdb;
		
		if ( $wp_query->is_search )
			$join .= " LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id ";
		
		return $join;
	}
	
	/**
	 * 'posts_where' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_posts_where( $where ) {
		global $wp_query, $wpdb;
		
		if ( $wp_query->is_archive )
			$where = str_replace( "post_type = 'post'", "post_type IN ('post','webcomic_post')", $where );
		
		if ( $wp_query->is_search ) {
			$query_terms = explode( ' ', $wp_query->query_vars[ 's' ] );
			
			$i  = 0;
			$or = '(';
			
			foreach ( $query_terms as $query_term ) {
				if ( $query_term !== '' ) {
					$or .= "(($wpdb->posts.post_title LIKE '%" . $wpdb->escape( $query_term ) . "%') OR ($wpdb->posts.post_content LIKE '%" . $wpdb->escape( $query_term ) . "%') OR (($wpdb->postmeta.meta_key = 'webcomic') AND $wpdb->postmeta.meta_value LIKE '%" . $wpdb->escape( $query_term ) . "%')) OR ";
					$i++;
				}
			}
			
			if ( $i > 1 )
				$or .= "(($wpdb->posts.post_title LIKE '" . $wpdb->escape( $wp_query->query_vars[ 's' ] ) . "') OR ($wpdb->posts.post_content LIKE '" . $wpdb->escape( $wp_query->query_vars[ 's' ] ) . "') OR (($wpdb->postmeta.meta_key = 'webcomic') AND $wpdb->postmeta.meta_value LIKE '%" . $wpdb->escape( $wp_query->query_vars[ 's' ] ) . "%')))";
			else
				$or = rtrim( $or, ' OR ') . ')';
			
			$where = preg_replace( "/\(\(\(.*\)\)/i", $or, $where, 1 );
		}
		
		return $where;
	}
	
	
	
	////
	// Hooks - Taxonomy
	// 
	// These functions hook into various WordPress
	// taxonomy actions and should never be called
	// directly.
	////
	
	/**
	 * 'list_terms_exclusions' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_list_terms_exclusions( $exclusions, $args ) {
		global $wpdb;
		
		if ( !empty( $args[ 'term_group' ] ) ) {
			if ( !empty( $args[ 'require_group' ] ) ) {
				$term_groups = "'" . implode( "','", preg_split( '/[\s,]+/', $args[ 'term_group' ] ) ) . "'";
				$exclusions .= " AND t.term_group IN ($term_groups) ";
			} else {
				$term_groups = preg_split( '/[\s,]+/', $args[ 'term_group' ] );
				
				foreach ( ( array ) $term_groups as $term_group ) {
					if ( $term_groups[ 0 ] == $term_group )
						$exclusions .= " AND (t.term_group = $term_group";
					else
						$exclusions .= " OR t.term_group = $term_group";
				}
				
				$exclusions .= ') ';
			}
		}
		
		$taxonomies = array();
		
		if ( !empty( $args[ 'webcomic_storyline' ] ) ) $taxonomies[] = 'webcomic_storyline';
		if ( !empty( $args[ 'webcomic_character' ] ) ) $taxonomies[] = 'webcomic_character';
		
		if ( !empty( $taxonomies ) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$terms   = preg_split( '/[\s,]+/', $args[ $taxonomy ] );
				$include = $exclude = array();
				
				foreach ( ( array ) $terms as $term ) {
					if ( $term > 0 )
						$include[] = $term;
					else
						$exclude[] = abs( $term );
				}
				
				if ( $args[ 'webcomic_require' ] ) {
					if ( $include ) {
						$terms       = "'" . implode( "','", $include ) . "'";
						$exclusions .= " AND t.term_id IN (SELECT tr.object_id FROM $wpdb->term_relationships AS tr INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy IN ('$taxonomy') AND tt.term_id IN ($terms)) ";
					} if ( $exclude ) {
						$terms       = "'" . implode( "','", $exclude ) . "'";
						$exclusions .= " AND t.term_id NOT IN (SELECT tr.object_id FROM $wpdb->term_relationships AS tr INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy IN ('$taxonomy') AND tt.term_id IN ($terms)) ";
					}
				} else {
					if ( $include ) {
						$exclusions .= " AND (t.term_id IN (SELECT tr.object_id FROM $wpdb->term_relationships AS tr INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy IN ('$taxonomy')";
					
						foreach ( $include as $term ) {
							if ( $include[ 0 ] == $term )
								$exclusions .= " AND tt.term_id = $term";
							else
								$exclusions .= " OR tt.term_id = $term";
						}
					
						$exclusions .= ')) ';
					} if ( $exclude ) {
						$exclusions .= " AND (t.term_id NOT IN (SELECT tr.object_id FROM $wpdb->term_relationships AS tr INNER JOIN $wpdb->term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id WHERE tt.taxonomy IN ('$taxonomy')";
					
						foreach ( ( array ) $exclude as $term ) {
							if ( $exclude[ 0 ] == $term )
								$exclusions .= " AND tt.term_id = $term";
							else
								$exclusions .= " OR tt.term_id = $term";
						}
					
						$exclusions .= ')) ';
					}
				}
			}
		}
		
		return $exclusions;
	}
	
	/**
	 * 'get_terms_orderby' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_get_terms_orderby( $orderby, $args ) {
		$orderby = ( 'term_group_name' == $args[ 'orderby' ] ) ? 't.term_group,t.name' : $orderby;
		return $orderby;
	}
	
	/**
	 * 'get_term' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_get_term( $term, $taxonomy ) {
		if ( 'webcomic_collection' == $taxonomy || 'webcomic_storyline' == $taxonomy || 'webcomic_character' == $taxonomy ) {
			$term_meta = $this->option( 'term_meta' );
			$type = end( explode( '_', $taxonomy ) );
			$key  = ( 'webcomic_collection' == $taxonomy ) ? $term->term_id : $term->term_group;
			
			if ( isset( $term_meta[ 'collection' ][ $key ] ) )
				$term->webcomic_files = $this->retrieve( $term->term_id, $type, $term_meta[ 'collection' ][ $key ][ 'slug' ] );
			
			if ( 'webcomic_collection' == $taxonomy )
				$term->webcomic_default = ( $term->term_id == $this->option( 'default_collection' ) ) ? true : false;
			else
				$term->webcomic_default = $term_meta[ $type ][ $term->term_id ][ 'default' ];
			
			if ( 'webcomic_collection' == $taxonomy ) {
				$term->webcomic_theme    = ( isset( $term_meta[ $type ][ $term->term_id ][ 'theme' ] ) ) ? $term_meta[ $type ][ $term->term_id ][ 'theme' ] : false;
				$term->webcomic_bookend  = ( isset( $term_meta[ $type ][ $term->term_id ][ 'bookend' ] ) ) ? $term_meta[ $type ][ $term->term_id ][ 'bookend' ] : false;
				$term->webcomic_restrict = ( isset( $term_meta[ $type ][ $term->term_id ][ 'restrict' ] ) ) ? $term_meta[ $type ][ $term->term_id ][ 'restrict' ] : false;
				$term->webcomic_paypal   = ( isset( $term_meta[ $type ][ $term->term_id ][ 'payapl' ] ) ) ? $term_meta[ $type ][ $term->term_id ][ 'paypal' ] : false;
			} elseif ( 'webcomic_storyline' == $taxonomy )
				$term->webcomic_order = $term_meta[ $type ][ $term->term_id ][ 'order' ];
		}
		
		return $term;
	}
	
	/**
	 * 'get_terms' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_get_terms( $terms, $taxonomies, $args ) {
		if ( in_array( 'webcomic_collection', $taxonomies ) || in_array( 'webcomic_storyline', $taxonomies ) || in_array( 'webcomic_character', $taxonomies ) ) {
			$term_meta = $this->option( 'term_meta' );
			
			foreach ( $terms as $term ) {
				if ( !is_object( $term ) || !( 'webcomic_collection' == $term->taxonomy || 'webcomic_storyline' == $term->taxonomy || 'webcomic_character' == $term->taxonomy ) )
					continue;
				
				$type = end( explode( '_', $term->taxonomy ) );
				$key  = ( 'collection' == $type ) ? $term->term_id : $term->term_group;
				
				$term->webcomic_files   = $this->retrieve( $term->term_id, $type, $term_meta[ 'collection' ][ $key ][ 'slug' ] );
				
				if ( 'webcomic_collection' == $term->taxonomy )
					$term->webcomic_default = ( $term->term_id == $this->option( 'default_collection' ) ) ? true : false;
				else
					$term->webcomic_default = $term_meta[ $type ][ $term->term_id ][ 'default' ];
				
				if ( 'collection' == $type ) {
					$term->webcomic_theme    = $term_meta[ $type ][ $term->term_id ][ 'theme' ];
					$term->webcomic_bookend  = $term_meta[ $type ][ $term->term_id ][ 'bookend' ];
					$term->webcomic_restrict = $term_meta[ $type ][ $term->term_id ][ 'restrict' ];
					$term->webcomic_paypal   = $term_meta[ $type ][ $term->term_id ][ 'paypal' ];
				} elseif ( 'storyline' == $type )
					$term->webcomic_order = $term_meta[ 'storyline' ][ $term->term_id ][ 'order' ];
			}
			
			if ( in_array( 'webcomic_storyline', $taxonomies ) && !empty( $args[ 'webcomic_order' ] ) )
				usort( $terms, array( &$this, 'usort_storylines' ) );
		}
		
		return $terms;
	}
	
	/**
	 * 'wp_get_object_terms' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_wp_get_object_terms( $terms, $object_ids, $taxonomies, $args ) {
		if ( 'all' == $args[ 'fields' ] || 'all_with_object_id' == $args[ 'fields' ] ) {
			$term_meta = $this->option( 'term_meta' );
			
			foreach ( $terms as $term ) {
				if ( !( 'webcomic_collection' == $term->taxonomy || 'webcomic_storyline' == $term->taxonomy || 'webcomic_character' == $term->taxonomy ) )
					continue;
				
				$type = end( explode( '_', $term->taxonomy ) );
				$key  = ( 'collection' == $type ) ? $term->term_id : $term->term_group;
				
				$term->webcomic_files = $this->retrieve( $term->term_id, $type, $term_meta[ 'collection' ][ $key ][ 'slug' ] );
			
				if ( 'webcomic_collection' == $term->taxonomy )
					$term->webcomic_default = ( $term->term_id == $this->option( 'default_collection' ) ) ? true : false;
				else
					$term->webcomic_default = $term_meta[ $type ][ $term->term_id ][ 'default' ];
				
				if ( 'collection' == $type ) {
					$term->webcomic_theme    = $term_meta[ $type ][ $term->term_id ][ 'theme' ];
					$term->webcomic_bookend  = $term_meta[ $type ][ $term->term_id ][ 'bookend' ];
					$term->webcomic_restrict = $term_meta[ $type ][ $term->term_id ][ 'restrict' ];
					$term->webcomic_paypal   = $term_meta[ $type ][ $term->term_id ][ 'paypal' ];
				} elseif ( 'storyline' == $type )
					$term->webcomic_order = $term_meta[ 'storyline' ][ $term->term_id ][ 'order' ];
			}
		}
		
		return $terms;
	}
	
	
	
	////
	// Hooks - Webcomic
	// 
	// These functions hook into special Webcomic
	// defined actions and should never be called
	// directly.
	////
	
	/**
	 * 'webcomic_buffer_alert' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_webcomic_buffer_alert() {
		if ( !$this->option( 'buffer_toggle' ) )
			return false;
		
		$this->domain();
		
		global $wpdb;
		
		$now   = time();
		$terms = get_terms( 'webcomic_collection', 'get=all' );
		
		foreach ( $terms as $term ) {
			$ids = get_objects_in_term( $term->term_id, 'webcomic_collection' );
			
			if ( $ids && !is_wp_error( $ids ) && $post = $wpdb->get_var( "SELECT post_date FROM $wpdb->posts WHERE post_status = 'future' AND ID IN (" . implode( ',', $ids ) . ") ORDER BY post_date DESC" ) ) {
				$eta = floor( ( strtotime( $post ) - $now ) / 86400 );
				
				if ( $eta && $eta <= $this->option( 'buffer_size' ) )
					wp_mail( get_option( 'admin_email' ), sprintf( _n( '[%s] Buffer Alert for %s - Only %d Day Left', '[%s] Buffer Alert for %s - Only %d Days Left', $eta, 'webcomic' ), html_entity_decode( get_option( 'blogname' ) ), html_entity_decode( $term->name ), $eta ), sprintf( __( "This is an automated reminder that your buffer for %s will run out on %s ($d days from now).\n\nYou can disable these automatic reminders from the <a href='%s'>Webcomic Settings</a> page.", "webcomic" ), html_entity_decode( $term->name ), get_the_time( 'j F y', strtotime( $post ) ), $eta, admin_url( 'admin.php?page=webcomic_settings' ) ) );
			}
		}
	}
	
	
	
	////OK
	// Utilities
	// 
	// These functions are designed for internal use
	// and should never be called directly.
	////
	
	/**
	 * Sorts storylines by the user-defined order.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function usort_storylines( $a, $b ) {
		if ( $a->webcomic_order > $b->webcomic_order )
			return 1;
		elseif ( $a->webcomic_order < $b->webcomic_order )
			return -1;
		else
			return 0;
	}
	
	/**
	 * Sorts terms by term_group.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function usort_terms_by_collection( $a, $b ) {
		if ( $a->term_group > $b->term_group )
			return 1;
		elseif ( $a->term_group < $b->term_group )
			return -1;
		else
			return 0;
	}
	
	/**
	 * Sorts term object ID's by date.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function usort_term_objects_by_date( $a, $b ) {
		$a = get_the_time( 'U', $a );
		$b = get_the_time( 'U', $b );
		
		if ( $a > $b )
			return 1;
		elseif ( $a < $b )
			return -1;
		else
			return 0;
	}
	
	/**
	 * Retrieves path or URI to the specified file directory.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $type One of 'abs' or 'url'.
	 * @param str $sub Name of the subdirectory to retrieve.
	 */
	function directory( $type = null, $sub = null ) {
		switch ( $type ) {
			case 'abs': if ( $sub ) return $this->cdir . 'webcomic/' . $sub . '/'; else return $this->cdir . 'webcomic/';
			case 'url': if ( $sub ) return $this->curl . 'webcomic/' . $sub . '/'; else return $this->curl . 'webcomic/';
			default: return false;
		}
	}
	
	/**
	 * Determines the age (in years) of the current user.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function age() {
		global $current_user;
		
		$user_meta = ( is_array( $a = get_user_meta( $current_user->ID, 'webcomic' ) ) ) ? current( $a ) : array();
		
		if ( isset( $user_meta[ 'birthday' ] ) )
			return ( time() - $user_meta[ 'birthday' ] ) / 31556926;
		elseif ( isset( $_COOKIE[ 'webcomic_birthday_' . COOKIEHASH ] ) )
			return ( time() - $_COOKIE[ 'webcomic_birthday_' . COOKIEHASH ] ) / 31556926;
		else
			return false;
	}
	/**
	 * Verifies that the current user can access webcomic content.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $type One of 'age' or 'restrict'.
	 * @param str $id The post ID to get verification info from.
	 * @return True if the user is verified, false otherwise.
	 */
	function verify( $type = false, $id = false ) {
		global $post;
		
		$collection = $storyline = $character = false;
		
		if ( !$post && !$id && !( ( $collection = get_query_var( 'webcomic_collection' ) ) || !( $storyline = get_query_var( 'webcomic_storyline' ) ) || !( $character = get_query_var( 'webcomic_character' ) ) ) || ( 'age' == $type && !$this->option( 'age_toggle' ) ) || ( 'restrict' == $type && is_user_logged_in() ) )
			return true;
		
		if ( $id )
			$wc = current( wp_get_object_terms( $id, 'webcomic_collection' ) );
		elseif ( $post )
			$wc = current( wp_get_object_terms( $post->ID, 'webcomic_collection' ) );
		elseif ( $collection )
			$wc = get_term_by( 'slug', $collection, 'webcomic_collection' );
		elseif ( $storyline && ( $t = get_term_by( 'slug', $collection, 'webcomic_storyline' ) ) )
			$wc = get_term( $t->term_group, 'webcomic_collection' );
		elseif ( $character && ( $t = get_term_by( 'slug', $collection, 'webcomic_character' ) ) )
			$wc = get_term( $t->term_group, 'webcomic_collection' );
		else
			return true;
		
		if ( empty( $wc ) )
			return true;
		
		$age = ( !$this->option( 'age_toggle' ) || $wc->term_group < $this->option( 'age_size' ) || $this->age() > $wc->term_group ) ? true : false;
		$restrict = ( $wc->webcomic_restrict && !is_user_logged_in() ) ? false : true;
		
		if ( 'age' == $type )
			return $age;
		elseif ( 'restrict' == $type )
			return $restrict;
		elseif ( $age && $restrict )
			return true;
		else
			return false;
	}
	
	/**
	 * Calculates a price based on input.
	 * 
	 * Given a $base, this function iterates through specified
	 * amounts and adds or subtracts their value (based on $rate
	 * and $type). Discount amounts that would reduce the final
	 * price to zero or less are ignored.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param float $base The starting amount. Cannot be negative or zero.
	 * @param arr $amount An array of float amounts that will adjust the $base.
	 * @param arr $rate An array of str rates, one for each amount. Must be 'fixed' or 'percent'.
	 * @return float|bool The final price, rounded to two decimal places, or false on error.
	 */
	function price( $base, $amount, $rate = false ) {
		$output = abs( floatval( $base ) );
		$m = $c = 0;
		
		if ( !$output )
			return false;
		
		foreach ( $amount as $k => $v ) {
			if ( !$v )
				continue;
			
			$r = ( isset( $rate[ $k ] ) ) ? $rate[ $k ] : 'percent';
			$m = ( 'fixed' == $r ) ? abs( $v ) : $output * ( abs( $v ) / 100 );
			$c = ( $v < 0 ) ? $output - $m : $output + $m;
			
			if ( $c <= .01 )
				continue;
			else
				$output = $c;
		}
		
		return round( $output, 2 );
	}
	
	/**
	 * Fetches the filenames associated with an object.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param int $id ID of the object to fetch files from.
	 * @param str $type The type of object to fetch for. Must be one of 'collection', 'storyline', 'character', 'post', or 'orphan'.
	 * @param str $src The directory to search when fetch files for 'orphan' type objects.
	 * @param bool $match Attempt to match the object with posts if no posts are associated with it.
	 * @return arr Array of filenames, or false on error.
	 */
	function fetch( $id, $type, $src, $match = false ) {
		$abs  = $this->directory( 'abs', $src );
		$tabs = $abs . 'thumbs/';
		
		if ( 'collection' == $type || 'storyline' == $type || 'character' == $type ) {
			$term_meta = $this->option( 'term_meta' );
			$files = $term_meta[ $type ][ $id ][ 'files' ];
		} elseif( 'post' == $type ) {
			$post_meta = current( ( array ) get_post_meta( $id, 'webcomic' ) );
			$files = ( empty( $post_meta[ 'files' ] ) && $match ) ? $this->match( $id, $src ) : $post_meta[ 'files' ];
		} elseif ( 'orphan' == $type ) {
			$info = pathinfo( $abs . stripslashes( $id ) );
			$files[ 'full' ] = array( $info[ 'basename' ] );
			
			if ( is_file( $tabs . $info[ 'filename' ] . '-small.' . $info[ 'extension' ] ) )
				$files[ 'small' ] = array( $info[ 'filename' ] . '-small.' . $info[ 'extension' ] );
			if ( is_file( $tabs . $info[ 'filename' ] . '-medium.' . $info[ 'extension' ] ) )
				$files[ 'medium' ] = array( $info[ 'filename' ] . '-medium.' . $info[ 'extension' ] );
			if ( is_file( $tabs . $info[ 'filename' ] . '-large.' . $info[ 'extension' ] ) )
				$files[ 'large' ] = array( $info[ 'filename' ] . '-large.' . $info[ 'extension' ] );
		} else
			return false;
		
		return $files;
	}
	
	/**
	 * Retrives detailed information for the files associated with an object.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param int $id ID of the object to fetch files from.
	 * @param str $type The type of object to fetch for. Must be one of 'collection', 'storyline', 'character', 'post', or 'orphan'.
	 * @param str $src The directory to search when fetch files for 'orphan' type objects.
	 * @param bool $match Attempt to match the object with posts if no posts are associated with it.
	 * @return arr Array of file data, or false on error.
	 */
	function retrieve( $id, $type, $src, $match = false ) {
		if ( !( $files = $this->fetch( $id, $type, $src, $match ) ) )
			return false;
		
		global $wpdb;
		
		$output    = array();
		$abs       = $this->directory( 'abs', $src );
		$url       = $this->directory( 'url', $src );
		$tabs      = $abs . 'thumbs/';
		$turl      = $url . 'thumbs/';
		$size      = array( 'full', 'large', 'medium', 'small' );
		$image     = apply_filters( 'webcomic_retrieve_image', '<span class="webcomic-object webcomic-object-' . $type . ' webcomic-object-%size webcomic-object-' . $id . '"><img src="%url" %heightwidth alt="%alt" title="%des"></span>', $id, $type );
		$flash     = apply_filters( 'webcomic_retrieve_flash', '<span class="webcomic-object webcomic-object-' . $type . ' webcomic-object-%size webcomic-object-' . $id . '"><span id="webcomic-object-%uid" title="%des">%alt</span><script>swfobject.embedSWF("%url","webcomic-object-%uid","%width","%height","' . apply_filters( 'webcomic_flash_version', 9 ) . '");</script></span>' );
		$post_meta = ( 'post' == $type ) ? current( get_post_meta( $id, 'webcomic' ) ) : false;
		
		foreach ( array_keys( $files[ 'full' ] ) as $k ) {
			if ( 'post' == $type ) {
				$alt = ( isset( $post_meta[ 'alternate' ][ $k ] ) ) ? $post_meta[ 'alternate' ][ $k ] : '';
				$des = ( isset( $post_meta[ 'description' ][ $k ] ) ) ? $post_meta[ 'description' ][ $k ] : '';
			} else
				$alt = $des = ( 'collection' == $type || 'storyline' == $type || 'character' == $type ) ? '' : $files[ 'full' ][ $k ];
			
			foreach ( $size as $s ) {
				if ( empty( $files[ $s ][ $k ] ) )
					continue;
				
				$output[ $s ][ $k ] = array_merge( getimagesize( ( ( 'full' == $s ) ? $abs : $tabs ) . $files[ $s ][ $k ] ), pathinfo( ( ( 'full' == $s ) ? $abs : $tabs ) . $files[ $s ][ $k ] ) );
				$output[ $s ][ $k ][ 'url' ]  = ( ( 'full' == $s ) ? $url : $turl ) . $files[ $s ][ $k ];
				$output[ $s ][ $k ][ 'surl' ] = get_bloginfo( 'url' ) . '/?webcomic_object=' . $type . '/' . $id . '/' . $s . '/' . $k;
				
				$obj = ( 'application/x-shockwave-flash' == $output[ $s ][ $k ][ 'mime' ] ) ? str_replace( '%des', $des, str_replace( '%alt', $alt, str_replace( '%size', $s, str_replace( '%width', $output[ $s ][ $k ][ 0 ], str_replace( '%height', $output[ $s ][ $k ][ 1 ], str_replace( '%uid', hash( 'md5', $output[ $s ][ $k ][ 'url' ] ), $flash ) ) ) ) ) ) : str_replace( '%des', $des, str_replace( '%alt', $alt, str_replace( '%size', $s, str_replace( '%heightwidth', $output[ $s ][ $k ][ 3 ], str_replace( '%uid', hash( 'md5', $output[ $s ][ $k ][ 'url' ] ), $image ) ) ) ));
				
				$output[ $s ][ $k ][ 'html' ]    = str_replace( '%url', $output[ $s ][ $k ][ 'url' ], $obj );
				$output[ $s ][ $k ][ 'shtml' ]   = str_replace( '%url', $output[ $s ][ $k ][ 'surl' ], $obj );
				$output[ $s ][ $k ][ 'bbcode' ]  = ( 'application/x-shockwave-flash' == $output[ $s ][ $k ][ 'mime' ] ) ? '' : '[img]' . $output[ $s ][ $k ][ 'url' ] . '[/img]';
				$output[ $s ][ $k ][ 'sbbcode' ] = ( 'application/x-shockwave-flash' == $output[ $s ][ $k ][ 'mime' ] ) ? '' : '[img]' . $output[ $s ][ $k ][ 'surl' ] . '[/img]';
			}
			
			unset( $alt, $des, $obj );
		}
		
		return apply_filters( 'webcomic_retrieve_files', $output, $id, $type, $src, $match );
	}
	
	/**
	 * Matches a post with an existing file.
	 * 
	 * Attempts to match a specified post with an
	 * existing file. Uses the post date by default,
	 * but the 'webcomic_match_key' filter can be used
	 * to change the format or replace the date with any
	 * post-specific criteria for matching.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param int $id ID of the post to be matched.
	 * @param str $src The directory to search for matching files.
	 * @return arr An array of files, or false if no match can be found.
	 */
	function match( $id, $src ) {
		$key    = apply_filters( 'webcomic_match_key', get_the_time( 'Y-m-d', $id ), $id, get_term_by( 'slug', $src, 'webcomic_collection' ) );
		$abs    = $this->directory( 'abs', $src );
		$tabs   = $abs . 'thumbs/';
		$output = array();
		
		if ( !( $files = glob( $abs . '*.*' ) ) )
			return false;
		
		foreach ( $files as $file ) {
			if ( false !== strpos( $file, $key ) ) {
				$output[ 'full' ] = array( basename( $file ) );
				break;
			}
		}
		
		if ( empty( $output ) )
			return false;
		
		$thumbs = glob( $tabs . '*.*' );
		
		foreach ( $thumbs as $thumb ) {
			if ( false !== strpos( $thumb, $key ) && false !== strpos( $thumb, '-large.' ) )
				$output[ 'large' ] = array( basename( $thumb ) );
			
			if ( false !== strpos( $thumb, $key ) && false !== strpos( $thumb, '-medium.' ) )
				$output[ 'medium' ] = array( basename( $thumb ) );
			
			if ( false !== strpos( $thumb, $key ) && false !== strpos( $thumb, '-small.' ) )
				$output[ 'small' ] = array( basename( $thumb ) );
		}
		
		return $output;
	}
	
	/**
	 * Attempts to retrieve a webcomic collection
	 * ID based on the current URL. Borrowed from
	 * category.php > get_category_by_path.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function get_collection_by_path( $collection_path = false, $full_match = true, $output = OBJECT ) {
		$wc = false;
		
		if ( get_option( 'permalink_structure' ) ) {
			$s    = ( isset( $_SERVER[ 'HTTPS' ] ) && 'on' == $_SERVER[ 'HTTPS' ] ) ? 's' : '';
			$port = ( '80' == $_SERVER[ 'SERVER_PORT' ] ) ? '' : ':' . $_SERVER[ 'SERVER_PORT' ];
			
			if ( $id = url_to_postid( rawurlencode( urldecode( "http$s://" . $_SERVER[ 'SERVER_NAME' ] . $port . $_SERVER[ 'REQUEST_URI' ] ) ) ) ) {
				$pm = current( get_post_meta( $id, 'webcomic_collection' ) );
				$wc = get_term( $pm, 'webcomic_collection' );
				break;
			} else {
				global $wp_taxonomies, $wp_post_types;
				$path  ='/' . trim( str_replace( '%20', ' ', str_replace( '%2F', '/', rawurlencode( urldecode( "http$s://" . $_SERVER[ 'SERVER_NAME' ] . $port . $_SERVER[ 'REQUEST_URI' ] ) ) ) ), '/' );
				$parts = explode( '/', $path );
				$leaf  = sanitize_title( basename( $path ) );
				
				foreach ( $parts as $part ) {
					if ( $part == $wp_taxonomies[ 'webcomic_collection' ]->rewrite[ 'slug' ] ) {
						$wc = get_term_by( 'slug', $leaf, 'webcomic_collection' );
						break;
					} elseif ( $part == $wp_taxonomies[ 'webcomic_storyline' ]->rewrite[ 'slug' ] ) {
						$st = get_term_by( 'slug', $leaf, 'webcomic_storyline' );
						$wc = get_term( $st->term_group, 'webcomic_collection' );
						break;
					} elseif ( $part == $wp_taxonomies[ 'webcomic_character' ]->rewrite[ 'slug' ] ) {
						$ch = get_term_by( 'slug', $leaf, 'webcomic_character' );
						$wc = get_term( $st->term_group, 'webcomic_collection' );
						break;
					} elseif ( $part == $wp_post_types[ 'webcomic_post' ]->rewrite[ 'slug' ] ) {
						$ps = current( get_posts( 'post_type=webcomic_post&numberposts=1&name=' . $leaf ) );
						$wc = current( wp_get_object_terms( $ps->ID, 'webcomic_collection' ) );
						break;
					}
				}
			}
		} else {
			if ( isset( $_GET[ 'webcomic_collection' ] ) ) {
				$wc = get_term_by( 'slug', $_GET[ 'webcomic_collection' ], 'webcomic_collection' );
			} elseif ( isset( $_GET[ 'webcomic_storyline' ] ) ) {
				$st = get_term_by( 'slug', $_GET[ 'webcomic_storyline' ], 'webcomic_storyline' );
				$wc = get_term( $st->term_group, 'webcomic_collection' );
			} elseif ( isset( $_GET[ 'webcomic_character' ] ) ) {
				$ch = get_term_by( 'slug', $_GET[ 'webcomic_character' ], 'webcomic_character' );
				$wc = get_term( $ch->term_group, 'webcomic_collection' );
			} elseif ( isset( $_GET[ 'webcomic_post' ] ) ) {
				$ps = current( get_posts( 'post_type=webcomic_post&numberposts=1&name=' . $_GET[ 'webcomic_post' ] ) );
				$wc = current( wp_get_object_terms( $ps->ID, 'webcomic_collection' ) );
			} elseif ( isset( $_GET[ 'page_id' ] ) ) {
				$pm = current( get_post_meta( $_GET[ 'page_id' ], 'webcomic_collection' ) );
				$wc = get_term( $pm, 'webcomic_collection' );
			}
		}
		
		if ( !is_wp_error( $wc ) )
			return  $wc;
		else
			return false;
	}
} global $webcomic;

include_once( 'webcomic-includes/tags-legacy.php' );
include_once( 'webcomic-includes/widgets.php' );

if ( is_admin() ) {
	include_once( 'webcomic-includes/admin.php' );
	include_once( 'webcomic-includes/admin-walker.php' );
} else {
	include_once( 'webcomic-includes/tags.php' );
	include_once( 'webcomic-includes/walker.php' );
}

$instance = apply_filters( 'webcomic_initialize_class', 'webcomic' );
$webcomic = new $instance;
?>
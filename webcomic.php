<?php
/*
Text Domain: webcomic
Plugin Name: WebComic
Plugin URI: http://maikeruon.com/wcib/
Description: WebComic adds a collection of new features to WordPress geared specifically at publishing webcomics.
Version: 2.0.0
Author: Michael Sisk
Author URI: http://maikeruon.com/

Copyright 2008 - 2009 Michael Sisk (email: mike@maikeruon.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//
// Base Loaders
//

/**
 * Loads the webcomic domain for translations.
 * 
 * @package WebComic
 * @since 1.6.0
 */
function load_webcomic_domain() {
	load_plugin_textdomain( 'webcomic', PLUGINDIR . '/' . dirname( plugin_basename( __FILE__ ) ), dirname( plugin_basename( __FILE__ ) ) );
}

/**
 * Creates the default WebComic settings.
 * 
 * This funciton should only run when the plugin is first activated.
 * It will attempt to create all of the default WebComic settings and
 * standard comic directories, and upgrade older features as necessary.
 * 
 * @package WebComic
 * @since 1.0.0
 */
if ( !get_option( 'webcomic_version' ) || '2.0.0' != get_option( 'webcomic_version' ) ) {
	function webcomic_install() {
		load_webcomic_domain();
		
		$default_category = get_option( 'default_category' );
		
		/** Make sure all of our default options have a value. */
		add_option( 'comic_category', array( $default_category ) );
		add_option( 'comic_directory', 'comics' );
		add_option( 'comic_current_chapter', array( $default_category => '-1' ) );
		add_option( 'comic_name_format', 'date' );
		add_option( 'comic_name_format_date', 'Y-m-d' );
		add_option( 'comic_secure_names', '' );
		add_option( 'comic_post_draft', '' );
		add_option( 'comic_feed', '1' );
		add_option( 'comic_feed_size', 'full' );
		add_option( 'comic_transcripts_allowed', '' );
		add_option( 'comic_transcripts_required', '' );
		add_option( 'comic_transcripts_loggedin', '' );
		add_option( 'comic_thumb_crop', '' );
		add_option( 'comic_large_size_w', get_option( 'large_size_w' ) );
		add_option( 'comic_large_size_h', get_option( 'large_size_h' ) );
		add_option( 'comic_medium_size_w', get_option( 'medium_size_w' ) );
		add_option( 'comic_medium_size_h', get_option( 'medium_size_h' ) );
		add_option( 'comic_thumb_size_w', get_option( 'thumbnail_size_w' ) );
		add_option( 'comic_thumb_size_h', get_option( 'thumbnail_size_h' ) );
		
		/** Make sure our default comic directories exist. */
		if ( file_exists( ABSPATH . 'wpmu-settings.php' ) && !file_exists( BLOGUPLOADDIR ) ) //WPMU Check
			mkdir( BLOGUPLOADDIR, 0775, true );
		
		if ( !file_exists( get_comic_directory( 'abs', true ) ) )
			mkdir( get_comic_directory( 'abs', true ), 0775, true );
		
		/** Upgrade older features */
		webcomic_upgrade();
		
		/** Add or update the 'webcomic_version' setting and create the first series, if necessary */
		if ( get_option( 'webcomic_version' ) ) {
			update_option( 'webcomic_version', '2.0.0' );
		} else {
			add_option( 'webcomic_version', '2.0.0' );
			
			if ( !get_the_collection( 'hide_empty=0&depth=1' ) ) {
				$first_series = get_term( $default_category, 'category' );
				wp_insert_term( $first_series->name, 'chapter' );
			}
		}
		
		echo '<div class="updated fade"><p>' . sprintf( __( 'Thanks for choosing WebComic! Please <a href="%s">update your settings</a>.', 'webcomic' ), 'admin.php?page=comic-settings' ) . '</p></div>';
	} add_action( 'admin_notices', 'webcomic_install' );
}



//
// Data Retrieval
//

/**
 * Returns the specified comic category.
 * 
 * This is a utility funciton for retrieving a comic category. If no
 * 'id' is specified, the first comic category is returned. An array
 * containing all comic categories is returned if 'id' is set to 'all'.
 * 
 * @package WebComic
 * @since 1.0.0
 * 
 * @param bool $all Return all comic categories as an array.
 * @param str $format The format multiple categories should be returned in.
 * @return int|arr ID of the first comic category or an array of all comic categories.
 */
function get_comic_category( $all = false, $format = false ) {
	$category = get_option( 'comic_category' );
	
	if ( $all ) {
		if ( 'include' == $format ) {
			return implode( ',', $category );
		} elseif ( 'exclude' == $format) {
			return '-' . implode( ',-', $category );
		} elseif ( 'random' == $format ) {
			$key = array_rand( $category );
			return $category[ $key ];
		} elseif ( 'post_link_exclude' == $format ) {
			return implode( ' and ', $category );
		} else {
			return $category;
		}
	} else {
		return ( int ) $category[ 0 ];
	}
}

/**
 * Returns the the specified comic directory.
 * 
 * This is a utility funciton for retrieving the specified comic
 * directory. This function can return either the absolute or url
 * path to the comic root or 'thumbs' directory.
 * 
 * @package WebComic
 * @since 1.0.0
 * 
 * @param str $type The type of path to return, 'abs' or 'url'.
 * @param bool $thumbs Return the comic thumbnail directory.
 * @param int $category ID of a specified comic category.
 * @return str Path to the comic root or thumbnail directory.
 */
function get_comic_directory( $type = 'abs', $thumbs = false, $category = false ) {
	if ( 'root' == $type )
		return get_settings( 'siteurl' ) . '/' . get_option( 'comic_directory' ) . '/';
	
	$prepend = ( 'abs' == $type ) ? ABSPATH : get_settings( 'siteurl' ) . '/';
	
	$catdir  = ( $category ) ? '/' . $category : '/' . get_comic_category();
	
	if ( file_exists( ABSPATH . 'wpmu-settings.php' ) ) //WPMU Check
		$prepend = ( 'abs' == $type ) ? BLOGUPLOADDIR : get_settings( 'siteurl' ).'/files/' ;
	
	if ( $thumbs )
		return $prepend . get_option( 'comic_directory' ) . $catdir . '/thumbs/';
		
	return $prepend . get_option( 'comic_directory' ) . $catdir . '/';
}

/**
 * Returns the the current comic chapter for the specified series.
 * 
 * This is a utility funciton for retrieving the current chapter for the
 * specified comic series, or all current chapters if $series is set
 * to 'all'.
 * 
 * @package WebComic
 * @since 1.0.0
 * 
 * @param int|str $series ID of a specific comic category or -1.
 * @return int|array ID of the slected current comic chapter or all comic chapters.
 */
function get_comic_current_chapter( $series = false ) {
	$current_chapters = get_option( 'comic_current_chapter' );
	
	if ( !$series )
		return array_shift( array_values( $current_chapters ) );
	elseif ( true === $series )
		return $current_chapters;
	else
		return $current_chapters[ $series ];
}

/**
 * Returns the Comic Library view.
 * 
 * This is a utility funciton for retriving the user-selected Library view.
 * 
 * @package WebComic
 * @since 1.0.0
 * 
 * @param bool $class Displays the "current" class for the active view.
 * @return str The current useres selected view or the "current" class.
 */
function get_comic_library_view( $class = false ) {
	global $current_user;
	
	if ( $class ) {
		if ( $class == get_usermeta( $current_user->ID, 'comic_library_view' ) )
			echo ' class="current"';
		else
			return;
	}
	
	return get_usermeta( $current_user->ID, 'comic_library_view' );
}

/**
 * Retrieves the comic category for a given post.
 * 
 * This is a utility function to determine which (if any) comic category
 * a given post belongs to. If a match is found, the comic category ID
 * is returned immediately.
 * 
 * @package WebComic
 * @since 1.8.0
 * 
 * @param int $id Post ID.
 * @return int Category ID.
 */
function get_post_comic_category( $id = false ) {
	global $post;
	
	$id = ( $id ) ? ( int ) $id : $post->ID;
	
	$post_cats  = wp_get_object_terms( $id, 'category', array( 'fields' => 'ids' ) );
	$comic_cats = get_comic_category( true );
	
	foreach ( $post_cats as $post_cat )
		foreach ( $comic_cats as $comic_cat )
			if ( $post_cat == $comic_cat )
				return ( int ) $comic_cat;
}

/**
 * Retrieves the chapter objects for a given post.
 * 
 * This is a utility function used to generate an object of taxonomy
 * objects for the specified or current posts chapter, volume, and series.
 * 
 * @package WebComic
 * @since 1.8.0
 * 
 * @param int $id A valid post ID.
 * @return obj Object containg chapter taxonomy objects.
 */
function get_post_chapters( $id = false ) {
	global $post;
	
	$id = ( $id ) ? ( int ) $id : $post->ID;
	
	$chapters = wp_get_object_terms( $id, 'chapter' );
	
	if ( !$chapters )
		return; //The post does not beling to any chapters
	
	$post_chapters = new stdClass();
	
	foreach ( $chapters as $value ) {
		if ( !$value->parent )
			$post_chapters->series = $value;
		elseif ( !get_term_children( $value->term_id, 'chapter' ) )
			$post_chapters->chapter = $value;
		else
			$post_chapters->volume = $value;
	}
	
	return $post_chapters;
}



//
// Search Unification
//

/**
 * Removes duplicates from search results.
 * 
 * @package WebComic
 * @since 1.5.0
 */
function webcomic_search_distinct( $query ) {
	global $wp_query;
	
	if ( $wp_query->is_search && false === strpos( $where, 'DISTINCT' ) )
		$query = str_replace( 'SELECT', 'SELECT DISTINCT', $query );
	
	return $query;
} add_filter( 'posts_request', 'webcomic_search_distinct' );

/**
 * Adds post meta data to the search query.
 * 
 * @package WebComic
 * @since 1.5.0
 */
function webcomic_search_join( $join ) {
	global $wp_query;
	
	if ( $wp_query->is_search )
		$join .= " LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id ";
	
	return $join;
} add_filter( 'posts_join', 'webcomic_search_join' );

/**
 * Adds specific checks for finding content based on the 'comic_description'
 * and 'comic_transcript' custom field matches.
 * 
 * @package WebComic
 * @since 1.5.0
 */
function webcomic_search_where( $where ) {
	global $wp_query, $wpdb;
	
	$query_terms = explode( ' ', $wp_query->query_vars[ 's' ] );
	
	$or = '(';
	
	foreach ( $query_terms as $query_term ) {
		if ( $query_term !== '' ) {
			$or .= "(($wpdb->posts.post_title LIKE '%" . $wpdb->escape( $query_term ) . "%') OR ($wpdb->posts.post_content LIKE '%" . $wpdb->escape( $query_term ) . "%') OR (($wpdb->postmeta.meta_key = 'comic_transcript' OR $wpdb->postmeta.meta_key = 'comic_description') AND $wpdb->postmeta.meta_value LIKE '%" . $wpdb->escape( $query_term ) . "%')) OR ";
			$i++;
		}
	}
	
	if ( $i > 1 )
		$or .= "(($wpdb->posts.post_title LIKE '" . $wpdb->escape( $wp_query->query_vars[ 's' ] ) . "') OR ($wpdb->posts.post_content LIKE '" . $wpdb->escape( $wp_query->query_vars[ 's' ] ) . "') OR (($wpdb->postmeta.meta_key = 'comic_transcript' OR $wpdb->postmeta.meta_key = 'comic_description') AND $wpdb->postmeta.meta_value LIKE '%" . $wpdb->escape( $wp_query->query_vars[ 's' ] ) . "%')))";
	else
		$or = rtrim( $or, ' OR ') . ')';
	
	$where = preg_replace( "/\(\(\(.*\)\)/i", $or, $where, 1 );
	
	return $where;
} add_filter( 'posts_where', 'webcomic_search_where' );



//
// Comic Templates
//

/**
 * Loads a comic-specific template and sets the $webcomic_series global.
 * 
 * This function checks the requested url to see if the
 * requested page is either directly related to a specific
 * comic category or has the 'comic_series' custom field set.
 * If either is true, it will check if a template exists in
 * "webcomic-#id" and load that template instead, if one exists.
 * 
 * @package WebComic
 * @since 2.0.0
 * 
 * @param str $template The current template string.
 * @return str New theme directory or $template
 */
function webcomic_series_template( $template ) {
	global $webcomic_series;
		
	$theme_root      = get_theme_root();
	$series          = get_series_by_path();
	$webcomic_series = $series;
	
	if ( $series && is_dir( $theme_root . "/webcomic-$series" ) ) {
		return "webcomic-$series";
	}
	
	return $template;
} add_filter( 'template', 'webcomic_series_template' ); add_filter( 'stylesheet', 'webcomic_series_template' );

/**
 * Returns the current webcomic series ID based on the requested path.
 * 
 * This funciton checks the requrested URL for various parameters
 * in an attempt to find what comic series, if any, the requested
 * pages is associated with. If the page is associated witha comic
 * series the series ID is returned.
 * 
 * @package WebComic
 * @since 2.0.0
 * 
 * @return Category ID.
 */
function get_series_by_path() {
	if ( get_option( 'permalink_structure' ) ) {
		if ( $_SERVER[ 'HTTPS' ] )
			$s = ( 'on' == $_SERVER[ 'HTTPS' ] ) ? 's' : '';
		
		$port = ( '80' == $_SERVER[ 'SERVER_PORT' ] ) ? '' : ':' . $_SERVER[ 'SERVER_PORT' ];
		$url  = "http$s://" . $_SERVER[ 'SERVER_NAME' ] . $port . $_SERVER[ 'REQUEST_URI' ];
		
		list( $url ) = explode( '?', $url );
		
		$pid = url_to_postid( $url );
		
		list( $url ) = explode( '/page/', $url );
		
		$cid = get_category_by_path( $url, false );
		$cid = $cid->cat_ID;
		
		$url  = rawurlencode( urldecode( $url ) );
		$url  = str_replace( '%2F', '/', $url );
		$url  = str_replace( '%20', ' ', $url );
		$urls = '/' . trim( $url, '/' );
		$slug = sanitize_title( basename( $urls ) );
		
		$chapter = get_term_by( 'slug', $slug, 'chapter' );
		
		if ( $chapter ) {
			if ( $chapter->parent ) {
				$chapter = get_term( $chapter->parent, 'chapter' );
				
				if ( $chapter->parent  )
					$chapter = get_term( $chapter->parent, 'chapter' );
			}
			
			$sid = $chapter->term_id;
		}
	} else {
		$pid = $_GET[ 'p' ];
		$cid = $_GET[ 'cat' ];
	}
	
	if ( get_post_meta( $pid, 'comic_series', true ) )
		$pid = get_post_meta( $pid, 'comic_series', true );
	else
		$pid = get_post_comic_category( $pid );
	
	if ( $pid )
		return $pid;
	elseif ( $cid )
		return $cid;
	elseif ( $sid )
		return $sid;
}



//
// Other Stuff
//

/**
 * Registers the 'chapter' taxonomy.
 * 
 * @package WebComic
 * @since 2.0.0
 */
function webcomic_init() {
	register_taxonomy( 'chapter', 'post', array( 'hierarchical' => true, 'update_count_callback' => '_update_post_term_count', 'label' => 'Chapter' ) );
} add_action( 'init', 'webcomic_init' );

/**
 * Displays the comic in the site feed.
 * 
 * @package WebComic
 * @since 1.0.0
 */
if ( get_option( 'comic_feed' ) ) {
	function webcomic_feed( $content ) {
		if ( is_feed() )
			$prepend = ( in_comic_category() ) ? '<p>' . get_comic_object( get_the_comic(), get_option( 'comic_feed_size' ) ) . '</p>' : '';
		
		return $prepend . $content; 
	} add_filter( 'the_content', 'webcomic_feed' );
}

/**
 * Enqueue's necessary javascript and handles transcript.php form requests.
 * 
 * @package WebComic
 * @since 2.0.0
 */
function webcomic_template_redirect() {
	//Enqueue javascript required for various WebComic features
	wp_enqueue_script( 'jquery-cookie', plugins_url( 'webcomic/includes/jquery.cookie.js' ), array( 'jquery' ) );
	wp_enqueue_script( 'webcomic-scripts', plugins_url( 'webcomic/includes/scripts.js' ), array( 'jquery', 'jquery-form', 'jquery-cookie' ) );
	
	//Handle transcript.php form requests
	if ( $_POST[ 'comic_transcript_submit' ] ) {
		global $transcript_response;
		
		$before  = '<span class="error">';
		$after   = '</span>';
		$captcha = ( $_POST[ 'trans_checksum' ] ) ? md5( $_POST[ 'trans_captcha' ] ) == $_POST[ 'trans_checksum' ] : true;
		$human   = ( 1 < $_POST[ 'comic_transcript_submit' ] ) ? $_POST[ 'trans_human' ] : true;
		
		if ( !get_post_meta( $_POST[ 'trans_id' ], 'comic_transcript_draft', true ) && !get_post_meta( $_POST[ 'trans_id' ], 'comic_transcript', true ) && ( $human && $captcha ) ) {
			if ( !$_POST[ 'transcript' ] || ( get_option( 'comic_transcripts_required' ) && ( !$_POST[ 'trans_author' ] || !$_POST[ 'trans_email' ] ) ) ) {
				$error = 1;
				$message = ( get_option( 'comic_transcripts_required' ) && ( !$_POST[ 'trans_author' ] || !$_POST[ 'trans_email' ] ) ) ? __( 'Error: all fields are required.', 'webcomic' ) : __( 'Error: please type a transcript.', 'webcomic' ) ;
			} elseif ( get_option( 'comic_transcripts_required' ) && !filter_var( $_POST[ 'trans_email' ], FILTER_VALIDATE_EMAIL ) ) {
				$error = 1;
				$message = __( 'Error: invalid e-mail address.', 'webcomic' );
			} else {
				$email      = ( $_POST[ 'trans_email' ] ) ? ' (' . $_POST[ 'trans_email' ] . ')' : '';
				$from       = ( $_POST[ 'trans_author' ] ) ? stripslashes( $_POST[ 'trans_author' ] ) . $email : __( 'Anonymous', 'webcomc' );
				$title      = stripslashes( $_POST[ 'trans_title' ] );
				$transcript = "\n\n" . wp_filter_kses( $_POST[ 'transcript' ] ) . "\n\n";
				$message    = sprintf( __( '%1$s has submitted a new transcript for %2$s.%3$sYou can approve, edit, or delete this transcript by visiting: %4$s', 'webcomic' ), $from, $title, $transcript, admin_url( 'post.php?action=edit&post=' . $_POST[ 'trans_id' ] ) );
				$postmeta   = sprintf( __( '{ Submitted by %1$s on %2$s }', 'webcomic' ), $from, date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) ) . rtrim( $transcript );
				
				add_post_meta( $_POST[ 'trans_id' ], 'comic_transcript_draft', $postmeta, true );
				
				if ( 1 < $_POST[ 'trans_type' ] )
					delete_post_meta( $_POST[ 'trans_id' ], 'comic_transcript_pending' );
				
				@wp_mail( get_option( 'admin_email' ), sprintf( __( '[%s] New Transcript for "%s"' ), get_option( 'blogname' ), $title ), $message);
				
				$before  = '<span class="success">';
				$message = __( 'Thanks! Your transcript has been submitted for approval.', 'webcomic' );
			}
		} elseif ( $_POST[ 'trans_human' ] || md5( $_POST[ 'trans_captcha' ] ) == $_POST[ 'trans_checksum' ] ) {
			$error = 1;
			$message = ( get_post_meta( $_POST[ 'trans_id' ], 'comic_transcript', true ) ) ? __( 'Error: a transcript has already been approved.', 'webcomic' ) : __( 'Error: a transcript is already awaiting approval.', 'webcomic' );
		} else {
			$error = 1;
			$message = __( 'Error: human check failed, please try again.', 'webcomic' );
		}
				
		if ( 1 < $_POST[ 'comic_transcript_submit' ] ) 
			die( $before . $message . $after );
		elseif ( $error )
			wp_die( $before . $message . $after );
		else
			$transcript_response = $before . $message . $after;
	}	
} add_action( 'template_redirect', 'webcomic_template_redirect' );

/**
 * wc-core.php           contains all of the new template tags.
 * wc-widgets.php        contains all of the new widgets.
 * wc-admin.php          initializes core administrative functions.
 * wc-admin-settings.php contains all Settings page functionality.
 * wc-admin-library.php  contains all Library page functionality.
 * wc-admin-chapters.php contains all Chapters page functionality.
 * wc-admin-metabox.php  contains all Metabox functionality.
 */
require_once( 'includes/wc-core.php' );
require_once( 'includes/wc-widgets.php' );
if ( is_admin() ) {
	require_once( 'includes/wc-admin.php' );
	require_once( 'includes/wc-admin-settings.php' );
	require_once( 'includes/wc-admin-library.php' );
	require_once( 'includes/wc-admin-chapters.php' );
	require_once( 'includes/wc-admin-metabox.php' );
}
?>
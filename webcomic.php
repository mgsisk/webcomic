<?php
/*
Plugin Name: WebComic
Plugin URI: http://maikeruon.com/wcib/
Description: WebComic makes any WordPress theme webcomic ready by adding additional template tags and widgets specifically designed for publishing webcomics.
Version: 1.7
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

/**
 * Loads the webcomic domain for translations.
 * 
 * This is a utility function for loading domain files, enabling WebComic
 * I18n support.
 * 
 * @package WebComic
 * @since 1.6
 */
function load_webcomic_domain(){
	load_plugin_textdomain('webcomic', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)), dirname(plugin_basename(__FILE__)));
}

/**
 * Creates the WebComic default settings.
 * 
 * This funciton should only run when the plugin is first activated.
 * It will attempt to create all of the default WebComic stetings as
 * well as the standard comic directories.
 * 
 * @package WebComic
 * @since 1.0
 */
if(!get_option('comic_category') || !get_option('comic_directory') || !get_option('comic_current_chapter') || !get_option('comic_feed') || !get_option('comic_feed_size') || !get_option('comic_auto_post') || !get_option('comic_name_format') || !get_option('comic_name_format_date') || !get_option('comic_secure_names') || !get_option('comic_thumbnail_crop') || false===get_option('comic_thumbnail_size_w') || false===get_option('comic_thumbnail_size_h') || false===get_option('comic_medium_size_w') || false===get_option('comic_medium_size_h') || false===get_option('comic_large_size_w') || false===get_option('comic_large_size_h')):
	function comic_set_defaults(){
		load_webcomic_domain();
		
		add_option('comic_category','1/');
		add_option('comic_directory','comics');
		add_option('comic_current_chapter','-1');
		add_option('comic_feed','on');
		add_option('comic_feed_size','full');
		add_option('comic_auto_post','off');
		add_option('comic_name_format','date');
		add_option('comic_name_format_date','Y-m-d');
		add_option('comic_secure_names','off');
		add_option('comic_thumbnail_crop','off');
		add_option('comic_thumbnail_size_w',get_option('thumbnail_size_w'));
		add_option('comic_thumbnail_size_h',get_option('thumbnail_size_h'));
		add_option('comic_medium_size_w',get_option('medium_size_w'));
		add_option('comic_medium_size_h',get_option('medium_size_h'));
		add_option('comic_large_size_w',get_option('large_size_w'));
		add_option('comic_large_size_h',get_option('large_size_h'));
		
		if(!file_exists(ABSPATH.get_comic_directory()))
			mkdir(ABSPATH.get_comic_directory(),0775);
		if(!file_exists(ABSPATH.get_comic_directory(true)))
			mkdir(ABSPATH.get_comic_directory(true),0775);
		
		echo '<div class="updated fade"><p>'.sprintf(__('Thanks for choosing WebComic! Please check the <a href="%s">settings page</a> to configure the plugin.','webcomic'),'admin.php?page=webcomic/wc-admin.php').'</p></div>';
	}
	add_action('admin_notices', 'comic_set_defaults');
endif;

/**
 * Returns the comic category.
 * 
 * This is a utility funciton for retriving the selected comic
 * category ID.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @return int ID of the selected comic category.
 */
function get_comic_category(){
	return intval(get_option('comic_category'));
}

/**
 * Returns the the defined comic directory.
 * 
 * This is a utility funciton for retriving the defined comic
 * directory or comic thumbnail directory.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @param bool $thumbs Returns the comic thumbnail directory.
 * @return str The comic directory or comic thumbnail directory.
 */
function get_comic_directory($thumbs=false){
	if($thumbs)
		return get_option('comic_directory').'/thumbs/';
		
	return get_option('comic_directory').'/';
}

/**
 * Returns the the current comic chapter.
 * 
 * This is a utility funciton for retriving the selected current
 * comic chapter.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @return int ID of the slected current comic chapter.
 */
function get_comic_current_chapter(){
	return intval(get_option('comic_current_chapter'));
}

/**
 * Returns the the defined comic directory.
 * 
 * This is a utility funciton for retriving the user-selected
 * Library view.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @param bool $view Displays the "current" class.
 * @return str The current useres selected view or the "current" class.
 */
function get_comic_library_view($view=false){
	global $current_user;
	
	if($view && ($view == get_usermeta($current_user->ID,'comic_library_view')))
		echo ' class="current"';
	
	if($view && ($view != get_usermeta($current_user->ID,'comic_library_view')))
		return;
	
	return get_usermeta($current_user->ID,'comic_library_view');
}



/**
 * Adds the 'chapter' taxonomy.
 * 
 * This function adds the 'chapter' taxonomy, enabling all of
 * WebComic's specialized chapter functions.
 * 
 * @package WebComic
 * @since 1.0
 */
function register_chapters(){
	register_taxonomy('chapter','post',array('hierarchical' => true, 'update_count_callback' => '_update_post_term_count'));
}
add_action('init','register_chapters');



/**
 * Displays the comic in the site feed.
 * 
 * This function displays the comic associated with a given post
 * in the site feed based on user settings.
 * 
 * @package WebComic
 * @since 1.0
 */
if('on' == get_option('comic_feed')):
	function webcomic_feed($content) {
		if(is_feed() && in_category(get_comic_category())):
			$comic = get_the_comic();
			return '<p><img src="'.get_comic_image($comic,get_option('comic_feed_size')).'" alt="'.$comic['title'].'" title="'.$comic['description'].'" class="'.$comic['class'].'" /></p>'.$content;
		else:
			return $content;
		endif;
	}
	add_filter('the_content','webcomic_feed');
endif;

/**
 * Removes duplicates from search results.
 * 
 * This function removes duplicate entries from WordPress search results.
 * 
 * @package WebComic
 * @since 1.5
 */
function webcomic_search_distinct($query){
	global $wp_query;
	
	if (is_search() && false === strpos($where, 'DISTINCT'))
		$query = str_replace('SELECT', 'SELECT DISTINCT', $query);
	
	return $query;
}
add_filter('posts_request', 'webcomic_search_distinct');

/**
 * Adds post meta data to the search query.
 * 
 * This function adds the postmeta table to search queries, allowing post
 * meta data (comic descriptions and transcripts, in our case) to be searched.
 * 
 * @package WebComic
 * @since 1.5
 */
function webcomic_search_join($join){
	global $wpdb;
	
	if(is_search())
		$join .= " LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id ";
	
	return $join;
}
add_filter('posts_join', 'webcomic_search_join');

/**
 * Adds specific checks for finding content based on 'comic_description' and 'comic_transcript' matches.
 * 
 * This function adds additional checks to the WHERE query statement that
 * enable posts to be matched and retrieved against the contents of both the
 * 'comic_description' and 'comic_transcript' meta fields.
 * 
 * @package WebComic
 * @since 1.5
 */
function webcomic_search_where($where){
	global $wp_query,$wpdb;
	
	$query_terms = explode(' ',$wp_query->query_vars['s']);
	
	$or = '(';
	foreach($query_terms as $query_term):
		if($Query_term !== ''):
			$or .= "(($wpdb->posts.post_title LIKE '%".$wpdb->escape($query_term)."%') OR ($wpdb->posts.post_content LIKE '%".$wpdb->escape($query_term)."%') OR (($wpdb->postmeta.meta_key = 'comic_transcript' OR $wpdb->postmeta.meta_key = 'comic_description') AND $wpdb->postmeta.meta_value LIKE '%".$wpdb->escape($query_term)."%')) OR ";
			$i++;
		endif;
	endforeach;
	if($i > 1)
		$or .= "(($wpdb->posts.post_title LIKE '".$wpdb->escape($wp_query->query_vars['s'])."') OR ($wpdb->posts.post_content LIKE '".$wpdb->escape($wp_query->query_vars['s'])."') OR (($wpdb->postmeta.meta_key = 'comic_transcript' OR $wpdb->postmeta.meta_key = 'comic_description') AND $wpdb->postmeta.meta_value LIKE '%".$wpdb->escape($wp_query->query_vars['s'])."%')))";
	else
		$or = rtrim($or,' OR ').')';
	
	$where = preg_replace("/\(\(\(.*\)\)/i",$or,$where,1);
	
	return $where;
}
add_filter('posts_where', 'webcomic_search_where');

/**
 * wc-core.php contains all of the new template tags.
 * wc-admin.php initializes core administrative functions.
 * wc-admin-settings.php contains all Settings page functionality.
 * wc-admin-library.php contains all Library page functionality.
 * wc-admin-chapters.php contains all Chapters page functionality.
 * wc-admin-metabox.php contains all Metabox functionality.
 * wc-widgets.php contains all of the new widgets.
 * markdown.php contains the Markdown Extra library.
 */
require_once('wc-core.php');

if(is_admin()):
	require_once('wc-admin.php');
	require_once('wc-admin-settings.php');
	require_once('wc-admin-library.php');
	require_once('wc-admin-chapters.php');
	require_once('wc-admin-metabox.php');
endif;

@include_once('wc-widgets.php');
@include_once('markdown.php');
?>
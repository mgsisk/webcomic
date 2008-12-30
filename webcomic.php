<?php
/*
Plugin Name: WebComic
Plugin URI: http://maikeruon.com/wcib/
Description: WebComic makes any WordPress theme webcomic ready by adding additional template tags and widgets specifically designed for publishing webcomics.
Version: 1.5
Author: Michael Sisk
Author URI: http://maikeruon.com/

Copyright 2008 Michael Sisk (email: mike@maikeruon.com)

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



//Activation Check - Make sure all of our options have a default value
if(!get_option('comic_category') || !get_option('comic_directory') || !get_option('comic_current_chapter') || !get_option('comic_feed') || !get_option('comic_feed_size') || !get_option('comic_auto_post') || !get_option('comic_name_format') || !get_option('comic_name_format_date') || !get_option('comic_secure_names') || !get_option('comic_thumbnail_crop') || false===get_option('comic_thumbnail_size_w') || false===get_option('comic_thumbnail_size_h') || false===get_option('comic_medium_size_w') || false===get_option('comic_medium_size_h') || false===get_option('comic_large_size_w') || false===get_option('comic_large_size_h') || !get_option('comic_library_view')):
	function comic_set_defaults(){
		add_option('comic_category','1');
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
		add_option('comic_library_view','list');
		
		if(!file_exists(ABSPATH.get_comic_directory()))
			mkdir(ABSPATH.get_comic_directory(),0775,true);
		if(!file_exists(ABSPATH.get_comic_directory().'thumbs/'))
			mkdir(ABSPATH.get_comic_directory().'thumbs/',0775,true);
		
		echo '<div id="comic-warning" class="updated fade"><p><strong>Thanks for choosing WebComic! Please check the <a href="admin.php?page=webcomic/wc-admin.php">setting page</a> to configure the plugin.</strong></p></div>';
	}
	add_action('admin_notices', 'comic_set_defaults');
endif;



//Option Retrieval Functions - For all your option retrieving needs
function get_comic_category(){
	return intval(get_option('comic_category'));
}

function get_comic_directory($thumbs=false){
	if($thumbs)
		return get_option('comic_directory').'/thumbs/';
		
	return get_option('comic_directory').'/';
}

function get_comic_current_chapter(){
	return intval(get_option('comic_current_chapter'));
}

function get_comic_library_view($view=false){
	if($view && ($view == get_option('comic_library_view')))
		echo ' class="current"';
	
	if($view && ($view != get_option('comic_library_view')))
		return;
	
	return get_option('comic_library_view');
}



//Add the "chapter" taxonomy
function register_chapters(){
	register_taxonomy('chapter','post',array('hierarchical' => true, 'update_count_callback' => '_update_post_term_count'));
}
add_action('init','register_chapters');



//Show or hide comic images based on user settings
if('on' == get_option('comic_feed')):
	function webcomic_feed($content) {
		if(is_feed() && in_category('comic'))
			return '<p>'.get_the_comic(false,'image',get_option('comic_feed_size')).'</p>'.$content;
		else
			return $content;
	}
	add_filter('the_content','webcomic_feed');
endif;



//Search transcripts and descriptions
function webcomic_search_distinct($query){
	global $wp_query;
	
	if (is_search() && !strstr($where, 'DISTINCT'))
		$query = str_replace('SELECT', 'SELECT DISTINCT', $query);
	
	return $query;
}
add_filter('posts_request', 'webcomic_search_distinct');

function webcomic_search_join($join){
	global $wpdb;
	
	if(is_search())
		$join .= " LEFT JOIN $wpdb->postmeta ON $wpdb->posts.ID = $wpdb->postmeta.post_id ";
	
	return $join;
}
add_filter('posts_join', 'webcomic_search_join');

function webcomic_search_where($where){
	global $wp_query,$wpdb;
	
	if(is_search())
		$or = " OR (($wpdb->postmeta.meta_key = 'comic_transcript' OR $wpdb->postmeta.meta_key = 'comic_description') AND $wpdb->postmeta.meta_value LIKE '%" . $wpdb->escape($wp_query->query_vars['s']) . "%') ";
	
	$where = preg_replace("/\bor\b/i",$or." OR",$where,1);
	
	return $where;
}
add_filter('posts_where', 'webcomic_search_where');



if(is_admin()):							   //Load tha admin files only when necessary
	require_once('wc-admin.php');          //Contains general administrative functions
	require_once('wc-admin-settings.php'); //Contains administrative functions for the settings page
	require_once('wc-admin-library.php');  //Contains administrative functions for the library page
	require_once('wc-admin-chapters.php'); //Contains administrative functions for the chapters page
endif;
require_once('wc-core.php');               //Contains the core functions and template tags for displaying and navigating comics
@include_once('wc-widgets.php');           //Contains widgits for recent comics, random comic, dropdown comics, comic archive, and modified recent posts
@include_once('markdown.php');             //Totally optional, only used for comic transcripts
?>
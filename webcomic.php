<?php
/*
Plugin Name: WebComic
Plugin URI: http://maikeruon.com/wcib/
Description: WebComic makes any WordPress theme webcomic ready by adding additional template tags and widgets specifically designed for publishing webcomics.
Version: 1.8
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
 * This is a utility function for loading domain files, enabling I18n support.
 * 
 * @package WebComic
 * @since 1.6
 */
function load_webcomic_domain(){
	load_plugin_textdomain('webcomic', PLUGINDIR.'/'.dirname(plugin_basename(__FILE__)), dirname(plugin_basename(__FILE__)));
}

/**
 * Creates the WebComic default settings and upgrades older versions of WebComic.
 * 
 * This funciton should only run when the plugin is first activated.
 * It will attempt to create all of the default WebComic settings, and the
 * standard comic directories and upgrade older features as necessary.
 * 
 * @package WebComic
 * @since 1.0
 */
if(!get_option('webcomic_version') || '1.9' != get_option('webcomic_version')):
	function comic_upgrade(){
		load_webcomic_domain();
		
		global $blog_id;
		
		/** Make sure all of our default options have a value. */
		add_option('comic_category',array('1'));
		add_option('comic_directory','comics');
		add_option('comic_current_chapter',array('1' => '-1'));
		add_option('comic_feed_size','full');
		add_option('comic_name_format','date');
		add_option('comic_name_format_date','Y-m-d');
		add_option('comic_feed','1');
		add_option('comic_auto_post','');
		add_option('comic_secure_names','');
		add_option('comic_thumbnail_crop','');
		add_option('comic_thumbnail_size_w',get_option('thumbnail_size_w'));
		add_option('comic_thumbnail_size_h',get_option('thumbnail_size_h'));
		add_option('comic_medium_size_w',get_option('medium_size_w'));
		add_option('comic_medium_size_h',get_option('medium_size_h'));
		add_option('comic_large_size_w',get_option('large_size_w'));
		add_option('comic_large_size_h',get_option('large_size_h'));
		
		/** Make sure our default comic directories exist. */
		if(file_exists(ABSPATH.'wpmu-settings.php') && !file_exists(BLOGUPLOADDIR)): //WPMU Check
			mkdir(ABSPATH.'wp-content/blogs.dir/'.$blog_id.'/',0775);
			mkdir(BLOGUPLOADDIR,0775);
		endif;
		if(!file_exists(get_comic_directory('abs')))
			mkdir(get_comic_directory('abs'),0775);
		if(!file_exists(get_comic_directory('abs',true)))
			mkdir(get_comic_directory('abs',true),0775);
		
		/** Make sure our Comic Category has a Series, upgrading older collections as necessary. The upgrade will be removed in the next version. */
		$chapters     = get_terms('chapter',array('hide_empty' => false));
		$first_series = get_term(get_comic_category(),'category');
		$the_series   = wp_insert_term($first_series->name,'chapter');
		if($chapters):
			$series = get_term($the_series['term_id'],'chapter');
			foreach($chapters as $chapter):
				if(!$chapter->parent)
					wp_update_term($chapter->term_id,'chapter',array('parent' => $series->term_id));
			endforeach;
			
			$collection = get_the_collection();
			$new_tax    = array();
			
			foreach($collection as $series):
				foreach($series['volumes'] as $volume):
					foreach($volume['chapters'] as $chapter):
						foreach($chapter['posts'] as $the_post):
							$new_tax[0] = $series['slug'];
							$new_tax[1] = $volume['slug'];
							$new_tax[2] = $chapter['slug'];
							wp_set_object_terms($the_post['id'],$new_tax,'chapter');
						endforeach;
					endforeach;
				endforeach;
			endforeach;
		endif;
		
		/** Upgrade old Comic Category and Current Chapter settings. This will be removed in the next version. */
		if(!is_array(get_option('comic_current_chapter')))
			update_option('comic_current_chapter',array(get_option('comic_category') => get_option('comic_current_chapter')));
		if(!is_array(get_option('comic_category')))
			update_option('comic_category',array(get_option('comic_category')));
		
		/** Add or update the 'webcomic_version' setting. */
		if(get_option('webcomic_version'))
			update_option('webcomic_version','1.8');		
		else
			add_option('webcomic_version','1.8');
		
		echo '<div class="updated fade"><p>'.sprintf(__('Thanks for choosing WebComic! Please <a href="%s">update your settings</a>.','webcomic'),'admin.php?page=comic-settings').'</p></div>';
	}
	add_action('admin_notices', 'comic_upgrade');
endif;

//
// Option Retrieval
//

/**
 * Returns the comic category.
 * 
 * This is a utility funciton for retriving a comic category. If no
 * 'id' is specified, the first comic category is returned. An array
 * containing all comic categories is returned if 'id' is set to 'all'.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @param int|str ID of a specific comic category, or 'all'.
 * @return int|arr ID of the selected comic category or all comic categories.
 */
function get_comic_category($id=false){
	$category = get_option('comic_category');
	
	if(!$id)
		return (int) $category[0];
	elseif($id == 'all')
		return $category;
	else
		foreach($category as $the_category)
			if($id == $the_category)
				return (int) $the_category;
}

/**
 * Returns the the defined comic directory.
 * 
 * This is a utility funciton for retriving the defined comic
 * directory or comic thumbnail directory. This function can
 * return either the absolute or url path to the root comic or
 * 'thumbs' directory.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @param str $type The type of path to return, 'abs' or 'url'.
 * @param bool $thumbs Returns the comic thumbnail directory.
 * @param int $category ID of a specified comic category.
 * @return str Path to the comic directory or comic thumbnail directory.
 */
function get_comic_directory($type='abs',$thumbs=false,$category=false){
	global $blog_id;
	
	$prepend = ('abs' == $type) ? ABSPATH : get_settings('siteurl').'/';
	$subdir  = ($category) ? '/'.$category : '';
	$append  = ($thumbs) ? '/thumbs/' : '/';
	
	if(file_exists(ABSPATH.'wpmu-settings.php')) //WPMU Check
		$prepend = ('abs' == $type) ? BLOGUPLOADDIR : get_settings('siteurl').'/files/' ;
		
	if($thumbs)
		return $prepend.get_option('comic_directory').$subdir.'/thumbs/';
		
	return $prepend.get_option('comic_directory').$subdir.'/';
}

/**
 * Returns the the current comic chapter.
 * 
 * This is a utility funciton for retriving the current chapter for the
 * specified comic series, or all current chapters if 'category' is set
 * to 'all'.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @param int|str ID of a specific comic category, or 'all'.
 * @return int|arr ID of the slected current comic chapter or all comic chapters.
 */
function get_comic_current_chapter($series=false){
	$current_chapters = get_option('comic_current_chapter');
	
	if(!$series)
		return array_shift(array_values($current_chapters));
	elseif('all' == $series)
		return $current_chapters;
	else
		return $current_chapters[$series];
}

/**
 * Returns the Comic Library view.
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
	
	if($view):
		if($view == get_usermeta($current_user->ID,'comic_library_view'))
			echo ' class="current"';
		else
			return;
	endif;
	
	return get_usermeta($current_user->ID,'comic_library_view');
}

//
// Search Unification
//

/**
 * Removes duplicates from search results.
 * 
 * This function removes duplicate entries from WordPress search results that
 * may be retrieved during search.
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
 * Adds specific checks for finding content based on 'comic_description' and
 * 'comic_transcript' custom field matches.
 * 
 * This function adds additional checks to the WHERE search query statement
 * that enable posts to be matched and retrieved against the contents of both
 * the 'comic_description' and 'comic_transcript' meta fields.
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

//
// Other Stuff
//

/**
 * Displays the comic in the site feed.
 * 
 * This function displays the comic associated with a given post
 * in the site feed based on user settings.
 * 
 * @package WebComic
 * @since 1.0
 */
if(get_option('comic_feed')):
	function webcomic_feed($content){
		if(is_feed()):
			if(in_comic_category()):
				$comic = get_the_comic();
				if($comic)
					$prepend = '<p><img src="'.get_comic_image($comic,get_option('comic_feed_size')).'" alt="'.$comic['title'].'" title="'.$comic['description'].'" class="'.$comic['class'].'" /></p>';
			endif;
		endif;
		return $prepend.$content; 
	}
	add_filter('the_content','webcomic_feed');
endif;

/**
 * Handles the 'submit transcript' form.
 * 
 * This functions handles the 'submit transcript' form for a comic,
 * validating form input and e-mailing it to a specified address
 * upon success (or returning an error on failure).
 * 
 * @package WebComic
 * @since 1.8
 */
function comic_transcript_submit(){
	if($_POST['comic_trans_submit']):
		global $comic_trans_message;
		
		if(!$_POST['trans_from'])
			$errors += 1;
		if(!$_POST['trans_mail'])
			$errors += 1;
		if(!$_POST['trans_script'])
			$errors += 1;
		
		if($errors > 0):
			$comic_trans_message = '<span class="comic-transcript-submit-error">All fields are required.</span>';
		elseif(!filter_var($_POST['trans_mail'], FILTER_VALIDATE_EMAIL)):
			$comic_trans_message = '<span class="comic-transcript-submit-error">A valid e-mail address is required.</span>';
		else:
			$from    = (get_magic_quotes_gpc()) ? stripslashes($_POST['trans_from']) : $_POST['trans_from'];
			$title   = (get_magic_quotes_gpc()) ? stripslashes($_POST['comic_trans_title']) : $_POST['comic_trans_title'];
			$message = (get_magic_quotes_gpc()) ? stripslashes($_POST['trans_script']) : $_POST['trans_script'];
			mail(get_option('comic_transcript_email'), '[Transcript] '.$title, $message, 'From: "'.$from.'" <'.$_POST['trans_mail'].'>');
			$comic_trans_message = '<span class="comic-transcript-submit-success">Thanks! Your transcript has been submitted.</span>';
		endif;
		
		if(1 < $_POST['comic_trans_submit']):
			echo $comic_trans_message;
			die();
		endif;
	endif;
}
add_action('init','comic_transcript_submit');

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
	register_taxonomy('chapter','post',array('hierarchical' => true, 'update_count_callback' => '_update_post_term_count', 'label' => 'Chapter'));
}
add_action('init','register_chapters');

//
// External File Loading
//

/**
 * wc-core.php contains all of the new template tags.
 * wc-admin.php initializes core administrative functions.
 * wc-admin-settings.php contains all Settings page functionality.
 * wc-admin-library.php contains all Library page functionality.
 * wc-admin-chapters.php contains all Chapters page functionality.
 * wc-admin-metabox.php contains all Metabox functionality.
 * wc-widgets.php contains all of the new widgets. Optional.
 * markdown.php contains the Markdown Extra library. Optional.
 */
require_once('includes/wc-core.php');
if(is_admin()):
	require_once('includes/wc-admin.php');
	require_once('includes/wc-admin-settings.php');
	require_once('includes/wc-admin-library.php');
	require_once('includes/wc-admin-chapters.php');
	require_once('includes/wc-admin-metabox.php');
endif;
@include_once('includes/wc-widgets.php');
@include_once('includes/markdown.php');
?>
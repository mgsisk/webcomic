<?php
/*
Plugin Name: WebComic
Plugin URI: http://rad.maikeruon.com/wcib/
Description: WebComic makes any WordPress theme webcomic ready by adding additional template tags and widgets specifically designed for publishing webcomics.
Version: 1.0
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
if(!get_option('comic_category') || !get_option('comic_directory') || !get_option('comic_name_format') || !get_option('comic_name_format_date') || !get_option('comic_feed') || !get_option('comic_library_view') || !get_option('comic_current_chapter')):
	function comic_set_defaults(){
		add_option('comic_category','1');
		add_option('comic_directory','comics');
		add_option('comic_name_format','date');
		add_option('comic_name_format_date','Y-m-d');
		add_option('comic_current_chapter','-1');
		add_option('comic_feed','on');
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
function get_comic_category(){return intval(get_option('comic_category'));}
function get_comic_directory(){return get_option('comic_directory').'/';}
function get_comic_current_chapter(){return intval(get_option('comic_current_chapter'));}
function get_comic_library_view($view=false){
	if($view && ($view == get_option('comic_library_view'))) echo ' class="current"';
	if($view && ($view != get_option('comic_library_view'))) return false;
	return get_option('comic_library_view');
}



//Feed Filter - Didn't know where else to stick it XD
if('on' == get_option('comic_feed')):
	function webcomic_feed($content) {
		if(is_feed() && in_category('comic')):
			return '<p>'.the_comic().'</p>'.$content;
		else:
			return $content;
		endif;
	}
	add_filter('the_content','webcomic_feed');
endif;



//Split the functions to avoide 1000+ line plugin file
require_once('wc-admin.php');    //Contains all administrative functions for managing the plugin
require_once('wc-core.php');     //Contains the core functions and template tags for displaying and navigating comics
require_once('wc-chapters.php'); //Contains taxonomy functions and template tags for working with chapters
include_once('wc-widgets.php');  //Contains widgits for recent comics, random comic, dropdown comics, comic archive, and modified recent posts
include_once('markdown.php');    //Totally optional, only used for comic transcripts
?>
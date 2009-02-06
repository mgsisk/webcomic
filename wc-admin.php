<?php
/**
 * This document contains has various administration related functions,
 * including adding the WebComic admin pages and Chapter taxonomy functions.
 * 
 * @package WebComic
 * @since 1.0
 */

/**
 * Registers the WebComic administrative pages.
 * 
 * @package WebComic
 * @since 1.0
 */
function comic_admin_pages_add(){
	load_webcomic_domain();
	
	add_menu_page(__('WebComic','webcomic'),__('WebComic','webcomic'),'manage_options', __FILE__,'comic_page_settings',plugins_url('webcomic/webcomic-small.png'));
	add_submenu_page(__FILE__,__('Settings','webcomic'),__('Settings','webcomic'),'manage_options',__FILE__,'comic_page_settings');
	add_submenu_page(__FILE__,__('Comic Library','webcomic'),__('Library','webcomic'),'upload_files','comic-library','comic_page_library');
	add_submenu_page(__FILE__,__('Comic Chapters','webcomic'),__('Chapters','webcomic'),'manage_categories','comic-chapters','comic_page_chapters');
	
	if(function_exists('add_meta_box'))
		add_meta_box('webcomic', __('WebComic','webcomic'), 'comic_meta_box', 'post', 'normal', 'high');
}
add_action('admin_menu', 'comic_admin_pages_add');

/**
 * Ensures the correct javascript files are available for advanced effects.
 * 
 * @package WebComic
 * @since 1.0
 */
function wc_admin_scripts() {
	wp_enqueue_script('admin-forms');
}
add_action('admin_init','wc_admin_scripts');

/**
 * Adds a comic post to the specified chapter.
 * 
 * This function adds a WordPress post to the specified chapter
 * taxonomy. It first checks to see if the specified post is
 * actually assigned to the defined comic category and, if so,
 * assigns it to the specified chapter.
 * 
 * @package WebComic
 * @since 1.0
 */
function add_post_to_chapter($id,$chapter,$overwrite=true){
	$cats = get_the_category($id);
	
	foreach($cats as $cat)
		if($cat->cat_ID == get_comic_category())
			$comic = true;
	
	if($comic):
		$taxonomies = get_the_taxonomies($id);
		$has_chapter = (array_key_exists('chapter',$taxonomies)) ? true : false;
		
		if($has_chapter):
			if($overwrite)
				wp_delete_object_term_relationships($id,'chapter');
			else
				return;
		endif;
		
		wp_set_object_terms($id,intval($chapter),'chapter');
	endif;
}

/**
 * Removes a comic post from a chapter.
 * 
 * This function removes the specified post from it's chapter.
 * 
 * @package WebComic
 * @since 1.0
 */
function remove_post_from_chapter($id){
	wp_delete_object_term_relationships($id,'chapter');
}
add_action('delete_post','remove_post_from_chapter');

/**
 * Adds new comic posts to the defined current chapter.
 * 
 * This function adds any newly created comic posts to the defined
 * current chapter.
 * 
 * @package WebComic
 * @since 1.0
 */
if(-1 != get_comic_current_chapter()):
	function add_post_to_current_chapter($id){
		add_post_to_chapter($id,get_comic_current_chapter(),false);
	}
	add_action('save_post','add_post_to_current_chapter');
endif;
?>
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
	
	add_menu_page(__('WebComic','webcomic'),__('WebComic','webcomic'),'upload_files', __FILE__,'comic_page_library',plugins_url('webcomic/includes/webcomic-small.png'));
	add_submenu_page(__FILE__,__('Comic Library','webcomic'),__('Library','webcomic'),'upload_files',__FILE__,'comic_page_library');
	add_submenu_page(__FILE__,__('Comic Chapters','webcomic'),__('Chapters','webcomic'),'manage_categories','comic-chapters','comic_page_chapters');
	add_submenu_page(__FILE__,__('Comic Settings','webcomic'),__('Settings','webcomic'),'manage_options','comic-settings','comic_page_settings');
	
	if(function_exists('add_meta_box'))
		add_meta_box('webcomic', __('WebComic','webcomic'), 'comic_meta_box', 'post', 'normal', 'high');
}
add_action('admin_menu', 'comic_admin_pages_add');

/**
 * Registers plugin settings and ensures the correct javascript
 * files are available for advanced effects.
 * 
 * @package WebComic
 * @since 1.0
 */
function wc_admin_init(){
	register_setting('webcomic_options','comic_category');
	register_setting('webcomic_options','comic_directory');
	register_setting('webcomic_options','comic_feed_size');
	register_setting('webcomic_options','comic_name_format');
	register_setting('webcomic_options','comic_name_format_date');
	register_setting('webcomic_options','comic_feed');
	register_setting('webcomic_options','comic_auto_post');
	register_setting('webcomic_options','comic_transcript_email');
	register_setting('webcomic_options','comic_secure_names');
	register_setting('webcomic_options','comic_thumbnail_crop');
	register_setting('webcomic_options','comic_thumbnail_size_w');
	register_setting('webcomic_options','comic_thumbnail_size_h');
	register_setting('webcomic_options','comic_medium_size_w');
	register_setting('webcomic_options','comic_medium_size_h');
	register_setting('webcomic_options','comic_large_size_w');
	register_setting('webcomic_options','comic_large_size_h');		
	wp_enqueue_script('admin-forms');
}
add_action('admin_init','wc_admin_init');

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
	$post_cats  = wp_get_object_terms($id,'category',array('fields' => 'ids'));
	$comic_cats = get_comic_category('all');
	
	foreach($post_cats as $post_cat)
		foreach($comic_cats as $comic_cat)
			if($post_cat == $comic_cat)
				$comic = $post_cat;
	
	if($comic):
		$taxonomies = get_the_taxonomies($id);
		$has_chapter = (array_key_exists('chapter',$taxonomies)) ? true : false;
				
		if($has_chapter):
			if($overwrite)
				remove_post_from_chapter($id);
			else
				return;
		endif;
		
		if(-1 == $chapter):
			remove_post_from_chapter($id);
		else:
			$the_chapter = get_term($chapter,'chapter');
			$the_volume  = get_term($the_chapter->parent,'chapter');
			$the_series  = get_term($the_volume->parent,'chapter');
			
			$new_tax = array();
			$new_tax[0] = $the_chapter->slug;
			$new_tax[1] = $the_volume->slug;
			$new_tax[2] = $the_series->slug;
			
			wp_set_object_terms($id,$new_tax,'chapter');
		endif;
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
 * current chapter for a given series. If there is no defined current
 * chapter (-1), the post is still added to the comic series.
 * 
 * @package WebComic
 * @since 1.0
 */
function add_post_to_current_chapter($id){
	$post_cats  = wp_get_object_terms($id,'category',array('fields' => 'ids'));
	$comic_cats = get_comic_category('all');
	
	foreach($post_cats as $post_cat)
		foreach($comic_cats as $comic_cat)
			if($post_cat == $comic_cat)
				$comic = $post_cat;

	if($comic)
		$chapter = get_comic_current_chapter($comic);
	else
		return;
	
	add_post_to_chapter($id,$chapter,false);
}
add_action('save_post','add_post_to_current_chapter');
?>

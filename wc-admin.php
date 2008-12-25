<?php
//Register the WebComic administrative pages
function comic_admin_pages_add(){
	add_menu_page('WebComic','WebComic',8, __FILE__,'comic_page_settings');
	add_submenu_page(__FILE__,'Settings','Settings',8,__FILE__,'comic_page_settings');
	add_submenu_page(__FILE__,'Comic Library','Library',8,'comic-library','comic_page_library');
	add_submenu_page(__FILE__,'Comic Chapters','Chapters',8,'comic-chapters','comic_page_chapters');
}
add_action('admin_menu', 'comic_admin_pages_add');

//Enqueue admin scripts for fancy effects
function wc_admin_scripts() {
	wp_enqueue_script('admin-forms');
}
add_action('admin_init','wc_admin_scripts');

//Add comic post to chapter
function add_post_to_chapter($id,$chapter,$overwrite=true){		
	$taxonomies = get_the_taxonomies($id);
	$has_chapter = (array_key_exists('chapter',$taxonomies)) ? true : false;
	
	if($has_chapter):
		if($overwrite)
			wp_delete_object_term_relationships($id,'chapter');
		else
			return;
	endif;
	
	wp_set_object_terms($id,intval($chapter),'chapter');
}

//Remove any associated chapter information whenever a comic post is deleted	
function remove_post_from_chapter($id){
	wp_delete_object_term_relationships($id,'chapter');
}
add_action('delete_post','remove_post_from_chapter');

//Make sure any new comic posts or unassigned comic posts are assigned to the current chapter
if(-1 != get_comic_current_chapter()):
	function add_post_to_current_chapter($id){
		$cats = get_the_category($id);
		
		foreach($cats as $cat)
			if($cat->cat_ID == get_comic_category())
				$comic = true;
	
		if($comic)
			add_post_to_chapter($id,get_comic_current_chapter(),false);
		else
			return;
	}
	add_action('save_post','add_post_to_current_chapter');
endif;
?>
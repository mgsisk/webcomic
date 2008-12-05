<?php
//Add the "chapter" taxonomy and flushes the rewrite rules
function register_chapters(){
	register_taxonomy('chapter','post',array('hierarchical' => true, 'update_count_callback' => '_update_post_term_count'));
}
add_action('init','register_chapters');


//Displays the chapter the current post is associated with, linked to the first page in the chapter
function the_chapter(){
	$output = get_the_chapter(false,'link');
	echo $output;
}

//Displays the volume the current post is associated with, linked to the first page in the volume
function the_volume(){
	$output = get_the_chapter('volume','link');
	echo $output;
}

//Retrieves the chapter associated with the current post or information about the specified chapter
function get_the_chapter($new=false,$info=false){
	global $post;
	
	$chapter = ($new) ? get_a_chapter($new) : wp_get_object_terms($post->ID,'chapter');
	if(!$new) $chapter = $chapter[0];
	
	if(!$chapter || is_wp_error($chapter)) return 'N/A';
	
	$chapter_name = $chapter->name;
	$chapter_description = $chapter->description;
	$chapter_pages = $chapter->count;
	
	if(0 != $chapter->parent):
		$chapter_posts = get_objects_in_term(intval($chapter->term_id),'chapter');
		$chapter_link = get_the_comic($chapter_posts[0],'post');
	else:
		$volume_chapters = get_term_children($chapter->term_id,'chapter');
		foreach($volume_chapters as $the_chapter):
			$chapter_pages += get_the_chapter($the_chapter,'pages');
		endforeach;
		
		$volume_first_chapter = min($volume_chapters);
		$chapter_posts = get_objects_in_term(intval($volume_first_chapter),'chapter');
		$chapter_link = get_the_comic($chapter_posts[0],'post');
	endif;
	
	switch($info):
		case 'link': return '<a href="'.$chapter_link.'" title="Go to the beginning of '.$chapter_name.'">'.$chapter_name.'</a>';
		case 'post': return $chapter_link;
		case 'title': return $chapter_name;
		case 'description': return $chapter_description;
		case 'pages': return $chapter_pages;
		default: return $chapter;
	endswitch;
}

//Retrieves information related to a specific chapter (internal use only)
function get_a_chapter($chapter){
	global $post;
	
	if('volume' == $chapter):
		$child = wp_get_object_terms($post->ID,'chapter');
		$the_chapter = get_term($child[0]->parent,'chapter');
	else:
		$the_chapter = get_term($chapter,'chapter');
	endif;
	
	return $the_chapter;
}



//Displays a copmlete list of comic volumes, chapters, and posts
function comic_archive($descriptions=false,$pages=false){
	$collection = _get_term_hierarchy('chapter');
	
	if($hide_empty):
		foreach($collection as $volume => $chapters):
			foreach($chapters as $chapter => $the_chapter):
				if(0 == get_the_chapter($the_chapter,'pages'))
					unset($chapters[$chapter]);
			endforeach;
			if(!$chapters)
				unset($collection[$volume]);
			else
				$collection[$volume] = $chapters;
		endforeach;
	endif;
	
	if(!$collection) return; //Nothin' to show
	
	$output = '<ol class="comic-library">';
	
	foreach($collection as $volume => $chapters):
		sort($chapters); //Make sure they're in the right order
		$description = ($descriptions && get_the_chapter($volume,'description')) ? '<p>'.get_the_chapter($volume,'description').'</p>' : false;
		$the_pages = ($pages && get_the_chapter($chapter,'pages')) ? ' ('.get_the_chapter($volume,'pages').' pages)' : false;
		
		$output .= '<li class="comic-volume-item comic-volume-item-'.$volume.'"><a href="'.get_the_chapter($volume,'post').'">'.get_the_chapter($volume,'title').$the_pages.'</a>'.$description.'<ol class="comic-volume-chapters">';
		
		foreach($chapters as $chapter):
			$chapter_posts = get_objects_in_term($chapter,'chapter');
			if(!$chapter_posts) continue; //Nothin' to show
			
			$description = ($descriptions && get_the_chapter($chapter,'description')) ? '<p>'.get_the_chapter($chapter,'description').'</p>' : false;
			$the_pages = ($pages && get_the_chapter($chapter,'pages')) ? ' ('.get_the_chapter($chapter,'pages').' pages)' : false;
			
			$output .= '<li class="comic-chapter-item comic-chapter-item-'.$chapter.'"><a href="'.get_the_chapter($chapter,'post').'">'.get_the_chapter($chapter,'title').$the_pages.'</a>'.$description.'<ol class="comic-chapter-pages">';
			foreach($chapter_posts as $the_post)
				$output .= '<li class="comic-page-item comic-page-item-'.$the_post.'">'.get_the_comic($the_post,'link').'</li>';
			$output .= '</ol></li>';
		endforeach;
		
		$output .= '</ol></li>';
		
	endforeach;
	
	$output .= '</ol>';
	
	echo $output;
}

//Retrieves all chapters (internal use only)
function get_chapters($args=''){
	$defaults = array('orderby' => 'id','pad_counts' => true);
	$args = wp_parse_args($args, $defaults);
	$chapters = (array) get_terms('chapter',$args);
	return $chapters;
}



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
		foreach($cats as $cat):
			if($cat->cat_ID == get_comic_category()) $comic = true;
		endforeach;
	
		if(!$comic)
			return;
		else
			add_post_to_chapter($id,get_comic_current_chapter(),false);
	}
	add_action('save_post','add_post_to_current_chapter');
endif;
?>
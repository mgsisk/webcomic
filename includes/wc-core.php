<?php
/**
 * This document contains all of the new template tags provided by WebComic
 * for use in WordPress themes.
 * 
 * @package WebComic
 * @since 1.0
 */
 
//
// Comic Post Control
//
 
/**
 * Generates a new WP_Query object containing only posts in the designated comic category.
 * 
 * Be default, this function generates a new WP_Query object containing a single post
 * from the designated comic category. To retrieve all comic posts, use $number=-1. If the
 * $limit parameter is unset, comic_loop will include all comic posts from all series.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @param int $number The number of posts to return.
 * @param str $limit Comma separated list of category ID's to include.
 * @param str $query Appended to the query string used to generate the new WP_Query object.
 * @return obj New WordPress query object.
 */
function comic_loop($number=1,$limit=false,$query=false){
	global $wp_query,$paged;
	$wp_query->in_the_loop=true;
	
	$include = ($limit) ? $limit : implode(',',get_comic_category('all'));
	
	$comics = new WP_Query;
	$comics->query('cat='.$include.'&paged='.$paged.'&posts_per_page='.$number.$query);
	
	return $comics;
}

/**
 * Removes the designated comic category from a standard loop.
 * 
 * This function will remove the designated comic category (and all associated posts)
 * from the regular WordPress loop. It can also generate an entirely new WP_Query
 * object which also ignores the designated comic category. If the 'limit' parameter
 * is unset, ignore_comics will remove all comic posts from the standard loop.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @param int $number The number of posts to return in a new WP_Query object.
 * @param str $limit Comma separated list of category ID's to exclude.
 * @param str $query Appended to the query string used to generate the new WP_Query object.
 * @return obj New WordPress query object (only if $number has been set).
 */
function ignore_comics($number=false,$limit=false,$query=false){
	global $paged;
	
	$exclude = ($limit) ? $limit : '-'.implode(',-',get_comic_category('all'));
	
	if($number):
		$new_query = new WP_Query;
		$new_query->query('cat='.$exclude.'&paged='.$paged.'&posts_per_page='.$number.$query);
		return $new_query;
	endif;
	
	query_posts('cat='.$exclude.'&paged='.$paged);
}

/**
 * Checks if the current post is contained within a valid comic category.
 * 
 * This function checks to see if the current post is contained in any defined
 * comic category. The optional $id parameter allows checking for a specific
 * comic category. Must be used within The Loop.
 * 
 * @package WebComic
 * @since 1.8
 * 
 * @uses get_post_comic_category()
 * 
 * @param int $id A specific comic category ID.
 * @return bool True if the post belongs to a valid comic category.
 */
function in_comic_category($id=false){
	global $post;
	
	$comic_cat = get_post_comic_category($post->ID);
	
	if($id)
		$comic_cat = ($id == $comic_cat) ? true : false;
	
	if($comic_cat)
		return true;
}


//
// Comic Data Retrieval
//

/**
 * Returns information associated with the designated comic.
 * 
 * This function retrieves information related to the designated comic file based on
 * the specified post ID, including all associated comic file URL's (the master file and
 * any associated thumbnails), custom description, transcript, post title, and post
 * permalink.
 * 
 * The $comic parameter is optional when this funciton is used inside a WordPress Loop.
 * If left as false, get_the_comic will attempt to retrieve comic information associated
 * with the current post (if any).
 * 
 * The $category paramter is only necessary (and only used) when $comic is set to 'first',
 * 'last', or 'random'.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @param int|str $comic A valid post id or one of 'first', 'last', or 'rand'.
 * @param int $category A valid category ID; required when $comic equals 'first', 'last', or 'random'.
 * @return arr An array containing comic information.
 */
function get_the_comic($comic=false,$category=false){
	load_webcomic_domain();
	
	global $wpdb,$post;
	
	if($comic):
		$new_post = $comic;
		
		switch($comic):
			case 'first':  $order = 'post_date asc'; break;
			case 'last':   $order = 'post_date desc'; break;
			case 'random': $order = 'rand()'; break;
		endswitch;
		
		if($order && $category):
			$category = get_term($category,'category');
			$new_post = $wpdb->get_var("SELECT $wpdb->posts.id FROM $wpdb->posts,$wpdb->term_relationships where post_type = 'post' and post_status = 'publish' and $wpdb->posts.id = $wpdb->term_relationships.object_id and $wpdb->term_relationships.term_taxonomy_id = '".$category->term_taxonomy_id."' order by ".$order);
		endif;
		
		$new_post = &get_post($new_post);
	endif;
	
	if($comic && !$new_post) //Specified post information could not be found
		return;
	
	$comic_post = ($comic) ? $new_post : $post;
	
	switch(get_option('comic_name_format')):
		case 'date': $comic_name = mysql2date(get_option('comic_name_format_date'), $comic_post->post_date); break;
		case 'slug': $comic_name = $comic_post->post_name; break;
		default:     $comic_name = get_post_meta($comic_post->ID,'comic_filename',true);
	endswitch;
	
	if(!$comic_name) //No comic name could be found
		return;
	
	if(1 < count(get_comic_category('all')))
		$comic_dir = ($category) ? $category->term_id : get_post_comic_category($comic_post->ID);
	
	$comic_files = glob(get_comic_directory('abs',false,$comic_dir).'*.*');
	
	if(!is_array($comic_files)) //Could not access comic directory
		return;
	
	foreach(array_keys($comic_files) as $key):
		if(false !== strpos(basename($comic_files[$key]),$comic_name)):
			$comic_file = basename($comic_files[$key]);
			break;
		endif;
	endforeach;
	
	if(!$comic_file) //The post could not be matched with a comic
		return;
	
	$output['file']        = get_comic_directory('url',false,$comic_dir).$comic_file;
	$output['id']          = (int) $comic_post->ID;
	$output['title']       = $comic_post->post_title;
	$output['description'] = (get_post_meta($comic_post->ID,'comic_description',true)) ? get_post_meta($comic_post->ID,'comic_description',true) : $comic_post->post_title;
	$output['link']        = get_permalink($comic_post->ID);
	$output['class']       = 'comic-item comic-item-'.$comic_post->ID;
	if(get_post_meta($comic_post->ID,'comic_transcript',true))
		$output['transcript']  = (function_exists('Markdown')) ? Markdown(get_post_meta($comic_post->ID,'comic_transcript',true)) : get_post_meta($comic_post->ID,'comic_transcript',true);
	
	$comic_thumbs      = array();
	$comic_thumb_files = glob(get_comic_directory('abs',true,$comic_dir).'*.*');
	
	foreach(array_keys($comic_thumb_files) as $key):
		if(false !== strpos(basename($comic_thumb_files[$key]),$comic_name)):
			array_push($comic_thumbs,basename($comic_thumb_files[$key]));
		endif;
	endforeach;
	
	foreach($comic_thumbs as $comic_thumb):
		if(strpos($comic_thumb,'thumb') && !$output['thumb'])
			$output['thumb'] = get_comic_directory('url',true,$comic_dir).$comic_thumb;
		if(strpos($comic_thumb,'medium') && !$output['medium'])
			$output['medium'] = get_comic_directory('url',true,$comic_dir).$comic_thumb;
		if(strpos($comic_thumb,'large') && !$output['large'])
			$output['large'] = get_comic_directory('url',true,$comic_dir).$comic_thumb;
	endforeach;
	
	return $output;
}

/**
 * Returns information associated with the designated chapter.
 * 
 * This function retrieves information related to the designated chapter from
 * WebComic's "chapter" taxonomy. The taxonomy itself is split into chapters,
 * volumes, and series.
 * 
 * When used inside a WordPress Loop, this function returns the chapter information
 * associated with the current post (if any). Posts can only be assigned to chapters,
 * so setting the $chapter parameter to 'volume' or 'series' will let the function
 * know that we want the volume or series the current post belongs to (not the chapter).
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @uses get_the_comic()
 * @uses get_post_chapters()
 * 
 * @param int|str $chapter A valid chapter term_id, 'volume', or 'series'.
 * @return array An array containing all of the chapter information.
 */
function get_the_chapter($chapter=false){
	global $post;
	
	if(is_int($chapter)):
		$chapter = get_term($chapter,'chapter');
		if(!$chapter->parent)
			$css = 'series';
		elseif(!get_term_children($chapter->term_id,'chapter'))
			$css = 'chapter';
		else
			$css = 'volume';
	else:
		$chapters = get_post_chapters($post->ID);
		if('series' == $chapter):
			$chapter = $chapters['series'];
			$css = 'series';
		elseif('volume' == $chapter):
			$chapter = $chapters['volume'];
			$css = 'volume';
		else:
			$chapter = $chapters['chapter'];
			$css = 'chapter';
		endif;
	endif;
	
	if(!$chapter || is_wp_error($chapter)) //No chapter could be retrieved
		return;
	
	$posts_flat = get_objects_in_term((int) $chapter->term_id,'chapter');
		
	if($posts_flat):
		foreach(array_keys($posts_flat) as $post_key):
			$chapter_post = &get_post($posts_flat[$post_key]);
			if('publish' == $chapter_post->post_status && 'post' == $chapter_post->post_type)
				$posts_sort[$chapter_post->ID] = strtotime($chapter_post->post_date);
		endforeach;
		
		if(!empty($posts_sort)):
			$first_post = array_keys($posts_sort,min($posts_sort));
			$last_post  = array_keys($posts_sort,max($posts_sort));
		endif;
	endif;
	
	$output['id']          = (int) $chapter->term_id;
	$output['title']       = $chapter->name;
	$output['slug']        = $chapter->slug;
	$output['description'] = $chapter->description;
	$output['pages']       = (int) $chapter->count;
	$output['class']       = 'comic-'.$css.'-item comic-'.$css.'-item-'.$chapter->term_id;
	$output['parent']      = (int) $chapter->parent;
	$output['link']        = get_term_link((int) $chapter->term_id,'chapter');
	$output['feed']        = get_term_link((int) $chapter->term_id,'chapter').'feed/';
	$output['first']       = get_the_comic($first_post[0]);
	$output['last']        = get_the_comic($last_post[0]);
	
	return $output;
}

/**
 * Returns all series, volume, chapter, and post information for the entire site.
 * 
 * This function returns a multidimensional, hiearchical array with four tiers.
 * The first tier contains all of the fomic series, each of which corresponds to 
 * a selected comic category.
 * 
 * Each series contains a second teir (designated by the 'volumes' key) that contains
 * all of the volume information for the comic. Each volume contains a third teir
 * (designated by the 'chapters' key) that contains all of that volumes chapter information.
 * 
 * Each chapter then contains a fourth tier (designated by the 'posts' key) that contains
 * all of that chapters comic information, as retrieved by get_the_comic().
 * 
 * This allows for simple manipulation of all information related to any comic series using
 * standard PHP loops like foreach(). For a basic example, see the Comic Archives section
 * below.
 * 
 * @package WebComic
 * @since 1.4
 * 
 * @uses get_the_comic()
 * @uses get_the_chapter()
 * 
 * @param arr|str $args Arguments accepted by get_terms(). See wp-includes/taxonomy.php.
 * @param bool $reverse_posts Reverses the order of comic posts.
 * @return arr Multidimensional array containig series, volume, chapter, and comic information.
 */
function get_the_collection($args='',$reverse_posts=false){
	$defaults = array('orderby' => 'id', 'order' => 'ASC', 'hide_empty' => true);
	$args = wp_parse_args($args, $defaults);
	
	$collection = get_terms('chapter',$args);
	
	if(!$collection) //No collection could be found
		return;
	
	$chapters = array();
	
	foreach(array_keys($collection) as $key):
		if(!$collection[$key]->parent):
			$series[$collection[$key]->term_id] = get_the_chapter((int) $collection[$key]->term_id);
			$series[$collection[$key]->term_id]['volumes'] = array();
		else:
			$chapters[$collection[$key]->term_id] = get_the_chapter((int) $collection[$key]->term_id);
		endif;
	endforeach;
	
	foreach(array_keys($chapters) as $key):
		if(array_key_exists($chapters[$key]['parent'],$series)):
			$chapters[$key]['chapters'] = array();
			$volumes[$key] = $chapters[$key];
			$series[$chapters[$key]['parent']]['volumes'][$key] = $chapters[$key];
			unset($chapters[$key]);
		else:
			$chapters[$key]['posts'] = array();
		endif;
	endforeach;
	
	foreach(array_keys($chapters) as $key):
		$posts_flat = get_objects_in_term($key,'chapter');
		
		if($posts_flat):
			foreach(array_keys($posts_flat) as $post_key):
				$chapter_post = get_post($posts_flat[$post_key]);
				if('publish' == $chapter_post->post_status && 'post' == $chapter_post->post_type)
					$posts_sort[$chapter_post->ID] = strtotime($chapter_post->post_date);
			endforeach;
			
			natsort($posts_sort);
			
			$posts_order = ($reverse_posts) ? array_reverse($posts_sort,true) : $posts_sort;
			
			foreach(array_keys($posts_order) as $the_post)
				$chapter_posts[$the_post] = get_the_comic($the_post);
			
			$chapters[$key]['posts'] = $chapter_posts;
		endif;
		
		unset($posts_flat,$posts_sort,$posts_order,$chapter_posts);
		
		$series[$volumes[$chapters[$key]['parent']]['parent']]['volumes'][$chapters[$key]['parent']]['chapters'][$key] = $chapters[$key];
	endforeach;
	
	return $series;
}

/**
 * Returns the designated comic thumbnail URL from a $comic array.
 * 
 * This is a utility function designed to retrieve the appropriate image URL based
 * on the specified size from an array generated with get_the_comic(). It accepts two
 * parameters: the comic array and the size (one of 'large', 'medium', or 'thumb').
 * 
 * If the specified size is not found in the array, this function will attempt to
 * retrieve the next smallest size (large > medium > thumb). If no comic thumbnail can
 * be retrieved (or no size is specified) the standard comic image is returned.
 * 
 * @package Webcomic
 * @since 1.7
 * 
 * @param arr $comic A comic array generated by get_the_comic().
 * @param str $size The size of the comic image to return.
 * @return str URL to specified comic image (or next closest size).
 */
function get_comic_image($comic,$size=false){
	if(!is_array($comic)) //Must provide an array
		return;
	
	if('large' != $size && 'medium' != $size && 'thumb' != $size)
		$size = $comic['file'];
	if('large' == $size)
		$size = ($comic['large']) ? $comic['large'] : 'medium';
	if('medium' == $size)
		$size = ($comic['medium']) ? $comic['medium'] : 'thumb';
	if('thumb' == $size)
		$size = ($comic['thumb']) ? $comic['thumb'] : $comic['file'];
	
	return $size;
}

/**
 * Retrieves the comic category for a given post.
 * 
 * This is a utility function to determine which (if any) comic category
 * a given post belongs to. If a match is found, the comic category ID
 * is returned immediately.
 * 
 * @package WebComic
 * @since 1.8
 * 
 * @param int $post_id A WordPress post post ID.
 * @return int|bool Comic category ID or false.
 */
function get_post_comic_category($post_id=false){
	global $post;
	
	$id = ($post_id) ? (int) $post_id : $post->ID;
	
	$post_cats  = wp_get_object_terms($id,'category',array('fields' => 'ids'));
	$comic_cats = get_comic_category('all');
	
	foreach($post_cats as $post_cat)
		foreach($comic_cats as $comic_cat)
			if($post_cat == $comic_cat)
				return (int) $comic_cat;
}

/**
 * Retrieves the chapter objects for a given post.
 * 
 * This is a utility function used to generate an array of taxonomy
 * objects for the current posts chapter, volume, and series.
 * 
 * @package WebComic
 * @since 1.8
 * 
 * @param obj|int $post A WordPress post object or post ID.
 * @return array An array containg chapter taxonomy objects.
 */
function get_post_chapters($post_id=false){
	global $post;
	
	$id = ($post_id) ? (int) $post_id : $post->ID;
	
	$chapters = wp_get_object_terms($id,'chapter');
	
	if(!$chapters) //The post does not beling to any chapters
		return;
	
	foreach($chapters as $value):
		if(!$value->parent)
			$the_chapters['series'] = $value;
		elseif(!get_term_children($value->term_id,'chapter'))
			$the_chapters['chapter'] = $value;
		else
			$the_chapters['volume'] = $value;
	endforeach;
	
	return $the_chapters;
}

//
// Comic Display
//

/**
 * Displays the comic associated with the current post.
 * 
 * This is a shortcut function for displaying the comic image associated with the
 * current post. The comic may optionally be linked to an adjacent comic post.
 * 
 * @uses get_the_comic()
 * @uses get_the_chapter()
 * @uses get_comic_image()
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @param str $size The size of the comic image to return.
 * @param str $link Links the comic to the next comic, optionally limited to the current volume or chapter.
 * @param str $previous Link the image to the previous comic (defaults to next).
 */
function the_comic($size=false,$link=false,$previous=false){
	global $wp_query;
	
	$comic =  get_the_comic();
	
	if($comic):
		if($link):
			if(!is_single()):
				$wp_query->is_single = true;
				$adj_post = get_adjacent_post(true,'',$previous);
				$wp_query->is_single = false;
			else:
				$adj_post = get_adjacent_post(true,'',$previous);
			endif;
			
			if($adj_post):
				if('chapter' == $link || 'volume' == $link):
					$adj_chapters = get_post_chapters($post->ID);
					$chapter      = get_the_chapter($limit);
					$adj_chapter  = ('volume' == $limit) ? $adj_chapters['volume'] : $adj_chapters['chapter'];
					
					if($adj_chapter->term_id == $chapter['id']):
						$before = '<a href="'.get_permalink($adj_post->ID).'">';
						$after = '</a>';
					endif;
				else:
					$before = '<a href="'.get_permalink($adj_post->ID).'">';
					$after = '</a>';
				endif;
			endif;
		endif;
		echo $before.'<img src="'.get_comic_image($comic,$size).'" alt="'.$comic['title'].'" title="'.$comic['description'].'" class="'.$comic['class'].'" />'.$after;
	endif;
}

/**
 * Displays the comic embed code associated with the current post.
 * 
 * This function displays an input box with XHTML useful for sharing the
 * comic on other websites (embed code). It must be used within a
 * WordPress Loop.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @uses get_the_comic()
 * @uses get_comic_image()
 * 
 * @param str $size The size of the comic image the embed code will display.
 * @param str $before Text to display before the embed code input box.
 * @param str $after Text to display after the embed code input box.
 */
function the_comic_embed($size='medium',$before='<p>',$after='</p>'){
	$comic = get_the_comic();
	
	if($comic)
		echo $before.'<input class="comic-embed-code" type="text" readonly="readonly" value="&lt;div class=&quot;embedded-comic&quot;&gt;&lt;p&gt;&lt;a href=&quot;'.$comic['link'].'&quot;&gt;&lt;img src=&quot;'.get_comic_image($comic,$size).'&quot; alt=&quot;'.$comic['title'].'&quot; title=&quot;'.$comic['description'].'&quot; /&gt;&lt;/a&gt;&lt;/p&gt;&lt;p&gt;&lt;cite&gt;'.$comic['title'].' | &lt;a href=&quot;'.get_option('home').'/&quot; title=&quot;'.get_option('blogdescription').'&quot;&gt;'.get_option('blogname').'&lt;/a&gt;&lt;/cite&gt;&lt;/p&gt;&lt;/div&gt;" />'.$after;
}

/**
 * Displays the comic transcript associated with the current post.
 * 
 * This is a shortcut function for displaying the comic transcript associated with the
 * current post. If the 'comic_transcript_emial' option is set and no transcript exists,
 * this function will display a form allowing users to submit a transcript for the comic.
 * Must be used within a WordPress Loop.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @uses get_the_comic()
 * 
 * @param str $title The text to display for the "title" of the transcript.
 * @param str $submit The text to display for the "title" of the submit transcript form.
 */
function the_comic_transcript( $title = '', $submit = '' ) {
	load_webcomic_domain();
	global $comic_trans_message;
	
	$comic = get_the_comic();
	
	if ( $comic[ 'transcript' ] ) {
		$title = ( $title ) ? $title : __( 'View Transcript', 'webcomic' );
		echo '<div class="comic-transcript-title"><span>' . $title . '</span></div><div class="comic-transcript">' . $comic['transcript'] . '</div>';
	} elseif ( get_option( 'comic_transcript_email' ) ) {
		$title = ( $submit ) ? $submit : __( 'Submit Transcript', 'webcomic' );
		echo '<div class="comic-transcript-title"><span>' . $title . '</span></div><div class="comic-transcript">
			<form action="" method="post" id="comic-transcript-form">
				<div id="comic-trans-message">' . $comic_trans_message . '</div>
				<p><label for="comic_trans_from">' . __( 'Name', 'webcomic' ) . '</label><input type="text" name="comic_trans_from" id="comic_trans_from" /></p>
				<p><label for="comic_trans_mail">' . __( 'E-mail', 'webcomic' ) . '</label><input type="text" name="comic_trans_mail" id="comic_trans_mail" /></p>
				<p><label for="comic_trans_captcha">' . __( "Is fire hot or cold?", "webcomic" ) . '</label><input type="text" maxlength="1" name="comic_trans_captcha" id="comic_trans_captcha" /></p>
				<p><label for="comic_trans_script">' . __( 'Transcript', 'webcomic' ) . '</label><textarea rows="7" cols="40" name="comic_trans_script" id="comic_trans_script"></textarea></p>
				<p>' . sprintf( __( 'Transcripts can be formatted using <a href="%s">HTML</a> or <a href="%s">Markdown</a> <a href="%s">Extra</a>.', 'webcomic' ), 'http://www.w3schools.com/html/default.asp', 'http://daringfireball.net/projects/markdown/syntax', 'http://michelf.com/projects/php-markdown/extra/' ) . '</p>
				<p>
					<input type="submit" value="' . __('Submit','webcomic') . '" />
					<input type="hidden" name="comic_trans_title" value="' . $comic['title'] . '" />
					<input type="hidden" name="comic_trans_human" value="0" />
					<input type="hidden" name="comic_trans_submit" value="1" />
				</p>
			</form>
		</div>';
	}
}

//
// Comic Post Navigation
//

/**
 * Displays the standard set of comic navigation links.
 * 
 * This is a shortcut function for displaying the standard set of comic
 * navigation links (first, back, next, last). Must be used within a
 * WordPress Loop.
 * 
 * @package WebComic
 * @since   1.0
 * 
 * @uses first_comic_link()
 * @uses next_comic_link()
 * @uses previous_comic_link()
 * @uses last_comic_link()
 * 
 * @param str $limit Navigation link boundary, one of 'chapter or 'volume'.
 * @param str $sep The text to display between each comic link.
 * @param str $fstlabel The text to display for the first comic link.
 * @param str $prelabel The text to display for the previous comic link.
 * @param str $nxtlabel The text to display for the next comic link.
 * @param str $lstlabel The text to display for the last comic link.
 */
function comics_nav_link($limit=false,$sep='',$fstlabel='',$prelabel='',$nxtlabel='',$lstlabel=''){	
	first_comic_link($limit,$fstlabel);
	if($sep) echo '<span class="comic-link-separator">'.$sep.'</span>';
	previous_comic_link($limit,$prelabel);
	if($sep) echo '<span class="comic-link-separator">'.$sep.'</span>';
	next_comic_link($limit,$nxtlabel);
	if($sep) echo '<span class="comic-link-separator">'.$sep.'</span>';
	last_comic_link($limit,$lstlabel);
}

/**
 * Displays a link to the first comic.
 * 
 * This is a shortcut function for displaying a link to the first comic in
 * the defined comic category, useful for building standard comic navigation.
 * 
 * If the optional $limit parameter is set, the 'first' comic changes from first
 * in the defined comic category to first in the current volume or chapter. Must
 * be used within a WordPress Loop.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @uses get_the_comic()
 * @uses get_the_chapter()
 * @uses get_post_comic_category()
 * 
 * @param str $limit Navigation link boundary, one of 'chapter or 'volume'.
 * @param str $label The text to display for the link.
 * 
 */
function first_comic_link($limit=false,$label=''){
	load_webcomic_domain();
	
	global $post;
	
	$label = ($label) ? $label : __('&laquo; First','webcomic');
	if($limit):
		$chapter     = get_the_chapter($limit);
		$css         = ('volume' == $limit) ? $limit : 'chapter';
		$link        = $chapter['first']['link'];
		$description = $chapter['first']['description'];
		$css         = ($post->ID == $chapter['first']['id']) ? ' current-comic' : '' ;
	else:
		$comic_cat   = get_post_comic_category($post->ID);
		$comic       = get_the_comic('first',$comic_cat);
		$link        = $comic['link'];
		$description = $comic['description'];
		$css         = ($post->ID == $comic['id']) ? ' current-comic' : '' ;
	endif;
	
	$class = 'first-comic-link'.$css;
	
	echo '<a href="'.$link.'" class="'.$class.'" title="'.$description.'"><span>'.$label.'</span></a>';
}

/**
 * Displays a link to the last comic.
 * 
 * This is a shortcut function for displaying a link to the last comic in
 * the defined comic category, useful for building standard comic navigation.
 * 
 * If the optional $limit parameter is set, the 'last' comic changes from last
 * in the defined comic category to last in the current volume or chapter. Must
 * be used within a WordPress Loop.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @uses get_the_comic()
 * @uses get_the_chapter()
 * @uses get_post_comic_category()
 * 
 * @param str $limit Navigation link boundary, one of 'chapter or 'volume'.
 * @param str $label The text to display for the link.
 */
function last_comic_link($limit=false,$label=''){
	load_webcomic_domain();
	
	global $post;
	
	$label = ($label) ? $label : __('Last &raquo;','webcomic');
	
	if($limit):
		$chapter     = get_the_chapter($limit);
		$css         = ('volume' == $limit) ? $limit : 'chapter';
		$link        = $chapter['last']['link'];
		$description = $chapter['last']['description'];
		$css         = ($post->ID == $chapter['last']['id']) ? ' current-comic' : '' ;
	else:
		$comic_cat   = get_post_comic_category($post->ID);
		$comic       = get_the_comic('last',$comic_cat);
		$link        = $comic['link'];
		$description = $comic['description'];
		$css         = ($post->ID == $comic['id']) ? ' current-comic' : '' ;
	endif;
	
	$class = 'last-comic-link'.$css;
	
	echo '<a href="'.$link.'" class="'.$class.'" title="'.$description.'"><span>'.$label.'</span></a>';
}

/**
 * Displays a link to the previous comic.
 * 
 * This function uses the standard WordPress single post navigation functions
 * to create a previous comic link. Because this link is only available
 * on single post pages previous_comic_link() temporarily defines is_single as
 * true if necessary, allowing it to be used anywhere (like the homepage).
 * 
 * If the optional $limit parameter is set, 'previous' is limited to the current
 * volume or chapter. Must be used within a WordPress Loop.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @uses get_the_comic()
 * @uses get_the_chapter()
 * @uses get_post_chapters()
 * 
 * @param str $limit Navigation link boundary, one of 'chapter or 'volume'.
 * @param str $label The text to display for the link
 */
function previous_comic_link($limit=false,$label=''){
	load_webcomic_domain();
	
	global $wp_query,$post;
	
	$label = ($label) ? $label : __('&lt; Back','webcomic');
	
	if(!is_single()):
		$wp_query->is_single = true;
		$prev = get_adjacent_post(true);
		$wp_query->is_single=false;
	else:
		$prev = get_adjacent_post(true);
	endif;
	
	if($limit):
		$prev_chapters = get_post_chapters($prev->ID);
		if($prev_chapters):
			$chapter      = get_the_chapter($limit);
			$prev_chapter = ('volume' == $limit) ? get_the_chapter((int) $prev_chapters['volume']->term_id) : get_the_chapter((int) $prev_chapters['chapter']->term_id);
		endif;
		$comic       = ($chapter['id'] == $prev_chapter['id']) ? get_the_comic($prev->ID) : get_the_comic($post->ID);
		$link        = $comic['link'];
		$description = $comic['description'];
		$css         = ($prev_chapters && $chapter['id'] == $prev_chapter['id']) ? '' : ' current-comic';
	else:
		$comic       = ($prev) ? get_the_comic($prev->ID) : get_the_comic($post->ID);
		$link        = $comic['link'];
		$description = $comic['description'];
		$css         = ($prev) ? '' : ' current-comic';
	endif;
	
	$class = 'previous-comic-link'.$css;
	
	echo '<a href="'.$link.'" class="'.$class.'" title="'.$description.'"><span>'.$label.'</span></a>';
}

/**
 * Displays a link to the next comic.
 * 
 * This function uses the standard WordPress single post navigation functions
 * to create a next comic link. Because this link is only available
 * on single post pages next_omic_link() temporarily defines is_single as
 * true if necessary, allowing it to be used anywhere (like the homepage).
 * 
 * If the optional $limit parameter is set, 'next' is limited to the current
 * volume or chapter. Must be used within a WordPress Loop.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @uses get_the_comic()
 * @uses get_the_chapter()
 * @uses get_post_chapters()
 * 
 * @param str $limit Navigation link boundary, one of 'chapter or 'volume'.
 * @param str $label The text to display for the link.
 */
function next_comic_link($limit=false,$label=''){
	load_webcomic_domain();
	
	global $wp_query,$post;
	
	$label = ($label) ? $label : __('Next &gt;','webcomic');
	
	if(!is_single()):
		$wp_query->is_single = true;
		$next = get_adjacent_post(true,'',false);
		$wp_query->is_single=false;
	else:
		$next = get_adjacent_post(true,'',false);
	endif;
	
	if($limit):
		$next_chapters = get_post_chapters($next->ID);
		if($next_chapters):
			$chapter      = get_the_chapter($limit);
			$next_chapter = ('volume' == $limit) ? get_the_chapter((int) $next_chapters['volume']->term_id) : get_the_chapter((int) $next_chapters['chapter']->term_id);
			$css          = ('volume' == $limit) ? $limit : 'chapter';
		endif;
		$comic       = ($chapter['id'] == $next_chapter['id']) ? get_the_comic($next->ID) : get_the_comic($post->ID);
		$link        = $comic['link'];
		$description = $comic['description'];
		$css         = ($next_chapters && $chapter['id'] == $next_chapter['id']) ? '' : ' current-comic';
	else:
		$comic       = ($next) ? get_the_comic($next->ID) : get_the_comic($post->ID);
		$link        = $comic['link'];
		$description = $comic['description'];
		$css         = ($next) ? '' : ' current-comic';
	endif;
	
	$class = 'next-comic-link'.$css;
	
	echo '<a href="'.$link.'" class="'.$class.'" title="'.$description.'"><span>'.$label.'</span></a>';
}

/**
 * Displays a randomly selected comic in the specified format.
 * 
 * This is a shortcut function for displaying a link to a randomly selected comic.
 * If the optional $series parameter is not set, the function will randomly select
 * a comic category.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @uses get_the_comic()
 * @uses get_comic_image()
 * 
 * @param str $label The link text or image size (one of 'thumb', 'medium', 'large', or 'full').
 * @param str $series A specific comic category to randomly select comics from.
 */
function random_comic_link($label=false,$series=false){
	if(!$series):
		$comic_cats = get_comic_category('all');
		$random_key = array_rand($comic_cats);
		$series     = $comic_cats[$random_key];
	endif;
	
	$comic = get_the_comic('random',$series);
	
	if($comic):
		if($label):
			switch($label):
				case 'thumb' : $image = true; break;
				case 'medium': $image = true; break;
				case 'large' : $image = true; break;
				case 'full'  : $image = true; break;
				default      : $image = false;
			endswitch;
			$the_comic = ($image) ? '<img src="'.get_comic_image($comic,$label).'" alt="'.$comic['title'].'" />' : $label;
		else:
			$the_comic = $comic['title'];
		endif;
		
		echo '<a href="'.$comic['link'].'" title="'.$comic['description'].'">'.$the_comic.'</a>';
	endif;
}
/** Deprecated; use random_comic_link() above. */
function random_comic($label=false,$series=false){
	random_comic_link($label,$serires);
}

//
// Comic Chapter Navigation
//

/**
 * Displays the standard set of chapter navigation links.
 * 
 * This is a shortcut function for displaying the standard set of chapter
 * navigation links (first, back, next, last).
 * 
 * @package WebComic
 * @since 1.8
 * 
 * @uses first_chapter_link()
 * @uses next_chapter_link()
 * @uses previous_chapter_link()
 * @uses last_chapter_link()
 * 
 * @param arr $collection Multi-dimensional comic collection array produced by get_the_collection() or a sort argument for get_the_collection()
 * @param str $volume Links point to series volumes instead of chapters.
 * @param str $bound Where the links should point to, one of 'first','last', or 'page'.
 * @param str $sep The text to display between each comic link.
 * @param str $fstlabel The text to display for the first chapter link.
 * @param str $prelabel The text to display for the previous chapter link.
 * @param str $nxtlabel The text to display for the next chapter link.
 * @param str $lstlabel The text to display for the last chapter link.
 */
function chapters_nav_link($collection='id',$volume=false,$bound='first',$sep='',$fstlabel='',$prelabel='',$nxtlabel='',$lstlabel=''){	
	$collection = (is_array($collection)) ? $collection : get_the_collection('orderby='.$collection);
	
	first_chapter_link($collection,$volume,$bound,$fstlabel);
	echo '<span class="chapter-link-separator">'.$sep.'</span>';
	previous_chapter_link($collection,$volume,$bound,$prelabel);
	echo '<span class="chapter-link-separator">'.$sep.'</span>';
	next_chapter_link($collection,$volume,$bound,$nxtlabel);
	echo '<span class="chapter-link-separator">'.$sep.'</span>';
	last_chapter_link($collection,$volume,$bound,$lstlabel);
}

/**
 * Displays a link to the first chapter of the current series.
 * 
 * This function displays a link to the first volume or chapter of the
 * current series. Like the_chapter_link(), the link itself can point
 * to the beginning or end of the chapter or the chapter archive page
 * and must be used within a WordPress Loop.
 * 
 * @package Web Comic
 * @since 1.8
 * 
 * @uses get_the_chapter()
 * @uses get_post_chapters()
 * 
 * @param arr $collection Multi-dimensional comic collection array produced by get_the_collection() or a sort argument for get_the_collection()
 * @param str $volume The chapter link to display, one of false or 'volume'
 * @param str $bound Where the link should point to, one of 'first','last', or 'page'.
 * @param str $label The text to display for the link.
 */
function first_chapter_link($collection='id',$volume=false,$bound='first',$label=''){
	load_webcomic_domain();
	
	global $post;
	
	$post_chapters = get_post_chapters($post->ID);	
	
	if(!$post_chapters) //Post does not belong to any chapters
		return;
	
	$collection = (is_array($collection)) ? $collection : get_the_collection('orderby='.$collection);
	$type       = ($volume) ? 'Volume' : 'Chapter';
	$chapter    = ($volume) ? array_shift($collection[$post_chapters['series']->term_id]['volumes']) : array_shift($collection[$post_chapters['series']->term_id]['volumes'][$post_chapters['volume']->term_id]['chapters']);
	
	$label = ($label) ? $label : sprintf(__('&laquo; First %s','webcomic'),$type);
	switch($bound):
		case 'page':
			$link  = $chapter['link'];
			$title = sprintf(__('Go to the %s archive','webcomic'),$chapter['title']);
		break;
		case 'last':
			$link  = $chapter['last']['link'];
			$title = sprintf(__('Go to the end of %s','webcomic'),$chapter['title']);
		break;
		default:
			$link  = $chapter['first']['link'];
			$title = sprintf(__('Go to the beginning of %s','webcomic'),$chapter['title']);
	endswitch;
	
	$type  = strtolower($type);
	if('volume' == $type)
		$css   = ($post_chapters['volume']->term_id == $chapter['id']) ? ' current-'.$type : '';
	else
		$css   = ($post_chapters['chapter']->term_id == $chapter['id']) ? ' current-'.$type : '';
		
	$class = $chapter['class'].' first-'.$type.'-link'.$css;
	
	if($chapter)
		echo '<a href="'.$link.'" class="'.$class.'" title="'.$title.'"><span>'.$label.'</span></a>';
}

/**
 * Displays a link to the last chapter of the current series.
 * 
 * This function displays a link to the last volume or chapter of the
 * current series. Like the_chapter_link(), the link itself can point
 * to the beginning or end of the chapter or the chapter archive page
 * and must be used within a WordPress Loop.
 * 
 * @package Web Comic
 * @since 1.8
 * 
 * @uses get_the_chapter()
 * @uses get_post_chapters()
 * 
 * @param arr $collection Multi-dimensional comic collection array produced by get_the_collection() or a sort argument for get_the_collection()
 * @param str $volume The chapter link to display, one of false or 'volume'
 * @param str $bound Where the link should point to, one of 'first','last', or 'page'.
 * @param str $label The text to display for the link.
 */
function last_chapter_link($collection='id',$volume=false,$bound='first',$label=''){
	load_webcomic_domain();
	
	global $post;
	
	$post_chapters = get_post_chapters($post->ID);	
	
	if(!$post_chapters) //Post does not belong to any chapters
		return;
	
	$collection = ($collection) ? $collection : get_the_collection('orderby='.$collection);
	$type       = ($volume) ? 'Volume' : 'Chapter';
	$chapter    = ($volume) ? array_pop($collection[$post_chapters['series']->term_id]['volumes']) : array_pop($collection[$post_chapters['series']->term_id]['volumes'][$post_chapters['volume']->term_id]['chapters']);
	
	$label = ($label) ? $label : sprintf(__('Last %s &raquo;','webcomic'),$type);
	switch($bound):
		case 'page':
			$link  = $chapter['link'];
			$title = sprintf(__('Go to the %s archive','webcomic'),$chapter['title']);
		break;
		case 'last':
			$link  = $chapter['last']['link'];
			$title = sprintf(__('Go to the end of %s','webcomic'),$chapter['title']);
		break;
		default:
			$link  = $chapter['first']['link'];
			$title = sprintf(__('Go to the beginning of %s','webcomic'),$chapter['title']);
	endswitch;
	
	$type = strtolower($type);
	if('volume' == $type)
		$css = ($post_chapters['volume']->term_id == $chapter['id']) ? ' current-'.$type : '';
	else
		$css = ($post_chapters['chapter']->term_id == $chapter['id']) ? ' current-'.$type : '';
		
	$class = $chapter['class'].' last-'.$type.'-link'.$css;
	
	if($chapter)
		echo '<a href="'.$link.'" class="'.$class.'" title="'.$title.'"><span>'.$label.'</span></a>';
}

/**
 * Displays a link to the previous chapter of the current series.
 * 
 * This function displays a link to the previous volume or chapter of the
 * current series. Like the_chapter_link(), the link itself can point
 * to the beginning or end of the chapter or the chapter archive page
 * and must be used within a WordPress Loop.
 * 
 * @package Web Comic
 * @since 1.8
 * 
 * @uses get_the_chapter()
 * @uses get_post_chapters()
 * 
 * @param arr $collection Multi-dimensional comic collection array produced by get_the_collection() or a sort argument for get_the_collection()
 * @param str $volume The chapter link to display, one of false or 'volume'
 * @param str $bound Where the link should point to, one of 'first','last', or 'page'.
 * @param str $label The text to display for the link.
 */
function previous_chapter_link($collection='id',$volume=false,$bound='first',$label=''){
	load_webcomic_domain();
	
	global $post;
	
	$post_chapters = get_post_chapters($post->ID);	
	
	if(!$post_chapters) //No Collection was provided or the post does not belong to any chapters
		return;
	
	$collection = ($collection) ? $collection : get_the_collection('orderby='.$collection);
	$type       = ('volume' == $chapter) ? 'Volume' : 'Chapter';
	
	if($volume):
		foreach(array_reverse(array_keys($collection[$post_chapters['series']->term_id]['volumes']),true) as $volume_key):
			if($after):
				$chapter = $collection[$post_chapters['series']->term_id]['volumes'][$volume_key];
				break;
			endif;
			$after = ($post_chapters['volume']->term_id == $volume_key) ? true : false;
		endforeach;
	else:
		foreach(array_reverse(array_keys($collection[$post_chapters['series']->term_id]['volumes'][$post_chapters['volume']->term_id]['chapters']),true) as $chapter_key):
			if($after):
				$chapter = $collection[$post_chapters['series']->term_id]['volumes'][$post_chapters['volume']->term_id]['chapters'][$chapter_key];
				break;
			endif;
			$after = ($post_chapters['chapter']->term_id == $chapter_key) ? true : false;
		endforeach;
	endif;
	
	if(!is_array($chapter))
		$chapter = ($volume) ? get_the_chapter('volume') : get_the_chapter();
	
	$label = ($label) ? $label : sprintf(__('&lt; Previous %s','webcomic'),$type);
	switch($bound):
		case 'page':
			$link  = $chapter['link'];
			$title = sprintf(__('Go to the %s archive','webcomic'),$chapter['title']);
		break;
		case 'last':
			$link  = $chapter['last']['link'];
			$title = sprintf(__('Go to the end of %s','webcomic'),$chapter['title']);
		break;
		default:
			$link  = $chapter['first']['link'];
			$title = sprintf(__('Go to the beginning of %s','webcomic'),$chapter['title']);
	endswitch;
	
	$type  = strtolower($type);
	if('volume' == $type)
		$css   = ($post_chapters['volume']->term_id == $chapter['id']) ? ' current-'.$type : '';
	else
		$css   = ($post_chapters['chapter']->term_id == $chapter['id']) ? ' current-'.$type : '';
		
	$class = $chapter['class'].' previous-'.$type.'-link'.$css;
	
	if($chapter)
		echo '<a href="'.$link.'" class="'.$class.'" title="'.$title.'"><span>'.$label.'</span></a>';
}

/**
 * Displays a link to the next chapter of the current series.
 * 
 * This function displays a link to the next volume or chapter of the
 * current series. Like the_chapter_link(), the link itself can point
 * to the beginning or end of the chapter or the chapter archive page
 * and must be used within a WordPress Loop.
 * 
 * @package Web Comic
 * @since 1.8
 * 
 * @uses get_the_chapter()
 * @uses get_post_chapters()
 * 
 * @param arr $collection Multi-dimensional comic collection array produced by get_the_collection() or a sort argument for get_the_collection()
 * @param str $volume The chapter link to display, one of false or 'volume'
 * @param str $bound Where the link should point to, one of 'first','last', or 'page'.
 * @param str $label The text to display for the link.
 */
function next_chapter_link($collection='id',$volume=false,$bound='first',$label=''){
	load_webcomic_domain();
	
	global $post;
	
	$post_chapters = get_post_chapters($post->ID);	
	
	if(!$post_chapters) //Post does not belong to any chapters
		return;
	
	$collection = ($collection) ? $collection : get_the_collection('orderby='.$collection);
	$type       = ($volume) ? 'Volume' : 'Chapter';
	
	if($volume):
		foreach(array_keys($collection[$post_chapters['series']->term_id]['volumes']) as $volume_key):
			if($after):
				$chapter = $collection[$post_chapters['series']->term_id]['volumes'][$volume_key];
				break;
			endif;
			$after = ($post_chapters['volume']->term_id == $volume_key) ? true : false;
		endforeach;
	else:
		foreach(array_keys($collection[$post_chapters['series']->term_id]['volumes'][$post_chapters['volume']->term_id]['chapters']) as $chapter_key):
			if($after):
				$chapter = $collection[$post_chapters['series']->term_id]['volumes'][$post_chapters['volume']->term_id]['chapters'][$chapter_key];
				break;
			endif;
			$after = ($post_chapters['chapter']->term_id == $chapter_key) ? true : false;
		endforeach;
	endif;
	
	if(!is_array($chapter))
		$chapter = ($volume) ? get_the_chapter('volume') : get_the_chapter();
	
	$label = ($label) ? $label : sprintf(__('Next %s &gt;','webcomic'),$type);
	switch($bound):
		case 'page':
			$link  = $chapter['link'];
			$title = sprintf(__('Go to the %s archive','webcomic'),$chapter['title']);
		break;
		case 'last':
			$link  = $chapter['last']['link'];
			$title = sprintf(__('Go to the end of %s','webcomic'),$chapter['title']);
		break;
		default:
			$link  = $chapter['first']['link'];
			$title = sprintf(__('Go to the beginning of %s','webcomic'),$chapter['title']);
	endswitch;
	
	$type  = strtolower($type);
	if('volume' == $type)
		$css = ($post_chapters['volume']->term_id == $chapter['id']) ? ' current-'.$type : '';
	else
		$css = ($post_chapters['chapter']->term_id == $chapter['id']) ? ' current-'.$type : '';
		
	$class = $chapter['class'].' next-'.$type.'-link'.$css;
	
	if($chapter)
		echo '<a href="'.$link.'" class="'.$class.'" title="'.$title.'"><span>'.$label.'</span></a>';
}

/**
 * Displays the chapter link associated with the current post.
 * 
 * This is a shortcut function for displaying a link to the beginning, end,
 * or archive page  of the chapter, volume, or series associated with the
 * current post and must be used within a WordPress Loop.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @uses get_the_chapter()
 * 
 * @param str $chapter The chapter link to display, one of 'volume', or 'series'.
 * @param str $bound Where the link should point to, one of 'first','last', or 'page'.
 * @param str $label The text to display for the link.
 */
function the_chapter_link($chapter=false,$bound='first',$label=''){
	load_webcomic_domain();
	
	$css     = ($chapter) ? $chapter : 'chapter';
	$chapter = get_the_chapter($chapter);
	$label   = ($label) ? $label : $chapter['title'];
	switch($bound):
		case 'page':
			$link  = $chapter['link'];
			$title = sprintf(__('Go to the %s archive','webcomic'),$chapter['title']);
		break;
		case 'last':
			$link  = $chapter['last']['link'];
			$title = sprintf(__('Go to the end of %s','webcomic'),$chapter['title']);
		break;
		default:
			$link  = $chapter['first']['link'];
			$title = sprintf(__('Go to the beginning of %s','webcomic'),$chapter['title']);
	endswitch;
	
	$class = 'current-'.$css.'-link';
	
	if($chapter)
		echo '<a href="'.$link.'" class="'.$class.'" title="'.$title.'"><span>'.$label.'</span></a>';
}

//
// Chapter Archive Pages
//
/**
 * Displays the chapter title on chapter archive pages.
 * 
 * This function displays the chapter title on chapter archive pages. Like
 * single_cat_title() it accepts both $prefix and $display parameters.
 * 
 * @package WebComic
 * @since 1.8
 * 
 * @param str $prefix Prefix to prepend to the chapter name.
 * @param bool $display Returns the chapter title if set to false.
 * @return str The chapter title, if $display is set to false.
 */
function single_chapter_title($prefix='',$display=true){
	$chapter = get_term_by('slug',get_query_var('chapter'),'chapter');
	if(!$display)
		return $chapter->name;
	else
		echo $prefix.$chapter->name;
}

/**
 * Returns the specified chapter description.
 * 
 * This function returns the specified chapter description. If used on
 * a chapter archive page with no defined $chapter the current chapter
 * description is returned.
 * 
 * @package WebComic
 * @since 1.8
 * 
 * @param int $chapter A valid chapter term ID.
 * @return str The chapter description.
 */
function chapter_description($chapter=false){
	if(!$chapter)
		$chapter = get_term_by('slug',get_query_var('chapter'),'chapter');
	else
		$chapter = get_term($chapter,'chapter');
	
	return $chapter->description;
}

/**
 * Returns the specified chapter page count.
 * 
 * This function returns the specified chapter page count. If used on
 * a chapter archive page with no defined $chapter the current chapter
 * page count is returned.
 * 
 * @package WebComic
 * @since 1.8
 * 
 * @param int $chapter A valid chapter term ID.
 * @return str The chapter page count.
 */
function chapter_pages($chapter=false){
	if(!$chapter)
		$chapter = get_term_by('slug',get_query_var('chapter'),'chapter');
	else
		$chapter = get_term($chapter,'chapter');
	
	return $chapter->count;
}

//
// Comic Archive Examples
//

/**
 * Displays a list of recently posted comics.
 * 
 * This is a shortcut function for displaying a list of recently posted comics
 * using comic_loop() and get_the_comic().
 * 
 * @package WebComic
 * @since   1.0
 * 
 * @uses comic_loop()
 * @uses get_the_comic()
 * @uses get_comic_image()
 * 
 * @param int $number The number of comics to display.
 * @param str $label The link text or image size (one of 'thumb', 'medium', 'large', or 'full').
 * @param str $series Comma separated list of category ID's.
 * @param str $before Text to display before each comic.
 * @param str $after Text to display after each comic.
 */
function recent_comics($number=5,$label=false,$series=false,$before='<li>',$after='</li>'){
	$comics = comic_loop($number,$series);
	
	if($comics->have_posts()): while($comics->have_posts()): $comics->the_post();
		$comic = get_the_comic();
		
		if($comic):
			if($label)
				$the_comic = '<img src="'.get_comic_image($comic,$label).'" alt="'.$comic['title'].'" />';
			else
				$the_comic = $comic['title'];
			
			$output .= $before.'<a href="'.$comic['link'].'" title="'.$comic['description'].'">'.$the_comic.'</a>'.$after;
		endif;
	endwhile; endif;
	
	echo $output;
}

/**
 * Displays a form select control with comic posts or chapters.
 * 
 * This function displays a form select control (dropdown box) listing comics
 * or comic chapters, optionally orgnized and/or displayed with additional
 * information.
 * 
 * In the interest of clean output, this function does not generate the javascript
 * necessary to make the dropdown list fully functional. An example of such code
 * using the version of jQuery included with WordPress:
 *
 * jQuery('select.dropdown-comics').change(function(){
 * 		if(jQuery(this).attr('value') != 0){
 * 			window.location = jQuery(this).attr('value');
 * 		}
 * 	});
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @uses comic_loop()
 * @uses get_the_collection()
 * 
 * @param str $category A valid comic category ID.
 * @param str $label The label to display for the very first 'null' option.
 * @param bool $numbers Automatically prepends chapters or posts with a number.
 * @param bool $group Groups posts by chapter or chapters by volume.
 * @param bool $reverse Reverses the order of chapters or posts.
 * @param bool $pages Automatically appends page counts to chapters.
 * @return bool Returns if no collection or no comic posts can be found.
 */
function dropdown_comics($category=false,$label='',$numbers=false,$reverse_posts=false,$group=false,$reverse=false,$pages=false){
	load_webcomic_domain();
	
	$label = ($label) ? $label : __('Quick Archive','webcomic');
	$order = ($reverse) ? 'DESC' : 'ASC';
	
	$output = '<select name="comic-select" class="dropdown-comics"><option value="0">'.$label.'</option>';
	
	if($group):
		$collection = get_the_collection('order='.$order,$reverse_posts);
		
		if(!$collection) //No colleciton could be found.
			return;
			
		foreach($collection as $series):
			if('series' == $group):
				$append  = ($pages) ? ' ('.$series['pages'].')' : '';
				$output .= '<optgroup label="'.$series['title'].$append.'">';
				
				if($numbers)
					$i = ($reverse) ? count($series['volumes']) : 1;	
				foreach($series['volumes'] as $volume):
					$prepend = ($i) ? $i.'. ' : '';
					$append  = ($pages) ? ' ('.$volume['pages'].')' : '';
					$output .= '<option value="'.$volume['first']['link'].'">'.$prepend.$volume['title'].$append.'</option>';
					if($numbers)
						$i = ($reverse) ? $i-1 : $i+1;
				endforeach;
				$output .= '</optgroup>';
			else:
				if(!$category || $category == $series['id']):
					foreach($series['volumes'] as $volume):
						if('volume' == $group):
							$append  = ($pages) ? ' ('.$volume['pages'].')' : '';
							$output .= '<optgroup label="'.$volume['title'].$append.'">';
							if($numbers)
								$i = ($reverse) ? count($volume['chapters']) : 1;	
								
							foreach($volume['chapters'] as $chapter):
								$prepend = ($i) ? $i.'. ' : '';
								$append  = ($pages) ? ' ('.$chapter['pages'].')' : '';
								$output .= '<option value="'.$chapter['first']['link'].'">'.$prepend.$chapter['title'].$append.'</option>';
								if($numbers)
									$i = ($reverse) ? $i-1 : $i+1;
							endforeach;
							$output .= '</optgroup>';
						else:
							foreach($volume['chapters'] as $chapter):			
								$append  = ($pages) ? ' ('.$chapter['pages'].')' : '';
								$output .= '<optgroup label="'.$chapter['title'].$append.'">';
								if($numbers)
									$i = ($reverse_posts) ? count($chapter['posts']) : 1;	
								foreach($chapter['posts'] as $the_post):
									$prepend = ($i) ? $i.'. ' : '';
									$output .= '<option value="'.$the_post['link'].'">'.$prepend.$the_post['title'].'</option>';
									if($numbers)
										$i = ($reverse_posts) ? $i-1 : $i+1;
								endforeach;
								$output .= '</optgroup>';
							endforeach;
						endif;
					endforeach;
				endif;
			endif;
		endforeach;
	else:
		$order = ($reverse_posts) ? 'ASC' : 'DESC';
		$comics = comic_loop(-1,$category,'&order='.$order);
		if($numbers)
			$i = ($reverse_posts) ? 1 : $comics->post_count;
		
		if($comics->have_posts()):
			while($comics->have_posts()):
				$comics->the_post();
				$prepend = ($i) ? $i.'. ' : '';
				$output .= '<option value="'.get_permalink().'">'.$prepend.get_the_title().'</option>';
				if($numbers)
					$i = ($reverse_posts) ? $i+1 : $i-1;
			endwhile;
		else:
			return;
		endif;
	endif;
	
	$output .= '</select>';
	
	echo $output;
}

/**
 * Displays the comic archive organized by series, volume, and chapter.
 * 
 * This is a fully functional example function that displays a simple comic
 * archive using get_the_collection().
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @uses get_the_collection()
 * 
 * @param int $category A valid comic category ID.
 * @param bool $format The format for comic links, one of false (text), 'full', 'large', 'medium', or 'thumb'.
 * @param bool $reverse_posts Reverses the order of comic posts.
 * @param bool $reverse Reverses the order of series, volumes, and chapters.
 * @param bool $description Displays series, chapter, and volume descriptions.
 * @param bool $pages Displays series, chapter, and volume page counts.
 * @return bool Returns false if there is no collection.
 */
function comic_archive($category=false,$format='',$reverse_posts=false,$reverse=false,$descriptions=false,$pages=false){
	$reverse = ($reverse) ? 'DESC' : 'ASC';
	$collection = get_the_collection('order='.$reverse,$reverse_posts);
	
	if(!$collection) //No collection could be found.
		return;
	
	$single = ($category) ? 'single-series ' : '';
	$output = '<ol class="'.$single.'comic-library">';
	
	foreach($collection as $series):
		if(!$category && 1 < count(get_comic_category('all'))):
			$description = ($descriptions && $series['description']) ? '<p>'.$series['description'].'</p>' : false;
			$the_pages = ($pages && $series['pages']) ? ' ('.$series['pages'].' pages)' : false;
			
			$output .= '<li class="'.$series['class'].'"><a href="'.$series['first']['link'].'">'.$series['title'].$the_pages.'</a>'.$description.'<ol class="comic-series-volumes">';
		endif;
	
		if(!$category || $category == $series['id']):
			foreach($series['volumes'] as $volume):
				$description = ($descriptions && $volume['description']) ? '<p>'.$volume['description'].'</p>' : false;
				$the_pages = ($pages && $volume['pages']) ? ' ('.$volume['pages'].' pages)' : false;
				
				$output .= '<li class="'.$volume['class'].'"><a href="'.$volume['first']['link'].'">'.$volume['title'].$the_pages.'</a>'.$description.'<ol class="comic-volume-chapters">';
				foreach($volume['chapters'] as $chapter):
					$description = ($descriptions && $chapter['description']) ? '<p>'.$chapter['description'].'</p>' : false;
					$the_pages = ($pages && $chapter['pages']) ? ' ('.$chapter['pages'].' pages)' : false;
					
					$output .= '<li class="'.$chapter['class'].'"><a href="'.$chapter['first']['link'].'">'.$chapter['title'].$the_pages.'</a>'.$description.'<ol class="comic-chapter-pages">';
					foreach($chapter['posts'] as $the_post):
						$the_post_label = ($format) ? '<img src="'.get_comic_image($the_post,$format).'" alt="" />' : $the_post['title'];
						$output .= '<li class="comic-page-item comic-page-item-'.$the_post['id'].'"><a href="'.$the_post['link'].'" title="'.$the_post['description'].'">'.$the_post_label.'</a></li>';
					endforeach;
					$output .= '</ol></li>';
				endforeach;
				$output .= '</ol></li>';
			endforeach;
		endif;
		
		if(!$category):
			$output .= '</ol></li>';
		endif;
	endforeach;
	
	$output .= '</ol>';
	
	echo $output;
}
?>
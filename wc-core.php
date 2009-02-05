<?php
/**
 * This document contains all of the new template tags provided by WebComic
 * for use in WordPress themes.
 * 
 * @package WebComic
 * @since 1.0
 */
 
/**
 * Generates a new WP_Query object containing only posts in the designated comic category.
 * 
 * Be default, this function generates a new WP_Query object containing a single post
 * from the designated comic category. To retrieve all comic posts, use $number=-1.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @param int $number The number of posts to return.
 * @param str $query Appended to the query string used to generate the new WP_Query object.
 * @return obj New WordPress query object.
 */
function comic_loop($number=1,$query=false){
	global $wp_query,$paged;
	$wp_query->in_the_loop=true;
	
	$comics = new WP_Query;
	$comics->query('cat='.get_comic_category().'&posts_per_page='.$number.'&paged='.$paged.$query);
	
	return $comics;
}

/**
 * Removes the designated comic category from a standard loop.
 * 
 * This function will remove the designated comic category (and all associated posts)
 * from the regular WordPress loop. It can also generate an entirely new WP_Query
 * object which also ignores the designated comic category.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @param int $number The number of posts to return in a new WP_Query object.
 * @param str $query Appended to the query string used to generate the new WP_Query object.
 * @return obj New WordPress query object (only if $number has been set).
 */
function ignore_comics($number=false,$query=false){
	global $paged;
	
	if($number):
		$new_query = new WP_Query;
		$new_query->query('cat=-'.get_comic_category().'&posts_per_page='.$number.'&paged='.$paged.$query);
		return $new_query;
	endif;
	
	query_posts('cat=-'.get_comic_category().'&paged='.$paged);
}

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
 * @package WebComic
 * @since 1.0
 * 
 * @param int|str $comic A valide post id or one of 'first', 'last', or 'rand'.
 * @return bool|arr False if no comic can be found or an array containing comic information.
 */
function get_the_comic($comic=false){
	load_webcomic_domain();
	
	global $wpdb,$post;
	
	$path  = ABSPATH.get_comic_directory();
	$tpath  = ABSPATH.get_comic_directory(true);
	if(!is_dir($path)) //The comic directory or comic thumbnails directory can't be accessed
		die('<p class="error"><strong>'.__('Webcomic could not access your comic directory. Please make sure that the directory exists and that your <em>Comic Category</em> setting is set correctly.','webcomic').'</strong></p>');
	
	if($comic):
		$new_post = $comic;
		
		switch($comic):
			case 'first':  $order = 'post_date asc'; break;
			case 'last':   $order = 'post_date desc'; break;
			case 'random': $order = 'rand()'; break;
		endswitch;
		
		if($order)
			$new_post = $wpdb->get_var("SELECT $wpdb->posts.id FROM $wpdb->posts,$wpdb->term_relationships where post_type = 'post' and post_status = 'publish' and $wpdb->posts.id = $wpdb->term_relationships.object_id and $wpdb->term_relationships.term_taxonomy_id = '".get_comic_category()."' order by ".$order);
		if(($comic == 'first' || $comic == 'last') && $new_post == $post->ID) //Link hiding magic
			return;
		
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
	
	$dir = opendir($path);
	while(($file = readdir($dir)) !== false):
		if(false !== strpos($file,$comic_name)):
			$comic_file = $file;
			break;
		endif;
	endwhile;
	closedir($dir);
	
	if(!$comic_file) //No comic file could be found
		return;
	
	$output['id']          = $comic_post->ID;
	$output['title']       = $comic_post->post_title;
	$output['file']        = get_settings('siteurl').'/'.get_comic_directory().$comic_file;
	$output['link']        = get_permalink($comic_post->ID);
	$output['class']       = 'comic-item comic-time-'.$comic_post->ID;
	$output['description'] = (get_post_meta($comic_post->ID,'comic_description',true)) ? get_post_meta($comic_post->ID,'comic_description',true) : $comic_post->post_title;
	
	if(get_post_meta($comic_post->ID,'comic_transcript',true))
		$output['transcript']  = (function_exists('Markdown')) ? Markdown(get_post_meta($comic_post->ID,'comic_transcript',true)) : get_post_meta($comic_post->ID,'comic_transcript',true);
	
	$comic_thumbs = array();
	
	$dir = opendir($tpath);
	while(($file = readdir($dir)) !== false):
		if(false !== strpos($file,$comic_name)):
			array_push($comic_thumbs,$file);
		endif;
	endwhile;
	closedir($dir);
	
	foreach($comic_thumbs as $comic_thumb):
		if(strpos($comic_thumb,'thumb'))
			$output['thumb'] = get_settings('siteurl').'/'.get_comic_directory(true).$comic_thumb;
		if(strpos($comic_thumb,'medium'))
			$output['medium'] = get_settings('siteurl').'/'.get_comic_directory(true).$comic_thumb;
		if(strpos($comic_thumb,'large'))
			$output['large'] = get_settings('siteurl').'/'.get_comic_directory(true).$comic_thumb;
	endforeach;
	
	return $output;
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
 * Returns information associated with the designated Chapter.
 * 
 * This function retrieves information related to the designated Chapter from
 * WebComic's "chapter" taxonomy. The taxonomy itself is split into chapters
 * and volumes.
 * 
 * When used inside a WordPress Loop, this function returns the chapter information
 * associated with the current post (if any). Posts can only be assigned to chapters,
 * so setting the $chapter parameter to 'volume' will let the function know that
 * we want the volume the current post belongs to (not the chapter).
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @uses get_the_comic()
 * 
 * @param int|str $chapter A valid chapter term_id or 'volume'.
 * @return array An array containing all of the chapter information.
 */
function get_the_chapter($chapter=false){
	global $post;
	
	if(is_int($chapter)):
		$chapter = get_term($chapter,'chapter');
	else:
		$volume = ('volume' == $chapter) ? true : false;
		$chapter = wp_get_object_terms($post->ID,'chapter');
		$chapter = ($volume) ? get_term($chapter[0]->parent,'chapter') : $chapter[0];
	endif;
	
	if(!$chapter || is_wp_error($chapter))
		return false;

	$chapter_post_times = array();
	$chapter_posts = ($chapter->parent) ? get_objects_in_term(intval($chapter->term_id),'chapter') : get_objects_in_term(intval(@min(get_term_children($chapter->term_id,'chapter'))),'chapter');
	foreach($chapter_posts as $chapter_post):
		$chapter_post = &get_post($chapter_post);
		if('publish' == $chapter_post->post_status && 'post' == $chapter_post->post_type)
			array_push($chapter_post_times,$chapter_post->post_date);
	endforeach;
	if(!empty($chapter_post_times))
		$chapter_first_post = array_keys($chapter_post_times,min($chapter_post_times));
	
	if(!$chapter->parent):
		$chapters = get_term_children($chapter->term_id,'chapter');
		foreach($chapters as $the_chapter):
			$the_chapter = get_term($the_chapter,'chapter');
			$page_count += $the_chapter->count;
		endforeach;
	endif;
	
	$chapter_comic = get_the_comic($chapter_posts[$chapter_first_post[0]]);
	
	$output['id']          = $chapter->term_id;
	$output['title']       = $chapter->name;
	$output['description'] = $chapter->description;
	$output['link']        = $chapter_comic['link'];
	$output['pages']       = ($chapter->parent) ? $chapter->count : $page_count;
	$output['class']       = ($chapter->parent) ? 'comic-chapter-item comic-chapter-'.$chapter->term_id : 'comic-volume-item comic-volume-'.$chapter->term_id;
	$output['parent']      = $chapter->parent;
	
	return $output;
}

/**
 * Returns all volume, chapter, and post information for the entire comic.
 * 
 * This function returns a multidimensional, hiearchical array with three tiers.
 * The first tier contains all of the volume information for the comic. Each
 * volume contains a second teir (designated by the 'chapters' key) that contain
 * all of that volumes chapter information. Each chapter then contains a third
 * tier (designated by the 'posts' key) that contains all of that chapters comic
 * information, as retrieved by get_the_comic().
 * 
 * This allows for simple manipulation of all information related to the comic using
 * standard PHP loops like foreach(). For a basic example, see the comic_archive()
 * function.
 * 
 * @package WebComic
 * @since 1.4
 * 
 * @uses get_the_chapter()
 * 
 * @param array $args Arguments accepted by get_terms(). See wp-includes/taxonomy.php.
 * @return array Multidimensional array containig volume, chapter, and comic information.
 */
function get_the_collection($args = ''){
	$defaults = array('orderby' => 'id', 'order' => 'ASC', 'hide_empty' => true);
	$args = wp_parse_args($args, $defaults);
	
	$collection_flat = get_terms('chapter',$args);
	
	if(!$collection_flat) //No collection could be found
		return;
		
	$collection = array();
	
	foreach($collection_flat as $key => $value):
		if(!$value->parent):
			$collection[$value->term_id] = get_the_chapter(intval($value->term_id));
			$collection[$value->term_id]['chapters'] = array();
			unset($collection_flat[$key]);
		endif;
	endforeach;
	
	foreach($collection as $volume):
		foreach($collection_flat as $key => $value):
			if($volume['id'] == $value->parent):
				$collection[$volume['id']]['chapters'][$value->term_id] = get_the_chapter(intval($value->term_id));
				
				$chapter_posts_flat = get_objects_in_term($value->term_id,'chapter');
				$chapter_sorted_posts = array();
				
				foreach($chapter_posts_flat as $chapter_post):
					$chapter_post = &get_post($chapter_post);
					if('publish' == $chapter_post->post_status && 'post' == $chapter_post->post_type)
						$chapter_sorted_posts[$chapter_post->ID] = strtotime($chapter_post->post_date);
				endforeach;
				
				natsort($chapter_sorted_posts);
				
				$chapter_ordered_posts = ('DESC' == $args['order']) ? array_reverse($chapter_sorted_posts,true) : $chapter_sorted_posts;
				$chapter_posts = array();
				
				foreach($chapter_ordered_posts as $the_post => $timestamp)
					$chapter_posts[$the_post] = get_the_comic($the_post);
					//array_push($chapter_post_keys,$the_post);
				
				$collection[$volume['id']]['chapters'][$value->term_id]['posts'] = $chapter_posts;
				
				unset($collection_flat[$key]);
			endif;
		endforeach;
	endforeach;
	
	return $collection;
}

/**
 * Displays the comic associated with the current post.
 * 
 * This is a shortcut function for displaying the comic image associated with the
 * current post.
 * 
 * @uses get_the_comic()
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @param str $size The size of the comic image to return.
 */
function the_comic($size=false){
	$comic =  get_the_comic();
	
	echo '<img src="'.get_comic_image($comic,$size).'" alt="'.$comic['title'].'" title="'.$comic['description'].'" />';
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
	
	echo $before.'<input class="comic-embed-code" type="text" readonly="readonly" value="&lt;div class=&quot;embedded-comic&quot;&gt;&lt;p&gt;&lt;a href=&quot;'.$comic['link'].'&quot;&gt;&lt;img src=&quot;'.get_comic_image($comic,$size).'&quot; alt=&quot;'.$comic['title'].'&quot; title=&quot;'.$comic['description'].'&quot; /&gt;&lt;/a&gt;&lt;/p&gt;&lt;p&gt;&lt;cite&gt;'.$comic['title'].' | &lt;a href=&quot;'.get_option('home').'/&quot; title=&quot;'.get_option('blogdescription').'&quot;&gt;'.get_option('blogname').'&lt;/a&gt;&lt;/cite&gt;&lt;/p&gt;&lt;/div&gt;" />'.$after;
}

/**
 * Displays the comic transcript associated with the current post.
 * 
 * This is a shortcut function for displaying the comic transcript associated with the
 * current post and must be used within a WordPress Loop.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @uses get_the_comic()
 * 
 * @param str $title The text tod display for the "title" of the transcript.
 */
function the_comic_transcript($title=''){
	load_webcomic_domain();
	
	$comic = get_the_comic();
	$title = ($title) ? $title : __('View Transcript','webcomic');
	
	if($comic['transcript'])
		echo '<div class="transcript-title"><span>'.$title.'</span></div><div class="transcript">'.$comic['transcript'].'</div>';
}

/**
 * Displays a randomly selected comic in the specified format.
 * 
 * This is a shortcut function for displaying a link to a randomly selected comic.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @uses get_the_comic()
 * @uses get_comic_image()
 * 
 * @param str $format  The format to display the comic in
 * @param str $display The text or image size to display
 */
function random_comic($format='text',$display=false){
	$comic = get_the_comic('random');
	
	if('text' == $format)
		$the_comic = ($display) ? $display : $comic['title'];
	if('image' == $format)
		$the_comic = '<img src="'.get_comic_image($comic,$display).'" alt="'.$comic['title'].'" />';
	
	echo '<a href="'.$comic['link'].'" title="'.$comic['description'].'">'.$the_comic.'</a>';
}

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
 * @param str $format The format to display the comic in
 * @param str $display The text or image size to display
 * @param str $before Text to display before each comic.
 * @param str $after Text to display after each comic.
 */
function recent_comics($number=5,$format='text',$display=false,$before='<li>',$after='</li>'){
	$comics = comic_loop($number);
	
	if($comics->have_posts()): while($comics->have_posts()): $comics->the_post();
		$comic = get_the_comic();
		
		if('text' == $format)
			$the_comic = ($display) ? $display : $comic['title'];
		if('image' == $format)
			$the_comic = '<img src="'.get_comic_image($comic,$display).'" alt="'.$comic['title'].'" />';
		
		$output .= $before.'<a href="'.$comic['link'].'" title="'.$comic['description'].'">'.$the_comic.'</a>'.$after;
	endwhile; endif;
	
	echo $output;
}

/**
 * Displays the standard set of comic navigation links.
 * 
 * This is a shortcut function for displaying the standard set of comic
 * navigation links (first, back, next, last).
 * 
 * @package WebComic
 * @since   1.0
 * 
 * @uses get_the_comic()
 * @uses first_comic_link()
 * @uses next_comic_link()
 * @uses previous_comic_link()
 * @uses last_comic_link()
 * 
 * @param str $sep The text to display between each comic link.
 * @param str $fstlabel The text to display for the first comic link.
 * @param str $prelabel The text to display for the previous comic link.
 * @param str $nxtlabel The text to display for the next comic link.
 * @param str $lstlabel The text to display for the last comic link.
 */
function comics_nav_link($sep=' &bull; ',$fstlabel='&laquo; First',$prelabel='&lt; Back',$nxtlabel='Next &gt;',$lstlabel='Last &raquo;'){
	$midsep = $sep;
	
	if(!get_the_comic('first') || !get_the_comic('last'))
		$midsep = '';
	
	first_comic_link($fstlabel);
	if(get_the_comic('first'))
		echo $sep;
	previous_comic_link($prelabel);
	echo $midsep;
	next_comic_link($nxtlabel);
	if(get_the_comic('last'))
		echo $sep;
	last_comic_link($lstlabel);
}

/**
 * Displays a link to the first comic.
 * 
 * This is a shortcut function for displaying a link to the first comic in
 * the defined comic category, useful for building standard comic navigation.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @uses get_the_comic()
 * 
 * @param str $label The text to display for the link.
 */
function first_comic_link($label='&laquo; First'){
	$comic = get_the_comic('first');
	
	if($comic)
		echo '<a href="'.$comic['link'].'" title="'.$comic['description'].'">'.$label.'</a>';
}

/**
 * Displays a link to the last comic.
 * 
 * This is a shortcut function for displaying a link to the last comic in
 * the defined comic category, useful for building standard comic navigation.
 * 
 * @package WebComic
 * @since   1.0
 * 
 * @uses    get_the_comic()
 * 
 * @param   str $label The text to display for the link.
 */
function last_comic_link($label='Last &raquo;'){
	$comic = get_the_comic('last');
	
	if($comic)
		echo '<a href="'.$comic['link'].'" title="'.$comic['description'].'">'.$label.'</a>';
}

/**
 * Displays a link to the previous comic.
 * 
 * This function uses the standard WordPress single post navigation function
 * to create a previous comic link. Because this link is only available
 * on single post pages previous_comic_link() temporarily defines is_single as
 * true if necessary, allowing it to be used anywhere (like the homepage).
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @param str $label The text to display for the link.
 */
function previous_comic_link($label='&lt; Back'){
	global $wp_query;
	
	if(!is_single()):
		$wp_query->is_single=true;
		$trick = true;
	endif;
	
	previous_post_link('%link',$label,TRUE);
	
	if($trick)
		$wp_query->is_single=false;
}

/**
 * Displays a link to the next comic.
 * 
 * This function uses the standard WordPress single post navigation function
 * to create a next comic link. Because this link is only available
 * on single post pages next_omic_link() temporarily defines is_single as
 * true if necessary, allowing it to be used anywhere (like the homepage).
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @param str $label The text to display for the link.
 */
function next_comic_link($label='Next &gt;'){
	global $wp_query;
	
	if(!is_single()):
		$wp_query->is_single=true;
		$trick = true;
	endif;
	
	next_post_link('%link',$label,TRUE);
	
	if($trick)
		$wp_query->is_single=false;
}

/**
 * Displays the chapter link associated with the current post.
 * 
 * This is a shortcut function for displaying a link to the beginning
 * of the chapter associated with the current post and must be used within
 * a WordPress Loop.
 * 
 * @package WebComic
 * @since   1.0
 * 
 * @uses get_the_chapter()
 */
function the_chapter(){
	load_webcomic_domain();
	
	$chapter = get_the_chapter();
	
	if($chapter)
		echo '<a href="'.$chapter['link'].'" title="'.sprintf(__('Go to the beginning of %1$s','webcomic'),$chapter['title']).'">'.$chapter['title'].'</a>';
}

/**
 * Displays the volume link associated with the current post.
 * 
 * This is a shortcut function for displaying a link to the beginning
 * of the volume associated with the current post and must be used within
 * a WordPress Loop.
 * 
 * @package WebComic
 * @since   1.0
 * 
 * @uses    get_the_chapter()
 */
function the_volume(){
	load_webcomic_domain();
	
	$volume = get_the_chapter('volume');
	
	if($volume)
		echo '<a href="'.$volume['link'].'" title="'.sprintf(__('Go to the beginning of %1$s','webcomic'),$volume['title']).'">'.$volume['title'].'</a>';
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
 * @param str $label The label to display for the very first 'null' option.
 * @param bool $group Groups posts by chapter or chapters by volume.
 * @param bool $numbers Automatically prepends chapters or posts with a number.
 * @param bool $pages Automatically appends page counts to chapters.
 * @param bool $reverse Reverses the order of chapters or posts.
 * @return bool Returns false if no collection or no comic posts can be found.
 */
function dropdown_comics($label='',$group=false,$reverse=false,$numbers=false,$pages=false){
	load_webcomic_domain();
	
	$label = ($label) ? $label : __('Quick Archive','webcomic');
	$order = ($reverse) ? 'DESC' : 'ASC';
	
	$output = '<select name="comic-select" class="dropdown-comics"><option value="0">'.$label.'</option>';
	
	if($group):
		$collection = get_the_collection(array('order' => $order));
		
		if(!$collection) //No colleciton could be found.
			return;

		foreach($collection as $volume):
			if('volumes' === $group):
				$append = ($pages) ? ' ('.$volume['pages'].')' : '';
				$output .= '<optgroup label="'.$volume['title'].$append.'">';
				
				if($numbers)
					$i = ($reverse) ? count($volume['chapters']) : 1;	
					
				foreach($volume['chapters'] as $chapter):
					$prepend = ($i) ? $i.'. ' : '';
					$append = ($pages) ? ' ('.$chapter['pages'].')' : '';
					
					$output .= '<option value="'.$chapter['link'].'">'.$prepend.$chapter['title'].$append.'</option>';
					
					if($numbers)
						$i = ($reverse) ? $i-1 : $i+1;
				endforeach;
				
				$output .= '</optgroup>';
			else:
				foreach($volume['chapters'] as $chapter):			
					$append = ($pages) ? ' ('.$chapter['pages'].')' : '';
					$output .= '<optgroup label="'.$chapter['title'].$append.'">';
				
					if($numbers)
						$i = ($reverse) ? count($chapter['posts']) : 1;	
					
					foreach($chapter['posts'] as $the_post):
						$prepend = ($i) ? $i.'. ' : '';
						$output .= '<option value="'.$the_post['link'].'">'.$prepend.$the_post['title'].'</option>';
						
						if($numbers)
							$i = ($reverse) ? $i-1 : $i+1;
					endforeach;
					$output .= '</optgroup>';
				endforeach;
			endif;
		endforeach;
	else:
		$order = ($reverse) ? 'ASC' : 'DESC';
		$comics = comic_loop(-1,'&order='.$order);
		if($numbers)
			$i = ($reverse) ? 1 : $comics->post_count;
		
		if($comics->have_posts()):
			while($comics->have_posts()): $comics->the_post();
				$prepend = ($i) ? $i.'. ' : '';
				$output .= '<option value="'.get_permalink().'">'.$prepend.get_the_title().'</option>';
				if($numbers)
					$i = ($reverse) ? $i+1 : $i-1;
			endwhile;
		else:
			return;
		endif;
	endif;
	
	$output .= '</select>';
	
	echo $output;
}

/**
 * Displays the comic archive organized by volume and chapter.
 * 
 * This is a fully functional example function that displays a simple comic
 * archive using get_the_collection.
 * 
 * @package WebComic
 * @since 1.0
 * 
 * @uses get_the_collection()
 * 
 * @param bool $description Displays chapter and volume descriptions.
 * @param bool $pages Displays chapter and volume page counts.
 * @param bool $reverse Reverses the order of volumes, chapters, and pages.
 * @return bool Returns false if there is no collection.
 */
function comic_archive($descriptions=false,$pages=false,$reverse=false){
	$reverse = ($reverse) ? 'DESC' : 'ASC';
	$collection = get_the_collection(array('order' => $reverse));
	
	if(!$collection) //No collection could be found.
		return;
	
	$output = '<ol class="comic-library">';
	
	foreach($collection as $volume):
		$description = ($descriptions && $volume['description']) ? '<p>'.$volume['description'].'</p>' : false;
		$the_pages = ($pages && $volume['pages']) ? ' ('.$volume['pages'].' pages)' : false;
		
		$output .= '<li class="'.$volume['class'].'"><a href="'.$volume['link'].'">'.$volume['title'].$the_pages.'</a>'.$description.'<ol class="comic-volume-chapters">';
		
		foreach($volume['chapters'] as $chapter):
			
			$description = ($descriptions && $chapter['description']) ? '<p>'.$chapter['description'].'</p>' : false;
			$the_pages = ($pages && $chapter['pages']) ? ' ('.$chapter['pages'].' pages)' : false;
			
			$output .= '<li class="'.$chapter['class'].'"><a href="'.$chapter['link'].'">'.$chapter['title'].$the_pages.'</a>'.$description.'<ol class="comic-chapter-pages">';
			
			foreach($chapter['posts'] as $the_post):
				$output .= '<li class="comic-page-item comic-page-item-'.$the_post['id'].'"><a href="'.$the_post['link'].'" title="'.$the_post['description'].'">'.$the_post['title'].'</a></li>';
			endforeach;
				
			$output .= '</ol></li>';
		endforeach;
		
		$output .= '</ol></li>';
		
	endforeach;
	
	$output .= '</ol>';
	
	echo $output;
}
?>
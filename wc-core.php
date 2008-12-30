<?php
//Generates a new comic loop
function comic_loop($number=1){
	global $wp_query,$paged;
	$wp_query->in_the_loop=true;
	
	$comics = new WP_Query;
	$comics->query('cat='.get_comic_category().'&posts_per_page='.$number.'&paged='.$paged);
	
	return $comics;
}

//Removes comic posts from the standard loop or generates a new loop that excludes comic psots
function ignore_comics($number=false){
	global $paged;
	
	if($number):
		$new_query = new WP_Query;
		$new_query->query('posts_per_page='.$number.'&cat=-'.get_comic_category().'&paged='.$paged);
		return $new_query;
	endif;
	
	query_posts('cat=-'.get_comic_category().'&paged='.$paged);
}



//Retrieves the comic information associated with the specified or current post
function get_the_comic($comic=false,$format='post',$display=false){
	global $wpdb,$post;
	
	if($comic):
		$new_post = $comic;
		
		switch($comic):
			case 'first': $order = ' post_date asc'; break;
			case 'last':  $order = ' post_date desc'; break;
			case 'rand':  $order = ' rand()'; break;
		endswitch;
		
		if($order)
			$new_post = $wpdb->get_var("SELECT $wpdb->posts.id FROM $wpdb->posts,$wpdb->term_relationships where post_type = 'post' and post_status = 'publish' and $wpdb->posts.id =  $wpdb->term_relationships.object_id and $wpdb->term_relationships.term_taxonomy_id = '".get_comic_category()."' order by".$order);
			
		if(($comic == 'first' || $comic == 'last') && $new_post == $post->ID)
			return;
		
		$new_post = &get_post($new_post);
	endif;
	
	if($comic && !$new_post)
		return;
	
	$comic_id = ($comic) ? $new_post->ID : $post->ID;
	$comic_date = ($comic) ? $new_post->post_date : $post->post_date;
	$comic_slug = ($comic) ? $new_post->post_name : $post->post_name;
	$comic_title = ($comic) ? get_the_title($new_post->ID) : get_the_title($post->ID);
	$comic_perma = ($comic) ? get_permalink($new_post->ID) : get_permalink($post->ID);
	$comic_transcript = (function_exists('Markdown')) ? Markdown(get_post_meta($comic_id,'comic_transcript',true)) : get_post_meta($comic_id,'comic_transcript',true);
	$comic_description = (get_post_meta($comic_id,'comic_description',true)) ? get_post_meta($comic_id,'comic_description',true) : $comic_title;
	if(!$display):
		switch($format):
			case 'link': $display = $comic_title; break;
			case 'transcript': $display = __('View Transcript','webcomic');
		endswitch;
	endif;
	
	if('transcript' == $format && (!$comic_transcript || "\n" == $comic_transcript))
		return;
	
	switch(get_option('comic_name_format')):
		case 'date': $comic_name = mysql2date(get_option('comic_name_format_date'), $comic_date); break;
		case 'slug': $comic_name = $comic_slug; break;
		default:     $comic_name = get_post_meta($comic_id,'comic_filename',true);
	endswitch;
	
	if(!$comic_name)
		return;
	
	$path = ABSPATH.get_comic_directory();
	$http_path = get_settings('siteurl').'/'.get_comic_directory();
	
	if('large' == $display || 'medium' == $display || 'thumb' == $display):
		$thumb = true;
		$path = ABSPATH.get_comic_directory(true);
		$http_path = get_settings('siteurl').'/'.get_comic_directory(true);
	endif;
	
	if(!is_dir($path))
		die('<p class="error"><strong>'.__('Webcomic could not access your comic directory.','webcomic').'</strong></p>');
	
	$dir = opendir($path);
	while(($file = readdir($dir)) !== false):
		if(false !== strstr($file,$comic_name)):
			if($thumb):
				if(false !== strstr($file,$display)):
					$comic_file = $file;
					break;
				else:
					continue;
				endif;
			endif;
			$comic_file = $file;
			break;
		endif;
	endwhile;
	closedir($dir);
	
	if(!$comic_file)
		return;
	else
		$comic_url = $http_path.$comic_file;
	
	switch($format):
		case 'transcript':   $output = '<div class="transcript-title"><span>'.$display.'</san></div><div class="transcript">'.$comic_transcript.'</div>'; break;
		case 'embed':        $output = '<input class="comic-embed-code" type="text" readonly="readonly" value="&lt;div class=&quot;embedded-comic&quot;&gt;&lt;p&gt;&lt;a href=&quot;'.$comic_perma.'&quot;&gt;&lt;img src=&quot;'.$comic_url.'&quot; alt=&quot;'.$comic_title.'&quot; title=&quot;'.$comic_description.'&quot; /&gt;&lt;/a&gt;&lt;/p&gt;&lt;p&gt;&lt;cite&gt;'.$comic_title.' | &lt;a href=&quot;'.get_option('home').'/&quot; title=&quot;'.get_option('blogdescription').'&quot;&gt;'.get_option('blogname').'&lt;/a&gt;&lt;/cite&gt;&lt;/p&gt;&lt;/div&gt;" />'; break;
		case 'full':         $output = '<a href="'.$comic_perma.'" title="'.$comic_description.'"><img src="'.$comic_url.'" alt="'.$comic_title.'" /></a>'; break;
		case 'image':        $output = '<img src="'.$comic_url.'" alt="'.$comic_title.'" title="'.$comic_description.'" />'; break;
		case 'link':         $output = '<a href="'.$comic_perma.'" title="'.$comic_description.'">'.$display.'</a>'; break;
		case 'file':         $output = $comic_url; break;
		case 'post':default: $output = $comic_perma;
	endswitch;
	
	return $output;
}

//Returns the chapter associated with the specified or current post
function get_the_chapter($chapter=false){
	global $post;
	
	if($chapter):
		$chapter = get_term($chapter,'chapter');
	else:
		$chapter = wp_get_object_terms($post->ID,'chapter');
		$chapter = $chapter[0];
	endif;
	
	if(!$chapter || is_wp_error($chapter))
		return false;

	$chapter_post_times = array();
	$chapter_posts = get_objects_in_term(intval($chapter->term_id),'chapter');
	foreach($chapter_posts as $chapter_post):
		$chapter_post = &get_post($chapter_post);
		if('publish' == $chapter_post->post_status && 'post' == $chapter_post->post_type)
			array_push($chapter_post_times,$chapter_post->post_date);
	endforeach;
	if(!empty($chapter_post_times))
		$chapter_first_post = array_keys($chapter_post_times,min($chapter_post_times));
	
	$output['title'] = $chapter->name;
	$output['description'] = $chapter->description;
	$output['pages'] = $chapter->count;
	$output['link'] = get_the_comic($chapter_posts[$chapter_first_post[0]]);
	$output['class'] = 'comic-chapter-item comic-chapter-'.$chapter->term_id;
	$output['parent'] = $chapter->parent;
	
	return $output;
}

//Returns the volume associated with the specified or current post
function get_the_volume($volume=false){
	global $post;
	
	if($volume):
		$volume = get_term($volume,'chapter');
	else:
		$chapter = wp_get_object_terms($post->ID,'chapter');
		$volume = get_term($chapter[0]->parent,'chapter');
	endif;
	
	if(!$volume || is_wp_error($volume))
		return false;
	
	$chapter_post_times = array();
	$chapter_posts = get_objects_in_term(intval(@min(get_term_children($volume->term_id,'chapter'))),'chapter');
	foreach($chapter_posts as $chapter_post):
		$chapter_post = &get_post($chapter_post);
		if('publish' == $chapter_post->post_status && 'post' == $chapter_post->post_type):
			array_push($chapter_post_times,$chapter_post->post_date);
		endif;
	endforeach;
	if(!empty($chapter_post_times))
		$chapter_first_post = array_keys($chapter_post_times,min($chapter_post_times));
		
	$chapters = get_term_children($volume->term_id,'chapter');
	foreach($chapters as $chapter):
		$chapter = get_term($chapter,'chapter');
		$volume_pages += $chapter->count;
	endforeach;
	
	$output['title'] = $volume->name;
	$output['description'] = $volume->description;
	$output['pages'] = intval($volume_pages);
	$output['link'] = get_the_comic($chapter_posts[$chapter_first_post[0]]);
	$output['class'] = 'comic-volume-item comic-volume-item-'.$volume->term_id;
	
	return $output;
}

//Returns a multi-dimensional array containing volume, chapter, and post information for the entire comic
function get_the_collection($hide_empty=true,$reverse=false){
	$collection_flat = get_terms('chapter',array('hide_empty' => $hide_empty));
	
	if(!$collection_flat)
		return;
		
	$collection = array();
	
	foreach($collection_flat as $key => $value):
		if(0 == $value->parent):
			$collection[$value->term_id] = get_the_volume($value->term_id);
			$collection[$value->term_id]['chapters'] = array();
			unset($collection_flat[$key]);
		endif;
	endforeach;
	
	if($reverse)
		krsort($collection);
	else
		ksort($collection);
	
	foreach($collection as $vid => $volume):
		foreach($collection_flat as $key => $value):
			if($vid == $value->parent):
				$collection[$vid]['chapters'][$value->term_id] = get_the_chapter($value->term_id);
				
				$chapter_posts = get_objects_in_term($value->term_id,'chapter');
				$chapter_sorted_posts = array();
				
				foreach($chapter_posts as $chapter_post):
					$chapter_post = &get_post($chapter_post);
					if('publish' == $chapter_post->post_status && 'post' == $chapter_post->post_type)
						$chapter_sorted_posts[$chapter_post->ID] = strtotime($chapter_post->post_date);
				endforeach;
				
				natsort($chapter_sorted_posts);
				
				$chapter_ordered_posts = ($reverse) ? array_reverse($chapter_sorted_posts,true) : $chapter_sorted_posts;
				$chapter_post_keys = array();
				
				foreach($chapter_ordered_posts as $the_post => $timestamp)
					array_push($chapter_post_keys,$the_post);
				
				$collection[$vid]['chapters'][$value->term_id]['posts'] = $chapter_post_keys;
				
				unset($collection_flat[$key]);
			endif;
		endforeach;
		if($reverse)
			krsort($collection[$vid]['chapters']);
		else
			ksort($collection[$vid]['chapters']);
	endforeach;
	
	return $collection;
}



//Displays the comic associated with the current post
function the_comic($size=false){
	echo get_the_comic(false,'image',$size);
}

//Displays the comic embed code associated with the current post
function the_comic_embed($size='medium',$before='<p>',$after='</p>'){
	echo $before.get_the_comic(false,'embed',$size).$after;
}

//Displays the comic transcript associated with the current post
function the_comic_transcript($title=''){
	echo get_the_comic(false,'transcript',$title);
}

//Displays a randomly selected comic
function random_comic($format='link',$display=false){
	echo get_the_comic('rand',$format,$display);
}

//Displays recently published comics
function recent_comics($number=5,$format='link',$display=false,$before='<li>',$after='</li>'){
	$comics = comic_loop($number);
	
	if($comics->have_posts()): while($comics->have_posts()): $comics->the_post();
		$output .= $before;
		$output .= get_the_comic(false,$format,$display);
		$output .= $after;
	endwhile; endif;
	
	echo $output;
}

//Displays the comic navigation links
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

//Displays a link to the first comic
function first_comic_link($display='&laquo; First'){
	echo get_the_comic('first','link',$display);
}

//Displays a link to the last comic
function last_comic_link($display='Last &raquo;'){
	echo get_the_comic('last','link',$display);
}

//Displays a link to the previous comic
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

//Displays a link to the next comic
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

//Displays the chapter the current post is associated with, linked to the first page in the chapter
function the_chapter(){
	$chapter = get_the_chapter();
	echo '<a href="'.$chapter['link'].'" title="'.__('Go to the beginning of','webcomic').' '.$chapter['title'].'">'.$chapter['title'].'</a>';
}

//Displays the volume the current post is associated with, linked to the first page in the volume
function the_volume(){
	$volume = get_the_volume();
	echo '<a href="'.$volume['link'].'" title="'.__('Go to the beginning of','webcomic').' '.$volume['title'].'">'.$volume['title'].'</a>';
}

//Displays a copmlete list of comic volumes, chapters, and posts
function comic_archive($descriptions=false,$pages=false,$reverse=false){
	$collection = get_the_collection(true,$reverse);
	
	if(!$collection)
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
			
			foreach($chapter['posts'] as $the_post)
				$output .= '<li class="comic-page-item comic-page-item-'.$the_post.'">'.get_the_comic($the_post,'link').'</li>';
				
			$output .= '</ol></li>';
		endforeach;
		
		$output .= '</ol></li>';
		
	endforeach;
	
	$output .= '</ol>';
	
	echo $output;
}

//Displays a form select with all comic posts
function dropdown_comics($label='',$group=false,$numbered=false,$pages=false,$reverse=false){
	$label = ($label) ? $label : __('Quick Archive','webcomic');
	
	$output = '<select name="comic-select" class="dropdown-comics"><option value="0">'.$label.'</option>';
	
	if($group):
		$collection = get_the_collection(true,$reverse);
		
		if(!$collection)
			return;

		foreach($collection as $volume):
			if('volumes' === $group):
				$append = ($pages) ? ' ('.$volume['pages'].')' : '';
				$output .= '<optgroup label="'.$volume['title'].$append.'">';
				if($numbered)
					$i = 1;	
				foreach($volume['chapters'] as $chapter):
					$prepend = ($i) ? $i.'. ' : '';
					$append = ($pages) ? ' ('.$chapter['pages'].')' : '';
					$output .= '<option value="'.$chapter['link'].'">'.$prepend.$chapter['title'].$append.'</option>';
					if($numbered)
						$i++;
				endforeach;
				$output .= '</optgroup>';
			else:
				foreach($volume['chapters'] as $chapter):			
					$append = ($pages) ? ' ('.$chapter['pages'].')' : '';
					$output .= '<optgroup label="'.$chapter['title'].$append.'">';
					if($numbered)
						$i = 1;
					foreach($chapter['posts'] as $the_post):
						$prepend = ($i) ? $i.'. ' : '';
						$output .= '<option value="'.get_permalink($the_post).'">'.$prepend.get_the_title($the_post).'</option>';
						if($numbered)
							$i++;
					endforeach;
					$output .= '</optgroup>';
				endforeach;
			endif;
		endforeach;
	else:
		$comics = comic_loop(-1);
		if($numbered)
			$i = $comics->post_count;
		
		if($comics->have_posts()):
			while($comics->have_posts()): $comics->the_post();
				$prepend = ($i) ? $i.'. ' : '';
				$output .= '<option value="'.get_permalink().'">'.$prepend.get_the_title().'</option>';
				if($numbered)
					$i--;
			endwhile;
		else:
			return;
		endif;
	endif;
	
	$output .= '</select>';
	
	echo $output;
}
?>
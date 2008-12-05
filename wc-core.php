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
		$new_query = new WP_Query; $new_query->query('posts_per_page='.$number.'&cat=-'.get_comic_category().'&paged='.$paged);
		return $new_query;
	endif;
	
	query_posts('cat=-'.get_comic_category().'&paged='.$paged);
}



//Displays the comic associated with the current post
function the_comic($size=false){
	$output = get_the_comic(false,'image',$size);
	echo $output;
}

//Displays the comic embed code
function the_comic_embed($size='medium'){
	$output = get_the_comic(false,'embed',$size);
	echo $output;
}

//Displays the comic transcript
function the_comic_transcript($title=''){
	global $post;
	
	$transcript = get_post_meta($post->ID,'comic_transcript',true);
	
	if(!$transcript)
		return;
	
	if(function_exists('Markdown'))	
		$transcript = Markdown($transcript);
	
	$title = ($title) ? $title : 'View Transcript';
	
	echo '<div class="transcript-title"><span>'.$title.'</san></div><div class="transcript">'.$transcript.'</div>';
}

//Retrieves the comic associated with the specified post
function get_the_comic($comic=false,$format='post',$display=false){
	global $post;
	
	$new_post = ($comic) ? get_a_comic($comic) : false; if($comic && !$new_post) return false;
	
	$comic_id = ($comic) ? $new_post->ID : $post->ID;
	$comic_date = ($comic) ? $new_post->post_date : $post->post_date;
	$comic_slug = ($comic) ? $new_post->post_name : $post->post_name;
	$comic_title = ($comic) ? get_the_title($new_post->ID) : get_the_title($post->ID);
	$comic_perma = ($comic) ? get_permalink($new_post->ID) : get_permalink($post->ID);
	$comic_description = (get_post_meta($comic_id,'comic_description',true)) ? get_post_meta($comic_id,'comic_description',true) : $comic_title;
	
	if(!$display && 'link' == $format) $display = $comic_title;
	
	switch(get_option('comic_name_format')):
		case 'date': $comic_url = get_the_comic_url(mysql2date(get_option('comic_name_format_date'), $comic_date),$display); break;
		case 'slug': $comic_url = get_the_comic_url($comic_slug,$dipslay); break;
		default: $comic_url = get_the_comic_url(get_post_meta($comic_id,'comic_filename',true),$display);
	endswitch;
	
	switch($format):
		case 'embed': $output = '<input class="embed-comic-code" type="text" readonly="readonly" value="&lt;div class=&quot;embedded-comic&quot;&gt;&lt;p&gt;&lt;a href=&quot;'.$comic_perma.'&quot;&gt;&lt;img src=&quot;'.$comic_url.'&quot; alt=&quot;'.$comic_title.'&quot; title=&quot;'.$comic_description.'&quot; /&gt;&lt;/a&gt;&lt;/p&gt;&lt;p&gt;&lt;cite&gt;'.$comic_title.' | &lt;a href=&quot;'.get_option('home').'/&quot; title=&quot;'.get_option('blogdescription').'&quot;&gt;'.get_option('blogname').'&lt;/a&gt;&lt;/cite&gt;&lt;/p&gt;&lt;/div&gt;" />'; break;
		case 'full': $output = '<a href="'.$comic_perma.'" title="'.$comic_description.'"><img src="'.$comic_url.'" alt="'.$comic_title.'" /></a>'; break;
		case 'image': $output = '<img src="'.$comic_url.'" alt="'.$comic_title.'" title="'.$comic_description.'" />'; break;
		case 'link': $output = '<a href="'.$comic_perma.'" title="'.$comic_description.'">'.$display.'</a>'; break;
		case 'file': $output = $comic_url; break;
		case 'post':default: $output = $comic_perma;
	endswitch;
	
	return $output;
}

//Retrieves the url to the comic file (internal use only)
function get_the_comic_url($id,$size=false){
	$path = ABSPATH.get_comic_directory();
	$http_path = get_settings('siteurl').'/'.get_comic_directory();
	
	if($size && ('large' == $size || 'medium' == $size || 'thumb' == $size)):
		switch($size):
			case 'large': $id = $id.'-large'; break;
			case 'medium': $id = $id.'-medium'; break;
			case 'thumb': $id = $id.'-thumb';
		endswitch;
		$path = $path = ABSPATH.get_comic_directory().'thumbs/';
		$http_path = get_settings('siteurl').'/'.get_comic_directory().'thumbs/';
	endif;
	
	if(!is_dir($path)) die('<p class="error"><strong>Webcomic could not access your comic directory.</strong></p>');
	
	$dir = opendir($path);
	while(($file = readdir($dir)) !== false):
		if(strstr($file,$id) !== false ):
			$output = $http_path.$file; break;
		endif;
	endwhile;
	closedir($dir);
	
	return $output;
}

//Retrieves information on the post associated with a specific comic (internal use only)
function get_a_comic($comic){
	global $wpdb,$post;
	
	switch($comic):
		case 'first': $order = ' post_date asc'; break;
		case 'last': $order = ' post_date desc'; break;
		case 'rand': $order = ' rand()'; break;
		default: $output = $comic; $skip_query = true;
	endswitch;
	
	if(!$skip_query)
		$output = $wpdb->get_var("SELECT $wpdb->posts.id FROM $wpdb->posts,$wpdb->term_relationships where post_type = 'post' and post_status = 'publish' and $wpdb->posts.id =  $wpdb->term_relationships.object_id and $wpdb->term_relationships.term_taxonomy_id = '".get_comic_category()."' order by".$order);
		
	if(($comic == 'first' || $comic == 'last') && $output == $post->ID) return false;
	
	$post = &get_post($output);
	
	return $post;
}



//Displays a randomly selected comic
function random_comic($format='link',$display=false){
	$output = get_the_comic('rand',$format,$display);
	echo $output;
}

//Displays recently published comics
function recent_comics($number=5,$format='link',$display=false,$before='<li>',$after='</li>'){
	$comics = comic_loop($number);
	
	if($comics->have_posts()): while($comics->have_posts()): $comics->the_post();
		$output .= $before;
		$output .= get_the_comic(false,$format,$display);
		$output .= $after;
	endwhile; else:
		$output = $before.'No recent comics to display.'.$after;
	endif;
	
	echo $output;
}

//Displays a form select with all comic posts
function dropdown_comics($label='',$group=false,$numbered=false){
	$label = ($label) ? $label : 'Quick Archive';
	$output = '<select name="comic-select" class="comic-dropdown"><option value="0">'.$label.'</option>';
	
	if($group):
		$collection = _get_term_hierarchy('chapter');
		
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
		
		if(!$collection) return; //Nothin' to show
		
		
		if('volumes' === $group):
			foreach($collection as $volume => $chapters):
				$output .= '<optgroup label="'.get_the_chapter($volume,'title').'">';
				if($numbered)
					$i = 1;	
				foreach($chapters as $chapter):				
					$prepend = ($i) ? $i.'. ' : '';
					$output .= '<option value="'.get_the_chapter($chapter,'post').'">'.$prepend.get_the_chapter($chapter,'title').'</option>';
					if($numbered)
						$i++;
				endforeach;
				$output .= '</optgroup>';
			endforeach;
		else:
			foreach($collection as $volume => $chapters):
				sort($chapters); //Make sure they're in the right order
				foreach($chapters as $chapter):
					$output .= '<optgroup label="'.get_the_chapter($chapter,'title').'">';
					$chapter_posts = get_objects_in_term($chapter,'chapter');
					if($numbered)
						$i = 1;
					foreach($chapter_posts as $the_post):
						$prepend = ($i) ? $i.'. ' : '';
						$output .= '<option value="'.get_permalink($the_post).'">'.$prepend.get_the_title($the_post).'</option>';
						if($numbered)
							$i++;
					endforeach;
					$output .= '</optgroup>';
				endforeach;
			endforeach;
		endif;
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
			return; //Nothin' to show
		endif;
	endif;
	
	$output .= '</select>';
	
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
	$output = get_the_comic('first','link',$display);
	echo $output;
}

//Displays a link to the last comic
function last_comic_link($display='Last &raquo;'){
	$output = get_the_comic('last','link',$display);
	echo $output;
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
?>
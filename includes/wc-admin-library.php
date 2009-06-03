<?php
/**
 * This document contains all functions related to the Library page.
 * These functions were in wc-admin.php prior to version 1.4.
 * 
 * @package WebComic
 * @since 1.4
 */
 
function comic_page_library(){
	load_webcomic_domain();
	
	global $current_user;
	
	$the_series  = ($_REQUEST['series_view']) ? $_REQUEST['series_view'] : get_comic_category();
	$series_link = '&amp;series_view='.$the_series;
	$comic_dir   = (1 < count(get_option('comic_category'))) ? $the_series : false;
	
	/** Display the 'Edit Comic' page */
	if('edit_comic' == $_REQUEST['action']):
		$post = &get_post($_REQUEST['comic']);
		if(current_user_can('edit_others_posts') || $current_user->ID == $post->post_author):
			$comic = get_the_comic($_REQUEST['comic']);
	?>
	<div class="wrap">
		<div id="icon-webcomic" class="icon32"><img src="<?php echo plugins_url('webcomic/includes/webcomic.png') ?>" alt="icon" /></div>
		<h2><?php _e('Edit Comic','webcomic')?></h2>
		<form action="admin.php?page=webcomic/includes/wc-admin.php" method="post">
			<?php wp_nonce_field('webcomic_rename'); ?>
			<table class="slidetoggle describe form-table">
				<thead class="media-item-info">
					<tr>
						<td rowspan="5"><a href="<?php echo $comic['link'] ?>" title="<?php echo $comic['description'] ?>"><img src="<?php echo get_comic_image($comic,'medium') ?>" alt="<?php echo $comic['title'] ?>" /></a></td>
					</tr>
					<tr>
						<td><?php echo end(explode('/',$comic['file'])) ?></td>
					</tr>
					<tr>
						<td><?php if($comic['large']) echo end(explode('/',$comic['large'])); else _e('No Large Size','webcomic'); ?></td>
					</tr>
					<tr>
						<td><?php if($comic['medium']) echo end(explode('/',$comic['medium'])); else _e('No Medium Size','webcomic'); ?></td>
					</tr>
					<tr>
						<td><?php if($comic['thumb']) echo end(explode('/',$comic['thumb'])); else _e('No Thumbnail Size','webcomic'); ?></td>
					</tr>
				</thead>
				<tbody>
					<tr class="form-field">
						<th scope="row"><label for="chapter_name"><?php _e('Filename','webcomic') ?></label></th>
						<td>
						<input type="text" name="webcomic_new_name" class="regular-text" /> <span class="setting-description"><?php _e('Do not include the file extension','webcomic') ?></span><br />
						<?php if('meta' == get_option('comic_name_format')): ?><label><input type="checkbox" name="webcomic_name_overwright" style="width: auto;" /> Update the <code>comic_filename</code> for this comics post</label><?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit"><input type="submit" class="button" name="submit" value="<?php _e('Edit Comic','webcomic') ?>" /><input type="hidden" name="webcomic_old_name" value="<?php echo end(explode('/',$comic['file'])) ?>" /><input type="hidden" name="webcomic_post_id" value="<?php echo $comic['id'] ?>" /><input type="hidden" name="action" value="webcomic_rename" /><input type="hidden" name="series_view" value="<?php echo $the_series; ?>" /></p> 
		</form>
	</div>
	<?php else: ?>
	<p><?php _e('You do not have sufficient permissions to access this page.','webcomic') ?></p>
	<?php
		endif;
	else:
	
	/** Set the default user view. */
	if(!get_usermeta($current_user->ID,'comic_library_view'))
		update_usermeta($current_user->ID,'comic_library_view','list');
	
	/** Update the current users view. */
	if(isset($_REQUEST['comic_library_view']))
		update_usermeta($current_user->ID,'comic_library_view',$_REQUEST['comic_library_view']);
	
	/** Attempt to update the chapter information for the selected comics. */
	if('update_comic_chapters' == $_REQUEST['action']):
		check_admin_referer('update_comic_chapters');
		
		$comics = $_REQUEST['comics'];
		$chapter = (isset($_REQUEST['Submit1'])) ? $_REQUEST['comic_chapter1'] : $_REQUEST['comic_chapter2'];
		
		if(!$comics):
			echo '<div id="message" class="error"><p>'.__('Please select at least one comic.','webcomic').'</p></div>';
		else:
			foreach($comics as $comic)
				add_post_to_chapter($comic,$chapter);
				
			if(-1 == $chapter)
				echo '<div id="message" class="updated fade"><p>'.__('All comics removed from chapters.','webcomic').'</p></div>';
			else					
				echo '<div id="message" class="updated fade"><p>'.__('All comics assigned to new chapter.','webcomic').'</p></div>';
		endif;
	endif;
	
	/** Attempt to upload the selected comic file, generate comic thumbnails, and automatically generate a post (if that option is enabled. */
	if('webcomic_upload' == $_REQUEST['action']):
		check_admin_referer('webcomic_upload');
		
		$ext = strtolower(end(explode('.',basename($_FILES['new_comic_file']['name']))));
		
		switch($ext):
			case 'gif': break;
			case 'jpg': break;
			case 'jpeg': break;
			case 'png': break;
			default: $invalid_format = true;
		endswitch;
		
		if($invalid_format):
			echo '<div id="message" class="error"><p>'.__('Invalid file format. Images must be gif, jpg, jpeg, or png.','webcomic').'</p></div>';
		else:
			$ext = '.'.$ext;
			$file = basename($_FILES['new_comic_file']['name'], $ext);
			
			if(get_option('comic_secure_names'))
				$hash = '-'.md5(microtime().basename($_FILES['new_comic_file']['name']));
			
			$target_path = get_comic_directory('abs',false,$comic_dir).$file.$hash.$ext;
			
			if(is_file($target_path) && !$_REQUEST['new_comic_overwrite']):
				echo '<div id="message" class="error"><p>'.__('A comic with that filename already exists.','webcomic').'</p></div>';
			else:
				if(@move_uploaded_file($_FILES['new_comic_file']['tmp_name'],$target_path)):
					if(strpos(PHP_OS,'WIN'))
						chmod($target_path,0777);
					else
						chmod($target_path,0664);
					
					$img_dim = getimagesize($target_path);
					$img_crop = get_option('comic_thumbnail_crop');
					$img_lw = get_option('comic_large_size_w');
					$img_lh = get_option('comic_large_size_h');
					$img_mw = get_option('comic_medium_size_w');
					$img_mh = get_option('comic_medium_size_h');
					$img_tw = get_option('comic_thumbnail_size_w');
					$img_th = get_option('comic_thumbnail_size_h');
										
					if($img_dim[0] > $img_lw || $img_dim[1] > $img_lh)
						image_resize($target_path,$img_lw,$img_lh,0,'large',get_comic_directory('abs',true,$comic_dir));
					if($img_dim[0] > $img_mw || $img_dim[1] > $img_mh)
						image_resize($target_path,$img_mw,$img_mh,0,'medium',get_comic_directory('abs',true,$comic_dir));
					if($img_dim[0] > $img_tw || $img_dim[1] > $img_th)
						image_resize($target_path,$img_tw,$img_th,$img_crop,'thumb',get_comic_directory('abs',true,$comic_dir));
						
					if(get_option('comic_auto_post') && !$_REQUEST['disable_auto_post']):
						$post_date = ('date' != get_option('comic_name_format') || ('date' == get_option('comic_name_format') && '1969-12-31 19:00:00' == date('Y-m-d H:i:s', strtotime($file))) || $_REQUEST['new_comic_date_override']) ? $_REQUEST['aa'].'-'.$_REQUEST['mm'].'-'.$_REQUEST['jj'].' '.$_REQUEST['hh'].':'.$_REQUEST['mn'].':'.$_REQUEST['ss'] : date('Y-m-d H:i:s', strtotime($file));
						$psot_date_gmt = get_gmt_from_date($post_date);
						$error = wp_insert_post(array(
							'post_content' => '&nbsp;',
							'post_status' => 'publish',
							'post_category' => array($the_series),
							'post_date' => $post_date,
							'post_date_gmt' => $post_date_gmt,
							'post_title' => $file
						));
						if(!$error):
							$nopost = __(' A post could not be automatically generated.','webcomic');
						elseif('meta' == get_option('comic_name_format')):
							add_post_meta($error,'comic_filename',$file);
						endif;
					endif;
						
					echo '<div id="message" class="updated fade"><p>'.__('New comic uploaded.','webcomic').$nopost.'</p></div>';
				else:
					switch($_FILES['new_comic_file']['error']):
						case 1: //For simplicities sake we treat these as the same error.
						case 2: echo '<div id="message" class="error"><p>'.__('The comic was too large to upload.','webcomic').'</p></div>'; break;
						case 3: echo '<div id="message" class="error"><p>'.__('The comic was only partially uploaded.','webcomic').'</p></div>'; break;
						case 4: echo '<div id="message" class="error"><p>'.__('No comic was uploaded.','webcomic').'</p></div>'; break;
						case 6: echo '<div id="message" class="error"><p>'.__('WebComic could not find your web servers temporary directory.','webcomic').'</p></div>'; break;
						case 7: echo '<div id="message" class="error"><p>'.__('The comic could not be saved properly after upload.','webcomic').'</p></div>'; break;
						case 8: echo '<div id="message" class="error"><p>'.__('The comic upload was haulted by a PHP extensions.','webcomic').'</p></div>'; break;
					endswitch;
				endif;
			endif;
		endif;
	endif;
	
	/** Attempt to rename the specified comic. */
	if('webcomic_rename' == $_REQUEST['action']):
		check_admin_referer('webcomic_rename');
		
		if('' != $_REQUEST['webcomic_new_name']):
			$info = pathinfo(ABSPATH.get_option('comic_directory').'/'.$_REQUEST['webcomic_old_name']);
			$old_name = $info['filename'];
			$ext = $info['extension'];
			
			if(get_option('comic_secure_names'))
				$hash = '-'.md5(microtime().basename($_FILES['new_comic_file']['name']));
			
			if(@rename(get_comic_directory('abs',false,$comic_dir).$_REQUEST['webcomic_old_name'],get_comic_directory('abs',false,$comic_dir).$_REQUEST['webcomic_new_name'].$hash.'.'.$ext)):
			
				if('meta' == get_option('comic_name_format') && $_REQUEST['webcomic_post_id'] && $_REQUEST['webcomic_name_overwright'])
					update_post_meta($_REQUEST['webcomic_post_id'], 'comic_filename', $_REQUEST['webcomic_new_name']);
			 
				$dir = opendir(get_comic_directory('abs',true,$comic_dir));
				while(($file = readdir($dir)) !== false):
					if(false !== strpos($file,$old_name)):
						if(false !== strpos($file,$old_name.'-large.'.$ext))
							rename(get_comic_directory('abs',true,$comic_dir).$old_name.'-large.'.$ext,get_comic_directory('abs',true,$comic_dir).$_REQUEST['webcomic_new_name'].$hash.'-large.'.$ext);
						if(false !== strpos($file,$old_name.'-medium.'.$ext))
							rename(get_comic_directory('abs',true,$comic_dir).$old_name.'-medium.'.$ext,get_comic_directory('abs',true,$comic_dir).$_REQUEST['webcomic_new_name'].$hash.'-medium.'.$ext);
						if(false !== strpos($file,$old_name.'-thumb.'.$ext))
							rename(get_comic_directory('abs',true,$comic_dir).$old_name.'-thumb.'.$ext,get_comic_directory('abs',true,$comic_dir).$_REQUEST['webcomic_new_name'].$hash.'-thumb.'.$ext);
					endif;
				endwhile;
				closedir($dir);
				
				echo '<div id="message" class="updated fade"><p>';
				printf(__('Comic <em>%1$s</em> renamed to <em>%2$s</em>','webcomic'),$_REQUEST['webcomic_old_name'],$_REQUEST['webcomic_new_name'].'.'.$ext);
				echo '</p></div>';
			else:
				echo '<div id="message" class="error"><p>'.__('A filename must be provided','webcomic').'</p></div>';
			endif;
		else:
			echo '<div id="message" class="error"><p>'.sprintf(__('WebComic could not rename %1$s','webcomic'),$_REQUEST['webcomic_old_name']).'</strong></p></div>';
		endif;
	endif;
	
	/** Attempt to delete the selected comic. */
	if('webcomic_delete' == $_REQUEST['action']):
		check_admin_referer('webcomic_delete');
		
		if(@unlink(get_comic_directory('abs',false,$comic_dir).$_REQUEST['file'])):
			$ext = strrchr($_REQUEST['file'],'.');
			$fid = substr($_REQUEST['file'], 0, -strlen($ext));
			 
			$dir = opendir(get_comic_directory('abs',true,$comic_dir));
			while(($file = readdir($dir)) !== false):
				if(false !== strpos($file,$fid))
					@unlink(get_comic_directory('abs',true,$comic_dir).$file);
			endwhile;
			closedir($dir);
			
			echo '<div id="message" class="updated fade"><p>'.sprintf(__('Deleted comic %1$s','webcomic'),$_REQUEST['file']).'</p></div>';
		else:
			echo '<div id="message" class="error"><p>'.sprintf(__('%1$s could not be deleted.','webcomic'),$_REQUEST['file']).'</p></div>';
		endif;
	endif;
	
	/** Attempt to regenerate the thumbnails of the specified comic. */
	if('regen_comic_thumbs' == $_REQUEST['action']):
		check_admin_referer('regen_comic_thumbs');
		
		$original_path = get_comic_directory('abs',false,$comic_dir);
		$target_path = get_comic_directory('abs',true,$comic_dir);
		$img_crop = get_option('comic_thumbnail_crop') ? true : false;
		$img_lw = get_option('comic_large_size_w');
		$img_lh = get_option('comic_large_size_h');
		$img_mw = get_option('comic_medium_size_w');
		$img_mh = get_option('comic_medium_size_h');
		$img_tw = get_option('comic_thumbnail_size_w');
		$img_th = get_option('comic_thumbnail_size_h');
		
		$ext = '.'.end(explode('.',$_REQUEST['file']));
		$file_id = rtrim($_REQUEST['file'],$ext);
		
		$dir = opendir($target_path);
		while(($file = readdir($dir)) !== false):
			if(is_dir($target_path.$file) || false === strpos($file,$file_id))
				continue;
			@unlink($target_path.$file);
		endwhile;
		closedir($dir);
		
		$dir = opendir($original_path);
		while(false !== ($file = readdir($dir))):
			if(is_dir($original_path.$file) || $file != $_REQUEST['file'])
				continue;
				
			$img_dim = getimagesize($original_path.$file);
			
			if($img_dim[0] > $img_lw || $img_dim[1] > $img_lh)
				image_resize($original_path.$file,$img_lw,$img_lh,0,'large',$target_path);
			if($img_dim[0] > $img_mw || $img_dim[1] > $img_mh)
				image_resize($original_path.$file,$img_mw,$img_mh,0,'medium',$target_path);
			if($img_dim[0] > $img_tw || $img_dim[1] > $img_th)
				image_resize($original_path.$file,$img_tw,$img_th,$img_crop,'thumb',$target_path);
		endwhile;
		closedir($dir);
			
		echo '<div id="message" class="updated fade"><p>'.sprintf(__('%1$s thumbnails regenerated.','webcomic'),$_REQUEST['file']).'</p></div>';
		
	endif;
	
	/** Attempt to regenerate all comic thumbnails. */
	if('regen_all_thumbs' == $_REQUEST['action']):
		check_admin_referer('regen_all_thumbs');
		
		echo '<div id="message" class="updated fade"><p><img src="'.plugins_url('webcomic/includes/load.gif').'" alt="Working..." style="vertical-align:middle" /> '.__('Please wait while WebComic attempts to regenerate your comic thumbnails. This could take several minutes for large libraries.','webcomic').'</p></div>';
		
		$original_path = get_comic_directory('abs',false,$comic_dir);
		$target_path = get_comic_directory('abs',true,$comic_dir);
		$img_crop = get_option('comic_thumbnail_crop') ? true : false;
		$img_lw = get_option('comic_large_size_w');
		$img_lh = get_option('comic_large_size_h');
		$img_mw = get_option('comic_medium_size_w');
		$img_mh = get_option('comic_medium_size_h');
		$img_tw = get_option('comic_thumbnail_size_w');
		$img_th = get_option('comic_thumbnail_size_h');
		
		$dir = opendir($target_path);
		while(($file = readdir($dir)) !== false):
			if(is_dir($target_path.$file))
				continue;
			@unlink($target_path.$file);
		endwhile;
		closedir($dir);
		
		$dir = opendir($original_path);
		while(false !== ($file = readdir($dir))):
			if(is_dir($original_path.$file))
				continue;
				
			$img_dim = getimagesize($original_path.$file);
			
			if($img_dim[0] > $img_lw || $img_dim[1] > $img_lh)
				image_resize($original_path.$file,$img_lw,$img_lh,0,'large',$target_path);
			if($img_dim[0] > $img_mw || $img_dim[1] > $img_mh)
				image_resize($original_path.$file,$img_mw,$img_mh,0,'medium',$target_path);
			if($img_dim[0] > $img_tw || $img_dim[1] > $img_th)
				image_resize($original_path.$file,$img_tw,$img_th,$img_crop,'thumb',$target_path);
		endwhile;
		closedir($dir);
			
		echo '<script type="text/javascript">jQuery("#message").hide(0);</script><div id="message" class="updated fade"><p>'.__('All thumbnails regenrated.','webcomic').'</p></div>';
	endif;
	
	/** Attempt to generate orphaned comic posts. */
	if('generate_comic_posts' == $_REQUEST['action']):
		check_admin_referer('generate_comic_posts');
			
		switch($_REQUEST['auto_increment_period']):
			case 'minute': $period = 60; break;
			case 'hour':   $period = 3600; break;
			case 'day':    $period = 86400; break;
			case 'week':   $period = 604800; break;
			case 'month':  $period = 2629743.83; break;
			case 'year':   $period = 31556926; break;
			default:       $period = 0;
		endswitch;
		
		$basetime = strtotime($_REQUEST['aa'].'-'.$_REQUEST['mm'].'-'.$_REQUEST['jj'])+(60*60*$_REQUEST['hh'])+(60*$_REQUEST['mn'])+$_REQUEST['ss'];
		$increment = intval($_REQUEST['auto_increment_number']) * $period;
		
		if(('date' != get_option('comic_name_format') || $_REQUEST['auto_post_date_override']) && !$increment):
			echo '<div id="message" class="error"><p>'.__('Invalid time increment, posts could not be automatically generated.','webcomic').'</p></div>';
		else:
			$orphans = explode('/',$_REQUEST['orphaned_comics']);
			array_pop($orphans);
			
			foreach($orphans as $key => $value):
				$ext = strrchr($value,'.');
				$file = substr($value, 0, -strlen($ext));
				
				$post_date = ('date' != get_option('comic_name_format') || $_REQUEST['auto_post_date_override']) ? date('Y-m-d H:i:s',$basetime) : date('Y-m-d H:i:s', strtotime($file));
				
				if('1969-12-31 19:00:00' != $post_date):
					$psot_date_gmt = get_gmt_from_date($post_date);
					$error = wp_insert_post(array(
						'post_content' => '&nbsp;',
						'post_status' => 'publish',
						'post_category' => array($the_series),
						'post_date' => $post_date,
						'post_date_gmt' => $post_date_gmt,
						'post_title' => $file
					));
					if($error && 'meta' == get_option('comic_name_format'))
						add_post_meta($error,'comic_filename',$file);
					
					$basetime+=$increment;				
					$i++;
				endif;
			endforeach;
			
			if(!$i)
				echo '<div id="message" class="error"><p>'.__('No posts could be automatically generated.','webcomic').'</p></div>';
			else
				echo '<div id="message" class="updated fade"><p>'.sprintf(__ngettext('%d post automatically generated.','%d posts automatically generated.',$i,'webcomic'),$i).'</p></div>';
		endif;
	endif;
	
	/** Begin library output. */
	global $post,$paged;
	
	$paged = ($_GET['paged']) ? $_GET['paged'] : 1;
	$paged_link = '&amp;paged='.$paged.$series_link;
	
	//Get all the comic files
	$comic_files = array();
	$path = get_comic_directory('abs',false,$comic_dir);
	if(!is_dir($path)) 
		die('<p class="error">'.__('Webcomic could not access your comic directory. Please make sure that the directory exists and that your <em>Comic Category</em> setting is set correctly.','webcomic').'</p>');
	$dir = opendir($path);
	while(false !== ($file = readdir($dir))):
		if(is_dir($path.$file))
			continue;
		array_push($comic_files,$file);
	endwhile;
	closedir($dir);
	
	//Get just the comic files associated with a post
	$comic_posts_compare = array();
	$comics_check = comic_loop(-1,$the_series);
	if($comics_check->have_posts()):
		while($comics_check->have_posts()):
			$comics_check->the_post();
			$comic = get_the_comic();
			array_push($comic_posts_compare,end(explode('/',$comic['file'])));
			$total_post_num += 1;
		endwhile;
	endif;
	$comic_posts_compare = array_filter($comic_posts_compare);
	
	//Compare our lists
	$comic_files = array_diff($comic_files,$comic_posts_compare);
	
	//Construct our Library array, fifteen comics at a time
	$comic_posts_temp = array();
	$comic_posts = array();
	$comics = comic_loop(15,$the_series);
	if($comics->have_posts()):
		while($comics->have_posts()): $comics->the_post();
			$chapter_temp = get_the_chapter();
			$volume_temp = get_the_chapter('volume');
			$user_temp = get_userdata($post->post_author);
			
			$comic = get_the_comic();
			
			$comic_posts_temp['id'] = $post->ID;
			$comic_posts_temp['permalink'] = get_permalink();
			$comic_posts_temp['title'] = (get_the_title()) ? get_the_title() : '(no title)';
			$comic_posts_temp['author'] = $user_temp->display_name;
			$comic_posts_temp['author_id'] = $user_temp->ID;
			$comic_posts_temp['custom'] = (get_post_meta($comic_id,'comic_filename',true)) ? get_post_meta($comic_id,'comic_filename',true) : '&mdash';
			$comic_posts_temp['slug'] = $post->post_name;
			$comic_posts_temp['date'] = get_the_time(get_option('date_format'));
			$comic_posts_temp['file'] = $comic['file'];
			$comic_posts_temp['thumb'] = $comic['thumb'];
			$comic_posts_temp['name'] = end(explode('/',$comic_posts_temp['file']));
			$comic_posts_temp['volume'] = ($volume_temp) ? $volume_temp['title'] : '&mdash;';
			$comic_posts_temp['chapter'] = ($chapter_temp) ? $chapter_temp['title'].' &laquo; ' : '';
			
			switch($post->post_status):
				case 'future': $comic_posts_temp['status'] = __('Scheduled','webcomic'); break;
				case 'publish': $comic_posts_temp['status'] = __('Published','webcomic'); break;
				case 'pending': $comic_posts_temp['status'] = __('Pending','webcomic'); break;
				case 'draft': $comic_posts_temp['status'] = __('Draft','webcomic'); break;
				default: $comic_posts_temp['status'] = '<strong style="color:#c00">'.__('Unknown','webcomic').'</strong>';
			endswitch;
			
			array_push($comic_posts,$comic_posts_temp);
			
			$current_posts_num+=1;
		endwhile;
		$max_num_pages = $comics->max_num_pages;
	endif;
	
	//Do we have more than one page to show?
	if($max_num_pages > 1):
		$i = 1;
		$previous = $paged - 1;
		$next = $paged + 1;
		
		$to_post_num = ($max_num_pages>1) ? $paged*15 : $total_post_num;
		$to_post_num = ($to_post_num>$total_post_num) ? $total_post_num : $to_post_num;
		
		$from_post_num = ($max_num_pages>1) ? $to_post_num-14 : 1;
		$from_post_num = ($to_post_num==$total_post_num) ? $to_post_num-$current_posts_num+1 : $from_post_num;
		
		$paged_output = '<div class="tablenav-pages"><span class="displaying-num">'.sprintf(__('Displaying %1$d &#8211; %2$d of %3$d','webcomic'),$from_post_num,$to_post_num,$total_post_num).'</span>';
		if($paged != 1)
			$paged_output .= '<a href="admin.php?page=webcomic/includes/wc-admin.php&amp;series_view='.$the_series.'&amp;paged='.$previous.'" class="prev page-numbers">&laquo;</a> ';
		while($i<=$max_num_pages):
			if(($i != 1 && $i < $paged-2) || ($i != $max_num_pages && $i > $paged+2)):
				if($i == 2 || $i == $max_num_pages-1)
					$paged_output .= '<span class="page-numbers dots">...</span>';
				$i++;
				continue;
			endif;
			$paged_output .= ($paged == $i) ? '<span class="page-numbers current">'.$i.'</span> ' : '<a href="admin.php?page=webcomic/includes/wc-admin.php&amp;series_view='.$the_series.'&amp;paged='.$i.'" class="page-numbers">'.$i.'</a> ';
			$i++;
		endwhile;
		if($paged < $max_num_pages)
			$paged_output .= '<a href="admin.php?page=webcomic/includes/wc-admin.php&amp;series_view='.$the_series.'&amp;paged='.$next.'" class="next page-numbers">&raquo;</a>';
		$paged_output .= '</div>'; $i = 0;
	endif;
	
	//And how about those thumbnails? 
	if('thumbnail' == get_comic_library_view())
		$comic_thumb = '<th scope="col" style="width:'.get_option('thumbnail_size_h').'px"></th>';
	
	$collection = get_the_collection('hide_empty=0');
?>
	<div class="wrap">
		<div id="icon-webcomic" class="icon32"><img src="<?php echo plugins_url('webcomic/includes/webcomic.png') ?>" alt="icon" /></div>
		<h2><?php _e('Library','webcomic') ?></h2>
		<?php if(count(get_option('comic_category')) > 1): ?>
			<form action="admin.php?page=webcomic/includes/wc-admin.php" method="post" class="search-form topmargin">
				<p class="search-box">
					<select name="series_view">
					<?php foreach($collection as $series): ?>
						<option value="<?php echo $series['id'] ?>"<?php if($the_series == $series['id']) echo ' selected="selected"'; ?>><?php echo $series['title'] ?></option>
					<?php endforeach; ?>
					</select>
					<input type="submit" value="<?php _e('Change Series','webcomic') ?>" name="change_series_view" class="button-secondary action" />
				</p>
			</form>
		<?php endif; ?>
		<form method="post" action="admin.php?page=webcomic/includes/wc-admin.php" enctype="multipart/form-data">
			<?php wp_nonce_field('webcomic_upload'); ?>
			<p class="alignleft">
				<input type="file" name="new_comic_file" id="new_comic_file" />
				<?php if(current_user_can('edit_others_posts')): ?><label title="<?php _e('Overwrite an existing file with the same name','webcomic') ?>"><input type="checkbox" name="new_comic_overwrite" id="new_comic_overwrite" value="1" /> <?php _e('Overwrite','webcomic') ?></label><?php endif ?>
				<input type="submit" name="submit-upload" class="button-primary" value="<?php _e('Upload Comic','webcomic') ?>" /><br />
				<?php if(get_option('comic_auto_post')): ?>
				<span id="timestampdiv" class="misc-pub-section curtime misc-pub-section-last">
					<span id="timestamp"> 
					<?php if('date' == get_option('comic_name_format')): ?><label title="<?php _e('Ignore the filename and use this date and time instead','webcomic') ?>"><input type="checkbox" name="new_comic_date_override" id="new_comic_date_override" value="1" /> <?php _e('Publish on:','webcomic') ?></label><?php else: _e('Publish on:','webcomic'); endif ?>
					<select id="mm" name="mm">
						<option value="01"<?php if('01' == date('m',strtotime('tomorrow'))) echo ' selected="selected"' ?>><?php _e('Jan','webcomic') ?></option>
						<option value="02"<?php if('02' == date('m',strtotime('tomorrow'))) echo ' selected="selected"' ?>><?php _e('Feb','webcomic') ?></option>
						<option value="03"<?php if('03' == date('m',strtotime('tomorrow'))) echo ' selected="selected"' ?>><?php _e('Mar','webcomic') ?></option>
						<option value="04"<?php if('04' == date('m',strtotime('tomorrow'))) echo ' selected="selected"' ?>><?php _e('Apr','webcomic') ?></option>
						<option value="05"<?php if('05' == date('m',strtotime('tomorrow'))) echo ' selected="selected"' ?>><?php _e('May','webcomic') ?></option>
						<option value="06"<?php if('06' == date('m',strtotime('tomorrow'))) echo ' selected="selected"' ?>><?php _e('Jun','webcomic') ?></option>
						<option value="07"<?php if('07' == date('m',strtotime('tomorrow'))) echo ' selected="selected"' ?>><?php _e('Jul','webcomic') ?></option>
						<option value="08"<?php if('08' == date('m',strtotime('tomorrow'))) echo ' selected="selected"' ?>><?php _e('Aug','webcomic') ?></option>
						<option value="09"<?php if('09' == date('m',strtotime('tomorrow'))) echo ' selected="selected"' ?>><?php _e('Sep','webcomic') ?></option>
						<option value="10"<?php if('10' == date('m',strtotime('tomorrow'))) echo ' selected="selected"' ?>><?php _e('Oct','webcomic') ?></option>
						<option value="11"<?php if('11' == date('m',strtotime('tomorrow'))) echo ' selected="selected"' ?>><?php _e('Nov','webcomic') ?></option>
						<option value="12"<?php if('12' == date('m',strtotime('tomorrow'))) echo ' selected="selected"' ?>><?php _e('Dec','webcomic') ?></option>
					</select><input type="text" id="jj" name="jj" value="<?php echo date('d',strtotime('tomorrow')) ?>" size="2" maxlength="2" />, <input type="text" id="aa" name="aa" value="<?php echo date('Y',strtotime('tomorrow')) ?>" size="4" maxlength="5" /> @ <input type="text" id="hh" name="hh" value="<?php echo date('H',strtotime('tomorrow')) ?>" size="2" maxlength="2" tabindex="4" autocomplete="off" /> : <input type="text" id="mn" name="mn" value="<?php echo date('i',strtotime('tomorrow')) ?>" size="2" maxlength="2" />
					<input type="hidden" id="ss" name="ss" value="<?php echo date('s',strtotime('tomorrow')) ?>" />
					<label title="<?php _e('Prevent WebComic from attempting to automatically post this comic','webcomic') ?>"><input type="checkbox" id="disable_auto_post" name="disable_auto_post" value="1" /> <?php _e('Do Not Post','webcomic') ?></label>
					</span>
				</span>
				<?php endif ?>
				<input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
				<input type="hidden" name="webcomic_upload_status" value="0" />
				<input type="hidden" name="action" value="webcomic_upload" />
				<input type="hidden" name="series_view" value="<?php echo $the_series; ?>" />
			</p>
		</form>
<?php if($comic_posts): //Display the Library ?>
		<form action="admin.php?page=webcomic/includes/wc-admin.php" method="post">
			<?php wp_nonce_field('update_comic_chapters'); ?>
			<div class="tablenav">
			<?php if(current_user_can('manage_categories') && $collection): ?>
				<div class="alignleft actions">
					<select name="comic_chapter1">
						<option value="-1"><?php _e('N\A','webcomic') ?></option>
					<?php
						foreach($collection as $series):
							if($series['id'] == $the_series):
								foreach($series['volumes'] as $volume):
					?>
						<optgroup label="<?php echo $volume['title'] ?>">
						<?php foreach($volume['chapters'] as $chapter): ?>
							<option value="<?php echo $chapter['id'] ?>"><?php echo $chapter['title'] ?></option>
							<?php endforeach; ?>
						</optgroup>
					<?php endforeach; endif; endforeach; ?>
					</select>
					<input type="submit" value="<?php _e('Update Chapters','webcomic') ?>" name="Submit1" class="button-secondary action" /><input type="hidden" name="series_view" value="<?php echo $the_series; ?>" />
					<?php if(current_user_can('edit_others_posts')): ?>&emsp;<a href="<?php echo wp_nonce_url('admin.php?page=webcomic/includes/wc-admin.php'.$paged_link.'&amp;action=regen_all_thumbs','regen_all_thumbs') ?>" title="<?php _e('This may take several minutes for large libraries','webcomic') ?>" class="button"><?php _e('Regenerate All Thumbnails','webcomic') ?></a><?php endif ?>
				</div>
			<?php endif; echo $paged_output ?>
				<div class="view-switch">
					<a href="admin.php?page=webcomic/includes/wc-admin.php<?php echo $paged_link; ?>&amp;comic_library_view=list"><img<?php get_comic_library_view('list') ?> id="view-switch-list" src="../wp-includes/images/blank.gif" width="20" height="20" title="<?php _e('List View','webcomic'); ?>" alt="<?php _e('List View','webcomic'); ?>" /></a>
					<a href="admin.php?page=webcomic/includes/wc-admin.php<?php echo $paged_link; ?>&amp;comic_library_view=thumbnail"><img<?php get_comic_library_view('thumbnail') ?>  id="view-switch-excerpt" src="../wp-includes/images/blank.gif" width="20" height="20" title="<?php _e('Thumbnail View','webcomic'); ?>" alt="<?php _e('Thumbnail View','webcomic'); ?>" /></a>
				</div>
			</div>
			<table class="widefat">
				<thead>
					<tr>
						<?php if(current_user_can('manage_categories')): ?><th scope="col" class="check-column"><input type="checkbox" /></th><?php endif ?>
						<?php echo $comic_thumb ?>
						<th scope="col"><?php _e('Comic','webcomic') ?></th>
						<th scope="col"><?php _e('Post','webcomic') ?></th>
						<th scope="col"><?php _e('Author','webcomic') ?></th>
						<th scope="col"><?php _e('Collection','webcomic') ?></th>
						<?php if('meta' == get_option('comic_name_format')): ?>
							<th scope="col"><?php _e('Custom','webcomic') ?></th>
						<?php endif ?>
						<?php if('slug' == get_option('comic_name_format')): ?>
							<th scope="col"><?php _e('Slug','webcomic') ?></th>
						<?php endif ?>
						<th scope="col"><?php _e('Date','webcomic') ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<?php if(current_user_can('manage_categories')): ?><th scope="col" class="check-column"><input type="checkbox" /></th><?php endif ?>
						<?php echo $comic_thumb ?>
						<th scope="col"><?php _e('Comic','webcomic') ?></th>
						<th scope="col"><?php _e('Post','webcomic') ?></th>
						<th scope="col"><?php _e('Author','webcomic') ?></th>
						<th scope="col"><?php _e('Collection','webcomic') ?></th>
						<?php if('meta' == get_option('comic_name_format')): ?>
							<th scope="col"><?php _e('Custom','webcomic') ?></th>
						<?php endif ?>
						<?php if('slug' == get_option('comic_name_format')): ?>
							<th scope="col"><?php _e('Slug','webcomic') ?></th>
						<?php endif ?>
						<th scope="col"><?php _e('Date','webcomic') ?></th>
					</tr>
				</tfoot>
				<tbody>
				<?php foreach($comic_posts as $post): ?>
					<tr<?php if($post['file']): if($i%2): ?> class="alt"<?php endif; else: ?> style="background:#fdd"<?php endif; ?>>
						<?php if(current_user_can('manage_categories')): ?><th scope="row" class="check-column"><input type="checkbox" name="comics[]" value="<?php echo $post['id'] ?>" /></th><?php endif ?>
						<?php if($comic_thumb): ?>
							<td style="text-align:center"><a href="<?php echo $post['file'] ?>"><img src="<?php echo $post['thumb'] ?>" alt="<?php echo $post['name'] ?>" /></a></td>
						<?php endif ?>
						<td>
						<?php if($post['file']): ?>
							<strong><a href="<?php echo $post['file'] ?>"><?php echo $post['name'] ?></a></strong>
							<?php if(current_user_can('edit_others_posts') || $current_user->ID == $post['author_id']): ?><div class="row-actions"><a href="admin.php?page=webcomic/includes/wc-admin.php<?php echo $paged_link; ?>&amp;action=edit_comic&amp;comic=<?php echo $post['id'] ?>" title="<?php _e('Edit this comic','webcomic') ?>"><?php _e('Edit','webcomic') ?></a> | <a href="<?php echo wp_nonce_url('admin.php?page=webcomic/includes/wc-admin.php'.$paged_link.'&amp;action=regen_comic_thumbs&amp;file='.$post['name'], 'regen_comic_thumbs') ?>" title="<?php _e('Regenerate thumbnails for this comic','webcomic') ?>"><?php _e('Regenerate Thumbnails','webcomic') ?></a> | <span class="delete"><a href="<?php echo wp_nonce_url('admin.php?page=webcomic/includes/wc-admin.php'.$paged_link.'&amp;action=webcomic_delete&amp;file='.$post['name'], 'webcomic_delete') ?>" onclick="if(confirm('<?php echo js_escape(sprintf(__("You are about to delete '%s'\n 'Cancel' to stop, 'OK' to delete.","webcomic"),$post['name'])) ?>')) {return true;}return false;" title="<?php _e('Delete this comic','webcomic') ?>"><?php _e('Delete','webcomic') ?></a></span></div><?php endif ?>
						<?php else: ?>
							<strong><?php _e('No Comic Found','webcomic') ?></strong>
						<?php endif ?>
						</td>
						<td>
							<strong><?php if(current_user_can('edit_others_posts') || $current_user->ID == $post['author_id']): ?><a href="post.php?action=edit&amp;post=<?php echo $post['id'] ?>&amp;referredby=admin.php?page=webcoic/includes/wc-admin.php<?php echo $paged_link; ?>" title="Edit &quot;<?php echo $post['title'] ?>&quot;"><?php echo $post['title'] ?></a><?php else: echo $post['title']; endif ?></strong>
							<div class="row-actions"><?php if(current_user_can('edit_others_posts') || $current_user->ID == $post['author_id']): ?><a href="post.php?action=edit&amp;post=<?php echo $post['id'] ?>&amp;referredby=admin.php?page=webcoic/includes/wc-admin.php<?php echo $paged_link; ?>" title="<?php _e('Edit this post','webcomic') ?>"><?php _e('Edit','webcomic') ?></a> | <span class="delete"><a href="<?php echo wp_nonce_url('post.php?action=delete&amp;post='.$post['id'].'&amp;referredby=admin.php?page=webcomic/includes/wc-admin.php'.$paged_link, 'delete-post_'.$post['id']) ?>" title="<?php _e('Delete this post','webcomic') ?>" onclick="if(confirm('<?php echo js_escape(sprintf(__("You are about to delete '%s'\n 'Cancel' to stop, 'OK' to delete.","webcomic"),$post['title'])) ?>')) {return true;}return false;"><?php _e('Delete','webcomic') ?></a> | </span><?php endif ?><span class="view"><a href="<?php echo $post['permalink'] ?>" title="<?php _e('View','webcomic'); ?> &quot;<?php echo $post['title'] ?>&quot;"><?php _e('View','webcomic'); ?></a></span></div>
						</td>
						<td><?php echo $post['author'] ?></td>
						<td><?php echo $post['chapter'].$post['volume'] ?></td>
						<?php if('meta' == get_option('comic_name_format')): ?>
							<td><?php echo $post['custom'] ?></td>
						<?php endif ?>
						<?php if('slug' == get_option('comic_name_format')): ?>
							<td><?php echo $post['slug'] ?></td>
						<?php endif ?>
						<td><?php echo $post['date'] ?><br /><?php echo $post['status'] ?></td>
					</tr>
				<?php $i++; endforeach; $i=0; ?>
				</tbody>
			</table>
			<div class="tablenav">
			<?php if(current_user_can('manage_categories') && $collection): ?>
				<div class="alignleft actions">
					<select name="comic_chapter2">
						<option value="-1"><?php _e('N\A','webcomic') ?></option>
					<?php
						foreach($collection as $series):
							if($series['id'] == $the_series):
								foreach($series['volumes'] as $volume):
					?>
						<optgroup label="<?php echo $volume['title'] ?>">
						<?php foreach($volume['chapters'] as $chapter): ?>
							<option value="<?php echo $chapter['id'] ?>"><?php echo $chapter['title'] ?></option>
							<?php endforeach; ?>
						</optgroup>
					<?php endforeach; endif; endforeach; ?>
					</select>
					<input type="submit" value="<?php _e('Update Chapters','webcomic') ?>" name="Submit2" class="button-secondary action" />
					<input type="hidden" name="action" value="update_comic_chapters" />
				</div>
			<?php endif ?>
				<?php echo $paged_output; ?>
			</div>
		</form>
<?php endif; if($comic_files && current_user_can('edit_others_posts')): natsort($comic_files); ?>
		<h3 class="alignleft" style="clear:left;"><?php _e('Orphaned Comics','webcomic') ?></h3>
		<form action="admin.php?page=webcomic/includes/wc-admin.php" method="post">
			<?php wp_nonce_field('generate_comic_posts'); ?>
			<p class="alignright">
				<span id="timestampdiv" class="misc-pub-section curtime misc-pub-section-last">
					<span id="timestamp">
					<?php if('date' == get_option('comic_name_format')): ?><label title="<?php _e('Ignore filenames and use these settings instead','webcomic') ?>"><input type="checkbox" name="auto_post_date_override" id="auto_post_date_override" value="1" /> <?php _e('Start on:','webcomic') ?></label><?php else: _e('Start on:','webcomic'); endif ?>
					<select id="mm" name="mm">
						<option value="01"<?php if('01' == date('m')) echo ' selected="selected"' ?>><?php _e('Jan','webcomic') ?></option>
						<option value="02"<?php if('02' == date('m')) echo ' selected="selected"' ?>><?php _e('Feb','webcomic') ?></option>
						<option value="03"<?php if('03' == date('m')) echo ' selected="selected"' ?>><?php _e('Mar','webcomic') ?></option>
						<option value="04"<?php if('04' == date('m')) echo ' selected="selected"' ?>><?php _e('Apr','webcomic') ?></option>
						<option value="05"<?php if('05' == date('m')) echo ' selected="selected"' ?>><?php _e('May','webcomic') ?></option>
						<option value="06"<?php if('06' == date('m')) echo ' selected="selected"' ?>><?php _e('Jun','webcomic') ?></option>
						<option value="07"<?php if('07' == date('m')) echo ' selected="selected"' ?>><?php _e('Jul','webcomic') ?></option>
						<option value="08"<?php if('08' == date('m')) echo ' selected="selected"' ?>><?php _e('Aug','webcomic') ?></option>
						<option value="09"<?php if('09' == date('m')) echo ' selected="selected"' ?>><?php _e('Sep','webcomic') ?></option>
						<option value="10"<?php if('10' == date('m')) echo ' selected="selected"' ?>><?php _e('Oct','webcomic') ?></option>
						<option value="11"<?php if('11' == date('m')) echo ' selected="selected"' ?>><?php _e('Nov','webcomic') ?></option>
						<option value="12"<?php if('12' == date('m')) echo ' selected="selected"' ?>><?php _e('Dec','webcomic') ?></option>
					</select>
					<input type="text" id="jj" name="jj" value="<?php echo date('d') ?>" size="2" maxlength="2" />, <input type="text" id="aa" name="aa" value="<?php echo date('Y') ?>" size="4" maxlength="5" /> @ <input type="text" id="hh" name="hh" value="<?php echo date('H') ?>" size="2" maxlength="2" tabindex="4" autocomplete="off" /> : <input type="text" id="mn" name="mn" value="<?php echo date('i') ?>" size="2" maxlength="2" />
					<input type="hidden" id="ss" name="ss" value="<?php echo date('s') ?>" />
					<?php _e('and post every') ?>
					<input type="text" id="jj" name="auto_increment_number" value="1" size="2" maxlength="2" />
					<select name="auto_increment_period" id="auto_increment_period">
						<option value="minute"><?php _e('Minutes','webcomic') ?></option>
						<option value="hour"><?php _e('Hours','webcomic') ?></option>
						<option value="day"><?php _e('Days','webcomic') ?></option>
						<option value="week"><?php _e('Weeks','webcomic') ?></option>
						<option value="month"><?php _e('Months','webcomic') ?></option>
						<option value="year"><?php _e('Years','webcomic') ?></option>
					</select>
					</span>
				</span>
				<input type="submit" class="button-primary" value="<?php _e('Generate Posts','webcomic') ?>" title="<?php _e('Attempt to generate posts for orphaned comics','webcomic') ?>" />
				<input type="hidden" name="orphaned_comics" value="<?php foreach($comic_files as $file) echo $file.'/'; ?>" />
				<input type="hidden" name="action" value="generate_comic_posts" />
				<input type="hidden" name="series_view" value="<?php echo $the_series; ?>" />
			</p>
		</form>
		<table class="widefat">
			<thead>
				<tr>
					<th scope="col"><?php _e('File','webcomic') ?></th>
					<th scope="col"><?php _e('Actions','webcomic') ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th scope="col"><?php _e('File','webcomic') ?></th>
					<th scope="col"><?php _e('Actions','webcomic') ?></th>
				</tr>
			</tfoot>
			<tbody>
			<?php foreach($comic_files as $file): ?>
				<tr<?php if($i%2): ?> class="alt"<?php endif; ?>>
					<td>
						<strong><a href="<?php echo get_comic_directory('url',false,$comic_dir).$file ?>"><?php echo $file ?></a></strong>
						<div class="row-actions"><span class="delete"><a href="<?php echo wp_nonce_url('admin.php?page=webcomic/includes/wc-admin.php'.$paged_link.'&amp;action=webcomic_delete&amp;file='.$file,'webcomic_delete') ?>" onclick="if(confirm('<?php echo js_escape(sprintf(__("You are about to delete '%s'\n 'Cancel' to stop, 'OK' to delete.","webcomic"),$file)) ?>')) { return true;}return false;" title="<?php _e('Delete this comic','webcomic') ?>"><?php _e('Delete','webcomic') ?></a></span></div>
					</td>
					<td><form action="admin.php?page=webcomic/includes/wc-admin.php" method="post"><?php wp_nonce_field('webcomic_rename'); ?><input type="text" name="webcomic_new_name" class="small-text" /> <input type="submit" name="Submit" class="button-secondary" value="<?php _e('Rename','webcomic') ?>" /><input type="hidden" name="webcomic_old_name" value="<?php echo $file ?>" /><input type="hidden" name="action" value="webcomic_rename" /><input type="hidden" name="series_view" value="<?php echo $the_series; ?>" /></form></td>
				</tr>
			<?php $i++; endforeach; $i=0; ?>
			</tbody>
		</table>
<?php endif; endif; ?>
	</div>
<?php } ?>

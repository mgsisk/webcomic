<?php
//Register the WebComic administrative pages (which are all defined below)
function comic_admin_pages_add(){
	add_menu_page('WebComic', 'WebComic', 8, __FILE__, 'comic_page_settings');
	add_submenu_page(__FILE__,'Settings','Settings',8,__FILE__,'comic_page_settings');
	add_submenu_page(__FILE__,'Comic Library','Library',8,'comic-library','comic_page_library');
	add_submenu_page(__FILE__,'Comic Chapters','Chapters',8,'comic-chapters','comic_page_chapters');
}
add_action('admin_menu', 'comic_admin_pages_add');



//Enqueue the admin form scripts for fancy administrative stuff
function wc_admin_scripts() {
	wp_enqueue_script('admin-forms');
}
add_action('admin_init','wc_admin_scripts');



//Handles everything for the Settings page
function comic_page_settings(){ 
	if('save_webcomic_settings' == $_REQUEST['action']):
		update_option('comic_category',$_REQUEST['comic_category']);
		update_option('comic_directory',trim($_REQUEST['comic_directory'],'/'));
		
		if(!file_exists(ABSPATH.get_comic_directory()))
			mkdir(ABSPATH.get_comic_directory(),0775,true);
		
		if(!file_exists(ABSPATH.get_comic_directory().'thumbs/'))
			mkdir(ABSPATH.get_comic_directory().'thumbs/',0775,true);
			
		update_option('comic_current_chapter',$_REQUEST['comic_current_chapter']);
			
		if('' == $_REQUEST['comic_feed'])
			update_option('comic_feed','off');
		else
			update_option('comic_feed','on');
		
		update_option('comic_name_format',$_REQUEST['comic_name_format']);
		
		if(!$_REQUEST['comic_name_format_date'])
			update_option('comic_name_format_date','Y-m-d');
		else
			update_option('comic_name_format_date',$_REQUEST['comic_name_format_date']);
		
		echo '<div id="message" class="updated fade"><p><strong>Settings saved.</strong></p></div>';
	endif;
?>
	<div class="wrap">
		<h2>WebComic</h2>
		<form method="post" action="">
			<table class="form-table">
				<tr>
					<th scope="row"><label for="comic_category">Comic Category</label></th>
					<td>
						<select name="comic_category" id="comic_category"> 
						<?php 
							$categories = get_categories('hierarchical=0&hide_empty=0'); 
							foreach ($categories as $cat):
								$option = '<option value="'.$cat->cat_ID;
								if($cat->cat_ID == get_comic_category())
									$option .= '" selected="selected">';
								else
									$option .= '">';
								$option .= $cat->cat_name.'</option>';
								echo $option;
							endforeach;
						?>
						</select>
						<span class="setting-description">Select the category that your comic posts are assigned to.</span>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="comic_directory">Comic Directory</label></th>
					<td>
						<input type="text" name="comic_directory" id="comic_directory" value="<?php print get_option('comic_directory'); ?>" class="code" /> <span class="setting-description">WebComic will look for your comics in <a href="<?php echo get_settings('siteurl').'/'.get_comic_directory(); ?>"><?php echo get_settings('siteurl').'/'.get_comic_directory(); ?></a></span>
					</td>
				</tr>
				<?php $chapters = get_chapters(array('hide_empty' => false)); if($chapters): ?>
				<tr>
					<th scope="row"><label for="comic_current_chapter">Current Chapter</label></th>
					<td>
						<select name="comic_current_chapter">
						<?php
							echo '<option value="-1">N/A</option>';
							foreach($chapters as $chapter):
								if($chapter->parent == 0):
									if($not_first_volume):
										echo '</optgroup>';
									endif;
									echo '<optgroup label="'.$chapter->name.'">';
									$not_first_volume = true;
									continue;
								endif;
								$option = '<option value="'.$chapter->term_id;
								if($chapter->term_id == get_comic_current_chapter())
									$option .= '" selected="selected">';
								else
									$option .= '">';
								$option .= $chapter->name.'</option>';
								echo $option;
							endforeach;
							echo '</optgroup>';
						?>
						</select><span class="setting-description">Select the chapter new comic posts will be assigned to.</span>
					</td>
				</tr>
				<?php endif; ?>
				<tr>
					<th scope="row">Feed</th>
					<td><label for="comic_feed"><input name="comic_feed" type="checkbox" id="comic_feed" value="on"<?php if('on' == get_option('comic_feed')) print ' checked="checked"'; ?> /> Include comic images in feed</label></td>
				</tr>
			</table>
			<?php if(!$chapters):?><input type="hidden" name="comic_current_chapter" value="-1" /><?php endif; ?>
			<h3>How do you name your comics?</h3>
			<table class="form-table">
				<tr>
					<th scope="row"><label><input type="radio" name="comic_name_format" value="date"<?php if('date' == get_option('comic_name_format')) echo ' checked="checked"'; ?> /> Date</label></th>
					<td><input type="text" name="comic_name_format_date" value="<?php print get_option('comic_name_format_date'); ?>" class="code" /><p>Your comics must have the  <a href="http://us2.php.net/date">date</a> somewhere in the filename, in the format of <code><?php echo date(get_option('comic_name_format_date')); ?></code>.</p></td>
				</tr>
				<tr>
					<th><label><input type="radio" name="comic_name_format" value="slug"<?php if('slug' == get_option('comic_name_format')) echo ' checked="checked"'; ?> /> Title</label></th>
					<td>Your comics must have the <a href="http://lorelle.wordpress.com/2007/09/02/understanding-the-wordpress-post-title-and-post-slug/">post slug</a> somewhere in the filename.</td>
				</tr>
				<tr>
					<th><label><input type="radio" name="comic_name_format" value="meta"<?php if('meta' == get_option('comic_name_format')) echo ' checked="checked"'; ?> /> Custom</label></th>
					<td>Your comics must have the value of a <a href="http://codex.wordpress.org/Using_Custom_Fields">custom field</a> called <code>comic_filename</code> somewhere in the filename.</td>
				</tr>
			</table>
		<p class="submit"><input type="submit" name="Submit" class="button-primary" value="Save Changes" /><input type="hidden" name="action" value="save_webcomic_settings" /></p>
		</form>
	</div>
<?php
}



//Handles everything for the Library page
function comic_page_library(){
	if(isset($_REQUEST['comic_library']))
		update_option('comic_library_view',$_REQUEST['comic_library']);
		
	if('update_comic_chapters' == $_REQUEST['action']):
		$comics = $_REQUEST['comics'];
		$chapter = (isset($_REQUEST['Submit1'])) ? $_REQUEST['comic_chapter1'] : $_REQUEST['comic_chapter2'];
		if(!$comics):
			echo '<div id="message" class="error"><p>Please select at least one comic.</p></div>';
		else:
			if(-1 == $chapter):
				foreach($comics as $comic)
					remove_post_from_chapter($comic);
				echo '<div id="message" class="updated fade"><p><strong>All comics removed from chapters.</strong></p></div>';
			else:
				foreach($comics as $comic):
					add_post_to_chapter($comic,$chapter);
				endforeach;
				echo '<div id="message" class="updated fade"><p><strong>All comics assigned to new chapter.</strong></p></div>';
			endif;
		endif;
	endif;
	
	if('webcomic_upload' == $_REQUEST['action']):
		$ext = strtolower(end(explode('.',basename($_FILES['new_comic_file']['name']))));
		
		switch($ext):
			case 'gif': break;
			case 'jpg': break;
			case 'jpeg': break;
			case 'png': break;
			default: $invalid_format = true;
		endswitch;
		
		if($invalid_format):
			echo '<div id="message" class="error"><p><strong>Invalid file format. Images must be either gif, jpg, jpeg, or png.</strong></p></div>';
		else:
			$target_path = ABSPATH.get_comic_directory().basename($_FILES['new_comic_file']['name']); 
			
			if(move_uploaded_file($_FILES['new_comic_file']['tmp_name'],$target_path)):
				$img_dim = getimagesize($target_path);
				$img_crop = get_option('thumbnail_crop');
				$img_lw = get_option('large_size_w');
				$img_lh = get_option('large_size_h');
				$img_mw = get_option('medium_size_w');
				$img_mh = get_option('medium_size_h');
				$img_tw = get_option('thumbnail_size_w');
				$img_th = get_option('thumbnail_size_h');
				
				if($img_dim[0] > $img_lw || $img_dim[1] > $img_lh)
					image_resize($target_path,$img_lw,$img_lh,0,'large',ABSPATH.get_comic_directory().'thumbs/');
				if($img_dim[0] > $img_mw || $img_dim[1] > $img_mh)
					image_resize($target_path,$img_mw,$img_mh,0,'medium',ABSPATH.get_comic_directory().'thumbs/');
				if($img_dim[0] > $img_tw || $img_dim[1] > $img_th)
					image_resize($target_path,$img_tw,$img_th,$img_crop,'thumb',ABSPATH.get_comic_directory().'thumbs/');
					
				echo '<div id="message" class="updated fade"><p><strong>Comic uploaded.</strong></p></div>';
			else:
				switch($_FILES['new_comic_file']['error']):
					case 1: //For simplicities sake we treat these as the same error.
					case 2: echo '<div id="message" class="error"><p><strong>The comic is too large to upload.</strong></p></div>'; break;
					case 3: echo '<div id="message" class="error"><p><strong>The comic was only partially uploaded.</strong></p></div>'; break;
					case 4: echo '<div id="message" class="error"><p><strong>No comic was uploaded.</strong></p></div>'; break;
					case 6: echo '<div id="message" class="error"><p><strong>WebComic could not find your web servers temporary directory.</strong></p></div>'; break;
					case 7: echo '<div id="message" class="error"><p><strong>The comic could not be saved properly after upload.</strong></p></div>'; break;
					case 8: echo '<div id="message" class="error"><p><strong>The comic upload was haulted by a PHP extensions.</strong></p></div>'; break;
				endswitch;
			endif;
		endif;
	endif;
	
	if('webcomic_rename' == $_REQUEST['action']):
		if('' != $_REQUEST['webcomic_new_name']):
			$info = pathinfo(ABSPATH.get_option('comic_directory').'/'.$_REQUEST['webcomic_old_name']);
			$old_name = $info['filename'];
			$ext = $info['extension'];
			if(rename(ABSPATH.get_comic_directory().$_REQUEST['webcomic_old_name'],ABSPATH.get_comic_directory().$_REQUEST['webcomic_new_name'].'.'.$ext)):
			 
				$dir = opendir(ABSPATH.get_comic_directory().'thumbs/');
				while(($file = readdir($dir)) !== false):
					if(strstr($file,$old_name) !== false ):
						if(strstr($file,$old_name.'-large.'.$ext))
							rename(ABSPATH.get_comic_directory().'thumbs/'.$old_name.'-large.'.$ext,ABSPATH.get_comic_directory().'thumbs/'.$_REQUEST['webcomic_new_name'].'-large.'.$ext);
						if(strstr($file,$old_name.'-medium.'.$ext))
							rename(ABSPATH.get_comic_directory().'thumbs/'.$old_name.'-medium.'.$ext,ABSPATH.get_comic_directory().'thumbs/'.$_REQUEST['webcomic_new_name'].'-medium.'.$ext);
						if(strstr($file,$old_name.'-thumb.'.$ext))
							rename(ABSPATH.get_comic_directory().'thumbs/'.$old_name.'-thumb.'.$ext,ABSPATH.get_comic_directory().'thumbs/'.$_REQUEST['webcomic_new_name'].'-thumb.'.$ext);
					endif;
				endwhile;
				closedir($dir);
				
				echo '<div id="message" class="updated fade"><p><strong>Comic <em>'.$_REQUEST['webcomic_old_name'].'</em> renamed to <em>'.$_REQUEST['webcomic_new_name'].'.'.$ext.'</em></strong></p></div>';
			else:
				echo '<div id="message" class="error"><p><strong>A filename must be provided.</strong></p></div>';
			endif;
		else:
			echo '<div id="message" class="error"><p><strong>WebComic could not rename'.$_REQUEST['webcomic_old_name'].'</strong></p></div>';
		endif;
	endif;
	
	if('webcomic_delete' == $_REQUEST['action']):
		if(unlink(ABSPATH.get_comic_directory().$_REQUEST['file'])):
			$ext = strrchr($_REQUEST['file'],'.');  
			$fid = substr($_REQUEST['file'], 0, -strlen($ext));
			 
			$dir = opendir(ABSPATH.get_comic_directory().'thumbs/');
			while(($file = readdir($dir)) !== false):
				if(strstr($file,$fid) !== false )
					unlink(ABSPATH.get_comic_directory().'thumbs/'.$file);
			endwhile;
			closedir($dir);
			
			echo '<div id="message" class="updated fade"><p><strong>Comic '.$_REQUEST['file'].' deleted.</strong></p></div>';
		else:
			echo '<div id="message" class="error"><p><strong>Comic '.$_REQUEST['file'].' could not be deleted.</strong></p></div>';
		endif;
	endif;
	
	if('regen_comic_thumbs' == $_REQUEST['action']):
		//Get our comic and media options
		$original_path = ABSPATH.get_comic_directory();
		$target_path = ABSPATH.get_comic_directory().'thumbs/';
		$img_crop = get_option('thumbnail_crop');
		$img_lw = get_option('large_size_w');
		$img_lh = get_option('large_size_h');
		$img_mw = get_option('medium_size_w');
		$img_mh = get_option('medium_size_h');
		$img_tw = get_option('thumbnail_size_w');
		$img_th = get_option('thumbnail_size_h');
		
		//Delete everything in the thumbs folder
		$dir = opendir(ABSPATH.get_comic_directory().'thumbs/');
		while(($file = readdir($dir)) !== false):
			if(is_dir(ABSPATH.get_comic_directory().'thumbs/'.$file))
				continue;
			unlink(ABSPATH.get_comic_directory().'thumbs/'.$file);
		endwhile;
		closedir($dir);
		
		//Next, get everything in the comics folder
		$dir = opendir(ABSPATH.get_comic_directory());
		while(false !== ($file = readdir($dir))):
			if(is_dir(ABSPATH.get_comic_directory().$file))
				continue;
				
			$img_dim = getimagesize(ABSPATH.get_comic_directory().$file);
			
			if($img_dim[0] > $img_lw || $img_dim[1] > $img_lh)
				image_resize($original_path.$file,$img_lw,$img_lh,0,'large',$target_path);
			if($img_dim[0] > $img_mw || $img_dim[1] > $img_mh)
				image_resize($original_path.$file,$img_mw,$img_mh,0,'medium',$target_path);
			if($img_dim[0] > $img_tw || $img_dim[1] > $img_th)
				image_resize($original_path.$file,$img_tw,$img_th,$img_crop,'thumb',$target_path);
		endwhile;
		closedir($dir);
			
		echo '<div id="message" class="updated fade"><p><strong>All thumbnails regenrated.</strong></p></div>';
	endif;
	
	global $post,$paged;
	
	$paged = ($_GET['paged']) ? $_GET['paged'] : 1;
	$paged_link = '&amp;paged='.$paged;
	
	//Get all the comic files
	$comic_files = array();
	$path = ABSPATH.get_comic_directory();
	if(!is_dir($path)) 
		die('<p class="error"><strong>Webcomic could not access your comic directory.</strong></p>');
	$dir = opendir($path);
	while(false !== ($file = readdir($dir))):
		if(is_dir($path.$file))
			continue;
		array_push($comic_files,$file);
	endwhile;
	closedir($dir);
	
	//Get just the comic files associated with a post
	$comic_posts_compare = array();
	$comics_check = comic_loop(-1); if($comics_check->have_posts()): while($comics_check->have_posts()): $comics_check->the_post();
		array_push($comic_posts_compare,end(explode('/',get_the_comic('','file'))));
		$total_post_num += 1;
	endwhile; endif;
	$comic_posts_compare = array_filter($comic_posts_compare);
	
	//Compare our lists
	$comic_files = array_diff($comic_files,$comic_posts_compare);
	
	//Construct our Library array, fifteen comics at a time
	$comic_posts_temp = array();
	$comic_posts = array();
	$comics = comic_loop(15);
	if($comics->have_posts()):
		while($comics->have_posts()) : $comics->the_post();	
			$comic_posts_temp['id'] = $post->ID;
			$comic_posts_temp['permalink'] = get_permalink();
			$comic_posts_temp['title'] = get_the_title();
			$comic_posts_temp['slug'] = $post->post_name;
			$comic_posts_temp['date'] = get_the_time(get_option('date_format'));
			$comic_posts_temp['file'] = get_the_comic(false,'file');
			$comic_posts_temp['thumb'] = get_the_comic(false,'file','thumb');
			$comic_posts_temp['name'] = end(explode('/',$comic_posts_temp['file']));
			$comic_posts_temp['volume'] = get_the_chapter('volume','title');
			$comic_posts_temp['chapter'] = get_the_chapter(false,'title');
			switch($post->post_status):
				case 'future': $comic_posts_temp['status'] = 'Scheduled'; break;
				case 'publish': $comic_posts_temp['status'] = 'Published'; break;
				case 'pending': $comic_posts_temp['status'] = 'Pending'; break;
				case 'draft': $comic_posts_temp['status'] = 'Draft'; break;
				default: $comic_posts_temp['status'] = 'Unknown';
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
		
		$paged_output = '<div class="tablenav-pages"><span class="displaying-num">Displaying '.$from_post_num.'&#8211;'.$to_post_num.' of '.$total_post_num.'</span>';
		if($paged != 1)
			$paged_output .= '<a href="admin.php?page=comic-library&amp;paged='.$previous.'" class="prev page-numbers">&laquo;</a> ';
		while($i<=$max_num_pages):
			$paged_output .= ($paged == $i) ? '<span class="page-numbers current">'.$i.'</span> ' : '<a href="admin.php?page=comic-library&paged='.$i.'" class="page-numbers">'.$i.'</a> ';
			$i++;
		endwhile;
		if($paged < $max_num_pages)
			$paged_output .= '<a href="admin.php?page=comic-library&amp;paged='.$next.'" class="next page-numbers">&raquo;</a>';
		$paged_output .= '</div>'; $i = 0;
	endif;
	
	//And how about them thumbnails? 
	if('thumbnail' == get_comic_library_view())
		$comic_thumb = '<th scope="col" style=" width: '.get_option('thumbnail_size_h').'px"></th>';
?>
	<div class="wrap">
		<h2>Comic Library</h2>
		<form method="post" action="" enctype="multipart/form-data">
			<p class="alignleft">
				<input type="file" name="new_comic_file" id="new_comic_file" />
				<input type="submit" name="submit-upload" class="button-primary upload" value="Upload Comic" />
				<input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
				<input type="hidden" name="webcomic_upload_status" value="0" />
				<input type="hidden" name="action" value="webcomic_upload" />
			</p>
		</form>
		<form method="post" action="">
			<p class="alignright">
				<input type="submit" value="Regenerate Thumbnails" class="button-primary" title="This may take several minutes for large libraries." />
				<input type="hidden" name="action" value="regen_comic_thumbs" />
			</p>
		</form>
<?php if($comic_posts): //Display the Library ?>
		<form action="" method="post">
			<div class="tablenav">
				<div class="alignleft actions">
					<select name="comic_chapter1">
					<?php
						echo '<option value="-1">N/A</option>';
						$chapters = get_chapters(array('hide_empty' => false));
						foreach($chapters as $chapter):
							if($chapter->parent == 0):
								if($not_first_volume):
									echo '</optgroup>';
								endif;
								echo '<optgroup label="'.$chapter->name.'">';
								$not_first_volume = true;
								continue;
							endif;
							$option = '<option value="'.$chapter->term_id.'">';
							$option .= $chapter->name.'</option>';
							echo $option;
						endforeach;
						echo '</optgroup>';
					?>
					</select>
					<input type="submit" value="Update Chapter" name="Submit1" class="button-secondary action" />
				</div>
				<?php echo $paged_output; ?>
				<div class="view-switch">
					<a href="admin.php?page=comic-library&amp;comic_library=list"><img<?php get_comic_library_view('list') ?> id="view-switch-list" src="../wp-includes/images/blank.gif" width="20" height="20" title="List View" alt="List View" /></a>
					<a href="admin.php?page=comic-library&amp;comic_library=thumbnail"><img<?php get_comic_library_view('thumbnail') ?>  id="view-switch-excerpt" src="../wp-includes/images/blank.gif" width="20" height="20" title="Thumbnail View" alt="Thumbnail View" /></a>
				</div>
			</div>
			<table class="widefat">
				<thead>
					<tr>
						<th scope="col" class="check-column"><input type="checkbox" /></th><?php echo $comic_thumb ?>
						<th scope="col">Comic</th>
						<th scope="col">Post</th>
						<th scope="col">Volume</th>
						<th scope="col">Chapter</th>
						<th scope="col">Slug</th>
						<th scope="col">Date</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th scope="col" class="check-column"><input type="checkbox" /></th><?php echo $comic_thumb ?>
						<th scope="col">Comic</th>
						<th scope="col">Post</th>
						<th scope="col">Volume</th>
						<th scope="col">Chapter</th>
						<th scope="col">Slug</th>
						<th scope="col">Date</th>
					</tr>
				</tfoot>
				<tbody>
				<?php foreach($comic_posts as $post): ?>
					<tr<?php if($post['file']): if($i%2): ?> class="alt"<?php endif; else: ?> style="background:#fdd"<?php endif; ?>>
						<th scope="row" class="check-column"><input type="checkbox" name="comics[]" value="<?php echo $post['id'] ?>" /></th>
						<?php if($comic_thumb): ?><td><a href="<?php echo $post['file'] ?>"><img src="<?php echo $post['thumb'] ?>" alt="<?php echo $post['name'] ?>" /></a></td><? endif; ?>
						<td><?php if($post['file']): ?><strong><a href="<?php echo $post['file'] ?>"><?php echo $post['name'] ?></a></strong><div class="row-actions"><a href="admin.php?page=comic-library<?php echo $paged_link ?>&amp;action=webcomic_delete&amp;file=<?php echo $post['name'] ?>">Delete</a></div><?php else: ?><strong>No Comic Found</strong><?php endif; ?></td>
						<td><strong><a href="<?php echo $post['permalink'] ?>"><?php echo $post['title'] ?></a></strong><div class="row-actions"><a href="post.php?action=edit&amp;post=<?php echo $post['id'] ?>">Edit</a></div></td>
						<td><?php echo $post['volume'] ?></td>
						<td><?php echo $post['chapter'] ?></td>
						<td><?php echo $post['slug'] ?></td>
						<td><?php echo $post['date'] ?><br /><?php echo $post['status'] ?></td>
					</tr>
				<?php $i++; endforeach; $i=0; ?>
				</tbody>
			</table>
			<div class="tablenav">
				<div class="alignleft actions">
					<select name="comic_chapter2">
					<?php
						echo '<option value="-1">N/A</option>';
						$chapters = get_chapters(array('hide_empty' => false));
						foreach($chapters as $chapter):
							if($chapter->parent == 0):
								if($not_first_volume):
									echo '</optgroup>';
								endif;
								echo '<optgroup label="'.$chapter->name.'">';
								$not_first_volume = true;
								continue;
							endif;
							$option = '<option value="'.$chapter->term_id.'">';
							$option .= $chapter->name.'</option>';
							echo $option;
						endforeach;
						echo '</optgroup>';
					?>
					</select>
					<input type="submit" value="Update Chapter" name="Submit2" class="button-secondary action" />
					<input type="hidden" name="action" value="update_comic_chapters" />
				</div>
				<?php echo $paged_output; ?>
			</div>
		</form>
<? else: ?>
		<p class="error"><strong>No comic posts could be found.</strong></p>
<?php endif; if($comic_files): //Display the orphaned comic files ?>
		<p>WebComic couldn't find any post information for the following comics:</p>
		<table class="widefat">
			<thead>
				<tr>
					<th scope="col">File</th>
					<th scope="col">Actions</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th scope="col">File</th>
					<th scope="col">Actions</th>
				</tr>
			</tfoot>
			<tbody>
			<?php foreach($comic_files as $file): ?>
				<tr<?php if($i%2): ?> class="alt"<?php endif; ?>>
					<td><strong><a href="<?php echo get_settings('siteurl').'/'.get_comic_directory().$file ?>"><?php echo $file ?></a></strong><div class="row-actions"><a href="admin.php?page=comic-library<?php echo $paged_link?>&amp;action=webcomic_delete&amp;file=<?php echo $file ?>">Delete</a></div></td>
					<td><form method="post" action=""><input type="text" name="webcomic_new_name" class="small-text" /><input type="submit" name="Submit" class="button-secondary upload" value="Rename" /> <input type="hidden" name="webcomic_old_name" value="<?php echo $file ?>" /><input type="hidden" name="action" value="webcomic_rename" /></form></td>
				</tr>
			<?php $i++; endforeach; $i=0; ?>
			</tbody>
		</table>
<? endif; ?>
	</div>
<?php
}



//Handles everything for the Chapters page
function comic_page_chapters(){
	if('create_new_chapter' == $_REQUEST['action']):  
		$chapter_name = trim($_REQUEST['chapter_name']);
		$chapter_nicename = ($_REQUEST['chapter_nicename']) ? sanitize_title($_REQUEST['chapter_nicename']) : sanitize_title($_REQUEST['chapter_name']);
		$chapter_parent = $_REQUEST['chapter_parent'];
		$chapter_description = $_REQUEST['chapter_description'];
		
		if(!$chapter_name):
			$error = 1;
		elseif(is_term($chapter_name,'chapter')):
			$error = 2;
		else:
			wp_insert_term($chapter_name,'chapter',array('description' => $chapter_description, 'parent' => $chapter_parent, 'slug' => $chapter_nicename));
			echo '<div id="message" class="updated fade"><p><strong>Added chapter: <q>'.$chapter_name.'</q></strong></p></div>';
		endif;
	endif;
	
	if('update_chapter' == $_REQUEST['action']):
		$chapter_name = trim($_REQUEST['chapter_name']);
		$chapter_nicename = ($_REQUEST['chapter_nicename']) ? sanitize_title($_REQUEST['chapter_nicename']) : sanitize_title($_REQUEST['chapter_name']);
		$chapter_parent = $_REQUEST['chapter_parent'];
		$chapter_description = $_REQUEST['chapter_description'];
		
		if(!is_wp_error(wp_update_term($_REQUEST['chapter_id'],'chapter',array('name' => $chapter_name, 'slug' => $chapter_nicename, 'parent' => $chapter_parent, 'description' => $chapter_description))))
			echo '<div id="message" class="updated fade"><p><strong>Updted chapter: <q>'.$chapter_name.'</q></strong></p></div>';
		else
			echo '<div id="message" class="error"><p><strong>The chapter could not be updated.</strong></p></div>';
	endif;
	
	if('delete_chapter' == $_REQUEST['action']):
		wp_delete_term($_REQUEST['chapter'],'chapter');
		echo '<div id="message" class="updated fade"><p><strong>Deleted chapter: <q>'.$chapter_name.'</q></strong></p></div>';
	endif;
	
	if('modify_chapters' == $_REQUEST['action']):
		$action = (isset($_REQUEST['Submit1'])) ? $_REQUEST['chapters_action1'] : $_REQUEST['chapters_action2'];
		$chapters = $_REQUEST['chapters'];
		if(!$chapters):
			echo '<div id="message" class="error"><p><strong>Please select at least one chapter.</strong></p></div>';
		else:
			switch($action):
				case 'delete':
					foreach($chapters as $chapter):
						if($chapter == get_comic_current_chapter())
							update_option('comic_current_chapter',-1);
						wp_delete_term($chapter,'chapter');
					endforeach;
					echo '<div id="message" class="updated fade"><p><strong>Chapters deleted.</strong></p></div>';
				break;
				default: echo '<div id="message" class="error"><p><strong>Please select a valid action.</strong></p></div>';
			endswitch;
		endif;
	endif;
?>
	<div class="wrap">
<?php if('edit_chapter' == $_REQUEST['action']): $the_chapter = get_term_to_edit($_REQUEST['chapter'],'chapter'); //Show the edit chapter form ?>
		<h2>Edit Chapter</h2>
		<form action="" method="post">
			<table class="form-table">
				<tr class="form-field">
					<th scope="row"><label for="chapter_name">Chapter Name</label></th>
					<td><input name="chapter_name" id="chapter_name" type="text" value="<?php echo $the_chapter->name ?>" size="40"<?php if($error) echo ' style="border-color:#c00"' ?> /><br />The name is used to identify the chapter almost everywhere.</td>
				</tr>
				<tr class="form-field">
					<th scope="row"><label for="chapter_nicename">Chapter Slug</label></th>
					<td><input name="chapter_nicename" id="chapter_nicename" type="text" value="<?php echo $the_chapter->slug ?>" size="40" /><br />The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</td>
				</tr>
				<?php if($the_chapter->parent != 0): ?>
				<tr class="form-field">
					<th scope="row"><label for="chapter_parent">Chapter Volume</label></th>
					<td>
						<select name="chapter_parent" id="chapter_parent"> 
						<?php
							$chapters = get_chapters(array('hide_empty' => false));
							foreach($chapters as $chapter):
								if($chapter->parent != 0) continue;
								$option = '<option value="'.$chapter->term_id;
								if($chapter->term_id == $the_chapter->parent)
									$option .= '" selected="selected">';
								else
									$option .= '">';
								$option .= $chapter->name.'</option>';
								echo $option;
							endforeach;
							echo '</optgroup>';
						?>
						</select><br />Select the volume that this chapter belongs to.
					</td>
				</tr>
				<?php endif; ?>
				<tr class="form-field">
					<th scope="row"><label for="chapter_description">Description</label><br /></th>
					<td><textarea name="chapter_description" id="chapter_description" rows="5" cols="40"><?php echo $the_chapter->description ?></textarea><br />Useful for providing a brief overview of the events covered in this chapter.</td>
				</tr>
			</table>
			<p class="submit"><input type="submit" class="button" name="submit" value="Edit Chapter" /><input type="hidden" name="action" value="update_chapter" /><input type="hidden" name="chapter_id" value="<?php echo $_REQUEST['chapter'] ?>" /></p> 
		</form>
<?php else: ?>
		<h2>Comic Chapters</h2>
		<div id="col-right">
			<div class="col-wrap">
			<?php if(!get_chapters(array('hide_empty' => false))): ?>
				<p>Chapters are a useful (but optional) way of categorizing your comics, similar to post categories. You can create new chapters or modify existing ones here and assign comics to them from the Comic Library.</p>
				<p>To start using chapters, you'll need to create two of them: the first will be a <em>volume</em>, which can contain any number of regular chapters. You should assign the second chapter to your newly created volume.</p>
				<p>Don't forget to update the <strong>Current Chapter</strong> setting, which will assign any new comic posts to the chapter you select.</p>
			<?php else: ?>
				<form action="" method="post">
					<div class="tablenav">
						<div class="alignleft actions">
							<select name="chapters_action1">
								<option value="-1">Action</option>
								<option value="delete">Delete</option>
							</select>
							<input type="submit" value="Apply" name="Submit1" class="button-secondary action" />
						</div>
					</div>
					<table class="widefat">
						<colgroup>
							<col />
							<col style="width:20%" />
							<col />
							<col style="width:20%" />
							<col />
						</colgroup>
						<thead>
							<tr>
								<th scope="col" id="cb" class="check-column"><input type="checkbox" /></th>
								<th scope="col" style="width:22%">Name</th>
								<th scope="col">Description</th>
								<th scope="col" style="width:22%">Slug</th>
								<th scope="col" style="width:1%">Pages</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th scope="col" id="cb" class="check-column"><input type="checkbox" /></th>
								<th scope="col">Name</th>
								<th scope="col">Description</th>
								<th scope="col">Slug</th>
								<th scope="col">Pages</th>
							</tr>
						</tfoot>
						<tbody>
						<?php
							$chapters = get_chapters(array('hide_empty' => false));
							foreach($chapters as $chapter):
							$chapter_title = (0 != $chapter->parent) ? '&mdash; '.$chapter->name : $chapter->name;
						?>
							<tr<?php if($i%2) echo' class="alt"'; ?>>
								<th scope="row" class="check-column"><input type="checkbox" name="chapters[]" value="<?php echo $chapter->term_id ?>" /></th>
								<td><strong><?php echo $chapter_title ?></strong><div class="row-actions"><a href="admin.php?page=comic-chapters&amp;action=edit_chapter&amp;chapter=<?php echo $chapter->term_id ?>">Edit</a> | <a href="admin.php?page=comic-chapters&amp;action=delete_chapter&amp;chapter=<?php echo $chapter->term_id ?>">Delete</a></div></td>
								<td><?php echo $chapter->description ?></td>
								<td><?php echo $chapter->slug ?></td>
								<td class="num"><?php echo $chapter->count ?></td>
							</tr>
						<?php $i++; endforeach; $i=0; ?>
						</tbody>
					</table>
					<div class="tablenav">
						<div class="alignleft actions">
							<select name="chapters_action2">
								<option value="-1">Action</option>
								<option value="delete">Delete</option>
							</select>
							<input type="submit" value="Apply" name="Submit2" class="button-secondary action" />
							<input type="hidden" name="action" value="modify_chapters" />
						</div>
					</div>
				</form>
			<?php endif; ?>
			</div>
		</div>
		
		<div id="col-left">
			<div class="col-wrap">
				<div class="form-wrap">  
					<h3>Add Chapter</h3>
					<form action="" method="post">
						<?php if($error>1) echo '<div id="message" class="error"><p>The chapter you are trying to create already exists.</p></div>' ?>
						<div class="form-field form-required"<?php if($error) echo ' style="background:#ffebe8"' ?>> 
							<label for="chapter_name">Chapter Name</label> 
							<input name="chapter_name" id="chapter_name" type="text" value="" size="40"<?php if($error) echo ' style="border-color:#c00"' ?> /> 
							<p>The name is used to identify the chapter almost everywhere.</p> 
						</div>
						<div class="form-field"> 
							<label for="chapter_nicename">Chapter Slug</label> 
							<input name="chapter_nicename" id="chapter_nicename" type="text" value="" size="40" /> 
							<p>The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</p> 
						</div>
						<div class="form-field"> 
							<label for="chapter_parent">Chapter Volume</label> 
							<select name="chapter_parent" id="chapter_parent"> 
							<?php
								echo '<option value="0">None</option>';
								$chapters = get_chapters(array('hide_empty' => false,'pad_counts' => 1));
								foreach($chapters as $chapter):
									if($chapter->parent != 0) continue;
									$option = '<option value="'.$chapter->term_id;
									if($chapter->term_id == $the_chapter->parent)
										$option .= '" selected="selected">';
									else
										$option .= '">';
									$option .= $chapter->name.'</option>';
									echo $option;
								endforeach;
								echo '</optgroup>';
							?>
							</select> 
							<p>Select <strong>None</strong> to turn this chapter into a new volume that you can add other chapters to.</p> 
						</div>
						<div class="form-field"> 
							<label for="chapter_description">Description</label> 
							<textarea name="chapter_description" id="chapter_description" rows="5" cols="40"></textarea> 
							<p>Useful for providing a brief overview of the events covered in this chapter.</p> 
						</div> 
						<p class="submit"><input type="submit" class="button" name="submit" value="Add Chapter" /><input type="hidden" name="action" value="create_new_chapter" /></p> 
					</form>
				</div>
			</div>
		</div>
 <?php endif; ?>
	</div>
<?php } ?>
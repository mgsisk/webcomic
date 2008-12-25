<?php
function comic_page_library(){
	if(isset($_REQUEST['comic_library']))
		update_option('comic_library_view',$_REQUEST['comic_library']);
	
	
	
	if('update_comic_chapters' == $_REQUEST['action']):
		check_admin_referer('update_comic_chapters');
		
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
				foreach($comics as $comic)
					add_post_to_chapter($comic,$chapter);
					
				echo '<div id="message" class="updated fade"><p><strong>All comics assigned to new chapter.</strong></p></div>';
			endif;
		endif;
	endif;
	
	
	
	if('webcomic_upload' == $_REQUEST['action']):
		check_admin_referer('webcomic_upload');
		
		$ext = strtolower(end(explode('.',basename($_FILES['new_comic_file']['name']))));
		
		switch($ext):
			case 'gif': break;
			case 'jpg': break;
			case 'jpeg': break;
			case 'png': break;
			case 'zip': break;
			default: $invalid_format = true;
		endswitch;
		
		if($invalid_format):
			echo '<div id="message" class="error"><p><strong>Invalid file format. Images must be either gif, jpg, jpeg, or png.</strong></p></div>';
		else:
			$ext = '.'.$ext;
			$file = basename($_FILES['new_comic_file']['name'], $ext);
			
			if('on' == get_option('comic_secure_names'))
				$hash = '-'.md5(microtime().basename($_FILES['new_comic_file']['name']));
			
			$target_path = ABSPATH.get_comic_directory().$file.$hash.$ext; 
			
			if(move_uploaded_file($_FILES['new_comic_file']['tmp_name'],$target_path)):
				$img_dim = getimagesize($target_path);
				$img_crop = ('on' == get_option('comic_thumbnail_crop')) ? true : false;
				$img_lw = get_option('comic_large_size_w');
				$img_lh = get_option('comic_large_size_h');
				$img_mw = get_option('comic_medium_size_w');
				$img_mh = get_option('comic_medium_size_h');
				$img_tw = get_option('comic_thumbnail_size_w');
				$img_th = get_option('comic_thumbnail_size_h');
				
				if(!file_exists(ABSPATH.get_comic_directory(true)))
					mkdir(ABSPATH.get_comic_directory(true),0775,true);
				
				if($img_dim[0] > $img_lw || $img_dim[1] > $img_lh)
					image_resize($target_path,$img_lw,$img_lh,0,'large',ABSPATH.get_comic_directory(true));
				if($img_dim[0] > $img_mw || $img_dim[1] > $img_mh)
					image_resize($target_path,$img_mw,$img_mh,0,'medium',ABSPATH.get_comic_directory(true));
				if($img_dim[0] > $img_tw || $img_dim[1] > $img_th)
					image_resize($target_path,$img_tw,$img_th,$img_crop,'thumb',ABSPATH.get_comic_directory(true));
					
				if('date' == get_option('comic_name_format') && 'on' == get_option('comic_auto_post') && strtotime($file)):
					$post_date = date('Y-m-d H:i:s', strtotime($file));
					$psot_date_gmt = get_gmt_from_date($post_date);
					$error = wp_insert_post(array(
						'post_content' => ' ',
						'post_status' => 'publish',
						'post_category' => array(get_comic_category()),
						'post_date' => $post_date,
						'post_date_gmt' => $post_date_gmt,
						'post_title' => $file
					));
				endif;
					
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
		check_admin_referer('webcomic_rename');
		
		if('' != $_REQUEST['webcomic_new_name']):
			$info = pathinfo(ABSPATH.get_option('comic_directory').'/'.$_REQUEST['webcomic_old_name']);
			$old_name = $info['filename'];
			$ext = $info['extension'];
			
			if('on' == get_option('comic_secure_names'))
				$hash = '-'.md5(microtime().basename($_FILES['new_comic_file']['name']));
			
			if(rename(ABSPATH.get_comic_directory().$_REQUEST['webcomic_old_name'],ABSPATH.get_comic_directory().$_REQUEST['webcomic_new_name'].$hash.'.'.$ext)):
			 
				$dir = opendir(ABSPATH.get_comic_directory(true));
				while(($file = readdir($dir)) !== false):
					if(strstr($file,$old_name) !== false ):
						if(strstr($file,$old_name.'-large.'.$ext))
							rename(ABSPATH.get_comic_directory(true).$old_name.'-large.'.$ext,ABSPATH.get_comic_directory(true).$_REQUEST['webcomic_new_name'].$hash.'-large.'.$ext);
						if(strstr($file,$old_name.'-medium.'.$ext))
							rename(ABSPATH.get_comic_directory(true).$old_name.'-medium.'.$ext,ABSPATH.get_comic_directory(true).$_REQUEST['webcomic_new_name'].$hash.'-medium.'.$ext);
						if(strstr($file,$old_name.'-thumb.'.$ext))
							rename(ABSPATH.get_comic_directory(true).$old_name.'-thumb.'.$ext,ABSPATH.get_comic_directory(true).$_REQUEST['webcomic_new_name'].$hash.'-thumb.'.$ext);
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
		check_admin_referer('webcomic_delete');
		
		if(unlink(ABSPATH.get_comic_directory().$_REQUEST['file'])):
			$ext = strrchr($_REQUEST['file'],'.');
			$fid = substr($_REQUEST['file'], 0, -strlen($ext));
			 
			$dir = opendir(ABSPATH.get_comic_directory(true));
			while(($file = readdir($dir)) !== false):
				if(strstr($file,$fid) !== false )
					unlink(ABSPATH.get_comic_directory(true).$file);
			endwhile;
			closedir($dir);
			
			echo '<div id="message" class="updated fade"><p><strong>Comic '.$_REQUEST['file'].' deleted.</strong></p></div>';
		else:
			echo '<div id="message" class="error"><p><strong>Comic '.$_REQUEST['file'].' could not be deleted.</strong></p></div>';
		endif;
	endif;
	
	
	
	if('regen_comic_thumbs' == $_REQUEST['action']):
		check_admin_referer('regen_comic_thumbs');
		
		//Get our comic and media options
		$original_path = ABSPATH.get_comic_directory();
		$target_path = ABSPATH.get_comic_directory(true);
		$img_crop = ('on' == get_option('comic_thumbnail_crop')) ? true : false;
		$img_lw = get_option('comic_large_size_w');
		$img_lh = get_option('comic_large_size_h');
		$img_mw = get_option('comic_medium_size_w');
		$img_mh = get_option('comic_medium_size_h');
		$img_tw = get_option('comic_thumbnail_size_w');
		$img_th = get_option('comic_thumbnail_size_h');
		
		//Delete everything in the thumbs folder
		$dir = opendir(ABSPATH.get_comic_directory(true));
		while(($file = readdir($dir)) !== false):
			if(is_dir(ABSPATH.get_comic_directory(true).$file))
				continue;
			unlink(ABSPATH.get_comic_directory(true).$file);
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
	
	
	
	if('generate_comic_posts' == $_REQUEST['action']):
		check_admin_referer('generate_comic_posts');
		
		$orphans = explode('/',$_REQUEST['orphaned_comics']);
		array_pop($orphans);
		
		foreach($orphans as $key => $value):
			$ext = strrchr($value,'.');
			$file = substr($value, 0, -strlen($ext));
			
			if(strtotime($file)):
				$post_date = date('Y-m-d H:i:s', strtotime($file));
				$psot_date_gmt = get_gmt_from_date($post_date);
				$error = wp_insert_post(array(
					'post_content' => ' ',
					'post_status' => 'publish',
					'post_category' => array(get_comic_category()),
					'post_date' => $post_date,
					'post_date_gmt' => $post_date_gmt,
					'post_title' => $file
				));
				$i++;
			endif;
		endforeach;
		
		if(!$i):
			echo '<div id="message" class="error"><p><strong>No posts could be automatically generated.</strong></p></div>';
		else:
			$s = ($i > 1) ? 's' : '';
			echo '<div id="message" class="updated fade"><p><strong>'.$i.' post'.$s.' automatically generated.</strong></p></div>';
		endif;
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
			$chapter_temp = get_the_chapter();
			$volume_temp = get_the_volume();
			$comic_posts_temp['id'] = $post->ID;
			$comic_posts_temp['permalink'] = get_permalink();
			$comic_posts_temp['title'] = (get_the_title()) ? get_the_title() : '(no title)';
			$comic_posts_temp['custom'] = (get_post_meta($comic_id,'comic_filename',true)) ? get_post_meta($comic_id,'comic_filename',true) : '&mdash';
			$comic_posts_temp['slug'] = $post->post_name;
			$comic_posts_temp['date'] = get_the_time(get_option('date_format'));
			$comic_posts_temp['file'] = get_the_comic(false,'file');
			$comic_posts_temp['thumb'] = get_the_comic(false,'file','thumb');
			$comic_posts_temp['name'] = end(explode('/',$comic_posts_temp['file']));
			$comic_posts_temp['volume'] = ($volume_temp) ? $volume_temp['title'] : '&mdash;';
			$comic_posts_temp['chapter'] = ($chapter_temp) ? $chapter_temp['title'] : '&mdash;';
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
			<?php wp_nonce_field('webcomic_upload'); ?>
			<p class="alignleft">
				<input type="file" name="new_comic_file" id="new_comic_file" />
				<input type="submit" name="submit-upload" class="button-primary upload" value="Upload Comic" />
				<input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
				<input type="hidden" name="webcomic_upload_status" value="0" />
				<input type="hidden" name="action" value="webcomic_upload" />
			</p>
		</form>
		<form method="post" action="">
			<?php wp_nonce_field('regen_comic_thumbs'); ?>
			<p class="alignright">
				<input type="submit" value="Regenerate Thumbnails" class="button-primary" title="This may take several minutes for large libraries." />
				<input type="hidden" name="action" value="regen_comic_thumbs" />
			</p>
		</form>
<?php if($comic_posts): //Display the Library ?>
		<form action="" method="post">
			<?php wp_nonce_field('update_comic_chapters'); ?>
			<div class="tablenav">
				<div class="alignleft actions">
					<select name="comic_chapter1">
						<option value="-1">N/A</option>
					<?php
						$collection = get_the_collection(false);
						foreach($collection as $vid => $volume):
					?>
						<optgroup label="<?php echo $volume['title'] ?>">
						<?php foreach($volume['chapters'] as $cid => $chapter): ?>
							<option value="<?php echo $cid ?>"><?php echo $chapter['title'] ?></option>
							<?php endforeach ?>
						</optgroup>
					<?php endforeach ?>
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
						<th scope="col">Custom</th>
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
						<th scope="col">Custom</th>
						<th scope="col">Slug</th>
						<th scope="col">Date</th>
					</tr>
				</tfoot>
				<tbody>
				<?php foreach($comic_posts as $post): ?>
					<tr<?php if($post['file']): if($i%2): ?> class="alt"<?php endif; else: ?> style="background:#fdd"<?php endif; ?>>
						<th scope="row" class="check-column"><input type="checkbox" name="comics[]" value="<?php echo $post['id'] ?>" /></th>
						<?php if($comic_thumb): ?><td><a href="<?php echo $post['file'] ?>"><img src="<?php echo $post['thumb'] ?>" alt="<?php echo $post['name'] ?>" /></a></td><?php endif; ?>
						<td><?php if($post['file']): ?><strong><a href="<?php echo $post['file'] ?>"><?php echo $post['name'] ?></a></strong><div class="row-actions"><a href="<?php echo wp_nonce_url('admin.php?page=comic-library'.$paged_link.'&amp;action=webcomic_delete&amp;file='.$post['name'], 'webcomic_delete') ?>">Delete</a></div><?php else: ?><strong>No Comic Found</strong><?php endif; ?></td>
						<td><strong><a href="<?php echo $post['permalink'] ?>"><?php echo $post['title'] ?></a></strong><div class="row-actions"><a href="post.php?action=edit&amp;post=<?php echo $post['id'] ?>">Edit</a></div></td>
						<td><?php echo $post['volume'] ?></td>
						<td><?php echo $post['chapter'] ?></td>
						<td><?php echo $post['custom'] ?></td>
						<td><?php echo $post['slug'] ?></td>
						<td><?php echo $post['date'] ?><br /><?php echo $post['status'] ?></td>
					</tr>
				<?php $i++; endforeach; $i=0; ?>
				</tbody>
			</table>
			<div class="tablenav">
				<div class="alignleft actions">
					<select name="comic_chapter2">
						<option value="-1">N/A</option>
					<?php
						$collection = get_the_collection(false);
						foreach($collection as $vid => $volume):
					?>
						<optgroup label="<?php echo $volume['title'] ?>">
						<?php foreach($volume['chapters'] as $cid => $chapter): ?>
							<option value="<?php echo $cid ?>"><?php echo $chapter['title'] ?></option>
							<?php endforeach ?>
						</optgroup>
					<?php endforeach ?>
					</select>
					<input type="submit" value="Update Chapter" name="Submit2" class="button-secondary action" />
					<input type="hidden" name="action" value="update_comic_chapters" />
				</div>
				<?php echo $paged_output; ?>
			</div>
		</form>
<?php else: ?>
		<p class="error"><strong>No comic posts could be found.</strong></p>
<?php endif; if($comic_files): //Display the orphaned comic files ?>
		<h3 class="alignleft">Orphaned Comics</h3>
		<?php if('date' == get_option('comic_name_format')): ?>
		<form method="post" action="">
			<?php wp_nonce_field('generate_comic_posts'); ?>
			<p class="alignright">
				<input type="submit" value="Generate Missing Posts" class="button-primary" title="Attempt to generate posts for orphaned comics." />
				<input type="hidden" name="orphaned_comics" value="<?php foreach($comic_files as $file) echo $file.'/'; ?>" />
				<input type="hidden" name="action" value="generate_comic_posts" />
			</p>
		</form>
		<?php endif ?>
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
					<td><strong><a href="<?php echo get_settings('siteurl').'/'.get_comic_directory().$file ?>"><?php echo $file ?></a></strong><div class="row-actions"><a href="<?php echo wp_nonce_url('admin.php?page=comic-library'.$paged_link.'&amp;action=webcomic_delete&amp;file='.$file,'webcomic_delete') ?>">Delete</a></div></td>
					<td><form method="post" action=""><?php wp_nonce_field('webcomic_rename'); ?><input type="text" name="webcomic_new_name" class="small-text" /><input type="submit" name="Submit" class="button-secondary upload" value="Rename" /> <input type="hidden" name="webcomic_old_name" value="<?php echo $file ?>" /><input type="hidden" name="action" value="webcomic_rename" /></form></td>
				</tr>
			<?php $i++; endforeach; $i=0; ?>
			</tbody>
		</table>
<?php endif; ?>
	</div>
<?php } ?>
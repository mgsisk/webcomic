<?php
function comic_page_settings(){ 
	if('webcomic_save_settings' == $_REQUEST['action']):
		check_admin_referer('webcomic_save_settings');
		
		$comic_feed           = ($_REQUEST['comic_feed']) ? 'on' : 'off' ;
		$comic_thumbnail_crop = ($_REQUEST['comic_thumbnail_crop']) ? 'on' : 'off' ;
		$comic_auto_post      = ($_REQUEST['comic_auto_post']) ? 'on' : 'off' ;
		$comic_secure_names   = ($_REQUEST['comic_secure_names']) ? 'on' : 'off' ;
		
		update_option('comic_category',$_REQUEST['comic_category']);
		update_option('comic_directory',strval(trim($_REQUEST['comic_directory'],'/')));
		update_option('comic_current_chapter',$_REQUEST['comic_current_chapter']);
		update_option('comic_feed',$comic_feed);
		update_option('comic_feed_size',$_REQUEST['comic_feed_size']);
		update_option('comic_thumbnail_size_w',intval($_REQUEST['comic_thumbnail_size_w']));
		update_option('comic_thumbnail_size_h',intval($_REQUEST['comic_thumbnail_size_h']));
		update_option('comic_thumbnail_crop',$comic_thumbnail_crop);
		update_option('comic_medium_size_w',intval($_REQUEST['comic_medium_size_w']));
		update_option('comic_medium_size_h',intval($_REQUEST['comic_medium_size_h']));
		update_option('comic_large_size_w',intval($_REQUEST['comic_large_size_w']));
		update_option('comic_large_size_h',intval($_REQUEST['comic_large_size_h']));
		update_option('comic_name_format',$_REQUEST['comic_name_format']);
		update_option('comic_auto_post',$comic_auto_post);
		update_option('comic_secure_names',$comic_secure_names);
		
		$days = array('d','j','z','w');
		foreach($days as $day):
			if(strstr($_REQUEST['comic_name_format_date'],$day)):
				$days = true;
				break;
			endif;
		endforeach;
		
		$months = array('F','m','M','n');
		foreach($months as $month):
			if(strstr($_REQUEST['comic_name_format_date'],$month)):
				$months = true;
				break;
			endif;
		endforeach;
		
		$years = array('o','Y','y');
		foreach($years as $year):
			if(strstr($_REQUEST['comic_name_format_date'],$year)):
				$years = true;
				break;
			endif;
		endforeach;
		
		if($_REQUEST['comic_name_format_date'] || true === $days || true === $months || true === $years):
			update_option('comic_name_format_date',$_REQUEST['comic_name_format_date']);
		endif;
		
		if(!file_exists(ABSPATH.get_comic_directory()))
			mkdir(ABSPATH.get_comic_directory(),0775,true);
		if(!file_exists(ABSPATH.get_comic_directory(true)))
			mkdir(ABSPATH.get_comic_directory(true),0775,true);
		
		echo '<div id="message" class="updated fade"><p><strong>Settings saved.</strong></p></div>';
	endif;
?>
	<div class="wrap">
		<h2>WebComic</h2>
		<form method="post" action="">
			<?php wp_nonce_field('webcomic_save_settings'); ?>
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
						<input type="text" name="comic_directory" id="comic_directory" value="<?php echo get_option('comic_directory'); ?>" class="code" /> <span class="setting-description">WebComic will look for your comics in <a href="<?php echo get_settings('siteurl').'/'.get_comic_directory(); ?>"><?php echo get_settings('siteurl').'/'.get_comic_directory(); ?></a></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="comic_current_chapter">Current Chapter</label></th>
					<td>
						<select name="comic_current_chapter">
							<option value="-1">N/A</option>
						<?php
							$collection = get_the_collection(false);
							foreach($collection as $vid => $volume):
						?>
							<optgroup label="<?php echo $volume['title'] ?>">
							<?php foreach($volume['chapters'] as $cid => $chapter): ?>
								<option value="<?php echo $cid ?>"<?php if($cid == get_option('comic_current_chapter')) echo ' selected="selected"' ?>><?php echo $chapter['title'] ?></option>
								<?php endforeach ?>
							</optgroup>
						<?php endforeach ?>
						</select><span class="setting-description">Select the chapter new comic posts will be assigned to.</span>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="comic_feed">Feed</label></th>
					<td><input name="comic_feed" type="checkbox" id="comic_feed" value="on"<?php if('on' == get_option('comic_feed')) print ' checked="checked"'; ?> />
					<label>Include
						<select name="comic_feed_size">
							<option value="full"<?php if('full' == get_option('comic_feed_size')) echo ' selected="selected"'; ?>>Full</option>
							<option value="large"<?php if('large' == get_option('comic_feed_size')) echo ' selected="selected"'; ?>>Large</option>
							<option value="medium"<?php if('medium' == get_option('comic_feed_size')) echo ' selected="selected"'; ?>>Medium</option>
							<option value="thumb"<?php if('thumb' == get_option('comic_feed_size')) echo ' selected="selected"'; ?>>Thumbnail</option>
						</select>
					comic images in feed</label></td>
				</tr>
				<tr>
					<th scope="row">Thumbnail Size</th>
					<td>
						<label>Width <input type="text" name="comic_thumbnail_size_w" value="<?php echo get_option('comic_thumbnail_size_w') ?>" class="small-text" /></label> <label>Height <input type="text" name="comic_thumbnail_size_h" value="<?php echo get_option('comic_thumbnail_size_h') ?>" class="small-text" /></label>
						<p><label><input type="checkbox" name="comic_thumbnail_crop" value="on"<?php if('on' == get_option('comic_thumbnail_crop')) echo ' checked="checked"'; ?> /> Crop thumbnail to exact dimensions (normally thumbnails are proportional)</label></p>
					</td>
				</tr>
				<tr>
					<th scope="row">Medium Size</th>
					<td><label>Max Width <input type="text" name="comic_medium_size_w" value="<?php echo get_option('comic_medium_size_w') ?>" class="small-text" /></label> <label>Max Height <input type="text" name="comic_medium_size_h" value="<?php echo get_option('comic_medium_size_h') ?>" class="small-text" /></label></td>
				</tr>
				<tr>
					<th scope="row">Large Size</th>
					<td><label>Max Width <input type="text" name="comic_large_size_w" value="<?php echo get_option('comic_large_size_w') ?>" class="small-text" /></label> <label>Max Height <input type="text" name="comic_large_size_h" value="<?php echo get_option('comic_large_size_h') ?>" class="small-text" /></label></td>
				</tr>
			</table>
			<h3>How do you name your comics?</h3>
			<table class="form-table">
				<tr>
					<th scope="row"><label><input type="radio" name="comic_name_format" value="date"<?php if('date' == get_option('comic_name_format')) echo ' checked="checked"'; ?> /> Date</label></th>
					<td>
						<input type="text" name="comic_name_format_date" value="<?php print get_option('comic_name_format_date'); ?>" class="small-text" /> Your comics must have the  <a href="http://us2.php.net/date">date</a> somewhere in the filename, in the format of <code><?php echo date(get_option('comic_name_format_date')); ?></code>.
						<p><label for="comic_auto_post"><input name="comic_auto_post" type="checkbox" id="comic_auto_post" value="on"<?php if('on' == get_option('comic_auto_post')) print ' checked="checked"'; ?> /> Automatically create comic posts during upload</label></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label><input type="radio" name="comic_name_format" value="slug"<?php if('slug' == get_option('comic_name_format')) echo ' checked="checked"'; ?> /> Title</label></th>
					<td>Your comics must have the <a href="http://lorelle.wordpress.com/2007/09/02/understanding-the-wordpress-post-title-and-post-slug/">post slug</a> somewhere in the filename.</td>
				</tr>
				<tr>
					<th scope="row"><label><input type="radio" name="comic_name_format" value="meta"<?php if('meta' == get_option('comic_name_format')) echo ' checked="checked"'; ?> /> Custom</label></th>
					<td>Your comics must have the value of a <a href="http://codex.wordpress.org/Using_Custom_Fields">custom field</a> called <code>comic_filename</code> somewhere in the filename.</td>
				</tr>
				<tr>
					<th scope="row"><label><input type="checkbox" name="comic_secure_names" value="on"<?php if('on' == get_option('comic_secure_names')) print ' checked="checked"'; ?> /> Secure</label></th>
					<td>Automatically append a secure hash to comic filenames during upload to prevent read ahead and archive scraping.</td>
				</tr>
			</table>
		<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="Save Changes" />
			<input type="hidden" name="action" value="webcomic_save_settings" />
		</p>
		</form>
	</div>
<?php } ?>
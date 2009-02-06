<?php
/**
 * This document contains all functions related to the Settings page.
 * These functions were in wc-admin.php prior to version 1.4.
 * 
 * @package WebComic
 * @since 1.4
 */
 
function comic_page_settings(){
	load_webcomic_domain();
	
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
		
		$days = array('d','j','z');
		foreach($days as $day):
			if(false !== strpos($_REQUEST['comic_name_format_date'],$day)):
				$days = true;
				break;
			endif;
		endforeach;
		
		$months = array('F','m','M','n');
		foreach($months as $month):
			if(false !== strpos($_REQUEST['comic_name_format_date'],$month)):
				$months = true;
				break;
			endif;
		endforeach;
		
		$years = array('o','Y','y');
		foreach($years as $year):
			if(false !== strpos($_REQUEST['comic_name_format_date'],$year)):
				$years = true;
				break;
			endif;
		endforeach;
		
		if($_REQUEST['comic_name_format_date'] && true === $days && true === $months && true === $years)
			update_option('comic_name_format_date',$_REQUEST['comic_name_format_date']);
		
		if(!file_exists(ABSPATH.get_comic_directory()))
			mkdir(ABSPATH.get_comic_directory(),0775);
		if(!file_exists(ABSPATH.get_comic_directory(true)))
			mkdir(ABSPATH.get_comic_directory(true),0775);
		
		echo '<div id="message" class="updated fade"><p><strong>'.__('Settings saved.','webcomic').'</strong></p></div>';
	endif;
?>
	<div class="wrap">
		<div id="icon-webcomic" class="icon32"><img src="<?php echo plugins_url('webcomic/webcomic.png') ?>" alt="icon" /></div>
		<h2><?php _e('Settings','webcomic') ?></h2>
		<form method="post" action="">
			<?php wp_nonce_field('webcomic_save_settings') ?>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="comic_category"><?php _e('Comic Category','webcomic') ?></label></th>
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
						<span class="setting-description"><?php _e('Select the category that your comic posts are assigned to.','webcomic') ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="comic_directory"><?php _e('Comic Directory','webcomic') ?></label></th>
					<td>
						<input type="text" name="comic_directory" id="comic_directory" value="<?php echo get_option('comic_directory'); ?>" class="code" /> <span class="setting-description"><?php _e('WebComic will look for your comics in','webcomic') ?> <a href="<?php echo get_settings('siteurl').'/'.get_comic_directory(); ?>"><?php echo get_settings('siteurl').'/'.get_comic_directory(); ?></a></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="comic_current_chapter"><?php _e('Current Chapter','webcomic') ?></label></th>
					<td>
						<select name="comic_current_chapter" id="comic_current_chapter">
							<option value="-1"><?php _e('N\A','webcomic') ?></option>
						<?php
							$collection = get_the_collection('hide_empty=0');
							foreach($collection as $volume):
						?>
							<optgroup label="<?php echo $volume['title'] ?>">
							<?php foreach($volume['chapters'] as $chapter): ?>
								<option value="<?php echo $chapter['id'] ?>"<?php if($chapter['id'] == get_option('comic_current_chapter')) echo ' selected="selected"' ?>><?php echo $chapter['title'] ?></option>
								<?php endforeach ?>
							</optgroup>
						<?php endforeach ?>
						</select><span class="setting-description"><?php _e('Select the chapter new comic posts will be assigned to.','webcomic') ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="comic_feed"><?php _e('Feeds','webcomic') ?></label></th>
					<td><input name="comic_feed" type="checkbox" id="comic_feed" value="on"<?php if('on' == get_option('comic_feed')) print ' checked="checked"'; ?> />
					<label><?php _e('Include','webcomic') ?>
						<select name="comic_feed_size">
							<option value="full"<?php if('full' == get_option('comic_feed_size')) echo ' selected="selected"'; ?>><?php _e('Full','webcomic') ?></option>
							<option value="large"<?php if('large' == get_option('comic_feed_size')) echo ' selected="selected"'; ?>><?php _e('Large','webcomic') ?></option>
							<option value="medium"<?php if('medium' == get_option('comic_feed_size')) echo ' selected="selected"'; ?>><?php _e('Medium','webcomic') ?></option>
							<option value="thumb"<?php if('thumb' == get_option('comic_feed_size')) echo ' selected="selected"'; ?>><?php _e('Thumbnail','webcomic') ?></option>
						</select>
					<?php _e('comic images in feeds','webcomic') ?></label></td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Posts','webcomic') ?></th>
					<td><label for="comic_auto_post"><input name="comic_auto_post" type="checkbox" id="comic_auto_post" value="on"<?php if('on' == get_option('comic_auto_post')) print ' checked="checked"'; ?> /> <?php _e('Automatically create comic posts during upload','webcomic') ?></label></td>
				</tr>
			</table>
			<h3><?php _e('Image Sizes','webcomic') ?></h3>
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e('Thumbnail Size','webcomic') ?></th>
					<td>
						<label><?php _e('Width','webcomic') ?> <input type="text" name="comic_thumbnail_size_w" value="<?php echo get_option('comic_thumbnail_size_w') ?>" class="small-text" /></label> <label><?php _e('Height','webcomic') ?> <input type="text" name="comic_thumbnail_size_h" value="<?php echo get_option('comic_thumbnail_size_h') ?>" class="small-text" /></label>
						<p><label><input type="checkbox" name="comic_thumbnail_crop" value="on"<?php if('on' == get_option('comic_thumbnail_crop')) echo ' checked="checked"'; ?> /> <?php _e('Crop thumbnail to exact dimensions (normally thumbnails are proportional)','webcomic') ?></label></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Medium Size','webcomic') ?></th>
					<td><label><?php _e('Max Width','webcomic') ?> <input type="text" name="comic_medium_size_w" value="<?php echo get_option('comic_medium_size_w') ?>" class="small-text" /></label> <label><?php _e('Max Height','webcomic') ?> <input type="text" name="comic_medium_size_h" value="<?php echo get_option('comic_medium_size_h') ?>" class="small-text" /></label></td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Large Size','webcomic') ?></th>
					<td><label><?php _e('Max Width','webcomic') ?> <input type="text" name="comic_large_size_w" value="<?php echo get_option('comic_large_size_w') ?>" class="small-text" /></label> <label><?php _e('Max Height','webcomic') ?> <input type="text" name="comic_large_size_h" value="<?php echo get_option('comic_large_size_h') ?>" class="small-text" /></label></td>
				</tr>
			</table>
			<h3><?php _e('How do you name your comics?','webcomic') ?></h3>
			<table class="form-table">
				<tr>
					<th scope="row"><label><input type="radio" name="comic_name_format" value="date"<?php if('date' == get_option('comic_name_format')) echo ' checked="checked"'; ?> /> <?php _e('Date','webcomic') ?></label></th>
					<td><input type="text" name="comic_name_format_date" value="<?php print get_option('comic_name_format_date'); ?>" class="small-text" /> <?php printf(__('Your comics must have the  <a href="%1$s">date</a> somewhere in the filename, in the format of','webcomic'), 'http://us2.php.net/date') ?> <code><?php echo date(get_option('comic_name_format_date')); ?></code>.</td>
				</tr>
				<tr>
					<th scope="row"><label><input type="radio" name="comic_name_format" value="slug"<?php if('slug' == get_option('comic_name_format')) echo ' checked="checked"'; ?> /> <?php _e('Title','webcomic') ?></label></th>
					<td><?php printf(__('Your comics must have the <a href="%1$s">post slug</a> somewhere in the filename.','webcomic'), 'http://lorelle.wordpress.com/2007/09/02/understanding-the-wordpress-post-title-and-post-slug/') ?></td>
				</tr>
				<tr>
					<th scope="row"><label><input type="radio" name="comic_name_format" value="meta"<?php if('meta' == get_option('comic_name_format')) echo ' checked="checked"'; ?> /> <?php _e('Custom','webcomic') ?></label></th>
					<td><?php printf(__('Your comics must have the value of a <a href="%1$s">custom field</a> called <code>comic_filename</code> somewhere in the filename.','webcomic'),'http://codex.wordpress.org/Using_Custom_Fields') ?></td>
				</tr>
				<tr>
					<th scope="row"><label><input type="checkbox" name="comic_secure_names" value="on"<?php if('on' == get_option('comic_secure_names')) print ' checked="checked"'; ?> /> <?php _e('Secure','webcomic') ?></label></th>
					<td><?php _e('Automatically append a secure hash to comic filenames during upload to prevent read ahead and archive scraping.','webcomic') ?></td>
				</tr>
			</table>
		<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes','webcomic') ?>" />
			<input type="hidden" name="action" value="webcomic_save_settings" />
		</p>
		</form>
	</div>
<?php } ?>
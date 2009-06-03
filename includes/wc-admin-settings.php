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
	
	if($_REQUEST['updated']):
		//Update Series based on new Comic Category selection
		$cats             = get_categories('hierarchical=0&hide_empty=0');
		$comic_cats       = get_comic_category('all');
		$current_chapters = get_comic_current_chapter('all');
		$comic_series     = array();
		
		//Push all the category ID's into an array
		foreach($cats as $cat)
			array_push($comic_series,$cat->term_id);
		
		//Create any new series, if necessary
		foreach($comic_cats as $comic_cat):
			$series_key = array_search($comic_cat,$comic_series);
			if(false !== $series_key):
				$new_series = get_term($comic_series[$series_key],'category');
				wp_insert_term($new_series->name,'chapter');
				$current_chapters[$new_series->term_id] = -1;
			endif;
			unset($comic_series[$series_key]);
		endforeach;
		
		//Delete any old series, if necessary
		if($comic_series):
			foreach($comic_series as $the_series):
				$s_parent   = get_the_chapter($the_series);
				$v_children = get_term_children($the_series,'chapter');
				
				foreach($v_children as $volume):
					$c_children = get_term_children($volume,'chapter');
					foreach($c_children as $chapter):
						wp_delete_term($chapter,'chapter');
					endforeach;
					wp_delete_term($volume,'chapter');
				endforeach;
				
				unset($current_chapters[$the_series]);
				wp_delete_term($the_series,'chapter');
			endforeach;
		endif;
			
		update_option('comic_current_chapter',$current_chapters);
		
		//Generate comic directories if they don't already exist
		if(!file_exists(get_comic_directory('abs')))
			mkdir(get_comic_directory('abs'),0775);
			
		if(1 < count(get_option('comic_category'))):
			foreach(get_option('comic_category') as $comic_category):
				if(!file_exists(get_comic_directory('abs',false,$comic_category)))
					mkdir(get_comic_directory('abs',false,$comic_category),0775);
				if(!file_exists(get_comic_directory('abs',true,$comic_category)))
					mkdir(get_comic_directory('abs',true,$comic_category),0775);
			endforeach;
		else:
			if(!file_exists(get_comic_directory('abs',true)))
				mkdir(get_comic_directory('abs',true),0775);
		endif;
		
		echo '<div id="message" class="updated fade"><p><strong>'.__('Settings saved.','webcomic').'</strong></p></div>';
	endif;
?>
	<div class="wrap">
		<div id="icon-webcomic" class="icon32"><img src="<?php echo plugins_url('webcomic/includes/webcomic.png') ?>" alt="icon" /></div>
		<h2><?php _e('Settings','webcomic'); ?></h2>
		<form method="post" action="options.php">
			<?php wp_nonce_field('update-options'); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="comic_category"><?php _e('Comic Category','webcomic') ?></label></th>
					<td>
						<select name="comic_category[]" id="comic_category" multiple="multiple" style="height: 7em;"> 
						<?php 
							$categories = get_categories('hierarchical=0&hide_empty=0'); 
							$comic_cats = get_comic_category('all');
							foreach ($categories as $the_category):
								$option = '<option value="'.$the_category->cat_ID.'"';
								foreach($comic_cats as $the_comic_cat):
									if($the_category->term_id == $the_comic_cat):
										$option .= ' selected="selected"';
										break 1;
									endif;
								endforeach;
								$option .= '>'.$the_category->cat_name.'</option>';
								echo $option;	
							endforeach;
						?>
						</select><br />
						<span class="setting-description"><?php _e('Select the category or categories that your comic posts are assigned to. Hold down <code>Ctrl</code> or <code>Command</code> to select multiple categories and activate multi-comic support.','webcomic') ?></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="comic_directory"><?php _e('Comic Directory','webcomic') ?></label></th>
					<td>
						<input type="text" name="comic_directory" id="comic_directory" value="<?php echo get_option('comic_directory'); ?>" class="code" /> <span class="setting-description"><?php _e('WebComic will look for your comics in','webcomic') ?> <a href="<?php echo get_comic_directory('url'); ?>"><?php echo get_comic_directory('url'); ?></a></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="comic_feed"><?php _e('Feeds','webcomic') ?></label></th>
					<td><input name="comic_feed" type="checkbox" id="comic_feed" value="1"<?php if(get_option('comic_feed')) print ' checked="checked"'; ?> />
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
					<td><label for="comic_auto_post"><input name="comic_auto_post" type="checkbox" id="comic_auto_post" value="1"<?php if(get_option('comic_auto_post')) print ' checked="checked"'; ?> /> <?php _e('Automatically create comic posts during upload','webcomic') ?></label></td>
				</tr>
				<tr>
					<th scope="row"><label for="comic_transcript_email"><?php _e('Transcripts','webcomic') ?></label></th>
					<td><input type="text" name="comic_transcript_email" id="comic_transcript_email" value="<?php echo get_option('comic_transcript_email'); ?>" class="regular-text code" /> <span class="setting-description"><?php _e('The e-mail address user submitted transcripts will be sent to.','webcomic') ?></span></td>
				</tr>
			</table>
			<h3><?php _e('Image Sizes','webcomic'); ?></h3>
			<p><?php _e('The sizes listed below determine the maximum dimensions in pixels to use when generating comic thumbnails during upload.','webcomic'); ?></p>
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e('Thumbnail Size','webcomic') ?></th>
					<td>
						<label><?php _e('Width','webcomic') ?> <input type="text" name="comic_thumbnail_size_w" value="<?php echo get_option('comic_thumbnail_size_w') ?>" class="small-text" /></label> <label><?php _e('Height','webcomic') ?> <input type="text" name="comic_thumbnail_size_h" value="<?php echo get_option('comic_thumbnail_size_h') ?>" class="small-text" /></label><br />
						<label><input type="checkbox" name="comic_thumbnail_crop" value="1"<?php if(get_option('comic_thumbnail_crop')) echo ' checked="checked"'; ?> /> <?php _e('Crop thumbnail to exact dimensions (normally thumbnails are proportional)','webcomic') ?></label>
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
					<th scope="row"><label><input type="checkbox" name="comic_secure_names" value="1"<?php if(get_option('comic_secure_names')) print ' checked="checked"'; ?> /> <?php _e('Secure','webcomic') ?></label></th>
					<td><?php _e('Automatically append a secure hash to comic filenames during upload to prevent read ahead and archive scraping.','webcomic') ?></td>
				</tr>
			</table>
		<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes','webcomic') ?>" />
			<input type="hidden" name="action" value="update" />
			<?php settings_fields('webcomic_options'); ?>
		</p>
		</form>
	</div>
<?php } ?>

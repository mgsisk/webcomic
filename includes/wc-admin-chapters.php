<?php
/**
 * This document contains all functions related to the Chapters page.
 * These functions were in wc-admin.php prior to version 1.4.
 * 
 * @package WebComic
 * @since 1.4
 */
 
function comic_page_chapters(){
	load_webcomic_domain();
	
	$the_series  = ($_REQUEST['series_view']) ? $_REQUEST['series_view'] : get_comic_category();
	$series_link = '&amp;series_view='.$the_series;
	$current_chapter  = get_comic_current_chapter($the_series);	
	$current_chapters = get_comic_current_chapter('all');
	
	/** Attempts to set the current chapter */
	if('set_current_chapter' == $_REQUEST['action']):
		check_admin_referer('set_current_chapter');
		
		foreach($current_chapters as $key => $value)
			if($key == $_REQUEST['series'])
				$current_chapters[$key] = $_REQUEST['chapter'];
		
		update_option('comic_current_chapter',$current_chapters);
	endif;
	
	/** Attempts to remove the current chapter */
	if('remove_current_chapter' == $_REQUEST['action']):
		check_admin_referer('remove_current_chapter');
		
		foreach($current_chapters as $key => $value)
			if($key == $_REQUEST['series'])
				$current_chapters[$key] = -1;
		
		update_option('comic_current_chapter',$current_chapters);
	endif;
	
	/** Attempts to create a new chapter. */
	if('create_new_chapter' == $_REQUEST['action']):
		check_admin_referer('create_new_chapter');
		
		$chapter_name = trim($_REQUEST['chapter_name']);
		$chapter_nicename = ($_REQUEST['chapter_nicename']) ? sanitize_title($_REQUEST['chapter_nicename']) : sanitize_title($_REQUEST['chapter_name']);
		$chapter_parent = ($_REQUEST['chapter_parent']) ? $_REQUEST['chapter_parent'] : $_REQUEST['volume_parent'];
		$chapter_description = $_REQUEST['chapter_description'];
		
		if(!$chapter_name):
			$error = 1;
		elseif(is_term($chapter_name,'chapter')):
			$error = 2;
		elseif($_REQUEST['chapter_nicename'] && is_term($chapter_nicename,'chapter')):
			$error = 3;
		else:
			wp_insert_term($chapter_name,'chapter',array('description' => $chapter_description, 'parent' => $chapter_parent, 'slug' => $chapter_nicename));
			echo '<div id="message" class="updated fade"><p>'.sprintf(__('Added new chapter "%s"','webcomic'),stripslashes($chapter_name)).'</p></div>';
		endif;
	endif;
	
	/** Attempts to delete the selected chapter. */
	if('delete_chapter' == $_REQUEST['action']):
		check_admin_referer('delete_chapter');
		$current_chapter  = get_comic_current_chapter($the_series);
		$current_chapters = get_comic_current_chapter('all');
		
		if($_REQUEST['volume']):
			$the_chapter = get_the_chapter((int) $_REQUEST['chapter']);
			$children = get_term_children($_REQUEST['chapter'],'chapter');
			
			foreach($children as $chapter):
				if($chapter == $current_chapter)
					$current_chapters[$the_series] = -1;
				wp_delete_term($chapter,'chapter');
			endforeach;
			
			update_option('comic_current_chapter',$current_chapters);
			wp_delete_term($_REQUEST['chapter'],'chapter');
			
			$extra = __(' and all the chapters it contained.','webcomic');
		else:
			if($_REQUEST['chapter'] == $current_chapter)
				$current_chapters[$the_series] = -1;
			$the_chapter = get_the_chapter((int) $_REQUEST['chapter']);
			update_option('comic_current_chapter',$current_chapters);
			wp_delete_term($_REQUEST['chapter'],'chapter');
		endif;
		
		echo '<div id="message" class="updated fade"><p>'.sprintf(__('Deleted "%s"','webcomic'),$the_chapter['title']).$extra.'</p></div>';
	endif;
	
	/** Attempts to modify the selected chapters. */
	if('modify_chapters' == $_REQUEST['action']):
		check_admin_referer('modify_chapters');
		
		$action = (isset($_REQUEST['Submit1'])) ? $_REQUEST['chapters_action1'] : $_REQUEST['chapters_action2'];
		$chapters = $_REQUEST['chapters'];
		if(!$chapters):
			echo '<div id="message" class="error"><p>'.__('Please select at least one chapter.','webcomic').'</p></div>';
		else:
			switch($action):
				case 'delete':
					foreach($chapters as $chapter):
						if($chapter == $current_chapter)
							$current_chapters[$the_series] = -1;
						wp_delete_term($chapter,'chapter');
					endforeach;
					
					update_option('comic_current_chapter',$current_chapters);
					
					echo '<div id="message" class="updated fade"><p>'.__('Chapters deleted.','webcomic').'</p></div>';
				break;
				default: echo '<div id="message" class="error"><p>'.__('Please select a valid action.','webcomic').'</p></div>';
			endswitch;
		endif;
	endif;
	
	/** Attempts to update the selected chapter. */
	if('update_chapter' == $_REQUEST['action']):
		check_admin_referer('update_chapter');
		
		$chapter_name = trim($_REQUEST['chapter_name']);
		$chapter_nicename = ($_REQUEST['chapter_nicename']) ? sanitize_title($_REQUEST['chapter_nicename']) : sanitize_title($_REQUEST['chapter_name']);
		$chapter_parent = ($_REQUEST['chapter_parent']) ? $_REQUEST['chapter_parent'] : $_REQUEST['volume_parent'];
		$chapter_parent = ($_REQUEST['chapter_id'] == $chapter_parent) ? 0 : $chapter_parent;
		$chapter_description = $_REQUEST['chapter_description'];
		$chapter_name_check = is_term($_REQUEST['chapter_name'],'chapter');
		$chapter_slug_check = ($_REQUEST['chapter_nicename']) ? is_term($_REQUEST['chapter_nicename'],'chapter') : '';
		
		if(!$chapter_name):
			$update_error = 1;
		elseif($chapter_name_check && $_REQUEST['chapter_id'] != $chapter_name_check['term_id']):
			$update_error = 2;
		elseif($chapter_slug_check && $_REQUEST['chapter_id'] != $chapter_slug_check['term_id']):
			$update_error = 3;
		endif;
		
		if(!$update_error && !is_wp_error(wp_update_term($_REQUEST['chapter_id'],'chapter',array('name' => $chapter_name, 'slug' => $chapter_nicename, 'parent' => $chapter_parent, 'description' => $chapter_description))))
			echo '<div id="message" class="updated fade"><p>'.sprintf(__('Updted chapter "%1$s"','webcomic'),$chapter_name).'</p></div>';
		elseif(1 == $update_error)
			echo '<div id="message" class="error"><p>'.__('A chapter name must be provided.','webcomic').'</p></div>';
		elseif(2 == $update_error)
			echo '<div id="message" class="error"><p>'.__('A chapter with that name already exists.','webcomic').'</p></div>';
		elseif(3 == $update_error)
			echo '<div id="message" class="error"><p>'.__('A chapter with that slug already exists.','webcomic').'</p></div>';
		else
			echo '<div id="message" class="error"><p>'.__('The chapter could not be updated.','webcomic').'</p></div>';
	endif;
	
	$current_chapter  = get_comic_current_chapter($the_series);
	$current_chapters = get_comic_current_chapter('all');
	$collection = get_the_collection('hide_empty=0');
?>
	<div class="wrap">
		<div id="icon-webcomic" class="icon32"><img src="<?php echo plugins_url('webcomic/includes/webcomic.png') ?>" alt="icon" /></div>
<?php if('edit_chapter' == $_REQUEST['action']): $the_chapter = get_term_to_edit($_REQUEST['chapter'],'chapter'); //Show the edit chapter form ?>
		<h2><?php _e('Edit Chapter','webcomic') ?></h2>
		<form action="admin.php?page=comic-chapters" method="post">
			<?php wp_nonce_field('update_chapter'); ?>
			<table class="form-table">
				<tr class="form-field">
					<th scope="row"><label for="chapter_name"><?php _e('Chapter Name','webcomic') ?></label></th>
					<td><input name="chapter_name" id="chapter_name" type="text" value="<?php echo $the_chapter->name ?>" /><br /><?php _e('The name is used to identify the chapter almost everywhere.','webcomic') ?></td>
				</tr>
				<tr class="form-field">
					<th scope="row"><label for="chapter_nicename"><?php _e('Chapter Slug','webcomic') ?></label></th>
					<td><input name="chapter_nicename" id="chapter_nicename" type="text" value="<?php echo $the_chapter->slug ?>" /><br /><?php _e('The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.','webcomic') ?></td>
				</tr>
				<?php if(!$_REQUEST['volume'] && !$_REQUEST['series']): ?>
				<tr class="form-field">
					<th scope="row"><label for="chapter_parent"><?php _e('Chapter Volume','webcomic') ?></label></th>
					<td>
							<select name="chapter_parent" id="chapter_parent">
								<option value="0"><?php _e('None','webcomic') ?></option>
							<?php
								foreach($collection as $series):
									if($series['id'] == $the_series):
										foreach($series['volumes'] as $volume):
											if($volume['id'] == $the_chapter->parent)
												echo '<option value="'.$volume['id'].'" selected="selected">'.$volume['title'].'</option>';
											else
												echo '<option value="'.$volume['id'].'">'.$volume['title'].'</option>';
										endforeach;
									endif;
								endforeach;
							?>
							</select><br /><?php _e('Select the volume that this chapter belongs to.','webcomic') ?>
					</td>
				</tr>
				<?php endif; ?>
				<tr class="form-field">
					<th scope="row"><label for="chapter_description"><?php _e('Descriptions','webcomic') ?></label><br /></th>
					<td><textarea name="chapter_description" id="chapter_description" rows="5" cols="40"><?php echo $the_chapter->description ?></textarea><br /><?php _e('Useful for providing a brief overview of the events covered in this chapter.','webcomic') ?></td>
				</tr>
			</table>
			<p class="submit"><input type="submit" class="button" name="submit" value="<?php _e('Edit Chapter','webcomic') ?>" /><input type="hidden" name="action" value="update_chapter" /><input type="hidden" name="chapter_id" value="<?php echo $_REQUEST['chapter'] ?>" /><input type="hidden" name="volume_parent" value="<?php echo $the_series; ?>" /><input type="hidden" name="series_view" value="<?php echo $the_series; ?>" /></p> 
		</form>
<?php else: ?>
		<h2><?php _e('Chapters','webcomic') ?></h2>
			<?php if(count(get_option('comic_category')) > 1): ?>
				<form action="admin.php?page=comic-chapters" method="post" class="search-form topmargin">
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
		<div id="col-right">
			<div class="col-wrap">
				<form action="admin.php?page=comic-chapters" method="post">
					<?php wp_nonce_field('modify_chapters'); ?>
					<div class="tablenav">
						<div class="alignleft actions">
							<select name="chapters_action1">
								<option value="-1"><?php _e('Action','webcomic') ?></option>
								<option value="delete"><?php _e('Delete','webcomic') ?></option>
							</select>
							<input type="submit" value="<?php _e('Apply','webcomic') ?>" name="Submit1" class="button-secondary action" />
						</div>
					</div>
					<table class="widefat">
						<thead>
							<tr>
								<th scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></th>
								<th scope="col" class="manage-column column-name"><?php _e('Name','webcomic') ?></th>
								<th scope="col" class="manage-column column-description"><?php _e('Description','webcomic') ?></th>
								<th scope="col" class="manage-column column-slug"><?php _e('Slug','webcomic') ?></th>
								<th scope="col" class="manage-column column-posts num"><?php _e('Pages','webcomic') ?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th scope="col" class="manage-column column-cb check-column"><input type="checkbox" /></th>
								<th scope="col" class="manage-column column-name"><?php _e('Name','webcomic') ?></th>
								<th scope="col" class="manage-column column-description"><?php _e('Description','webcomic') ?></th>
								<th scope="col" class="manage-column column-slug"><?php _e('Slug','webcomic') ?></th>
								<th scope="col" class="manage-column column-posts num"><?php _e('Pages','webcomic') ?></th>
							</tr>
						</tfoot>
						<tbody>
						<?php
							foreach($collection as $series):
								if($series['id'] == $the_series):
									if(!$series['volumes'])
										$using_chapters = true;
						?>
							<tr<?php if($i%2) echo' class="alt"'; ?>>
								<th scope="row" class="check-column"><input type="checkbox" name="chapters[]" value="<?php echo $volume['id'] ?>" /></th>
								<td><?php if($series['pages']): ?><a href="<?php echo $series['link'] ?>" title="<?php echo $series['title'] ?>"><strong><?php echo $series['title'] ?></strong></a><?php else: ?><strong><?php echo $series['title'] ?></strong><?php endif; ?><div class="row-actions"><a href="admin.php?page=comic-chapters<?php echo $series_link; ?>&amp;action=edit_chapter&amp;series=1&amp;chapter=<?php echo $series['id'] ?>"><?php _e('Edit','webcomic') ?></a></div></td>
								<td><?php echo $series['description'] ?></td>
								<td><?php echo $series['slug']; ?></td>
								<td class="num"><?php echo $series['pages'] ?></td>
							</tr>
						<?php
								foreach($series['volumes'] as $volume): $i++;
						?>
							<tr<?php if($i%2) echo' class="alt"'; ?>>
								<th scope="row" class="check-column"><input type="checkbox" name="chapters[]" value="<?php echo $volume['id'] ?>" /></th>
								<td><?php if($volume['pages']): ?><a href="<?php echo $volume['link'] ?>" title="<?php echo $volume['title'] ?>"><strong>&mdash; <?php echo $volume['title'] ?></strong></a><?php else: ?><strong>&mdash; <?php echo $volume['title'] ?></strong><?php endif; ?><div class="row-actions"><a href="admin.php?page=comic-chapters<?php echo $series_link; ?>&amp;action=edit_chapter&amp;volume=1&amp;chapter=<?php echo $volume['id'] ?>"><?php _e('Edit','webcomic') ?></a> | <span class="delete"><a href="<?php echo wp_nonce_url('admin.php?page=comic-chapters'.$series_link.'&amp;action=delete_chapter&amp;volume=1&amp;chapter='.$volume['id'],'delete_chapter') ?>" onclick="if(confirm('<?php echo js_escape(sprintf(__("You are about to delete '%s'. Any chapters in this volume will also be deleted.\n 'Cancel' to stop, 'OK' to delete.","webcomic"),$volume['title'])) ?>')) { return true;}return false;" title="<?php _e('Delete this volume','webcomic') ?>"><?php _e('Delete','webcomic') ?></a></span></div></td>
								<td><?php echo $volume['description'] ?></td>
								<td><?php echo $volume['slug']; ?></td>
								<td class="num"><?php echo $volume['pages'] ?></td>
							</tr>
							<?php foreach($volume['chapters'] as $chapter): $i++; ?>
							<tr<?php if($i%2) echo' class="alt"'; if($chapter['id'] == $current_chapter) echo ' style="background-color:#fdf9c6;"'?>
								<th scope="row" class="check-column"><input type="checkbox" name="chapters[]" value="<?php echo $chapter['id'] ?>" /></th>
								<td><?php if($chapter['pages']): ?><a href="<?php echo $chapter['link'] ?>" title="<?php echo $chapter['title'] ?>"><strong>&mdash; &mdash; <?php echo $chapter['title'] ?></strong></a><?php else: ?><strong>&mdash; &mdash; <?php echo $chapter['title'] ?></strong><?php endif; ?><div class="row-actions"><a href="admin.php?page=comic-chapters<?php echo $series_link; ?>&amp;action=edit_chapter&amp;chapter=<?php echo $chapter['id'] ?>"><?php _e('Edit','webcomic') ?></a><?php if($chapter['id'] != $current_chapter): ?> | <a href="<?php echo wp_nonce_url('admin.php?page=comic-chapters'.$series_link.'&amp;action=set_current_chapter&amp;series='.$the_series.'&amp;chapter='.$chapter['id'],'set_current_chapter') ?>" title="<?php _e("New posts will be assigned to this chapter",'webcomic') ?>"><?php _e('Make Current','webcomic'); ?></a><?php else: ?> | <a href="<?php echo wp_nonce_url('admin.php?page=comic-chapters'.$series_link.'&amp;action=remove_current_chapter&amp;series='.$the_series.'&amp;chapter='.$chapter['id'],'remove_current_chapter') ?>" title="<?php _e("New posts will not be assigned to a chapter",'webcomic') ?>"><?php _e('Remove Current','webcomic'); ?></a><?php endif; ?> | <span class="delete"><a href="<?php echo wp_nonce_url('admin.php?page=comic-chapters'.$series_link.'&amp;action=delete_chapter&amp;chapter='.$chapter['id'],'delete_chapter') ?>" onclick="if(confirm('<?php echo js_escape(sprintf(__("You are about to delete '%s'\n 'Cancel' to stop, 'OK' to delete.","webcomic"),$chapter['title'])) ?>')) { return true;}return false;" title="<?php _e('Delete this chapter','webcomic') ?>"><?php _e('Delete','webcomic') ?></a></span></div></td>
								<td><?php echo $chapter['description']; ?></td>
								<td><?php echo $chapter['slug']; ?></td>
								<td class="num"><?php echo $chapter['pages']; ?></td>
							</tr>
							<?php endforeach; ?>
						<?php $i++; endforeach; endif; endforeach; $i=0; ?>
						</tbody>
					</table>
					<div class="tablenav">
						<div class="alignleft actions">
							<select name="chapters_action2">
								<option value="-1"><?php _e('Action','webcomic') ?></option>
								<option value="delete"><?php _e('Delete','webcomic') ?></option>
							</select>
							<input type="submit" name="Submit2" class="button-secondary action" value="<?php _e('Apply','webcomic') ?>" />
							<input type="hidden" name="action" value="modify_chapters" />
							<input type="hidden" name="series_view" value="<?php echo $the_series; ?>" />
						</div>
					</div>
				</form>
				<?php if($using_chapters): ?>
				<h3><?php _e('Using Chapters','webcomic'); ?></h3>
				<p><?php _e('Chapters are a useful (but optional) way of categorizing your comics, similar to post categories. You can create new chapters or modify existing ones here and assign comics to them from the Comic Library or the Add/Edit Post pages.','webcomic'); ?></p>
				<p><?php _e("Above you'll see your first <em>series</em>. Each series corresponds to one of the categories you've selected for the Comic Category setting, and series are automatically created or destroyed (along with any volumes and chapters they contain) whenever the Comic Category setting is updated.",'webcomic'); ?></p>
				<p><?php _e("To start using chapters, you'll need to create two of them: the first will be a <em>volume</em> assigned to the current series, which can contain any number of regular chapters. You should assign the second chapter to your newly created volume using the Chapter Volume option.",'webcomic'); ?></p>
				<p><?php _e('From there, you can assign posts to any chapter from either the Add Post or Edit Post pages or the Comic Library. You can also set a chapter as the "current" chapter, ensuring that any new comic posts are automatically assigned to it.','webcomic') ?></p>
				<?php endif; ?>
			</div>
		</div>
		
		<div id="col-left">
			<div class="col-wrap">
				<div class="form-wrap">  
					<h3><?php _e('Add Chapter','webcomic') ?></h3>
					<?php if(2==$error) echo '<div style="background:#ffebe8; border: 1px solid #c00; margin: 5px 0; padding: 0 7px;"><p>'.__('The chapter you are trying to create already exists.','webcomic').'</p></div>'; ?>
					<form action="admin.php?page=comic-chapters" method="post">
						<?php wp_nonce_field('create_new_chapter');	?>
						<div class="form-field form-required"<?php if(1==$error || 2==$error) echo ' style="background:#ffebe8"'; ?>>
							<label for="chapter_name"><?php _e('Chapter Name','webcomic') ?></label>
							<input name="chapter_name" id="chapter_name" type="text" value="<?php if($error) echo $_REQUEST['chapter_name']; ?>" size="40"<?php if(1==$error || 2==$error) echo ' style="border-color:#c00"' ?> />
							<p><?php _e('The name is used to identify the chapter almost everywhere.','webcomic') ?></p>
						</div>
						<?php 
						if(3==$error) echo '<div style="background:#ffebe8; border: 1px solid #c00; margin: 5px 0; padding: 0 7px;"><p>'.__('A chapter with that slug already exists.','webcomic').'</p></div>'; ?>
						<div class="form-field"<?php if(3==$error) echo ' style="background:#ffebe8"'; ?>>
							<label for="chapter_nicename"><?php _e('Chapter Slug','webcomic'); ?></label>
							<input name="chapter_nicename" id="chapter_nicename" type="text" value="<?php if($error) echo $_REQUEST['chapter_nicename']; ?>" size="40" />
							<p><?php _e('The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.','webcomic'); ?></p>
						</div>
						<div class="form-field">
							<label for="chapter_parent"><?php _e('Chapter Volume','webcomic') ?></label>
							<select name="chapter_parent" id="chapter_parent">
								<option value="0"><?php _e('None','webcomic') ?></option>
							<?php
								foreach($collection as $series):
									if($series['id'] == $the_series):
										foreach($series['volumes'] as $volume)
											echo '<option value="'.$volume['id'].'">'.$volume['title'].'</option>';
									endif;
								endforeach;
							?>
							</select>
							<p><?php _e('Select <strong>None</strong> to turn this chapter into a new volume that you can assign other chapters to.','webcomic') ?></p>
						</div>
						<div class="form-field">
							<label for="chapter_description"><?php _e('Description','webcomic') ?></label>
							<textarea name="chapter_description" id="chapter_description" rows="5" cols="40"><?php if($error) echo $_REQUEST['chapter_description']; ?></textarea>
							<p><?php _e('Useful for providing a brief overview of the events covered in this chapter.','webcomic') ?></p>
						</div> 
						<p class="submit"><input type="submit" class="button" name="submit" value="<?php _e('Add Chapter','webcomic') ?>" /><input type="hidden" name="action" value="create_new_chapter" /><input type="hidden" name="volume_parent" value="<?php echo $the_series; ?>" /><input type="hidden" name="series_view" value="<?php echo $the_series; ?>" /></p> 
					</form>
				</div>
			</div>
		</div>
 <?php endif; ?>
	</div>
<?php } ?>
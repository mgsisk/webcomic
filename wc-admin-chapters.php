<?php
function comic_page_chapters(){
	load_webcomic_domain();
	
	if('create_new_chapter' == $_REQUEST['action']):
		check_admin_referer('create_new_chapter');
		
		$chapter_name = trim($_REQUEST['chapter_name']);
		$chapter_nicename = sanitize_title($_REQUEST['chapter_name']);
		$chapter_parent = $_REQUEST['chapter_parent'];
		$chapter_description = $_REQUEST['chapter_description'];
		
		if(!$chapter_name):
			$error = 1;
		elseif(is_term($chapter_name,'chapter')):
			$error = 2;
		else:
			wp_insert_term($chapter_name,'chapter',array('description' => $chapter_description, 'parent' => $chapter_parent, 'slug' => $chapter_nicename));
			echo '<div id="message" class="updated fade"><p>'.__('Added new chapter','webcomic').' <q>'.$chapter_name.'</q></strong></p></div>';
		endif;
	endif;
	
	
	
	if('delete_chapter' == $_REQUEST['action']):
		check_admin_referer('delete_chapter');
		
		$chapter = get_the_chapter($_REQUEST['chapter']);
		wp_delete_term($_REQUEST['chapter'],'chapter');
		echo '<div id="message" class="updated fade"><p>'.__('Deleted chapter','webcomic').' <q>'.$chapter['title'].'</q></p></div>';
	endif;
	
	
	
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
						if($chapter == get_comic_current_chapter())
							update_option('comic_current_chapter',-1);
						wp_delete_term($chapter,'chapter');
					endforeach;
					echo '<div id="message" class="updated fade"><p>'.__('Chapters deleted.','webcomic').'</p></div>';
				break;
				default: echo '<div id="message" class="error"><p>'.__('Please select a valid action.','webcomic').'</p></div>';
			endswitch;
		endif;
	endif;
	
	
	
	if('update_chapter' == $_REQUEST['action']):
		check_admin_referer('update_chapter');
		
		$chapter_name = trim($_REQUEST['chapter_name']);
		$chapter_nicename = sanitize_title($_REQUEST['chapter_name']);
		$chapter_parent = $_REQUEST['chapter_parent'];
		$chapter_description = $_REQUEST['chapter_description'];
		
		if(!is_wp_error(wp_update_term($_REQUEST['chapter_id'],'chapter',array('name' => $chapter_name, 'slug' => $chapter_nicename, 'parent' => $chapter_parent, 'description' => $chapter_description))))
			echo '<div id="message" class="updated fade"><p>'.__('Updted chapter','webcomic').' <q>'.$chapter_name.'</q></p></div>';
		else
			echo '<div id="message" class="error"><p>'.__('The chapter could not be updated.','webcomic').'</p></div>';
	endif;
?>
	<div class="wrap">
<?php if('edit_chapter' == $_REQUEST['action']): $the_chapter = get_term_to_edit($_REQUEST['chapter'],'chapter'); //Show the edit chapter form ?>
		<h2><?php _e('Edit Chapter','webcomic') ?></h2>
		<form action="" method="post">
			<?php wp_nonce_field('update_chapter'); ?>
			<table class="form-table">
				<tr class="form-field">
					<th scope="row"><label for="chapter_name"><?php _e('Chapter Name','webcomic') ?></label></th>
					<td><input name="chapter_name" id="chapter_name" type="text" value="<?php echo $the_chapter->name ?>" <?php if($error) echo ' style="border-color:#c00"' ?> /><br /><?php _e('The name is used to identify the chapter almost everywhere.','webcomic') ?></td>
				</tr>
				<?php if($the_chapter->parent != 0): ?>
				<tr class="form-field">
					<th scope="row"><label for="chapter_parent"><?php _e('Chapter Volume','webcomic') ?></label></th>
					<td>
							<select name="chapter_parent" id="chapter_parent">
							<?php
								$collection = get_the_collection(false);
								foreach($collection as $vid => $volume):
									if($vid == $the_chapter->parent)
										$the_parent = ' selected="selected"';
									else
										unset($the_parent);
									echo '<option value="'.$vid.'"'.$the_parent.'>'.$volume['title'].'</option>';
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
			<p class="submit"><input type="submit" class="button" name="submit" value="<?php _e('Edit Chapter','webcomic') ?>" /><input type="hidden" name="action" value="update_chapter" /><input type="hidden" name="chapter_id" value="<?php echo $_REQUEST['chapter'] ?>" /></p> 
		</form>
<?php else: ?>
		<h2><?php _e('Comic Chapters','webcomic') ?></h2>
		<div id="col-right">
			<div class="col-wrap">
			<?php if(!get_the_collection(false)): ?>
				<p><?php _e('Chapters are a useful (but optional) way of categorizing your comics, similar to post categories. You can create new chapters or modify existing ones here and assign comics to them from the Comic Library.','webcomic') ?></p>
				<p><?php _e("To start using chapters, you'll need to create two of them: the first will be a <em>volume</em>, which can contain any number of regular chapters. You should assign the second chapter to your newly created volume.",'webcomic') ?></p>
				<p><?php _e("Don't forget to update the <strong>Current Chapter</strong> setting, which will assign any new comic posts to the chapter you select.",'webcomic') ?></p>
			<?php else: ?>
				<form action="" method="post">
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
						<colgroup>
							<col />
							<col style="width:19%" />
							<col />
							<col style="width:1%" />
						</colgroup>
						<thead>
							<tr>
								<th scope="col" id="cb" class="check-column"><input type="checkbox" /></th>
								<th scope="col"><?php _e('Name','webcomi') ?></th>
								<th scope="col"><?php _e('Description','webcomi') ?></th>
								<th scope="col"><?php _e('Pages','webcomi') ?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th scope="col" id="cb" class="check-column"><input type="checkbox" /></th>
								<th scope="col"><?php _e('Name','webcomi') ?></th>
								<th scope="col"><?php _e('Description','webcomi') ?></th>
								<th scope="col"><?php _e('Pages','webcomi') ?></th>
							</tr>
						</tfoot>
						<tbody>
						<?php
							$collection = get_the_collection(false);
							foreach($collection as $vid => $volume):
						?>
							<tr<?php if($i%2) echo' class="alt"'; ?>>
								<th scope="row" class="check-column"><input type="checkbox" name="chapters[]" value="<?php echo $vid ?>" /></th>
								<td><a href="<?php echo $volume['link'] ?>" title="Go to the beginning of <?php echo $volume['title'] ?>"><strong><?php echo $volume['title'] ?></strong></a><div class="row-actions"><a href="admin.php?page=comic-chapters&amp;action=edit_chapter&amp;chapter=<?php echo $vid ?>"><?php _e('Edit','webcomic') ?></a> | <a href="<?php echo wp_nonce_url('admin.php?page=comic-chapters&amp;action=delete_chapter&amp;chapter='.$vid,'delete_chapter') ?>"><?php _e('Delete','webcomic') ?></a></div></td>
								<td><?php echo $volume['description'] ?></td>
								<td class="num"><?php echo $volume['pages'] ?></td>
							</tr>
							<?php foreach($volume['chapters'] as $cid => $chapter): $i++; ?>
							<tr<?php if($i%2) echo' class="alt"'; ?>>
								<th scope="row" class="check-column"><input type="checkbox" name="chapters[]" value="<?php echo $cid ?>" /></th>
								<td><a href="<?php echo $chapter['link'] ?>" title="Go to the beginning of <?php echo $chapter['title'] ?>"><strong>&mdash; <?php echo $chapter['title'] ?></strong></a><div class="row-actions"><a href="admin.php?page=comic-chapters&amp;action=edit_chapter&amp;chapter=<?php echo $cid ?>"><?php _e('Edit','webcomic') ?></a> | <a href="<?php echo wp_nonce_url('admin.php?page=comic-chapters&amp;action=delete_chapter&amp;chapter='.$cid,'delete_chapter') ?>"><?php _e('Delete','webcomic') ?></a></div></td>
								<td><?php echo $chapter['description'] ?></td>
								<td class="num"><?php echo $chapter['pages'] ?></td>
							</tr>
							<?php endforeach; ?>
						<?php $i++; endforeach; $i=0; ?>
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
						</div>
					</div>
				</form>
			<?php endif; ?>
			</div>
		</div>
		
		<div id="col-left">
			<div class="col-wrap">
				<div class="form-wrap">  
					<h3><?php _e('Add Chapter','webcomic') ?></h3>
					<form action="" method="post">
						<?php wp_nonce_field('create_new_chapter'); ?>
						<?php if($error>1) echo '<div id="message" class="error"><p>'.__('The chapter you are trying to create already exists.','webcomic').'</p></div>' ?>
						<div class="form-field form-required"<?php if($error) echo ' style="background:#ffebe8"' ?>> 
							<label for="chapter_name"><?php _e('Chapter Name','webcomic') ?></label> 
							<input name="chapter_name" id="chapter_name" type="text" value="" size="40"<?php if($error) echo ' style="border-color:#c00"' ?> /> 
							<p><?php _e('The name is used to identify the chapter almost everywhere.','webcomic') ?></p> 
						</div>
						<div class="form-field">
							<label for="chapter_parent"><?php _e('Chapter Volume','webcomic') ?></label> 
							<select name="chapter_parent" id="chapter_parent"> 
								<option value="0"><?php _e('None','webcomic') ?></option>
							<?php
								$collection = get_the_collection(false);
								foreach($collection as $vid => $volume)
									echo '<option value="'.$vid.'">'.$volume['title'].'</option>';
							?>
							</select> 
							<p><?php _e('Select <strong>None</strong> to turn this chapter into a new volume that you can add other chapters to.','webcomic') ?></p> 
						</div>
						<div class="form-field"> 
							<label for="chapter_description"><?php _e('Description','webcomic') ?></label> 
							<textarea name="chapter_description" id="chapter_description" rows="5" cols="40"></textarea> 
							<p><?php _e('Useful for providing a brief overview of the events covered in this chapter.','webcomic') ?></p> 
						</div> 
						<p class="submit"><input type="submit" class="button" name="submit" value="<?php _e('Add Chapter','webcomic') ?>" /><input type="hidden" name="action" value="create_new_chapter" /></p> 
					</form>
				</div>
			</div>
		</div>
 <?php endif; ?>
	</div>
<?php } ?>
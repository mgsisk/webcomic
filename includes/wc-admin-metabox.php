<?php
/**
 * This document contains all of the functions related to the WebComic Metabox.
 * 
 * @package WebComic
 * @since 1.6
 */

function comic_meta_box($post){	
	load_webcomic_domain();
	
	$post_chapters = get_post_chapters($post->ID);
	$the_series    = get_post_comic_category($post->ID);
	$collection    = get_the_collection('hide_empty=0');
	?>
	<script type="text/javascript">jQuery('form#post').attr('enctype','multipart/form-data').attr('encoding','multipart/form-data')</script>
	<?php if(get_post_comic_category($post->ID) && get_the_comic()): $comic = get_the_comic(); ?>
		<p class="alignright"><a href="<?php echo $comic['link'] ?>" title="<?php echo $comic['description'] ?>"><img src="<?php echo $comic['thumb'] ?>" alt="<?php echo $comic['title'] ?>" /></a></p>
	<?php elseif(get_post_comic_category($post->ID) && !get_the_comic()): ?>
		<p class="alignright"><strong class="error"><?php _e('WebComic could not match this post with a comic.','webcomic') ?></strong></p>
	<?php endif ?>
	<p>
		<input type="file" name="new_comic_file" id="new_comic_file" />
		<input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
		<input type="hidden" name="webcomic_upload_status" value="0" />
		<?php if(current_user_can('edit_others_posts')): ?><label title="<?php _e('Overwrite an existing file with the same name','webcomic') ?>"><input type="checkbox" name="new_comic_overwrite" id="new_comic_overwrite" value="1" /> <?php _e('Overwrite','webcomic') ?></label><?php endif ?><br />
	</p><br />
	<?php if('meta' == get_option('comic_name_format')): ?>
	<p>
		<label for="comic_filename"><strong><?php _e('Filename','webcomic') ?></strong></label><br />
		<input type="text" name="comic_filename" id="comic_filename" style="width:25%" value="<?php echo get_post_meta($post->ID,'comic_filename',true) ?>" />
	</p>
	<p><?php _e('If you are uploading a comic and leave this blank it will automatically be set to the name of the uploaded comic file.','webcomic') ?></p><br />
	<?php endif ?>
	<?php if($post_chapters && current_user_can('manage_categories')): ?>
	<p>
		<label for="comic_chapter"><strong><?php _e('Chapter','webcomic') ?></strong></label><br />
		<?php _e('','webcomic') ?>
		<select name="comic_chapter" id="comic_chapter" style="vertical-align:middle">
			<option value="-1"><?php _e('N\A','webcomic') ?></option>
		<?php
			foreach($collection as $series):
				if($series['id'] == $the_series):
					foreach($series['volumes'] as $volume):
		?>
			<optgroup label="<?php echo $volume['title'] ?>">
			<?php foreach($volume['chapters'] as $chapter): ?>
				<option value="<?php echo $chapter['id'] ?>"<?php if($post_chapters['chapter']->term_id == $chapter['id']) echo ' selected="selected"'; ?>><?php echo $chapter['title'] ?></option>
				<?php endforeach; ?>
			</optgroup>
		<?php endforeach; endif; endforeach; ?>
		</select>
	</p>
	<?php endif ?>
	<?php if(get_the_chapter()): ?><p><?php _e('This comic currently belongs to ','webcomic') ?><?php the_chapter_link() ?> &laquo; <?php the_chapter_link('volume') ?></p><br /><?php else: ?><br /><?php endif ?>
	<p style="clear:both"><label for="comic_description"><strong><?php _e('Description','webcomic') ?></strong></label> <input type="text" name="comic_description" id="comic_description" style="width:99%" value="<?php echo get_post_meta($post->ID,'comic_description',true) ?>" /></p>
	<p><?php _e('If provided, the comic description will replace the post title as the hover text for links to and images of the comic.','webcomic') ?></p><br />
	<p><label for="comic_transcript"><strong><?php _e('Transcript','webcomic') ?></strong></label><textarea rows="2" cols="40" name="comic_transcript" id="comic_transcript" style="margin:0;height:10em;width:98%"><?php echo get_post_meta($post->ID,'comic_transcript',true) ?></textarea></p>
	<p><?php printf(__('Transcripts can be formatted using HTML or <a href="%1$s">Markdown</a> <a href="%2$s">Extra</a>.','webcomic'),'http://daringfireball.net/projects/markdown/syntax','http://michelf.com/projects/php-markdown/extra/') ?></p>
	<?php
}

function comic_meta_box_save($id,$post){
	
	/** Attempt to upload the selected comic file and generate comic thumbnails. */
	if($_FILES['new_comic_file']):
		$true_id   = ($post->ID == $post->post_parent) ? $post->ID : $post->post_parent;
		$comic_dir = (1 < count(get_option('comic_category'))) ? get_post_comic_category($true_id) : false;
		$ext       = strtolower(end(explode('.',basename($_FILES['new_comic_file']['name']))));
		
		switch($ext):
			case 'gif': break;
			case 'jpg': break;
			case 'jpeg': break;
			case 'png': break;
			default: $invalid_format = true;
		endswitch;
		
		if(!$invalid_format):
			$ext = '.'.$ext;
			$file = basename($_FILES['new_comic_file']['name'], $ext);
			
			if(get_option('comic_secure_names'))
				$hash = '-'.md5(microtime().basename($_FILES['new_comic_file']['name']));
			
			$target_path = get_comic_directory('abs',false,$comic_dir).$file.$hash.$ext;
			
			if((!is_file($target_path) || $_REQUEST['new_comic_overwrite']) && move_uploaded_file($_FILES['new_comic_file']['tmp_name'],$target_path)):
				if(strpos(PHP_OS,'WIN'))
					chmod($target_path,0777);
				else
					chmod($target_path,0664);
				
				$img_dim = getimagesize($target_path);
				$img_crop = get_option('comic_thumbnail_crop') ? true : false;
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
				
				if('meta' == get_option('comic_name_format'))
					add_post_meta($id,'comic_filename',$file);
			endif;
		endif;
	endif;
	
	/** Attempt to update the chapter information for the selected comics. */
	if($_REQUEST['comic_chapter'])
			add_post_to_chapter($id,$_REQUEST['comic_chapter']);
	
	/** Attempt to update the comic filename. */
	if($_REQUEST['comic_filename']):
		if(false !== get_post_meta($id,'comic_filename',true)):
			update_post_meta($id,'comic_filename',$_REQUEST['comic_filename']);
		else:
			add_post_meta($id,'comic_filename',$_REQUEST['comic_filename']);
		endif;
	endif;
	
	/** Attempt to update the comic description. */
	if($_REQUEST['comic_description']):
		if(false !== get_post_meta($id,'comic_description',true)):
			update_post_meta($id,'comic_description',$_REQUEST['comic_description']);
		else:
			add_post_meta($id,'comic_description',$_REQUEST['comic_description']);
		endif;
	endif;
	
	/** Attempt to update the comic transcript. */
	if($_REQUEST['comic_transcript']):
		if(false !== get_post_meta($id,'comic_transcript',true)):
			update_post_meta($id,'comic_transcript',$_REQUEST['comic_transcript']);
		else:
			add_post_meta($id,'comic_transcript',$_REQUEST['comic_transcript']);
		endif;
	endif;
}
add_action('save_post', 'comic_meta_box_save',10,2);
?>

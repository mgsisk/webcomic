<?php
function comic_meta_box(){	
	load_webcomic_domain();
	
	global $post;
	?>
	<script type="text/javascript">jQuery('form#post').attr('enctype','multipart/form-data').attr('encoding','multipart/form-data')</script>
		<?php if(in_category(get_comic_category()) && get_the_comic()): ?>
			<p class="alignright"><?php echo get_the_comic(false,'full','thumb') ?></p>
		<?php elseif(in_category(get_comic_category()) && !get_the_comic()): ?>
			<p class="alignright"><strong class="error"><?php _e('WebComic could not match this post with a comic.','webcomic') ?></strong></p>
		<?php endif ?>
	<p>
		<input type="file" name="new_comic_file" id="new_comic_file" />
		<input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
		<input type="hidden" name="webcomic_upload_status" value="0" />
		<?php if(current_user_can('edit_others_posts')): ?><label title="<?php _e('Overwrite an existing file with the same name','webcomic') ?>"><input type="checkbox" name="new_comic_overwrite" id="new_comic_overwrite" value="1" /> <?php _e('Overwrite','webcomic') ?></label><?php endif ?>
	</p>
	<?php if('meta' == get_option('comic_name_format')): ?>
	<p>
		<label for="comic_filename"><strong><?php _e('Filename','webcomic') ?></strong></label><br />
		<input type="text" name="comic_filename" id="comic_filename" style="width:25%" value="<?php echo get_post_meta($post->ID,'comic_filename',true) ?>" />
	</p>
	<p><?php _e('If you are uploading a comic WebComic will automatically set this value using the name of the uploaded file.','webcomic') ?></p><br />
	<?php endif ?>
	<p style="clear:both"><label for="comic_description"><strong><?php _e('Description','webcomic') ?></strong></label> <input type="text" name="comic_description" id="comic_description" style="width:99%" value="<?php echo get_post_meta($post->ID,'comic_description',true) ?>" /></p>
	<p><?php _e('If provided, the comic description will replace the post title as the hover text for links to and images of the comic.','webcomic') ?></p><br />
	<p><label for="comic_transcript"><strong><?php _e('Transcript','webcomic') ?></strong></label><textarea rows="2" cols="40" name="comic_transcript" id="comic_transcript" style="margin:0;height:10em;width:98%"><?php echo get_post_meta($post->ID,'comic_transcript',true) ?></textarea></p>
	<p><?php printf(__('Transcripts can be formatted using HTML or <a href="%1$s">Markdown</a> <a href="%2$s">Extra</a>.','webcomic'),'http://daringfireball.net/projects/markdown/syntax','http://michelf.com/projects/php-markdown/extra/') ?></p>
	<?php
}
add_meta_box('webcomic', __('WebComic','webcomic'), 'comic_meta_box', 'post', 'normal', 'high');

function comic_meta_box_save($id){	
	if($_FILES['new_comic_file']):		
		$ext = strtolower(end(explode('.',basename($_FILES['new_comic_file']['name']))));
		
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
			
			if('on' == get_option('comic_secure_names'))
				$hash = '-'.md5(microtime().basename($_FILES['new_comic_file']['name']));
			
			$target_path = ABSPATH.get_comic_directory().$file.$hash.$ext;
			
			if((!is_file($target_path) || $_REQUEST['new_comic_overwrite']) && move_uploaded_file($_FILES['new_comic_file']['tmp_name'],$target_path)):
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
				
				if('meta' == get_option('comic_name_format'))
					add_post_meta($id,'comic_filename',$file);
			endif;
		endif;
	endif;
	
	if($_REQUEST['comic_filename']):
		if(false !== get_post_meta($id,'comic_filename',true)):
			update_post_meta($id,'comic_filename',$_REQUEST['comic_filename']);
		else:
			add_post_meta($id,'comic_filename',$_REQUEST['comic_filename']);
		endif;
	endif;
	
	if($_REQUEST['comic_description']):
		if(false !== get_post_meta($id,'comic_description',true)):
			update_post_meta($id,'comic_description',$_REQUEST['comic_description']);
		else:
			add_post_meta($id,'comic_description',$_REQUEST['comic_description']);
		endif;
	endif;
	
	if($_REQUEST['comic_transcript']):
		if(false !== get_post_meta($id,'comic_transcript',true)):
			update_post_meta($id,'comic_transcript',$_REQUEST['comic_transcript']);
		else:
			add_post_meta($id,'comic_transcript',$_REQUEST['comic_transcript']);
		endif;
	endif;
}
add_action('save_post', 'comic_meta_box_save');
?>
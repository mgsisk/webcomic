<?php
/**
 * Contains all of the functions related to the Webcomic Metaboxes.
 * 
 * @package Webcomic
 * @since 1.6.0
 */

function comic_post_meta_box( $post ) {
	load_webcomic_domain();
	
	$series        = get_post_comic_category();
	$file_path     = get_comic_directory( 'abs', false, $series );
	$post_chapters = get_post_comic_chapters();
	$collection    = get_the_collection( 'hide_empty=0&depth=3&series=' . $series );
	?>
	<script type="text/javascript">jQuery('form#post').attr('enctype','multipart/form-data').attr('encoding','multipart/form-data')</script>
	
	<?php if ( $series && get_the_comic()->file ) { $comic = get_the_comic(); if ( $comic->fallback ) { ?>
		<p class="updated"><?php _e( 'Files are being matched with this post using the fallback method.', 'webcomic' ) ?></p>
	<?php } ?>
		<div class="alignright"><p><?php the_comic( 'thumb' ); ?></p><a href="<?php echo $comic->link; ?>" title="<?php echo $comic->description; ?>"><?php _e( 'View', 'webcomic' ); ?></a></div>
	<?php } elseif ( $series && !get_the_comic()->file ) { ?>
		<p class="error"><?php _e( 'This post is not linked to and cannot be matched with a file.', 'webcomic' ); ?></p>
		<?php
			//Get all the comic files
			$comic_files = glob( $file_path . '*.*' );
			
			if ( !is_array( $comic_files ) )
				$comic_files = arrar();
			
			//Get just the comic files associated with a post
			$comics      = comic_loop( -1, $series );
			$comic_posts = array();
			
			while ( $comics->have_posts() ) : $comics->the_post();
				$the_comic = get_the_comic();
				
				if ( $the_comic->file )
					array_push( $comic_posts, $file_path . $the_comic->file_name );
			endwhile;
			
			//Compare our lists to see if there are any orphaned files
			$comic_orphans = array_diff( $comic_files, $comic_posts );
			natsort( $comic_orphans );
			
			if ( $comic_orphans ) {
		?>
		<p><?php _e( 'Select an orphan or upload a new file below:', 'webcomic' ); ?></p>
		<div style="height:<?php echo get_option( 'comic_thumb_size_h' ); ?>px;overflow:auto">
			<?php
				foreach ( $comic_orphans as $orphan ) {
					$orphan_info = pathinfo( $orphan );
					
					if ( !$orphan_info[ 'filename' ] )
						$orphan_info[ 'filename' ] = substr( $orphan_info[ 'basename' ], 0, strrpos( $orphan_info[ 'basename' ], '.' ) );
					
					if ( 'swf' == $orphan_info[ 'extension' ] ) {
						$preview = '<object type="application/x-shockwave-flash" data="' . get_comic_directory( 'url', false, $series ) . $orphan_info[ 'basename' ] . '" height="' . get_option( 'comic_thumb_size_h' ) . '" width="' . get_option( 'comic_thumb_size_w' ) . '"><param name="movie" value="' . get_comic_directory( 'url', false, $series ) . $orphan_info[ 'basename' ] . '" /></object>';
					} else {
						$img     = ( is_file( get_comic_directory( 'abs', true, $series ) . $orphan_info[ 'filename' ] . '-thumb.' . $orphan_info[ 'extension' ] ) ) ? get_comic_directory( 'url', true, $series ) . $orphan_info[ 'filename' ] . '-thumb.' . $orphan_info[ 'extension' ] : get_comic_directory( 'url', false, $series ) . $orphan_info[ 'filename' ] . '.' . $orphan_info[ 'extension' ];
						$preview = '<img src="' . $img . '" alt="' . $orphan_info[ 'basename' ] . '" title="' . $orphan_info[ 'basename' ] . '" style="vertical-align:top" />';
					}
					
					echo '<label style="float:left;margin:0 1em 1em 0"><input type="radio" name="comic_orphan" value="' . $orphan_info[ 'basename' ] . '" /> ' . $preview . '</label>';
				}
			?>
		</div>
	<?php } } ?>
		<p>
			<input type="file" name="new_comic_file" id="new_comic_file" />
			<input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
			<input type="hidden" name="webcomic_upload_status" value="0" />
			<?php if ( current_user_can( 'edit_others_posts' ) ) { ?><label title="<?php _e( 'Overwrite an existing file with the same name', 'webcomic' ); ?>"><input type="checkbox" name="new_comic_overwrite" id="new_comic_overwrite" value="1" /> <?php _e( 'Overwrite', 'webcomic' ); ?></label><?php } ?>
		</p>
		<p><?php if ( $comic && !$comic->fallback ) _e( 'Any files currently associated with this post will be deleted when uploading a new file.', 'webcomic' ); ?></p>
		<br />
	<?php if ( $comic && !$comic->fallback ) { $file = pathinfo( get_comic_directory( 'abs', false, $series ) . $comic->file_name ); ?>
		<p style="width:50%">
			<label for="comic_file"><strong><?php _e( 'File', 'webcomic' ); ?></strong></label><br />
			<input type="text" name="comic_file" id="comic_file" style="width:60%" value="<?php echo substr( $file[ 'basename' ], 0, strrpos( $file[ 'basename' ], '.' ) ); ?>" />
			<input type="text" name="comic_ext" id="comic_ext" style="background:#eee;color:#999;width:3.25em" readonly="readonly" value="<?php echo '.' . $file[ 'extension' ]; ?>" />
			<label title="<?php _e( 'Delete this comic', 'webcomic' ); ?>"><input type="checkbox" name="comic_file_delete" id="comic_file_delete" value="1" /> <?php _e( 'Delete', 'webcomic' ); ?></label>
		</p>
		<?php if ( !$comic->flash ) { ?>
		<p>
		<?php
			if ( $comic->large )
				_e( 'Large, Medium, and Thumbnail sizes available.', 'webcomic' );
			elseif ( $comic->medium )
				_e( 'Medium and Thumbnail sizes available.', 'webcomic' );
			elseif ( $comic->thumb )
				_e( 'Thumbnail size available.', 'webcomic' );
			else
				_e( 'No additional sizes available.', 'webcomic' );
			?>
		</p>
	<?php } ?>
		<br />
	<?php } if ( $series && count( get_object_vars( $collection->$series->volumes ) ) && current_user_can( 'manage_categories' ) ) { ?>
		<p>
			<label for="comic_chapter"><strong><?php _e( 'Chapter', 'webcomic' ); ?></strong></label><br />
			<select name="comic_chapter" id="comic_chapter">
				<option value="-1"><?php _e( 'N\A', 'webcomic' ); ?></option>
			<?php foreach ( array_keys( get_object_vars( $collection->$series->volumes ) ) as $volume ) { ?>
				<optgroup label="<?php echo $collection->$series->volumes->$volume->title ?>">
				<?php foreach ( array_keys( get_object_vars( $collection->$series->volumes->$volume->chapters ) ) as $chapter ) { ?>
					<option value="<?php echo $collection->$series->volumes->$volume->chapters->$chapter->ID ?>"<?php if ( $post_chapters->chapter->term_id == $collection->$series->volumes->$volume->chapters->$chapter->ID ) echo ' selected="selected"'; ?>><?php echo $collection->$series->volumes->$volume->chapters->$chapter->title; ?></option>
					<?php } ?>
				</optgroup>
			<?php } ?>
			</select>
		</p>
	<?php if ( $post_chapters ) { ?><p><?php _e( 'This comic currently belongs to ','webcomic' ); the_chapter_link(); ?> &laquo; <?php the_chapter_link( 'volume' ); ?></p><br /><?php } } ?>
	<p style="clear:both">
		<label for="comic_description"><strong><?php _e( 'Description', 'webcomic' ); ?></strong></label>
	<?php if ( get_post_meta( $post->ID, 'comic_description', true ) ) { ?>
		<label title="<?php _e( 'Delete this description', 'webcomic' ); ?>" style="float:right;"><input type="checkbox" name="comic_description_delete" id="comic_description_delete" value="1" /> <?php _e( 'Delete', 'webcomic' ); ?></label>
	<?php } ?>
		<input type="text" name="comic_description" id="comic_description" style="width:99%" value="<?php echo get_post_meta( $post->ID, 'comic_description', true ); ?>" />
	</p>
	<p><?php _e( 'If provided, the comic description will replace the post title as the hover text for links to and images of the comic.', 'webcomic' ); ?></p><br />
	<p>
		<span style="float:right"><label for="comic_transcript_action"><strong><?php _e( 'Action', 'webcomic' ); ?>&emsp;</strong></label>
			<select name="comic_transcript_action" id="comic_transcript_action" style="vertical-align:middle;">
				<option value="publish"<?php if ( 'publish' == $comic->transcript_status ) echo 'selected="selected"'; ?>><?php _e( 'Publish', 'webcomic' ); ?></option>
				<option value="pending"<?php if ( 'pending' == $comic->transcript_status ) echo 'selected="selected"'; ?>><?php _e( 'Request Improvement', 'webcomic' ); ?></option>
				<option value="draft"<?php if ( 'draft' == $comic->transcript_status ) echo 'selected="selected"'; ?>><?php _e( 'Save as Draft', 'webcomic' ); ?></option>
			<?php if ( get_post_meta( $post->ID, 'comic_transcript_backup', true ) ) { ?>
				<option value="restore"><?php _e( 'Restore Previous', 'webcomic' ); ?></option>
			<?php } if ( $comic->transcript ) { ?>
				<option value="delete"><?php _e( 'Delete', 'webcomic' ); ?></option>
			<?php } ?>
			</select>
		</span>
		<label><strong><?php _e( 'Transcript', 'webcomic' ); ?></strong>
			<textarea rows="7" cols="25" name="comic_transcript" id="comic_transcript" style="width:100%"><?php
				if ( 'publish' == $comic->transcript_status )
					echo get_post_meta( $post->ID, 'comic_transcript', true );
				elseif ( 'pending' == $comic->transcript_status )
					echo get_post_meta( $post->ID, 'comic_transcript_pending', true );
				elseif ( 'draft' == $comic->transcript_status )
					echo get_post_meta( $post->ID, 'comic_transcript_draft', true );
			?></textarea>
		</label>
		<input type="hidden" name="comic_transcript_type" value="<?php echo $comic->transcript_status; ?>" />
	</p>
	<?php
}

function comic_post_meta_box_save( $id, $post ) {
	if ( !$_REQUEST[ 'original_publish' ] )
		return; //User did not manually update the post
	
	//Make sure we're working with the post, not a revisions
	if ( $_post = wp_is_post_revision( $id ) )
		$id = $_post;
	
	$series     = get_post_comic_category( $id );
	$file_path  = get_comic_directory( 'abs', false, $series );
	$thumb_path = get_comic_directory( 'abs', true, $series );
	
	/** Attempt to upload the selected comic file and generate comic thumbnails if necessary */
	if ( 0 === $_FILES[ 'new_comic_file' ][ 'error' ] ) {
		$file = pathinfo( $_FILES[ 'new_comic_file' ][ 'name' ] );
		
		//Validate the file format. Files must be gif, jpg, jpeg, png, or swf (bulk upload not supported for metabox uploads).
		switch ( strtolower( $file[ 'extension' ] ) ) {
			case 'gif':
			case 'jpg':
			case 'jpeg':
			case 'png':
			case 'swf': break;
			default: $invalid_format = true;
		}
		
		if ( !$invalid_format ) {
			//Set the filename key for older versions of PHP
			if ( !$file[ 'filename' ] )
				$file[ 'filename' ] = substr( $file[ 'basename' ], 0, strlen( $file[ 'basename' ] ) - strlen( $file[ 'extension' ] ) - 1 );
			
			//Generate a file hash if secure filenames are enabled
			$hash = ( get_option( 'comic_secure_names' ) ) ? '-' . substr( md5( uniqid( rand() ) ), 0, 7) : '';
			
			//Set the target path for the new file
			$target_path = $file_path . $file[ 'filename' ] . $hash . '.' . $file[ 'extension' ];
			
			//Attempt to move the uploaded file to the comic directory if a file with the new files filename doesn't already exist or overwrite is enabled 
			if ( ( !is_file( $target_path ) || $_REQUEST[ 'new_comic_overwrite' ] ) && move_uploaded_file( $_FILES[ 'new_comic_file' ][ 'tmp_name' ], $target_path ) ) {
				//Delete any old files still associated with the post
				if ( ( $hash || !$_REQUEST[ 'new_comic_overwrite' ] ) && is_file( $file_path . get_post_meta( $id, 'comic_file', true ) ) && $_FILES[ 'new_comic_file' ][ 'name' ] != get_post_meta( $id, 'comic_file', true ) )
					unlink( $file_path . get_post_meta( $id, 'comic_file', true ) );
				if ( is_file( $thumb_path . get_post_meta( $id, 'comic_large', true ) ) )
					unlink( $thumb_path . get_post_meta( $id, 'comic_large', true ) );
				if ( is_file( $thumb_path . get_post_meta( $id, 'comic_medium', true ) ) )
					unlink( $thumb_path . get_post_meta( $id, 'comic_medium', true ) );
				if ( is_file( $thumb_path . get_post_meta( $id, 'comic_thumb', true ) ) )
					unlink( $thumb_path . get_post_meta( $id, 'comic_thumb', true ) );
				
				//Set the correct file permissions based on the server operating system
				if ( strpos( PHP_OS, 'WIN' ) )
					chmod( $target_path, 0777 );
				else
					chmod( $target_path, 0664 );
				
				//Get the file information
				$img_dim  = getimagesize( $target_path );
				
				//Add or update the post custom filed for the new file
				if ( !add_post_meta( $id, 'comic_file', basename( $target_path ), true ) )
					update_post_meta( $id, 'comic_file', basename( $target_path ) );
				
				if ( 'application/x-shockwave-flash' != $img_dim[ 'mime' ] ) { //Attempt to create alternative sizes if this isn't an swf file
					generate_comic_thumbnails( $id, $target_path, $img_dim );
				} else { //Delete any alternate size custom fields if this is an swf file
					if ( get_post_meta( $id, 'comic_large', true ) )
						delete_post_meta( $id, 'comic_large' );
					if ( get_post_meta( $id, 'comic_medium', true ) )
						delete_post_meta( $id, 'comic_medium' );
					if ( get_post_meta( $id, 'comic_thumb', true ) )
						delete_post_meta( $id, 'comic_thumb' );
				}
			}
		}
	} else {
		if ( $_REQUEST[ 'comic_file_delete' ] ) {
			if ( is_file( $file_path . get_post_meta( $id, 'comic_file', true ) ) )
				if ( unlink( $file_path . get_post_meta( $id, 'comic_file', true ) ) )
					delete_post_meta( $id, 'comic_file' );
			
			if ( is_file( $thumb_path . get_post_meta( $id, 'comic_large', true ) ) )
				if ( unlink( $thumb_path . get_post_meta( $id, 'comic_large', true ) ) )
					delete_post_meta( $id, 'comic_large' );
			
			if ( is_file( $thumb_path . get_post_meta( $id, 'comic_medium', true ) ) )
				if ( unlink( $thumb_path . get_post_meta( $id, 'comic_medium', true ) ) )
					delete_post_meta( $id, 'comic_medium' );
			
			if ( is_file( $thumb_path . get_post_meta( $id, 'comic_thumb', true ) ) )
				if ( unlink( $thumb_path . get_post_meta( $id, 'comic_thumb', true ) ) )
					delete_post_meta( $id, 'comic_thumb' );
					
		} elseif ( $_REQUEST[ 'comic_file' ] && $_REQUEST[ 'comic_file' ] . $_REQUEST[ 'comic_ext' ] != get_post_meta( $id, 'comic_file', true ) ) {
			if ( is_file( $file_path . get_post_meta( $id, 'comic_file', true ) ) && !is_file( $file_path . $_REQUEST[ 'comic_file' ] . $_REQUEST[ 'comic_ext' ] ) ) {
				if ( rename( $file_path . get_post_meta( $id, 'comic_file', true ), $file_path . $_REQUEST[ 'comic_file' ] . $_REQUEST[ 'comic_ext' ] ) ) {
					update_post_meta( $id, 'comic_file', $_REQUEST[ 'comic_file' ] . $_REQUEST[ 'comic_ext' ] );
					
					$file = pathinfo( $file_path . $_REQUEST[ 'comic_file' ] . $_REQUEST[ 'comic_ext' ] ); 
					
					if ( !$file[ 'filename' ] )
						$file[ 'filename' ] = substr( $file[ 'basename' ], 0, strlen( $file[ 'basename' ] ) - strlen( $file[ 'extension' ] ) - 1 );
					
					if ( is_file( $thumb_path . get_post_meta( $id, 'comic_large', true ) ) && !is_file( $thumb_path . $file[ 'filename' ] . '-large.' . $file[ 'extension' ] ) )
						if ( rename( $thumb_path . get_post_meta( $id, 'comic_large', true ), $thumb_path . $file[ 'filename' ] . '-large.' . $file[ 'extension' ] ) )
							update_post_meta( $id, 'comic_large', $file[ 'filename' ] . '-large.' . $file[ 'extension' ] );
					
					if ( is_file( $thumb_path . get_post_meta( $id, 'comic_medium', true ) ) && !is_file( $thumb_path . $file[ 'filename' ] . '-medium.' . $file[ 'extension' ] ) )
						if ( rename( $thumb_path . get_post_meta( $id, 'comic_medium', true ), $thumb_path . $file[ 'filename' ] . '-medium.' . $file[ 'extension' ] ) )
							update_post_meta( $id, 'comic_medium', $file[ 'filename' ] . '-medium.' . $file[ 'extension' ] );
					
					if ( is_file( $thumb_path . get_post_meta( $id, 'comic_thumb', true ) ) && !is_file( $thumb_path . $file[ 'filename' ] . '-thumb.' . $file[ 'extension' ] ) )
						if ( rename( $thumb_path . get_post_meta( $id, 'comic_thumb', true ), $thumb_path . $file[ 'filename' ] . '-thumb.' . $file[ 'extension' ] ) )
							update_post_meta( $id, 'comic_thumb', $file[ 'filename' ] . '-thumb.' . $file[ 'extension' ] );
				}
			}
		} elseif ( $_REQUEST[ 'comic_orphan' ] ) {
			$file   = pathinfo( $file_path . $_REQUEST[ 'comic_orphan' ] );
			$thumbs = glob( $thumb_path . '*.*' );
			$sizes  = array();
			
			if ( !$file[ 'filename' ] )
				$file[ 'filename' ] = substr( $file[ 'basename' ], 0, strlen( $file[ 'basename' ] ) - strlen( $file[ 'extension' ] ) - 1 );
			
			if ( !add_post_meta( $id, 'comic_file', $file[ 'basename' ], true ) )
				update_post_meta( $id, 'comic_file', $file[ 'basename' ] );
			
			foreach ( $thumbs as $thumb ) {
				if ( strpos( $thumb, $file[ 'filename' ] . '-large' ) ) {
					if ( !add_post_meta( $id, 'comic_large', basename( $thumb ), true ) )
						update_post_meta( $id, 'comic_large', basename( $thumb ) );
					
					$sizes[ 'large' ] = true;
				}
				
				if ( strpos( $thumb, $file[ 'filename' ] . '-medium' ) ) {
					if ( !add_post_meta( $id, 'comic_medium', basename( $thumb ), true ) )
						update_post_meta( $id, 'comic_medium', basename( $thumb ) );
					
					$sizes[ 'medium' ] = true;
				}
				
				if ( strpos( $thumb, $file[ 'filename' ] . '-thumb' ) ) {
					if ( !add_post_meta( $id, 'comic_thumb', basename( $thumb ), true ) )
						update_post_meta( $id, 'comic_thumb', basename( $thumb ) );
					
					$sizes[ 'thumb' ] = true;
				}
			}
			
			if ( !$sizes[ 'large' ] )
				delete_post_meta( $id, 'comic_large' );
			
			if ( !$sizes[ 'medium' ] )
				delete_post_meta( $id, 'comic_medium' );
			
			if ( !$sizes[ 'thumb' ] )
				delete_post_meta( $id, 'comic_thumb' );
		}
	}
	
	/** Attempt to update the comic chapter information */
	if ( $_REQUEST[ 'comic_chapter' ] )
		add_post_to_chapter( $id, $_REQUEST[ 'comic_chapter' ] );
	
	/** Attempt to update the comic description */
	if ( $_REQUEST[ 'comic_description_delete' ] ) {
		delete_post_meta( $id, 'comic_description' );
	} elseif ( $_REQUEST[ 'comic_description' ] && $_REQUEST[ 'comic_description' ] != get_post_meta( $id, 'comic_description', true ) ) {
		if ( !add_post_meta( $id, 'comic_description', $_REQUEST[ 'comic_description' ], true ) )
			update_post_meta( $id, 'comic_description', $_REQUEST[ 'comic_description' ] );
	}
	
	/** Attempt to update the comic transcript */
	if ( 'delete' == $_REQUEST[ 'comic_transcript_action' ] ) {
		delete_post_meta( $id, 'comic_transcript' );
		delete_post_meta( $id, 'comic_transcript_pending' );
		delete_post_meta( $id, 'comic_transcript_draft' );
		delete_post_meta( $id, 'comic_transcript_backup' );
	} elseif ( $_REQUEST[ 'comic_transcript' ] ) {
		if ( 'restore' == $_REQUEST[ 'comic_transcript_action' ] ) {
			if ( $backup = get_post_meta( $id, 'comic_transcript_backup', true ) ) {
				if ( !add_post_meta( $id, 'comic_transcript_draft', $backup, true ) )
					update_post_meta( $id, 'comic_transcript_draft', $backup );
				
				delete_post_meta( $id, 'comic_transcript' );
				delete_post_meta( $id, 'comic_transcript_pending' );
				delete_post_meta( $id, 'comic_transcript_backup' );
			}
		} elseif ( 'draft' == $_REQUEST[ 'comic_transcript_action' ] ) {
			if ( 'draft' != $_REQUEST[ 'comic_transcript_type' ] || $_REQUEST[ 'comic_transcript' ] != get_post_meta( $id, 'comic_transcript_draft', true ) ) 
				if ( !add_post_meta( $id, 'comic_transcript_draft', $_REQUEST[ 'comic_transcript' ], true ) )
					update_post_meta( $id, 'comic_transcript_draft', $_REQUEST[ 'comic_transcript' ] );
			
			delete_post_meta( $id, 'comic_transcript' );
			delete_post_meta( $id, 'comic_transcript_pending' );
		} elseif ( 'pending' == $_REQUEST[ 'comic_transcript_action' ] ) {
			if ( 'pending' != $_REQUEST[ 'comic_transcript_type' ] || $_REQUEST[ 'comic_transcript' ] != get_post_meta( $id, 'comic_transcript_pending', true ) ) 
				if ( !add_post_meta( $id, 'comic_transcript_pending', $_REQUEST[ 'comic_transcript' ], true ) )
					update_post_meta( $id, 'comic_transcript_pending', $_REQUEST[ 'comic_transcript' ] );
			
			delete_post_meta( $id, 'comic_transcript' );
			delete_post_meta( $id, 'comic_transcript_draft' );
			delete_post_meta( $id, 'comic_transcript_backup' );
		} else {
			if ( 'publish' != $_REQUEST[ 'comic_transcript_type' ] || $_REQUEST[ 'comic_transcript' ] != get_post_meta( $id, 'comic_transcript', true ) ) 
				if ( !add_post_meta( $id, 'comic_transcript', $_REQUEST[ 'comic_transcript' ], true ) )
					update_post_meta( $id, 'comic_transcript', $_REQUEST[ 'comic_transcript' ] );
			
			delete_post_meta( $id, 'comic_transcript_pending' );
			delete_post_meta( $id, 'comic_transcript_draft' );
			delete_post_meta( $id, 'comic_transcript_backup' );
		}
	}
} add_action( 'save_post', 'comic_post_meta_box_save', 10, 2 );

function comic_page_meta_box( $post ) {
	$categories = get_comic_category( true );
	?>
	<p>
		<label for="comic_series"><strong><?php _e( 'Comic Series', 'webcomic' ); ?></strong></label><br />
		<select name="comic_series" id="comic_series">
			<option value=""><?php _e( 'All', 'webcomic' ); ?></option>
		<?php foreach ( $categories as $cat ) { ?>
			<option value="<?php echo $cat ?>"<?php if ( get_post_meta( $post->ID, 'comic_series', true ) == $cat ) echo ' selected="selected"'; echo '>' . get_term_field( 'name', $cat, 'chapter' ); ?></option>
		<?php } ?>
		</select>
	</p>
	<?php
}

function comic_page_meta_box_save( $id ) {
	if ( !$_REQUEST[ 'original_publish' ] )
		return; //User did not manually update the post
	
	//Make sure we're working with the post, not a revisions
	if ( $_post = wp_is_post_revision( $id ) )
		$id = $_post;
	
	/** Attempt to update the comic description */
	if ( !$_REQUEST[ 'comic_series' ] ) {
		delete_post_meta( $id, 'comic_series' );
	} elseif ( $_REQUEST[ 'comic_series' ] && $_REQUEST[ 'comic_series' ] != get_post_meta( $id, 'comic_series', true ) ) {
		if ( !add_post_meta( $id, 'comic_series', $_REQUEST[ 'comic_series' ], true ) )
			update_post_meta( $id, 'comic_series', $_REQUEST[ 'comic_series' ] );
	}
} add_action('save_post', 'comic_page_meta_box_save');
?>
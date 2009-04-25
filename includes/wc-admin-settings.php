<?php
/**
 * Contains all functions related to the Settings page.
 * 
 * @package WebComic
 * @since 1.4.0
 */
 
function comic_page_settings(){
	load_webcomic_domain();
	
	if ( $_REQUEST[ 'updated' ] ) {
		/** Update Series based on new Comic Category selection */
		$cats             = get_categories( 'hierarchical=0&hide_empty=0' );
		$comic_cats       = get_comic_category( true );
		$current_chapters = get_comic_current_chapter( true );
		$comic_series     = array();
		
		/** Push all the category ID's into an array */
		foreach ( $cats as $cat )
			array_push( $comic_series, $cat->term_id );
		
		/** Create any new series, if necessary */
		foreach ( $comic_cats as $comic_cat ) {
			$series_key = array_search( $comic_cat, $comic_series );
			if ( false !== $series_key ) {
				$new_series = get_term( $comic_series[ $series_key ], 'category' );
				wp_insert_term( $new_series->name, 'chapter' );
				$current_chapters[ $new_series->term_id ] = -1;
			}
			unset( $comic_series[ $series_key ] );
		}
		
		//Delete any old series, if necessary
		if ( $comic_series ) {
			foreach ( $comic_series as $the_series ) {
				$s_parent   = get_term( ( int ) $the_series, 'chapter' );
				$v_children = get_term_children( $the_series, 'chapter' );
				
				foreach ( $v_children as $volume ) {
					$c_children = get_term_children( $volume, 'chapter' );
					
					foreach ( $c_children as $chapter )
						wp_delete_term( $chapter, 'chapter' );
					
					wp_delete_term( $volume, 'chapter' );
				}
				
				unset( $current_chapters[ $the_series ] );
				wp_delete_term( $the_series, 'chapter' );
			}
		}
			
		update_option( 'comic_current_chapter', $current_chapters );
		
		//Generate comic directories if they don't already exist
		foreach ( get_option( 'comic_category' ) as $comic_category )
			if ( !file_exists( get_comic_directory( 'abs', true, $comic_category ) ) )
				mkdir( get_comic_directory( 'abs', true, $comic_category ), 0775, true );
		
		echo '<div id="message" class="updated fade"><p><strong>' . __( 'Settings saved.', 'webcomic') . '</strong></p></div>';
	}
?>
	<div class="wrap">
		<div id="icon-webcomic" class="icon32"><img src="<?php echo plugins_url( 'webcomic/includes/webcomic.png' ); ?>" alt="icon" /></div>
		<h2><?php _e( 'Settings', 'webcomic' ); ?></h2>
		<form method="post" action="options.php">
			<?php wp_nonce_field( 'update-options' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="comic_category"><?php _e( 'Categories', 'webcomic' ); ?></label></th>
					<td>
						<p><?php _e( 'Select the category or categories that your comic posts are assigned to. Hold down <code>Ctrl</code> or <code>Command</code> to select multiple categories.', 'webcomic' ); ?></p>
						<select name="comic_category[]" id="comic_category" multiple="multiple" style="height: 10em;"> 
						<?php 
							$categories = get_categories( 'hierarchical=0&hide_empty=0' );
							$comic_cats = get_comic_category( 'all' );
							foreach ( $categories as $the_category ) {
								$option = '<option value="' . $the_category->cat_ID . '"';
								foreach ( $comic_cats as $the_comic_cat ) {
									if ( $the_category->term_id == $the_comic_cat ) {
										$option .= ' selected="selected"';
										break 1;
									}
								}
								$option .= '>' . $the_category->cat_name . '</option>';
								echo $option;	
							}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="comic_directory"><?php _e( 'Directory', 'webcomic' ); ?></label></th>
					<td>
						<input type="text" name="comic_directory" id="comic_directory" value="<?php echo get_option( 'comic_directory' ); ?>" class="code" />
						<span class="setting-description"><?php _e( 'WebComic will look for your files in', 'webcomic'); ?> <a href="<?php echo get_comic_directory( 'root' ); ?>"><?php echo get_comic_directory( 'root' ); ?></a></span>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="comic_name_format"><?php _e( 'Files', 'webcomic' ); ?></label></th>
					<td>
						<label><?php _e( 'Use the post', 'webcomic' ); ?>
						<select name="comic_name_format" id="comic_name_format" style="vertical-align: middle;">
							<option value="date"<?php if ( 'date' == get_option( 'comic_name_format' ) ) echo ' selected="selected"'; ?>><?php _e( 'date', 'webcomic' ); ?></option>
							<option value="slug"<?php if ( 'slug' == get_option( 'comic_name_format' ) ) echo ' selected="selected"'; ?>><?php _e( 'slug', 'webcomic' ); ?></option>
							<option value="unid"<?php if ( 'unid' == get_option( 'comic_name_format' ) ) echo ' selected="selected"'; ?>><?php _e( 'ID', 'webcomic' ); ?></option>
							<option value="meta"<?php if ( 'meta' == get_option( 'comic_name_format' ) ) echo ' selected="selected"'; ?>><?php _e( 'custom field', 'webcomic' ); ?></option>
						</select>
						<?php _e( 'fallback method for matching files with posts.', 'webcomic' ); ?>
						</label><label><?php printf( __( 'Dates should be in this <a href="%s">format</a>:', 'webcomic' ), 'http://php.net/manual/function.date.php' ); ?> <input type="text" name="comic_name_format_date" value="<?php echo get_option( 'comic_name_format_date' ); ?>" class="small-text" /> <code><?php echo date( get_option( 'comic_name_format_date' ) ); ?></code></label><br />
						<label><input type="checkbox" name="comic_secure_names" value="1"<?php if ( get_option( 'comic_secure_names' ) ) echo ' checked="checked"'; ?> /> <?php _e( 'Secure filenames during upload to prevent read ahead and archive scraping', 'webcomic' ); ?></label><br />
						<label><input type="checkbox" name="comic_post_draft" value="1"<?php if ( get_option( 'comic_post_draft' ) ) echo ' checked="checked"'; ?> /> <?php _e( 'Save posts automatically generated from the Library as drafts', 'webcomic' ); ?></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="comic_feed"><?php _e( 'Feeds', 'webcomic' ); ?></label></th>
					<td><input name="comic_feed" type="checkbox" id="comic_feed" value="1"<?php if ( get_option( 'comic_feed' ) ) echo ' checked="checked"'; ?> />
					<label><?php _e( 'Include', 'webcomic' ); ?>
						<select name="comic_feed_size">
							<option value="full"<?php if ( 'full' == get_option( 'comic_feed_size' ) ) echo ' selected="selected"'; ?>><?php _e( 'full', 'webcomic' ); ?></option>
							<option value="large"<?php if ( 'large' == get_option( 'comic_feed_size' ) ) echo ' selected="selected"'; ?>><?php _e( 'large', 'webcomic' ); ?></option>
							<option value="medium"<?php if ( 'medium' == get_option( 'comic_feed_size' ) ) echo ' selected="selected"'; ?>><?php _e( 'medium', 'webcomic' ); ?></option>
							<option value="thumb"<?php if ( 'thumb' == get_option( 'comic_feed_size' ) ) echo ' selected="selected"'; ?>><?php _e( 'thumbnail', 'webcomic' ); ?></option>
						</select>
					<?php _e( 'comics in feeds', 'webcomic' ); ?></label></td>
				</tr>
				<tr>
					<th scope="row"><label for="comic_transcripts_allowed"><?php _e( 'Transcripts', 'webcomic' ); ?></label></th>
					<td>
						<label><input type="checkbox" name="comic_transcripts_allowed" value="1"<?php if ( get_option( 'comic_transcripts_allowed' ) ) echo ' checked="checked"'; ?> /> <?php _e( 'Allow people to submit transcripts for individual comics', 'webcomic' ); ?></label><br />
						<label><input type="checkbox" name="comic_transcripts_required" value="1"<?php if ( get_option( 'comic_transcripts_required' ) ) echo ' checked="checked"'; ?> /> <?php _e( 'Transcript author must provide a name and e-mail', 'webcomic' ); ?></label><br />
						<label><input type="checkbox" name="comic_transcripts_loggedin" value="1"<?php if ( get_option( 'comic_transcripts_loggedin' ) ) echo ' checked="checked"'; ?> /> <?php _e( 'Users must be registered and logged in to transcribe', 'webcomic' ); ?></label>
					</td>
				</tr>
			</table>
			<h3><?php _e( 'Image Sizes', 'webcomic' ); ?></h3>
			<p><?php _e( 'The sizes listed below determine the maximum dimensions in pixels to use when generating comic thumbnails from image files during upload, or the exact dimensions to use when displaying Flash files.', 'webcomic' ); ?></p>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="comic_thumb_size_w"><?php _e( 'Thumbnail Size', 'webcomic' ); ?></label></th>
					<td>
						<label><?php _e( 'Width', 'webcomic' ); ?> <input type="text" name="comic_thumb_size_w" id="comic_thumb_size_w" value="<?php echo get_option( 'comic_thumb_size_w' ); ?>" class="small-text" /></label> &emsp;
						<label><?php _e( 'Height', 'webcomic' ); ?> <input type="text" name="comic_thumb_size_h" value="<?php echo get_option( 'comic_thumb_size_h' ); ?>" class="small-text" /></label><br />
						<label><input type="checkbox" name="comic_thumb_crop" value="1"<?php if ( get_option( 'comic_thumb_crop' ) ) echo ' checked="checked"'; ?> /> <?php _e('Crop thumbnail to exact dimensions (normally thumbnails are proportional)','webcomic') ?></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="comic_medium_size_w"><?php _e( 'Medium Size', 'webcomic' ); ?></label></th>
					<td>
						<label><?php _e( 'Max Width', 'webcomic' ); ?> <input type="text" name="comic_medium_size_w" id="comic_medium_size_w" value="<?php echo get_option( 'comic_medium_size_w' ); ?>" class="small-text" /></label>  &emsp;
						<label><?php _e( 'Max Height', 'webcomic' ); ?> <input type="text" name="comic_medium_size_h" value="<?php echo get_option( 'comic_medium_size_h' ); ?>" class="small-text" /></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="comic_large_size_w"><?php _e( 'Large Size', 'webcomic' ); ?></label></th>
					<td>
						<label><?php _e( 'Max Width', 'webcomic' ); ?> <input type="text" name="comic_large_size_w" id="comic_large_size_w" value="<?php echo get_option( 'comic_large_size_w' ); ?>" class="small-text" /></label> &emsp;
						<label><?php _e( 'Max Height', 'webcomic' ); ?> <input type="text" name="comic_large_size_h" value="<?php echo get_option( 'comic_large_size_h' ); ?>" class="small-text" /></label>
					</td>
				</tr>
			</table>
		<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="<?php _e( 'Save Changes', 'webcomic'); ?>" />
			<input type="hidden" name="action" value="update" />
			<?php settings_fields( 'webcomic_options' ); ?>
		</p>
		</form>
	</div>
<?php } ?>
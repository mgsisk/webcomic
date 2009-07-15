<?php
/**
 * Contains all functions related to the Settings page.
 * 
 * @package Webcomic
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
				if ( !mkdir( get_comic_directory( 'abs', true, $comic_category ), 0775, true ) )
					$mkdir_error = true;
		
		//Disable the buffer alert if 0 is entered for days
		if ( !get_option( 'comic_buffer_alert' ) )
			update_option( 'comic_buffer', '' );
		
		echo '<div id="message" class="updated fade"><p><strong>' . __( 'Settings saved.', 'webcomic') . '</strong></p></div>';
		
		if ( $mkdir_error )
			echo '<div id="message" class="error"><p>' . __( 'Webcomic was not able to create necessary comic directories.', 'webcomic' ) . '</p></div>';
	}
?>
	<div class="wrap">
		<div id="icon-webcomic" class="icon32"><img src="<?php echo webcomic_include_url( 'webcomic.png' ); ?>" alt="icon" /></div>
		<h2><?php _e( 'Comic Settings', 'webcomic' ); ?></h2>
		<form method="post" action="options.php">
			<?php wp_nonce_field( 'update-options' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="comic_directory"><?php _e( 'Directory', 'webcomic' ); ?></label></th>
					<td>
						<input type="text" name="comic_directory" id="comic_directory" value="<?php echo get_option( 'comic_directory' ); ?>" class="code" />
						<span class="description"><?php _e( 'Comic files will be stored in subdirectories of', 'webcomic'); ?> <a href="<?php echo get_comic_directory( 'root' ); ?>"><?php echo get_comic_directory( 'root' ); ?></a></span><br />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="comic_category"><?php _e( 'Categories', 'webcomic' ); ?></label>
						<p class="description"><?php _e( 'Hold down <code>Ctrl</code> or <code>Command</code> to select multiple categories.', 'webcomic' ); ?></p>
					</th>
					<td>
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
					<th scope="row"><label for="comic_secure_names"><?php _e( 'Files', 'webcomic' ); ?></label></th>
					<td>
						<label><input type="checkbox" name="comic_secure_names" id="comic_secure_names" value="1"<?php if ( get_option( 'comic_secure_names' ) ) echo ' checked="checked"'; ?> /> <?php _e( 'Secure filenames during upload to prevent archive scraping', 'webcomic' ); ?></label><br />
						<label><input type="checkbox" name="comic_secure_paths" value="1"<?php if ( get_option( 'comic_secure_paths' ) ) echo ' checked="checked"'; ?> /> <?php _e( "Secure URL's to obscure the location and name of comic files", 'webcomic' ); ?></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="comic_transcripts_allowed"><?php _e( 'Transcripts', 'webcomic' ); ?></label></th>
					<td>
						<label><input type="checkbox" name="comic_transcripts_allowed" id="comic_transcripts_allowed" value="1"<?php if ( get_option( 'comic_transcripts_allowed' ) ) echo ' checked="checked"'; ?> /> <?php _e( 'Allow people to submit transcripts for individual comics', 'webcomic' ); ?></label><br />
						<label><input type="checkbox" name="comic_transcripts_required" value="1"<?php if ( get_option( 'comic_transcripts_required' ) ) echo ' checked="checked"'; ?> /> <?php _e( 'Transcript author must provide a name and e-mail', 'webcomic' ); ?></label><br />
						<label><input type="checkbox" name="comic_transcripts_loggedin" value="1"<?php if ( get_option( 'comic_transcripts_loggedin' ) ) echo ' checked="checked"'; ?> /> <?php _e( 'Users must be registered and logged in to transcribe', 'webcomic' ); ?></label>
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
					<th scope="row"><label for="comic_buffer"><?php _e( 'Buffers', 'webcomic' ); ?></label></th>
					<td>
						<input type="checkbox" name="comic_buffer" id="comic_buffer" value="1"<?php if ( get_option( 'comic_buffer' ) ) echo ' checked="checked"'; ?> />
						<label>
							<?php _e( 'Send an e-mail notification', 'webcomic' ); ?>
							<input type="text" name="comic_buffer_alert" value="<?php echo get_option( 'comic_buffer_alert' ); ?>" class="small-text" />
							<?php _e( 'days before a buffer runs out', 'webcomic' ); ?>
						</label>
					</td>
				</tr>
			</table>
			<h3><?php _e( 'Dimensions', 'webcomic' ); ?></h3>
			<p><?php _e( 'The sizes listed below determine the maximum dimensions in pixels to use when generating thumbnail images, or the exact dimensions to use when displaying Flash files.', 'webcomic' ); ?></p>
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
				<input type="submit" name="Submit" class="button-primary" value="<?php _e( 'Save Changes', 'webcomic'); ?>" /> <span class="alignright description"><?php printf( __( '<a href="%s" title="Show your support by donating">Donate</a> | Webcomic Version %s', 'webcomic' ), 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=5683100', get_option( 'webcomic_version' ) ); ?></span>
				<input type="hidden" name="action" value="update" />
				<?php settings_fields( 'webcomic_options' ); ?>
			</p>
		</form>
	</div>
<?php } ?>
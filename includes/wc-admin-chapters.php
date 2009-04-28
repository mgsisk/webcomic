<?php
/**
 * Contains all functions related to the Chapters page.
 * 
 * @package WebComic
 * @since 1.4.0
 */
 
function comic_page_chapters(){
	load_webcomic_domain();
	
	//Set important variables
	$mu_check         = pathinfo( __FILE__ );
	$icon             = ( strstr( $mu_check[ 'dirname' ], 'mu-plugins' ) ) ? get_option( 'home' ) . '/' . 'wp-content/mu-plugins/includes/webcomic.png' : plugins_url( 'webcomic/includes/webcomic.png' );
	$page             = 'comic-chapters';
	$paged            = ( $_REQUEST[ 'paged' ] ) ? $_REQUEST[ 'paged' ] : 1;
	$series           = ( $_REQUEST[ 'series' ] ) ? $_REQUEST[ 'series' ] : get_comic_category();
	$view_link        = '?page=' . $page . '&amp;series=' . $series . '&amp;paged=' . $paged;
	$categories       = get_comic_category( true );
	$current_chapters = get_comic_current_chapter( true );
	
	/** Attempt to update the editted chapter */
	if ( 'chapter_set_current' == $_REQUEST[ 'action' ] ) {
		check_admin_referer('chapter_set_current');
		
		$current_chapters[ $series ] = $_REQUEST[ 'chapter' ];
		
		update_option( 'comic_current_chapter', $current_chapters );
	}
	
	/** Attempt to update the editted chapter */
	if ( 'chapter_unset_current' == $_REQUEST[ 'action' ] ) {
		check_admin_referer('chapter_unset_current');
		
		$current_chapters[ $series ] = -1;
		
		update_option( 'comic_current_chapter', $current_chapters );
	}
	
	/** Attempt to update the editted chapter */
	if ( 'chapter_update' == $_REQUEST[ 'action' ] ) {
		check_admin_referer( 'chapter_update' );
		
		$chapter_name        = trim( $_REQUEST[ 'chapter_name' ] );
		$chapter_nicename    = ( $_REQUEST[ 'chapter_nicename' ] ) ? sanitize_title( $_REQUEST[ 'chapter_nicename' ] ) : sanitize_title( $_REQUEST[ 'chapter_name' ] );
		$chapter_description = $_REQUEST[ 'chapter_description' ];
		
		if ( $_REQUEST[ 'chapter' ] == $series )
			$chapter_parent = 0;
		elseif ( $_REQUEST[ 'chapter_parent' ] )
			$chapter_parent = $_REQUEST[ 'chapter_parent' ];
		else
			$chapter_parent = $series;
		
		$chapter_name_check  = is_term( $_REQUEST[ 'chapter_name' ], 'chapter' );
		$chapter_slug_check  = ( $_REQUEST[ 'chapter_nicename' ] ) ? is_term( $_REQUEST[ 'chapter_nicename' ], 'chapter' ) : 0;
		
		if ( !$chapter_name )
			$update_error = 1;
		elseif ( $chapter_name_check && $_REQUEST[ 'chapter' ] != $chapter_name_check[ 'term_id' ] )
			$update_error = 2;
		elseif ( $chapter_slug_check && $_REQUEST[ 'chapter' ] != $chapter_slug_check[ 'term_id' ] )
			$update_error = 3;
		
		if ( !$update_error && !is_wp_error( wp_update_term( $_REQUEST[ 'chapter' ], 'chapter', array( 'name' => $chapter_name, 'slug' => $chapter_nicename, 'parent' => $chapter_parent, 'description' => $chapter_description ) ) ) )
			$updated = sprintf( __( 'Updated chapter &#8220;%s&#8220;', 'webcomic'), $chapter_name );
		elseif ( 1 == $update_error )
			$error = __( 'A chapter name must be provided.', 'webcomic' );
		elseif ( 2 == $update_error )
			$error = __( 'A chapter with that name already exists.', 'webcomic' );
		elseif ( 3 == $update_error )
			$error = __( 'A chapter with that slug already exists.', 'webcomic' );
		else
			$error = __( 'The chapter could not be updated.', 'webcomic' );
	}
	
	/** Attempt to create a new chapter */
	if ( 'chapter_add' == $_REQUEST[ 'action' ] ) {
		check_admin_referer( 'chapter_add' );
		
		$chapter_name        = trim( $_REQUEST[ 'chapter_name' ] );
		$chapter_nicename    = ( $_REQUEST[ 'chapter_nicename' ] ) ? sanitize_title( $_REQUEST[ 'chapter_nicename' ] ) : sanitize_title( $_REQUEST[ 'chapter_name' ] );
		$chapter_parent      = ( $_REQUEST[ 'chapter_parent' ] ) ? $_REQUEST[ 'chapter_parent' ] : $series;
		$chapter_description = $_REQUEST[ 'chapter_description' ];
		
		if ( !$chapter_name ) {
			$add_error = 1;
		} elseif ( is_term( $chapter_name, 'chapter' ) ) {
			$add_error = 2;
		} elseif ( $_REQUEST[ 'chapter_nicename' ] && is_term( $chapter_nicename, 'chapter' ) ) {
			$add_error = 3;
		} else {
			wp_insert_term( $chapter_name, 'chapter', array( 'description' => $chapter_description, 'parent' => $chapter_parent, 'slug' => $chapter_nicename ) );
			$updated = sprintf( __( 'Added new chapter &#8220;%s&#8220;', 'webcomic' ), $chapter_name );
		}
	}
	
	/** Attempt to create a new chapter */
	if ( 'chapter_delete' == $_REQUEST[ 'action' ] ) {
		check_admin_referer( 'chapter_delete' );
		
		if ( $_REQUEST['is_volume'] ) {
			$the_chapter = get_the_chapter( $_REQUEST[ 'chapter' ] );
			$children    = get_term_children( $_REQUEST[ 'chapter' ], 'chapter' );
			$extra       = __( ' and all the chapters it contained.', 'webcomic' );
			
			foreach ( $children as $chapter ) {
				$posts = get_objects_in_term( $chapter, 'chapter' );
				
				foreach ( $posts as $the_post )
					wp_delete_object_term_relationships( $the_post, 'chapter' );
				
				if ( $chapter == $current_chapters[ $the_series ] )
					$current_chapters[ $the_series ] = -1;
				
				wp_delete_term( $chapter,'chapter' );
			}
			
			update_option( 'comic_current_chapter', $current_chapters );
			wp_delete_term( $_REQUEST[ 'chapter' ], 'chapter' );
		} else {
			$the_chapter = get_the_chapter( $_REQUEST[ 'chapter' ] );
			
			$posts = get_objects_in_term( $_REQUEST[ 'chapter' ], 'chapter' );
			
			foreach ( $posts as $the_post )
				wp_delete_object_term_relationships( $the_post, 'chapter' );
			
			if ( $_REQUEST[ 'chapter' ] == $current_chapters[ $the_series ] ) {
				$current_chapters[ $the_series ] = -1;
				update_option( 'comic_current_chapter', $current_chapters );
			}
			
			wp_delete_term( $_REQUEST[ 'chapter' ], 'chapter' );
		}
		
		$updated =sprintf( __( 'Deleted &#8220;%1$s&#8220;%2$s', 'webcomic' ), $the_chapter->title, $extra );
	}
	
	//Display any messages
	if ( $updated )
		echo '<div id="message" class="updated fade"><p>' . $updated . '</p></div>';
	
	if ( $error )
		echo '<div id="message" class="error"><p>' . $error	. '</p></div>';
	
	$collection = get_the_collection( 'hide_empty=0&depth=3&series=' . $series );
?>
<div class="wrap">
	<div id="icon-webcomic" class="icon32"><img src="<?php echo $icon; ?>" alt="icon" /></div>
<?php if ( 'chapter_edit' == $_REQUEST[ 'action' ] ) { $the_chapter = get_term_to_edit( $_REQUEST[ 'chapter' ], 'chapter' ); ?>
	<h2><?php _e( 'Edit Chapter', 'webcomic' ); ?></h2>
		<form action="" method="post">
			<?php wp_nonce_field( 'chapter_update' ); ?>
			<table class="form-table">
				<tr class="form-field">
					<th scope="row"><label for="chapter_name"><?php _e( 'Chapter Name', 'webcomic' ); ?></label></th>
					<td><input name="chapter_name" id="chapter_name" type="text" value="<?php echo $the_chapter->name; ?>" /><br />
					<?php _e( 'The name is used to identify the chapter almost everywhere.', 'webcomic' ); ?></td>
				</tr>
				<tr class="form-field">
					<th scope="row"><label for="chapter_nicename"><?php _e( 'Chapter Slug', 'webcomic' ); ?></label></th>
					<td><input name="chapter_nicename" id="chapter_nicename" type="text" value="<?php echo $the_chapter->slug; ?>" /><br /
					><?php _e('The &#8220;slug&#8220; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'webcomic' ); ?></td>
				</tr>
				<?php if ( !$_REQUEST[ 'is_volume' ] && !$_REQUEST[ 'is_series' ] ) { ?>
				<tr class="form-field">
					<th scope="row"><label for="chapter_parent"><?php _e( 'Chapter Volume', 'webcomic' ); ?></label></th>
					<td>
						<select name="chapter_parent" id="chapter_parent">
							<option value="0"><?php _e( 'None', 'webcomic' ); ?></option>
						<?php
							foreach ( array_keys( get_object_vars( $collection->$series->volumes ) ) as $volume ) {
								if ( $collection->$series->volumes->$volume->ID == $the_chapter->parent )
									echo '<option value="' . $collection->$series->volumes->$volume->ID . '" selected="selected">' . $collection->$series->volumes->$volume->title . '</option>';
								else
									echo '<option value="' . $collection->$series->volumes->$volume->ID . '">' . $collection->$series->volumes->$volume->title . '</option>';
							}
						?>
						</select><br />
						<?php _e( 'Select the volume that this chapter belongs to.', 'webcomic' ); ?>
					</td>
				</tr>
				<?php } ?>
				<tr class="form-field">
					<th scope="row"><label for="chapter_description"><?php _e( 'Descriptions', 'webcomic' ); ?></label></th>
					<td>
						<textarea name="chapter_description" id="chapter_description" rows="5" cols="40"><?php echo $the_chapter->description; ?></textarea>
						<br /><?php _e( 'Useful for providing a brief overview of the events covered in this chapter.', 'webcomic' ); ?>
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" name="submit" value="<?php _e( 'Update Chapter', 'webcomic' ); ?>" /><input type="hidden" name="action" value="chapter_update" />
				<input type="hidden" name="chapter" value="<?php echo $_REQUEST[ 'chapter' ]; ?>" />
				<input type="hidden" name="series" value="<?php echo $series; ?>" />
			</p> 
		</form>
<?php } else { //Display the "Chapters" page ?>
	<h2><?php _e( 'Chapters', 'webcomic' ); ?></h2>
	<div id="col-right">
		<div class="col-wrap">
			<div class="tablenav">
			<?php if ( 1 < count( $categories ) ) { ?>
				<form action="" method="get">
					<div class="alignright actions">
						<input type="hidden" name="page" value="<?php echo $page; ?>" />
						<select name="series">
						<?php foreach ( $categories as $cat ) { ?>
							<option value="<?php echo $cat ?>"<?php if ( $series == $cat ) echo ' selected="selected"'; echo '>' . get_term_field( 'name', $cat, 'chapter' ); ?></option>
						<?php } ?>
						</select>
						<input type="submit" value="<?php _e( 'Change Series', 'webcomic' ); ?>" class="button-secondary action" />
					</div>
				</form>
			<?php } ?>
			</div>
			<table class="widefat">
				<thead>
					<tr>
						<th scope="col" class="manage-column column-name"><?php _e( 'Name', 'webcomic' ); ?></th>
						<th scope="col" class="manage-column column-description"><?php _e( 'Description', 'webcomic' ); ?></th>
						<th scope="col" class="manage-column column-slug"><?php _e( 'Slug', 'webcomic' ); ?></th>
						<th scope="col" class="manage-column column-posts num"><?php _e( 'Pages', 'webcomic' ); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th scope="col" class="manage-column column-name"><?php _e( 'Name', 'webcomic' ); ?></th>
						<th scope="col" class="manage-column column-description"><?php _e( 'Description', 'webcomic' ); ?></th>
						<th scope="col" class="manage-column column-slug"><?php _e( 'Slug', 'webcomic' ); ?></th>
						<th scope="col" class="manage-column column-posts num"><?php _e( 'Pages', 'webcomic' ); ?></th>
					</tr>
				</tfoot>
				<tbody>
					<tr class="alt">
						<td>
							<a href="<?php echo $view_link; ?>&amp;action=chapter_edit&amp;chapter=<?php echo $collection->$series->ID; ?>&amp;is_series=1" title='<?php printf( __( 'Edit "%s"', 'webcomic' ), $collection->$series->title ); ?>' class="row-title"><?php echo $collection->$series->title; ?></a>
							<div class="row-actions"><a href="<?php echo $view_link; ?>&amp;action=chapter_edit&amp;chapter=<?php echo $collection->$series->ID; ?>&amp;is_series=1"><?php _e( 'Edit', 'webcomic' ); ?></a></div>
						</td>
						<td><?php echo $collection->$series->description; ?></td>
						<td><?php echo $collection->$series->slug; ?></td>
						<td class="num"><?php echo $collection->$series->count; ?></td>
					</tr>
				<?php if ( $collection->$series->volumes ) { { foreach ( array_keys( get_object_vars( $collection->$series->volumes ) ) as $volume ) { $i++; ?>
					<tr<?php if ( !( $i % 2 ) ) echo ' class="alt"'; ?>>
						<td>
							<a href="<?php echo $view_link; ?>&amp;action=chapter_edit&amp;chapter=<?php echo $collection->$series->volumes->$volume->ID; ?>&amp;is_volume=1" title="<?php printf( __( 'Edit "%s"', 'webcomic' ), $collection->$series->volumes->$volume->title ); ?>" class="row-title">&mdash; <?php echo $collection->$series->volumes->$volume->title; ?></a>
							<div class="row-actions"><a href="<?php echo $view_link; ?>&amp;action=chapter_edit&amp;chapter=<?php echo $collection->$series->volumes->$volume->ID; ?>&amp;is_volume=1"><?php _e( 'Edit', 'webcomic' ); ?></a>
							| <span class="delete"><a href="<?php echo wp_nonce_url( $view_link . '&amp;action=chapter_delete&amp;chapter=' . $collection->$series->volumes->$volume->ID . '&amp;is_volume=1', 'chapter_delete' ); ?>" onclick="if (confirm('<?php echo js_escape( sprintf( __( "You are about to delete '%s'. Any chapters in this volume will also be deleted.\n 'Cancel' to stop, 'OK' to delete.", "webcomic" ), $collection->$series->volumes->$volume->title ) ); ?>')) { return true;}return;"><?php _e( 'Delete', 'webcomic' ); ?></a></span></div>
						</td>
						<td><?php echo $collection->$series->volumes->$volume->description; ?></td>
						<td><?php echo $collection->$series->volumes->$volume->slug; ?></td>
						<td class="num"><?php echo $collection->$series->volumes->$volume->count; ?></td>
					</tr>
				<?php foreach ( array_keys ( get_object_vars ( $collection->$series->volumes->$volume->chapters ) ) as $chapter ) { $i++; ?>
					<tr<?php if ( !( $i % 2 ) ) echo' class="alt"'; if ( $collection->$series->volumes->$volume->chapters->$chapter->ID == $current_chapters[ $series ] ) echo ' style="background-color:#fdf9c6"'; ?>>
						<td>
							<a href="<?php echo $view_link; ?>&amp;action=chapter_edit&amp;chapter=<?php echo $collection->$series->volumes->$volume->chapters->$chapter->ID; ?>" title='<?php printf( __( 'Edit "%s"', 'webcomic' ), $collection->$series->volumes->$volume->chapters->$chapter->title ); ?>' class="row-title">&mdash; &mdash; <?php echo $collection->$series->volumes->$volume->chapters->$chapter->title; ?></a>
							<div class="row-actions">
								<a href="<?php echo $view_link; ?>&amp;action=chapter_edit&amp;chapter=<?php echo $collection->$series->volumes->$volume->chapters->$chapter->ID; ?>"><?php _e( 'Edit', 'webcomic' ); ?></a>
							<?php if ( $collection->$series->volumes->$volume->chapters->$chapter->ID != $current_chapters[ $series ] ) { ?>
								| <a href="<?php echo wp_nonce_url( $view_link . '&amp;action=chapter_set_current&amp;chapter=' . $collection->$series->volumes->$volume->chapters->$chapter->ID, 'chapter_set_current' ); ?>"><?php _e( 'Set Current', 'webcomic' ); ?></a>
							<?php } else { ?>
								| <a href="<?php echo wp_nonce_url( $view_link.'&amp;action=chapter_unset_current', 'chapter_unset_current' ); ?>"><?php _e( 'Unset Current', 'webcomic' ); ?></a>
							<?php } ?>
								| <span class="delete"><a href="<?php echo wp_nonce_url( $view_link . '&amp;action=chapter_delete&amp;chapter=' . $collection->$series->volumes->$volume->chapters->$chapter->ID, 'chapter_delete' ); ?>" onclick="if (confirm('<?php echo js_escape( sprintf( __( "You are about to delete '%s'\n 'Cancel' to stop, 'OK' to delete.", "webcomic" ), $collection->$series->volumes->$volume->chapters->$chapter->title ) ); ?>')) { return true;}return;"><?php _e( 'Delete', 'webcomic' ); ?></a></span>
							</div>
						</td>
						<td><?php echo $collection->$series->volumes->$volume->chapters->$chapter->description; ?></td>
						<td><?php echo $collection->$series->volumes->$volume->chapters->$chapter->slug; ?></td>
						<td class="num"><?php echo $collection->$series->volumes->$volume->chapters->$chapter->count; ?></td>
					</tr>
				<?php } } } } ?>
				</tbody>
			</table>
			<?php if ( !count( get_object_vars( $collection->$series->volumes ) ) ) { ?>
			<h3><?php _e( 'Using Chapters', 'webcomic' ); ?></h3>
			<p><?php _e( 'Chapters are a useful way of categorizing your comics, similar to post categories. You can create new chapters or modify existing ones here and assign comics to them from the Library or Edit Post pages.', 'webcomic' ); ?></p>
			<p><?php _e( "Above you'll see your first <em>series</em>. Each series corresponds to one of your selected comic categories, and series are automatically created or destroyed (along with any volumes and chapters they contain) when updating your comic categories on the Settings page.", 'webcomic' ); ?></p>
			<p><?php _e( "To start using chapters, you'll need to create two of them: the first will be a <em>volume</em> assigned to the current series, which can contain any number of <em>chapters</em>. You should assign the second chapter to your newly created volume using the Chapter Volume option.", 'webcomic' ); ?></p>
			<p><?php _e( 'After creating a chapter, you can set it as the <em>current chapter</em> by clicking <em>Set Current</em> just underneath the chapter title. Any new posts in this series will be automatically assigned to that chapter.', 'webcomic' ); ?></p>
			<?php } ?>
		</div>
	</div>
	<div id="col-left">
		<div class="col-wrap">
			<div class="form-wrap">  
				<h3><?php _e( 'Add Chapter', 'webcomic' ); ?></h3>
				<?php if ( 2 == $add_error ) echo '<div style="background:#ffebe8;border:1px solid #c00;margin:5px 0;padding:0 7px"><p>' . __( 'The chapter you are trying to create already exists. Chapter names must be unique across all series.', 'webcomic' ) . '</p></div>'; ?>
				<form action="" method="post">
					<?php wp_nonce_field( 'chapter_add' );	?>
					<div class="form-field form-required"<?php if ( 1 == $add_error || 2 == $add_error ) echo ' style="background:#ffebe8"'; ?>>
						<label for="chapter_name"><?php _e( 'Chapter Name', 'webcomic' ); ?></label>
						<input name="chapter_name" id="chapter_name" type="text" value="<?php if ( $add_error ) echo $_REQUEST[ 'chapter_name' ]; ?>" size="40"<?php if ( 1 == $add_error || 2 == $add_error ) echo ' style="border-color:#c00"'; ?> />
						<p><?php _e( 'The name is used to identify the chapter almost everywhere.', 'webcomic' ); ?></p>
					</div>
					<?php 
					if ( 3 == $add_error ) echo '<div style="background:#ffebe8;border:1px solid #c00;margin:5px 0;padding:0 7px;"><p>' . __( 'A chapter with that slug already exists. Chapter slugs must be unique across all series.', 'webcomic' ) . '</p></div>'; ?>
					<div class="form-field"<?php if ( 3 == $add_error ) echo ' style="background:#ffebe8"'; ?>>
						<label for="chapter_nicename"><?php _e( 'Chapter Slug', 'webcomic' ); ?></label>
						<input name="chapter_nicename" id="chapter_nicename" type="text" value="<?php if ( $add_error ) echo $_REQUEST[ 'chapter_nicename' ]; ?>" size="40" />
						<p><?php _e( 'The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'webcomic' ); ?></p>
					</div>
					<div class="form-field">
						<label for="chapter_parent"><?php _e( 'Chapter Volume', 'webcomic' ); ?></label>
						<select name="chapter_parent" id="chapter_parent">
							<option value="0"><?php _e( 'None', 'webcomic' ); ?></option>
						<?php foreach ( array_keys( get_object_vars( $collection->$series->volumes ) ) as $volume) echo '<option value="' . $collection->$series->volumes->$volume->ID . '">' . $collection->$series->volumes->$volume->title . '</option>'; ?>
						</select>
						<p><?php _e( 'Select <strong>None</strong> to turn this chapter into a new volume that you can assign other chapters to.', 'webcomic' ); ?></p>
					</div>
					<div class="form-field">
						<label for="chapter_description"><?php _e( 'Description', 'webcomic' ); ?></label>
						<textarea name="chapter_description" id="chapter_description" rows="5" cols="40"><?php if ( $add_error ) echo $_REQUEST[ 'chapter_description' ]; ?></textarea>
						<p><?php _e( 'Useful for providing a brief overview of the events covered in this chapter.', 'webcomic' ); ?></p>
					</div> 
					<p class="submit">
						<input type="submit" class="button" name="submit" value="<?php _e( 'Add Chapter', 'webcomic' ); ?>" />
						<input type="hidden" name="action" value="chapter_add" />
						<input type="hidden" name="series" value="<?php echo $series; ?>" />
					</p> 
				</form>
			</div>
		</div>
	</div>
	<?php } ?>
</div>
<?php } ?>
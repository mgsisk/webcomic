<?php
/**
 * Contains various administration related functions.
 * 
 * @package Webcomic
 * @since 1.0.0
 */

/**
 * Registers the Webcomic administrative pages and adds contextual help links.
 * 
 * @package Webcomic
 * @since 1.0.0
 */
function webcomic_admin_menu() {
	load_webcomic_domain();
	
	add_menu_page( __( 'Webcomic', 'webcomic' ), __( 'Webcomic', 'webcomic' ), 'upload_files', 'comic-library', 'comic_page_library', webcomic_include_url( 'webcomic-small.png' ) );
	
	$library  = add_submenu_page( 'comic-library', __( 'Webcomic Library', 'webcomic'), __( 'Library', 'webcomic' ), 'upload_files', 'comic-library', 'comic_page_library' );
	$chapters = add_submenu_page( 'comic-library', __( 'Webcomic Chapters', 'webcomic'), __( 'Chapters', 'webcomic' ), 'manage_categories', 'comic-chapters', 'comic_page_chapters' );
	$settings = add_submenu_page( 'comic-library', __( 'Webcomic Settings', 'webcomic'), __( 'Settings', 'webcomic' ), 'manage_options', 'comic-settings', 'comic_page_settings' );
	
	add_meta_box( 'webcomic', __( 'Webcomic', 'webcomic' ), 'comic_post_meta_box', 'post', 'normal', 'high' );
	add_meta_box( 'webcomic', __( 'Webcomic', 'webcomic' ), 'comic_page_meta_box', 'page', 'normal', 'high' );
	
	$help[0] = '<a href="http://code.google.com/p/webcomic/wiki/Library" target="_blank">' . __( 'Library Documentation', 'webcomic' ) . '</a>';
	$help[1] = '<a href="http://code.google.com/p/webcomic/wiki/Chapters" target="_blank">' . __( 'Chapters Documentation', 'webcomic' ) . '</a>';
	$help[2] = '<a href="http://code.google.com/p/webcomic/wiki/Settings" target="_blank">' . __( 'Settings Documentation', 'webcomic' ) . '</a>';
	
	foreach ( $help as $key => $value )
		$help[ $key ] .= '<br /><a href="http://code.google.com/p/webcomic/w/list" target="_blank">' . __( 'Webcomic Documentation', 'webcomic' ) . '</a><br /><a href="http://maikeruon.com/wcib/forum/" target="_blank">' . __( 'Webcomic Support Forum', 'webcomic' ) . '</a>';
	
	add_contextual_help( $library, $help[ 0 ] );
	add_contextual_help( $chapters, $help[ 1 ] );
	add_contextual_help( $settings, $help[ 2 ] );
	
	register_column_headers( $library, array( 'collection' => __( 'Collection', 'webcomic' ), 'comments' => '<div class="vers"><img alt="Comments" src="images/comment-grey-bubble.png" /></div>', 'date' => __( 'Date', 'webcomic' ) ) );
} add_action( 'admin_menu', 'webcomic_admin_menu' );

/**
 * Corrects the pagenow javascript variable for dynamically
 * saving Library hidden column information.
 * 
 * @package Webcomic
 * @since 2.1.0
 */
function webcomic_admin_enqueue_scripts( $suffix ) {
	if ( false !== strpos( $suffix, 'comic-library' ) ) 
		echo "<script type='text/javascript'>\n//<![CDATA[\nvar pagenow = '$suffix';\n//]]>\n</script>";
} add_action( 'admin_enqueue_scripts', 'webcomic_admin_enqueue_scripts' );

/**
 * Checks if the current column is hidden and outputs CSS as necessary.
 * 
 * @package Webcomic
 * @since 2.1.0
 * 
 * @param str $column Column ID
 */
function webcomic_hide_column( $column ) {
	$hidden = get_hidden_columns( 'toplevel_page_comic-library' );
	
	if ( in_array( $column, $hidden ) )
		echo ' style="display:none"';
}

function webcomic_print_cells( $item ) {
	if ( !$item )
		return;
	
	$hidden = get_hidden_columns( 'toplevel_page_comic-library' );
	
	if ( in_array( 'collection', $hidden ) )
		$hide_collection = ' style="display:none"';
	
	if ( in_array( 'comments', $hidden ) )
		$hide_comments = ' style="display:none"';
	
	if ( in_array( 'date', $hidden ) )
		$hide_date = ' style="display:none"';
	
	echo '<td class="collection column-collection"' . $hide_collection . '>' . $item->chapter . $item->volume . '</td>' . "\n";
	echo '<td class="comments column-comments"' . $hide_comments . '><div class="post-com-count-wrapper">' . $item->comments . '</div></td>' . "\n";
	echo '<td class="date column-date"' . $hide_date . '>' . $item->date . '<br />' . $item->status . '</td>' . "\n";
}

/**
 * Adds contextual help links to various administrative pages.
 * 
 * @package Webcomic
 * @since 2.0.0
 */
function webcomic_contextual_help_list( $_wp_contextual_help ) {
	$help = '<p><a href="http://code.google.com/p/webcomic/wiki/Metaboxes" target="_blank">' . __( 'Using the Webcomic Metabox', 'webcomic' ) . '</a><br /><a href="http://code.google.com/p/webcomic/w/list" target="_blank">' . __( 'Webcomic Documentation', 'webcomic' ) . '</a><br /><a href="http://maikeruon.com/wcib/forum/" target="_blank">' . __( 'Webcomic Support Forum', 'webcomic' ) . '</a></p>';
	
	if ( $_wp_contextual_help[ 'post' ] )
		$_wp_contextual_help[ 'post' ] .= $help;
		
	if ( $_wp_contextual_help[ 'page' ] )
		$_wp_contextual_help[ 'page' ] .= $help;
	
	return $_wp_contextual_help;
} add_filter( 'contextual_help_list', 'webcomic_contextual_help_list' );

/**
 * Registers plugin settings and ensures the correct javascript files are enqueued.
 * 
 * @package Webcomic
 * @since 1.0.0
 */
function webcomic_admin_init() {
	register_setting( 'webcomic_options', 'comic_category' );
	register_setting( 'webcomic_options', 'comic_directory' );
	register_setting( 'webcomic_options', 'comic_secure_paths' );
	register_setting( 'webcomic_options', 'comic_secure_names' );
	register_setting( 'webcomic_options', 'comic_post_draft' );
	register_setting( 'webcomic_options', 'comic_transcripts_allowed' );
	register_setting( 'webcomic_options', 'comic_transcripts_required' );
	register_setting( 'webcomic_options', 'comic_transcripts_loggedin' );
	register_setting( 'webcomic_options', 'comic_feed' );
	register_setting( 'webcomic_options', 'comic_feed_size' );
	register_setting( 'webcomic_options', 'comic_buffer' );
	register_setting( 'webcomic_options', 'comic_buffer_alert' );
	register_setting( 'webcomic_options', 'comic_keyboard_shortcuts' );
	register_setting( 'webcomic_options', 'comic_thumb_crop' );
	register_setting( 'webcomic_options', 'comic_thumb_size_w' );
	register_setting( 'webcomic_options', 'comic_thumb_size_h' );
	register_setting( 'webcomic_options', 'comic_medium_size_w' );
	register_setting( 'webcomic_options', 'comic_medium_size_h' );
	register_setting( 'webcomic_options', 'comic_large_size_w' );
	register_setting( 'webcomic_options', 'comic_large_size_h' );
} add_action( 'admin_init', 'webcomic_admin_init' );

/**
 * Adds a comic post to the specified chapter.
 * 
 * This function adds a post to the specified chapter taxonomy.
 * It first checks to see if the specified post is actually
 * assigned to the defined comic category and, if so, assigns
 * it to the specified chapter.
 * 
 * @package Webcomic
 * @since 1.0.0
 * 
 * @param int $id Post ID.
 * @param int $chapter Chapter ID.
 * @param bool $overwrite Overwrites a posts existing chapter, if any.
 */
function add_post_to_chapter( $id, $chapter, $overwrite = 1 ) {
	if ( get_post_comic_category( $id ) ) {
		$post_chapters = get_post_comic_chapters( $id );
		
		if ( $post_chapters ) {
			if ( $overwrite )
				remove_post_from_chapter( $id );
			else
				return;
		}
		
		if ( -1 == $chapter ) {
			remove_post_from_chapter( $id );
		} else {
			$the_chapter = get_term( $chapter, 'chapter' );
			$the_volume  = get_term( $the_chapter->parent, 'chapter' );
			$the_series  = get_term( $the_volume->parent, 'chapter' );
			
			$new_tax = array();
			$new_tax[ 0 ] = $the_chapter->slug;
			$new_tax[ 1 ] = $the_volume->slug;
			$new_tax[ 2 ] = $the_series->slug;
			
			wp_set_object_terms( $id, $new_tax, 'chapter' );
		}
	}
}

/**
 * Removes a comic post from a chapter.
 * 
 * @package Webcomic
 * @since 1.0.0
 * 
 * @param int $id Post ID.
 */
function remove_post_from_chapter( $id ){
	wp_delete_object_term_relationships( $id, 'chapter' );
}
add_action( 'delete_post', 'remove_post_from_chapter' );

/**
 * Adds new comic posts to the defined current chapter.
 * 
 * This function adds any newly created comic posts to the defined
 * current chapter for a given series.
 * 
 * @package Webcomic
 * @since 1.0.0
 * 
 * @param int $id Post ID.
 */
function add_post_to_current_chapter( $id ) {
	$series = get_post_comic_category( $id );
	
	if ( $series )
		$chapter = get_comic_current_chapter( $series );
	else
		return;
	
	add_post_to_chapter( $id, $chapter, 0 );
} add_action( 'save_post', 'add_post_to_current_chapter' );

/**
 * Removes comic series information.
 * 
 * This function automatically removes series information
 * when a corresponding Category is deleted.
 * 
 * @package Webcomic
 * @since 2.0.0
 * 
 * @param int $id Category ID.
 */
function webcomic_category_delete( $id ) {
	$categories = get_comic_category( true );
	
	if ( $key = array_search( $id, $categories ) ) {
		$s_parent         = get_term( ( int ) $id, 'chapter' );
		$v_children       = get_term_children( $id, 'chapter' );
		$current_chapters = get_comic_current_chapter( true );
		
		foreach ( $v_children as $volume ) {
			$c_children = get_term_children( $volume, 'chapter' );
			
			foreach ( $c_children as $chapter )
				wp_delete_term( $chapter, 'chapter' );
			
			wp_delete_term( $volume, 'chapter' );
		}
		
		unset( $current_chapters[ $id ] );
		unset( $categories[ $key ] );
		
		update_option( 'comic_current_chapter', $current_chapters );
		update_option( 'comic_category', $categories );
		
		wp_delete_term( $id, 'chapter' );
	}
} add_action( 'category_delete', 'webcomic_category_delete' );

/**
 * Displays the slug warning for comic categories.
 * 
 * This function adds a warning to the Edit Category page for comic
 * categories, informing the user that changing the category slug
 * will also require changing the categories associated comic 
 * directory to match the new slug name.
 * 
 * @package Webcomic
 * @since 2.0.0
 * 
 * @param obj $category The category object for the selected category.
 */
function webcomic_edit_category_form_pre( $category ) {
	$categories = get_comic_category( true );
	
	if ( false !== array_search( $category->term_id, $categories ) )
		echo '<div id="message" class="updated fade"><p>' . sprintf( __( 'This is a comic category. Changing the <strong>Category Slug</strong> will also rename the directory <a href="%s">%s</a> to match the new slug.', 'webcomic'), get_comic_directory( 'url', false, $category->term_id ), get_comic_directory( 'url', false, $category->term_id ) ) . '</p></div>';
} add_action( 'edit_category_form_pre', 'webcomic_edit_category_form_pre' );

/**
 * Adds the 'comic_directory' hidden field to the Edit Category page.
 * 
 * This function adds the 'comic_directory' hidden field to the Edit
 * Category page. When detected, Webcomic copmares the existing directory
 * path to the one in 'comic_directory' and renames comic subdirectories
 * as necessary.
 * 
 * @package Webcomic
 * @since 2.0.0
 * 
 * @param obj $category The category object for the selected category.
 */
function webcomic_edit_category_form( $category ) {
	$categories = get_comic_category( true );
	
	if ( false !== array_search( $category->term_id, $categories ) )
		echo '<input type="hidden" name="webcomic_subdirectory" value="' . get_comic_directory( 'abs', false, $category->term_id ) . '" />';
} add_action( 'edit_category_form', 'webcomic_edit_category_form' );

/**
 * Renames comic subdirectories as necessary.
 * 
 * This function compares the value of a unique hidden field
 * to an existing comic subdirectory; if the two don't match,
 * Webcomic renames the old directory using the new name.
 * 
 * @package Webcomic
 * @since 2.0.0
 * 
 * @param int $id Category ID.
 */
function webcomic_rename_subdirectory( $id ) {
	if ( $_REQUEST[ 'webcomic_subdirectory' ] && $_REQUEST[ 'webcomic_subdirectory' ] != get_comic_directory( 'abs', false, $id ) )
		rename( $_REQUEST[ 'webcomic_subdirectory' ], get_comic_directory( 'abs', false, $id ) );
} add_action( 'edited_category', 'webcomic_rename_subdirectory' ); add_action( 'edited_chapter', 'webcomic_rename_subdirectory' );

/**
 * Generates comic thumbnails and associates them with the comic post.
 * 
 * This is a utility function used to generate comic thumbnails from a
 * source comic file and link them with the associated comic post.
 * 
 * @package Webcomic
 * @since 2.0.0
 * 
 * @param int $id Post ID.
 * @param str $target_path Source file.
 * @param arr $img_dim Source file information as generated by getimagesize().
 */
function generate_comic_thumbnails( $id = false, $target_path = false, $img_dim = false ) {
	if ( !$id || !$target_path || !$img_dim )
		return;
	
	$img_lw     = get_option( 'comic_large_size_w' );
	$img_lh     = get_option( 'comic_large_size_h' );
	$img_mw     = get_option( 'comic_medium_size_w' );
	$img_mh     = get_option( 'comic_medium_size_h' );
	$img_tw     = get_option( 'comic_thumb_size_w' );
	$img_th     = get_option( 'comic_thumb_size_h' );
	$img_crop   = get_option( 'comic_thumb_crop' ) ? true : false;
	$thumb_path = get_comic_directory( 'abs', true, get_post_comic_category( $id ) );
	
	//Generate a new large size image and add or update the post custom field, or delete the post custom field
	if ( $img_dim[ 0 ] > $img_lw || $img_dim[ 1 ] > $img_lh ) {
		$comic_large = basename( image_resize( $target_path, $img_lw, $img_lh, 0, 'large', $thumb_path ) );
		
		if ( !add_post_meta( $id, 'comic_large', $comic_large, true ) )
			update_post_meta( $id, 'comic_large', $comic_large );
	} elseif ( get_post_meta( $id, 'comic_large', true ) ) {
		delete_post_meta( $id, 'comic_large' );
	}
	
	//Generate a new medium size image and add or update the post custom field, or delete the post custom field
	if ( $img_dim[ 0 ] > $img_mw || $img_dim[ 1 ] > $img_mh ) {
		$comic_medium = basename( image_resize( $target_path, $img_mw, $img_mh, 0, 'medium', $thumb_path ) );
		
		if ( !add_post_meta( $id, 'comic_medium', $comic_medium, true ) )
			update_post_meta( $id, 'comic_medium', $comic_medium );
	} elseif ( get_post_meta( $id, 'comic_medium', true ) ) {
		delete_post_meta( $id, 'comic_medium' );
	}
	
	//Generate a new thumbnail size image and add or update the post custom field, or delete the post custom field
	if ( $img_dim[ 0 ] > $img_tw || $img_dim[ 1 ] > $img_th ) {
		$comic_thumb = basename( image_resize( $target_path, $img_tw, $img_th, $img_crop, 'thumb', $thumb_path ) );
		
		if ( !add_post_meta( $id, 'comic_thumb', $comic_thumb, true ) )
			update_post_meta( $id, 'comic_thumb', $comic_thumb );
	} elseif ( get_post_meta( $id, 'comic_thumb', true ) ) {
		delete_post_meta( $id, 'comic_thumb' );
	}
}
?>
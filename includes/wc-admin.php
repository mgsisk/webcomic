<?php
/**
 * Contains various administration related functions.
 * 
 * @package WebComic
 * @since 1.0.0
 */

/**
 * Registers the WebComic administrative pages and adds contextual help links.
 * 
 * @package WebComic
 * @since 1.0.0
 */
function comic_admin_pages_add() {
	load_webcomic_domain();
	
	add_menu_page( __( 'WebComic', 'webcomic' ), __( 'WebComic', 'webcomic' ), 'upload_files', __FILE__, 'comic_page_library', webcomic_include_url( 'webcomic-small.png' ) );
	$library  = add_submenu_page( __FILE__, __( 'WebComic Library', 'webcomic'), __( 'Library', 'webcomic' ),'upload_files',__FILE__,'comic_page_library' );
	$chapters = add_submenu_page( __FILE__, __( 'WebComic Chapters', 'webcomic'), __( 'Chapters', 'webcomic' ), 'manage_categories', 'comic-chapters', 'comic_page_chapters' );
	$settings = add_submenu_page( __FILE__, __( 'WebComic Settings', 'webcomic'), __( 'Settings', 'webcomic' ), 'manage_options', 'comic-settings', 'comic_page_settings' );
	
	add_meta_box( 'webcomic', __( 'WebComic', 'webcomic' ), 'comic_post_meta_box', 'post', 'normal', 'high' );
	add_meta_box( 'webcomic', __( 'WebComic', 'webcomic' ), 'comic_page_meta_box', 'page', 'normal', 'high' );
	
	$help[0] = '<a href="http://maikeruon.com/wcib/documentation/webcomic/library/" target="_blank">' . __( 'Library Documentation', 'webcomic' ) . '</a>';
	$help[1] = '<a href="http://maikeruon.com/wcib/documentation/webcomic/library/" target="_blank">' . __( 'Chapters Documentation', 'webcomic' ) . '</a>';
	$help[2] = '<a href="http://maikeruon.com/wcib/documentation/webcomic/library/" target="_blank">' . __( 'Settings Documentation', 'webcomic' ) . '</a>';
	
	foreach ( $help as $key => $value )
		$help[ $key ] .= '<br /><a href="http://maikeruon.com/wcib/documentation/webcomic/" target="_blank">' . __( 'WebComic Documentation', 'webcomic' ) . '</a><br /><a href="http://maikeruon.com/wcib/forum/" target="_blank">' . __( 'WebComic &amp; InkBlot Support Forum', 'webcomic' ) . '</a>';
	
	add_contextual_help( $library, $help[0] );
	add_contextual_help( $chapters, $help[1] );
	add_contextual_help( $settings, $help[2] );
} add_action( 'admin_menu', 'comic_admin_pages_add' );

/**
 * Adds contextual help links to various administrative pages.
 * 
 * @package WebComic
 * @since 2.0.0
 */
function webcomic_contextual_help_list( $_wp_contextual_help ) {
	$help = '<p><a href="http://maikeruon.com/wcib/documentation/webcomic/metaboxes/" target="_blank">' . __( 'Using the WebComic Metabox', 'webcomic' ) . '</a><br /><a href="http://maikeruon.com/wcib/documentation/webcomic/" target="_blank">' . __( 'WebComic Documentation', 'webcomic' ) . '</a><br /><a href="http://maikeruon.com/wcib/forum/" target="_blank">' . __( 'WebComic &amp; InkBlot Support Forum', 'webcomic' ) . '</a></p>';
	
	if ( $_wp_contextual_help[ 'post' ] )
		$_wp_contextual_help[ 'post' ] .= $help;
		
	if ( $_wp_contextual_help[ 'page' ] )
		$_wp_contextual_help[ 'page' ] .= $help;
	
	return $_wp_contextual_help;
} add_filter( 'contextual_help_list', 'webcomic_contextual_help_list' );

/**
 * Registers plugin settings and ensures the correct javascript files are enqueued.
 * 
 * @package WebComic
 * @since 1.0.0
 */
function webcomic_admin_init() {
	register_setting( 'webcomic_options', 'comic_press_compatibility' );
	register_setting( 'webcomic_options', 'comic_category' );
	register_setting( 'webcomic_options', 'comic_directory' );
	register_setting( 'webcomic_options', 'comic_feed_size' );
	register_setting( 'webcomic_options', 'comic_name_format' );
	register_setting( 'webcomic_options', 'comic_name_format_date' );
	register_setting( 'webcomic_options', 'comic_secure_paths' );
	register_setting( 'webcomic_options', 'comic_secure_names' );
	register_setting( 'webcomic_options', 'comic_post_draft' );
	register_setting( 'webcomic_options', 'comic_feed' );
	register_setting( 'webcomic_options', 'comic_transcripts_allowed' );
	register_setting( 'webcomic_options', 'comic_transcripts_required' );
	register_setting( 'webcomic_options', 'comic_transcripts_loggedin' );
	register_setting( 'webcomic_options', 'comic_thumb_crop' );
	register_setting( 'webcomic_options', 'comic_thumb_size_w' );
	register_setting( 'webcomic_options', 'comic_thumb_size_h' );
	register_setting( 'webcomic_options', 'comic_medium_size_w' );
	register_setting( 'webcomic_options', 'comic_medium_size_h' );
	register_setting( 'webcomic_options', 'comic_large_size_w' );
	register_setting( 'webcomic_options', 'comic_large_size_h' );
	wp_enqueue_script( 'admin-forms' );
} add_action( 'admin_init', 'webcomic_admin_init' );

/**
 * Adds a comic post to the specified chapter.
 * 
 * This function adds a post to the specified chapter taxonomy.
 * It first checks to see if the specified post is actually
 * assigned to the defined comic category and, if so, assigns
 * it to the specified chapter.
 * 
 * @package WebComic
 * @since 1.0.0
 * 
 * @param int $id Post ID.
 * @param int $chapter Chapter ID.
 * @param bool $overwrite Overwrites a posts existing chapter, if any.
 */
function add_post_to_chapter( $id, $chapter, $overwrite = 1 ) {
	if ( get_post_comic_category( $id ) ) {
		$post_chapters = get_post_chapters( $id );
		
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
 * @package WebComic
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
 * @package WebComic
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
 * @package WebComic
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
 * @package WebComic
 * @since 2.0.0
 * 
 * @param obj $category The category object for the selected category.
 */
function webcomic_edit_category_form_pre( $category ) {
	$categories = get_comic_category( true );
	
	$cat = array_search( $category->term_id, $categories );
	
	if ( false !== $cat )
		echo '<div id="message" class="updated fade"><p>' . sprintf( __( 'This is a comic category. Changing the <strong>Category Slug</strong> will also rename the directory <a href="%s">%s</a> to match the new slug.', 'webcomic'), get_comic_directory( 'url', false, $category->term_id ), get_comic_directory( 'url', false, $category->term_id ) ) . '</p></div>';
} add_action( 'edit_category_form_pre', 'webcomic_edit_category_form_pre' );

/**
 * Adds the 'comic_directory' hidden field to the Edit Category page.
 * 
 * This function adds the 'comic_directory' hidden field to the Edit
 * Category page. When detected, WebComic copmares the existing directory
 * path to the one in 'comic_directory' and renames comic subdirectories
 * as necessary.
 * 
 * @package WebComic
 * @since 2.0.0
 * 
 * @param obj $category The category object for the selected category.
 */
function webcomic_edit_category_form( $category ) {
	$categories = get_comic_category( true );
	
	$cat = array_search( $category->term_id, $categories );
	
	if ( false !== $cat )
		echo '<input type="hidden" name="webcomic_subdirectory" value="' . get_comic_directory( 'abs', false, $category->term_id ) . '" />';
} add_action( 'edit_category_form', 'webcomic_edit_category_form' );

/**
 * Renames comic subdirectories as necessary.
 * 
 * This function compares the value of a unique hidden field
 * to an existing comic subdirectory; if the two don't match,
 * WebComic renames the old directory using the new name.
 * 
 * @package WebComic
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
 * @package WebComic
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

/**
 * Upgrades settings from previous versions of WebComic.
 * 
 * This is a run-once function that should only be called when WebComic
 * is first activated, checking to see if certain critical WebComic settings
 * need to be upgraded and upgrades them as necessary.
 * necessary.
 * 
 * @package WebComic
 * @since 2.0.0
 */
function webcomic_upgrade() {
	//1.8.0: Make sure our Comic Category has a Series, upgrading older collections as necessary, and update the 'current chapter' and 'comic category' settings
	if ( !is_array( get_option( 'comic_category' ) ) ) {
		$category    = get_comic_category();
		$collection  = get_the_collection( 'hide_empty=0depth=1' );
		
		if ( $collection ) {
			$first_series = get_term( ( int ) get_option( 'comic_category' ), 'category' );
			$new_series   = wp_insert_term( $first_series->name, 'chapter' );
			$the_series   = get_term( ( int ) $new_series[ 'term_id' ], 'chapter' );
			$chapters     = get_terms( 'chapter', array( 'hide_empty' => 0 ) );
			$collection   = get_the_collection( 'hide_empty=0' );
			
			foreach ( $chapters as $the_chapter )
				if ( !$the_chapter->parent )
					wp_update_term( $the_chapter->term_id, 'chapter', array( 'parent' => $the_series->term_id ) );
			
			foreach ( array_keys( get_object_vars( $collection ) ) as $series )
				foreach ( array_keys( get_object_vars( $collection->$series->volumes ) ) as $volume )
					foreach( array_keys( get_object_vars( $collection->$series->volumes->$volume->chapters ) ) as $chapter )
						foreach( array_keys( get_object_vars( $collection->$series->volumes->$volume->chapters->$chapter->posts ) ) as $post )
							add_post_to_chapter( $collection->$series->volumes->$volume->chapters->$chapter->posts->$post->ID, $collection->$series->volumes->$volume->chapters->$chapter->ID );
		}
		
		update_option( 'comic_current_chapter', array( get_option( 'comic_category' ) => get_option( 'comic_current_chapter' ) ) );
		update_option( 'comic_category', array( get_option( 'comic_category' ) ) );
	}
	
	// 2.0.0: Upgrade 'thumbnail' settings to 'thumb' settings replace the 'transcript_email' setting with 'transcripts_allowed'
	if ( false !== get_option( 'comic_thumbnail_size_h' ) || false !== get_option( 'comic_thumbnail_size_w' ) || false !== get_option('comic_thumbnail_crop') || get_option( 'comic_transcript_email' ) ) {
		update_option( 'comic_thumb_size_h', get_option( 'comic_thumbnail_size_h' ) );
		update_option( 'comic_thumb_size_w', get_option( 'comic_thumbnail_size_w' ) );
		update_option( 'comic_thumb_crop', get_option( 'comic_thumbnail_crop' ) );
		update_option( 'comic_transcripts_allowed', '1' );
		delete_option( 'comic_thumbnail_size_w' );
		delete_option( 'comic_thumbnail_size_h' );
		delete_option( 'comic_thumbnail_crop' );
		delete_option( 'comic_transcript_email' );
	}
}
?>
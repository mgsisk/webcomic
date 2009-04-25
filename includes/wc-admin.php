<?php
/**
 * Contains various administration related functions.
 * 
 * @package WebComic
 * @since 1.0.0
 */

/**
 * Registers the WebComic administrative pages.
 * 
 * @package WebComic
 * @since 1.0.0
 */
function comic_admin_pages_add() {
	load_webcomic_domain();
	
	add_menu_page( __( 'WebComic', 'webcomic' ), __( 'WebComic', 'webcomic' ), 'upload_files', __FILE__, 'comic_page_library', plugins_url( 'webcomic/includes/webcomic-small.png' ) );
	add_submenu_page( __FILE__, __( 'Comic Library', 'webcomic'), __( 'Library', 'webcomic' ),'upload_files',__FILE__,'comic_page_library' );
	add_submenu_page( __FILE__, __( 'Comic Chapters', 'webcomic'), __( 'Chapters', 'webcomic' ), 'manage_categories', 'comic-chapters', 'comic_page_chapters' );
	add_submenu_page( __FILE__, __( 'Comic Settings', 'webcomic'), __( 'Settings', 'webcomic' ), 'manage_options', 'comic-settings', 'comic_page_settings' );
	
	if ( function_exists( 'add_meta_box' ) ) {
		add_meta_box( 'webcomic', __( 'WebComic', 'webcomic' ), 'comic_post_meta_box', 'post', 'normal', 'high' );
		add_meta_box( 'webcomic', __( 'WebComic', 'webcomic' ), 'comic_page_meta_box', 'page', 'normal', 'high' );
	}
} add_action( 'admin_menu', 'comic_admin_pages_add' );

/**
 * Registers plugin settings and ensures the correct javascript files are enqueued.
 * 
 * @package WebComic
 * @since 1.0.0
 */
function webcomic_admin_init() {
	register_setting( 'webcomic_options', 'comic_category' );
	register_setting( 'webcomic_options', 'comic_directory' );
	register_setting( 'webcomic_options', 'comic_feed_size' );
	register_setting( 'webcomic_options', 'comic_name_format' );
	register_setting( 'webcomic_options', 'comic_name_format_date' );
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
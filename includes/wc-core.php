<?php
/**
 * Contains all of the new template tags provided by Webcomic.
 * 
 * @package Webcomic
 * @since 1.0
 */
//
// Post Control
//
 
/**
 * Generates a new WP_Query object containing only posts in the designated comic category.
 * 
 * Be default, this function generates a new WP_Query object containing a single post
 * from the specified comic category. If the $include parameter is unset, comic_loop
 * will include comic posts from all series.
 * 
 * @package Webcomic
 * @since 1.0.0
 * 
 * @param int $number The number of posts to return.
 * @param str $include Comma separated list of category ID's to include.
 * @param str $query Appended to the query string used to generate the new WP_Query object.
 * @return object New WordPress query object.
 */
function comic_loop( $number = 1, $include = false, $query = false ) {
	global $wp_query;
	
	$include = ( $include ) ? $include : get_comic_category( true, 'include' );
	
	$comics = new WP_Query;
	$comics->query( 'posts_per_page=' . $number . '&cat=' . $include . $query );
	
	return $comics;
}

/**
 * Removes the specified comic category from a standard loop.
 * 
 * This function will remove the specified comic category (and all associated posts)
 * from the regular WordPress loop. It can also generate an entirely new WP_Query
 * object which ignores the specified comic category. If the $exclude parameter
 * is unset, ignore_comics will remove all comic posts from the standard loop. If the
 * $number parameter is set, ignore_comics returns a new WP_query Object.
 * 
 * @package Webcomic
 * @since 1.0.0
 * 
 * @param int $number The number of posts to return in a new WP_Query object.
 * @param str $exclude Comma separated list of category ID's to exclude.
 * @param str $query Appended to the query string used to generate the new WP_Query object.
 * @return object New WordPress query object (only if $number has been set).
 */
function ignore_comics( $number = false, $exclude = false, $query = false ) {
	global $paged;
	
	$exclude = ( $exclude ) ? $exclude : get_comic_category( true, 'exclude' );
	
	if ( $number ) {
		$new_query = new WP_Query;
		$new_query->query( 'posts_per_page=' . $number . '&cat=' . $exclude . $query);
		return $new_query;
	}
	
	query_posts( 'cat=' . $exclude . '&paged=' . $paged . $query );
}

/**
 * Checks if the current post is contained within a specified comic category.
 * 
 * This function checks if the current post is contained in any defined
 * comic category. The optional $id parameter allows checking for a specific
 * comic category. Must be used within The Loop.
 * 
 * @package Webcomic
 * @since 1.8.0
 * 
 * @uses get_post_comic_category()
 * 
 * @param int|str|array $category. Category ID, name or slug, or array of said.
 * @param int|obj Post to check instead of the current post.
 * @return bool True if the current post is in any of the given categories.
 */
function in_comic_category( $category = false, $_post = null ) {
	$category = ( $category ) ? $category : get_comic_category( true );

	if ( $_post )
		$_post = get_post( $_post );
	else
		$_post =& $GLOBALS[ 'post' ];

	if ( !$_post )
		return;

	$r = is_object_in_term( $_post->ID, 'category', $category );
	
	if ( is_wp_error( $r ) )
		return;
	
	return $r;
}

/**
 * Checks if the current post is contained within a specified comic chapter.
 * 
 * This function checks if the current post is contained in any defined
 * comic chapter. The optional $id parameter allows checking for a specific
 * comic chapter. Must be used within The Loop.
 * 
 * @package Webcomic
 * @since 1.8.0
 * 
 * @uses get_post_comic_category()
 * 
 * @param int|str|array $category. Category ID, name or slug, or array of said.
 * @param int|obj Post to check instead of the current post.
 * @return bool True if the current post is in any of the given chapters.
 */
function in_comic_chapter( $chapter = false, $_post = null ) {
	if ( empty( $chapter ) ) {
		if ( get_post_comic_chapters() )
			return true;
		else
			return;
	}
	
	if ( $_post )
		$_post = get_post( $_post );
	else
		$_post =& $GLOBALS[ 'post' ];
	
	if ( !$_post )
		return;
	
	$r = is_object_in_term( $_post->ID, 'chapter', $chapter );
	
	if ( is_wp_error( $r ) )
		return;
	
	return $r;
}



//
// Data Retrieval
//

/**
 * Returns comic information associated with the specified post.
 * 
 * The $id parameter is optional when this funciton is used inside a WordPress Loop.
 * If left unset, get_the_comic will attempt to retrieve comic information associated
 * with the current post.
 * 
 * The $limit parameter is only used when $id is one of 'first', 'previous', 'next',
 * 'last', or 'random' and should be a valid category ID or one of 'chapter' or 'volume'.
 * 
 * The $chapter parameter is only used when $limit is one of 'chapter' or 'volume' and
 * should be a valid chapter ID.
 * 
 * When used outside a WordPress Loop and $id is 'random' and no $limit is set get_the_comic
 * will randomly select from the list of available comic categories.
 * 
 * @package Webcomic
 * @since 1.0.0
 * 
 * @param int|str $id Post ID or one of 'first', 'previous', 'next', 'last', or 'random'.
 * @param int|str $limit Category ID or one of 'chapter', 'volume', or 'series'.
 * @param int $chapter Chapter ID.
 * @return object Object containing various comic information.
 */
function get_the_comic( $id = false, $limit = false, $chapter = false ) {
	global $wp_query, $wpdb, $post;
	
	if ( $id ) {
		$new_post = $id;
		
		switch ( $id ) {
			case 'first':
				$order = ' p.post_date ASC'; break;
			case 'last':
				$order = ' p.post_date DESC'; break;
			case 'previous':
				$order = ' p.post_date DESC LIMIT 1';
				$op    =  " p.post_date < '$post->post_date' AND"; break;
			case 'next':
				$order = ' p.post_date ASC LIMIT 1';
				$op    =  " p.post_date > '$post->post_date' AND"; break;
			case 'random':
				$order = ' RAND()'; break;
		}
		
		if ( $order ) {
			$taxonomy = ( $limit && is_string( $limit ) ) ? 'chapter' : 'category';
						
			if ( 'chapter' == $taxonomy ) {
				if ( $chapter )
					$tax_id = $chapter;
				elseif ( in_comic_chapter() )
					$tax_id = get_post_comic_chapters()->$limit->term_id;
			}
			
			if ( 'category' == $taxonomy || !$tax_id ) {
				if ( $limit && is_numeric( $limit ) )
					$tax_id = $limit;
				elseif ( 'random' == $id && !$wp_query->in_the_loop )
					$tax_id = get_comic_category( true, 'random' );
				elseif ( in_comic_category() )
					$tax_id = get_post_comic_category();
				else
					return; //No taxonomy information could be found
			}
			
			$join = " AND tt.taxonomy = '$taxonomy' AND tt.term_id IN ($tax_id)";
			
			$new_post = $wpdb->get_var( "SELECT p.id FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id $join WHERE $op p.post_type = 'post' AND p.post_status = 'publish' ORDER BY $order" );
		}
		
		$comic_post = &get_post( $new_post );
	} elseif ( in_comic_category() ) {
		$comic_post =& $post;
	} else {
		return; //Not a comic post
	}
	
	$comic_dir = ( $category ) ? $category : get_post_comic_category( $comic_post->ID );
	
	$output              = new stdClass();
	$output->ID          = ( int ) $comic_post->ID;
	$output->title       = $comic_post->post_title;
	$output->description = ( get_post_meta( $comic_post->ID, 'comic_description', true ) ) ? convert_chars( wptexturize( get_post_meta( $comic_post->ID, 'comic_description', true ) ) ) : $comic_post->post_title;
	$output->link        = get_permalink( $comic_post->ID );
	$output->class       = 'comic-item comic-item-' . $output->ID;
	
	if ( get_post_meta( $comic_post->ID, 'comic_transcript', true ) ) {
		$output->transcript        = wpautop( convert_chars( wptexturize( get_post_meta( $comic_post->ID, 'comic_transcript', true ) ) ) );
		$output->transcript_status = 'publish';
	} elseif ( get_post_meta( $comic_post->ID, 'comic_transcript_pending', true ) ) {
		$output->transcript        = get_post_meta( $comic_post->ID, 'comic_transcript_pending', true );
		$output->transcript_status = 'pending';
	} elseif ( get_post_meta( $comic_post->ID, 'comic_transcript_draft', true ) ) {
		$output->transcript        = true;
		$output->transcript_status = 'draft';
	} else {
		$output->transcript = $output->transcript_status = false;
	}
	
	if ( is_file( get_comic_directory( 'abs', false, $comic_dir ) . get_post_meta( $comic_post->ID, 'comic_file', true ) ) ) {
		$output->file        = ( get_option( 'comic_secure_paths' ) ) ? get_settings( 'home' ) . '?comic_object=' . $output->ID : get_comic_directory( 'url', false, $comic_dir ) . rawurlencode( get_post_meta( $comic_post->ID, 'comic_file', true ) );
		$output->file_name   = get_post_meta( $comic_post->ID, 'comic_file', true );
		$output->file_data   = getimagesize( get_comic_directory( 'abs', false, $comic_dir ) . $output->file_name );
		$output->flash       = ( 'application/x-shockwave-flash' == $output->file_data[ 'mime' ] ) ? true : false;
		
		if ( is_file( get_comic_directory( 'abs', true, $comic_dir ) . get_post_meta( $comic_post->ID, 'comic_large', true ) ) ) {
			$output->large      = ( get_option( 'comic_secure_paths' ) ) ? get_settings( 'home' ) . '?comic_object=' . $output->ID . '/large' : get_comic_directory( 'url', true, $comic_dir ) . rawurlencode( get_post_meta( $comic_post->ID, 'comic_large', true ) );
			$output->large_name = get_post_meta( $comic_post->ID, 'comic_large', true );
			$output->large_data = getimagesize( get_comic_directory( 'abs', true, $comic_dir ) . $output->large_name );
		}
		
		if ( is_file( get_comic_directory( 'abs', true, $comic_dir ) . get_post_meta( $comic_post->ID, 'comic_medium', true ) ) ) {
			$output->medium      = ( get_option( 'comic_secure_paths' ) ) ? get_settings( 'home' ) . '?comic_object=' . $output->ID . '/medium' : get_comic_directory( 'url', true, $comic_dir ) . rawurlencode( get_post_meta( $comic_post->ID, 'comic_medium', true ) );
			$output->medium_name = get_post_meta( $comic_post->ID, 'comic_medium', true );
			$output->medium_data = getimagesize( get_comic_directory( 'abs', true, $comic_dir ) . $output->medium_name );
		}
		
		if ( is_file( get_comic_directory( 'abs', true, $comic_dir ) . get_post_meta( $comic_post->ID, 'comic_thumb', true ) ) ) {
			$output->thumb      = ( get_option( 'comic_secure_paths' ) ) ? get_settings( 'home' ) . '?comic_object=' . $output->ID . '/thumb' : get_comic_directory( 'url', true, $comic_dir ) . rawurlencode( get_post_meta( $comic_post->ID, 'comic_thumb', true ) );
			$output->thumb_name = get_post_meta( $comic_post->ID, 'comic_thumb', true );
			$output->thumb_data = getimagesize( get_comic_directory( 'abs', true, $comic_dir ) . $output->thumb_name );
		}
	} elseif ( $comic_files = glob( get_comic_directory( 'abs', 0, $comic_dir ) . '*.*' ) ) {
		if ( get_option( 'comic_name_format_date' ) )
			$date_format = get_option( 'comic_name_format_date' );
		elseif ( get_option( 'comicpress-manager-cpm-date-format' ) )
			$date_format = get_option( 'comicpress-manager-cpm-date-format' );
		elseif ( get_option( 'stripshow_date_format' ) )
			$date_format = get_option( 'stripshow_date_format' );
		else
			$date_format = 'Y-m-d';
		
		$date_id = mysql2date( $date_format, $comic_post->post_date );
		$slug_id = $comic_post->post_name;
		$meta_id = get_post_meta( $comic_post->ID, 'comic_filename', true );
		
		foreach( array_keys( $comic_files ) as $key ) {
			if ( ( $date_id && false !== strpos( basename( $comic_files[ $key ] ), $date_id ) ) || ( $slug_id && false !== strpos( basename( $comic_files[ $key ] ), $slug_id ) ) || ( $meta_id && false !== strpos( basename( $comic_files[ $key ] ), $meta_id ) ) ) {
				$output->file      = ( get_option( 'comic_secure_paths' ) ) ? get_settings( 'home' ) . '?comic_object=' . $output->ID : get_comic_directory( 'url', 0, $comic_dir ) . rawurlencode( basename( $comic_files[ $key ] ) );
				$output->file_name = basename( $comic_files[ $key ] );
				$output->file_data = getimagesize( $comic_files[ $key ] );
				break;
			}
		}
		
		if ( $output->file ) {
			$output->fallback = true;
		
			$output->flash = ( 'application/x-shockwave-flash' == $output->file_data[ 'mime' ] ) ? true : false;
			
			if ( $comic_thumb_files = glob( get_comic_directory( 'abs', true, $comic_dir ) . '*.*' ) ) {
				$comic_thumbs = array();
			
				foreach ( array_keys( $comic_thumb_files ) as $key )
					if ( ( $date_id && false !== strpos( basename( $comic_files[ $key ] ), $date_id ) ) || ( $slug_id && false !== strpos( basename( $comic_files[ $key ] ), $slug_id ) ) || ( $meta_id && false !== strpos( basename( $comic_files[ $key ] ), $meta_id ) ) )
						array_push( $comic_thumbs, basename( $comic_thumb_files[ $key ] ) );
				
				foreach ( $comic_thumbs as $comic_thumb ) {
					if ( strpos( $comic_thumb, 'large' ) && !$output->large ) {
						$output->large      = ( get_option( 'comic_secure_paths' ) ) ? get_settings( 'home' ) . '?comic_object=' . $output->ID . '/large' : get_comic_directory( 'url', true, $comic_dir ) . $comic_thumb;
						$output->large_name = $comic_thumb;
						$output->large_data = getimagesize( get_comic_directory( 'abs', true, $comic_dir ) . $comic_thumb );
					}
					
					if ( strpos( $comic_thumb, 'medium' ) && !$output->medium ) {
						$output->medium      = ( get_option( 'comic_secure_paths' ) ) ? get_settings( 'home' ) . '?comic_object=' . $output->ID . '/medium' : get_comic_directory( 'url', true, $comic_dir ) . $comic_thumb;
						$output->medium_name = $comic_thumb;
						$output->medium_data = getimagesize( get_comic_directory( 'abs', true, $comic_dir ) . $comic_thumb );
					}
					
					if ( strpos( $comic_thumb, 'thumb' ) && !$output->thumb ) {
						$output->thumb      = ( get_option( 'comic_secure_paths' ) ) ? get_settings( 'home' ) . '?comic_object=' . $output->ID . '/thumb' : get_comic_directory( 'url', true, $comic_dir ) . $comic_thumb;
						$output->thumb_name = $comic_thumb;
						$output->thumb_data = getimagesize( get_comic_directory( 'abs', true, $comic_dir ) . $comic_thumb );
					}
				}
			}
		} else {
			$output->file = false;
		}
	}
	
	return $output;
}

/**
 * Returns a properly formatted <object> or <img> comic element.
 * 
 * This is a utility function designed to retrieve an appropriately
 * formatted <object> or <img> element from a $comic object based on
 * the specified size. 
 * 
 * If the specified size is not found for regular image comics, this
 * function will attempt to retrieve the next smallest size. If no
 * comic thumbnail can be retrieved (or no size is specified) the
 * standard comic file is used.
 * 
 * @package Webcomic
 * @since 1.7.0
 * 
 * @param object $comic A comic object generated by get_the_comic.
 * @param str $size The size of the comic image to return.
 * @param bool $secure Use secure URL's regardless of the comic_secure_path setting
 * @return str Properly formatted <object> or <img> element for use in HTML output.
 */
function get_comic_object(  $comic = false, $size = false, $secure = false ) {
	if ( !$comic || !is_object( $comic ) )
		return; //A $comic object with a file is required
	
	if ( !$comic->file )
		return $comic->title; //No comic file, return the post title instead
	
	$size = ( $size ) ? $size : 'full';
	
	if ( $comic->flash ) {
		if ( 'full' == $size )
			$dims = $comic->file_data[ 3 ];
		else
			$dims = 'height="' . get_option( 'comic_' . $size . '_size_h' ) . '" width="' . get_option( 'comic_' . $size . '_size_w' ) . '"';
		
		$output = '<object type="' . $comic->file_data[ 'mime' ] . '" data="' . $comic->file . '" ' . $dims . ' title="' . $comic->description . '" class="' . $comic->class . '"><param name="movie" value="' . $comic->file . '" /></object>';
	} else {
		if ( 'full' == $size ) {
			$size = ( $secure ) ? get_option( 'home' ) . '?comic_object=' . $comic->ID : $comic->file;
			$dims = $comic->file_data[ 3 ];
		}
		
		if ( 'large' == $size ) {
			if ( $comic->large )
				$size = ( $secure ) ? get_option( 'home' ) . '?comic_object=' . $comic->ID . '/large' : $comic->large;
			else
				$size = 'medium';
			
			$dims = ( $comic->large ) ? $comic->large_data[ 3 ] : '';
		}
		
		if ( 'medium' == $size ) {
			if ( $comic->medium )
				$size = ( $secure ) ? get_option( 'home' ) . '?comic_object=' . $comic->ID . '/medium' : $comic->medium;
			else
				$size = 'thumb';
			
			$dims = ( $comic->medium ) ? $comic->medium_data[ 3 ] : '';
		}
		
		if ( 'thumb' == $size ) {
			if ( $comic->thumb )
				$size = ( $secure ) ? get_option( 'home' ) . '?comic_object=' . $comic->ID . '/thumb' : $comic->thumb;
			else
				$size = ( $secure ) ? get_option( 'home' ) . '?comic_object=' . $comic->ID : $comic->file;
			
			$dims = ( $comic->thumb ) ? $comic->thumb_data[ 3 ] : $comic->file_data[ 3 ];
		}
		
		$output = '<img src="' . $size . '" ' . $dims . ' alt="' . $comic->title . '" title="' . $comic->description . '" class="' . $comic->class . '" />';
	}
	
	return $output;
}

/**
 * Returns buffer comic information.
 * 
 * This function retrieves information related to "buffer" comics.
 * For Webcomic's purposes a buffer comic is a post that is:
 * 
 * 1. Scheduled to post some time in the future
 * 2. Associated with a comic file
 * 
 * @package Webcomic
 * @since 2.1.0
 * 
 * @param int $series Category ID.
 * @return obj An object containing buffer comic information.
 */
function get_comic_buffer( $series = false ) {
	global $webcomic_series;
	
	$series = ( $series ) ? $series : $webcomic_series;
	
	if ( !$series )
		return; //No series defined;
	
	$posts = get_objects_in_term( $series, 'category' );
	
	if ( !$posts )
		return; //No posts
	
	foreach ( array_keys( $posts ) as $key ) {
		$_post = get_post( $posts[ $key ] );
		
		if ( 'future' == $_post->post_status && 'post' == $_post->post_type && get_the_comic( $_post->ID )->file )
			$posts_sort[] = strtotime( $_post->post_date );
	}
	
	if ( !$posts_sort )
		return; //No scheduled posts
	
	natsort( $posts_sort );
	$last = array_reverse( $posts_sort );
	
	$output            = new stdClass();
	$output->count     = ( int ) count( $last );
	$output->date      = date( get_option( 'date_format' ), $last[ 0 ] );
	$output->time      = date( get_option( 'time_format' ), $last[ 0 ] );
	$output->datetime  = date( get_option( 'date_format' ), $last[ 0 ] ) . ' @ ' . date( get_option( 'time_format' ), $last[ 0 ] );
	$output->timestamp = $last[ 0 ];
	
	return $output;
}

/**
 * Returns information associated with the specified chapter.
 * 
 * This function retrieves information related to the specified chapter
 * from Webcomic's "chapter" taxonomy. The taxonomy itself is split into
 * chapters, volumes, and series.
 * 
 * When used inside a WordPress Loop, this function returns the chapter
 * information associated with the current post (if any). Posts can only
 * be assigned to chapters, so setting the $chapter parameter to 'volume'
 * or 'series' will let the function know that we want the volume or series
 * the current post belongs to inestead of the chapter.
 * 
 * @package Webcomic
 * @since 1.0.0
 * 
 * @uses get_the_comic()
 * @uses get_post_comic_chapters()
 * 
 * @param int|str $chapter Chapter ID or one of 'chapter, 'volume', or 'series'.
 * @return object An object containing chapter information.
 */
function get_the_chapter( $id = false ) {
	global $post;
	
	$id = ( $id ) ? $id : 'chapter';
	
	if ( is_numeric( $id ) ) {
		$chapter = get_term( ( int ) $id, 'chapter' );
		
		if ( !$chapter->parent )
			$css = 'series';
		elseif ( !get_term_children( $chapter->term_id, 'chapter' ) )
			$css = 'chapter';
		else
			$css = 'volume';
	} else {
		$chapters = get_post_comic_chapters( $post->ID );
		$chapter  = $chapters->$id;
		$css      = $id;
	}
	
	if ( !$chapter || is_wp_error( $chapter ) )
		return; //No chapter could be found
		
	$output              = new stdClass();
	$output->ID          = ( int ) $chapter->term_id;
	$output->title       = $chapter->name;
	$output->slug        = $chapter->slug;
	$output->description = $chapter->description;
	$output->count       = ( int ) $chapter->count;
	$output->parent      = ( int ) $chapter->parent;
	$output->class       = 'comic-' . $css . '-item comic-' . $css . '-item-' . $chapter->term_id;
	$output->link        = get_term_link( ( int ) $chapter->term_id, 'chapter' );
	$output->feed        = $output->link . 'feed/';
	$output->first       = get_the_comic( 'first', $css, $chapter->term_id );
	$output->last        = get_the_comic( 'last', $css, $chapter->term_id );
	
	return $output;
}

/**
 * Returns all series, volume, chapter, and comic information for the entire site.
 * 
 * This function returns a multidimensional, hiearchical object with four tiers.
 * The first tier contains all of the comic series, each of which corresponds to 
 * a selected comic category.
 * 
 * Each series contains a second teir (designated by the 'volumes' property) that
 * contains all of the volume information for a given series. Each volume contains
 * a third teir (designated by the 'chapters' property) that contains all of that
 * volumes chapter information.
 * 
 * Each chapter then contains a fourth tier (designated by the 'posts' property)
 * that contains all of that chapters comic post information, as retrieved by
 * get_the_comic().
 * 
 * Accepts a string or array of any arguments get_terms() will accept, as well as
 * the additional parameters 'series', 'depth', and 'post_order'.
 * 
 * series - Comma separated list of category ID's. Defaults to 0 (all).
 * 
 * depth - How deep to function should iterate. Defaults to 4 (posts).
 * 
 * post_order - What order posts should be returned in. Defaults to 'ASC'.
 * 
 * @package Webcomic
 * @since 1.4.0
 * 
 * @uses get_the_comic()
 * @uses get_the_chapter()
 * 
 * @param str|array $args An array or string of arguments (see above).
 * @return object Multidimensional object containig series, volume, chapter, and comic information.
 */
function get_the_collection( $args = '' ) {
	$defaults = array(
		'orderby' => 'id',
		'order' => 'ASC',
		'hide_empty' => 1,
		'series' => false,
		'depth' => 4,
		'post_order' => 'ASC'
	);
	
	$args = wp_parse_args( $args, $defaults );
	
	if ( $args[ 'series' ] )
		$args[ 'series' ] = explode( ',', $args[ 'series' ] );
	
	$collection = get_terms( 'chapter', $args );
	
	if ( !$collection )
		return; //No collection could be found
	
	$series = new stdClass();
	
	foreach ( array_keys( $collection ) as $key ) {
		if ( !$collection[ $key ]->parent && ( !$args[ 'series' ] || false !== array_search( $collection[ $key ]->term_id, $args[ 'series' ] ) ) ) {
			$series->{ $collection[ $key]->term_id } = get_the_chapter( $collection[ $key ]->term_id );
			$series->{ $collection[ $key]->term_id }->volumes = new stdClass();
		} else {
			$chapters[ $collection[ $key ]->term_id ] = get_the_chapter( $collection[ $key ]->term_id );
		}
	}
	
	if ( 1 < $args[ 'depth' ] && $chapters ) {
		foreach ( array_keys( $chapters ) as $key ) {
			if ( array_key_exists( $chapters[ $key ]->parent, get_object_vars( $series ) ) ) {
				$chapters[ $key ]->chapters = new stdClass();
				$volumes[ $key ] = $chapters[ $key ];
				$series->{ $chapters[ $key ]->parent }->volumes->$key = $chapters[ $key ];
				unset( $chapters[ $key ] );
			} else {
				$chapters[ $key ]->posts = new stdClass();
			}
		}
	}
	
	if ( 2 < $args[ 'depth' ] && $volumes && $chapters ) {
		foreach ( array_keys( $chapters ) as $key) {
			if ( array_key_exists( $chapters[ $key ]->parent, $volumes ) ) {
				if (  3 < $args[ 'depth' ] ) {
					$chapter_posts = new stdClass();
					$posts_flat    = get_objects_in_term( $key, 'chapter' );
					
					if ( $posts_flat ) {
						foreach ( array_keys( $posts_flat ) as $post_key ) {
							$chapter_post = get_post( $posts_flat[ $post_key ] );
							
							if ( 'publish' == $chapter_post->post_status && 'post' == $chapter_post->post_type )
								$posts_sort[ $chapter_post->ID ] = strtotime( $chapter_post->post_date );
						}
						
						natsort( $posts_sort );
						
						$posts_order = ( 'DESC' == $args[ 'post_order' ] ) ? array_reverse( $posts_sort, true ) : $posts_sort;
						
						foreach( array_keys( $posts_order ) as $_post )
							$chapter_posts->$_post = get_the_comic( $_post );
						
						$chapters[ $key ]->posts = $chapter_posts;
					}
					
					unset( $posts_flat, $posts_sort, $posts_order, $chapter_posts );
				}
				
				$series->{ $volumes[ $chapters[ $key ]->parent ]->parent }->volumes->{ $chapters[ $key ]->parent }->chapters->$key = $chapters[ $key ];
			}
		}
	}
	
	unset( $collection, $volumes, $chapters );
	
	return $series;
}



//
// Comic Display
//

/**
 * Displays the comic associated with the current post.
 * 
 * This is a shortcut function for displaying the comic associated with the
 * current post. The comic may optionally be linked to an adjacent comic post
 * if it is not a Flash comic.
 * 
 * @package Webcomic
 * @since 1.0.0
 * 
 * @uses get_the_comic()
 * @uses get_comic_object()
 * 
 * @param str $size The size of the comic image to return.
 * @param str $link Make comic images clickable links, one of 'previous', 'next', or 'self'.
 * @param str $limit Restrict the next/previous link, one of 'chapter', 'volume', or 'series'.
 * @return bool False if not a comic post.
 */
function the_comic( $size = false, $link = false, $limit = false ) {
	if ( !in_comic_category() )
		return; //Not a comic post
	
	$comic = get_the_comic();
	
	if ( $link && !$comic->flash ) {
		if ( 'self' == $link ) {
			$before = '<a href="' . $comic->link . '">';
		} else {
			$adj_comic = get_the_comic( $link, $limit );
			$before    = '<a href="' . $adj_comic->link . '">';
		}
		
		$after     = '</a>';
	}
	
	echo $before . get_comic_object( $comic, $size ) . $after;
}

/**
 * Displays the comic embed code associated with the current post.
 * 
 * This function displays an input box with XHTML useful for sharing the
 * current comic on other websites (embed code). It must be used within a
 * WordPress Loop.
 * 
 * @package Webcomic
 * @since 1.0.0
 * 
 * @uses get_the_comic()
 * @uses get_comic_object()
 * 
 * @param str $size The size of the comic image the embed code will display, one of 'full', 'large', 'medium', or 'thumb'.
 * @param str $format The markup to use for the display code, one of 'html' or 'bbcode'.
 * @return bool False if not a comic post or $format = bbcode and the comic is a flash file.
 */
function the_comic_embed( $size = 'medium', $format = false ) {
	if ( !in_comic_category() )
		return; //Not a comic post
	
	$comic  = get_the_comic();
	$object = get_comic_object( $comic, $size, true );
	
	switch ( $format ) {
		case 'bbcode': if ( !$comic->flash ) $output = '[url=' . $comic->link . '][img]' . get_option( 'home' ) . '?comic_object=' . $comic->ID . '/' . $size . '[/img][/url]'; else return; break;
		case 'html'  : 
		default      : $output = htmlspecialchars( '<div class="embedded-comic"><p>' . $object . '</p><p><cite><a href="' . $comic->link . '" title="' . $comic->description . '">' . $comic->title . '</a> | <a href="' . get_option( 'home' ) . '" title="' . get_option( 'blogdescription' ) . '">' . get_option( 'blogname' ) . '</a></cite></p></div>' ); break;
	}
	
	echo '<input class="comic-embed-code" typ="text" readonly="readonly" value="' . $output . '" />';
}

/**
 * Displays buffer comic information.
 * 
 * This function displays various comic buffer related information.
 * 
 * @package Webcomic
 * @since 2.1.0
 * 
 * @param str $type The information to return, one of 'count', 'date', 'time', 'datetime', or 'timestamp'
 * @param int $series Category ID.
 */
function the_comic_buffer( $type = 'count', $series = false ) {
	global $webcomic_series;
	
	$series = ( $series ) ? $series : $webcomic_series;
	
	if ( $buffer = get_comic_buffer( $series ) )
		echo $buffer->$type;
}

/**
 * Displays a comic series link for the current post.
 * 
 * This function displays a category link for the comic category
 * the current post belongs to.
 * 
 * @package Webcomic
 * @since 2.1.0
 * 
 * 
 * @param str $label The text to display for the link, or one of 'full', 'large', 'medium', or 'thumb'.
 * @return bool False if not a comic post.
 */
function the_comic_series( $label = false ) {
	if ( !in_comic_category() )
		return; //Not a comic post
	
	$term_id = get_post_comic_category();
	$series  = get_term( $term_id, 'category' );
	
	if ( 'thumb' == $label || 'medium' == $label || 'large' == $label || 'full' == $label ) {
		if ( $comic->flash ) {
			$image = get_comic_object( get_the_comic( 'random', $series->ID ), $label ) . '<br />';
			$link  = $series->name;
		} else {
			$link = get_comic_object( get_the_comic( 'random', $series->ID ), $label );
		}
	} elseif ( $label ) {
		$link = $label;
	} else {
		$link = $series->name;
	}
	
	echo '<a href="' . get_term_link( $term_id, 'category' ) . '" title="' . __( 'View all posts in ', 'webcomic' ) . $series->name . '">' . $link . '</a>';
}



//
// Comic Transcripts
//

/**
 * Displays the comic transcript template.
 * 
 * This function includes the 'transcript.php' file included with a
 * theme and intended for use with Webcomic's comic transcripts. If
 * no 'transcript.php' file exists in the current theme, the default
 * template included with Webcomic is used.
 * 
 * Themes should provide their own transcript.php template, using the
 * transcript.php provided with Webcomic as a general guideline.
 *  
 * @package Webcomic
 * @since 2.0.0
 * 
 * @param str $file The template file to look for, defaults to '/transcript.php'.
 * @return False if not a comic post.
 */
function transcript_template( $file = '/transcript.php' ) {
	if ( !in_comic_category() )
		return; //Not a comic post
	
	global $user_identity, $transcript_status, $transcript_response;
	
	$req               = get_option( 'comic_transcripts_required' );
	$comic             = get_the_comic();
	$transcript        = $comic->transcript;
	$transcript_status = $comic->transcript_status;
	
	if ( $transcript_response )
		echo '<div id="transcript-response">' . $transcript_response . '</div>';
	
	if ( file_exists( TEMPLATEPATH . $file ) ) {
		require( TEMPLATEPATH . $file );
	} else {
		require( webcomic_include_url( 'transcript.php', 'abs' ) );
	}
}

/**
 * Checks the status of a transcript.
 *
 * This function checks the status of the transcript associated
 * with the current post and should only be used in the transcript
 * template file.
 * 
 * @package Webcomic
 * @since 2.1.0
 * 
 * @param str $status The transcript status, one of 'publish', 'pending', or 'draft'
 * @return True if the $status equals $transcript_status
 */
function have_transcript( $status = false ) {
	global $transcript_status;
	
	if ( $transcript_status == $status )
		return true;
}

/**
 * Displays the comic transcript form title.
 * 
 * This function displays the correct form title based on whether
 * no transcript exists (submitting a new transcript) or the author
 * is requesting improvement of an existing transcript.
 * 
 * @package Webcomic
 * @since 2.0.0
 * 
 * @param str $new The text to use for "new transcript" forms.
 * @param str $improve The text to use for "improve transcript" forms.
 * @return Returns false if $comic is not an array.
 */
function transcript_form_title( $new = false, $improve = false ) {
	global $transcript_status;
	
	if ( 'pending' == $transcript_status )
		echo ( $improve ) ? $improve : __( 'Improve Transcript', 'webcomic' );
	else
		echo ( $new ) ? $new : __( 'Submit Transcript', 'webcomic' );
}

/**
 * Displays additional necessary hidden fileds used when submitting a transcript.
 * 
 * This function outputs additional hidden fields used by the submit
 * transcript form handler, including the comic ID, comic title, the type of
 * existing transcript, a javascript "human check" field to enable auto-captcha,
 * and user information if the user is already logged in.
 * 
 * It also providese a single parameter, $captcha, that is used as a human
 * validation answer for an optional 'trans_captcha' field in the transcript form.
 * If provided, transcript_id_fields hashes the answer and then compares the hash
 * to the hash of the submitted trans_captcha field to validate human submissions.
 * 
 * @package Webcomic
 * @since 2.0.0
 * 
 * @param str $captcha The specified answer to a user-specified "captcha" question.
 */
function transcript_id_fields( $captcha = false ) {
	global $post, $user_ID, $user_identity, $user_email, $transcript_status;
	
	$type = ( 'pending' == $transcript_status ) ? 2 : 1;
	
	if ( $user_ID )
		$userfields = '
		<input type="hidden" name="trans_author" value="' . $user_identity . '" />
		<input type="hidden" name="trans_email" value="' . $user_email . '" />
		<input type="hidden" name="trans_human" value="1" />';
	elseif ( $captcha )
		$userfields = '
		<input type="hidden" name="trans_checksum" value="' . md5( $captcha ) . '" />
		<input type="hidden" name="trans_human" value="0" />';
	else
		$userfields = '
		<input type="hidden" name="trans_human" value="0" />';
	
	echo $userfields . '
		<input type="hidden" name="trans_id" value="' . $post->ID . '" />
		<input type="hidden" name="trans_type" value="' . $type . '" />
		<input type="hidden" name="trans_title" value="' . $post->post_title . '" />
		<input type="hidden" name="comic_transcript_submit" value="1" />';
}



//
// Post Navigation
//

/**
 * Displays the standard set of comic navigation links.
 * 
 * This is a shortcut function for displaying the standard set of comic
 * navigation links (first, back, next, last). Must be used within a
 * WordPress Loop.
 * 
 * @package Webcomic
 * @since 1.0.0
 * 
 * @uses first_comic_link()
 * @uses next_comic_link()
 * @uses previous_comic_link()
 * @uses last_comic_link()
 * 
 * @param str $limit Navigation link boundary, one of 'chapter or 'volume'.
 * @param str $sep The text to display between each comic link.
 * @param str $fstlabel The text to display for the first comic link.
 * @param str $prelabel The text to display for the previous comic link.
 * @param str $nxtlabel The text to display for the next comic link.
 * @param str $lstlabel The text to display for the last comic link.
 * @return bool False if not a comic post.
 */
function comics_nav_link( $args = '' ) {
	if ( !in_comic_category() )
		return; //Not a comic post
	
	$defaults = array(
		'sep'      => false,
		'limit'    => false,
		'label'    => false,
		'bookend'  => false,
		'fstlabel' => false,
		'prelabel' => false,
		'nxtlabel' => false,
		'lstlabel' => false,
		'fstbookend' => false,
		'lstbookend' => false
	);
	
	$args = wp_parse_args( $args, $defaults );
	
	if ( $args[ 'sep' ] )
		$sep = '<span class="comic-link-separator">' . $args[ 'sep' ] . '</span>';
	
	if ( $args[ 'bookend' ] )
		$args[ 'fstbookend' ] = $args[ 'lstbookend' ] = $args[ 'bookend' ];
	
	if ( $args[ 'label' ] )
		$args[ 'fstlabel' ] = $args[ 'prelabel' ] = $args[ 'nxtlabel' ] = $args[ 'lstlabel' ] = $args[ 'label' ];
	
	first_comic_link( $args[ 'fstlabel' ], $args[ 'limit' ], $args[ 'fstbookend' ] );
	echo $sep;
	previous_comic_link( $args[ 'prelabel' ], $args[ 'limit' ], $args[ 'fstbookend' ] );
	echo $sep;
	next_comic_link( $args[ 'nxtlabel' ], $args[ 'limit' ], $args[ 'lstbookend' ] );
	echo $sep;
	last_comic_link( $args[ 'lstlabel' ], $args[ 'limit' ], $args[ 'lstbookend' ] );
}

/**
 * Displays a link to the first comic.
 * 
 * This is a shortcut function for displaying a link to the first comic in
 * the current comic series, useful for building standard comic navigation.
 * 
 * If the optional $limit parameter is set, the 'first' comic changes from first
 * in the current comic series to first in the current volume or chapter. Must
 * be used within a WordPress Loop.
 * 
 * @package Webcomic
 * @since 1.0.0
 * 
 * @uses get_the_comic()
 * @uses get_comic_object()
 * @uses get_the_chapter()
 * 
 * @param str $label The text to display for the link, or one of 'full', 'large', 'medium', 'thumb', or 'title'.
 * @param int|str $limit Category ID or one of 'chapter', 'volume', or 'series'.
 * @param int $bookend Post or Page ID.
 * @return bool False if not a comic post.
 */
function first_comic_link( $label = false, $limit = false, $bookend = false ) {
	if ( !in_comic_category() )
		return; //Not a comic post
	
	load_webcomic_domain();
	
	global $post;
	
	$comic   = get_the_comic( 'first', $limit );
	$chapter = ( $limit ) ? get_the_chapter( $limit ) : false;
	
	if ( 'thumb' == $label || 'medium' == $label || 'large' == $label || 'full' == $label ) {
		if ( $comic->flash ) {
			$object = get_comic_object( $comic, $label );
			$link   = $comic->title;
		} else {
			$link = get_comic_object( $comic, $label );
		}
	} elseif ( 'title' == $label ) {
		$link = $comic->title;
	} else {
		$link = ( $label ) ? $label : __( '&laquo; First', 'webcomic' );
	}
	
	$current = ( ( $limit && $post->ID == $chapter->first->ID ) || ( $post->ID == $comic->ID ) ) ? ' current-comic' : '';
	
	if ( $current && $bookend && $permalink = get_permalink( $bookend ) ) {
		$comic->link = $permalink;
		$current     = '';
	}
	
	$class = 'first-comic-link' . $current;
	
	echo $object . '<a href="' . $comic->link . '" title="' . $comic->description . '" class="' . $class . '"><span>' . $link . '</span></a>';
}

/**
 * Displays a link to the last comic.
 * 
 * This is a shortcut function for displaying a link to the last comic in
 * the current comic series, useful for building standard comic navigation.
 * 
 * If the optional $limit parameter is set, the 'last' comic changes from last
 * in the current comic series to last in the current volume or chapter. Must
 * be used within a WordPress Loop.
 * 
 * @package Webcomic
 * @since 1.0.0
 * 
 * @uses get_the_comic()
 * @uses get_comic_object()
 * @uses get_the_chapter()
 * 
 * @param str $label The text to display for the link, or one of 'full', 'large', 'medium', 'thumb', or 'title'.
 * @param int|str $limit Category ID or one of 'chapter', 'volume', or 'series'.
 * @param int $bookend Post or Page ID.
 * @return bool False if not a comic post.
 */
function last_comic_link( $label = false, $limit = false, $bookend = false ) {
	if ( !in_comic_category() )
		return; //Not a comic post
	
	load_webcomic_domain();
	
	global $post;
	
	$comic   = get_the_comic( 'last', $limit );
	$chapter = ( $limit ) ? get_the_chapter( $limit ) : false;
	
	if ( 'thumb' == $label || 'medium' == $label || 'large' == $label || 'full' == $label ) {
		if ( $comic->flash ) {
			$object = get_comic_object( $comic, $label );
			$link   = $comic->title;
		} else {
			$link = get_comic_object( $comic, $label );
		}
	} elseif ( 'title' == $label ) {
		$link = $comic->title;
	} else {
		$link = ( $label ) ? $label : __( 'Last &raquo;', 'webcomic' );
	}
	
	$current = ( ( $limit && $post->ID == $chapter->last->ID ) || ( $post->ID == $comic->ID ) ) ? ' current-comic' : '';
	
	if ( $current && $bookend && $permalink = get_permalink( $bookend ) ) {
		$comic->link = $permalink;
		$current     = '';
	}
	
	$class = 'last-comic-link' . $current;
	
	echo $object . '<a href="' . $comic->link . '" title="' . $comic->description . '" class="' . $class . '"><span>' . $link . '</span></a>';
}

/**
 * Displays a link to the previous comic.
 * 
 * This is a shortcut function for displaying a link to the previous comic 
 * which exists in chronological order from the current comic in the current
 * comic series.
 * 
 * If the optional $limit parameter is set, 'previous' is limited to the current
 * volume or chapter. Must be used within a WordPress Loop.
 * 
 * @package Webcomic
 * @since 1.0.0
 * 
 * @uses get_the_comic()
 * @uses get_comic_object()
 * @uses get_the_chapter()
 * 
 * @param str $label The text to display for the link, or one of 'full', 'large', 'medium', 'thumb', or 'title'.
 * @param str $limit Navigation link boundary, one of 'chapter or 'volume'
 * @param int $bookend Post or Page ID.
 * @return bool False if not a comic post.
 */
function previous_comic_link( $label = false, $limit = false, $bookend = false ) {
	if ( !in_comic_category() )
		return; //Not a comic post
	
	load_webcomic_domain();
	
	global $post;
	
	$comic   = get_the_comic( 'previous', $limit );
	$chapter = ( $limit ) ? get_the_chapter( $limit ) : false;
	
	if ( 'thumb' == $label || 'medium' == $label || 'large' == $label || 'full' == $label ) {
		if ( $comic->flash ) {
			$object = get_comic_object( $comic, $label );
			$link   = $comic->title;
		} else {
			$link = get_comic_object( $comic, $label );
		}
	} elseif ( 'title' == $label ) {
		$link = $comic->title;
	} else {
		$link = ( $label ) ? $label : __( '&lsaquo; Previous', 'webcomic' );
	}
	
	$current = ( ( $limit && $post->ID == $chapter->first->ID ) || ( $post->ID == $comic->ID ) ) ? ' current-comic' : '';
	
	if ( $current && $bookend && $permalink = get_permalink( $bookend ) ) {
		$comic->link = $permalink;
		$current     = '';
	}
	
	$class = 'previous-comic-link' . $current;
	
	echo $object . '<a href="' . $comic->link . '" title="' . $comic->description . '" class="' . $class . '"><span>' . $link . '</span></a>';
}

/**
 * Displays a link to the next comic.
 * 
 * This is a shortcut function for displaying a link to the next comic 
 * which exists in chronological order from the current comic in the current
 * comic series.
 * 
 * If the optional $limit parameter is set, 'next' is limited to the current
 * volume or chapter. Must be used within a WordPress Loop.
 * 
 * @package Webcomic
 * @since 1.0.0
 * 
 * @uses get_the_comic()
 * @uses get_comic_object()
 * @uses get_the_chapter()
 * 
 * @param str $label The text to display for the link, or one of 'full', 'large', 'medium', 'thumb', or 'title'.
 * @param str $limit Navigation link boundary, one of 'chapter or 'volume'.
 * @param int $bookend Post or Page ID.
 * @return bool False if not a comic post.
 */
function next_comic_link( $label = false, $limit = false, $bookend = false ) {
	if ( !in_comic_category() )
		return; //Not a comic post
	
	load_webcomic_domain();
	
	global $post;
	
	$comic   = get_the_comic( 'next', $limit );
	$chapter = ( $limit ) ? get_the_chapter( $limit ) : false;
	
	if ( 'thumb' == $label || 'medium' == $label || 'large' == $label || 'full' == $label ) {
		if ( $comic->flash ) {
			$object = get_comic_object( $comic, $label );
			$link   = $comic->title;
		} else {
			$link = get_comic_object( $comic, $label );
		}
	} elseif ( 'title' == $label ) {
		$link = $comic->title;
	} else {
		$link = ( $label ) ? $label : __( 'Next &rsaquo;', 'webcomic' );
	}
	
	$current = ( ( $limit && $post->ID == $chapter->last->ID ) || ( $post->ID == $comic->ID ) ) ? ' current-comic' : '';
	
	if ( $current && $bookend && $permalink = get_permalink( $bookend ) ) {
		$comic->link = $permalink;
		$current     = '';
	}
	
	$class = 'next-comic-link' . $current;
	
	echo $object . '<a href="' . $comic->link . '" title="' . $comic->description . '" class="' . $class . '"><span>' . $link . '</span></a>';
}

/**
 * Displays a link to a randomly selected comic in the specified format.
 * 
 * This is a shortcut function for displaying a link to a randomly selected comic.
 * If the optional $limit parameter is not set, random_comic_link will randomly
 * select from the available comic series, or use the current comic series when
 * used in the Loop.
 * 
 * @package Webcomic
 * @since 1.0.0
 * 
 * @uses get_the_comic()
 * @uses get_comic_object()
 * 
 * @param str $label The link text or image size, one of 'thumb', 'medium', 'large', or 'full'.
 * @param int|str $limit Category ID or one of 'chapter', 'volume', or 'series'.
 * @param int $chapter Chapter ID. Required when outside the loop and $limit is 'chapter' or 'volume'.
 */
function random_comic_link( $label = false, $limit = false, $chapter = false ) {
	$comic = get_the_comic( 'random', $limit, $chapter );
	
	if ( 'thumb' == $label || 'medium' == $label || 'large' == $label || 'full' == $label ) {
		if ( $comic->flash ) {
			$image = get_comic_object( $comic, $label ) . '<br />';
			$link  = $comic->title;
		} else {
			$link = get_comic_object( $comic, $label );
		}
	} elseif ( $label ) {
		$link = $label;
	} else {
		$link = $comic->title;
	}
	
	echo $image . '<a href="' . $comic->link . '" title="' . $comic->description . '" class="random-comic-link"><span>' . $link . '</span></a>';
}

/**
 * Displays a set of comic bookmark links.
 * 
 * This function displays a set of comic bookmark links, designed to
 * allow users to save their place and then return to or clear it at
 * a later date. The bookmark differentiates between different series
 * by using the <a> "rel" attribute and setting a unique cookie based
 * on the current series.
 * 
 * The javascrip that powers this function can be found in scripts.js.
 * 
 * @package Webcomic
 * @since 2.0.0
 * 
 * @uses get_series_by_path
 * 
 * @param str $sep Text to display between each bookmark link.
 * @param str $bookmark Text to display for the 'bookmark' link.
 * @param str $return Text to display for the 'return' link.
 * @param str $clear Text to display for the 'clear' link.
 */
function bookmark_comic( $sep = false, $bookmark = false, $return = false, $clear = false ) {
	global $post;
	
	if ( is_home() || is_category( get_comic_category( true ) ) || ( is_page() && get_post_meta( $post->ID, 'comic_series', true ) ) || ( is_single() && in_comic_category() ) ) {
		if ( $sep ) $sep = '<span class="bookmark-comic-separator">' . $sep . '</span>';
		
		$bookmark = ( $bookmark ) ? $bookmark : __( 'Bookmark', 'webcomic' );
		$return   = ( $return ) ? $return : __( 'Return', ' webcomic' );
		$clear    = ( $clear ) ? $clear : __( 'Clear', 'webcomic' );
		$series   = get_series_by_path();
		
		if ( $series )
			$series = $series->term_id;
		
		echo '
		<div class="bookmark-comic">
			<a class="bookmark-this" title="' . __( 'Save your place so you can continue reading later.', 'webcomic' ) . '" rel="-series-' . $series . '"><span>' . $bookmark . '</span></a>' . $sep . '
			<a class="bookmark-goto" title="' . __( 'Return to your bookmark.', 'webcomic' ) . '" rel="-series-' . $series . '"><span>' . $return . '</span></a>' . $sep . '
			<a class="bookmark-clear" title="' . __( 'Remove your bookmark.', 'webcomic' ) . '" rel="-series-' . $series . '"><span>' . $clear . '</span></a>
		</div>';
	}
}



//
// Chapter Navigation
//

/**
 * Displays the standard set of chapter navigation links.
 * 
 * This is a shortcut function for displaying the standard set of chapter
 * navigation links (first, back, next, last).
 * 
 * @package Webcomic
 * @since 1.8.0
 * 
 * @uses first_chapter_link()
 * @uses next_chapter_link()
 * @uses previous_chapter_link()
 * @uses last_chapter_link()
 * 
 * @param str $volume Links point to series volumes instead of chapters.
 * @param str $bound Where the links should point to, one of 'first','last', or 'page'.
 * @param str $sep The text to display between each chapter link.
 * @param str $fstlabel The text to display for the first chapter link.
 * @param str $prelabel The text to display for the previous chapter link.
 * @param str $nxtlabel The text to display for the next chapter link.
 * @param str $lstlabel The text to display for the last chapter link.
 */
function chapters_nav_link( $args = '' ) {
	if ( !in_comic_category() )
		return; //Not a comic post
	
	$defaults = array(
		'sep'        => false,
		'type'       => false,
		'label'      => false,
		'bound'      => false,
		'bookend'    => false,
		'fstlabel'   => false,
		'prelabel'   => false,
		'nxtlabel'   => false,
		'lstlabel'   => false,
		'fstbookend' => false,
		'lstbookend' => false
	);
	
	$args = wp_parse_args( $args, $defaults );
	
	if ( $args[ 'sep' ] ) {
		$type = ( $args[ 'type' ] ) ? $args[ 'type' ] : 'chapter';
		$sep  = '<span class="' . $type . '-link-separator">' . $args[ 'sep' ] . '</span>';
	}
	
	if ( $args[ 'label' ] )
		$args[ 'fstlabel' ] = $args[ 'prelabel' ] = $args[ 'nxtlabel' ] = $args[ 'lstlabel' ] = $args[ 'label' ];
	
	if ( $args[ 'bookend' ] )
		$args[ 'fstbookend' ] = $args[ 'lstbookend' ] = $args[ 'bookend' ];
	
	first_chapter_link( $args[ 'fstlabel' ], $args[ 'type' ], $args[ 'bound' ], $args[ 'fstbookend' ] );
	echo $sep;
	previous_chapter_link( $args[ 'prelabel' ], $args[ 'type' ], $args[ 'bound' ], $args[ 'fstbookend' ] );
	echo $sep;
	next_chapter_link( $args[ 'nxtlabel' ], $args[ 'type' ], $args[ 'bound' ], $args[ 'lstbookend' ] );
	echo $sep;
	last_chapter_link( $args[ 'lstlabel' ], $args[ 'type' ], $args[ 'bound' ], $args[ 'lstbookend' ] );
}

/**
 * Displays a link to the first chapter of the current series.
 * 
 * This function displays a link to the first volume or chapter of the
 * current series. Like the_chapter_link(), the link itself can point
 * to the beginning or end of the chapter or the chapter archive page
 * and must be used within a WordPress Loop.
 * 
 * @package Web Comic
 * @since 1.8.0
 * 
 * @uses get_the_chapter()
 * @uses get_comic_object()
 * @uses get_post_comic_chapters()
 * @uses get_post_comic_category()
 * 
 * @param str $label The text to display for the link, or one of 'full', 'large', 'medium', 'thumb', or 'title'.
 * @param str $type The chapter link to display, one of 'chapter' or 'volume'.
 * @param str $bound Where the link should point to, one of 'first','last', or 'page'.
 */
function first_chapter_link( $label = false, $type = false, $bound = false, $bookend = false ) {
	if ( in_comic_category() && $post_chapters = get_post_comic_chapters( $post->ID ) ) {
		load_webcomic_domain();
	
		global $wpdb, $post;
	
		$type     = ( $type ) ? $type : 'chapter';
		$bound    = ( $bound ) ? $bound : 'first';
		$preview  = ( 'thumb' == $label || 'medium' == $label || 'large' == $label || 'full' == $label ) ? true : false;
		$chapters = get_post_comic_chapters( $wpdb->get_var( "SELECT p.id FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'category' AND tt.term_id IN (" . get_post_comic_category( $post->ID ) . ") WHERE p.post_type = 'post' AND p.post_status = 'publish' ORDER BY p.post_date ASC" ) );
		$chapter  = get_the_chapter( $chapters->$type->term_id );
		
		if ( 'page' == $bound ) {
			$url   = $chapter->link;
			$title = sprintf( __( 'Go to the %s archive', 'webcomic' ), $chapter->title );
			$comic = ( $preview ) ? get_comic_object( get_the_comic( 'random', $type, $chapter->ID ), $label ) : false;
		} else {
			$url   = ( 'first' == $bound ) ? $chapter->first->link : $chapter->last->link;
			$title = ( 'first' == $bound ) ? sprintf( __( 'Go to the beginning of %s', 'webcomic' ), $chapter->title ) : sprintf( __( 'Go to the end of %s', 'webcomic' ), $chapter->title );
			$comic = ( $preview ) ? get_comic_object( $chapter->$bound, $label ) : false;
		}
		
		if ( false !== strpos( $comic, '<o' ) ) {
			$object = $link . '<br />';
			$link   = $chapter->title;
		} elseif ( $comic ) {
			$link = $comic;
		} elseif ( 'title' == $label ) {
			$link = $chapter->title;
		} else {
			$link = ( $label ) ? $label : sprintf( __( '&laquo; First %s', 'webcomic' ), ucfirst( $type ) );
		}
		
		$current = ( $post_chapters->$type->term_id == $chapter->ID ) ? ' current-' . $type : '';
	
		if ( $current && $bookend && $permalink = get_permalink( $bookend ) ) {
			$url     = $permalink;
			$current = '';
		}
		
		$class = $chapter->class . ' first-' . $type . '-link' . $current;
		
		echo $object . '<a href="' . $url . '" title="' . $title . '" class="' . $class . '"><span>' . $link . '</span></a>';
	}
}

/**
 * Displays a link to the last chapter of the current series.
 * 
 * This function displays a link to the last volume or chapter of the
 * current series. Like the_chapter_link(), the link itself can point
 * to the beginning or end of the chapter or the chapter archive page
 * and must be used within a WordPress Loop.
 * 
 * @package Web Comic
 * @since 1.8.0
 * 
 * @uses get_the_chapter()
 * @uses get_comic_object()
 * @uses get_post_comic_chapters()
 * @uses get_post_comic_category()
 * 
 * @param str $label The text to display for the link, or one of 'full', 'large', 'medium', 'thumb', or 'title'.
 * @param str $type The chapter link to display, one of 'chapter' or 'volume'.
 * @param str $bound Where the link should point to, one of 'first','last', or 'page'.
 */
function last_chapter_link( $label = false, $type = false, $bound = false, $bookend = false ) {
	if ( in_comic_category() && $post_chapters = get_post_comic_chapters( $post->ID ) ) {
		load_webcomic_domain();
		
		global $wpdb, $post;
		
		$type     = ( $type ) ? $type : 'chapter';
		$bound    = ( $bound ) ? $bound : 'first';
		$preview  = ( 'thumb' == $label || 'medium' == $label || 'large' == $label || 'full' == $label ) ? true : false;
		$chapters = get_post_comic_chapters( $wpdb->get_var( "SELECT p.id FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'category' AND tt.term_id IN (" . get_post_comic_category( $post->ID ) . ") WHERE p.post_type = 'post' AND p.post_status = 'publish' ORDER BY p.post_date DESC" ) );
		$chapter  = get_the_chapter( $chapters->$type->term_id );
		
		if ( 'page' == $bound ) {
			$url   = $chapter->link;
			$title = sprintf( __( 'Go to the %s archive', 'webcomic' ), $chapter->title );
			$comic = ( $preview ) ? get_comic_object( get_the_comic( 'random', $type, $chapter->ID ), $label ) : false;
		} else {
			$url   = ( 'first' == $bound ) ? $chapter->first->link : $chapter->last->link;
			$title = ( 'first' == $bound ) ? sprintf( __( 'Go to the beginning of %s', 'webcomic' ), $chapter->title ) : sprintf( __( 'Go to the end of %s', 'webcomic' ), $chapter->title );
			$comic = ( $preview ) ? get_comic_object( $chapter->$bound, $label ) : false;
		}
		
		if ( false !== strpos( $comic, '<o' ) ) {
			$object = $link . '<br />';
			$link   = $chapter->title;
		} elseif ( $comic ) {
			$link = $comic;
		} elseif ( 'title' == $label ) {
			$link = $chapter->title;
		} else {
			$link = ( $label ) ? $label : sprintf( __( 'Last %s &raquo;', 'webcomic' ), ucfirst( $type ) );
		}
		
		$current = ( $post_chapters->$type->term_id == $chapter->ID ) ? ' current-' . $type : '';
	
		if ( $current && $bookend && $permalink = get_permalink( $bookend ) ) {
			$url     = $permalink;
			$current = '';
		}
		
		$class = $chapter->class . ' last-' . $type . '-link' . $current;
		
		echo $object . '<a href="' . $url . '" title="' . $title . '" class="' . $class . '"><span>' . $link . '</span></a>';
	}
}

/**
 * Displays a link to the previous chapter of the current series.
 * 
 * This function displays a link to the previous volume or chapter of the
 * current series. Like the_chapter_link(), the link itself can point
 * to the beginning or end of the chapter or the chapter archive page
 * and must be used within a WordPress Loop.
 * 
 * @package Web Comic
 * @since 1.8.0
 * 
 * @uses get_the_chapter()
 * @uses get_comic_object()
 * @uses get_post_comic_chapters()
 * @uses get_post_comic_category()
 * 
 * @param str $label The text to display for the link, or one of 'full', 'large', 'medium', 'thumb', or 'title'.
 * @param str $type The chapter link to display, one of 'chapter' or 'volume'.
 * @param str $bound Where the link should point to, one of 'first','last', or 'page'.
 */
function previous_chapter_link( $label = false, $type = false, $bound = false, $bookend = false ) {
	if ( in_comic_category() && $post_chapters = get_post_comic_chapters( $post->ID ) ) {
		load_webcomic_domain();
		
		global $wpdb, $post;
		
		$type     = ( $type ) ? $type : 'chapter';
		$bound    = ( $bound ) ? $bound : 'first';
		$preview  = ( 'thumb' == $label || 'medium' == $label || 'large' == $label || 'full' == $label ) ? true : false;
		$_post    = get_post( get_the_chapter( $post_chapters->$type->term_id )->first->ID );
		$chapters = get_post_comic_chapters( $wpdb->get_var( "SELECT p.id FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'category' AND tt.term_id IN (" . get_post_comic_category( $post->ID ) . ") WHERE p.post_date < '$_post->post_date' AND p.post_type = 'post' AND p.post_status = 'publish' ORDER BY p.post_date DESC LIMIT 1" ) );
		$chapter  = get_the_chapter( $chapters->$type->term_id );
		
		if ( 'page' == $bound ) {
			$url   = $chapter->link;
			$title = sprintf( __( 'Go to the %s archive', 'webcomic' ), $chapter->title );
			$comic = ( $preview ) ? get_comic_object( get_the_comic( 'random', $type, $chapter->ID ), $label ) : false;
		} else {
			$url   = ( 'first' == $bound ) ? $chapter->first->link : $chapter->last->link;
			$title = ( 'first' == $bound ) ? sprintf( __( 'Go to the beginning of %s', 'webcomic' ), $chapter->title ) : sprintf( __( 'Go to the end of %s', 'webcomic' ), $chapter->title );
			$comic = ( $preview ) ? get_comic_object( $chapter->$bound, $label ) : false;
		}
		
		if ( false !== strpos( $comic, '<o' ) ) {
			$object = $link . '<br />';
			$link   = $chapter->title;
		} elseif ( $comic ) {
			$link = $comic;
		} elseif ( 'title' == $label ) {
			$link = $chapter->title;
		} else {
			$link = ( $label ) ? $label : sprintf( __( '&lsaquo; Previous %s', 'webcomic' ), ucfirst( $type ) );
		}
		
		$current = ( $post_chapters->$type->term_id == $chapter->ID ) ? ' current-' . $type : '';
	
		if ( $current && $bookend && $permalink = get_permalink( $bookend ) ) {
			$url     = $permalink;
			$current = '';
		}
		
		$class = $chapter->class . ' previous-' . $type . '-link' . $current;
		
		echo $object . '<a href="' . $url . '" title="' . $title . '" class="' . $class . '"><span>' . $link . '</span></a>';
	}
}

/**
 * Displays a link to the next chapter of the current series.
 * 
 * This function displays a link to the next volume or chapter of the
 * current series. Like the_chapter_link(), the link itself can point
 * to the beginning or end of the chapter or the chapter archive page
 * and must be used within a WordPress Loop.
 * 
 * @package Web Comic
 * @since 1.8.0
 * 
 * @uses get_the_chapter()
 * @uses get_comic_object()
 * @uses get_post_comic_chapters()
 * @uses get_post_comic_category()
 * 
 * @param str $label The text to display for the link, or one of 'full', 'large', 'medium', 'thumb', or 'title'.
 * @param str $type The chapter link to display, one of 'chapter' or 'volume'.
 * @param str $bound Where the link should point to, one of 'first','last', or 'page'.
 */
function next_chapter_link( $label = false, $type = false, $bound = false, $bookend = false ) {
	if ( in_comic_category() && $post_chapters = get_post_comic_chapters( $post->ID ) ) {
		load_webcomic_domain();
		
		global $wpdb, $post;
		
		$type     = ( $type ) ? $type : 'chapter';
		$bound    = ( $bound ) ? $bound : 'first';
		$preview  = ( 'thumb' == $label || 'medium' == $label || 'large' == $label || 'full' == $label ) ? true : false;
		$_post    = get_post( get_the_chapter( $post_chapters->$type->term_id )->last->ID );
		$chapters = get_post_comic_chapters( $wpdb->get_var( "SELECT p.id FROM $wpdb->posts AS p INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'category' AND tt.term_id IN (" . get_post_comic_category( $post->ID ) . ") WHERE p.post_date > '$_post->post_date' AND p.post_type = 'post' AND p.post_status = 'publish' ORDER BY p.post_date ASC LIMIT 1" ) );
		$chapter  = get_the_chapter( $chapters->$type->term_id );
		
		if ( 'page' == $bound ) {
			$url   = $chapter->link;
			$title = sprintf( __( 'Go to the %s archive', 'webcomic' ), $chapter->title );
			$comic = ( $preview ) ? get_comic_object( get_the_comic( 'random', $type, $chapter->ID ), $label ) : false;
		} else {
			$url   = ( 'first' == $bound ) ? $chapter->first->link : $chapter->last->link;
			$title = ( 'first' == $bound ) ? sprintf( __( 'Go to the beginning of %s', 'webcomic' ), $chapter->title ) : sprintf( __( 'Go to the end of %s', 'webcomic' ), $chapter->title );
			$comic = ( $preview ) ? get_comic_object( $chapter->$bound, $label ) : false;
		}
		
		if ( false !== strpos( $comic, '<o' ) ) {
			$object = $link . '<br />';
			$link   = $chapter->title;
		} elseif ( $comic ) {
			$link = $comic;
		} elseif ( 'title' == $label ) {
			$link = $chapter->title;
		} else {
			$link = ( $label ) ? $label : sprintf( __( 'Next %s &rsaquo;', 'webcomic' ), ucfirst( $type ) );
		}
		
		$current = ( $post_chapters->$type->term_id == $chapter->ID ) ? ' current-' . $type : '';
	
		if ( $current && $bookend && $permalink = get_permalink( $bookend ) ) {
			$url     = $permalink;
			$current = '';
		}
		
		$class = $chapter->class . ' next-' . $type . '-link' . $current;
		
		echo $object . '<a href="' . $url . '" title="' . $title . '" class="' . $class . '"><span>' . $link . '</span></a>';
	}
}

/**
 * Displays the chapter link associated with the current post.
 * 
 * This is a shortcut function for displaying a link to the beginning, end,
 * or archive page  of the chapter, volume, or series associated with the
 * current post and must be used within a WordPress Loop.
 * 
 * @package Webcomic
 * @since 1.0.0
 * 
 * @uses get_the_chapter()
 * @uses get_comic_object()
 * @uses get_post_comic_chapters()
 * 
 * @param str $label The text to display for the link, or one of 'full', 'large', 'medium', 'thumb', or 'title'.
 * @param str $type The chapter link to display, one of 'chapter' or 'volume'.
 * @param str $bound Where the link should point to, one of 'first','last', or 'page'.
 */
function the_chapter_link( $label = false, $type = false, $bound = false ) {
	if ( in_comic_category() && $chapter = get_the_chapter( $type ) ) {
		load_webcomic_domain();
		
		global $post;
		
		$type    = ( $type ) ? $type : 'chapter';
		$bound   = ( $bound ) ? $bound : 'first';
		$preview = ( 'thumb' == $label || 'medium' == $label || 'large' == $label || 'full' == $label ) ? true : false;
		
		if ( 'page' == $bound ) {
			$url   = $chapter->link;
			$title = sprintf( __( 'Go to the %s archive', 'webcomic' ), $chapter->title );
			$comic = ( $preview ) ? get_comic_object( get_the_comic( 'random', $type, $chapter->ID ), $label ) : false;
		} else {
			$url   = ( 'first' == $bound ) ? $chapter->first->link : $chapter->last->link;
			$title = ( 'first' == $bound ) ? sprintf( __( 'Go to the beginning of %s', 'webcomic' ), $chapter->title ) : sprintf( __( 'Go to the end of %s', 'webcomic' ), $chapter->title );
			$comic = ( $preview ) ? get_comic_object( $chapter->$bound, $label ) : false;
		}
		
		if ( false !== strpos( $comic, '<o' ) ) {
			$object = $link . '<br />';
			$link  = $chapter->title;
		} elseif ( $comic ) {
			$link = $comic;
		} else {
			$link = ( $label ) ? $label : $chapter->title;
		}
		
		$class = 'current-' . $type . '-link';
		
		echo $object . '<a href="' . $url . '" title="' . $title . '" class="' . $class . '"><span>' . $link . '</span></a>';
	}
}



//
// Chapter Archives
//

/**
 * Displays the chapter title on chapter archive pages.
 * 
 * This function displays the chapter title on chapter archive pages. Like
 * single_cat_title() it accepts both $prefix and $display parameters.
 * 
 * @package Webcomic
 * @since 1.8.0
 * 
 * @param str $prefix Prefix to prepend to the chapter name.
 * @param bool $display Returns the chapter title if set to false.
 * @return str The chapter title, if $display is set to false.
 */
function single_chapter_title( $prefix = false, $display = true ) {
	$chapter = get_term_by( 'slug', get_query_var( 'chapter' ), 'chapter' );
	
	if ( $display )
		echo $prefix . $chapter->name;
	else
		return $chapter->name;
}

/**
 * Returns the specified chapter description.
 * 
 * This function returns the specified chapter description. If used on
 * a chapter archive page with no defined $chapter the current chapter
 * description is returned.
 * 
 * @package Webcomic
 * @since 1.8.0
 * 
 * @param int $id Chapter ID.
 * @return str The chapter description.
 */
function chapter_description( $id = false ) {
	return term_description( $id, 'chapter' );
}



//
// Comic Archives
//

/**
 * Displays a list of recently posted comics.
 * 
 * This is a shortcut function for displaying a list of recently posted comics
 * using comic_loop() and get_the_comic().
 * 
 * @package Webcomic
 * @since   1.0.0
 * 
 * @uses comic_loop()
 * @uses get_the_comic()
 * @uses get_comic_object()
 * 
 * @param int $number The number of comics to display.
 * @param str $label The image size, one of 'full', 'large', 'medium', or 'thumb'.
 * @param str $limit Comma separated list of category ID's.
 */
function recent_comics( $number = 5, $label = false, $limit = false ) {
	$comics = comic_loop( $number, $limit );
	
	if ( $comics->have_posts() ) : while ( $comics->have_posts() ) : $comics->the_post();
		$comic = get_the_comic();
		
		$link = ( $label ) ? get_comic_object( $comic, $label ) : $comic->title;
		
		if ( $comic->flash && $label ) {
			$object = $link . '<br />';
			$link  = $comic->title;
		}
		
		$output .= '<li>' . $object . '<a href="' . $comic->link . '" title="' . $comic->description . '" class="recent-comic recent-comic-' .  $comic->ID . '"><span>' . $link . '</span></a>' . '</li>';
	endwhile; wp_reset_query(); endif;
	
	echo $output;
}

/**
 * Displays a form select control with comic posts or chapters.
 * 
 * This function displays a form select control (dropdown box) listing comics
 * or comic chapters. The javascrip that powers this function can be found in
 * scripts.js. A number of arguments can be specified:
 * 
 * label - The text displayed on the top first, neutral select value. defaults to
 * 'Quick Archive'.
 * 
 * post_order - The order of posts. Defaults to 'DESC'.
 * 
 * number - Whether or not to prepend each post or chapter with a number.
 * Defaults to 0 (false).
 * 
 * groupby - Group posts by chapter, chapters, by volume, or volumes by series. One
 * of 'chapter', 'volume', or 'series'.
 * 
 * orderby - The field used to order chapters. Defaults to 'id'.
 * 
 * order = The order to retrieve chapters in. Defaults to 'ASC'.
 * 
 * series - Comma-separated list of series ID's to include. Defaults to false (all).
 * 
 * bound - Where chapter options should point to. One of 'first' (default),
 * 'last', or 'page'
 * 
 * pages - Whether to append chapter titles with page counts. Defaults to false.
 * 
 * @package Webcomic
 * @since 1.0.0
 * 
 * @uses comic_loop()
 * @uses get_the_collection()
 * 
 * @param str|arr $args An array or string of arguments.
 */
function dropdown_comics( $args = '' ) {
	load_webcomic_domain();
	
	$defaults = array(
		'label'      => __( 'Quick Archive', 'webcomic' ),
		'pages'      => false,
		'order'      => 'ASC',
		'bound'      => 'first',
		'number'     => false,
		'series'     => false,
		'groupby'    => false,
		'orderby'    => 'id',
		'post_order' => 'DESC'
	);
	
	$args = wp_parse_args( $args, $defaults );
	
	$output = '<select name="comic-select" class="dropdown-comics"><option value="0">' . $args[ 'label' ] . '</option>';
	
	if ( !$args[ 'groupby' ] ) {
		$comics = comic_loop( -1, $args[ 'series' ], '&order=' . $args[ 'post_order' ] );
		
		if ( $args[ 'number' ] ) {
			$i = ( 'ASC' == $args[ 'post_order' ] ) ? 1 : $comics->post_count;
			$p = '.';
		}
		
		if ( $comics->have_posts() ) : while( $comics->have_posts() ) : $comics->the_post();
			$output .= '<option value="' . get_permalink() . '">' . $i . $p . get_the_title() . '</option>';
			
			if ( $args[ 'number' ] ) $i = ( 'ASC' == $args[ 'post_order' ] ) ? $i + 1 : $i - 1;
		endwhile; wp_reset_query(); endif;
	} else {
		switch( $args[ 'groupby' ] ) {
			case 'series' : $args[ 'depth' ] = '2'; break;
			case 'volume' : $args[ 'depth' ] = '3'; break;
			default       : $args[ 'depth' ] = '4';
		}
		
		$collection = get_the_collection( 'order=' . $args[ 'order' ] . '&orderby=' . $args[ 'orderby' ] . '&series=' . $args[ 'series' ] . '&post_order=' . $args[ 'post_order' ] . '&depth=' . $args[ 'depth' ] );
		
		if ( $collection ) {
			foreach ( array_keys( get_object_vars( $collection ) ) as $series ) {
				if ( 'series' == $args[ 'groupby' ] ) {
					$append  = ( $args[ 'pages' ] ) ? ' (' . $collection->$series->count . ')' : '';
					$output .= '<optgroup label="' . $collection->$series->title . $append . '">';
					
					if ( $args[ 'number' ] ) {
						$i = ( 'DESC' == $args[ 'order' ] ) ? 1 : count( $collection->$series->volumes );
						$p = '.';
					}
					
					foreach ( array_keys( get_object_vars( $collection->$series->volumes ) ) as $volume ) {
						$append  = ( $args[ 'pages' ] ) ? ' (' . $collection->$series->volumes->$volume->count . ')' : '';
						
						switch ( $args[ 'bound' ] ) {
							case 'page' : $link = $collection->$series->volumes->$volume->link; break;
							case 'last' : $link = $collection->$series->volumes->$volume->last->link;break;
							default     : $link = $collection->$series->volumes->$volume->first->link;
						}
						
						$output .= '<option value="' . $link . '">' . $i . $p . $collection->$series->volumes->$volume->title . $append . '</option>';
						
						if ( $args[ 'number' ] ) $i = ( 'DESC' == $args[ 'order' ] ) ? $i - 1 : $i + 1;
					}
					
					$output .= '</optgroup>';
				} elseif ( 'volume' == $args[ 'groupby' ] ) {
					foreach ( array_keys( get_object_vars( $collection->$series->volumes ) ) as $volume ) {
						$append  = ( $args[ 'pages' ] ) ? ' (' . $collection->$series->volumes->$volume->count . ')' : '';
						$output .= '<optgroup label="' . $collection->$series->volumes->$volume->title . $append . '">';
						
						if ( $args[ 'number' ] ) {
							$i = ( 'DESC' == $args[ 'order' ] ) ? 1 : count( $collection->$series->volumes->$volume->chapters );
							$p = '.';
						}
						
						foreach ( array_keys( get_object_vars( $collection->$series->volumes->$volume->chapters ) ) as $chapter ) {
							$append  = ( $args[ 'pages' ] ) ? ' (' . $collection->$series->volumes->$volume->chapters->$chapter->count . ')' : '';
							
							switch ( $args[ 'bound' ] ) {
								case 'page': $link = $collection->$series->volumes->$volume->chapters->$chapter->link; break;
								case 'last': $link = $collection->$series->volumes->$volume->chapters->$chapter->last->link;break;
								default:     $link = $collection->$series->volumes->$volume->chapters->$chapter->first->link;
							}
							
							$output .= '<option value="' . $link . '">' . $i . $p . $collection->$series->volumes->$volume->chapters->$chapter->title . $append . '</option>';
							
							if ( $args[ 'number' ] ) $i = ( 'DESC' == $args[ 'order' ] ) ? $i - 1 : $i + 1;
						}
						
						$output .= '</optgroup>';
					}
				} elseif ( 'chapter' == $args[ 'groupby' ] ) {
					foreach ( array_keys( get_object_vars( $collection->$series->volumes ) ) as $volume ) {
						foreach ( array_keys( get_object_vars( $collection->$series->volumes->$volume->chapters ) ) as $chapter ) {
							$append  = ( $args[ 'pages' ] ) ? ' (' . $collection->$series->volumes->$volume->chapters->$chapter->count . ')' : '';
							$output .= '<optgroup label="' . $collection->$series->volumes->$volume->chapters->$chapter->title . $append . '">';
							
							if ( $args[ 'number' ] ) {
								$i = ( 'ASC' == $args[ 'post_order' ] ) ? 1 : count( $collection->$series->volumes->$volume->chapters->$chapter->posts );
								$p = '.';
							}
							
							foreach ( array_keys( get_object_vars( $collection->$series->volumes->$volume->chapters->$chapter->posts ) ) as $post ) {
								$output .= '<option value="' . $collection->$series->volumes->$volume->chapters->$chapter->posts->$post->link . '">' . $i . $p . $collection->$series->volumes->$volume->chapters->$chapter->posts->$post->title . '</option>';
								
								if ( $args[ 'number' ] ) $i = ( 'ASC' == $args[ 'post_order' ] ) ? $i + 1 : $i - 1;
							}
							
							$output .= '</optgroup>';
						}
					}
				}
			}
		}
		
		unset( $collection );
	}
	
	$output .= '</select>';
	
	echo $output;
}

/**
 * Displays the comic archive in the specified format organized in the specified way.
 * 
 * This is a very flexible function for display comic archives organized either by date
 * or chapter (storyline). A number of arguments can be specified to control the output:
 * 
 * groupby - Group posts by year, month, and day or series, volume, and chapter, one of 'date' or 'chapter'.
 * 
 * format - How to display individual comic links. One of 'number' (display as a page number),
 * 'full', 'large', 'medium', or 'thumb' (for images), or false (use the post title).
 * 
 * series - Comma-separated list of series ID's to include. Defaults to 0 (all).
 * 
 * post_order - The order of posts. Defaults to 'DESC'.
 * 
 * orderby - The field used to order chapters. Defaults to 'id'.
 * 
 * order - The order to retrieve chapters in. Defaults to 'ASC'.
 * 
 * depth - Sets the depth parameter for storline archives. Defaults to 4.
 * 
 * bound - Where series, volume, and chapter link should point to. One of
 * 'first' (default), 'last', or 'page'
 * 
 * descriptions - Whether to display series, volume, and chapter descriptions.
 * Defaults to false.
 * 
 * pages - Whether to display series, volume, and chapter page counts.
 * Defaults to false.
 * 
 * @package Webcomic
 * @since 1.0.0
 * 
 * @uses comic_loop()
 * @uses get_the_comic()
 * @uses get_the_collection()
 * 
 * @param str|arr $args An array or string of arguments.
 */
function comic_archive( $args = '' ) {
	$defaults = array(
		'order'        => 'ASC',
		'depth'        => 4,
		'bound'        => 'first',
		'pages'        => false,
		'series'       => false,
		'format'       => false,
		'groupby'      => 'date',
		'orderby'      => 'id',
		'post_order'   => 'DESC',
		'descriptions' => false
	);
	
	$args = wp_parse_args( $args, $defaults );
	
	if ( 'date' == $args[ 'groupby' ] ) {
		$comics = comic_loop( -1, $args[ 'series' ], '&order=' . $args[ 'post_order' ] );
		
		if ( $comics->have_posts() ) : while( $comics->have_posts() ) : $comics->the_post();
			if ( $the_year != get_the_time( 'Y' ) ) {
				$i = 0;
				$the_month = 0;
				
				if ( $the_year )
					$output .= ( $args[ 'format' ] ) ? '</td></tr></table>' : '</table>';
				
				$the_year = get_the_time( 'Y' );
				$output  .= '<div class="comic-year comic-year-' . $the_year . '"><span>' . $the_year . '</span></div><table class="comic-archive comic-archive-' . $the_year . '"><colgroup><col class="comic-date-col" /><col /></colgroup>';
			}
			
			$tr_class = ( !( $i % 2 ) ) ? ' class="alt"' : '';
			
			if ( $args[ 'format' ] && 'number' != $args[ 'format' ]) {
				if ( $the_month != get_the_time( 'm' ) ) {
					if ( $the_month )
						$output .= '</td></tr>';
					
					$output .= '<tr' . $tr_class . '><th class="comic-archive-month comic-archive-month-' . $the_month . '"><span>' . get_the_time( 'F' ) . '</span></th><td class="comic-archive-days">';
					
					$the_month = get_the_time( 'm' );
					
					$i++;
				}
				
				$comic = get_the_comic();
				$link  = get_comic_object( $comic, $args[ 'format' ] );
				
				if ( $comic->flash ) {
					$image = $link;
					$link = $comic->title;
				}
				
				$output .= $image . '<a href="' . $comic->link . '" title="' . $comic->description . '">' . $link . '</a>';
			} else {
				$output .= '
				<tr' . $tr_class . '>
					<th scope="row" class="comic-archive-date comic-archive-date-' . get_the_time( 'md' ) . '"><span>' . get_the_time( 'F jS' ) . '</span></th>
					<td class="comic-archive-item comic-archive-item-' . get_the_id() . '"><a href="' . get_permalink() . '" rel="bookmark" title="' . sprintf( __( 'Permanent Link to %s', 'webcomic' ), the_title_attribute( 'echo=0' ) ) . '"><span>' . the_title_attribute( 'echo=0' ) . '</span></a></td>
				</tr>';
			
				$i++;
			}
		endwhile; $output .= ( $args[ 'format' ] ) ? '</td></tr></table>' : '</table>'; wp_reset_query(); endif;
	} elseif ( 'chapter' == $args[ 'groupby' ] ) {
		$collection = get_the_collection( 'order=' . $args[ 'order' ] . '&orderby=' . $args[ 'orderby' ] . '&series=' . $args[ 'series' ] . '&post_order=' . $args[ 'post_order' ] . '&depth=' . $args[ 'depth' ] );
		
		if ( $collection ) {
			$output = '<ol class="comic-archive">';
			
			foreach ( array_keys( get_object_vars( $collection ) ) as $series ) {
				if ( 1 < count( $colleciton ) ) {
					$description = ( $args[ 'descriptions' ] && $collection->$series->description ) ? '<p>' . $collection->$series->description . '</p>' :'';
					$pages       = ( $args[ 'pages' ] ) ? ' (' . $collection->$series->count . ')' : '';
				
					switch( $args[ 'bound' ] ) {
						case 'page':
							$link  = $collection->$series->link;
							$title = sprintf( __( 'Go to the %s archive', 'webcomic' ), $collection->$series->title );
						break;
						case 'last':
							$link  = $collection->$series->last->link;
							$title = sprintf( __( 'Go to the end of %s', 'webcomic' ), $collection->$series->title );
						break;
						default:
							$link  = $collection->$series->first->link;
							$title = sprintf( __( 'Go to the beginning of %s', 'webcomic'), $collection->$series->title );
					}
				
					$output .= '<li class="' . $collection->$series->class . '"><a href="' . $link . '" title="' . $title . '"><span>' . $collection->$series->title . $pages . '</span></a>' . $description . '<ol class="comic-series-volumes">';
				}
				
				foreach ( array_keys( get_object_vars( $collection->$series->volumes ) ) as $volume ) {
					$description = ( $args[ 'descriptions' ] && $collection->$series->volumes->$volume->description ) ? '<p>' . $collection->$series->volumes->$volume->description . '</p>' : '';
					$pages       = ( $args[ 'pages' ] ) ? ' (' . $collection->$series->volumes->$volume->count . ')' : '';
					
					switch( $args[ 'bound' ] ) {
						case 'page':
							$link  = $collection->$series->volumes->$volume->link;
							$title = sprintf( __( 'Go to the %s archive', 'webcomic' ), $collection->$series->volumes->$volume->title );
						break;
						case 'last':
							$link  = $collection->$series->volumes->$volume->last->link;
							$title = sprintf( __( 'Go to the end of %s', 'webcomic' ), $collection->$series->volumes->$volume->title );
						break;
						default:
							$link  = $collection->$series->volumes->$volume->first->link;
							$title = sprintf( __( 'Go to the beginning of %s', 'webcomic'), $collection->$series->volumes->$volume->title );
					}
					
					$output .= '<li class="' . $collection->$series->volumes->$volume->class . '"><a href="' . $link . '" title="' . $title . '"><span>' . $collection->$series->volumes->$volume->title . $pages . '</span></a>' . $description . '<ol class="comic-volume-chapters">';
						
					foreach ( array_keys( get_object_vars( $collection->$series->volumes->$volume->chapters ) ) as $chapter ) {
						$description = ( $args[ 'descriptions' ] && $collection->$series->volumes->$volume->chapters->$chapter->description ) ? '<p>' . $collection->$series->volumes->$volume->chapters->$chapter->description . '</p>' : '';
						$pages       = ( $args[ 'pages' ] ) ? ' (' . $collection->$series->volumes->$volume->chapters->$chapter->count . ')' : '';
						
						if ( 'number' == $args[ 'format' ] )
							$num = ( 'ASC' ==  $args[ 'post_order' ] ) ? 1 : count( get_object_vars( $collection->$series->volumes->$volume->chapters->$chapter->posts ) );
						
						switch( $args[ 'bound' ] ) {
							case 'page':
								$link  = $collection->$series->volumes->$volume->chapters->$chapter->link;
								$title = sprintf( __( 'Go to the %s archive', 'webcomic' ), $collection->$series->volumes->$volume->chapters->$chapter->title );
							break;
							case 'last':
								$link  = $collection->$series->volumes->$volume->chapters->$chapter->last->link;
								$title = sprintf( __( 'Go to the end of %s', 'webcomic' ), $collection->$series->volumes->$volume->chapters->$chapter->title );
							break;
							default:
								$link  = $collection->$series->volumes->$volume->chapters->$chapter->first->link;
								$title = sprintf( __( 'Go to the beginning of %s', 'webcomic'), $collection->$series->volumes->$volume->chapters->$chapter->title );
						}
						
						$output .= '<li class="' . $collection->$series->volumes->$volume->chapters->$chapter->class . '"><a href="' . $link . '" title="' . $title . '"><span>' . $collection->$series->volumes->$volume->chapters->$chapter->title . $pages . '</span></a>' . $description . '<ol class="comic-chapter-pages">';
						
						foreach ( array_keys( get_object_vars( $collection->$series->volumes->$volume->chapters->$chapter->posts ) ) as $post ) {
							if ( 'number' == $args[ 'format' ] ) {
								$link = $num;
							} elseif ( $args[ 'format' ] ) {
								$link = get_comic_object( $collection->$series->volumes->$volume->chapters->$chapter->posts->$post, $args[ 'format' ] );
								
								if ( $collection->$series->volumes->$volume->chapters->$chapter->posts->$post->flash ) {
									$object = $link;
									$link = $collection->$series->volumes->$volume->chapters->$chapter->posts->$post->title;
								}
							} else {
								$link = $collection->$series->volumes->$volume->chapters->$chapter->posts->$post->title;
							}
							
							$output .= '<li class="comic-page-item comic-page-item-' . $collection->$series->volumes->$volume->chapters->$chapter->posts->$post->ID . '">' . $object . '<a href="' . $collection->$series->volumes->$volume->chapters->$chapter->posts->$post->link . '" title="' . $collection->$series->volumes->$volume->chapters->$chapter->posts->$post->description . '">' . $link . '</a></li>';
							
							if ( 'number' == $args[ 'format' ] )
								$num = ( 'ASC' ==  $args[ 'post_order' ] ) ? $num + 1 : $num - 1;
						}
						
						$output .= '</ol></li>';
					}
						
					$output .= '</ol></li>';
				}
				
				if ( 1 < count( $colleciton ) )
					$output .= '</ol></li>';
			}
			
			$output .= '</ol>';
		}
		
		unset( $collection );
	}
	
	echo $output;
}
?>
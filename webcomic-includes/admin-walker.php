<?php
////
// Walkers
// 
// These classes extend WordPress' Walker class
// and are intended for use internaly by Webcomic
// on administrative pages.
////

/**
 * Returns the file list for a given collection.
 * 
 * @package webcomic
 * @since 3
 */
class webcomic_Walker_AdminFileList extends Walker {
	var $tree_type = 'webcomic_post';
	var $db_fields = array( 'parent' => 'post_parent', 'id' => 'ID' );
	
	function start_el( &$output, $obj, $depth, $args ) {
		global $current_user, $webcomic;
		static $i = 0;
		
		$webcomic->domain();
		
		$post  = get_post( $obj->ID );
		$files = $webcomic->retrieve( $post->ID, 'post', $args[ 'src' ], true );
		$alt   = ( !( $i % 2 ) ) ? ' class="alt"' : '';
		$thide = ( in_array( 'thumbnails', $args[ 'hidden' ] ) ) ? ' style="display:none"' : '';
		$phide = ( in_array( 'post', $args[ 'hidden' ] ) ) ? ' style="display:none"' : '';
		$shide = ( in_array( 'storylines', $args[ 'hidden' ] ) ) ? ' style="display:none"' : '';
		$chide = ( in_array( 'characters', $args[ 'hidden' ] ) ) ? ' style="display:none"' : '';
		$mhide = ( in_array( 'comments', $args[ 'hidden' ] ) ) ? ' style="display:none"' : '';
		$dhide = ( in_array( 'date', $args[	'hidden' ] ) ) ? ' style="display:none"' : '';
		$flist = $img = '';
		
		if ( !empty( $files ) ) {
			$post_meta = current( get_post_meta( $post->ID, 'webcomic' ) );
			$wc  = current( wp_get_object_terms( $post->ID, 'webcomic_collection' ) );
			
			foreach ( $files[ 'full' ] as $k => $v ) {
				$faction = '';
				
				if ( !empty( $files[ 'small' ][ $k ] ) )
					$file = $files[ 'small' ][ $k ];
				elseif ( !empty( $files[ 'medium' ][ $k ] ) )
					$file = $files[ 'medium' ][ $k ];
				elseif ( !empty( $files[ 'large' ][ $k ] ) )
					$file = $files[ 'large' ][ $k ];
				else
					$file = $v;
				
				$reg     = ( in_array( $files[ 'full' ][ $k ][ 'mime' ], array( 'image/jpeg', 'image/gif', 'image/png' ) ) ) ? '| <a href="' . wp_nonce_url( $args[ 'view' ] . '&amp;action=regen_webcomic_file&amp;webcomic_key=' . $k . '&amp;post=' . $post->ID, 'regen_webcomic_file' ) . '">' . __( 'Regenerate Thumbnails', 'webcomic' ) . '</a> | ' : '';
				$bind    = ( empty( $post_meta[ 'files' ] ) ) ? '<a href="' . wp_nonce_url( $args[ 'view' ] . '&amp;action=bind_webcomic_file&amp;post=' . $post->ID, 'bind_webcomic_file' ) . '">' . __( 'Bind', 'webcomic' ) . '</a> | ' : '<a href="' . wp_nonce_url( $args[ 'view' ] . '&amp;action=unbind_webcomic_file&amp;webcomic_key=' . $k . '&amp;post=' . $post->ID, 'bind_webcomic_file' ) . '">' . __( 'Unbind', 'webcomic' ) . '</a> | ';
				$faction = ( current_user_can( 'edit_others_posts' ) || $current_user->ID == $post->post_author ) ? '<a href="' . admin_url( 'admin.php?page=webcomic_tools&subpage=edit_files&type=post&src=' . $wc->slug . '&id=' . $post->ID . '&key=' . $k ) . '">' . __( 'Edit', 'webcomic' ) . '</a>' . $reg . $bind . '<span class="delete"><a href="' . wp_nonce_url( $args[ 'view' ] . '&amp;action=delete_webcomic_file&amp;webcomic_key=' . $k . '&amp;post=' . $post->ID, 'delete_webcomic_file' ) . '" onclick="if(confirm(\'' . esc_js( sprintf( __( "You are about to delete '%s'\n 'Cancel' to stop, 'OK' to delete.", "webcomic" ), $files[ 'full' ][ $k ][ 'basename' ] ) ) . '\')){return true;}return false;">' . __( 'Delete', 'webcomic' ) . '</a> | </span><span class="view"><a href="' . $files[ 'full' ][ $k ][ 'url' ] . '" target="_blank">' . __( 'View', 'webcomic' ) . '</a></span>' : '<span class="view"><a href="' . $files[ 'full' ][ $k ][ 'url' ] . '" target="_blank">' . __( 'View', 'webcomic' ) . '</a></span>';
				$flist  .= '<a href="' . admin_url( 'admin.php?page=webcomic_tools&subpage=edit_files&type=post&src=' . $wc->slug . '&id=' . $post->ID . '&key=' . $k ) . '" title="' . __( 'Edit this file', 'webcomic' ) . '" class="row-title">' . $files[ 'full' ][ $k ][ 'filename' ] . '</a><br>' . $files[ 'full' ][ $k ][ 'mime' ] . '<p class="row-actions">' . $faction . '</p>';
				$img    .= ( 'application/x-shockwave-flash' == $file[ 'mime' ] ) ? '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" id="webcomic-file-' . hash( 'md5', $file[ 'url' ] ) . '" height="' . $webcomic->option( 'small_h' ) . '" width="' . $webcomic->option( 'small_w' ) . '"><param name="movie" value="' . $file[ 'url' ] . '"><!--[if !IE]>--><object type="application/x-shockwave-flash" data="' . $file[ 'url' ] . '" height="' . $webcomic->option( 'small_h' ) . '" width="' . $webcomic->option( 'small_w' ) . '"><!--<![endif]--><p>' . $file[ 'full' ][ $k ][ 'filename' ] . '</p><!--[if !IE]>--></object><!--<![endif]--></object><script type="text/javascript">swfobject.registerObject("webcomic-file-' . hash( 'md5', $file[ 'url' ] ) . '","9");</script><br>' : '<img src="' . $file[ 'url' ] . '" alt="' . $files[ 'full' ][ $k ][ 'basename' ] . '" ' . $file[ 3 ] . '><br>';
			}
		}
		
		$title  = ( 'trash' != $post->post_status && ( current_user_can( 'edit_others_posts' ) || $current_user->ID == $post->post_author ) ) ? '<a href="post.php?action=edit&amp;post_type=webcomic_post&amp;post=' . $post->ID . '" title="' . __( 'Edit this post', 'webcomic' ) . '" class="row-title">' . $post->post_title . '</a>' : '<span class="row-title">' . $post->post_title . '</span>';
		$author = get_userdata( $post->post_author );
		$state  = $cterms = $sterms = array();
		$cbox   = ( current_user_can( 'edit_others_posts' ) || $current_user->ID == $post->post_author ) ? '<input type="checkbox" name="bulk[]" value="' . $post->ID . '">' : '';
		
		if ( 'trash' == $post->post_status )
			$paction = ( current_user_can( 'delete_others_posts' ) || $current_user->ID == $post->post_author ) ? '<span class="untrash"><a href="' . wp_nonce_url( $args[ 'view' ] . '&amp;action=undelete_webcomic_post&amp;post=' . $post->ID, 'undelete_webcomic_post' ) . '">' . __( 'Restore', 'webcomic' ) . '</a></span> | <span class="delete"><a href="' . wp_nonce_url( $args[ 'view' ] . '&amp;action=delete_webcomic_post&amp;post=' . $post->ID, 'delete_webcomic_post' ) . '">' . __( 'Delete Permanently', 'webcomic' ) . '</a></span>' : '';
		else
			$paction = ( current_user_can( 'edit_others_posts' ) || $current_user->ID == $post->post_author ) ? '<a href="post.php?action=edit&amp;post_type=webcomic_post&amp;post=' . $post->ID . '">' . __( 'Edit', 'webcomic' ) . '</a> | <span class="delete"><a href="' . wp_nonce_url( $args[ 'view' ] . '&amp;action=delete_webcomic_post&amp;post=' . $post->ID, 'delete_webcomic_post' ) . '">' . __( 'Trash', 'webcomic' ) . '</a></span> | <span class="view"><a href="' . get_permalink( $post->ID ) . '" target="_blank">' . __( 'View', 'webcomic' ) . '</a></span>' : '<span class="view"><a href="' . get_permalink( $post->ID ) . '">' . __( 'View', 'webcomic' ) . '</a></span>';
		
		if ( !empty( $post->post_password ) )
			$state[] = __( 'Password Protected', 'webcomic' );
		elseif ( 'private' == $post->post_status )
			$state[] = __( 'Private', 'webcomic' );
		
		if ( 'pending' == $post->post_status )
			$state[] = __( 'Pending', 'webcomic' );
		
		if ( 'draft' == $post->post_status )
			$state[] = __( 'Draft', 'webcomic' );
		
		if ( is_sticky( $post->ID ) )
			$state[] = __( 'Sticky', 'webcomic' );
		
		$state = ( !empty( $state ) ) ? ' - <strong>' . implode( ', ', $state ) . '</strong>' : '';
		
		$storylines = wp_get_object_terms( $post->ID, 'webcomic_storyline' );
		
		if ( !empty( $storylines ) && !is_wp_error( $storylines ) ) {
			foreach ( $storylines as $storyline )
				$sterms[] = '<a href="' . $args[ 'view' ] . '&amp;webcomic_storyline=' . $storyline->term_id . '">' . $storyline->name . '</a>';
		} else
			$sterms = array( __( 'No Storylines', 'webcomic' ) );
		
		$characters = wp_get_object_terms( $post->ID, 'webcomic_character' );
		
		if ( !empty( $characters ) && !is_wp_error( $characters ) ) {
			foreach ( $characters as $character )
				$cterms[] = '<a href="' . $args[ 'view' ] . '&amp;webcomic_character=' . $character->term_id . '">' . $character->name . '</a>';
		} else
			$cterms = array( __( 'No Characters', 'webcomic' ) );
		
		if ( '0000-00-00 00:00:00' == $post->post_date )
			$t_time = $h_time = __( 'Unpublished', 'webcomic' );
		else {
			$t_time = get_the_time( __( 'Y/m/d g:i:s A', 'webcomic' ), $post->ID );
			$m_time = $post->post_date;
			$time   = get_post_time( 'G', true, $post );
			$d_time = time() - $time;

			if ( ( 'future' == $post->post_status ) )
				$h_time = ( $d_time <= 0 ) ? sprintf( __( '%s from now', 'webcomic' ), human_time_diff( $time ) ) : $t_time;
			elseif( $d_time > 0 && $d_time < 86400 )
				$h_time = sprintf( __( '%s ago', 'webcomic' ), human_time_diff( $time ) );
			else
				$h_time = mysql2date( __( 'Y/m/d', 'webcomic' ), $m_time);
			
			$missed = ( $h_time == $t_time ) ? true : false;
		}
		
		if ( 'publish' == $post->post_status )
			$status = __( 'Published', 'webcomic' );
		elseif ( 'future' == $post->post_status && $missed )
			$status = '<strong class="attention">' . __( 'Missed Schedule', 'webcomic' ) . '</strong>';
		elseif ( 'future' == $post->post_status )
			$status = __( 'Scheduled', 'webcomic' );
		else
			$status = __( 'Last Modified', 'webcomic' );
		
		$output .= '<tr' . $alt . '>
			<th scope="row" class="check-column">' . $cbox . '</th>
			<td class="thumbnails column-thumbnails"' . $thide . '>' . $img . '</td>
			<td class="post column-post"' . $phide . '>' . $title . $state . '<br>' . $author->display_name . '<p class="row-actions">' . $paction . '</p></td>
			<td class="file column-file">' . $flist . '</td>
			<td class="storylines column-storylines"' . $shide . '>' . implode( ', ', $sterms ) . '</td>
			<td class="characters column-characters"' . $chide . '>' . implode( ', ', $cterms ) . '</td>
			<td class="comments column-comments"' . $mhide . '><a href="edit-comments.php?p=' . $post->ID . '" title="' . sprintf( __( '%s pending', 'webcomic' ), number_format( get_pending_comments_num( $post->ID ) ) ) . '" class="post-com-count"><span class="comment-count">' . $post->comment_count . '</span></a></td>
			<td class="date column-date"' . $dhide . '><abbr title="' . $t_time . '">' . $h_time . '</abbr><br>' . $status . '</td>
		</tr>';
		
		$i++;
	}
}

/**
 * Returns the term list for a given collection.
 * 
 * @package webcomic
 * @since 3
 */
class webcomic_Walker_AdminTermList extends Walker {
	var $tree_type = 'webcomic_term';
	var $db_fields = array( 'parent' => 'parent', 'id' => 'term_id' );
	
	function start_el( &$output, $term, $depth, $args ) {
		global $webcomic;
		
		static $i = 0;
		
		$webcomic->domain();
		
		$alt   = ( !( $i % 2 ) ) ? ' class="alt"' : '';
		$chide = ( in_array( 'cover', $args[ 'hidden' ] ) ) ? ' style="display:none"' : '';
		$shide = ( in_array( 'slug', $args[ 'hidden' ] ) ) ? ' style="display:none"' : '';
		$hhide = ( in_array( 'characters', $args[ 'hidden' ] ) ) ? ';display:none' : '';
		$thide = ( in_array( 'storylines', $args[ 'hidden' ] ) ) ? ';display:none' : '';
		$phide = ( in_array( 'posts', $args[ 'hidden' ] ) ) ? ' style="display:none"' : '';
		$img   = $default = $delete = '';
		
		if ( $term->webcomic_default ) {
			$style  = ' style="background:#fffeeb"';
			$label  = ( 'webcomic_collection' == $args[ 'page' ] ) ? __( 'Remove as Default', 'webcomic' ) : __( 'Remove from Defaults', 'webcomic' );
			$switch = 'off';
		} else {
			$style = '';
			$label  = ( 'webcomic_collection' == $args[ 'page' ] ) ? __( 'Set as Default', 'webcomic' ) : __( 'Add to Defaults', 'webcomic' );
			$switch = 'on';
		}
		
		if ( !empty( $term->webcomic_files ) ) {
			if ( $term->webcomic_files[ 'small' ] )
				$file = $term->webcomic_files[ 'small' ];
			elseif ( $term->webcomic_files[ 'medium' ] )
				$file = $term->webcomic_files[ 'medium' ];
			elseif ( $term->webcomic_files[ 'large' ] )
				$file = $term->webcomic_files[ 'large' ];
			else
				$file = $term->webcomic_files[ 'full' ];
			
			foreach ( $file as $f )
				$img .= ( isset( $file[ 'mime' ] ) && 'application/x-shockwave-flash' == $file[ 'mime' ] ) ? '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" id="webcomic-file-' . hash( 'md5', $f[ 'url' ] ) . '" height="' . $webcomic->option( 'small_h' ) . '" width="' . $webcomic->option( 'small_w' ) . '"><param name="movie" value="' . $f[ 'url' ] . '"><!--[if !IE]>--><object type="application/x-shockwave-flash" data="' . $f[ 'url' ] . '" height="' . $webcomic->option( 'small_h' ) . '" width="' . $webcomic->option( 'small_w' ) . '"><!--<![endif]--><p>' . $term->name . '</p><!--[if !IE]>--></object><!--<![endif]--></object><script type="text/javascript">swfobject.registerObject("webcomic-file-' . hash( 'md5', $f[ 'url' ] ) . '","9");</script><br>' : '<img src="' . $f[ 'url' ] . '" alt="' . $term->name . '" ' . $f[ 3 ] . '><br>';
		}
		
		$move = ( 'webcomic_storyline' == $args[ 'page' ] ) ? '<a href="' . wp_nonce_url( $args[ 'view' ] . '&amp;action=move_webcomic_term&amp;webcomic_term=' . $term->term_id . '&amp;direction=up', 'move_webcomic_term' ) . '"> &uarr; </a><a href="' . wp_nonce_url( $args[ 'view' ] . '&amp;action=move_webcomic_term&amp;webcomic_term=' . $term->term_id . '&amp;direction=dn', 'move_webcomic_term' ) . '"> &darr; </a> ' : '';
		$coll = ( 'webcomic_collection' != $args[ 'page' ] ) ? '&amp;webcomic_collection=' . $term->term_group : '';
		$char = ( 'webcomic_collection' == $args[ 'page' ] ) ? '<td class="characters column-characters" style="text-align:center' . $hhide . '"><a href="admin.php?page=webcomic_character&amp;' . $args[ 'page' ] . '=' . $term->term_id . '">' . get_terms( 'webcomic_character', 'hide_empty=0&fields=count&term_group=' . $term->term_id ) . '</a></td>' : '';
		$stor = ( 'webcomic_collection' == $args[ 'page' ] ) ? '<td class="storylines column-storylines" style="text-align:center' . $thide . '"><a href="admin.php?page=webcomic_storyline&amp;' . $args[ 'page' ] . '=' . $term->term_id . '">' . get_terms( 'webcomic_storyline', 'hide_empty=0&fields=count&term_group=' . $term->term_id ) . '</a></td>' : '';
		
		if ( !( 'webcomic_collection' == $args[ 'page' ] && $term->webcomic_default ) ) {
			$default = ' | <a href="' . wp_nonce_url( $args[ 'view' ] . '&amp;action=default_webcomic_term&amp;webcomic_term=' . $term->term_id . '&amp;switch=' .  $switch, 'default_webcomic_term' ) . '">' . $label . '</a>';
			$delete  = ' | <span class="delete"><a href="' . wp_nonce_url( $args[ 'view' ] . '&amp;action=delete_webcomic_term&amp;webcomic_term=' . $term->term_id, 'delete_webcomic_term' ) . '" onclick="if (confirm(\'' . esc_js( sprintf( __( "You are about to delete %s.\n 'Cancel' to stop, 'OK' to delete.", "webcomic" ), $term->name ) ) . '\')){return true;}return false;">' . __( 'Delete', 'webcomic' ) . '</a></span>';
		}
		
		$output .= '<tr' . $alt . $style . '>
			<th scope="row" class="check-column"><input type="checkbox" name="bulk[]" value="' . $term->term_id . '"></th>
			<td class="cover column-cover"' . $chide . '>' . $img . '</td>
			<td class="name column-name"><a href="' . $args[ 'view' ] . '&amp;subpage=edit_webcomic_term&amp;webcomic_term=' . $term->term_id . '" title="' . sprintf( __( 'Edit &#8220;%s&#8221;', 'webcomic' ), $term->name ) . '" class="row-title">' . str_repeat( '&mdash; ', $depth ) . $term->name . '</a><div class="row-actions">' . $move . '<a href="' . $args[ 'view' ] . '&amp;subpage=edit_webcomic_term&amp;webcomic_term=' . $term->term_id . '">' . __( 'Edit', 'webcomic' ) . '</a>' . $default . $delete . '</div></td>
			<td class="slug column-slug"' . $shide . '>' . $term->slug . '</td>' . $char . $stor . '
			<td class="posts column-posts"' . $phide . '><a href="admin.php?page=webcomic_files' . $coll . '&amp;' . $args[ 'page' ] . '=' . $term->term_id . '">' . $term->count . '</a></td>
		</tr>';
		
		$i++;
	}
}

/**
 * Returns the dropdown term list for a given collection.
 * 
 * @package webcomic
 * @since 3
 */
class webcomic_Walker_AdminTermDropdown extends Walker {
	var $tree_type = 'webcomic_collection';
	var $db_fields = array( 'parent' => 'parent', 'id' => 'term_id' );
	
	function start_el( &$output, $term, $depth, $args ) {
		$output .= '<option value="' . $term->term_id . '"';
		
		if ( ( ( empty( $args[ 'selected' ] ) && !$args[ 'no_def' ] && $term->webcomic_default ) || in_array( $term->term_id, $args[ 'selected' ] ) ) )
			$output .= ' selected';
		
		$output .= '>' . str_repeat( '&nbsp;', $depth * 3 ) . $term->name . '</option>';
	}
}

/**
 * Returns the dropdown parent term list for a given collection.
 * 
 * @package webcomic
 * @since 3
 */
class webcomic_Walker_AdminTermParent extends Walker {
	var $tree_type = 'webcomic_term';
	var $db_fields = array( 'parent' => 'parent', 'id' => 'term_id' );
	
	function start_el( &$output, $term, $depth, $args ) {
		$output .= '<option value="' . $term->term_id . '"';
		
		if ( $term->term_id == $args[ 'parent' ] )
			$output .= ' selected';
		
		$output .= '>' . str_repeat( '&nbsp;', $depth * 3 ) . $term->name . '</option>';
	}
}

/**
 * Returns term meta with normalized orders.
 * 
 * @package webcomic
 * @since 3
 */
class webcomic_Walker_AdminTermNormalize extends Walker {
	var $tree_type = 'webcomic_term';
	var $db_fields = array( 'parent' => 'parent', 'id' => 'term_id' );
	
	function start_el( &$output, $term, $depth, $args ) {
		global $webcomic;
		
		static $x = array();
		
		if ( empty( $output ) )
			$output = $args[ 'term_meta' ];
		
		if ( empty( $x[ $term->parent . '_' . $depth ] ) )
			$x[ $term->parent . '_' . $depth ] = 1;
		
		$output[ 'storyline' ][ $term->term_id ][ 'order' ] = $x[ $term->parent . '_' . $depth ];
		
		$x[ $term->parent . '_' . $depth ]++;
	}
}
?>
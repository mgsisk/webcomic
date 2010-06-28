<?php
////
// Walkers
// 
// These classes extend WordPress' Walker class
// and are intended for use internaly by Webcomic
// in template tag functions.
////

/**
 * Returns a formatted list of webcomic terms.
 * 
 * @package webcomic
 * @since 3
 */
class webcomic_Walker_TermList extends Walker {
	var $tree_type = 'webcomic_term';
	var $db_fields = array( 'parent' => 'parent', 'id' => 'term_id' );
	
	function start_lvl( &$output, $depth, $args ) {
		if ( !$args[ 'separator' ] )
			$output .= '<' . $args[ 'x' ] . 'l class="children">';
	}
	
	function end_lvl( &$output, $depth, $args ) {
		if ( !$args[ 'separator' ] )
			$output .= '</' . $args[ 'x' ] . 'l>';
	}
	
	function end_el( &$output, $page, $depth, $args ) {
		if ( $args[ 'separator' ] )
			$output .= $args[ 'separator' ];
		else
			$output .= '</li>';
	}
	
	function start_el( &$output, $term, $depth, $args ) {
		global $webcomic;
		
		$webcomic->domain();
		
		$term_name = esc_attr( $term->name );
		
		$link  = '<a href="' . get_term_link( ( int ) $term->term_id, $args[ 'taxonomy' ] ) . '"';
		$link .= ( isset( $args[ 'use_count_for_title' ] ) && false !== $args[ 'use_count_for_title' ] ) ? ' title="' . sprintf( __( '%d webcomics', 'webcomic' ), $term->count ) . '">' : '>';
		
		if ( $args[ 'image' ] && !empty( $term->webcomic_files[ $args[ 'image' ] ] ) ) {
			foreach ( $term->webcomic_files[ $args[ 'image' ] ] as $v )
				$link .= ( $webcomic->option( 'secure_toggle' ) ) ? $v[ 'shtml' ] : $v[ 'html' ];
		} else
			$link .= '<span>' . $term_name . '</span>';
		
		$link .= '</a>';
		
		if ( ( !empty( $args[ 'feed_image' ] ) ) || ( !empty( $args[ 'feed' ] ) ) ) {
			$link .= ( empty( $args[ 'feed_image' ] ) ) ? ' (' : ' ';
			$link .= '<a href="' . $webcomic->get_term_feed_link( $term, $args[ 'taxonomy' ], $args[ 'feed_type' ] ) . '">';
			
			if ( empty( $args[ 'feed' ] ) )
				$alt = ' alt="' . sprintf( __( 'Feed for all %s webcomics', 'webcomic' ), $term_name ) . '"';
			else {
				$title = ' title="' . $args[ 'feed' ] . '"';
				$alt   = ' alt="' . $args[ 'feed' ] . '"';
				$name  = $args[ 'feed' ];
				$link .= $title;
			}
			
			$link .= ( empty( $args[ 'feed_image' ] ) ) ? $name . '</a>)' : '<img src="' . $feed_image . '" ' . $alt . $title . '></a>';
		}
		
		if ( $args[ 'show_count' ] )
			$link .= ' (' . intval( $term->count ) . ')';
		
		$_term = ( isset( $args[ 'selected' ] ) && $args[ 'selected' ] ) ? get_term( $args[ 'selected' ], $args[ 'taxonomy' ] ) : false;
		
		if ( !$args[ 'separator' ] ) {
			$class = 'webcomic-term-item ' . $args[ 'taxonomy' ] . '-item ' . $args[ 'taxonomy' ]  . '-item-' . $term->term_id;
			
			if ( $args[ 'selected' ] && ( $term->term_id == $args[ 'selected' ] ) )
				$class .=  'current-webcomic-term current-' . $args[ 'taxonomy' ];
			elseif ( $_term && ( $term->term_id == $_term->parent ) )
				$class .=  'current-webcomic-term-parent current-' . $args[ 'taxonomy' ] . '-parent';
			
			$output .= '<li class="' . $class . '">' . $link;
		} else
			$output .= $link;
	}
}

/**
 * Returns a formatted select element of webcomic terms.
 * 
 * @package webcomic
 * @since 3
 */
class webcomic_Walker_TermDropdown extends Walker {
	var $tree_type = 'webcomic_term';
	var $db_fields = array ( 'parent' => 'parent', 'id' => 'term_id' );
	
	function start_el( &$output, $term, $depth, $args ) {
		$term_name = esc_attr( $term->name );
		
		$output .= '<option class="level-' . $depth . ' webcomic-term-item webcomic-' . $args[ 'taxonomy' ] . '-item webcomic-' . $args[ 'taxonomy' ] . '-item-' . $term->term_id . '" value="' . get_term_link( ( int ) $term->term_id, $args[ 'taxonomy' ] ) . '"';
		$output .= ( $term->term_id == $args[ 'selected' ] ) ? ' selected>' . str_repeat( '&nbsp;', $depth * 3 ) . $term_name: '>' . str_repeat( '&nbsp;', $depth * 3 ) . $term_name;
		$output .= ( $args[ 'show_count' ] ) ? ' (' . $term->count . ')</option>' : '</option>';
	}
}

/**
 * Returns a formatted list of webcomic posts.
 * 
 * @package webcomic
 * @since 3
 */
class webcomic_Walker_ArchiveList extends Walker {
	var $tree_type = 'webcomic_term';
	var $db_fields = array ( 'parent' => 'parent', 'id' => 'term_id' );
	
	function start_lvl( &$output, $depth, $args ) {
		if ( !$args[ 'separator' ] || ( 'collection' == $args[ 'group' ] || 'storyline' == $args[ 'group' ] || 'character' == $args[ 'group' ] ) )
			$output .= '<' . $args[ 'x' ] . 'l class="children">';
	}
	
	function end_lvl( &$output, $depth, $args ) {
		if ( !$args[ 'separator' ] || ( 'collection' == $args[ 'group' ] || 'storyline' == $args[ 'group' ] || 'character' == $args[ 'group' ] ) )
			$output .= '</' . $args[ 'x' ] . 'l>';
	}
	
	function end_el( &$output, $page, $depth, $args ) {
		if ( $args['separator'] && !( 'collection' == $args[ 'group' ] || 'storyline' == $args[ 'group' ] || 'character' == $args[ 'group' ] ) )
			$output .= $args[ 'separator' ];
		else
			$output .= '</li>';
	}
	
	function start_el( &$output, $term, $depth, $args ) {
		global $webcomic;
		
		$webcomic->domain();
				
		if ( $args[ 'group_image' ] ) {
			$link = $img = false;
			
			$wc = ( 'collection' == $args[ 'group' ] ) ? get_term( $term->term_id, 'webcomic_collection' ) : get_term( $term->term_group, 'webcomic_collection' );
			
			if ( $img = $webcomic->retrieve( $term->term_id, $args[ 'group' ], $wc->slug, true ) )
				foreach ( $img[ $args[ 'group_image' ] ] as $v )
					$link .= ( $webcomic->option( 'secure_toggle' ) ) ? $v[ 'shtml' ] : $v[ 'html' ];
		} else
			$link = $term->name;
		
		$output  .= '<li class="webcomic-archive-' . $args[ 'group' ] . ' webcomic-archive-' . $args[ 'group' ] . '-' . $term->slug . '"><a href="' . get_term_link( ( int ) $term->term_id, $term->taxonomy ) . '"><span>' . $link;
		$output  .= ( $args[ 'show_count' ] ) ? ' (' . $term->count . ')</span></a>' : '</span></a>';
		$output  .= ( $args[ 'show_description' ] ) ? $term->description : '';
		$children = ( $args[ 'last_only' ] ) ? get_term_children( $term->term_id, $term->taxonomy ) : array();
		
		if ( ( !empty( $args[ 'last_only' ] ) && empty( $children ) ) || empty( $args[ 'last_only' ] ) ) {
			if ( ( $posts = get_objects_in_term( $term->term_id, "webcomic_$args[group]" ) ) ) {
				usort( $posts, array( $webcomic, 'usort_term_objects_by_date' ) );
				
				if ( !empty( $args[ 'limit' ] ) )
					$posts = array_slice( $posts, 0, $args[ 'limit' ] );
				
				if ( $args[ 'separator' ] ) {
					$format  = false;
					$output .= '<span class="webcomic-archive-items">';
				} else {
					$format  = true;
					$output .= '<' . $args[ 'x' ] . 'l class="webcomic-archive-items">';
				}
				
				foreach ( $posts as $p ) {
					if ( get_post_status( $p ) != 'publish' )
						continue;
					
					$wc = ( isset( $wc ) ) ? $wc : current( wp_get_object_terms( $p, 'webcomic_collection' ) );
					
					if ( $args[ 'image' ] ) {
						$link = $img = false;
						
						if ( $img = $webcomic->retrieve( $p, 'post', $wc->slug, true ) )
							foreach ( $img[ $args[ 'image' ] ] as $v )
								$link .= ( $webcomic->option( 'secure_toggle' ) ) ? $v[ 'shtml' ] : $v[ 'html' ];
					} else
						$link = get_the_title( $p );
					
					$output .= ( $format ) ? '<li class="webcomic-archive-item webcomic-archive-item-' . $p . '"><a href="' . get_permalink( $p ) . '"><span>' . $link . '</span></a></li>' : '<a href="' . get_permalink( $p ) . '" class="webcomic-archive-item webcomic-archive-item-' . $p . '"><span>' . $link . '</span></a>' . $args[ 'separator' ];
				}
				
				
				$output .= ( $format ) ? '</' . $args[ 'x' ] . 'l>' : '</span>';
				
				if ( !$format )
					$output = substr( $output, 0, strrpos( $output, $args[ 'separator' ] ) );
			}
		}
	}
}

/**
 * Return a formatted dropdown of webcomics.
 * 
 * @package webcomic
 * @since 3
 */
class webcomic_Walker_ArchiveDropdown extends Walker {
	var $tree_type = 'webcomic_term';
	var $db_fields = array ( 'parent' => 'parent', 'id' => 'term_id' );
	
	function start_el( &$output, $term, $depth, $args ) {
		global $webcomic;
		
		$term_name = esc_attr( $term->name );
		
		$output  .= '<option class="level-' . $depth . ' webcomic-archive-term webcomic-archive-term-' . $term->slug . '" value="' . get_term_link( ( int ) $term->term_id, $term->taxonomy ) . '"';
		$output  .= ( isset( $args[ 'selected' ] ) && $term->term_id == $args[ 'selected' ] ) ? ' selected>' . str_repeat( '&nbsp;', $depth * 3 ) . $term_name: '>' . str_repeat( '&nbsp;', $depth * 3 ) . $term_name;
		$output  .= ( $args[ 'show_count' ] ) ? ' (' . $term->count . ')</option>' : '</option>';
		$children = ( $args[ 'last_only' ] ) ? get_term_children( $term->term_id, $term->taxonomy ) : array();
		
		if ( ( !empty( $args[ 'last_only' ] ) && empty( $children ) ) || empty( $args[ 'last_only' ] ) ) {
			if ( $posts = get_objects_in_term( $term->term_id, $term->taxonomy ) ) {
				usort( $posts, array( $webcomic, 'usort_term_objects_by_date' ) );
				
				if ( 'DESC' == $args[ 'order' ] )
					$posts = array_reverse( $posts );
				
				foreach ( $posts as $p )
					$output .= '<option class="level-' . $depth . ' webcomic-archive-item webcomic-archive-item-' . $p . '" value="' . get_permalink( $p ) . '">' . str_repeat( '&nbsp;', ( $depth + 1 ) * 3 ) . get_the_title( $p ) . '</option>';
			}
		}
	}
}

/**
 * Displays one or more transcripts associated with a webcommic post.
 * 
 * @package webcomic
 * @since 3
 */
class webcomic_Walker_Transcripts extends Walker {
	var $tree_type = 'webcomic_transcripts';
	var $db_fields = array ( 'parent' => 'language', 'id' => 'language_code' );
	
	function start_el( &$output, $transcript, $depth, $args ) {
		global $webcomic, $post, $webcomic_transcript;
		
		$webcomic_transcript = $transcript;
		
		if ( !empty( $args[ 'callback' ] ) ) {
			call_user_func( $args[ 'callback' ], $transcript, $args, $depth );
			return false;
		}
		
		extract( $args, EXTR_SKIP );
		?>
		<article <?php echo $webcomic->get_webcomic_transcript_class(); ?> id="webcomic-transcript-<?php echo $webcomic->get_webcomic_transcript_info( 'id' ) ?>" lang="<?php echo $webcomic->get_webcomic_transcript_info( 'language_code' ); ?>">
			<?php echo $webcomic->get_webcomic_transcript_info( 'text' ) ?>
			<footer class="webcomic-transcript-meta">
			<?php
				printf( __( '%s transcript submitted by %s on <time pubdate>%s @ %s</time>', 'webcomic' ),
					$webcomic->get_webcomic_transcript_info( 'language' ),
					$webcomic->get_webcomic_transcript_info( 'author' ),
					$webcomic->get_webcomic_transcript_info( 'the_date' ),
					$webcomic->get_webcomic_transcript_info( 'the_time' ) );
			?>
			</footer>
		</article>
<?php
	}
}
?>
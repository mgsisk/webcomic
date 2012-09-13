<?php
/** Contains the WebcomicTag class and template tag functions.
 * 
 * @package Webcomic
 */

/** Handle custom template tag functionality.
 * 
 * @package Webcomic
 */
class WebcomicTag extends Webcomic {
	/** Override the parent constructor. */
	public function __construct(){}
	
	///
	// Utility Tags
	///
	
	/** Return the current collection ID or configuration.
	 * 
	 * @param boolean $config Return the entire configuration for the current collection.
	 * @return string|array
	 * @uses Webcomic::$collection
	 */
	public static function get_webcomic_collection( $config = false ) {
		return ( $config and isset( self::$config[ 'collections' ][ self::$collection ] ) ) ? array( self::$collection => self::$config[ 'collections' ][ self::$collection ] ) : self::$collection;
	}
	
	/** Return all collection ID's or configurations.
	 * 
	 * @param boolean $config Return the entire configuration for all collections.
	 * @return array
	 * @uses Webcomic::$config
	 */
	public static function get_webcomic_collections( $config = false ) {
		return $config ? self::$config[ 'collections' ] : array_keys( self::$config[ 'collections' ] );
	}
	
	/** Sort webcomic collections by name.
	 * 
	 * Useful in combination with the usort() function to sort
	 * collections by name, like:
	 * 
	 * <code>
	 * 	$collections = WebcomicTag::get_webcomic_collections( true );
	 * 
	 * usort( $collections, array( 'WebcomicTag', 'sort_webcomic_collections_name' ) );
	 * 
	 * // reverse the order
	 * $collections = array_reverse( $collections );
	 * </code>
	 * 
	 * @param array $a A collection to compare for sorting.
	 * @param array $b A collection to compare for sorting.
	 * @return integer
	 */
	public static function sort_webcomic_collections_name( $a, $b ) {
		return strcmp( $a[ 'name' ], $b[ 'name' ] );
	}
	
	/** Sort webcomic collections by slug.
	 * 
	 * Useful in combination with the usort() function to sort
	 * collections by slug, like:
	 * 
	 * <code>
	 * 	$collections = WebcomicTag::get_webcomic_collections( true );
	 * 
	 * usort( $collections, array( 'WebcomicTag', 'sort_webcomic_collections_slug' ) );
	 * 
	 * // reverse the order
	 * $collections = array_reverse( $collections );
	 * </code>
	 * 
	 * @param array $a A collection to compare for sorting.
	 * @param array $b A collection to compare for sorting.
	 * @return integer
	 */
	public static function sort_webcomic_collections_slug( $a, $b ) {
		return strcmp( $a[ 'slugs' ][ 'name' ], $b[ 'slugs' ][ 'name' ] );
	}
	
	/** Sort webcomic collections by published post count.
	 * 
	 * Useful in combination with the usort() function to sort
	 * collections by published post count, like:
	 * 
	 * <code>
	 * $collections = WebcomicTag::get_webcomic_collections( true );
	 * 
	 * usort( $collections, array( 'WebcomicTag', 'sort_webcomic_collections_count' ) );
	 * 
	 * // reverse the order
	 * $collections = array_reverse( $collections );
	 * </code>
	 * 
	 * @param array $a A collection to compare for sorting.
	 * @param array $b A collection to compare for sorting.
	 * @return integer
	 */
	public static function sort_webcomic_collections_count( $a, $b ) {
		$count_a = wp_count_posts( $a[ 'id' ], 'readable' );
		$count_a = $count_a->publish + $count_a->private;
		$count_b = wp_count_posts( $b[ 'id' ], 'readable' );
		$count_b = $count_b->publish + $count_b->private;
		
		if ( $count_a === $count_b ) {
			return 0;
		}
		
		return $count_a < $count_b ? -1 : 1;
	}
	
	/** Sort webcomic collections by last update time.
	 * 
	 * Useful in combination with the usort() function to sort
	 * collections by last update time, like:
	 * 
	 * <code>
	 * $collections = WebcomicTag::get_webcomic_collections( true );
	 * 
	 * usort( $collections, array( 'WebcomicTag', 'sort_webcomic_collections_updated' ) );
	 * 
	 * // reverse the order
	 * $collections = array_reverse( $collections );
	 * </code>
	 * 
	 * @param array $a A collection to compare for sorting.
	 * @param array $b A collection to compare for sorting.
	 * @return integer
	 */
	public static function sort_webcomic_collections_updated( $a, $b ) {
		if ( $a[ 'updated' ] === $b[ 'updated' ] ) {
			return 0;
		}
		
		return $a[ 'updated' ] < $b[ 'updated' ] ? -1 : 1;
	}
	
	///
	// Conditional Tags
	///
	
	/** Is the query for any single webcomic?
	 * 
	 * Specific collection checks should be done using the is_singular()
	 * function directly.
	 * 
	 * @param boolean $dynamic Check for dynamically-requested webcomics.
	 * @return boolean
	 * @uses Webcomic::$config
	 */
	public static function is_webcomic( $dynamic = false ) {
		return ( is_singular( array_keys( self::$config[ 'collections' ] ) ) and ( !$dynamic or 'xmlhttprequest' === strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) ) );
	}
	
	/** Is the query for a relatieve webcomic?
	 * 
	 * @param string $relative The relative post to check for; one of 'first' or 'last'.
	 * @param mixed $in_same_term Whether the relative webcomic should be in a same term. May also be an array or comma-separated list of inclusive term IDs.
	 * @param mixed $excluded_terms An array or comma-separated list of excluded term IDs.
	 * @param string $taxonomy The taxonomy of the terms specified with $in_same_term and $excluded_terms arguments. The shorthand 'storyline' or 'character' may be used.
	 * @param string $collection The collection to compare from. Used when comparing outside of the loop.
	 * @return boolean
	 * @uses Webcomic::get_relative_webcomic()
	 */
	public static function is_relative_webcomic( $relative = 'first', $in_same_term = false, $excluded_terms = false, $taxonomy = 'storyline', $collection = '' ) {
		global $post;
		
		if ( !$the_post = self::get_relative_webcomic( $relative, $in_same_term, $excluded_terms, $taxonomy, $collection ) ) {
			return false;
		}
		
		return ( $post->ID === $the_post->ID );
	}
	
	/** Is the query for a Webcomic-recognized attachment?
	 * 
	 * @param mixed $collection Collection ID or an array of these to check.
	 * @return boolean
	 * @uses Webcomic::$config
	 */
	public static function is_webcomic_attachment( $collection = '' ) {
		global $post;
		
		$collection = $collection ? ( array ) $collection : array_keys( self::$config[ 'collections' ] );
		
		return ( is_attachment() and preg_match( '/^image\//', get_post_mime_type( $post ) ) and in_array( get_post_type( $post->post_parent ), $collection ) );
	}
	
	/** Is the query for a webcomic-related page?
	 * 
	 * @param mixed $the_post Post object or ID to check.
	 * @param mixed $collection Collection ID or an array of these to check.
	 * @return boolean
	 */
	public static function is_webcomic_page( $the_post = false, $collection = '' ) {
		if ( !$the_post = get_post( $the_post ) ) {
			return false;
		}
		
		$meta = get_post_meta( $the_post->ID, 'webcomic_collection', true );
		
		return ( $meta and ( !$collection or in_array( $meta, ( array ) $collection ) ) );
	}
	
	/** Is the query for a webcomic archive page?
	 * 
	 * Specific collection checks should be done using the
	 * is_post_type_archive() function directly.
	 * 
	 * @return boolean
	 * @uses Webcomic::$config
	 */
	public static function is_webcomic_archive() {
		return is_post_type_archive( array_keys( self::$config[ 'collections' ] ) );
	}

	/** Is the query for a webcomic taxonomy archive page?
	 * 
	 * Unlike the is_tax() function this function only checks for
	 * Webcomic-related taxonomies, expecting them to be specified in
	 * shorthand (minus the post type prefix). For example, to check for
	 * character taxonomies $taxonomy should be 'character'. Checking
	 * for specific Webcomic term taxonomies should be done using the
	 * is_tax() function directly.
	 * 
	 * @param string $taxonomy A shorthand taxonomy slug. May be 'storyline' or 'character'.
	 * @param mixed $term Term ID, name, slug or an array of term IDs, names, and slugs to check.
	 * @return boolean
	 * @uses Webcomic::$config
	 */
	public static function is_webcomic_tax( $taxonomy = '', $term = '' ) {
		$taxonomies = array();
		
		foreach( array_keys( self::$config[ 'collections' ] ) as $k ) {
			if ( $taxonomy ) {
				$taxonomies[] = "{$k}_{$taxonomy}";
			} else {
				$taxonomies[] = "{$k}_storyline";
				$taxonomies[] = "{$k}_character";
			}
		}
		
		return is_tax( $taxonomies, $term );
	}
	
	/** Is the current post a webcomic?
	 * 
	 * Specific collection checks should be done by comparing the post
	 * type directly.
	 * 
	 * @param mixed $the_post Post object or ID to check.
	 * @return boolean
	 * @uses Webcomic::$config
	 */
	public static function is_a_webcomic( $the_post = false ) {
		return in_array( get_post_type( $the_post ), array_keys( self::$config[ 'collections' ] ) );
	}
	
	/** Is the current post a Webcomic-recognized attachment?
	 * 
	 * @param mixed $the_post Post object or ID to check.
	 * @param mixed $collection Collection ID or an array of these to check.
	 * @return boolean
	 * @uses Webcomic::$config
	 */
	public static function is_a_webcomic_attachment( $the_post = false, $collection = '' ) {
		$the_post   = get_post( $the_post );
		$collection = $collection ? ( array ) $collection : array_keys( self::$config[ 'collections' ] );
		
		return ( $the_post and $the_post->post_parent and preg_match( '/^image\//', get_post_mime_type( $the_post ) ) and in_array( get_post_type( $the_post->post_parent ), $collection ) );
	}
	
	/** Does the current webcomic have any Webcomic-recognized attachments?
	 * 
	 * @param mixed $the_post Post object or ID to check.
	 * @return boolean
	 * @uses Webcomic::get_attachments
	 */
	public static function has_webcomic_attachments( $the_post = false ) {
		return ( boolean ) self::get_attachments( $the_post );
	}
	
	/** Does the current webcomic have any transcripts?
	 * 
	 * @param boolean $pending Does the current webcomic have any transcripts pending review?
	 * @param string $language Language slug to limit any transcripts to.
	 * @param mixed $the_post Post ID or object to check for transcripts.
	 * @return boolean
	 */
	public static function have_webcomic_transcripts( $pending = false, $language = '', $the_post = false ) {
		return ( $the_post = get_post( $the_post ) ) ? ( boolean ) get_children( array(
			'post_type'   => 'webcomic_transcript',
			'post_parent' => $the_post->ID,
			'post_status' => $pending ? 'pending' : get_post_stati( array( 'public' => true ) ),
			'tax_query'   => ( $language = $language ? $language : get_query_var( 'transcripts' ) ) ? array( array( 
				'taxonomy' => 'webcomic_language',
				'field'    => 'slug',
				'terms'    => $language
			) ) : array()
		) ) : false;
	}
	
	/** Does the current webcomic allow transcription?
	 * 
	 * @param mixed $the_post The post to check for transcription permissions.
	 * @return boolean
	 * @uses Webcomic::$config
	 */
	public static function webcomic_transcripts_open( $the_post = false ) {
		return ( $the_post = get_post( $the_post ) and isset( self::$config[ 'collections' ][ $the_post->post_type ] ) and get_post_meta( $the_post->ID, 'webcomic_transcripts', true ) and ( 'register' === self::$config[ 'collections' ][ $the_post->post_type ][ 'transcripts' ][ 'permission' ] ? is_user_logged_in() : true ) );
	}
	
	/** Does the current webcomic have prints available?
	 * 
	 * @param boolean $original Whether an original, traditional-media print is available.
	 * @param mixed $the_post The post to check for prints.
	 * @return boolean
	 * @uses Webcomic::$config
	 */
	public static function webcomic_prints_available( $original = false, $the_post = false ) {
		return ( $the_post = get_post( $the_post ) and !empty( self::$config[ 'collections' ][ $the_post->post_type ][ 'commerce' ][ 'business' ] ) and get_post_meta( $the_post->ID, 'webcomic_prints', true ) and ( $original ? get_post_meta( $the_post->ID, 'webcomic_original', true ) : true ) );
	}
	
	/** Verify a users age against collection age limit.
	 * 
	 * @param string $collection The collection to verify against.
	 * @param object $user The user to verify (defaults to the current user).
	 * @param integer $age Age (in years) to verify against. Overrides the collection age setting, or forces use of the collection age if -1.
	 * @return boolean|null
	 * @uses Webcomic::$config
	 * @uses Webcomic::$collection
	 */
	public static function verify_webcomic_age( $collection = '', $user = false, $age = 0 ) {
		$collection = $collection ? $collection : self::$collection;
		
		if ( empty( self::$config[ 'collections' ][ $collection ][ 'access' ][ 'byage' ] ) and !$age ) {
			return true;
		} else {
			$save = false;
			$user = is_object( $user ) ? $user : wp_get_current_user();
			$age  = ( $age and 0 < $age ) ? intval( $age ) : self::$config[ 'collections' ][ $collection ][ 'access' ][ 'age' ];
			
			if ( (
					( isset( $user->ID ) and $birthday = get_user_meta( $user->ID, 'webcomic_birthday', true ) )
					or ( empty( $user->ID ) and isset( $_COOKIE[ 'webcomic_birthday_' . COOKIEHASH ] ) and $birthday = $_COOKIE[ 'webcomic_birthday_' . COOKIEHASH ] )
					or ( isset( $_POST[ 'webcomic_birthday' ] ) and $birthday = $_POST[ 'webcomic_birthday' ] and $save = true )
				)
				and $time = strtotime( $birthday )
			) {
				if ( $save and !empty( $user->ID ) ) {
					update_user_meta( $user->ID, 'webcomic_birthday', $birthday );
				} else if ( $save ) {
					setcookie( 'webcomic_birthday_' . COOKIEHASH, $birthday, ( integer ) current_time( 'timestamp' ) + 604800, COOKIEPATH );
				}
				
				return floor( ( ( integer ) current_time( 'timestamp' ) - $time ) / 31556926 ) >= $age;
			}
		}
		
		return null;
	}
	
	/** Verify a users role against allowed collection roles.
	 * 
	 * @param string $collection The collection to verify against.
	 * @param object $user The user to verify (defaults to the current user).
	 * @param array $roles The role or roles users must belong to. Overrides the collection role setting, or forces use of the collection role setting if -1.
	 * @return boolean|null
	 * @uses Webcomic::$config
	 * @uses Webcomic::$collection
	 */
	public static function verify_webcomic_role( $collection = '', $user = false, $roles = array() ) {
		$collection = $collection ? $collection : self::$collection;
		
		if ( empty( self::$config[ 'collections' ][ $collection ][ 'access' ][ 'byrole' ] ) and !$roles ) {
			return true;
		} else {
			$user  = is_object( $user ) ? $user : wp_get_current_user();
			$roles = ( $roles and -1 !== $roles ) ? explode( ',', $roles ) : self::$config[ 'collections' ][ $collection ][ 'access' ][ 'roles' ];
			
			if ( !empty( $user->ID ) and '!' === $roles[ 0 ] ) {
				return true;
			} else if ( isset( $user->roles ) ) {
				foreach ( $roles as $role ) {
					if ( in_array( $role, $user->roles ) ) {
						return true;
					}
				}
				
				return false;
			}
		}
		
		return null;
	}
	
	///
	// Single Webcomic Tags
	///
	
	/** Return webcomic attachments.
	 * 
	 * @param string $size The size attachments should be displayed at. May be any registered size; defaults are 'full', 'large', 'medium', and 'thumbnail'.
	 * @param string $relative Whether to link the webcomic. May be one of 'self', 'next', 'previous', 'first', 'last', 'random', or 'random-nocache'.
	 * @param mixed $in_same_term Whether the linked webcomic should be in a same term. May also be an array or comma-separated list of inclusive term IDs.
	 * @param mixed $excluded_terms An array or comma-separated list of excluded term IDs.
	 * @param string $taxonomy The taxonomy of the terms specified with $in_same_term and $excluded_terms arguments. The shorthand 'storyline' or 'character' may be used.
	 * @param mixed $the_post Post ID or object to return webcomic attachments for.
	 * @return string
	 * @uses Webcomic::$config
	 * @uses Webcomic::get_attachments()
	 * @uses WebcomicTag::relative_webcomic_link()
	 * @filter string the_webcomic Filters the output of `the_webcomic`.
	 */
	public static function the_webcomic( $size = 'full', $relative = '', $in_same_term = false, $excluded_terms = false, $taxonomy = 'storyline', $the_post = false ) {
		if ( $the_post = get_post( $the_post ) and isset( self::$config[ 'collections' ][ $the_post->post_type ] ) and $attachments = self::get_attachments( $the_post->ID ) ) {
			$output = '';
			
			foreach ( $attachments as $attachment ) {
				$output .= wp_get_attachment_image( $attachment->ID, $size );
			}
			
			$output = apply_filters( 'the_webcomic', $output, $the_post, $attachments );
			
			if ( 'self' === $relative ) {
				return sprintf( '<a href="%s" rel="bookmark">%s</a>', apply_filters( 'the_permalink', get_permalink( $the_post ) ), $output );
			} else if ( $relative ) {
				return self::relative_webcomic_link( '%link', $output, $relative, $in_same_term, $excluded_terms, $taxonomy, $the_post->post_type );
			} else {
				return $output;
			}
		}
	}
	
	/** Return an array of webomics related to the current webcomic.
	 * 
	 * @param boolean $storylines Match based on storylines.
	 * @param boolean $characters Match based on characters.
	 * @param mixed $the_post The post object or ID to match.
	 * @return array
	 * @filter array get_related_webcomics Filters the webcomics returned by `get_related_webcomics` and used by `the_related_webcomics`.
	 */
	public static function get_related_webcomics( $storylines = true, $characters = true, $the_post = false ) {
		if ( $the_post = get_post( $the_post ) and isset( self::$config[ 'collections' ][ $the_post->post_type ] ) ) {
			$storylines        = $storylines ? wp_get_object_terms( $the_post->ID, "{$the_post->post_type}_storyline", array( 'fields' => 'ids' ) ) : array();
			$characters        = $characters ? wp_get_object_terms( $the_post->ID, "{$the_post->post_type}_character", array( 'fields' => 'ids' ) ) : array();
			$story_related     = is_wp_error( $storylines ) ? array() : get_objects_in_term( $storylines[ array_rand( $storylines ) ], "{$the_post->post_type}_storyline" );
			$character_related = is_wp_error( $characters ) ? array() : get_objects_in_term( $characters[ array_rand( $characters ) ], "{$the_post->post_type}_character" );
			
			if ( $story_related and $character_related ) {
				$related_webcomics = array_intersect( $story_related, $character_related );
			} else if ( $story_related or $character_related ) {
				$related_webcomics = $story_related ? $story_related : $character_related;
			} else {
				$related_webcomics = array();
			}
			
			if ( false !== ( $key = array_search( $the_post->ID, $related_webcomics ) ) ) {
				unset( $related_webcomics[ $key ] );
			}
			
			return apply_filters( 'get_related_webcomics', $related_webcomics, $storylines, $characters, $the_post );
		}
		
		return array();
	}
	
	/** Returns a formatted list of related webcomics.
	 * 
	 * @param string $before Before list.
	 * @param string $sep Separate items using this.
	 * @param string $after After list.
	 * @param string $image Image size to use when displaying webcomic images for links.
	 * @param integer $limit The number of related webcomics to display.
	 * @param boolean $storylines Match based on storylines.
	 * @param boolean $characters Match based on characters.
	 * @param mixed $the_post The post object or ID to match.
	 * @return string
	 * @uses Webcomic::get_attachments()
	 * @uses WebcomicTag::get_related_webcomics()
	 */
	public static function the_related_webcomics( $before = '', $sep = ', ', $after = '', $image = '', $limit = 5, $storylines = true, $characters = true, $the_post = false ) {
		if ( $webcomics = self::get_related_webcomics( $storylines, $characters, $the_post ) ) {
			$count   = 0;
			$related = array();
			
			foreach ( $webcomics as $webcomic ) {
				$label = '';
				
				if ( $image and $attachments = self::get_attachments( $webcomic ) ) {
					foreach ( $attachments as $attachment ) {
						$label .= wp_get_attachment_image( $attachment->ID, $image );
					}
				} else {
					$label = get_the_title( $webcomic );
				}
				
				$related[] = sprintf( '<a href="%s">%s</a>', apply_filters( 'the_permalink', get_permalink( $webcomic ) ), $label );
				
				if ( 0 < $limit and $count >= $limit ) {
					break;
				} else {
					$count++;
				}
			}
			
			return $before . join( $sep, $related ) . $after;
		}
	}
	
	/** Return a relative webcomic.
	 * 
	 * @param string $relative The relative post to retrieve; one of 'next', 'previous', 'first', 'last', or 'random'.
	 * @param mixed $in_same_term Whether the linked webcomic should be in a same term. May also be an array or comma-separated list of inclusive term IDs.
	 * @param mixed $excluded_terms An array or comma-separated list of excluded term IDs.
	 * @param string $taxonomy The taxonomy of the terms specified with $in_same_term and $excluded_terms arguments. The shorthand 'storyline' or 'character' may be used.
	 * @param string $collection The collection to retrieve from. Used when retrieving first, last, or random webcomics outside of the loop.
	 * @return object
	 * @uses Webcomic::$config
	 * @uses Webcomic::$collection
	 * @filter string get_{$relative}_webcomic_join Filters the JOIN portion of the MySQL query used to retrieve relative webcomics, one of `next`, `previous`, `first`, `last`, or `random`.
	 * @filter string get_{$relative}_webcomic_where Filters the WHERE portion of the MySQL query used to retrieve relative webcomics, one of `next`, `previous`, `first`, `last`, or `random`.
	 * @filter string get_{$relative}_webcomic_sort Filters the SORT portion of the MySQL query used to retrieve relative webcomics, one of `next`, `previous`, `first`, `last`, or `random`.
	 */
	public static function get_relative_webcomic( $relative = 'random', $in_same_term = false, $excluded_terms = false, $taxonomy = 'storyline', $collection = '' ) {
		global $wpdb, $post;
		
		if ( 'previous' === $relative or 'next' === $relative ) {
			$collection = ( $post and isset( self::$config[ 'collections' ][ $post->post_type ] ) ) ? $post->post_type : '';
		} else if ( !$collection ) {
			$collection = ( $post and isset( self::$config[ 'collections' ][ $post->post_type ] ) ) ? $post->post_type : self::$collection;
		}
		
		if ( isset( self::$config[ 'collections' ][ $collection ] ) ) {
			$post_id  = $post ? $post->ID : 0;
			$taxonomy = ( 'storyline' === $taxonomy or 'character' === $taxonomy ) ? "{$collection}_{$taxonomy}" : $taxonomy;
			$exclude  = $join = '';
			
			if ( ( $in_same_term or $excluded_terms ) and $taxonomy ) {
				$join    = " INNER JOIN {$wpdb->term_relationships} AS tr ON p.ID = tr.object_id INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";
				$exclude = " AND tt.taxonomy = '{$taxonomy}'";
				
				if ( true === $in_same_term and $post_id ) {
					$include = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );
				} else if ( $in_same_term and true !== $in_same_term ) {
					$include = is_array( $in_same_term ) ? array_map( 'intval', $in_same_term ) : array_map( 'intval', explode( ',', $in_same_term ) );
				} else {
					$include = array();
				}
				
				if ( $include and !is_wp_error( $include ) ) {
					$join .= sprintf( " AND tt.taxonomy = '{$taxonomy}' AND tt.term_id IN ( %s )", join( ', ', $include ) );
				}
				
				if ( $excluded_terms ) {
					$exclude        = $include ? '' : $exclude;
					$excluded_terms = is_array( $excluded_terms ) ? array_map( 'intval', $excluded_terms ) : array_map( 'intval', explode( ',', $excluded_terms ) );
					$excluded_terms = $include ? array_diff( $excluded_terms, $include ) : $excluded_terms;
					
					if ( $excluded_terms ) {
						$exclude = sprintf( " AND tt.taxonomy = '{$taxonomy}' AND tt.term_id NOT IN ( %s )", join( ', ', $excluded_terms ) );
					}
				}
			}
			
			if ( 'previous' === $relative ) {
				$op = " p.post_date < '{$post->post_date}' AND";
			} else if ( 'next' === $relative ) {
				$op = " p.post_date > '{$post->post_date}' AND";
			} else {
				$op = '';
			}
			
			if ( 'first' === $relative or 'next' === $relative ) {
				$or = 'p.post_date ASC';
			} else if ( 'last' === $relative or 'previous' === $relative ) {
				$or = 'p.post_date DESC';
			} else {
				$or = 'RAND()';
			}
			
			$join      = apply_filters( "get_{$relative}_webcomic_join", $join, $in_same_term, $excluded_terms );
			$where     = apply_filters( "get_{$relative}_webcomic_where", "WHERE{$op} p.post_type = '{$collection}' AND p.post_status = 'publish'{$exclude}", $in_same_term, $excluded_terms );
			$sort      = apply_filters( "get_{$relative}_webcomic_sort", "ORDER BY {$or} LIMIT 1", $or );
			$query     = "SELECT p.* FROM {$wpdb->posts} AS p {$join} {$where} {$sort}";
			$query_key = 'relative_webcomic_' . md5( $query );
			$result    = wp_cache_get( $query_key, 'counts' );
			
			if ( false !== $result ) {
				return $result;
			}
			
			if ( !$result = $wpdb->get_row( $query ) ) {
				$result = $post ? $post : '';
			}
		
			wp_cache_set( $query_key, $result, 'counts' );
			
			return $result;
		}
	}
	
	/** Return a relative webcomic url.
	 * 
	 * @param string $relative The relative post to retrieve; one of 'next', 'previous', 'first', 'last', or 'random'.
	 * @param mixed $in_same_term Whether the linked webcomic should be in a same term. May also be an array or comma-separated list of inclusive term IDs.
	 * @param mixed $excluded_terms An array or comma-separated list of excluded term IDs.
	 * @param string $taxonomy The taxonomy of the terms specified with $in_same_term and $excluded_terms arguments. The shorthand 'storyline' or 'character' may be used.
	 * @param string $collection The collection to retrieve from. Used when retrieving first, last, or random webcomics outside of the loop.
	 * @return string
	 * @uses WebcomicTag::get_relative_webcomic()
	 * @filter string get_{$relative}_webcomic_link Filters the relative webcomic URL returned by `get_relative_webcomic_link` and used by `previous_webcomic_link`, `next_webcomic_link`, `first_webcomic_link`, `last_webcomic_link`, and `random_webcomic_link`.
	 */
	public static function get_relative_webcomic_link( $relative = 'random', $in_same_term = false, $excluded_terms = false, $taxonomy = 'storyline', $collection = '' ) {
		if ( $the_post = self::get_relative_webcomic( $relative, $in_same_term, $excluded_terms, $taxonomy, $collection ) ) {
			return apply_filters( "get_{$relative}_webcomic_link", apply_filters( 'the_permalink', get_permalink( $the_post ) ), $the_post, $in_same_term, $excluded_terms, $taxonomy, $collection );
		}
	}
	
	/** Return a parameter-based random webcomic url.
	 * 
	 * @param string $collection The collection to randomly select from.
	 * @param mixed $in_same_term An array or comma-separated list of inclusive term IDs.
	 * @param mixed $excluded_terms An array or comma-separated list of excluded term IDs.
	 * @param string $taxonomy The taxonomy of the terms specified with $in_same_term and $excluded_terms arguments. The shorthand 'storyline' or 'character' may be used.
	 * @return string
	 * @uses Webcomic::$config
	 * @filter string get_random-nocache_webcomic_link Filters the cache proof URL returned by `get_random_webcomic_link` and used by `relative_webcomic_link`.
	 */
	public static function get_random_webcomic_link( $collection, $in_same_term = false, $excluded_terms = false, $taxonomy = 'storyline' ) {
		return empty( self::$config[ 'collections' ][ $collection ] ) ? '' : apply_filters( 'get_random-nocache_webcomic_link', add_query_arg( array( 'random_webcomic' => $collection, 'in_same_story' => $in_same_term ? urlencode( maybe_serialize( $in_same_term ) ) : false, 'excluded_storylines' => $excluded_terms ? urlencode( maybe_serialize( $excluded_terms ) ) : false, 'taxonomy' => $taxonomy ? $taxonomy : false ), home_url() ), $collection, $in_same_term, $excluded_terms, $taxonomy );
	}
	
	/** Return a relative webcomic link.
	 * 
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $link Format string for the link text. Accepts %title, %date, and image size tokens.
	 * @param string $relative The relative post to retrieve; one of 'next', 'previous', 'first', 'last', or 'random'.
	 * @param mixed $in_same_term Whether the linked webcomic should be in a same term. May also be an array or comma-separated list of inclusive term IDs.
	 * @param mixed $excluded_terms An array or comma-separated list of excluded term IDs.
	 * @param string $taxonomy The taxonomy of the terms specified with $in_same_term and $excluded_terms arguments. The shorthand 'storyline' or 'character' may be used.
	 * @param string $collection The collection to retrieve from. Used when linking to first, last, or random webcomics outside of the loop.
	 * @return string
	 * @uses Webcomic::get_attachments()
	 * @uses WebcomicTag::get_relative_webcomic()
	 * @uses WebcomicTag::get_random_webcomic_link()
	 * @filter string {$relative}_webcomic_link Filters the output of the relative webcomic link template tags: `previous_webcomic_link`, `next_webcomic_link`, `first_webcomic_link`, `last_webcomic_link`, and `random_webcomic_link`.
	 */
	public static function relative_webcomic_link( $format, $link, $relative = 'random', $in_same_term = false, $excluded_terms = false, $taxonomy = 'storyline', $collection = '' ) {
		global $post;
		
		if ( 'random-nocache' === $relative or $the_post = self::get_relative_webcomic( $relative, $in_same_term, $excluded_terms, $taxonomy, $collection ) ) {
			if ( isset( $the_post ) ) {
				$collection = $the_post->post_type;
			} else if ( !$collection ) {
				$collection = ( $post and isset( self::$config[ 'collections' ][ $post->post_type ] ) ) ? $post->post_type : self::$collection;
			}
			
			$href     = 'random-nocache' === $relative ? self::get_random_webcomic_link( $collection, $in_same_term, $excluded_terms, $taxonomy ) : apply_filters( 'the_permalink', get_permalink( $the_post ) );
			$relative = str_replace( '-nocache', '', $relative );
			$class    = array( 'webcomic-link', "{$collection}-link", "{$relative}-webcomic-link", "{$relative}-{$collection}-link" );
			
			if ( 'random' !== $relative and $post and isset( $the_post ) and ( integer ) $post->ID === ( integer ) $the_post->ID ) {
				$class[] = 'current-webcomic';
			}
			
			if ( !$link ) {
				if ( 'previous' === $relative ) {
					$link = '&lsaquo;';
				} else if ( 'next' === $relative ) {
					$link = '&rsaquo;';
				} else if ( 'first' === $relative ) {
					$link = '&laquo;';
				} else if ( 'last' === $relative ) {
					$link = '&raquo;';
				} else {
					$link = '&infin;';
				}
			}
			
			if ( isset( $the_post ) and false !== strpos( $link, '%' ) ) {
				$tokens = array(
					'%date'  => mysql2date( get_option( 'date_format' ), $the_post->post_date ),
					'%title' => apply_filters( 'the_title', $the_post->post_title, $the_post->ID )
				);
				
				foreach ( array_merge( get_intermediate_image_sizes(), array( 'full' ) ) as $size ) {
					if ( false !== strpos( $link, "%{$size}" ) ) {
						$attachments = empty( $attachments ) ? self::get_attachments( $the_post->ID ) : $attachments;
						
						if ( !$attachments ) {
							break;
						} else {
							$image = '';
							
							foreach ( $attachments as $attachment ) {
								$image .= wp_get_attachment_image( $attachment->ID, $size );
							}
							
							$tokens[ "%{$size}" ] = $image;
						}
					}
				}
				
				$link = str_replace( array_keys( $tokens ), $tokens, $link );
			}
			
			$link = sprintf( '<a href="%s" class="%s"%s data-webcomic-dynamic="%s">%s</a>',
				$href,
				join( ' ', $class ),
				( 'previous' === $relative or 'next' === $relative ) ? sprintf( ' rel="%s"', str_replace( 'ious', '', $relative ) ) : '',
				"{$the_post->ID}/{$the_post->post_name}",
				$link
			);
			
			$format = str_replace( '%link', $link, $format );
			
			return apply_filters( "{$relative}_webcomic_link", $format, $link, $in_same_term, $excluded_terms, $taxonomy, $collection );
		}
	}
	
	/** Return a purchase webcomic url.
	 * 
	 * @param mixed $the_post Post object or ID to retrive the purchase link for.
	 * @return string
	 * @uses WebcomicTag::webcomic_prints_available()
	 * @filter string get_purchase_webcomic_link Filters the URL returned by `get_purchase_webcomic_link` and used by `purchase_webcomic_link`.
	 */
	public static function get_purchase_webcomic_link( $the_post = false ) {
		global $wp_rewrite;
		
		if ( self::webcomic_prints_available( false, $the_post ) ) {
			$link = $wp_rewrite->using_permalinks() ? user_trailingslashit( trailingslashit( apply_filters( 'the_permalink', get_permalink( $the_post ) ) ) . 'prints' ) : add_query_arg(  array( 'prints' => true ), apply_filters( 'the_permalink', get_permalink( $the_post ) ) );
			
			return apply_filters( 'get_purchase_webcomic_link', $link, $the_post );
		}
	}
	
	/** Return a purchase webcomic link.
	 * 
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $link Format string for the link text. Accepts %title, %date, and image size tokens.
	 * @param mixed $the_post The post object or ID to retrive the purchase link for.
	 * @return string
	 * @uses Webcomic::get_attachments()
	 * @uses WebcomicTag::get_purchase_webcomic_link()
	 * @filter string purchase_webcomic_link Filters the output of `purchase_webcomic_link`.
	 */
	public static function purchase_webcomic_link( $format, $link, $the_post = false ) {
		if ( $the_post = get_post( $the_post ) and $href = self::get_purchase_webcomic_link( $the_post ) ) {
			$class = array( 'webcomic-link', "{$the_post->post_type}-link", "purchase-webcomic-link", "purchase-{$the_post->post_type}-link" );
			
			if ( false !== strpos( $link, '%' ) ) {
				$tokens = array(
					'%date'  => mysql2date( get_option( 'date_format' ), $the_post->post_date ),
					'%title' => apply_filters( 'the_title', $the_post->post_title, $the_post->ID )
				);
				
				foreach ( array_merge( get_intermediate_image_sizes(), array( 'full' ) ) as $size ) {
					if ( false !== strpos( $link, "%{$size}" ) ) {
						$attachments = empty( $attachments ) ? self::get_attachments( $the_post->ID ) : $attachments;
						
						if ( !$attachments ) {
							break;
						} else {
							$image = '';
							
							foreach ( $attachments as $attachment ) {
								$image .= wp_get_attachment_image( $attachment->ID, $size );
							}
							
							$tokens[ "%{$size}" ] = $image;
						}
					}
				}
				
				$link = str_replace( array_keys( $tokens ), $tokens, $link );
			}
			
			$link   = sprintf( '<a href="%s" class="%s">%s</a>', $href, join( ' ', $class ), $link );
			$format = str_replace( '%link', $link, $format );
			
			return apply_filters( 'purchase_webcomic_link', $format, $link, $the_post );
		}
	}
	
	/** Return a webcomic collection link.
	 * 
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $link Format string for the link text. Accepts %title and image size tokens tokens.
	 * @param string $target Where the collection links should point to; may be one of 'archive', 'first', 'last', or 'random'.
	 * @param string $collection The collection ID to return a link for.
	 * @return string
	 * @uses Webcomic::$config
	 * @uses WebcomicTag::get_relative_webcomic_link()
	 * @filter string webcomic_collection_link Filters the output of `the_webcomic_collection`.
	 */
	public static function webcomic_collection_link( $format, $link, $target = 'archive', $collection = '' ) {
		global $post;
		
		if ( !$collection ) {
			$collection = ( $post and isset( self::$config[ 'collections' ][ $post->post_type ] ) ) ? $post->post_type : self::$collection;
		}
		
		if ( $collection ) {
			$class  = array( 'webcomic-link', 'webcomic-collection-link', "{$collection}-collection-link" );
			$href   = ( 'first' === $target or 'last' === $target or 'random' === $target ) ? self::get_relative_webcomic_link( $target, false, false, '', $collection ) : get_post_type_archive_link( $collection );
			
			if ( false !== strpos( $link, '%' ) ) {
				$tokens = array(
					'%title' => self::$config[ 'collections' ][ $collection ][ 'name' ]
				);
				
				if ( self::$config[ 'collections' ][ $collection ][ 'image' ] ) {
					foreach ( array_merge( get_intermediate_image_sizes(), array( 'full' ) ) as $size ) {
						if ( false !== strpos( $link, "%{$size}" ) ) {
							$tokens[ "%{$size}" ] = wp_get_attachment_image( $attachment->ID, $size );
						}
					}
				}
				
				$link = str_replace( array_keys( $tokens ), $tokens, $link );
			}
			
			$link   = sprintf( '<a href="%s" class="%s">%s</a>', $href, join( ' ', $class ), $link );
			$format = str_replace( '%link', $link, $format );
			
			return apply_filters( 'webcomic_collection_link', $format, $link, $target, $collection );
		}
	}
	
	/** Return a formatted list of terms related to the current webcomic.
	 * 
	 * @param integer $id The post ID to retrieve terms for.
	 * @param string $taxonomy The taxonomy the terms must belong to; may be the shorthand 'storyline' or 'character'.
	 * @param string $before Before list.
	 * @param string $sep Separate items using this.
	 * @param string $after After list.
	 * @param string $target Where the term links should point to, one of 'archive', 'first', 'last', or 'random'.
	 * @param string $image Image size to use when displaying term images for links.
	 * @return string
	 * @uses WebcomicTag::get_relative_webcomic_link()
	 * @filter string webcomic_term_links-webcomic\d+_{$taxonomy} Filters the array of term links generated by `get_the_webcomic_term_list` and used by `the_webcomic_storylines` and `the_webcomic_characters`.
	 * @filter string the_webcomic_term_list Filters the output of `get_the_webcomic_term_list` used by `the_webcomic_storylines` and `the_webcomic_characters`.
	 */
	public static function get_the_webcomic_term_list( $id = 0, $taxonomy, $before = '', $sep = ', ', $after = '', $target = 'archive', $image = '' ) {
		global $post;
		
		$id        = $id ? $id : $post->ID;
		$post_type = get_post_type( $id );
		$taxonomy  = ( 'storyline' === $taxonomy or 'character' === $taxonomy ) ? "{$post_type}_{$taxonomy}" : $taxonomy;
		
		if ( preg_match( '/^webcomic\d+_(storyline|character)$/', $taxonomy ) and $terms = get_the_terms( $id, $taxonomy ) and !is_wp_error( $terms ) ) {
			$term_links = array();
			
			foreach ( $terms as $term ) {
				$link = ( 'first' === $target or 'last' === $target or 'random' === $target ) ? self::get_relative_webcomic_link( $target, $term->ID, false, $term->taxonomy, $post_type ) : get_term_link( $term, $term->taxonomy );
				
				if ( is_wp_error( $link ) ) {
					return '';
				}
				
				$label = ( $image and $term->webcomic_image ) ? wp_get_attachment_image( $term->webcomic_image, $image ) : $term->name;
				
				$term_links[] = sprintf( '<a href="%s" rel="tag">%s</a>', $link, $label );
			}
			
			$term_links = apply_filters( "webcomic_term_links-{$taxonomy}", $term_links, $terms, $before, $sep, $after, $target, $image );
			
			return apply_filters( 'the_webcomic_term_list', $before . join( $sep, $term_links ) . $after, $id, $before, $sep, $after, $target, $image, $taxonomy );
		}
	}
	
	///
	// Single Term Tags
	///
	
	/** Return a relative term.
	 * 
	 * @param string $relative The relative term to retrieve; one of 'next', 'previous', 'first', 'last', or 'random'.
	 * @param string $taxonomy The taxonomy the relative term must belong to.
	 * @param array $args An array of arguments to pass to get_terms().
	 * @return object
	 */
	public static function get_relative_webcomic_term( $relative = 'random', $taxonomy = '', $args = array() ) {
		global $post;
		
		$object = is_tax() ? get_queried_object() : false;
		
		if ( !taxonomy_exists( $taxonomy ) and is_tax() ) {
			$taxonomy = $object->taxonomy;
		} else if ( ( 'next' === $relative or 'previous' === $relative ) and is_singular() and $terms = wp_get_object_terms( $post->ID, $taxonomy, array_merge( array( 'hide_empty' => true, 'orderby' => is_taxonomy_hierarchical( $taxonomy ) ? 'term_group' : 'name' ), ( array ) $args, array( 'cache_domain' => 'get_relative_webcomic_term' ) ) ) and !is_wp_error( $terms ) ) {
			$object = 'next' === $relative ? array_pop( $terms ) : array_shift( $terms );
		}
		
		$args = array_merge( array( 'hide_empty' => true, 'orderby' => is_taxonomy_hierarchical( $taxonomy ) ? 'term_group' : 'name' ), ( array ) $args, array( 'cache_domain' => 'get_relative_webcomic_term' ) );
		
		if ( taxonomy_exists( $taxonomy ) and ( 'previous' === $relative or 'next' === $relative ) ? isset( $object ) : true ) {
			if ( 'first' === $relative and $terms = get_terms( $taxonomy, array_merge( $args, array( 'parent' => 0 ) ) ) and !is_wp_error( $terms ) ) {
				$object = $terms[ 0 ];
			} else if ( 'random' === $relative and $terms = get_terms( $taxonomy, $args ) and !is_wp_error( $terms ) ) {
				shuffle( $terms );
				
				$object = $terms[ 0 ];
			} else if ( 'last' === $relative and $terms = get_terms( $taxonomy, array_merge( $args, array( 'parent' => 0 ) ) ) and !is_wp_error( $terms ) ) {
				$last = array_pop( $terms );
				
				while( $children = get_terms( $last->taxonomy, array_merge( $args, array( 'parent' => $last->term_id ) ) ) ) {
					$last = array_pop( $children );
				}
				
				$object = $last;
			} else if ( 'previous' === $relative ) {
				if ( !$object->term_group and $object->parent ) {
					$object = get_term( $object->parent, $object->taxonomy );
				} else if ( $terms = get_terms( $object->taxonomy, array_merge( $args, array( 'parent' => $object->parent ) ) ) and !is_wp_error( $terms ) and false !== ( $key = array_search( $object, $terms ) ) and isset( $terms[ $key - 1 ] ) ) {
					$previous = $terms[ $key - 1 ];
					
					while ( $children = get_terms( $previous->taxonomy, array_merge( $args, array( 'parent' => $previous->term_id ) ) ) ) {
						$previous = array_pop( $children );
					}
					
					$object = $previous;
				}
			} else if ( 'next' === $relative ) {
				if ( $children = get_terms( $object->taxonomy, array_merge( $args, array( 'parent' => $object->term_id ) ) ) and !is_wp_error( $children ) ) {
					$object = $children[ 0 ];
				} else if ( $terms = get_terms( $object->taxonomy, array_merge( $args, array( 'parent' => $object->parent ) ) ) and !is_wp_error( $terms ) and false !== ( $key = array_search( $object, $terms ) ) and isset( $terms[ $key + 1 ] ) ) {
					$object = $terms[ $key + 1 ];
				} else {
					$next = $object;
					
					while ( $next = get_term( $next->parent, $next->taxonomy ) ) {
						if ( $children = get_terms( $next->taxonomy, array_merge( $args, array( 'parent' => $next->parent ) ) ) and !is_wp_error( $children ) and false !== ( $key = array_search( $next, $children ) ) and isset( $children[ $key + 1 ] ) ) {
							$object = $children[ $key + 1 ];
							
							break;
						}
						
						if ( !$next->parent ) {
							break;
						}
					}
				}
			}
			
			return $object;
		}
	}
	
	/** Return a relative term url.
	 * 
	 * @param string $target The target url, one of 'archive', 'first', 'last', or 'random'.
	 * @param string $relative The relative term to retrieve; one of 'next', 'previous', 'first', 'last', or 'random'.
	 * @param string $taxonomy The taxonomy the relative term must belong to.
	 * @param array $args An array of arguments to pass to get_terms().
	 * @return string
	 * @uses WebcomicTag::get_relative_webcomic_term()
	 * @filter string get_{$relative}_webcomic_term_link Filters the URL returned by `get_relative_webcomic_term_link` and used by the 
	 */
	public static function get_relative_webcomic_term_link( $target = 'archive', $relative = 'random', $taxonomy = '', $args = array() ) {
		global $wpdb;
		
		$args = 'archive' === $target ? $args : array_merge( $args, array( 'hide_empty' => true ) );
		
		if ( $term = self::get_relative_webcomic_term( $relative, $taxonomy, $args ) ) {
			if ( 'archive' !== $target and $objects = get_objects_in_term( $term->term_id, $term->taxonomy ) ) {
				if ( 'first' === $target or 'last' === $target ) {
					$post_id = $wpdb->get_var( sprintf( "SELECT ID FROM {$wpdb->posts} WHERE ID IN ( %s ) ORDER BY post_date %s LIMIT 1", join( ', ', $objects ), 'last' === $target ? 'DESC' : 'ASC' ) );
					$link    = apply_filters( 'the_permalink', get_permalink( $post_id ) );
				} else {
					shuffle( $objects );
					
					$link = apply_filters( 'the_permalink', get_permalink( $objects[ 0 ] ) );
				}
			} else {
				$link = get_term_link( $term, $term->taxonomy );
			}
			
			return apply_filters( "get_{$relative}_webcomic_term_link", $link, $target, $term, $args );
		}
	}
	
	/** Return a parameter-based random term url.
	 * 
	 * @param string $taxonomy The taxonomy the relative term must belong to.
	 * @param string $target The target url, one of 'archive', 'first', 'last', or 'random'.
	 * @param array $args An array of arguments to pass to get_terms().
	 * @return string
	 * @filter string get_random-nocache_webcomic_term_link Filters the cache-proof URL returned by `get_random_webcomic_term_link` and used by `random_webcomic_link`.
	 */
	public static function get_random_webcomic_term_link( $taxonomy, $target = 'archive', $args = array() ) {
		return apply_filters( 'get_random-nocache_webcomic_term_link', add_query_arg( array( 'random_webcomic_term' => $taxonomy, 'target' => $target, 'args' => $args ? urlencode( maybe_serialize( $args ) ) : false ), home_url() ), $taxonomy, $target, $args );
	}
	
	/** Return a relative term link.
	 * 
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $link Format string for the link text. Accepts %title and image size tokens.
	 * @param string $target The target url, one of 'archive', 'first', 'last', or 'random'.
	 * @param string $relative The relative term to retrieve; one of 'next', 'previous', 'first', 'last', or 'random'.
	 * @param string $taxonomy The taxonomy the relative term must belong to.
	 * @param array $args An array of arguments to pass to get_terms().
	 * @return string
	 * @uses WebcomicTag::get_relative_webcomic_term()
	 * @uses WebcomicTag::get_relative_webcomic_term_link()
	 * @uses WebcomicTag::get_random_webcomic_term_link()
	 * @filter string {$relative}_webcomic_term_link Filters the output of the relative webcomic term link template tags: `previous_webcomic_storyline_link`, `next_webcomic_storyline_link`, `first_webcomic_storyline_link`, `last_webcomic_storyline_link`, `random_webcomic_storyline_link`, `previous_webcomic_character_link`, `next_webcomic_character_link`, `first_webcomic_character_link`, `last_webcomic_character_link`, `random_webcomic_character_link`.
	 */
	public static function relative_webcomic_term_link( $format, $link, $target = 'archive', $relative = 'random', $taxonomy = '', $args = array() ) {
		global $wpdb;
		
		if ( 'random-nocache' === $relative or $term = self::get_relative_webcomic_term( $relative, $taxonomy, $args ) ) {
			$object   = get_queried_object();
			$taxonomy = isset( $term ) ? $term->taxonomy : $taxonomy;
			$href     = 'random-nocache' === $relative ? self::get_random_webcomic_term_link( $taxonomy, $target, $args ) : self::get_relative_webcomic_term_link( $target, $relative, $taxonomy, $args );
			$relative = str_replace( '-nocache', '', $relative );
			$class    = array( 'term-link', "{$taxonomy}-link", "{$relative}-term-link", "{$relative}-{$taxonomy}-link" );
			
			if ( 'random' !== $relative and isset( $object->taxonomy, $term ) and ( integer ) $object->term_id === ( integer ) $term->term_id ) {
				$class[] = 'current-term';
			}
			
			if ( !$link ) {
				if ( 'previous' === $relative ) {
					$link = '&lsaquo; %title';
				} else if ( 'next' === $relative ) {
					$link = '%title &rsaquo;';
				} else if ( 'first' === $relative ) {
					$link = '&laquo; %title';
				} else if ( 'last' === $relative ) {
					$link = '%title &raquo;';
				} else {
					$link = ' %title';
				}
			}
			
			if ( isset( $term ) and false !== strpos( $link, '%' ) ) {
				$tokens = array(
					'%title' => $term->name
				);
				
				if ( $term->webcomic_image ) {
					foreach ( array_merge( get_intermediate_image_sizes(), array( 'full' ) ) as $size ) {
						if ( false !== strpos( $link, "%{$size}" ) ) {
							$tokens[ "%{$size}" ] = wp_get_attachment_image( $term->webcomic_image, $size );
						}
					}
				}
				
				$link = str_replace( array_keys( $tokens ), $tokens, $link );
			}
			
			$link = sprintf( '<a href="%s" class="%s"%s>%s</a>',
				$href,
				join( ' ', $class ),
				( 'previous' === $relative or 'next' === $relative ) ? sprintf( ' rel="%s"', str_replace( 'ious', '', $relative ) ) : '',
				$link
			);
			
			$format = str_replace( '%link', $link, $format );
			
			return apply_filters( "{$relative}_webcomic_term_link", $format, $link, $target, $term, $args );
		}
	}
	
	/** Return a term title.
	 * 
	 * @param string $prefix Content to display before the title.
	 * @param mixed $term Term ID or object to return a title for.
	 * @param string $taxonomy The taxonomy $term belongs to.
	 * @return string
	 * @filter string webcomic_term_title Filters the term title returned by `webcomic_term_title` and used by `webcomic_storyline_title` and `webcomic_character_title`.
	 */
	public static function webcomic_term_title( $prefix = '', $term = 0, $taxonomy = '' ) {
		$term = $taxonomy ? get_term( $term, $taxonomy ) : get_queried_object();
		
		if ( isset( $term->taxonomy ) and preg_match( '/^webcomic\d+_(storyline|character)$/', $term->taxonomy ) ) {
			$title = apply_filters( 'single_term_title', $term->name );
			
			return $prefix . apply_filters( 'webcomic_term_title', $title, $prefix, $term );
		}
	}
	
	/** Return a term description.
	 * 
	 * @param integer $term Term ID to return a description for. Will use global term ID by default.
	 * @param string $taxonomy Taxonomy the term belongs to.
	 * @return string
	 * @filter string webcomic_term_description Filters the term description returned by `webcomic_term_description` and used by `webcomic_storyline_description` and `webcomic_character_description`.
	 */
	public static function webcomic_term_description( $term = 0, $taxonomy = '' ) {
		$term = $taxonomy ? get_term( $term, $taxonomy ) : get_queried_object();
		
		if ( isset( $term->taxonomy ) ) {
			return apply_filters( 'webcomic_term_description', term_description( $term->term_id, $taxonomy ), $term );
		}
	}
	
	/** Return a term image.
	 * 
	 * @param string $size The size of the image to return.
	 * @param integer $term Term ID. Will use global term ID by default.
	 * @param string $taxonomy The taxonomy $term must belongs to.
	 * @return string
	 * @filter string webcomic_term_image Filters the term image returned by `webcomic_term_image` and used by `webcomic_storyline_cover` and `webcomic_character_avatar`.
	 */
	public static function webcomic_term_image( $size = 'full', $term = 0, $taxonomy = '' ) {
		$term = $taxonomy ? get_term( $term, $taxonomy ) : get_queried_object();
		
		if ( isset( $term->webcomic_image ) ) {
			return apply_filters( "webcomic_term_image", wp_get_attachment_image( $term->webcomic_image, $size ), $size, $term );
		}
	}
	
	///
	// Single Collection Tags
	///
	
	/** Return a collection title.
	 * 
	 * @param string $prefix Content to display before the title.
	 * @param string $collection Collection ID to return a title for.
	 * @return string
	 * @uses Webcomic::$config
	 */
	public static function webcomic_collection_title( $prefix = '', $collection = '' ) {
		$object = isset( self::$config[ 'collections' ][ $collection ] ) ? get_post_type_object( $collection ) : get_queried_object();
		
		if ( !empty( $object->labels->name ) ) {
			$title = apply_filters( 'post_type_archive_title', $object->labels->name );
			
			return $prefix . apply_filters( 'webcomic_archive_title', $title, $prefix, $collection );
		}
	}
	
	/** Return a formatted webcomic collection description.
	 * 
	 * Filters: webcomic_collection_description
	 * 
	 * @param string $collection The collection to retrieve a description for.
	 * @return string
	 * @uses Webcomic::$config
	 * @uses Webcomic::$collection
	 * @filter string webcomic_collection_description Filters the output of `webcomic_collection_description`.
	 */
	public static function webcomic_collection_description( $collection = '' ) {
		$collection = $collection ? $collection : self::$collection;
		
		return empty( self::$config[ 'collections' ][ $collection ] ) ? '' : apply_filters( 'webcomic_collection_description', wpautop( self::$config[ 'collections' ][ $collection ][ 'description' ] ), $collection );
	}
	
	/** Return a collection image.
	 * 
	 * Filters: webcomic_collection_image
	 * 
	 * @param string $size The size of the image to return.
	 * @param string $collection Collection ID. Will use global post type by default.
	 * @return string
	 * @uses Webcomic::$config
	 * @uses Webcomic::$collection
	 * @filter string webcomic_collection_image Filters the the image returned by `webcomic_collection_image` and used by `webcomic_collection_poster`.
	 */
	public static function webcomic_collection_image( $size = 'full', $collection = '' ) {
		$collection = $collection ? $collection : self::$collection;
		
		return empty( self::$config[ 'collections' ][ $collection ][ 'image' ] ) ? '' : apply_filters( 'webcomic_collection_image', wp_get_attachment_image( self::$config[ 'collections' ][ $collection ][ 'image' ], $size ), $size, $collection );
	}
	
	/** Return a formatted collection print amount.
	 * 
	 * @param string $type The amount to return, one of 'domestic', 'domestic-price', 'domestic-shipping', 'international', 'international-price', 'international-shipping', 'original', 'original-price', or 'original-shipping'.
	 * @param string $dec Decimal point for number_format().
	 * @param string $sep Thousands separator for number_format().
	 * @param string $collection Collection ID. Will use Webcomic::$collection by default.
	 * @return string
	 * @uses Webcomic::$config
	 * @uses Webcomic::$collection
	 * @filter string webcomic_collection_print_amount Filters the output of `webcomic_collection_print_amount`.
	 */
	public static function webcomic_collection_print_amount( $type, $dec = '.', $sep = ',', $collection = '' ) {
		$collection = $collection ? $collection : self::$collection;
		
		if ( !$type or empty( self::$config[ 'collections' ][ $collection ][ 'commerce' ][ 'business' ] ) ) {
			return;
		}
		
		$type   = explode( '-', $type );
		$amount = empty( $type[ 1 ] ) ? self::$config[ 'collections' ][ $collection ][ 'total' ][ $type[ 0 ] ] : self::$config[ 'collections' ][ $collection ][ $type[ 1 ] ][ $type[ 0 ] ];
		$output = sprintf( '%s %s',
			number_format( $amount, 2, $dec, $sep ),
			self::$config[ 'collections' ][ $collection ][ 'commerce' ][ 'currency' ]
		);
		
		return apply_filters( 'webcomic_collection_print_amount', $output, $dec, $sep, $collection );
	}
	
	
	///
	// Commerce Tags
	///
	
	/** Return a donation amount.
	 * 
	 * @param string $dec Decimal point for number_format().
	 * @param string $sep Thousands separator for number_format().
	 * @param string $collection The collection to render a donation amount for.
	 * @return string
	 * @uses Webcomic::$config
	 * @uses Webcomic::$collection
	 * @filter string webcomic_donation_amount Filters the output of `webcomic_donation_amount`.
	 */
	public static function webcomic_donation_amount( $dec = '.', $sep = ',', $collection = '' ) {
		$collection = $collection ? $collection : self::$collection;
		
		if ( empty( self::$config[ 'collections' ][ $collection ][ 'commerce' ][ 'business' ] ) or !self::$config[ 'collections' ][ $collection ][ 'commerce' ][ 'donation' ] ) {
			return '';
		}
		
		$output = sprintf( '%s %s',
			number_format( self::$config[ 'collections' ][ $collection ][ 'commerce' ][ 'donation' ], 2, $dec, $sep ),
			self::$config[ 'collections' ][ $collection ][ 'commerce' ][ 'currency' ]
		);
		
		return apply_filters( 'webcomic_donation_amount', $output, $dec, $sep, $collection );
	}
	
	/** Return hidden donation form fields.
	 * 
	 * @param string $collection The collection to render donation fields for.
	 * @return string
	 * @uses Webcomic::$config
	 * @filter string webcomic_donation_fields Filters the output of `webcomic_donation_fields`.
	 */
	public static function webcomic_donation_fields( $collection = '' ) {
		$collection = $collection ? $collection : self::$collection;
		
		if ( empty( self::$config[ 'collections' ][ $collection ][ 'commerce' ][ 'business' ] ) ) {
			return '';
		}
		
		$output = sprintf( '
			<input type="hidden" name="return" value="%s">
			<input type="hidden" name="business" value="%s">
			<input type="hidden" name="item_name" value="%s">
			<input type="hidden" name="notify_url" value="%s">
			<input type="hidden" name="item_number" value="%s">
			<input type="hidden" name="cmd" value="_donations">
			<input type="hidden" name="currency_code" value="%s">%s',
			home_url(),
			self::$config[ 'collections' ][ $collection ][ 'commerce' ][ 'business' ],
			esc_attr( substr( self::$config[ 'collections' ][ $collection ][ 'name' ], 0, 127 ) ),
			add_query_arg( array( 'webcomic_commerce_ipn' => 'donation' ), home_url( '/' ) ),
			$collection,
			self::$config[ 'collections' ][ $collection ][ 'commerce' ][ 'currency' ],
			self::$config[ 'collections' ][ $collection ][ 'commerce' ][ 'donation' ] ? sprintf( '<input type="hidden" name="amount" value="%s">', self::$config[ 'collections' ][ $collection ][ 'commerce' ][ 'donation' ] ) : ''
		);
		
		$output = apply_filters( 'webcomic_donation_fields', $output, $collection );
		
		return $output;
	}
	
	/** Return a donation form.
	 * 
	 * https://www.paypal.com/cgi-bin/webscr | https://www.sandbox.paypal.com/cgi-bin/webscr
	 * 
	 * @param string $label The form submit button label.
	 * @param string $collection The collection to render a donation form for.
	 * @return string
	 * @uses Webcomic::$config
	 * @uses Webcomic::$collection
	 * @uses WebcomicTag::webcomic_donation_fields()
	 * @filter string webcomic_donation_form Filters the output of `webcomic_donation_form`.
	 */
	public static function webcomic_donation_form( $label = '', $collection = '' ) {
		$collection = $collection ? $collection : self::$collection;
		
		if ( !$fields = self::webcomic_donation_fields( $collection ) ) {
			return;
		}
		
		$output = sprintf( '
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" class="webcomic-donation-form %s-donation-form">
				<button type="submit">%s</button>
				%s
			</form>',
			$collection,
			$label ? $label : sprintf( __( 'Support %s', 'webcomic' ), esc_html( self::$config[ 'collections' ][ $collection ][ 'name' ] ) ),
			$fields
		);
		
		return apply_filters( 'webcomic_donation_form', $output, $label, $collection );
	}
	
	/** Return a formatted webcomic print amount.
	 * 
	 * @param string $type The amount to return, one of 'domestic', 'domestic-price', 'domestic-shipping', 'international', 'international-price', 'international-shipping', 'original', 'original-price', or 'original-shipping'.
	 * @param string $dec Decimal point for number_format().
	 * @param string $sep Thousands separator for number_format().
	 * @param mixed $the_post The post object or ID to get print amounts for.
	 * @return string
	 * @uses Webcomic::$config
	 * @filter string webcomic_print_amount Filters the output of `webcomic_print_amount`.
	 */
	public static function webcomic_print_amount( $type, $dec = '.', $sep = ',', $the_post = '' ) {
		$type = explode( '-', $type );
		
		if ( !$the_post = get_post( $the_post ) or empty( self::$config[ 'collections' ][ $the_post->post_type ][ 'commerce' ][ 'business' ] ) or !get_post_meta( $the_post->ID, 'webcomic_prints', true ) or ( 'original' === $type[ 0 ] and !get_post_meta( $the_post->ID, 'webcomic_original', true ) ) ) {
			return;
		}
		
		$commerce = get_post_meta( $the_post->ID, 'webcomic_commerce', true );
		$amount   = 1 === count( $type ) ? $commerce[ 'total' ][ $type[ 0 ] ] : $commerce[ $type[ 1 ] ][ $type[ 0 ] ];
		$output   = sprintf( '%s %s',
			number_format( $amount, 2, $dec, $sep ),
			self::$config[ 'collections' ][ $the_post->post_type ][ 'commerce' ][ 'currency' ]
		);
		
		return apply_filters( 'webcomic_print_amount', $output, $type, $dec, $sep, $the_post );
	}
	
	/** Return a formatted webcomic print adjustment.
	 * 
	 * @param string $type The adjustment to return, one of 'domestic', 'domestic-price', 'domestic-shipping', 'international', 'international-price', 'international-shipping', 'original', 'original-price', or 'original-shipping'.
	 * @param mixed $the_post The post object or ID to get print adjustments for.
	 * @return string
	 * @uses Webcomic::$config
	 * @filter string webcomic_print_adjustment Filters the output of `webcomic_print_adjustment`.
	 */
	public static function webcomic_print_adjustment( $type, $the_post = '' ) {
		$type = explode( '-', $type );
		
		if ( !$the_post = get_post( $the_post ) or empty( self::$config[ 'collections' ][ $the_post->post_type ][ 'commerce' ][ 'business' ] ) or !get_post_meta( $the_post->ID, 'webcomic_prints', true ) or ( 'original' === $type[ 0 ] and !get_post_meta( $the_post->ID, 'webcomic_original', true ) ) ) {
			return;
		}
		
		$commerce = get_post_meta( $the_post->ID, 'webcomic_commerce', true );
		$output   = sprintf( '%s%%', $commerce[ 'adjust' ][ 1 === count( $type ) ? 'total' : $type[ 1 ] ][ $type[ 0 ] ] );
		
		return apply_filters( 'webcomic_print_adjustment', $output, $type, $the_post );
	}
	
	/** Return hidden print form fields.
	 * 
	 * @param string $type The type of form fields to return, one of 'domestic', 'international', 'original', or 'cart'.
	 * @param mixed $the_post The post object or ID to get print adjustments for.
	 * @return string
	 * @uses Webcomic::$config
	 * @filter string webcomic_print_fields Filters the output of `webcomic_print_fields`.
	 */
	public static function webcomic_print_fields( $type, $the_post = false ) {
		if ( !$type or !$the_post = get_post( $the_post ) or empty( self::$config[ 'collections' ][ $the_post->post_type ][ 'commerce' ][ 'business' ] ) or ( 'cart' === $type and '_cart' !== self::$config[ 'collections' ][ $the_post->post_type ][ 'commerce' ][ 'method' ] ) or !get_post_meta( $the_post->ID, 'webcomic_prints', true ) or ( 'original' === $type and !get_post_meta( $the_post->ID, 'webcomic_original', true ) ) ) {
			return '';
		}
		
		$commerce = get_post_meta( $the_post->ID, 'webcomic_commerce', true );
		$quantity = 'original' === $type ? 'quantity' : 'undefined_quantity';
		$output   = 'cart' === $type ? sprintf( '
			<input type="hidden" name="cmd" value="%s">
			<input type="hidden" name="business" value="%s">
			<input type="hidden" name="display" value="1">',
			self::$config[ 'collections' ][ $the_post->post_type ][ 'commerce' ][ 'method' ],
			self::$config[ 'collections' ][ $the_post->post_type ][ 'commerce' ][ 'business' ]
		) : sprintf( '
			<input type="hidden" name="cmd" value="%s">
			<input type="hidden" name="return" value="%s">
			<input type="hidden" name="amount" value="%s">
			<input type="hidden" name="business" value="%s">
			<input type="hidden" name="item_name" value="%s">
			<input type="hidden" name="notify_url" value="%s">
			<input type="hidden" name="item_number" value="%s">
			<input type="hidden" name="currencey_code" value="%s">
			<input type="hidden" name="%s" value="1">%s',
			self::$config[ 'collections' ][ $the_post->post_type ][ 'commerce' ][ 'method' ],
			self::get_purchase_webcomic_link( $the_post ),
			$commerce[ 'total' ][ $type ],
			self::$config[ 'collections' ][ $the_post->post_type ][ 'commerce' ][ 'business' ],
			substr( $the_post->post_title, 0, 127 ),
			add_query_arg( array( 'webcomic_commerce_ipn' => 'print' ), home_url( '/' ) ),
			substr( "{$the_post->ID}-{$the_post->post_type}-{$type}", 0, 127 ),
			self::$config[ 'collections' ][ $the_post->post_type ][ 'commerce' ][ 'currency' ],
			'_cart' === self::$config[ 'collections' ][ $the_post->post_type ][ 'commerce' ][ 'method' ] ? 'add' : $quantity,
			'_cart' === self::$config[ 'collections' ][ $the_post->post_type ][ 'commerce' ][ 'method' ] ? sprintf( '<input type="hidden" name="shopping_url" value="%s">', self::get_purchase_webcomic_link( $the_post ) ) : ''
		);
		
		$output = apply_filters( 'webcomic_print_fields', $output, $type, $the_post, $commerce );
		
		return $output;
	}
	
	/** Return a print purchase form.
	 * 
	 * https://www.paypal.com/cgi-bin/webscr | https://www.sandbox.paypal.com/cgi-bin/webscr
	 * 
	 * @param string $type The type of print form, one of 'domestic', 'international', 'original', or 'cart'.
	 * @param string $label The form submit button label. Accepts %dec, %sep, %total, %price, and %shipping tokens.
	 * @param mixed $the_post The post object or ID to get print adjustments for.
	 * @return string
	 * @uses Webcomic::$config
	 * @uses WebcomicTag::webcomic_print_amount()
	 * @uses WebcomicTag::webcomic_print_fields()
	 * @filter string webcomic_print_form Filters the output of `webcomic_print_form`.
	 */
	public static function webcomic_print_form( $type, $label = '', $the_post = false ) {
		if ( !$the_post = get_post( $the_post ) or !$fields = self::webcomic_print_fields( $type, $the_post ) ) {
			return;
		}
		
		if ( !$label ) {
			if ( 'cart' === $type ) {
				$label = __( 'View Cart', 'webcomic' );
			} else {
				$label = '_cart' === self::$config[ 'collections' ][ $the_post->post_type ][ 'commerce' ][ 'method' ] ? __( 'Add to Cart', 'webcomic' ) : __( 'Buy Now', 'webcomic' );
			}
		} else {
			$match = array();
			
			preg_match( '/%dec(.)/', $label, $match );
			
			$dec = !empty( $match[ 1 ] ) ? $match[ 1 ] : '.';
			
			preg_match( '/%sep(.)/', $label, $match );
			
			$sep   = !empty( $match[ 1 ] ) ? $match[ 1 ] : ',';
			$label = preg_replace( '/(%dec.|%sep.)/', '', $label );
			
			$tokens = array(
				'%total'    => self::webcomic_print_amount( $type, $dec, $sep, $the_post ),
				'%price'    => self::webcomic_print_amount( "{$type}-price", $dec, $sep, $the_post ),
				'%shipping' => self::webcomic_print_amount( "{$type}-shipping", $dec, $sep, $the_post )
			);
			
			$label = str_replace( array_keys( $tokens ), $tokens, $label );
		}
		
		$output = sprintf( '
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post" class="webcomic-print-form webcomic-%s-print-form %s-print-form %s-%s-print-form">
				<button type="submit">%s</button>
				%s
			</form>',
			$type,
			$the_post->post_type,
			$the_post->post_type,
			$type,
			$label,
			$fields
		);
		
		return apply_filters( 'webcomic_print_form', $output, $type, $label, $the_post );
	}
	
	///
	// Transcript Tags
	///
	
	/** Load the transcripts template file.
	 * 
	 * @param string $template The template file to load.
	 * @uses Webcomic::$config
	 * @template transcripts-{$collection}.php, transcripts.php
	 */
	public static function webcomic_transcripts_template( $template = '' ) {
		global $post;
		
		if ( $post and isset( self::$config[ 'collections' ][ $post->post_type ] ) ) {
			locate_template( array( $template, "webcomic/transcripts-{$post->post_type}.php", 'webcomic/transcripts.php' ), true, false );
		}
	}
	
	/** Return a webcomic transcripts url.
	 * 
	 * @param mixed $language The language object or ID that transcripts should be limited to.
	 * @param mixed $the_post The post object or ID to get a transcript link to.
	 * @return string
	 * @uses WebcomicTag::webcomic_transcripts_open()
	 * @filter string get_webcomic_transcripts_link Filters the URL returned by `get_webcomic_transcripts_link` and used by `webcomic_transcripts_link`.
	 */
	public static function get_webcomic_transcripts_link( $language = false, $the_post = false ) {
		global $wp_rewrite;
		
		$link = apply_filters( 'the_permalink', get_permalink( $the_post ) );
		
		if ( $term = get_term( $language, 'webcomic_language' ) ) {
			$link = $wp_rewrite->using_permalinks() ? user_trailingslashit( trailingslashit( $link ) . "transcripts/{$term->slug}" ) : add_query_arg(  array( 'transcripts' => $term->slug ), $link );
		}
		
		return apply_filters( 'get_webcomic_transcripts_link', $link . ( self::have_webcomic_transcripts( false, $term ? $term->slug : '' ) ? '#webcomic-transcripts' : '#webcomic-transcribe0' ), $language, $the_post );
	}
	
	/** Return a webcomic transcripts link.
	 * 
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $none Format string for the link text when no transcripts have been published. Accepts %title and image size tokens.
	 * @param string $some Format string for the link text when one or more transcripts have been published. Accepts %title and image size tokens.
	 * @param string $off Format string for the link text when transcription has been disabled. Accepts %title and image size tokens.
	 * @param mixed $language The language object or ID that transcripts should be limited to.
	 * @param mixed $the_post The post object or ID to get a transcript link to.
	 * @return string
	 * @filter string webcomic_transcripts_link Filters the output of `webcomic_transcripts_link`.
	 */
	public static function webcomic_transcripts_link( $format, $none, $some, $off, $language = false, $the_post = false ) {
		if ( $the_post = get_post( $the_post ) and $href = self::get_webcomic_transcripts_link( $language, $the_post ) ) {
			$class = array( 'webcomic-transcript-link', "{$the_post->post_type}-transcript-link" );
			
			if ( !webcomic_transcripts_open() ) {
				$link = $off ? $off : __( 'Transcription Off', 'webcomic' );
			} else if ( have_webcomic_transcripts() ) {
				$link = $some ? $some : __( 'View %title Transcripts', 'webcomic' );
			} else {
				$link = $none ? $none : __( 'Transcribe %title', 'webcomic' );
			}
			
			if ( false !== strpos( $link, '%' ) ) {
				$tokens = array(
					'%title' => apply_filters( 'the_title', $the_post->post_title, $the_post->ID )
				);
				
				foreach ( array_merge( get_intermediate_image_sizes(), array( 'full' ) ) as $size ) {
					if ( false !== strpos( $link, "%{$size}" ) ) {
						$attachments = empty( $attachments ) ? self::get_attachments( $the_post->ID ) : $attachments;
						
						if ( !$attachments ) {
							break;
						} else {
							$image = '';
							
							foreach ( $attachments as $attachment ) {
								$image .= wp_get_attachment_image( $attachment->ID, $size );
							}
							
							$tokens[ "%{$size}" ] = $image;
						}
					}
				}
				
				$link = str_replace( array_keys( $tokens ), $tokens, $link );
			}
			
			$link   = sprintf( '<a href="%s" class="%s">%s</a>', $href, join( ' ', $class ), $link );
			$format = str_replace( '%link', $link, $format );
			
			return apply_filters( 'webcomic_transcripts_link', $format, $link, $the_post );
		}
	}
	
	/** Return an array of post objects.
	 * 
	 * @param boolean $pending Whether to retrieve transcripts pending review.
	 * @param array $args An array of arguments that will be passed to get_children().
	 * @param mixed $the_post The post object or ID to retrieve transcripts for.
	 * @return array
	 * @filter array get_webcomic_transcripts Filters the array of transcripts returned by `get_webcomic_transcripts`.
	 */
	public static function get_webcomic_transcripts( $pending = false, $args = array(), $the_post = false ) {
		if ( $the_post = get_post( $the_post ) ) {
			return apply_filters( 'get_webcomic_transcripts', get_children( array_merge( array(
				'post_type'   => 'webcomic_transcript',
				'post_parent' => $the_post->ID,
				'post_status' => $pending ? 'pending' : get_post_stati( array( 'public' => true ) ),
				'tax_query'   => ( $language = get_query_var( 'transcripts' ) ) ? array( array( 
					'taxonomy' => 'webcomic_language',
					'field'    => 'slug',
					'terms'    => $language
				) ) : array()
			), $args ) ), $pending, $args, $the_post );
		}
	}
	
	/** Return a formatted list of webcomic transcript authors.
	 * 
	 * @param integer $id The transcript ID to retrieve authors for.
	 * @param boolean $post_author Whether to include the WordPress-recognized post author in the list.
	 * @param string $before Before list.
	 * @param string $sep Separate items using this.
	 * @param string $after After list.
	 * @return string
	 * @filter string the_webcomic_transcript_authors Filters the author list returned by `get_webcomic_transcript_authors` and used by `the_webcomic_transcript_authors`.
	 */
	public static function get_webcomic_transcript_authors( $id = 0, $post_author = true, $before = '', $sep = ', ', $after = '' ) {
		global $post;
		
		$id = $id ? $id : $post->ID;
		
		if ( $post_author and $the_post = get_post( $id ) and !is_wp_error( $the_post ) ) {
			$output = array( get_the_author_meta( 'display_name', $the_post->post_author ) );
		} else {
			$output = array();
		}
		
		if ( $authors = get_post_meta( $id, 'webcomic_author' ) ) {
			foreach ( $authos as $author ) {
				$output[] = $author[ 'name' ];
			}
		}
		
		return apply_filters( 'the_webcomic_transcript_authors', $before . join( $sep, $output ) . $after );
	}
	
	/** Return a formatted list of terms related to the current webcomic transcript.
	 * 
	 * @param integer $id The post ID to retrieve terms for.
	 * @param string $taxonomy The taxonomy the terms must belong to.
	 * @param string $before Before list.
	 * @param string $sep Separate items using this.
	 * @param string $after After list.
	 * @return string
	 * @filter string the_webcomic_transcript_term_list Filters the output of `get_the_webcomic_transcript_term_list` used by `the_webcomic_transcript_languages`.
	 */
	public static function get_the_webcomic_transcript_term_list( $id = 0, $taxonomy, $before = '', $sep = ', ', $after = '' ) {
		global $post;
		
		$id = $id ? $id : $post->ID;
		
		if ( $terms = get_the_terms( $id, $taxonomy ) and !is_wp_error( $terms ) ) {
			$term_list = array();
			
			foreach ( $terms as $term ) {
				$term_list[] = sprintf( '<a rel="tag">%s</a>', $term->name );
			}
			
			return apply_filters( 'the_webcomic_transcript_term_list', $before . join( $sep, $term_list ) . $after, $id, $before, $sep, $after, $taxonomy );
		}
	}
	
	/** Return hidden transcript form fields.
	 * 
	 * @param mixed $transcript The post object or ID to update on submission.
	 * @param mixed $the_post The post object or ID to submit a transcript for.
	 * @return string
	 * @uses Webcomic::$config
	 * @filter string webcomic_transcript_fields Filters the output of `webcomic_transcript_fields`.
	 */
	public static function webcomic_transcript_fields( $transcript = false, $the_post = false ) {
		if ( $the_post = get_post( $the_post ) and isset( self::$config[ 'collections' ][ $the_post->post_type ] ) and get_post_meta( $the_post->ID, 'webcomic_transcripts', true ) ) {
			$transcript = ( $transcript and $update_post = get_post( $transcript ) and $the_post->ID !== $update_post->ID ) ? $update_post->ID : 0;
			
			$output = sprintf( '
				<input type="hidden" name="webcomic_transcript_post" value="%s">
				<input type="hidden" name="webcomic_transcript_update" value="%s">
				%s',
				$the_post->ID,
				$transcript,
				wp_nonce_field( 'webcomic_user_transcript', 'webcomic_user_transcript', true, false )
			);
			
			return apply_filters( 'webcomic_transcript_fields', $output, $transcript, $the_post );
		}
	}

	/** Render a complete transcription form for templates.
	 * 
	 * ### Arguments
	 * 
	 * - `array` **$fields** - An array of fields for unregistered users to fill out. Each array element should have a descriptive key and the full HTML output for the value.
	 * - `string` **$language_field** - HTML output for the transcript language field.
	 * - `string` **$transcript_field** - HTML output for the transcript content field. Used when $wysiwyg_editor is `false`.
	 * - `string` **$must_log_in** - Error text to display when users must be logged in to transcribe.
	 * - `string` **$logged_in_as** - Text to display when a user is already logged in.
	 * - `string` **$transcript_notes_before** - Transcription notes displayed to unregistered users before the `$fields` are output.
	 * - `string` **$transcript_notes_after** - Transcription notes displayed at the bottom of the form before the submit button.
	 * - `string` **$transcript_notes_success** - Text displayed after a transcript has been successfully submitted.
	 * - `string` **$transcript_notes_failure** - Text displayed if an error occurs during transcript submission.
	 * - `string` **$id_form** - ID to use for the `<form>` element.
	 * - `string` **$title_submit** - Title text to display for the transcript submission form.
	 * - `string` **$label_submit** - Text to display for the submit button.
	 * - `mixed` **$wysiwyg_editor** - Whether to display a WYSIWYG transcript editor. May pass an array of arguments for `wp_editor()`.
	 * 
	 * @param array $args Options for strings, fields etc in the form.
	 * @param mixed $transcript The transcript to update on submission.
	 * @param mixed $the_post The post object or ID to generate the form for.
	 * @uses Webcomic::$config
	 * @uses WebcomicTag::webcomic_transcripts_open()
	 * @uses WebcomicTag::webcomic_transcript_fields()
	 * @action webcomic_transcript_form_before Triggered before webcomic transcript form output begins.
	 * @action webcomic_transcript_form_must_log_in_after Triggered if users must be registered and logged in to transcribe and the current user is not logged in.
	 * @action webcomic_transcript_form_top Triggered just after the opening `<form>` tag.
	 * @action webcomic_transcript_form_logged_in_after Triggered if the current user is registered and logged in.
	 * @action webcomic_transcript_form_before_fields Triggered just before form field output begins.
	 * @action webcomic_transcript_form_after_fields Triggered just after form field output ends.
	 * @action webcomic_transcript_form Triggered  just before the closing `<form>` tag.
	 * @action webcomic_transcript_form_after Triggered after webcomic transcript form output has ended.
	 * @action webcomic_transcript_form_closed Triggered if transcription is closed.
	 * @filter array webcomic_transcript_form_defaults Filters the array of default arguments used by `webcomic_transcript_form`.
	 * @filter array webcomic_transcript_form_default_fields Filters the array of default fields used by `webcomic_transcript_form`.
	 * @filter string webcomic_transcript_form_logged_in Filters the message displayed by `webcomic_transcript_form` for logged in users.
	 * @filter string webcomic_transcript_form_field_{$field} Filters the individual fields from the `$fields` argument used by `webcomic_transcript_form`.
	 * @filter string webcomic_transcript_form_field_language Filters the webcomic transcript language field used by `webcomic_transcript_form`.
	 * @filter string webcomic_transcript_form_field_transcript Filters the non-wysiwyg `<textarea>` field used by `webcomic_transcript_form`.
	 * @filter string webcomic_transcript_form_field_submit Filters the submit button used by `webcomic_transcript_form`.
	 */
	public static function webcomic_transcript_form( $args = array(), $transcript = false, $the_post = false ) {
		if ( !$the_post = get_post( $the_post ) or empty( self::$config[ 'collections' ][ $the_post->post_type ] ) or ( $transcript and !$update_post = get_post( $transcript ) ) ) {
			return;
		}
		
		static $c = 0;
		
		$languages = array();
		
		if ( ( $terms = in_array( '!', self::$config[ 'collections' ][ $the_post->post_type ][ 'transcripts' ][ 'languages' ] ) ? get_terms( 'webcomic_language', array( 'get' => 'all', 'cache_domain' => 'webcomic_transcript_form' ) ) : get_terms( 'webcomic_language', array( 'get' => 'all', 'cache_domain' => 'webcomic_transcript_form', 'include' => self::$config[ 'collections' ][ $the_post->post_type ][ 'transcripts' ][ 'languages' ] ) ) ) and !is_wp_error( $terms ) ) {
			$update_terms     = empty( $update_post ) ? array() : wp_get_object_terms( $update_post->ID, 'webcomic_language', array( 'fields' => 'ids' ) );
			$queried_language = get_query_var( 'transcripts' );
			
			foreach ( $terms as $term ) {
				$languages[] = sprintf( '<option value="%s"%s>%s</option>',
					$term->slug,
					( $update_terms and in_array( $term->term_id, $update_terms ) or ( $term->slug === $queried_language ) ) ? ' selected' : '',
					$term->name
				);
			}
		}
		
		$required  = ( 'identify' === self::$config[ 'collections' ][ $the_post->post_type ][ 'transcripts' ][ 'permission' ] );
		$commenter = wp_get_current_commenter();
		$user      = wp_get_current_user();
		
		extract( wp_parse_args( $args, apply_filters( 'webcomic_transcript_form_defaults', array(
			'fields' => apply_filters( 'webcomic_transcript_form_default_fields', array(
				'author' => sprintf( '<p class="webcomic-transcript-author"><label for="webcomic-transcript-author' . $c . '">%s%s</label><input type="text" name="webcomic_transcript_author" id="webcomic-transcript-author' . $c . '" value="%s"%s></p>',
					 __( 'Name', 'webcomic' ),
					 $required ? '<span class="required">*</span>' : '',
					 esc_attr( $commenter[ 'comment_author' ] ),
					 $required ? ' required' : ''
				),
				'email' => sprintf( '<p class="webcomic-transcript-email"><label for="webcomic-transcript-email' . $c . '">%s%s</label><input type="email" name="webcomic_transcript_email" id="webcomic-transcript-email' . $c . '" value="%s"%s></p>',
					__( 'Email', 'webcomic' ),
					 $required ? '<span class="required">*</span>' : '',
					 esc_attr(  $commenter[ 'comment_author_email' ] ),
					 $required ? ' required' : ''
				),
				'url' => sprintf( '<p class="webcomic-transcript-url"><label for="webcomic-transcript-url' . $c . '">%s</label><input type="url" name="webcomic_transcript_url" id="webcomic-transcript-url' . $c . '" value="%s"></p>',
					__( 'Website', 'webcomic' ),
					esc_attr( $commenter[ 'comment_author_url' ] )
				)
			), $the_post->post_type ),
			'language_field' => $languages ? sprintf( '<p class="webcomic-transcript-language"><label for="webcomic-transcript-language' . $c . '">%s</label><select name="webcomic_transcript_language" id="webcomic-transcript-language' . $c . '">%s</select></p>',
				__( 'Language', 'webcomic' ),
				join( '', $languages )
			) : '',
			'transcript_field' => sprintf( '<p class="webcomic-transcript-content"><label for="webcomic-transcript-content' . $c . '">%s</label><textarea name="webcomic_transcript_content" id="webcomic-transcript-content' . $c . '" rows="10" cols="40" required>%s</textarea></p>',
				__( 'Transcript', 'webcomic' ),
				empty( $update_post ) ? '' : esc_html( $update_post->post_content )
			),
			'must_log_in'              => sprintf( '<p class="must-log-in">%s</p>', sprintf( __( 'You must be <a href="%s">logged in</a> to transcribe this webcomic.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( $the_post ) ) ) ) ),
			'logged_in_as'             => sprintf( '<p class="logged-in-as">%s</p>', sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s">Log out?</a>' ), admin_url( 'profile.php' ), $user->display_name, wp_logout_url( apply_filters( 'the_permalink', get_permalink( $the_post ) ) ) ) ),
			'transcript_notes_before'  => '<p class="webcomic-transcript-notes">' . __( 'Your email address will not be published.', 'webcomic' ) . ( $required ? sprintf( __( ' Required fields are marked %s', 'webcomic' ), '<span class="required">*</span>' ) : '' ) . '</p>',
			'transcript_notes_after'   => '',
			'transcript_notes_success' => sprintf( '<p class="webcomic-transcript-success">%s</p>', __( 'Thank you! Your transcript has been submitted.', 'webcomic' ) ),
			'transcript_notes_failure' => sprintf( '<p class="webcomic-transcript-failure">%s</p>', __( 'There was a problem submitting your transcript. Please try again.', 'webcomic' ) ),
			'id_form'                  => "webcomic-transcript-form{$c}",
			'title_submit'             => empty( $update_post ) ? __( 'Transcribe %s', 'webcomic' ) : __( 'Improve %s Transcript', 'webcomic' ),
			'label_submit'             => __( 'Submit Transcript', 'webcomic' ),
			'wysiwyg_editor'           => false
		), $the_post->post_type ) ) );
		
		if ( self::webcomic_transcripts_open( $the_post ) ) {
			do_action( 'webcomic_transcript_form_before', $the_post->post_type );
			
			printf( '
				<section id="webcomic-transcribe%s" class="webcomic-transcribe">
					<header class="webcomic-transcribe-header">
						<h3>%s</h3>
					</header>',
				$c,
				sprintf( $title_submit, get_the_title( $the_post->ID ) )
			);
			
			if ( 'register' === self::$config[ 'collections' ][ $the_post->post_type ][ 'transcripts' ][ 'permission' ] and !is_user_logged_in() ) {
				echo $must_log_in;
				
				do_action( 'webcomic_transcript_form_must_log_in_after', $the_post->post_type );
			} else {
				printf( '<form method="post" id="%s">', esc_attr( $id_form ) );
				
				do_action( 'webcomic_transcript_form_top', $the_post->post_type );
				
				if ( isset( $_POST[ 'webcomic_transcript_submission' ] ) ) {
					echo $_POST[ 'webcomic_transcript_submission' ] ? $transcript_notes_success : $transcript_notes_failure;
				}
				
				if ( is_user_logged_in() ) {
					echo apply_filters( 'webcomic_transcript_form_logged_in', $logged_in_as, $the_post->post_type, $commenter, $user->display_name );
					
					do_action( 'webcomic_transcript_form_logged_in_after', $the_post->post_type, $commenter, $user->display_name );
				} else {
					echo $transcript_notes_before;
					
					do_action( 'webcomic_transcript_form_before_fields', $the_post->post_type );
					
					foreach ( ( array ) $fields as $name => $field ) {
						echo apply_filters( "webcomic_transcript_form_field_{$name}", $field, $the_post->post_type );
					}
					
					do_action( 'webcomic_transcript_form_after_fields', $the_post->post_type );
				}
				
				echo apply_filters( 'webcomic_transcript_form_field_language', $language_field, $the_post->post_type, $languages, $terms, empty( $update_terms ) ? array() : $update_terms );
				
				if ( $wysiwyg_editor ) {
					wp_editor( '', "webcomic_transcript_content{$c}", array_merge( array( 'textarea_name' => 'webcomic_transcript_content', 'media_buttons' => false, 'teeny' => true, 'editor_css' => '<style scoped>*{box-sizing:content-box;-moz-box-sizing:content-box;-webkit-box-sizing:content-box}</style>' ), ( array ) $wysiwyg_editor ) );
				} else {
					echo apply_filters( 'webcomic_transcript_form_field_transcript', $transcript_field, $the_post->post_type );
				}
				
				echo $transcript_notes_after,
					 apply_filters( 'webcomic_transcript_form_field_submit', sprintf( '<p class="webcomic-transcript-submit"><button type="submit" name="webcomic_transcript_submit">%s</button></p>', esc_html( $label_submit ) ), $the_post->post_type ),
					 self::webcomic_transcript_fields( empty( $update_post ) ? 0 : $update_post->ID, $the_post );
				
				do_action( 'webcomic_transcript_form', $the_post );
				
				echo "</form><!-- #{$id_form} -->";
			}
			
			echo "</section><!-- #webcomic-transcribe{$c} -->";
			
			do_action( 'webcomic_transcript_form_after', $the_post->post_type );
		} else {
			do_action( 'webcomic_transcript_form_closed', $the_post->post_type );
		}
		
		$c++;
	}
	
	/** Return a `<select>` element of webcomic transcript terms.
	 * 
	 * Because this function relies on get_terms() to retrieve the term
	 * list the $args parameter accepts any arguments that get_terms()
	 * may accept. Only those get_terms() arguments that differ from
	 * their defaults are detailed here.
	 * 
	 * ### Arguments
	 * 
	 * - `string` **$name** - Value for the name attribute of the `<select>` element.
	 * - `string` **$id** - Value of the id attribute of the `<select>` element.
	 * - `mixed` **$class** - String or array of additional classes for the `<select>` element.
	 * - `string` **$before** - Content to display before the output.
	 * - `string` **$after** - Content to display after the output.
	 * - `string` **$show_option_all** - String to display for an "all" `<option>` (value="0").
	 * - `string` **$show_option_none** - String to display for a "none" `<option>` (value="-1").
	 * - `boolean` **$hierarchical** - Whether to indent child terms.
	 * - `boolean` **$hide_empty** - Whether to hide empty terms. Defaults to the opposite of WebcomicTag::webcomic_transcripts_open().
	 * - `boolean` **$hide_if_empty** - Whether to display the `<select>` even if it contains no `<option>'s`.
	 * - `string` **$taxonomy** - The taxonomy terms must belong to. Should be a valid Webcomic taxonomy.
	 * - `string` **$orderby** - What field to sort terms by.
	 * - `object` **$walker** - Custom walker object. Defaults Walker_WebcomicTerm_Dropdown.
	 * - `integer` **$depth** - How deep the walker should run. Defaults to 0 (all levels). A -1 depth will result in flat output.
	 * - `integer` **$selected** - The ID of the selected term.
	 * 
	 * @param array $args Array of arguments. See function description for detailed information.
	 * @param mixed $the_post The post object or ID transcripts should be related to.
	 * @return string
	 * @uses WebcomicTag::webcomic_transcripts_open()
	 * @uses Walker_WebcomicTranscriptTerm_Dropdown
	 * @filter string webcomic_dropdown_transcript_terms Filters the HTML returned by `webcomic_dropdown_transcript_terms` and used by `webcomic_dropdown_transcript_languages`.
	 */
	public static function webcomic_dropdown_transcript_terms( $args = array(), $the_post = false ) {
		if ( $the_post = get_post( $the_post ) and isset( self::$config[ 'collections' ][ $the_post->post_type ] ) ) {
			$r = wp_parse_args( $args, array(
				'name'             => 'webcomic_terms',
				'id'               => '',
				'class'            => '',
				'before'           => '',
				'after'            => '',
				'show_option_all'  => '',
				'show_option_none' => '',
				'hierarchical'     => true,
				'hide_empty'       => !self::webcomic_transcripts_open( $the_post ),
				'hide_if_empty'    => true,
				'taxonomy'         => '',
				'orderby'          => 'name',
				'walker'           => false,
				'depth'            => 0,
				'selected'         => 0
			) );
			
			$r[ 'the_post' ] = $the_post;
			
			if ( empty( $r[ 'selected' ] ) and 'webcomic_language' === $r[ 'taxonomy' ] and $slug = get_query_var( 'transcripts' ) and $term = get_term_by( 'slug', $slug, 'webcomic_language' ) and !is_wp_error( $term ) ) {
				$r[ 'selected' ] = $term->term_id;
			}
			
			if ( 'webcomic_language' === $r[ 'taxonomy' ] and !in_array( '!', self::$config[ 'collections' ][ $the_post->post_type ][ 'transcripts' ][ 'languages' ] ) ) {
				$r[ 'include' ] = self::$config[ 'collections' ][ $the_post->post_type ][ 'transcripts' ][ 'languages' ];
			}
			
			extract( $r );
			
			$output = '';
			
			if ( ( $terms = get_terms( $r[ 'taxonomy' ], $r ) and !is_wp_error( $terms ) ) and count( $terms ) > 1 or !$hide_if_empty ) {
				$output = sprintf( '%s<select%s%s class="%s">%s%s%s</select>%s',
					$before,
					$name ? sprintf( ' name="%s"', esc_attr( $name ) ) : '',
					$id ? sprintf( ' id="%s"', esc_attr( $id ) ) : '',
					join( ' ', array_merge( array( 'webcomic-transcript-terms', $taxonomy ), ( array ) $class ) ),
					$show_option_all ? sprintf( '<option value="0"%s>%s</option>', 0 === $selected ? ' selected' : '', $show_option_all ) : '',
					$show_option_none ? sprintf( '<option value="-1"%s>%s</option>', -1 === $selected ? ' selected' : '', $show_option_none ) : '',
					( $terms and !is_wp_error( $terms ) ) ? call_user_func( array( $walker ? $walker : new Walker_WebcomicTranscriptTerm_Dropdown, 'walk' ), $terms, 0, $r ) : '',
					$after
				);
			}
			
			return apply_filters( 'webcomic_dropdown_transcript_terms', $output, $r );
		}
	}
	
	/** Return a list of webcomic transcript terms.
	 * 
	 * Because this function relies on get_terms() to retrieve the term
	 * list the $args parameter accepts any arguments that get_terms()
	 * may accept. Only those get_terms() arguments that differ from
	 * their defaults are detailed here.
	 * 
	 * ### Arguments
	 * 
	 * - `string` **$id** - Value of the id attribute of the list element.
	 * - `mixed` **$class** - String or array of additional classes for the list element.
	 * - `string` **$before** - Content to display before the output.
	 * - `string` **$after** - Content to display after the output.
	 * - `boolean` **$ordered** - Use `<ol>` instead of `<ul>`.
	 * - `boolean` **$hierarchical** - Whether to indent child terms.
	 * - `boolean` **$hide_empty** - Whether to hide empty terms. Defaults to the opposite of WebcomicTag::webcomic_transcripts_open().
	 * - `string` **$taxonomy** - The taxonomy terms must belong to. Should be a valid Webcomic taxonomy.
	 * - `string` **$orderby** - What field to sort terms by.
	 * - `object` **$walker** - Custom walker object. Defaults Walker_WebcomicTranscriptTerm_List.
	 * - `integer` **$depth** - How deep the walker should run. Defaults to 0 (all levels). A -1 depth will result in flat output.
	 * - `integer` **$selected** - The ID of the selected term.
	 * 
	 * @param array $args Array of arguments. See function description for detailed information.
	 * @param mixed $the_post The post object or ID transcripts should be related to.
	 * @return string
	 * @uses WebcomicTag::webcomic_transcripts_open()
	 * @uses Walker_WebcomicTranscriptTerm_List
	 * @filter string webcomic_list_transcript_terms Filters the HTML returned by `webcomic_list_transcript_terms` and used by `webcomic_list_transcript_languages`.
	 */
	public static function webcomic_list_transcript_terms( $args = array(), $the_post = false ) {
		if ( $the_post = get_post( $the_post ) and isset( self::$config[ 'collections' ][ $the_post->post_type ] ) ) {
			$r = wp_parse_args( $args, array(
				'id'               => '',
				'class'            => '',
				'before'           => '',
				'after'            => '',
				'ordered'          => '',
				'hierarchical'     => true,
				'hide_empty'       => !self::webcomic_transcripts_open(),
				'taxonomy'         => '',
				'orderby'          => 'name',
				'walker'           => false,
				'depth'            => 0,
				'selected'         => 0
			) );
			
			$r[ 'the_post' ] = $the_post;
			
			if ( empty( $r[ 'selected' ] ) and 'webcomic_language' === $r[ 'taxonomy' ] and $slug = get_query_var( 'transcripts' ) and $term = get_term_by( 'slug', $slug, 'webcomic_language' ) and !is_wp_error( $term ) ) {
				$r[ 'selected' ] = $term->term_id;
			}
			
			if ( 'webcomic_language' === $r[ 'taxonomy' ] and !in_array( '!', self::$config[ 'collections' ][ $the_post->post_type ][ 'transcripts' ][ 'languages' ] ) ) {
				$r[ 'include' ] = self::$config[ 'collections' ][ $the_post->post_type ][ 'transcripts' ][ 'languages' ];
			}
			
			extract( $r );
			
			$output = '';
			
			if ( $terms = get_terms( $r[ 'taxonomy' ], $r ) and !is_wp_error( $terms ) and count( $terms ) > 1 ) {
				$output = sprintf( '%s<%s%s class="%s">%s</%s>%s',
					$before,
					$ordered ? 'ol' : 'ul',
					$id ? sprintf( ' id="%s"', esc_attr( $id ) ) : '',
					join( ' ', array_merge( array( 'webcomic-transcript-terms', $taxonomy ), ( array ) $class ) ),
					call_user_func( array( $walker ? $walker : new Walker_WebcomicTranscriptTerm_List, 'walk' ), $terms, $depth, $r ),
					$ordered ? 'ol' : 'ul',
					$after
				);
			}
			
			return apply_filters( 'webcomic_list_transcript_terms', $output, $r );
		}
	}
	
	///
	// Archive Tags
	///
	
	/** Return a `<select>` element of webcomic terms.
	 * 
	 * Because this function relies on get_terms() to retrieve the term
	 * list the $args parameter accepts any arguments that get_terms()
	 * may accept. Only those get_terms() arguments that differ from
	 * their defaults are detailed here.
	 * 
	 * ### Arguments
	 * 
	 * - `string` **$name** - Value for the name attribute of the `<select>` element.
	 * - `string` **$id** - Value of the id attribute of the `<select>` element.
	 * - `mixed` **$class** - String or array of additional classes for the `<select>` element.
	 * - `string` **$before** - Content to display before the output.
	 * - `string` **$after** - Content to display after the output.
	 * - `string` **$show_option_all** - String to display for an "all" `<option>` (value="0").
	 * - `string` **$show_option_none** - String to display for a "none" `<option>` (value="-1").
	 * - `boolean` **$hierarchical** - Whether to indent child terms.
	 * - `boolean` **$hide_if_empty** - Whether to display the `<select>` even if it contains no `<option>'s`.
	 * - `string` **$taxonomy** - The taxonomy terms must belong to. Should be a valid Webcomic taxonomy.
	 * - `string` **$orderby** - What field to sort terms by. Defaults to 'name' for characters and 'term_group' for storylines.
	 * - `object` **$walker** - Custom walker object. Defaults Walker_WebcomicTerm_Dropdown.
	 * - `integer` **$depth** - How deep the walker should run. Defaults to 0 (all levels). A -1 depth will result in flat output.
	 * - `boolean` **$webcomics** - Whether to display a dropdown of webcomic posts grouped by term. The 'hide_empty' argument is ignored when $webcomics is true.
	 * - `string` **$webcomic_order** - How to order webcomics, one of 'ASC' or 'DESC'. Defaults to 'ASC'.
	 * - `string` **$webcomic_orderby** - What field to order webcomics by. Defaults to 'date'. See WP_Query for details.
	 * - `boolean` **$show_count** - Whether to display the total number of webcomics in a term.
	 * - `string` **$target** - The target url for terms, one of 'archive', 'first', 'last', or 'random'. Defaults to 'archive'.
	 * - `integer` **$selected** - The ID of the selected term or webcomic.
	 * 
	 * @param array $args Array of arguments. See function description for detailed information.
	 * @return string
	 * @uses Walker_WebcomicTerm_Dropdown
	 * @filter string webcomic_dropdown_terms Filters the HTML returned by `webcomic_list_transcript_terms` and used by `webcomic_dropdown_storylines` and `webcomic_dropdown_characters`.
	 */
	public static function webcomic_dropdown_terms( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'name'             => 'webcomic_terms',
			'id'               => '',
			'class'            => '',
			'before'           => '',
			'after'            => '',
			'show_option_all'  => '',
			'show_option_none' => '',
			'hierarchical'     => true,
			'hide_if_empty'    => true,
			'taxonomy'         => '',
			'orderby'          => '',
			'walker'           => false,
			'depth'            => 0,
			'webcomics'        => false,
			'webcomic_order'   => 'ASC',
			'webcomic_orderby' => 'date',
			'show_count'       => false,
			'target'           => 'archive',
			'selected'         => 0
		) );
		
		if ( !$r[ 'orderby' ] ) {
			$r[ 'orderby' ] = false !== strpos( $r[ 'taxonomy' ], '_storyline' ) ? 'term_group' : 'name';
		}
		
		extract( $r );
		
		$output = '';
		
		if ( ( $terms = get_terms( $r[ 'taxonomy' ], $r ) and !is_wp_error( $terms ) ) or !$hide_if_empty ) {
			$output = sprintf( '%s<select%s%s class="%s">%s%s%s</select>%s',
				$before,
				$name ? sprintf( ' name="%s"', esc_attr( $name ) ) : '',
				$id ? sprintf( ' id="%s"', esc_attr( $id ) ) : '',
				join( ' ', array_merge( array( 'webcomic-terms', $taxonomy ), ( array ) $class ) ),
				$show_option_all ? sprintf( '<option value="0"%s>%s</option>', 0 === $selected ? ' selected' : '', $show_option_all ) : '',
				$show_option_none ? sprintf( '<option value="-1"%s>%s</option>', -1 === $selected ? ' selected' : '', $show_option_none ) : '',
				( $terms and !is_wp_error( $terms ) ) ? call_user_func( array( $walker ? $walker : new Walker_WebcomicTerm_Dropdown, 'walk' ), $terms, $depth, $r ) : '',
				$after
			);
		}
		
		return apply_filters( 'webcomic_dropdown_terms', $output, $r );
	}
	
	/** Return a `<select>` element of webcomic collections.
	 * 
	 * ### Arguments
	 * 
	 * - `string` **$name** - Value for the name attribute of the `<select>` element.
	 * - `string` **$id** - Value of the id attribute of the `<select>` element.
	 * - `mixed` **$class** - String or array of additional classes for the `<select>` element.
	 * - `string` **$before** - Content to display before the output.
	 * - `string` **$after** - Content to display after the output.
	 * - `string` **$show_option_all** - String to display for an "all" `<option>` (value="0").
	 * - `string` **$show_option_none** - String to display for a "none" `<option>` (value="-1").
	 * - `boolean` **$hide_empty** - Whether to hide collections with no readable posts. Defaults to true.
	 * - `boolean` **$hide_if_empty** - Whether to display the `<select>` even if it contains no `<option>'s`.
	 * - `string` **$collection** - Limits output to a single collection. Useful in combination with $webcomics.
	 * - `string` **$order** - How to order collections, one of 'ASC' (default) or 'DESC'.
	 * - `string` **$orderby** - What to sort the collections by. May be one of 'name', 'slug', 'count', or 'updated'. Defaults to collection ID.
	 * - `string` **$callback** - Custom callback function for generating `<option>'s`. Callback functions should accept three arguments: the collection configuration array, the function arguments array, and the posts array (if any).
	 * - `boolean` **$webcomics** - Whether to display a dropdown of webcomic posts grouped by collection. The 'hide_empty' argument is ignored when $webcomics is true.
	 * - `string` **$webcomic_order** - How to order webcomics, one of 'ASC' or 'DESC'. Defaults to 'ASC'.
	 * - `string` **$webcomic_orderby** - What field to order webcomics by. Defaults to 'date'. See WP_Query for details.
	 * - `boolean` **$show_count** - Whether to display the total number of published webcomics in a collection.
	 * - `string` **$target** - The target url for collections, one of 'archive', 'first', 'last', or 'random'. Defaults to 'archive'.
	 * - `string` **$selected** - The ID of the selected collection or webcomic.
	 * 
	 * @param array $args Array of arguments. See function description for detailed information.
	 * @return string
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::get_webcomic_collections()
	 * @uses WebcomicTag::sort_webcomic_collections_name()
	 * @uses WebcomicTag::sort_webcomic_collections_slug()
	 * @uses WebcomicTag::sort_webcomic_collections_count()
	 * @uses WebcomicTag::sort_webcomic_collections_updated()
	 * @uses WebcomicTag::get_relative_webcomic_link()
	 * @filter string webcomic_collection_dropdown_title Filters the collection titles used by `webcomic_dropdown_collections`.
	 * @filter string webcomic_dropdown_collections Filters the output of `webcomic_dropdown_collections`.
	 */
	public static function webcomic_dropdown_collections( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'name'             => 'webcomic_collections',
			'id'               => '',
			'class'            => '',
			'before'           => '',
			'after'            => '',
			'show_option_all'  => '',
			'show_option_none' => '',
			'hide_empty'       => true,
			'hide_if_empty'    => true,
			'collection'       => '',
			'order'            => 'ASC',
			'orderby'          => '',
			'callback'         => '',
			'webcomics'        => false,
			'webcomic_order'   => 'ASC',
			'webcomic_orderby' => 'date',
			'show_count'       => false,
			'target'           => 'archive',
			'selected'         => ''
		) );
		
		extract( $r );
		
		$selected    = $selected ? $selected : self::get_webcomic_collection();
		$collections = self::get_webcomic_collections( true );
		
		if ( 'name' === $orderby ) {
			usort( $collections, array( 'WebcomicTag', 'sort_webcomic_collections_name' ) );
		} else if ( 'slug' === $orderby ) {
			usort( $collections, array( 'WebcomicTag', 'sort_webcomic_collections_slug' ) );
		} else if ( 'count' === $orderby ) {
			usort( $collections, array( 'WebcomicTag', 'sort_webcomic_collections_count' ) );
		} else if ( 'updated' === $orderby ) {
			usort( $collections, array( 'WebcomicTag', 'sort_webcomic_collections_updated' ) );
		}
		
		if ( 'DESC' === $order ) {
			$collections = array_reverse( $collections );
		}
		
		$output = $options = '';
		
		foreach ( $collections as $v ) {
			if ( ( $collection and $v[ 'id' ] === $collection ) or ( $readable_count = wp_count_posts( $v[ 'id' ], 'readable' ) and 0 < ( $readable_count->publish + $readable_count->private ) ) or !$hide_empty ) {
				$readable_count   = $readable_count->publish + $readable_count->private;
				$collection_title = apply_filters( 'webcomic_collection_dropdown_title', $v[ 'name' ], $v );
				
				if ( $webcomics ) {
					$the_posts = new WP_Query( array( 'post_type' => $v[ 'id' ], 'order' => $webcomic_order, 'orderby' => $webcomic_orderby ) );
					
					if ( $the_posts->have_posts() ) {
						if ( $callback ) {
							$options .= call_user_func( $callback, $v, $r, $the_posts );
						} else {
							$options .= sprintf( '<optgroup label="%s%s">',
								$collection_title,
								$show_count ? " ({$readable_count})" : ''
							);
							
							while ( $the_posts->have_posts() ) { $the_posts->the_post();
								$options .= sprintf( '<option value="%s" data-webcomic-url="%s"%s>%s</option>',
									get_the_ID(),
									apply_filters( 'the_permalink', get_permalink() ),
									$selected === get_the_ID() ? ' selected' : '',
									the_title( '', '', false )
								);
							}
							
							$options .= '</optgroup>';
						}
					}
					
					wp_reset_postdata();
				} else {
					$options .= $callback ? call_user_func( $callback, $v, $r ) : sprintf( '<option value="%s" data-webcomic-url="%s" %s>%s%s</option>',
						$v[ 'id' ],
						'archive' === $target ? get_post_type_archive_link( $v[ 'id' ] ) : self::get_relative_webcomic_link( $target, false, false, '', $v[ 'id' ] ),
						$selected === $v[ 'id' ] ? ' selected' : '',
						$collection_title,
						$show_count ? " ({$readable_count})" : ''
					);
				}
			}
		}
		
		if ( $options or !$hide_if_empty ) {
			$output = sprintf( '%s<select%s%s class="%s">%s%s%s</select>%s',
				$before,
				$name ? sprintf( ' name="%s"', esc_attr( $name ) ) : '',
				$id ? sprintf( ' id="%s"', esc_attr( $id ) ) : '',
				join( ' ', array_merge( array( 'webcomic-collections', $k ), ( array ) $class ) ),
				$show_option_all ? sprintf( '<option value="0"%s>%s</option>', 0 === $selected ? ' selected' : '', $show_option_all ) : '',
				$show_option_none ? sprintf( '<option value="-1"%s>%s</option>', -1 === $selected ? ' selected' : '', $show_option_none ) : '',
				$options,
				$after
			);
		}
		
		return apply_filters( 'webcomic_dropdown_collections', $output, $r );
	}
	
	/** Return a list of webcomic terms.
	 * 
	 * Because this function relies on get_terms() to retrieve the term
	 * list the $args parameter accepts any arguments that get_terms()
	 * may accept. Only those get_terms() arguments that differ from
	 * their defaults are detailed here.
	 * 
	 * ### Arguments
	 * 
	 * - `string` **$id** - Value of the id attribute of the list element.
	 * - `mixed` **$class** - String or array of additional classes for the list element.
	 * - `string` **$before** - Content to display before the output.
	 * - `string` **$after** - Content to display after the output.
	 * - `boolean` **$ordered** - Use `<ol>` instead of `<ul>`.
	 * - `boolean` **$hierarchical** - Whether to indent child terms.
	 * - `string` **$taxonomy** - The taxonomy terms must belong to. Should be a valid Webcomic taxonomy.
	 * - `string` **$orderby** - What field to sort terms by. Defaults to 'name' for characters and 'term_group' for storylines.
	 * - `object` **$walker** - Custom walker object. Defaults Walker_WebcomicTerm_List.
	 * - `string` **$feed** - Text or image URL to use for a term feed link.
	 * - `string` **$feed_type** - The type of feed to link to.
	 * - `integer` **$depth** - How deep the walker should run. Defaults to 0 (all levels). A -1 depth will result in flat output.
	 * - `boolean` **$webcomics** - Whether to display a list of webcomic posts grouped by term. The 'hide_empty' argument is ignored when $webcomics is true.
	 * - `string` **$webcomic_order** - How to order webcomics, one of 'ASC' or 'DESC'. Defaults to 'ASC'.
	 * - `string` **$webcomic_orderby** - What field to order webcomics by. Defaults to 'date'. See WP_Query for details.
	 * - `string` **$webcomic_image** - Size of the webcomic image to use for webcomic links.
	 * - `boolean` **$show_count** - Whether to display the total number of webcomics in a term.
	 * - `boolean` **$show_description** - Whether to display term descriptions.
	 * - `boolean` **$show_image** - Size of the term image to use for term links.
	 * - `string` **$target** - The target url for terms, one of 'archive', 'first', 'last', or 'random'. Defaults to 'archive'.
	 * - `integer` **$selected** - The ID of the selected term or webcomic.
	 * 
	 * @param array $args Array of arguments. See function description for detailed information.
	 * @return string
	 * @uses Walker_WebcomicTerm_List
	 * @filter string webcomic_list_terms Filters the HTML returned by `webcomic_list_terms` and used by `webcomic_list_storylines` and `webcomic_list_characters`.
	 */
	public static function webcomic_list_terms( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'id'               => '',
			'class'            => '',
			'before'           => '',
			'after'            => '',
			'ordered'          => '',
			'hierarchical'     => true,
			'taxonomy'         => '',
			'orderby'          => '',
			'walker'           => false,
			'feed'             => '',
			'feed_type'        => 'rss2',
			'depth'            => 0,
			'webcomics'        => false,
			'webcomic_order'   => 'ASC',
			'webcomic_orderby' => 'date',
			'webcomic_image'   => '',
			'show_count'       => false,
			'show_description' => false,
			'show_image'       => '',
			'target'           => 'archive',
			'selected'         => 0
		) );
		
		if ( !$r[ 'orderby' ] ) {
			$r[ 'orderby' ] = false !== strpos( $r[ 'taxonomy' ], '_storyline' ) ? 'term_group' : 'name';
		}
		
		extract( $r );
		
		$output = '';
		
		if ( $terms = get_terms( $r[ 'taxonomy' ], $r ) and !is_wp_error( $terms ) ) {
			$output = sprintf( '%s<%s%s class="%s">%s</%s>%s',
				$before,
				$ordered ? 'ol' : 'ul',
				$id ? sprintf( ' id="%s"', esc_attr( $id ) ) : '',
				join( ' ', array_merge( array( 'webcomic-terms', $taxonomy ), ( array ) $class ) ),
				call_user_func( array( $walker ? $walker : new Walker_WebcomicTerm_List, 'walk' ), $terms, $depth, $r ),
				$ordered ? 'ol' : 'ul',
				$after
			);
		}
		
		return apply_filters( 'webcomic_list_terms', $output, $r );
	}
	
	/** Return a list of webcomic collections.
	 * 
	 * ### Arguments
	 * 
	 * - `string` **$id** - Value of the id attribute of the list element.
	 * - `mixed` **$class** - String or array of additional classes for the list element.
	 * - `string` **$before** - Content to display before the output.
	 * - `string` **$after** - Content to display after the output.
	 * - `boolean` **$hide_empty** - Whether to hide collections with no readable posts. Defaults to true.
	 * - `boolean` **$ordered** - Use `<ol>` instead of `<ul>`.
	 * - `string` **$collection** - Limits output to a single collection. Useful in combination with $webcomics.
	 * - `string` **$order** - How to order collections, one of 'ASC' (default) or 'DESC'.
	 * - `string` **$orderby** - What to sort the collections by. May be one of 'name', 'slug', 'count', or 'updated'. Defaults to collection ID.
	 * - `string` **$callback** - Custom callback function for generating list items. Callback functions should accept three arguments: the collection configuration array, the function arguments array, and the posts array (if any).
	 * - `string` **$feed** - Text or image URL to use for a collection feed link.
	 * - `string` **$feed_type** - The type of feed to link to.
	 * - `boolean` **$webcomics** - Whether to display a list of webcomic posts grouped by collection. The 'hide_empty' argument is ignored when $webcomics is true.
	 * - `string` **$webcomic_order** - How to order webcomics, one of 'ASC' or 'DESC'. Defaults to 'ASC'.
	 * - `string` **$webcomic_orderby** - What field to order webcomics by. Defaults to 'date'. See WP_Query for details.
	 * - `string` **$webcomic_image** - Size of the webcomic image to use for webcomic links.
	 * - `boolean` **$show_count** - Whether to display the total number of webcomics in a collection.
	 * - `boolean` **$show_description** - Whether to display collection descriptions.
	 * - `boolean` **$show_image** - Size of the collection image to use for collection links.
	 * - `string` **$target** - The target url for collections, one of 'archive', 'first', 'last', or 'random'. Defaults to 'archive'.
	 * - `integer` **$selected** - The ID of the selected collection or webcomic.
	 * 
	 * @param array $args Array of arguments. See function description for detailed information.
	 * @return string
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::get_webcomic_collections()
	 * @uses WebcomicTag::sort_webcomic_collections_name()
	 * @uses WebcomicTag::sort_webcomic_collections_slug()
	 * @uses WebcomicTag::sort_webcomic_collections_count()
	 * @uses WebcomicTag::sort_webcomic_collections_updated()
	 * @uses WebcomicTag::get_relative_webcomic_link()
	 * @filter string webcomic_collection_list_title Filters the collection titles used by `webcomic_list_collections`.
	 * @filter string webcomic_list_collections Filters the output of `webcomic_list_collections`.
	 */
	public static function webcomic_list_collections( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'id'               => '',
			'class'            => '',
			'before'           => '',
			'after'            => '',
			'hide_empty'       => true,
			'ordered'          => '',
			'collection'       => '',
			'order'            => 'ASC',
			'orderby'          => '',
			'callback'         => false,
			'feed'             => '',
			'feed_type'        => 'rss2',
			'webcomics'        => false,
			'webcomic_order'   => 'ASC',
			'webcomic_orderby' => 'date',
			'webcomic_image'   => '',
			'show_count'       => false,
			'show_description' => false,
			'show_image'       => '',
			'target'           => 'archive',
			'selected'         => 0
		) );
		
		extract( $r );
		
		$selected    = $selected ? $selected : self::get_webcomic_collection();
		$collections = self::get_webcomic_collections( true );
		
		if ( 'name' === $orderby ) {
			usort( $collections, array( 'WebcomicTag', 'sort_webcomic_collections_name' ) );
		} else if ( 'slug' === $orderby ) {
			usort( $collections, array( 'WebcomicTag', 'sort_webcomic_collections_slug' ) );
		} else if ( 'count' === $orderby ) {
			usort( $collections, array( 'WebcomicTag', 'sort_webcomic_collections_count' ) );
		} else if ( 'updated' === $orderby ) {
			usort( $collections, array( 'WebcomicTag', 'sort_webcomic_collections_updated' ) );
		}
		
		if ( 'DESC' === $order ) {
			$collections = array_reverse( $collections );
		}
		
		$output = $items = '';
		
		foreach ( $collections as $v ) {
			if ( ( $collection and $v[ 'id' ] === $collection ) or ( $readable_count = wp_count_posts( $v[ 'id' ], 'readable' ) and 0 < ( $readable_count->publish + $readable_count->private ) ) or !$hide_empty ) {
				$readable_count   = $readable_count->publish + $readable_count->private;
				$collection_title = apply_filters( 'webcomic_collection_list_title', $v[ 'name' ], $v );
				$feed_image       = filter_var( $feed, FILTER_VALIDATE_URL );
				$feed_link        = $feed ? sprintf( '<a href="%s" class="webcomic-collection-feed">%s</a>',
					get_post_type_archive_feed_link( $v[ 'id' ], $feed_type ),
					$feed_image ? sprintf( '<img src="%s" alt="%s">', $feed, sprintf( __( 'Feed for %s', 'webcomic' ), get_post_type_object( $v[ 'id' ] )->labels->name ) ) : $feed
				) : '';
				
				if ( $webcomics ) {
					$the_posts = new WP_Query( array( 'post_type' => $v[ 'id' ], 'order' => $webcomic_order, 'orderby' => $webcomic_orderby ) );
					
					if ( $the_posts->have_posts() ) {
						if ( $callback ) {
							$items .= call_user_func( $callback, $v, $r, $the_posts );
						} else {
							$items .= sprintf( '<li class=""webcomic-collection %s%s"><a href="%s" class="webcomic-collection-link">%s%s</a>%s%s<%s class="webcomics">',
								$v[ 'id' ],
								$selected === $v[ 'id' ] ? ' current' : '',
								'archive' === $target ? get_post_type_archive_link( $v[ 'id' ] ) : self::get_relative_webcomic_link( $target, false, false, '', $v[ 'id' ] ),
								sprintf( '<div class="webcomic-collection-name">%s%s</div>', $collection_title, $show_count ? " ({$readable_count})" : '' ),
								( $show_image and $v[ 'image' ] ) ? sprintf( '<div class="webcomic-collection-image">%s</div>', apply_filters( 'webcomic_collection_image', wp_get_attachment_image( $v[ 'image' ], $show_image ), $show_image, $v[ 'id' ] ) ) : '',
								( $show_description and $v[ 'description' ] ) ? sprintf( '<div class="webcomic-collection-description">%s</div>', apply_filters( 'webcomic_collection_description', wpautop( $v[ 'description' ] ), $v[ 'id' ] ) ) : '',
								$feed_link,
								$ordered ? 'ol' : 'ul'
							);
							
							while ( $the_posts->have_posts() ) { $the_posts->the_post();
								$items .= sprintf( '<li%s><a href="%s">%s</a></li>',
									$selected === get_the_ID() ? ' class="current"' : '',
									apply_filters( 'the_permalink', get_permalink() ),
									$webcomic_image ? WebcomicTag::the_webcomic( $webcomic_image, 'self' ) : the_title( '', '', false )
								);
							}
							
							$items .= $ordered ? '</ol></li>' : '</ul></li>';
						}
					}
					
					wp_reset_postdata();
				} else {
					$items .= $callback ? call_user_func( $callback, $v, $r ) : sprintf( '<li class="webcomic-collection %s%s"><a href="%s" class="webcomic-collection-link">%s%s</a>%s%s</li>',
						$v[ 'id' ],
						$selected === $v[ 'id' ] ? ' current' : '',
						'archive' === $target ? get_post_type_archive_link( $v[ 'id' ] ) : self::get_relative_webcomic_link( $target, false, false, '', $v[ 'id' ] ),
						sprintf( '<div class="webcomic-collection-name">%s%s</div>', $collection_title, $show_count ? " ({$readable_count})" : '' ),
						( $show_image and $v[ 'image' ] ) ? sprintf( '<div class="webcomic-collection-image">%s</div>', apply_filters( 'webcomic_collection_image', wp_get_attachment_image( $v[ 'image' ], $show_image ), $show_image, $v[ 'id' ] ) ) : '',
						( $show_description and $v[ 'description' ] ) ? sprintf( '<div class="webcomic-collection-description">%s</div>', apply_filters( 'webcomic_collection_description', wpautop( $v[ 'description' ] ), $v[ 'id' ] ) ) : '',
						$feed_link
					);
				}
			}
		}
		
		if ( $items ) {
			$output = sprintf( '%s<%s%s class="%s">%s</%s>%s',
				$before,
				$ordered ? 'ol' : 'ul',
				$id ? sprintf( ' id="%s"', esc_attr( $id ) ) : '',
				join( ' ', array_merge( array( 'webcomic-collections', $k ), ( array ) $class ) ),
				$items,
				$ordered ? 'ol' : 'ul',
				$after
			);
		}
		
		return apply_filters( 'webcomic_list_collections', $output, $r );
	}
	
	/** Return a "cloud" of webcomic terms.
	 * 
	 * Because this function relies on get_terms() to retrieve the term
	 * list the $args parameter accepts any arguments that get_terms()
	 * may accept. Only those get_terms() arguments that differ from
	 * their defaults are detailed here.
	 * 
	 * ### Arguments
	 * 
	 * - `string` **$id** - Value of the id attribute of the wrapping element.
	 * - `mixed` **$class** - String or array of additional classes for the wrapping element.
	 * - `integer` **$smallest** - The smallest font size to display links in.
	 * - `integer` **$largest** - The largest font size to display links in.
	 * - `string` **$unit** - The CSS unit to use for $smallest and $largest.
	 * - `string` **$image** - Size of the term image to use for term links. Modified by the number of posts in a given term and the $smallest and $largest values.
	 * - `string` **$before** - Content to display before the output.
	 * - `string` **$after** - Content to display after the output.
	 * - `string` **$sep** - Separator to use between links. An empty value generates an unordered list. Defaults to "\n".
	 * - `string` **$taxonomy** - The taxonomy terms must belong to. Should be a valid Webcomic taxonomy.
	 * - `string` **$order** - How to order terms. Defaults to 'RAND'.
	 * - `mixed` **$callback** - Callback function to use when building links.
	 * - `boolean` **$show_count** - Whether to display the total number of webcomics in a term.
	 * - `string` **$target** - The target url for terms, one of 'archive', 'first', 'last', or 'random'. Defaults to 'archive'.
	 * - `integer` **$selected** - The ID of the current term.
	 * 
	 * @param array $args Array of arguments. See function description for detailed information.
	 * @return string
	 * @filter string webcomic_term_cloud Filters the HTML returned by `webcomic_term_cloud` and used by `webcomic_storyline_cloud` and `webcomic_character_cloud`.
	 */
	public static function webcomic_term_cloud( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'id'         => '',
			'class'      => '',
			'smallest'   => 75,
			'largest'    => 150,
			'unit'       => '%',
			'image'      => '',
			'before'     => '',
			'after'      => '',
			'sep'        => "\n",
			'taxonomy'   => '',
			'order'      => 'RAND',
			'callback'   => '',
			'show_count' => false,
			'target'     => 'archive',
			'selected'   => 0
		) );
		
		extract( $r );
		
		$links  = $m = array();
		$output = '';
		
		if ( $terms = get_terms( $r[ 'taxonomy' ], $r ) and !is_wp_error( $terms ) ) {
			if ( 'RAND' === $order ) {
				shuffle( $terms );
			}
			
			if ( 0 < $number ) {
				$terms = array_slice( $terms, 0, $number );
			}
			
			$count = array();
			
			foreach ( $terms as $k => $v ) {
				$count[ $k ] = $v->count;
			}
			
			$minimum      = min( $count );
			$count_spread = 0 > max( $count ) - $minimum ? 1 : max( $count ) - $minimum;
			$font_spread  = 0 > $largest - $smallest ? 1 : $largest - $smallest;
			$font_step    = $font_spread / $count_spread;
			
			foreach ( $terms as $k => $v ) {
				$size       = $smallest + ( ( $v->count - $minimum ) * $font_step );
				$term_image = '';
				
				if ( $image and $v->webcomic_image ) {
					$dimensions = array();
					$term_image = wp_get_attachment_image( $v->webcomic_image, $image );
					
					if ( preg_match( '/width="(\d+)" height="(\d+)"/', $term_image, $dimensions ) ) {
						$width  = $dimensions[ 1 ] * ( $size / 100 );
						$height = $dimensions[ 2 ] * ( $size / 100 );
						
						$term_image = preg_replace( '/width="\d+" height="\d+"/', sprintf( 'width="%s" height="%s"', $width, $height ), $term_image );
					}
				}
				
				$links[] = $callback ? call_user_func( $callback, $v, $r ) : sprintf( '<a href="%s" class="webcomic-term webcomic-term-link-%s%s"%s style="font-size:%s%s">%s</a>',
					'archive' === $target ? get_term_link( $v, $v->taxonomy ) : self::get_relative_webcomic_link( $target, $v->term_id, false, $v->taxonomy, preg_replace( '/_(storyline|character)$/', '', $v->taxonomy ) ),
					$v->term_id,
					$selected === $v->term_id ? ' current' : '',
					$show_count ? ' title="' . sprintf( _n( '%s Webcomic', '%s Webcomics', $v->count, 'webcomic' ), $v->count ) . '"' : '',
					$size,
					$unit,
					$term_image ? $term_image : $v->name
				);
			}
			
			$id     = $id ? ' id="' . $id . '"' : '';
			$class  = sprintf( ' class="%s"', join( ' ', array_merge( array( 'webcomic-terms', $taxonomy, 'webcomic-terms-cloud', "{$taxonomy}-cloud" ), ( array ) $class ) ) );
			$output = $before . ( $sep ? "<div{$id}{$class}>" : '<ul{$id}{$class}><li>' ) .  join( $sep ? $sep : '</li><li>', $links ) . ( $sep ? '</div>' : '</li></ul>' ) . $after;
		}
		
		return apply_filters( 'webcomic_term_cloud', $output, $r );
	}
	
	/** Render a "cloud" of webcomic collections.
	 * 
	 * ### Arguments
	 * 
	 * - `string` **$id** - Value of the id attribute of the wrapping element.
	 * - `mixed` **$class** - String or array of additional classes for the wrapping element.
	 * - `integer` **$smallest** - The smallest font size to display links in.
	 * - `integer` **$largest** - The largest font size to display links in.
	 * - `string` **$unit** - The CSS unit to use for $smallest and $largest.
	 * - `string` **$image** - Size of the collection poster to use for collection links. Modified by the number of posts in a given term and the $smallest and $largest values.
	 * - `string` **$before** - Content to display before the output.
	 * - `string` **$after** - Content to display after the output.
	 * - `string` **$sep** - Separator to use between links. An empty value generates an unordered list. Defaults to "\n".
	 * - `string` **$order** - How to order collections, one of 'ASC' (default) or 'DESC'.
	 * - `string` **$orderby** - What to sort the collections by. May be one of 'name', 'slug', 'count', or 'updated'. Defaults to collection ID.
	 * - `string` **$order** - How to order collections. Defaults to 'RAND'.
	 * - `mixed` **$callback** - Callback function to use when building links.
	 * - `boolean` **$show_count** - Whether to display the total number of webcomics in a collection.
	 * - `string` **$target** - The target url for collections, one of 'archive', 'first', 'last', or 'random'. Defaults to 'archive'.
	 * - `integer` **$selected** - The ID of the current collection.
	 * 
	 * @param array $args Array of arguments. See function description for detailed information.
	 * @return string
	 * @filter string webcomic_collection_cloud Filters the output of `webcomic_collection_cloud`.
	 */
	public static function webcomic_collection_cloud( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'id'         => '',
			'class'      => '',
			'smallest'   => 75,
			'largest'    => 150,
			'unit'       => '%',
			'image'      => '',
			'before'     => '',
			'after'      => '',
			'sep'        => "\n",
			'order'      => 'ASC',
			'orderby'    => '',
			'order'      => 'RAND',
			'callback'   => '',
			'show_count' => false,
			'target'     => 'archive',
			'selected'   => 0
		) );
		
		extract( $r );
		
		$selected    = $selected ? $selected : self::get_webcomic_collection();
		$collections = self::get_webcomic_collections( true );
		
		if ( 'RAND' === $order ) {
			shuffle( $collections );
		} else if ( 'name' === $orderby ) {
			usort( $collections, array( 'WebcomicTag', 'sort_webcomic_collections_name' ) );
		} else if ( 'slug' === $orderby ) {
			usort( $collections, array( 'WebcomicTag', 'sort_webcomic_collections_slug' ) );
		} else if ( 'count' === $orderby ) {
			usort( $collections, array( 'WebcomicTag', 'sort_webcomic_collections_count' ) );
		} else if ( 'updated' === $orderby ) {
			usort( $collections, array( 'WebcomicTag', 'sort_webcomic_collections_updated' ) );
		}
		
		if ( 'DESC' === $order ) {
			$collections = array_reverse( $collections );
		}
		
		$links  = $m = array();
		$output = '';
		
		if ( 0 < $number ) {
			$collections = array_slice( $terms, 0, $number );
		}
		
		$count = array();
		
		foreach ( $collections as $v ) {
			$collection_count = wp_count_posts( $v[ 'id' ], 'readable' );
			$count[ $v[ 'id' ] ] = $collection_count->publish + $collection_count->private;
		}
		
		$minimum      = min( $count );
		$count_spread = 0 > max( $count ) - $minimum ? 1 : max( $count ) - $minimum;
		$font_spread  = 0 > $largest - $smallest ? 1 : $largest - $smallest;
		$font_step    = $font_spread / $count_spread;
		
		foreach ( $collections as $v ) {
			$size             = $smallest + ( ( $count[ $v[ 'id' ] ] - $minimum ) * $font_step );
			$collection_image = '';
			
			if ( $image and $v[ 'image' ] ) {
				$dimensions       = array();
				$collection_image = wp_get_attachment_image( $v[ 'image' ], $image );
				
				if ( preg_match( '/width="(\d+)" height="(\d+)"/', $collection_image, $dimensions ) ) {
					$width  = $dimensions[ 1 ] * ( $size / 100 );
					$height = $dimensions[ 2 ] * ( $size / 100 );
					
					$collection_image = preg_replace( '/width="\d+" height="\d+"/', sprintf( 'width="%s" height="%s"', $width, $height ), $collection_image );
				}
			}
			
			$links[] = $callback ? call_user_func( $callback, $v, $r ) : sprintf( '<a href="%s" class="webcomic-collection %s-collection-link%s"%s style="font-size:%s%s">%s</a>',
				'archive' === $target ? get_post_type_archive_link( $v[ 'id' ] ) : self::get_relative_webcomic_link( $target, false, false, '', $v[ 'id' ] ),
				$v[ 'id' ],
				$selected === $v[ 'id' ] ? ' current' : '',
				$show_count ? ' title="' . sprintf( _n( '%s Webcomic', '%s Webcomics', $count[ $v[ 'id' ] ], 'webcomic' ), $count[ $v[ 'id' ] ] ) . '"' : '',
				$size,
				$unit,
				$collection_image ? $collection_image : $v[ 'name' ]
			);
		}
		
		$id     = $id ? ' id="' . $id . '"' : '';
		$class  = sprintf( ' class="%s"', join( ' ', array_merge( array( 'webcomic-collections', 'webcomic-collections-cloud' ), ( array ) $class ) ) );
		$output = $before . ( $sep ? "<div{$id}{$class}>" : '<ul{$id}{$class}><li>' ) .  join( $sep ? $sep : '</li><li>', $links ) . ( $sep ? '</div>' : '</li></ul>' ) . $after;
		
		return apply_filters( 'webcomic_collections_cloud', $output, $r );
	}
}

///
// Utility Tags
///

if ( !function_exists( 'get_webcomic_collection' ) ) {
	/** Return the current collection ID or configuration.
	 * 
	 * <code>
	 * // return the current collection ID (if any)
	 * $collection = get_webcomic_collection();
	 * 
	 * // Return the current collection configuration in an array
	 * $collection = get_webcomic_collection( true );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param boolean $config Return the entire configuration for the current collection.
	 * @return string|array
	 * @uses WebcomicTag::get_webcomic_collection()
	 */
	function get_webcomic_collection( $config = false ) {
		return WebcomicTag::get_webcomic_collection( $config );
	}
}

if ( !function_exists( 'get_webcomic_collections' ) ) {
	/** Return all collection ID's or configurations.
	 * 
	 * <code>
	 * // return an array of all collection ID's
	 * $collections = get_webcomic_collections();
	 * 
	 * // Return an array of all collection configurations
	 * $collections = get_webcomic_collections( true );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param boolean $config Return the entire configuration for all collections.
	 * @return array
	 * @uses WebcomicTag::get_webcomic_collections()
	 */
	function get_webcomic_collections( $config = false ) {
		return WebcomicTag::get_webcomic_collections( $config );
	}
}

///
// Conditional Tags
///

if ( !function_exists( 'is_webcomic' ) ) {
	/** Is the query for any single webcomic?
	 * 
	 * <code>
	 * if ( is_webcomic() ) {
	 * 	// this is any single webcomic page
	 * }
	 * 
	 * if ( is_webcomic( true ) ) {
	 * 	// this is any single webcomic page that has been dynamically requested
	 * }
	 * 
	 * if ( is_singular( 'webcomic42' ) ) {
	 * 	// this is a single webcomic page belonging to collection 42
	 * }
	 * </code>
	 * 
	 * @package Webcomic
	 * @param boolean $dynamic Check for dynamically-requested webcomics.
	 * @return boolean
	 * @uses WebcomicTag::is_webcomic()
	 */
	function is_webcomic( $dynamic = false ) {
		return WebcomicTag::is_webcomic( $dynamic );
	}
}

if ( !function_exists( 'is_first_webcomic' ) ) {
	/** Is the query for the first webcomic?
	 * 
	 * <code>
	 * if ( is_first_webcomic() ) {
	 * 	// this is the first webcomic in the collection
	 * }
	 * 
	 * if ( is_first_webcomic( true, 42, 'character' ) ) {
	 * 	// This is the first webcomic featuring any similar character, excluding the character with ID 42
	 * }
	 * 
	 * if ( is_first_webcomic( false, false, '', 'webcomic42' ) ) {
	 * 	// this is the first webcomic in collection 42
	 * }
	 * </code>
	 * 
	 * @param mixed $in_same_term Whether the relative webcomic should be in a same term. May also be an array or comma-separated list of inclusive term IDs.
	 * @param mixed $excluded_terms An array or comma-separated list of excluded term IDs.
	 * @param string $taxonomy The taxonomy of the terms specified with $in_same_term and $excluded_terms arguments. The shorthand 'storyline' or 'character' may be used.
	 * @param string $collection The collection to compare from. Used when comparing outside of the loop.
	 * @return boolean
	 * @uses WebcomicTag::is_relative_webcomic()
	 */
	function is_first_webcomic( $in_same_term = false, $excluded_terms = false, $taxonomy = 'storyline', $collection = '' ) {
		return WebcomicTag::is_relative_webcomic( 'first', $in_same_term, $excluded_terms, $taxonomy, $collection );
	}
}

if ( !function_exists( 'is_last_webcomic' ) ) {
	/** Is the query for the last webcomic?
	 * 
	 * <code>
	 * if ( is_last_webcomic() ) {
	 * 	// this is the last webcomic in the collection
	 * }
	 * 
	 * if ( is_last_webcomic( true, 42, 'character' ) ) {
	 * 	// This is the last webcomic featuring any similar character, excluding the character with ID 42
	 * }
	 * 
	 * if ( is_last_webcomic( false, false, '', 'webcomic42' ) ) {
	 * 	// this is the last webcomic in collection 42
	 * }
	 * </code>
	 * 
	 * @param mixed $in_same_term Whether the relative webcomic should be in a same term. May also be an array or comma-separated list of inclusive term IDs.
	 * @param mixed $excluded_terms An array or comma-separated list of excluded term IDs.
	 * @param string $taxonomy The taxonomy of the terms specified with $in_same_term and $excluded_terms arguments. The shorthand 'storyline' or 'character' may be used.
	 * @param string $collection The collection to compare from. Used when comparing outside of the loop.
	 * @return boolean
	 * @uses WebcomicTag::is_relative_webcomic()
	 */
	function is_last_webcomic( $in_same_term = false, $excluded_terms = false, $taxonomy = 'storyline', $collection = '' ) {
		return WebcomicTag::is_relative_webcomic( 'last', $in_same_term, $excluded_terms, $taxonomy, $collection );
	}
}

if ( !function_exists( 'is_webcomic_attachment' ) ) {
	/** Is the query for a Webcomic-recognized attachment?
	 * 
	 * <code>
	 * if ( is_webcomic_attachment() ) {
	 * 	// this is a Webcomic-recognized attachment
	 * }
	 * 
	 * if ( is_webcomic_attachment( 'webcomic42' ) ) {
	 * 	// this is a webcomic-recognized attachment in collection 42
	 * }
	 * </code>
	 * 
	 * @param mixed $collection Collection ID or an array of these to check.
	 * @return boolean
	 * @uses WebcomicTag::is_webcomic_attachment()
	 */
	function is_webcomic_attachment( $collection = '' ) {
		return WebcomicTag::is_webcomic_attachment();
	}
}

if ( !function_exists( 'is_webcomic_page' ) ) {
	/** Is the query for a webcomic-related page?
	 * 
	 * <code>
	 * if ( is_webcomic_page() {
	 * 	// this is a webcomic-related page
	 * }
	 * 
	 * if ( is_webcomic_page( false, 'webcomic42' ) ) {
	 * 	// this is a page related to webcomic collection 42
	 * }
	 * 
	 * if ( is_webcomic_page( 2, 'webcomic42' ) {
	 * 	// the page with an ID of 2 is related to webcomic collection 42
	 * }
	 * </code>
	 * 
	 * @package Webcomic
	 * @param mixed $the_post Post object or ID to check.
	 * @param mixed $collection Collection ID or an array of these to check.
	 * @return boolean
	 * @uses WebcomicTag::is_webcomic_page()
	 */
	function is_webcomic_page( $the_post = false, $collection = '' ) {
		return WebcomicTag::is_webcomic_page( $the_post, $collection );
	}
}

if ( !function_exists( 'is_webcomic_archive' ) ) {
	/** Is the query for a webcomic archive page?
	 * 
	 * <code>
	 * if ( is_webcomic_archive() ) {
	 * 	// this is a webcomic archive page
	 * }
	 * 
	 * if ( is_post_type_archive( 'webcomic42' ) ) {
	 * 	// this is the archive page for webcomic collection 42
	 * }
	 * </code>
	 * 
	 * @package Webcomic
	 * @return boolean
	 * @uses WebcomicTag::is_webcomic_archive()
	 */
	function is_webcomic_archive() {
		return WebcomicTag::is_webcomic_archive();
	}
}

if ( !function_exists( 'is_webcomic_storyline' ) ) {
	/** Is the query for a webcomic storyline archive page?
	 * 
	 * <code>
	 * if ( is_webcomic_storyline() ) {
	 * 	// this is a webcomic storyline page
	 * }
	 * 
	 * if ( is_webcomic_storyline( 'mostly-harmless' ) {
	 * 	// this is a webcomic storyline page for any storyline in any collection with the slug 'mostly-harmless'
	 * }
	 * 
	 * if ( is_tax( 'webcomic42_storyline', 'mostly-harmless' ) ) {
	 * 	// this is a webcomic storyline page for a storyline in collection 42 with the slug 'mostly-harmless'
	 * }
	 * </code>
	 * 
	 * @package Webcomic
	 * @param mixed $storyline Term ID, name, slug or an array of term IDs, names, and slugs to check.
	 * @return boolean
	 * @uses WebcomicTag::is_webcomic_tax()
	 */
	function is_webcomic_storyline( $storyline = '' ) {
		return WebcomicTag::is_webcomic_tax( 'storyline', $storyline );
	}
}

if ( !function_exists( 'is_webcomic_character' ) ) {
	/** Is the query for a webcomic character archive page?
	 * 
	 * <code>
	 * if ( is_webcomic_character() ) {
	 * 	// this is a webcomic character page
	 * }
	 * 
	 * if ( is_webcomic_character( 'zaphod-beeblebrox' ) {
	 * 	// this is a webcomic character page for a character in any collection with the slug 'zaphod-beeblebrox'
	 * }
	 * 
	 * if ( is_tax( 'webcomic42_character', 'zaphod-beeblebrox' ) ) {
	 * 	// this is a webcomic character page for a character in collection 42 with the slug 'zaphod-beeblebrox'
	 * }
	 * </code>
	 * 
	 * @package Webcomic
	 * @param mixed $character Term ID, name, slug or an array of term IDs, names, and slugs to check.
	 * @return boolean
	 * @uses WebcomicTag::is_webcomic_tax()
	 */
	function is_webcomic_character( $character = '' ) {
		return WebcomicTag::is_webcomic_tax( 'character', $character );
	}
}

if ( !function_exists( 'is_a_webcomic' ) ) {
	/** Is the current post a webcomic?
	 * 
	 * <code>
	 * if ( is_a_webcomic() ) {
	 * 	// the current post is a webcomic
	 * }
	 * 
	 * if ( is_a_webcomic( 42 ) ) {
	 * 	// the post with an ID of 42 is a webcomic
	 * }
	 * 
	 * if ( 'webcomic42' === get_post_type() ) {
	 * 	// the current post is a webcomic in collection 42
	 * }
	 * </code>
	 * 
	 * @package Webcomic
	 * @param mixed $the_post Post object or ID to check.
	 * @return boolean
	 * @uses WebcomicTag::is_a_webcomic()
	 */
	function is_a_webcomic( $the_post = false ) {
		return WebcomicTag::is_a_webcomic( $the_post );
	}
}

if ( !function_exists( 'is_a_webcomic_attachment' ) ) {
	/** Is the current post a Webcomic-recognized attachment?
	 * 
	 * <code>
	 * if ( is_a_webcomic_attachment() ) {
	 * 	// the current post is a webcomic-recognized attachment
	 * }
	 * 
	 * if ( is_a_webcomic_attachment( 42 ) ) {
	 * 	// the post with an ID of 42 is a webcomic-recognized attachment
	 * }
	 * 
	 * if ( is_a_webcomic_attachment( 0, 'webcomic42' ) ) {
	 * 	// the current post is a webcomic-recognized attachment in collection 42
	 * }
	 * </code>
	 * 
	 * @param mixed $the_post Post object or ID to check.
	 * @param mixed $collection Collection ID or an array of these to check.
	 * @return boolean
	 * @uses WebcomicTag::is_a_webcomic_attachment()
	 */
	function is_a_webcomic_attachment( $the_post = false, $collection = '' ) {
		return WebcomicTag::is_a_webcomic_attachment( $the_post, $collection );
	}
}

if ( !function_exists( 'has_webcomic_attachments' ) ) {
	/** Does the current webcomic have any Webcomic-recognized attachments?
	 * 
	 * <code>
	 * if ( has_webcomic_attachments() ) {
	 * 	// the current post has webcomic-recognized attachments
	 * }
	 * 
	 * if ( has_webcomic_attachments( 42 ) ) {
	 * 	// the post with an ID of 42 has webcomic-recognized attachments
	 * }
	 * </code>
	 * 
	 * @param mixed $the_post Post object or ID to check.
	 * @return boolean
	 * @uses WebcomicTag::has_webcomic_attachments()
	 */
	function has_webcomic_attachments( $the_post = false ) {
		return WebcomicTag::has_webcomic_attachments( $the_post );
	}
}

if ( !function_exists( 'has_webcomic_storyline' ) ) {
	/** Does the current post belong to a specific storyline?
	 * 
	 * <code>
	 * if ( has_webcomic_storyline( 'mostly-harmless' ) ) {
	 * 	// the current post is part of a storyline with the slug 'mostly-harmless'
	 * }
	 * 
	 * if ( has_webcomic_storyline( 'mostly-harmless', 42 ) ) {
	 * 	// the post with an ID of 42 is part of a storyline with the slug 'mostly-harmless'
	 * }
	 * </code>
	 * 
	 * @package Webcomic
	 * @param mixed $term The name, storyline ID, slug, or an array of these to check.
	 * @param mixed $the_post Post object or ID to check for storylines.
	 */
	function has_webcomic_storyline( $term, $the_post = false ) {
		$post_type = get_post_type( $the_post );
		
		return has_term( $term, "{$post_type}_storyline", $the_post );
	}
}

if ( !function_exists( 'has_webcomic_character' ) ) {
	/** Does the current post feature to a specific character?
	 * 
	 * <code>
	 * if ( has_webcomic_character( 'zaphod-beeblebrox' ) ) {
	 * 	// the current post features a character with the slug 'zaphod-beeblebrox'
	 * }
	 * 
	 * if ( has_webcomic_character( 'zaphod-beeblebrox', 42 ) ) {
	 * 	// the post with an ID of 42 features a character with the slug 'zaphod-beeblebrox'
	 * }
	 * </code>
	 * 
	 * @package Webcomic
	 * @param mixed $term The name, character ID, slug, or an array of these to check.
	 * @param mixed $the_post Post object or ID to check for characters.
	 */
	function has_webcomic_character( $term, $the_post = false ) {
		$post_type = get_post_type( $the_post );
		
		return has_term( $term, "{$post_type}_character", $the_post );
	}
}

if ( !function_exists( 'have_webcomic_transcripts' ) ) {
	/** Does the current webcomic have any transcripts?
	 * 
	 * <code>
	 * if ( have_webcomic_transcripts() ) {
	 * 	// the current post has published transcripts
	 * }
	 * 
	 * if ( have_webcomic_transcripts( true ) ) {
	 * 	// the current post has transcripts pending review
	 * }
	 * 
	 * if ( have_webcomic_transcripts( false, 'en' ) ) {
	 * 	// the current post has published transcripts assigned to the language with a slug of 'en'
	 * }
	 * 
	 * if ( have_webcomic_transcripts( false, '', 42 ) ) {
	 * 	// the post with an ID of 42 has published transcripts
	 * }
	 * </code>
	 * 
	 * @package Webcomic
	 * @param boolean $pending Does the current webcomic have any transcripts pending review?
	 * @param string $language Language slug to limit any transcripts to.
	 * @param mixed $the_post Post object or ID to check for transcripts.
	 * @return boolean
	 * @uses WebcomicTag::have_webcomic_transcripts()
	 */
	function have_webcomic_transcripts( $pending = false, $language = '', $the_post = false ) {
		return WebcomicTag::have_webcomic_transcripts( $pending, $language, $the_post );
	}
}

if ( !function_exists( 'webcomic_transcripts_open' ) ) {
	/** Does the current webcomic allow transcribing?
	 * 
	 * <code>
	 * if ( webcomic_transcripts_open() ) {
	 * 	// the current post allows transcribing
	 * }
	 * 
	 * if ( webcomic_transcripts_open( 42 ) {
	 * 	// the post with an ID of 42 allows transcribing
	 * }
	 * </code>
	 * 
	 * @package Webcomic
	 * @param mixed $the_post Post object or ID to check for transcribing permissions.
	 * @return boolean
	 * @uses WebcomicTag::webcomic_transcripts_open()
	 */
	function webcomic_transcripts_open( $the_post = false ) {
		return WebcomicTag::webcomic_transcripts_open( $the_post );
	}
}

if ( !function_exists( 'webcomic_prints_available' ) ) {
	/** Does the current webcomic have prints available?
	 * 
	 * <code>
	 * if ( webcomic_prints_available() ) {
	 * 	// the current post has prints available
	 * }
	 * 
	 * if ( webcomic_prints_available( true ) ) {
	 * 	// the current post has an original, traditional-media print available
	 * }
	 * 
	 * if ( webcomic_prints_available( false, 42 ) {
	 * 	// the post with an ID of 42 has prints available
	 * }
	 * </code>
	 * 
	 * @package Webcomic
	 * @param boolean $original Whether an original, traditional-media print is available.
	 * @param mixed $the_post Post object or ID to check for prints.
	 * @return boolean
	 * @uses WebcomicTag::webcomic_prints_available()
	 */
	function webcomic_prints_available( $original = false, $the_post = false ) {
		return WebcomicTag::webcomic_prints_available( $original, $the_post );
	}
}

if ( !function_exists( 'verify_webcomic_age' ) ) {
	/** Verify a users age against collection age limit.
	 * 
	 * <code>
	 * if ( is_null( verify_webcomic_age() ) ) {
	 * 	// the current user's age has not be checked
	 * } else if ( verify_webcomic_age() ) {
	 * 	// the current user is old enough to view content in the current collection
	 * } else {
	 * 	// the current user is not old enough to view content in the current collection
	 * }
	 * 
	 * if ( verify_webcomic_age( 'webcomic42' ) ) {
	 * 	// the current user is old enough to view content in webcomic collection 42
	 * }
	 * 
	 * if ( verify_webcomic_age( 'webcomic42', 2 ) ) {
	 * 	// the user with an ID of 2 is old enough to view content in webcomic collection 42
	 * }
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $collection The collection to verify against.
	 * @param object $user The user to verify with (defaults to the current user).
	 * @param integer $age Age (in years) to verify against. Overrides the collection age setting, or forces use of the collection age if -1.
	 * @return boolean|null
	 * @uses WebcomicTag::verify_webcomic_age()
	 */
	function verify_webcomic_age( $collection = '', $user = false, $age = 0 ) {
		return WebcomicTag::verify_webcomic_age( $collection, $user, $age );
	}
}

if ( !function_exists( 'verify_webcomic_role' ) ) {
	/** Verify a users role against allowed collection roles.
	 * 
	 * <code>
	 * if ( is_null( verify_webcomic_role() ) ) {
	 * 	// the current user is not logged in
	 * } else if ( verify_webcomic_role() ) {
	 * 	// the current user has permission to view content in the current collection
	 * } else {
	 * 	// the current user does not have permission to view content in the current collection
	 * }
	 * 
	 * if ( verify_webcomic_role( 'webcomic42' ) ) {
	 * 	// the current user has permission  to view content in webcomic collection 42
	 * }
	 * 
	 * if ( verify_webcomic_role( 'webcomic42', 2 ) ) {
	 * 	// the user with an ID of 2 has permission to view content in webcomic collection 42
	 * }
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $collection The collection to verify against.
	 * @param object $user The user to verify with (defaults to the current user).
	 * @param array $roles The role or roles users must belong to. Overrides the collection role setting, or forces use of the collection role setting if -1.
	 * @return boolean|null
	 * @uses WebcomicTag::verify_webcomic_role()
	 */
	function verify_webcomic_role( $collection = '', $user = false, $roles = array() ) {
		return WebcomicTag::verify_webcomic_role( $collection, $user, $roles );
	}
}

///
// Single Webcomic Tags
///

if ( !function_exists( 'the_webcomic' ) ) {
	/** Render a webcomic.
	 * 
	 * <code>
	 * // render webcomic attachments for the current post
	 * the_webcomic();
	 * 
	 * // render small webcomic attachments for the post with an ID of 42 linked to the first webcomic in the collection
	 * the_webcomic( 'thumbnail', 'first', false, false, '', 42 );
	 * 
	 * // render large webcomic attachments for the current post linked to the next webcomic in the storyline with an ID of 42
	 * the_webomic( 'large', 'next', 42, false, 'storyline' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $size The size attachments should be displayed at. May be any registered size; defaults are 'full', 'large', 'medium', and 'thumbnail'.
	 * @param string $relative Whether to link the webcomic. May be one of 'self', 'next', 'previous', 'first', 'last', 'random', or 'random-nocache'.
	 * @param mixed $in_same_term An array or comma-separated list of inclusive term IDs.
	 * @param mixed $excluded_terms An array or comma-separated list of excluded term IDs.
	 * @param string $taxonomy The taxonomy of the terms specified in the $in_same_term and $excluded_terms arguments. The shorthand 'storyline' or 'character' may be used.
	 * @param mixed $the_post Post ID or object to render webcomics for.
	 * @uses WebcomicTag::the_webcomic()
	 */
	function the_webcomic( $size = 'full', $relative = '', $in_same_term = false, $excluded_terms = false, $taxonomy = 'storyline', $the_post = false ) {
		echo WebcomicTag::the_webcomic( $size, $relative, $in_same_term, $excluded_terms, $taxonomy, $the_post );
	}
}

if ( !function_exists( 'the_related_webcomics' ) ) {
	/** Render a formatted list of related webcomics.
	 * 
	 * <code>
	 * // render a comma-separated list of up to five related webcomics
	 * the_related_webcomics();
	 * 
	 * // render an ordered list of up to to ten webcomics related by characters using small images
	 * the_related_webcomics( '<ol class="related-webcomics"><li>', '</li><li>', '</li></ol>', 'thumbnail', 10, false, true );
	 * 
	 * // render a comma-separated list of all webcomics related by storyline to the post with an ID of 42
	 * the_related_webcomics( '<h2>Related Webcomics</h2><p>', ', ', '</p>', '', 0, true, false, 42 );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $before Before list.
	 * @param string $sep Separate items using this.
	 * @param string $after After list.
	 * @param string $image Image size to use when displaying webcomic images for links.
	 * @param integer $limit The number of related webcomics to display.
	 * @param boolean $storylines Match based on storylines.
	 * @param boolean $characters Match based on characters.
	 * @param mixed $the_post The post object or ID to match.
	 * @uses WebcomicTag::the_related_webcomics()
	 */
	function the_related_webcomics( $before = '', $sep = ', ', $after = '', $image = '', $limit = 5, $storylines = true, $characters = true, $the_post = false ) {
		echo Webcomic_tag::the_related_webcomics( $before, $sep, $after, $image, $limit, $storylines, $characters, $the_post );
	}
}

if ( !function_exists( 'previous_webcomic_link' ) ) {
	/** Render a link to the previous chronological webcomic.
	 * 
	 * <code>
	 * // render a link to the previous webcomic
	 * previous_webcomic_link();
	 * 
	 * // render a link to the previous webcomic with a small preview
	 * previous_webcomic_link( '%link', '%thumbnail' );
	 * 
	 * // render a link to the previous webcomic in the current storylines, excluding the storyline with an ID of 42
	 * previous_webcomic_link( '<b>%link</b>', '&lt; Previous', true, 42 );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $link Format string for the link text. Accepts %title, %date, and image size tokens.
	 * @param mixed $in_same_term Whether the linked webcomic should be in a same term. May also be an array or comma-separated list of inclusive term IDs.
	 * @param mixed $excluded_terms An array or comma-separated list of excluded term IDs.
	 * @param string $taxonomy The taxonomy of the terms specified in the $in_same_term and $excluded_terms arguments. The shorthand 'storyline' or 'character' may be used.
	 * @uses WebcomicTag::relative_webcomic_link()
	 */
	function previous_webcomic_link( $format = '%link', $link = '&lsaquo;', $in_same_term = false, $excluded_terms = false, $taxonomy = 'storyline' ) {
		echo WebcomicTag::relative_webcomic_link( $format, $link, 'previous', $in_same_term, $excluded_terms, $taxonomy );
	}
}

if ( !function_exists( 'next_webcomic_link' ) ) {
	/** Render a link to the next chronological webcomic.
	 * 
	 * <code>
	 * // render a link to the next webcomic
	 * next_webcomic_link();
	 * 
	 * // render a link to the next webcomic with a small preview
	 * next_webcomic_link( '%link', '%thumbnail' );
	 * 
	 * // render a link to the next webcomic in the current storylines, excluding the storyline with an ID of 42
	 * next_webcomic_link( '<b>%link</b>', 'Next &gt;', true, 42 );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $link Format string for the link text. Accepts %title, %date, and image size tokens.
	 * @param mixed $in_same_term Whether the linked webcomic should be in a same term. May also be an array or comma-separated list of inclusive term IDs.
	 * @param mixed $excluded_terms An array or comma-separated list of excluded term IDs.
	 * @param string $taxonomy The taxonomy of the terms specified in the $in_same_term and $excluded_terms arguments. The shorthand 'storyline' or 'character' may be used.
	 * @uses WebcomicTag::relative_webcomic_link()
	 */
	function next_webcomic_link( $format = '%link', $link = '&rsaquo;', $in_same_term = false, $excluded_terms = false, $taxonomy = 'storyline' ) {
		echo WebcomicTag::relative_webcomic_link( $format, $link, 'next', $in_same_term, $excluded_terms, $taxonomy );
	}
}

if ( !function_exists( 'first_webcomic_link' ) ) {
	/** Render a link to the first chronological webcomic.
	 * 
	 * <code>
	 * // render a link to the first webcomic
	 * first_webcomic_link();
	 * 
	 * // render a link to the first webcomic with a small preview
	 * first_webcomic_link( '%link', '%thumbnail' );
	 * 
	 * //  render a link to the first webcomic in the current storylines, excluding the storyline with an ID of 42
	 * first_webcomic_link( '<b>%link</b>', '&lt;&lt; First', true, 42 );
	 * 
	 * // render a link to the first webcomic with a large preview in collection 42
	 * first_webcomic_link( '%link', '%large', false, false, '', 'webcomic42' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $link Format string for the link text. Accepts %title, %date, and image size tokens.
	 * @param mixed $in_same_term Whether the linked webcomic should be in a same term. May also be an array or comma-separated list of inclusive term IDs.
	 * @param mixed $excluded_terms An array or comma-separated list of excluded term IDs.
	 * @param string $taxonomy The taxonomy of the terms specified in the $in_same_term and $excluded_terms arguments. The shorthand 'storyline' or 'character' may be used.
	 * @param string $collection The collection to retrieve from. Used when linking outside the loop.
	 * @uses WebcomicTag::relative_webcomic_link()
	 */
	function first_webcomic_link( $format = '%link', $link = '&laquo;', $in_same_term = false, $excluded_terms = false, $taxonomy = 'storyline', $collection = '' ) {
		echo WebcomicTag::relative_webcomic_link( $format, $link, 'first', $in_same_term, $excluded_terms, $taxonomy, $collection );
	}
}

if ( !function_exists( 'last_webcomic_link' ) ) {
	/** Render a link to the last chronological webcomic.
	 * 
	 * <code>
	 * // render a link to the last webcomic
	 * last_webcomic_link();
	 * 
	 * // render a link to the last webcomic with a small preview
	 * last_webcomic_link( '%link', '%thumbnail' );
	 * 
	 * // render a link to the last webcomic in the current storylines, excluding the storyline with an ID of 42
	 * last_webcomic_link( '<b>%link</b>', 'Last &gt;&gt;', true, 42 );
	 * 
	 * // render a link to the last webcomic with a large preview in collection 42
	 * last_webcomic_link( '%link', '%large', false, false, '', 'webcomic42' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $link Format string for the link text. Accepts %title, %date, and image size tokens.
	 * @param mixed $in_same_term Whether the linked webcomic should be in a same term. May also be an array or comma-separated list of inclusive term IDs.
	 * @param mixed $excluded_terms An array or comma-separated list of excluded term IDs.
	 * @param string $taxonomy The taxonomy of the terms specified in the $in_same_term and $excluded_terms arguments. The shorthand 'storyline' or 'character' may be used.
	 * @param string $collection The collection to retrieve from. Used when linking outside the loop.
	 * @uses WebcomicTag::relative_webcomic_link()
	 */
	function last_webcomic_link( $format = '%link', $link = '&raquo;', $in_same_term = false, $excluded_terms = false, $taxonomy = 'storyline', $collection = '' ) {
		echo WebcomicTag::relative_webcomic_link( $format, $link, 'last', $in_same_term, $excluded_terms, $taxonomy, $collection );
	}
}

if ( !function_exists( 'random_webcomic_link' ) ) {
	/** Render a link to a randomly selected webcomic.
	 * 
	 * <code>
	 * // render a link to a random webcomic
	 * random_webcomic_link();
	 * 
	 * // render a link to a random webcomic with a small preview
	 * random_webcomic_link( '%link', '%thumbnail' );
	 * 
	 * // render a link to a random webcomic in the current storylines, excluding the storyline with an ID of 42
	 * random_webcomic_link( '<b>%link</b>', 'Random Comic', true, 42 );
	 * 
	 * // render a link to a random webcomic with a large preview in collection 42 using a parameterized url
	 * random_webcomic_link( '%link', '%large', false, false, '', 'webcomic42', false );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $link Format string for the link text. Accepts %title, %date, and image size tokens.
	 * @param mixed $in_same_term Whether the linked webcomic should be in a same term. May also be an array or comma-separated list of inclusive term IDs.
	 * @param mixed $excluded_terms An array or comma-separated list of excluded term IDs.
	 * @param string $taxonomy The taxonomy of the terms specified in the $in_same_term and $excluded_terms arguments. The shorthand 'storyline' or 'character' may be used.
	 * @param string $collection The collection to retrieve from. Used when linking first, last, or random webcomics outside of the loop.
	 * @param boolean $cache Whether to use a parameterized random webcomic link.
	 * @uses WebcomicTag::relative_webcomic_link()
	 */
	function random_webcomic_link( $format = '%link', $link = '&infin;', $in_same_term = false, $excluded_terms = false, $taxonomy = 'storyline', $collection = '', $cache = true ) {
		echo WebcomicTag::relative_webcomic_link( $format, $link, $cache ? 'random' : 'random-nocache', $in_same_term, $excluded_terms, $taxonomy, $collection );
	}
}

if ( !function_exists( 'purchase_webcomic_link' ) ) {
	/** Render a purchase webcomic link.
	 * 
	 * <code>
	 * // render a link to purchase the current webcomic
	 * purchase_webcomic_link();
	 * 
	 * // render a link to a random webcomic with a small preview
	 * purchase_webcomic_link( '%link', '%thumbnail' );
	 * 
	 * // render a link to purchase the webcomic with an ID of 42
	 * purchase_webcomic_link( '%link', 'Purchase prints of %title', 42 );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $link Format string for the link text. Accepts %title, %date, and image size tokens.
	 * @param mixed $the_post The post object or ID to retrive the purchase link for.
	 * @uses WebcomicTag::purchase_webcomic_link()
	 */
	function purchase_webcomic_link( $format = '%link', $link = '&curren;', $the_post = false ) {
		echo WebcomicTag::purchase_webcomic_link( $format, $link, $the_post );
	}
}

if ( !function_exists( 'the_webcomic_collection' ) ) {
	/** Render a webcomic collection link.
	 * 
	 * <code>
	 * // render a link to the collection archive page for the collection the current webcomic belongs to
	 * the_webcomic_collection();
	 * 
	 * // render a link to the beginning of collection 42 with a small poster preview
	 * the_webcomic_collection( '%link', '%thumbnail', 'first', 'webcomic42' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $link Format string for the link text. Accepts %title and image size tokens tokens.
	 * @param string $target Where the collection links should point to; may be one of 'archive', 'first', 'last', or 'random'.
	 * @param string $collection The collection ID to render a link for.
	 * @uses WebcomicTag::webcomic_collection_link()
	 */
	function the_webcomic_collection( $format = '%link', $link = '%title', $target = 'archive', $collection = '' ) {
		echo WebcomicTag::webcomic_collection_link( $format, $link, $target, $collection );
	}
}

if ( !function_exists( 'the_webcomic_storylines' ) ) {
	/** Render a formatted list of storylines related to the current webcomic.
	 * 
	 * <code>
	 * // render a comma-separated list of storylines related to the current webcomic
	 * the_webcomic_storylines();
	 * 
	 * // render an unordered list of storylines related to the current webcomic
	 * the_webcomic_storylines( '<ul><li>', '</li><li>', </li></ul>' );
	 * 
	 * // render links to the first webcomic in each storyline related to the current webcomic with a small storyline cover
	 * the_webcomic_storylines( '<div><h2>Storylines</h2><figure>', '</figure><figure>', '</figure></div>', 'first', 'thumbnail' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $before Before list.
	 * @param string $sep Separate items using this.
	 * @param string $after After list.
	 * @param string $target Where the term links should point to, one of 'archive', 'first', 'last', or 'random'.
	 * @param string $image Image size to use when displaying term images for links.
	 * @uses WebcomicTag::get_the_webcomic_term_list()
	 */
	function the_webcomic_storylines( $before = '', $sep = ', ', $after = '', $target = 'archive', $image = '' ) {
		echo WebcomicTag::get_the_webcomic_term_list( 0, 'storyline', $before, $sep, $after, $target, $image );
	}
}

if ( !function_exists( 'the_webcomic_characters' ) ) {
	/** Render a formatted list of characters appearing to the current webcomic.
	 * 
	 * <code>
	 * // render a comma-separated list of characters appearing in the current webcomic
	 * the_webcomic_characters();
	 * 
	 * // render an unordered list of characters appearing in the current webcomic
	 * the_webcomic_characters( '<ul><li>', '</li><li>', </li></ul>' );
	 * 
	 * // render links to the first appearance of each character appearing in the current webcomic with a small character avatar
	 * the_webcomic_characters( '<div><h2>Characters</h2><figure>', '</figure><figure>', '</figure></div>', 'first', 'thumbnail' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $before Before list.
	 * @param string $sep Separate items using this.
	 * @param string $after After list.
	 * @param string $target Where the term links should point to, one of 'archive', 'first', 'last', or 'random'.
	 * @param string $image Image size to use when displaying term images for links.
	 * @uses WebcomicTag::get_the_webcomic_term_list()
	 */
	function the_webcomic_characters( $before = '', $sep = ', ', $after = '', $target = 'archive', $image = '' ) {
		echo WebcomicTag::get_the_webcomic_term_list( 0, 'character', $before, $sep, $after, $target, $image );
	}
}

///
// Single Term Tags
///

if ( !function_exists( 'previous_webcomic_storyline_link' ) ) {
	/** Render a link to the previous webcomic storyline.
	 * 
	 * <code>
	 * // render a link to the archive page for the previous storyline
	 * previous_webcomic_storyline_link();
	 * 
	 * // render a link to the first webcomic in the previous storyline with a small cover preview
	 * previous_webcomic_storyline_link( '%link', '%thumbnail', 'first' );
	 * 
	 * // render a link to the archive page for the previous storyline, even if it doesn't have any webcomics
	 * previous_webcomic_storyline_link( '%link', '&lt; Previous Arc', 'archive', array( 'hide_empty' => false ) );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $link Format string for the link text. Accepts %title and image size tokens.
	 * @param string $target The target url, one of 'archive', 'first', 'last', or 'random'.
	 * @param array $args An array of arguments to pass to get_terms().
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::relative_webcomic_term_link()
	 */
	function previous_webcomic_storyline_link( $format = '%link', $link = '&lsaquo; %title', $target = 'archive', $args = array() ) {
		global $post;
		
		$taxonomy = ( ( is_tax() or is_single() ) and $collection = WebcomicTag::get_webcomic_collection() ) ? "{$collection}_storyline" : '';
		
		if ( preg_match( '/^webcomic\d+_storyline$/', $taxonomy ) ) {
			echo WebcomicTag::relative_webcomic_term_link( $format, $link, $target, 'previous', $taxonomy, $args );
		}
	}
}

if ( !function_exists( 'next_webcomic_storyline_link' ) ) {
	/** Render a link to the next webcomic storyline.
	 * 
	 * <code>
	 * // render a link to the archive page for the next storyline
	 * next_webcomic_storyline_link();
	 * 
	 * // render a link to the first webcomic in the next storyline with a small cover preview
	 * next_webcomic_storyline_link( '%link', '%thumbnail', 'first' );
	 * 
	 * // render a link to the archive page for the next storyline, even if it doesn't have any webcomics
	 * next_webcomic_storyline_link( '%link', 'Next Arc &gt;', 'archive', array( 'hide_empty' => false ) );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $link Format string for the link text. Accepts %title and image size tokens.
	 * @param string $target The target url, one of 'archive', 'first', 'last', or 'random'.
	 * @param array $args An array of arguments to pass to get_terms().
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::relative_webcomic_term_link()
	 */
	function next_webcomic_storyline_link( $format = '%link', $link = '%title &rsaquo;', $target = 'archive', $args = array() ) {
		global $post;
		
		$taxonomy = ( ( is_tax() or is_single() ) and $collection = WebcomicTag::get_webcomic_collection() ) ? "{$collection}_storyline" : '';
		
		if ( preg_match( '/^webcomic\d+_storyline$/', $taxonomy ) ) {
			echo WebcomicTag::relative_webcomic_term_link( $format, $link, $target, 'next', $taxonomy, $args );
		}
	}
}

if ( !function_exists( 'first_webcomic_storyline_linke' ) ) {
	/** Render a link to the first webcomic storyline.
	 * 
	 * <code>
	 * // render a link to the archive page for the first storyline
	 * first_webcomic_storyline_link();
	 * 
	 * // render a link to the first webcomic in the first storyline with a small cover preview
	 * first_webcomic_storyline_link( '%link', '%thumbnail', 'first' );
	 * 
	 * // render a link to the archive page for the first storyline, even if it doesn't have any webcomics
	 * first_webcomic_storyline_link( '%link', '&lt;&lt; First Arc', 'archive', array( 'hide_empty' => false ) );
	 * 
	 * // render a link to the last page for the first storyline in webcomic 42
	 * first_webcomic_storyline_link( '%link', '&lt;&lt; First Arc', 'last', array(), 'webcomic42' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $link Format string for the link text. Accepts %title and image size tokens.
	 * @param string $target The target url, one of 'archive', 'first', 'last', or 'random'.
	 * @param array $args An array of arguments to pass to get_terms().
	 * @param string $collection Collection ID to retrieve storylines from.
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::relative_webcomic_term_link()
	 */
	function first_webcomic_storyline_link( $format = '%link', $link = '&laquo; %title', $target = 'archive', $args = array(), $collection = '' ) {
		global $post;
		
		$taxonomy = ( ( $collection and taxonomy_exists( "{$collection}_storyline" ) ) or $collection = WebcomicTag::get_webcomic_collection() ) ? "{$collection}_storyline" : '';
		
		if ( preg_match( '/^webcomic\d+_storyline$/', $taxonomy ) ) {
			echo WebcomicTag::relative_webcomic_term_link( $format, $link, $target, 'first', $taxonomy, $args );
		}
	}
}

if ( !function_exists( 'last_webcomic_storyline_link' ) ) {
	/** Render a link to the last webcomic storyline.
	 * 
	 * <code>
	 * // render a link to the archive page for the last storyline
	 * last_webcomic_storyline_link();
	 * 
	 * // render a link to the last webcomic in the last storyline with a small cover preview
	 * last_webcomic_storyline_link( '%link', '%thumbnail', 'last' );
	 * 
	 * // render a link to the archive page for the last storyline, even if it doesn't have any webcomics
	 * last_webcomic_storyline_link( '%link', 'Last Arc &gt;&gt;', 'archive', array( 'hide_empty' => false ) );
	 * 
	 * // render a link to the first page for the last storyline in webcomic 42
	 * last_webcomic_storyline_link( '%link', 'Last Arc &gt;&gt;', 'first', array(), 'webcomic42' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $link Format string for the link text. Accepts %title and image size tokens.
	 * @param string $target The target url, one of 'archive', 'first', 'last', or 'random'.
	 * @param array $args An array of arguments to pass to get_terms().
	 * @param string $collection Collection ID to retrieve storylines from.
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::relative_webcomic_term_link()
	 */
	function last_webcomic_storyline_link( $format = '%link', $link = '%title &raquo;', $target = 'archive', $args = array(), $collection = '' ) {
		global $post;
		
		$taxonomy = ( ( $collection and taxonomy_exists( "{$collection}_storyline" ) ) or $collection = WebcomicTag::get_webcomic_collection() ) ? "{$collection}_storyline" : '';
		
		if ( preg_match( '/^webcomic\d+_storyline$/', $taxonomy ) ) {
			echo WebcomicTag::relative_webcomic_term_link( $format, $link, $target, 'last', $taxonomy, $args );
		}
	}
}

if ( !function_exists( 'random_webcomic_storyline_link' ) ) {
	/** Render a link to a randomly selected webcomic storyline.
	 * 
	 * <code>
	 * // render a link to the archive page of a random webcomic
	 * random_webcomic_storyline_link();
	 * 
	 * // render a link to the last webcomic in a random storyline with a small cover preview
	 * random_webcomic_storyline_link( '%link', '%thumbnail', 'last' );
	 * 
	 * // render a link to the archive page of a random storyline, even if it doesn't have any webcomics
	 * random_webcomic_storyline_link( '%link', 'Random Arc', 'archive', array( 'hide_empty' => false ) );
	 * 
	 * // render a link to a random page in a random storyline in collection 42
	 * random_webcomic_storyline_link( '%link', 'Random Storyline', 'random', array(), 'webcomic42' );
	 * 
	 * // render a link to a random webcomic with a large cover in collection 42 using a parameterized url
	 * random_webcomic_storyline_link( '%link', '%large', 'last', array(), 'webcomic42', false );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $link Format string for the link text. Accepts %title and image size tokens.
	 * @param string $target The target url, one of 'archive', 'first', 'last', or 'random'.
	 * @param array $args An array of arguments to pass to get_terms().
	 * @param string $collection Collection ID to retrieve storylines from.
	 * @param boolean $cache Whether to use a parameterized random webcomic storyline link.
	 * @uses WebcomicTag::relative_webcomic_term_link()
	 */
	function random_webcomic_storyline_link( $format = '%link', $link = '%title', $target = 'archive', $args = array(), $collection = '', $cache = true ) {
		$taxonomy = ( ( $collection and taxonomy_exists( "{$collection}_storyline" ) ) or $collection = WebcomicTag::get_webcomic_collection() ) ? "{$collection}_storyline" : '';
		
		if ( preg_match( '/^webcomic\d+_storyline$/', $taxonomy ) ) {
			echo WebcomicTag::relative_webcomic_term_link( $format, $link, $target, $cache ? 'random' : 'random-nocache', $taxonomy, $args );
		}
	}
}

if ( !function_exists( 'previous_webcomic_character_link' ) ) {
	/** Render a link to the previous webcomic character.
	 * 
	 * <code>
	 * // render a link to the archive page for the previous character
	 * previous_webcomic_character_link();
	 * 
	 * // render a link to the first webcomic in the previous character with a small cover preview
	 * previous_webcomic_character_link( '%link', '%thumbnail', 'first' );
	 * 
	 * // render a link to the archive page for the previous character, even if it doesn't have any webcomics
	 * previous_webcomic_character_link( '%link', '&lt; Previous Character', 'archive', array( 'hide_empty' => false ) );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $link Format string for the link text. Accepts %title and image size tokens.
	 * @param string $target The target url, one of 'archive', 'first', 'last', or 'random'.
	 * @param array $args An array of arguments to pass to get_terms().
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::relative_webcomic_term_link()
	 */
	function previous_webcomic_character_link( $format = '%link', $link = '&lsaquo; %title', $target = 'archive', $args = array() ) {
		global $post;
		
		$taxonomy = ( ( is_tax() or is_single() ) and $collection = WebcomicTag::get_webcomic_collection() ) ? "{$collection}_character" : '';
		
		if ( preg_match( '/^webcomic\d+_character$/', $taxonomy ) ) {
			echo WebcomicTag::relative_webcomic_term_link( $format, $link, $target, 'previous', $taxonomy, $args );
		}
	}
}

if ( !function_exists( 'next_webcomic_character_link' ) ) {
	/** Render a link to the next webcomic character.
	 * 
	 * <code>
	 * // render a link to the archive page for the next character
	 * next_webcomic_character_link();
	 * 
	 * // render a link to the first webcomic in the next character with a small cover preview
	 * next_webcomic_character_link( '%link', '%thumbnail', 'first' );
	 * 
	 * // render a link to the archive page for the next character, even if it doesn't have any webcomics
	 * next_webcomic_character_link( '%link', 'Next Character &gt;', 'archive', array( 'hide_empty' => false ) );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $link Format string for the link text. Accepts %title and image size tokens.
	 * @param string $target The target url, one of 'archive', 'first', 'last', or 'random'.
	 * @param array $args An array of arguments to pass to get_terms().
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::relative_webcomic_term_link()
	 */
	function next_webcomic_character_link( $format = '%link', $link = '%title &rsaquo;', $target = 'archive', $args = array() ) {
		global $post;
		
		$taxonomy = ( ( is_tax() or is_single() ) and $collection = WebcomicTag::get_webcomic_collection() ) ? "{$collection}_character" : '';
		
		if ( preg_match( '/^webcomic\d+_character$/', $taxonomy ) ) {
			echo WebcomicTag::relative_webcomic_term_link( $format, $link, $target, 'next', $taxonomy, $args );
		}
	}
}

if ( !function_exists( 'first_webcomic_character_link' ) ) {
	/** Render a link to the first webcomic character.
	 * 
	 * <code>
	 * // render a link to the archive page for the first character
	 * first_webcomic_character_link();
	 * 
	 * // render a link to the first webcomic in the first character with a small cover preview
	 * first_webcomic_character_link( '%link', '%thumbnail', 'first' );
	 * 
	 * // render a link to the archive page for the first character, even if it doesn't have any webcomics
	 * first_webcomic_character_link( '%link', '&lt;&lt; First Character', 'archive', array( 'hide_empty' => false ) );
	 * 
	 * // render a link to the last page for the first character in webcomic 42
	 * first_webcomic_character_link( '%link', '&lt;&lt; First Character', 'last', array(), 'webcomic42' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $link Format string for the link text. Accepts %title and image size tokens.
	 * @param string $target The target url, one of 'archive', 'first', 'last', or 'random'.
	 * @param array $args An array of arguments to pass to get_terms().
	 * @param string $collection Collection ID to retrieve characters from.
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::relative_webcomic_term_link()
	 */
	function first_webcomic_character_link( $format = '%link', $link = '&laquo; %title', $target = 'archive', $args = array(), $collection = '' ) {
		global $post;
		
		$taxonomy = ( ( $collection and taxonomy_exists( "{$collection}_character" ) ) or $collection = WebcomicTag::get_webcomic_collection() ) ? "{$collection}_character" : '';
		
		if ( preg_match( '/^webcomic\d+_character$/', $taxonomy ) ) {
			echo WebcomicTag::relative_webcomic_term_link( $format, $link, $target, 'first', $taxonomy, $args );
		}
	}
}

if ( !function_exists( 'last_webcomic_character_link' ) ) {
	/** Render a link to the last webcomic character.
	 * 
	 * <code>
	 * // render a link to the archive page for the last character
	 * last_webcomic_character_link();
	 * 
	 * // render a link to the last webcomic in the last character with a small cover preview
	 * last_webcomic_character_link( '%link', '%thumbnail', 'last' );
	 * 
	 * // render a link to the archive page for the last character, even if it doesn't have any webcomics
	 * last_webcomic_character_link( '%link', 'Last Character &gt;&gt;', 'archive', array( 'hide_empty' => false ) );
	 * 
	 * // render a link to the first page for the last character in webcomic 42
	 * last_webcomic_character_link( '%link', 'Last Character &gt;&gt;', 'first', array(), 'webcomic42' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $link Format string for the link text. Accepts %title and image size tokens.
	 * @param string $target The target url, one of 'archive', 'first', 'last', or 'random'.
	 * @param array $args An array of arguments to pass to get_terms().
	 * @param string $collection Collection ID to retrieve characters from.
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::relative_webcomic_term_link()
	 */
	function last_webcomic_character_link( $format = '%link', $link = '%title &raquo;', $target = 'archive', $args = array(), $collection = '' ) {
		global $post;
		
		$taxonomy = ( ( $collection and taxonomy_exists( "{$collection}_character" ) ) or $collection = WebcomicTag::get_webcomic_collection() ) ? "{$collection}_character" : '';
		
		if ( preg_match( '/^webcomic\d+_character$/', $taxonomy ) ) {
			echo WebcomicTag::relative_webcomic_term_link( $format, $link, $target, 'last', $taxonomy, $args );
		}
	}
}

if ( !function_exists( 'random_webcomic_character_link' ) ) {
	/** Render a link to a randomly selected webcomic character.
	 * 
	 * <code>
	 * // render a link to the archive page of a random webcomic
	 * random_webcomic_character_link();
	 * 
	 * // render a link to the last webcomic in a random character with a small avatar preview
	 * random_webcomic_character_link( '%link', '%thumbnail', 'last' );
	 * 
	 * // render a link to the archive page of a random character, even if it doesn't have any webcomics
	 * random_webcomic_character_link( '%link', 'Random Character', 'archive', array( 'hide_empty' => false ) );
	 * 
	 * // render a link to a random page in a random character in collection 42
	 * random_webcomic_character_link( '%link', 'Random Character', 'random', array(), 'webcomic42' );
	 * 
	 * // render a link to the last webcomic in a random character with a large avatar in collection 42 using a parameterized url
	 * random_webcomic_character_link( '%link', '%large', 'last', array(), 'webcomic42', false );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $link Format string for the link text. Accepts %title and image size tokens.
	 * @param string $target The target url, one of 'archive', 'first', 'last', or 'random'.
	 * @param array $args An array of arguments to pass to get_terms().
	 * @param string $collection Collection ID to retrieve characters from.
	 * @param boolean $cache Whether to use a parameterized random webcomic character link.
	 * @uses WebcomicTag::relative_webcomic_term_link()
	 */
	function random_webcomic_character_link( $format = '%link', $link = '%title', $target = 'archive', $args = array(), $collection = '', $cache = true ) {
		$taxonomy = ( ( $collection and taxonomy_exists( "{$collection}_character" ) ) or $collection = WebcomicTag::get_webcomic_collection() ) ? "{$collection}_character" : '';
		
		if ( preg_match( '/^webcomic\d+_character$/', $taxonomy ) ) {
			echo WebcomicTag::relative_webcomic_term_link( $format, $link, $target, $cache ? 'random' : 'random-nocache', $taxonomy, $args );
		}
	}
}

if ( !function_exists( 'webcomic_storyline_title' ) ) {
	/** Render the webcomic storyline title on a storyline archive page.
	 * 
	 * <code>
	 * // render the storyline title
	 * webcomic_storyline_title();
	 * 
	 * // assign the title of the storyline with an id of 42 in the 'webcomic42' collection to a variable for later use
	 * $storyline_title = WebcomicTag::webcomic_term_title( 'Storyline: ', 42, 'webcomic42' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $prefix Content to display before the title.
	 * @param mixed $storyline Storyline object or ID to render the title for. Will use global term ID by default.
	 * @param string $collection The collection the storyline belongs to.
	 * @uses WebcomicTag::webcomic_term_title()
	 * @uses WebcomicTag::get_webcomic_collection()
	 */
	function webcomic_storyline_title( $prefix = '', $storyline = 0, $collection = '' ) {
		echo WebcomicTag::webcomic_term_title( $prefix, $storyline, $collection ? "{$collection}_storyline" : '' );
	}
}

if ( !function_exists( 'webcomic_character_title' ) ) {
	/** Render the webcomic character name on a character archive page.
	 * 
	 * <code>
	 * // render the character name
	 * webcomic_character_title();
	 * 
	 * // assign the title of the character with an id of 42 in the 'webcomic42' collection to a variable for later use
	 * $character_title = WebcomicTag::webcomic_term_title( 'Character: ', 42, 'webcomic42' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $prefix Content to display before the title.
	 * @param mixed $character Character object or ID to render the title for. Will use global term ID by default.
	 * @param string $collection The collection the character belongs to.
	 * @uses WebcomicTag::webcomic_term_title()
	 * @uses WebcomicTag::get_webcomic_collection()
	 */
	function webcomic_character_title( $prefix = '', $character = 0, $collection = '' ) {
		echo WebcomicTag::webcomic_term_title( $prefix, $character, $collection ? "{$collection}_character" : '' );
	}
}

if ( !function_exists( 'webcomic_storyline_description' ) ) {
	/** Render the description for a webcomic storyline.
	 * 
	 * <code>
	 * // render the description of a webcomic storyline on the storyline archive page
	 * webcomic_storyline_description();
	 * 
	 * // assign the description of the webcomic storyline with an ID of 1 from collection 42 to a variable for later use
	 * $description = WebcomicTag::webcomic_term_description( 1, 'webcomic42_storyline' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param integer $storyline Storyline ID to render a description for. Will use global term ID by default.
	 * @param string $collection The collection the storyline belongs to.
	 * @return string
	 * @uses WebcomicTag::webcomic_term_description()
	 * @uses WebcomicTag::get_webcomic_collection()
	 */
	function webcomic_storyline_description( $storyline = 0, $collection = '' ) {
		echo WebcomicTag::webcomic_term_description( $storyline, $collection ? "{$collection}_storyline" : '' );
	}
}

if ( !function_exists( 'webcomic_character_description' ) ) {
	/** Render the description for a webcomic character.
	 * 
	 * <code>
	 * // render the description of a webcomic character on the character archive page
	 * webcomic_character_description();
	 * 
	 * // assign the description of the webcomic character with an ID of 1 from collection 42 to a variable for later use
	 * $description = WebcomicTag::webcomic_term_description( 1, 'webcomic42_character' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param integer $character Character ID to render a description for. Will use global term ID by default.
	 * @param string $collection The collection the character belongs to.
	 * @return string
	 * @uses WebcomicTag::webcomic_term_description()
	 * @uses WebcomicTag::get_webcomic_collection()
	 */
	function webcomic_character_description( $character = 0, $collection = '' ) {
		echo WebcomicTag::webcomic_term_description( $character, $collection ? "{$collection}_character" : '' );
	}
}

if ( !function_exists( 'webcomic_storyline_cover' ) ) {
	/** Render the storyline cover on a storyline archive page.
	 * 
	 * <code>
	 * // render the full size storyline cover
	 * webcomic_storyline_cover();
	 * 
	 * // assign the medium size storyline cover for for the storyline with an ID of 42 in collection 42 to a variable for later use
	 * $poster = WebcomicTag::webcomic_term_image( medium, '42', 'webcomic42' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $size The size of the image to render.
	 * @param integer $storyline Storyline ID to render the cover for. Will use global term ID by default.
	 * @param string $collection The collection the storyline belongs to.
	 * @uses WebcomicTag::webcomic_term_image()
	 * @uses WebcomicTag::get_webcomic_collection()
	 */
	function webcomic_storyline_cover( $size = 'full', $storyline = 0, $collection = '' ) {
		echo WebcomicTag::webcomic_term_image( $size, $storyline, $collection ? "{$collection}_storyline" : '' );
	}
}

if ( !function_exists( 'webcomic_character_avatar' ) ) {
	/** Render the webcomic poster on a collection archive page.
	 * 
	 * <code>
	 * // render the full size collection image
	 * webcomic_character_avatar();
	 * 
	 * // assign the medium size character avatar for for the character with an ID of 42 in collection 42 to a variable for later use
	 * $poster = WebcomicTag::webcomic_term_image( 'medium', 42, 'webcomic42' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $size The size of the image to render.
	 * @param integer $character Character ID to render the avatar for. Will use global term ID by default.
	 * @param string $collection The collection the character belongs to.
	 * @uses WebcomicTag::webcomic_term_image()
	 * @uses WebcomicTag::get_webcomic_collection()
	 */
	function webcomic_character_avatar( $size = 'full', $character = 0, $collection = '' ) {
		echo WebcomicTag::webcomic_term_image( $size, $character, $collection ? "{$collection}_character" : '' );
	}
}

///
// Single Collection Tags
///

if ( !function_exists( 'webcomic_collection_title' ) ) {
	/** Render the webcomic collection title on a collection archive page.
	 * 
	 * <code>
	 * // render the collection title
	 * webcomic_collection_title();
	 * 
	 * // assign the collection title to a variable for later use
	 * $collection_title = WebcomicTag::webcomic_collection_title( 'Collection: ', false );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $prefix Content to display before the title.
	 * @param string $collection Collection ID to render a title for.
	 */
	function webcomic_collection_title( $prefix = '', $collection = '' ) {
		echo WebcomicTag::webcomic_collection_title( $prefix, $collection );
	}
}

if ( !function_exists( 'webcomic_collection_description' ) ) {
	/** Render the description for a webcomic collection.
	 * 
	 * <code>
	 * // render the description of a webcomic collection on the collection archive page
	 * webcomic_collection_description();
	 * 
	 * // assign the description of webcomic collection 42 to a variable for later use
	 * $description = WebcomicTag::webcomic_collection_description( 'webcomic42' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $collection The collection ID to render a description for.
	 * @uses WebcomicTag::webcomic_collection_description()
	 */
	function webcomic_collection_description( $collection = '' ) {
		echo WebcomicTag::webcomic_collection_description( $collection );
	}
}

if ( !function_exists( 'webcomic_collection_poster' ) ) {
	/** Render the webcomic poster on a collection archive page.
	 * 
	 * <code>
	 * // render the full size collection poster
	 * webcomic_collection_poster();
	 * 
	 * // assign the medium size collection poster for collection 42 to a variable for later use
	 * $poster = WebcomicTag::webcomic_collection_image( 'webcomic42', 'medium' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $size The size of the image to render.
	 * @param string $collection The collection ID to render an image for.
	 * @uses WebcomicTag::webcomic_collection_image()
	 */
	function webcomic_collection_poster( $size = 'full', $collection = '' ) {
		echo WebcomicTag::webcomic_collection_image( $size, $collection );
	}
}

if ( !function_exists( 'webcomic_collection_print_amount' ) ) {
	/** Render a formatted collection print amount.
	 * 
	 * <code>
	 * // render the current collection's print amount for a domestic print
	 * webcomic_collection_print_amount( 'domestic' );
	 * 
	 * // render the original-print shipping amount for collection 42 using ',' for the decimal and '.' for the thousands separator
	 * webcomic_collection_print_amount( 'original-shipping', ',', '.', 'webcomic42' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $type The amount to render, one of 'domestic', 'domestic-price', 'domestic-shipping', 'international', 'international-price', 'international-shipping', 'original', 'original-price', or 'original-shipping'.
	 * @param string $dec Decimal point for number_format().
	 * @param string $sep Thousands separator for number_format().
	 * @param string $collection Collection ID. Will use Webcomic::$collection by default.
	 * @uses WebcomicTag::webcomic_collection_print_amount()
	 */
	function webcomic_collection_print_amount( $type, $dec = '.', $sep = ',', $collection = '' ) {
		return WebcomicTag::webcomic_collection_print_amount( $type, $dec, $sep, $collection );
	}
}

///
// Commerce Tags
///

if ( !function_exists( 'webcomic_donation_amount' ) ) {
	/** Render a donation amount.
	 * 
	 * <code>
	 * // render the current collections donation amount
	 * webcomic_donation_amount();
	 * 
	 * // render the donation amount for collection 42 using ',' for the decimal and '.' for the thousands separator
	 * webcomic_donation_amount( ',', '.', 'webcomic42' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $dec Decimal point for number_format().
	 * @param string $sep Thousands separator for number_format().
	 * @param string $collection The collection to render a donation amount for.
	 * @uses WebcomicTag::webcomic_donation_amount()
	 */
	function webcomic_donation_amount( $dec = '.', $sep = ',', $collection = '' ) {
		echo WebcomicTag::webcomic_donation_amount( $dec, $sep, $collection );
	}
}

if ( !function_exists( 'webcomic_donation_fields' ) ) {
	/** Render hidden donation form fields.
	 * 
	 * <code>
	 * // render hidden donation fields for the current collection
	 * webcomic_donation_fields();
	 * 
	 * // render hidden donation fields for the collection 42
	 * webcomic_donation_fields( 'webcomic42' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $collection The collection to render donation fields for.
	 * @uses WebcomicTag::webcomic_donation_fields()
	 */
	function webcomic_donation_fields( $collection = '' ) {
		echo WebcomicTag::webcomic_donation_fields( $collection );
	}
}

if ( !function_exists( 'webcomic_donation_form' ) ) {
	/** Render a donation form.
	 * 
	 * <code>
	 * // render a donation form for the current collection
	 * webcomic_donation_form();
	 * 
	 * // render a donation form for collection 42 with a custom label
	 * webcomic_donation_form( 'Support This Webcomic', 'webcomic42' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $label The form submit button label.
	 * @param string $collection The collection to render a donation form for.
	 * @uses WebcomicTag::webcomic_donation_form()
	 */
	function webcomic_donation_form( $label = '', $collection = '' ) {
		echo WebcomicTag::webcomic_donation_form( $label, $collection );
	}
}

if ( !function_exists( 'webcomic_print_amount' ) ) {
	/** Render a formatted webcomic print amount.
	 * 
	 * <code>
	 * // render the current webcomic's print amount for a domestic print
	 * webcomic_print_amount( 'domestic' );
	 * 
	 * // render the original-print shipping amount for webcomic 42 using ',' for the decimal and '.' for the thousands separator
	 * webcomic_print_amount( 'original-shipping', ',', '.', 42 );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $type The amount to return, one of 'domestic', 'domestic-price', 'domestic-shipping', 'international', 'international-price', 'international-shipping', 'original', 'original-price', or 'original-shipping'.
	 * @param string $dec Decimal point for number_format().
	 * @param string $sep Thousands separator for number_format().
	 * @param mixed $the_post The post object or ID to get print amounts for.
	 * @uses WebcomicTag::webcomic_print_amount()
	 */
	function webcomic_print_amount( $type, $dec = '.', $sep = ',', $the_post = false ) {
		echo WebcomicTag::webcomic_print_amount( $type, $dec, $sep, $the_post );
	}
}

if ( !function_exists( 'webcomic_print_adjustment' ) ) {
	/** Render a formatted webcomic print adjustment.
	 * 
	 * <code>
	 * // render the current webcomic's adjustment for a domestic print
	 * webcomic_print_amount( 'domestic' );
	 * 
	 * // render the original-print shipping adjustment for webcomic 42
	 * webcomic_print_amount( 'original-shipping', 42 );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $type The adjustment to return, one of 'domestic', 'domestic-price', 'domestic-shipping', 'international', 'international-price', 'international-shipping', 'original', 'original-price', or 'original-shipping'.
	 * @param mixed $the_post The post object or ID to get print adjustments for.
	 * @uses WebcomicTag::webcomic_print_adjustment()
	 */
	function webcomic_print_adjustment( $type, $the_post = false ) {
		echo WebcomicTag::webcomic_print_adjustment( $type, $the_post );
	}
}

if ( !function_exists( 'webcomic_print_fields' ) ) {
	/** Return hidden print form fields.
	 * 
	 * <code>
	 * // render hidden domestic print form fields for the current webcomic
	 * webcomic_print_fields( 'domestic' );
	 * 
	 * // render hidden original print form fields for the current webcomic
	 * webcomic_print_fields( 'original' );
	 * 
	 * // render hidden shopping cart form fields
	 * webcomic_print_fields( 'cart' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $type The type of form fields to return, one of 'domestic', 'international', 'original', or 'cart'.
	 * @param mixed $the_post The post object or ID to get print adjustments for.
	 * @uses WebcomicTag::webcomic_print_fields()
	 */
	function webcomic_print_fields( $type, $the_post = false ) {
		echo WebcomicTag::webcomic_print_fields( $type, $the_post );
	}
}

if ( !function_exists( 'webcomic_print_form' ) ) {
	/** Render a print purchase form.
	 * 
	 * <code>
	 * // render a purchase domestic webcomic print form
	 * webcomic_print_form( 'domestic' );
	 * 
	 * // render a purchase international webcomic print form for webcomic 42
	 * webcomic_print_form( 'international', '', 42 );
	 * 
	 * // render a shopping cart form with a custom label
	 * webcomic_print_form( 'cart', 'View Your Cart' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $type The type of print form, one of 'domestic', 'international', 'original', or 'cart'.
	 * @param string $label The form submit button label. Accepts %dec, %sep, %total, %price, and %shipping tokens.
	 * @param mixed $the_post The post object or ID to get print adjustments for.
	 * @uses WebcomicTag::webcomic_print_form()
	 */
	function webcomic_print_form( $type, $label = '', $the_post = false ) {
		echo WebcomicTag::webcomic_print_form( $type, $label, $the_post );
	}
}

///
// Transcript Tags
///

if ( !function_exists( 'webcomic_transcripts_template' ) ) {
	/** Load the transcripts template file.
	 * 
	 * <code>
	 * // load the standard Webcomic transcripts template
	 * webcomic_transcripts_template();
	 * 
	 * // load a custom Webcomic transcripts template file
	 * webcomic_transcripts_template( 'webcomic/custom/transcripts.php' );
	 * </code>
	 * 
	 * @param string $template The template file to load.
	 * @package Webcomic
	 * @uses WebcomicTag::webcomic_transcripts_template()
	 */
	function webcomic_transcripts_template( $template = '' ) {
		WebcomicTag::webcomic_transcripts_template();
	}
}

if ( !function_exists( 'webcomic_transcripts_link' ) ) {
	/** Render a webcomic transcripts link.
	 * 
	 * <code>
	 * // render a transcripts link
	 * webcomic_transcripts_link();
	 * 
	 * // render a transcripts link for the language with a slug of 'en'
	 * webcomic_transcripts_link( '%link', '', '', '', 'en' );
	 * 
	 * // render a transcript lik for webcomic 42 with custom link text
	 * webcomic_transcripts_link( '%link', 'Transcribe Me!', 'Read Transcripts', 'Transcription Disabled', false, 42 );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $format Format string for the link. Should include the %link token, which will be replaced by the actual link.
	 * @param string $none Format string for the link text when no transcripts have been published. Accepts %title and image size tokens.
	 * @param string $some Format string for the link text when one or more transcripts have been published. Accepts %title and image size tokens.
	 * @param string $off Format string for the link text when transcription has been disabled. Accepts %title and image size tokens.
	 * @param mixed $language The term object or ID that transcripts should be limited to.
	 * @param mixed $the_post The post object or ID to get a transcript link to.
	 * @uses WebcomicTag::webcomic_transcripts_link()
	 */
	function webcomic_transcripts_link( $format = '%link', $none = '', $some = '', $off = '', $language = false, $the_post = false ) {
		echo WebcomicTag::webcomic_transcripts_link( $format, $none, $some, $off, $language, $the_post );
	}
}

if ( !function_exists( 'get_webcomic_transcripts' ) ) {
	/** Return an array of post objects.
	 * 
	 * <code>
	 * // render published transcripts
	 * if ( $transcripts = get_webcomic_transcripts() ) {
	 * 	foreach ( $transcripts as $post ) { setup_postdata( $post );
	 * 		the_content();
	 * 		?>
	 * 		<footer><?php the_webcomic_transcript_authors(); ?></footer>
	 * 		<?php
	 * 	}
	 * 	
	 * 	wp_reset_postdata();
	 * }
	 * 
	 * // render transcript forms to update transcripts marked as 'pending review' for the current post
	 * if ( $transcripts = get_webcomic_transcripts( true ) ) {
	 * 	foreach ( $transcripts as $transcript ) {
	 * 		webcomic_transcript_form( array(), $transcript );
	 * 	}
	 * }
	 * </code>
	 * 
	 * @package Webcomic
	 * @param boolean $pending Whether to retrieve transcripts pending review.
	 * @param array $args An array of arguments that will be passed to get_children().
	 * @param mixed $the_post The post object or ID to retrieve transcripts for.
	 * @return array
	 * @uses WebcomicTag::get_webcomic_transcripts()
	 */
	function get_webcomic_transcripts( $pending = false, $args = array(), $the_post = false ) {
		return WebcomicTag::get_webcomic_transcripts( $pending, $args, $the_post );
	}
}

if ( !function_exists( 'the_webcomic_transcript_authors' ) ) {
	/** Render a formatted list of webcomic transcript authors.
	 * 
	 * The WordPress-registered post author is not included in the
	 * returned list. Standard WordPress functions like the_author()
	 * should be used to display this information.
	 * 
	 * <code>
	 * // render all authors related to the current transcript
	 * the_webcomic_transcript_authors()
	 * 
	 * // render all authors except the WordPress registered author
	 * the_webcomic_transcript_authors( false );
	 * 
	 * // render an ordered list of transcript authors
	 * the_webcomic_transcript_authors( true, '<ul class="webcomic-transcript-authors"><li>', '</li><li>', '</li></ul>' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param boolean $post_author Whether to include the WordPress-recognized post author in the list.
	 * @param string $before Before list.
	 * @param string $sep Separate items using this.
	 * @param string $after After list.
	 * @uses WebcomicTag::get_webcomic_transcript_authors()
	 */
	function the_webcomic_transcript_authors( $post_author = true, $before = '', $sep = ', ', $after = '' ) {
		echo WebcomicTag::get_webcomic_transcript_authors( 0, $post_author, $before, $sep, $after );
	}
}

if ( !function_exists( 'the_webcomic_transcript_languages' ) ) {
	/** Render a formatted list of languages related to the current webcomic transcript.
	 * 
	 * <code>
	 * // render any languages associated with the current transcript
	 * the_webcomic_transcript_languages();
	 * 
	 * // render a list of languages associated with the current transcript
	 * the_webcomic_transcript_languages( '<ul class="webcomic-transcript-languages"><li>', '</li><li>', '</li></ul>' );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param string $before Before list.
	 * @param string $sep Separate items using this.
	 * @param string $after After list.
	 * @uses WebcomicTag::get_the_webcomic_transcript_term_list()
	 */
	function the_webcomic_transcript_languages( $before = '', $sep = ', ', $after = '' ) {
		echo WebcomicTag::get_the_webcomic_transcript_term_list( 0, 'webcomic_language', $before, $sep, $after );
	}
}

if ( !function_exists( 'webcomic_transcript_fields' ) ) {
	/** Render hidden transcript form fields.
	 * 
	 * <code>
	 * // render hidden transcript form fields
	 * webcomic_transcript_fields();
	 * 
	 * // render hidden transcript form fields to update the transcript with an ID of 42
	 * webcomic_transcript_fields( 42 );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param mixed $transcript The post object or ID to update on submission.
	 * @param mixed $the_post The post object or ID to submit a transcript for.
	 * @uses WebcomicTag::webcomic_transcript_fields()
	 */
	function webcomic_transcript_fields( $transcript = false, $the_post = false ) {
		echo WebcomicTag::webcomic_transcript_fields( $transcript, $the_post );
	}
}

if ( !function_exists( 'webcomic_transcript_form' ) ) {
	/** Render a complete transcription form for templates.
	 * 
	 * ### Arguments
	 * 
	 * - `array` **$fields** - An array of fields for unregistered users to fill out. Each array element should have a descriptive key and the full HTML output for the value.
	 * - `string` **$language_field** - HTML output for the transcript language field.
	 * - `string` **$transcript_field** - HTML output for the transcript content field. Used when $wysiwyg_editor is `false`.
	 * - `string` **$must_log_in** - Error text to display when users must be logged in to transcribe.
	 * - `string` **$logged_in_as** - Text to display when a user is already logged in.
	 * - `string` **$transcript_notes_before** - Transcription notes displayed to unregistered users before the `$fields` are output.
	 * - `string` **$transcript_notes_after** - Transcription notes displayed at the bottom of the form before the submit button.
	 * - `string` **$transcript_notes_success** - Text displayed after a transcript has been successfully submitted.
	 * - `string` **$transcript_notes_failure** - Text displayed if an error occurs during transcript submission.
	 * - `string` **$id_form** - ID to use for the `<form>` element.
	 * - `string` **$title_submit** - Title text to display for the transcript submission form.
	 * - `string` **$label_submit** - Text to display for the submit button.
	 * - `mixed` **$wysiwyg_editor** - Whether to display a WYSIWYG transcript editor. May pass an array of arguments for `wp_editor()`.
	 * 
	 * <code>
	 * // render the standard transcript form
	 * webcomic_transcript_form();
	 * 
	 * // render a WYSIWYG transcript form
	 * webcomic_transcript_form( array( 'wysiwyg_editor' => true ) );
	 * 
	 * // render a WYSIWYG transcript editor with a custom title to update the transcript with an ID of 42
	 * webcomic_transcript_form( array( 'wysiwyg_editor' => true, 'title_submit' => 'Improve this Transcript' ), 42 );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param array $args Options for strings, fields etc in the form.
	 * @param mixed $transcript The transcript to update on submission.
	 * @param mixed $the_post The post object or ID to generate the form for.
	 * @uses WebcomicTag::webcomic_transcript_form()
	 */
	function webcomic_transcript_form( $args = array(), $transcript = false, $the_post = false ) {
		WebcomicTag::webcomic_transcript_form( $args, $transcript, $the_post );
	}
}

if ( !function_exists( 'webcomic_dropdown_transcript_languages' ) ) {
	/** Render a `<select>` element of webcomic transcript terms.
	 * 
	 * Because this function relies on get_terms() to retrieve the term
	 * list the $args parameter accepts any arguments that get_terms()
	 * may accept. Only those get_terms() arguments that differ from
	 * their defaults are detailed here.
	 * 
	 * ### Arguments
	 * 
	 * - `string` **$name** - Value for the name attribute of the `<select>` element.
	 * - `string` **$id** - Value of the id attribute of the `<select>` element.
	 * - `mixed` **$class** - String or array of additional classes for the `<select>` element.
	 * - `string` **$before** - Content to display before the output.
	 * - `string` **$after** - Content to display after the output.
	 * - `string` **$show_option_all** - String to display for an "all" `<option>` (value="0").
	 * - `string` **$show_option_none** - String to display for a "none" `<option>` (value="-1").
	 * - `boolean` **$hierarchical** - Whether to indent child terms.
	 * - `boolean` **$hide_empty** - Whether to hide empty terms. Defaults to the opposite of WebcomicTag::webcomic_transcripts_open().
	 * - `boolean` **$hide_if_empty** - Whether to display the `<select>` even if it contains no `<option>'s`.
	 * - `string` **$orderby** - What field to sort terms by.
	 * - `object` **$walker** - Custom walker object. Defaults Walker_WebcomicTerm_Dropdown.
	 * - `integer` **$depth** - How deep the walker should run. Defaults to 0 (all levels). A -1 depth will result in flat output.
	 * - `integer` **$selected** - The ID of the selected term.
	 * 
	 * <code>
	 * // render a dropdown of available transcript languages
	 * webcomic_dropdown_transcript_languages();
	 * 
	 * // render a dropdown with an "all languages" option
	 * webcomic_dropdown_transcript_languages( array( 'show_option_all' => '- Languages -' ) );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param array $args Array of arguments. See function description for detailed information.
	 * @param mixed $the_post The post object or ID transcripts should be related to.
	 * @return string
	 * @uses webcomic_transcripts_open()
	 * @uses WebcomicTag::webcomic_dropdown_transcript_terms()
	 */
	function webcomic_dropdown_transcript_languages( $args = array(), $the_post = false ) {
		$r = wp_parse_args( $args, array(
			'name'             => 'webcomic_terms',
			'id'               => '',
			'class'            => '',
			'before'           => '',
			'after'            => '',
			'show_option_all'  => '',
			'show_option_none' => '',
			'hierarchical'     => true,
			'hide_empty'       => !webcomic_transcripts_open( $the_post ),
			'hide_if_empty'    => true,
			'orderby'          => 'name',
			'walker'           => false,
			'depth'            => 0,
			'selected'         => 0
		) );
		
		$r[ 'taxonomy' ] = 'webcomic_language';
		
		echo WebcomicTag::webcomic_dropdown_transcript_terms( $r, $the_post );
	}
}

if ( !function_exists( 'webcomic_list_transcript_languages' ) ) {
	/** Return a list of webcomic transcript terms.
	 * 
	 * Because this function relies on get_terms() to retrieve the term
	 * list the $args parameter accepts any arguments that get_terms()
	 * may accept. Only those get_terms() arguments that differ from
	 * their defaults are detailed here.
	 * 
	 * ### Arguments
	 * 
	 * - `string` **$id** - Value of the id attribute of the list element.
	 * - `mixed` **$class** - String or array of additional classes for the list element.
	 * - `string` **$before** - Content to display before the output.
	 * - `string` **$after** - Content to display after the output.
	 * - `boolean` **$ordered** - Use `<ol>` instead of `<ul>`.
	 * - `boolean` **$hierarchical** - Whether to indent child terms.
	 * - `boolean` **$hide_empty** - Whether to hide empty terms. Defaults to the opposite of WebcomicTag::webcomic_transcripts_open().
	 * - `string` **$orderby** - What field to sort terms by.
	 * - `object` **$walker** - Custom walker object. Defaults Walker_WebcomicTranscriptTerm_List.
	 * - `integer` **$depth** - How deep the walker should run. Defaults to 0 (all levels). A -1 depth will result in flat output.
	 * - `integer` **$selected** - The ID of the selected term.
	 * 
	 * <code>
	 * // render a list of available transcript languages
	 * webcomic_list_transcript_languages();
	 * 
	 * // render an ordered list of available transcript languages
	 * webcomic_list_transcript_languages( array( 'ordered' => true ) );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param array $args Array of arguments. See function description for detailed information.
	 * @param mixed $the_post The post object or ID transcripts should be related to.
	 * @uses webcomic_transcripts_open()
	 * @uses WebcomicTag::webcomic_list_transcript_terms()
	 */
	function webcomic_list_transcript_languages( $args = array(), $the_post = false ) {
		$r = wp_parse_args( $args, array(
			'id'               => '',
			'class'            => '',
			'before'           => '',
			'after'            => '',
			'ordered'          => '',
			'hierarchical'     => true,
			'hide_empty'       => !webcomic_transcripts_open(),
			'orderby'          => 'name',
			'walker'           => false,
			'depth'            => 0,
			'selected'         => 0
		) );
		
		$r[ 'taxonomy' ] = 'webcomic_language';
	}
}

///
// Archive Tags
///

if ( !function_exists( 'webcomic_dropdown_storylines' ) ) {
	/** Render a `<select>` element for webcomic storylines.
	 * 
	 * ### Arguments
	 * 
	 * - `string` **$name** - Value for the name attribute of the `<select>` element.
	 * - `string` **$id** - Value of the id attribute of the `<select>` element.
	 * - `mixed` **$class** - String or array of additional classes for the `<select>` element.
	 * - `string` **$show_option_all** - String to display for an "all" `<option>` (value="0").
	 * - `string` **$show_option_none** - String to display for a "none" `<option>` (value="-1").
	 * - `boolean` **$hierarchical** - Whether to indent child storylines.
	 * - `boolean` **$hide_if_empty** - Whether to display the `<select>` even if it contains no `<option>'s`.
	 * - `string` **$collection** - The collection storylines must belong to.
	 * - `string` **$orderby** - What field to sort storylines by. Defaults to 'term_group'.
	 * - `object` **$walker** - Custom walker object. Defaults Walker_WebcomicTerm_Dropdown.
	 * - `integer` **$depth** - How deep the walker should run. Defaults to 0 (all levels). A -1 depth will result in flat output.
	 * - `boolean` **$webcomics** - Whether to display a dropdown of webcomic posts grouped by storyline. The 'hide_empty' argument is ignored when $webcomics is true.
	 * - `boolean` **$show_count** - Whether to display the total number of webcomics in a storyline.
	 * - `string` **$target** - The target url for storylines, one of 'archive', 'first', 'last', or 'random'. Defaults to 'archive'.
	 * - `integer` **$selected** - The ID of the selected term or webcomic.
	 * 
	 * <code>
	 * // render a dropdown of storylines with at least one webcomic in the current collection
	 * webcomic_dropdown_storylines();
	 * 
	 * // render a dropdown of all storylines in collection 42 linked to the beginning of each storyline with a default option
	 * webcomic_dropdown_storylines( array( 'collection' => 'webcomic42', 'hide_empty' => false, 'target' => 'first', 'show_option_all' => '- Storylines -' ) );
	 * 
	 * // render a dropdown of published webcomics grouped by storyline in collection 42
	 * webcomic_dropdown_storylines( array( 'collection' => 'webcomic42', 'show_option_all' => '- Comics by Storyline -', 'webcomics' => true ) );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param array $args Array of arguments. See the WebcomicTag::webcomic_dropdown_terms() function description for detailed information.
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::webcomic_dropdown_terms()
	 */
	function webcomic_dropdown_storylines( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'name'             => 'webcomic_terms',
			'id'               => '',
			'class'            => '',
			'show_option_all'  => '',
			'show_option_none' => '',
			'hierarchical'     => true,
			'hide_if_empty'    => true,
			'collection'       => '',
			'orderby'         => 'term_group',
			'walker'           => false,
			'depth'            => 0,
			'webcomics'        => false,
			'show_count'       => false,
			'target'           => 'archive',
			'selected'         => 0
		) );
		
		$collection = $r[ 'collection' ] ? $r[ 'collection' ] : WebcomicTag::get_webcomic_collection();
		
		if ( taxonomy_exists( "{$collection}_storyline" ) ) {
			$r[ 'taxonomy' ] = "{$collection}_storyline";
			
			echo WebcomicTag::webcomic_dropdown_terms( $r );
		}
	}
}

if ( !function_exists( 'webcomic_dropdown_characters' ) ) {
	/** Render a `<select>` element for webcomic characters.
	 * 
	 * ### Arguments
	 * 
	 * - `string` **$name** - Value for the name attribute of the `<select>` element.
	 * - `string` **$id** - Value of the id attribute of the `<select>` element.
	 * - `mixed` **$class** - String or array of additional classes for the `<select>` element.
	 * - `string` **$show_option_all** - String to display for an "all" `<option>` (value="0").
	 * - `string` **$show_option_none** - String to display for a "none" `<option>` (value="-1").
	 * - `boolean` **$hide_if_empty** - Whether to display the `<select>` even if it contains no `<option>'s`.
	 * - `string` **$collection** - The collection characters must belong to.
	 * - `string` **$orderby** - What field to sort characters by. Defaults to 'name'.
	 * - `object` **$walker** - Custom walker object. Defaults Walker_WebcomicTerm_Dropdown.
	 * - `integer` **$depth** - How deep the walker should run. Defaults to 0 (all levels). A -1 depth will result in flat output.
	 * - `boolean` **$webcomics** - Whether to display a dropdown of webcomic posts grouped by character. The 'hide_empty' argument is ignored when $webcomics is true.
	 * - `boolean` **$show_count** - Whether to display the total number of webcomics featuring a character.
	 * - `string` **$target** - The target url for characters, one of 'archive', 'first', 'last', or 'random'. Defaults to 'archive'.
	 * - `integer` **$selected** - The ID of the selected character or webcomic.
	 * 
	 * <code>
	 * // render a dropdown of characters featured in at least one webcomic of the current collection
	 * webcomic_dropdown_characters();
	 * 
	 * // render a dropdown of all characters in collection 42 linked to the beginning of each character with a default option
	 * webcomic_dropdown_characters( array( 'collection' => 'webcomic42', 'hide_empty' => false, 'target' => 'first', 'show_option_all' => '- Characters -' ) );
	 * 
	 * // render a dropdown of published webcomics grouped by character in collection 42
	 * webcomic_dropdown_characters( array( 'collection' => 'webcomic42', 'show_option_all' => '- Comics by Character -', 'webcomics' => true ) );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param array $args Array of arguments. See the WebcomicTag::webcomic_dropdown_terms() function description for detailed information.
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::webcomic_dropdown_terms()
	 */
	function webcomic_dropdown_characters( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'name'             => 'webcomic_terms',
			'id'               => '',
			'class'            => '',
			'show_option_all'  => '',
			'show_option_none' => '',
			'hierarchical'     => true,
			'hide_if_empty'    => true,
			'collection'       => '',
			'orderby'         => 'name',
			'walker'           => false,
			'depth'            => 0,
			'webcomics'        => false,
			'show_count'       => false,
			'target'           => 'archive',
			'selected'         => 0
		) );
		
		$collection = $r[ 'collection' ] ? $r[ 'collection' ] : WebcomicTag::get_webcomic_collection();
		
		if ( taxonomy_exists( "{$collection}_character" ) ) {
			$r[ 'taxonomy' ] = "{$collection}_character";
			
			echo WebcomicTag::webcomic_dropdown_terms( $r );
		}
	}
}

if ( !function_exists( 'webcomic_dropdown_collections' ) ) {
	/** Render a `<select>` element for webcomic collections.
	 * 
	 * ### Arguments
	 * 
	 * - `string` **$name** - Value for the name attribute of the `<select>` element.
	 * - `string` **$id** - Value of the id attribute of the `<select>` element.
	 * - `mixed` **$class** - String or array of additional classes for the `<select>` element.
	 * - `string` **$show_option_all** - String to display for an "all" `<option>` (value="0").
	 * - `string` **$show_option_none** - String to display for a "none" `<option>` (value="-1").
	 * - `boolean` **$hide_empty** - Whether to hide collections with no readable posts. Defaults to true.
	 * - `boolean` **$hide_if_empty** - Whether to display the `<select>` even if it contains no `<option>'s`.
	 * - `string` **$collection** - Limits output to a single collection. Useful in combination with $webcomics.
	 * - `string` **$orderby** - What to sort the collections by. May be one of 'name', 'slug', 'count', or 'updated'. Defaults to collection ID.
	 * - `string` **$callback** - Custom callback function for generating `<option>'s`. Callback functions should accept three arguments: the collection configuration array, the function arguments array, and the posts array (if any).
	 * - `boolean` **$webcomics** - Whether to display a dropdown of webcomic posts grouped by collection. The 'hide_empty' argument is ignored when $webcomics is true.
	 * - `boolean` **$show_count** - Whether to display the total number of published webcomics in a collection.
	 * - `string` **$target** - The target url for collections, one of 'archive', 'first', 'last', or 'random'. Defaults to 'archive'.
	 * - `string` **$selected** - The ID of the selected collection or webcomic.
	 * 
	 * <code>
	 * // render a dropdown of all webcomic collections with at least one post
	 * webcomic_dropdown_collections();
	 * 
	 * // render a dropdown of all webcomic collections linked to the beginning of each collection with a default option
	 * webcomic_dropdown_collections( array( 'hide_empty' => false, 'target' => 'first', 'show_option_all' => '- Collections -' ) );
	 * 
	 * // render a dropdown of published webcomics grouped by collection only for collection 42
	 * webcomic_dropdown_collections( array( 'collection' => 'webcomic42', 'webcomics' => true ) );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param array $args Array of arguments. See function description for detailed information.
	 * @uses WebcomicTag::webcomic_dropdown_collections()
	 */
	function webcomic_dropdown_collections( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'name'             => 'webcomic_collections',
			'id'               => '',
			'class'            => '',
			'show_option_all'  => '',
			'show_option_none' => '',
			'hide_empty'       => true,
			'hide_if_empty'    => true,
			'collection'       => '',
			'orderby'          => '',
			'callback'         => '',
			'webcomics'        => false,
			'show_count'       => false,
			'target'           => 'archive',
			'selected'         => ''
		) );
		
		echo WebcomicTag::webcomic_dropdown_collections( $r );
	}
}

if ( !function_exists( 'webcomic_list_storylines' ) ) {
	/** Render a list of webcomic storylines.
	 * 
	 * ### Arguments
	 * 
	 * - `string` **$id** - Value of the id attribute of the `<select>` element.
	 * - `mixed` **$class** - String or array of additional classes for the `<select>` element.
	 * - `string` **$before** - Content to display before the output.
	 * - `string` **$after** - Content to display after the output.
	 * - `string` **$sep** - Separator to use between links. An empty value generates an unordered list. Defaults to "\n".
	 * - `boolean` **$hierarchical** - Whether to indent child storylines.
	 * - `string` **$collection** - The collection storylines must belong to.
	 * - `string` **$orderby** - What field to sort storylines by. Defaults to 'term_group'.
	 * - `object` **$walker** - Custom walker object. Defaults Walker_WebcomicTerm_List.
	 * - `string` **$feed** - Text or image URL to use for a storyline feed link.
	 * - `string` **$feed_type** - The type of feed to link to.
	 * - `integer` **$depth** - How deep the walker should run. Defaults to 0 (all levels). A -1 depth will result in flat output.
	 * - `boolean` **$webcomics** - Whether to display a list of webcomic posts grouped by storyline. The 'hide_empty' argument is ignored when $webcomics is true.
	 * - `string` **$webcomic_image** - Size of the webcomic image to use for webcomic links.
	 * - `boolean` **$show_count** - Whether to display the total number of webcomics in a storyline.
	 * - `boolean` **$show_description** - Whether to display storyline descriptions.
	 * - `boolean` **$show_image** - Size of the storyline cover to use for storyline links.
	 * - `string` **$target** - The target url for storylines, one of 'archive', 'first', 'last', or 'random'. Defaults to 'archive'.
	 * - `integer` **$selected** - The ID of the selected storyline or webcomic.
	 * 
	 * <code>
	 * // render a list of storylines with at least one webcomic in the current collection
	 * webcomic_list_storylines();
	 * 
	 * // render an ordered list of all storylines in collection 42 linked to the beginning of each storyline
	 * webcomic_list_storylines( array( 'collection' => 'webcomic42', 'hide_empty' => false, 'target' => 'first', 'ordered' => true ) );
	 * 
	 * // render a list of published webcomic thumbnails grouped by storyline in collection 42 with storyline descriptions
	 * webcomic_list_storylines( array( 'collection' => 'webcomic42', 'show_description' => true, 'webcomics' => true, 'webcomic_image' => 'thumbnail' ) );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param array $args Array of arguments. See function description for detailed information.
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::webcomic_list_terms()
	 */
	function webcomic_list_storylines( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'id'               => '',
			'class'            => '',
			'before'           => '',
			'after'            => '',
			'sep'              => '',
			'ordered'          => '',
			'hierarchical'     => true,
			'collection'       => '',
			'orderby'          => '',
			'walker'           => false,
			'feed'             => '',
			'feed_type'        => 'rss2',
			'depth'            => 0,
			'webcomics'        => false,
			'webcomic_image'   => '',
			'show_count'       => false,
			'show_description' => false,
			'show_image'       => '',
			'target'           => 'archive',
			'selected'         => 0
		) );
		
		$collection = $r[ 'collection' ] ? $r[ 'collection' ] : WebcomicTag::get_webcomic_collection();
		
		if ( taxonomy_exists( "{$collection}_storyline" ) ) {
			$r[ 'taxonomy' ] = "{$collection}_storyline";
			
			echo WebcomicTag::webcomic_list_terms( $r );
		}
	}
}

if ( !function_exists( 'webcomic_list_characters' ) ) {
	/** Render a list of webcomic characters.
	 * 
	 * ### Arguments
	 * 
	 * - `string` **$id** - Value of the id attribute of the `<select>` element.
	 * - `mixed` **$class** - String or array of additional classes for the `<select>` element.
	 * - `string` **$before** - Content to display before the output.
	 * - `string` **$after** - Content to display after the output.
	 * - `string` **$sep** - Separator to use between links. An empty value generates an unordered list. Defaults to "\n".
	 * - `boolean` **$hierarchical** - Whether to indent child characters.
	 * - `string` **$collection** - The collection characters must belong to.
	 * - `string` **$orderby** - What field to sort characters by. Defaults to 'term_group'.
	 * - `object` **$walker** - Custom walker object. Defaults Walker_WebcomicTerm_List.
	 * - `string` **$feed** - Text or image URL to use for a character feed link.
	 * - `string` **$feed_type** - The type of feed to link to.
	 * - `integer` **$depth** - How deep the walker should run. Defaults to 0 (all levels). A -1 depth will result in flat output.
	 * - `boolean` **$webcomics** - Whether to display a list of webcomic posts grouped by character. The 'hide_empty' argument is ignored when $webcomics is true.
	 * - `string` **$webcomic_image** - Size of the webcomic image to use for webcomic links.
	 * - `boolean` **$show_count** - Whether to display the total number of webcomics featuring a character.
	 * - `boolean` **$show_description** - Whether to display character descriptions.
	 * - `boolean` **$show_image** - Size of the character cover to use for character links.
	 * - `string` **$target** - The target url for characters, one of 'archive', 'first', 'last', or 'random'. Defaults to 'archive'.
	 * - `integer` **$selected** - The ID of the selected character or webcomic.
	 * 
	 * <code>
	 * // render a list of characters with at least one webcomic in the current collection
	 * webcomic_list_characters();
	 * 
	 * // render an ordered list of all characters in collection 42 linked to the beginning of each character
	 * webcomic_list_characters( array( 'collection' => 'webcomic42', 'hide_empty' => false, 'target' => 'first', 'ordered' => true ) );
	 * 
	 * // render a list of published webcomic thumbnails grouped by character in collection 42 with character descriptions
	 * webcomic_list_characters( array( 'collection' => 'webcomic42', 'show_description' => true, 'webcomics' => true, 'webcomic_image' => 'thumbnail' ) );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param array $args Array of arguments. See function description for detailed information.
	 * @uses WebcomicTag::get_webcomic_collection()
	 * @uses WebcomicTag::webcomic_list_terms()
	 */
	function webcomic_list_characters( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'id'               => '',
			'class'            => '',
			'before'           => '',
			'after'            => '',
			'sep'              => '',
			'ordered'          => '',
			'hierarchical'     => true,
			'collection'       => '',
			'orderby'          => '',
			'walker'           => false,
			'feed'             => '',
			'feed_type'        => 'rss2',
			'depth'            => 0,
			'webcomics'        => false,
			'webcomic_image'   => '',
			'show_count'       => false,
			'show_description' => false,
			'show_image'       => '',
			'target'           => 'archive',
			'selected'         => 0
		) );
		
		$collection = $r[ 'collection' ] ? $r[ 'collection' ] : WebcomicTag::get_webcomic_collection();
		
		if ( taxonomy_exists( "{$collection}_character" ) ) {
			$r[ 'taxonomy' ] = "{$collection}_character";
			
			echo WebcomicTag::webcomic_list_terms( $r );
		}
	}
}

if ( !function_exists( 'webcomic_list_collections' ) ) {
	/** Return a list of webcomic collections.
	 * 
	 * ### Arguments
	 * 
	 * - `string` **$id** - Value of the id attribute of the list element.
	 * - `mixed` **$class** - String or array of additional classes for the list element.
	 * - `string` **$before** - Content to display before the output.
	 * - `string` **$after** - Content to display after the output.
	 * - `boolean` **$hide_empty** - Whether to hide collections with no readable posts. Defaults to true.
	 * - `boolean` **$ordered** - Use `<ol>` instead of `<ul>`.
	 * - `string` **$collection** - Limits output to a single collection. Useful in combination with $webcomics.
	 * - `string` **$order** - How to order collections, one of 'ASC' (default) or 'DESC'.
	 * - `string` **$orderby** - What to sort the collections by. May be one of 'name', 'slug', 'count', or 'updated'. Defaults to collection ID.
	 * - `string` **$callback** - Custom callback function for generating list items. Callback functions should accept three arguments: the collection configuration array, the function arguments array, and the posts array (if any).
	 * - `string` **$feed** - Text or image URL to use for a collection feed link.
	 * - `string` **$feed_type** - The type of feed to link to.
	 * - `boolean` **$webcomics** - Whether to display a list of webcomic posts grouped by collection. The 'hide_empty' argument is ignored when $webcomics is true.
	 * - `string` **$webcomic_order** - How to order webcomics, one of 'ASC' or 'DESC'. Defaults to 'ASC'.
	 * - `string` **$webcomic_orderby** - What field to order webcomics by. Defaults to 'date'. See WP_Query for details.
	 * - `string` **$webcomic_image** - Size of the webcomic image to use for webcomic links.
	 * - `boolean` **$show_count** - Whether to display the total number of webcomics in a collection.
	 * - `boolean` **$show_description** - Whether to display collection descriptions.
	 * - `boolean` **$show_image** - Size of the collection image to use for collection links.
	 * - `string` **$target** - The target url for collections, one of 'archive', 'first', 'last', or 'random'. Defaults to 'archive'.
	 * - `integer` **$selected** - The ID of the selected collection or webcomic.
	 * 
	 * <code>
	 * // render a list of all webcomic collections with at least one post
	 * webcomic_list_collections();
	 * 
	 * // render an ordered list of all webcomic collections linked to the beginning of each collection
	 * webcomic_list_collections( array( 'hide_empty' => false, 'target' => 'first', 'ordered' => true ) );
	 * 
	 * // render a list of published webcomic thumbnails grouped by collection only for collection 42 with collection descriptions
	 * webcomic_list_collections( array( 'collection' => 'webcomic42', 'show_description' => true, 'webcomics' => true, 'webcomic_image' => 'thumbnail' ) );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param array $args Array of arguments. See function description for detailed information.
	 * @uses WebcomicTag::webcomic_list_collections()
	 */
	function webcomic_list_collections( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'id'               => '',
			'class'            => '',
			'before'           => '',
			'after'            => '',
			'hide_empty'       => true,
			'ordered'          => '',
			'collection'       => '',
			'order'            => 'ASC',
			'orderby'          => '',
			'callback'         => false,
			'feed'             => '',
			'feed_type'        => 'rss2',
			'webcomics'        => false,
			'webcomic_order'   => 'ASC',
			'webcomic_orderby' => 'date',
			'webcomic_image'   => '',
			'show_count'       => false,
			'show_description' => false,
			'show_image'       => '',
			'target'           => 'archive',
			'selected'         => 0
		) );
		
		echo WebcomicTag::webcomic_list_collections( $r );
	}
}

if ( !function_exists( 'webcomic_storyline_cloud' ) ) {
	/** Return a "cloud" of webcomic storylines.
	 * 
	 * ### Arguments
	 * 
	 * - `string` **$id** - Value of the id attribute of the wrapping element.
	 * - `mixed` **$class** - String or array of additional classes for the wrapping element.
	 * - `integer` **$smallest** - The smallest font size to display links in.
	 * - `integer` **$largest** - The largest font size to display links in.
	 * - `string` **$unit** - The CSS unit to use for $smallest and $largest.
	 * - `string` **$image** - Size of the storyline cover to use for storyline links. Modified by the number of posts in a given storyline and the $smallest and $largest values.
	 * - `string` **$before** - Content to display before the output.
	 * - `string` **$after** - Content to display after the output.
	 * - `string` **$sep** - Separator to use between links. An empty value generates an unordered list. Defaults to "\n".
	 * - `string` **$collection** - The collection storylines must belong to.
	 * - `string` **$order** - How to order storylines. Defaults to 'RAND'.
	 * - `mixed` **$callback** - Callback function to use when building links.
	 * - `boolean` **$show_count** - Whether to display the total number of webcomics in a storyline.
	 * - `string` **$target** - The target url for storylines, one of 'archive', 'first', 'last', or 'random'. Defaults to 'archive'.
	 * - `integer` **$selected** - The ID of the current storyline.
	 * 
	 * <code>
	 * // render a cloud of webcomic storylines
	 * webcomic_storyline_cloud();
	 * 
	 * // render a list cloud of webcomic storylines in collection 42 linked to the beginning of each storyline
	 * webcomic_storyline_cloud( array( 'collection' => 'webcomic42', 'target' => 'first', 'sep' => '' ) );
	 * 
	 * // render a cloud of thumbnail-sized webcomic storyline covers
	 * webcomic_storyline_cloud( array( 'image' => 'thumbnail' ) );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param array $args Array of arguments. See function description for detailed information.
	 * @uses WebcomicTag::webcomic_term_cloud()
	 */
	function webcomic_storyline_cloud( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'id'         => '',
			'class'      => '',
			'smallest'   => 75,
			'largest'    => 150,
			'unit'       => '%',
			'image'      => '',
			'before'     => '',
			'after'      => '',
			'sep'        => "\n",
			'collection' => '',
			'order'      => 'RAND',
			'callback'   => '',
			'show_count' => false,
			'target'     => 'archive',
			'selected'   => 0
		) );
		
		$collection = $r[ 'collection' ] ? $r[ 'collection' ] : WebcomicTag::get_webcomic_collection();
		
		if ( taxonomy_exists( "{$collection}_storyline" ) ) {
			$r[ 'taxonomy' ] = "{$collection}_storyline";
			
			echo WebcomicTag::webcomic_term_cloud( $r );
		}
	}
}

if ( !function_exists( 'webcomic_character_cloud' ) ) {
	/** Return a "cloud" of webcomic characters.
	 * 
	 * ### Arguments
	 * 
	 * - `string` **$id** - Value of the id attribute of the wrapping element.
	 * - `mixed` **$class** - String or array of additional classes for the wrapping element.
	 * - `integer` **$smallest** - The smallest font size to display links in.
	 * - `integer` **$largest** - The largest font size to display links in.
	 * - `string` **$unit** - The CSS unit to use for $smallest and $largest.
	 * - `string` **$image** - Size of the character avatar to use for character links. Modified by the number of posts in a given character and the $smallest and $largest values.
	 * - `string` **$before** - Content to display before the output.
	 * - `string` **$after** - Content to display after the output.
	 * - `string` **$sep** - Separator to use between links. An empty value generates an unordered list. Defaults to "\n".
	 * - `string` **$collection** - The collection characters must belong to.
	 * - `string` **$order** - How to order characters. Defaults to 'RAND'.
	 * - `mixed` **$callback** - Callback function to use when building links.
	 * - `boolean` **$show_count** - Whether to display the total number of webcomics featuring a character.
	 * - `string` **$target** - The target url for characters, one of 'archive', 'first', 'last', or 'random'. Defaults to 'archive'.
	 * - `integer` **$selected** - The ID of the current character.
	 * 
	 * <code>
	 * // render a cloud of webcomic characters
	 * webcomic_character_cloud();
	 * 
	 * // render a list cloud of webcomic characters in collection 42 linked to the beginning of each character
	 * webcomic_character_cloud( array( 'collection' => 'webcomic42', 'target' => 'first', 'sep' => '' ) );
	 * 
	 * // render a cloud of thumbnail-sized webcomic avatars
	 * webcomic_character_cloud( array( 'image' => 'thumbnail' ) );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param array $args Array of arguments. See function description for detailed information.
	 * @uses WebcomicTag::webcomic_term_cloud()
	 */
	function webcomic_character_cloud( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'id'         => '',
			'class'      => '',
			'smallest'   => 75,
			'largest'    => 150,
			'unit'       => '%',
			'image'      => '',
			'before'     => '',
			'after'      => '',
			'sep'        => "\n",
			'collection' => '',
			'order'      => 'RAND',
			'callback'   => '',
			'show_count' => false,
			'target'     => 'archive',
			'selected'   => 0
		) );
		
		$collection = $r[ 'collection' ] ? $r[ 'collection' ] : WebcomicTag::get_webcomic_collection();
		
		if ( taxonomy_exists( "{$collection}_character" ) ) {
			$r[ 'taxonomy' ] = "{$collection}_character";
			
			echo WebcomicTag::webcomic_term_cloud( $r );
		}
	}
}

if ( !function_exists( 'webcomic_collection_cloud' ) ) {
	/** Render a "cloud" of webcomic collections.
	 * 
	 * ### Arguments
	 * 
	 * - `string` **$id** - Value of the id attribute of the wrapping element.
	 * - `mixed` **$class** - String or array of additional classes for the wrapping element.
	 * - `integer` **$smallest** - The smallest font size to display links in.
	 * - `integer` **$largest** - The largest font size to display links in.
	 * - `string` **$unit** - The CSS unit to use for $smallest and $largest.
	 * - `string` **$image** - Size of the collection poster to use for collection links. Modified by the number of posts in a given term and the $smallest and $largest values.
	 * - `string` **$before** - Content to display before the output.
	 * - `string` **$after** - Content to display after the output.
	 * - `string` **$sep** - Separator to use between links. An empty value generates an unordered list. Defaults to "\n".
	 * - `string` **$orderby** - What to sort the collections by. May be one of 'name', 'slug', 'count', or 'updated'. Defaults to collection ID.
	 * - `string` **$order** - How to order collections. Defaults to 'RAND'.
	 * - `mixed` **$callback** - Callback function to use when building links.
	 * - `boolean` **$show_count** - Whether to display the total number of webcomics in a collection.
	 * - `string` **$target** - The target url for collections, one of 'archive', 'first', 'last', or 'random'. Defaults to 'archive'.
	 * - `integer` **$selected** - The ID of the current collection.
	 * 
	 * <code>
	 * // render a cloud of webcomic collections
	 * webcomic_collection_cloud();
	 * 
	 * // render a list cloud of webcomic collections linked to the beginning of each character
	 * webcomic_collection_cloud( array( 'target' => 'first', 'sep' => '' ) );
	 * 
	 * // render a cloud of thumbnail-sized webcomic collections
	 * webcomic_collection_cloud( array( 'image' => 'thumbnail' ) );
	 * </code>
	 * 
	 * @package Webcomic
	 * @param array $args Array of arguments. See function description for detailed information.
	 * @uses WebcomicTag::webcomic_collection_cloud()
	 */
	function webcomic_collection_cloud( $args = array() ) {
		$r = wp_parse_args( $args, array(
			'id'         => '',
			'class'      => '',
			'smallest'   => 75,
			'largest'    => 150,
			'unit'       => '%',
			'image'      => '',
			'before'     => '',
			'after'      => '',
			'sep'        => "\n",
			'orderby'    => '',
			'order'      => 'RAND',
			'callback'   => '',
			'show_count' => false,
			'target'     => 'archive',
			'selected'   => 0
		) );
		
		echo WebcomicTag::webcomic_collection_cloud( $r );
	}
}

///
// Walker Classes
///

if ( !class_exists( 'Walker_WebcomicTerm_Dropdown' ) ) {
	/** Handle webcomic_dropdown_term() output.
	 * 
	 * @package Webcomic
	 */
	class Walker_WebcomicTerm_Dropdown extends Walker {
		/** What the class handles.
		 * 
		 * Walker_WebcomicTerm_Dropdown handles both webcomic storylines and
		 * characters of various taxonomies, so we specify the singular tree
		 * type as the unhelpfully generic 'webcomic_term'. The Walker class
		 * doesn't seem to actually use this for anything.
		 * 
		 * @var string
		 */
		public $tree_type = 'webcomic_term';
		
		/** Database fields to use while walking the tree.
		 * @var array
		 */
		public $db_fields = array (
			'id'     => 'term_id',
			'parent' => 'parent'
		);
		
		/** Start element output.
		 * 
		 * The `<select>` element generated is inherently flat, so we can
		 * handle everything in start_el() without the need for end_el(),
		 * start_lvl(), or end_lvl.
		 * 
		 * @param string $output Walker output string.
		 * @param object $term Current term being handled by the walker.
		 * @param integer $depth Depth the walker is currently at.
		 * @param array $args Arguments passed to the walker.
		 * @uses WebcomicTag::get_relative_webcomic_link()
		 * @filter string webcomic_term_dropdown_title Filters the term titles used by `webcomic_dropdown_storylines` and `webcomic_dropdown_characters`.
		 */
		public function start_el( &$output, $term, $depth, $args ) {
			extract( $args, $args[ 'hierarchical' ] ? EXTR_SKIP : EXTR_OVERWRITE );
			
			$term_pad   = str_repeat( '&nbsp;', $depth * 4 );
			$term_title = apply_filters( 'webcomic_term_dropdown_title', esc_attr( $term->name ), $term );
			
			if ( $webcomics ) {
				$the_posts = new WP_Query( array(
					'post_type' => str_replace( array( '_storyline', '_character' ), '', $term->taxonomy ),
					'order'     => $webcomic_order,
					'orderby'   => $webcomic_orderby,
					'tax_query' => array(
						array(
							'taxonomy' => $term->taxonomy,
							'field'    => 'id',
							'terms'    => $term->term_id
						)
					)
				) );
				
				if ( $the_posts->have_posts() ) {
					$output .= sprintf( '<optgroup label="%s%s%s">',
						$term_pad,
						$term_title,
						$show_count ? " ({$term->count})" : ''
					);
					
					while ( $the_posts->have_posts() ) { $the_posts->the_post();
						$output .= sprintf( '<option value="%s" data-webcomic-url="%s"%s>%s%s</option>',
							get_the_ID(),
							apply_filters( 'the_permalink', get_permalink() ),
							$selected === get_the_ID() ? ' selected' : '',
							$term_pad,
							the_title( '', '', false )
						);
					}
					
					$output .= '</optgroup>';
				}
				
				wp_reset_postdata();
			} else {
				$output .= sprintf( '<option value="%s" data-webcomic-url="%s"%s>%s%s%s</option>',
					$term->term_id,
					'archive' === $target ? get_term_link( $term, $term->taxonomy ) : WebcomicTag::get_relative_webcomic_link( $target, $term->term_id, false, $term->taxonomy, preg_replace( '/_(storyline|character)$/', '', $term->taxonomy ) ),
					$selected === $term->term_id ? ' selected' : '',
					$term_pad,
					$term_title,
					$show_count ? " ({$term->count})" : ''
				);
			}
		}
	}
}

if ( !class_exists( 'Walker_WebcomicTerm_List' ) ) {
	/** Handle webcomic_list_term() output.
	 * 
	 * @package Webcomic
	 */
	class Walker_WebcomicTerm_List extends Walker {
		/** What the class handles.
		 * 
		 * Walker_WebcomicTerm_List handles both webcomic storylines and
		 * characters of various taxonomies, so we specify the singular tree
		 * type as the unhelpfully generic 'webcomic_term'. The Walker class
		 * doesn't seem to actually use this for anything.
		 * 
		 * @var string
		 */
		public $tree_type = 'webcomic_term';
		
		/** Database fields to use while walking the tree.
		 * @var array
		 */
		public $db_fields = array (
			'id'     => 'term_id',
			'parent' => 'parent'
		);
		
		/** Start level output.
		 * 
		 * @param string $output Walker output string.
		 * @param integer $depth Depth the walker is currently at.
		 * @param array $args Arguments passed to the walker.
		 */
		public function start_lvl( &$output, $depth, $args ) {
			if ( $args[ 'hierarchical' ] ) {
				$output .= $args[ 'ordered' ] ? '<ol class="children">' : '<ul class="children">';
			}
		}
		
		/** End level output.
		 * 
		 * @param string $output Walker output string.
		 * @param integer $depth Depth the walker is currently at.
		 * @param array $args Arguments passed to the walker.
		 */
		public function end_lvl( &$output, $depth, $args ) {
			if ( $args[ 'hierarchical' ] ) {
				$output .= $args[ 'ordered' ] ? '</ol>' : '</ul>';
			}
		}
		
		/** Start element output.
		 * 
		 * @param string $output Walker output string.
		 * @param object $term Current term being handled by the walker.
		 * @param integer $depth Depth the walker is currently at.
		 * @param array $args Arguments passed to the walker.
		 * @uses WebcomicTag::get_relative_webcomic_link()
		 * @filter string webcomic_term_list_title Filters the term titles used by `webcomic_list_storylines` and `webcomic_list_characters`.
		 * @filter string webcomic_term_image Filters the term images used by `webcomic_list_storylines` and `webcomic_list_characters`.
		 * @filter string webcomic_term_description Filters the term titles used by `webcomic_dropdown_transcript_languages`.
		 */
		public function start_el( &$output, $term, $depth, $args ) {
			extract( $args, $args[ 'hierarchical' ] ? EXTR_SKIP : EXTR_OVERWRITE );
			
			$term_title = apply_filters( 'webcomic_term_list_title', esc_attr( $term->name ), $term );
			$feed_image = filter_var( $feed, FILTER_VALIDATE_URL );
			$feed_link  = $feed ? sprintf( '<a href="%s" class="webcomic-term-feed">%s</a>',
				get_term_feed_link( $term->term_id, $term->taxonomy, $feed_type ),
				$feed_image ? sprintf( '<img src="%s" alt="%s">', $feed, sprintf( __( 'Feed for %s', 'webcomic' ), $term->name ) ) : $feed
			) : '';
			
			if ( $webcomics ) {
				$the_posts = new WP_Query( array(
					'post_type' => str_replace( array( '_storyline', '_character' ), '', $term->taxonomy ),
					'order'     => $webcomic_order,
					'orderby'   => $webcomic_orderby,
					'tax_query' => array(
						array(
							'taxonomy' => $term->taxonomy,
							'field'    => 'id',
							'terms'    => $term->term_id
						)
					)
				) );
				
				if ( $the_posts->have_posts() ) {
					$output .= sprintf( '<li class="webcomic-term %s webcomic-term-%s%s"><a href="%s" class="webcomic-term-link">%s%s</a>%s%s<%s class="webcomics">',
						$term->taxonomy,
						$term->term_id,
						$selected === $term->term_id ? ' current' : '',
						'archive' === $target ? get_term_link( $term, $term->taxonomy ) : WebcomicTag::get_relative_webcomic_link( $target, $term->term_id, false, $term->taxonomy, preg_replace( '/_(storyline|character)$/', '', $term->taxonomy ) ),
						sprintf( '<div class="webcomic-term-name">%s%s</div>', $term_title, $show_count ? " ({$term->count})" : '' ),
						( $show_image and $term->webcomic_image ) ? sprintf( '<div class="webcomic-term-image">%s</div>', apply_filters( 'webcomic_term_image', wp_get_attachment_image( $term->webcomic_image, $show_image ), $show_image, $term ) ) : '',
						( $show_description and $term->description ) ? sprintf( '<div class="webcomic-term-description">%s</div>', apply_filters( 'webcomic_term_description', $term->description, $term ) ) : '',
						$feed_link,
						$ordered ? 'ol' : 'ul'
					);
					
					while ( $the_posts->have_posts() ) { $the_posts->the_post();
						$output .= sprintf( '<li%s><a href="%s">%s</a></li>',
							$selected === get_the_ID() ? ' class="current"' : '',
							apply_filters( 'the_permalink', get_permalink() ),
							$webcomic_image ? WebcomicTag::the_webcomic( $webcomic_image, 'self' ) : the_title( '', '', false )
						);
					}
					
					$output .= $ordered ? '</ol>' : '</ul>';
				}
				
				wp_reset_postdata();
			} else {
				$output .= sprintf( '<li class="webcomic-term %s webcomic-term-%s%s"><a href="%s" class="webcomic-term-link">%s%s</a>%s%s',
					$term->taxonomy,
					$term->term_id,
					$selected === $term->term_id ? ' current' : '',
					'archive' === $target ? get_term_link( $term, $term->taxonomy ) : WebcomicTag::get_relative_webcomic_link( $target, $term->term_id, false, $term->taxonomy, preg_replace( '/_(storyline|character)$/', '', $term->taxonomy ) ),
					sprintf( '<div class="webcomic-term-name">%s%s</div>', $term_title, $show_count ? " ({$term->count})" : '' ),
					( $show_image and $term->webcomic_image ) ? sprintf( '<div class="webcomic-term-image">%s</div>', apply_filters( 'webcomic_term_image', wp_get_attachment_image( $term->webcomic_image, $show_image ), $show_image, $term ) ) : '',
					( $show_description and $term->description ) ? sprintf( '<div class="webcomic-term-description">%s</div>', apply_filters( 'webcomic_term_description', $term->description, $term ) ) : '',
					$feed_link
				);
			}
		}
		
		/** End element output.
		 * 
		 * @param string $output Walker output string.
		 * @param object $term Current term being handled by the walker.
		 * @param integer $depth Depth the walker is currently at.
		 * @param array $args Arguments passed to the walker.
		 */
		public function end_el( &$output, $term, $depth, $args ) {
			$output .= '</li>';
		}
	}
}

if ( !class_exists( 'Walker_WebcomicTranscriptTerm_Dropdown' ) ) {
	/** Handle webcomic_dropdown_transcript_term() output.
	 * 
	 * @package Webcomic
	 */
	class Walker_WebcomicTranscriptTerm_Dropdown extends Walker {
		/** What the class handles.
		 * 
		 * Walker_WebcomicTranscriptTerm_Dropdown may handle various
		 * taxonomies, so we specify the singular tree type as the
		 * unhelpfully generic 'webcomic_transcript_term'. The Walker class
		 * doesn't seem to actually use this for anything.
		 * 
		 * @var string
		 */
		public $tree_type = 'webcomic_transcript_term';
		
		/** Database fields to use while walking the tree.
		 * @var array
		 */
		public $db_fields = array (
			'id'     => 'term_id',
			'parent' => 'parent'
		);
		
		/** Start element output.
		 * 
		 * The `<select>` element generated is inherently flat, so we can
		 * handle everything in start_el() without the need for end_el(),
		 * start_lvl(), or end_lvl.
		 * 
		 * @param string $output Walker output string.
		 * @param object $term Current term being handled by the walker.
		 * @param integer $depth Depth the walker is currently at.
		 * @param array $args Arguments passed to the walker.
		 * @uses WebcomicTag::get_webcomic_transcripts_link()
		 * @filter string webcomic_transcript_term_dropdown_title Filters the term titles used by `webcomic_dropdown_transcript_languages`.
		 */
		public function start_el( &$output, $term, $depth, $args ) {
			extract( $args, $args[ 'hierarchical' ] ? EXTR_SKIP : EXTR_OVERWRITE );
			
			$term_title = apply_filters( 'webcomic_transcript_term_dropdown_title', esc_attr( $term->name ), $term );
			
			$output .= sprintf( '<option value="%s" data-webcomic-url="%s"%s>%s</option>',
				$term->term_id,
				WebcomicTag::get_webcomic_transcripts_link( $term, $the_post ),
				$selected === $term->term_id ? ' selected' : '',
				$term_title
			);
		}
	}
}

if ( !class_exists( 'Walker_WebcomicTranscriptTerm_List' ) ) {
	/** Handle webcomic_list_transcript_term() output.
	 * 
	 * @package Webcomic
	 */
	class Walker_WebcomicTranscriptTerm_List extends Walker {
		/** What the class handles.
		 * 
		 * Walker_WebcomicTerm_List handles both webcomic storylines and
		 * characters of various taxonomies, so we specify the singular tree
		 * type as the unhelpfully generic 'webcomic_term'. The Walker class
		 * doesn't seem to actually use this for anything.
		 * 
		 * @var string
		 */
		public $tree_type = 'webcomic_transcript_term';
		
		/** Database fields to use while walking the tree.
		 * @var array
		 */
		public $db_fields = array (
			'id'     => 'term_id',
			'parent' => 'parent'
		);
		
		/** Start level output.
		 * 
		 * @param string $output Walker output string.
		 * @param integer $depth Depth the walker is currently at.
		 * @param array $args Arguments passed to the walker.
		 */
		public function start_lvl( &$output, $depth, $args ) {
			if ( $args[ 'hierarchical' ] ) {
				$output .= $args[ 'ordered' ] ? '<ol class="children">' : '<ul class="children">';
			}
		}
		
		/** End level output.
		 * 
		 * @param string $output Walker output string.
		 * @param integer $depth Depth the walker is currently at.
		 * @param array $args Arguments passed to the walker.
		 */
		public function end_lvl( &$output, $depth, $args ) {
			if ( $args[ 'hierarchical' ] ) {
				$output .= $args[ 'ordered' ] ? '</ol>' : '</ul>';
			}
		}
		
		/** Start element output.
		 * 
		 * @param string $output Walker output string.
		 * @param object $term Current term being handled by the walker.
		 * @param integer $depth Depth the walker is currently at.
		 * @param array $args Arguments passed to the walker.
		 * @uses WebcomicTag::get_webcomic_transcripts_link()
		 * @filter string webcomic_transcript_term_list_title Filters the term titles used by `webcomic_list_transcript_languages`.
		 */
		public function start_el( &$output, $term, $depth, $args ) {
			extract( $args, $args[ 'hierarchical' ] ? EXTR_SKIP : EXTR_OVERWRITE );
			
			$term_title = apply_filters( 'webcomic_transcript_term_list_title', esc_attr( $term->name ), $term );
			
			$output .= sprintf( '<li class="webcomic-transcript-term %s webcomic-transcript-term-%s%s"><a href="%s" class="webcomic-term-link">%s</a>',
				$term->taxonomy,
				$term->term_id,
				$selected === $term->term_id ? ' current' : '',
				WebcomicTag::get_webcomic_transcripts_link( $term, $the_post ),
				$term_title
			);
		}
		
		/** End element output.
		 * 
		 * @param string $output Walker output string.
		 * @param object $term Current term being handled by the walker.
		 * @param integer $depth Depth the walker is currently at.
		 * @param array $args Arguments passed to the walker.
		 */
		public function end_el( &$output, $term, $depth, $args ) {
			$output .= '</li>';
		}
	}
}
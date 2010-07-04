<?php
add_filter( 'webcomic_initialize_class', create_function('', 'return "webcomic_admin";' ) );

class webcomic_admin extends webcomic {
	/**
	 * Upgrades legacy versions.
	 * 
	 * A specialized function for upgrading from versions
	 * of Webcomic prior to version 3. The upgrade process
	 * requires user activation and is performed in multiple
	 * steps to reduce failure potential.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param int $step The step of the upgrade process to run.
	 */
	function upgrade_legacy( $step = 0 ) {
		if ( !is_admin() || !get_option( 'webcomic_version' ) )
			return false;
		
		$this->domain();
		
		$e = array();
		$errors = sprintf( __( 'Step %d error:', 'webcomic' ), $step );
		$update = sprintf( __( 'Step %d complete!', 'webcomic' ), $step );
		
		if ( 1 == $step ) {
			if ( get_option( 'comic_transcripts_loggedin' ) )
				$transcribe_restrict = 'login';
			elseif ( get_option( 'comic_transcripts_required' ) )
				$transcribe_restrict = 'selfid';
			else
				$transcribe_restrict = 'anyone';
			
			if ( 'thumb' == get_option( 'comic_feed_size' ) )
				$feed_size = 'small';
			elseif ( get_option( 'comic_feed_size' ) )
				$feed_size = get_option( 'comic_feed_size' );
			else
				$feed_size = $this->option( 'feed_size' );
			
			$new = array();
			$new[ 'secure_toggle' ]       = ( get_option( 'comic_secure_names' ) || get_option( 'comic_secure_paths' ) ) ? true : false;
			$new[ 'transcribe_toggle' ]   = ( get_option( 'comic_transcripts_allowed' ) ) ? true : false;
			$new[ 'transcribe_restrict' ] = $transcribe_restrict;
			$new[ 'feed_toggle' ]         = ( get_option( 'comic_feed' ) ) ? true : false;
			$new[ 'feed_size' ]           = $feed_size;
			$new[ 'buffer_toggle' ]       = ( get_option( 'comic_buffer' ) ) ? true : false;
			$new[ 'buffer_size' ]         = intval( get_option( 'comic_buffer_alert' ) );
			$new[ 'shortcut_toggle' ]     = ( get_option( 'comic_keyboard_shortcuts' ) ) ? true : false;
			$new[ 'large_h' ]             = intval( get_option( 'comic_large_size_h' ) );
			$new[ 'large_w' ]             = intval( get_option( 'comic_large_size_w' ) );
			$new[ 'medium_h' ]            = intval( get_option( 'comic_medium_size_h' ) );
			$new[ 'medium_w' ]            = intval( get_option( 'comic_medium_size_h' ) );
			$new[ 'small_h' ]             = intval( get_option( 'comic_thumb_size_h' ) );
			$new[ 'small_w' ]             = intval( get_option( 'comic_thumb_size_w' ) );
			$new[ 'version' ]             = $this->version;
			$new[ 'default_collection' ]  = $this->option( 'default_collection' );
			$new[ 'integrate_toggle' ]    = $this->option( 'integrate_toggle' );
			$new[ 'transcribe_language' ] = $this->option( 'transcribe_language' );
			$new[ 'transcribe_default' ]  = $this->option( 'transcribe_default' );
			$new[ 'age_toggle' ]          = $this->option( 'age_toggle' );
			$new[ 'age_size' ]            = $this->option( 'age_size' );
			$new[ 'paypal_business' ]     = $this->option( 'paypal_business' );
			$new[ 'paypal_currency' ]     = $this->option( 'paypal_currency' );
			$new[ 'paypal_log' ]          = $this->option( 'paypal_log' );
			$new[ 'paypal_prints' ]       = $this->option( 'paypal_prints' );
			$new[ 'paypal_method' ]       = $this->option( 'paypal_method' );
			$new[ 'paypal_price_d' ]      = $this->option( 'paypal_price_d' );
			$new[ 'paypal_price_i' ]      = $this->option( 'paypal_price_i' );
			$new[ 'paypal_price_o' ]      = $this->option( 'paypal_price_o' );
			$new[ 'paypal_shipping_d' ]   = $this->option( 'paypal_shipping_d' );
			$new[ 'paypal_shipping_i' ]   = $this->option( 'paypal_shipping_i' );
			$new[ 'paypal_shipping_o' ]   = $this->option( 'paypal_shipping_o' );
			$new[ 'paypal_donation' ]     = $this->option( 'paypal_donation' );
			$new[ 'term_meta' ]           = $this->option( 'term_meta' );
			
			$this->option( $new );
			
			$this->update[ 'legacy_settings' ] = sprintf( __( '%s Settings have been transferred successfully.', 'webcomic' ), $update );
		} elseif ( 2 == $step ) {
			global $wpdb;
			
			$i             = 0;
			$comic_cats    = get_option( 'comic_category' );
			$cdir          = get_option( 'comic_directory' ) . '/';
			$path          = ( !defined( 'BLOGUPLOADDIR' ) ) ? ABSPATH : BLOGUPLOADDIR;
			$lang          = $this->option( 'transcribe_default' );
			$lkey          = current( array_keys( $lang ) );
			$trans_toggle  = ( $this->option( 'transcribe_toggle' ) ) ? true : false;
			$paypal_toggle = ( $this->option( 'paypal_prints' ) ) ? true : false;
			
			foreach ( $comic_cats as $cat_id ) {
				$cat = get_term( $cat_id, 'category' );
				
				if ( $files = glob( $path . $cdir . $cat->slug . '/thumbs/*-thumb.*' ) )
					foreach ( $files as $file )
						rename( $file, substr_replace( $file, '-small.', strrpos( $file, '-thumb.' ), 7 ) );
				
				if ( $posts = get_objects_in_term( $cat_id, 'category' ) ) {
					$wpdb->query( "UPDATE $wpdb->posts SET post_type = 'webcomic_post' WHERE ID IN (" . implode( ',', $posts ) . ") AND post_type = 'post'" );
					
					foreach ( $posts as $p ) {
						if ( get_post_meta( $p, 'comic_transcript', true ) ) {
							$status = 'publish';
							$text   = get_post_meta( $p, 'comic_transcript', true );
						} elseif ( get_post_meta( $p, 'comic_transcript_pending', true ) ) {
							$status = 'pending';
							$text   = get_post_meta( $p, 'comic_transcript_pending', true );
						} elseif ( get_post_meta( $p, 'comic_transcript_draft', true ) ) {
							$status = 'draft';
							$text   = get_post_meta( $p, 'comic_transcript_draft', true );
						} else
							$status = $text = false;
						
						if ( !( 'publish' == $status || 'pending' == $status ) && get_post_meta( $p, 'comic_transcript_backup', true ) )
							$backup = get_post_meta( $p, 'comic_transcript_backup', true );
						
						$meta = array(
							'files' => array( 'full' => array( get_post_meta( $p, 'comic_file', true ) ), 'large' => array( get_post_meta( $p, 'comic_large', true ) ), 'medium' => array( get_post_meta( $p, 'comic_medium', true ) ),	'small' => array( str_replace( '-thumb.', '-small.', get_post_meta( $p, 'comic_thumb', true ) ) ) ),
							'alternate' => array(),
							'description' => array( get_post_meta( $p, 'comic_description', true ) ),
							'transcripts' => array( $lkey => array( 'language' => $lang[ $lkey ], 'status' => $status, 'text' => $text ) ),
							'transcribe_toggle' => $trans_toggle,
							'paypal' => array( 'prints' => $paypal_toggle )
						);
						
						if ( isset( $backup ) )
							$meta[ 'transcripts' ][ $lkey ][ 'backup' ] = $backup;
						
						if ( empty( $meta[ 'files' ][ 'full' ] ) )
							$meta[ 'files' ] = array();
						else {
							if ( empty( $meta[ 'files' ][ 'large' ] ) )
								unset( $meta[ 'files' ][ 'large' ] );
							if ( empty( $meta[ 'files' ][ 'medium' ] ) )
								unset( $meta[ 'files' ][ 'medium' ] );
							if ( empty( $meta[ 'files' ][ 'small' ] ) )
								unset( $meta[ 'files' ][ 'small' ] );
						}
						
						update_post_meta( $p, 'webcomic', $meta );
						
						unset( $status, $text, $backup, $meta );
						
						$i++;
					}
				}
			}
			
			if ( $i )
				$this->update[ 'legacy_posts' ] = sprintf( __( '%s "Comic posts" converted to webcomics successfully.', 'webcomic' ), $update );
			else
				$this->errors[ 'no_legacy_posts' ] = sprintf( __( '%s No "comic posts" could be found.', 'webcomic' ), $error );
		} elseif ( 3 == $step ) {
			$comic_cats = get_option( 'comic_category' );
			$terms      = get_terms( 'chapter', 'get=all&orderby=' );
			$series     = $volumes = $chapters = $collections = array();
			
			if ( get_option( 'webcomic_temp_collections' ) )
				$collections = get_option( 'webcomic_temp_collections' );
			
			if ( get_option( 'webcomic_temp_volumes' ) )
				$volumes = get_option( 'webcomic_temp_volumes' );
			
			if ( get_option( 'webcomic_temp_chapters' ) )
				$chapters = get_option( 'webcomic_temp_chapters' );
			
			foreach ( $terms as $term ) {
				if ( !$term->parent )
					array_push( $series, $term );
				elseif ( in_array( $term->parent, $comic_cats ) )
					array_push( $volumes, $term );
				else
					array_push( $chapters, $term );
			}
			
			update_option( 'webcomic_temp_volumes', $volumes );
			update_option( 'webcomic_temp_chapters', $chapters );
			
			foreach ( $series as $k => $v ) {
				$posts = get_objects_in_term( $v->term_id, 'category' );
				
				if ( is_wp_error( $new = wp_insert_term( $v->name, 'webcomic_collection', array( 'slug' => $v->slug, 'description' => $v->description ) ) ) ) {
					$e[] = $v;
					continue;
				}
				
				wp_delete_term( $v->term_id, 'chapter' );
				
				if ( $posts )
					foreach ( $posts as $p )
						wp_set_object_terms( $p, ( int ) $new[ 'term_id' ], 'webcomic_collection', false );
				
				$collections[ $v->term_id ] = $new[ 'term_id' ];
			}
			
			update_option( 'webcomic_temp_collections', $collections );
			
			if ( $e ) {
				$l = '';
				
				foreach ( $e as $v )
					$l .= $v->name . '<br>';
				
				$this->errors[ 'legacy_chapters' ] = sprintf( __( "%s The following series' could not be converted to collections: $l", "webcomic" ), $errors );
			} else
				$this->update[ 'legacy_chapters' ] = sprintf( __( "%s Series' converted to collections successfully.", "webcomic" ), $update );
		} elseif ( 4 == $step ) {
			$volumes     = get_option( 'webcomic_temp_volumes' );
			$collections = get_option( 'webcomic_temp_collections' );
			$parents     = ( get_option( 'webcomic_temp_parents' ) ) ? get_option( 'webcomic_temp_parents' ) : array();
			
			foreach ( $volumes as $k => $v ) {
				if ( !$collections[ $v->parent ] )
					continue;
				
				$posts = get_objects_in_term( $v->term_id, 'chapter' );
				
				$_REQUEST[ 'webcomic_collection' ] = $collections[ $v->parent ];
				$_REQUEST[ 'webcomic_parent' ]     = 0;
				
				if ( is_wp_error( $new = wp_insert_term( $v->name, 'webcomic_storyline', array( 'slug' => $v->slug, 'description' => $v->description ) ) ) ) {
					$e[] = $v;
					continue;
				}
				
				wp_delete_term( $v->term_id, 'chapter' );
					
				if ( $posts )
					foreach ( $posts as $p )
						wp_set_object_terms( $p, ( int ) $new[ 'term_id' ], 'webcomic_storyline', true );
				
				$collections[ $v->term_id ] = $collections[ $v->parent ];
				$parents[ $v->term_id ] = $new[ 'term_id' ];
			}
			
			update_option( 'webcomic_temp_collections', $collections );
			update_option( 'webcomic_temp_parents', $parents );
			
			if ( $e ) {
				$l = '';
				
				foreach ( $e as $v )
					$l .= $v->name . '<br>';
				
				$this->errors[ 'legacy_chapters' ] = sprintf( __( "%s The following volumes could not be converted to storylines: $l", "webcomic" ), $errors );
			} else
				$this->update[ 'legacy_chapters' ] = sprintf( __( '%s Volumes converted to storylines successfully.', 'webcomic' ), $update );
		} elseif ( 5 == $step ) {
			$parents     = get_option( 'webcomic_temp_parents' );
			$chapters    = get_option( 'webcomic_temp_chapters' );
			$collections = get_option( 'webcomic_temp_collections' );
			
			foreach ( $chapters as $k => $v ) {
				if ( !$parents[ $v->parent ] || !$collections[ $v->parent ] )
					continue;
				
				$posts = get_objects_in_term( $v->term_id, 'chapter' );
				
				$_REQUEST[ 'webcomic_collection' ] = $collections[ $v->parent ];
				$_REQUEST[ 'webcomic_parent' ]     = $parents[ $v->parent ];
				
				if ( is_wp_error( $new = wp_insert_term( $v->name, 'webcomic_storyline', array( 'slug' => $v->slug, 'parent' => $parents[ $v->parent ], 'description' => $v->description ) ) ) ) {
					$e[] = $v;
					continue;
				}
				
				wp_delete_term( $v->term_id, 'chapter' );
					
				if ( $posts )
					foreach ( $posts as $p )
						wp_set_object_terms( $p, ( int ) $new[ 'term_id' ], 'webcomic_storyline', true );
			}
			
			if ( $e ) {
				$l = '';
				
				foreach ( $e as $v )
					$l .= $v->name . '<br>';
				
				$this->errors[ 'legacy_chapters' ] = sprintf( __( "%s The following chapters could not be converted to storylines: $l", "webcomic" ), $errors );
			} else
				$this->update[ 'legacy_chapters' ] = sprintf( __( '%s Chapters converted to storylines successfully.', 'webcomic' ), $update );
		} elseif ( 6 == $step ) {
			global $wpdb;
			
			clean_term_cache( array(), 'webcomic_storyline' );
			
			$term_meta = $this->option( 'term_meta' );
			
			$w = new webcomic_Walker_AdminTermNormalize();
			
			$normalized = $w->walk( get_terms( 'webcomic_storyline', 'get=all' ), 0, array( 'term_meta' => $term_meta ) );
			
			if ( is_array( $normalized ) )
				$this->option( 'term_meta', $normalized );
			
			$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key IN ( 'comic_file', 'comic_large', 'comic_medium', 'comic_thumb', 'comic_description', 'comic_transcript', 'comic_transcript_pending', 'comic_transcript_draft', 'comic_transcript_backup' )" );
			
			if ( $terms = get_terms( 'chapter', 'get=all' ) )
				foreach ( $terms as $term )
					wp_delete_term( $term->term_id, 'chapter' );
			
			wp_clear_scheduled_hook( 'webcomic_buffer_alert' );
			
			wp_schedule_event( time(), 'daily', 'webcomic_buffer_alert' );
			
			delete_option( 'comic_category' );
			delete_option( 'comic_directory' );
			delete_option( 'comic_current_chapter' );
			delete_option( 'comic_secure_paths' );
			delete_option( 'comic_secure_names' );
			delete_option( 'comic_transcripts_allowed' );
			delete_option( 'comic_transcripts_required' );
			delete_option( 'comic_transcripts_loggedin' );
			delete_option( 'comic_feed' );
			delete_option( 'comic_feed_size' );
			delete_option( 'comic_buffer' );
			delete_option( 'comic_buffer_alert' );
			delete_option( 'comic_keyboard_shortcuts' );
			delete_option( 'comic_thumb_crop' );
			delete_option( 'comic_large_size_w' );
			delete_option( 'comic_large_size_h' );
			delete_option( 'comic_medium_size_w' );
			delete_option( 'comic_medium_size_h' );
			delete_option( 'comic_thumb_size_w' );
			delete_option( 'comic_thumb_size_h' );
			delete_option( 'webcomic_temp_volumes' );
			delete_option( 'webcomic_temp_chapters' );
			delete_option( 'webcomic_temp_collections' );
			delete_option( 'webcomic_temp_parents' );
			delete_option( 'webcomic_version' );
			
			$this->update[ 'legacy_cleanup' ] = sprintf( __( "%s Congratulations, you're almost ready to roll.", "webcomic" ), $update );
		} else
			return false;
	}
	
	/**
	 * 'right_now_content_table_end' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_right_now_content_table_end() {
		$np   = wp_count_posts( 'webcomic_post' );
		$num  = number_format_i18n( $np->publish );
		$text = _n( __( 'Webcomic', 'webcomic' ), __( 'Webcomics', 'webcomic' ), intval( $np->publish ) );
		
		if ( current_user_can( 'upload_files' ) ) {
			$num  = "<a href=" . admin_url( 'admin.php?page=webcomic_files' ) . ">$num</a>";
			$text = "<a href=" . admin_url( 'admin.php?page=webcomic_files' ) . ">$text</a>";
		}
		
		echo '<tr><td class="first b b-webcomics">' . $num . '</td><td class="t webcomics">' . $text . '</td></tr>';
	}
	
	/**
	 * 'favorite_actions' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_favorite_actions( $actions ) {
		global $post_type_object, $post;
		
		$this->domain();
		
		foreach ( $actions as $k => $v )
			if ( false !== strpos( $k, 'webcomic_post' ) )
				unset( $actions[ $k ] );
		
		$wc = ( !empty( $_REQUEST[ 'webcomic_collection' ] ) ) ? '&amp;webcomic_collection=' . $_REQUEST[ 'webcomic_collection' ] : '';
		
		if ( $post && !$wc && is_object_in_term( $post->ID, 'webcomic_collection' ) && $id = current( wp_get_object_terms( $post->ID, 'webcomic_collection', array( 'fields' => 'ids' ) ) ) )
			$wc = '&amp;webcomic_collection=' . $id;
		
		$na = array();
		
		if ( isset( $post_type_object ) && $wc )
			$na[ "admin.php?page=webcomic_files$wc" ] = array( __( 'Edit Webcomics', 'webcomic' ), 'upload_files' );
		
		$na[ "post-new.php?post_type=webcomic_post$wc" ] = array( __( 'New Webcomic', 'webcomic' ), 'upload_files' );
		
		$actions = $na + $actions;
		
		return $actions;
	}
	
	/**
	 * 'admin_menu' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_admin_menu() {
		$this->domain();
		
		if ( !$this->option( 'uninstall' ) ) {
			global $menu;
			
			$p = ( isset( $menu[ 3 ] ) ) ? false : 3;
			
			add_menu_page( __( 'Webcomic', 'webcomic' ), __( 'Webcomic', 'webcomic' ), 'upload_files', 'webcomic_files', array( &$this, 'admin_files' ), $this->url . 'webcomic-includes/icon-small.png', $p );
			$pages[ 'webcomics' ]   = add_submenu_page( 'webcomic_files', __( 'Webcomic Files', 'webcomic' ), __( 'Webcomics', 'webcomic' ), 'upload_files', 'webcomic_files', array( &$this, 'admin_files' ) );
			$pages[ 'storylines' ]  = add_submenu_page( 'webcomic_files', __( 'Webcomic Storylines', 'webcomic' ), __( 'Storylines', 'webcomic' ), 'manage_categories', 'webcomic_storyline', array( &$this, 'admin_terms' ) );
			$pages[ 'characters' ]  = add_submenu_page( 'webcomic_files', __( 'Webcomic Characters', 'webcomic' ), __( 'Characters', 'webcomic' ), 'manage_categories', 'webcomic_character', array( &$this, 'admin_terms' ) );
			$pages[ 'collections' ] = add_submenu_page( 'webcomic_files', __( 'Webcomic Collections', 'webcomic' ), __( 'Collections', 'webcomic' ), 'upload_files', 'webcomic_collection', array( &$this, 'admin_terms' ) );
		} else
			add_menu_page( __( 'Webcomic', 'webcomic' ), __( 'Webcomic', 'webcomic' ), 'manage_options', 'webcomic_tools', array( &$this, 'admin_tools' ), $this->url . 'webcomic-includes/icon-small.png', 3 );
		
		$pages[ 'tools' ] = add_submenu_page( 'webcomic_files', __( 'Webcomic Tools', 'webcomic' ), __( 'Tools', 'webcomic' ), 'manage_options', 'webcomic_tools', array( &$this, 'admin_tools' ) );
		
		if ( !$this->option( 'uninstall' ) )
			$pages[ 'settings' ]    = add_submenu_page( 'webcomic_files', __( 'Webcomic Settings', 'webcomic' ), __( 'Settings', 'webcomic' ), 'manage_options', 'webcomic_settings', array( &$this, 'admin_settings' ) );
		
		if ( !$this->option( 'uninstall' ) ) {
			if ( !empty( $pages[ 'webcomics' ] ) )   register_column_headers( $pages[ 'webcomics' ], array( 'thumbnails' => __( 'Thumbnails', 'webcomic' ), 'post' => __( 'Post', 'webcomic' ), 'name' => __( 'Files', 'webcomic' ), 'storylines' => __( 'Storylines', 'webcomic' ), 'characters' => __( 'Characters', 'webcomic' ), 'comments' => '<div class="vers"><img src="images/comment-grey-bubble.png" alt="Comments"></div>', 'date' => __( 'Date', 'webcomic' ) ) );
			if ( !empty( $pages[ 'storylines' ] ) )  register_column_headers( $pages[ 'storylines' ], array( 'cover' => __( 'Cover', 'webcomic' ), 'name' => __( 'Name', 'webcomic' ), 'slug' => __( 'Slug', 'webcomic' ), 'posts' => __( 'Webcomics', 'webcomic' ) ) );
			if ( !empty( $pages[ 'characters' ] ) )  register_column_headers( $pages[ 'characters' ], array( 'cover' => __( 'Avatar', 'webcomic' ), 'name' => __( 'Name', 'webcomic' ), 'slug' => __( 'Slug', 'webcomic' ), 'posts' => __( 'Webcomics', 'webcomic' ) ) );
			if ( !empty( $pages[ 'collections' ] ) ) register_column_headers( $pages[ 'collections' ], array( 'cover' => __( 'Cover', 'webcomic' ), 'name' => __( 'Name', 'webcomic' ), 'slug' => __( 'Slug', 'webcomic' ), 'characters' => __( 'Characters', 'webcomic' ), 'storylines' => __( 'Storylines', 'webcomic' ), 'posts' => __( 'Webcomics', 'webcomic' ) ) );
			
			add_meta_box( 'webcomic', __( 'Webcomic', 'webcomic' ), array( &$this, 'admin_metabox' ), 'webcomic_post', 'normal', 'high' );
		}
		
		$help = '<a href="http://webcomicms.net/support/" target="_blank">' . __( 'Webcomic Help', 'webcomic' ) . '</a>';
		
		foreach ( $pages as $page )
			if ( !empty( $page ) )
				add_contextual_help( $page, $help );
	}
	
	/**
	 * 'admin_notices' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_admin_notices() {
		echo '<style>#menu-posts-webcomicpost{display:none}</style>';
		if ( $this->update ) { ?><div id="message" class="updated fade"><p><?php echo implode( '</p><p>', $this->update ); ?></p></div><?php }
		if ( $this->errors ) { ?><div id="message" class="error"><p><?php echo implode( '</p><p>', $this->errors ); ?></p></div><?php }
	}
	
	/**
	 * 'nav_menu_meta_box_object' hook.
	 * 
	 * This is a rather ugly hack to get our custom
	 * taxonomies into WordPress' new menu system as
	 * it insists on using 'show_ui' and not the more
	 * sensible 'public' attribute to determine visibility.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_nav_menu_meta_box_object( $tax ) {
		$taxonomies = get_taxonomies( array( 'public' => true ), 'object' );

		if ( !$taxonomies )
			return $tax;
	
		foreach ( $taxonomies as $t ) {
			if ( !( 'webcomic_collection' == $t->name || 'webcomic_storyline' == $t->name || 'webcomic_character' == $t->name ) )
				continue;
			
			$id = $t->name;
			add_meta_box( "add-{$id}", $t->label, 'wp_nav_menu_item_taxonomy_meta_box', 'nav-menus', 'side', 'default', $t );
		}
		
		return $tax;
	}
	
	/**
	 * 'admin_init' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_admin_init() {
		$this->admin_ajax();
		$this->domain();
		
		add_action( 'admin_footer-post.php', array( $this, 'admin_footer_files' ) );
		add_action( 'admin_footer-post-new.php', array( $this, 'admin_footer_files' ) );
		
		if ( !current_user_can( 'manage_categories' ) && ( ( isset( $_REQUEST[ 'subpage' ] ) && ( 'edit_webcomic_collection' == $_REQUEST[ 'subpage' ] ) ) || ( !empty( $_REQUEST[ 'bulk' ] ) && ( $action = ( !empty( $_REQUEST[ 'submit-1' ] ) ) ? $_REQUEST[ 'action-1' ] : $_REQUEST[ 'action-2' ] ) && 'batch_file' == $action ) ) )
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'webcomic' ) );
		
		if ( isset( $_REQUEST[ 'post_type' ], $_REQUEST[ 'trashed' ] ) && 'webcomic_post' == $_REQUEST[ 'post_type' ] ) {
			$wc = current( wp_get_object_terms( $_REQUEST[ 'ids' ], 'webcomic_collection', array( 'fields' => 'ids' ) ) );
			wp_redirect( admin_url( 'admin.php?page=webcomic_files&webcomic_post_trashed=1&webcomic_collection=' . $wc ) );
			die();
		}
		
		if ( isset( $_REQUEST[ 'webcomic_post_trashed' ] ) )
			$this->update[ 'deleted_post' ] = __( 'Post moved to the trash', 'webcomic' );
		
		if ( empty( $_REQUEST[ 'action' ] ) )
			return false;
		
		if ( 'add_webcomic_term' == $_REQUEST[ 'action' ] ) {
			check_admin_referer( 'add_webcomic_term' );
			
			$args = array( 'slug' => $_REQUEST[ 'webcomic_nicename' ], 'parent' => $_REQUEST[ 'webcomic_parent' ], 'description' => $_REQUEST[ 'webcomic_description' ] );
			
			if ( 'webcomic_collection' == $_REQUEST[ 'page' ] )
				unset( $args[ 'parent' ] );
			
			if ( is_wp_error( $new_term = wp_insert_term( trim( $_REQUEST[ 'webcomic_name' ] ), $_REQUEST[ 'page' ], $args ) ) )
				$this->errors = $new_term->get_error_messages();
			else
				$this->update[ 'added_term' ] = sprintf( __( 'Added %s', 'webcomic' ), trim( $_REQUEST[ 'webcomic_name' ] ) );
		}
		
		if ( 'update_webcomic_term' == $_REQUEST[ 'action' ] ) {
			check_admin_referer( 'update_webcomic_term' );
			
			$args = array( 'name' => trim( $_REQUEST[ 'webcomic_name' ] ), 'slug' => $_REQUEST[ 'webcomic_nicename' ], 'description' => $_REQUEST[ 'webcomic_description' ] );
			
			if ( 'webcomic_collection' != $_REQUEST[ 'page' ] )
				$args[ 'parent' ] = $_REQUEST[ 'webcomic_parent' ];
			else
				$args[ 'term_group' ] = $_REQUEST[ 'webcomic_group' ];
			
			if ( is_wp_error( $update_term = wp_update_term( $_REQUEST[ 'webcomic_term' ], $_REQUEST[ 'page' ], $args ) ) )
				$this->errors = $update_term->get_error_messages();
			else
				$this->update[ 'updated_term' ] = sprintf( __( 'Updated %s', 'webcomic' ), trim( $_REQUEST[ 'webcomic_name' ] ) );
		}
		
		if ( 'delete_webcomic_term' == $_REQUEST[ 'action' ] && $old_term = get_term( $_REQUEST[ 'webcomic_term' ], $_REQUEST[ 'page' ] ) ) {
			check_admin_referer( 'delete_webcomic_term' );
			
			global $wpdb;
			
			if ( 'webcomic_collection' == $old_term->taxonomy ) {
				if ( $old_term->webcomic_default ) {
					$this->errors[ 'default_term' ] = __( 'You cannot delete the default collection.', 'webcomic' );
					return false;
				}
				
				$posts = get_objects_in_term( $old_term->term_id, 'webcomic_collection' );
			}
			
			if ( is_wp_error( $deleted = wp_delete_term( $_REQUEST[ 'webcomic_term' ], $_REQUEST[ 'page' ] ) ) )
				$this->errors = $deleted->get_error_messages();
			else {
				if ( !empty( $posts ) ) {
					$cat = ( int ) get_option( 'default_category' );
					$wpdb->query( "UPDATE $wpdb->posts SET post_type = 'post' WHERE ID IN (" . implode( ',', $posts ) . ")" );
					$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE post_id IN (" . implode( ',', $posts ) . ") AND meta_key IN ( 'webcomic' ) " );
					
					foreach ( $posts as $p )
						wp_set_object_terms( $p, $cat, 'category' );
				}
				
				$this->update[ 'deleted_term' ] = sprintf( __( 'Deleted %s', 'webcomic' ), $old_term->name );
			}
		}
			
		if ( 'default_webcomic_term' == $_REQUEST[ 'action' ] && ( 'on' == $_REQUEST[ 'switch' ] || 'off' == $_REQUEST[ 'switch' ] ) && $term = get_term( $_REQUEST[ 'webcomic_term' ], $_REQUEST[ 'page' ] ) ) {
			check_admin_referer( 'default_webcomic_term' );
			
			$type = end( explode( '_', $_REQUEST[ 'page' ] ) );
			
			if ( 'collection' == $type ) {
				$a = __( 'set as default', 'webcomic' );
				
				$this->option( 'default_collection', $_REQUEST[ 'webcomic_term' ] );
			} else {
				$a = ( 'on' == $_REQUEST[ 'switch' ] ) ? __( 'added to defaults', 'webcomic' ) : __( 'removed from defaults', 'webcomic' );
				
				$term_meta = $this->option( 'term_meta' );
				$term_meta[ $type ][ $_REQUEST[ 'webcomic_term' ] ][ 'default' ] = ( 'on' == $_REQUEST[ 'switch' ] ) ? true : false;
				
				$this->option( 'term_meta', $term_meta );
			}
			
			$this->update[ 'default_term' ] = sprintf( __( '%s %s', 'webcomic' ), $term->name, $a );
		}
		
		if ( 'move_webcomic_term' == $_REQUEST[ 'action' ] && ( 'up' == $_REQUEST[ 'direction' ] || 'dn' == $_REQUEST[ 'direction' ] ) ) {
			check_admin_referer( 'move_webcomic_term' );
			
			$term  = get_term( $_REQUEST[ 'webcomic_term' ], $_REQUEST[ 'page' ] );
			$sibs  = get_terms( $_REQUEST[ 'page' ], 'hide_empty=0&webcomic_order=1&term_group=' . $_REQUEST[ 'webcomic_collection' ] . '&parent=' . $term->parent );
			$last  = end( $sibs );
			$first = reset( $sibs );
			
			echo $term->term_id . ' = ' . $last->term_id;
			
			if ( ( 'up' == $_REQUEST[ 'direction' ] && $term->term_id == $first->term_id ) || ( 'dn' == $_REQUEST[ 'direction' ] && $term->term_id == $last->term_id ) ) {
				$d = ( 'up' == $_REQUEST[ 'direction' ] ) ? __( 'first', 'webcomic' ) : __( 'last', 'webcomic' );
				$this->errors[ 'bad_move' ] = sprintf( __( '&#8220;%s&#8221; is already %s in this storyline.', 'webcomic' ), $term->name, $d );
			} elseif ( $new_order = ( 'up' == $_REQUEST[ 'direction' ] ) ? $term->webcomic_order - 1 : $term->webcomic_order + 1 ) {
				$type = end( explode( '_', $_REQUEST[ 'page' ] ) );
				$term_meta = $this->option( 'term_meta' );
				
				foreach ( $sibs as $sib ) {
					if ( $new_order == $sib->webcomic_order ) {
						$term_meta[ $type ][ $sib->term_id ][ 'order' ]  = ( 'up' == $_REQUEST[ 'direction' ] ) ? $sib->webcomic_order + 1 : $sib->webcomic_order - 1;
						$term_meta[ $type ][ $term->term_id ][ 'order' ] = $new_order;
						break;
					}
				}
				
				$this->option( 'term_meta', $term_meta );
				
				$a = ( 'up' == $_REQUEST[ 'direction' ] ) ? __( 'up', 'webcomic' ) : __( 'down', 'webcomic' );
				
				$this->update[ 'moved_term' ] = sprintf( __( '%s moved %s', 'webcomic' ), $term->name, $a );
			}
		}
		
		if ( 'bulk_webcomic_term' == $_REQUEST[ 'action' ] ) {
			check_admin_referer( 'bulk_webcomic_term' );
			
			if ( $_REQUEST[ 'bulk' ] && $action = ( !empty( $_REQUEST[ 'submit-1' ] ) ) ? $_REQUEST[ 'action-1' ] : $_REQUEST[ 'action-2' ] ) {
				$type = end( explode( '_', $_REQUEST[ 'page' ] ) );
				
				if ( 'regen' == $action ) {
					$i  = 0;
					$wc = get_term( $_REQUEST[ 'webcomic_collection' ], 'webcomic_collection' );
					
					foreach ( $_REQUEST[ 'bulk' ] as $bulk )
						if ( $this->regen( $bulk, $type, $wc->slug ) )
							$i++;
					
					if ( $i )
						$this->update[ 'regen_thumbs' ] = sprintf( _n( 'Thumbnails for %d file regenerated', 'Thumbnails for %d files regenerated', $i, 'webcomic' ), $i );
				} elseif ( 'add_default' == $action || 'remove_default' == $action ) {
					$i = 0;
					$a = ( 'add_default' == $action ) ? __( 'added to', 'webcomic' ) : __( 'removed from', 'webcomic' );
					$term_meta = $this->option( 'term_meta' );
					
					foreach ( $_REQUEST[ 'bulk' ] as $bulk ) {
						if ( ( 'add_default' == $action && !$term_meta[ $type ][ $bulk ][ 'default' ] ) || ( 'remove_default' == $action && $term_meta[ $type ][ $bulk ][ 'default' ] ) )
							$i++;
						
						$term_meta[ $type ][ $bulk ][ 'default' ] = ( 'add_default' == $action ) ? true : false;
					}
					
					$this->option( 'term_meta', $term_meta );
					
					if ( $i )
						$this->update[ 'default_term' ] = sprintf( _n( '%d term %s defaults', '%d terms %s defaults', $i, 'webcomic' ), $i, $a );
				} elseif ( 'delete' == $action ) {
					$i = 0;
					
					foreach ( $_REQUEST[ 'bulk' ] as $bulk )
						if ( $old_term = get_term( $bulk, $type ) )
							if ( !is_wp_error( $deleted = wp_delete_term( $bulk, $_REQUEST[ 'page' ] ) ) )
								$i++;
					
					if ( $i )
						$this->update[ 'deleted_term' ] = sprintf( _n( '%d term deleted', '%d terms deleted', $i, 'webcomic' ), $i );
					else
						$this->errors[ 'no_terms' ] = sprintf( __( 'None of the selected terms could be deleted', 'webcomic' ) );
				}
			}
		}
		
		if ( 'bind_webcomic_file' == $_REQUEST[ 'action' ] || ( 'unbind_webcomic_file' == $_REQUEST[ 'action' ] && isset( $_REQUEST[ 'webcomic_key' ] ) ) ) {
			check_admin_referer( 'bind_webcomic_file' );
			
			$a  = ( 'bind_webcomic_file' == $_REQUEST[ 'action' ] ) ? __( 'bound to', 'webcomic' ) : __( 'unbound from', 'webcomic' );
			$m  = ( 'bind_webcomic_file' == $_REQUEST[ 'action' ] ) ? true : false;
			$wc = get_term( $_REQUEST[ 'webcomic_collection' ], 'webcomic_collection' );
			
			if ( !( $files = $this->fetch( $_REQUEST[ 'post' ], 'post', $wc->slug, $m ) ) )
				return false;
			
			if ( 'unbind_webcomic_file' == $_REQUEST[ 'action' ] )
				foreach ( $files as $s => $f )
					foreach ( $f as $k => $v )
						if ( $k == $_REQUEST[ 'webcomic_key' ] )
							unset( $files[ $s ][ $k ] );
			
			if ( $this->bind( $_REQUEST[ 'post' ], 'post', $files ) )
				$this->update[ 'bound_file' ] = sprintf( __( 'Files %s %s', 'webcomic' ), $a, get_the_title( $_REQUEST[ 'post' ] ) );
		}
		
		if ( 'regen_webcomic_file' == $_REQUEST[ 'action' ] && isset( $_REQUEST[ 'webcomic_key' ] ) ) {
			check_admin_referer( 'regen_webcomic_file' );
			
			$wc = ( $_REQUEST[ 'webcomic_collection' ] ) ? get_term( $_REQUEST[ 'webcomic_collection' ], 'webcomic_collection' ) : get_term( $_REQUEST[ 'webcomic_term' ], 'webcomic_collection' );
			
			if ( $_REQUEST[ 'post' ] ) {
				$id   = $_REQUEST[ 'post' ];
				$type = 'post';
				$match = true;
			} elseif ( $_REQUEST[ 'orphan' ] ) {
				$id   = $_REQUEST[ 'orphan' ];
				$type = 'orphan';
			}
			
			if ( $files = $this->regen( $id, $type, $wc->slug, $_REQUEST[ 'webcomic_key' ] ) )
				$this->update[ 'regen_thumbs' ] = sprintf( __( 'Thumbnails regenerated for %s', 'webcomic' ), implode( ', ', $files ) );
		}
		
		if ( 'delete_webcomic_file' == $_REQUEST[ 'action' ] && isset( $_REQUEST[ 'webcomic_key' ] ) ) {
			check_admin_referer( 'delete_webcomic_file' );
			
			$wc = ( $_REQUEST[ 'webcomic_collection' ] ) ? get_term( $_REQUEST[ 'webcomic_collection' ], 'webcomic_collection' ) : get_term( $_REQUEST[ 'webcomic_term' ], 'webcomic_collection' );
			
			if ( $_REQUEST[ 'post' ] ) {
				$id   = $_REQUEST[ 'post' ];
				$type = 'post';
			} elseif ( $_REQUEST[ 'orphan' ] ) {
				$id   = $_REQUEST[ 'orphan' ];
				$type = 'orphan';
			}
			
			if ( is_array( $files = $this->delete( $id, $type, $wc->slug, $_REQUEST[ 'webcomic_key' ] ) ) )
				$this->errors[ "no_delete" ] = sprintf( __( 'The following files could not be deleted:<br><br>%s', 'webcomic' ), implode( '<br>', $files ) );
			else
				$this->update[ 'deleted_file' ] = sprintf( __( 'Deleted %s', 'webcomic' ), $files );
		}
		
		if ( 'delete_webcomic_post' == $_REQUEST[ 'action' ] ) {
			check_admin_referer( 'delete_webcomic_post' );
			
			$s = get_post_status( $_REQUEST[ 'post' ] );
			$a = ( 'trash' == $s ) ? __( 'permanently deleted', 'webcomic' ) : __( 'moved to the trash', 'webcomic' );
			
			if ( 'trash' == $s && wp_delete_post( $_REQUEST[ 'post' ] ) )
				$this->update[ 'deleted_post' ] = __( 'Post permanently deleted', 'webcomic' );
			elseif ( wp_trash_post( $_REQUEST[ 'post' ] ) )
				$this->update[ 'deleted_post' ] = __( 'Post moved to the trash', 'webcomic' );
			else
				$this->errors[ 'no_delete' ] = sprintf( __( 'The post could not be %a', 'webcomic' ), $a );
		}
		
		if ( 'undelete_webcomic_post' == $_REQUEST[ 'action' ] ) {
			check_admin_referer( 'undelete_webcomic_post' );
			
			if ( wp_untrash_post( $_REQUEST[ 'post' ] ) )
				$this->update[ 'restored_post' ] = __( 'Post restored', 'webcomic' );
			else
				$this->errors[ 'no_undelete' ] = __( 'The post could not be restored', 'webcomic' );
		}
		
		if ( 'bulk_webcomic_file' == $_REQUEST[ 'action' ] ) {
			check_admin_referer( 'bulk_webcomic_file' );
			
			$type = $_REQUEST[ 'webcomic_type' ];
			
			if ( !empty( $_REQUEST[ 'bulk' ] ) && $action = ( !empty( $_REQUEST[ 'submit-1' ] ) ) ? $_REQUEST[ 'action-1' ] : $_REQUEST[ 'action-2' ] ) {
				if ( 'regen' == $action ) {
					$i  = 0;
					$wc = get_term( $_REQUEST[ 'webcomic_collection' ], 'webcomic_collection' );
					
					foreach ( $_REQUEST[ 'bulk' ] as $bulk )
						if ( $files = $this->regen( $bulk, $type, $wc->slug ) )
							$i += count( $files );
					
					if ( $i )
						$this->update[ 'regen_thumbs' ] = sprintf( _n( 'Thumbnails for %d file regenerated', 'Thumbnails for %d files regenerated', $i, 'webcomic' ), $i );
				} elseif ( 'bind' == $action || 'unbind' == $action ) {
					$i  = 0;
					$a  = ( 'bind' == $action ) ? __( 'bound to' ) : __( 'unbound from' );
					$wc = get_term( $_REQUEST[ 'webcomic_collection' ], 'webcomic_collection' );
					
					foreach ( $_REQUEST[ 'bulk' ] as $bulk ) {
						$files = ( 'bind' == $action ) ? $this->fetch( $bulk, 'post', $wc->slug, true ) : array();
						
						if ( $this->bind( $bulk, $type, $files ) )
							$i++;
					}
					
					if ( $i )
						$this->update[ 'bound_file' ] = sprintf( _n( 'Files %s %d post', 'Files %s %d posts', $i, 'webcomic' ), $a, $i );
				} elseif ( 'delete_file' == $action || 'delete_post' == $action || 'delete_filepost' == $action ) {
					$i  = $x = 0;
					$a = ( 'trash' == get_post_status( current( $_REQUEST[ 'bulk' ] ) ) ) ? __( 'permanently deleted', 'webcomic' ) : __( 'moved to the trash', 'webcomic' );
					$wc = get_term( $_REQUEST[ 'webcomic_collection' ], 'webcomic_collection' );
					$no_delete = array();
					
					foreach ( $_REQUEST[ 'bulk' ] as $bulk ) {
						if ( 'delete_file' == $action || 'delete_filepost' == $action ) {
							if ( is_array( $files = $this->delete( $bulk, $type, $wc->slug ) ) )
								foreach ( $files as $file )
									array_push( $no_delete, $file );
							elseif ( $files )
								$i += count( explode( ',', $files ) );
						}
						
						if ( 'delete_post' == $action || 'delete_filepost' == $action ) {
							if ( 'trash' == get_post_status( $bulk ) ) {
								if ( wp_delete_post( $bulk ) )
									$x++;
							} elseif ( wp_trash_post( $bulk ) )
								$x++;
						}
					}
					
					$output = array();
					$output[ 'files' ] = ( $i ) ? sprintf( _n( '%d file deleted', '%d files deleted', $i, 'webcomic' ), $i ) : sprintf( __( 'No files deleted', 'webcomic' ) );
					$output[ 'posts' ] = ( $x ) ? sprintf( _n( '%d post %s', '%d posts %s', $x, 'webcomic' ), $x, $a ) : $output[ 'posts' ] = sprintf( __( 'no posts %s', 'webcomic' ), $a );
					
					if ( 'delete_file' == $action && $i )
						$this->update[ 'deleted_files' ] = $output[ 'files' ];
					elseif ( 'delete_file' == $action )
						$this->errors[ 'no_files' ] = $output[ 'files' ];
					elseif ( 'delete_post' == $action && $x )
						$this->update[ 'deleted_posts' ] = $output[ 'posts' ];
					elseif ( 'delete_post' == $action )
						$this->errors[ 'no_posts' ] = ucfirst( $output[ 'posts' ] );
					elseif ( !$i && !$x )
						$this->errors[ 'no_filepost' ] = implode( ' and ', $output );
					else
						$this->update[ 'deteled_fileposts' ] = implode( ' and ', $output );
				} elseif ( 'undelete' == $action ) {
					$i = 0;
					
					foreach ( $_REQUEST[ 'bulk' ] as $bulk )
						if ( wp_untrash_post( $bulk ) )
							$i++;
					
					if ( $i )
						$this->update[ 'restored_posts' ] = sprintf( _n( '%d post restored', '%d posts restored', $i, 'webcomic' ), $i );
					else
						$this->errors[ 'no_posts' ] = sprintf( __( 'No posts restored', 'webcomic' ) );
				}
			} elseif ( 'orphan' == $type ) {
				$i  = 0;
				$wc = get_term( $_REQUEST[ 'webcomic_collection' ], 'webcomic_collection' );
				$no_rename = array();
				
				foreach ( $_REQUEST[ 'webcomic_filename' ] as $k => $v ) {
					if ( !$v || $v == $_REQUEST[ 'webcomic_oldname' ][ $k ] )
						continue;
					
					if ( is_array( $names = $this->rename( $_REQUEST[ 'webcomic_oldname' ][ $k ] . $_REQUEST[ 'webcomic_extension' ][ $k ], $type, $wc->slug, $v, 0 ) ) )
						foreach ( $names as $name )
							array_push( $no_rename, $name );
					else
						$i++;
				}
				
				if ( $i )
					$this->update[ 'renamed_files' ] = sprintf( _n( '%d file renamed', '%d files renamed', $i, 'webcomic' ), $i );
				
				if ( !empty( $no_rename ) )
					$this->errors[ "no_rename" ] = sprintf( __( 'The following files could not be renamed:<br><br>%s', 'webcomic' ), implode( '<br>', $no_rename ) );
			}
			
			if ( isset( $_REQUEST[ 'delete_all' ] ) && 'Empty Trash' == $_REQUEST[ 'delete_all' ] ) {
				global $wpdb;
				
				$p = $wpdb->get_col( "SELECT * FROM $wpdb->posts AS p WHERE p.post_type = 'webcomic_post' AND p.post_status = 'trash'" );
				
				foreach ( $p as $k )
					wp_delete_post( $k );
				
				$this->update[ 'empty_trash' ] = sprintf( _n( '%d post permanently deleted', '%d posts permanently deleted', count( $p ), 'webcomic' ), count( $p ) );
			}
		}
		
		if ( !empty( $_REQUEST[ 'bulk' ] ) && 'batch_webcomic_posts' == $_REQUEST[ 'action' ] ) {
			check_admin_referer( 'batch_webcomic_posts' );
			
			global $wpdb;
			
			$i  = 0;
			$st = ( 'add' == $_REQUEST[ 'webcomic_post_storylines_action' ] ) ? true : false;
			$ct = ( 'add' == $_REQUEST[ 'webcomic_post_characters_action' ] ) ? true : false;
			$ps = ( 'publish' == $_REQUEST[ 'webcomic_post_status' ] || 'private' == $_REQUEST[ 'webcomic_post_status' ] || 'pending' == $_REQUEST[ 'webcomic_post_status' ] || 'draft' == $_REQUEST[ 'webcomic_post_status' ] ) ? $_REQUEST[ 'webcomic_post_status' ] : false;
			$ss = ( 'on' == $_REQUEST[ 'webcomic_post_sticky' ] || 'off' == $_REQUEST[ 'webcomic_post_sticky' ] ) ? $_REQUEST[ 'webcomic_post_sticky' ] : false;
			$cs = ( 'open' == $_REQUEST[ 'webcomic_post_comments' ] || 'closed' == $_REQUEST[ 'webcomic_post_comments' ] ) ? $_REQUEST[ 'webcomic_post_comments' ] : false;
			$gs = ( 'open' == $_REQUEST[ 'webcomic_post_pings' ] || 'closed' == $_REQUEST[ 'webcomic_post_pings' ] ) ? $_REQUEST[ 'webcomic_post_pings' ] : false;
			$ts = ( 'open' == $_REQUEST[ 'webcomic_post_transcripts' ] || 'closed' == $_REQUEST[ 'webcomic_post_transcripts' ] ) ? $_REQUEST[ 'webcomic_post_transcripts' ] : false;
			$xs = ( 'open' == $_REQUEST[ 'webcomic_post_prints' ] || 'closed' == $_REQUEST[ 'webcomic_post_prints' ] ) ? $_REQUEST[ 'webcomic_post_prints' ] : false;
			$os = ( 'open' == $_REQUEST[ 'webcomic_post_originals' ] || 'closed' == $_REQUEST[ 'webcomic_post_originals' ] ) ? $_REQUEST[ 'webcomic_post_originals' ] : false;
			
			if ( !empty( $_REQUEST[ 'webcomic_post_storylines' ] ) )
				foreach ( ( array ) $_REQUEST[ 'webcomic_post_storylines' ] as $k => $v )
					$_REQUEST[ 'webcomic_post_storylines' ][ $k ] = ( int ) $v;
			
			if ( !empty( $_REQUEST[ 'webcomic_post_characters' ] ) )
				foreach ( ( array ) $_REQUEST[ 'webcomic_post_characters' ] as $k => $v )
					$_REQUEST[ 'webcomic_post_characters' ][ $k ] = ( int ) $v;
			
			foreach ( $_REQUEST[ 'bulk' ] as $bulk ) {
				$i++;
				
				if ( $ss ) {
					if ( 'on' == $ss )
						stick_post( $bulk );
					else
						unstick_post( $bulk );
				}
				
				if ( $ps ) $wpdb->update( $wpdb->posts, array( 'post_status' => $ps ), array( 'ID' => $bulk ) );
				if ( $cs ) $wpdb->update( $wpdb->posts, array( 'comment_status' => $cs ), array( 'ID' => $bulk ) );
				if ( $gs ) $wpdb->update( $wpdb->posts, array( 'ping_status' => $gs ), array( 'ID' => $bulk ) );
				
				if ( $ts || $xs || $os ) {
					$post_meta = current( get_post_meta( $bulk, 'webcomic' ) );
					
					if ( $ts )
						$post_meta[ 'transcribe_toggle' ] = ( 'open' == $_REQUEST[ 'webcomic_post_transcripts' ] ) ? true : false;
					
					if ( $xs )
						$post_meta[ 'paypal' ][ 'prints' ] = ( 'open' == $_REQUEST[ 'webcomic_post_prints' ] ) ? true : false;
					
					if ( $os )
						$post_meta[ 'paypal' ][ 'original' ] = ( 'open' == $_REQUEST[ 'webcomic_post_originals' ] ) ? true : false;
					
					update_post_meta( $bulk, 'webcomic', $post_meta );
				}
				
				$storylines = wp_get_object_terms( $bulk, 'webcomic_storyline', array( 'fields' => 'ids' ) );
				$characters = wp_get_object_terms( $bulk, 'webcomic_character', array( 'fields' => 'ids' ) );
				
				wp_update_term_count( $storylines, 'webcomic_storyline' );
				wp_update_term_count( $characters, 'webcomic_character' );
				
				if ( $_REQUEST[ 'webcomic_collection' ] != $_REQUEST[ 'webcomic_post_collection' ] ) {
					$this->bind( $bulk, 'post' );
					wp_delete_object_term_relationships( $bulk, array( 'webcomic_collection', 'webcomic_storyline', 'webcomic_character' ) );
					wp_set_object_terms( $bulk, ( int ) $_REQUEST[ 'webcomic_post_collection' ], 'webcomic_collection', false );
				}
				
				if ( !empty( $_REQUEST[ 'webcomic_post_storylines' ] ) )
					wp_set_object_terms( $bulk, $_REQUEST[ 'webcomic_post_storylines' ], 'webcomic_storyline', $st );
				elseif ( !$st )
					wp_delete_object_term_relationships( $bulk, array( 'webcomic_storyline' ) );
				
				if ( !empty( $_REQUEST[ 'webcomic_post_characters' ] ) )
					wp_set_object_terms( $bulk, $_REQUEST[ 'webcomic_post_characters' ], 'webcomic_character', $ct );
				elseif ( !$ct )
					wp_delete_object_term_relationships( $bulk, array( 'webcomic_character' ) );
			}
			
			if ( $i )
				$this->update[ 'batched_posts' ] = sprintf( _n( '%d post updated', '%d posts updated', $i, 'webcomic' ), $i );
		}
		
		if ( 'batch_webcomic_files' == $_REQUEST[ 'action' ] && !empty( $_REQUEST[ 'bulk' ] ) ) {
			check_admin_referer( 'batch_webcomic_files' );
			
			$i      = 0;
			$wc     = get_term( $_REQUEST[ 'webcomic_collection' ], 'webcomic_collection' );
			$week   = array( 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday' );
			$start  = $now = ( !empty( $_REQUEST[ 'webcomic_file_auto' ] ) ) ? false : strtotime( $_REQUEST[ 'aa' ] . '-' . $_REQUEST[ 'mm' ] . '-' . $_REQUEST[ 'jj' ] );
			$status = ( !empty( $_REQUEST[ 'webcomic_file_draft' ] ) ) ? 'draft' : 'publish';
			
			if ( $start ) {
				$today = date( 'N', $start );
				
				switch ( $_REQUEST[ 'webcomic_file_interval' ] ) {
					case 'day' : $days = array( 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday' ); break;
					case 'bus' : $days = array( 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday' ); break;
					case 'end' : $days = array( 6 => 'Saturday', 7 => 'Sunday' ); break;
					case 'mwf' : $days = array( 1 => 'Monday', 3 => 'Wednesday', 5 => 'Friday' ); break;
					case 'trd' : $days = array( 2 => 'Tuesday', 4 => 'Thursday' ); break;
					case 'week':
						if ( $_REQUEST[ 'days' ] ) {
							foreach ( $_REQUEST[ 'days' ] as $d ) {
								$a = explode( '/', $d );
								$days[ $a[ 0 ] ] = $a[ 1 ];
							}
						} else
							$days[ $today ] = $week[ $today ];
					break;
				}
			}
			
			foreach ( $_REQUEST[ 'bulk' ] as $bulk ) {
				$info = pathinfo( stripslashes( $bulk ) );
				
				if ( $start ) {
					$date = date( 'Y-m-d H:i:s', $now );
					$gmt  = get_gmt_from_date( $date );
					$today++;
					
					while ( $today < 9 ) {
						$today = ( 8 == $today ) ? 1 : $today;
						
						if ( isset( $days[ $today ] ) ) {
							$key = $today;
							break;
						}
						
						$today++;
					}	
					
					$now = strtotime( 'next ' . $days[ $key ], $now );
				} else {			
					$match = array();
					
					preg_match( '#(\d\d\d\d)[- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])#', $info[ 'filename' ], $match );
					
					if ( empty( $match ) )
						continue;
					
					$date = date( 'Y-m-d H:i:s', strtotime( $match[ 0 ] ) );
					$gmt  = get_gmt_from_date( $date );
				}
				
				if ( !is_wp_error( $new_post = wp_insert_post( array( 'post_type' => 'webcomic_post', 'post_title' => $info[ 'filename' ], 'post_content' => '&hellip;', 'post_status' => $status, 'post_date' => $date, 'post_date_gmt' => $gmt ) ) ) ) {
					$files = $this->fetch( $bulk, 'orphan', $wc->slug );
					
					wp_set_object_terms( $new_post, ( int ) $wc->term_id, 'webcomic_collection', false );
					
					$this->bind( $new_post, 'post', $files );
					
					$post_meta = current( get_post_meta( $new_post, 'webcomic' ) );
					$post_meta[ 'alternate' ]         = array();
					$post_meta[ 'description' ]       = array();
					$post_meta[ 'transcripts' ]       = array();
					$post_meta[ 'transcribe_toggle' ] = $this->option( 'transcribe_toggle' );
					$post_meta[ 'paypal' ] = array(
						'prints'       => $wc->webcomic_paypal,
						'original'     => true,
						'price_d'      => 0,
						'price_i'      => 0,
						'price_o'      => 0,
						'shipping_d'   => 0,
						'shipping_i'   => 0,
						'shipping_o'   => 0
					);
					
					update_post_meta( $new_post, 'webcomic', $post_meta );
					
					$i++;
				}
			}
			
			if ( $i )
				$this->update[ 'batched_files' ] = sprintf( _n( 'Post generated for %d file', 'Posts generated for %d files', $i, 'webcomic' ), $i );
			elseif ( !$t && !$i )
				$this->errors[ 'no_publish' ] = __( 'No files could be automatically published', 'webcomic' );
		}
		
		if ( 'bulk_webcomic_upload' == $_REQUEST[ 'action' ] ) {
			check_admin_referer( 'bulk_webcomic_upload' );
			
			$wc = get_term( $_REQUEST[ 'webcomic_collection' ], 'webcomic_collection' );
			
			if ( is_array( $files = $this->upload( $wc->slug, 'bulk' ) ) ) {
				$this->update[ 'upload_success' ] = sprintf( _n( '%d file uploaded to %s', '%d files uploaded to %s', count( $files[ 'full' ] ), 'webcomic' ), count( $files[ 'full' ] ), $wc->name );
				$_REQUEST[ 'uploaded_webcomic_files' ] = $files[ 'full' ];
			}
		}
		
		if ( 'edit_webcomic_files' == $_REQUEST[ 'action' ] ) {
			check_admin_referer( 'edit_webcomic_files' );
			
			if ( !empty( $_REQUEST[ 'webcomic_delete' ] ) ) {
				foreach ( $_REQUEST[ 'webcomic_delete' ] as $k => $v ) {
					if ( 'full' == $k )
						continue;
					
					if ( is_array( $files = $this->delete( $_REQUEST[ 'id' ], $_REQUEST[ 'type' ], $_REQUEST[ 'src' ], $_REQUEST[ 'key' ], $k ) ) )
						$this->errors[ "no_delete_$k" ] = sprintf( __( 'The following files could not be deleted:<br><br>%s', 'webcomic' ), implode( '<br>', $files ) );
					else
						$this->update[ "delete_$k" ] = sprintf( __( 'Deleted %s size', 'webcomic' ), $k );
				}
			}
			
			if ( is_array( $files = $this->upload( $_REQUEST[ 'src' ], 'singular' ) ) )
				$this->bind( $_REQUEST[ 'id' ], $_REQUEST[ 'type' ], $files, true );
					
			$this->update[ 'edit_files' ] = sprintf( __( 'If you uploaded any new files you may need to <a href="%s">go back and manually refresh the page</a> to see them.', 'webcomic' ), $_REQUEST[ 'referer' ] );
		}
		
		if ( 'upgrade_legacy_webcomic' == $_REQUEST[ 'action' ] ) {
			check_admin_referer( 'upgrade_legacy_webcomic' );
			
			$this->upgrade_legacy( ( int ) $_REQUEST[ 'step' ] );
		}
		
		if ( 'uninstall_webcomic' == $_REQUEST[ 'action' ] ) {
			check_admin_referer( 'uninstall_webcomic' );
			
			$this->uninstall();
		}
		
		if ( 'webcomic_settings' == $_REQUEST[ 'action' ] ) {
			check_admin_referer( 'webcomic_settings' );
			
			$a = explode( '/', $_REQUEST[ 'transcribe_default' ] );
			$def_language  = array( $a[ 0 ] => $a[ 1 ] );
			$new_languages = array();
			
			if ( !empty( $_REQUEST[ 'transcribe_language' ] ) ) {
				foreach ( $_REQUEST[ 'transcribe_language' ] as $l ) {
					$a = explode( '/', $l );
					$new_languages[ $a[ 0 ] ] = $a[ 1 ];
				}
			}
			
			$new_languages = array_merge( $new_languages, $def_language );
			natcasesort( $new_languages );
			
			$new[ 'version' ]             = $this->version;
			$new[ 'default_collection' ]  = $this->option( 'default_collection' );
			$new[ 'integrate_toggle' ]    = ( isset( $_POST[ 'integrate_toggle' ] ) ) ? true : false;
			$new[ 'secure_toggle' ]       = ( isset( $_POST[ 'secure_toggle' ] ) ) ? true : false;
			$new[ 'transcribe_toggle' ]   = ( isset( $_POST[ 'transcribe_toggle' ] ) ) ? true : false;
			$new[ 'transcribe_restrict' ] = $_POST[ 'transcribe_restrict' ];
			$new[ 'transcribe_language' ] = $new_languages;
			$new[ 'transcribe_default' ]  = $def_language;
			$new[ 'feed_toggle' ]         = ( isset( $_POST[ 'feed_toggle' ] ) ) ? true : false;
			$new[ 'feed_size' ]           = $_POST[ 'feed_size' ];
			$new[ 'buffer_toggle' ]       = ( isset( $_POST[ 'buffer_toggle' ])  ) ? true : false;
			$new[ 'buffer_size' ]         = abs( intval( $_POST[ 'buffer_size' ] ) );
			$new[ 'age_toggle' ]          = ( isset( $_POST[ 'age_toggle' ] ) ) ? true : false;
			$new[ 'age_size' ]            = abs( intval( $_POST[ 'age_size' ] ) );
			$new[ 'shortcut_toggle' ]     = ( isset( $_POST[ 'shortcut_toggle' ] ) ) ? true : false;
			$new[ 'paypal_business' ]     = $_POST[ 'paypal_business' ];
			$new[ 'paypal_currency' ]     = $_POST[ 'paypal_currency' ];
			$new[ 'paypal_log' ]          = ( isset( $_POST[ 'paypal_log' ] ) ) ? true : false;
			$new[ 'paypal_prints' ]       = ( isset( $_POST[ 'paypal_prints' ] ) ) ? true : false;
			$new[ 'paypal_method' ]       = $_POST[ 'paypal_method' ];
			$new[ 'paypal_price_d' ]      = round( abs( floatval( $_POST[ 'paypal_price_d' ] ) ), 2 );
			$new[ 'paypal_price_i' ]      = round( abs( floatval( $_POST[ 'paypal_price_i' ] ) ), 2 );
			$new[ 'paypal_price_o' ]      = round( abs( floatval( $_POST[ 'paypal_price_o' ] ) ), 2 );
			$new[ 'paypal_shipping_d' ]   = round( abs( floatval( $_POST[ 'paypal_shipping_d' ] ) ), 2 );
			$new[ 'paypal_shipping_i' ]   = round( abs( floatval( $_POST[ 'paypal_shipping_i' ] ) ), 2 );
			$new[ 'paypal_shipping_o' ]   = round( abs( floatval( $_POST[ 'paypal_shipping_o' ] ) ), 2 );
			$new[ 'paypal_donation' ]     = round( abs( floatval( $_POST[ 'paypal_donation' ] ) ), 2 );
			$new[ 'large_h' ]             = abs( intval( $_POST[ 'large_h' ] ) );
			$new[ 'large_w' ]             = abs( intval( $_POST[ 'large_w' ] ) );
			$new[ 'medium_h' ]            = abs( intval( $_POST[ 'medium_h' ] ) );
			$new[ 'medium_w' ]            = abs( intval( $_POST[ 'medium_w' ] ) );
			$new[ 'small_h' ]             = abs( intval( $_POST[ 'small_h' ] ) );
			$new[ 'small_w' ]             = abs( intval( $_POST[ 'small_w' ] ) );
			$new[ 'term_meta' ]           = $this->option( 'term_meta' );
			
			$this->option( $new );
			$this->update[ 'settings' ] = __( 'Settings saved', 'webcomic' );
		}
	}
	
	/**
	 * 'profile_update' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_profile_update( $id ) {
		$user_meta = ( current( get_user_meta( $id, 'webcomic' ) ) ) ? current( get_user_meta( $id, 'webcomic' ) ) : array();
		
		$user_meta[ 'birthday' ] = ( $_REQUEST[ 'webcomic_birth_year' ] && $_REQUEST[ 'webcomic_birth_month' ] && $_REQUEST[ 'webcomic_birth_day' ] ) ? strtotime( $_REQUEST[ 'webcomic_birth_year' ] . '/' . $_REQUEST[ 'webcomic_birth_month' ] . '/' . $_REQUEST[ 'webcomic_birth_day' ] ) : '';
		
		update_user_meta( $id, 'webcomic', $user_meta );
	}
	
	/**
	 * 'user_profile' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_show_user_profile( $user ) {
		$this->domain();
		
		$user_meta = current( get_user_meta( $user->ID, 'webcomic' ) );
		$birthday  = ( $time = $user_meta[ 'birthday' ] ) ? explode( ' ', date( 'Y n j', $time ) ) : array();
		?>
		<h3><?php _e( 'Webcomic', 'webcomic' ); ?></h3>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="webcomic_birth_year"><?php _e( 'Birthday' ); ?></label></th>
				<td>
					<select name="webcomic_birth_day">
						<?php $i = 1; while ( $i <= 31 ) { ?>
						<option value="<?php echo $i; ?>"<?php if ( $i == $birthday[ 2 ] ) echo ' selected'; ?>><?php echo $i; ?></option>
						<?php $i++; } ?>
					</select>
					<select name="webcomic_birth_month">
						<option value="1"<?php if ( 1 == $birthday[ 1 ] ) echo ' selected'; ?>><?php _e( 'January', 'webcomic' ); ?></option>
						<option value="2"<?php if ( 2 == $birthday[ 1 ] ) echo ' selected'; ?>><?php _e( 'February', 'webcomic' ); ?></option>
						<option value="3"<?php if ( 3 == $birthday[ 1 ] ) echo ' selected'; ?>><?php _e( 'March', 'webcomic' ); ?></option>
						<option value="4"<?php if ( 4 == $birthday[ 1 ] ) echo ' selected'; ?>><?php _e( 'April', 'webcomic' ); ?></option>
						<option value="5"<?php if ( 5 == $birthday[ 1 ] ) echo ' selected'; ?>><?php _e( 'May', 'webcomic' ); ?></option>
						<option value="6"<?php if ( 6 == $birthday[ 1 ] ) echo ' selected'; ?>><?php _e( 'June', 'webcomic' ); ?></option>
						<option value="7"<?php if ( 7 == $birthday[ 1 ] ) echo ' selected'; ?>><?php _e( 'July', 'webcomic' ); ?></option>
						<option value="8"<?php if ( 8 == $birthday[ 1 ] ) echo ' selected'; ?>><?php _e( 'August', 'webcomic' ); ?></option>
						<option value="9"<?php if ( 9 == $birthday[ 1 ] ) echo ' selected'; ?>><?php _e( 'September', 'webcomic' ); ?></option>
						<option value="10"<?php if ( 10 == $birthday[ 1 ] ) echo ' selected'; ?>><?php _e( 'October', 'webcomic' ); ?></option>
						<option value="11"<?php if ( 11 == $birthday[ 1 ] ) echo ' selected'; ?>><?php _e( 'November', 'webcomic' ); ?></option>
						<option value="12"<?php if ( 12 == $birthday[ 1 ] ) echo ' selected'; ?>><?php _e( 'December', 'webcomic' ); ?></option>
					</select>
					<select name="webcomic_birth_year">
						<?php $i = intval( date( 'Y' ) ); while ( $i >= intval( date( 'Y' ) ) - 80 ) { ?>
						<option value="<?php echo $i; ?>"<?php if ( $i == $birthday[ 0 ] ) echo ' selected'; ?>><?php echo $i; ?></option>
						<?php $i--; } ?>
					</select>
					<span class="description"><?php _e( 'Your birthday is used for age verification.', 'webcomic' ); ?></span>
				</td>
			</tr>
		</table>
		<?php
	}
	
	
	
	////
	// Hooks - Posts
	// 
	// These functions hook into various WordPress
	// post actions and should never be called
	// directly.
	////
	
	/**
	 * 'save_post' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_save_post( $id, $post ) {
		if ( empty( $_REQUEST[ 'original_publish' ] ) || wp_is_post_autosave( $id ) || wp_is_post_revision( $id ) )
			return false;
		
		global $current_user;
		
		if ( !empty( $_REQUEST[ 'webcomic_collection' ] ) && 'webcomic_post' == get_post_type( $id ) ) {
			if ( !empty( $_REQUEST[ 'webcomic_storyline' ] ) )
				foreach ( ( array ) $_REQUEST[ 'webcomic_storyline' ] as $k => $v )
					$_REQUEST[ 'webcomic_storyline' ][ $k ] = ( int ) $v;
				
			if ( !empty( $_REQUEST[ 'webcomic_character' ] ) )
				foreach ( ( array ) $_REQUEST[ 'webcomic_character' ] as $k => $v )
					$_REQUEST[ 'webcomic_character' ][ $k ] = ( int ) $v;
			
			wp_set_object_terms( $id, ( int ) $_REQUEST[ 'webcomic_collection' ], 'webcomic_collection', false );
			wp_set_object_terms( $id, $_REQUEST[ 'webcomic_storyline' ], 'webcomic_storyline', false );
			wp_set_object_terms( $id, $_REQUEST[ 'webcomic_character' ], 'webcomic_character', false );
			
			$wc = get_term( $_REQUEST[ 'webcomic_collection' ], 'webcomic_collection' );
			$p  = ( $_REQUEST[ 'webcomic_paypal_prints' ] ) ? true : false;
			$o  = ( $_REQUEST[ 'webcomic_paypal_original' ] ) ? false : true;
			$a  = ( 'sub' == $_REQUEST[ 'webcomic_paypal_price_type_d' ] ) ? intval( 0 - $_REQUEST[ 'webcomic_paypal_price_d' ] ) : intval( $_REQUEST[ 'webcomic_paypal_price_d' ] );
			$b  = ( 'sub' == $_REQUEST[ 'webcomic_paypal_price_type_i' ] ) ? intval( 0 - $_REQUEST[ 'webcomic_paypal_price_i' ] ) : intval( $_REQUEST[ 'webcomic_paypal_price_i' ] );
			$c  = ( 'sub' == $_REQUEST[ 'webcomic_paypal_price_type_o' ] ) ? intval( 0 - $_REQUEST[ 'webcomic_paypal_price_o' ] ) : intval( $_REQUEST[ 'webcomic_paypal_price_o' ] );
			$d  = ( 'sub' == $_REQUEST[ 'webcomic_paypal_shipping_type_d' ] ) ? intval( 0 - $_REQUEST[ 'webcomic_paypal_shipping_d' ] ) : intval( $_REQUEST[ 'webcomic_paypal_shipping_d' ] );
			$e  = ( 'sub' == $_REQUEST[ 'webcomic_paypal_shipping_type_i' ] ) ? intval( 0 - $_REQUEST[ 'webcomic_paypal_shipping_i' ] ) : intval( $_REQUEST[ 'webcomic_paypal_shipping_i' ] );
			$f  = ( 'sub' == $_REQUEST[ 'webcomic_paypal_shipping_type_o' ] ) ? intval( 0 - $_REQUEST[ 'webcomic_paypal_shipping_o' ] ) : intval( $_REQUEST[ 'webcomic_paypal_shipping_o' ] );
			
			$post_meta = current( get_post_meta( $id, 'webcomic' ) );
			$post_meta[ 'files' ]             = ( $_REQUEST[ 'current_webcomic_collection' ] == $_REQUEST[ 'webcomic_collection' ] ) ? $post_meta[ 'files' ] : array();
			$post_meta[ 'alternate' ]         = ( $_REQUEST[ 'current_webcomic_collection' ] == $_REQUEST[ 'webcomic_collection' ] ) ? $post_meta[ 'alternate' ] : array();
			$post_meta[ 'description' ]       = ( $_REQUEST[ 'current_webcomic_collection' ] == $_REQUEST[ 'webcomic_collection' ] ) ? $post_meta[ 'description' ] : array();
			$post_meta[ 'transcripts' ]       = array();
			$post_meta[ 'transcribe_toggle' ] = ( $_REQUEST[ 'webcomic_transcribe_toggle' ] ) ? true : false;
			$post_meta[ 'paypal' ] = array(
				'prints'       => $p,
				'original'     => $o,
				'price_d'      => $a,
				'price_i'      => $b,
				'price_o'      => $c,
				'shipping_d'   => $d,
				'shipping_i'   => $e,
				'shipping_o'   => $f
			);
			
			if ( $_REQUEST[ 'webcomic_alternate' ] )
				foreach ( $_REQUEST[ 'webcomic_alternate' ] as $k => $v )
					$post_meta[ 'alternate' ][ $k ] = $v;
			
			if ( $_REQUEST[ 'webcomic_description' ] )
				foreach ( $_REQUEST[ 'webcomic_description' ] as $k => $v )
					$post_meta[ 'description' ][ $k ] = $v;
			
			$languages = $this->option( 'transcribe_language' );
			
			foreach ( $languages as $k => $v ) {
				if ( 'delete' == $_REQUEST[ 'webcomic_transcript_action' ][ $k ] || !$_REQUEST[ 'webcomic_transcript' ][ $k ] )
					continue;
				
				$post_meta[ 'transcripts' ][ $k ][ 'language_code' ] = $k;
				$post_meta[ 'transcripts' ][ $k ][ 'language' ]      = $v;
				$post_meta[ 'transcripts' ][ $k ][ 'status' ]        = ( 'restore' == $_REQUEST[ 'webcomic_transcript_action' ][ $k ] ) ? 'draft' : $_REQUEST[ 'webcomic_transcript_action' ][ $k ];
				$post_meta[ 'transcripts' ][ $k ][ 'author' ]        = ( 'restore' == $_REQUEST[ 'webcomic_transcript_action' ][ $k ] ) ? $_REQUEST[ 'webcomic_transcript_backup_author' ][ $k ] : $_REQUEST[ 'webcomic_transcript_author' ][ $k ];
				$post_meta[ 'transcripts' ][ $k ][ 'time' ]          = ( 'restore' == $_REQUEST[ 'webcomic_transcript_action' ][ $k ] ) ? $_REQUEST[ 'webcomic_transcript_backup_time' ][ $k ] : $_REQUEST[ 'webcomic_transcript_time' ][ $k ];
				$post_meta[ 'transcripts' ][ $k ][ 'text' ]          = ( 'restore' == $_REQUEST[ 'webcomic_transcript_action' ][ $k ] ) ? $_REQUEST[ 'webcomic_transcript_backup' ][ $k ] : $_REQUEST[ 'webcomic_transcript' ][ $k ];
				
				if ( $current_user->data->rich_editing )
					$post_meta[ 'transcripts' ][ $k ][ 'text' ] = wpautop( $post_meta[ 'transcripts' ][ $k ][ 'text' ] );
				
				if ( 'draft' == $_REQUEST[ 'webcomic_transcript_action' ][ $k ] && $_REQUEST[ 'webcomic_transcript_backup' ][ $k ] ) {
					$post_meta[ 'transcripts' ][ $k ][ 'backup' ]        = $_REQUEST[ 'webcomic_transcript_backup' ][ $k ];
					$post_meta[ 'transcripts' ][ $k ][ 'backup_time' ]   = $_REQUEST[ 'webcomic_transcript_backup_time' ][ $k ];
					$post_meta[ 'transcripts' ][ $k ][ 'backup_author' ] = $_REQUEST[ 'webcomic_transcript_backup_author' ][ $k ];
				}
			}
			
			$files = ( $_REQUEST[ 'current_webcomic_collection' ] == $_REQUEST[ 'webcomic_collection' ] ) ? $this->fetch( $id, 'post', $wc->slug, true ) : array();
			
			if ( $_REQUEST[ 'webcomic_orphan' ] ) {
				$tabs  = $this->directory( 'abs', $wc->slug . '/thumbs' );
				
				foreach ( $_REQUEST[ 'webcomic_orphan' ] as $orphan ) {
					$info  = pathinfo( stripslashes( $orphan ) );
					$files[ 'full' ][] = $info[ 'basename' ];
					
					if ( is_file( $tabs . $info[ 'filename' ] . '-small.' . $info[ 'extension' ] ) )
						$files[ 'small' ][] = $info[ 'filename' ] . '-small.' . $info[ 'extension' ];
					if ( is_file( $tabs . $info[ 'filename' ] . '-medium.' . $info[ 'extension' ] ) )
						$files[ 'medium' ][] = $info[ 'filename' ] . '-medium.' . $info[ 'extension' ];
					if ( is_file( $tabs . $info[ 'filename' ] . '-large.' . $info[ 'extension' ] ) )
						$files[ 'large' ][] = $info[ 'filename' ] . '-large.' . $info[ 'extension' ];
				}
				
				natcasesort( $files[ 'full' ] );
				
				$post_meta[ 'files' ] = $files;
			}
			
			update_post_meta( $id, 'webcomic', $post_meta );
			
			if ( is_array( $files = $this->upload( $wc->slug, 'bulk' ) ) )
				$this->bind( $id, 'post', $files, false );
			elseif ( !empty( $files ) && !$_REQUEST[ 'webcomic_orphan' ] ) {
				if ( isset( $_REQUEST[ 'webcomic_filename' ] ) )
					foreach ( $_REQUEST[ 'webcomic_filename' ] as $k => $v )
						if ( $v && $files[ 'full' ][ $k ] != $v . $_REQUEST[ 'webcomic_extension' ][ $k ] && is_array( $names = $this->rename( $id, 'post', $wc->slug, $v, $k ) ) )
							$this->errors[ "no_rename_$k" ] = sprintf( __( 'The following files could not be renamed:<br><br>%s', 'webcomic' ), implode( '<br>', $names ) );
				
				if ( !empty( $_REQUEST[ 'webcomic_action' ] ) ) {
					foreach ( $_REQUEST[ 'webcomic_action' ] as $k => $v ) {
						$files = $this->fetch( $id, 'post', $wc->slug, true );
						
						if ( 'regen' == $v )
							$this->regen( $id, 'post', $wc->slug, $k );
						elseif ( 'bind' == $v )
							$this->bind( $id, 'post', $files );
						elseif ( 'unbind' == $v ) {
							foreach ( $files as $s => $f )
								unset( $files[ $s ][ $k ] );
							
							$this->bind( $id, 'post', $files );
						} elseif ( 'delete' == $v && is_array( $files = $this->delete( $id, 'post', $wc->slug, $k ) ) )
							$this->errors[ "no_delete_$k" ] = sprintf( __( 'The following files could not be deleted:<br><br>%s', 'webcomic' ), implode( '<br>', $files ) );
					}
				}
			}
		} else {
			wp_delete_object_term_relationships( $id, array( 'webcomic_collection', 'webcomic_storyline', 'webcomic_character' ) );
			delete_post_meta( $id, 'webcomic' );
		}
	}
	
	
	
	////
	// Hooks - Taxonomy
	// 
	// These functions hook into various WordPress
	// taxonomy actions and should never be called
	// directly.
	////
	
	/**
	 * 'created_webcomic_collection' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_created_webcomic_collection( $term_id, $tt_id ) {
		$this->domain();
		
		$term_meta = $this->option( 'term_meta' );
		$term = get_term( $term_id, 'webcomic_collection' );
		$tabs = $this->directory( 'abs', $term->slug . '/thumbs' );
		
		if ( !@mkdir( $tabs, 0755, true ) )
			$this->errors[ 'no_directory' ] = sprintf( __( 'The collection directory could not be created. You will need to create the following directory (if it does not already exist) before you can manage webcomics for this collection: %s', 'webcomic' ), $tabs );
		
		$term_meta[ 'collection' ][ $term_id ] = array(
			'files'    => array(),
			'slug'     => $term->slug,
			'theme'    => false,
			'restrict' => false,
			'bookend'  => array(
				'first' => false,
				'last'  => false
			),
			'paypal'   => array(
				'prints'     => $this->option( 'paypal_prints' ),
				'price_d'    => 0,
				'price_i'    => 0,
				'price_o'    => 0,
				'shipping_d' => 0,
				'shipping_i' => 0,
				'shipping_o' => 0
			)
		); $this->option( 'term_meta', $term_meta );
	}
	
	/**
	 * 'created_webcomic_storyline' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_created_webcomic_storyline( $term_id, $tt_id ) {
		$term_meta = $this->option( 'term_meta' );
		$term_meta[ 'storyline' ][ $term_id ] = array(
			'files'   => array(),
			'order'   => count( get_terms( 'webcomic_storyline', 'hide_empty=0&webcomic_order=1&parent=' . $_REQUEST[ 'webcomic_parent' ] . '&term_group=' . $_REQUEST[ 'webcomic_collection' ] ) ) + 1,
			'parent'  => $_REQUEST[ 'webcomic_parent' ],
			'group'   => $_REQUEST[ 'webcomic_collection' ],
			'default' => false
		); $this->option( 'term_meta', $term_meta );
		
		wp_update_term( $term_id, 'webcomic_storyline', array( 'term_group' => $_REQUEST[ 'webcomic_collection' ] ) );
	}
	
	/**
	 * 'created_webcomic_character' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_created_webcomic_character( $term_id, $tt_id ) {
		$term_meta = $this->option( 'term_meta' );
		$term_meta[ 'character' ][ $term_id ] = array(
			'files'   => array(),
			'group'   => $_REQUEST[ 'webcomic_collection' ],
			'default' => false
		); $this->option( 'term_meta', $term_meta );
		
		wp_update_term( $term_id, 'webcomic_character', array( 'term_group' => $_REQUEST[ 'webcomic_collection' ] ) );
	}
	
	/**
	 * 'edited_term' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_edited_term( $term_id, $tt_id, $taxonomy ) {
		$this->domain();
		
		if ( 'webcomic_collection' == $taxonomy || 'webcomic_storyline' == $taxonomy || 'webcomic_character' == $taxonomy ) {
			$term_meta = $this->option( 'term_meta' );
			$term = get_term( $term_id, $taxonomy );
			$type = end( explode( '_', $taxonomy ) );
			$key  = ( 'collection' == $type ) ? $term->term_id : $term->term_group;
			
			if ( !empty( $term_meta[ $type ][ $term_id ][ 'files' ] ) ) {
				foreach ( $_REQUEST[ 'webcomic_filename' ] as $k => $v )
					if ( $v && $term_meta[ $type ][ $term_id ][ 'files' ][ 'full' ][ $k ] != $v . $_REQUEST[ 'webcomic_extension' ][ $k ] && is_array( $names = $this->rename( $term_id, $type, $term_meta[ 'collection' ][ $key ][ 'slug' ], $v, $k ) ) )
						$this->errors[ "no_rename_$k" ] = sprintf( __( 'The following files could not be renamed:<br><br>%s', 'webcomic' ), implode( '<br>', $names ) );
				
				foreach ( $_REQUEST[ 'webcomic_action' ] as $k => $v ) {
					if ( 'regen' == $v )
						$this->regen( $term_id, $type, $term_meta[ 'collection' ][ $key ][ 'slug' ], $k );
					elseif ( 'delete' == $v && is_array( $files = $this->delete( $term_id, $type, $term_meta[ 'collection' ][ $key ][ 'slug' ], $k ) ) )
						$this->errors[ "no_delete_$k" ] = sprintf( __( 'The following files could not be deleted:<br><br>%s', 'webcomic' ), implode( '<br>', $files ) );
				}
			}
		
			if ( is_array( $files = $this->upload( $term_meta[ 'collection' ][ $key ][ 'slug' ] ) ) )
				$this->bind( $term_id, $type, $files, false );
		}
	}
	
	/**
	 * 'edited_webcomic_collection' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_edited_webcomic_collection( $term_id, $tt_id ) {
		$this->domain();
		
		$term_meta = $this->option( 'term_meta' );
		$term = get_term( $term_id, 'webcomic_collection' );
		$nabs = $this->directory( 'abs', $term->slug );
		$oabs = $this->directory( 'abs', $term_meta[ 'collection' ][ $term_id ][ 'slug' ] );
		
		if ( $nabs != $oabs )
			if ( !rename( $oabs, $nabs ) )
				$this->errors[ 'no_rename' ] = sprintf( __( 'The directory for this collection could not be renamed. You will need to rename %s to %s', 'webcomic' ), $oabs, $nabs );
		
		$p = ( !empty( $_REQUEST[ 'webcomic_paypal_prints' ] ) ) ? true : false;
		$a = ( 'sub' == $_REQUEST[ 'webcomic_paypal_price_type_d' ] ) ? intval( 0 - $_REQUEST[ 'webcomic_paypal_price_d' ] ) : intval( $_REQUEST[ 'webcomic_paypal_price_d' ] );
		$b = ( 'sub' == $_REQUEST[ 'webcomic_paypal_price_type_i' ] ) ? intval( 0 - $_REQUEST[ 'webcomic_paypal_price_i' ] ) : intval( $_REQUEST[ 'webcomic_paypal_price_i' ] );
		$c = ( 'sub' == $_REQUEST[ 'webcomic_paypal_price_type_o' ] ) ? intval( 0 - $_REQUEST[ 'webcomic_paypal_price_o' ] ) : intval( $_REQUEST[ 'webcomic_paypal_price_o' ] );
		$d = ( 'sub' == $_REQUEST[ 'webcomic_paypal_shipping_type_d' ] ) ? intval( 0 - $_REQUEST[ 'webcomic_paypal_shipping_d' ] ) : intval( $_REQUEST[ 'webcomic_paypal_shipping_d' ] );
		$e = ( 'sub' == $_REQUEST[ 'webcomic_paypal_shipping_type_i' ] ) ? intval( 0 - $_REQUEST[ 'webcomic_paypal_shipping_i' ] ) : intval( $_REQUEST[ 'webcomic_paypal_shipping_i' ] );
		$f = ( 'sub' == $_REQUEST[ 'webcomic_paypal_shipping_type_o' ] ) ? intval( 0 - $_REQUEST[ 'webcomic_paypal_shipping_o' ] ) : intval( $_REQUEST[ 'webcomic_paypal_shipping_o' ] );
		
		$term_meta[ 'collection' ][ $term_id ][ 'slug' ]     = $term->slug;
		$term_meta[ 'collection' ][ $term_id ][ 'theme' ]    = ( empty( $_REQUEST[ 'webcomic_theme' ] ) ) ? false : $_REQUEST[ 'webcomic_theme' ];
		$term_meta[ 'collection' ][ $term_id ][ 'restrict' ] = ( isset( $_REQUEST[ 'webcomic_restrict' ] ) ) ? true : false;
		$term_meta[ 'collection' ][ $term_id ][ 'bookend' ]  = array(
			'first' => $_REQUEST[ 'webcomic_bookend_first' ],
			'last'  => $_REQUEST[ 'webcomic_bookend_last' ]
		);
		$term_meta[ 'collection' ][ $term_id ][ 'paypal' ] = array(
			'prints'     => $p,
			'price_d'    => $a,
			'price_i'    => $b,
			'price_o'    => $c,
			'shipping_d' => $d,
			'shipping_i' => $e,
			'shipping_o' => $f
		);
		
		$this->option( 'term_meta', $term_meta );
	}
	
	/**
	 * 'edited_webcomic_storyline' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_edited_webcomic_storyline( $term_id, $tt_id ) {
		$term_meta = $this->option( 'term_meta' );
		$term = get_term( $term_id, 'webcomic_storyline' );
		
		if ( $term->parent != $term_meta[ 'storyline' ][ $term_id ][ 'parent' ] ) {
			$i = 1;
			
			if ( $old_sibs = get_terms( 'webcomic_storyline', 'hide_empty=0&webcomic_order=1&parent=' . $term_meta[ 'storyline' ][ $term_id ][ 'parent' ] . '&term_group=' . $term->term_group ) ) {
				foreach ( $old_sibs as $sib ) {
					$term_meta[ 'storyline' ][ $sib->term_id ][ 'order' ] = $i;
					$i++;
				}
				
				$i = 1;
			}
			
			if ( $new_sibs = get_terms( 'webcomic_storyline', 'hide_empty=0&webcomic_order=1&parent=' . $term->parent . '&term_group=' . $term->term_group ) ) {
				foreach ( $new_sibs as $sib ) {
					if ( $sib->term_id == $term_id )
						continue;
					
					$term_meta[ 'storyline' ][ $sib->term_id ][ 'order' ] = $i;
					$i++;
				}
				
				$term_meta[ 'storyline' ][ $term_id ][ 'order' ] = $i;
			}
			
			$term_meta[ 'storyline' ][ $term_id ][ 'parent' ] = $term->parent;
			$this->option( 'term_meta', $term_meta );
		}
	}
	
	/**
	 * 'delete_webcomic_collection' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_delete_webcomic_collection( $term_id, $tt_id ) {
		$this->domain();
		
		global $wpdb;
		
		$term_meta = $this->option( 'term_meta' );
		$abs       = $this->directory( 'abs', $term_meta[ 'collection' ][ $term_id ][ 'slug' ] );
		$tabs      = $abs . 'thumbs/';
		$terms     = get_terms( array( 'webcomic_storyline', 'webcomic_character' ), 'hide_empty=0&term_group=' . $term_id );
		
		if ( $terms )
			foreach ( $terms as $term )
				wp_delete_term( $term->term_id, $term->taxonomy );
		
		if ( is_array( $files = $this->delete( $term_id, 'collection', $term_meta[ 'collection' ][ $term_id ][ 'slug' ] ) ) )
			$this->errors[ "no_delete_$term_id" ] = sprintf( __( 'The following files could not be deleted:<br><br>%s', 'webcomic' ), implode( '<br>', $files ) );
		
		unset( $term_meta[ 'collection' ][ $term_id ] );
		$this->option( 'term_meta', $term_meta );
	}
	
	/**
	 * 'delete_webcomic_storyline' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_delete_webcomic_storyline( $term_id, $tt_id ) {
		$this->domain();
		
		$term_meta  = $this->option( 'term_meta' );
		$term_group = $term_meta[ 'storyline' ][ $term_id ][ 'group' ];
		
		if ( is_array( $files = $this->delete( $term_id, 'storyline', $term_meta[ 'collection' ][ $term_group ][ 'slug' ] ) ) )
			$this->errors[ "no_delete_$term_id" ] = sprintf( __( 'The following files could not be deleted:<br><br>%s', 'webcomic' ), implode( '<br>', $files ) );
		
		$i = 1;
		if ( $sibs = get_terms( 'webcomic_storyline', 'hide_empty=0&webcomic_order=1&parent=' . $term_meta[ 'storyline' ][ $term_id ][ 'parent' ] . '&term_group=' . $term_group ) ) {
			foreach ( $sibs as $sib ) {
				$term_meta[ 'storyline' ][ $sib->term_id ][ 'order' ] = $i;
				$i++;
			}
		}
		
		unset( $term_meta[ 'storyline' ][ $term_id ] );
		$this->option( 'term_meta', $term_meta );
	}
	
	/**
	 * 'delete_webcomic_character' hook.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function hook_delete_webcomic_character( $term_id, $tt_id ) {
		$this->domain();
		
		$term_meta  = $this->option( 'term_meta' );
		$term_group = $term_meta[ 'character' ][ $term_id ][ 'group' ];
		
		if ( is_array( $files = $this->delete( $term_id, 'character', $term_meta[ 'collection' ][ $term_group ][ 'slug' ] ) ) )
			$this->errors[ "no_delete_$term_id" ] = sprintf( __( 'The following files could not be deleted:<br><br>%s', 'webcomic' ), implode( '<br>', $files ) );
		
		unset( $term_meta[ 'character' ][ $term_id ] );
		$this->option( 'term_meta', $term_meta );
	}
	
	
	
	////
	// Utilities
	// 
	// These functions are designed for internal use
	// and should never be called directly.
	////
	
	/**
	 * Uploads one or more files to the specified directory.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $src The directory to upload files to.
	 * @param bool $method How to handle the uploaded file.
	 * @return arr|bool An array of filenames, or false on error.
	 */	
	function upload( $src, $method = false ) {
		$uploads = array();
		
		if ( empty( $_FILES[ 'webcomic_file' ] ) )
			return true;
		
		foreach ( $_FILES[ 'webcomic_file' ] as $a => $r )
			foreach ( $r as $k => $v )
				$uploads[ $k ][ $a ] = $v;
		
		$this->domain();
		
		$match = ( isset( $_REQUEST[ 'type' ] ) && 'post' == $_REQUEST[ 'type' ] ) ? true : false;
		$files = ( 'singular' == $method ) ? $this->fetch( $_REQUEST[ 'id' ], $_REQUEST[ 'type' ], $src, $match ) : array();
		$abs   = $this->directory( 'abs', $src );
		$url   = $this->directory( 'url', $src );
		$tabs  = $abs . 'thumbs/';
		$lw    = $this->option( 'large_w' );
		$lh    = $this->option( 'large_h' );
		$mw    = $this->option( 'medium_w' );
		$mh    = $this->option( 'medium_h' );
		$sw    = $this->option( 'small_w' );
		$sh    = $this->option( 'small_h' );
		$i = $x = $z = 0;
		
		foreach ( $uploads as $key => $upload ) {
			if ( 0 === $upload[ 'error' ] ) {
				if ( 'singular' == $method ) {
					$z    = true;
					$info = pathinfo( $upload[ 'name' ] );
					$name = stripslashes( $_REQUEST[ 'webcomic_filename' ] ) . '-' . $key . '.' . $info[ 'extension' ];
					$file = $tabs . $name;
					
					if ( @move_uploaded_file( $upload[ 'tmp_name' ], $file ) )
						$files[ $key ][ $_REQUEST[ 'key' ] ] = $name;
				} elseif ( is_array( $size = @getimagesize( $upload[ 'tmp_name' ] ) ) ) {
					$z    = true;
					$info = pathinfo( $upload[ 'name' ] );
					$hash = ( $this->option( 'secure_toggle' ) ) ? '-' . substr( hash( 'md5', uniqid( rand() ) ), 0, 7 ) : '';
					$name = $info[ 'filename' ] . $hash . '.' . $info[ 'extension' ];
					$file = $abs . $name;
						
					if ( !$name || 0 === strpos( $name, '.' ) )
						continue;
					
					if ( ( !is_file( $file ) || !empty( $_REQUEST[ 'webcomic_overwrite' ] ) ) && @move_uploaded_file( $upload[ 'tmp_name' ], $file ) ) {
						$stat = stat( dirname( $file ) );
						$perm = $stat[ 'mode' ] & 0000666;
						chmod( $file, $perm );
						
						$files[ 'full' ][ $x ] = $name;
						
						if ( 'image/jpeg' == $size[ 'mime' ] || 'image/gif' == $size[ 'mime' ] || 'image/png' == $size[ 'mime' ] ) {
							if ( $size[ 0 ] > $lw || $size[ 1 ] > $lh )
								if ( $large = $this->resize( $file, $lw, $lh, 0, 'large', $tabs ) )
									$files[ 'large' ][ $x ] = basename( $large );
							
							if ( $size[ 0 ] > $mw || $size[ 1 ] > $mh )
								if ( $medium = $this->resize( $file, $mw, $mh, 0, 'medium', $tabs ) )
									$files[ 'medium' ][ $x ] = basename( $medium );
							
							if ( $size[ 0 ] > $sw || $size[ 1 ] > $sh )
								if ( $small = $this->resize( $file, $sw, $sh, 0, 'small', $tabs ) )
									$files[ 'small' ][ $x ] = basename( $small );
						}
					} elseif ( is_file( $file ) )
						$this->errors[ "file_exists_$x" ] = sprintf( __( 'A file named <a href="%s" target="_blank">%s</a> already exists.', 'webcomic' ), $url . $upload[ 'name' ], $upload[ 'name' ] );
					else
						$this->errors[ "no_move_$x" ] =  sprintf( __( '%s could not be moved to the correct directory.', 'webcomic' ), $upload[ 'name' ] );
				} elseif ( is_resource( $data = @zip_open( $upload[ 'tmp_name' ] ) ) && 'bulk' == $method ) {
					if ( $z )
						continue;
					
					while ( $entry = zip_read( $data ) ) {
						$base = basename( zip_entry_name( $entry ) );
						$extn = substr( strrchr( $base, '.'), 0 );
						$hash = ( $this->option( 'secure_toggle' ) ) ? '-' . substr( hash( 'md5', uniqid( rand() ) ), 0, 7 ) : '';
						$name = substr( $base, 0, -strlen( $extn ) )  . $hash . $extn;
						$file = $abs . $name;
						
						if ( !$name || 0 === strpos( $name, '.' ) )
							continue;
						
						if ( ( !is_file( $file ) || !empty( $_REQUEST[ 'webcomic_overwrite' ] ) ) && zip_entry_open( $data, $entry ) ) {
							$content = zip_entry_read( $entry, zip_entry_filesize( $entry ) );
							
							$newfile = fopen( $file, 'w' );
							
							if ( !is_resource( $newfile ) )
								continue;
							
							fwrite( $newfile, $content );
							fclose( $newfile );
							
							if ( is_array( $size = @getimagesize( $file ) ) ) {
								$i++;
								
								$stat = stat( dirname( $file ) );
								$perm = $stat[ 'mode' ] & 0000666;
								chmod( $file, $perm );
								
								$files[ 'full' ][ $i ] = basename( $file );
								
								if ( 'image/jpeg' == $size[ 'mime' ] || 'image/gif' == $size[ 'mime' ] || 'image/png' == $size[ 'mime' ] ) {
									if ( $size[ 0 ] > $lw || $size[ 1 ] > $lh )
										if ( $large = $this->resize( $file, $lw, $lh, 0, 'large', $tabs ) )
											$files[ 'large' ][ $i ] = basename( $large );
									
									if ( $size[ 0 ] > $mw || $size[ 1 ] > $mh )
										if ( $medium = $this->resize( $file, $mw, $mh, 0, 'medium', $tabs ) )
											$files[ 'medium' ][ $i ] = basename( $medium );
									
									if ( $size[ 0 ] > $sw || $size[ 1 ] > $sh )
										if ( $small = $this->resize( $file, $sw, $sh, 0, 'small', $tabs ) )
											$files[ 'small' ][ $i ] = basename( $small );
								}
							} else
								unlink( $file );
							
							zip_entry_close( $entry );
						}
					}
					
					zip_close( $data );
					
					if ( $i )
						$this->update[ "upload_archive_$x" ] = sprintf( _n( '%d file extracted from %s', '%d files extracted from %s', $i, 'webcomic' ), $i, $upload[ 'name' ] );
					else
						$this->errors[ "no_archive_$x" ] = sprintf( __( 'No files could be extracted from %s', 'webcomic' ), $upload[ 'name' ] );
					
					break;
				} else
					$this->errors[ "bad_type_$x" ] = sprintf( __( '%s is an invalid file type', 'webcomic' ), $upload[ 'name' ] );
			} else {
				switch ( $upload[ 'error' ] ) {
					case 1:
					case 2: $this->errors[ "too_big_$x" ] = sprintf( __( 'The file is larger than the maximum upload size of %s', 'webcomic' ), ini_get( 'upload_max_filesize' ) ); break;
					case 3: $this->errors[ "partial_file_$x" ] = __( 'The file was only partially uploaded', 'webcomic' ); break;
					case 4: continue;
					case 6: $this->errors[ "no_temp_$x" ] = __( 'The server temporary directory could not be found', 'webcomic' ); break;
					case 7: $this->errors[ "no_save_$x" ] = __( 'The file could not be saved after upload', 'webcomic' ); break;
					case 8: $this->errors[ "file_halt_$x" ] =  __( 'The upload was halted by a PHP extension', 'webcomic'); break;
				}
			}
			
			$x++;
		}
		
		if ( empty( $files ) )
			return true;
		elseif ( !empty( $files[ 'full' ] ) )
			natcasesort( $files[ 'full' ] );
		else
			return false;
		
		return $files;
	}
	
	/**
	 * Resizes the specified file.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param str $file The file to be resized.
	 * @param int $width The new maximum width.
	 * @param int $height The new maximum height.
	 * @param bool $crop Wehter to crop the resized image to the exact dimensions specified.
	 * @param str $suffix A suffix to append to the end of the filename.
	 * @param str $directory The directory to save the resized file to.
	 * @return str|bool The name of the resized file, or false on error.
	 */	
	function resize( $file, $width, $height, $crop, $suffix, $directory ) {
		if ( !is_file( $file ) || !is_resource( $image = imagecreatefromstring( file_get_contents( $file ) ) ) || !( $size = getimagesize( $file ) ) || ( $width <= 0 && $height <= 0 ) )
			return false;
		
		if ( $size[ 0 ] <= 0 || $size[ 1 ] <= 0 )
			return false;
		
		if ( $crop ) {
			$ar = $size[ 0 ] / $size[ 1 ];
			$nw = min( $width, $size[ 0 ] );
			$nh = min( $height, $size[ 1 ] );
	
			if ( !$nw )
				$nw = intval( $nh * $ar );
			
			if ( !$nh )
				$nh = intval( $nw / $ar );
			
			$sr = max( $nw / $size[ 0 ], $nh / $size[ 1 ] );
			$cw = round( $nw / $sr );
			$ch = round( $nh / $sr );
			$sx = floor( ( $size[ 0 ] - $cw) / 2 );
			$sy = floor( ( $size[ 1 ] - $ch) / 2 );
		} else {
			$cw = $size[ 0 ];
			$ch = $size[ 1 ];
			$sx = $sy = 0;
			$wr = $hr = 1.0;
		
			if ( $width > 0 && $size[ 0 ] > 0 && $size[ 0 ] > $width )
				$wr = $width / $size[ 0 ];
		
			if ( $height > 0 && $size[ 1 ] > 0 && $size[ 1 ] > $height )
				$hr = $height / $size[ 1 ];
			
			$r  = min( $wr, $hr );
			$nw = intval( $size[ 0 ] * $r );
			$nh = intval( $size[ 1 ] * $r );
		}
		
		if ( $nw >= $size[ 0 ] && $nh >= $size[ 1 ] )
			return false;
		
		if ( is_resource( $newimg = imagecreatetruecolor( $nw, $nh ) ) ) {
			imagealphablending( $newimg, false );
			imagesavealpha( $newimg, true );
		} else
			return false;
		
		imagecopyresampled( $newimg, $image, 0, 0, $sx, $sy, $nw, $nh, $cw, $ch );
		
		if ( ( 'image/png' == $size[ 'mime' ] || 'image/gif' == $size[ 'mime' ] ) && !imageistruecolor( $image ) ) {
			imagetruecolortopalette( $newimg, true, imagecolorstotal( $image ) );
			
			if ( false !== ( $trans = imagecolortransparent( $image ) ) ) {
				$color = ( $trans >= 0 ) ? imagecolorsforindex( $image, imagecolortransparent( $image ) ) : array( 'red' => 255, 'green' => 255, 'blue' => 255 );
				$trans = imagecolorallocate( $newimg, $color[ 'red' ], $color[ 'green' ], $color[ 'blue' ] );
				imagefill( $newimg, 0, 0, $trans );
				imagecolortransparent( $newimg, $trans );
			}	
		}
		
		imagedestroy( $image );
		
		if ( $suffix )
			$suffix = '-' . $suffix;
		
		$info = pathinfo( $file );
		
		if ( !is_null( $directory ) && $path = realpath( $directory ) )
			$dir = $path;
		
		$filename = $dir . '/' . $info[ 'filename' ] . $suffix . '.' . $info[ 'extension' ];
		
		if ( 'image/jpeg' == $size[ 'mime' ] ) {
			if ( !imagejpeg( $newimg, $filename, 100 ) )
				return false;
		} elseif ( 'image/gif' == $size[ 'mime' ] ) {
			if ( !imagegif( $newimg, $filename ) )
				return false;
		} elseif ( 'image/png' == $size[ 'mime' ] ) {
			if ( !imagepng( $newimg, $filename ) )
				return false;
		} else
			return false;
		
		imagedestroy( $newimg );
		
		$stat = stat( dirname( $filename ) );
		$perm = $stat[ 'mode' ] & 0000666;
		chmod( $filename, $perm );
		
		return $filename;
	}
	
	/**
	 * Binds a set of files to the specified object.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param int $id ID of the object files will be bound to.
	 * @param str $type The type of object, one of 'collection', 'storyline', 'character', or 'post'.
	 * @param arr $files An array of files to bind.
	 * @param bool $altdes Maintain alternative and descripttive text associations if true.
	 * @return bool False if a non-standard type is specified, true otherwise.
	 */
	function bind( $id, $type, $files = array(), $altdes = true ) {
		$files = ( empty( $files[ 'full' ] ) ) ? array() : $files;
		
		if ( !empty( $files ) )
			foreach ( $files as $s => $f )
				foreach ( $f as $k => $v )
					$files[ $s ][ $k ] = stripslashes( $v );
		
		if ( 'collection' == $type || 'storyline' == $type || 'character' == $type ) {
			$term_meta = $this->option( 'term_meta' );
			
			$term_meta[ $type ][ $id ][ 'files' ] = $files;
			
			$this->option( 'term_meta', $term_meta );
		} elseif ( 'post' == $type ) {
			$post_meta = current( get_post_meta( $id, 'webcomic' ) );
			
			if ( empty( $files ) || !$altdes ) {
				$post_meta[ 'alternate' ]   = array();
				$post_meta[ 'description' ] = array();
			} elseif ( ( !empty( $post_meta[ 'alternate' ] ) || !empty( $post_meta[ 'description' ] ) ) && ( $a = array_diff( array_keys( ( array ) $post_meta[ 'alternate' ] ), array_keys( $files[ 'full' ] ) ) ) ) {
				foreach ( $a as $k ) {
					unset( $post_meta[ 'alternate' ][ $k ] );
					unset( $post_meta[ 'description' ][ $k ] );
				}
			}
			
			$post_meta[ 'files' ] = $files;
			
			update_post_meta( $id, 'webcomic', $post_meta );
		} else
			return false;
		
		return true;
	}
	
	/**
	 * Renames the files associated with an object.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param int $id ID of the object to fetch files from.
	 * @param str $type The type of object to fetch for. Must be one of 'collection', 'storyline', 'character', 'post', or 'orphan'.
	 * @param str $src The directory to search when fetch files for 'orphan' type objects.
	 * @param str $filename The new filename to use.
	 * @param int $key The key that points to the file associated with the specified object.
	 * @return bool|arr True on success, false if no files can be found. Array of files that could not be renamed on error.
	 */
	function rename( $id, $type, $src, $filename, $key = false ) {
		if ( false === $key )
			return false;
		
		$match    = ( 'post' == $type || 'orphan' == $type ) ? true : false;
		$abs      = $this->directory( 'abs', $src );
		$tabs     = $abs . 'thumbs/';
		$hash     = ( $this->option( 'secure_toggle' ) ) ? '-' . substr( hash( 'md5', uniqid( rand() ) ), 0, 7 ) : '';
		$output   = $bind = array();
		$filename = stripslashes( $filename );
		
		if ( !( $files = $this->fetch( $id, $type, $src, $match ) ) )
			return false;
		
		foreach ( $files as $s => $f ) {
			foreach ( $f as $k => $v ) {
				if ( $key == $k ) {
					$extension = '.' . end( explode( '.', $v ) );
					
					if ( !@rename( $abs . $v, $abs . $filename . $hash . $extension ) ) {
						if ( !@rename( $tabs . $v, $tabs . $filename . $hash . '-' . $s . $extension ) )
							$output[ $k ] = $v;
						else
							$files[ $s ][ $k ] = $filename . $hash . '-' . $s . $extension;
					} else
						$files[ $s ][ $k ] = $filename . $hash . $extension;
				}
			}
		}
		
		natcasesort( $files[ 'full' ] );
		
		$this->bind( $id, $type, $files );
		
		if ( $output )
			return $output;
		else
			return true;
	}
	
	/**
	 * Regenerates the thumbnails associated with an object.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param int $id ID of the object to fetch files from.
	 * @param str $type The type of object to fetch for. Must be one of 'collection', 'storyline', 'character', 'post', or 'orphan'.
	 * @param str $src The directory to search when fetch files for 'orphan' type objects.
	 * @param int $key The key that points to the file associated with the specified object. If not specified, all file thumbnails are regenerated.
	 * @return bool|arr True on success, false if no files can be found. Array of files that could not be regenerated on error.
	 */
	function regen( $id, $type, $src, $key = false ) {
		$match = ( 'post' == $type || 'orphan' == $type ) ? true : false;
		
		if ( !( $files = $this->fetch( $id, $type, $src, $match ) ) )
			return false;
		
		$i    = 0;
		$abs  = $this->directory( 'abs', $src );
		$tabs = $abs . 'thumbs/';
		$lw   = $this->option( 'large_w' );
		$lh   = $this->option( 'large_h' );
		$mw   = $this->option( 'medium_w' );
		$mh   = $this->option( 'medium_h' );
		$sw   = $this->option( 'small_w' );
		$sh   = $this->option( 'small_h' );
		
		foreach ( $files as $s => $f ) {
			foreach ( $f as $k => $v ) {
				if ( false !== $key && $key != $k )
					continue;
				
				if ( @unlink( $tabs . $v ) )
					unset( $files[ $s ][ $k ] );
			}
		}
		
		foreach ( $files[ 'full' ] as $k => $v ) {
			if ( ( false !== $key && $key != $k ) || !is_array( $data = @getimagesize( $abs . $v ) ) || !( 'image/jpeg' == $data[ 'mime' ] || 'image/gif' == $data[ 'mime' ] || 'image/png' == $data[ 'mime' ] ) )
				continue;
			
			$i++;
			
			if ( $data[ 0 ] > $lw || $data[ 1 ] > $lh )
				if ( $large = $this->resize( $abs . $v, $lw, $lh, 0, 'large', $tabs ) )
					$files[ 'large' ][ $k ] = basename( $large );
			
			if ( $data[ 0 ] > $mw || $data[ 1 ] > $mh )
				if ( $medium = $this->resize( $abs . $v, $mw, $mh, 0, 'medium', $tabs ) )
					$files[ 'medium' ][ $k ] = basename( $medium );
			
			if ( $data[ 0 ] > $sw || $data[ 1 ] > $sh )
				if ( $small = $this->resize( $abs . $v, $sw, $sh, 0, 'small', $tabs ) )
					$files[ 'small' ][ $k ] = basename( $small );
		}
		
		$this->bind( $id, $type, $files );
		
		if ( $i && $key )
			return array( $files[ 'full' ][ $key ] );
		elseif ( $i )
			return $files[ 'full' ];
		else
			return false;
	}
	
	/**
	 * Deletes the files associated with an object.
	 * 
	 * @package webcomic
	 * @since 3
	 * 
	 * @param int $id ID of the object to fetch files from.
	 * @param str $type The type of object to fetch for. Must be one of 'collection', 'storyline', 'character', 'post', or 'orphan'.
	 * @param str $src The directory to search when fetch files for 'orphan' type objects.
	 * @param int $key The key that points to the file associated with the specified object. If not specified, all files are deleted.
	 * @param str $size The size of the file to be deleted. Can be combined with $key to target a singular file in the files array.
	 * @return str|bool|arr String of comma-separated filenames on success, false if no files can be found. An array of filenames that could not be deleted on error.
	 */
	function delete( $id, $type, $src, $key = false, $size = false ) {
		$match = ( 'post' == $type || 'orphan' == $type ) ? true : false;
		
		if ( !( $files = $this->fetch( $id, $type, $src, $match ) ) )
			return false;
		
		$abs    = $this->directory( 'abs', $src );
		$tabs   = $abs . 'thumbs/';
		$names  = array();
		$output = array();
		
		foreach ( $files as $s => $f ) {
			if ( false !== $size && $size != $s )
				continue;
			
			foreach ( $f as $k => $v ) {
				if ( false !== $key && $key != $k )
					continue;
				
				if ( !@unlink( $abs . $v ) ) {
					if ( !@unlink( $tabs . $v ) )
						array_push( $output, $file );
					else
						unset( $files[ $s ][ $k ] );
				} else {
					array_push( $names, $files[ $s ][ $k ] );
					unset( $files[ $s ][ $k ] );
				}
			}
			
			if ( empty( $files[ $s ] ) )
				unset( $files[ $s ] );
		}
		
		$this->bind( $id, $type, $files );
		
		if ( $output )
			return $output;
		else
			return implode( ', ', $names );
	}
	
	
	
	////
	// Administration Pages
	// 
	// These functions display the various Webcomic
	// related administrative pages and should never
	// be called directly.
	////
	
	/**
	 * Displays the post and file management pages.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function admin_files() {
		$this->domain();
		
		global $current_user, $wpdb;
		
		$wc      = ( !empty( $_REQUEST[ 'webcomic_collection' ] ) ) ? get_term( $_REQUEST[ 'webcomic_collection' ], 'webcomic_collection' ) : get_term( ( int ) $this->option( 'default_collection' ), 'webcomic_collection' );
		$wcs     = ( !empty( $_REQUEST[ 'webcomic_storyline' ] ) ) ? '&amp;webcomic_storyline=' . $_REQUEST[ 'webcomic_storyline' ] : '';
		$wcc     = ( !empty( $_REQUEST[ 'webcomic_character' ] ) ) ? '&amp;webcomic_character=' . $_REQUEST[ 'webcomic_character' ] : '';
		$sub     = ( !empty( $_REQUEST[ 'subpage' ] ) ) ? '&amp;subpage=' . $_REQUEST[ 'subpage' ] : '';
		$page    = $_REQUEST[ 'page' ];
		$pagenum = ( !empty( $_REQUEST[ 'pagenum' ] ) ) ? $_REQUEST[ 'pagenum' ] : 1;
		$hidden  = get_hidden_columns( 'toplevel_page_webcomic_files' );
		$find    = ( !empty( $_REQUEST[ 's' ] ) ) ? '&amp;s=' . $_REQUEST[ 's' ] : '';
		$view    = '?page=' . $page . '&amp;webcomic_collection=' . $wc->term_id . $wcs . $wcc . $sub . $find;
		
		if ( 'webcomic_collection' != $page && ( !$wc || is_wp_error( $wc ) ) ) {
		?>
		<div class="wrap">
			<div id="icon-webcomic" class="icon32"><img src="<?php echo $this->url . 'webcomic-includes/icon.png'; ?>" alt="icon"></div>
			<h2><?php _e( 'Webcomic Error', 'webcomc' ); ?></h2>
			<p><?php printf( __( "Hold up: it looks like you don't have any collections! You should definitely <a href='%s'>create a collection</a> before you go any further.", 'webcomic' ), admin_url( 'admin.php?page=webcomic_collection' ) ); ?></p>
		</div>
		<?php
		} elseif ( isset( $_REQUEST[ 'action' ] ) && 'bulk_webcomic_upload' == $_REQUEST[ 'action' ] ) { $i = 0; ?>
		<style>#screen-options-link-wrap{display:none}.widefat img{max-height:<?php echo $this->option( 'small_h' ); ?>px;max-width:<?php echo $this->option( 'small_w' ); ?>px}.form-field input{width:auto}</style>
		<div class="wrap">
			<div id="icon-webcomic" class="icon32"><img src="<?php echo $this->url . 'webcomic-includes/icon.png'; ?>" alt="icon"></div>
			<h2><?php printf( __( 'Upload Files to %s', 'webcomic' ), $wc->name ); ?></h2>
			<form action="<?php echo $view; ?>" method="post" enctype="multipart/form-data">
				<?php wp_nonce_field( 'bulk_webcomic_upload' ); ?>
				<div id="col-container" style="clear:both">
					<?php if ( isset( $_REQUEST[ 'uploaded_webcomic_files' ] ) && is_array( $_REQUEST[ 'uploaded_webcomic_files' ] ) ) { ?>
					<div id="col-right">
						<div class="col-wrap">
							<h3><?php _e( 'Uploaded Files', 'webcomic' ); ?></h3>
							<table class="widefat">
								<thead>
									<tr>
										<th style="width:<?php echo $this->option( 'small_w' ); ?>px"><label for="check1"><?php _e( 'Thumbnail', 'webcomic' ); ?></label></th>
										<th><?php _e( 'File', 'webcomic' ); ?></th>
									</tr>
								</thead>
								<tfoot>
									<tr>
										<th><label for="check2"><?php _e( 'Thumbnail', 'webcomic' ); ?></label></th>
										<th><?php _e( 'File', 'webcomic' ); ?></th>
									</tr>
								</tfoot>
								<tbody>
								<?php natcasesort( $_REQUEST[ 'uploaded_webcomic_files' ] ); foreach ( $_REQUEST[ 'uploaded_webcomic_files' ] as $f ) { $alt = ( !( $i % 2 ) ) ? ' class="alt"' : ''; ?>
									<tr<?php echo $alt; ?>>
										<td>
										<?php
											$new = $this->retrieve( $this->directory( 'abs', $wc->slug ) . $f, 'orphan', $wc->slug );
											
											if ( $new[ 'small' ] )
												echo $new[ 'small' ][ 0 ][ 'html' ];
											elseif ( $new[ 'medium' ] )
												echo $new[ 'medium' ][ 0 ][ 'html' ];
											elseif ( $new[ 'large' ] )
												echo $new[ 'large' ][ 0 ][ 'html' ];
											else
												echo $new[ 'full' ][ 0 ][ 'html' ];
										?>
										</td>
										<td>
											<span class="row-title"><?php echo $new[ 'full' ][ 0 ][ 'filename' ]; ?></span><br>
											<?php echo $new[ 'full' ][ 0 ][ 'mime' ]; ?>
										</td>
									</tr>
								<?php $i++; } ?>
								</tbody>
							</table>
						</div>
					</div>
					<?php } ?>
					<div id="col-left">
						<div class="col-wrap">
							<div class="form-wrap">
								<div class="form-field">
									<p id="webcomic_files"><input type="file" name="webcomic_file[]"> <label style="display:inline"><?php if ( current_user_can( 'manage_categories' ) ) { ?><input type="checkbox" name="webcomic_ovewrite" value="1"><?php _e( 'Overwrite', 'webcomic' ); ?></label><?php } ?><a id="add_webcomic_file" style="cursor:pointer"><?php _e( '+ Add More', 'webcomic' ); ?></a></p>
									<p><?php $z = ( function_exists( 'zip_open' ) ) ? __( 'a zipped archive of images or', 'webcomic' ) : ''; printf( __( 'You may upload %s one or more individual images by clicking <strong>+ Add More</strong>.', 'webcomic' ), $z ); ?></p>
								</div>
								<p class="submit">
									<a href="<?php echo $view; ?>" class="button"><?php if ( !isset( $_REQUEST[ 'uploaded_webcomic_files' ] ) ) _e( 'Cancel', 'webcomic' ); else _e( '&laquo; Back', 'webcomic' ); ?></a>
									<input type="submit" name="submit" value="<?php _e( 'Upload Files', 'webcomic' ); ?>" class="button-primary">
									<input type="hidden" name="webcomic_collection" value="<?php echo $wc->term_id; ?>">
									<input type="hidden" name="action" value="bulk_webcomic_upload">
								</p>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
		<script type="text/javascript">jQuery(document).ready(function($){$('#add_webcomic_file').click(function(){$('#webcomic_files').append('<input type="file" name="webcomic_file[]"><br>')});});</script>
		<?php } elseif ( !empty( $_REQUEST[ 'action' ] ) && !empty( $_REQUEST[ 'bulk' ] ) && ( isset( $_REQUEST[ 'action-1' ] ) || isset( $_REQUEST[ 'action-2' ] ) ) && ( $action = ( !empty( $_REQUEST[ 'submit-1' ] ) ) ? $_REQUEST[ 'action-1' ] : $_REQUEST[ 'action-2' ] ) && 'batch_post' == $action ) { $i = 0; ?>
		<style>#screen-options-link-wrap{display:none}</style>
		<div class="wrap">
			<div id="icon-webcomic" class="icon32"><img src="<?php echo $this->url . 'webcomic-includes/icon.png'; ?>" alt="icon"></div>
			<h2><?php printf( __( 'Editing Posts in %s', 'webcomic' ), $wc->name ); ?></h2>
			<form action="<?php echo $view; ?>" method="post">
				<?php wp_nonce_field( 'batch_webcomic_posts' ); ?>
				<div id="col-container" style="clear:both">
					<div id="col-right">
						<div class="col-wrap">
							<table class="widefat">
								<thead>
									<tr>
										<th scope="col" class="manage-column column-cb check-column"><input type="checkbox" id="check1" checked></th>
										<th><label for="check1"><?php _e( 'Post', 'webcomic' ); ?></label></th>
										<th><?php _e( 'Storylines', 'webcomic' ); ?></th>
										<th><?php _e( 'Characters', 'webcomic' ); ?></th>
									</tr>
								</thead>
								<tfoot>
									<tr>
										<th scope="col" class="manage-column column-cb check-column"><input type="checkbox" id="check2" checked></th>
										<th><label for="check2"><?php _e( 'Post', 'webcomic' ); ?></label></th>
										<th><?php _e( 'Storylines', 'webcomic' ); ?></th>
										<th><?php _e( 'Characters', 'webcomic' ); ?></th>
									</tr>
								</tfoot>
								<tbody>
								<?php foreach ( $_REQUEST[ 'bulk' ] as $bulk ) { $alt = ( !( $i % 2 ) ) ? ' class="alt"' : ''; ?>
									<tr<?php echo $alt; ?>>
										<th scope="row" class="check-column"><input type="checkbox" name="bulk[]" value="<?php echo $bulk; ?>" id="#bulk-<?php echo $bulk; ?>" checked></th>
										<td><label for="#bulk-<?php echo $bulk; ?>" class="row-title"><?php echo get_the_title( $bulk ); ?></label></td>
										<td>
										<?php
											$storylines = wp_get_object_terms( $bulk, 'webcomic_storyline' );
											
											if ( !empty( $storylines ) && !is_wp_error( $storylines ) ) {
												$sterms = array();
												
												foreach ( $storylines as $storyline )
													$sterms[] = $storyline->name;
											} else
												$sterms = array( __( 'No Storylines', 'webcomic' ) );
											
											echo implode( ', ', $sterms );
										?>
										</td>
										<td>
										<?php
											$characters = wp_get_object_terms( $bulk, 'webcomic_character' );
											
											if ( !empty( $characters ) && !is_wp_error( $characters ) ) {
												$cterms = array();
												
												foreach ( $characters as $character )
													$cterms[] = $character->name;
											} else
												$cterms = array( __( 'No Characters', 'webcomic' ) );
											
											echo implode( ', ', $cterms );
										?>
										</td>
									</tr>
								<?php $i++; } ?>
								</tbody>
							</table>
						</div>
					</div>
					<div id="col-left">
						<div class="col-wrap">
							<div class="form-wrap">
								<div class="form-field">
									<label for="webcomic_post_collection"><b><?php _e( 'Collection', 'webcomic' ); ?></b></label>
									<select name="webcomic_post_collection" id="webcomic_post_collection">
									<?php
										$walker = new webcomic_Walker_AdminTermDropdown();
										echo $walker->walk( get_terms( 'webcomic_collection', 'get=all' ), 0, array( 'selected' => array( $wc->term_id ) ) );
									?>
									</select>
									<p><?php _e( 'Changing the collection will unbind any files, storylines, and characters from the selected posts.', 'webcomic' ); ?></p>
								</div>
								<div id="webcomic_ajax">
									<div class="form-field">
										<label for="webcomic_post_storylines" style="float:left"><b><?php _e( 'Storylines', 'webcomic' ); ?></b></label>
										<select name="webcomic_post_storylines_action" style="float:right">
											<option value="add"><?php _e( 'Add', 'webcomic' ); ?></option>
											<option value="replace"><?php _e( 'Replace', 'webcomic' ); ?></option>
										</select>
										<select name="webcomic_post_storylines[]" id="webcomic_post_storylines" size="2" style="height:10em;width:100%" multiple>
										<?php
											$walker   = new webcomic_Walker_AdminTermDropdown();
											echo $walker->walk( get_terms( 'webcomic_storyline', 'hide_empty=0&orderby=term_group_name&webcomic_order=1&term_group=' . $wc->term_id ), 0, array( 'selected' => array(), 'no_def' => true ) );
										?>
										</select>
										<p><?php _e( 'Hold <code>CTRL</code> or <code>Command</code> to select multiple storylines.', 'webcomic' ); ?></p>
									</div>
									<div class="form-field">
										<label for="webcomic_post_characters" style="float:left"><b><?php _e( 'Characters', 'webcomic' ); ?></b></label>
										<select name="webcomic_post_characters_action" style="float:right">
											<option value="add"><?php _e( 'Add', 'webcomic' ); ?></option>
											<option value="replace"><?php _e( 'Replace', 'webcomic' ); ?></option>
										</select>
										<select name="webcomic_post_characters[]" id="webcomic_post_characters" size="2" style="height:10em;width:100%" multiple>
										<?php
											$walker   = new webcomic_Walker_AdminTermDropdown();
											echo $walker->walk( get_terms( 'webcomic_character', 'hide_empty=0&orderby=term_group_name&term_group=' . $wc->term_id ), 0, array( 'selected' => array(), 'no_def' => true ) );
										?>
										</select>
										<p><?php _e( 'Hold <code>CTRL</code> or <code>Command</code> to select multiple characters.', 'webcomic' ); ?></p>
									</div>
								</div>
								<div class="form-field">
									<label for="webcomic_post_status"><?php _e( 'Status', 'webcomic' ); ?></label>
									<select name="webcomic_post_status" id="webcomic_post_status">
										<option value=""><?php _e( '&mdash; No Change &mdash;', 'webcomic' ); ?></option>
										<option value="publish"><?php _e( 'Published', 'webcomic' ); ?></option>
										<option value="private"><?php _e( 'Private', 'webcomic' ); ?></option>
										<option value="pending"><?php _e( 'Pending Review', 'webcomic' ); ?></option>
										<option value="draft"><?php _e( 'Draft', 'webcomic' ); ?></option>
									</select>
								</div>
								<div class="form-field">
									<label for="webcomic_post_sticky"><?php _e( 'Sticky', 'webcomic' ); ?></label>
									<select name="webcomic_post_sticky" id="webcomic_post_sticky">
										<option value=""><?php _e( '&mdash; No Change &mdash;', 'webcomic' ); ?></option>
										<option value="on"><?php _e( 'Sticky', 'webcomic' ); ?></option>
										<option value="off"><?php _e( 'Not Sticky', 'webcomic' ); ?></option>
									</select>
								</div>
								<div class="form-field">
									<label for="webcomic_post_comments"><?php _e( 'Comments', 'webcomic' ); ?></label>
									<select name="webcomic_post_comments" id="webcomic_post_comments">
										<option value=""><?php _e( '&mdash; No Change &mdash;', 'webcomic' ); ?></option>
										<option value="open"><?php _e( 'Allowed', 'webcomic' ); ?></option>
										<option value="closed"><?php _e( 'Not Allowed', 'webcomic' ); ?></option>
									</select>
								</div>
								<div class="form-field">
									<label for="webcomic_post_pings"><?php _e( 'Pings', 'webcomic' ); ?></label>
									<select name="webcomic_post_pings" id="webcomic_post_pings">
										<option value=""><?php _e( '&mdash; No Change &mdash;', 'webcomic' ); ?></option>
										<option value="open"><?php _e( 'Allowed', 'webcomic' ); ?></option>
										<option value="closed"><?php _e( 'Not Allowed', 'webcomic' ); ?></option>
									</select>
								</div>
								<div class="form-field">
									<label for="webcomic_post_transcripts"><?php _e( 'Transcripts', 'webcomic' ); ?></label>
									<select name="webcomic_post_transcripts" id="webcomic_post_transcripts">
										<option value=""><?php _e( '&mdash; No Change &mdash;', 'webcomic' ); ?></option>
										<option value="open"><?php _e( 'Allowed', 'webcomic' ); ?></option>
										<option value="closed"><?php _e( 'Not Allowed', 'webcomic' ); ?></option>
									</select>
								</div>
								<div class="form-field">
									<label for="webcomic_post_prints"><?php _e( 'Prints', 'webcomic' ); ?></label>
									<select name="webcomic_post_prints" id="webcomic_post_prints">
										<option value=""><?php _e( '&mdash; No Change &mdash;', 'webcomic' ); ?></option>
										<option value="open"><?php _e( 'Allowed', 'webcomic' ); ?></option>
										<option value="closed"><?php _e( 'Not Allowed', 'webcomic' ); ?></option>
									</select>
								</div>
								<div class="form-field">
									<label for="webcomic_post_originals"><?php _e( 'Originals', 'webcomic' ); ?></label>
									<select name="webcomic_post_originals" id="webcomic_post_originals">
										<option value=""><?php _e( '&mdash; No Change &mdash;', 'webcomic' ); ?></option>
										<option value="open"><?php _e( 'Available', 'webcomic' ); ?></option>
										<option value="closed"><?php _e( 'Not Available', 'webcomic' ); ?></option>
									</select>
								</div>
								<p class="submit">
									<a href="<?php echo $view; ?>" class="button"><?php _e( 'Cancel', 'webcomic' ); ?></a>
									<input type="submit" name="submit" value="<?php _e( 'Apply Changes', 'webcomic' ); ?>" class="button-primary">
									<input type="hidden" name="webcomic_collection" value="<?php echo $wc->term_id; ?>">
									<input type="hidden" name="action" value="batch_webcomic_posts">
								</p>
							</div>
						</div>
					</div>
				</div>
				<input type="hidden" name="webcomic_ajax" value="0">
			</form>
		</div>
		<script type="text/javascript">jQuery(document).ready(function($){$('select#webcomic_post_collection').change(function(){$('input[name=webcomic_ajax]').val('batch_post');var params=$('form').serialize();$('#webcomic_ajax').load('<?php echo $_SERVER[ 'PHP_SELF' ]; ?>',params);$('input[name=webcomic_ajax]').val(0);}).change();});</script>
		<?php
		} elseif ( !empty( $_REQUEST[ 'action' ] ) && !empty( $_REQUEST[ 'bulk' ] ) && ( isset( $_REQUEST[ 'action-1' ] ) || isset( $_REQUEST[ 'action-2' ] ) ) && ( $action = ( !empty( $_REQUEST[ 'submit-1' ] ) ) ? $_REQUEST[ 'action-1' ] : $_REQUEST[ 'action-2' ] ) && 'batch_file' == $action ) { $i = 0;
		?>
		<style>#screen-options-link-wrap{display:none}.form-field input[type=checkbox]{width:auto}.widefat img{max-height:<?php echo $this->option( 'small_h' ); ?>px;max-width:<?php echo $this->option( 'small_w' ); ?>px}</style>
		<div class="wrap">
			<div id="icon-webcomic" class="icon32"><img src="<?php echo $this->url . 'webcomic-includes/icon.png'; ?>" alt="icon"></div>
			<h2><?php printf( __( 'Post Files in %s', 'webcomic' ), $wc->name ); ?></h2>
			<form action="<?php echo $view; ?>" method="post">
				<?php wp_nonce_field( 'batch_webcomic_files' ); ?>
				<div id="col-container" style="clear:both">
					<div id="col-right">
						<div class="col-wrap">
							<table class="widefat">
								<thead>
									<tr>
										<th scope="col" class="manage-column column-cb check-column"><input type="checkbox" id="check1" checked></th>
										<th style="width:<?php echo $this->option( 'small_w' ); ?>px"><label for="check1"><?php _e( 'Thumbnail', 'webcomic' ); ?></label></th>
										<th><?php _e( 'File', 'webcomic' ); ?></th>
									</tr>
								</thead>
								<tfoot>
									<tr>
										<th scope="col" class="manage-column column-cb check-column"><input type="checkbox" id="check2" checked></th>
										<th><label for="check2"><?php _e( 'Thumbnail', 'webcomic' ); ?></label></th>
										<th><?php _e( 'File', 'webcomic' ); ?></th>
									</tr>
								</tfoot>
								<tbody>
								<?php natcasesort( $_REQUEST[ 'bulk' ] ); foreach ( $_REQUEST[ 'bulk' ] as $bulk ) { $alt = ( !( $i % 2 ) ) ? ' class="alt"' : ''; ?>
									<tr<?php echo $alt; ?>>
										<th scope="row" class="check-column"><input type="checkbox" name="bulk[<?php echo hash( 'md5', $bulk ); ?>]" value="<?php echo stripslashes( $bulk ); ?>" id="bulk-<?php echo hash( 'md5', $bulk ); ?>" checked></th>
										<td>
										<?php
											$orphan = $this->retrieve( $bulk, 'orphan', $wc->slug );
											
											if ( $orphan[ 'small' ] )
												echo $orphan[ 'small' ][ 0 ][ 'html' ];
											elseif ( $orphan[ 'medium' ] )
												echo $orphan[ 'medium' ][ 0 ][ 'html' ];
											elseif ( $orphan[ 'large' ] )
												echo $orphan[ 'large' ][ 0 ][ 'html' ];
											else
												echo $orphan[ 'full' ][ 0 ][ 'html' ];
										?>
										</td>
										<td>
											<label for="bulk-<?php echo hash( 'md5', $bulk ); ?>" class="row-title"><?php echo $orphan[ 'full' ][ 0 ][ 'filename' ]; ?></label><br>
											<?php echo $orphan[ 'full' ][ 0 ][ 'mime' ]; ?>
										</td>
									</tr>
								<?php $i++; } ?>
								</tbody>
							</table>
						</div>
					</div>
					<div id="col-left">
						<div class="col-wrap">
							<div class="form-wrap">
								<div class="form-field">
									<label for="mm"><b><?php _e( 'Start on&hellip;', 'webcomic' ); ?></b></label>
									<select id="mm" name="mm">
										<option value="01"<?php if ( '01' == date( 'm', strtotime( 'now' ) ) ) echo ' selected="selected"'; echo '>' . __( 'January', 'webcomic'); ?></option>
										<option value="02"<?php if ( '02' == date( 'm', strtotime( 'now' ) ) ) echo ' selected="selected"'; echo '>' . __( 'February', 'webcomic'); ?></option>
										<option value="03"<?php if ( '03' == date( 'm', strtotime( 'now' ) ) ) echo ' selected="selected"'; echo '>' . __( 'March', 'webcomic'); ?></option>
										<option value="04"<?php if ( '04' == date( 'm', strtotime( 'now' ) ) ) echo ' selected="selected"'; echo '>' . __( 'April', 'webcomic'); ?></option>
										<option value="05"<?php if ( '05' == date( 'm', strtotime( 'now' ) ) ) echo ' selected="selected"'; echo '>' . __( 'May', 'webcomic'); ?></option>
										<option value="06"<?php if ( '06' == date( 'm', strtotime( 'now' ) ) ) echo ' selected="selected"'; echo '>' . __( 'June', 'webcomic'); ?></option>
										<option value="07"<?php if ( '07' == date( 'm', strtotime( 'now' ) ) ) echo ' selected="selected"'; echo '>' . __( 'July', 'webcomic'); ?></option>
										<option value="08"<?php if ( '08' == date( 'm', strtotime( 'now' ) ) ) echo ' selected="selected"'; echo '>' . __( 'August', 'webcomic'); ?></option>
										<option value="09"<?php if ( '09' == date( 'm', strtotime( 'now' ) ) ) echo ' selected="selected"'; echo '>' . __( 'September', 'webcomic'); ?></option>
										<option value="10"<?php if ( '10' == date( 'm', strtotime( 'now' ) ) ) echo ' selected="selected"'; echo '>' . __( 'October', 'webcomic'); ?></option>
										<option value="11"<?php if ( '11' == date( 'm', strtotime( 'now' ) ) ) echo ' selected="selected"'; echo '>' . __( 'November', 'webcomic'); ?></option>
										<option value="12"<?php if ( '12' == date( 'm', strtotime( 'now' ) ) ) echo ' selected="selected"'; echo '>' . __( 'December', 'webcomic'); ?></option>
									</select>
									<input type="text" id="jj" name="jj" value="<?php echo date( 'd', strtotime( 'now' ) ); ?>" size="2" maxlength="2">,
									<input type="text" id="aa" name="aa" value="<?php echo date( 'Y', strtotime( 'now' ) ); ?>" size="4" maxlength="5">
									<p><?php _e( 'The first file in the list will be published on this day.', 'webcomic' ); ?></p>
								</div>
								<div class="form-field">
									<label for="webcomic_file_interval"><b><?php _e( 'Publish every&hellip;', 'webcomic' ); ?></b></label>
									<select name="webcomic_file_interval" id="webcomic_file_interval">
										<option value="day"><?php _e( 'Day', 'webcomic' ); ?></option>
										<option value="week"><?php _e( 'Week', 'webcomic' ); ?></option>
										<option value="bus"><?php _e( 'Weekday (Mon&ndash;Fri)', 'webcomic' ); ?></option>
										<option value="end"><?php _e( 'Weekend (Sat &amp; Sun)', 'webcomic' ); ?></option>
										<option value="mwf"><?php _e( 'Mon, Wed, &amp; Fri', 'webcomic' ); ?></option>
										<option value="trd"><?php _e( 'Tue &amp; Thu', 'webcomic' ); ?></option>
									</select>
									<div id="interval" style="display:none">
										<label><input type="checkbox" name="days[]" value="7/Sunday"> <?php _e( 'Sunday', 'webcomic' ); ?></label>
										<label><input type="checkbox" name="days[]" value="1/Monday"> <?php _e( 'Monday', 'webcomic' ); ?></label>
										<label><input type="checkbox" name="days[]" value="2/Tuesday"> <?php _e( 'Tuesday', 'webcomic' ); ?></label>
										<label><input type="checkbox" name="days[]" value="3/Wednesday"> <?php _e( 'Wednesday', 'webcomic' ); ?></label>
										<label><input type="checkbox" name="days[]" value="4/Thursday"> <?php _e( 'Thursday', 'webcomic' ); ?></label>
										<label><input type="checkbox" name="days[]" value="5/Friday"> <?php _e( 'Friday', 'webcomic' ); ?></label>
										<label><input type="checkbox" name="days[]" value="6/Saturday"> <?php _e( 'Saturday', 'webcomic' ); ?></label>
									</div>
									<p><?php _e( 'Each subsequent file will be published based on this selection.', 'webcomic' ); ?></p>
								</div>
								<div class="form-field">
								<label><input type="checkbox" name="webcomic_file_draft" value="1"> <b><?php _e( 'Save posts as drafts', 'webcomic' ); ?></b></label>
								<p><?php _e( 'Generated posts will be saved as drafts and will not appear on your site until you have published them.', 'webcomic' ); ?></p>
								</div>
								<div class="form-field">
								<label><input type="checkbox" name="webcomic_file_auto" value="1"> <b><?php _e( 'Automatically detect publish date for files', 'webcomic' ); ?></b></label>
								<p><?php _e( 'Webcomic will attempt to automatically determine the date files should be published based on their filename by searching for date information in the format YYYY-MM-DD. This option may produce unexpected results with certain filenames. <b>Start on&hellip;</b> and <b>Publish every&hellip;</b> settings will be ignored.', 'webcomic' ); ?></p>
								</div>
								<p class="submit">
									<a href="<?php echo $view; ?>" class="button"><?php _e( 'Cancel', 'webcomic' ); ?></a>
									<input type="submit" name="submit" value="<?php _e( 'Generate Posts', 'webcomic' ); ?>" class="button-primary">
									<input type="hidden" name="webcomic_collection" value="<?php echo $wc->term_id; ?>">
									<input type="hidden" name="action" value="batch_webcomic_files">
								</p>
							</div>
						</div>
					</div>
				</div>
				<input type="hidden" name="webcomic_ajax" value="0">
			</form>
		</div>
		<script type="text/javascript">jQuery(document).ready(function($){$('select#webcomic_file_interval').change(function(){if('week'==$(this).val()){ $('#interval').show(); }else{ $('#interval').hide();}});});</script>
		<?php
		} else {
			$user_meta = current( get_user_meta( $current_user->ID, 'webcomic' ) );
			
			if ( empty( $user_meta[ 'files_per_page' ] ) ) {
				$user_meta[ 'files_per_page' ] = 20;
				update_user_meta( $current_user->ID, 'webcomic', $user_meta );
			} elseif ( !empty( $_REQUEST[ 'webcomic_files_per_page' ] ) ) {
				$user_meta[ 'files_per_page' ] = intval( $_REQUEST[ 'webcomic_files_per_page' ] );
				update_user_meta( $current_user->ID, 'webcomic', $user_meta );
			}
			
			$fpp = $user_meta[ 'files_per_page' ];
			
			$abs         = $this->directory( 'abs', $wc->slug );
			$url         = $this->directory( 'url', $wc->slug );
			$tabs        = $abs . 'thumbs/';
			$turl        = $url . 'thumbs/';
			$files       = glob( $abs . '*.*' );
			$posts       = get_objects_in_term( $wc->term_id, 'webcomic_collection' );
			$orphans     = $matched = $count = array();
			$term_meta   = $this->option( 'term_meta' );
			$post_object = get_post_type_object( 'webcomic_post' );
			
			$count[ 'all' ] = $count[ 'future' ] = $count[ 'publish' ] = $count[ 'private' ] = $count[ 'pending' ] = $count[ 'draft' ] = $count[ 'trash' ] = $count[ 'orphaned' ] = $count[ 'matched' ] = 0;
			
			foreach ( $term_meta as $taxonomy )
				foreach ( $taxonomy as $term )
					if ( !empty( $term[ 'files' ] ) )
						foreach ( $term[ 'files' ][ 'full' ] as $k => $v )
							if ( false !== ( $key = array_search( $abs . $term[ 'files' ][ 'full' ][ $k ], $files ) ) )
								unset( $files[ $key ] );
			
			foreach ( $posts as $k => $v ) {
				if ( 'webcomic_post' != get_post_type( $v ) )
					continue;
				
				$status = get_post_status( $v );
				
				if ( 'trash' != $status )
					$count[ 'all' ]++;
				
				switch( $status ) {
					case 'future' : $count[ 'future' ]++; break;
					case 'publish': $count[ 'publish' ]++; break;
					case 'private': $count[ 'private' ]++; break;
					case 'pending': $count[ 'pending' ]++; break;
					case 'draft'  : $count[ 'draft' ]++; break;
					case 'trash'  : $count[ 'trash' ]++; break;
				}
				
				$post_meta  = current( get_post_meta( $v, 'webcomic' ) );
				$post_files = $this->fetch( $v, 'post', $wc->slug, true );
				
				if ( empty( $post_files ) && 'trash' != $status ) {
					array_push( $orphans, $v );
					$count[ 'orphaned' ]++;
				} elseif ( !empty( $post_files ) ) {
					if ( empty( $post_meta[ 'files' ] ) && 'trash' != $status ) {
						array_push( $matched, $v );
						$count[ 'matched' ]++;
					}
					
					foreach ( $post_files[ 'full' ] as $f ) {
						$fk = array_search( $abs . $f, $files );
						unset( $files[ $fk ] );
					}
				}
			}
			
			if ( !empty( $_REQUEST[ 'webcomic_storyline' ] ) ) {
				$a = get_objects_in_term( $_REQUEST[ 'webcomic_storyline' ], 'webcomic_storyline' );
				$posts = array_intersect( $posts, $a );
			}
			
			if ( !empty( $_REQUEST[ 'webcomic_character' ] ) ) {
				$a = get_objects_in_term( $_REQUEST[ 'webcomic_character' ], 'webcomic_character' );
				$posts = array_intersect( $posts, $a );
			}
			
			$status = ( !empty( $_REQUEST[ 'subpage' ] ) && 'orphaned' != $_REQUEST[ 'subpage' ] && 'matched' != $_REQUEST[ 'subpage' ] ) ? "p.post_status = '" . $_REQUEST[ 'subpage' ] . "'" : "p.post_status != 'trash'";
			$search = ( !empty( $_REQUEST[ 's' ] ) ) ? "AND ( ( p.post_title LIKE '%" . $wpdb->escape( $_REQUEST[ 's' ] ) . "%' ) OR ( p.post_content LIKE '%" . $wpdb->escape( $_REQUEST[ 's' ] ) . "%' ) OR ( p.post_excerpt LIKE '%" . $wpdb->escape( $_REQUEST[ 's' ] ) . "%' ) OR ( pm.meta_value LIKE '%" . $wpdb->escape( $_REQUEST[ 's' ] ) . "%' ) )" : '';
			$join   = ( $search ) ? " LEFT JOIN $wpdb->postmeta AS pm ON p.ID = pm.post_id AND pm.meta_key = 'webcomic' " : '';
			
			if ( isset( $_REQUEST[ 'subpage' ] ) && 'orphaned' == $_REQUEST[ 'subpage' ] )
				$posts = $orphans;
			elseif ( isset( $_REQUEST[ 'subpage' ] ) && 'matched' == $_REQUEST[ 'subpage' ] )
				$posts = $matched;
			
			$author_posts = ( empty( $posts ) ) ? array() : $wpdb->get_col( "SELECT * FROM $wpdb->posts AS p $join WHERE p.ID IN (" . implode( ',', $posts ) . ") AND p.post_type = 'webcomic_post' AND p.post_status != 'trash' AND p.post_author = " . $current_user->ID . " $search ORDER BY post_date DESC" );
			
			$count[ 'mine' ] = count( $author_posts );
			
			$posts = ( ( isset( $_REQUEST[ 'subpage' ] ) && 'mine' == $_REQUEST[ 'subpage' ] ) || empty( $posts ) ) ? $author_posts : $wpdb->get_col( "SELECT * FROM $wpdb->posts AS p $join WHERE p.ID IN (" . implode( ',', $posts ) . ") AND p.post_type = 'webcomic_post' AND $status $search ORDER BY post_date DESC" );
			
			foreach ( $posts as $k => $v )
				$posts[ $k ] = ( object ) array( 'ID' => $v, 'post_parent' => 0 );
			
			$max_post = ( count( $posts ) < ( $pagenum * $fpp ) ) ? count( $posts ) : $pagenum * $fpp;
		?>
		<style>.widefat img{max-height:<?php echo $this->option( 'small_h' ); ?>px;max-width:<?php echo $this->option( 'small_w' ); ?>px}#availablethemes img{height:auto;max-height:<?php echo $this->option( 'small_h' ); ?>px;max-width:<?php echo $this->option( 'small_w' ); ?>px;width:auto}#availablethemes input[type=checkbox]{display:none}#availablethemes label{border:1px solid transparent;display:inline-block;padding:.25em}#availablethemes input:checked + label{background:#ffffe0;border:1px solid #e6db55}hr{border:3px double #ddd}</style>
		<div class="wrap">
			<div id="icon-webcomic" class="icon32"><img src="<?php echo $this->url . 'webcomic-includes/icon.png'; ?>" alt="icon"></div>
			<h2><?php printf( __( '%s in %s', 'webcomic' ), $post_object->labels->name, $wc->name ); ?> <a href="post-new.php?post_type=webcomic_post&amp;webcomic_collection=<?php echo $wc->term_id; ?>" class="button add-new-h2"><?php echo $post_object->labels->add_new; ?></a><?php if ( current_user_can( 'manage_categories' ) ) { ?><a href="<?php echo wp_nonce_url( $view . '&amp;action=bulk_webcomic_upload', 'bulk_webcomic_upload' ); ?>" class="button add-new-h2"><?php _e( 'Upload', 'webcomic' ); ?></a><?php } ?></h2>
			<form action="<?php echo $view; ?>" method="post">
			<p class="search-box">
				<select name="webcomic_collection" id="webcomic_collection">
				<?php
					$walker = new webcomic_Walker_AdminTermDropdown();
					$selected = array( $wc->term_id );
					echo $walker->walk( get_terms( 'webcomic_collection', 'get=all' ), 0, array( 'selected' => $selected ) );
				?>
				</select>
				<label class="screen-reader-text" for="s"><?php echo $post_object->labels->search_items; ?></label>
				<input type="text" id="s" name="s" value="<?php if ( !empty( $_REQUEST[ 's' ] ) ) echo $_REQUEST[ 's' ]; ?>">
				<input type="submit" value="<?php echo $post_object->labels->search_items; ?>" class="button">
			</p>
			</form>
			<form action="<?php echo $view; ?>" method="post">
				<?php wp_nonce_field( 'bulk_webcomic_file' ); ?>
				<ul class="subsubsub">
					<?php if ( !current_user_can( 'edit_others_posts' ) ) { ?><li><a href="<?php echo '?page=' . $page . '&amp;webcomic_collection=' . $wc->term_id . '&amp;subpage=mine'; ?>"<?php if ( 'mine' == $_REQUEST[ 'subpage' ] ) echo ' class="current"'; ?>><?php printf( __( 'Mine <span class="count">(%s)</span>', 'webcomic' ), $count[ 'mine' ] ); ?></a> |</li><?php } ?>
					<li><a href="<?php echo '?page=' . $page . '&amp;webcomic_collection=' . $wc->term_id; ?>"<?php if ( empty( $_REQUEST[ 'subpage' ] ) ) echo ' class="current"'; ?>><?php printf( __( 'All <span class="count">(%s)</span>', 'webcomic' ), $count[ 'all' ] ); ?></a></li>
					<?php if ( $count[ 'matched' ] ) { ?><li>| <a href="<?php echo '?page=' . $page . '&amp;webcomic_collection=' . $wc->term_id . '&amp;subpage=matched'; ?>"<?php if ( isset( $_REQUEST[ 'subpage' ] ) && 'matched' == $_REQUEST[ 'subpage' ] ) echo ' class="current"'; ?>><?php printf( __( 'Matched <span class="count">(%s)</span>', 'webcomic' ), $count[ 'matched' ] ); ?></a></li><?php } ?>
					<?php if ( $count[ 'orphaned' ] ) { ?><li>| <a href="<?php echo '?page=' . $page . '&amp;webcomic_collection=' . $wc->term_id . '&amp;subpage=orphaned'; ?>"<?php if ( isset( $_REQUEST[ 'subpage' ] ) && 'orphaned' == $_REQUEST[ 'subpage' ] ) echo ' class="current"'; ?>><?php printf( __( 'Orphaned <span class="count">(%s)</span>', 'webcomic' ), $count[ 'orphaned' ] ); ?></a></li><?php } ?>
					<?php if ( $count[ 'publish' ] ) { ?><li>| <a href="<?php echo '?page=' . $page . '&amp;webcomic_collection=' . $wc->term_id . '&amp;subpage=publish'; ?>"<?php if ( isset( $_REQUEST[ 'subpage' ] ) && 'publish' == $_REQUEST[ 'subpage' ] ) echo ' class="current"'; ?>><?php printf( __( 'Published <span class="count">(%s)</span>', 'webcomic' ), $count[ 'publish' ] ); ?></a></li><?php } ?>
					<?php if ( $count[ 'future' ] ) { ?><li>| <a href="<?php echo '?page=' . $page . '&amp;webcomic_collection=' . $wc->term_id . '&amp;subpage=future'; ?>"<?php if ( isset( $_REQUEST[ 'subpage' ] ) && 'future' == $_REQUEST[ 'subpage' ] ) echo ' class="current"'; ?>><?php printf( __( 'Scheduled <span class="count">(%s)</span>', 'webcomic' ), $count[ 'future' ] ); ?></a></li><?php } ?>
					<?php if ( $count[ 'draft' ] ) { ?><li>| <a href="<?php echo '?page=' . $page . '&amp;webcomic_collection=' . $wc->term_id . '&amp;subpage=draft'; ?>"<?php if ( isset( $_REQUEST[ 'subpage' ] ) && 'draft' == $_REQUEST[ 'subpage' ] ) echo ' class="current"'; ?>><?php printf( __( 'Draft <span class="count">(%s)</span>', 'webcomic' ), $count[ 'draft' ] ); ?></a></li><?php } ?>
					<?php if ( $count[ 'pending' ] ) { ?><li>| <a href="<?php echo '?page=' . $page . '&amp;webcomic_collection=' . $wc->term_id . '&amp;subpage=pending'; ?>"<?php if ( isset( $_REQUEST[ 'subpage' ] ) && 'pending' == $_REQUEST[ 'subpage' ] ) echo ' class="current"'; ?>><?php printf( __( 'Pending <span class="count">(%s)</span>', 'webcomic' ), $count[ 'pending' ] ); ?></a></li><?php } ?>
					<?php if ( $count[ 'private' ] ) { ?><li>| <a href="<?php echo '?page=' . $page . '&amp;webcomic_collection=' . $wc->term_id . '&amp;subpage=private'; ?>"<?php if ( isset( $_REQUEST[ 'subpage' ] ) && 'private' == $_REQUEST[ 'subpage' ] ) echo ' class="current"'; ?>><?php printf( __( 'Private <span class="count">(%s)</span>', 'webcomic' ), $count[ 'private' ] ); ?></a></li><?php } ?>
					<?php if ( $count[ 'trash' ] ) { ?><li>| <a href="<?php echo '?page=' . $page . '&amp;webcomic_collection=' . $wc->term_id . '&amp;subpage=trash'; ?>"<?php if ( isset( $_REQUEST[ 'subpage' ] ) && 'trash' == $_REQUEST[ 'subpage' ] ) echo ' class="current"'; ?>><?php printf( __( 'Trash <span class="count">(%s)</span>', 'webcomic' ), $count[ 'trash' ] ); ?></a></li><?php } ?>
				</ul>
				<?php if ( $posts ) { ?>
				<div class="tablenav">
					<div class="tablenav-pages"><span class="displaying-num"><?php if ( count( $posts ) > $fpp ) printf( __( 'Displaying %1$d &#8211; %2$d of %3$d', 'webcomic' ) , ( ( $pagenum - 1 ) * $fpp + 1 ), $max_post, count( $posts ) ); ?></span><?php echo paginate_links( array( 'base' => 'admin.php' . $view . '%_%', 'format' => '&amp;pagenum=%#%', 'prev_text' => __( '&laquo;', 'webcomic' ), 'next_text' => __( '&raquo;', 'webcomic' ), 'total' => ceil ( count( $posts ) / $fpp ), 'current' => $pagenum ) ); ?></div>
					<div class="alignleft actions">
						<select name="action-1">
							<option value=""><?php _e( 'Bulk Actions', 'webcomic' ); ?></option>
							<?php if ( isset( $_REQUEST[ 'subpage' ] ) && 'trash' == $_REQUEST[ 'subpage' ] ) { ?>
							<option value="undelete"><?php _e( 'Restore', 'webcomic' ); ?></option>
							<option value="delete_post"><?php _e( 'Delete Permanently', 'webcomic' ); ?></option>
							<?php } elseif ( isset( $_REQUEST[ 'subpage' ] ) && 'orphaned' == $_REQUEST[ 'subpage' ] ) { ?>
							<option value="batch_post"><?php _e( 'Edit', 'webcomic' ); ?></option>
							<option value="delete_post"><?php _e( 'Move to Trash', 'webcomic' ); ?></option>
							<?php } else { ?>
								<optgroup label="<?php _e( 'Files' ); ?>">
									<option value="bind"><?php _e( 'Bind', 'webcomic' ); ?></option>
									<?php if ( empty( $_REQUEST[ 'subpage' ] ) || 'matched' != $_REQUEST[ 'subpage' ] ) { ?><option value="unbind"><?php _e( 'Unbind', 'webcomic' ); ?></option><?php } ?>
									<option value="regen"><?php _e( 'Regenerate Thumbnails', 'webcomic' ); ?></option>
									<option value="delete_file"><?php _e( 'Delete', 'webcomic' ); ?></option>
								</optgroup>
								<optgroup label="<?php _e( 'Posts', 'webcomic' ); ?>">
									<option value="batch_post"><?php _e( 'Edit', 'webcomic' ); ?></option>
									<option value="delete_post"><?php _e( 'Move to Trash', 'webcomic' ); ?></option>
								</optgroup>
								<optgroup label="<?php _e( 'Files &amp; Posts', 'webcomic' ); ?>">
									<option value="delete_filepost"><?php _e( 'Delete', 'webcomic' ); ?></option>
								</optgroup>
							<?php } ?>
						</select>
						<input type="submit" value="<?php _e( 'Apply', 'webcomic' ); ?>" name="submit-1" id="doaction" class="button-secondary action">
						<select name="webcomic_storyline">
							<option value=""><?php _e( 'Show all storylines', 'webcomic' ); ?></option>
							<?php
								$walker = new webcomic_Walker_AdminTermDropdown();
								$req_story = ( !empty( $_REQUEST[ 'webcomic_storyline' ] ) ) ? $_REQUEST[ 'webcomic_storyline' ] : false;
								echo $walker->walk( get_terms( 'webcomic_storyline', 'hide_empty=0&orderby=term_group_name&webcomic_order=1&term_group=' . $wc->term_id ), 0, array( 'selected' => array( $req_story ) ) );
							?>
						</select>
						<select name="webcomic_character">
							<option value=""><?php _e( 'Show all characters', 'webcomic' ); ?></option>
							<?php
								$walker = new webcomic_Walker_AdminTermDropdown();
								$req_character = ( !empty( $_REQUEST[ 'webcomic_character' ] ) ) ? $_REQUEST[ 'webcomic_character' ] : false;
								echo $walker->walk( get_terms( 'webcomic_character', 'hide_empty=0&orderby=term_group_name&term_group=' . $wc->term_id ), 0, array( 'selected' => array( $req_character ) ) );
							?>
						</select>
						<input type="submit" value="<?php _e( 'Filter', 'webcomic' ); ?>" name="filter" class="button-secondary action">
						<?php if ( isset( $_REQUEST[ 'subpage' ] ) && 'trash' == $_REQUEST[ 'subpage' ] ) { ?><input type="submit" name="delete_all" value="<?php _e( 'Empty Trash', 'webcomic' ); ?>" class="button-secondary apply"><?php } ?>
					</div>
				</div>
				<table class="widefat">
					<thead><tr><th scope="col" class="manage-column column-cb check-column"><input type="checkbox"></th><?php print_column_headers( 'toplevel_page_' . $page ); ?></tr></thead>
					<tfoot><tr><th scope="col" class="manage-column column-cb check-column"><input type="checkbox"></th><?php print_column_headers( 'toplevel_page_' . $page ); ?></tr></tfoot>
					<tbody>
						<?php
							$walker = new webcomic_Walker_AdminFileList();
							echo $walker->paged_walk( $posts, 0, $pagenum, $fpp, array( 'hidden' => $hidden, 'view' => $view, 'src' => $wc->slug ) );
						?>
					</tbody>
				</table>
				<div class="tablenav">
					<div class="tablenav-pages"><span class="displaying-num"><?php if ( count( $posts ) > $fpp ) printf( __( 'Displaying %1$d &#8211; %2$d of %3$d', 'webcomic' ) , ( ( $pagenum - 1 ) * $fpp + 1 ), $max_post, count( $posts ) ); ?></span><?php echo paginate_links( array( 'base' => 'admin.php' . $view . '%_%', 'format' => '&amp;pagenum=%#%', 'prev_text' => __( '&laquo;', 'webcomic' ), 'next_text' => __( '&raquo;', 'webcomic' ), 'total' => ceil ( count( $posts ) / $fpp ), 'current' => $pagenum ) ); ?></div>
					<div class="alignleft actions">
						<select name="action-2">
							<option value=""><?php _e( 'Bulk Actions', 'webcomic' ); ?></option>
							<?php if ( isset( $_REQUEST[ 'subpage' ] ) && 'trash' == $_REQUEST[ 'subpage' ] ) { ?>
							<option value="undelete"><?php _e( 'Restore', 'webcomic' ); ?></option>
							<option value="delete_post"><?php _e( 'Delete Permanently', 'webcomic' ); ?></option>
							<?php } elseif ( isset( $_REQUEST[ 'subpage' ] ) && 'orphaned' == $_REQUEST[ 'subpage' ] ) { ?>
							<option value="delete_post"><?php _e( 'Move to Trash', 'webcomic' ); ?></option>
							<?php } else { ?>
								<optgroup label="<?php _e( 'Files' ); ?>">
									<option value="bind"><?php _e( 'Bind', 'webcomic' ); ?></option>
									<?php if ( empty( $_REQUEST[ 'subpage' ] ) || 'matched' != $_REQUEST[ 'subpage' ] ) { ?><option value="unbind"><?php _e( 'Unbind', 'webcomic' ); ?></option><?php } ?>
									<option value="regen"><?php _e( 'Regenerate Thumbnails', 'webcomic' ); ?></option>
									<option value="delete_file"><?php _e( 'Delete', 'webcomic' ); ?></option>
								</optgroup>
								<optgroup label="<?php _e( 'Posts', 'webcomic' ); ?>">
									<option value="batch_post"><?php _e( 'Edit', 'webcomic' ); ?></option>
									<option value="delete_post"><?php _e( 'Move to Trash', 'webcomic' ); ?></option>
								</optgroup>
								<optgroup label="<?php _e( 'Files &amp; Posts', 'webcomic' ); ?>">
									<option value="delete_filepost"><?php _e( 'Delete', 'webcomic' ); ?></option>
								</optgroup>
							<?php } ?>
						</select>
						<input type="submit" value="<?php _e( 'Apply', 'webcomic' ); ?>" name="submit-2" class="button-secondary action">
						<input type="hidden" name="webcomic_collection" value="<?php echo $wc->term_id; ?>">
						<input type="hidden" name="subpage" value="<?php if ( isset( $_REQUEST[ 'subpage' ] ) ) echo $_REQUEST[ 'subpage' ]; ?>">
						<input type="hidden" name="webcomic_type" value="post">
						<input type="hidden" name="action" value="bulk_webcomic_file">
					</div>
				</div>
				<?php } ?>
			</form>
			<?php if ( $files && current_user_can( 'edit_others_posts' ) ) { natcasesort( $files ); if ( $posts ) { ?><br><hr><br><?php } ?>
			<form action="<?php echo $view; ?>" method="post">
				<?php wp_nonce_field( 'bulk_webcomic_file' ); ?>
				<div class="tablenav">
					<div class="tablenav-pages"><input type="submit" name="Submit" class="button-primary" value="<?php _e( 'Update Orphans', 'webcomic' ); ?>"></div>
					<div class="alignleft actions">
						<select name="action-2">
							<option value=""><?php _e( 'Bulk Actions', 'webcomic' ); ?></option>
							<option value="batch_file"><?php _e( 'Generate Posts', 'webcomic' ); ?></option>
							<option value="regen"><?php _e( 'Regenerate Thumbnails', 'webcomic' ); ?></option>
							<option value="delete_file"><?php _e( 'Delete', 'webcomic' ); ?></option>
						</select>
						<label><input type="checkbox" name="webcomic_selectall"> <?php _e( 'Select All', 'webcomic' ); ?></label>
						<input type="hidden" name="webcomic_collection" value="<?php echo $wc->term_id; ?>">
						<input type="hidden" name="webcomic_type" value="orphan">
						<input type="hidden" name="action" value="bulk_webcomic_file">
					</div>
				</div>
				<table id="availablethemes" cellspacing="0" cellpadding="0">
				<?php
					$rows  = ceil ( count( $files ) / 3 );
					$table = array();
					
					for ( $row = 1; $row <= $rows; $row++ )
						for ( $col = 1; $col <= 3; $col++ )
							$table[ $row ][ $col ] = array_shift( $files );
					
					foreach ( $table as $row => $cols ) {
					?>
					<tr>
					<?php
					foreach ( $cols as $col => $file ) {
						$class = array( 'available-theme' );
						
						if ( $row == 1 )     $class[] = 'top';
						if ( $row == $rows ) $class[] = 'bottom';
						if ( $col == 1 )     $class[] = 'left';
						if ( $col == 3 )     $class[] = 'right';
						if ( !$term ) { echo '<td class="' . join( ' ', $class ) . '"></td>'; continue; }
						?>
						<td class="<?php echo join( ' ', $class ); ?>">
						<?php if ( $file ) { $info = pathinfo( $file ); ?>
							<input type="checkbox" name="bulk[<?php echo hash( 'md5', $file ); ?>]" id="webcomic_orphan[<?php echo hash( 'md5', $file ); ?>]" value="<?php echo stripslashes( $file ); ?>">
							<label for="webcomic_orphan[<?php echo hash( 'md5', $file ); ?>]">
								<?php
									$orphan = $this->retrieve( $file, 'orphan', $wc->slug );
									
									if ( $orphan[ 'small' ] )
										echo $orphan[ 'small' ][ 0 ][ 'html' ];
									elseif ( $orphan[ 'medium' ] )
										echo $orphan[ 'medium' ][ 0 ][ 'html' ];
									elseif ( $orphan[ 'large' ] )
										echo $orphan[ 'large' ][ 0 ][ 'html' ];
									else
										echo $orphan[ 'full' ][ 0 ][ 'html' ];
								?>
							</label><br>
							<input type="text" name="webcomic_filename[<?php echo hash( 'md5', $orphan[ 'full' ][ 0 ][ 'basename' ] ); ?>]" value="<?php echo $orphan[ 'full' ][ 0 ][ 'filename' ]; ?>" style="width:100%">
							<input type="hidden" name="webcomic_extension[<?php echo hash( 'md5', $orphan[ 'full' ][ 0 ][ 'basename' ] ); ?>]" value=".<?php echo $orphan[ 'full' ][ 0 ][ 'extension' ]; ?>">
							<input type="hidden" name="webcomic_oldname[<?php echo hash( 'md5', $orphan[ 'full' ][ 0 ][ 'basename' ] ); ?>]" value="<?php echo $orphan[ 'full' ][ 0 ][ 'filename' ]; ?>">
							<br>
							<a href="<?php echo wp_nonce_url( $view . '&amp;action=regen_webcomic_file&amp;webcomic_key=0&amp;orphan=' . $orphan[ 'full' ][ 0 ][ 'basename' ], 'regen_webcomic_file' ); ?>"><?php _e( 'Regenerate Thumbnails', 'webcomic' ); ?></a> |
							<a href="<?php echo wp_nonce_url( $view . '&amp;action=delete_webcomic_file&amp;webcomic_key=0&amp;orphan=' . $orphan[ 'full' ][ 0 ][ 'basename' ], 'delete_webcomic_file' ); ?>" onclick="if(confirm('<?php echo esc_js( sprintf( __( "You are about to delete the orphaned file '%s'\n 'Cancel' to stop, 'OK' to delete.", "webcomic" ), $orphan[ 'full' ][ 0 ][ 'basename' ] ) ); ?>')){return true;}return false;"><?php _e( 'Delete', 'webcomic' ); ?></a> |
							<a href="<?php echo $orphan[ 'full' ][ 0 ][ 'url' ]; ?>" target="_blank"><?php _e( 'View', 'webcomic' ); ?></a>
							<?php } ?>
						</td>
						<?php } ?>
					</tr>
					<?php } ?>
				</table>
			</form>
			<?php } elseif ( !$posts ) { ?>
			<div class="clear"></div>
			<p><?php echo ( 'trash' == $_REQUEST[ 'subpage' ] ) ? $post_object->labels->not_found_in_trash : $post_object->labels->not_found; ?></p>
			<?php } ?>
		</div>
		<script type="text/javascript">var x='<form action="<?php echo $view; ?>" method="post"><input type="hidden" name="page" value="<?php echo $page; ?>"><div class="screen-options"><label><input type="text" name="webcomic_files_per_page" value="<?php echo $fpp; ?>" maxlength="3" class="screen-per-page"> <?php _e( 'Webcomics', 'webcomic' ); ?></label> <input type="submit" value="<?php _e( 'Apply', 'webcomic' ); ?>" class="button-secondary action"><input type="hidden" name="webcomic_collection" value="<?php echo $wc->term_id; ?>"></div></form>';jQuery('#screen-options-wrap').append(x);var t=false;jQuery('input[name=webcomic_selectall]').click(function(){jQuery('#availablethemes input[type=checkbox]').attr('checked',!t);t=!t;});jQuery(document).ready(function($){$('select#webcomic_collection').change(function(){ window.location = '<?php echo admin_url( 'admin.php?page=' . $page . '&webcomic_collection=' ); ?>' + $(this).val();});});</script>
		<?php
		}
	}
	
	/**
	 * Displays the taxonomy management pages.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function admin_terms() {
		$this->domain();
		
		global $current_user;
		
		$wc        = ( !empty( $_REQUEST[ 'webcomic_collection' ] ) ) ? get_term( $_REQUEST[ 'webcomic_collection' ], 'webcomic_collection' ) : get_term( ( int ) $this->option( 'default_collection' ), 'webcomic_collection' );
		$page      = $_REQUEST[ 'page' ];
		$pagenum   = ( !empty( $_REQUEST[ 'pagenum' ] ) ) ? $_REQUEST[ 'pagenum' ] : 1;
		$hidden    = get_hidden_columns( 'webcomic_page_' . $page );
		$find      = ( !empty( $_REQUEST[ 's' ] ) ) ? '&amp;s=' . $_REQUEST[ 's' ] : '';
		$view      = ( 'webcomic_collection' == $page ) ? '?page=' . $page : '?page=' . $page . '&amp;webcomic_collection=' . $wc->term_id . $find;
		$img_field = ( 'webcomic_character' == $page ) ? __( 'avatar', 'webcomic' ) : __( 'cover', 'webcomic' );
		$taxonomy  = get_taxonomy( $page );
		
		$user_meta = current( get_user_meta( $current_user->ID, 'webcomic' ) );
		
		if ( empty( $user_meta[ $page . '_per_page' ] ) ) {
			$user_meta[ $page . '_per_page' ] = 20;
			update_user_meta( $current_user->ID, 'webcomic', $user_meta );
		} elseif ( !empty( $_REQUEST[ $page . '_per_page' ] ) ) {
			$user_meta[ $page . '_per_page' ] = intval( $_REQUEST[ $page . '_per_page' ] );
			update_user_meta( $current_user->ID, 'webcomic', $user_meta );
		}
		
		$tpp = $user_meta[ $page . '_per_page' ];
		
		if ( $current_user->data->rich_editing ) {
			$tinymce_width = ( !empty( $_REQUEST[ 'subpage' ] ) ) ? '460px' : '100%';
			wp_tiny_mce( false, array( 'editor_selector' => 'webcomic_tinymce', 'width' => $tinymce_width, 'theme_advanced_buttons1' => implode( ',', array( 'bold', 'italic', '|', 'bullist', 'numlist', 'blockquote', '|', 'link', 'unlink', '|', 'charmap', 'spellchecker', 'fullscreen', 'wp_adv' ) ), 'theme_advanced_buttons2' => implode( ',', array( 'formatselect', '|', 'justifyleft', 'justifycenter', 'justifyright', 'justifyfull',  '|', 'sub', 'sup', '|', 'removeformat', 'code' ) ) ) );
		}
		
		if ( 'webcomic_collection' != $page && ( !$wc || is_wp_error( $wc ) ) ) {
		?>
		<div class="wrap">
			<div id="icon-webcomic" class="icon32"><img src="<?php echo $this->url . 'webcomic-includes/icon.png'; ?>" alt="icon"></div>
			<h2><?php _e( 'Webcomic Error', 'webcomc' ); ?></h2>
			<p><?php printf( __( "Hold up: it looks like you don't have any collections! You should definitely <a href='%s'>create a collection</a> before you go any further.", 'webcomic' ), admin_url( 'admin.php?page=webcomic_collection' ) ); ?></p>
		</div>
		<?php
		} elseif ( isset( $_REQUEST[ 'subpage' ] ) && 'edit_webcomic_term' == $_REQUEST[ 'subpage' ] ) {
			$term = get_term_to_edit( $_REQUEST[ 'webcomic_term' ], $page );
		?>
		<style>#screen-options-link-wrap{display:none}th img{height:auto;max-height:<?php echo $this->option( 'small_h' ); ?>px;max-width:<?php echo $this->option( 'small_w' ); ?>px;width:auto}.form-field input[type=checkbox]{width:auto}</style>
		<div class="wrap">
			<div id="icon-webcomic" class="icon32"><img src="<?php echo $this->url . 'webcomic-includes/icon.png'; ?>" alt="icon"></div>
			<h2><?php echo $taxonomy->labels->edit_item; ?></h2>
			<form action="<?php echo $view; ?>" method="post" enctype="multipart/form-data" class="media-upload-form">
				<?php wp_nonce_field( 'update_webcomic_term' ); ?>
				<div class="media-single">
					<table class="slidetoggle describe form-table">
						<thead class="media-item-info">
							<tr>
								<th rowspan="5" class="label">
									<label for="webcomic_file">
										<?php echo ucfirst( $img_field ); ?><br>
										<?php
										if ( !empty( $term->webcomic_files ) ) {
											foreach ( $term->webcomic_files[ 'full' ] as $k => $v ) {
												if ( $term->webcomic_files[ 'small' ][ $k ] )
													echo $term->webcomic_files[ 'small' ][ $k ][ 'html' ];
												elseif ( $term->webcomic_files[ 'medium' ][ $k ] )
													echo $term->webcomic_files[ 'medium' ][ $k ][ 'html' ];
												elseif ( $term->webcomic_files[ 'large' ][ $k ] )
													echo $term->webcomic_files[ 'large' ][ $k ][ 'html' ];
												else
													echo $v[ 'html' ];
											}
										}
										?>
									</label>
								</th>
								<td>
									<p><input type="file" name="webcomic_file[]" id="webcomic_file"></p>
									<?php if ( !empty( $term->webcomic_files ) ) { foreach ( $term->webcomic_files[ 'full' ] as $k => $v ) { ?>
									<input type="text" name="webcomic_filename[<?php echo $k; ?>]" value="<?php echo $v[ 'filename' ]; ?>">
									<input type="hidden" name="webcomic_extension[<?php echo $k; ?>]" value=".<?php echo $v[ 'extension' ]; ?>">
									<select name="webcomic_action[<?php echo $k; ?>]">
										<option value=""><?php _e( 'File Actions', 'webcomic' ); ?></option>
										<?php if ( 'image/jpeg' == $v[ 'mime' ] || 'image/gif' == $v[ 'mime' ] || 'image/png' == $v[ 'mime' ] ) { ?><option value="regen"><?php _e( 'Regenerate Thumbnails', 'webcomic' ); ?></option><?php } ?>
										<option value="delete"><?php _e( 'Delete', 'webcomic' ); ?></option>
									</select><br>
									<?php
										$s = array();
										
										if ( isset( $term->webcomic_files[ 'large' ][ $k ] ) )
											$s[] = __( 'large', 'webcomic' );
										if ( isset( $term->webcomic_files[ 'medium' ][ $k ] ) )
											$s[] = __( 'medium', 'webcomic' );
										if ( isset( $term->webcomic_files[ 'small' ][ $k ] ) )
											$s[] = __( 'small', 'webcomic' );
										
										if ( 1 < count( $s ) )
											printf( __( '%s sizes available', 'webcomic' ), ucfirst( substr_replace( implode( ', ', $s ), __( ' and', 'webcomic' ), strrpos( implode( ', ', $s ), ',' ), 1 ) ) );
										elseif ( !empty( $s ) )
											printf( __( '%s size available', 'webcomic' ), ucfirst( current( $s ) ) );
										else
											_e( 'No thumbnail sizes available', 'webcomic' );
										
										echo ' | <a href="' . admin_url( 'admin.php?page=webcomic_tools&subpage=edit_files&type=' . end( explode( '_', $page ) ) . '&src=' . $wc->slug . '&id=' . $term->term_id . '&key=' . $k ) . '">' . __( 'Edit', 'webcomic' ) . '</a>';
									} }
									?>
								</td>
							</tr>
						</thead>
						<tbody>
							<tr class="form-field">
								<th scope="row" class="label"><label for="webcomic_name"><?php _e( 'Name', 'webcomic' ); ?></label></th>
								<td>
									<input type="text" name="webcomic_name" value="<?php echo $term->name; ?>" id="webcomic_name"><br>
									<?php printf( __( 'The name is how the %s appears on your site.', 'webcomic' ), strtolower( $taxonomy->labels->singular_name ) ); ?>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" class="label"><label for="webcomic_nicename"><?php _e( 'Slug', 'webcomic' ); ?></label></th>
								<td>
									<input type="text" name="webcomic_nicename" value="<?php echo $term->slug; ?>" id="webcomic_nicename"><br>
									<?php _e( 'The &#8220;slug&#8221; is the URL-friendly version of the name.', 'webcomic' ); ?>
								</td>
							</tr>
							<?php if ( 'webcomic_collection' == $page ) { ?>
							<tr class="form-field">
								<th scope="row" class="label"><label for="webcomic_group"><?php _e( 'Age', 'webcomic' ); ?></label></th>
								<td>
									<input type="text" name="webcomic_group" size="2" maxlength="2" value="<?php echo $term->term_group; ?>" style="text-align:center;width:auto" id="webcomic_group"><br>
									<?php printf( __( 'The minimum recommended age for this %s.', 'webcomic' ), strtolower( $taxonomy->labels->singular_name ) ); ?>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" class="label"><label for="webcomic_restrict"><?php _e( 'Restrict', 'webcomic' ); ?></label></th>
								<td>
									<label><input type="checkbox" name="webcomic_restrict" id="webcomic_restrict"<?php if ( $term->webcomic_restrict ) echo ' checked';?>> <?php printf( __( 'Users must be registered and logged-in to view webcomics in this %s', 'webcomic' ), strtolower( $taxonomy->labels->singular_name ) ); ?></label>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" class="label"><label for="webcomic_theme"><?php _e( 'Theme', 'webcomic' ); ?></label></th>
								<td>
									<select name="webcomic_theme" id="webcomic_theme">
										<option value="0"><?php _e( '&ndash; Current Theme &ndash;', 'webcomic' ); ?></option>
										<?php
											$default = get_option( 'stylesheet' );
											$themes  = get_themes();
											
											foreach ( $themes as $t ) {
												if ( $t[ 'Stylesheet' ] == $default )
													continue;
												
												echo '<option value="' . $t[ 'Stylesheet' ] . '"' . ( ( $t[ 'Stylesheet' ] == $term->webcomic_theme ) ? ' selected' : '' ) . '>' . $t[ 'Name' ] . '</option>';
											}
										?>
									</select><br>
									<?php printf( __( 'The stylesheet from this theme will be used for any pages related to this %s.', 'webcomic' ), strtolower( $taxonomy->labels->singular_name ) ); ?>
								</td>
							</tr>
							<tr class="form-field">
								<th scope="row" class="label"><label for="webcomic_bookend_first"><b><?php _e( 'Bookends', 'webcomic' ); ?></b></label></th>
								<td>
									<label for="webcomic_bookend_first"><?php _e( 'First: ', 'webcomic' ); wp_dropdown_pages( array( 'post_type' => 'page', 'selected' => $term->webcomic_bookend[ 'first' ], 'name' => 'webcomic_bookend_first', 'show_option_none' => __( 'None', 'webcomic' ), 'sort_column'=> 'menu_order, post_title' ) ); ?></label>
									<label for="webcomic_bookend_last"><?php _e( 'Last: ', 'webcomic' ); wp_dropdown_pages( array( 'post_type' => 'page', 'selected' => $term->webcomic_bookend[ 'last' ], 'name' => 'webcomic_bookend_last', 'show_option_none' => __( 'None', 'webcomic' ), 'sort_column'=> 'menu_order, post_title' ) ); ?></label><br>
									<?php printf( __( 'The very first and very last pages when browsing webcomics in this %s.', 'webcomic' ), strtolower( $taxonomy->labels->singular_name ) ); ?>
								</td>
							</tr>
							<?php } elseif ( 'webcomic_storyline' == $page || 'webcomic_character' == $page ) { ?>
							<tr class="form-field">
								<th scope="row" class="label"><label for="webcomic_parent"><?php _e( 'Parent', 'webcomic' ); ?></label></th>
								<td>
									<select name="webcomic_parent" id="webcomic_parent">
										<option value="0"><?php _e( 'None', 'webcomic' ); ?></option>
										<?php
											$order  = ( 'webcomic_storyline' == $page ) ? '&webcomic_order=1' : '';
											$terms  = get_terms( $page, 'exclude=' . $_REQUEST[ 'webcomic_term' ] . '&hide_empty=0' . $order . '&term_group=' . $wc->term_id );
											$walker = new webcomic_Walker_AdminTermParent();
											echo $walker->walk( $terms, 0, array( 'parent' => $term->parent ) );
										?>
									</select><br>
									<?php printf( __( '%s are hierarchical, so one %s may contain any number of other %s.', 'webcomic' ), $taxonomy->labels->name, strtolower( $taxonomy->labels->singular_name ), strtolower( $taxonomy->labels->name ) ); ?>
								</td>
							</tr>
							<?php } ?>
							<tr class="form-field">
								<th scope="row" class="label"><label for="webcomic_description"><?php _e( 'Description', 'webcomic' ); ?></label></th>
								<td>
									<textarea name="webcomic_description" id="webcomic_description" rows="5" cols="40" class="webcomic_tinymce"><?php echo $term->description; ?></textarea><br>
									<?php printf( __( 'The description is useful for providing a brief explanation of the %s.', 'webcomic' ), strtolower( $taxonomy->labels->singular_name ) ); ?>
								</td>
							</tr>
							<?php if ( 'webcomic_collection' == $page ) { ?>
							<tr class="form-field">
								<th scope="row" class="label"><label for="webcomic_paypal_price_amount_d"><b><?php _e( 'Prints', 'webcomic' ); ?></b></label></th>
								<td style="padding:0">
									<!-- Nested tables, I know. Forgive me. >.< -->
									<table>
										<thead>
											<tr>
												<td colspan="3"><label><input type="checkbox" name="webcomic_paypal_prints" value="1"<?php if ( $term->webcomic_paypal[ 'prints' ] || ( !isset( $term->webcomic_paypal[ 'prints' ] ) && $this->option( 'paypal_prints' ) ) ) echo ' checked'; ?>> <?php printf( __( 'Sell prints of new webcomics in this %s', 'webcomic' ), strtolower( $taxonomy->labels->singular_name ) ); ?></label></td>
											</tr>
											<tr>
												<td></td>
												<td><i><?php _e( 'Price', 'webcomic' ); ?></i></td>
												<td><i><?php _e( 'Shipping', 'webcomic' ); ?></i></td>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td><i><?php _e( 'Domestic', 'webcomic' ); ?></i></td>
												<td>
													<?php printf ( '%.2f', $this->option( 'paypal_price_d' ) ); ?>
													<select name="webcomic_paypal_price_type_d">
														<option value="add"<?php if ( $term->webcomic_paypal[ 'price_d' ] > 0 ) echo ' selected'; ?>>+</option>
														<option value="sub"<?php if ( $term->webcomic_paypal[ 'price_d' ] < 0 ) echo ' selected'; ?>>&minus;</option>
													</select>
													<input type="text" name="webcomic_paypal_price_d" size="1" value="<?php echo abs( $term->webcomic_paypal[ 'price_d' ] ); ?>" style="text-align:center;width:auto" id="webcomic_paypal_price_d">% =
													<span class="webcomic_paypal_price_d"><?php printf( __( '%.2f %s', 'webcomic' ), $this->price( $this->option( 'paypal_price_d' ), array( $term->webcomic_paypal[ 'price_d' ] ) ), $this->option( 'paypal_currency' ) ); ?></span>
												</td>
												<td>
													<?php printf ( '%.2f', $this->option( 'paypal_shipping_d' ) ); ?>
													<select name="webcomic_paypal_shipping_type_d">
														<option value="add"<?php if ( $term->webcomic_paypal[ 'shipping_d' ] > 0 ) echo ' selected'; ?>>+</option>
														<option value="sub"<?php if ( $term->webcomic_paypal[ 'shipping_d' ] < 0 ) echo ' selected'; ?>>&minus;</option>
													</select>
													<input type="text" name="webcomic_paypal_shipping_d" size="1" value="<?php echo abs( $term->webcomic_paypal[ 'shipping_d' ] ); ?>" style="text-align:center;width:auto" id="webcomic_paypal_shipping_d">% =
													<span class="webcomic_paypal_shipping_d"><?php printf( __( '%.2f %s', 'webcomic' ), $this->price( $this->option( 'paypal_shipping_d' ), array( $term->webcomic_paypal[ 'shipping_d' ] ) ), $this->option( 'paypal_currency' ) ); ?></span>
												</td>
											</tr>
											<tr>
												<td><i><?php _e( 'International', 'webcomic' ); ?></i></td>
												<td>
													<?php printf ( '%.2f', $this->option( 'paypal_price_i' ) ); ?>
													<select name="webcomic_paypal_price_type_i">
														<option value="add"<?php if ( $term->webcomic_paypal[ 'price_i' ] > 0 ) echo ' selected'; ?>>+</option>
														<option value="sub"<?php if ( $term->webcomic_paypal[ 'price_i' ] < 0 ) echo ' selected'; ?>>&minus;</option>
													</select>
													<input type="text" name="webcomic_paypal_price_i" size="1" value="<?php echo abs( $term->webcomic_paypal[ 'price_i' ] ); ?>" style="text-align:center;width:auto" id="webcomic_paypal_price_i">% =
													<span class="webcomic_paypal_price_i"><?php printf( __( '%.2f %s', 'webcomic' ), $this->price( $this->option( 'paypal_price_i' ), array( $term->webcomic_paypal[ 'price_i' ] ) ), $this->option( 'paypal_currency' ) ); ?></span>
												</td>
												<td>
													<?php printf ( '%.2f', $this->option( 'paypal_shipping_i' ) ); ?>
													<select name="webcomic_paypal_shipping_type_i">
														<option value="add"<?php if ( $term->webcomic_paypal[ 'shipping_i' ] > 0 ) echo ' selected'; ?>>+</option>
														<option value="sub"<?php if ( $term->webcomic_paypal[ 'shipping_i' ] < 0 ) echo ' selected'; ?>>&minus;</option>
													</select>
													<input type="text" name="webcomic_paypal_shipping_i" size="1" value="<?php echo abs( $term->webcomic_paypal[ 'shipping_i' ] ); ?>" style="text-align:center;width:auto" id="webcomic_paypal_shipping_i">% =
													<span class="webcomic_paypal_shipping_i"><?php printf( __( '%.2f %s', 'webcomic' ), $this->price( $this->option( 'paypal_shipping_i' ), array( $term->webcomic_paypal[ 'shipping_i' ] ) ), $this->option( 'paypal_currency' ) ); ?></span>
												</td>
											</tr>
											<tr>
												<td><i><?php _e( 'Original', 'webcomic' ); ?></i></td>
												<td>
													<?php printf ( '%.2f', $this->option( 'paypal_price_o' ) ); ?>
													<select name="webcomic_paypal_price_type_o">
														<option value="add"<?php if ( $term->webcomic_paypal[ 'price_o' ] > 0 ) echo ' selected'; ?>>+</option>
														<option value="sub"<?php if ( $term->webcomic_paypal[ 'price_o' ] < 0 ) echo ' selected'; ?>>&minus;</option>
													</select>
													<input type="text" name="webcomic_paypal_price_o" size="1" value="<?php echo abs( $term->webcomic_paypal[ 'price_o' ] ); ?>" style="text-align:center;width:auto" id="webcomic_paypal_price_o">% =
													<span class="webcomic_paypal_price_o"><?php printf( __( '%.2f %s', 'webcomic' ), $this->price( $this->option( 'paypal_price_o' ), array( $term->webcomic_paypal[ 'price_o' ] ) ), $this->option( 'paypal_currency' ) ); ?></span>
												</td>
												<td>
													<?php printf ( '%.2f', $this->option( 'paypal_shipping_o' ) ); ?>
													<select name="webcomic_paypal_shipping_type_o">
														<option value="add"<?php if ( $term->webcomic_paypal[ 'shipping_o' ] > 0 ) echo ' selected'; ?>>+</option>
														<option value="sub"<?php if ( $term->webcomic_paypal[ 'shipping_o' ] < 0 ) echo ' selected'; ?>>&minus;</option>
													</select>
													<input type="text" name="webcomic_paypal_shipping_o" size="1" value="<?php echo abs( $term->webcomic_paypal[ 'shipping_o' ] ); ?>" style="text-align:center;width:auto" id="webcomic_paypal_shipping_o">% =
													<span class="webcomic_paypal_shipping_o"><?php printf( __( '%.2f %s', 'webcomic' ), $this->price( $this->option( 'paypal_shipping_o' ), array( $term->webcomic_paypal[ 'shipping_o' ] ) ), $this->option( 'paypal_currency' ) ); ?></span>
												</td>
											</tr>
										</tbody>
									</table>
									<?php printf( __( 'These print options apply to all webcomics in this %s.', 'webcomic' ), strtolower( $taxonomy->labels->singular_name ) ); ?>
									<script type="text/javascript">jQuery('select[name*=webcomic_paypal],input[name*=webcomic_paypal]').change( function(){var i,x,p,m,f,a,t,s,c,cost_d,cost_i,cost_o,ship_d,ship_i,ship_o,currency;i=jQuery( this ) . attr( 'name' ) . lastIndexOf( '_price' );cost_d=<?php echo $this->price( $this->option( 'paypal_price_d' ), array( $term->webcomic_paypal[ 'price_d' ] ) ); ?>;cost_i=<?php echo $this->price( $this->option( 'paypal_price_i' ), array( $term->webcomic_paypal[ 'price_i' ] ) ); ?>;cost_o=<?php echo $this->price( $this->option( 'paypal_price_o' ), array( $term->webcomic_paypal[ 'price_o' ] ) ); ?>;ship_d=<?php echo $this->price( $this->option( 'paypal_shipping_d' ), array( $term->webcomic_paypal[ 'shipping_d' ] ) ); ?>;ship_i=<?php echo $this->price( $this->option( 'paypal_shipping_i' ), array( $term->webcomic_paypal[ 'shipping_i' ] ) ); ?>;ship_o=<?php echo $this->price( $this->option( 'paypal_shipping_o' ), array( $term->webcomic_paypal[ 'shipping_o' ] ) ); ?>;currency=' <?php echo $this->option( 'paypal_currency' ); ?>';if(0<jQuery(this).attr('name').lastIndexOf('_d')){x='d';c=cost_d;s=ship_d;}else if(0<jQuery(this).attr('name').lastIndexOf('_i')){x='i';c=cost_i;s=ship_i;}else{x='o';c=cost_o;s=ship_o;}a=(i>0)?'webcomic_paypal_price_'+x:'webcomic_paypal_shipping_'+x;t=(i>0)?'webcomic_paypal_price_type_'+x:'webcomic_paypal_shipping_type_'+x;p=(i>0)?c:s;m=p*(Math.abs(jQuery('input[name='+a+']').val())/100);
											f=('sub'==jQuery('select[name='+t+']').val())?p-m:p+m;f=(f<=.01)?'<span class="error">!</span>':f.toFixed(2)+currency;jQuery('.'+a).html(f);});</script>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
				<p class="submit">
					<a href="<?php echo $view; ?>" class="button-secondary"><?php _e( 'Cancel', 'webcomic' ); ?></a>
					<input type="submit" name="submit" value="<?php echo $taxonomy->labels->update_item; ?>" class="button-primary">
					<input type="hidden" name="webcomic_term" value="<?php echo $term->term_id; ?>">
					<input type="hidden" name="webcomic_collection" value="<?php echo $wc->term_id; ?>">
					<input type="hidden" name="action" value="update_webcomic_term">
					<input type="hidden" name="subpage" value="">
				</p> 
			</form>
		</div>
		<?php } else { $wco = ( 'webcomic_storyline' == $page ) ? true : false; $search = ( !empty( $_REQUEST[ 's' ] ) ) ? '&search=' . $_REQUEST[ 's' ] : ''; $tg = ( 'webcomic_collection' != $page ) ? '&term_group=' . $wc->term_id : ''; $terms = get_terms( $page, 'hide_empty=0&webcomic_order=' . $wco . $tg . $search ); $num_term = count( get_terms( $page, 'hide_empty=0&parent=0&term_group=' . $wc->term_id ) ); $max_term = ( $num_term < ( $pagenum * $tpp ) ) ? $num_term : $pagenum * $tpp; ?>
		<style>.widefat .term-description{display:none}.widefat .term-description-toggle{cursor:pointer}.widefat th.column-characters,.widefat th.column-storylines{text-align:center}</style>
		<div class="wrap">
			<div id="icon-webcomic" class="icon32"><img src="<?php echo $this->url . 'webcomic-includes/icon.png'; ?>" alt="icon"></div>
			<h2>
			<?php
				if ( 'webcomic_collection' == $page )
					echo $taxonomy->labels->name;
				else
					printf( __( '%s in %s', 'webcomic' ), $taxonomy->labels->name, $wc->name );
			?>
			</h2>
			<form action="<?php echo $view; ?>" method="post">
			<p class="search-box">
				<?php if ( 'webcomic_collection' != $page ) { ?>
				<select name="webcomic_collection" id="webcomic_collection">
				<?php
					$walker = new webcomic_Walker_AdminTermDropdown();
					$selected = array( $wc->term_id );
					echo $walker->walk( get_terms( 'webcomic_collection', 'get=all' ), 0, array( 'selected' => $selected ) );
				?>
				</select>
				<?php } ?>
				<label class="screen-reader-text" for="s"><?php echo $taxonomy->labels->search_items; ?></label>
				<input type="text" id="s" name="s" value="<?php if ( !empty( $_REQUEST[ 's' ] ) ) echo $_REQUEST[ 's' ]; ?>">
				<input type="submit" value="<?php echo $taxonomy->labels->search_items; ?>" class="button">
			</p>
			</form>
			<div id="col-container" style="clear:both">
				<div id="col-right">
					<div class="col-wrap">
					<?php if ( !$terms && 'webcomic_collection' != $page ) { ?>
						<p><?php printf( __( "%s doesn't have any %s.", 'webcomic' ), $wc->name, strtolower( $taxonomy->labels->name ) ); ?></p>
					<?php } elseif ( !$terms ) { ?>
						<p><?php _e( "You'll need to create at least one collection before you can start publishing webcomics.", 'webcomic' ); ?></p>
					<?php } else { ?>
						<form action="<?php echo $view; ?>" method="post">
							<?php wp_nonce_field( 'bulk_webcomic_term' ); ?>
							<div class="tablenav">
								<div class="tablenav-pages"><?php echo paginate_links( array( 'base' => 'admin.php' . $view . '%_%', 'format' => '&amp;pagenum=%#%', 'prev_text' => __( '&laquo;', 'webcomic' ), 'next_text' => __( '&raquo;', 'webcomic' ), 'total' => ceil ( $num_term / $tpp ), 'current' => $pagenum ) ); ?></div>
								<div class="alignleft actions">
									<select name="action-1">
										<option value=""><?php _e( 'Bulk Actions', 'webcomic' ); ?></option>
										<option value="regen"><?php _e( 'Regenerate Thumbnails', 'webcomic' ); ?></option>
										<?php if ( 'webcomic_storyline' != $page ) { ?><option value="delete"><?php _e( 'Delete', 'webcomic' ); ?></option><?php } ?>
										<?php if ( 'webcomic_collection' != $page ) { ?>
										<optgroup label="<?php _e( 'Defaults', 'webcomic' ); ?>">
											<option value="add_default"><?php _e( 'Add', 'webcomic' ); ?></option>
											<option value="remove_default"><?php _e( 'Remove', 'webcomic' ); ?></option>
										</optgroup>
										<?php } ?>
									</select>
									<input type="submit" value="<?php _e( 'Apply', 'webcomic' ); ?>" name="submit-1" class="button-secondary action">
								</div>
							</div>
							<table class="widefat">
								<thead><tr><th scope="col" class="manage-column column-cb check-column"><input type="checkbox"></th><?php print_column_headers( 'webcomic_page_' . $page ); ?></tr></thead>
								<tfoot><tr><th scope="col" class="manage-column column-cb check-column"><input type="checkbox"></th><?php print_column_headers( 'webcomic_page_' . $page ); ?></tr></tfoot>
								<tbody>
									<?php
										$walker = new webcomic_Walker_AdminTermList();
										echo $walker->paged_walk( $terms, 0, $pagenum, $tpp, array( 'hidden' => $hidden, 'view' => $view, 'page' => $page ) );
									?>
								</tbody>
							</table>
							<div class="tablenav">
								<div class="tablenav-pages"><span class="displaying-num"><?php if ( $num_term > $tpp ) printf( __( 'Displaying %1$d &#8211; %2$d of %3$d', 'webcomic' ) , ( ( $pagenum - 1 ) * $tpp + 1 ), $max_term, $num_term ); ?></span><?php echo paginate_links( array( 'base' => 'admin.php' . $view . '%_%', 'format' => '&amp;pagenum=%#%', 'prev_text' => __( '&laquo;', 'webcomic' ), 'next_text' => __( '&raquo;', 'webcomic' ), 'total' => ceil ( $num_term / $tpp ), 'current' => $pagenum ) ); ?></div>
								<div class="alignleft actions">
									<select name="action-2">
										<option value=""><?php _e( 'Bulk Actions', 'webcomic' ); ?></option>
										<option value="regen"><?php _e( 'Regenerate Thumbnails', 'webcomic' ); ?></option>
										<?php if ( 'webcomic_storyline' != $page ) { ?><option value="delete"><?php _e( 'Delete', 'webcomic' ); ?></option><?php } ?>
										<?php if ( 'webcomic_collection' != $page ) { ?>
										<optgroup label="<?php _e( 'Defaults', 'webcomic' ); ?>">
											<option value="add_default"><?php _e( 'Add', 'webcomic' ); ?></option>
											<option value="remove_default"><?php _e( 'Remove', 'webcomic' ); ?></option>
										</optgroup>
										<?php } ?>
									</select>
									<input type="submit" value="<?php _e( 'Apply', 'webcomic' ); ?>" name="submit-2" class="button-secondary action">
									<input type="hidden" name="webcomic_collection" value="<?php echo $wc->term_id; ?>">
									<input type="hidden" name="action" value="bulk_webcomic_term">
								</div>
							</div>
						</form>
					<?php } ?>
					</div>
				</div>
				<div id="col-left">
					<div class="col-wrap">
						<div class="form-wrap">
							<h3><?php printf( __( 'Add a New %s', 'webcomic' ), ucfirst( end( explode( '_', $page ) ) ) ); ?></h3>
							<form action="<?php echo $view; ?>" method="post" enctype="multipart/form-data">
								<?php wp_nonce_field( 'add_webcomic_term' ); ?>
								<div class="form-field">
									<label for="webcomic_file"><?php echo ucfirst( $img_field ); ?></label>
									<input type="file" name="webcomic_file[]" id="webcomic_file">
									<p><?php printf( __( 'The %s is a representative image that can be displayed on your site.', 'webcomic' ), $img_field ); ?></p>
								</div>
								<div class="form-field">
									<label for="webcomic_name"><?php _e( 'Name', 'webcomic' ); ?></label>
									<input type="text" name="webcomic_name" id="webcomic_name" size="40" value="">
									<p><?php printf( __( 'The name is how the %s appears on your site.', 'webcomic' ), strtolower( $taxonomy->labels->singular_name ) ); ?></p>
								</div>
								<div class="form-field">
									<label for="webcomic_nicename"><?php _e( 'Slug', 'webcomic' ); ?></label>
									<input type="text" name="webcomic_nicename" id="webcomic_nicename" size="40" value="">
									<p><?php _e( 'The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'webcomic' ); ?></p>
								</div>
								<?php if ( 'webcomic_storyline' == $page || 'webcomic_character' == $page ) { ?>
								<div class="form-field">
									<label for="webcomic_parent"><?php _e( 'Parent', 'wecomic' ); ?></label>
									<select name="webcomic_parent" id="webcomic_parent">
										<option value="0"><?php _e( 'None', 'webcomic' ); ?></option>
										<?php
											$walker = new webcomic_Walker_AdminTermParent();
											echo $walker->walk( $terms, 0, array( 'parent' => '' ) );
										?>
									</select>
									<p><?php printf( __( '%s are hierarchical, so one %s may contain any number of other %s.', 'webcomic' ), $taxonomy->labels->name, strtolower( $taxonomy->labels->singular_name ), strtolower( $taxonomy->labels->name ) ); ?></p>
								</div>
								<?php } else { ?>
								<input type="hidden" name="webcomic_parent" value="0">
								<?php } ?>
								<div class="form-field">
									<label for="webcomic_description"><?php _e( 'Description', 'webcomic' ); ?></label>
									<textarea name="webcomic_description" id="webcomic_description" rows="5" cols="40" class="webcomic_tinymce"></textarea>
									<p><?php printf( __( 'The description is useful for providing a brief explanation of the %s.', 'webcomic' ), strtolower( $taxonomy->labels->singular_name ) ); ?></p>
								</div> 
								<p class="submit">
									<input type="submit" class="button" name="submit" value="<?php echo $taxonomy->labels->add_new_item; ?>">
									<input type="hidden" name="webcomic_collection" value="<?php echo $wc->term_id; ?>">
									<input type="hidden" name="action" value="add_webcomic_term">
								</p>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript">var x='<form action="<?php echo $view; ?>" method="post"><input type="hidden" name="page" value="<?php echo $page; ?>"><div class="screen-options"><label><input type="text" name="<?php echo $page; ?>_per_page" value="<?php echo $tpp; ?>" maxlength="3" class="screen-per-page"> <?php echo $taxonomy->labels->name; ?></label> <input type="submit" value="<?php _e( 'Apply', 'webcomic' ); ?>" class="button-secondary action"><input type="hidden" name="webcomic_collection" value="<?php echo $wc->term_id; ?>"></div></form>';jQuery(document).ready(function($){$('.term-description-toggle').toggle(function(){$(this).parent().siblings('.term-description').show();$(this).html('<?php _e( 'Hide Description', 'webcomic' ) ?>');},function(){$(this).parent().siblings('.term-description').hide();$(this).html('<?php _e( 'Show Description', 'webcomic' ) ?>');});$('#screen-options-wrap').append(x);$('select#webcomic_collection').change(function(){ window.location = '<?php echo admin_url( 'admin.php?page=' . $page . '&webcomic_collection=' ); ?>' + $(this).val();});});</script>
		<?php }
	}
	
	/**
	 * Displays the tools pages.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function admin_tools() {
		$this->domain();
		
		$page    = $_REQUEST[ 'page' ];
		$subpage = ( !empty( $_REQUEST[ 'subpage' ] ) ) ? $_REQUEST[ 'subpage' ] : '';
		$subview = ( $subpage ) ? '&amp;subpage=' . $subpage : '';
		$view    = '?page=' . $page . $subview;
		
		if ( $subpage && 'edit_files' == $_REQUEST[ 'subpage' ] ) {
			$match   = ( 'post' == $_REQUEST[ 'type' ] ) ? true : false;
			$files   = $this->retrieve( $_REQUEST[ 'id' ], $_REQUEST[ 'type' ], $_REQUEST[ 'src' ], $match );
			$referer = ( !empty( $_REQUEST[ 'referer' ] ) ) ? $_REQUEST[ 'referer' ] : $_SERVER[ 'HTTP_REFERER' ];
			
			if ( $files ) {
				$file = array();
				$file[ 'full' ]   = ( !empty( $files[ 'full' ][ $_REQUEST[ 'key' ] ] ) ) ? $files[ 'full' ][ $_REQUEST[ 'key' ] ] : array();
				$file[ 'large' ]  = ( !empty( $files[ 'large' ][ $_REQUEST[ 'key' ] ] ) ) ? $files[ 'large' ][ $_REQUEST[ 'key' ] ] : array();
				$file[ 'medium' ] = ( !empty( $files[ 'medium' ][ $_REQUEST[ 'key' ] ] ) ) ? $files[ 'medium' ][ $_REQUEST[ 'key' ] ] : array();
				$file[ 'small' ]  = ( !empty( $files[ 'small' ][ $_REQUEST[ 'key' ] ] ) ) ? $files[ 'small' ][ $_REQUEST[ 'key' ] ] : array();
		?>
		<div class="wrap">
			<div id="icon-webcomic" class="icon32"><img src="<?php echo $this->url . 'webcomic-includes/icon.png'; ?>" alt="icon"></div>
			<h2><?php printf( __( 'Editing %s', 'webcomic' ), $file[ 'full' ][ 'filename' ] ); ?></h2>
			<form action="<?php echo $view; ?>&type=<?php echo $_REQUEST[ 'type' ]; ?>&src=<?php echo $_REQUEST[ 'src' ]; ?>&id=<?php echo $_REQUEST[ 'id' ]; ?>&key=<?php echo $_REQUEST[ 'key' ]; ?>" method="post" enctype="multipart/form-data">
				<?php wp_nonce_field( 'edit_webcomic_files' ); ?>
				<table>
					<tbody>
						<?php
							foreach ( $file as $k => $v ) {
								if ( 'full' == $k )
									$s = __( 'full', 'webcomic' );
								if ( 'large' == $k )
									$s = __( 'large', 'webcomic' );
								if ( 'medium' == $k )
									$s = __( 'medium', 'webcomic' );
								if ( 'small' == $k )
									$s = __( 'small', 'webcomic' );
						?>
						<tr>
							<td style="vertical-align:top">
							<?php if ( 'full' == $k ) { echo $v[ 'html' ]; ?>
								<input type="hidden" name="webcomic_filename" value="<?php echo stripslashes( $v[ 'filename' ] ); ?>">
							<?php } else { ?>
								<h3><?php printf( __( '%s Size', 'webcomic' ), ucfirst( $s ) ); ?></h3>
								<label for="webcomic_file[<?php echo $k; ?>]"><?php echo ( $v ) ? $v[ 'html' ] : _e( 'Not Available', 'webcomic' ); ?></label>
								<p>
									<input type="file" name="webcomic_file[<?php echo $k; ?>]" id="webcomic_file[<?php echo $k; ?>]">
									<?php if ( $v ) { ?><label><input type="checkbox" name="webcomic_delete[<?php echo $k; ?>]" value="1" style="vertical-align:text-top"> <?php _e( 'Delete', 'webcomic' ); ?></label><?php } ?>
								</p>
							<?php } ?>
							</td>
						</tr>
						<tr><td colspan="2"><hr style="border:3px double #ddd"></td></tr>
						<?php } ?>
					</tbody>
				</table>
				<p>
					<a href="<?php echo $referer; ?>" class="button-secondary"><?php _e( '&laquo; Back', 'webcomic' ); ?></a>
					<input type="hidden" name="referer" value="<?php echo $referer; ?>">
					<input type="submit" name="Submit" class="button-primary" value="<?php _e( 'Save Changes', 'webcomic' ); ?>">
				</p>
				<input type="hidden" name="action" value="edit_webcomic_files">
			</form>
		</div>
		<?php } } elseif ( $subpage && 'upgrade_webcomic' == $_REQUEST[ 'subpage' ] ) { ?>
		<div class="wrap">
			<div id="icon-webcomic" class="icon32"><img src="<?php echo $this->url . 'webcomic-includes/icon.png'; ?>" alt="icon"></div>
			<h2><?php _e( 'Upgrade Webcomic', 'webcomic' ); ?></h2>
			<div id="col-wrap">
				<div id="col-left">
					<div class="col-wrap">
						<?php if ( get_option( 'webcomic_version' ) ) { ?>
						<p><?php printf( __( 'Thanks for choosing Webcomic! Upgrading to Webcomic %d is a little more complicated than previous versions, but this tool will take care of the hard work for you.', 'webcomic' ), $this->version ); ?></p>
						<p><?php _e( "This is what the Upgrade Webcomic tool will try to do for you:", "webcomic" ); ?></p>
						<ol>
							<li<?php if ( $_REQUEST[ 'step' ] > 0 ) echo ' style="color:#008000;text-decoration:line-through";'; ?>><?php _e( 'Transfer your existing settings', 'webcomic' ); ?></li>
							<li<?php if ( $_REQUEST[ 'step' ] > 1 ) echo ' style="color:#008000;text-decoration:line-through";'; ?>><?php _e( 'Convert any existing "comic posts" to Webcomics', 'webcomic' ); ?></li>
							<li<?php if ( $_REQUEST[ 'step' ] > 2 ) echo ' style="color:#008000;text-decoration:line-through";'; ?>><?php _e( 'Convert any existing series to collections', 'webcomic' ); ?></li>
							<li<?php if ( $_REQUEST[ 'step' ] > 3 ) echo ' style="color:#008000;text-decoration:line-through";'; ?>><?php _e( 'Convert any existing volumes to storylines', 'webcomic' ); ?></li>
							<li<?php if ( $_REQUEST[ 'step' ] > 4 ) echo ' style="color:#008000;text-decoration:line-through";'; ?>><?php _e( 'Convert any existing chapters to storylines', 'webcomic' ); ?></li>
							<li><?php _e( 'Remove any old settings, metadata, series, volumes, and chapters left over after the upgrade', 'webcomic' ); ?></li>
						</ol>
						<p><?php _e( "And this is what you'll need to do after the upgrade is complete:", "webcomic" ); ?></p>
						<ol>
							<li><?php printf( __( 'Move your existing comic files to the correct collection folder in the new Webcomic directory at <a href="%s" target="_blank">%s</a>', 'webcomic' ), $this->directory( 'url' ), $this->directory( 'url' ) ); ?></li>
							<li><?php printf( __( "Update your themes with Webcomic %d's new widgets and template tags, or try out one of the new <a href='%s' target='_blank'>official Webcomic-ready themes</a>", "webcomic" ), $this->version, 'http://webcomicms.net/support/manual/themes/' ); ?></li>
						</ol>
						<p><strong><?php _e( 'Do not stop the upgrade process once it has begun.', 'webcomic' ); ?></strong></p>
						<?php if ( empty( $_REQUEST[ 'step' ] ) ) { ?><p><strong><?php printf( __( 'Please <a href="%s">backup all of your data</a> before proceeding.', 'webcomic' ), admin_url( 'export.php' ) ); ?></strong></p><?php } ?>
						<p>
						<?php if ( empty( $_REQUEST[ 'step' ] ) ) { $_REQUEST[ 'step' ] = 0; ?>
							<a href="?page=<?php echo $page; ?>" class="button-secondary"><?php _e( 'Cancel', 'webcomic' ); ?></a>
						<?php } elseif ( 1 < $_REQUEST[ 'step' ] ) { ?>
							<a href="<?php echo wp_nonce_url( '?page=' . $page . '&amp;subpage=upgrade_webcomic&amp;action=upgrade_legacy_webcomic&amp;step=' . $_REQUEST[ 'step' ], 'upgrade_legacy_webcomic' ); ?>" class="button-secondary"><?php printf( __( 'Retry Step %d', 'webcomic' ), $_REQUEST[ 'step' ] ); ?></a>
						<?php } $_REQUEST[ 'step' ]++; ?>
							<a href="<?php echo wp_nonce_url( '?page=' . $page . '&amp;subpage=upgrade_webcomic&amp;action=upgrade_legacy_webcomic&amp;step=' . $_REQUEST[ 'step' ], 'upgrade_legacy_webcomic' ); ?>" class="button-primary"><?php if ( 1 == $_REQUEST[ 'step' ]  ) printf( __( 'Upgrade to Webcomic %s &raquo;', 'webcomic' ), $this->version ); else printf( __( 'Continue with Step %d &raquo;', 'webcomic' ), $_REQUEST[ 'step' ] ); ?></a>
						</p>
						<?php } else { ?>
						<p><?php _e( "There's nothing left for the Upgrade Webcomic tool to do. Thanks again for choosing Webcomic, and don't forget to:", "webcomic" ); ?></p>
						<ol>
							<li><?php printf( __( 'Move your existing comic files to the correct collection folder in the new Webcomic directory at <a href="%s" target="_blank">%s</a>', 'webcomic' ), $this->directory( 'url' ), $this->directory( 'url' ) ); ?></li>
							<li><?php printf( __( "Update your themes with Webcomic %d's new widgets and template tags, or try out one of the new <a href='%s' target='_blank'>official Webcomic-ready themes</a>", "webcomic" ), $this->version, 'http://webcomicms.net/support/manual/themes/' ); ?></li>
						</ol>
						<p><a href="?page=<?php echo $page; ?>" class="button-secondary"><?php _e( '&laquo; Back to Tools', 'webcomic' ); ?></a></p>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<?php } elseif ( $subpage && 'uninstall_webcomic' == $_REQUEST[ 'subpage' ] || $this->option( 'uninstall' ) ) { ?>
		<div class="wrap">
			<div id="icon-webcomic" class="icon32"><img src="<?php echo $this->url . 'webcomic-includes/icon.png'; ?>" alt="icon"></div>
			<h2><?php _e( 'Uninstall Webcomic', 'webcomic' ); ?></h2>
			<div id="col-wrap">
				<div id="col-left">
					<div class="col-wrap">
						<?php if ( !$this->option( 'uninstall' ) ) { ?>
						<p><?php _e( 'This tool will completely remove any information, files, and settings related to Webcomic, including:', 'webcomic' ); ?></p>
						<ol>
							<li><?php _e( 'Settings', 'webcomic' ); ?></li>
							<li><?php _e( 'Post metadata', 'webcomic' ); ?></li>
							<li><?php _e( 'User metadata', 'webcomic' ); ?></li>
							<li><?php _e( 'Thumbnail files', 'webcomic' ); ?></li>
							<li><?php _e( 'Collections, storylines, and characters', 'webcomic' ); ?></li>
						</ol>
						<p><?php printf( __( 'This information cannot be restored. Existing Webcomics will be converted to regular posts and added to the default category. You will still need to deactivate and delete the plugin after uninstalling, and remove any files in <a href="%s">%s</a>.', 'webcomic' ), $this->directory( 'url' ), $this->directory( 'url' ) ); ?></p>
						<p><strong><?php _e( 'Are you sure you want to uninstall Webcomic?', 'webcomic' ); ?></strong></p>
						<p>
							<a href="?page=<?php echo $page; ?>" class="button-secondary"><?php _e( 'Cancel', 'webcomic' ); ?></a>
							<a href="<?php echo wp_nonce_url( '?page=' . $page . '&amp;subpage=uninstall_webcomic&amp;action=uninstall_webcomic', 'uninstall_webcomic' ); ?>" class="button-primary"><?php _e( 'Uninstall Webcomic', 'webcomic' ); ?></a>
						</p>
						<?php } else { ?>
						<p><?php _e( 'Thanks for giving Webcomic a chance!', 'webcomic' ); ?></p>
						<p><?php printf( __( 'Please <a href="%s">deactivate the plugin</a> and delete the Webcomic directory at <a href="%s">%s</a> to complete the uninstallation.', 'webcomic' ), admin_url( 'plugins.php' ), $this->directory( 'url' ), $this->directory( 'url' ) ); ?></p>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<?php } else { ?>
		<div class="wrap">
			<div id="icon-webcomic" class="icon32"><img src="<?php echo $this->url . 'webcomic-includes/icon.png'; ?>" alt="icon"></div>
			<h2><?php _e( 'Tools', 'webcomic' ); ?></h2>
			<table class="widefat" cellspacing="0">
				<?php if ( get_option( 'webcomic_version' ) ) { ?>
				<tr class="alternate">
					<th scope="row" class="import-system row-title"><a href="<?php echo $view . '&amp;subpage=upgrade_webcomic'; ?> "><?php _e( 'Upgrade Webcomic', 'webcomic' ); ?></a></th>
					<td class="desc" style="vertical-align:middle"><?php _e( 'Upgrade from legacy versions of Webcomic.', 'webcomic' ); ?></td>
				</tr>
				<?php } if ( !$this->option( 'uninstall' ) ) { ?>
				<tr<?php if ( !get_option( 'webcomic_version' ) ) echo ' class="alternate"'; ?>>
					<th scope="row" class="import-system row-title delete"><a href="<?php echo $view . '&amp;subpage=uninstall_webcomic'; ?> "><?php _e( 'Uninstall Webcomic', 'webcomic' ); ?></a></th>
					<td class="desc" style="vertical-align:middle"><?php _e( 'Remove all files and information related to Webcomic.', 'webcomic' ); ?></td>
				</tr>
				<?php } ?>
			</table>
		</div>
		<?php
		}
	}
	
	/**
	 * Displays the settings page.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function admin_settings() {
		$this->domain();
		
		$languages = array( 'aa' => __( 'Afar', 'webcomic' ), 'ab' => __( 'Abkhazian', 'webcomic' ), 'ae' => __( 'Avestan', 'webcomic' ), 'af' => __( 'Afrikaans', 'webcomic' ), 'ak' => __( 'Akan', 'webcomic' ), 'am' => __( 'Amharic', 'webcomic' ), 'an' => __( 'Aragonese', 'webcomic' ), 'ar' => __( 'Arabic', 'webcomic' ), 'as' => __( 'Assamese', 'webcomic' ), 'av' => __( 'Avaric', 'webcomic' ), 'ay' => __( 'Aymara', 'webcomic' ), 'az' => __( 'Azerbaijani', 'webcomic' ), 'ba' => __( 'Bashkir', 'webcomic' ), 'be' => __( 'Belarusian', 'webcomic' ), 'bg' => __( 'Bulgarian', 'webcomic' ), 'bh' => __( 'Bihari', 'webcomic' ), 'bi' => __( 'Bislama', 'webcomic' ), 'bm' => __( 'Bambara', 'webcomic' ), 'bn' => __( 'Bengali', 'webcomic' ), 'bo' => __( 'Tibetan', 'webcomic' ), 'br' => __( 'Breton', 'webcomic' ), 'bs' => __( 'Bosnian', 'webcomic' ), 'ca' => __( 'Catalan', 'webcomic' ), 'ce' => __( 'Chechen', 'webcomic' ), 'ch' => __( 'Chamorro', 'webcomic' ), 'co' => __( 'Corsican', 'webcomic' ), 'cr' => __( 'Cree', 'webcomic' ), 'cs' => __( 'Czech', 'webcomic' ), 'cu' => __( 'Church Slavic', 'webcomic' ), 'cv' => __( 'Chuvash', 'webcomic' ), 'cy' => __( 'Welsh', 'webcomic' ), 'da' => __( 'Danish', 'webcomic' ), 'de' => __( 'German', 'webcomic' ), 'dv' => __( 'Divehi', 'webcomic' ), 'dz' => __( 'Dzongkha', 'webcomic' ), 'ee' => __( 'Ewe', 'webcomic' ), 'el' => __( 'Greek', 'webcomic' ), 'en' => __( 'English', 'webcomic' ), 'eo' => __( 'Esperanto', 'webcomic' ), 'es' => __( 'Spanish', 'webcomic' ), 'et' => __( 'Estonian', 'webcomic' ), 'eu' => __( 'Basque', 'webcomic' ), 'fa' => __( 'Persian', 'webcomic' ), 'ff' => __( 'Fulah', 'webcomic' ), 'fi' => __( 'Finnish', 'webcomic' ), 'fj' => __( 'Fijian', 'webcomic' ), 'fo' => __( 'Faroese', 'webcomic' ), 'fr' => __( 'French', 'webcomic' ), 'fy' => __( 'Western Frisian', 'webcomic' ), 'ga' => __( 'Irish', 'webcomic' ), 'gd' => __( 'Scottish Gaelic', 'webcomic' ), 'gl' => __( 'Galician', 'webcomic' ), 'gn' => __( 'Guarani', 'webcomic' ), 'gu' => __( 'Gujarati', 'webcomic' ), 'gv' => __( 'Manx', 'webcomic' ), 'ha' => __( 'Hausa', 'webcomic' ), 'he' => __( 'Hebrew', 'webcomic' ), 'hi' => __( 'Hindi', 'webcomic' ), 'ho' => __( 'Hiri Motu', 'webcomic' ), 'hr' => __( 'Croatian', 'webcomic' ), 'ht' => __( 'Haitian', 'webcomic' ), 'hu' => __( 'Hungarian', 'webcomic' ), 'hy' => __( 'Armenian', 'webcomic' ), 'hz' => __( 'Herero', 'webcomic' ), 'ia' => __( 'Interlingua', 'webcomic' ), 'id' => __( 'Indonesian', 'webcomic' ), 'ie' => __( 'Interlingue', 'webcomic' ), 'ig' => __( 'Igbo', 'webcomic' ), 'ii' => __( 'Sichuan Yi', 'webcomic' ), 'ik' => __( 'Inupiaq', 'webcomic' ), 'io' => __( 'Ido', 'webcomic' ), 'is' => __( 'Icelandic', 'webcomic' ), 'it' => __( 'Italian', 'webcomic' ), 'iu' => __( 'Inuktitut', 'webcomic' ), 'ja' => __( 'Japanese', 'webcomic' ), 'jv' => __( 'Javanese', 'webcomic' ), 'ka' => __( 'Georgian', 'webcomic' ), 'kg' => __( 'Kongo', 'webcomic' ), 'ki' => __( 'Kikuyu', 'webcomic' ), 'kj' => __( 'Kwanyama', 'webcomic' ), 'kk' => __( 'Kazakh', 'webcomic' ), 'kl' => __( 'Kalaallisut', 'webcomic' ), 'km' => __( 'Khmer', 'webcomic' ), 'kn' => __( 'Kannada', 'webcomic' ), 'ko' => __( 'Korean', 'webcomic' ), 'kr' => __( 'Kanuri', 'webcomic' ), 'ks' => __( 'Kashmiri', 'webcomic' ), 'ku' => __( 'Kurdish', 'webcomic' ), 'kv' => __( 'Komi', 'webcomic' ), 'kw' => __( 'Cornish', 'webcomic' ), 'ky' => __( 'Kirghiz', 'webcomic' ), 'la' => __( 'Latin', 'webcomic' ), 'lb' => __( 'Luxembourgish', 'webcomic' ), 'lg' => __( 'Ganda', 'webcomic' ), 'li' => __( 'Limburgish', 'webcomic' ), 'ln' => __( 'Lingala', 'webcomic' ), 'lo' => __( 'Lao', 'webcomic' ), 'lt' => __( 'Lithuanian', 'webcomic' ), 'lu' => __( 'Luba-Katanga', 'webcomic' ), 'lv' => __( 'Latvian', 'webcomic' ), 'mg' => __( 'Malagasy', 'webcomic' ), 'mh' => __( 'Marshallese', 'webcomic' ), 'mi' => __( 'Maori', 'webcomic' ), 'mk' => __( 'Macedonian', 'webcomic' ), 'ml' => __( 'Malayalam', 'webcomic' ), 'mn' => __( 'Mongolian', 'webcomic' ), 'mr' => __( 'Marathi', 'webcomic' ), 'ms' => __( 'Malay', 'webcomic' ), 'mt' => __( 'Maltese', 'webcomic' ), 'my' => __( 'Burmese', 'webcomic' ), 'na' => __( 'Nauru', 'webcomic' ), 'nb' => __( 'Norwegian Bokmal', 'webcomic' ), 'nd' => __( 'North Ndebele', 'webcomic' ), 'ne' => __( 'Nepali', 'webcomic' ), 'ng' => __( 'Ndonga', 'webcomic' ), 'nl' => __( 'Dutch', 'webcomic' ), 'nn' => __( 'Norwegian Nynorsk', 'webcomic' ), 'no' => __( 'Norwegian', 'webcomic' ), 'nr' => __( 'South Ndebele', 'webcomic' ), 'nv' => __( 'Navajo', 'webcomic' ), 'ny' => __( 'Chichewa', 'webcomic' ), 'oc' => __( 'Occitan', 'webcomic' ), 'oj' => __( 'Ojibwa', 'webcomic' ), 'om' => __( 'Oromo', 'webcomic' ), 'or' => __( 'Oriya', 'webcomic' ), 'os' => __( 'Ossetian', 'webcomic' ), 'pa' => __( 'Panjabi', 'webcomic' ), 'pi' => __( 'Pali', 'webcomic' ), 'pl' => __( 'Polish', 'webcomic' ), 'ps' => __( 'Pashto', 'webcomic' ), 'pt' => __( 'Portuguese', 'webcomic' ), 'qu' => __( 'Quechua', 'webcomic' ), 'rm' => __( 'Raeto-Romance', 'webcomic' ), 'rn' => __( 'Kirundi', 'webcomic' ), 'ro' => __( 'Romanian', 'webcomic' ), 'ru' => __( 'Russian', 'webcomic' ), 'rw' => __( 'Kinyarwanda', 'webcomic' ), 'sa' => __( 'Sanskrit', 'webcomic' ), 'sc' => __( 'Sardinian', 'webcomic' ), 'sd' => __( 'Sindhi', 'webcomic' ), 'se' => __( 'Northern Sami', 'webcomic' ), 'sg' => __( 'Sango', 'webcomic' ), 'si' => __( 'Sinhala', 'webcomic' ), 'sk' => __( 'Slovak', 'webcomic' ), 'sl' => __( 'Slovenian', 'webcomic' ), 'sm' => __( 'Samoan', 'webcomic' ), 'sn' => __( 'Shona', 'webcomic' ), 'so' => __( 'Somali', 'webcomic' ), 'sq' => __( 'Albanian', 'webcomic' ), 'sr' => __( 'Serbian', 'webcomic' ), 'ss' => __( 'Swati', 'webcomic' ), 'st' => __( 'Southern Sotho', 'webcomic' ), 'su' => __( 'Sundanese', 'webcomic' ), 'sv' => __( 'Swedish', 'webcomic' ), 'sw' => __( 'Swahili', 'webcomic' ), 'ta' => __( 'Tamil', 'webcomic' ), 'te' => __( 'Telugu', 'webcomic' ), 'tg' => __( 'Tajik', 'webcomic' ), 'th' => __( 'Thai', 'webcomic' ), 'ti' => __( 'Tigrinya', 'webcomic' ), 'tk' => __( 'Turkmen', 'webcomic' ), 'tl' => __( 'Tagalog', 'webcomic' ), 'tn' => __( 'Tswana', 'webcomic' ), 'to' => __( 'Tonga', 'webcomic' ), 'tr' => __( 'Turkish', 'webcomic' ), 'ts' => __( 'Tsonga', 'webcomic' ), 'tt' => __( 'Tatar', 'webcomic' ), 'tw' => __( 'Twi', 'webcomic' ), 'ty' => __( 'Tahitian', 'webcomic' ), 'ug' => __( 'Uighur', 'webcomic' ), 'uk' => __( 'Ukrainian', 'webcomic' ), 'ur' => __( 'Urdu', 'webcomic' ), 'uz' => __( 'Uzbek', 'webcomic' ), 've' => __( 'Venda', 'webcomic' ), 'vi' => __( 'Vietnamese', 'webcomic' ), 'vo' => __( 'Volapuk', 'webcomic' ), 'wa' => __( 'Walloon', 'webcomic' ), 'wo' => __( 'Wolof', 'webcomic' ), 'xh' => __( 'Xhosa', 'webcomic' ), 'yi' => __( 'Yiddish', 'webcomic' ), 'yo' => __( 'Yoruba', 'webcomic' ), 'za' => __( 'Zhuang', 'webcomic' ), 'zh' => __( 'Chinese', 'webcomic' ), 'zu' => __( 'Zulu', 'webcomic' ) );
		
		natcasesort( $languages );
		?>
		<div class="wrap">
			<div id="icon-webcomic" class="icon32"><img src="<?php echo $this->url . 'webcomic-includes/icon.png'; ?>" alt="icon"></div>
			<h2><?php _e( 'Settings', 'webcomic' ); ?></h2>
			<form method="post" action="">
				<?php wp_nonce_field( 'webcomic_settings' ); ?>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="integrate_toggle"><?php _e( 'Integration', 'webcomic' ); ?></label></th>
						<td><label><input type="checkbox" name="integrate_toggle" value="1" id="integrate_toggle"<?php if ( $this->option( 'integrate_toggle' ) ) echo ' checked'; ?>> <?php printf( __( 'Integrate webcomics into the site automatically (not for use with <a href="%s" target="_blank">official Webcomic themes</a>)', 'webcomic' ), 'http://webcomicms.net/support/manual/themes/' ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><label for="shortcut_toggle"><?php _e( 'Shortcuts', 'webcomic' ); ?></label></th>
						<td><label><input type="checkbox" name="shortcut_toggle" value="1" id="shortcut_toggle"<?php if ( $this->option( 'shortcut_toggle' ) ) echo ' checked'; ?>> <?php _e( 'Enable keyboard shortcuts for webcomic navigation', 'webcomic' ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><label for="secure_toggle"><?php _e( 'Security', 'webcomic' ); ?></label></th>
						<td><label><input type="checkbox" name="secure_toggle" value="1" id="secure_toggle"<?php if ( $this->option( 'secure_toggle' ) ) echo ' checked'; ?>> <?php _e( 'Secure filenames and obscure the location of files', 'webcomic' ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><label for="age_toggle"><?php _e( 'Verification', 'webcomic' ); ?></label></th>
						<td><input type="checkbox" name="age_toggle" value="1" id="age_toggle"<?php if ( $this->option( 'age_toggle' ) ) echo ' checked'; ?>> <label> <?php printf( __( 'Require age verification for any collection rated %s years or older', 'webcomic' ), '<input type="text" name="age_size" size="2" maxlength="2" value="' . $this->option( 'age_size' ) . '" style="text-align:center;width:auto">' ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><label for="buffer_toggle"><?php _e( 'Buffers', 'webcomic' ); ?></label></th>
						<td><input type="checkbox" name="buffer_toggle" value="1" id="buffer_toggle"<?php if ( $this->option( 'buffer_toggle' ) ) echo ' checked'; ?>> <label> <?php printf( __( 'Start sending e-mail notifications %s days before a collection buffer runs out', 'webcomic' ), '<input type="text" name="buffer_size" size="3" maxlength="3" value="' . $this->option( 'buffer_size' ) . '" style="text-align:center;width:auto">' ); ?></label></td>
					</tr>
					<tr>
						<th scope="row"><label for="feed_toggle"><?php _e( 'Feeds', 'webcomic' ); ?></label></th>
						<td><input type="checkbox" name="feed_toggle" value="1" id="feed_toggle"<?php if ( $this->option( 'feed_toggle' ) ) echo ' checked'; ?>> <label>
							<?php
								$a = $b = $c = $d = '';
								
								switch ( $this->option( 'feed_size' ) ) {
									case 'small' : $a = ' selected'; break;
									case 'medium': $b = ' selected'; break;
									case 'large' : $c = ' selected'; break;
									default      : $d = ' selected';
								}
								
								$s = '
								<select name="feed_size">
									<option value="full"' . $d . '>' . __( 'full', 'webcomic' ) . '</option>
									<option value="large"' . $c . '>' . __( 'large', 'webcomic' ) . '</option>
									<option value="medium"' . $b . '>' . __( 'medium', 'webcomic' ) . '</option>
									<option value="small"' . $a . '>' . __( 'small', 'webcomic' ) . '</option>
								</select>'; unset( $a, $b, $c, $d );
								
								printf( __( 'Show %s previews in site feeds', 'webcomic' ), $s );
							?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="transcribe_toggle"><?php _e( 'Transcripts', 'webcomic' ); ?></label><br><br>
							<p class="description"><?php _e( 'Hold <code>Ctrl</code> or <code>Command</code> to select multiple languages.', 'webcomic' ); ?></p>
						</th>
						<td><input type="checkbox" name="transcribe_toggle" value="1" id="transcribe_toggle"<?php if ( $this->option( 'transcribe_toggle' ) ) echo ' checked'; ?>> <label>
							<?php
								$a = $b = $c = '';
								
								switch ( $this->option( 'transcribe_restrict' ) ) {
									case 'login' : $a = ' selected'; break;
									case 'selfid': $b = ' selected'; break;
									default      : $c = ' selected';
								}
								
								$s = '
								<select name="transcribe_restrict">
									<option value="anyone"' . $c . '>' . __( 'anonymous users', 'webcomic' ) . '</option>
									<option value="selfid"' . $b . '>' . __( 'self-identified users', 'webcomic' ) . '</option>
									<option value="login"' . $a . '>' . __( 'registered users', 'webcomic' ) . '</option>
								</select>'; unset( $a, $b, $c );
								
								printf( __( 'Allow %s to transcribe new webcomics in the following languages:', 'webcomic' ), $s );
							?>
							</label>
							<p>
								<label style="float:left;margin-right:2em">
									<b><?php _e( 'Languages', 'webcomic' ); ?></b><br>
									<select name="transcribe_language[]" size="2" style="height:10em" multiple>
									<?php $transcribe_languages = $this->option( 'transcribe_language' ); foreach ( $languages as $k => $v ) { ?>
										<option value="<?php echo $k; ?>/<?php echo $v; ?>"<?php if ( isset( $transcribe_languages[ $k ] ) ) echo ' selected'; ?>><?php echo $v; ?></option>
									<?php } ?>
									</select>
								</label>
								<label>
									<b><?php _e( 'Default', 'webcomic' ); ?></b><br>
									<select name="transcribe_default">
									<?php $transcribe_default = $this->option( 'transcribe_default' ); foreach ( $languages as $k => $v ) { ?>
										<option value="<?php echo $k; ?>/<?php echo $v; ?>"<?php if ( isset( $transcribe_default[ $k ] ) ) echo ' selected'; ?>><?php echo $v; ?></option>
									<?php } ?>
									</select>
								</label>
							</p>
						</td>
					</tr>
				</table>
				<h3><?php _e( 'Thumbnails', 'webcomic' ); ?></h3>
				<p><?php _e( 'These sizes determine the maximum dimensions (width &#215; height) in pixels to use when generating thumbnails.', 'webcomic' ); ?></p>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="small_w"><?php _e( 'Small Size', 'webcomic' ); ?></label></th>
						<td><input type="text" name="small_w" value="<?php echo $this->option( 'small_w' ); ?>" size="5" style="text-align:center;width:auto" id="small_w"> &#215; <input type="text" name="small_h" value="<?php echo $this->option( 'small_h' ); ?>" size="5" style="text-align:center;width:auto"></td>
					</tr>
					<tr>
						<th scope="row"><label for="medium_w"><?php _e( 'Medium Size', 'webcomic' ); ?></label></th>
						<td><input type="text" name="medium_w" value="<?php echo $this->option( 'medium_w' ); ?>" size="5" style="text-align:center;width:auto" id="medium_w"> &#215; <input type="text" name="medium_h" value="<?php echo $this->option( 'medium_h' ); ?>" size="5" style="text-align:center;width:auto"></td>
					</tr>
					<tr>
						<th scope="row"><label for="large_w"><?php _e( 'Large Size', 'webcomic' ); ?></label></th>
						<td><input type="text" name="large_w" value="<?php echo $this->option( 'large_w' ); ?>" size="5" style="text-align:center;width:auto"id="large_w"> &#215; <input type="text" name="large_h" value="<?php echo $this->option( 'large_h' ); ?>" size="5" style="text-align:center;width:auto"></td>
					</tr>
				</table>
				<h3><?php _e( 'Paypal', 'webcomic' ); ?></h3>
				<p><?php _e( 'These settings can be used to provide a simple way for users to send donations or purchase prints.', 'webcomic' ); ?></p>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="paypal_business"><?php _e( 'Business', 'webcomic' ); ?></label></th>
						<td>
						<input type="text" name="paypal_business" value="<?php echo $this->option( 'paypal_business' ); ?>" id="paypal_business" class="regular-text">
						<span class="settings-description"><?php printf( __( '<a href="%s" target="_blank">Get a PayPal Account</a>', 'webcomic' ), 'http://paypal.com/' ); ?></span></td>
					</tr>
					<tr>
						<th scope="row"><label for="paypal_currency"><?php _e( 'Currency', 'webcomic' ); ?></label></th>
						<td>
							<select name="paypal_currency" id="paypal_currency">
								<option value="AUD"<?php if ( 'AUD' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'Australian Dollar', 'webcomic' ); ?></option>
								<option value="BRL"<?php if ( 'BRL' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'Brazilian Real', 'webcomic' ); ?></option>
								<option value="CAD"<?php if ( 'CAD' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'Canadian Dollar', 'webcomic' ); ?></option>
								<option value="CZK"<?php if ( 'CZK' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'Czech Koruna', 'webcomic' ); ?></option>
								<option value="DKK"<?php if ( 'DKK' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'Danish Krone', 'webcomic' ); ?></option>
								<option value="EUR"<?php if ( 'EUR' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'Euro', 'webcomic' ); ?></option>
								<option value="HKD"<?php if ( 'HKD' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'Hong Kong Dollar', 'webcomic' ); ?></option>
								<option value="HUF"<?php if ( 'HUF' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'Hungarian Forint', 'webcomic' ); ?></option>
								<option value="ILS"<?php if ( 'ILS' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'Israeli New Sheqel', 'webcomic' ); ?></option>
								<option value="JPY"<?php if ( 'JPY' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'Japanese Yen', 'webcomic' ); ?></option>
								<option value="MYR"<?php if ( 'MYR' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'Malaysian Ringgit', 'webcomic' ); ?></option>
								<option value="MXN"<?php if ( 'MXN' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'Mexican Peso', 'webcomic' ); ?></option>
								<option value="NOK"<?php if ( 'NOK' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'Norwegian Krone', 'webcomic' ); ?></option>
								<option value="NZD"<?php if ( 'NZD' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'New Zealand Dollar', 'webcomic' ); ?></option>
								<option value="PHP"<?php if ( 'PHP' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'Philippine Peso', 'webcomic' ); ?></option>
								<option value="PLN"<?php if ( 'PLN' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'Polish Zloty', 'webcomic' ); ?></option>
								<option value="GBP"<?php if ( 'GBP' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'Pound Sterling', 'webcomic' ); ?></option>
								<option value="SGD"<?php if ( 'SGD' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'Singapore Dollar', 'webcomic' ); ?></option>
								<option value="SEK"<?php if ( 'SEK' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'Swedish Krona', 'webcomic' ); ?></option>
								<option value="CHF"<?php if ( 'CHF' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'Swiss Franc', 'webcomic' ); ?></option>
								<option value="TWD"<?php if ( 'TWD' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'Taiwan New Dollar', 'webcomic' ); ?></option>
								<option value="THB"<?php if ( 'THB' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'Thai Baht', 'webcomic' ); ?></option>
								<option value="USD"<?php if ( 'USD' == $this->option( 'paypal_currency' ) ) echo 'selected'; ?>><?php _e( 'U.S. Dollar', 'webcomic' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<th><label for="paypal_log"><?php _e( 'Logs', 'webcomic' ); ?></label></th>
						<td><label><input type="checkbox" name="paypal_log" id="paypal_log" value="1"<?php if ( $this->option( 'paypal_log' ) ) echo ' checked'; ?>> <?php _e( 'Log instant payment notifications', 'webcomic' ); ?></label></td>
					</tr>
				</table>
				<h4><?php _e( 'Prints', 'webcomic' ); ?></h4>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="paypal_prints"><?php _e( 'Method', 'webcomic' ); ?></label></th>
						<td>
							<input type="checkbox" name="paypal_prints" id="paypal_prints" value="1"<?php if ( $this->option( 'paypal_prints' ) ) echo ' checked'; ?>>
							<label>
							<?php
								$a = $b = '';
								
								switch ( $this->option( 'paypal_method' ) ) {
									case '_cart': $a = ' selected'; break;
									default     : $b = ' selected'; break;
								}
								
								$s = '
								<select name="paypal_method" id="paypal_method">
									<option value="_xclick"' . $b . '>' . __( 'single item', 'webcomic' ) . '</option>
									<option value="_cart"' . $a . '>' . __( 'shopping cart', 'webcomic' ) . '</option>
								</select>
								';
								
								printf( __( 'Sell prints of new webcomics using the %s method', 'webcomic' ), $s );
							?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="paypal_price_d"><?php _e( 'Price', 'webcomic' ); ?></label></th>
						<td>
							<label><input type="text" name="paypal_price_d" value="<?php echo $this->option( 'paypal_price_d' ); ?>"  size="3" id="paypal_price_d" style="text-align:center;width:auto"> <?php _e( 'Domestic', 'webcomic' ); ?></label> &emsp;
							<label><input type="text" name="paypal_price_i" value="<?php echo $this->option( 'paypal_price_i' ); ?>"  size="3" style="text-align:center;width:auto"> <?php _e( 'International', 'webcomic' ); ?></label> &emsp;
							<label><input type="text" name="paypal_price_o" value="<?php echo $this->option( 'paypal_price_o' ); ?>"  size="3" style="text-align:center;width:auto"> <?php _e( 'Original', 'webcomic' ); ?></label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="paypal_shipping_d"><?php _e( 'Shipping', 'webcomic' ); ?></label></th>
						<td>
							<label><input type="text" name="paypal_shipping_d" value="<?php echo $this->option( 'paypal_shipping_d' ); ?>" size="3" id="paypal_shipping_d" style="text-align:center;width:auto"> <?php _e( 'Domestic', 'webcomic' ); ?></label> &emsp;
							<label><input type="text" name="paypal_shipping_i" value="<?php echo $this->option( 'paypal_shipping_i' ); ?>" size="3"  style="text-align:center;width:auto"> <?php _e( 'International', 'webcomic' ); ?></label> &emsp;
							<label><input type="text" name="paypal_shipping_o" value="<?php echo $this->option( 'paypal_shipping_o' ); ?>" size="3"  style="text-align:center;width:auto"> <?php _e( 'Original', 'webcomic' ); ?></label>
						</td>
					</tr>
				</table>
				<h4><?php _e( 'Donations', 'webcomic' ); ?></h4>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="paypal_donation"><?php _e( 'Amount', 'webcomic' ); ?></label></th>
						<td>
							<input type="text" name="paypal_donation" value="<?php echo $this->option( 'paypal_donation' ); ?>"  size="3" id="paypal_donation" style="text-align:center;width:auto">
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" name="Submit" class="button-primary" value="<?php _e( 'Save Changes', 'webcomic' ); ?>"> <span class="alignright description"><?php printf( __( '<a href="%s" title="Show your support by donating" target="_blank">Donate</a> | Webcomic %s', 'webcomic' ), 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=R6SH66UF6F9DG', $this->option( 'version' ) ); ?></span>
					<input type="hidden" name="action" value="webcomic_settings">
				</p>
			</form>
		</div>
		<?php
	}
	
	/**
	 * Displays the post metabox.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function admin_metabox( $post ) {
		$this->domain();
		
		global $current_user;
		
		$wc = $ids = $post_meta = false;
		
		if ( is_object_in_term( $post->ID, 'webcomic_collection' ) ) {
			$ids       = true;
			$wc        = current( wp_get_object_terms( $post->ID, 'webcomic_collection' ) );
			$post_meta = current( get_post_meta( $post->ID, 'webcomic' ) );
		} elseif ( !empty( $_REQUEST[ 'webcomic_collection' ] ) )
			$wc = get_term( $_REQUEST[ 'webcomic_collection' ], 'webcomic_collection' );
		?>
		<style>#webcomic_terms input,input[type=checkbox]{width:auto}#wpbody-content .media-item-attributes input[type=text],#wpbody-content .media-item-attributes textarea{width:100%}#webcomic th img{max-height:<?php echo $this->option( 'small_h' ); ?>px;max-width:<?php echo $this->option( 'small_w' ); ?>px}#availablethemes img{height:auto;max-height:<?php echo $this->option( 'small_h' ); ?>px;max-width:<?php echo $this->option( 'small_w' ); ?>px;width:auto}#availablethemes input[type=checkbox]{display:none}#availablethemes label{border:1px solid transparent;display:inline-block;padding:.25em}#availablethemes input:checked + label{background:#ffffe0;border:1px solid #e6db55}</style>
		<div class="media-single">
			<table class="slidetoggle describe form-table" style="width:100%">
				<tbody>
					<tr id="webcomic_terms">
						<td style="vertical-align:top;width:33%">
							<select name="webcomic_collection" id="webcomic_collection">
							<?php if ( isset( $_REQUEST[ 'post_type' ] ) && 'webcomic_post' != $_REQUEST[ 'post_type' ] ) { ?><option value=""><?php _e( '&mdash; Collections &mdash;', 'webcomic' ); ?></option><?php } ?>
							<?php
								$walker = new webcomic_Walker_AdminTermDropdown();
								$selected = ( $wc ) ? array( $wc->term_id ) : array();
								echo $walker->walk( get_terms( 'webcomic_collection', 'get=all' ), 0, array( 'selected' => $selected, 'no_def' => $ids ) );
							?>
							</select><br>
							<?php
								if ( !$wc )
									echo '<p>' . __( 'Select a collection to make this a webcomic post.', 'webcomic' ) . '</p>';
								else
									echo '<p>' . __( 'Hold <code>Ctrl</code> or <code>Command</code> to select multiple storylines and characters.', 'webcomic' ) . '</p>';
							?>
						</td>
						<td style="width:33%">
							<?php if ( $wc ) { ?>
							<label for="webcomic_storyline"><b><?php _e( 'Storylines', 'webcomic' ); ?></b></label><br>
							<select name="webcomic_storyline[]" id="webcomic_storyline" size="2" style="height:10em;width:100%" multiple>
							<?php
								$walker   = new webcomic_Walker_AdminTermDropdown();
								$selected = wp_get_object_terms( $post->ID, 'webcomic_storyline', array( 'fields' => 'ids' ) );
								echo $walker->walk( get_terms( 'webcomic_storyline', 'hide_empty=0&orderby=term_group_name&webcomic_order=1&term_group=' . $wc->term_id ), 0, array( 'selected' => $selected, 'no_def' => $ids ) );
							?>
							</select>
							<?php } ?>
						</td>
						<td style="width:33%">
							<?php if ( $wc ) { ?>
							<label for="webcomic_character"><b><?php _e( 'Characters', 'webcomic' ); ?></b></label><br>
							<select name="webcomic_character[]" id="webcomic_character" size="2" style="height:10em;width:100%" multiple>
							<?php
								$walker   = new webcomic_Walker_AdminTermDropdown();
								$selected = wp_get_object_terms( $post->ID, 'webcomic_character', array( 'fields' => 'ids' ) );
								echo $walker->walk( get_terms( 'webcomic_character', 'hide_empty=0&orderby=term_group_name&term_group=' . $wc->term_id ), 0, array( 'selected' => $selected, 'no_def' => $ids ) );
							?>
							</select>
							<?php } ?>
						</td>
					</tr>
				</tbody>
			</table>
			<style>#icon-edit{background:url('<?php echo $this->url . 'webcomic-includes/icon.png'; ?>') 50% 50% no-repeat;}</style>
			<div id="webcomic_orphans">
			<?php
				if ( $wc ) {
					$abs       = $this->directory( 'abs', $wc->slug );
					$url       = $this->directory( 'url', $wc->slug );
					$tabs      = $abs . 'thumbs/';
					$turl      = $url . 'thumbs/';
					$files     = glob( $abs . '*.*' );
					$posts     = get_objects_in_term( $wc->term_id, 'webcomic_collection' );
					$term_meta = $this->option( 'term_meta' );
					
					foreach ( $term_meta as $taxonomy )
						foreach ( $taxonomy as $term )
							if ( !empty( $term[ 'files' ] ) )
								foreach ( $term[ 'files' ][ 'full' ] as $k => $v )
									if ( false !== ( $key = array_search( $abs . $term[ 'files' ][ 'full' ][ $k ], $files ) ) )
										unset( $files[ $key ] );
					
					foreach ( $posts as $k => $v ) {
						if ( 'webcomic_post' != get_post_type( $v ) )
							continue;
						
						$status = get_post_status( $v );
						
						$post_files = $this->fetch( $v, 'post', $wc->slug, true );
						
						if ( !empty( $post_files ) )
							foreach ( $post_files[ 'full' ] as $f ) {
								$fk = array_search( $abs . $f, $files );
								unset( $files[ $fk ] );
							}
					}
					
					if ( $files ) {
			?>
			<p><?php _e( 'Select one or more orphaned files to bind them to this post, or upload new files below.', 'webcomic' ); ?></p>
			<table id="availablethemes" cellspacing="0" cellpadding="0">
			<?php
				$rows  = ceil ( count( $files ) / 3 );
				$table = array();
				
				for ( $row = 1; $row <= $rows; $row++ )
					for ( $col = 1; $col <= 3; $col++ )
						$table[ $row ][ $col ] = array_shift( $files );
				
				foreach ( $table as $row => $cols ) {
				?>
				<tr>
				<?php
				foreach ( $cols as $col => $file ) {
					$class = array( 'available-theme' );
					
					if ( $row == 1 )     $class[] = 'top';
					if ( $row == $rows ) $class[] = 'bottom';
					if ( $col == 1 )     $class[] = 'left';
					if ( $col == 3 )     $class[] = 'right';
					if ( !$term ) { echo '<td class="' . join( ' ', $class ) . '"></td>'; continue; }
					?>
					<td class="<?php echo join( ' ', $class ); ?>">
					<?php if ( $file ) { ?>
						<input type="checkbox" name="webcomic_orphan[<?php echo hash( 'md5', $file ); ?>]" id="webcomic_orphan[<?php echo hash( 'md5', $file ); ?>]" value="<?php echo $file; ?>">
						<label for="webcomic_orphan[<?php echo hash( 'md5', $file ); ?>]">
						<?php
							$orphan = $this->retrieve( $file, 'orphan', $wc->slug );
							
							if ( $orphan[ 'small' ] )
								echo $orphan[ 'small' ][ 0 ][ 'html' ];
							elseif ( $orphan[ 'medium' ] )
								echo $orphan[ 'medium' ][ 0 ][ 'html' ];
							elseif ( $orphan[ 'large' ] )
								echo $orphan[ 'large' ][ 0 ][ 'html' ];
							else
								echo $orphan[ 'full' ][ 0 ][ 'html' ];
						?>
						</label><br>
						<a href="<?php echo $orphan[ 'full' ][ 0 ][ 'url' ]; ?>" target="_blank"><?php _e( 'View', 'webcomic' ); ?></a>
						<?php } ?>
					</td>
					<?php } ?>
				</tr>
				<?php } ?>
			</table>
			<?php } } $files = $this->retrieve( $post->ID, 'post', $wc->slug, true ); ?>
			</div>
			
			<table class="slidetoggle describe form-table">
				<thead class="media-item-info">
					<tr>
						<th class="label"><label for="webcomic_file"><b><?php _e( 'Files', 'webcomic' ); ?></b></label></th>
						<td>
							<p>
								<span id="webcomic_files"><input type="file" name="webcomic_file[]" id="webcomic_file"><label><?php if ( current_user_can( 'manage_categories' ) ) { ?><input type="checkbox" name="webcomic_ovewrite" value="1"> <?php _e( 'Overwrite', 'webcomic' ); ?></label><?php } ?><a id="add_webcomic_file" style="cursor:pointer;line-height:0"><?php _e( '+ Add More', 'webcomic' ); ?></a></span><br>
								<?php $z = ( function_exists( 'zip_open' ) ) ? __( 'a zipped archive of images or', 'webcomic' ) : ''; printf( __( 'You may upload %s one or more individual images by clicking <em>+ Add More</em>.', 'webcomic' ), $z ); ?>
							</p>
						</td>
					</tr>
				<?php if ( !empty( $files ) ) { ?>
					<tr><td colspan="2"><hr style="border:3px double #ddd"></td></tr>
					<?php foreach ( $files[ 'full' ] as $k => $v ) { ?>
					<tr>
						<th class="label">
							<label for="webcomic_filename[<?php echo $k; ?>]">
							<?php
								if ( $files[ 'small' ][ $k ] )
									echo $files[ 'small' ][ $k ][ 'html' ];
								elseif ( $files[ 'medium' ][ $k ] )
									echo $files[ 'medium' ][ $k ][ 'html' ];
								elseif ( $files[ 'large' ][ $k ] )
									echo $files[ 'large' ][ $k ][ 'html' ];
								else
									echo $v[ 'html' ];
							?>
							</label>
						</th>
						<td style="vertical-align:top">
							<input type="text" name="webcomic_filename[<?php echo $k; ?>]" value="<?php echo $v[ 'filename' ]; ?>" id="webcomic_filename[<?php echo $k; ?>]" style="width:50%">
							<input type="hidden" name="webcomic_extension[<?php echo $k; ?>]" value=".<?php echo $v[ 'extension' ]; ?>">
							<select name="webcomic_action[<?php echo $k; ?>]">
								<option value=""><?php _e( 'File Actions', 'webcomic' ); ?></option>
								<?php if ( empty( $post_meta[ 'files' ] ) ) { ?>
								<option value="bind"><?php _e( 'Bind', 'webcomic' ); ?></option>
								<?php } else { ?>
								<option value="unbind"><?php _e( 'Unbind', 'webcomic' ); ?></option>
								<?php } ?>
								<?php if ( 'image/jpeg' == $v[ 'mime' ] || 'image/gif' == $v[ 'mime' ] || 'image/png' == $v[ 'mime' ]  ) { ?><option value="regen"><?php _e( 'Regenerate Thumbnails', 'webcomic' ); ?></option><?php } ?>
								<option value="delete"><?php _e( 'Delete', 'webcomic' ); ?></option>
							</select><br>
							<?php
								$s = array();
								
								if ( isset( $files[ 'large' ][ $k ] ) )
									$s[] = __( 'large', 'webcomic' );
								if ( isset( $files[ 'medium' ][ $k ] ) )
									$s[] = __( 'medium', 'webcomic' );
								if ( isset( $files[ 'small' ][ $k ] ) )
									$s[] = __( 'small', 'webcomic' );
								
								echo '<p>';
										
								if ( 1 < count( $s ) )
									printf( __( '%s sizes available', 'webcomic' ), ucfirst( substr_replace( implode( ', ', $s ), __( ' and', 'webcomic' ), strrpos( implode( ', ', $s ), ',' ), 1 ) ) );
								elseif ( !empty( $s ) )
									printf( __( '%s size available', 'webcomic' ), ucfirst( current( $s ) ) );
								else
									_e( 'No thumbnail sizes available', 'webcomic' );
								
								echo ' | <a href="' . admin_url( 'admin.php?page=webcomic_tools&subpage=edit_files&type=post&src=' . $wc->slug . '&id=' . $post->ID . '&key=' . $k ) . '">' . __( 'Edit', 'webcomic' ) . '</a></p>';
							?>
							<label for="webcomic_alternate[<?php echo $k; ?>]"><b><?php _e( 'Alternate', 'webcomic' ); ?></b><?php _e( ' - The alternate text is displayed if this file cannot be loaded.', 'webcomic' ); ?></label><br>
							<input type="text" name="webcomic_alternate[<?php echo $k; ?>]" value="<?php echo ( isset( $post_meta[ 'alternate' ][ $k ] ) ) ? $post_meta[ 'alternate' ][ $k ] : ''; ?>" id="webcomic_alternate[<?php echo $k; ?>]"><br>
							<label for="webcomic_description[<?php echo $k; ?>]"><b><?php _e( 'Description', 'webcomic' ); ?></b><?php _e( ' - The description is displayed when a user hovers over this file.', 'webcomic' ); ?></label><br>
							<input type="text" name="webcomic_description[<?php echo $k; ?>]" value="<?php echo ( isset( $post_meta[ 'description' ][ $k ] ) ) ? $post_meta[ 'description' ][ $k ] : ''; ?>" id="webcomic_description[<?php echo $k; ?>]">
						</td>
					</tr>
					<tr><td colspan="2"><hr style="border:3px double #ddd"></td></tr>
				<?php } } ?>
				</thead>
				<tbody class="media-item-attributes">
					<tr class="form-field">
						<th scope="row" class="label"><label for="webcomic_transcript_language"><b><?php _e( 'Transcripts', 'webcomic' ); ?></b></label></th>
						<td>
							<select name="webcomic_transcript_language" id="webcomic_transcript_language" style="float:left;vertical-align:middle;">
								<?php
									$default   = $this->option( 'transcribe_default' );
									$languages = $this->option( 'transcribe_language' );
									
									foreach ( $languages as $k => $v ) {
								?>
								<option value="<?php echo $k; ?>"<?php if ( isset( $default[ $k ] ) ) echo 'selected'; ?>><?php echo $v; ?></option>
								<?php } ?>
							</select>
							<?php
							foreach ( $languages as $k => $v ) {
							?>
							<span id="webcomic_transcript_action[webcomic_lang_<?php echo $k; ?>]" style="float:right">
								<select name="webcomic_transcript_action[<?php echo $k; ?>]">
									<option value="publish"<?php if ( isset( $post_meta[ 'transcripts' ][ $k ][ 'status' ] ) && 'publish' == $post_meta[ 'transcripts' ][ $k ][ 'status' ] ) echo 'selected="selected"'; ?>><?php _e( 'Publish', 'webcomic' ); ?></option>
									<option value="pending"<?php if ( isset( $post_meta[ 'transcripts' ][ $k ][ 'status' ] ) && 'pending' == $post_meta[ 'transcripts' ][ $k ][ 'status' ] ) echo 'selected="selected"'; ?>><?php _e( 'Request Improvement', 'webcomic' ); ?></option>
									<option value="draft"<?php if ( isset( $post_meta[ 'transcripts' ][ $k ][ 'status' ] ) && 'draft' == $post_meta[ 'transcripts' ][ $k ][ 'status' ] ) echo 'selected="selected"'; ?>><?php _e( 'Save as Draft', 'webcomic' ); ?></option>
									<?php if ( isset( $post_meta[ 'transcripts' ][ $k ][ 'backup' ] ) ) { ?>
									<option value="restore"><?php _e( 'Restore Previous', 'webcomic' ); ?></option>
									<?php } if ( isset( $post_meta[ 'transcripts' ][ $k ][ 'text' ] ) ) { ?>
									<option value="delete"><?php _e( 'Delete', 'webcomic' ); ?></option>
									<?php } ?>
								</select>
							</span>
							<div id="webcomic_transcript[webcomic_lang_<?php echo $k; ?>]" style="celar:both"><textarea rows="7" cols="25" name="webcomic_transcript[<?php echo $k; ?>]" id="webcomic_transcript[<?php echo $k; ?>]" class="webcomic_tinymce"><?php echo ( isset( $post_meta[ 'transcripts' ][ $k ][ 'text' ] ) ) ? $post_meta[ 'transcripts' ][ $k ][ 'text' ] : ''; ?></textarea></div>
							<input type="hidden" name="webcomic_transcript_author[<?php echo $k; ?>]" value="<?php if ( !empty( $post_meta[ 'transcripts' ][ $k ][ 'author' ] ) ) echo $post_meta[ 'transcripts' ][ $k ][ 'author' ]; else echo $current_user->display_name; ?>">
							<input type="hidden" name="webcomic_transcript_time[<?php echo $k; ?>]" value="<?php if ( !empty( $post_meta[ 'transcripts' ][ $k ][ 'time' ] ) ) echo $post_meta[ 'transcripts' ][ $k ][ 'time' ]; else echo time(); ?>">
							<textarea name="webcomic_transcript_backup[<?php echo $k; ?>]" style="display:none"><?php echo ( !empty( $post_meta[ 'transcripts' ][ $k ][ 'backup' ] ) ) ? $post_meta[ 'transcripts' ][ $k ][ 'backup' ] : ''; ?></textarea>
							<input type="hidden" name="webcomic_transcript_backup_author[<?php echo $k; ?>]" value="<?php echo ( !empty( $post_meta[ 'transcripts' ][ $k ][ 'backup_author' ] ) ) ? $post_meta[ 'transcripts' ][ $k ][ 'backup_author' ] : ''; ?>">
							<input type="hidden" name="webcomic_transcript_backup_time[<?php echo $k; ?>]" value="<?php echo ( !empty( $post_meta[ 'transcripts' ][ $k ][ 'backup_time' ] ) ) ? $post_meta[ 'transcripts' ][ $k ][ 'backup_time' ] : ''; ?>">
							<?php
								if ( !empty( $post_meta[ 'transcripts' ][ $k ][ 'author' ] ) ) {
									$d = date( get_option( 'date_format' ), $post_meta[ 'transcripts' ][ $k ][ 'time' ] );
									$t = date( get_option( 'time_format' ), $post_meta[ 'transcripts' ][ $k ][ 'time' ] );
							?>
							<span id="webcomic_transcript_author[webcomic_lang_<?php echo $k; ?>]"><?php printf( __( 'Submitted by %s on %s @ %s', 'webcomic' ), $post_meta[ 'transcripts' ][ $k ][ 'author' ], $d, $t ); ?></span>
							<?php } } ?>
							<br><label><input type="checkbox" name="webcomic_transcribe_toggle" value="1"<?php if ( ( isset( $post_meta[ 'transcribe_toggle' ] ) && $post_meta[ 'transcribe_toggle' ] ) || ( !isset( $post_meta[ 'transcribe_toggle' ] ) && $this->option( 'transcribe_toggle' ) ) ) echo ' checked'; ?>> <?php _e( 'Allow Transcribing', 'webcomic' ); ?></label>
						</td>
					</tr>
					<tr class="form-field">
						<th scope="row" class="label"><label for="webcomic_paypal_price_amount_d"><b><?php _e( 'Prints', 'webcomic' ); ?></b></label></th>
						<td style="padding:0">
							<!-- Nested tables, I know. Forgive me. >.< -->
							<table>
								<thead>
									<tr>
										<td><label><input type="checkbox" name="webcomic_paypal_prints" value="1"<?php if ( ( isset( $post_meta[ 'paypal' ][ 'prints' ] ) && $post_meta[ 'paypal' ][ 'prints' ] ) || ( !isset( $post_meta[ 'paypal' ][ 'prints' ] ) && $wc->webcomic_paypal[ 'prints' ] ) || ( !isset( $wc->webcomic_paypal[ 'prints' ] ) && $this->option( 'paypal_prints' ) ) ) echo ' checked'; ?>> <?php _e( 'Sell Prints', 'webcomic' ); ?></label></td>
										<td><i><?php _e( 'Price', 'webcomic' ); ?></i></td>
										<td><i><?php _e( 'Shipping', 'webcomic' ); ?></i></td>
										<td></td>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><i><?php _e( 'Domestic', 'webcomic' ); $price_d = ( isset( $post_meta[ 'paypal' ][ 'price_d' ] ) ) ? $post_meta[ 'paypal' ][ 'price_d' ] : 0; $shipping_d = ( isset( $post_meta[ 'paypal' ][ 'shipping_d' ] ) ) ? $post_meta[ 'paypal' ][ 'shipping_d' ] : 0; ?></i></td>
										<td>
											<?php printf ( '%.2f', $this->price( $this->option( 'paypal_price_d' ), array( $wc->webcomic_paypal[ 'price_d' ] ) ) ); ?>
											<select name="webcomic_paypal_price_type_d">
												<option value="add"<?php if ( $price_d > 0 ) echo ' selected'; ?>>+</option>
												<option value="sub"<?php if ( $price_d < 0 ) echo ' selected'; ?>>&minus;</option>
											</select>
											<input type="text" name="webcomic_paypal_price_d" size="1" value="<?php echo abs( $price_d ); ?>" style="text-align:center;width:auto" id="webcomic_paypal_price_d">% =
											<span class="webcomic_paypal_price_d"><?php printf( __( '%.2f %s', 'webcomic' ), $this->price( $this->option( 'paypal_price_d' ), array( $wc->webcomic_paypal[ 'price_d' ], $price_d ) ), $this->option( 'paypal_currency' ) ); ?></span>
										</td>
										<td>
											<?php printf ( '%.2f', $this->price( $this->option( 'paypal_shipping_d' ), array( $wc->webcomic_paypal[ 'shipping_d' ] ) ) ); ?>
											<select name="webcomic_paypal_shipping_type_d">
												<option value="add"<?php if ( $shipping_d < 0 ) echo ' selected'; ?>>+</option>
												<option value="sub"<?php if ( $shipping_d < 0 ) echo ' selected'; ?>>&minus;</option>
											</select>
											<input type="text" name="webcomic_paypal_shipping_d" size="1" value="<?php echo abs( $shipping_d ); ?>" style="text-align:center;width:auto" id="webcomic_paypal_shipping_d">% =
											<span class="webcomic_paypal_shipping_d"><?php printf( __( '%.2f %s', 'webcomic' ), $this->price( $this->option( 'paypal_shipping_d' ), array( $wc->webcomic_paypal[ 'shipping_d' ], $shipping_d ) ), $this->option( 'paypal_currency' ) ); ?></span>
										</td>
									</tr>
									<tr>
										<td><i><?php _e( 'International', 'webcomic' ); $price_i = ( isset( $post_meta[ 'paypal' ][ 'price_i' ] ) ) ? $post_meta[ 'paypal' ][ 'price_i' ] : 0; $shipping_i = ( isset( $post_meta[ 'paypal' ][ 'shipping_i' ] ) ) ? $post_meta[ 'paypal' ][ 'shipping_i' ] : 0; ?></i></td>
										<td>
											<?php printf ( '%.2f', $this->price( $this->option( 'paypal_price_i' ), array( $wc->webcomic_paypal[ 'price_i' ] ) ) ); ?>
											<select name="webcomic_paypal_price_type_i">
												<option value="add"<?php if ( $price_i < 0 ) echo ' selected'; ?>>+</option>
												<option value="sub"<?php if ( $price_i < 0 ) echo ' selected'; ?>>&minus;</option>
											</select>
											<input type="text" name="webcomic_paypal_price_i" size="1" value="<?php echo abs( $price_i ); ?>" style="text-align:center;width:auto" id="webcomic_paypal_price_i">% =
											<span class="webcomic_paypal_price_i"><?php printf( __( '%.2f %s', 'webcomic' ), $this->price( $this->option( 'paypal_price_i' ), array( $wc->webcomic_paypal[ 'price_i' ], $price_i ) ), $this->option( 'paypal_currency' ) ); ?></span>
										</td>
										<td>
											<?php printf ( '%.2f', $this->price( $this->option( 'paypal_shipping_i' ), array( $wc->webcomic_paypal[ 'shipping_i' ] ) ) ); ?>
											<select name="webcomic_paypal_shipping_type_i">
												<option value="add"<?php if ( $shipping_i < 0 ) echo ' selected'; ?>>+</option>
												<option value="sub"<?php if ( $shipping_i < 0 ) echo ' selected'; ?>>&minus;</option>
											</select>
											<input type="text" name="webcomic_paypal_shipping_i" size="1" value="<?php echo abs( $shipping_i ); ?>" style="text-align:center;width:auto" id="webcomic_paypal_shipping_i">% =
											<span class="webcomic_paypal_shipping_i"><?php printf( __( '%.2f %s', 'webcomic' ), $this->price( $this->option( 'paypal_shipping_i' ), array( $wc->webcomic_paypal[ 'shipping_i' ], $shipping_i ) ), $this->option( 'paypal_currency' ) ); ?></span>
										</td>
									</tr>
									<tr>
										<td colspan="4"><hr style="border:3px double #ddd"></td>
									</tr>
									<tr>
										<td><i><?php _e( 'Original', 'webcomic' ); $price_o = ( isset( $post_meta[ 'paypal' ][ 'price_o' ] ) ) ? $post_meta[ 'paypal' ][ 'price_o' ] : 0; $shipping_o = ( isset( $post_meta[ 'paypal' ][ 'shipping_o' ] ) ) ? $post_meta[ 'paypal' ][ 'shipping_o' ] : 0; ?></i></td>
										<td>
											<?php printf ( '%.2f', $this->price( $this->option( 'paypal_price_o' ), array( $wc->webcomic_paypal[ 'price_o' ] ) ) ); ?>
											<select name="webcomic_paypal_price_type_o">
												<option value="add"<?php if ( $price_o < 0 ) echo ' selected'; ?>>+</option>
												<option value="sub"<?php if ( $price_o < 0 ) echo ' selected'; ?>>&minus;</option>
											</select>
											<input type="text" name="webcomic_paypal_price_o" size="1" value="<?php echo abs( $price_o ); ?>" style="text-align:center;width:auto" id="webcomic_paypal_price_o">% =
											<span class="webcomic_paypal_price_o"><?php printf( __( '%.2f %s', 'webcomic' ), $this->price( $this->option( 'paypal_price_o' ), array( $wc->webcomic_paypal[ 'price_o' ], $price_o ) ), $this->option( 'paypal_currency' ) ); ?></span>
										</td>
										<td>
											<?php printf ( '%.2f', $this->price( $this->option( 'paypal_shipping_o' ), array( $wc->webcomic_paypal[ 'shipping_o' ] ) ) ); ?>
											<select name="webcomic_paypal_shipping_type_o">
												<option value="add"<?php if ( $shipping_o < 0 ) echo ' selected'; ?>>+</option>
												<option value="sub"<?php if ( $shipping_o < 0 ) echo ' selected'; ?>>&minus;</option>
											</select>
											<input type="text" name="webcomic_paypal_shipping_o" size="1" value="<?php echo abs( $shipping_o ); ?>" style="text-align:center;width:auto" id="webcomic_paypal_shipping_o">% =
											<span class="webcomic_paypal_shipping_o"><?php printf( __( '%.2f %s', 'webcomic' ), $this->price( $this->option( 'paypal_shipping_o' ), array( $wc->webcomic_paypal[ 'shipping_o' ], $shipping_o ) ), $this->option( 'paypal_currency' ) ); ?></span>
										</td>
										<td><label><input type="checkbox" name="webcomic_paypal_original" value="1"<?php if ( false == $post_meta[ 'paypal' ][ 'original' ] )  echo ' checked'; ?>> <?php _e( 'Sold', 'webcomic' ); ?></label></td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<input type="hidden" name="current_webcomic_collection" value="<?php echo $wc->term_id; ?>">
		<input type="hidden" name="webcomic_ajax" value="0">
		<script type="text/javascript">jQuery(document).ready(function($){$('form#post').attr('enctype','multipart/form-data').attr('encoding','multipart/form-data');$('select#webcomic_collection').change(function(){$('input[name=webcomic_ajax]').val('collection');var params=$('form').serialize();$('#webcomic_terms').load('<?php echo $_SERVER[ 'PHP_SELF' ]; ?>',params);$('input[name=webcomic_ajax]').val('orphans');var params=$('form').serialize();$('#webcomic_orphans').load('<?php echo $_SERVER[ 'PHP_SELF' ]; ?>',params);$('input[name=webcomic_ajax]').val(0);}).change();$('#add_webcomic_file').click(function(){$('#webcomic_files').append('<br><input type="file" name="webcomic_file[]">')});$('#webcomic_transcript_language').change(function(){var x=$(this).val();$('[id*=webcomic_lang_]').hide();$('[id*=[webcomic_lang_'+x+']]').show();}).change();});jQuery('select[name*=webcomic_paypal],input[name*=webcomic_paypal]').change( function(){var i,x,p,m,f,a,t,s,c,cost_d,cost_i,cost_o,ship_d,ship_i,ship_o,currency;i=jQuery( this ) . attr( 'name' ) . lastIndexOf( '_price' );cost_d=<?php echo $this->price( $this->option( 'paypal_price_d' ), array( $wc->webcomic_paypal[ 'price_d' ], $price_d ) ); ?>;cost_i=<?php echo $this->price( $this->option( 'paypal_price_i' ), array( $wc->webcomic_paypal[ 'price_i' ], $price_i ) ); ?>;cost_o=<?php echo $this->price( $this->option( 'paypal_price_o' ), array( $wc->webcomic_paypal[ 'price_o' ], $price_o ) ); ?>;ship_d=<?php echo $this->price( $this->option( 'paypal_shipping_d' ), array( $wc->webcomic_paypal[ 'shipping_d' ], $shipping_d ) ); ?>;ship_i=<?php echo $this->price( $this->option( 'paypal_shipping_i' ), array( $wc->webcomic_paypal[ 'shipping_i' ], $shipping_i ) ); ?>;ship_o=<?php echo $this->price( $this->option( 'paypal_shipping_o' ), array( $wc->webcomic_paypal[ 'shipping_o' ], $shipping_o ) ); ?>;currency=' <?php echo $this->option( 'paypal_currency' ); ?>';if(0<jQuery(this).attr('name').lastIndexOf('_d')){x='d';c=cost_d;s=ship_d;}else if(0<jQuery(this).attr('name').lastIndexOf('_i')){x='i';c=cost_i;s=ship_i;}else{x='o';c=cost_o;s=ship_o;}a=(i>0)?'webcomic_paypal_price_'+x:'webcomic_paypal_shipping_'+x;t=(i>0)?'webcomic_paypal_price_type_'+x:'webcomic_paypal_shipping_type_'+x;p=(i>0)?c:s;m=p*(Math.abs(jQuery('input[name='+a+']').val())/100);
f=('sub'==jQuery('select[name='+t+']').val())?p-m:p+m;f=(f<=.01)?'<span class="error">!</span>':f.toFixed(2)+currency;jQuery('.'+a).html(f);});</script>
		<?php
	}
	
	/**
	 * Displays appropriate content based on webcomic_ajax request.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function admin_ajax() {
		$this->domain();
		
		if ( empty( $_REQUEST[ 'webcomic_ajax' ] ) )
			return false;
		elseif ( 'collection' == $_REQUEST[ 'webcomic_ajax' ] ) {
			global $post;
			
			$wc  = ( $_REQUEST[ 'webcomic_collection' ] ) ? get_term( $_REQUEST[ 'webcomic_collection' ], 'webcomic_collection' ) : false;
			$ids = ( $_REQUEST[ 'post_ID' ] && is_object_in_term( $_REQUEST[ 'post_ID' ], 'webcomic_collection', $wc->term_id ) ) ? true : false;
		?>
		<td style="vertical-align:top;width:33%">
			<select name="webcomic_collection" id="webcomic_collection">
			<?php if ( 'webcomic_post' != $_REQUEST[ 'post_type' ] ) { ?><option value=""><?php _e( '&mdash; Collections &mdash;', 'webcomic' ); ?></option><?php } ?>
			<?php
				$walker = new webcomic_Walker_AdminTermDropdown();
				echo $walker->walk( get_terms( 'webcomic_collection', 'get=all' ), 0, array( 'selected' => array( $wc->term_id ), 'no_def' => false ) );
			?>
			</select><br>
			<?php
				if ( !$wc )
					printf( __( '<p>Select a collection to make this a webcomic %s.</p>', 'webcomic' ), $_REQUEST[ 'post_type' ] );
				else
					echo '<p>' . __( 'Hold <code>Ctrl</code> or <code>Command</code> to select multiple storylines and characters.', 'webcomic' ) . '</p>';
			?>
		</td>
		<td style="width:33%">
			<?php if ( $wc ) { ?>
			<label for="webcomic_storyline"><b><?php _e( 'Storylines', 'webcomic' ); ?></b></label><br>
			<select name="webcomic_storyline[]" id="webcomic_storyline" size="2" style="height:10em;width:100%" multiple>
			<?php
				$walker   = new webcomic_Walker_AdminTermDropdown();
				$selected = wp_get_object_terms( $_REQUEST[ 'post_ID' ], 'webcomic_storyline', array( 'fields' => 'ids' ) );
				echo $walker->walk( get_terms( 'webcomic_storyline', 'hide_empty=0&orderby=term_group_name&webcomic_order=1&term_group=' . $wc->term_id ), 0, array( 'selected' => $selected, 'no_def' => $ids ) );
			?>
			</select>
			<?php } ?>
		</td>
		<td style="width:33%">
			<?php if ( $wc ) { ?>
			<label for="webcomic_character"><b><?php _e( 'Characters', 'webcomic' ); ?></b></label><br>
			<select name="webcomic_character[]" id="webcomic_character" size="2" style="height:10em;width:100%" multiple>
			<?php
				$walker   = new webcomic_Walker_AdminTermDropdown();
				$selected = wp_get_object_terms( $_REQUEST[ 'post_ID' ], 'webcomic_character', array( 'fields' => 'ids' ) );
				echo $walker->walk( get_terms( 'webcomic_character', 'hide_empty=0&orderby=term_group_name&term_group=' . $wc->term_id ), 0, array( 'selected' => $selected, 'no_def' => $ids ) );
			?>
			</select>
			<?php } ?>
			<script type="text/javascript">jQuery(document).ready(function($){$('select#webcomic_collection').change(function(){$('input[name=webcomic_ajax]').val('collection');var params=$('form').serialize();$('#webcomic_terms').load('<?php echo $_SERVER[ 'PHP_SELF' ]; ?>',params);$('input[name=webcomic_ajax]').val('orphans');var params=$('form').serialize();$('#webcomic_orphans').load('<?php echo $_SERVER[ 'PHP_SELF' ]; ?>',params);$('input[name=webcomic_ajax]').val(0);});});</script>
		</td>
		<?php
			die();
		} elseif ( 'orphans' == $_REQUEST[ 'webcomic_ajax' ] && 'webcomic_post' == get_post_type( $_REQUEST[ 'post_ID' ] ) ) {
			$wc    = ( $_REQUEST[ 'webcomic_collection' ] ) ? get_term( $_REQUEST[ 'webcomic_collection' ], 'webcomic_collection' ) : false;
			
			if ( $wc ) {
				$abs       = $this->directory( 'abs', $wc->slug );
				$url       = $this->directory( 'url', $wc->slug );
				$tabs      = $abs . 'thumbs/';
				$turl      = $url . 'thumbs/';
				$files     = glob( $abs . '*.*' );
				$posts     = get_objects_in_term( $wc->term_id, 'webcomic_collection' );
				$term_meta = $this->option( 'term_meta' );
				
				foreach ( $term_meta as $taxonomy )
					foreach ( $taxonomy as $term )
						if ( !empty( $term[ 'files' ] ) )
							foreach ( $term[ 'files' ][ 'full' ] as $k => $v )
								if ( false !== ( $key = array_search( $abs . $term[ 'files' ][ 'full' ][ $k ], $files ) ) )
									unset( $files[ $key ] );
				
				foreach ( $posts as $k => $v ) {
					if ( 'webcomic_post' != get_post_type( $v ) )
						continue;
					
					$status = get_post_status( $v );
					
					$post_files = $this->fetch( $v, 'post', $wc->slug, true );
					
					if ( !empty( $post_files ) )
						foreach ( $post_files[ 'full' ] as $f ) {
							$fk = array_search( $abs . $f, $files );
							unset( $files[ $fk ] );
						}
				}
				
				if ( $files ) {
			?>
			<p><?php _e( 'Select one or more orphaned files to bind them to this post, or upload new files below.', 'webcomic' ); ?></p>
			<table id="availablethemes" cellspacing="0" cellpadding="0">
			<?php
				$rows  = ceil ( count( $files ) / 3 );
				$table = array();
				
				for ( $row = 1; $row <= $rows; $row++ )
					for ( $col = 1; $col <= 3; $col++ )
						$table[ $row ][ $col ] = array_shift( $files );
				
				foreach ( $table as $row => $cols ) {
				?>
				<tr>
				<?php
				foreach ( $cols as $col => $file ) {
					$class = array( 'available-theme' );
					
					if ( $row == 1 )     $class[] = 'top';
					if ( $row == $rows ) $class[] = 'bottom';
					if ( $col == 1 )     $class[] = 'left';
					if ( $col == 3 )     $class[] = 'right';
					if ( !$term ) { echo '<td class="' . join( ' ', $class ) . '"></td>'; continue; }
					?>
					<td class="<?php echo join( ' ', $class ); ?>">
					<?php if ( $file ) { ?>
						<input type="checkbox" name="webcomic_orphan[<?php echo hash( 'md5', $file ); ?>]" id="webcomic_orphan[<?php echo hash( 'md5', $file ); ?>]" value="<?php echo stripslashes( $file ); ?>">
						<label for="webcomic_orphan[<?php echo hash( 'md5', $file ); ?>]">
						<?php
							$orphan = $this->retrieve( $file, 'orphan', $wc->slug );
							
							if ( $orphan[ 'small' ] )
								echo $orphan[ 'small' ][ 0 ][ 'html' ];
							elseif ( $orphan[ 'medium' ] )
								echo $orphan[ 'medium' ][ 0 ][ 'html' ];
							elseif ( $orphan[ 'large' ] )
								echo $orphan[ 'large' ][ 0 ][ 'html' ];
							else
								echo $orphan[ 'full' ][ 0 ][ 'html' ];
						?>
						</label><br>
						<a href="<?php echo $orphan[ 'full' ][ 0 ][ 'url' ]; ?>" target="_blank"><?php _e( 'View', 'webcomic' ); ?></a>
						<?php } ?>
					</td>
					<?php } ?>
				</tr>
				<?php } ?>
			</table>
		<?php } }
			die();
		} elseif ( 'batch_post' == $_REQUEST[ 'webcomic_ajax' ] ) {
		?>
		<div class="form-field">
			<label for="webcomic_post_storylines" style="float:left"><b><?php _e( 'Storylines', 'webcomic' ); ?></b></label>
			<select name="webcomic_post_storylines_action" style="float:right">
				<option value="add"><?php _e( 'Add', 'webcomic' ); ?></option>
				<option value="replace"><?php _e( 'Replace', 'webcomic' ); ?></option>
			</select>
			<select name="webcomic_post_storylines[]" id="webcomic_post_storylines" size="2" style="height:10em;width:100%" multiple>
			<?php
				$walker   = new webcomic_Walker_AdminTermDropdown();
				echo $walker->walk( get_terms( 'webcomic_storyline', 'hide_empty=0&orderby=term_group_name&webcomic_order=1&term_group=' . $_REQUEST[ 'webcomic_post_collection' ] ), 0, array( 'selected' => array(), 'no_def' => true ) );
			?>
			</select>
			<p><?php _e( 'Hold <code>CTRL</code> or <code>Command</code> to select multiple storylines. Remove posts from all storylines by selecting &#8220;Replace&#8221; with no storylines selected.', 'webcomic' ); ?></p>
		</div>
		<div class="form-field">
			<label for="webcomic_post_characters" style="float:left"><b><?php _e( 'Characters', 'webcomic' ); ?></b></label>
			<select name="webcomic_post_characters_action" style="float:right">
				<option value="add"><?php _e( 'Add', 'webcomic' ); ?></option>
				<option value="replace"><?php _e( 'Replace', 'webcomic' ); ?></option>
			</select>
			<select name="webcomic_post_characters[]" id="webcomic_post_characters" size="2" style="height:10em;width:100%" multiple>
			<?php
				$walker   = new webcomic_Walker_AdminTermDropdown();
				echo $walker->walk( get_terms( 'webcomic_character', 'hide_empty=0&orderby=term_group_name&term_group=' . $_REQUEST[ 'webcomic_post_collection' ] ), 0, array( 'selected' => array(), 'no_def' => true ) );
			?>
			</select>
			<p><?php _e( 'Hold <code>CTRL</code> or <code>Command</code> to select multiple characters. Remove posts from all characters by selecting &#8220;Replace&#8221; with no characters selected.', 'webcomic' ); ?></p>
		</div>
		<?php
			die();
		} else
			die();
	}
	
	/**
	 * Administrative footer hook.
	 * 
	 * We can't add this using the auto-hook method
	 * because the hook names have '-' in them. See
	 * hook_admin_init for the add_action() calls.
	 * 
	 * @package webcomic
	 * @since 3
	 */
	function admin_footer_files() {
		global $post, $current_user;
		
		/** We have to avoid using wp_tiny_mce() here as it conflicts with the main editor, so let's call tinyMCE.init directly. */
		if ( isset( $post ) && 'webcomic_post' == $post->post_type && $current_user->data->rich_editing ) {
			$mce_locale = ( '' == get_locale() ) ? 'en' : strtolower( substr( get_locale(), 0, 2 ) );
		?>
		<script>/* <![CDATA[ */tinyMCE.init( { mode:"specific_textareas", editor_selector:"webcomic_tinymce", width:"100%", theme:"advanced", skin:"wp_theme", theme_advanced_buttons1:"bold,italic,|,bullist,numlist,blockquote,|,link,unlink,|,charmap,spellchecker,wp_adv", theme_advanced_buttons2:"formatselect,|,justifyleft,justifycenter,justifyright,justifyfull,|,sub,sup,|,removeformat", theme_advanced_buttons3:"", theme_advanced_buttons4:"", language:"<?php echo $mce_locale; ?>", spellchecker_languages:"+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv", theme_advanced_toolbar_location:"top", theme_advanced_toolbar_align:"left", theme_advanced_statusbar_location:"bottom", theme_advanced_resizing:"1", theme_advanced_resize_horizontal:"", dialog_type:"modal", relative_urls:"", remove_script_host:"", convert_urls:"", apply_source_formatting:"", remove_linebreaks:"1", gecko_spellcheck:"1", entities:"38,amp,60,lt,62,gt", accessibility_focus:"", tabfocus_elements:"major-publishing-actions", media_strict:"", paste_remove_styles:"1", paste_remove_spans:"1", paste_strip_class_attributes:"all", wpeditimage_disable_captions:"", plugins:"safari,inlinepopups,spellchecker,paste,wordpress,fullscreen,tabfocus", content_css:"http://localhost/rd/wp-content/themes/archimedes/style-editor.css" } ); /* ]]> */</script>
		<?php
		}
	}
}
?>
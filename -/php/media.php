<?php
/**
 * Contains the WebcomicMedia class.
 * 
 * @todo core.trac.wordpress.org/ticket/16031
 * 
 * @package Webcomic
 */

/**
 * Handle media-related tasks.
 * 
 * @package Webcomic
 */
class WebcomicMedia extends Webcomic {
	/**
	 * Register hooks.
	 * 
	 * @uses WebcomicMedia::admin_init()
	 * @uses WebcomicMedia::admin_menu()
	 * @uses WebcomicMedia::admin_foot()
	 * @uses WebcomicMedia::delete_attachment()
	 * @uses WebcomicMedia::admin_enqueue_scripts()
	 * @uses WebcomicMedia::media_upload_webcomic_media()
	 * @uses WebcomicMedia::attachment_submitbox_misc_actions()
	 * @uses WebcomicMedia::media_upload_tabs()
	 * @uses WebcomicMedia::media_row_actions()
	 * @uses WebcomicMedia::display_media_states()
	 * @uses WebcomicMedia::image_size_names_choose()
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_footer', array( $this, 'admin_footer' ) );
		add_action( 'delete_attachment', array( $this, 'delete_attachment' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'media_upload_webcomic_media', array( $this, 'media_upload_webcomic_media' ) );
		add_action( 'attachment_submitbox_misc_actions', array( $this, 'attachment_submitbox_misc_actions' ), 20 );

		add_filter( 'media_row_actions',  array( $this, 'media_row_actions' ), 10, 2 );
		add_filter( 'display_media_states', array( $this, 'display_media_states' ), 10, 1 );
		add_filter( 'image_size_names_choose', array( $this, 'image_size_names_choose' ), 10, 1 );
	}
	
	/**
	 * Handle media functions.
	 * 
	 * @uses Webcomic::$config
	 * @uses WebcomicAdmin::notify()
	 * @hook admin_init
	 */
	public function admin_init() {
		global $wpdb;
		
		if ( isset( $_GET[ 'webcomic_action' ], $_GET[ 'post' ] ) and 'regenerate' === $_GET[ 'webcomic_action' ] and $meta = wp_get_attachment_metadata( $_GET[ 'post' ] ) ) {
			check_admin_referer( 'webcomic_regenerate' );
			
			$dir = wp_upload_dir();
			
			foreach ( get_intermediate_image_sizes() as $size ) {
				if ( $file = image_get_intermediate_size( $_GET[ 'post' ], $size ) ) {
					@unlink( path_join( $dir[ 'basedir' ], $file[ 'path' ] ) );
				}
			}
			
			if ( is_wp_error( $meta = wp_generate_attachment_metadata( $_GET[ 'post' ], get_attached_file( $_GET[ 'post' ] ) ) ) and empty( $_GET[ 'webcomic_hide_notice' ] ) ) {
				WebcomicAdmin::notify( __( 'Images could not be regenerated.', "webcomic" ), 'error' );
			} elseif ( empty( $_GET[ 'webcomic_hide_notice' ] ) ) {
				wp_update_attachment_metadata( $_GET[ 'post' ], $meta );
				
				WebcomicAdmin::notify( __( 'Regenerated images for media attachments.', "webcomic" ) );
			}
		}
		
		if ( isset( $_GET[ 'webcomic_action' ], $_GET[ 'post' ] ) and 'detach' === $_GET[ 'webcomic_action' ] and intval( $_GET[ 'post' ] ) ) {
			check_admin_referer( 'webcomic_detach' );
			
			if ( wp_update_post( array( 'ID' => ( integer ) $_GET[ 'post' ], 'post_parent' => 0 ) ) and empty( $_GET[ 'webcomic_hide_notice' ] ) ) {
				WebcomicAdmin::notify( __( 'Detached media.', "webcomic" ) );
			} elseif ( empty( $_GET[ 'webcomic_hide_notice' ] ) ) {
				WebcomicAdmin::notify( __( 'Media could not be detached.', "webcomic" ), 'error' );
			}
		}
		
		if ( isset( $_POST[ 'webcomic_action' ] ) and 'webcomic_regenerate' === $_POST[ 'webcomic_action' ] and !empty( $_POST[ 'media' ] ) ) {
			check_admin_referer( 'bulk-media' );
			
			$dir   = wp_upload_dir();
			$good  = $bad = 0;
			$sizes = get_intermediate_image_sizes();
			
			foreach ( $_POST[ 'media' ] as $id ) {
				if ( false !== strpos( get_post_mime_type( $id ), 'image' ) ) {
					foreach ( $sizes as $size ) {
						if ( $file = image_get_intermediate_size( $id, $size ) ) {
							@unlink( path_join( $dir[ 'basedir' ], $file[ 'path' ] ) );
						}
					}
					
					if ( is_wp_error( $meta = wp_generate_attachment_metadata( $id, get_attached_file( $id ) ) ) ) {
						$bad++;
					} else {
						wp_update_attachment_metadata( $id, $meta );
						
						$good++;
					}
				}
			}
			
			if ( $bad ) {
				WebcomicAdmin::notify( sprintf( _n( 'Images could not be regenerated for %s media attachment.', 'Images could not be regenerated for %s media attachments.', $bad, "webcomic" ), $bad ), 'error' );
			}
			
			if ( $good ) {
				WebcomicAdmin::notify( sprintf( _n( 'Images regenerated for %s media attachment.', 'Images regenerated for %s media attachments.', $good, "webcomic" ), $good ) );
			}
		}
		
		if ( isset( $_POST[ 'webcomic_action' ] ) and 'webcomic_detach' === $_POST[ 'webcomic_action' ] and !empty( $_POST[ 'media' ] ) ) {
			check_admin_referer( 'bulk-media' );
			$good = $bad = 0;
			
			foreach ( $_POST[ 'media' ] as $id ) {
				if ( $wpdb->get_var( $wpdb->prepare( "SELECT post_parent FROM {$wpdb->posts} WHERE ID = %s", $id ) ) ) {
					if ( wp_update_post( array( 'ID' => ( integer ) $id, 'post_parent' => 0 ) ) ) {
						$good++;
					} else {
						$bad++;
					}
				}
			}
			
			if ( $bad ) {
				WebcomicAdmin::notify( sprintf( _n( 'Could not detach %s attachment.', 'Could not detach %s attachments.', $bad, "webcomic" ), $bad ), 'error' );
			}
			
			if ( $good ) {
				WebcomicAdmin::notify( sprintf( _n( 'Detached %s attachment.', 'Detached %s attachments.', $good, "webcomic" ), $good ) );
			}
		}
		
		if ( isset( $_POST[ 'webcomic_action' ] ) and 'attach' === $_POST[ 'webcomic_action' ] and wp_verify_nonce( $_POST[ 'webcomic_attach' ], 'webcomic_attach' ) ) {
			if ( empty( $_POST[ 'matches' ] ) ) {
				WebcomicAdmin::notify( __( 'No matches selected.', "webcomic" ), 'error' );
			} else {
				$good = $bad = 0;
				
				foreach ( $_POST[ 'matches' ] as $match ) {
					$ids = explode( '|', $match );
					
					if ( wp_update_post( array( 'ID' => ( integer ) $ids[ 0 ], 'post_parent' => ( integer ) $ids[ 1 ] ) ) ) {
						$good++;
					} else {
						$bad++;
					}
				}
				
				if ( $bad ) {
					WebcomicAdmin::notify( sprintf( _n( 'A media could not be attached to %s post.', 'Media could not be attached to %s posts.', $bad, "webcomic" ), $bad ), 'error' );
				}
				
				WebcomicAdmin::notify( sprintf( _n( 'Media attached to %s post.', 'Media attached to %s posts.', $good, "webcomic" ), $good ) );
			}
		}
		
		if ( isset( $_POST[ 'webcomic_action' ] ) and 'generate' === $_POST[ 'webcomic_action' ] and wp_verify_nonce( $_POST[ 'webcomic_generate' ], 'webcomic_generate' ) ) {
			if ( empty( $_POST[ 'attachments' ] ) or !$attachments = $_POST[ 'attachments' ] ) {
				WebcomicAdmin::notify( '<b>' . __( 'No media selected.', "webcomic" ) . '</b>', 'error' );
			} elseif ( !$start = strtotime( $_POST[ 'webcomic_generate_start' ] ) ) {
				WebcomicAdmin::notify( '<b>' . __( 'The start date could not be understood.', "webcomic" ) . '</b>', 'error' );
			} elseif ( !$days = $_POST[ 'webcomic_generate_days' ] ) {
				WebcomicAdmin::notify( '<b>' . __( 'No weekdays selected.', "webcomic" ) . '</b>', 'error' );
			} else {
				$now      = $start;
				$good     = $bad = 0;
				$draft    = isset( $_POST[ 'webcomic_generate_draft' ] );
				$weekday  = date( 'N', $start );
				$weekdays = array(
					1 => 'Monday',
					2 => 'Tuesday',
					3 => 'Wednesday',
					4 => 'Thursday',
					5 => 'Friday',
					6 => 'Saturday',
					7 => 'Sunday'
				);
				
				foreach ( $attachments as $id ) {
					$attachment = get_post( $id );
					
					if ( $new_post = wp_insert_post( array(
						'post_type'     => $_POST[ 'webcomic_collection' ],
						'post_date'     => date( 'Y-m-d H:i:s', $now ),
						'post_title'    => $attachment->post_title,
						'post_status'   => $draft ? 'draft' : 'publish',
						'post_content'  => '&nbsp;',
						'post_date_gmt' => get_gmt_from_date( date( 'Y-m-d H:i:s', $now ) )
					) ) and !is_wp_error( $new_post ) ) {
						if ( wp_update_post( array( 'ID' => $id, 'post_parent' => $new_post ) ) ) {
							add_post_meta( $new_post, 'webcomic_prints', self::$config[ 'collections' ][ $_POST[ 'webcomic_collection' ] ][ 'commerce' ][ 'prints' ], true );
							add_post_meta( $new_post, 'webcomic_original', true, true );
							add_post_meta( $new_post, 'webcomic_transcripts', self::$config[ 'collections' ][ $_POST[ 'webcomic_collection' ] ][ 'transcripts' ][  'open' ], true );
							
							$good++;
						} else {
							$bad++;
						}
					} else {
						$bad++;
					}
					
					if ( false !== ( $key = array_search( $weekday, $days ) ) ) {
						$weekday = empty( $days[ $key + 1 ] ) ? $days[ 0 ] : $days[ $key + 1 ];
					} elseif ( $weekday > max( $days ) or $weekday < min( $days ) ) {
						$weekday = $days[ 0 ];
					} else {
						foreach ( $days as $day ) {
							if ( $weekday < $day ) {
								$weekday = $day;
								break;
							}
						}
					}
					
					$now = strtotime( "next {$weekdays[ $weekday ]}", $now );
				}
				
				if ( $bad ) {
					WebcomicAdmin::notify( sprintf( _n( 'A post could not be generated for %d media.', 'Posts could not be generated for %d media.', $bad, "webcomic" ), $bad ), 'error' );
				}
				
				WebcomicAdmin::notify( sprintf( _n( 'Post created for %d media.', 'Posts created for %d media.', $good, "webcomic" ), $good ) );
			}
		}
	}
	
	/**
	 * Register Media submenu pages.
	 * 
	 * @uses WebcomicMedia::generator()
	 * @hook admin_menu
	 */
	public function admin_menu() {
		add_submenu_page( 'upload.php', __( 'Webcomic Attacher', "webcomic" ), __( 'Webcomic Attacher', "webcomic" ), 'upload_files', 'webcomic-attacher', array( $this, 'page_attacher' ) );
		add_submenu_page( 'upload.php', __( 'Webcomic Generator', "webcomic" ), __( 'Webcomic Generator', "webcomic" ), 'upload_files', 'webcomic-generator', array( $this, 'page_generator' ) );
	}
	
	/**
	 * Render HTML data for bulk media actions.
	 * 
	 * @hook admin_footer
	 */
	public function admin_footer() {
		$screen = get_current_screen();
		
		if ( 'upload' === $screen->id ) {
			echo'<span data-webcomic-admin-url="', admin_url( 'upload.php' ), '" data-webcomic-regenerate="', esc_attr__( 'Regenerate Images', "webcomic" ), '" data-webcomic-detach="', esc_attr__( 'Detach', "webcomic" ), '"></span>';
		}
	}
	
	/**
	 * Manage webcomic media from the modal media manager.
	 * 
	 * @hook media_upload_webcomic_media
	 */
	public function media_upload_webcomic_media( $output = false ) {
		if ( $output ) {
			if ( $attachments = self::get_attachments( $_GET[ 'post_id' ] ) ) {
				echo '<div data-webcomic-modal-media="', admin_url(), '"><p>', __( 'Drag and drop the media attachments to change the order Webcomic will display them.', "webcomic" ), '</p></div><ul class="webcomic-media-sort">';
				
				foreach ( $attachments as $attachment ) {
					echo '<li><b>', wp_get_attachment_image( $attachment->ID ), '</b><a href="', esc_html( wp_nonce_url( add_query_arg( array_merge( $_GET, array( 'post' => $attachment->ID, 'action' => 'edit', 'webcomic_action' => 'regenerate', 'webcomic_hide_notice' => true ) ), admin_url( 'media-upload.php' ) ), 'webcomic_regenerate' ) ), '" title="', __( 'Regenerate', "webcomic" ), '">', __( 'Regenerate', "webcomic" ), '</a><a href="', esc_html( wp_nonce_url( add_query_arg( array_merge( $_GET, array( 'post' => $attachment->ID, 'action' => 'edit', 'webcomic_action' => 'detach', 'webcomic_hide_notice' => true ) ), admin_url( 'media-upload.php' ) ), 'webcomic_detach' ) ), '" title="', __( 'Detach', "webcomic" ), '">', __( 'Detach', "webcomic" ), '</a><input type="hidden" name="ids[]" value="', $attachment->ID, '"></li>';
				}
			} else {
				echo '<div><p>', __( 'To manage Webcomic Media please attach one or more images to this webcomic.', "webcomic" ), '</p></div>';
			}
		} else {
			wp_iframe( array( $this, 'media_upload_webcomic_media' ), true );
		}
	}
	
	/**
	 * Unset term attachment info when a media object is deleted.
	 * 
	 * @param integer $id The deleted attachment ID.
	 * @uses Webcomic::$config
	 * @hook delete_attachment
	 */
	public function delete_attachment( $id ) {
		foreach ( self::$config[ 'terms' ] as $k => $v ) {
			if ( $v[ 'image' ] === $id ) {
				unset( self::$config[ 'terms' ][ $k ] );
				
				update_option( 'webcomic_options', self::$config );
				
				break;
			}
		}
		
		foreach ( array_keys( self::$config[ 'collections' ] ) as $k ) {
			if ( self::$config[ 'collections' ][ $k ][ 'image' ] === $id ) {
				self::$config[ 'collections' ][ $k ][ 'image' ] = 0;
				
				update_option( 'webcomic_options', self::$config );
				
				break;
			}
		}
	}
	
	/**
	 * Register and enqueue media scripts.
	 * 
	 * @hook admin_enqueue_scripts
	 */
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		
		if ( 'upload' === $screen->id or 'media_page_webcomic-generator' === $screen->id or ( isset( $_GET[ 'tab' ] ) and 'webcomic_media' === $_GET[ 'tab' ] ) ) {
			wp_enqueue_script( 'webcomic-admin-media', self::$url . '-/js/admin-media.js', array( 'jquery-ui-sortable' ) );
		}
		
		if ( isset( $_GET[ 'tab' ] ) and 'webcomic_media' === $_GET[ 'tab' ] ) {
			wp_enqueue_style( 'webcomic-admin-media', self::$url . '-/css/admin-media.css' );
		}
	}
	
	/**
	 * Display attachment and alternate size details on Edit Media page.
	 * 
	 * @hook attachment_submitbox_misc_actions
	 */
	public function attachment_submitbox_misc_actions() {
		global $post;
		
		if ( $post->post_parent ) {
			echo '<div class="misc-pub-section">', __( 'Attached to:', "webcomic" ), ' <b>', ( current_user_can( 'edit_post', $post->post_parent ) and 'trash' !== get_post_status( $post->post_parent ) ) ? '<a href="' . get_edit_post_link( $post->post_parent ) . '">' . esc_html( get_the_title( $post->post_parent ) ) . '</a>' : esc_html( get_the_title( $post->post_parent ) ), ' ', get_the_time( 'Y/m/d', $post->post_parent ), '</b><br><a href="', esc_html( wp_nonce_url( add_query_arg( array( 'post' => $post->ID, 'action' => 'edit', 'webcomic_action' => 'detach' ), admin_url( 'post.php' ) ), 'webcomic_detach' ) ), '" class="button">', __( 'Detach', "webcomic" ), '</a></div>';
		}
		
		if ( 0 === strpos( $post->post_mime_type, 'image' ) ) {
			$sizes = array();
			
			foreach ( get_intermediate_image_sizes() as $size ) {
				if ( $file = image_get_intermediate_size( $post->ID, $size ) ) {
					$sizes[ $file[ 'width' ] * $file[ 'height' ] ] = $size . ' &ndash; ' . $file[ 'width' ] . ' &times; ' . $file[ 'height' ];
				}
			}
			
			krsort( $sizes );
			
			echo '<div class="misc-pub-section">', __( 'Alternate Sizes:', "webcomic" ), '<br><b>', $sizes ? implode( '<br>', $sizes ) : __( 'None available', "webcomic" ), '</b><br><a href="', esc_html( wp_nonce_url( add_query_arg( array( 'post' => $post->ID, 'action' => 'edit', 'webcomic_action' => 'regenerate' ), admin_url( 'post.php' ) ), 'webcomic_regenerate' ) ), '" class="button">', __( 'Regenerate', "webcomic" ), '</a></div>';
		}
	}
	
	/**
	 * Add regenerate and detach options to media table.
	 * 
	 * This feature is not Webcomic-specific; any attached media object
	 * will gain a functional Detach option. With luck a future
	 * WordPress update will make this function obsolete.
	 * 
	 * @param array $actions The available actions array.
	 * @param object $attachment The current attachment.
	 * @return array
	 * @hook media_row_actions
	 */
	public function media_row_actions( $actions, $attachment ) {
		if ( 0 === strpos( $attachment->post_mime_type, 'image' ) ) {
			$actions[ 'webcomic_regen' ] = '<a href="' . esc_html( wp_nonce_url( add_query_arg( array( 'post' => $attachment->ID, 'webcomic_action' => 'regenerate' ), admin_url( 'upload.php' ) ), 'webcomic_regenerate' ) ) . '">' . __( 'Regenerate', "webcomic" ) . '</a>';
		}
		
		if ( $attachment->post_parent ) {
			$actions[ 'webcomic_detach' ] = '<a href="' . esc_html( wp_nonce_url( add_query_arg( array( 'post' => $attachment->ID, 'webcomic_action' => 'detach' ), admin_url( 'upload.php' ) ), 'webcomic_detach' ) ) . '">' . __( 'Detach', "webcomic" ) . '</a>';
		}
		
		return $actions;
	}
	
	/**
	 * Display relevant status for webcomic media.
	 * 
	 * @param array $states List of media states.
	 * @return array
	 * @hook display_media_states
	 */
	public function display_media_states( $states ) {
		global $post;
		
		if ( $type = get_post_meta( $post->ID, '_wp_attachment_context', true ) and preg_match( '/^(widget-webcomic-print|widget-webcomic-donation|widget-(purchase-)?webcomic(-(collection|storyline|character|transcripts))?-link|webcomic\d+(_(storyline|character)))?$/', $type ) ) {
			$type = explode( '_', $type );
			
			if ( 'widget-webcomic-print' === $type[ 0 ] ) {
				$states[] = __( 'Webcomic Print Image', "webcomic" );
			} elseif ( 'widget-webcomic-link' === $type[ 0 ] ) {
				$states[] = __( 'Webcomic Link Image', "webcomic" );
			} elseif ( 'widget-webcomic-donation' === $type[ 0 ] ) {
				$states[] = __( 'Webcomic Donation Image', "webcomic" );
			} elseif ( 'widget-purchase-webcomic-link' === $type[ 0 ] ) {
				$states[] = __( 'Purchase Webcomic Link Image', "webcomic" );
			} elseif ( 'widget-webcomic-storyline-link' === $type[ 0 ] ) {
				$states[] = __( 'Webcomic Storyline Link Image', "webcomic" );
			} elseif ( 'widget-webcomic-character-link' === $type[ 0 ] ) {
				$states[] = __( 'Webcomic Character Link Image', "webcomic" );
			} elseif ( 'widget-webcomic-collection-link' === $type[ 0 ] ) {
				$states[] = __( 'Webcomic Collection Link Image', "webcomic" );
			} elseif ( 'widget-webcomic-transcripts-link' === $type[ 0 ] ) {
				$states[] = __( 'Webcomic Transcripts Link Image', "webcomic" );
			} elseif ( empty( $type[ 1 ] ) ) {
				$states[] = sprintf( __( '%s Poster', "webcomic" ), esc_html( self::$config[ 'collections' ][ $type[ 0 ] ][ 'name' ] ) );
			} elseif ( 'storyline' === $type[ 1 ] ) {
				$states[] = sprintf( __( '%s Cover', "webcomic" ), esc_html( self::$config[ 'collections' ][ $type[ 0 ] ][ 'name' ] ) );
			} elseif ( 'character' === $type[ 1 ] ) {
				$states[] = sprintf( __( '%s Avatar', "webcomic" ), esc_html( self::$config[ 'collections' ][ $type[ 0 ] ][ 'name' ] ) );
			}
		}
		
		return $states;
	}
	
	/**
	 * Add additional image sizes for inserting into posts.
	 * 
	 * @uses Webcomic::$config
	 */
	public function image_size_names_choose( $sizes ) {
		foreach ( self::$config[ 'sizes' ] as $k => $v ) {
			$sizes[ $k ] = ucwords( str_replace( '-', ' ', $k ) );
		}
		
		return $sizes;
	}
	
	/**
	 * Render the webcomic attacher.
	 * 
	 * @uses Webcomic::$config
	 */
	public function page_attacher() {
		global $wpdb, $post;
		?>
		<div class="wrap">
			<div class="icon32" id="icon-upload"></div>
			<h2><?php echo get_admin_page_title(); ?></h2>
			<div id="col-container">
				<div id="col-right">
					<div class="col-wrap">
					<?php
						if ( $_POST ) {
							$matches = $dupe = array();
							
							$columns = "
								<tr>
									<th class='check-column'><input type='checkbox'></th>
									<th class='column-icon'></th>
									<th>" . __( "File", "webcomic" ) . "</th>
									<th>" . __( "Matched With", "webcomic" ) . "</th>
								</tr>";
							
							$attachments = get_posts( array(
								"post_type" => "attachment",
								"post_status" => "inherit",
								"post_mime_type" => "image",
								"post_parent" => 0,
								"numberposts" => -1
							) );
							
							$posts = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->posts} WHERE post_type = %s AND post_status NOT IN ( 'auto-draft', 'private', 'inherit', 'trash' ) AND ID NOT IN ( SELECT post_parent FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%%' )", $_POST[ "webcomic_collection" ] ) );
							
							if ( $attachments and $posts ) {
								foreach ( $posts as $post ) {
									$post_field = "webcomic_collection";
									
									if ( "post_title" === $_POST[ "webcomic_post_field" ] ) {
										$post_field = $post->post_title;
									} elseif ( "post_name" === $_POST[ "webcomic_post_field" ] ) {
										$post_field = $post->post_name;
									} elseif ( "post_date" === $_POST[ "webcomic_post_field" ] ) {
										$post_field = mysql2date( $_POST[ "webcomic_date_format" ], $post->post_date );
									} elseif ( "post_custom" === $_POST[ "webcomic_post_field" ] ) {
										$post_field = get_post_meta( $post->ID, $_POST[ "webcomic_custom_key" ] );
									}
									
									foreach ( $attachments as $attachment ) {
										$attachment_field = "post_title";
										
										if ( "post_title" === $_POST[ "webcomic_media_field" ] ) {
											$attachment_field = $attachment->post_title;
										} elseif ( "post_name" === $_POST[ "webcomic_media_field" ] ) {
											$attachment_field = $attachment->post_name;
										} elseif ( "post_date" === $_POST[ "webcomic_media_field" ] ) {
											$attachment_field = mysql2date( $_POST[ "webcomic_date_format" ], $attachment->post_date );
										} elseif ( "guid" === $_POST[ "webcomic_media_field" ] ) {
											$attachment_field = pathinfo( $attachment->guid );
										}
										
										if ( $post_field and $attachment_field ) {
											if ( empty( $_POST[ "webcomic_exact_match" ] ) ) {
												if ( ( !is_array( $attachment_field ) and false !== strpos( $attachment_field, $post_field ) ) or ( is_array( $attachment_field ) and false !== strpos( $attachment_field[ "filename" ], $post_field ) ) ) {
													$matches[] = array( "media" => $attachment, "post" => $post );
												}
											} elseif ( $post_field === $attachment_field or ( is_array( $attachment_field ) and $post_field === $attachment_field[ "filename" ] ) ) {
												$matches[] = array( "media" => $attachment, "post" => $post );
											}
										}
									}
								}
							}
					?>
						<form method="post">
							<?php wp_nonce_field( "webcomic_attach", "webcomic_attach" ); ?>
							<table class="wp-list-table widefat fixed posts">
								<thead><?php echo $columns; ?></thead>
								<tfoot><?php echo $columns; ?></tfoot>
								<tbody>
									<?php $i = 0; if ( $matches ) { foreach ( $matches as $match ) { $post = $match[ "media" ]; ?>
									<tr<?php echo ( isset( $dupe[ $match[ "post" ]->ID ] ) or isset( $dupe[ $match[ "media" ]->ID ] ) ) ? " style='background:#ffebe8'" : ""; echo $i % 2 ? "" : " class='alternate'"; ?>>
										<th class="check-column"><input type="checkbox" name="matches[]" id="match-<?php echo $i; ?>" value="<?php echo $match[ "media" ]->ID, "|", $match[ "post" ]->ID; ?>"<?php echo ( isset( $dupe[ $match[ "post" ]->ID ] ) or isset( $dupe[ $match[ "media" ]->ID ] ) ) ? "" : " checked"; ?>></th>
										<td class="media-icon"><label for="match-<?php echo $i; ?>"><?php echo wp_get_attachment_image( $match[ "media" ]->ID, array( 60, 60 ) ); ?></label></td>
										<td>
											<a href="<?php echo esc_url( add_query_arg( array( "post" => $match[ "media" ]->ID, "action" => "edit" ), admin_url( "post.php" ) ) ); ?>"><span class="row-title"><?php echo get_the_title( $match[ "media" ]->ID ); ?></span></a><b><?php _media_states( $match[ "media" ] ); ?></b>
											<p><?php echo ( preg_match( "/^.*?\.(\w+)$/", get_attached_file( $match[ 'media' ]->ID ), $m ) ) ? esc_html( strtoupper( $m[ 1 ] ) ) : strtoupper( str_replace( 'image/', '', get_post_mime_type( $match[ "media" ]->ID ) ) ); ?></p>
										</td>
										<td>
											<?php
												$post = $match[ "post" ];
												
												echo "<b>", ( current_user_can( "edit_post", $post->post_parent ) and "trash" !== get_post_status( $post->post_parent ) ) ? "<a href='" . get_edit_post_link( $post->post_parent ) . "'>" . esc_html( get_the_title( $post->post_parent ) ) . "</a>" : esc_html( get_the_title( $post->post_parent ) ), "</b><p>", get_the_time( "Y/m/d", $post->post_parent ), "</p>";
											?>
										</td>
									</tr>
									<?php $i++; $dupe[ $match[ "post" ]->ID ] = $dupe[ $match[ "media" ]->ID ] = true; } } else { ?>
									<tr class="no-items">
										<td colspan="4">
											<?php
												if ( !$attachments or is_wp_error( $attachments ) ) {
													_e( "No unattached media could be found.", "webcomic" );
												} elseif ( !$posts ) {
													_e( "No orphaned webcomic posts could be found.", "webcomic" );
												} else {
													_e( "No matches could be found.", "webcomic" );
												}
											?>
										</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
							<p>
								<?php
									if ( $matches ) {
										submit_button( __( "Attach Media", "webcomic" ) );
									}
								?>
							</p>
							<input type="hidden" name="webcomic_collection" value="<?php echo $_POST[ "webcomic_collection" ]; ?>">
							<input type="hidden" name="webcomic_post_field" value="<?php echo $_POST[ "webcomic_post_field" ]; ?>">
							<input type="hidden" name="webcomic_media_field" value="<?php echo $_POST[ "webcomic_media_field" ]; ?>">
							<input type="hidden" name="webcomic_date_format" value="<?php echo $_POST[ "webcomic_date_format" ]; ?>">
							<input type="hidden" name="webcomic_custom_key" value="<?php echo $_POST[ "webcomic_custom_key" ]; ?>">
							<input type="hidden" name="webcomic_action" value="attach">
						</form>
					<?php } ?>
					</div>
				</div>
				<div id="col-left">
					<form method="post">
						<div class="col-wrap">
							<div class="form-wrap">
								<label>
									<h3><?php _e( "Collection", "webcomic" ); ?></h3>
									<select name="webcomic_collection">
									<?php
										foreach ( self::$config[ "collections" ] as $k => $v ) {
											echo "<option value='", $k, "'", selected( $k, empty( $_POST[ "webcomic_collection" ] ) ? "" : $_POST[ "webcomic_collection" ] ), ">", esc_html( $v[ "name" ] ), "</option>";
										}
									?>
									</select>
								</label>
								<label>
									<h3><?php _e( "Match post&hellip;", "webcomic" ); ?></h3>
									<select name="webcomic_post_field">
										<option value="post_title"<?php selected( "post_title", empty( $_POST[ "webcomic_post_field" ] ) ? "" : $_POST[ "webcomic_post_field" ] ); ?>><?php _e( "Title", "webcomic" ); ?></option>
										<option value="post_name"<?php selected( "post_name", empty( $_POST[ "webcomic_post_field" ] ) ? "" : $_POST[ "webcomic_post_field" ] ); ?>><?php _e( "Slug", "webcomic" ); ?></option>
										<option value="post_date"<?php selected( "post_date", empty( $_POST[ "webcomic_post_field" ] ) ? "" : $_POST[ "webcomic_post_field" ] ); ?>><?php _e( "Date", "webcomic" ); ?></option>
										<option value="post_custom"<?php selected( "post_custom", empty( $_POST[ "webcomic_post_field" ] ) ? "" : $_POST[ "webcomic_post_field" ] ); ?>><?php _e( "Custom Field", "webcomic" ); ?></option>
									</select>
								</label>
								<label>
									<h3><?php _e( "With media&hellip;", "webcomic" ); ?></h3>
									<select name="webcomic_media_field">
										<option value="guid"<?php selected( "guid", empty( $_POST[ "webcomic_media_field" ] ) ? "" : $_POST[ "webcomic_media_field" ] ); ?>><?php _e( "Filename", "webcomic" ); ?></option>
										<option value="post_title"<?php selected( "post_title", empty( $_POST[ "webcomic_media_field" ] ) ? "" : $_POST[ "webcomic_media_field" ] ); ?>><?php _e( "Title", "webcomic" ); ?></option>
										<option value="post_name"<?php selected( "post_name", empty( $_POST[ "webcomic_media_field" ] ) ? "" : $_POST[ "webcomic_media_field" ] ); ?>><?php _e( "Slug", "webcomic" ); ?></option>
										<option value="post_date"<?php selected( "post_date", empty( $_POST[ "webcomic_media_field" ] ) ? "" : $_POST[ "webcomic_media_field" ] ); ?>><?php _e( "Date", "webcomic" ); ?></option>
									</select>
								</label>
								<label>
									<h3><?php _e( "Date Format", "webcomic" ); ?></h3>
									<input type="text" name="webcomic_date_format" value="<?php echo isset( $_POST[ "webcomic_date_format" ] ) ? $_POST[ "webcomic_date_format" ] : "Y-m-d"; ?>">
									<p><?php printf( __( "The <a href='%s' target='_blank'>format</a> to use for comparison when <b>Match post&hellip;</b> is <b>Date</b>.", "webcomic" ), "//php.net/manual/en/function.date.php" ) ?></p>
								</label>
								<label>
									<h3><?php _e( "Custom Field Key", "webcomic" ); ?></h3>
									<input type="text" name="webcomic_custom_key" value="<?php echo isset( $_POST[ "webcomic_custom_key" ] ) ? $_POST[ "webcomic_custom_key" ] : ""; ?>">
									<p><?php _e( "The key to use for comparison when <b>Match post&hellip;</b> is <b>Custom Field</b>.", "webcomic" ); ?></p>
								</label>
								<label>
									<h3><input type="checkbox" name="webcomic_exact_match" value="1"<?php checked( isset( $_POST[ "webcomic_exact_match" ] ) ); ?>?> <?php _e( "Post and media values must match exactly", "webcomic" ); ?></h3>
								</label>
								<?php submit_button( __( "Find Matches", "webcomic" ) ); ?>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Render the webcomic generator.
	 * 
	 * @uses Webcomic::$config
	 * @uses Webcomic::get_attachments()
	 */
	public function page_generator() {
		global $post;
		
		$attachments = self::get_attachments();
		$columns = '
			<tr>
				<th class="check-column"><input type="checkbox"></th>
				<th class="column-icon"></th>
				<th>' . __( 'File', "webcomic" ) . '</th>
			</tr>';
		?>
		<div class="wrap">
			<div class="icon32" id="icon-upload"></div>
			<h2><?php echo get_admin_page_title(); ?></h2>
			<form method="post" class="webcomic-generator" data-webcomic-daycheck="<?php esc_attr_e( 'The start date is not one of the selected publish days. Continue anyway?', "webcomic" ); ?>">
				<?php wp_nonce_field( 'webcomic_generate', 'webcomic_generate' ); ?>
				<div id="col-container">
					<div id="col-right">
						<div class="col-wrap">
							<table class="wp-list-table widefat fixed posts">
								<thead><?php echo $columns; ?></thead>
								<tfoot><?php echo $columns; ?></tfoot>
								<tbody>
									<?php $i = 0; if ( $attachments ) { foreach ( $attachments as $attachment ) { $post = $attachment; ?>
									<tr<?php echo $i % 2 ? '' : ' class="alternate"'; ?>>
										<th class="check-column"><input type="checkbox" name="attachments[]" id="attachment-<?php echo $attachment->ID; ?>" value="<?php echo $attachment->ID; ?>"></th>
										<td class="media-icon"><label for="attachment-<?php echo $attachment->ID; ?>"><?php echo wp_get_attachment_image( $attachment->ID, array( 60, 60 ) ); ?></label></td>
										<td>
											<a href="<?php echo esc_url( add_query_arg( array( 'post' => $attachment->ID, 'action' => 'edit' ), admin_url( 'post.php' ) ) ); ?>"><span class="row-title"><?php echo get_the_title( $attachment->ID ); ?></span></a><b><?php _media_states( $attachment ); ?></b>
											<p><?php echo ( preg_match( '/^.*?\.(\w+)$/', get_attached_file( $attachment->ID ), $matches ) ) ? esc_html( strtoupper( $matches[ 1 ] ) ) : strtoupper( str_replace( 'image/', '', get_post_mime_type( $attachment->ID ) ) ); ?></p>
										</td>
									</tr>
									<?php $i++; } } else { ?>
									<tr class="no-items">
										<td colspan="3"><?php _e( 'No unattached media found.', "webcomic" ); ?></td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
					<div id="col-left">
						<div class="col-wrap">
							<div class="form-wrap">
								<label>
									<h3><?php _e( 'Collection', "webcomic" ); ?></h3>
									<select name="webcomic_collection">
									<?php
										foreach ( self::$config[ 'collections' ] as $k => $v ) {
											echo '<option value="', $k, '">', esc_html( $v[ 'name' ] ), '</option>';
										}
									?>
									</select>
								</label>
								<label>
									<h3><?php _e( 'Start on&hellip;', "webcomic" ); ?></h3>
									<input type="date" name="webcomic_generate_start" value="<?php echo date( 'Y-m-d' ); ?>">
								</label>
								<h3><?php _e( 'Publish every&hellip;', "webcomic" ); ?></h3>
								<label><input type="checkbox" name="webcomic_generate_days[]" value="1"> <?php _e( 'Monday', "webcomic" ); ?></label>
								<label><input type="checkbox" name="webcomic_generate_days[]" value="2"> <?php _e( 'Tuesday', "webcomic" ); ?></label>
								<label><input type="checkbox" name="webcomic_generate_days[]" value="3"> <?php _e( 'Wednesday', "webcomic" ); ?></label>
								<label><input type="checkbox" name="webcomic_generate_days[]" value="4"> <?php _e( 'Thursday', "webcomic" ); ?></label>
								<label><input type="checkbox" name="webcomic_generate_days[]" value="5"> <?php _e( 'Friday', "webcomic" ); ?></label>
								<label><input type="checkbox" name="webcomic_generate_days[]" value="6"> <?php _e( 'Saturday', "webcomic" ); ?></label>
								<label><input type="checkbox" name="webcomic_generate_days[]" value="7"> <?php _e( 'Sunday', "webcomic" ); ?></label>
								<label><h3><input name="webcomic_generate_draft" type="checkbox"> <?php _e( 'Save posts as drafts', "webcomic" ); ?></h3></label>
								<?php submit_button( __( 'Generate Webcomics', "webcomic" ) ); ?>
							</div>
						</div>
					</div>
				</div>
				<input type="hidden" name="webcomic_action" value="generate">
			</form>
		</div>
		<?php
	}
	
	/**
	 * Save new media order.
	 * 
	 * @param array $ids Array of attachment ID's to update.
	 */
	public static function ajax_sort_media( $ids ) {
		$i = 0;
		
		foreach ( $ids as $id ) {
			wp_update_post( array( 'ID' => $id[ 'value' ], 'menu_order' => $i ) );
			
			$i++;
		}
		
		_e( 'Display order updated.', "webcomic" );
	}
}
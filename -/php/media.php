<?php
/** Contains the WebcomicMedia class.
 * 
 * @package Webcomic
 */

/** Handle media-related tasks.
 * 
 * @package Webcomic
 */
class WebcomicMedia extends Webcomic {
	/** Register hooks.
	 * 
	 * @uses Webcomic::$error
	 * @uses Webcomic::$notice
	 * @uses WebcomicMedia::admin_init()
	 * @uses WebcomicMedia::admin_menu()
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
		add_action( 'delete_attachment', array( $this, 'delete_attachment' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'media_upload_webcomic_media', array( $this, 'media_upload_webcomic_media' ) );
		add_action( 'attachment_submitbox_misc_actions', array( $this, 'attachment_submitbox_misc_actions' ), 20 );
		
		add_filter( 'media_upload_tabs', array( $this, 'media_upload_tabs' ), 10, 1 );
		add_filter( 'media_row_actions',  array( $this, 'media_row_actions' ), 10, 2 );
		add_filter( 'display_media_states', array( $this, 'display_media_states' ), 10, 1 );
		add_filter( 'image_size_names_choose', array( $this, 'image_size_names_choose' ), 10, 1 );
	}
	
	/** Handle media functions.
	 * 
	 * @uses Webcomic::$error
	 * @uses Webcomic::$notice
	 * @uses Webcomic::$config
	 * @hook admin_init
	 */
	public function admin_init() {
		if ( isset( $_GET[ 'webcomic_action' ], $_GET[ 'post' ] ) and 'regenerate' === $_GET[ 'webcomic_action' ] and $meta = wp_get_attachment_metadata( $_GET[ 'post' ] ) ) {
			check_admin_referer( 'webcomic_regenerate' );
			
			$dir = wp_upload_dir();
			
			foreach ( get_intermediate_image_sizes() as $size ) {
				if ( $file = image_get_intermediate_size( $_GET[ 'post' ], $size ) ) {
					@unlink( path_join( $dir[ 'basedir' ], $file[ 'path' ] ) );
				}
			}
			
			if ( is_wp_error( $meta = wp_generate_attachment_metadata( $_GET[ 'post' ], get_attached_file( $_GET[ 'post' ] ) ) ) ) {
				self::$error[] = __( 'Images could not be regenerated.', 'webcomic' );
			} else {
				wp_update_attachment_metadata( $_GET[ 'post' ], $meta );
				
				self::$notice[] = __( 'Regenerated images for media.', 'webcomic' );
			}
		}
		
		if ( isset( $_GET[ 'webcomic_action' ], $_GET[ 'post' ] ) and 'detach' === $_GET[ 'webcomic_action' ] and intval( $_GET[ 'post' ] ) ) {
			check_admin_referer( 'webcomic_detach' );
			
			if ( wp_update_post( array( 'ID' => ( integer ) $_GET[ 'post' ], 'post_type' => 'attachment', 'post_parent' => 0, 'menu_order' => 0 ) ) ) {
				self::$notice[] = __( 'Detached media.', 'webcomic' );
			} else {
				self::$error[] = __( 'Media could not be detached.', 'webcomic' );
			}
		}
		
		if ( isset( $_POST[ 'webcomic_action' ] ) and 'attach' === $_POST[ 'webcomic_action' ] and wp_verify_nonce( $_POST[ 'webcomic_attach' ], 'webcomic_attach' ) ) {
			if ( empty( $_POST[ 'matches' ] ) ) {
				self::$error[] = __( 'No matches selected.', 'webcomic' );
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
					self::$error[] = sprintf( _n( 'A media could not be attached to %d post.', 'Media could not be attached to %d posts.', $bad, 'webcomic' ), $bad );
				}
				
				self::$notice[] = sprintf( _n( 'Media attached to %d post.', 'Media attached to %d posts.', $good, 'webcomic' ), $good );
			}
		}
		
		if ( isset( $_POST[ 'webcomic_action' ] ) and 'generate' === $_POST[ 'webcomic_action' ] and wp_verify_nonce( $_POST[ 'webcomic_generate' ], 'webcomic_generate' ) ) {
			if ( empty( $_POST[ 'attachments' ] ) or !$attachments = $_POST[ 'attachments' ] ) {
				self::$error[] = sprintf( '<strong>%s</strong>', __( 'No media selected.', 'webcomic' ) );
			} elseif ( !$start = strtotime( $_POST[ 'webcomic_generate_start' ] ) ) {
				self::$error[] = sprintf( '<strong>%s</strong>', __( 'The start date could not be understood.', 'webcomic' ) );
			} elseif ( !$days = $_POST[ 'webcomic_generate_days' ] ) {
				self::$error[] = sprintf( '<strong>%s</strong>', __( 'No weekdays selected.', 'webcomic' ) );
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
					self::$error[] = sprintf( _n( 'A post could not be generated for %d media.', 'Posts could not be generated for %d media.', $bad, 'webcomic' ), $bad );
				}
				
				self::$notice[] = sprintf( _n( 'Post created for %d media.', 'Posts created for %d media.', $good, 'webcomic' ), $good );
			}
		}
	}
	
	/** Register Media submenu pages.
	 * 
	 * @uses WebcomicMedia::generator()
	 * @hook admin_menu
	 */
	public function admin_menu() {
		add_submenu_page( 'upload.php', __( 'Webcomic Attacher', 'webcomic' ), __( 'Webcomic Attacher', 'webcomic' ), 'upload_files', 'webcomic-attacher', array( $this, 'page_attacher' ) );
		add_submenu_page( 'upload.php', __( 'Webcomic Generator', 'webcomic' ), __( 'Webcomic Generator', 'webcomic' ), 'upload_files', 'webcomic-generator', array( $this, 'page_generator' ) );
	}
	
	/** Manage webcomic media from the modal media manager.
	 * 
	 * @hook media_upload_webcomic_media
	 */
	public function media_upload_webcomic_media( $output = false ) {
		if ( $output ) {
			if ( $attachments = self::get_attachments( $_GET[ 'post_id' ] ) ) {
				printf( '<div data-webcomic-modal-media="%s"><p>%s</p><hr></div><ul class="webcomic-media">', admin_url(), __( 'Drag and drop the media attachments to change the order Webcomic will display them.', 'webcomic' ) );
				
				foreach ( $attachments as $attachment ) {
					printf( '<li><span>%s</span><a href="%s" title="%s">%s</a><a href="%s" title="%s">%s</a><input type="hidden" name="ids[]" value="%s"></li>',
						wp_get_attachment_image( $attachment->ID ),
						esc_html( wp_nonce_url( add_query_arg( array_merge( $_GET, array( 'post' => $attachment->ID, 'action' => 'edit', 'webcomic_action' => 'regenerate' ) ), admin_url( 'media-upload.php' ) ), 'webcomic_regenerate' ) ),
						__( 'Regenerate', 'webcomic' ),
						__( 'Regenerate', 'webcomic' ),
						esc_html( wp_nonce_url( add_query_arg( array_merge( $_GET, array( 'post' => $attachment->ID, 'action' => 'edit', 'webcomic_action' => 'detach' ) ), admin_url( 'media-upload.php' ) ), 'webcomic_detach' ) ),
						__( 'Detach', 'webcomic' ),
						__( 'Detach', 'webcomic' ),
						$attachment->ID
					);
				}
			} else {
				printf( '<div><p>%s</p></div>', __( 'To manage Webcomic Media please attach one or more images to this webcomic.', 'webcomic' ) );
			}
		} else {
			wp_iframe( array( $this, 'media_upload_webcomic_media' ), true );
		}
	}
	
	/** Unset term attachment info when a media object is deleted.
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
	
	/** Register and enqueue media scripts.
	 * 
	 * @hook admin_enqueue_scripts
	 */
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		
		if ( 'media_page_webcomic-generator' === $screen->id or ( isset( $_GET[ 'tab' ] ) and 'webcomic_media' === $_GET[ 'tab' ] ) ) {
			wp_register_script( 'webcomic-admin-media', self::$url . '-/js/admin-media.js', array( 'jquery-ui-sortable' ) );
			
			wp_enqueue_script( 'webcomic-admin-media' );
		}
		
		if ( isset( $_GET[ 'tab' ] ) and 'webcomic_media' === $_GET[ 'tab' ] ) {
			wp_register_style( 'webcomic-admin-media', self::$url . '-/css/admin-media.css' );
			
			wp_enqueue_style( 'webcomic-admin-media' );
		}
	}
	
	/** Display attachment and alternate size details on Edit Media page.
	 * 
	 * @hook attachment_submitbox_misc_actions
	 */
	public function attachment_submitbox_misc_actions() {
		global $post;
		
		if ( $post->post_parent ) {
			printf( '
				<div class="misc-pub-section">
					%s <strong>%s</strong><br><a href="%s" class="button">%s</a>
				</div>',
				__( 'Attached to:', 'webcomic' ),
				sprintf( '%s, %s',
					( current_user_can( 'edit_post', $post->post_parent ) and 'trash' !== get_post_status( $post->post_parent ) ) ? sprintf( '<a href="%s">%s</a>', get_edit_post_link( $post->post_parent ), esc_html( get_the_title( $post->post_parent ) ) ) : esc_html( get_the_title( $post->post_parent ) ),
					get_the_time( 'Y/m/d', $post->post_parent )
				),
				esc_html( wp_nonce_url( add_query_arg( array( 'post' => $post->ID, 'action' => 'edit', 'webcomic_action' => 'detach' ), admin_url( 'post.php' ) ), 'webcomic_detach' ) ),
				__( 'Detach', 'webcomic' )
			);
		}
		
		if ( 0 === strpos( $post->post_mime_type, 'image' ) ) {
			$sizes = array();
			
			foreach ( get_intermediate_image_sizes() as $size ) {
				if ( $file = image_get_intermediate_size( $post->ID, $size ) ) {
					$sizes[ $file[ 'width' ] * $file[ 'height' ] ] = sprintf( '%s &ndash; %s &times; %s', $size, $file[ 'width' ], $file[ 'height' ] );
				}
			}
			
			krsort( $sizes );
			
			printf( '
				<div class="misc-pub-section">
					%s<br><strong>%s</strong><br><a href="%s" class="button">%s</a>
				</div>',
				__( 'Alternate Sizes:', 'webcomic' ),
				$sizes ? join( '<br>', $sizes ) : __( 'None available', 'webcomic' ),
				esc_html( wp_nonce_url( add_query_arg( array( 'post' => $post->ID, 'action' => 'edit', 'webcomic_action' => 'regenerate' ), admin_url( 'post.php' ) ), 'webcomic_regenerate' ) ),
				__( 'Regenerate', 'webcomic' )
			);
		}
	}
	
	/** Add 'Webcomic Media' modal media page.
	 * 
	 * @return array
	 * @hook media_upload_tabs
	 */
	public function media_upload_tabs( $tabs ) {
		global $post;
		
		$post_type = '';
		
		if ( $post ) {
			$post_type = $post->post_type;
		} elseif ( isset( $_GET[ 'post_id' ], $_GET[ 'tab' ] ) and 'webcomic_media' === $_GET[ 'tab' ] ) {
			$post_type = get_post_type( $_GET[ 'post_id' ] );
		}
		
		if ( $post_type and preg_match( '/^webcomic\d+$/', $post_type ) ) {
			$tabs[ 'webcomic_media' ] = __( 'Webcomic Media', 'webcomic' );
		}
		
		return $tabs;
	}
	
	/** Add regenerate and detach options to media table.
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
			$actions[ 'webcomic_regen' ] = sprintf( '<a href="%s">%s</a>',
				esc_html( wp_nonce_url( add_query_arg( array( 'post' => $attachment->ID, 'webcomic_action' => 'regenerate' ), admin_url( 'upload.php' ) ), 'webcomic_regenerate' ) ),
				__( 'Regenerate', 'webcomic' )
			);
		}
		
		if ( $attachment->post_parent ) {
			$actions[ 'webcomic_detach' ] = sprintf( '<a href="%s">%s</a>',
				esc_html( wp_nonce_url( add_query_arg( array( 'post' => $attachment->ID, 'webcomic_action' => 'detach' ), admin_url( 'upload.php' ) ), 'webcomic_detach' ) ),
				__( 'Detach', 'webcomic' )
			);
		}
		
		return $actions;
	}
	
	/** Display relevant status for webcomic media.
	 * 
	 * @param array $states List of media states.
	 * @return array
	 * @hook display_media_states
	 */
	public function display_media_states( $states ) {
		global $post;
		
		if ( $type = get_post_meta( $post->ID, '_wp_attachment_context', true ) and preg_match( '/^webcomic\d+(_(storyline|character))?$/', $type ) ) {
			$type = explode( '_', $type );
			
			if ( empty( $type[ 1 ] ) ) {
				$states[] = sprintf( __( '%s Poster', 'webcomic' ), esc_html( self::$config[ 'collections' ][ $type[ 0 ] ][ 'name' ] ) );
			} elseif ( 'storyline' === $type[ 1 ] ) {
				$states[] = sprintf( __( '%s Cover', 'webcomic' ), esc_html( self::$config[ 'collections' ][ $type[ 0 ] ][ 'name' ] ) );
			} elseif ( 'character' === $type[ 1 ] ) {
				$states[] = sprintf( __( '%s Avatar', 'webcomic' ), esc_html( self::$config[ 'collections' ][ $type[ 0 ] ][ 'name' ] ) );
			}
		}
		
		return $states;
	}
	
	/** Add additional image sizes for inserting into posts.
	 * 
	 * @uses Webcomic::$config
	 */
	public function image_size_names_choose( $sizes ) {
		foreach ( self::$config[ 'sizes' ] as $k => $v ) {
			$sizes[ $k ] = ucwords( str_replace( '-', ' ', $k ) );
		}
		
		return $sizes;
	}
	
	/** Render the webcomic attacher.
	 * 
	 * @uses Webcomic::$config
	 */
	public function page_attacher() {
		global $wpdb, $post;
		?>
		<div class="wrap">
			<div class="icon32" id="icon-upload"></div>
			<h2><?php _e( 'Webcomic Attacher', 'webcomic' ); ?></h2>
			<div id="col-container">
				<div id="col-right">
					<div class="col-wrap">
					<?php
						if ( $_POST ) {
							$matches = $dupe = array();
							
							$columns = sprintf( '
								<tr>
									<th class="check-column"><input type="checkbox"></th>
									<th class="column-icon"></th>
									<th>%s</th>
									<th>%s</th>
								</tr>',
								__( 'File', 'webcomic' ),
								__( 'Matched With', 'webcomic' )
							);
							
							$attachments = get_posts( array(
								'post_type'      => 'attachment',
								'post_status'    => 'inherit',
								'post_mime_type' => 'image',
								'post_parent'    => 0,
								'numberposts'    => -1
							) );
							
							$posts = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->posts} WHERE post_type = %s AND post_status NOT IN ( 'auto-draft', 'private', 'inherit', 'trash' ) AND ID NOT IN ( SELECT post_parent FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%%' )", $_POST[ 'webcomic_collection' ] ) );
							
							if ( $attachments and $posts ) {
								foreach ( $posts as $post ) {
									$post_field = '';
									
									if ( 'post_title' === $_POST[ 'webcomic_post_field' ] ) {
										$post_field = $post->post_title;
									} elseif ( 'post_name' === $_POST[ 'webcomic_post_field' ] ) {
										$post_field = $post->post_name;
									} elseif ( 'post_date' === $_POST[ 'webcomic_post_field' ] ) {
										$post_field = mysql2date( $_POST[ 'webcomic_date_format' ], $post->post_date );
									} elseif ( 'post_custom' === $_POST[ 'webcomic_post_field' ] ) {
										$post_field = get_post_meta( $post->ID, $_POST[ 'webcomic_custom_key' ] );
									}
									
									foreach ( $attachments as $attachment ) {
										$attachment_field = '';
										
										if ( 'post_title' === $_POST[ 'webcomic_media_field' ] ) {
											$attachment_field = $attachment->post_title;
										} elseif ( 'post_name' === $_POST[ 'webcomic_media_field' ] ) {
											$attachment_field = $attachment->post_name;
										} elseif ( 'post_date' === $_POST[ 'webcomic_media_field' ] ) {
											$attachment_field = mysql2date( $_POST[ 'webcomic_date_format' ], $attachment->post_date );
										} elseif ( 'guid' === $_POST[ 'webcomic_media_field' ] ) {
											$attachment_field = pathinfo( $attachment->guid );
										}	
										
										if ( $post_field and $attachment_field and ( $post_field === $attachment_field or ( is_array( $attachment_field ) and $post_field === $attachment_field[ 'filename' ] ) ) ) {
											$matches[] = array( 'media' => $attachment, 'post' => $post );
										}
									}
								}
							}
					?>
						<form method="post">
							<?php wp_nonce_field( 'webcomic_attach', 'webcomic_attach' ); ?>
							<table class="wp-list-table widefat fixed posts">
								<thead><?php echo $columns; ?></thead>
								<tfoot><?php echo $columns; ?></tfoot>
								<tbody>
									<?php $i = 0; if ( $matches ) { foreach ( $matches as $match ) { $post = $match[ 'media' ]; ?>
									<tr<?php echo ( isset( $dupe[ $match[ 'post' ]->ID ] ) or isset( $dupe[ $match[ 'media' ]->ID ] ) ) ? ' style="background:#ffebe8"' : ''; echo $i % 2 ? '' : ' class="alternate"'; ?>>
										<th class="check-column"><input type="checkbox" name="matches[]" id="match-<?php echo $i; ?>" value="<?php echo $match[ 'media' ]->ID, '|', $match[ 'post' ]->ID; ?>"<?php echo ( isset( $dupe[ $match[ 'post' ]->ID ] ) or isset( $dupe[ $match[ 'media' ]->ID ] ) ) ? '' : ' checked'; ?>></th>
										<td class="media-icon"><label for="match-<?php echo $i; ?>"><?php echo wp_get_attachment_image( $match[ 'media' ]->ID, array( 60, 60 ) ); ?></label></td>
										<td>
											<a href="<?php echo esc_url( add_query_arg( array( 'post' => $match[ 'media' ]->ID, 'action' => 'edit' ), admin_url( 'post.php' ) ) ); ?>"><span class="row-title"><?php echo get_the_title( $match[ 'media' ]->ID ); ?></span></a><b><?php _media_states( $match[ 'media' ] ); ?></b>
											<p><?php echo ( preg_match( '/^.*?\.(\w+)$/', get_attached_file( $match[ 'media' ]->ID ), $m ) ) ? esc_html( strtoupper( $m[ 1 ] ) ) : strtoupper( str_replace( 'image/', '', get_post_mime_type( $match[ 'media' ]->ID ) ) ); ?></p>
										</td>
										<td>
											<?php
												$post = $match[ 'post' ];
												
												printf( '<strong>%s</strong><p>%s</p>',
													( current_user_can( 'edit_post', $post->post_parent ) and 'trash' !== get_post_status( $post->post_parent ) ) ? sprintf( '<a href="%s">%s</a>', get_edit_post_link( $post->post_parent ), esc_html( get_the_title( $post->post_parent ) ) ) : esc_html( get_the_title( $post->post_parent ) ),
													get_the_time( 'Y/m/d', $post->post_parent )
												);
											?>
										</td>
									</tr>
									<?php $i++; $dupe[ $match[ 'post' ]->ID ] = $dupe[ $match[ 'media' ]->ID ] = true; } } else { ?>
									<tr class="no-items">
										<td colspan="4">
											<?php
												if ( !$attachments or is_wp_error( $attachments ) ) {
													_e( 'No unattached media could be found.', 'webcomic' );
												} else if ( !$posts ) {
													_e( 'No orphaned webcomic posts could be found.', 'webcomic' );
												} else {
													_e( 'No matches could be found.', 'webcomic' );
												}
											?>
										</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
							<?php
								if ( $matches ) {
									submit_button( __( 'Attach Media', 'webcomic' ) );
								}
							?>
							<input type="hidden" name="webcomic_collection" value="<?php echo $_POST[ 'webcomic_collection' ]; ?>">
							<input type="hidden" name="webcomic_post_field" value="<?php echo $_POST[ 'webcomic_post_field' ]; ?>">
							<input type="hidden" name="webcomic_media_field" value="<?php echo $_POST[ 'webcomic_media_field' ]; ?>">
							<input type="hidden" name="webcomic_date_format" value="<?php echo $_POST[ 'webcomic_date_format' ]; ?>">
							<input type="hidden" name="webcomic_custom_key" value="<?php echo $_POST[ 'webcomic_custom_key' ]; ?>">
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
									<h3><?php _e( 'Collection', 'webcomic' ); ?></h3>
									<select name="webcomic_collection">
									<?php
										foreach ( self::$config[ 'collections' ] as $k => $v ) {
											printf( '<option value="%s"%s>%s</option>',
												$k,
												selected( $k, empty( $_POST[ 'webcomic_collection' ] ) ? '' : $_POST[ 'webcomic_collection' ] ),
												esc_html( $v[ 'name' ] )
											);
										}
									?>
									</select>
								</label>
								<label>
									<h3><?php _e( 'Match post&hellip;', 'webcomic' ); ?></h3>
									<select name="webcomic_post_field">
										<option value="post_title"<?php selected( 'post_title', empty( $_POST[ 'webcomic_post_field' ] ) ? '' : $_POST[ 'webcomic_post_field' ] ); ?>><?php _e( 'Title', 'webcomic' ); ?></option>
										<option value="post_name"<?php selected( 'post_name', empty( $_POST[ 'webcomic_post_field' ] ) ? '' : $_POST[ 'webcomic_post_field' ] ); ?>><?php _e( 'Slug', 'webcomic' ); ?></option>
										<option value="post_date"<?php selected( 'post_date', empty( $_POST[ 'webcomic_post_field' ] ) ? '' : $_POST[ 'webcomic_post_field' ] ); ?>><?php _e( 'Date', 'webcomic' ); ?></option>
										<option value="post_custom"<?php selected( 'post_custom', empty( $_POST[ 'webcomic_post_field' ] ) ? '' : $_POST[ 'webcomic_post_field' ] ); ?>><?php _e( 'Custom Field', 'webcomic' ); ?></option>
									</select>
								</label>
								<label>
									<h3><?php _e( 'With media&hellip;', 'webcomic' ); ?></h3>
									<select name="webcomic_media_field">
										<option value="guid"<?php selected( 'guid', empty( $_POST[ 'webcomic_media_field' ] ) ? '' : $_POST[ 'webcomic_media_field' ] ); ?>><?php _e( 'Filename', 'webcomic' ); ?></option>
										<option value="post_title"<?php selected( 'post_title', empty( $_POST[ 'webcomic_media_field' ] ) ? '' : $_POST[ 'webcomic_media_field' ] ); ?>><?php _e( 'Title', 'webcomic' ); ?></option>
										<option value="post_name"<?php selected( 'post_name', empty( $_POST[ 'webcomic_media_field' ] ) ? '' : $_POST[ 'webcomic_media_field' ] ); ?>><?php _e( 'Slug', 'webcomic' ); ?></option>
										<option value="post_date"<?php selected( 'post_date', empty( $_POST[ 'webcomic_media_field' ] ) ? '' : $_POST[ 'webcomic_media_field' ] ); ?>><?php _e( 'Date', 'webcomic' ); ?></option>
									</select>
								</label>
								<label>
									<h3><?php _e( 'Date Format', 'webcomic' ); ?></h3>
									<input type="text" name="webcomic_date_format" value="<?php echo isset( $_POST[ 'webcomic_date_format' ] ) ? $_POST[ 'webcomic_date_format' ] : 'Y-m-d'; ?>">
									<p><?php printf( __( 'The <a href="%s" target="_blank">format</a> to use for comparison when <b>Match post&hellip;</b> is <b>Date</b>.', 'webcomic' ), '//php.net/manual/en/function.date.php' ) ?></p>
								</label>
								<label>
									<h3><?php _e( 'Custom Field Key', 'webcomic' ); ?></h3>
									<input type="text" name="webcomic_custom_key" value="<?php echo isset( $_POST[ 'webcomic_custom_key' ] ) ? $_POST[ 'webcomic_custom_key' ] : ''; ?>">
									<p><?php _e( 'The key to use for comparison when <b>Match post&hellip;</b> is <b>Custom Field</b>.', 'webcomic' ); ?></p>
								</label>
								<?php submit_button( __( 'Find Matches', 'webcomic' ) ); ?>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<?php
	}
	
	/** Render the webcomic generator.
	 * 
	 * @uses Webcomic::$config
	 * @uses Webcomic::get_attachments()
	 */
	public function page_generator() {
		global $post;
		
		$attachments = self::get_attachments();
		$columns = sprintf( '
			<tr>
				<th class="check-column"><input type="checkbox"></th>
				<th class="column-icon"></th>
				<th>%s</th>
			</tr>',
			__( 'File', 'webcomic' )
		);
		?>
		<div class="wrap">
			<div class="icon32" id="icon-upload"></div>
			<h2><?php _e( 'Webcomic Generator', 'webcomic' ); ?></h2>
			<form method="post" class="webcomic-generator" data-webcomic-daycheck="<?php esc_attr_e( 'The start date is not one of the selected publish days. Continue anyway?', 'webcomic' ); ?>">
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
										<td colspan="3"><?php _e( 'No unattached media found.', 'webcomic' ); ?></td>
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
									<h3><?php _e( 'Collection', 'webcomic' ); ?></h3>
									<select name="webcomic_collection">
									<?php
										foreach ( self::$config[ 'collections' ] as $k => $v ) {
											printf( '<option value="%s">%s</option>', $k, esc_html( $v[ 'name' ] ) );
										}
									?>
									</select>
								</label>
								<label>
									<h3><?php _e( 'Start on&hellip;', 'webcomic' ); ?></h3>
									<input type="date" name="webcomic_generate_start" value="<?php echo date( 'Y-m-d' ); ?>">
								</label>
								<h3><?php _e( 'Publish every&hellip;', 'webcomic' ); ?></h3>
								<label><input type="checkbox" name="webcomic_generate_days[]" value="1"> <?php _e( 'Monday', 'webcomic' ); ?></label>
								<label><input type="checkbox" name="webcomic_generate_days[]" value="2"> <?php _e( 'Tuesday', 'webcomic' ); ?></label>
								<label><input type="checkbox" name="webcomic_generate_days[]" value="3"> <?php _e( 'Wednesday', 'webcomic' ); ?></label>
								<label><input type="checkbox" name="webcomic_generate_days[]" value="4"> <?php _e( 'Thursday', 'webcomic' ); ?></label>
								<label><input type="checkbox" name="webcomic_generate_days[]" value="5"> <?php _e( 'Friday', 'webcomic' ); ?></label>
								<label><input type="checkbox" name="webcomic_generate_days[]" value="6"> <?php _e( 'Saturday', 'webcomic' ); ?></label>
								<label><input type="checkbox" name="webcomic_generate_days[]" value="7"> <?php _e( 'Sunday', 'webcomic' ); ?></label>
								<label><h3><input name="webcomic_generate_draft" type="checkbox"> <?php _e( 'Save posts as drafts', 'webcomic' ); ?></h3></label>
								<?php submit_button( __( 'Generate Webcomics', 'webcomic' ) ); ?>
							</div>
						</div>
					</div>
				</div>
				<input type="hidden" name="webcomic_action" value="generate">
			</form>
		</div>
		<?php
	}
	
	/** Save new media order.
	 * 
	 * @param array $ids Array of attachment ID's to update.
	 */
	public static function ajax_order_media( $ids ) {
		$i = 0;
		
		foreach ( $ids as $id ) {
			wp_update_post( array( 'ID' => $id[ 'value' ], 'menu_order' => $i ) );
			
			$i++;
		}
		
		_e( 'Display order updated.', 'webcomic' );
	}
}
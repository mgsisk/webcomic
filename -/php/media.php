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
	 * @uses WebcomicMedia::admin_footer()
	 * @uses WebcomicMedia::delete_attachment()
	 * @uses WebcomicMedia::admin_enqueue_scripts()
	 * @uses WebcomicMedia::media_row_actions()
	 * @uses WebcomicMedia::display_media_states()
	 * @uses WebcomicMedia::attachment_fields_to_edit()
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_footer', array( $this, 'admin_footer' ) );
		add_action( 'delete_attachment', array( $this, 'delete_attachment' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		
		add_filter( 'media_row_actions',  array( $this, 'media_row_actions' ), 10, 2 );
		add_filter( 'display_media_states', array( $this, 'display_media_states' ), 10, 1 );
		add_filter( 'attachment_fields_to_edit', array( $this, 'attachment_fields_to_edit' ), 10, 2 );
		
		if ( isset( $_GET[ 'webcomic_action_status' ] ) and 1 === count( $_GET ) ) {
			if ( 'regenerate-clear' === $_GET[ 'webcomic_action_status' ] ) {
				self::$notice[] = __( 'Regenerated images for media object.', 'webcomic' );
			} else if ( 'regenerate-error' === $_GET[ 'webcomic_action_status' ] ) {
				self::$error[] = __( 'Images could not be regenerated.', 'webcomic' );
			} else if ( 'detach-clear' === $_GET[ 'webcomic_action_status' ] ) {
				self::$notice[] = __( 'Detached media object.', 'webcomic' );
			}
		}
	}
	
	/** Handle media detaching and generation.
	 * 
	 * @uses Webcomic::$error
	 * @uses Webcomic::$notice
	 * @uses Webcomic::$config
	 * @hook admin_init
	 */
	public function admin_init() {
		if ( isset( $_GET[ 'webcomic_action' ], $_GET[ 'attachment_id' ] ) and 'regenerate' === $_GET[ 'webcomic_action' ] and $meta = wp_get_attachment_metadata( $_GET[ 'attachment_id' ] ) ) {
			check_admin_referer( 'webcomic_regenerate' );
			
			$dir      = wp_upload_dir();
			$redirect = remove_query_arg( ( empty( $_GET[ 'action' ] ) and empty( $_GET[ 'tab' ] ) ) ? array_keys( $_GET ) : array( 'webcomic_action', '_wpnonce' ), stripslashes( $_SERVER[ 'REQUEST_URI' ] ) );
			
			foreach ( get_intermediate_image_sizes() as $size ) {
				if ( $file = image_get_intermediate_size( $_GET[ 'attachment_id' ], $size ) ) {
					@unlink( path_join( $dir[ 'basedir' ], $file[ 'path' ] ) );
				}
			}
			
			if ( is_wp_error( $meta = wp_generate_attachment_metadata( $_GET[ 'attachment_id' ], get_attached_file( $_GET[ 'attachment_id' ] ) ) ) ) {
				$redirect = add_query_arg( array( 'webcomic_action_status' => 'regenerate-error' ), $redirect );
			} else {
				wp_update_attachment_metadata( $_GET[ 'attachment_id' ], $meta );
				
				$redirect = add_query_arg( array( 'webcomic_action_status' => 'regenerate-clear' ), $redirect );
			}
			
			wp_redirect( $redirect );
			
			die;
		}
		
		if ( isset( $_GET[ 'webcomic_action' ], $_GET[ 'attachment_id' ] ) and 'detach' === $_GET[ 'webcomic_action' ] and intval( $_GET[ 'attachment_id' ] ) ) {
			check_admin_referer( 'webcomic_detach' );
			
			$redirect = remove_query_arg( ( empty( $_GET[ 'action' ] ) and empty( $_GET[ 'tab' ] ) ) ? array_keys( $_GET ) : array( 'webcomic_action', '_wpnonce' ), stripslashes( $_SERVER[ 'REQUEST_URI' ] ) );
			
			wp_update_post( array( 'ID' => ( integer ) $_GET[ 'attachment_id' ], 'post_type' => 'attachment', 'post_parent' => 0, 'menu_order' => 0 ) );
			
			wp_redirect( add_query_arg( array( 'webcomic_action_status' => 'detach-clear' ), $redirect ) );
			
			die;
		}
		
		if ( isset( $_POST[ 'webcomic_action' ] ) and 'generate' === $_POST[ 'webcomic_action' ] and wp_verify_nonce( $_POST[ 'webcomic_generate' ], 'webcomic_generate' ) ) {
			if ( empty( $_POST[ 'attachments' ] ) or !$attachments = $_POST[ 'attachments' ] ) {
				self::$error[] = sprintf( '<strong>%s</strong>', __( 'No media objects selected.', 'webcomic' ) );
			} else if ( !$start = strtotime( $_POST[ 'webcomic_generate_start' ] ) ) {
				self::$error[] = sprintf( '<strong>%s</strong>', __( 'The start date could not be understood.', 'webcomic' ) );
			} else if ( !$days = $_POST[ 'webcomic_generate_days' ] ) {
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
					$date       = current_time( 'mysql' );
					$attachment = get_post( $id );
					
					if ( $new_post = wp_insert_post( array(
						'post_type'     => $_POST[ 'webcomic_collection' ],
						'post_date'     => $date,
						'post_title'    => $attachment->post_title,
						'post_status'   => $draft ? 'draft' : 'publish',
						'post_content'  => '&nbsp;',
						'post_date_gmt' => get_gmt_from_date( $date )
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
					} else if ( $weekday > max( $days ) or $weekday < min( $days ) ) {
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
					self::$error[] = sprintf( _n( 'A post could be generated for %d media object.', 'Posts could not be generated for %d media objects.', $bad, 'webcomic' ), $bad );
				}
				
				self::$notice[] = sprintf( _n( 'Post created for %d media object.', 'Posts created for %d media objects.', $good, 'webcomic' ), $good );
			}
		}
	}
	
	/** Register Media submenu pages.
	 * 
	 * @uses WebcomicMedia::generator()
	 * @hook admin_menu
	 */
	public function admin_menu() {
		add_submenu_page( 'upload.php', __( 'Webcomic Generator', 'webcomic' ), __( 'Webcomic Generator', 'webcomic' ), 'upload_files', 'webcomic-generator', array( $this, 'generator' ) );
	}
	
	/** Render javascript for the webcomic generator.
	 * 
	 * @hook admin_footer
	 */
	public function admin_footer() {
		$screen = get_current_screen();
		
		if ( 'media_page_webcomic-generator' === $screen->id ) {
			printf( "<script>webcomic_generator( '%s' );</script>", __( 'The start date is not one of the selected publish days. Continue anyway?', 'webcomic' ) );
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
				
				update_option( 'webcomic', self::$config );
				
				break;
			}
		}
		
		foreach ( array_keys( self::$config[ 'collections' ] ) as $k ) {
			if ( self::$config[ 'collections' ][ $k ][ 'image' ] === $id ) {
				self::$config[ 'collections' ][ $k ][ 'image' ] = 0;
				
				update_option( 'webcomic', self::$config );
				
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
		
		if ( 'media_page_webcomic-generator' === $screen->id ) {
			wp_register_script( 'webcomic-admin-media', self::$url . '-/js/admin-media.js', array( 'jquery' ) );
			
			wp_enqueue_script( 'webcomic-admin-media' );
		}
	}
	
	/** Add detach option to media table.
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
				esc_html( wp_nonce_url( add_query_arg( array( 'attachment_id' => $attachment->ID, 'webcomic_action' => 'regenerate' ), admin_url( 'upload.php' ) ), 'webcomic_regenerate' ) ),
				__( 'Regenerate', 'webcomic' )
			);
		}
		
		if ( $attachment->post_parent ) {
			$actions[ 'webcomic_detach' ] = sprintf( '<a href="%s">%s</a>',
				esc_html( wp_nonce_url( add_query_arg( array( 'attachment_id' => $attachment->ID, 'webcomic_action' => 'detach' ), admin_url( 'upload.php' ) ), 'webcomic_detach' ) ),
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
			} else if ( 'storyline' === $type[ 1 ] ) {
				$states[] = sprintf( __( '%s Cover', 'webcomic' ), esc_html( self::$config[ 'collections' ][ $type[ 0 ] ][ 'name' ] ) );
			} else if ( 'character' === $type[ 1 ] ) {
				$states[] = sprintf( __( '%s Avatar', 'webcomic' ), esc_html( self::$config[ 'collections' ][ $type[ 0 ] ][ 'name' ] ) );
			}
		}
		
		return $states;
	}
	
	/** Render detach and regenerate options for attached media.
	 * 
	 * These features are not Webcomic-specific; any attached media
	 * object will gain an Attached To field with a Detach option, and
	 * any image object will gain an Alternative Sizes field with a
	 * Regenerate option.
	 * 
	 * @param array $fields The array of media object fields.
	 * @param object $attachment The attachment being edited.
	 * @return array
	 * @hook attachment_fields_to_edit
	 */
	public function attachment_fields_to_edit( $fields, $attachment ) {
		global $post;
		
		if ( isset( $_GET[ 'post_id' ] ) ) {
			$id = $_GET[ 'post_id' ];
		} else if ( $post and $post->post_parent ) {
			$id = $post->post_parent;
		} else if ( $post and isset( self::$config[ 'collections' ][ $post->post_type ] ) ) {
			$id = $post->ID;
		} else {
			$id = false;
		}
		
		if ( $attachment->post_parent ) {
			$fields[ 'webcomic_detach' ] = array(
				'label' => __( 'Attached To', 'webcomic' ),
				'value' => $attachment->post_parent,
				'input' => 'html',
				'html'  => sprintf( '<strong>%s</strong>, %s | <a href="%s">%s</a>',
						( current_user_can( 'edit_post', $attachment->post_parent ) and 'trash' !== get_post_status( $attachment->post_parent ) ) ? sprintf( '<a href="%s">%s</a>', get_edit_post_link( $attachment->post_parent ), esc_html( get_the_title( $attachment->post_parent ) ) ) : esc_html( get_the_title( $attachment->post_parent ) ),
						get_the_time( 'Y/m/d', $attachment->post_parent ),
						esc_url( wp_nonce_url( add_query_arg( array( 'attachment_id' => $attachment->ID, 'webcomic_action' => 'detach' ), stripslashes( $_SERVER[ 'REQUEST_URI' ] ) ), 'webcomic_detach' ) ),
						__( 'Detach', 'webcomic' )
					)
			);
		}
		
		if ( 0 === strpos( $attachment->post_mime_type, 'image' ) ) {
			$sizes = array();
			
			foreach ( get_intermediate_image_sizes() as $size ) {
				if ( $file = image_get_intermediate_size( $attachment->ID, $size ) ) {
					$sizes[ $size ] = sprintf( '%s (%sx%s)', $size, $file[ 'width' ], $file[ 'height' ] );
				}
			}
			
			$fields[ 'webcomic_regenerate' ] = array(
				'label' => __( 'Alternate Sizes', 'webcomic' ),
				'value' => $attachment->post_mime_type,
				'input' => 'html',
				'html'  => sprintf( '%s<br><a href="%s">%s</a>',
					count( $sizes ) ? join( '<br>', $sizes ) : __( 'No alternate sizes available.', 'webcomic' ),
					esc_url( wp_nonce_url( add_query_arg( array( 'attachment_id' => $attachment->ID, 'webcomic_action' => 'regenerate' ), stripslashes( $_SERVER[ 'REQUEST_URI' ] ) ), 'webcomic_regenerate' ) ),
					__( 'Regenerate', 'webcomic' )
				)
			);
		}
		
		return $fields;
	}
	
	/** Render the webcomic generator.
	 * 
	 * @uses Webcomic::$config
	 * @uses Webcomic::get_attachments()
	 */
	public function generator() {
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
			<form method="post">
				<?php wp_nonce_field( 'webcomic_generate', 'webcomic_generate' ); ?>
				<div id="col-container">
					<div id="col-right">
						<table class="wp-list-table widefat fixed posts">
							<thead><?php echo $columns; ?></thead>
							<tfoot><?php echo $columns; ?></tfoot>
							<tbody>
								<?php $i = 0; if ( $attachments ) { foreach ( $attachments as $attachment ) { $post = $attachment; ?>
								<tr<?php echo $i % 2 ? '' : ' class="alternate"'; ?>>
									<th class="check-column"><input type="checkbox" name="attachments[]" id="attachment-<?php echo $attachment->ID; ?>" value="<?php echo $attachment->ID; ?>"></th>
									<td class="media-icon"><label for="attachment-<?php echo $attachment->ID; ?>"><?php echo wp_get_attachment_image( $attachment->ID, array( 60, 60 ) ); ?></label></td>
									<td>
										<a href="<?php echo esc_url( add_query_arg( array( 'attachment_id' => $attachment->ID, 'action' => 'edit' ), admin_url( 'media.php' ) ) ); ?>"><span class="row-title"><?php echo get_the_title( $attachment->ID ); ?></span></a><b><?php _media_states( $attachment ); ?></b>
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
}
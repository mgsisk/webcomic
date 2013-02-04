<?php
/** Contains the WebcomicPosts class.
 * 
 * @package Webcomic
 */

/** Handle post-related tasks.
 * 
 * @package Webcomic
 */
class WebcomicPosts extends Webcomic {
	/** Register hooks.
	 * 
	 * @uses Webcomic::$config
	 * @uses WebcomicPosts::add_meta_boxes()
	 * @uses WebcomicPosts::pre_post_update()
	 * @uses WebcomicPosts::update_collection()
	 * @uses WebcomicPosts::admin_enqueue_scripts()
	 * @uses WebcomicPosts::restrict_manage_posts()
	 * @uses WebcomicPosts::save_webcomic_commerce()
	 * @uses WebcomicPosts::wp_insert_post_data()
	 * @uses WebcomicPosts::save_webcomic_transcripts()
	 * @uses WebcomicPosts::bulk_edit_custom_box()
	 * @uses WebcomicPosts::quick_edit_custom_box()
	 * @uses WebcomicPosts::posts_where()
	 * @uses WebcomicPosts::manage_webcomic_posts_custom_column()
	 * @uses WebcomicPosts::view_edit_webcomics()
	 * @uses WebcomicPosts::manage_edit_webcomic_columns()
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'pre_post_update', array( $this, 'pre_post_update' ), 10, 1 );
		add_action( 'wp_insert_post', array( $this, 'update_collection' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts' ) );
		add_action( 'wp_insert_post', array( $this, 'save_webcomic_commerce' ), 10, 2 );
		add_action( 'wp_insert_post_data', array( $this, 'wp_insert_post_data' ), 10, 2 );
		add_action( 'wp_insert_post,', array( $this, 'save_webcomic_transcripts' ), 10, 2 );
		add_action( 'bulk_edit_custom_box', array( $this, 'bulk_edit_custom_box' ), 10, 2 );
		add_action( 'quick_edit_custom_box', array( $this, 'quick_edit_custom_box' ), 10, 2 );
		
		add_filter( 'posts_where', array( $this, 'posts_where' ), 10, 1 );
		
		foreach ( array_keys( self::$config[ 'collections' ] ) as $k ) {
			add_action( "manage_{$k}_posts_custom_column", array( $this, 'manage_webcomic_posts_custom_column' ), 10, 2 );
			
			add_filter( "views_edit-{$k}", array( $this, 'view_edit_webcomic' ), 10, 1 );
			add_filter( "manage_edit-{$k}_columns", array( $this, 'manage_edit_webcomic_columns' ), 10, 1 );
		}
	}
	
	/** Add webcomic meta boxes.
	 * 
	 * @uses Webcomic::$config
	 * @uses WebcomicPosts::media()
	 * @uses WebcomicPosts::commerce()
	 * @uses WebcomicPosts::transcripts()
	 * @hook add_meta_boxes
	 */
	public function add_meta_boxes() {
		foreach ( array_keys( self::$config[ 'collections' ] ) as $k ) {
			add_meta_box( 'webcomic-media', __( 'Webcomic Media', 'webcomic' ), array( $this, 'box_media' ), $k, 'side', 'high' );
			add_meta_box( 'webcomic-commerce', __( 'Webcomic Commerce', 'webcomic' ), array( $this, 'box_commerce' ), $k, 'normal', 'high' );
			add_meta_box( 'webcomic-transcripts', __( 'Webcomic Transcripts', 'webcomic' ), array( $this, 'box_transcripts' ), $k, 'normal', 'high' );
		}
	}
	
	/** Handle bulk edit meta updates.
	 * 
	 * @param integer $id Bulk-edited post ID.
	 * @hook pre_post_update
	 */
	public function pre_post_update( $id ) {
		if ( isset( $_GET[ 'bulk_edit' ], $_GET[ 'webcomic_meta_bulk' ] ) and wp_verify_nonce( $_GET[ 'webcomic_meta_bulk' ], 'webcomic_meta_bulk' ) ) {
			if ( !empty( $_GET[ 'webcomic_transcripts' ] ) ) {
				update_post_meta( $id, 'webcomic_transcripts', 'y' === $_GET[ 'webcomic_transcripts' ] );
			}
			
			if ( !empty( $_GET[ 'webcomic_prints' ] ) ) {
				update_post_meta( $id, 'webcomic_prints', 'y' === $_GET[ 'webcomic_prints' ] );
			}
			
			if ( !empty( $_GET[ 'webcomic_original' ] ) ) {
				update_post_meta( $id, 'webcomic_original', 'y' === $_GET[ 'webcomic_original' ] );
			}
		}
	}
	
	/** Update the collection updated time.
	 * 
	 * @param integer $id Post ID to update.
	 * @param object $post Post object to update.
	 * @uses Webcomic::$config
	 * @hook wp_insert_post
	 */
	public function update_collection( $id, $post ) {
		if ( ( !defined( 'DOING_AUTOSAVE' ) or !DOING_AUTOSAVE ) and 'publish' === $post->post_status and isset( self::$config[ 'collections' ][ $post->post_type ] ) and self::$config[ 'collections' ][ $post->post_type ][ 'updated' ] < mysql2date( 'U', $post->post_date ) ) {
			self::$config[ 'collections' ][ $post->post_type ][ 'updated' ] = mysql2date( 'U', $post->post_date );
			
			update_option( 'webcomic_options', self::$config );
		}
	}
	
	/** Register and enqueue meta box scripts.
	 * 
	 * If the post editor is disabled we also need to enqueue the media
	 * upload script and thickbox stylesheet to ensure uploads work.
	 * 
	 * @uses Webcomic::$url
	 * @uses Webcomic::$config
	 * @hook admin_enqueue_scripts
	 */
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		
		if ( preg_match( '/^edit-webcomic\d+$/', $screen->id ) ) {
			wp_register_script( 'webcomic-admin-posts', self::$url . '-/js/admin-posts.js', array( 'jquery' ) );
			
			wp_enqueue_script( 'webcomic-admin-posts' );
		} elseif ( isset( self::$config[ 'collections' ][ $screen->id ] ) ) {
			wp_register_script( 'webcomic-admin-meta', self::$url . '-/js/admin-meta.js', array( 'jquery' ) );
			
			wp_enqueue_script( 'webcomic-admin-meta' );
			
			if ( !in_array( 'editor', self::$config[ 'collections' ][ $screen->id ][ 'supports' ] ) ) {
				wp_enqueue_media( array( 'post' => get_post() ) );
			}
		}
	}
	
	/** Render additional post filtering options.
	 * 
	 * The wp_dropdown_categories() function works well enough, but we
	 * need to swap out the term ID values for term slug values to get
	 * it to work correctly with administrative pages.
	 * 
	 * @uses Webcomic::$config
	 * @hook restrict_manage_posts
	 */
	public function restrict_manage_posts() {
		global $post_type;
		
		if ( isset( self::$config[ 'collections' ][ $post_type ] ) ) {
			if ( $terms = get_terms( "{$post_type}_storyline", array( 'hide_empty' => false ) ) ) {
				$dropdown = wp_dropdown_categories( array(
					'echo'            => false,
					'name'            => "{$post_type}_storyline",
					'orderby'         => 'term_group',
					'taxonomy'        => "{$post_type}_storyline",
					'hide_empty'      => false,
					'hierarchical'    => true,
					'show_option_all' => __( 'View all storylines', 'webcomic' )
				) );
				
				foreach ( $terms as $term ) {
					$dropdown = ( isset( $_GET[ "{$post_type}_storyline" ] ) and $term->slug === $_GET[ "{$post_type}_storyline" ] ) ? str_replace( 'value="' . $term->term_id . '"', 'value="' . $term->slug . '" selected', $dropdown ) : str_replace( 'value="' . $term->term_id . '"', 'value="' . $term->slug . '"', $dropdown );
				}
				
				echo $dropdown;
			} else {
				echo '<select><option value="">', __( 'View all storylines', 'webcomic' ), '</option></select>';
			}
			
			if ( isset( $_GET[ 'webcomic_orphaned' ] ) ) {
				echo '<input type="hidden" name="webcomic_orphaned" value="1">';
			}
		}
	}
	
	/** Save commerce metadata with webcomics.
	 * 
	 * @param integer $id Post ID to update.
	 * @param object $post Post object to update.
	 * @uses Webcomic::$config
	 * @hook wp_insert_post
	 */
	public function save_webcomic_commerce( $id, $post ) {
		if (
			isset( $_POST[ 'webcomic_meta_commerce' ], self::$config[ 'collections' ][ $post->post_type ] )
			and ( !defined( 'DOING_AUTOSAVE' ) or !DOING_AUTOSAVE )
			and wp_verify_nonce( $_POST[ 'webcomic_meta_commerce' ], 'webcomic_meta_commerce' )
			and current_user_can( 'edit_post', $id )
		) {
			if ( $post_id = wp_is_post_revision( $id ) ) {
				$id = $post_id;
			}
			
			$commerce = array();
			$commerce[ 'price' ][ 'domestic' ]                     = round( self::$config[ 'collections' ][ $_POST[ 'post_type' ] ][ 'commerce' ][ 'price' ][ 'domestic' ] * ( 1 + $_POST[ 'webcomic_commerce_adjust_prices_domestic' ] * .01 ), 2 );
			$commerce[ 'price' ][ 'original' ]                     = round( self::$config[ 'collections' ][ $_POST[ 'post_type' ] ][ 'commerce' ][ 'price' ][ 'original' ] * ( 1 + $_POST[ 'webcomic_commerce_adjust_prices_original' ] * .01 ), 2 );
			$commerce[ 'price' ][ 'international' ]                = round( self::$config[ 'collections' ][ $_POST[ 'post_type' ] ][ 'commerce' ][ 'price' ][ 'international' ] * ( 1 + $_POST[ 'webcomic_commerce_adjust_prices_international' ] * .01 ), 2 );
			$commerce[ 'shipping' ][ 'domestic' ]                  = round( self::$config[ 'collections' ][ $_POST[ 'post_type' ] ][ 'commerce' ][ 'shipping' ][ 'domestic' ] * ( 1 + $_POST[ 'webcomic_commerce_adjust_shipping_domestic' ] * .01 ), 2 );
			$commerce[ 'shipping' ][ 'original' ]                  = round( self::$config[ 'collections' ][ $_POST[ 'post_type' ] ][ 'commerce' ][ 'shipping' ][ 'original' ] * ( 1 + $_POST[ 'webcomic_commerce_adjust_shipping_original' ] * .01 ), 2 );
			$commerce[ 'shipping' ][ 'international' ]             = round( self::$config[ 'collections' ][ $_POST[ 'post_type' ] ][ 'commerce' ][ 'shipping' ][ 'international' ] * ( 1 + $_POST[ 'webcomic_commerce_adjust_shipping_international' ] * .01 ), 2 );
			$commerce[ 'total' ][ 'domestic' ]                     = $commerce[ 'price' ][ 'domestic' ] + $commerce[ 'shipping' ][ 'domestic' ];
			$commerce[ 'total' ][ 'original' ]                     = $commerce[ 'price' ][ 'original' ] + $commerce[ 'shipping' ][ 'original' ];
			$commerce[ 'total' ][ 'international' ]                = $commerce[ 'price' ][ 'international' ] + $commerce[ 'shipping' ][ 'international' ];
			$commerce[ 'adjust' ][ 'price' ][ 'domestic' ]         = intval( $_POST[ 'webcomic_commerce_adjust_prices_domestic' ] );
			$commerce[ 'adjust' ][ 'price' ][ 'original' ]         = intval( $_POST[ 'webcomic_commerce_adjust_prices_original' ] );
			$commerce[ 'adjust' ][ 'price' ][ 'international' ]    = intval( $_POST[ 'webcomic_commerce_adjust_prices_international' ] );
			$commerce[ 'adjust' ][ 'shipping' ][ 'domestic' ]      = intval( $_POST[ 'webcomic_commerce_adjust_shipping_domestic' ] );
			$commerce[ 'adjust' ][ 'shipping' ][ 'original' ]      = intval( $_POST[ 'webcomic_commerce_adjust_shipping_original' ] );
			$commerce[ 'adjust' ][ 'shipping' ][ 'international' ] = intval( $_POST[ 'webcomic_commerce_adjust_shipping_international' ] );
			$commerce[ 'adjust' ][ 'total' ][ 'domestic' ]         = self::$config[ 'collections' ][ $_POST[ 'post_type' ] ][ 'commerce' ][ 'total' ][ 'domestic' ] ? 0 - intval( round( ( 1 - $commerce[ 'total' ][ 'domestic' ] / self::$config[ 'collections' ][ $_POST[ 'post_type' ] ][ 'commerce' ][ 'total' ][ 'domestic' ] ) * 100 ) ) : 0;
			$commerce[ 'adjust' ][ 'total' ][ 'original' ]         = self::$config[ 'collections' ][ $_POST[ 'post_type' ] ][ 'commerce' ][ 'total' ][ 'original' ] ? 0 - intval( round( ( 1 - $commerce[ 'total' ][ 'original' ] / self::$config[ 'collections' ][ $_POST[ 'post_type' ] ][ 'commerce' ][ 'total' ][ 'original' ] ) * 100 ) ) : 0;
			$commerce[ 'adjust' ][ 'total' ][ 'international' ]    = self::$config[ 'collections' ][ $_POST[ 'post_type' ] ][ 'commerce' ][ 'total' ][ 'international' ] ? 0 - intval( round( ( 1 - $commerce[ 'total' ][ 'international' ] / self::$config[ 'collections' ][ $_POST[ 'post_type' ] ][ 'commerce' ][ 'total' ][ 'international' ] ) * 100 ) ) : 0;
			
			update_post_meta( $id, 'webcomic_commerce', $commerce );
			update_post_meta( $id, 'webcomic_prints', isset( $_POST[ 'webcomic_commerce_prints' ] ) );
			update_post_meta( $id, 'webcomic_original', isset( $_POST[ 'webcomic_commerce_original_available' ] ) );
		}
	}
	
	/** Update webcomic title, slug, and content prior to saving.
	 * 
	 * @param array $data An array of post data.
	 * @param array $raw An array of raw post data.
	 * @return array
	 * @hook wp_insert_post_data
	 */
	public function wp_insert_post_data( $data, $raw ) {
		if (
			isset( self::$config[ 'collections' ][ $raw[ 'post_type' ] ] )
			and !empty( $raw[ 'ID' ] )
			and current_user_can( 'edit_post', $raw[ 'ID' ] )
		) {
			if ( !in_array( 'title', self::$config[ 'collections' ][ $data[ 'post_type' ] ][ 'supports' ] ) ) {
				$data[ 'post_title' ] = 'Auto Draft' === $raw[ 'post_title' ] ? $raw[ 'ID' ] : $raw[ 'post_title' ];
				$data[ 'post_name' ]  = wp_unique_post_slug( sanitize_title( $raw[ 'post_name' ] ? $raw[ 'post_name' ] : $data[ 'post_title' ] ), $raw[ 'ID' ], $raw[ 'post_status' ], $raw[ 'post_type' ], $raw[ 'post_parent' ] );
			}
			
			if ( !in_array( 'editor', self::$config[ 'collections' ][ $data[ 'post_type' ] ][ 'supports' ] ) ) {
				$data[ 'post_content' ] = '&nbsp;';
			}
		}
		
		return $data;
	}
	
	/** Save transcript metadata with webcomics.
	 * 
	 * @param integer $id The post ID to update.
	 * @param object $post Post object to update.
	 * @uses Webcomic::$config
	 * @hook wp_insert_post
	 */
	public function save_webcomic_transcripts( $id, $post ) {
		if (
			isset( $_POST[ 'webcomic_meta_transcripts' ], self::$config[ 'collections' ][ $post->post_type ] )
			and ( !defined( 'DOING_AUTOSAVE' ) or !DOING_AUTOSAVE )
			and wp_verify_nonce( $_POST[ 'webcomic_meta_transcripts' ], 'webcomic_meta_transcripts' )
			and current_user_can( 'edit_post', $id )
		) {
			if ( $post_id = wp_is_post_revision( $id ) ) {
				$id = $post_id;
			}
			
			update_post_meta( $id, 'webcomic_transcripts', isset( $_POST[ 'webcomic_transcripts' ] ) );
		}
	}
	
	/** Render bulk edit options.
	 * 
	 * @param string $column Name of the custom column.
	 * @param string $type Post type.
	 * @uses Webcomic::$config
	 * @hook bulk_edit_custom_box
	 */
	public function bulk_edit_custom_box( $column, $type ) {
		global $post;
		
		if ( 'webcomic_attachments' === $column and preg_match( '/^webcomic\d+$/', $type ) ) {
			wp_nonce_field( 'webcomic_meta_bulk', 'webcomic_meta_bulk' );
		?>
		<fieldset class="inline-edit-col-right" data-webcomic-admin-url="<?php echo admin_url(); ?>">
			<div class="inline-edit-group">
				<label class="alignleft">
					<span class="title"><?php _e( 'Transcribe', 'webcomic' ); ?></span>
					<select name="webcomic_transcripts">
						<option value=""><?php _e( '- No Change -', 'webcomic' ); ?></option>
						<option value="y"><?php _e( 'Allow', 'webcomic' ); ?></option>
						<option value="n"><?php _e( 'Do not allow', 'webcomic' ); ?></option>
					</select>
				</label>
			</div>
			<div class="inline-edit-group">
				<label class="alignleft">
					<span class="title"><?php _e( 'Prints', 'webcomic' ); ?></span>
					<select name="webcomic_prints"<?php disabled( !self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'business' ] ); ?>>
						<option value=""><?php _e( '- No Change -', 'webcomic' ); ?></option>
						<option value="y"><?php _e( 'Sell', 'webcomic' ); ?></option>
						<option value="n"><?php _e( 'Do not sell', 'webcomic' ); ?></option>
					</select>
				</label>
				<label class="alignright">
					<span class="title"><?php _e( 'Original', 'webcomic' ); ?></span>
					<select name="webcomic_original">
						<option value=""><?php _e( '- No Change -', 'webcomic' ); ?></option>
						<option value="y"><?php _e( 'Available', 'webcomic' ); ?></option>
						<option value="n"><?php _e( 'Not available', 'webcomic' ); ?></option>
					</select>
				</label>
			</div>
		</fieldset>
		<?php
		}
	}
	
	/** Render quick edit options.
	 *
	 * @param string $column Name of the custom column.
	 * @param string $type Post type.
	 * @hook quick_edit_custom_box
	 */
	public function quick_edit_custom_box( $column, $type ) {
		if ( 'webcomic_attachments' === $column and preg_match( '/^webcomic\d+$/', $type ) ) {
			wp_nonce_field( 'webcomic_inline_save', 'webcomic_inline_save' );
		?>
		<fieldset class="inline-edit-col-right">
			<div class="inline-edit-col">
				<div class="inline-edit-group webcomic_quick_edit">
					<label><input type="checkbox" name="webcomic_transcripts" id="webcomic_transcripts" value="1"> <span class="checkbox-title"><?php _e( 'Allow Transcribing', 'webcomic' ); ?> </span></label>
				</div>
				<div class="inline-edit-group webcomic_quick_edit">
					<label class="alignleft"><input type="checkbox" name="webcomic_prints" id="webcomic_prints" value="1"> <span class="checkbox-title"><?php _e( 'Sell Prints', 'webcomic' ); ?>  &nbsp;</span></label>
					<label><input type="checkbox" name="webcomic_original" id="webcomic_original" value="1"> <span class="checkbox-title"><?php _e( 'Original Print Available', 'webcomic' ); ?></span></label>
				</div>
			</div>
		</fieldset>
		<?php
		}
	}
	
	/** Filter posts list to display webcomics with no attachments.
	 * 
	 * @param string $where The WHERE query string.
	 * @return string
	 * @hook posts_where
	 */
	public function posts_where( $where ) {
		global $wpdb;
		
		if ( isset( $_GET[ 'post_type' ], self::$config[ 'collections' ][ $_GET[ 'post_type' ] ], $_GET[ 'webcomic_orphaned' ] ) ) {
			$where .= " AND ID NOT IN ( SELECT post_parent FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%' )";
		}
		
		return $where;
	}
	
	/** Render column content for post admin pages.
	 * 
	 * @param string $column The column name.
	 * @param integer $id Current post ID.
	 * @uses Webcomic::get_attachments()
	 * @hook manage_(webcomic\d+)_posts_custom_column
	 */
	public function manage_webcomic_posts_custom_column( $column, $id ) {
		global $post;
		
		if ( 'webcomic_attachments' === $column ) {
			if ( $attachments = self::get_attachments( $id ) ) {
				foreach ( $attachments as $attachment ) {
					printf( '<a href="%s">%s</a>',
						esc_url( add_query_arg( array( 'post' => $attachment->ID, 'action' => 'edit' ), admin_url( 'post.php' ) ) ),
						wp_get_attachment_image( $attachment->ID )
					);
				}
			} else {
				_e( '&mdash;', 'webcomic' );
			}
		}
	}
	
	/** Add Orphaned view for webcomic posts.
	 * 
	 * @param array $views Array of view links.
	 * @return array
	 * @hook views_edit-(webcomic\d+)
	 */
	public function view_edit_webcomic( $views ) {
		global $wpdb;
		
		if ( $orphans = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s AND ID NOT IN ( SELECT post_parent FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%%' )", $_GET[ 'post_type' ] ) ) ) {
			$posts = wp_count_posts( $_GET[ 'post_type' ], 'readable' );
			
			foreach ( get_post_stati( array( 'show_in_admin_all_list' => false) ) as $state ) {
				$orphans -= $posts->$state;
			}
			
			if ( 0 < $orphans ) {
				$views[ 'webcomic_orphans' ] = sprintf( '<a href="%s"%s>%s <span class="count">(%d)</span></a>',
					esc_url( add_query_arg( array( 'post_type' => $_GET[ 'post_type' ], 'post_status' => 'all', 'webcomic_orphaned' => true ), admin_url( 'edit.php' ) ) ),
					isset( $_GET[ 'webcomic_orphaned' ] ) ? ' class="current"' : '',
					__( 'Orphaned', 'webcomic' ),
					$orphans
				);
			}
		}
		
		return $views;
	}
	
	/** Add custom webcomic post type columns to management pages.
	 * 
	 * @param array $columns Array of of post columns.
	 * @return array
	 * @hook manage_edit-(webcomic\d+)_columns
	 */
	public function manage_edit_webcomic_columns( $columns ) {
		$pre = array_slice( $columns, 0, 1 );
		//$mid = array_slice( $columns, 1, 2 );
		
		$pre[ 'webcomic_attachments' ] = '';
		$columns[ "taxonomy-{$_GET[ 'post_type' ]}_character" ] = __( 'Characters', 'webcomic' );
		$columns[ "taxonomy-{$_GET[ 'post_type' ]}_storyline" ] = __( 'Storylines', 'webcomic' );
		
		return array_merge( $pre, $columns );
	}
	
	/** Render the webcomic media meta box.
	 * 
	 * @param object $post Current post object.
	 * @uses WebcomicPosts::ajax_media_preview()
	 */
	public function box_media( $post ) {
		?>
		<div id="webcomic_media_preview" data-webcomic-admin-url="<?php echo admin_url(); ?>"><?php self::ajax_media_preview( $post->ID ); ?></div>
		<?php
	}
	
	/** Render the webcomic commerce meta box.
	 * 
	 * @param object $post Current post object.
	 * @uses Webcomic::$config
	 */
	public function box_commerce( $post ) {
		$commerce = get_post_meta( $post->ID, 'webcomic_commerce', true );
		$commerce = $commerce ? $commerce : array();
		
		wp_nonce_field( 'webcomic_meta_commerce', 'webcomic_meta_commerce' );
		?>
		<p><label><input type="checkbox" name="webcomic_commerce_prints"<?php checked( ( 'auto-draft' === $post->post_status and self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'prints' ] ) or ( self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'business' ] and get_post_meta( $post->ID, 'webcomic_prints', true ) ) ); disabled( !self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'business' ] ); ?>> <?php _e( 'Sell prints', 'webcomic' ); ?></label></p>
		<div style="margin:0 -11px" data-webcomic-currency="<?php echo self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'currency' ]; ?>" data-webcomic-original="<?php esc_attr_e( '- SOLD -', 'webcomic' ); ?>">
			<table class="widefat fixed">
				<thead>
					<tr>
						<th class="check-column"></th>
						<th></th>
						<th><?php _e( 'Price', 'webcomic' ); ?></th>
						<th><?php _e( 'Shipping', 'webcomic' ); ?></th>
						<th><?php _e( 'Total', 'webcomic' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th></th>
						<th><?php _e( 'Domestic', 'webcomic' ); ?></th>
						<td>
							<p>
								<label>
									<span id="webcomic_commerce_domestic_price"><?php echo self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'price' ][ 'domestic' ]; ?></span>
									<input type="number" name="webcomic_commerce_adjust_prices_domestic" id="webcomic_commerce_adjust_prices_domestic" value="<?php echo isset( $commerce[ 'adjust' ][ 'price' ][ 'domestic' ] ) ? $commerce[ 'adjust' ][ 'price' ][ 'domestic' ] : 0; ?>" min="-100" class="small-text" style="text-align:center">%
								</label>
							</p>
						</td>
						<td>
							<p>
								<label>
									<span id="webcomic_commerce_domestic_shipping"><?php echo self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'shipping' ][ 'domestic' ]; ?></span>
									<input type="number" name="webcomic_commerce_adjust_shipping_domestic" id="webcomic_commerce_adjust_shipping_domestic" value="<?php echo isset( $commerce[ 'adjust' ][ 'shipping' ][ 'domestic' ] ) ? $commerce[ 'adjust' ][ 'shipping' ][ 'domestic' ] : 0; ?>" min="-100" class="small-text" style="text-align:center">%
								</label>
							</p>
						</td>
						<td><p id="webcomic_domestic_total"><?php echo isset( $commerce[ 'total' ][ 'domestic' ] ) ? number_format( $commerce[ 'total' ][ 'domestic' ], 2 ) . ' ' . self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'currency' ] : number_format( self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'total' ][ 'domestic' ], 2 ) . ' ' . self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'currency' ] ?></p></td>
					</tr>
					<tr>
						<th></th>
						<th><?php _e( 'International', 'webcomic' ); ?></th>
						<td>
							<p>
								<label>
									<span id="webcomic_commerce_international_price"><?php echo self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'price' ][ 'international' ]; ?></span>
									<input type="number" name="webcomic_commerce_adjust_prices_international" id="webcomic_commerce_adjust_prices_international" value="<?php echo isset( $commerce[ 'adjust' ][ 'price' ][ 'international' ] ) ? $commerce[ 'adjust' ][ 'price' ][ 'international' ] : 0; ?>" min="-100" class="small-text" style="text-align:center">%
								</label>
							</p>
						</td>
						<td>
							<p>
								<label>
									<span id="webcomic_commerce_international_shipping"><?php echo self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'shipping' ][ 'international' ]; ?></span>
									<input type="number" name="webcomic_commerce_adjust_shipping_international" id="webcomic_commerce_adjust_shipping_international" value="<?php echo isset( $commerce[ 'adjust' ][ 'shipping' ][ 'international' ] ) ? $commerce[ 'adjust' ][ 'shipping' ][ 'international' ] : 0; ?>" min="-100" class="small-text" style="text-align:center">%
								</label>
							</p>
						</td>
						<td><p id="webcomic_international_total"><?php echo isset( $commerce[ 'total' ][ 'international' ] ) ? number_format( $commerce[ 'total' ][ 'international' ], 2 ) . ' ' . self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'currency' ] : number_format( self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'total' ][ 'international' ], 2 ) . ' ' . self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'currency' ] ?></p></td>
					</tr>
					<tr>
						<th><input type="checkbox" name="webcomic_commerce_original_available" id="webcomic_commerce_original_available" style="vertical-align:bottom"<?php checked( ( ( !$commerce and self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'originals' ] ) or get_post_meta( $post->ID, 'webcomic_original', true ) ) ); ?>></th>
						<th><label for="webcomic_commerce_original_available"><?php _e( 'Original', 'webcomic' ); ?></label></th>
						<td>
							<p>
								<label>
									<span id="webcomic_commerce_original_price"><?php echo self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'price' ][ 'original' ]; ?></span>
									<input type="number" name="webcomic_commerce_adjust_prices_original" id="webcomic_commerce_adjust_prices_original" value="<?php echo isset( $commerce[ 'adjust' ][ 'price' ][ 'original' ] ) ? $commerce[ 'adjust' ][ 'price' ][ 'original' ] : 0; ?>" min="-100" class="small-text" style="text-align:center">%
								</label>
							</p>
						</td>
						<td>
							<p>
								<label>
									<span id="webcomic_commerce_original_shipping"><?php echo self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'shipping' ][ 'original' ]; ?></span>
									<input type="number" name="webcomic_commerce_adjust_shipping_original" id="webcomic_commerce_adjust_shipping_original" value="<?php echo isset( $commerce[ 'adjust' ][ 'shipping' ][ 'original' ] ) ? $commerce[ 'adjust' ][ 'shipping' ][ 'original' ] : 0; ?>" min="-100" class="small-text" style="text-align:center">%
								</label>
							</p>
						</td>
						<td>
							<p id="webcomic_original_total">
							<?php
								if ( ( !$commerce and self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'originals' ] ) or get_post_meta( $post->ID, 'webcomic_original', true ) ) {
									echo $commerce ? number_format( $commerce[ 'total' ][ 'original' ], 2 ) . ' ' . self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'currency' ] : number_format( self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'total' ][ 'original' ], 2 ) . ' ' . self::$config[ 'collections' ][ $post->post_type ][ 'commerce' ][ 'currency' ];
								} else {
									_e( '- SOLD -', 'webcomic' );
								}
							?>
							</p>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}
	
	/** Render the webcomic transcripts meta box.
	 * 
	 * @param object $post Current post object.
	 * @uses Webcomic::$config
	 */
	public function box_transcripts( $post ) {
		wp_nonce_field( 'webcomic_meta_transcripts', 'webcomic_meta_transcripts' );
		?>
		<p><label><input type="checkbox" name="webcomic_transcripts"<?php checked( ( 'auto-draft' === $post->post_status and self::$config[ 'collections' ][ $post->post_type ][ 'transcripts' ][ 'open' ] ) or get_post_meta( $post->ID, 'webcomic_transcripts', true ) ); ?>> <?php _e( 'Allow transcribing', 'webcomic' ); ?></label></p>
		<div style="margin: 0 -11px">
			<table class="widefat fixed">
			<?php
				$posts = get_posts( array( 'post_type' => 'webcomic_transcript', 'post_status' => 'any', 'post_parent' => $post->ID ) );
				
				if ( $posts ) {
					foreach ( $posts as $p ) {
						$date    = strtotime( $p->post_date );
						$status  = get_post_status_object( $p->post_status )->label;
						$authors = array( get_userdata( $p->post_author )->nickname );
						
						foreach ( get_post_meta( $p->ID, 'webcomic_author' ) as $author ) {
							$authors[] = esc_html( $author[ 'name' ] );
						}
						
						$authors = join( ', ', $authors );
						
						if ( $languages = wp_get_object_terms( $p->ID, 'webcomic_language' ) ) {
							$terms = array();
							
							foreach ( $languages as $language ) {
								$terms[] = esc_html( sanitize_term_field( 'name', $language->name, $language->term_id, 'webcomic_transcript', 'display' ) );
							}
							
							$terms = join( ', ', $terms );
						} else {
							$terms = __( ' - ', 'webcomic' );
						}
						
						printf( '
							<tr>
								<td style="width:33%%">
									%s<br>%s
								</td>
								<td>
									<div class="submitted-on">%s</div>%s
								</td>
							</tr>',
							current_user_can( 'edit_post', $p->ID ) ? sprintf( '<strong><a href="%s" class="row-title">%s</a></strong>', get_edit_post_link( $p->ID ), esc_html( $p->post_title ) ) : sprintf( '<strong>%s</strong>', esc_html( $p->post_title ) ),
							$status,
							sprintf( __( 'Submitted on %1$s at %2$s by %3$s (%4$s)', 'webcomic' ), date( 'Y/n/j', $date ), date( 'g:i a', $date ), $authors, $terms ),
							apply_filters( 'the_content', $p->post_content )
						);
					}
				} else {
					printf( '<tr><td><div class="submitted-on">%s</div></td></tr>', get_post_type_object( 'webcomic_transcript' )->labels->not_found );
				}
			?>
			</table>
		</div>
		<?php
	}
	
	/** Render the webcomic attachments meta box.
	 * 
	 * @param integer $id Post ID to render attachments for.
	 * @uses Webcomic::get_attachments()
	 */
	public static function ajax_media_preview( $id ) {
		global $content_width, $_wp_additional_image_sizes, $post_ID;
		
		if ( $attachments = self::get_attachments( $id ) ) {
			$old_content_width = $content_width;
			$content_width = 266;
			
			echo '<style scoped>.insert-media img{vertical-align:bottom}</style><a href="#" class="insert-media">';
			
			foreach ( $attachments as $attachment ) {
				echo wp_get_attachment_image( $attachment->ID, array( $content_width, $content_width ) );
			}
			
			echo '</a>';
			
			$content_width = $old_content_width;
		} else {
			$post_ID = $post_ID ? $post_ID : $id;
			
			printf( '<a href="#" class="button insert-media">%s</a><p>%s</p>',
				__( 'Add Media', 'webcomic' ),
				__( 'Webcomic will automatically recognize any images attached to this post. You <strong>do not</strong> have to insert them directly into the post content.', 'webcomic' )
			);
		}
	}
	
	/** Update quick edit meta values.
	 * 
	 * @param integer $id Post ID to retrieve values for.
	 */
	public static function ajax_quick_edit( $id ) {
		$type = get_post_type( $id );
		
		echo json_encode( array(
			'prints'          => ( !empty( self::$config[ 'collections' ][ $type ][ 'commerce' ][ 'business' ] ) and get_post_meta( $id, 'webcomic_prints', true ) ),
			'original'        => ( boolean ) get_post_meta( $id, 'webcomic_original', true ),
			'transcripts'     => ( boolean ) get_post_meta( $id, 'webcomic_transcripts', true ),
			'prints_disabled' => empty( self::$config[ 'collections' ][ $type ][ 'commerce' ][ 'business' ] )
		) );
	}
	
	/** Save quick edit meta values.
	 * 
	 * @param integer $id Post ID to save values for.
	 * @param boolean $prints Updated prints meta value.
	 * @param boolean $original Updated original print meta value.
	 * @param boolean $transcripts Updated transcription meta value.
	 */
	public static function ajax_quick_save( $id, $prints, $original, $transcripts ) {
		if ( isset( $_GET[ 'webcomic_inline_save' ] ) and wp_verify_nonce( $_GET[ 'webcomic_inline_save' ], 'webcomic_inline_save' ) and current_user_can( 'edit_post', $id ) ) {
			update_post_meta( $id, 'webcomic_prints', ( boolean ) $prints );
			update_post_meta( $id, 'webcomic_original', ( boolean ) $original );
			update_post_meta( $id, 'webcomic_transcripts', ( boolean ) $transcripts );
		}
	}
}
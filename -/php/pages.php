<?php
/** Contains the WebcomicPages class.
 * 
 * @package Webcomic
 */

/** Handle page-related tasks.
 * 
 * @package Webcomic
 */
class WebcomicPages extends Webcomic {
	/** Register hooks.
	 * 
	 * @uses WebcomicPages::admin_footer()
	 * @uses WebcomicPages::add_meta_boxes()
	 * @uses WebcomicPages::wp_insert_post()
	 * @uses WebcomicPages::pre_post_update()
	 * @uses WebcomicPages::admin_enqueue_scripts()
	 * @uses WebcomicPages::bulk_edit_custom_box()
	 * @uses WebcomicPages::quick_edit_custom_box()
	 * @uses WebcomicPages::manage_page_posts_custom_column()
	 * @uses WebcomicPages::request()
	 * @uses WebcomicPages::manage_edit_page_columns()
	 * @uses WebcomicPages::manage_edit_page_sortable_columns()
	 */
	public function __construct() {
		add_action( 'admin_footer', array( $this, 'admin_footer' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'wp_insert_post', array( $this, 'wp_insert_post' ), 10, 2 );
		add_action( 'pre_post_update', array( $this, 'pre_post_update' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'bulk_edit_custom_box', array( $this, 'bulk_edit_custom_box' ), 10, 2 );
		add_action( 'quick_edit_custom_box', array( $this, 'quick_edit_custom_box' ), 10, 2 );
		add_action( 'manage_page_posts_custom_column', array( $this, 'manage_page_posts_custom_column' ), 10, 2 );
		
		add_filter( 'request', array( $this, 'request' ), 10, 1 );
		add_filter( 'manage_edit-page_columns', array( $this, 'manage_edit_page_columns' ), 10, 1 );
		add_filter( 'manage_edit-page_sortable_columns', array( $this, 'manage_edit_page_sortable_columns' ), 10, 1 );
	}
	
	/** Render javascript for page meta boxes.
	 * 
	 * @uses Webcomic::$config
	 * @hook admin_footer
	 */
	public function admin_footer() {
		$screen = get_current_screen();
		
		if ( 'edit-page' === $screen->id  ) {
			printf( "<script>webcomic_page_quick_save( '%s' );webcomic_page_quick_edit( '%s' );</script>", admin_url(), admin_url() );
		}
	}
	
	/** Add page meta boxes.
	 * 
	 * @uses WebcomicPages::box_webcomic_collection()
	 * @hook add_meta_boxes
	 */
	public function add_meta_boxes() {
		add_meta_box( 'webcomic-collection', __( 'Webcomic Collection', 'webcomic' ), array( $this, 'box_webcomic_collection' ), 'page', 'side' );
	}
	
	/** Save metadata with pages.
	 * 
	 * @param integer $id The page ID to update.
	 * @param object $post Post object to update.
	 * @hook wp_insert_post
	 */
	public function wp_insert_post( $id, $post ) {
		if (
			isset( $_POST[ 'webcomic_meta_collection' ], $_POST[ 'webcomic_page_collection' ] )
			and 'page' === $post->post_type
			and ( !defined( 'DOING_AUTOSAVE' ) or !DOING_AUTOSAVE )
			and wp_verify_nonce( $_POST[ 'webcomic_meta_collection' ], 'webcomic_meta_collection' )
			and current_user_can( 'edit_page', $id )
		) {
			if ( $post_id = wp_is_post_revision( $id ) ) {
				$id = $post_id;
			}
			
			if ( $_POST[ 'webcomic_page_collection' ] ) {
				update_post_meta( $id, 'webcomic_collection', $_POST[ 'webcomic_page_collection' ] );
			} else {
				delete_post_meta( $id, 'webcomic_collection' );
			}
		}
	}
	
	/** Handle bulk edit meta updates.
	 * 
	 * @param integer $id Bulk-edited post ID.
	 * @hook pre_post_update
	 */
	public function pre_post_update( $id ) {
		if ( isset( $_GET[ 'bulk_edit' ], $_GET[ 'webcomic_page_meta_bulk' ] ) and wp_verify_nonce( $_GET[ 'webcomic_page_meta_bulk' ], 'webcomic_page_meta_bulk' ) ) {
			if ( !empty( $_GET[ 'webcomic_page_collection' ] ) ) {
				update_post_meta( $id, 'webcomic_collection', $_GET[ 'webcomic_page_collection' ] );
			}
		}
	}
	
	/** Register and enqueue meta box scripts.
	 * 
	 * @uses Webcomic::$url
	 * @hook admin_enqueue_scripts
	 */
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		
		if ( 'edit-page' === $screen->id ) {
			wp_register_script( 'webcomic-admin-pages', self::$url . '-/js/admin-pages.js', array( 'jquery' ) );
			
			wp_enqueue_script( 'webcomic-admin-pages' );
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
		
		if ( 'webcomic_collection' === $column and 'page' === $type ) {
			wp_nonce_field( 'webcomic_page_meta_bulk', 'webcomic_page_meta_bulk' );
		?>
		<fieldset class="inline-edit-col-right">
			<div class="inline-edit-group">
				<label class="alignleft">
					<span class="title"><?php _e( 'Webcomic Collection', 'webcomic' ); ?></span>
					<select name="webcomic_page_collection" id="webcomic_page_collection">
						<option value=""><?php _e( '&mdash; No Change &mdash;', 'webcomic' ); ?></option>
					<?php
						foreach ( self::$config[ 'collections' ] as $collection ) {
							printf( '<option value="%s">%s</option>', $collection[ 'id' ], esc_html( $collection[ 'name' ] ) );
						}
					?>
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
		if ( 'webcomic_collection' === $column and 'page' === $type ) {
			wp_nonce_field( 'webcomic_page_inline_save', 'webcomic_page_inline_save' );
		?>
		<fieldset class="inline-edit-col-left">
			<div class="inline-edit-col">
				<div class="inline-edit-group webcomic_quick_edit">
					<label>
						<span class="title"><?php _e( 'Webcomic Collection', 'webcomic' ); ?></span>
						<select name="webcomic_page_collection" id="webcomic_page_collection">
							<option value=""><?php _e( '(no collection)', 'webcomic' ); ?></option>
						<?php
							foreach ( self::$config[ 'collections' ] as $collection ) {
								printf( '<option value="%s">%s</option>', $collection[ 'id' ], esc_html( $collection[ 'name' ] ) );
							}
						?>
						</select>
					</label>
				</div>
			</div>
		</fieldset>
		<?php
		}
	}
	
	/** Render column content for page admin pages.
	 * 
	 * @param string $column The column name.
	 * @param integer $id Current post ID.
	 * @hook manage_page_posts_custom_column
	 */
	public function manage_page_posts_custom_column( $column, $id ) {
		if ( 'webcomic_collection' === $column ) {
			echo ( $collection = get_post_meta( $id, 'webcomic_collection', true ) and isset( self::$config[ 'collections' ][ $collection ] ) ) ? sprintf( '<a href="%s">%s</a>', esc_url( add_query_arg( array( 'post_type' => 'page', 'meta_key' => 'webcomic_collection', 'meta_value' => $collection ), admin_url( 'edit.php' ) ) ), self::$config[ 'collections' ][ $collection ][ 'name' ] ) : __( 'No Collection', 'webcomic' );
		}
	}
	
	/** Handle sorting and filtering pages by webcomic collection.
	 * 
	 * Because of how WordPress handles custom field queries, sorting
	 * this way has the unintentional side effect of filtering out any
	 * pages without a `webcomci_collection` custom field.
	 * 
	 * @param array $request An array of request parameters.
	 * @return array
	 * @hook request
	 */
	public function request( $request ) {
		if ( isset( $request[ 'post_type' ], $request[ 'orderby' ] ) and 'page' === $request[ 'post_type' ] and 'webcomic_collection' === $request[ 'orderby' ] ) {
			$request = array_merge( $request, array(
				'meta_key' => 'webcomic_collection',
				'orderby'  => 'meta_value'
			) );
		} elseif ( isset( $request[ 'post_type' ], $_GET[ 'meta_key' ], $_GET[ 'meta_value' ] ) and 'page' === $request[ 'post_type' ] and 'webcomic_collection' === $_GET[ 'meta_key' ] ) {
			$request = array_merge( $request, array(
				'meta_key'   => 'webcomic_collection',
				'meta_value' => $_GET[ 'meta_value' ]
			) );
		}
		
		return $request;
	}
	
	/** Add custom webcomic collection to management pages.
	 * 
	 * @param array $columns Array of of post columns.
	 * @return array
	 * @hook manage_edit-page_columns
	 */
	public function manage_edit_page_columns( $columns ) {
		$pre = array_slice( $columns, 0, 3 );
		
		$pre[ 'webcomic_collection' ] = __( 'Webcomic Collection', 'webcomic' );
		
		return array_merge( $pre, $columns );
	}
	
	/** Add sortable webcomic collection column.
	 * 
	 * @param array $columns An array of sortable columns.
	 * @return array
	 * @hook manage_edit-page_sortable_columns
	 */
	public function manage_edit_page_sortable_columns( $columns ) {
		return array_merge( array( 'webcomic_collection' => 'webcomic_collection' ), $columns );
	}
	
	/** Render the page collection meta box.
	 * 
	 * @param object $page Current page object.
	 * @uses Webcomic::$config
	 */
	public function box_webcomic_collection( $page ) {
		$page_collection = get_post_meta( $page->ID, 'webcomic_collection', true );
		
		wp_nonce_field( 'webcomic_meta_collection', 'webcomic_meta_collection' );
		?>
		<p>
			<select name="webcomic_page_collection">
				<option value=""><?php _e( '(no collection)', 'webcomic' ); ?></option>
				<?php
					foreach ( self::$config[ 'collections' ] as $k => $v ) {
						printf( '<option value="%s"%s>%s</option>',
							$k,
							selected( $k, $page_collection, false ),
							esc_html( $v[ 'name' ] )
						);
					}
				?>
			</select>
		</p>
		<?php
	}
	
	/** Update quick edit meta values.
	 * 
	 * @param integer $id Post ID to retrieve values for.
	 */
	public static function ajax_quick_edit( $id ) {
		echo json_encode( array(
			'collection' => get_post_meta( $id, 'webcomic_collection', true ),
		) );
	}
	
	/** Save quick edit meta values.
	 * 
	 * @param integer $id Post ID to save values for.
	 * @param string $page_collection Update collection meta value.
	 */
	public static function ajax_quick_save( $id, $page_collection ) {
		if ( isset( $_GET[ 'webcomic_page_inline_save' ] ) and wp_verify_nonce( $_GET[ 'webcomic_page_inline_save' ], 'webcomic_page_inline_save' ) and current_user_can( 'edit_page', $id ) ) {
			if ( $page_collection ) {
				update_post_meta( $id, 'webcomic_collection', $page_collection );
			} else {
				delete_post_meta( $id, 'webcomic_collection' );
			}
		}
	}
}
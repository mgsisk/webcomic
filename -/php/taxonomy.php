<?php
/** Contains WebcomicTaxonomy and related classes.
 * 
 * @package Webcomic
 */

/** Handle taxonomy-related tasks.
 * 
 * @package Webcomic
 */
class WebcomicTaxonomy extends Webcomic {
	/** Register hooks.
	 * 
	 * @uses Webcomic::$config
	 * @uses WebcomicTaxonomy::admin_init()
	 * @uses WebcomicTaxonomy::admin_menu()
	 * @uses WebcomicTaxonomy::edit_term()
	 * @uses WebcomicTaxonomy::create_term()
	 * @uses WebcomicTaxonomy::delete_term()
	 * @uses WebcomicTaxonomy::admin_enqueue_scripts()
	 * @uses WebcomicTaxonomy::get_terms_args()
	 * @uses WebcomicTaxonomy::pre_add_form()
	 * @uses WebcomicTaxonomy::add_form_fields()
	 * @uses WebcomicTaxonomy::edit_form_fields()
	 * @uses WebcomicTaxonomy::manage_custom_column()
	 * @uses WebcomicTaxonomy::manage_edit_webcomic_storyline_columns()
	 * @uses WebcomicTaxonomy::manage_edit_webcomic_character_columns()
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'edit_term', array( $this, 'edit_term' ), 10, 3 );
		add_action( 'create_term', array( $this, 'create_term' ), 10, 3 );
		add_action( 'delete_term', array( $this, 'delete_term' ), 10, 3 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		
		add_filter( 'get_terms_args', array( $this, 'get_terms_args' ), 10, 2 );
		
		foreach ( array_keys( self::$config[ 'collections' ] ) as $k ) {
			add_action( "{$k}_storyline_pre_add_form", array( $this, 'pre_add_form' ), 10, 1 );
			add_action( "{$k}_storyline_add_form_fields", array( $this, 'add_form_fields' ), 10, 1 );
			add_action( "{$k}_character_add_form_fields", array( $this, 'add_form_fields' ), 10, 1 );
			add_action( "{$k}_storyline_edit_form_fields", array( $this, 'edit_form_fields' ), 10, 2 );
			add_action( "{$k}_character_edit_form_fields", array( $this, 'edit_form_fields' ), 10, 2 );
			
			add_filter( "manage_{$k}_storyline_custom_column", array( $this, 'manage_custom_column' ), 10, 3 );
			add_filter( "manage_{$k}_character_custom_column", array( $this, 'manage_custom_column' ), 10, 3 );
			add_filter( "manage_edit-{$k}_storyline_columns", array( $this, 'manage_edit_webcoimc_storyline_columns' ), 10, 3 );
			add_filter( "manage_edit-{$k}_character_columns", array( $this, 'manage_edit_webcoimc_character_columns' ), 10, 3 );
		}
	}
	
	/** Handle storyline sorting.
	 * 
	 * @uses Webcomic::$error
	 * @uses Webcomic::$notice
	 * @hook admin_init
	 */
	public function admin_init() {
		global $wpdb;
		
		if ( isset( $_POST[ 'webcomic_action' ] ) and 'term_sort' === $_POST[ 'webcomic_action' ] and wp_verify_nonce( $_POST[ 'webcomic_term_sort' ], 'webcomic_term_sort' ) ) {
			if ( isset( $_POST[ 'webcomic_cancel_sort' ] ) ) {
				wp_redirect( add_query_arg( array( 'taxonomy' => $_POST[ 'webcomic_taxonomy' ], 'post_type' => $_POST[ 'webcomic_collection' ] ), admin_url( 'edit-tags.php' ) ) );
				
				die;
			} else {
				$terms = $count = array();
				
				parse_str( $_POST[ 'webcomic_terms' ], $terms );
				
				foreach ( $terms[ 'term' ] as $k => $v ) {
					$count[ $v ] = empty( $count[ $v ] ) ? 1 : $count[ $v ] + 1;
					
					$wpdb->update( $wpdb->terms, array( 'term_group' => $count[ $v ] ), array( 'term_id' => $k ) );
					$wpdb->update( $wpdb->term_taxonomy, array( 'parent' => 'null' === $v ? 0 : $v ), array( 'term_id' => $k, 'taxonomy' => $_POST[ 'webcomic_taxonomy' ] ) );
				}
				
				clean_term_cache( array_keys( $terms[ 'term' ] ), $_POST[ 'webcomic_taxonomy' ] );
				
				self::$notice[] = sprintf( '<strong>%s</strong>', __( 'Order updated.', 'webcomic' ) );
			}
		}
	}
	
	/** Register taxonomy submenu pages.
	 * 
	 * @uses WebcomicTaxonomy::page_term_sort()
	 * @hook admin_menu
	 */
	public function admin_menu() {
		add_submenu_page( 'options.php', __( 'Sort Webcomic Terms', 'webcomic' ), __( 'Sort Webcomic Terms', 'webcomic' ), 'manage_categories', 'webcomic-term-sort', array( $this, 'page_term_sort' ) );
	}
	
	/** Detach media, upload new media, and update term group.
	 * 
	 * @param integer $id The edited term ID.
	 * @param integer $tax_id The edited term taxonomy ID.
	 * @param string $taxonomy The edited term taxonomy.
	 * @uses Webcomic::$config
	 * @hook edit_term
	 */
	public function edit_term( $id, $tax_id, $taxonomy ) {
		global $wpdb;
		
		if ( preg_match( '/^webcomic\d+_(storyline|character)$/', $taxonomy ) ) {
			if ( isset( $_POST[ 'webcomic_image' ] ) ) {
				if ( $_POST[ 'webcomic_image' ] ) {
					if ( isset( self::$config[ 'terms' ][ $id ][ 'image' ] ) and self::$config[ 'terms' ][ $id ][ 'image' ] !== $_POST[ 'webcomic_image' ] ) {
						delete_post_meta( self::$config[ 'terms' ][ $id ][ 'image' ], '_wp_attachment_context', $taxonomy );
					}
					
					self::$config[ 'terms' ][ $id ][ 'image' ] = $_POST[ 'webcomic_image' ];
					
					update_post_meta( $_POST[ 'webcomic_image' ], '_wp_attachment_context', $taxonomy );
				} else {
					delete_post_meta( self::$config[ 'terms' ][ $id ][ 'image' ], '_wp_attachment_context', $taxonomy );
					
					unset( self::$config[ 'terms' ][ $id ][ 'image' ] );
				}
				
				update_option( 'webcomic_options', self::$config );
			}
			
			if ( false !== strpos( $taxonomy, '_storyline' ) ) {
				$terms = get_terms( $taxonomy, array( 'get' => 'all', 'cache_domain' => "webcomic_edit_term{$id}" ) );
				$count = array();
				
				foreach ( $terms as $term ) {
					$count[ $term->parent ] = empty( $count[ $term->parent ] ) ? 1 : $count[ $term->parent ] + 1;
					
					$wpdb->update( $wpdb->terms, array( 'term_group' => $count[ $term->parent ] ), array( 'term_id' => $term->term_id ) );
				}
			}
		}
	}
	
	/** Upload media and set storyline group when creating a new term.
	 * 
	 * @param integer $id The created term ID.
	 * @param integer $tax_id The created term taxonomy ID.
	 * @param string $taxonomy The created term taxonomy.
	 * @uses Webcomic::$config
	 * @hook create_term
	 */
	public function create_term( $id, $tax_id, $taxonomy ) {
		global $wpdb;
		
		if ( preg_match( '/^webcomic\d+_(storyline|character)$/', $taxonomy ) ) {
			if ( !empty( $_POST[ 'webcomic_image' ] ) ) {
				self::$config[ 'terms' ][ $id ][ 'image' ] = $_POST[ 'webcomic_image' ];
				
				update_post_meta( $_POST[ 'webcomic_image' ], '_wp_attachment_context', $taxonomy );
				
				update_option( 'webcomic_options', self::$config );
			}
			
			if ( false !== strpos( $taxonomy, '_storyline' ) ) {
				$terms = get_terms( $taxonomy, array(
					'order'      => 'DESC',
					'orderby'    => 'term_group',
					'child_of'   => ( isset( $_POST[ 'parent' ] ) and 0 < $_POST[ 'parent' ] ) ? $_POST[ 'parent' ] : 0,
					'hide_empty' => false
				) );
				
				foreach ( $terms as $k => $v ) {
					if ( $id === ( integer ) $v->term_id ) {
						unset( $terms[ $k ] );
						break;
					}
				}
				
				if ( $terms ) {
					$term = array_shift( $terms );
					
					$wpdb->update( $wpdb->terms, array( 'term_group' => $term->term_group + 1 ), array( 'term_id' => $id ) );
				}
			}
		}
	}
	
	/** Remove term meta and normalize storyline groups.
	 * 
	 * @param integer $id The deleted term ID.
	 * @param integer $tax_id The deleted term taxonomy ID.
	 * @param string $taxonomy The deleted term taxonomy.
	 * @uses Webcomic::$config
	 * @hook delete_term
	 */
	public function delete_term( $id, $tax_id, $taxonomy ) {
		global $wpdb;
		
		if ( 'webcomic_language' === $taxonomy ) {
			foreach ( self::$config[ 'collections' ] as $k => $v ) {
				if ( false !== ( $key = array_search( $id, $v[ 'transcripts' ][ 'languages' ] ) ) ) {
					unset( self::$config[ 'collections' ][ $k ][ 'transcripts' ][ 'languages' ][ $key ] );
				}
			}
			
			update_option( 'webcomic_options', self::$config );
		} elseif ( preg_match( '/^webcomic\d+_(storyline|character)$/', $taxonomy ) ) {
			if ( isset( self::$config[ 'terms' ][ $id ] ) ) {
				delete_post_meta( self::$config[ 'terms' ][ $id ][ 'image' ], '_wp_attachment_context' );
				
				unset( self::$config[ 'terms' ][ $id ] );
				
				update_option( 'webcomic_options', self::$config );
			}
			
			if ( false !== strpos( $taxonomy, '_storyline' ) ) {
				$terms = get_terms( $taxonomy, array( 'get' => 'all', 'cache_domain' => "webcomic_delete_term{$id}" ) );
				$count = array();
				
				foreach ( $terms as $term ) {
					$count[ $term->parent ] = empty( $count[ $term->parent ] ) ? 1 : $count[ $term->parent ] + 1;
					
					$wpdb->update( $wpdb->terms, array( 'term_group' => $count[ $term->parent ] ), array( 'term_id' => $term->term_id ) );
				}
			}
		}
	}
	
	/** Register and enqueue scripts.
	 * 
	 * @uses Webcomic::$url
	 * @hook admin_enqueue_scripts
	 */
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		
		if ( preg_match( '/^admin_page_webcomic-term-sort|edit-webcomic\d+_(storyline|character)$/', $screen->id ) ) {
			wp_register_script( 'jquery-nestedsortable', self::$url . '-/library/jquery.nestedsortable.js', array( 'jquery', 'jquery-ui-sortable' ) );
			wp_register_script( 'webcomic-admin-taxonomy', self::$url . '-/js/admin-taxonomy.js', array( 'jquery-nestedsortable' ) );
			
			wp_enqueue_script( 'webcomic-admin-taxonomy' );
			
			wp_enqueue_media();
		}
		
		if ( 'admin_page_webcomic-term-sort' === $screen->id ) {
			wp_register_style( 'webcomic-admin-taxonomy', self::$url . '-/css/admin-taxonomy.css' );
			
			wp_enqueue_style( 'webcomic-admin-taxonomy' );
		}
	}
	
	/** Force sorting storylines by term_group in the admin dashboard.
	 * 
	 * @param array $args The get_terms arguments array.
	 * @param array $taxonomies The get_terms taxonomies array.
	 * @return array
	 * @hook get_terms_args
	 */
	public function get_terms_args( $args, $taxonomies ) {
		if ( 1 === count( $taxonomies ) and preg_match( '/^webcomic\d+_storyline$/', $taxonomies[ 0 ] ) ) {
			$args[ 'orderby' ] = 'term_group';
		}
		
		return $args;
	}
	
	/** Render link to sort webcomic terms.
	 * 
	 * @hook (webcomid+)_storyline_pre_add_form
	 */
	public function pre_add_form( $taxonomy ) {
		$taxonomy = get_taxonomy( $taxonomy );
		?>
		<div class="webcomic-sort-link">
			<p><a href="<?php echo esc_url( add_query_arg( array( 'page' => 'webcomic-term-sort', 'taxonomy' => $taxonomy->name, 'webcomic_collection' => $_GET[ 'post_type' ] ), admin_url( 'options.php' ) ) ); ?>" class="button"><?php printf( __( 'Sort %s', 'webcomic' ), $taxonomy->label ); ?></a></p>
		</div>
		<?php
	}
	
	/** Render file field for term imagery on the add term form.
	 * 
	 * We have to use a small amount of Javascript to disable the AJAX
	 * submission and ensure any files are actually uploaded.
	 * 
	 * @filter integer webcomic_upload_size_limit Filters the maximum allowed upload size for cover and avatar uploads. Defaults to the value returned by `wp_max_upload_size`.
	 * @param string $taxonomy The add term form taxonomy.
	 * @uses WebcomciTaxonomy::ajax_term_image()
	 * @hook (webcomic\d+)_storyline_add_form_fields
	 * @hook (webcomic\d+)_character_add_form_fields
	 */
	public function add_form_fields( $taxonomy ) {
		$storyline = strstr( $taxonomy, '_storyline' );
		?>
		<div class="form-field">
			<label>
				<?php $storyline ? _e( 'Cover', 'webcomic' ) : _e( 'Avatar', 'webcomic' ); ?>
			</label>
			<div id="webcomic_term_image" data-webcomic-admin-url="<?php echo admin_url(); ?>" data-webcomic-taxonomy="<?php echo $storyline ? 'storyline' : 'character'; ?>"><?php self::ajax_term_image( 0, $storyline ? 'storyline' : 'character', '' ); ?></div>
			<p><?php $storyline ? _e( "The cover is a representative image that can be displayed on your site." ) : _e( "The avatar is a representative image that can be displayed on your site.", 'webcomic' ); ?></p>
		</div>
		<?php
	}
	
	/** Render file field for term imagery on the edit form.
	 * 
	 * We have to use a small amount of Javascript to disable the AJAX
	 * submission and ensure any files are actually uploaded.
	 * 
	 * @filter integer webcomic_upload_size_limit Filters the maximum allowed upload size for cover and avatar uploads. Defaults to the value returned by `wp_max_upload_size`.
	 * @param object $term The current term object.
	 * @param string $taxonomy The taxonomy of the current term.
	 * @hook (webcomic\d+)_storyline_edit_form_fields
	 * @hook (webcomic\d+)_character_edit_form_fields
	 */
	public function edit_form_fields( $term, $taxonomy ) {
		$storyline = strstr( $taxonomy, '_storyline' );
		?>
		<tr class="form-field">
			<th><label for="webcomic_image"><?php $storyline ? _e( 'Cover', 'webcomic' ) : _e( 'Avatar', 'webcomic' ); ?></label></th>
			<td>
				<div id="webcomic_term_image" data-webcomic-admin-url="<?php echo admin_url(); ?>" data-webcomic-taxonomy="<?php echo $storyline ? 'storyline' : 'character'; ?>" data-webcomic-term="<?php echo esc_attr( $term->name ); ?>"><?php self::ajax_term_image( $term->webcomic_image, $storyline ? 'storyline' : 'character', $term->name ); ?></div>
				<p class="description"><?php $storyline ? _e( "The cover is a representative image that can be displayed on your site. Don't forget to <strong>Save Changes</strong> after updating the cover." ) : _e( "The avatar is a representative image that can be displayed on your site. Don't forget to <strong>Save Changes</strong> after updating the avatar.", 'webcomic' ); ?></p>
			</td>
		</tr>
		<?php
	}
	
	/** Render custom taxonomy columns.
	 * 
	 * @param string $value
	 * @param string $column Name of the current column.
	 * @param integer $id Current term ID.
	 * @hook manage_(webcomic\d+)_storyline_custom_column
	 * @hook manage_(webcomic\d+)_character_custom_column
	 */
	public function manage_custom_column( $value, $column, $id ) {
		if ( 'webcomic_image' === $column and isset( self::$config[ 'terms' ][ $id ][ 'image' ] ) and $image = wp_get_attachment_image( self::$config[ 'terms' ][ $id ][ 'image' ] ) ) {
			echo current_user_can( 'edit_post', self::$config[ 'terms' ][ $id ][ 'image' ] ) ? sprintf( '<a href="%s">%s</a>',
				esc_url( add_query_arg( array( 'post' => self::$config[ 'terms' ][ $id ][ 'image' ], 'action' => 'edit' ), admin_url( 'post.php' ) ) ),
				$image
			) : $image;
		}
	}
	
	/** Rename the 'Posts' column and add the image column for storylines.
	 * 
	 * @param array $columns Array of of term columns.
	 * @return array
	 * @hook manage_edit-(webcomic\d+)_storyline_columns
	 */
	public function manage_edit_webcoimc_storyline_columns( $columns ) {
		$pre                     = array_slice( $columns, 0, 1 );
		$pre[ 'webcomic_image' ] = __( 'Cover', 'webcomic' );
		$columns[ 'posts' ]      = __( 'Webcomics', 'webcomic' );
		
		return array_merge( $pre, $columns );
	}
	
	/** Rename the 'Posts' column and add the image column for characters.
	 * 
	 * @param array $columns Array of of term columns.
	 * @return array
	 * @hook manage_edit-(webcomic\d+)_character_columns
	 */
	public function manage_edit_webcoimc_character_columns( $columns ) {
		$pre                     = array_slice( $columns, 0, 1 );
		$pre[ 'webcomic_image' ] = __( 'Avatar', 'webcomic' );
		$columns[ 'posts' ]      = __( 'Webcomics', 'webcomic' );
		
		return array_merge( $pre, $columns );
	}
	
	/** Render the term sorter.
	 * 
	 * @uses Walker_WebcomicTerm_SortList
	 */
	public function page_term_sort() {
		$taxonomy = get_taxonomy( $_GET[ 'taxonomy' ] );
		?>
		<div class="wrap">
			<div class="icon32" id="icon-edit"></div>
			<h2><?php printf( __( 'Sort %s', 'webcomic' ), $taxonomy->label ); ?></h2>
			<form method="post">
				<?php wp_nonce_field( 'webcomic_term_sort', 'webcomic_term_sort' ); ?>
				<input type="hidden" name="webcomic_action" value="term_sort">
				<input type="hidden" name="webcomic_terms" value="">
				<input type="hidden" name="webcomic_taxonomy" value="<?php echo $taxonomy->name; ?>">
				<input type="hidden" name="webcomic_collection" value="<?php echo $_GET[ 'webcomic_collection' ]; ?>">
				<div id="col-container">
					<div id="col-right">
						<div class="col-wrap">
							<ol class="webcomic-sort">
								<?php echo call_user_func( array( new Walker_WebcomicTerm_SortList, 'walk' ), get_terms( $taxonomy->name, array( 'get' => 'all' ) ), 0, array() ) ?>
							</ol>
						</div>
					</div>
					<div id="col-left">
						<div class="col-wrap">
							<p><?php _e( 'Drag and drop the terms to change their order.', 'webcomic' ) ?></p>
							<p>
								<?php submit_button( '', 'primary', '', false ); ?>
								<?php submit_button( __( 'Cancel', 'webcomic' ), 'secondary', 'webcomic_cancel_sort', false ); ?>
							</p>
						</div>
					</div>
				</div>
			</form>
		</div>
		<?php
	}
	
	/** Handle term image updating.
	 * 
	 * @param integer $id ID of the selected image.
	 * @param string $taxonomy Collection the poster is for.
	 */
	public static function ajax_term_image( $id, $taxonomy, $term ) {
		$choose = 'storyline' === $taxonomy ? __( 'a Cover', 'webcomic' ) : __( 'an Avatar', 'webcomic' );
		
		if ( $id ) {
			printf( '<a href="%s">%s</a><br>',
				esc_url( add_query_arg( array( 'post' => $id, 'action' => 'edit' ), admin_url( 'post.php' ) ) ),
				wp_get_attachment_image( $id )
			);
		}
		
		printf( '<input type="hidden" name="webcomic_image" value="%s"><a class="button webcomic-term-image" data-title="%s" data-update="%s">%s</a>',
			$id,
			$term ? sprintf( __( 'Choose %s for %s', 'webcomic' ), $choose, esc_html( $term ) ) : sprintf( __( 'Choose %s', 'webcomic' ), $choose ),
			__( 'Update', 'webcomic' ),
			$id ? __( 'Change', 'webcomic' ) : __( 'Select', 'webcomic' )
		);
		
		if ( $id ) {
			printf( ' <a class="button webcomic-term-image-x">%s</a>', __( 'Remove', 'webcomic' ) );
		}
	}
}

/** Handle sorting list output.
 * 
 * @package Webcomic
 */
class Walker_WebcomicTerm_SortList extends Walker {
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
		$output .= '<ol>';
	}
	
	/** End level output.
	 * 
	 * @param string $output Walker output string.
	 * @param integer $depth Depth the walker is currently at.
	 * @param array $args Arguments passed to the walker.
	 */
	public function end_lvl( &$output, $depth, $args ) {
		$output .= '</ol>';
	}
	
	/** Start element output.
	 * 
	 * @param string $output Walker output string.
	 * @param object $term Current term being handled by the walker.
	 * @param integer $depth Depth the walker is currently at.
	 * @param array $args Arguments passed to the walker.
	 */
	public function start_el( &$output, $term, $depth, $args ) {
		extract( $args, EXTR_SKIP );
		
		$output .= sprintf( '<li id="term_%s"><b><i>%s</i>%s</b>',
			$term->term_id,
			$term->webcomic_image ? wp_get_attachment_image( $term->webcomic_image, array( 36, 0 ) ) : '',
			$term->name
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
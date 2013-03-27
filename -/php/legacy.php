<?php
/** Contains the WebcomicLegacy class.
 * 
 * @package Webcomic
 */

/** Upgrade legacy Webcomic installations.
 * 
 * @package Webcomic
 */
class WebcomicLegacy extends Webcomic {
	/** Stores the maximum safe script execution time.
	 * @var integer
	 */
	private $limit;
	
	/** Register action and filter hooks.
	 * 
	 * Also sets the maximum "safe" script execution time, defined here
	 * as the `max_execution_time` minus a 10% buffer.
	 * 
	 * @uses WebcomicLegacy::init()
	 * @uses WebcomicLegacy::admin_init()
	 * @uses WebcomicLegacy::admin_menu()
	 * @uses WebcomicLegacy::admin_notices()
	 * @uses WebcomicLegacy::admin_enqueue_scripts()
	 * @uses WebcomicLegacy::list_term_exclusions()
	 */
	public function __construct() {
		$this->limit = ( integer ) ini_get( 'max_execution_time' ) - ceil( ( integer ) ini_get( 'max_execution_time' ) * .1 );
		
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'list_term_exclusions', array( $this, 'list_term_exclusions' ), 10, 2 );
	}
	
	/** Register legacy post types and taxonomies.
	 * 
	 * @uses Webcomic::$config
	 */
	public function init() {
		if ( 3 === self::$config[ 'legacy' ] ) {
			register_post_type( 'webcomic_post', array(
				'label'      => __( 'Webcomic 3', 'webcomic' ),
				'public'     => true,
				'taxonomies' => array( 'category', 'post_tag' ),
			) );
			
			register_taxonomy( 'webcomic_collection', array( 'webcomic_post' ), array(
				'label'        => __( 'Collections', 'webcomic' ),
				'hierarchical' => true
			) );
				
			register_taxonomy( 'webcomic_storyline', array( 'webcomic_post' ), array(
				'label'        => __( 'Storylines', 'webcomic' ),
				'hierarchical' => true
			) );
			
			register_taxonomy( 'webcomic_character', array( 'webcomic_post' ), array(
				'label' => 'Characters',
			) );
		} elseif ( is_numeric( self::$config[ 'legacy' ] ) ) {
			register_taxonomy( 'chapter', 'post', array(
				'label' => sprintf( __( 'Webcomic %s Chapters', 'webcomic' ), self::$config[ 'legacy' ] ),
				'hierarchical' => true
			) );
		}
	}
	
	/** Disable or upgrade the plugin.
	 * 
	 * @uses Webcomic::$dir
	 * @uses Webcomic::$config
	 * @uses WebcomicLegacy::upgrade1()
	 * @uses WebcomicLegacy::upgrade2()
	 * @uses WebcomicLegacy::upgrade3()
	 * @uses WebcomicLegacy::upgrade_comicpress()
	 * @hook admin_init
	 */
	public function admin_init() {
		if ( isset( $_POST[ 'webcomic_upgrade' ] ) and wp_verify_nonce( $_POST[ 'webcomic_upgrade' ], 'webcomic_upgrade' ) ) {
			if ( isset( $_POST[ 'disable_legacy' ] ) ) {
				$file = plugin_basename( self::$dir . '/webcomic.php' );
				
				self::$config[ 'uninstall' ] = true;
				
				update_option( 'webcomic_options', self::$config );
				
				wp_redirect( html_entity_decode( wp_nonce_url( add_query_arg( array( 'action' => 'deactivate', 'plugin' => $file ), admin_url( 'plugins.php' ) ), 'deactivate-plugin_' . $file ) ) );
				
				die;
			} elseif ( isset( $_POST[ 'upgrade_legacy' ] ) ) {
				$stage = !empty( $_POST[ 'webcomic_upgrade_stage' ] ) ? $_POST[ 'webcomic_upgrade_stage' ] : 0;
				
				if ( !empty( $_POST[ 'webcomic_upgrade_complete' ] ) ) {
					unset( self::$config[ 'legacy' ] );
					
					delete_option( 'webcomic_legacy' );
					
					update_option( 'webcomic_options', self::$config );
					
					flush_rewrite_rules();
					
					wp_redirect( admin_url() );
					
					die;
				} elseif ( 3 === self::$config[ 'legacy' ] ) {
					$_POST[ 'webcomic_upgrade_status' ] = self::upgrade3( $stage );
				} elseif ( 2 === self::$config[ 'legacy' ] ) {
					$_POST[ 'webcomic_upgrade_status' ] = self::upgrade2( $stage );
				} elseif ( 1 === self::$config[ 'legacy' ] ) {
					$_POST[ 'webcomic_upgrade_status' ] = self::upgrade1( $stage );
				} elseif ( 'ComicPress' === self::$config[ 'legacy' ] ) {
					$_POST[ 'webcomic_upgrade_status' ] = self::upgrade_comicpress( $stage );
				}
			}
		}
	}
	
	/** Register submenu page for legacy upgrades.
	 * 
	 * @uses WebcomicLegacy::page()
	 * @hook admin_menu
	 */
	public function admin_menu() {
		add_submenu_page( 'tools.php', sprintf( __( 'Upgrade to %s', 'webcomic' ), 'Webcomic ' . self::$version ), __( 'Upgrade Webcomic', 'webcomic' ), 'manage_options', 'webcomic-upgrader', array( $this, 'page' ) );
	}
	
	/** Render upgrade tool notification.
	 * 
	 * @return null
	 * @uses Webcomic::$config
	 * @hook admin_notices
	 */
	public function admin_notices() {
		if ( isset( self::$config[ 'legacy_notice' ] ) ) {
			$screen = get_current_screen();
			
			if ( 'tools_page_webcomic-upgrader' === $screen->id ) {
				unset( self::$config[ 'legacy_notice' ] );
				
				update_option( 'webcomic_options', self::$config );
				
				return;
			}
			
			echo '<div class="updated webcomic legacy"><a href="', esc_url( add_query_arg( array( 'page' => 'webcomic-upgrader' ), admin_url( 'tools.php' ) ) ), '"><b>&#x2605;</b> ', __( 'Upgrading? Let Webcomic Help', 'webcomic' ),'</a></div>';
		}
	}
	
	/** Enqueue custom styles for upgrade notice.
	 * 
	 * @uses Webcomic::$url
	 * @uses Webcomic::$config
	 * @hook admin_enqueue_scripts
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_script( 'webcomic-legacy', self::$url . '-/js/admin-legacy.js', array( 'jquery' ) );
		
		if ( isset( self::$config[ 'legacy_notice' ] ) ) {
			wp_register_style( 'webcomic-google-font', 'http://fonts.googleapis.com/css?family=Maven+Pro' );
			
			wp_enqueue_style( 'webcomic-special', self::$url . '-/css/admin-special.css', array( 'webcomic-google-font' ) );
		}
	}
	
	/** Allow retrieving terms by term_group.
	 * 
	 * @param string $exclusions The WHERE clause used by get_terms.
	 * @param array $args The array of arguments passed to get_terms.
	 * @return string
	 */
	public function list_terms_exclusions( $exclusions, $args ) {
		if ( !empty( $args[ 'term_group' ] ) ) {
			$exclusions .= " AND ( t.term_group = {$args[ 'term_group' ]} )";
		}
		
		return $exclusions;
	}
	
	/** Render the upgrade tool page.
	 * 
	 * @uses Webcomic::$config
	 * @uses Webcomic::$version
	 */
	public function page() {
		?>
		<div class="wrap">
			<div id="icon-tools" class="icon32"></div>
			<h2><?php echo get_admin_page_title(); ?></h2>
			<div>
				<div class="col-wrap">
					<?php if ( isset( $_POST[ 'webcomic_upgrade_status' ] ) and 0 === $_POST[ 'webcomic_upgrade_status' ] ) { ?>
					
					<h3 style="color:green;font-size:larger"><?php _e( "Just one more click!", 'webcomic' ); ?></h3>
					<p><?php printf( __( 'Thanks again for using Webcomic! Clicking <b>Complete the Upgrade</b> will remove the upgrade tool, delete any leftover data, and take you to the administrative dashboard. If you notice any problems with the upgrade please <a href="%s" target="_blank">let the developer know</a>.', 'webcomic' ), '//github.com/mgsisk/webcomic/issues' ); ?></p>
					<form method="post">
						<?php wp_nonce_field( 'webcomic_upgrade', 'webcomic_upgrade' ); ?>
						<div class="form-wrap">
							<?php submit_button( __( 'Complete the Upgrade', 'webcomic' ), 'primary', 'upgrade_legacy', false ); ?>
							<input type="hidden" name="webcomic_upgrade_complete" value="1">
						</div>
					</form>
					
					<?php } elseif ( isset( $_POST[ 'webcomic_upgrade_status' ] ) ) { ?>
					
					<p style="color:#e66f00;font-size:larger"><b><?php _e( 'Webcomic has paused the upgrade to prevent a timeout error.', 'webcomic' ); ?></b></p>
					<p><?php _e( 'The upgrade will automatically resume in 5 seconds, or you may click <b>Continue Upgrading</b> to resume now.', 'webcomic' ); ?></p>
					<form method="post" class="webcomic-auto" data-webcomic-upgrade-continue="<?php _e( 'Continuing upgrade&hellip;', 'webcomic' ); ?>">
						<?php wp_nonce_field( 'webcomic_upgrade', 'webcomic_upgrade' ); ?>
						<div class="form-wrap">
							<?php submit_button( __( 'Continue Upgrading', 'webcomic' ), 'primary', 'upgrade_legacy', false ); ?>
							<input type="hidden" name="webcomic_upgrade_stage" value="<?php echo $_POST[ 'webcomic_upgrade_status' ]; ?>">
							<div class="webcomic-auto-message" style="font-size:larger;font-weight:bold;"></div>
						</div>
					</form>
					
					<?php } else { ?>
					
					<p><?php printf( __( 'This tool will attempt to automatically convert your existing %1$s data to Webcomic %2$s. Depending on the size of your site the upgrade may require multiple steps. If you do not want to upgrade click <b>Not Interested</b> to uninstall Webcomic %2$s.', 'webcomic' ), is_numeric( self::$config[ 'legacy' ] ) ? 'Webcomic %s' . self::$config[ 'legacy' ] : self::$config[ 'legacy' ], self::$version ); ?></p>
					<div class="error"><p><b><?php printf( __( 'Upgrades are not reversible and, once begun, should not be stopped. Please <a href="%1$s" target="_blank">read this</a> and <a href="%2$s">backup your site</a> before upgrading.', 'webcomic' ), '//github.com/mgsisk/webcomic/wiki/Upgrading', esc_url( admin_url( 'export.php' ) ) ); ?></b></p></div>
					<form method="post">
						<?php wp_nonce_field( 'webcomic_upgrade', 'webcomic_upgrade' ); ?>
						<div class="form-wrap">
							<?php submit_button( __( 'Upgrade Now', 'webcomic' ), 'primary', 'upgrade_legacy', false ); ?>
							<span style="float:right"><?php submit_button( __( 'Not Interested', 'webcomic' ), 'secondary', 'disable_legacy', false ); ?></span>
						</div>
					</form>
					
					<?php } ?>
				</div>
			</div>
		</div>
		<?php
	}
	
	/** Upgrade Webcomic 1 installations.
	 * 
	 * @return integer
	 * @uses Webcomic::$config
	 * @uses WebcomicLegacy::legacy_path()
	 * @uses WebcomicLegacy::update_media_library()
	 */
	private function upgrade1( $stage = 0 ) {
		global $wpdb;
		
		$start         = microtime( true );
		$admin_email   = get_bloginfo( 'admin_email' );
		$legacy_config = get_option( 'webcomic_legacy' );
		
		if ( !$stage ) {
			self::$config[ 'increment' ] = 1;
			
			if ( get_option( 'thumbnail_size_w' ) !== $legacy_config[ 'comic_thumbnail_size_w' ] or get_option( 'thumbnail_size_h' ) !== $legacy_config[ 'comic_thumbnail_size_h' ] ) {
				update_option( 'thumbnail_size_w', $legacy_config[ 'comic_thumbnail_size_w' ] );
				update_option( 'thumbnail_size_h', $legacy_config[ 'comic_thumbnail_size_h' ] );
			}
			
			if ( get_option( 'medium_size_w' ) !== $legacy_config[ 'comic_medium_size_w' ] or get_option( 'medium_size_h' ) !== $legacy_config[ 'comic_medium_size_h' ] ) {
				update_option( 'medium_size_w', $legacy_config[ 'comic_medium_size_w' ] );
				update_option( 'medium_size_h', $legacy_config[ 'comic_medium_size_h' ] );
			}
			
			if ( get_option( 'large_size_w' ) !== $legacy_config[ 'comic_large_size_w' ] or get_option( 'large_size_h' ) !== $legacy_config[ 'comic_large_size_h' ] ) {
				update_option( 'large_size_w', $legacy_config[ 'comic_large_size_w' ] );
				update_option( 'large_size_h', $legacy_config[ 'comic_large_size_h' ] );
			}
			
			update_option( 'webcomic_options', self::$config );
			
			$stage++;
		}
		
		if ( $terms = get_terms( 'category', array( 'get' => 'all', 'include' => $legacy_config[ 'comic_category' ], 'orderby' => 'id' ) ) and !is_wp_error( $terms ) ) {
			foreach ( $terms as $k => $v ) {
				$collection_id = 'webcomic' . self::$config[ 'increment' ];
				
				if ( 1 === intval( $stage ) ) {
					self::$config[ 'collections' ][ $collection_id ] = array(
						'id'          => $collection_id,
						'name'        => $v->name,
						'image'       => 0,
						'theme'       => '',
						'updated'     => 0,
						'supports'    => array( 'title', 'editor', 'author', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions' ),
						'taxonomies'  => array( 'category', 'post_tag' ),
						'description' => $v->description,
						'feeds' => array(
							'hook' => ( boolean ) $legacy_config[ 'comic_feed' ],
							'size' => $legacy_config[ 'comic_feed_size' ],
							'main' => true
						),
						'slugs' => array(
							'name'      => $v->slug,
							'archive'   => $v->slug,
							'webcomic'  => $v->slug,
							'storyline' => "{$v->slug}-storyline",
							'character' => "{$v->slug}-character"
						),
						'buffer' => array(
							'hook'  => true,
							'days'  => 7,
							'email' => $admin_email
						),
						'access' => array(
							'byage'  => false,
							'byrole' => false,
							'age'    => 18,
							'roles'  => array( '!' )
						),
						'twitter' => array(
							'media'           => false,
							'format'          => __( '%collection-name has updated! %site-url', 'webcomic' ),
							'oauth_token'     => '',
							'oauth_secret'    => '',
							'consumer_key'    => '',
							'consumer_secret' => '',
							'request_token'   => '',
							'request_secret'  => ''
						),
						'commerce' => array(
							'business'  => '',
							'currency'  => 'USD',
							'prints'    => false,
							'originals' => false,
							'method'    => '_xclick',
							'donation'  => 0,
							'price'     => array(
								'domestic'      => 6,
								'international' => 9,
								'original'      => 12
							),
							'shipping' => array(
								'domestic'      => 4,
								'international' => 6,
								'original'      => 8
							),
							'total' => array(
								'domestic'      => 10,
								'international' => 15,
								'original'      => 20
							)
						),
						'transcripts' => array(
							'open'       => false,
							'languages'  => array( '!' ),
							'permission' => 'register',
							'notify'     => array(
								'hook'  => true,
								'email' => $admin_email
							)
						)
					);
					
					update_option( 'webcomic_options', self::$config );
					
					$stage++;
				}
				
				if ( 2 === intval( $stage ) ) {
					if ( $chapters = get_terms( 'chapter', array( 'fields' => 'ids', 'orderby' => 'term_group', 'child_of' => $v->term_id, 'get' => 'all' ) ) and !is_wp_error( $chapters ) ) {
						$count = array();
						
						foreach ( $chapters as $chapter ) {
							$chapter = get_term( $chapter, 'chapter' );
							
							$wpdb->update( $wpdb->terms, array( 'term_group' => isset( $count[ $chapter->parent ] ) ? $count[ $chapter->parent ] : 0 ), array( 'term_id' => $chapter->term_id ) );
							$wpdb->update( $wpdb->term_taxonomy, array( 'taxonomy' => "{$collection_id}_storyline" ), array( 'term_id' => $chapter->term_id, 'taxonomy' => 'chapter' ) );
							
							if ( empty( $count[ $chapter->parent ] ) ) {
								$count[ $chapter->parent ] = 0;
							}
							
							$count[ $chapter->parent ]++;
							
							if ( microtime( true ) - $start >= $this->limit ) {
								return $stage;
							}
						}
					}
					
					$stage++;
				}
				
				if ( 3 === intval( $stage ) ) {
					$meta_files = ( array ) glob( self::legacy_path( $legacy_config[ 'comic_directory' ], 1 < count( $legacy_config[ 'comic_category' ] ) ? $v->term_id : '' ) . '*.*' );
					
					if ( $posts = get_posts( array(
						'fields'      => 'ids',
						'numberposts' => -1,
						'post_type'   => 'post',
						'post_status' => get_post_stati(),
						'order'       => 'ASC',
						'tax_query'   => array( array(
							'taxonomy' => 'category',
							'field'    => 'id',
							'terms'    => $v->term_id
						) )
					) ) ) {
						foreach ( $posts as $post ) {
							if ( 'date' === $legacy_config[ 'comic_name_format' ] ) {
								$format = get_the_time( $legacy_config[ 'comic_name_format_date' ], $post );
							} elseif ( 'slug' === $legacy_config[ 'comic_name_format' ] ) {
								$format = '';
							} else {
								$format = get_post_meta( $post, 'comic_filename', true );
							}
							
							$meta_file = preg_grep( "/{$format}/", $meta_files );
							
							if ( !empty( $meta_file ) ) {
								self::update_media_library( $meta_file[ 0 ], ( integer ) $post, array(
									'post_excerpt' => ( $meta_description = get_post_meta( $post, 'comic_description', true ) ) ? $meta_description : ''
								) );
							}
							
							if ( $meta_transcript = get_post_meta( $post, 'comic_transcript', true ) ) {
								$date  = get_the_time( 'Y-m-d H:i:s', $post );
								$title = sprintf( __( '%s Transcript', 'webcomic' ), get_the_title( $post ) );
								
								wp_insert_post( array(
									'post_name'     => sanitize_title( $title ),
									'post_type'     => 'webcomic_transcript',
									'post_date'     => $date,
									'post_title'    => $title,
									'post_author'   => 1,
									'post_parent'   => $post,
									'post_status'   => 'publish',
									'post_content'  => $meta_transcript,
									'post_date_gmt' => get_gmt_from_date( $date )
								) );
							}
							
							update_post_meta( $post, 'webcomic_prints', false );
							
							update_post_meta( $post, 'webcomic_original', false );
							
							update_post_meta( $post, 'webcomic_transcripts', true );
							
							update_post_meta( $post, 'webcomic_commerce', array(
								'price' => array(
									'domestic'      => 6,
									'international' => 9,
									'original'      => 12
								),
								'shipping' => array(
									'domestic'      => 4,
									'international' => 6,
									'original'      => 8
								),
								'total'  => array(
									'domestic'      => 10,
									'international' => 15,
									'original'      => 20
								),
								'adjust' => array(
									'price' => array(
										'domestic'      => 0,
										'international' => 0,
										'original'      => 0
									),
									'shipping' => array(
										'domestic'      => 0,
										'international' => 0,
										'original'      => 0
									),
									'total'  => array(
										'domestic'      => 0,
										'international' => 0,
										'original'      => 0
									)
								)
							) );
							
							delete_post_meta( $post, 'comic_filename' );
							delete_post_meta( $post, 'comic_transcript' );
							delete_post_meta( $post, 'comic_description' );
							
							$wpdb->update( $wpdb->posts, array( 'post_type' => $collection_id ), array( 'ID' => ( integer ) $post ) );
							
							self::$config[ 'collections' ][ $collection_id ][ 'updated' ] = get_the_time( 'U', $post );
							
							update_option( 'webcomic_options', self::$config );
							
							if ( microtime( true ) - $start >= $this->limit ) {
								return $stage;
							}
						}
					}
				}
				
				$stage = 1;
				
				self::$config[ 'increment' ]++;
				
				update_option( 'webcomic_options', self::$config );
				
				if ( false !== ( $key = array_search( $v->term_id, $legacy_config[ 'comic_category' ] ) ) ) {
					unset( $legacy_config[ 'comic_category' ][ $key ] );
					
					update_option( 'webcomic_legacy', $legacy_config );
				}
				
				if ( microtime( true ) - $start >= $this->limit ) {
					return $stage;
				}
			}
		}
		
		return 0;
	}
	
	/** Upgrade Webcomic 2 installations.
	 * 
	 * @return integer
	 * @uses Webcomic::$config
	 * @uses WebcomicLegacy::legacy_path()
	 * @uses WebcomicLegacy::update_media_library()
	 */
	private function upgrade2( $stage = 0 ) {
		global $wpdb;
		
		$start         = microtime( true );
		$admin_email   = get_bloginfo( 'admin_email' );
		$legacy_config = get_option( 'webcomic_legacy' );
		
		if ( $legacy_config[ 'comic_transcripts_loggedin' ] ) {
			$transcripts_permission = 'register';
		} elseif ( $legacy_config[ 'comic_transcripts_required' ] ) {
			$transcripts_permission = 'identify';
		} else {
			$transcripts_permission = 'everyone';
		}
		
		if ( !$stage ) {
			self::$config[ 'increment' ] = 1;
			self::$config[ 'shortcuts' ] = ( boolean ) $legacy_config[ 'comic_keyboard_shortcuts' ];
			
			if ( get_option( 'thumbnail_size_w' ) !== $legacy_config[ 'comic_thumb_size_w' ] or get_option( 'thumbnail_size_h' ) !== $legacy_config[ 'comic_thumb_size_h' ] ) {
				update_option( 'thumbnail_size_w', $legacy_config[ 'comic_thumb_size_w' ] );
				update_option( 'thumbnail_size_h', $legacy_config[ 'comic_thumb_size_h' ] );
			}
			
			if ( get_option( 'medium_size_w' ) !== $legacy_config[ 'comic_medium_size_w' ] or get_option( 'medium_size_h' ) !== $legacy_config[ 'comic_medium_size_h' ] ) {
				update_option( 'medium_size_w', $legacy_config[ 'comic_medium_size_w' ] );
				update_option( 'medium_size_h', $legacy_config[ 'comic_medium_size_h' ] );
			}
			
			if ( get_option( 'large_size_w' ) !== $legacy_config[ 'comic_large_size_w' ] or get_option( 'large_size_h' ) !== $legacy_config[ 'comic_large_size_h' ] ) {
				update_option( 'large_size_w', $legacy_config[ 'comic_large_size_w' ] );
				update_option( 'large_size_h', $legacy_config[ 'comic_large_size_h' ] );
			}
			
			update_option( 'webcomic_options', self::$config );
			
			$stage++;
		}
		
		if ( $terms = get_terms( 'category', array( 'get' => 'all', 'include' => $legacy_config[ 'comic_category' ], 'orderby' => 'id' ) ) and !is_wp_error( $terms ) ) {
			foreach ( $terms as $k => $v ) {
				$collection_id = 'webcomic' . self::$config[ 'increment' ];
				
				if ( 1 === intval( $stage ) ) {
					self::$config[ 'collections' ][ $collection_id ] = array(
						'id'          => $collection_id,
						'name'        => $v->name,
						'image'       => 0,
						'theme'       => '',
						'updated'     => 0,
						'supports'    => array( 'title', 'editor', 'author', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions' ),
						'taxonomies'  => array( 'category', 'post_tag' ),
						'description' => $v->description,
						'feeds' => array(
							'hook' => ( boolean ) $legacy_config[ 'comic_feed' ],
							'size' => $legacy_config[ 'comic_feed_size' ],
							'main' => true
						),
						'slugs' => array(
							'name'      => $v->slug,
							'archive'   => $v->slug,
							'webcomic'  => $v->slug,
							'storyline' => "{$v->slug}-storyline",
							'character' => "{$v->slug}-character"
						),
						'buffer' => array(
							'hook'  => true,
							'days'  => 7,
							'email' => $admin_email
						),
						'access' => array(
							'byage'  => false,
							'byrole' => false,
							'age'    => 18,
							'roles'  => array( '!' )
						),
						'twitter' => array(
							'media'           => false,
							'format'          => __( '%collection-name has updated! %site-url', 'webcomic' ),
							'oauth_token'     => '',
							'oauth_secret'    => '',
							'consumer_key'    => '',
							'consumer_secret' => '',
							'request_token'   => '',
							'request_secret'  => ''
						),
						'commerce' => array(
							'business'  => '',
							'currency'  => 'USD',
							'prints'    => false,
							'originals' => false,
							'method'    => '_xclick',
							'donation'  => 0,
							'price'     => array(
								'domestic'      => 6,
								'international' => 9,
								'original'      => 12
							),
							'shipping' => array(
								'domestic'      => 4,
								'international' => 6,
								'original'      => 8
							),
							'total' => array(
								'domestic'      => 10,
								'international' => 15,
								'original'      => 20
							)
						),
						'transcripts' => array(
							'open'       => ( boolean ) $legacy_config[ 'comic_transcripts_allowed' ],
							'languages'  => array( '!' ),
							'permission' => $transcripts_permission,
							'notify'     => array(
								'hook'  => true,
								'email' => $admin_email
							)
						)
					);
					
					update_option( 'webcomic_options', self::$config );
					
					$stage++;
				}
				
				if ( 2 === intval( $stage ) ) {
					if ( $chapters = get_terms( 'chapter', array( 'fields' => 'ids', 'orderby' => 'term_group', 'term_group' => $v->term_id, 'get' => 'all' ) ) and !is_wp_error( $chapters ) ) {
						$count = array();
						
						foreach ( $chapters as $chapter ) {
							$chapter = get_term( $chapter, 'chapter' );
							
							$wpdb->update( $wpdb->terms, array( 'term_group' => isset( $count[ $chapter->parent ] ) ? $count[ $chapter->parent ] : 0 ), array( 'term_id' => $chapter->term_id ) );
							$wpdb->update( $wpdb->term_taxonomy, array( 'taxonomy' => "{$collection_id}_storyline" ), array( 'term_id' => $chapter->term_id, 'taxonomy' => 'chapter' ) );
							
							if ( empty( $count[ $chapter->parent ] ) ) {
								$count[ $chapter->parent ] = 0;
							}
							
							$count[ $chapter->parent ]++;
							
							if ( microtime( true ) - $start >= $this->limit ) {
								return $stage;
							}
						}
					}
					
					$stage++;
				}
				
				if ( 3 === intval( $stage ) ) {
					if ( $posts = get_posts( array(
						'fields'      => 'ids',
						'numberposts' => -1,
						'post_type'   => 'post',
						'post_status' => get_post_stati(),
						'tax_query'   => array( array(
							'taxonomy' => 'category',
							'field'    => 'id',
							'terms'    => $v->term_id
						) )
					) ) ) {
						foreach ( $posts as $post ) {
							if ( $meta_file = get_post_meta( $post, 'comic_file', true ) ) {
								self::update_media_library( self::legacy_path( $legacy_config[ 'comic_directory' ], $v->slug ) . $meta_file, ( integer ) $post, array(
									'post_excerpt' => ( $meta_description = get_post_meta( $post, 'comic_description', true ) ) ? $meta_description : ''
								) );
							}
							
							if ( $meta_transcript = get_post_meta( $post, 'comic_transcript', true ) or $meta_transcript = get_post_meta( $post, 'comic_transcript_pending', true ) or $meta_transcript = get_post_meta( $post, 'comic_transcript_draft', true ) ) {
								$date  = get_the_time( 'Y-m-d H:i:s', $v[ 'time' ] );
								$title = sprintf( __( '%s Transcript', 'webcomic' ), get_the_title( $post ) );
								
								if ( get_post_meta( $post, 'comic_transcript', true ) ) {
									$status = 'publish';
								} elseif ( get_post_meta( $post, 'comic_transcript_pending', true ) ) {
									$status = 'pending';
								} else {
									$status = 'draft';
								}
								
								wp_insert_post( array(
									'post_name'     => sanitize_title( $title ),
									'post_type'     => 'webcomic_transcript',
									'post_date'     => $date,
									'post_title'    => $title,
									'post_author'   => 1,
									'post_parent'   => $post,
									'post_status'   => $status,
									'post_content'  => $meta_transcript,
									'post_date_gmt' => get_gmt_from_date( $date )
								) );
							}
							
							update_post_meta( $post, 'webcomic_prints', false );
							
							update_post_meta( $post, 'webcomic_original', false );
							
							update_post_meta( $post, 'webcomic_transcripts', ( boolean ) $legacy_config[ 'comic_transcripts_allowed' ] );
							
							update_post_meta( $post, 'webcomic_commerce', array(
								'price' => array(
									'domestic'      => 6,
									'international' => 9,
									'original'      => 12
								),
								'shipping' => array(
									'domestic'      => 4,
									'international' => 6,
									'original'      => 8
								),
								'total'  => array(
									'domestic'      => 10,
									'international' => 15,
									'original'      => 20
								),
								'adjust' => array(
									'price' => array(
										'domestic'      => 0,
										'international' => 0,
										'original'      => 0
									),
									'shipping' => array(
										'domestic'      => 0,
										'international' => 0,
										'original'      => 0
									),
									'total'  => array(
										'domestic'      => 0,
										'international' => 0,
										'original'      => 0
									)
								)
							) );
							
							delete_post_meta( $post, 'comic_file' );
							delete_post_meta( $post, 'comic_large' );
							delete_post_meta( $post, 'comic_thumb' );
							delete_post_meta( $post, 'comic_medium' );
							delete_post_meta( $post, 'comic_transcript' );
							delete_post_meta( $post, 'comic_description' );
							delete_post_meta( $post, 'comic_transcript_draft' );
							delete_post_meta( $post, 'comic_transcript_pending' );
							
							$wpdb->update( $wpdb->posts, array( 'post_type' => $collection_id ), array( 'ID' => ( integer ) $post ) );
							
							self::$config[ 'collections' ][ $collection_id ][ 'updated' ] = get_the_time( 'U', $post );
							
							update_option( 'webcomic_options', self::$config );
							
							if ( microtime( true ) - $start >= $this->limit ) {
								return $stage;
							}
						}
					}
				}
				
				$stage = 1;
				
				self::$config[ 'increment' ]++;
				
				update_option( 'webcomic_options', self::$config );
				
				if ( false !== ( $key = array_search( $v->term_id, $legacy_config[ 'comic_category' ] ) ) ) {
					unset( $legacy_config[ 'comic_category' ][ $key ] );
					
					update_option( 'webcomic_legacy', $legacy_config );
				}
				
				if ( microtime( true ) - $start >= $this->limit ) {
					return $stage;
				}
			}
		}
		
		return 0;
	}
	
	/** Upgrade Webcomic 3 installations.
	 * 
	 * @return integer
	 * @uses Webcomic::$config
	 * @uses WebcomicLegacy::update_media_library()
	 */
	private function upgrade3( $stage = 0 ) {
		global $wpdb;
		
		$start         = microtime( true );
		$upload_dir    = wp_upload_dir();
		$admin_email   = get_bloginfo( 'admin_email' );
		$legacy_config = get_option( 'webcomic_legacy' );
		
		if ( 'login' === $legacy_config[ 'transcribe_restrict' ] ) {
			$transcripts_permission = 'register';
		} elseif ( 'selfid' === $legacy_config[ 'transcribe_restrict' ] ) {
			$transcripts_permission = 'identify';
		} else {
			$transcripts_permission = 'everyone';
		}
		
		if ( !$stage ) {
			self::$config[ 'increment' ] = 1;
			self::$config[ 'integrate' ] = ( boolean ) $legacy_config[ 'integrate_toggle' ];
			self::$config[ 'shortcuts' ] = ( boolean ) $legacy_config[ 'shortcut_toggle' ];
			
			if ( get_option( 'thumbnail_size_w' ) !== $legacy_config[ 'small_w' ] or get_option( 'thumbnail_size_h' ) !== $legacy_config[ 'small_h' ] ) {
				update_option( 'thumbnail_size_w', $legacy_config[ 'small_w' ] );
				update_option( 'thumbnail_size_h', $legacy_config[ 'small_h' ] );
			}
			
			if ( get_option( 'medium_size_w' ) !== $legacy_config[ 'medium_w' ] or get_option( 'medium_size_h' ) !== $legacy_config[ 'medium_h' ] ) {
				update_option( 'medium_size_w', $legacy_config[ 'medium_w' ] );
				update_option( 'medium_size_h', $legacy_config[ 'medium_h' ] );
			}
			
			if ( get_option( 'large_size_w' ) !== $legacy_config[ 'large_w' ] or get_option( 'large_size_h' ) !== $legacy_config[ 'large_h' ] ) {
				update_option( 'large_size_w', $legacy_config[ 'large_w' ] );
				update_option( 'large_size_h', $legacy_config[ 'large_h' ] );
			}
			
			foreach ( ( array ) $legacy_config[ 'transcribe_language' ] as $k => $v ) {
				wp_insert_term( $v, 'webcomic_language', array( 'slug' => $k ) );
			}
			
			update_option( 'webcomic_options', self::$config );
			
			$stage++;
		}
		
		if ( $terms = get_terms( 'webcomic_collection', array( 'get' => 'all', 'orderby' => 'id' ) ) and !is_wp_error( $terms ) ) {
			foreach ( $terms as $k => $v ) {
				$collection_id = 'webcomic' . self::$config[ 'increment' ];
				
				if ( 1 === intval( $stage ) ) {
					$image_id                        = empty( $legacy_config[ 'term_meta' ][ 'collection' ][ $v->term_id ][ 'files' ][ 'full' ][ 0 ] ) ? 0 : self::update_media_library( dirname( $upload_dir[ 'basedir' ] ) . "/webcomic/{$v->slug}/" . $legacy_config[ 'term_meta' ][ 'collection' ][ $v->term_id ][ 'files' ][ 'full' ][ 0 ], $collection_id );
					$commerce_domestic_price         = round( $legacy_config[ 'paypal_price_d' ] * ( 1 + .01 * $legacy_config[ 'term_meta' ][ 'collection' ][ $v->term_id ][ 'paypal' ][ 'price_d' ] ), 2 );
					$commerce_international_price    = round( $legacy_config[ 'paypal_price_i' ] * ( 1 + .01 * $legacy_config[ 'term_meta' ][ 'collection' ][ $v->term_id ][ 'paypal' ][ 'price_i' ] ), 2 );
					$commerce_original_price         = round( $legacy_config[ 'paypal_price_o' ] * ( 1 + .01 * $legacy_config[ 'term_meta' ][ 'collection' ][ $v->term_id ][ 'paypal' ][ 'price_o' ] ), 2 );
					$commerce_domestic_shipping      = round( $legacy_config[ 'paypal_shipping_d' ] * ( 1 + .01 * $legacy_config[ 'term_meta' ][ 'collection' ][ $v->term_id ][ 'paypal' ][ 'shipping_d' ] ), 2 );
					$commerce_international_shipping = round( $legacy_config[ 'paypal_shipping_i' ] * ( 1 + .01 * $legacy_config[ 'term_meta' ][ 'collection' ][ $v->term_id ][ 'paypal' ][ 'shipping_i' ] ), 2 );
					$commerce_original_shipping      = round( $legacy_config[ 'paypal_shipping_o' ] * ( 1 + .01 * $legacy_config[ 'term_meta' ][ 'collection' ][ $v->term_id ][ 'paypal' ][ 'shipping_o' ] ), 2 );
					
					self::$config[ 'collections' ][ $collection_id ] = array(
						'id'          => $collection_id,
						'name'        => $v->name,
						'image'       => $image_id,
						'theme'       => $legacy_config[ 'term_meta' ][ 'collection' ][ $v->term_id ][ 'theme' ],
						'updated'     => 0,
						'supports'    => array( 'title', 'editor', 'author', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions' ),
						'taxonomies'  => array( 'category', 'post_tag' ),
						'description' => $v->description,
						'feeds' => array(
							'hook' => ( boolean ) $legacy_config[ 'feed_toggle' ],
							'size' => $legacy_config[ 'feed_size' ],
							'main' => true
						),
						'slugs' => array(
							'name'      => $v->slug,
							'archive'   => $v->slug,
							'webcomic'  => $v->slug,
							'storyline' => "{$v->slug}-storyline",
							'character' => "{$v->slug}-character"
						),
						'buffer' => array(
							'hook'  => $legacy_config[ 'buffer_toggle' ],
							'days'  => $legacy_config[ 'buffer_size' ],
							'email' => $admin_email
						),
						'access' => array(
							'byage'  => ( boolean ) $legacy_config[ 'age_toggle' ],
							'byrole' => ( boolean ) $legacy_config[ 'term_meta' ][ 'collection' ][ $v->term_id ][ 'restrict' ],
							'age'    => $legacy_config[ 'age_size' ],
							'roles'  => array( '!' )
						),
						'twitter' => array(
							'media'           => false,
							'format'          => __( '%collection-name has updated! %site-url', 'webcomic' ),
							'oauth_token'     => '',
							'oauth_secret'    => '',
							'consumer_key'    => '',
							'consumer_secret' => '',
							'request_token'   => '',
							'request_secret'  => ''
						),
						'commerce' => array(
							'business'  => $legacy_config[ 'paypal_business' ],
							'currency'  => $legacy_config[ 'paypal_currency' ],
							'prints'    => ( boolean ) $legacy_config[ 'term_meta' ][ 'collection' ][ $v->term_id ][ 'paypal' ][ 'prints' ],
							'originals' => ( boolean ) $legacy_config[ 'term_meta' ][ 'collection' ][ $v->term_id ][ 'paypal' ][ 'prints' ],
							'method'    => $legacy_config[ 'paypal_method' ],
							'donation'  => $legacy_config[ 'paypal_donation' ],
							'price'     => array(
								'domestic'      => $commerce_domestic_price,
								'international' => $commerce_international_price,
								'original'      => $commerce_original_price
							),
							'shipping' => array(
								'domestic'      => $commerce_domestic_shipping,
								'international' => $commerce_international_shipping,
								'original'      => $commerce_original_shipping
							),
							'total' => array(
								'domestic'      => round( $commerce_domestic_price + $commerce_domestic_shipping, 2 ),
								'international' => round( $commerce_international_price + $commerce_international_price, 2 ),
								'original'      => round( $commerce_original_price + $commerce_original_shipping, 2 )
							)
						),
						'transcripts' => array(
							'open'       => ( boolean ) $legacy_config[ 'transcribe_toggle' ],
							'languages'  => array( '!' ),
							'permission' => $transcripts_permission,
							'notify'     => array(
								'hook'  => true,
								'email' => $admin_email
							)
						)
					);
					
					update_option( 'webcomic_options', self::$config );
					
					$stage++;
				}
				
				if ( 2 === intval( $stage ) ) {
					if ( $characters = get_terms( 'webcomic_character', array( 'fields' => 'ids', 'term_group' => $v->term_id, 'get' => 'all' ) ) and !is_wp_error( $characters ) ) {
						foreach ( $characters as $character ) {
							$character = get_term( $character, 'webcomic_character' );
							
							$wpdb->update( $wpdb->terms, array( 'term_group' => 0 ), array( 'term_id' => $character->term_id ) );
							$wpdb->update( $wpdb->term_taxonomy, array( 'taxonomy' => "{$collection_id}_character" ), array( 'term_id' => $character->term_id ) );
							
							if ( !empty( $legacy_config[ 'term_meta' ][ 'character' ][ $character->term_id ][ 'files' ] ) and $image_id = self::update_media_library( dirname( $upload_dir[ 'basedir' ] ) . "/webcomic/{$v->slug}/" . $legacy_config[ 'term_meta' ][ 'character' ][ $character->term_id ][ 'files' ][ 'full' ][ 0 ], "{$collection_id}_character" ) ) {
								self::$config[ 'terms' ][ $character ][ 'image' ] = $image_id;
								
								update_option( 'webcomic_options', self::$config );
							}
							
							if ( microtime( true ) - $start >= $this->limit ) {
								return $stage;
							}
						}
					}
					
					$stage++;
				}
				
				if ( 3 === intval( $stage ) ) {
					if ( $storylines = get_terms( 'webcomic_storyline', array( 'fields' => 'ids', 'orderby' => 'term_group', 'term_group' => $v->term_id, 'get' => 'all' ) ) and !is_wp_error( $storylines ) ) {
						$count = array();
						
						foreach ( $storylines as $storyline ) {
							$storyline = get_term( $storyline, 'webcomic_storyline' );
							
							$wpdb->update( $wpdb->terms, array( 'term_group' => isset( $count[ $storyline->parent ] ) ? $count[ $storyline->parent ] : 0 ), array( 'term_id' => $storyline->term_id ) );
							$wpdb->update( $wpdb->term_taxonomy, array( 'taxonomy' => "{$collection_id}_storyline" ), array( 'term_id' => $storyline->term_id ) );
							
							if ( !empty( $legacy_config[ 'term_meta' ][ 'storyline' ][ $storyline->term_id ][ 'files' ] ) and $image_id = self::update_media_library( dirname( $upload_dir[ 'basedir' ] ) . "/webcomic/{$v->slug}/" . $legacy_config[ 'term_meta' ][ 'storyline' ][ $storyline->term_id ][ 'files' ][ 'full' ][ 0 ], "{$collection_id}_storyline" ) ) {
								self::$config[ 'terms' ][ $storyline->term_id ][ 'image' ] = $image_id;
								
								update_option( 'webcomic_options', self::$config );
							}
							
							if ( empty( $count[ $storyline->parent ] ) ) {
								$count[ $storyline->parent ] = 0;
							}
							
							$count[ $storyline->parent ]++;
							
							if ( microtime( true ) - $start >= $this->limit ) {
								return $stage;
							}
						}
					}
					
					$stage++;
				}
				
				if ( 4 === intval( $stage ) ) {
					if ( $posts = get_posts( array(
						'fields'      => 'ids',
						'numberposts' => -1,
						'post_type'   => 'webcomic_post',
						'post_status' => get_post_stati(),
						'tax_query'   => array( array(
							'taxonomy' => 'webcomic_collection',
							'field'    => 'id',
							'terms'    => $v->term_id
						) )
					) ) ) {
						foreach ( $posts as $post ) {
							if ( $meta = get_post_meta( $post, 'webcomic', true ) ) {
								$post_domestic_price            = round( $commerce_domestic_price * ( 1 + .01 * $meta[ 'paypal' ][ 'price_d' ] ), 2 );
								$post_international_price       = round( $commerce_international_price * ( 1 + .01 * $meta[ 'paypal' ][ 'price_i' ] ), 2 );
								$post_original_price            = round( $commerce_original_price * ( 1 + .01 * $meta[ 'paypal' ][ 'price_o' ] ), 2 );
								$post_domestic_shipping         = round( $commerce_domestic_shipping * ( 1 + .01 * $meta[ 'paypal' ][ 'shipping_d' ] ), 2 );
								$post_international_shipping    = round( $commerce_international_shipping * ( 1 + .01 * $meta[ 'paypal' ][ 'shipping_i' ] ), 2 );
								$post_original_shipping         = round( $commerce_original_shipping * ( 1 + .01 * $meta[ 'paypal' ][ 'shipping_o' ] ), 2 );
								
								update_post_meta( $post, 'webcomic_prints', !empty( $meta[ 'paypal' ][ 'prints' ] ) );
								
								update_post_meta( $post, 'webcomic_original', !empty( $meta[ 'paypal' ][ 'original' ] ) );
								
								update_post_meta( $post, 'webcomic_transcripts', !empty( $meta[ 'transcribe_toggle' ] ) );
								
								update_post_meta( $post, 'webcomic_commerce', array(
									'price' => array(
										'domestic'      => $post_domestic_price,
										'international' => $post_international_price,
										'original'      => $post_original_price
									),
									'shipping' => array(
										'domestic'      => $post_domestic_shipping,
										'international' => $post_international_shipping,
										'original'      => $post_original_shipping
									),
									'total'  => array(
										'domestic'      => $post_domestic_price + $post_domestic_shipping,
										'international' => $post_international_price + $post_international_shipping,
										'original'      => $post_original_price + $post_original_shipping
									),
									'adjust' => array(
										'price' => array(
											'domestic'      => intval( $meta[ 'paypal' ][ 'price_d' ] ),
											'international' => intval( $meta[ 'paypal' ][ 'price_i' ] ),
											'original'      => intval( $meta[ 'paypal' ][ 'price_o' ] )
										),
										'shipping' => array(
											'domestic'      => intval( $meta[ 'paypal' ][ 'shipping_d' ] ),
											'international' => intval( $meta[ 'paypal' ][ 'shipping_i' ] ),
											'original'      => intval( $meta[ 'paypal' ][ 'shipping_o' ] )
										),
										'total'  => array(
											'domestic'      => ( $commerce_domestic_price or $commerce_domestic_shipping ) ? 0 - intval( round( ( 1 - ( $post_domestic_price + $post_domestic_shipping ) / ( $commerce_domestic_price + $commerce_domestic_shipping ) ) * 100 ) ) : 0,
											'international' => ( $commerce_international_price or $commerce_international_shipping ) ? 0 - intval( round( ( 1 - ( $post_international_price + $post_international_shipping ) / ( $commerce_international_price + $commerce_international_shipping ) ) * 100 ) ) : 0,
											'original'      => ( $commerce_original_price or $commerce_original_shipping ) ? 0 - intval( round( ( 1 - ( $post_original_price + $post_original_shipping ) / ( $commerce_original_price + $commerce_original_shipping ) ) * 100 ) ) : 0
										)
									)
								) );
								
								if ( !empty( $meta[ 'files' ] ) ) {
									foreach ( $meta[ 'files' ][ 'full' ] as $a => $b ) {
										self::update_media_library( dirname( $upload_dir[ 'basedir' ] ) . "/webcomic/{$v->slug}/{$b}", ( integer ) $post, array(
											'post_alt'     => empty( $meta[ 'alternate' ][ $a ] ) ? '' : $meta[ 'alternate' ][ $a ],
											'post_excerpt' => empty( $meta[ 'description' ][ $a ] ) ? '' : $meta[ 'description' ][ $a ]
										) );
										
										unset( $meta[ 'files' ][ 'full' ][ $a ] );
										
										update_post_meta( $post, 'webcomic', $meta );
										
										if ( microtime( true ) - $start >= $this->limit ) {
											return $stage;
										}
									}
								}
								
								if ( !empty( $meta[ 'transcripts' ] ) ) {
									foreach ( $meta[ 'transcripts' ] as $a => $b ) {
										$date  = date( 'Y-m-d H:i:s', $b[ 'time' ] );
										$title = sprintf( __( '%s Transcript', 'webcomic' ), get_the_title( $post ) );
										
										if ( $new_transcript = wp_insert_post( array(
											'post_name'     => sanitize_title( $title ),
											'post_type'     => 'webcomic_transcript',
											'post_date'     => $date,
											'post_title'    => $title,
											'post_author'   => 1,
											'post_parent'   => $post,
											'post_status'   => $b[ 'status' ],
											'post_content'  => $b[ 'text' ],
											'post_date_gmt' => get_gmt_from_date( $date )
										) ) and !is_wp_error( $new_transcript ) ) {
											add_post_meta( $new_transcript, 'webcomic_author', array(
												'name'  => $b[ 'author' ],
												'email' => '',
												'url'   => '',
												'ip'    => '',
												'time'  => ( integer ) $b[ 'time' ]
											) );
											
											if ( term_exists( $a, 'webcomic_language' ) ) {
												wp_set_post_terms( $new_transcript, $a, 'webcomic_language' );
											}
										}
										
										if ( microtime( true ) - $start >= $this->limit ) {
											return $stage;
										}
									}
								}
								
								$wpdb->update( $wpdb->posts, array( 'post_type' => $collection_id ), array( 'ID' => ( integer ) $post ) );
								
								self::$config[ 'collections' ][ $collection_id ][ 'updated' ] = get_the_time( 'U', $post );
								
								update_option( 'webcomic_options', self::$config );
								
								delete_post_meta( $post, 'webcomic' );
							}
						}
					}
				}
				
				$stage = 1;
				
				self::$config[ 'increment' ]++;
				
				update_option( 'webcomic_options', self::$config );
				
				wp_delete_term( $v->term_id, 'webcomic_collection' );
				
				if ( microtime( true ) - $start >= $this->limit ) {
					return $stage;
				}
			}
		}
		
		return 0;
	}
	
	/** Upgrade ComicPress installations.
	 * 
	 * @return integer
	 * @uses Webcomic::$config
	 * @uses WebcomicLegacy::update_media_library()
	 */
	private function upgrade_comicpress( $stage = 0 ) {
		global $wpdb;
		
		$start         = microtime( true );
		$upload_dir    = wp_upload_dir();
		$admin_email   = get_bloginfo( 'admin_email' );
		$legacy_config = get_option( 'webcomic_legacy' );
		
		$term = get_term( ( integer ) $legacy_config[ 'comiccat' ], 'category' );
		
		if ( !$stage ) {
			self::$config[ 'collections' ][ 'webcomic1' ][ 'name' ]                                      = $term->name;
			self::$config[ 'collections' ][ 'webcomic1' ][ 'slugs' ][ 'name' ]                           = $term->slug;
			self::$config[ 'collections' ][ 'webcomic1' ][ 'slugs' ][ 'archive' ]                        = $term->slug;
			self::$config[ 'collections' ][ 'webcomic1' ][ 'slugs' ][ 'webcomic' ]                       = $term->slug;
			self::$config[ 'collections' ][ 'webcomic1' ][ 'slugs' ][ 'storyline' ]                      = "{$term->slug}-storyline";
			self::$config[ 'collections' ][ 'webcomic1' ][ 'slugs' ][ 'character' ]                      = "{$term->slug}-character";
			self::$config[ 'collections' ][ 'webcomic1' ][ 'taxonomies' ]                                = array( 'category', 'post_tag' );
			self::$config[ 'collections' ][ 'webcomic1' ][ 'description' ]                               = $term->description;
			self::$config[ 'collections' ][ 'webcomic1' ][ 'commerce' ][ 'business' ]                    = $legacy_config[ 'buy_print_email' ];
			self::$config[ 'collections' ][ 'webcomic1' ][ 'commerce' ][ 'price' ][ 'domestic' ]         = $legacy_config[ 'buy_print_amount' ];
			self::$config[ 'collections' ][ 'webcomic1' ][ 'commerce' ][ 'price' ][ 'international' ]    = $legacy_config[ 'buy_print_amount' ];
			self::$config[ 'collections' ][ 'webcomic1' ][ 'commerce' ][ 'price' ][ 'original' ]         = $legacy_config[ 'buy_print_orig_amount' ];
			self::$config[ 'collections' ][ 'webcomic1' ][ 'commerce' ][ 'shipping' ][ 'domestic' ]      = 0;
			self::$config[ 'collections' ][ 'webcomic1' ][ 'commerce' ][ 'shipping' ][ 'international' ] = 0;
			self::$config[ 'collections' ][ 'webcomic1' ][ 'commerce' ][ 'shipping' ][ 'original' ]      = 0;
			self::$config[ 'collections' ][ 'webcomic1' ][ 'commerce' ][ 'total' ][ 'domestic' ]         = $legacy_config[ 'buy_print_amount' ];
			self::$config[ 'collections' ][ 'webcomic1' ][ 'commerce' ][ 'total' ][ 'international' ]    = $legacy_config[ 'buy_print_amount' ];
			self::$config[ 'collections' ][ 'webcomic1' ][ 'commerce' ][ 'total' ][ 'original'      ]    = $legacy_config[ 'buy_print_orig_amount' ];
			
			update_option( 'webcomic_options', self::$config );
			
			if ( $terms = get_terms( 'category', array( 'child_of' => ( integer ) $legacy_config[ 'comiccat' ], 'fields' => 'ids' ) ) and !is_wp_error( $terms ) ) {
				$wpdb->query( sprintf( "UPDATE {$wpdb->term_taxonomy} SET taxonomy = 'webcomic1_storyline' WHERE term_id IN (%s)", join( ",", $terms ) ) );
			}
			
			$stage++;
		}
		
		if ( 1 === $stage ) {
			$meta_files = ( array ) glob( self::legacy_path( $legacy_config[ 'comic_folder' ] ) . '*.*' );
			
			if ( $posts = get_posts( array(
				'fields'      => 'ids',
				'numberposts' => -1,
				'post_type'   => 'post',
				'post_status' => get_post_stati(),
				'tax_query'   => array(
					'relation' => 'OR',
					array(
						'taxonomy' => 'webcomic1_storyline',
						'field'    => 'id',
						'terms'    => get_terms( 'webcomic1_storyline', array( 'fields' => 'ids' ) )
					), array(
						'taxonomy' => 'category',
						'field'    => 'id',
						'terms'    => ( integer ) $legacy_config[ 'comiccat' ]
					)
				)
			) ) ) {
				foreach ( $posts as $post ) {
					$format    = get_the_time( $legacy_config[ 'date_format' ], $post );
					$meta_file = preg_grep( "/{$format}/", $meta_files );
					
					if ( !empty( $meta_file ) ) {
						self::update_media_library( $meta_file[ 0 ], ( integer ) $post, array(
							'post_excerpt' => ( $meta_description = get_post_meta( $post, 'hovertext', true ) ) ? $meta_description : ''
						) );
					}
					
					if ( $meta_transcript = get_post_meta( $post, 'transcript', true ) ) {
						$date  = get_the_time( 'Y-m-d H:i:s', $post );
						$title = sprintf( __( '%s Transcript', 'webcomic' ), get_the_title( $post ) );
						
						wp_insert_post( array(
							'post_name'     => sanitize_title( $title ),
							'post_type'     => 'webcomic_transcript',
							'post_date'     => $date,
							'post_title'    => $title,
							'post_author'   => 1,
							'post_parent'   => $post,
							'post_status'   => 'publish',
							'post_content'  => $meta_transcript,
							'post_date_gmt' => get_gmt_from_date( $date )
						) );
					}
					
					update_post_meta( $post, 'webcomic_prints', false );
					
					update_post_meta( $post, 'webcomic_original', false );
					
					update_post_meta( $post, 'webcomic_transcripts', true );
					
					update_post_meta( $post, 'webcomic_commerce', array(
						'price' => array(
							'domestic'      => $legacy_config[ 'buy_print_amount' ],
							'international' => $legacy_config[ 'buy_print_amount' ],
							'original'      => $legacy_config[ 'buy_print_orig_amount' ]
						),
						'shipping' => array(
							'domestic'      => 0,
							'international' => 6,
							'original'      => 0
						),
						'total'  => array(
							'domestic'      => $legacy_config[ 'buy_print_amount' ],
							'international' => $legacy_config[ 'buy_print_amount' ],
							'original'      => $legacy_config[ 'buy_print_orig_amount' ]
						),
						'adjust' => array(
							'price' => array(
								'domestic'      => 0,
								'international' => 0,
								'original'      => 0
							),
							'shipping' => array(
								'domestic'      => 0,
								'international' => 0,
								'original'      => 0
							),
							'total'  => array(
								'domestic'      => 0,
								'international' => 0,
								'original'      => 0
							)
						)
					) );
					
					delete_post_meta( $post, 'hovertext' );
					delete_post_meta( $post, 'transcript' );
					
					$wpdb->update( $wpdb->posts, array( 'post_type' => 'webcomic1' ), array( 'ID' => ( integer ) $post ) );
					
					self::$config[ 'collections' ][ 'webcomic1' ][ 'updated' ] = get_the_time( 'U', $post );
					
					update_option( 'webcomic_options', self::$config );
					
					if ( microtime( true ) - $start >= $this->limit ) {
						return $stage;
					}
				}
			}
		}
		
		return 0;
	}
	
	/** Insert legacy Webcomic files into the Media Library.
	 * 
	 * @param string $file Absolute path and filename of the file to insert into the Media Library.
	 * @param mixed $context Context to add to the new file, or the integer ID of the attachment parent.
	 * @param array $attr Additional image attributes.
	 * @return integer
	 */
	private function update_media_library( $file, $context = '', $attr = array() ) {
		$attachment_id = 0;
		
		if ( is_readable( $file ) ) {
			$filename      = basename( $file );
			$fileinfo      = wp_check_filetype( $filename );
			$attachment    = wp_upload_bits( $filename, null, file_get_contents( $file ), get_option( 'uploads_use_yearmonth_folders' ) ? date( 'Y/m', filemtime( $file ) ) : null );
			$attachment_id = wp_insert_attachment( array(
				'guid'           => $attachment[ 'url' ],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
				'post_status'    => 'inherit',
				'post_parent'    => is_int( $context ) ? $context : 0,
				'post_content'   => '',
				'post_excerpt'   => empty( $attr[ 'post_excerpt' ] ) ? '' : $attr[ 'post_excerpt' ],
				'post_mime_type' => $fileinfo[ 'type' ]
			), $attachment[ 'file' ] );
			
			wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $attachment[ 'file' ] ) );
			
			if ( !empty( $attr[ 'post_alt' ] ) ) {
				update_post_meta( $attachment_id, '_wp_attachment_image_alt', $attr[ 'post_alt' ] );
			}
			
			if ( $context and !is_int( $context ) ) {
				update_post_meta( $attachment_id, '_wp_attachment_context', $context );
			}
		}
		
		return ( integer ) $attachment_id;
	}
	
	/** Gets the comic file path for legacy installations.
	 * 
	 * @param string $directory The `comic_directory` option from the legacy configuration.
	 * @param string $category The category slug for subdirectories, if any.
	 * @return string
	 */
	private function legacy_path( $directory, $category = '' ) {
		return ABSPATH . ( ( $category ) ? "{$directory}/{$category}/" : "{$directory}/" );
	}
}
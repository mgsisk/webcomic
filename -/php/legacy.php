<?php
/** Contains the WebcomicLegacy class.
 * 
 * @package Webcomic
 */

/** Upgrade legacy Webcomic installations.
 * 
 * @package Webcomic
 * @todo Implement
 */
class WebcomicLegacy extends Webcomic {
	/** Register action and filter hooks.
	 * 
	 * @uses WebcomicLegacy::init()
	 * @uses WebcomicLegacy::admin_init()
	 * @uses WebcomicLegacy::admin_menu()
	 * @uses WebcomicLegacy::admin_notices()
	 * @uses WebcomicLegacy::admin_enqueue_scripts()
	 */
	public function __construct() {
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
		} else {
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
	 * @hook admin_init
	 */
	public function admin_init() {
		if ( isset( $_POST[ 'disable_legacy' ], $_POST[ 'webcomic_upgrade' ] ) and wp_verify_nonce( $_POST[ 'webcomic_upgrade' ], 'webcomic_upgrade' ) ) {
			$file = plugin_basename( self::$dir . '/webcomic.php' );
			
			self::$config[ 'uninstall' ] = true;
			
			update_option( 'webcomic_options', self::$config );
			
			wp_redirect( html_entity_decode( wp_nonce_url( add_query_arg( array( 'action' => 'deactivate', 'plugin' => $file ), admin_url( 'plugins.php' ) ), 'deactivate-plugin_' . $file ) ) );
			
			die;
		}
		
		if ( isset( $_POST[ 'upgrade_legacy' ], $_POST[ 'webcomic_upgrade' ] ) and wp_verify_nonce( $_POST[ 'webcomic_upgrade' ], 'webcomic_upgrade' ) ) {
			if ( 3 === self::$config[ 'legacy' ] ) {
				$_POST[ 'webcomic_upgrade_continue' ] = self::upgrade3();
			} else if ( 2 === self::$config[ 'legacy' ] ) {
				$_POST[ 'webcomic_upgrade_continue' ] = self::upgrade2();
			} else {
				$_POST[ 'webcomic_upgrade_continue' ] = self::upgrade1();
			}
		}
	}
	
	/** Register submenu page for legacy upgrades.
	 * 
	 * @uses WebcomicLegacy::page()
	 * @hook admin_menu
	 */
	public function admin_menu() {
		add_submenu_page( 'tools.php', __( 'Upgrade Webcomic', 'webcomic' ), __( 'Upgrade Webcomic', 'webcomic' ), 'manage_options', 'webcomic-upgrader', array( $this, 'page' ) );
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
			
			printf( '<div class="updated webcomic legacy"><a href="%s"><b>&#x2605;</b> %s &raquo;</a></div>', esc_url( add_query_arg( array( 'page' => 'webcomic-upgrader' ), admin_url( 'tools.php' ) ) ), sprintf( __( 'Upgrading? Let Webcomic Help', 'webcomic' ), self::$version ) );
		}
	}
	
	/** Enqueue custom styles for upgrade notice.
	 * 
	 * @uses Webcomic::$url
	 * @uses Webcomic::$config
	 * @hook admin_enqueue_scripts
	 */
	public function admin_enqueue_scripts() {
		if ( isset( self::$config[ 'legacy_notice' ] ) ) {
			wp_register_style( 'webcomic-google-font', 'http://fonts.googleapis.com/css?family=Maven+Pro' );
			wp_register_style( 'webcomic-special', self::$url . '-/css/admin-special.css', array( 'webcomic-google-font' ) );
			
			wp_enqueue_style( 'webcomic-special' );
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
			<h2><?php _e( 'Upgrade Webcomic', 'webcomic' ); ?></h2>
			<div id="col-left">
				<div class="col-wrap">
					<?php if ( isset( $_POST[ 'webcomic_upgrade_continue' ] ) and 'complete' === $_POST[ 'webcomic_upgrade_continue' ] ) { ?>
					
					
					
					<?php } else if ( isset( $_POST[ 'webcomic_upgrade_continue' ] ) ) { ?>
					
					
					
					<?php } else { ?>
					
					<p><?php printf( __( 'This tool will attempt to automatically convert your existing Webcomic %s data to Webcomic %s. Depending on the size of your site the upgrade may require multiple steps. If you do not want to upgrade click <strong>Not Interested</strong> to uninstall Webcomic %s.', 'webcomic' ), self::$config[ 'legacy' ], self::$version, self::$version ); ?></p>
					<p style="color:#bc0b0b;font-size:larger;line-height:1.5"><strong><?php printf( __( 'Upgrades are not reversible. Please <a href="%s" target="_blank">read this</a> and <a href="%s">backup your site</a> before upgrading.', 'webcomic' ), 'http://webcomic.nu/upgrade', esc_url( admin_url( 'export.php' ) ) ); ?></strong></p>
					<form method="post">
						<?php wp_nonce_field( 'webcomic_upgrade', 'webcomic_upgrade' ); ?>
						<div class="form-wrap">
							<?php submit_button( __( 'Upgrade Now', 'webcomic' ), 'primary', 'upgrade_legacy', false ); ?>
							<span style="float:right"><?php submit_button( __( "Not Interested", 'webcomic' ), 'secondary', 'disable_legacy', false ); ?></span>
						</div>
					</form>
					
					<?php } ?>
				</div>
			</div>
		</div>
		<?php
	}
	
	/** Upgrade Webcomic 1 installations.
	 */
	private function upgrade1() {
		$now           = time();
		$timelimit     = ini_get( 'max_execution_time' );
		$upload_dir    = wp_upload_dir();
		$admin_email   = get_bloginfo( 'admin_email' );
		$legacy_config = get_option( 'webcomic_legacy' );
	}
	
	/** Upgrade Webcomic 2 installations.
	 */
	private function upgrade2() {
		global $wpdb;
		
		$now           = time();
		$timelimit     = ini_get( 'max_execution_time' );
		$upload_dir    = wp_upload_dir();
		$admin_email   = get_bloginfo( 'admin_email' );
		$legacy_config = get_option( 'webcomic_legacy' );
		$pages         = get_posts( array(
			'fields'      => 'ids',
			'numberposts' => -1,
			'post_type'   => 'page',
			'post_status' => get_post_stati()
		) );
		
		if ( $legacy_config[ 'comic_transcripts_loggedin' ] ) {
			$transcripts_permission = 'register';
		} else if ( $legacy_config[ 'comic_transcripts_required' ] ) {
			$transcripts_permission = 'identify';
		} else {
			$transcripts_permission = 'everyone';
		}
		
		if ( empty( $_POST[ 'webcomic_upgrade_continue' ] ) ) {
			self::$config[ 'webcomic' ][ 'shortcuts' ] = ( boolean ) $legacy_config[ 'comic_keyboard_shortcuts' ];
			
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
		}
		
		if ( $terms = get_terms( 'category', array( 'get' => 'all', 'include' => $legacy_config[ 'comic_category' ] ) ) and !is_wp_error( $terms ) ) {
			foreach ( $terms as $k => $v ) {
				if ( $k ) {
					$collection_id = 'webcomic' . self::$config[ 'increment' ];
				} else if ( !empty( $_POST[ 'webcomic_upgrade_continue' ] ) ) {
					$collection_id = $_POST[ 'webcomic_upgrade_continue' ];
				} else {
					$collection_id = 'webcomic1';
				}
				
				self::$config[ 'webcomic' ][ 'collections' ][ $collection_id ] = array(
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
				
				if ( $chapters = get_terms( 'chapter', array( 'fields' => 'ids', 'term_group' => $v->term_id, 'get' => 'all' ) ) and !is_wp_error( $storylines ) ) {
					// do something?
				}
				
				if ( $posts = get_posts( array(
					'fields'      => 'ids',
					'numberposts' => -1,
					'post_type'   => 'post',
					'post_status' => get_post_stati(),
					'tax_query'   => array(
						'taxonomy' => 'category',
						'field'    => 'id',
						'terms'    => $v->term_id
					)
				) ) ) {
					$wpdb->query( sprintf( "UPDATE {$wpdb->posts} SET post_type = '{$collection_id}' WHERE ID IN ( %s )", join( ', ', $posts ) ) );
					
					foreach ( $posts as $post ) {
						if ( $meta_file = get_post_meta( $post, 'comic_file', true ) ) {
							self::update_media_library( self::legacy_directory( $legacy_config[ 'comic_directory' ], $v->slug ) . $meta_file, $post, array(
								'post_excerpt' => $meta_description = get_post_meta( $post, 'comic_description', true ) ? $meta_description : ''
							) );
						}
						
						if ( $meta_transcript = get_post_meta( $post, 'comic_transcript', true ) or $meta_transcript = get_post_meta( $post, 'comic_transcript_pending', true ) or $meta_transcript = get_post_meta( $post, 'comic_transcript_draft', true ) ) {
							$date  = get_the_time( 'Y-m-d H:i:s', $v[ 'time' ] );
							$title = sprintf( __( '%s Transcript', 'webcomic' ), get_the_title( $post ) );
							
							if ( get_post_meta( $post, 'comic_transcript', true ) ) {
								$status = 'publish';
							} else if ( get_post_meta( $post, 'comic_transcript_pending', true ) ) {
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
					}
				}
				
				if ( $k ) {
					self::$config[ 'increment' ]++;
				}
				
				update_option( 'webcomic_options', self::$config );
				
				wp_delete_term( $v->term_id, 'webcomic_collection' );
				
				if ( microtime( true ) - $start >= $timelimit - 1 ) {
					return 'webcomic' . self::$config[ 'increment' ];
				}
				
				if ( $pages ) {
					foreach ( $pages as $page ) {
						if ( $meta_series = get_post_meta( $page, 'comic_series', true ) and ( integer ) $meta_series === ( integer ) $v->term_id ) {
							update_post_meta( $page, 'webcomic_collection', $collection_id );
							delete_post_meta( $page, 'comic_series' );
						}
					}
				}
			}
		}
		
		return 'complete';
	}
	
	/** Upgrade Webcomic 3 installations.
	 * 
	 * @todo update time
	 * @todo save new configuration
	 * @todo convert page collections
	 * 
	 * @uses Webcomic::$config
	 * @return string
	 */
	private function upgrade3( $stage = 0 ) {
		global $wpdb;
		
		$start         = microtime( true );
		$timelimit     = ini_get( 'max_execution_time' );
		$upload_dir    = wp_upload_dir();
		$admin_email   = get_bloginfo( 'admin_email' );
		$legacy_config = get_option( 'webcomic_legacy' );
		
		if ( 'login' === $legacy_config[ 'transcribe_restrict' ] ) {
			$transcripts_permission = 'register';
		} else if ( 'selfid' === $legacy_config[ 'transcribe_restrict' ] ) {
			$transcripts_permission = 'identify';
		} else {
			$transcripts_permission = 'everyone';
		}
		
		if ( empty( $_POST[ 'webcomic_upgrade_continue' ] ) ) {
			self::$config[ 'webcomic' ][ 'integrate' ] = ( boolean ) $legacy_config[ 'integrate_toggle' ];
			self::$config[ 'webcomic' ][ 'shortcuts' ] = ( boolean ) $legacy_config[ 'shortcut_toggle' ];
			
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
			
			foreach ( $legacy_config[ 'transcribe_language' ] as $k => $v ) {
				wp_insert_term( $v, 'webcomic_language', array( 'slug' => $k ) );
			}
		}
		
		if ( $terms = get_terms( 'webcomic_collection', array( 'get' => 'all' ) ) and !is_wp_error( $terms ) ) {
			foreach ( $terms as $k => $v ) {
				if ( $k ) {
					$collection_id = 'webcomic' . self::$config[ 'increment' ];
				} else if ( !empty( $_POST[ 'webcomic_upgrade_continue' ] ) ) {
					$collection_id = $_POST[ 'webcomic_upgrade_continue' ];
				} else {
					$collection_id = 'webcomic1';
				}
				
				$image_id                       = empty( $legacy_config[ 'term_meta' ][ 'collection' ][ $v->term_id ][ 'files' ] ) ? 0 : self::update_media_library( dirname( $upload_dir[ 'basedir' ] ) . "/webcomic/{$v->slug}/" . $legacy_config[ 'term_meta' ][ 'collection' ][ $v->term_id ][ 'files' ][ 'full' ][ 0 ], $collection_id );
				$comerce_domestic_price         = round( $legacy_config[ 'paypal_price_d' ] * ( 1 + .01 * $legacy_config[ 'term_meta' ][ 'collection' ][ $v->term_id ][ 'paypal' ][ 'price_d' ] ), 2 );
				$comerce_international_price    = round( $legacy_config[ 'paypal_price_i' ] * ( 1 + .01 * $legacy_config[ 'term_meta' ][ 'collection' ][ $v->term_id ][ 'paypal' ][ 'price_i' ] ), 2 );
				$comerce_original_price         = round( $legacy_config[ 'paypal_price_o' ] * ( 1 + .01 * $legacy_config[ 'term_meta' ][ 'collection' ][ $v->term_id ][ 'paypal' ][ 'price_o' ] ), 2 );
				$comerce_domestic_shipping      = round( $legacy_config[ 'paypal_shipping_d' ] * ( 1 + .01 * $legacy_config[ 'term_meta' ][ 'collection' ][ $v->term_id ][ 'paypal' ][ 'shipping_d' ] ), 2 );
				$comerce_international_shipping = round( $legacy_config[ 'paypal_shipping_i' ] * ( 1 + .01 * $legacy_config[ 'term_meta' ][ 'collection' ][ $v->term_id ][ 'paypal' ][ 'shipping_i' ] ), 2 );
				$comerce_original_shipping      = round( $legacy_config[ 'paypal_shipping_o' ] * ( 1 + .01 * $legacy_config[ 'term_meta' ][ 'collection' ][ $v->term_id ][ 'paypal' ][ 'shipping_o' ] ), 2 );
				
				self::$config[ 'webcomic' ][ 'collections' ][ $collection_id ] = array(
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
							'domestic'      => $comerce_domestic_price,
							'international' => $comerce_international_price,
							'original'      => $comerce_original_price
						),
						'shipping' => array(
							'domestic'      => $comerce_domestic_shipping,
							'international' => $comerce_international_shipping,
							'original'      => $comerce_original_shipping
						),
						'total' => array(
							'domestic'      => round( $comerce_domestic_price + $comerce_domestic_shipping, 2 ),
							'international' => round( $comerce_international_price + $comerce_international_price, 2 ),
							'original'      => round( $comerce_original_price + $comerce_original_shipping, 2 )
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
				
				if ( $storylines = get_terms( 'webcomic_storyline', array( 'fields' => 'ids', 'term_group' => $v->term_id, 'get' => 'all' ) ) and !is_wp_error( $storylines ) ) {
					$count = array();
					
					$wpdb->query( sprintf( "UPDATE {$wpdb->term_taxonomy} SET taxonomy = '{$collection_id}_storyline' WHERE term_id IN ( %s )", join( ', ', $storylines ) ) );
					
					foreach ( $storylines as $storyline ) {
						if ( !empty( $legacy_config[ 'term_meta' ][ 'storyline' ][ $storyline ][ 'files' ] ) and $image_id = self::update_media_library( dirname( $upload_dir[ 'basedir' ] ) . "/webcomic/{$v->slug}/" . $legacy_config[ 'term_meta' ][ 'storyline' ][ $storyline ][ 'files' ][ 'full' ][ 0 ], "{$collection_id}_storyline" ) ) {
							self::$config[ 'terms' ][ $storyline ][ 'image' ] = $image_id;
						}
						
						if ( empty( $count[ $term->parent ] ) ) {
							$count[ $term->parent ] = 0;
						}
						
						$wpdb->update( $wpdb->terms, array( 'term_group' => $count[ $term->parent ] ), array( 'term_id' => $term->term_id ) );
						
						$count[ $term->parent ]++;
					}
				}
				
				if ( $characters = get_terms( 'webcomic_character', array( 'fields' => 'ids', 'term_group' => $v->term_id, 'get' => 'all' ) ) and !is_wp_error( $characters ) ) {
					$wpdb->query( sprintf( "UPDATE {$wpdb->term_taxonomy} SET taxonomy = '{$collection_id}_character' WHERE term_id IN ( %s )", join( ', ', $characters ) ) );
					$wpdb->query( sprintf( "UPDATE {$wpdb->terms} SET term_group = 0 WHERE term_id IN ( %s )", join( ', ', $characters ) ) );
					
					foreach ( $characters as $characters ) {
						if ( !empty( $legacy_config[ 'term_meta' ][ 'character' ][ $character ][ 'files' ] ) and $image_id = self::update_media_library( dirname( $upload_dir[ 'basedir' ] ) . "/webcomic/{$v->slug}/" . $legacy_config[ 'term_meta' ][ 'character' ][ $character ][ 'files' ][ 'full' ][ 0 ], "{$collection_id}_character" ) ) {
							self::$config[ 'terms' ][ $character ][ 'image' ] = $image_id;
						}
					}
				}
				
				if ( $posts = get_posts( array(
					'fields'      => 'ids',
					'numberposts' => -1,
					'post_type'   => 'webcomic_post',
					'post_status' => get_post_stati(),
					'tax_query'   => array(
						'taxonomy' => 'webcomic_collection',
						'field'    => 'id',
						'terms'    => $v->term_id
					)
				) ) ) {
					$wpdb->query( sprintf( "UPDATE {$wpdb->posts} SET post_type = '{$collection_id}' WHERE ID IN ( %s )", join( ', ', $posts ) ) );
					
					foreach ( $posts as $post ) {
						if ( $meta = get_post_meta( $post, 'webcomic', true ) ) {
							$post_domestic_price         = round( $commerce_domestic_price * ( 1 + .01 * $meta[ 'paypal' ][ 'price_d' ] ), 2 );
							$post_international_price    = round( $commerce_international_price * ( 1 + .01 * $meta[ 'paypal' ][ 'price_i' ] ), 2 );
							$post_original_price         = round( $commerce_shipping_price * ( 1 + .01 * $meta[ 'paypal' ][ 'price_o' ] ), 2 );
							$post_domestic_shipping      = round( $commerce_domestic_shipping * ( 1 + .01 * $meta[ 'paypal' ][ 'shipping_d' ] ), 2 );
							$post_international_shipping = round( $commerce_international_shipping * ( 1 + .01 * $meta[ 'paypal' ][ 'shipping_i' ] ), 2 );
							$post_original_shipping      = round( $commerce_shipping_shipping * ( 1 + .01 * $meta[ 'paypal' ][ 'shipping_o' ] ), 2 );
							
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
										'domestic'      => 0 - intval( round( ( 1 - ( $post_domestic_price + $post_domestic_shipping ) / ( $commerce_domestic_price + $commerce_domestic_shipping ) ) * 100 ) ),
										'international' => 0 - intval( round( ( 1 - ( $post_international_price + $post_international_shipping ) / ( $commerce_international_price + $commerce_international_shipping ) ) * 100 ) ),
										'original'      => 0 - intval( round( ( 1 - ( $post_original_price + $post_original_shipping ) / ( $commerce_original_price + $commerce_original_shipping ) ) * 100 ) )
									)
								)
							) );
							
							if ( !empty( $meta[ 'files' ] ) ) {
								foreach ( $meta[ 'files' ][ 'full' ] as $k => $v ) {
									self::update_media_library( $v, $post, array(
										'post_alt'     => empty( $meta[ 'alternate' ][ $k ] ) ? '' : $meta[ 'alternate' ][ $k ],
										'post_excerpt' => empty( $meta[ 'description' ][ $k ] ) ? '' : $meta[ 'description' ][ $k ]
									) );
								}
							}
							
							if ( !empty( $meta[ 'transcripts' ] ) ) {
								foreach ( $meta[ 'transcripts' ] as $k => $v ) {
									$date  = date( 'Y-m-d H:i:s', $v[ 'time' ] );
									$title = sprintf( __( '%s Transcript', 'webcomic' ), get_the_title( $post ) );
									
									if ( $transcript = wp_insert_post( array(
										'post_name'     => sanitize_title( $title ),
										'post_type'     => 'webcomic_transcript',
										'post_date'     => $date,
										'post_title'    => $title,
										'post_author'   => 1,
										'post_parent'   => $post,
										'post_status'   => $v[ 'status' ],
										'post_content'  => $v[ 'text' ],
										'post_date_gmt' => get_gmt_from_date( $date )
									) ) and !is_wp_error( $transcript ) ) {
										add_post_meta( $new_post, 'webcomic_author', array(
											'name'  => $v[ 'author' ],
											'email' => '',
											'url'   => '',
											'ip'    => '',
											'time'  => ( integer ) $v[ 'time' ]
										) );
										
										if ( term_exists( $k, 'webcomic_language' ) ) {
											wp_set_post_terms( $transcript, $k, 'webcomic_language' );
										}
									}
								}
							}
							
							delete_post_meta( $post, 'webcomic' );
						}
					}
				}
				
				if ( $pages ) {
					foreach ( $pages as $page ) {
						if ( $meta = get_post_meta( $page, 'webcomic_collection', true ) ) {
							
						}
					}
				}
				
				if ( $k ) {
					self::$config[ 'increment' ]++;
				}
				
				update_option( 'webcomic_options', self::$config );
				
				wp_delete_term( $v->term_id, 'webcomic_collection' );
				
				if ( microtime( true ) - $start >= $timelimit - 1 ) {
					return 'webcomic' . self::$config[ 'increment' ];
				}
			}
		}
		
		return 'complete';
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
		return ABSPATH . ( $category ) ? "{$directory}/{$category}/" : "{$directory}/";
	}
}
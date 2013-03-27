<?php
/** Contains the WebcomicAdmin class.
 * 
 * @package Webcomic
 */

/** Handle general administrative tasks.
 * 
 * @package Webcomic
 */
class WebcomicAdmin extends Webcomic {
	/** Register hooks and istantiate the administrative classes.
	 * 
	 * @uses Webcomic::$dir
	 * @uses Webcomic::$config
	 * @uses Webcomic::__construct()
	 * @uses WebcomicAdmin::activate()
	 * @uses WebcomicAdmin::deactivate()
	 * @uses WebcomicAdmin::admin_init()
	 * @uses WebcomicAdmin::admin_head()
	 * @uses WebcomicAdmin::admin_notices()
	 * @uses WebcomicAdmin::admin_enqueue_scripts()
	 * @uses WebcomicAdmin::plugin_row_meta()
	 * @uses WebcomicAdmin::plugin_action_links()
	 * @uses WebcomicPosts
	 * @uses WebcomicPages
	 * @uses WebcomicUsers
	 * @uses WebcomicMedia
	 * @uses WebcomicConfig
	 * @uses WebcomicCommerce
	 * @uses WebcomicTaxonomy
	 * @uses WebcomicTranscripts
	 * @uses WebcomicLegacy
	 */
	public function __construct() {
		parent::__construct();
		
		register_activation_hook( self::$dir . 'webcomic.php', array( $this, 'activate' ) );
		register_deactivation_hook( self::$dir . 'webcomic.php', array( $this, 'deactivate' ) );
		
		if ( !self::$config or version_compare( self::$config[ 'version' ], self::$version, '<' ) ) {
			add_action( 'admin_init', array( $this, 'activate' ) );
		}
		
		if ( self::$config and version_compare( self::$config[ 'version' ], '4x', '>=' ) ) {
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_head', array( $this, 'admin_head' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			add_action( 'wp_dashboard_setup', array( $this, 'wp_dashboard_setup' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
			
			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 3 );
			add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 4 );
			
			require_once self::$dir . '-/php/posts.php';		new WebcomicPosts;
			require_once self::$dir . '-/php/pages.php';		new WebcomicPages;
			require_once self::$dir . '-/php/users.php';		new WebcomicUsers;
			require_once self::$dir . '-/php/media.php';		new WebcomicMedia;
			require_once self::$dir . '-/php/config.php';		new WebcomicConfig;
			require_once self::$dir . '-/php/commerce.php';		new WebcomicCommerce;
			require_once self::$dir . '-/php/taxonomy.php';		new WebcomicTaxonomy;
			require_once self::$dir . '-/php/transcripts.php';	new WebcomicTranscripts;
			
			if ( !empty( self::$config[ 'legacy' ] ) ) {
				require_once self::$dir . '-/php/legacy.php'; new WebcomicLegacy;
			}
		}
	}
	
	/** Activation hook.
	 *
	 * If no configuration exists or a legacy configuration is detected
	 * we load the default configuration (saving legacy configurations
	 * to 'webcomic_legacy' as necessary), schedule the
	 * webcomic_buffer_alert event, and flush rewrite rules. If an
	 * existing modern configuration exists but is older than the
	 * internal version we run an upgrade.
	 * 
	 * @uses Webcomic::$config
	 * @uses Webcomic::$version
	 * @uses WebcomicUpgrade
	 * @hook webcomic.php
	 */
	public function activate() {
		if ( !self::$config or version_compare( self::$config[ 'version' ], '4x', '<' ) ) {
			$name   = __( 'Untitled Webcomic', 'webcomic' );
			$slug   = sanitize_title( $name );
			$legacy = self::$config ? self::$config : get_option( 'webcomic_version' );
			
			self::$config = array(
				'version'      => self::$version,
				'increment'    => 2,
				'api'          => '',
				'thanks'       => true,
				'convert'      => false,
				'dynamic'      => false,
				'gestures'     => false,
				'integrate'    => false,
				'shortcuts'    => false,
				'uninstall'    => false,
				'sizes'        => array(),
				'terms'        => array(),
				'network'      => array(
					'showcase' => false
				),
				'collections'  => array(
					'webcomic1' => array(
						'id'          => 'webcomic1',
						'name'        => $name,
						'image'       => 0,
						'theme'       => '',
						'updated'     => 0,
						'supports'    => array( 'title', 'editor', 'author', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions' ),
						'taxonomies'  => array(),
						'description' => '',
						'feeds' => array(
							'hook' => true,
							'size' => 'thumbnail',
							'main' => true
						),
						'slugs' => array(
							'name'      => $slug,
							'archive'   => $slug,
							'webcomic'  => $slug,
							'storyline' => "{$slug}-storyline",
							'character' => "{$slug}-character"
						),
						'buffer' => array(
							'hook'  => true,
							'days'  => 7,
							'email' => get_bloginfo( 'admin_email' )
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
							'open'       => true,
							'languages'  => array( '!' ),
							'permission' => 'register',
							'notify'     => array(
								'hook'  => true,
								'email' => get_bloginfo( 'admin_email' )
							)
						)
					)
				)
			);
			
			if ( is_array( $legacy ) ) {
				self::$config[ 'legacy' ] = 3;
				
				$legacy_config = $legacy;
			} elseif ( $legacy ) {
				self::$config[ 'legacy' ] = version_compare( $legacy, '2', '>=' ) ? 2 : 1;
				
				$legacy_config = array(
					'webcomic_version'     => $legacy,
					'comic_category'       => get_option( 'comic_category' ),
					'comic_directory'      => get_option( 'comic_directory' ),
					'comic_current_chaper' => get_option( 'comic_current_chapter' ),
					'comic_secure_names'   => get_option( 'comic_secure_names' ),
					'comic_feed'           => get_option( 'comic_feed' ),
					'comic_feed_size'      => get_option( 'comic_feed_size' ),
					'comic_large_size_w'   => get_option( 'comic_large_size_w' ),
					'comic_large_size_h'   => get_option( 'comic_large_size_h' ),
					'comic_medium_size_w'  => get_option( 'comic_medium_size_w' ),
					'comic_medium_size_h'  => get_option( 'comic_medium_size_h' )
				);
				
				delete_option( 'webcomic_version' );
				delete_option( 'comic_category' );
				delete_option( 'comic_directory' );
				delete_option( 'comic_current_chapter' );
				delete_option( 'comic_secure_names' );
				delete_option( 'comic_feed' );
				delete_option( 'comic_feed_size' );
				delete_option( 'comic_large_size_w' );
				delete_option( 'comic_large_size_h' );
				delete_option( 'comic_medium_size_w' );
				delete_option( 'comic_medium_size_h' );
				
				if ( 2 === self::$config[ 'legacy' ] ) {
					$legacy_config = array_merge( $legacy_config, array(
						'comic_buffer'               => get_option( 'comic_buffer' ),
						'comic_thumb_crop'           => get_option( 'comic_thumb_crop' ),
						'comic_buffer_alert'         => get_option( 'comic_buffer_alert' ),
						'comic_secure_paths'         => get_option( 'comic_secure_paths' ),
						'comic_thumb_size_w'         => get_option( 'comic_thumb_size_w' ),
						'comic_thumb_size_h'         => get_option( 'comic_thumb_size_h' ),
						'comic_keyboard_shortcuts'   => get_option( 'comic_keyboard_shortcuts' ),
						'comic_transcripts_allowed'  => get_option( 'comic_transcripts_allowed' ),
						'comic_transcripts_required' => get_option( 'comic_transcripts_required' ),
						'comic_transcripts_loggedin' => get_option( 'comic_transcripts_loggedin' )
					) );
					
					delete_option( 'comic_buffer' );
					delete_option( 'comic_thumb_crop' );
					delete_option( 'comic_buffer_alert' );
					delete_option( 'comic_secure_paths' );
					delete_option( 'comic_thumb_size_w' );
					delete_option( 'comic_thumb_size_h' );
					delete_option( 'comic_keyboard_shortcuts' );
					delete_option( 'comic_transcripts_allowed' );
					delete_option( 'comic_transcripts_required' );
					delete_option( 'comic_transcripts_loggedin' );
				} else {
					$legacy_config = array_merge( $legacy_config, array(
						'comic_auto_post'        => get_option( 'comic_auto_post' ),
						'comic_name_format'      => get_option( 'comic_name_format' ),
						'comic_thumbnail_crop'   => get_option( 'comic_thumbnail_crop' ),
						'comic_name_format_date' => get_option( 'comic_name_format_date' ),
						'comic_thumbnail_size_w' => get_option( 'comic_thumbnail_size_w' ),
						'comic_thumbnail_size_h' => get_option( 'comic_thumbnail_size_h' )
					) );
					
					delete_option( 'comic_auto_post' );
					delete_option( 'comic_name_format' );
					delete_option( 'comic_thumbnail_crop' );
					delete_option( 'comic_name_format_date' );
					delete_option( 'comic_thumbnail_size_w' );
					delete_option( 'comic_thumbnail_size_h' );
				}
			} elseif ( $legacy_config = get_option( 'comicpress-options' ) ) {
				global $comiccat, $comic_folder;
				
				self::$config[ 'legacy' ] = 'ComicPress';
				
				$legacy_config = array_merge( array(
					'comiccat'     => $comiccat,
					'date_format'  => CP_DATE_FORMAT,
					'comic_folder' => $comic_folder
				), $legacy_config );
			}
			
			if ( isset( self::$config[ 'legacy' ] ) ) {
				self::$config[ 'legacy_notice' ] = true;
				
				delete_option( 'webcomic_legacy' );
				
				update_option( 'webcomic_legacy', $legacy_config, '', 'no' );
			}
			
			delete_option( 'webcomic_options' );
			
			add_option( 'webcomic_options', self::$config );
			
			wp_schedule_event( ( integer ) current_time( 'timestamp' ), 'daily', 'webcomic_buffer_alert' );
		} elseif ( version_compare( self::$config[ 'version' ], self::$version, '<' ) ) {
			require_once self::$dir . '-/php/upgrade.php'; new WebcomicUpgrade;
		}
		
		flush_rewrite_rules();
	}
	
	/** Deactivation hook.
	 * 
	 * Flushes rewrite rules and purges Webcomic Network data when
	 * deactivated. If the uninstall option has been set we also need to
	 * do delete all webcomics, transcripts, storylines, characters, and
	 * languages. If a legacy configuration exists it will be reloaded.
	 * If the convert option has been set we run
	 * Webcomic::save_collection() on each collection and convert
	 * languages into tags.
	 * 
	 * @uses Webcomic::$config
	 * @uses Webcomic::api_request()
	 * @uses WebcomicAdmin::save_collection()
	 * @hook webcomic.php
	 */
	public function deactivate() {
		global $wpdb;
		
		if ( self::$config[ 'api' ] ) {
			self::api_request( 'key/' . self::$config[ 'api' ], 'DELETE' );
		}
		
		if ( self::$config[ 'uninstall' ] ) {
			$wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key = 'webcomic_birthday'" );
			
			if ( self::$config[ 'convert' ] ) {
				$language_cache = array();
				
				foreach ( array_keys( self::$config[ 'collections' ] ) as $k ) {
					self::save_collection( $k );
				}
				
				foreach ( get_terms( 'webcomic_language', array( 'get' => 'all' ) ) as $language ) {
					if ( $tag = ( integer ) $wpdb->get_var( $wpdb->prepare( "SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy} WHERE term_id = %d AND taxonomy = 'post_tag'", $language->term_id ) ) ) {
						$values           = array();
						$objects          = get_objects_in_term( $language->term_id, $language->taxonomy );
						$language_cache[] = $language->term_id;
						
						foreach ( $objects as $object ) {
							$values[] = $wpdb->prepare( '( %d, %d, %d ) ', $object, $tag, 0 );
							
							clean_post_cache( $object );
						}
						
						if ( $values ) {
							$wpdb->query( sprintf( "INSERT INTO {$wpdb->term_relationships} ( object_id, term_taxonomy_id, term_order ) VALUES %s ON DUPLICATE KEY UPDATE term_order = VALUES( term_order )", join( ',', $values ) ) );
							
							$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->term_relationships} WHERE term_taxonomy_id = %d", $language->term_id ) );
							
							$wpdb->update( $wpdb->term_taxonomy, array( 'count' => $count ), array( 'term_id' => $language->term_id, 'taxonomy' => 'post_tag' ) );
						}
						
						wp_delete_term( $language->term_id, $language->taxonomy );
						
						continue;
					}
					
					$wpdb->update( $wpdb->term_taxonomy, array( 'taxonomy' => 'post_tag' ), array( 'term_id' => $language->term_id, 'taxonomy' => $language->taxonomy ) );
				}
				
				if ( $language_cache = array_unique( array_values( $language_cache ) ) ) {
					clean_term_cache( $language_cache, 'post_tag' );
				}
			} else {
				$wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key = 'webcomic_collection'" );
				
				$post_types = array( 'webcomic_transcript' );
				$taxonomies = array( 'webcomic_language' );
				
				foreach ( array_keys( self::$config[ 'collections' ] ) as $k ) {
					$post_types[] = $k;
					$taxonomies[] = "{$k}_storyline";
					$taxonomies[] = "{$k}_character";
				}
				
				foreach ( get_terms( $taxonomies, array( 'get' => 'all' ) ) as $term ) {
					wp_delete_term( $term->term_id, $term->taxonomy );
				}
				
				foreach ( get_posts( array( 'numberposts' => -1, 'post_type' => $post_types, 'post_status' => get_post_stati() ) ) as $p ) {
					wp_delete_post( $p->ID, true );
				}
			}
			
			wp_clear_scheduled_hook( 'webcomic_buffer_alert' );
			
			delete_option( 'webcomic_options' );
			
			if ( $legacy = get_option( 'webcomic_legacy' ) ) {
				if ( 3 === self::$config[ 'legacy' ] ) {
					add_option( 'webcomic_options', $legacy );
				} elseif ( is_numeric( self::$config[ 'legacy' ] ) ) {
					add_option( 'webcomic_version', $legacy[ 'webcomic_version' ] );
					add_option( 'comic_category', $legacy[ 'comic_category' ] );
					add_option( 'comic_directory', $legacy[ 'comic_directory' ] );
					add_option( 'comic_current_chapter', $legacy[ 'comic_current_chapter' ] );
					add_option( 'comic_secure_names', $legacy[ 'comic_secure_names' ] );
					add_option( 'comic_feed', $legacy[ 'comic_feed' ] );
					add_option( 'comic_feed_size', $legacy[ 'comic_feed_size' ] );
					add_option( 'comic_large_size_w', $legacy[ 'comic_large_size_w' ] );
					add_option( 'comic_large_size_h', $legacy[ 'comic_large_size_h' ] );
					add_option( 'comic_medium_size_w', $legacy[ 'comic_medium_size_w' ] );
					add_option( 'comic_medium_size_h', $legacy[ 'comic_medium_size_h' ] );
					
					if ( 2 === self::$config[ 'legacy' ] ) {
						add_option( 'comic_buffer', $legacy[ 'comic_buffer' ] );
						add_option( 'comic_thumb_crop', $legacy[ 'comic_thumb_crop' ] );
						add_option( 'comic_buffer_alert', $legacy[ 'comic_buffer_alert' ] );
						add_option( 'comic_secure_paths', $legacy[ 'comic_secure_paths' ] );
						add_option( 'comic_thumb_size_w', $legacy[ 'comic_thumb_size_w' ] );
						add_option( 'comic_thumb_size_h', $legacy[ 'comic_thumb_size_h' ] );
						add_option( 'comic_keyboard_shortcuts', $legacy[ 'comic_keyboard_shortcuts' ] );
						add_option( 'comic_transcripts_allowed', $legacy[ 'comic_transcripts_allowed' ] );
						add_option( 'comic_transcripts_required', $legacy[ 'comic_transcripts_required' ] );
						add_option( 'comic_transcripts_loggedin', $legacy[ 'comic_transcripts_loggedin' ] );
					} else {
						add_option( 'comic_auto_post', $legacy[ 'comic_auto_post' ] );
						add_option( 'comic_name_format', $legacy[ 'comic_name_format' ] );
						add_option( 'comic_thumbnail_crop', $legacy[ 'comic_thumbnail_crop' ] );
						add_option( 'comic_name_format_date', $legacy[ 'comic_name_format_date' ] );
						add_option( 'comic_thumbnail_size_w', $legacy[ 'comic_thumbnail_size_w' ] );
						add_option( 'comic_thumbnail_size_h', $legacy[ 'comic_thumbnail_size_h' ] );
					}
				}
				
				delete_option( 'webcomic_legacy' );
			}
		}
		
		flush_rewrite_rules();
	}
	
	/** Handle dynamic requests and remove taxonomy submenus.
	 * 
	 * Dynamic request must have a 'webcomic_admin_ajax' value that is a
	 * valid callback in the form of a static class method, like
	 * 'Webcomic::method'.
	 * 
	 * @hook admin_init
	 */
	public function admin_init() {
		if ( ( isset( $_GET[ 'webcomic_admin_ajax' ] ) or isset( $_POST[ 'webcomic_admin_ajax' ] ) ) and isset( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) and 'xmlhttprequest' === strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) ) {
			call_user_func_array( explode( '::', isset( $_GET[ 'webcomic_admin_ajax' ] ) ? $_GET[ 'webcomic_admin_ajax' ] : $_POST[ 'webcomic_admin_ajax' ] ), isset( $_GET[ 'webcomic_admin_ajax' ] ) ? $_GET : $_POST );
			
			die;
		}
		
		foreach ( self::$config[ 'collections' ] as $k => $v ) {
			foreach ( $v[ 'taxonomies' ] as $taxonomy ) {
				if ( preg_match( '/^webcomic\d+_(storyline|character)$/', $taxonomy ) ) {
					remove_submenu_page( "edit.php?post_type={$k}", "edit-tags.php?taxonomy={$taxonomy}&amp;post_type={$k}" );
				}
			}
		}
	}
	
	/** Add contextual help and change the post type icon.
	 * 
	 * @uses Webcomic::$dir
	 * @uses Webcomic::$url
	 * @uses WebcomicHelp
	 * @hook admin_head
	 */
	public function admin_head() {
		$screen = get_current_screen();
		
		if ( preg_match( '/^(dashboard|page|options-media|tools_page_webcomic-commerce|tools_page_webcomic-upgrader|media_page_webcomic-attacher|media_page_webcomic-generator|admin_page_webcomic-term-sort|settings_page_webcomic-options|admin_page_webcomic-network|(edit-)?webcomic_(transcript|language)|(webcomic\d+_page_|edit-)?webcomic\d+(-options|_storyline|_character)?)$/', $screen->id ) ) {
			require_once self::$dir . '-/php/help.php';
			
			new WebcomicHelp( $screen );
			
			if ( preg_match( '/^(edit-)?webcomic_(transcript|language)$/', $screen->id ) ) {
				echo '<style>#icon-edit{background:url("', self::$url, '-/img/transcript.png")}</style>';
			} elseif ( 'admin_page_webcomic-term-sort' === $screen->id or preg_match( '/^(edit-)?webcomic\d+(_storyline|_character)?$/', $screen->id ) ) {
				echo '<style>#icon-edit{background:url("', self::$url, '-/img/webcomic.png")}</style>';
			}
		}
	}
	
	/** Render administrative notifications and the thank you message.
	 * 
	 * @uses Webcomic::$config
	 * @uses Webcomic::$version
	 * @hook admin_notices
	 */
	public function admin_notices() {
		if ( $notice = get_transient( 'webcomic_notice' ) ) {
			delete_transient( 'webcomic_notice' );
			
			echo '<div class="updated"><p>', join( '</p></div><div class="updated"><p>', $notice ), '</p></div>';
		}
		
		if ( $error = get_transient( 'webcomic_error' ) ) {
			delete_transient( 'webcomic_error' );
			
			echo '<div class="error"><p>', join( '</p></div><div class="error"><p>', $error ), '</p></div>';
		}
		
		if ( isset( self::$config[ 'thanks' ] ) ) {
			echo '<div class="updated webcomic"><a href="' . add_query_arg( array( 'cmd' => 's-xclick', 'hosted_button_id' => 'UD3J2DJPSN9UC' ), 'http://paypal.com/cgi-bin/webscr' ) . '" target="_blank"><b>&#x2764;</b>', sprintf( __( 'Thank you for using %s', 'webcomic' ), 'Webcomic ' . self::$version ), '</a></div>';
			
			unset( self::$config[ 'thanks' ] );
			
			update_option( 'webcomic_options', self::$config );
		}
	}
	
	/** Add administrative dashboard widgets.
	 * 
	 * @uses WebcomicAdmin::widget_showcase()
	 * @hook wp_dhasboard_setup
	 */
	public function wp_dashboard_setup() {
		wp_add_dashboard_widget( 'webcomic-showcase', __( 'Webcomic Showcase', 'webcomic' ), array( $this, 'widget_showcase' ), array( $this, 'widget_showcase_config' ) );
	}
	
	/** Enqueue custom styles for thank you message.
	 * 
	 * @uses Webcomic::$url
	 * @uses Webcomic::$config
	 * @hook admin_enqueue_scripts
	 */
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		
		if ( 'dashboard' === $screen->id and self::$config[ 'api' ] ) {
			wp_register_style( 'webcomic-google-font', 'http://fonts.googleapis.com/css?family=Maven+Pro' );
			
			wp_enqueue_style( 'webcomic-showcase', self::$url . '-/css/admin-showcase.css', array( 'webcomic-google-font' ) );
			
			wp_enqueue_script( 'webcomic-showcase', self::$url . '-/js/admin-showcase.js' );
		}
		
		if ( isset( self::$config[ 'thanks' ] ) ) {
			wp_register_style( 'webcomic-google-font', 'http://fonts.googleapis.com/css?family=Maven+Pro' );
			
			wp_enqueue_style( 'webcomic-special', self::$url . '-/css/admin-special.css', array( 'webcomic-google-font' ) );
		}
		
		if ( '41' === date( 'nj' ) ) {
			wp_register_script( 'webcomic-cornify', 'http://cornify.com/js/cornify.js' );
			
			wp_enqueue_script( 'webcomic-special', self::$url . '-/js/admin-special.js', array( 'webcomic-cornify' ) );
		}
	}
	
	/** Add donate link and uninstallation reminder.
	 * 
	 * @param array $meta Array of metadata.
	 * @param array $file Plugin basename.
	 * @param array $data Array of plugin data.
	 * @return array
	 * @uses Webcomic::$config
	 * @hook plugin_row_meta
	 */
	public function plugin_row_meta( $meta, $file, $data ) {
		if ( 'Webcomic' === $data[ 'Name' ] ) {
			$meta[] = '<a href="' . add_query_arg( array( 'cmd' => 's-xclick', 'hosted_button_id' => 'UD3J2DJPSN9UC' ), 'http://paypal.com/cgi-bin/webscr' ) . '" target="_blank">' . __( 'Donate', 'webcomic' ) . '</a>';
			
			if ( self::$config[ 'uninstall' ] ) {
				$meta[] = '<b style="color:#d98500">' . ( self::$config[ 'convert' ] ?  __( 'Webcomic data will be converted if the plugin is deactivated.', 'webcomic' ) : __( 'Webcomic data will be deleted if the plugin is deactivated.', 'webcomic' ) ) . '</b>';
			}
			
			if ( self::$config[ 'api' ] ) {
				$meta[] = '<b style="color:#bc0b0b">' . __( 'Webcomic Network data will be purged if the plugin is deactivated.', 'webcomic' ) . '</b>';
			}
		}
		
		return $meta;
	}
	
	/** Add quick links to the general settings page and official support.
	 * 
	 * @param array $actions Array of actions.
	 * @param array $file Plugin basename.
	 * @param array $data Array of plugin data.
	 * @param string $context
	 * @return array
	 * @hook plugin_action_links
	 */
	public function plugin_action_links( $actions, $file, $data, $context ) {
		if ( 'Webcomic' === $data[ 'Name' ] ) {
			$actions[ 'settings' ] = '<a href="' . esc_url( add_query_arg( array( 'page' => 'webcomic-options' ), admin_url( 'options-general.php' ) ) ) . '">' . __( 'Settings', 'webcomic' ) . '</a>';
			$actions[ 'support' ]  = '<a href="http://groups.google.com/d/forum/webcomicnu" target="_blank">' . __( 'Support', 'webcomic' ) . '</a>';
		}
		
		return $actions;
	}
	
	/** Save collection data as posts, categories, and tags.
	 * 
	 * @param string $collection ID of the collection to save.
	 */
	public static function save_collection( $collection ) {
		global $wpdb;
		
		$storyline_cache = $character_cache = array();
		
		$webcomics = get_posts( array( 'numberposts' => -1, 'post_type' => $collection, 'post_status' => get_post_stati() ) );
		$terms     = array_merge( ( array ) get_terms( "{$collection}_storyline", array( 'get' => 'all' ) ), ( array ) get_terms( "{$collection}_character", array( 'get' => 'all' ) ) );
		
		foreach ( $webcomics as $webcomic ) {
			wp_update_post( array( 'ID' => $webcomic->ID, 'post_type' => 'post' ) );
		}
		
		foreach ( $terms as $term ) {
			if ( $the_term = ( integer ) $wpdb->get_var( $wpdb->prepare( "SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy} WHERE term_id = %d AND taxonomy = %s", $term->term_id, "{$collection}_storyline" === $term->taxonomy ? 'category' : 'post_tag' ) ) ) {
				$values  = array();
				$objects = get_objects_in_term( $term->term_id, $term->taxonomy );
				
				if ( "{$collection}_storyline" === $term->taxonomy ) {
					$storyline_cache[] = $term->term_id;
				} else {
					$character_cache[] = $term->term_id;
				}
				
				foreach ( $objects as $object ) {
					$values[] = $wpdb->prepare( '( %d, %d, %d )', $object, $the_term, 0 );
					
					clean_post_cache( $object );
				}
				
				if ( $values ) {
					$wpdb->query( sprintf( "INSERT INTO {$wpdb->term_relationships} ( object_id, term_taxonomy_id, term_order ) VALUES %s ON DUPLICATE KEY UPDATE term_order = VALUES( term_order )", join( ',', $values ) ) );
					
					$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->term_relationships} WHERE term_taxonomy_id = %d", $term->term_id ) );
					
					$wpdb->update( $wpdb->term_taxonomy, array( 'count' => $count ), array( 'term_id' => $term->term_id, 'taxonomy' => "{$collection}_storyline" === $term->taxonomy ? 'category' : 'post_tag' ) );
				}
				
				wp_delete_term( $term->term_id, $term->taxonomy );
				
				continue;
			}
			
			$wpdb->update( $wpdb->term_taxonomy, array( 'taxonomy' => "{$collection}_storyline" === $term->taxonomy ? 'category' : 'post_tag' ), array( 'term_id' => $term->term_id, 'taxonomy' => $term->taxonomy ) );
		}
		
		if ( $storyline_cache = array_unique( array_values( $storyline_cache ) ) ) {
			clean_term_cache( $storyline_cache, 'category' );
		}
		
		if ( $character_cache = array_unique( array_values( $character_cache ) ) ) {
			clean_term_cache( $character_cache, 'post_tag' );
		}
	}
	
	/** Render the Webcomic Showcase dashboard widget.
	 * 
	 * @uses Webcomic::$config
	 * @uses Webcomic::api_request()
	 */
	public function widget_showcase() {
		if ( self::$config[ 'api' ] ) {
			$options = get_option( 'dashboard_widget_options' );
			$data    = array(
				'genre'  => isset( $options[ 'webcomic_showcase' ][ 'genre' ] ) ? $options[ 'webcomic_showcase' ][ 'genre' ] : array(),
				'rating' => isset( $options[ 'webcomic_showcase' ][ 'rating' ] ) ? $options[ 'webcomic_showcase' ][ 'rating' ] : array()
			);
			
			$http = self::api_request( 'showcase/' . ( isset( $options[ 'webcomic_showcase' ][ 'number' ] ) ? $options[ 'webcomic_showcase' ][ 'number' ] : 5 ), 'GET', $data );
			
			if ( isset( $http[ 200 ] ) ) {
				if ( empty( $http[ 200 ] ) ) {
					echo '<a href="', add_query_arg( array( 'page' => 'webcomic-network' ), admin_url( 'options.php' ) ), '" class="button">', __( 'Join the Webcomic Showcase', 'webcomic' ), '</a>';
				} else {
					echo '<div class="slideshow"><div>';
					
					foreach ( $http[ 200 ] as $entry ) {
						foreach ( $entry[ 'creators' ] as $k => $v ) {
							if ( 0 === strpos( $v, '@' ) ) {
								$entry[ 'creators' ][ $k ] = '<a href="http://twitter.com/' . str_replace( '@', '', $v ) . '" target="_blank">' . str_replace( '@', '', $v ) . '</a>';
							}
						}
						
						$caption = empty( $entry[ 'template' ] ) ? sprintf( __( 'Created by %1$s with %2$s', 'webcomic' ),
							join( ', ', $entry[ 'creators' ] ),
							'<a href="' . $entry[ 'theme' ][ 'url' ] . '" target="_blank">' . $entry[ 'theme' ][ 'name' ] . '</a>'
						) : sprintf( __( 'Created by %1$s with %3$s (based on %4$s)', 'webcomic' ),
							join( ', ', $entry[ 'creators' ] ),
							'<a href="' . $entry[ 'theme' ][ 'url' ] . '" target="_blank">' . $entry[ 'theme' ][ 'name' ] . '</a>',
							'<a href="' . $entry[ 'template' ][ 'url' ] . '" target="_blank">' . $entry[ 'template' ][ 'name' ] . '</a>'
						);
						
						echo '<figure><a href="', $entry[ 'url' ], '" target="_blank"><img src="', $entry[ 'image' ], '" alt=""><h4>', $entry[ 'name' ], '</h4><blockquote>', wpautop( $entry[ 'description' ] ), '</blockquote></a><figcaption>', $caption, ' <span>#', $entry[ 'rating' ], $entry[ 'genre' ] ? ' #' . join( ' #', $entry[ 'genre' ] ) : '', '</span></figcaption></figure>';
					}
					
					echo '</div></div>';
				}
			} else {
				echo '<p>', array_shift( $http ), '</p>';
			}
		} else {
			echo '<a href="', add_query_arg( array( 'page' => 'webcomic-network' ), admin_url( 'options.php' ) ), '" class="button">', __( 'Join the Webcomic Network', 'webcomic' ), '</a>';
		}
	}
	
	/** Render the Webcomic Showcase dashboard widget configuration form. */
	public function widget_showcase_config() {
		$options = get_option( 'dashboard_widget_options' );
		
		if ( empty( $options ) ) {
			$options = array();
		} elseif ( empty( $options[ 'webcomic_showcase' ] ) ) {
			$options[ 'webcomic_showcase' ] = array(
				'genre'  => array(),
				'rating' => array(),
				'number' => 5
			);
		}
		
		if ( $_POST and isset( $_POST[ 'widget_id' ] ) and 'webcomic-showcase' === $_POST[ 'widget_id' ] ) {
			$options[ 'webcomic_showcase' ] = array(
				'genre'  => 24 === count( $_POST[ 'webcomic_showcase' ][ 'genre' ] ) ? array() : $_POST[ 'webcomic_showcase' ][ 'genre' ],
				'rating' => 4 === count( $_POST[ 'webcomic_showcase' ][ 'rating' ] ) ? array() : $_POST[ 'webcomic_showcase' ][ 'rating' ],
				'number' => intval( $_POST[ 'webcomic_showcase' ][ 'number' ] )
			);
			
			update_option( 'dashboard_widget_options', $options );
		}
		
		?>
		<p>
			<b><label for="webcomic_showcase[number]"><?php _e( 'Number', 'webcomic' ); ?></label></b>
			<input type="number" name="webcomic_showcase[number]" id="webcomic_showcase[number]" min="1" max="10" value="<?php echo $options[ 'webcomic_showcase' ][ 'number' ]; ?>">
		</p>
		<p>
			<b><?php _e( 'Ratings', 'webcomic' ); ?></b>
			<?php
				foreach ( array(
					'everyone' => __( 'Everyone', 'webcomic' ),
					'teen'     => __( 'Teen', 'webcomic' ),
					'mature'   => __( 'Mature', 'webcomic' ),
					'adult'    => __( 'Adult', 'webcomic' )
				) as $k => $v ) {
					echo '<label><input type="checkbox" name="webcomic_showcase[rating][]" value="', $k, '"', checked( ( empty( $options[ 'webcomic_showcase' ][ 'rating' ] ) or in_array( $k, $options[ 'webcomic_showcase' ][ 'rating' ] ) ), true, false ), '> ', esc_html( $v ), '</label>';
				}
			?>
		</p>
		<hr>
		<p>
			<b><?php _e( 'Genres', 'webcomic' ); ?></b>
			<?php
				foreach ( array(
					'absurdist'     => __( 'Absurdist', 'webcomic' ),
					'adventure'     => __( 'Adventure', 'webcomic' ),
					'comedy'        => __( 'Comedy', 'webcomic' ),
					'drama'         => __( 'Drama', 'webcomic' ),
					'education'     => __( 'Education', 'webcomic' ),
					'experimental'  => __( 'Experimental', 'webcomic' ),
					'erotic'        => __( 'Erotic', 'webcomic' ),
					'fantasy'       => __( 'Fantasy', 'webcomic' ),
					'historical'    => __( 'Historical', 'webcomic' ),
					'horror'        => __( 'Horror', 'webcomic' ),
					'meta'          => __( 'Meta', 'webcomic' ),
					'nonfiction'    => __( 'Nonfiction', 'webcomic' ),
					'philosophical' => __( 'Philosophical', 'webcomic' ),
					'political'     => __( 'Political', 'webcomic' ),
					'pulp'          => __( 'Pulp', 'webcomic' ),
					'religious'     => __( 'Religious', 'webcomic' ),
					'romance'       => __( 'Romance', 'webcomic' ),
					'saga'          => __( 'Saga', 'webcomic' ),
					'satire'        => __( 'Satire', 'webcomic' ),
					'scifi'         => __( 'Science Fiction', 'webcomic' ),
					'suspense'      => __( 'Suspense', 'webcomic' ),
					'thriller'      => __( 'Thriller', 'webcomic' ),
					'tragedy'       => __( 'Tragedy', 'webcomic' ),
					'western'       => __( 'Western', 'webcomic' )
				) as $k => $v ) {
					echo '<label><input type="checkbox" name="webcomic_showcase[genre][]" value="', $k, '"', checked( ( empty( $options[ 'webcomic_showcase' ][ 'genre' ] ) or in_array( $k, $options[ 'webcomic_showcase' ][ 'genre' ] ) ), true, false ), '> ', esc_html( $v ), '</label>';
				}
			?>
		</p>
		<hr>
		<?php
	}
	
	/** Save administrative notifications.
	 * 
	 * @param string $message The message to display.
	 * @param string $type The type of message, one of 'notice' or 'error'.
	 */
	public static function notify( $message, $type = 'notice' ) {
		$notify = get_transient( "webcomic_{$type}" );
		
		set_transient( "webcomic_{$type}", array_merge( array( $message ), $notify ? $notify : array() ), 30 );
	}
}
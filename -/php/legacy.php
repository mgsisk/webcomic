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
	}
	
	/** Register legacy post types and taxonomies.
	 * 
	 * @uses Webcomic::$config
	 */
	public function init() {
		if ( 3 === self::$config[ 'legacy' ] ) {
			register_post_type( $k, array(
				'label'      => __( 'Webcomics', 'webcomic' ),
				'public'     => true,
				'supports'   => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions' ),
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
				'label' => 'Chapter',
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
			
			update_option( 'webcomic', self::$config );
			
			wp_redirect( html_entity_decode( wp_nonce_url( add_query_arg( array( 'action' => 'deactivate', 'plugin' => $file ), admin_url( 'plugins.php' ) ), 'deactivate-plugin_' . $file ) ) );
			
			die;
		}
		
		if ( isset( $_POST[ 'upgrade_legacy' ], $_POST[ 'webcomic_upgrade' ] ) and wp_verify_nonce( $_POST[ 'webcomic_upgrade' ], 'webcomic_upgrade' ) ) {
			
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
				
				update_option( 'webcomic', self::$config );
				
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
					<p><?php printf( __( 'This tool will attempt to automatically convert your existing Webcomic %s data to Webcomic %s. Depending on the size of your site the upgrade may require multiple steps. If you do not want to upgrade click <strong>Not Interested</strong> to uninstall Webcomic %s.', 'webcomic' ), self::$config[ 'legacy' ], self::$version, self::$version ); ?></p>
					<p style="color:#bc0b0b;font-size:larger;line-height:1.5"><strong><?php printf( __( 'Upgrades are not reversible. Please <a href="%s">read this</a> and <a href="%s">backup your site</a> before upgrading.', 'webcomic' ), '//webcomic.nu/upgrade', esc_url( admin_url( 'export.php' ) ) ); ?></strong></p>
					<form method="post">
						<?php wp_nonce_field( 'webcomic_upgrade', 'webcomic_upgrade' ); ?>
						<div class="form-wrap">
							<?php
								submit_button( __( 'Upgrade Now', 'webcomic' ), 'primary', 'upgrade_legacy', false );
								submit_button( __( "Not Interested", 'webcomic' ), 'secondary', 'disable_legacy', false );
							?>
						</div>
					</form>
				</div>
			</div>
		</div>
		<?php
	}
	
	/** Upgrade Webcomic 1 installations.
	 */
	private function upgrade1() {
		$timelimit     = ini_get( 'max_execution_time' );
		$legacy_config = get_option( 'webcomic_legacy' );
	}
	
	/** Upgrade Webcomic 2 installations.
	 */
	private function upgrade2() {
		$timelimit     = ini_get( 'max_execution_time' );
		$legacy_config = get_option( 'webcomic_legacy' );
	}
	
	/** Upgrade Webcomic 3 installations.
	 */
	private function upgrade3() {
		$timelimit     = ini_get( 'max_execution_time' );
		$legacy_config = get_option( 'webcomic_legacy' );
	}
}
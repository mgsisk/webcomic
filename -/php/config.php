<?php
/** Contains the WebcomicConfig class.
 * 
 * @package Webcomic
 */

/** Handle configuration tasks.
 * 
 * @package Webcomic
 */
class WebcomicConfig extends Webcomic {
	/** Register hooks.
	 * 
	 * @uses WebcomicConfig::admin_init()
	 * @uses WebcomicConfig::admin_menu()
	 * @uses WebcomicConfig::admin_enqueue_scripts()
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}
	
	/** Regester settings via the Settings API.
	 * 
	 * Registers the 'webcomic' setting (an array that stores all of the
	 * plugin's configuration data), adds setting sections and fields for
	 * the plugin settings page, individual collection settings pages,
	 * and the media page, and flushes rewrite rules when settings are
	 * updated to support adding/editing custom post types and
	 * taxonomies.
	 * 
	 * @uses Webcomic::$config
	 * @uses Webcomic::$notice
	 * @uses WebcomicConfig::save()
	 * @uses WebcomicConfig::section()
	 * @uses WebcomicConfig::integrate()
	 * @uses WebcomicConfig::navigate()
	 * @uses WebcomicConfig::uninstall()
	 * @uses WebcomicConfig::collections()
	 * @uses WebcomicConfig::sizes()
	 * @uses WebcomicConfig::collection_name()
	 * @uses WebcomicConfig::collection_description()
	 * @uses WebcomicConfig::collection_image()
	 * @uses WebcomicConfig::collection_theme()
	 * @uses WebcomicConfig::collection_buffer()
	 * @uses WebcomicConfig::collection_feeds()
	 * @uses WebcomicConfig::collection_transcripts_default()
	 * @uses WebcomicConfig::collection_transcripts_permission()
	 * @uses WebcomicConfig::collection_transcripts_notify()
	 * @uses WebcomicConfig::collection_transcripts_languages()
	 * @uses WebcomicConfig::collection_commerce_business()
	 * @uses WebcomicConfig::collection_commerce_prints()
	 * @uses WebcomicConfig::collection_commerce_sales()
	 * @uses WebcomicConfig::collection_commerce_prices()
	 * @uses WebcomicConfig::collection_commerce_shipping()
	 * @uses WebcomicConfig::collection_commerce_donation()
	 * @uses WebcomicConfig::collection_commerce_currency()
	 * @uses WebcomicConfig::collection_access_age()
	 * @uses WebcomicConfig::collection_access_roles()
	 * @uses WebcomicConfig::collection_supports_content()
	 * @uses WebcomicConfig::collection_supports_discussion()
	 * @uses WebcomicConfig::collection_supports_miscellanea()
	 * @uses WebcomicConfig::collection_slugs_archive()
	 * @uses WebcomicConfig::collection_slugs_webcomic()
	 * @uses WebcomicConfig::collection_slugs_storyline()
	 * @uses WebcomicConfig::collection_slugs_character()
	 * @uses WebcomicConfig::save_sizes()
	 * @hook admin_init
	 */
	public function admin_init() {
		register_setting( 'webcomic-options', 'webcomic_options', array( $this, 'save' ) );
		
		add_settings_section( 'webcomic-main', '', array( $this, 'section' ), 'webcomic-options' );
		add_settings_field( 'webcomic_integrate', __( 'Integrate', 'webcomic' ), array( $this, 'integrate' ), 'webcomic-options', 'webcomic-main', array( 'label_for' => 'webcomic_integrate' ) );
		add_settings_field( 'webcomic_navigate', __( 'Navigate', 'webcomic' ), array( $this, 'navigate' ), 'webcomic-options', 'webcomic-main', array( 'label_for' => 'webcomic_dynamic' ) );
		add_settings_field( 'webcomic_uninstall', __( 'Uninstall', 'webcomic' ), array( $this, 'uninstall' ), 'webcomic-options', 'webcomic-main', array( 'label_for' => 'webcomic_uninstall' ) );
		
		add_settings_section( 'webcomic-collections', __( 'Collections', 'webcomic' ), array( $this, 'collections' ), 'webcomic-options' );
		
		add_settings_section( 'webcomic-sizes', __( 'Additional Image Sizes', 'webcomic' ), array( $this, 'sizes' ), 'media' );
		
		foreach ( array_keys( self::$config[ 'collections' ] ) as $k ) {
			add_settings_section( "{$k}-main", __( 'General Settings', 'webcomic' ), array( $this, 'section' ), "{$k}-options" );
			add_settings_field( "{$k}_name", __( 'Name', 'webcomic' ), array( $this, 'collection_name' ), "{$k}-options", "{$k}-main", array( 'label_for' => 'webcomic_name' ) );
			add_settings_field( "{$k}_slug", __( 'Slug', 'webcomic' ), array( $this, 'collection_slug' ), "{$k}-options", "{$k}-main", array( 'label_for' => 'webcomic_slug' ) );
			add_settings_field( "{$k}_description", __( 'Description', 'webcomic' ), array( $this, 'collection_description' ), "{$k}-options", "{$k}-main", array( 'label_for' => 'webcomic_description' ) );
			add_settings_field( "{$k}_image", __( 'Poster', 'webcomic' ), array( $this, 'collection_image' ), "{$k}-options", "{$k}-main", array( 'label_for' => 'webcomic_image' ) );
			add_settings_field( "{$k}_theme", __( 'Theme', 'webcomic' ), array( $this, 'collection_theme' ), "{$k}-options", "{$k}-main", array( 'label_for' => 'webcomic_theme' ) );
			add_settings_field( "{$k}_buffer", __( 'Buffer', 'webcomic' ), array( $this, 'collection_buffer' ), "{$k}-options", "{$k}-main", array( 'label_for' => 'webcomic_buffer_hook' ) );
			add_settings_field( "{$k}_feeds", __( 'Feeds', 'webcomic' ), array( $this, 'collection_feeds' ), "{$k}-options", "{$k}-main", array( 'label_for' => 'webcomic_feeds_main' ) );
			
			add_settings_section( "{$k}-transcripts", __( 'Transcript Settings', 'webcomic' ), array( $this, 'section' ), "{$k}-options" );
			add_settings_field( "{$k}_transcripts_default", __( 'Default', 'webcomic' ), array( $this, 'collection_transcripts_default' ), "{$k}-options", "{$k}-transcripts", array( 'label_for' => 'webcomic_transcripts_open' ) );
			add_settings_field( "{$k}_transcripts_permission", __( 'Permission', 'webcomic' ), array( $this, 'collection_transcripts_permission' ), "{$k}-options", "{$k}-transcripts", array( 'label_for' => 'webcomic_transcripts_permission' ) );
			add_settings_field( "{$k}_transcripts_notify", __( 'Notification', 'webcomic' ), array( $this, 'collection_transcripts_notify' ), "{$k}-options", "{$k}-transcripts", array( 'label_for' => 'webcomic_transcripts_hook' ) );
			add_settings_field( "{$k}_transcripts_languages", __( 'Languages', 'webcomic' ), array( $this, 'collection_transcripts_languages' ), "{$k}-options", "{$k}-transcripts", array( 'label_for' => 'webcomic_transcripts_languages' ) );
			
			add_settings_section( "{$k}-commerce", __( 'Commerce Settings', 'webcomic' ), array( $this, 'section' ), "{$k}-options" );
			add_settings_field(  "{$k}_commerce_business", __( 'Business Email', 'webcomic' ), array( $this, 'collection_commerce_business' ), "{$k}-options", "{$k}-commerce", array( 'label_for' => 'webcomic_commerce_business' )  );
			add_settings_field(  "{$k}_commerce_prints", __( 'Prints', 'webcomic' ), array( $this, 'collection_commerce_prints' ), "{$k}-options", "{$k}-commerce", array( 'label_for' => 'webcomic_commerce_prints' )  );
			add_settings_field(  "{$k}_commerce_sales", __( 'Sales', 'webcomic' ), array( $this, 'collection_commerce_sales' ), "{$k}-options", "{$k}-commerce", array( 'label_for' => 'webcomic_commerce_method' )  );
			add_settings_field(  "{$k}_commerce_price", __( 'Prices', 'webcomic' ), array( $this, 'collection_commerce_prices' ), "{$k}-options", "{$k}-commerce", array( 'label_for' => 'webcomic_commerce_prices_domestic' )  );
			add_settings_field(  "{$k}_commerce_shipping", __( 'Shipping', 'webcomic' ), array( $this, 'collection_commerce_shipping' ), "{$k}-options", "{$k}-commerce", array( 'label_for' => 'webcomic_commerce_shipping_domestic' )  );
			add_settings_field(  "{$k}_commerce_donation", __( 'Donations', 'webcomic' ), array( $this, 'collection_commerce_donation' ), "{$k}-options", "{$k}-commerce", array( 'label_for' => 'webcomic_commerce_donation' )  );
			add_settings_field(  "{$k}_commerce_currency", __( 'Currency', 'webcomic' ), array( $this, 'collection_commerce_currency' ), "{$k}-options", "{$k}-commerce", array( 'label_for' => 'webcomic_commerce_currency' )  );
			
			add_settings_section( "{$k}-access", __( 'Access Settings', 'webcomic' ), array( $this, 'section' ), "{$k}-options" );
			add_settings_field( "{$k}_access_age", __( 'Age', 'webcomic' ), array( $this, 'collection_access_age' ), "{$k}-options", "{$k}-access", array( 'label_for' => 'webcomic_access_byage' ) );
			add_settings_field( "{$k}_access_roles", __( 'Role', 'webcomic' ), array( $this, 'collection_access_roles' ), "{$k}-options", "{$k}-access", array( 'label_for' => 'webcomic_access_byrole' ) );
			
			add_settings_section( "{$k}-features", __( 'Post Settings', 'webcomic' ), array( $this, 'section' ), "{$k}-options" );
			add_settings_field( "{$k}_supports_content", __( 'Content', 'webcomic' ), array( $this, 'collection_supports_content' ), "{$k}-options", "{$k}-features", array( 'label_for' => 'webcomic_posts_title' ) );
			add_settings_field( "{$k}_supports_discussion", __( 'Discussion', 'webcomic' ), array( $this, 'collection_supports_discussion' ), "{$k}-options", "{$k}-features", array( 'label_for' => 'webcomic_posts_comments' ) );
			add_settings_field( "{$k}_supports_miscellanea", __( 'Miscellanea', 'webcomic' ), array( $this, 'collection_supports_miscellanea' ), "{$k}-options", "{$k}-features", array( 'label_for' => 'webcomic_posts_revisions' ) );
			add_settings_field( "{$k}_supports_taxonomies", __( 'Taxonomies', 'webcomic' ), array( $this, 'collection_supports_taxonomies' ), "{$k}-options", "{$k}-features", array( 'label_for' => 'webcomic_posts_taxonomy' ) );
			
			add_settings_section( "{$k}-permalinks", __( 'Permalink Settings', 'webcomic' ), array( $this, 'section' ), "{$k}-options" );
			add_settings_field( "{$k}_slug_archive", __( 'Archive', 'webcomic' ), array( $this, 'collection_slugs_archive' ), "{$k}-options", "{$k}-permalinks", array( 'label_for' => 'webcomic_slugs_archive' ) );
			add_settings_field( "{$k}_slug_webcomic", __( 'Webcomics', 'webcomic' ), array( $this, 'collection_slugs_webcomic' ), "{$k}-options", "{$k}-permalinks", array( 'label_for' => 'webcomic_slugs_webcomic' ) );
			add_settings_field( "{$k}_slug_storyline", __( 'Storylines', 'webcomic' ), array( $this, 'collection_slugs_storyline' ), "{$k}-options", "{$k}-permalinks", array( 'label_for' => 'webcomic_slugs_storyline' ) );
			add_settings_field( "{$k}_slug_character", __( 'Characters', 'webcomic' ), array( $this, 'collection_slugs_character' ), "{$k}-options", "{$k}-permalinks", array( 'label_for' => 'webcomic_slugs_character' ) );
			
			add_settings_section( "{$k}-twitter" , __( 'Twitter Settings', 'webcomic' ), array( $this, 'section' ), "{$k}-options" );
			add_settings_field( "{$k}_twitter_account", __( 'Authorized Account', 'webcomic' ), array( $this, 'collection_twitter_account' ), "{$k}-options", "{$k}-twitter" );
			add_settings_field( "{$k}_twitter_consumer_key", __( 'Consumer Key', 'webcomic' ), array( $this, 'collection_twitter_consumer_key' ), "{$k}-options", "{$k}-twitter", array( 'label_for' => 'webcomic_twitter_consumer_key' ) );
			add_settings_field( "{$k}_twitter_consumer_secret", __( 'Consumer Secret', 'webcomic' ), array( $this, 'collection_twitter_consumer_secret' ), "{$k}-options", "{$k}-twitter", array( 'label_for' => 'webcomic_twitter_consumer_secret' ) );
			add_settings_field( "{$k}_twitter_format", __( 'Tweet Format', 'webcomic' ), array( $this, 'collection_twitter_format' ), "{$k}-options", "{$k}-twitter", array( 'label_for' => 'webcomic_twitter_format' ) );
		}
		
		if ( ( isset( $_GET[ 'page' ], $_GET[ 'settings-updated' ] ) and 'webcomic-options' === $_GET[ 'page' ] and 'true' === $_GET[ 'settings-updated' ] ) or ( isset( $_GET[ 'page' ], $_GET[ 'post_type' ], $_GET[ 'settings-updated' ] ) and preg_match( '/^webcomic\d+-options$/', $_GET[ 'page' ] ) and isset( self::$config[ 'collections' ][ $_GET[ 'post_type' ] ] ) and 'true' === $_GET[ 'settings-updated' ] ) ) {
			flush_rewrite_rules();
		}
		
		if ( isset( $_GET[ 'page' ], $_GET[ 'settings-updated'] ) and 'true' === $_GET[ 'settings-updated' ] and preg_match( '/^webcomic\d+-options$/', $_GET[ 'page' ] ) ) {
			self::$notice[] = sprintf( '<strong>%s</strong>', __( 'Settings saved.', 'webcomic' ) );
		}
		
		$this->save_sizes();
	}
	
	/** Register submenu settings pages.
	 * 
	 * @uses Webcomic::$config
	 * @uses WebcomicConfig::page()
	 * @hook admin_menu
	 */
	public function admin_menu() {
		add_submenu_page( 'options-general.php', __( 'Webcomic Settings', 'webcomic' ), __( 'Webcomic', 'webcomic' ), 'manage_options', 'webcomic-options', array( $this, 'page' ) );
		
		foreach ( self::$config[ 'collections' ] as $k => $v ) {
			add_submenu_page( "edit.php?post_type={$k}", sprintf( __( '%s Settings', 'webcomic' ), esc_html( $v[ 'name' ] ) ), __( 'Settings', 'webcomic' ), 'manage_options', "{$k}-options", array( $this, 'page' ) );
		}
	}
	
	/** Register and enqueue settings scripts.
	 * 
	 * @uses Webcomic::$url
	 * @hook admin_enqueue_scripts
	 */
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		
		if ( preg_match( '/^webcomic\d+_page_webcomic\d+-options$/', $screen->id ) ) {
			wp_register_script( 'webcomic-config', self::$url . '-/js/admin-config.js', array( 'jquery' ) );
			
			wp_enqueue_script( 'webcomic-config' );
			
			wp_enqueue_media();
		}
	}
	
	/** Render the Integrate setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function integrate() {
		?>
		<label><input type="checkbox" name="webcomic_integrate" id="webcomic_integrate"<?php checked( self::$config[ 'integrate' ] ); ?>> <?php _e( 'Attempt to integrate webcomics into the site automatically', 'webcomic' ); ?></label>
		<?php
	}
	
	/** Render the Navigate settings.
	 * 
	 * @uses Webcomic::$config
	 */
	public function navigate() {
		?>
		<label><input type="checkbox" name="webcomic_dynamic" id="webcomic_dynamic"<?php checked( self::$config[ 'dynamic' ] ); ?>> <?php _e( 'Enable dynamic webcomic navigation', 'webcomic' ); ?></label><br>
		<label><input type="checkbox" name="webcomic_gestures"<?php checked( self::$config[ 'gestures' ] ); ?>> <?php _e( 'Enable touch gestures for webcomic navigation', 'webcomic' ); ?></label><br>
		<label><input type="checkbox" name="webcomic_shortcuts"<?php checked( self::$config[ 'shortcuts' ] ); ?>> <?php _e( 'Enable keyboard shortcuts for webcomic navigation', 'webcomic' ); ?></label>
		<?php
	}
	
	/** Render the Uninstall setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function uninstall() {
		?>
		<label><input type="checkbox" name="webcomic_uninstall" id="webcomic_uninstall"<?php checked( self::$config[ 'uninstall' ] ); ?>> <?php _e( 'Delete Webcomic data when the plugin is deactivated', 'webcomic' ); ?></label><br>
		<label><input type="checkbox" name="webcomic_convert"<?php checked( self::$config[ 'convert' ] ); ?>> <?php _e( 'Save webcomics and transcripts as posts, storylines as categories, and characters and languages as tags', 'webcomic' ); ?></label>
		<p class="description"><?php _e( 'This cannot be undone. Uploaded media will not be deleted.', 'webcomic' ); ?></p>
		<?php
	}
	
	/** Render the Collections setting section.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collections() {
		$columns = sprintf( '
			<tr>
				<th class="check-column"><input type="checkbox"></th>
				<th>%s</th>
				<th>%s</th>
				<th>%s</th>
				<th>%s</th>
			</tr>',
			__( 'Collection', 'webcomic' ),
			__( 'Webcomics', 'webcomic' ),
			__( 'Storylines', 'webcomic' ),
			__( 'Characters', 'webcomic' )
		);
		?>
		<div class="tablenav top">
			<div class="alignleft actions">
				<input type="text" name="webcomic_new_collection" placeholder="<?php esc_attr_e( 'Name', 'webcomic' ); ?>" class="regular-text">
				<?php submit_button( __( 'Add Collection', 'webcomic' ), 'secondary', 'webcomic_add_collection', false, array( 'id' => 'doaction' ) ); ?>
			</div>
		</div>
		<table class="wp-list-table widefat fixed posts">
			<thead><?php echo $columns; ?></thead>
			<tfoot><?php echo $columns; ?></tfoot>
			<tbody>
				<?php
				$i = 0;
				
				foreach ( self::$config[ 'collections' ] as $k => $v ) {
					$preview    = 
					$webcomics  = wp_count_posts( $k )->publish;
					$storylines = wp_count_terms( "{$k}_storyline" );
					$characters = wp_count_terms( "{$k}_character" );
				?>
				<tr<?php echo $i % 2 ? '' : ' class="alternate"'; ?>>
					<th class="check-column">
					<?php if ( 'webcomic1' !== $k ) { ?>
						<input type="checkbox" name="webcomic_collection[]" value="<?php echo $k; ?>">
					<?php } else { echo '&nbsp;'; } ?>
					</th>
					<td><a href="<?php echo esc_url( add_query_arg( array( 'post_type' => $k, 'page' => "{$k}-options" ), admin_url( 'edit.php' ) ) ); ?>" class="row-title"><?php echo $v[ 'image' ] ? wp_get_attachment_image( $v[ 'image' ] ) : esc_html( $v[ 'name' ] ); ?></a></td>
					<td><a href="<?php echo esc_url( add_query_arg( array( 'post_type' => $k ), admin_url( 'edit.php' ) ) ); ?>"><?php echo $webcomics; ?></a></td>
					<td><a href="<?php echo esc_url( add_query_arg( array( 'post_type' => $k, 'taxonomy' => "{$k}_storyline" ), admin_url( 'edit-tags.php' ) ) ); ?>"><?php echo $storylines; ?></a></td>
					<td><a href="<?php echo esc_url( add_query_arg( array( 'post_type' => $k, 'taxonomy' => "{$k}_character" ), admin_url( 'edit-tags.php' ) ) ); ?>"><?php echo $characters; ?></a></td>
				</tr>
				<?php $i++; } ?>
			</tbody>
		</table>
		<div class="tablenav bottom">
			<div class="alignleft actions">
				<select name="webcomic_bulk_collection" id="webcomic_bulk_collection">
					<option value=""><?php _e( 'Bulk Actions', 'webcomic' ); ?></option>
					<option value="delete_save"><?php _e( 'Delete and Save', 'webcomic' ); ?></option>
					<option value="delete"><?php _e( 'Delete Permanently', 'webcomic' ); ?></option>
				</select>
			</div>
		</div>
		<?php
	}
	
	/** Render the Additional Sizes settings section.
	 * 
	 * @uses Webcomic::$config
	 */
	public function sizes() {
		global $_wp_additional_image_sizes;
		
		wp_nonce_field( 'webcomic_media_sizes', 'webcomic_media_sizes' );
		
		$columns = sprintf( '
			<tr>
				<th class="check-column"><input type="checkbox"></th>
				<th><label for="webcomic_new_size">%s</label></th>
				<th><label for="webcomic_new_size_width">%s</label></th>
				<th><label for="webcomic_new_size_height">%s</label></th>
				<th><label for="webcomic_new_size_crop">%s</label></th>
			</tr>',
			__( 'Name', 'webcomic' ),
			__( 'Width', 'webcomic' ),
			__( 'Height', 'webcomic' ),
			__( 'Crop', 'webcomic' )
		);
		?>
		<table class="wp-list-table widefat fixed posts">
			<thead><?php echo $columns; ?></thead>
			<tfoot><?php echo $columns; ?></tfoot>
			<tbody>
				<tr class="alternate">
					<th class="check-column">&nbsp;</th>
					<td><input type="text" name="webcomic_new_size" id="webcomic_new_size" placeholder="<?php esc_attr_e( 'New Size', 'webcomic' ); ?>"></td>
					<td><input type="number" name="webcomic_new_size_width" id="webcomic_new_size_width" min="0" class="small-text"></td>
					<td><input type="number" name="webcomic_new_size_height" id="webcomic_new_size_height" min="0" class="small-text"></td>
					<td><input type="checkbox" name="webcomic_new_size_crop" id="webcomic_new_size_crop"></td>
				</tr>
				<?php
				$i = 1;
				
				foreach ( get_intermediate_image_sizes() as $size ) {
					if ( empty( $_wp_additional_image_sizes[ $size ] ) ) {
						continue;
					}
					
					$disabled = disabled( empty( self::$config[ 'sizes' ][ $size ] ), true, false );
				?>
				<tr<?php echo $i % 2 ? '' : ' class="alternate"'; ?>>
					<th class="check-column">
					<?php if ( isset( self::$config[ 'sizes' ][ $size ] ) ) { ?>
						<input type="checkbox" name="webcomic_sizes[]" value="<?php echo $size; ?>">
					<?php } else { echo '&nbsp;'; } ?>
					</th>
					<td><?php echo $size; ?></td>
					<td><input type="number" name="webcomic_size[<?php echo $size; ?>][width]" value="<?php echo $_wp_additional_image_sizes[ $size ][ 'width' ]; ?>" class="small-text"<?php echo $disabled; ?>></td>
					<td><input type="number" name="webcomic_size[<?php echo $size; ?>][height]" value="<?php echo $_wp_additional_image_sizes[ $size ][ 'height' ]; ?>" class="small-text"<?php echo $disabled; ?>></td>
					<td><input name="webcomic_size[<?php echo $size; ?>][crop]"  type="checkbox"<?php echo checked( $_wp_additional_image_sizes[ $size ][ 'crop' ], true, false ), $disabled; ?>></td>
				</tr>
				<?php $i++; } ?>
			</tbody>
		</table>
		<div class="tablenav bottom">
			<div class="alignleft actions">
				<select name="webcomic_bulk_size" id="webcomic_bulk_size">
					<option value=""><?php _e( 'Bulk Actions', 'webcomic' ); ?></option>
					<option value="delete"><?php _e( 'Delete', 'webcomic' ); ?></option>
				</select>
			</div>
		</div>
		<?php
	}
	
	/** Render the Name setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_name() {
		?>
		<input type="text" name="webcomic_name" id="webcomic_name" value="<?php echo esc_attr( self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'name' ] ); ?>" class="regular-text">
		<?php
	}
	
	/** Render the Slug setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_slug() {
		?>
		<input type="text" name="webcomic_slug" id="webcomic_slug" value="<?php echo esc_attr( self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'slugs' ][ 'name' ] ); ?>" class="regular-text">
		<p class="description"><?php _e( 'The The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'webcomic' ); ?></p>
		<?php
	}
	
	/** Render the Description setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_description() {
		?>
		<textarea name="webcomic_description" id="webcomic_description" rows="5" cols="50" class="large-text"><?php echo esc_html( self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'description' ] ); ?></textarea>
		<span class="description"><?php _e( 'The description is not prominent by default; it may be used in various ways, however.', 'webcomic' ); ?></span>
		<?php
	}
	
	/** Render the Cover setting.
	 * 
	 * @uses Webcomic::$config
	 * @uses WebcomicConfig::ajax_collection_image()
	 */
	public function collection_image() {
		?>
		<div id="webcomic_collection_image" data-webcomic-admin-url="<?php echo admin_url(); ?>"><?php self::ajax_collection_image( self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'image' ], $_GET[ 'post_type' ] ); ?></div>
		<p class="description"><?php _e( "The poster is a representative image that can be displayed on your site. Don't forget to <strong>Save Changes</strong> after updating the poster.", 'webcomic' ); ?></p>
		<?php
	}
	
	/** Render the Theme setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_theme() {
		?>
		<select name="webcomic_theme" id="webcomic_theme">
			<option value=""><?php _e( '(current theme)', 'webcomic' ); ?></option>
			<?php
				foreach ( wp_get_themes() as $theme ) {
					printf(
						'<option value="%s|%s"%s>%s</option>',
						$theme[ 'Template' ],
						$theme[ 'Stylesheet' ],
						selected( $theme[ 'Template' ] . '|' . $theme[ 'Stylesheet' ], self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'theme' ], false ),
						esc_html( $theme[ 'Name' ] )
					);
				}
			?>
		</select>
		<span class="description"><?php _e( 'The theme will be used for pages related to this collection.', 'webcomic' ); ?></span>
		<?php
	}
	
	/** Render the Feeds setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_feeds() {
		$select = '<select name="webcomic_feeds_size">';
		
		foreach ( get_intermediate_image_sizes() as $size ) {
			$select .= sprintf( '<option value="%s"%s>%s</option>',
				$size,
				selected( $size, self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'feeds' ][ 'size' ], false ),
				$size
			);
		}
		
		$select .= sprintf( '<option value="full"%s>%s</option></select>',
			selected( 'full', self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'feeds' ][ 'size' ], false ),
			__( 'full', 'webcomic' )
		);
		
		?>
		<label><input type="checkbox" name="webcomic_feeds_main" id="webcomic_feeds_main"<?php checked( self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'feeds' ][ 'main' ] ); ?>> <?php _e( 'Include webcomics in the main syndication feed', 'webcomic' ); ?></label><br>
		<label><input type="checkbox" name="webcomic_feeds_hook" id="webcomic_feeds_hook"<?php checked( self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'feeds' ][ 'hook' ] ); ?>></label>
		<label><?php printf( __( 'Show %s previews in syndication feeds', 'webcomic' ), $select ); ?></label>
		<?php
	}
	
	/** Render the Buffer setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_buffer() {
		?>
		<label><input type="checkbox" name="webcomic_buffer_hook" id="webcomic_buffer_hook"<?php checked( self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'buffer' ][ 'hook' ] ); ?>></label>
		<?php
		printf(
			__( '<label>Start sending e-mail reminders to %s</label> <label>%s days before the buffer runs out</label>', 'webcomic' ),
			sprintf( '<input type="email" name="webcomic_buffer_email" value="%s">', esc_attr( self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'buffer' ][ 'email' ] ) ),
			sprintf( '<input type="number" name="webcomic_buffer_days" value="%s" min="0" class="small-text" style="text-align:center">', self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'buffer' ][ 'days' ] )
		);
	}
	
	/** Render the Transcripts setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_transcripts_default() {
		?>
		<label><input type="checkbox" name="webcomic_transcripts_open" id="webcomic_transcripts_open"<?php checked( self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'transcripts' ][ 'open' ] ); ?>> <?php _e( 'Allow people to transcribe new webcomics', 'webcomic' ); ?></label>
		<?php
	}
	
	/** Render the Transcripts setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_transcripts_permission() {
		echo '<label>', sprintf( __( '%s may transcribe webcomics', 'webcomic' ), sprintf( '
			<select name="webcomic_transcripts_permission" id="webcomic_transcripts_permission">
				<option value="everyone"%s>%s</option>
				<option value="identify"%s>%s</option>
				<option value="register"%s>%s</option>
			</select>',
			selected( 'everyone', self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'transcripts' ][ 'permission' ], false ),
			__( 'Anyone', 'webcomic' ),
			selected( 'identify', self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'transcripts' ][ 'permission' ], false ),
			__( 'Self-identified users', 'webcomic' ),
			selected( 'register', self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'transcripts' ][ 'permission' ], false ),
			__( 'Registered users', 'webcomic' )
		) ), '</label>';
	}
	
	/** Render the Transcripts setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_transcripts_notify() {
		?>
		<label><input type="checkbox" name="webcomic_transcripts_hook" id="webcomic_transcripts_hook"<?php checked( self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'transcripts' ][ 'notify' ][ 'hook' ] ); ?>></label>
		<?php
		printf(
			__( '<label>Send email notifications to %s whenever a transcript is submitted</label>', 'webcomic' ),
			sprintf( '<input type="email" name="webcomic_transcripts_email" value="%s">', esc_attr( self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'transcripts' ][ 'notify' ][ 'email' ] ) )
		);
	}
	
	/** Render the Transcripts > Languages setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_transcripts_languages() {
		?>
		<select name="webcomic_transcripts_languages[]" id="webcomic_transcripts_languages" style="min-height:8em;vertical-align:top" multiple>
			<optgroup label="<?php esc_attr_e( 'Transcript Languages', 'webcomic' ); ?>">
				<option value="!"<?php selected( '!', self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'transcripts' ][ 'languages' ][ 0 ] ); ?>><?php _e( '(any)', 'webcomic' ); ?></option>
				<?php
					if ( $terms = get_terms( 'webcomic_language', array( 'get' => 'all' ) ) and !is_wp_error( $terms ) ) {
						foreach ( $terms as $term ) {
							printf( '<option value="%s"%s>%s</option>',
								$term->term_id,
								selected( in_array( $term->term_id, self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'transcripts' ][ 'languages' ] ), true, false ),
								$term->name
							);
						}
					}
				?>
			</optgroup>
		</select>
		<span class="description"><?php echo _e( 'Hold <code>CTRL</code>, <code>Command</code>, or <code>Shift</code> to select multiple languages.', 'webcomic' ); ?></span><br>
		<a href="<?php echo add_query_arg( array( 'taxonomy' => 'webcomic_language', 'post_type' => 'webcomic_transcript' ), admin_url( 'edit-tags.php' ) ); ?>" class="button"><?php echo _e( 'Manage Languages', 'webcomic' ); ?></a>
		<?php
	}
	
	/** Render the Commerce > Business Email setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_commerce_business() {
		?>
		<input type="email" name="webcomic_commerce_business" id="webcomic_commerce_business" value="<?php echo esc_attr( self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'commerce' ][ 'business' ] ); ?>" class="regular-text">
		<span><a href="//paypal.com" target="_blank" class="button"><?php _e( 'Get a PayPal Account', 'webcomic' ); ?></a></span>
		<?php
	}
	
	/** Render the Commerce > Default webcomic settings setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_commerce_prints() {
		?>
		<label><input type="checkbox" name="webcomic_commerce_prints" id="webcomic_commerce_prints"<?php checked( self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'commerce' ][ 'prints' ] ); ?>> <span><?php _e( 'Sell prints of new webcomics', 'webcomic' ); ?></span></label><br>
		<label><input type="checkbox" name="webcomic_commerce_originals" id="webcomic_commerce_originals"<?php checked( self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'commerce' ][ 'originals' ] ); ?>> <span><?php _e( 'Sell an original, traditional-media print of new webcomics', 'webcomic' ); ?></span></label><br>
		<?php
	}
	
	/** Render the Commerce > Sales setting.
	 * @uses Webcomic::$config
	 */
	public function collection_commerce_sales() {
		?>
		<label>
		<?php
			printf( __( 'Sell prints using the %s method', 'webcomic' ),
				sprintf( '
					<select name="webcomic_commerce_method" id="webcomic_commerce_method">
						<option value="_xclick"%s>%s</option>
						<option value="_cart"%s>%s</option>
					</select>',
					selected( '_xclick', self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'commerce' ][ 'method' ], false ),
					__( 'single item', 'webcomic' ),
					selected( '_cart', self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'commerce' ][ 'method' ], false ),
					__( 'shopping cart', 'webcomic' )
				)
			);
		?>
		</label>
		<?php
	}
	
	/** Render the Commerce > Prices setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_commerce_prices() {
		?>
		<label>
			<input type="number" name="webcomic_commerce_prices_domestic" id="webcomic_commerce_prices_domestic" value="<?php echo self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'commerce' ][ 'price' ][ 'domestic' ]; ?>" min="0" class="small-text" style="text-align:center">
			<?php _e( 'Domestic', 'webcomic' ); ?>
		</label>
		&emsp;&emsp;
		<label>
			<input type="number" name="webcomic_commerce_prices_international" value="<?php echo self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'commerce' ][ 'price' ][ 'international' ]; ?>" min="0" class="small-text" style="text-align:center">
			<?php _e( 'International', 'webcomic' ); ?>
		</label>
		&emsp;&emsp;
		<label>
			<input type="number" name="webcomic_commerce_prices_original" value="<?php echo self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'commerce' ][ 'price' ][ 'original' ]; ?>" min="0" class="small-text" style="text-align:center">
			<?php _e( 'Original', 'webcomic' ); ?>
		</label>
		<?php
	}
	
	/** Render the Commerce > Shipping setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_commerce_shipping() {
		?>
		<label>
			<input type="number" name="webcomic_commerce_shipping_domestic" id="webcomic_commerce_shipping_domestic" value="<?php echo self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'commerce' ][ 'shipping' ][ 'domestic' ]; ?>" min="0" class="small-text" style="text-align:center">
			<?php _e( 'Domestic', 'webcomic' ); ?>
		</label>
		&emsp;&emsp;
		<label>
			<input type="number" name="webcomic_commerce_shipping_international" value="<?php echo self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'commerce' ][ 'shipping' ][ 'international' ]; ?>" min="0" class="small-text" style="text-align:center">
			<?php _e( 'International', 'webcomic' ); ?>
		</label>
		&emsp;&emsp;
		<label>
			<input type="number" name="webcomic_commerce_shipping_original" value="<?php echo self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'commerce' ][ 'shipping' ][ 'original' ]; ?>" min="0" class="small-text" style="text-align:center">
			<?php _e( 'Original', 'webcomic' ); ?>
		</label>
		<?php
	}
	
	/** Render the Commerce > Donation setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_commerce_donation() {
		?>
		<input type="number" name="webcomic_commerce_donation" id="webcomic_commerce_donation" value="<?php echo self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'commerce' ][ 'donation' ]; ?>" min="0" class="small-text" style="text-align:center">
		<span class="description"><?php _e( 'Use zero to allow donors to specify their own amount.', 'webcomic' ); ?></span>
		<?php
	}
	
	/** Render the Commerce > Currencey setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_commerce_currency() {
		$currencies = array(
			'AUD' => __( 'Australian Dollar', 'webcomic' ),
			'BRL' => __( 'Brazilian Real', 'webcomic' ),
			'CAD' => __( 'Canadian Dollar', 'webcomic' ),
			'CHF' => __( 'Swiss Franc', 'webcomic' ),
			'CZK' => __( 'Czech Koruna', 'webcomic' ),
			'DKK' => __( 'Danish Krone', 'webcomic' ),
			'EUR' => __( 'Euro', 'webcomic' ),
			'GBP' => __( 'Pound Sterling', 'webcomic' ),
			'HKD' => __( 'Hong Kong Dollar', 'webcomic' ),
			'HUF' => __( 'Hungarian Forint', 'webcomic' ),
			'ILS' => __( 'Israeli New Sheqel', 'webcomic' ),
			'JPY' => __( 'Japanese Yen', 'webcomic' ),
			'MXN' => __( 'Mexican Peso', 'webcomic' ),
			'MYR' => __( 'Malaysian Ringgit', 'webcomic' ),
			'NOK' => __( 'Norwegian Krone', 'webcomic' ),
			'NZD' => __( 'New Zealand Dollar', 'webcomic' ),
			'PHP' => __( 'Philippine Peso', 'webcomic' ),
			'PLN' => __( 'Polish Zloty', 'webcomic' ),
			'SEK' => __( 'Swedish Krona', 'webcomic' ),
			'SGD' => __( 'Singapore Dollar', 'webcomic' ),
			'THB' => __( 'Thai Baht', 'webcomic' ),
			'TRY' => __( 'Turkish Lira', 'webcomic' ),
			'TWD' => __( 'Taiwan New Dollar', 'webcomic' ),
			'USD' => __( 'U.S. Dollar', 'webcomic' )
		);
		
		asort( $currencies, SORT_STRING );
		?>
		<select name="webcomic_commerce_currency" id="webcomic_commerce_currency">
		<?php
		foreach( $currencies as $k => $v ) {
			printf(
				'<option value="%s"%s>%s</option>',
				$k,
				selected( $k, self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'commerce' ][ 'currency' ], false ),
				$v
			);
		}
		?>
		</select>
		<span class="description"><?php _e( 'All transactions will use this currency.', 'webcomic' ); ?></span>
		<?php
	}
	
	/** Render the Access > Age setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_access_age() {
		?>
		<label><input type="checkbox" name="webcomic_access_byage" id="webcomic_access_byage"<?php checked( self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'access' ][ 'byage' ] ); ?>></label>
		<label>
		<?php
			printf( __( 'People must be at least %s years or older to view webcomics in this collection', 'webcomic' ), sprintf(
				'<input type="number" name="webcomic_access_age" value="%s" min="0" class="small-text" style="text-align:center">',
				self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'access' ][ 'age' ]
			) );
		?>
		</label>
		<?php
	}
	
	/** Render the Access > Role setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_access_roles() {
		?>
		<label><input type="checkbox" name="webcomic_access_byrole" id="webcomic_access_byrole"<?php checked( self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'access' ][ 'byrole' ] ); ?>> <?php _e( 'People must be registered and logged in to view webcomics in this collection', 'webcomic' ); ?></label><br>
		<p>
			<select name="webcomic_access_roles[]" style="min-height:8em;vertical-align:top" multiple>
				<optgroup label="<?php esc_attr_e( 'Allowed Roles', 'webcomic' ); ?>">
					<option value="!"<?php selected( ( isset( self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'access' ][ 'roles' ][ 0 ]  ) and '!' === self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'access' ][ 'roles' ][ 0 ] ) ); ?>><?php _e( '(any)', 'webcomic' ); ?></option>
					<?php
						$roles = get_editable_roles();
						
						foreach ( $roles as $k => $v ) {
							printf( '<option value="%s"%s>%s</option>',
								$k,
								selected( in_array( $k, self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'access' ][ 'roles' ] ), true, false ),
								translate_user_role( $v[ 'name' ] )
							);
						}
					?>
				</optgroup>
			</select>
			<span class="description"><?php _e( 'Hold <code>CTRL</code>, <code>Command</code>, or <code>Shift</code> to select multiple roles.', 'webcomic' ); ?></span>
		</p>
		<?php
	}
	
	/** Render the Posts > Content settings.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_supports_content() {
		?>
		<label><input type="checkbox" name="webcomic_supports[]" value="title" id="webcomic_posts_title"<?php checked( in_array( 'title', self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'supports' ] ) ); ?>> <?php _e( 'Enable post titles', 'webcomic' ); ?></label><br>
		<label><input type="checkbox" name="webcomic_supports[]" value="excerpt"<?php checked( in_array( 'excerpt', self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'supports' ] ) ); ?>> <?php _e( 'Enable post excerpts', 'webcomic' ); ?></label><br>
		<label><input type="checkbox" name="webcomic_supports[]" value="editor"<?php checked( in_array( 'editor', self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'supports' ] ) ); ?>> <?php _e( 'Enable the post editor', 'webcomic' ); ?></label><br>
		<label><input type="checkbox" name="webcomic_supports[]" value="thumbnail"<?php checked( in_array( 'thumbnail', self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'supports' ] ) ); ?>> <?php _e( 'Enable featured images', 'webcomic' ); ?></label>
		<?php
	}
	
	/** Render the Posts > Discussion settings.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_supports_discussion() {
		?>
		<label><input type="checkbox" name="webcomic_supports[]" value="comments" id="webcomic_posts_comments"<?php checked( in_array( 'comments', self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'supports' ] ) ); ?>> <?php _e( 'Enable comments', 'webcomic' ); ?></label><br>
		<label><input type="checkbox" name="webcomic_supports[]" value="trackbacks"<?php checked( in_array( 'trackbacks', self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'supports' ] ) ); ?>> <?php _e( 'Enable trackbacks', 'webcomic' ); ?></label>
		<?php
	}
	
	/** Render the Posts > Miscellanea settings.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_supports_miscellanea() {
		?>
		<label><input type="checkbox" name="webcomic_supports[]" value="revisions" id="webcomic_posts_revisions"<?php checked( in_array( 'revisions', self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'supports' ] ) ); ?>> <?php _e( 'Enable revisions', 'webcomic' ); ?></label><br>
		<label><input type="checkbox" name="webcomic_supports[]" value="custom-fields"<?php checked( in_array( 'custom-fields', self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'supports' ] ) ); ?>> <?php _e( 'Enable custom fields', 'webcomic' ); ?></label><br>
		<label><input type="checkbox" name="webcomic_supports[]" value="post-formats"<?php checked( in_array( 'post-formats', self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'supports' ] ) ); ?>> <?php _e( 'Enable post formats', 'webcomic' ); ?></label>
		<?php
	}
	
	/** Render the Posts > Taxonomies settings.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_supports_taxonomies() {
		$first = true;
		
		foreach ( get_taxonomies( array( 'public' => true ), 'objects' ) as $k => $v ) {
			//if ( 'post_format' === $v->name or 'webcomic_language' === $v->name or preg_match( '/^webcomic\d+_(storyline|character)$/', $v->name ) ) {
			if ( 'post_format' === $v->name or 'webcomic_language' === $v->name or "{$_GET[ 'post_type' ]}_storyline" === $v->name or "{$_GET[ 'post_type' ]}_character" === $v->name ) {
				continue;
			}
			
			printf( '<label><input type="checkbox" name="webcomic_taxonomies[]" value="%s"%s%s> %s</label><br>',
				$v->name,
				$first ? ' id="webcomic_posts_taxonomy"' : '',
				checked( in_array( $v->name, self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'taxonomies' ] ), true, false ),
				$v->labels->name
			);
			
			$first = false;
		}
	}
	
	/** Render the Slug > Archive setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_slugs_archive() {
		?>
		<label>
			<input type="text" name="webcomic_slugs[archive]" id="webcomic_slugs_archive" value="<?php echo self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'slugs' ][ 'archive' ]; ?>" class="regular-text">
			<?php printf( '<span class="description">%s/<b>%s</b>/</span>', home_url(), self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'slugs' ][ 'archive' ] ); ?>
		</label>
		<?php
	}
	
	/** Render the Slug > Webcomics setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_slugs_webcomic() {
		?>
		<label>
			<input type="text" name="webcomic_slugs[webcomic]" id="webcomic_slugs_webcomic" value="<?php echo self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'slugs' ][ 'webcomic' ]; ?>" class="regular-text">
			<?php printf( '<span class="description">%s/<b>%s</b>/single-webcomic-slug</span>', home_url(), self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'slugs' ][ 'webcomic' ] ); ?>
		</label>
		<?php
	}
	
	/** Render the Slugs > Storylines setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_slugs_storyline() {
		?>
		<label>
			<input type="text" name="webcomic_slugs[storyline]" id="webcomic_slugs_storyline" value="<?php echo self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'slugs' ][ 'storyline' ]; ?>" class="regular-text">
			<?php printf( '<span class="description">%s/<b>%s</b>/single-storyline-slug</span>', home_url(), self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'slugs' ][ 'storyline' ] ); ?>
		</label>
		<?php
	}
	
	/** Render the Slugs > Characters setting.
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_slugs_character() {
		?>
		<label>
			<input type="text" name="webcomic_slugs[character]" id="webcomic_slugs_character" value="<?php echo self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'slugs' ][ 'character' ]; ?>" class="regular-text">
			<?php printf( '<span class="description">%s/<b>%s</b>/single-character-slug</span>', home_url(), self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'slugs' ][ 'character' ] ); ?>
		</label>
		<?php
	}
	
	/** Render Twitter > Authorized Account
	 * 
	 * @uses Webcomic::$config
	 * @uses WebcomicConfig::ajax_twitter_account()
	 */
	public function collection_twitter_account() {
		echo '<div id="webcomic_twitter_account">', self::ajax_twitter_account( self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'twitter' ][ 'consumer_key' ], self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'twitter' ][ 'consumer_secret' ], $_GET[ 'post_type' ] ), '</div>';
	}
	
	/** Render Twitter > Consumer Key
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_twitter_consumer_key() {
		?>
		<input type="text" name="webcomic_twitter_consumer_key" id="webcomic_twitter_consumer_key" value="<?php echo self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'twitter' ][ 'consumer_key' ]; ?>" class="regular-text">
		<?php
	}
	
	/** Render Twitter > Consumer Secret
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_twitter_consumer_secret() {
		?>
		<input type="text" name="webcomic_twitter_consumer_secret" id="webcomic_twitter_consumer_secret" value="<?php echo self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'twitter' ][ 'consumer_secret' ]; ?>" class="regular-text">
		<?php
	}
	
	/** Render Twitter > Format
	 * 
	 * @uses Webcomic::$config
	 */
	public function collection_twitter_format() {
		?>
		<input type="text" name="webcomic_twitter_format" id="webcomic_twitter_format" value="<?php echo self::$config[ 'collections' ][ $_GET[ 'post_type' ] ][ 'twitter' ][ 'format' ]; ?>" maxlength="140" class="regular-text">
		<?php
	}
	
	/** Save callback for the webcomic option.
	 * 
	 * If 'webcomic_general' is set we're working on the general
	 * settings page.
	 * 
	 * If 'webcomic_add_collection' is set, create a new collection.
	 * 
	 * If 'webcomic_bulk_collection' is set and one or more collections
	 * is selected, perform bulk collection actions (thanks WPCat2Tag).
	 * 
	 * Otherwise, save general settings.
	 * 
	 * If 'webcomic_collection is set we're working on a collection
	 * settings page.
	 * 
	 * @param array $options Configuration array.
	 * @return array
	 * @uses Webcomic::$config
	 * @uses WebcomicAdmin::save_collection()
	 */
	public function save( $options ) {
		$new = $bulk = false;
		
		if ( isset( $_POST[ 'webcomic_general' ] ) ) {
			if ( isset( $_POST[ 'webcomic_add_collection' ] ) ) {
				$new  = self::$config[ 'collections' ][ 'webcomic1' ];
				$name = $_POST[ 'webcomic_new_collection' ] ? $_POST[ 'webcomic_new_collection' ] : sprintf( __( 'Untitled Webcomic %d', 'webcomic' ), self::$config[ 'increment' ] );
				$slug = sanitize_title( $name );
				
				$new[ 'id' ]          = 'webcomic' . self::$config[ 'increment' ];
				$new[ 'name' ]        = $name;
				$new[ 'image' ]       = 0;
				$new[ 'updated' ]     = 0;
				$new[ 'description' ] = '';
				$new[ 'slugs' ] = array(
					'name'       => $slug,
					'archive'    => $slug,
					'webcomic'   => $slug,
					'storyline'  => "{$slug}-storyline",
					'character'  => "{$slug}-character"
				);
				
				self::$config[ 'collections' ][ 'webcomic' . self::$config[ 'increment' ] ] = $new;
				self::$config[ 'increment' ]++;
				
				add_settings_error( 'webcomic_otions', 'new-collection', sprintf( __( 'Added <q>%s</q>', 'webcomic' ), esc_html( $name ) ), 'updated' );
			} elseif ( $_POST[ 'webcomic_bulk_collection' ] and isset( $_POST[ 'webcomic_collection' ] ) ) {
				$bulk  = true;
				$count = 0;
				
				if ( 'delete' === $_POST[ 'webcomic_bulk_collection' ] ) {
					foreach ( $_POST[ 'webcomic_collection' ] as $id ) {
						foreach ( get_posts( array( 'numberposts' => -1, 'post_type' => $id, 'post_status' => 'any' ) ) as $webcomic ) {
							wp_delete_post( $webcomic->ID, true );
						}
						
						foreach ( get_terms( "{$id}_storyline", array( 'get' => 'all', 'fields' => 'ids' ) ) as $storyline ) {
							wp_delete_term( $storyline, "{$id}_storyline" );
						}
						
						foreach ( get_terms( "{$id}_character", array( 'get' => 'all', 'fields' => 'ids' ) ) as $character ) {
							wp_delete_term( $character, "{$id}_character" );
						}
						
						unset( self::$config[ 'collections' ][ $id ] );
						
						$count++;
					}
				} elseif ( 'delete_save' === $_POST[ 'webcomic_bulk_collection' ] ) {
					foreach ( $_POST[ 'webcomic_collection' ] as $id ) {
						WebcomicAdmin::save_collection( $id );
						
						unset( self::$config[ 'collections' ][ $id ] );
						
						$count++;
					}
				}
				
				if ( 'delete' === $_POST[ 'webcomic_bulk_collection' ] or 'delete_save' === $_POST[ 'webcomic_bulk_collection' ] ) {
					add_settings_error( 'webcomic-options', 'delete-convert-collections', sprintf( _n( 'Deleted %s collection', 'Deleted %s collections', $count, 'webcomic' ), $count ), 'updated' );
				}
			} else {
				self::$config[ 'dynamic' ]   = isset( $_POST[ 'webcomic_dynamic' ] );
				self::$config[ 'gestures' ]  = isset( $_POST[ 'webcomic_gestures' ] );
				self::$config[ 'integrate' ] = isset( $_POST[ 'webcomic_integrate' ] );
				self::$config[ 'shortcuts' ] = isset( $_POST[ 'webcomic_shortcuts' ] );
				self::$config[ 'uninstall' ] = isset( $_POST[ 'webcomic_uninstall' ] );
				self::$config[ 'convert' ]   = isset( $_POST[ 'webcomic_uninstall' ], $_POST[ 'webcomic_convert' ] );
			}
		} elseif ( isset( $_POST[ 'webcomic_collection' ] ) ) {
			$id         = $_POST[ 'webcomic_collection' ];
			$tokens     = array( '%year%', '%monthnum%', '%day%', '%hour%', '%minute%', '%second%', '%post_id%', '%author%', "%{$id}_storyline%" );
			$collection = array(
				'id'          => $id,
				'name'        => $_POST[ 'webcomic_name' ] ? $_POST[ 'webcomic_name' ] : self::$config[ 'collections' ][ $id ][ 'name' ],
				'image'       => $_POST[ 'webcomic_image' ],
				'theme'       => $_POST[ 'webcomic_theme' ] ? $_POST[ 'webcomic_theme' ] : '',
				'updated'     => self::$config[ 'collections' ][ $id ][ 'updated' ],
				'supports'    => isset( $_POST[ 'webcomic_supports' ] ) ? array_merge( $_POST[ 'webcomic_supports' ], array( 'author' ) ) : array( 'author' ),
				'taxonomies'  => isset( $_POST[ 'webcomic_taxonomies' ] ) ? $_POST[ 'webcomic_taxonomies' ] : array(),
				'description' => $_POST[ 'webcomic_description' ],
				'feeds' => array(
					'hook' => isset( $_POST[ 'webcomic_feeds_hook' ] ),
					'size' => $_POST[ 'webcomic_feeds_size' ],
					'main' => isset( $_POST[ 'webcomic_feeds_main' ] )
				),
				'slugs' => array(
					'name'      => sanitize_title( $_POST[ 'webcomic_slug' ], self::$config[ 'collections' ][ $id ][ 'slugs' ][ 'name' ] ),
					'archive'   => self::$config[ 'collections' ][ $id ][ 'slugs' ][ 'archive' ],
					'webcomic'  => self::$config[ 'collections' ][ $id ][ 'slugs' ][ 'webcomic' ],
					'storyline' => self::$config[ 'collections' ][ $id ][ 'slugs' ][ 'storyline' ],
					'character' => self::$config[ 'collections' ][ $id ][ 'slugs' ][ 'character' ]
				),
				'buffer' => array(
					'hook'  => isset( $_POST[ 'webcomic_buffer_hook' ] ),
					'days'  => intval( $_POST[ 'webcomic_buffer_days' ] ),
					'email' => filter_var( $_POST[ 'webcomic_buffer_email' ], FILTER_VALIDATE_EMAIL ) ? $_POST[ 'webcomic_buffer_email' ] : self::$config[ 'collections' ][ $id ][ 'buffer' ][ 'email' ]
				),
				'access' => array(
					'byage'  => isset( $_POST[ 'webcomic_access_byage' ] ),
					'byrole' => isset( $_POST[ 'webcomic_access_byrole' ] ),
					'age'    => intval( $_POST[ 'webcomic_access_age' ] ),
					'roles'  => array( '!' )
				),
				'twitter' => array(
					'format'          => $_POST[ 'webcomic_twitter_format' ],
					'oauth_token'     => self::$config[ 'collections' ][ $id ][ 'twitter' ][ 'oauth_token' ],
					'oauth_secret'    => self::$config[ 'collections' ][ $id ][ 'twitter' ][ 'oauth_secret' ],
					'consumer_key'    => $_POST[ 'webcomic_twitter_consumer_key' ],
					'consumer_secret' => $_POST[ 'webcomic_twitter_consumer_secret' ],
					'request_token'   => self::$config[ 'collections' ][ $id ][ 'twitter' ][ 'request_token' ],
					'request_secret'  => self::$config[ 'collections' ][ $id ][ 'twitter' ][ 'request_secret' ]
				),
				'commerce' => array(
					'business'  => filter_var( $_POST[ 'webcomic_commerce_business' ], FILTER_VALIDATE_EMAIL ) ? $_POST[ 'webcomic_commerce_business' ] : '',
					'currency'  => $_POST[ 'webcomic_commerce_currency' ],
					'method'    => $_POST[ 'webcomic_commerce_method' ],
					'donation'  => is_float( 0 + $_POST[ 'webcomic_commerce_donation' ] ) ? round( $_POST[ 'webcomic_commerce_donation' ], 2 ) : intval( $_POST[ 'webcomic_commerce_donation' ] ),
					'price'     => array(
						'domestic'      => is_float( 0 + $_POST[ 'webcomic_commerce_prices_domestic' ] ) ? round( $_POST[ 'webcomic_commerce_prices_domestic' ], 2 ) : intval( $_POST[ 'webcomic_commerce_prices_domestic' ] ),
						'international' => is_float( 0 + $_POST[ 'webcomic_commerce_prices_international' ] ) ? round( $_POST[ 'webcomic_commerce_prices_international' ], 2 ) : intval( $_POST[ 'webcomic_commerce_prices_international' ] ),
						'original'      => is_float( 0 + $_POST[ 'webcomic_commerce_prices_original' ] ) ? round( $_POST[ 'webcomic_commerce_prices_original' ], 2 ) : intval( $_POST[ 'webcomic_commerce_prices_original' ] )
					),
					'shipping' => array(
						'domestic'      => is_float( 0 + $_POST[ 'webcomic_commerce_shipping_domestic' ] ) ? round( $_POST[ 'webcomic_commerce_shipping_domestic' ], 2 ) : intval( $_POST[ 'webcomic_commerce_shipping_domestic' ] ),
						'international' => is_float( 0 + $_POST[ 'webcomic_commerce_shipping_international' ] ) ? round( $_POST[ 'webcomic_commerce_shipping_international' ], 2 ) : intval( $_POST[ 'webcomic_commerce_shipping_international' ] ),
						'original'      => is_float( 0 + $_POST[ 'webcomic_commerce_shipping_original' ] ) ? round( $_POST[ 'webcomic_commerce_shipping_original' ], 2 ) : intval( $_POST[ 'webcomic_commerce_shipping_original' ] )
					)
				),
				'transcripts' => array(
					'open'       => isset( $_POST[ 'webcomic_transcripts_open' ] ),
					'languages'  => array( '!' ),
					'permission' => $_POST[ 'webcomic_transcripts_permission' ],
					'notify' => array(
						'hook'  => isset( $_POST[ 'webcomic_transcripts_hook' ] ),
						'email' => filter_var( $_POST[ 'webcomic_transcripts_email' ], FILTER_VALIDATE_EMAIL ) ? $_POST[ 'webcomic_transcripts_email' ] : self::$config[ 'collections' ][ $id ][ 'transcripts' ][ 'notify' ][ 'email' ]
					)
				)
			);
			
			foreach ( $_POST[ 'webcomic_slugs' ] as $k  => $v ) {
				$slug = array();
				
				foreach ( explode( '/', $v ) as $b ) {
					if ( ( 'webcomic' === $k and in_array( $b, $tokens ) ) or $b = sanitize_title( $b ) ) {
						$slug[] = $b;
					}
				}
				
				if ( $slug = join( '/', $slug ) ) {
					$collection[ 'slugs' ][ $k ] = $slug;
				}
			}
			
			if ( $_POST[ 'webcomic_access_roles' ] and !in_array( '!', $_POST[ 'webcomic_access_roles' ] ) ) {
				$collection[ 'access' ][ 'roles' ] = array();
				
				foreach ( $_POST[ 'webcomic_access_roles' ] as $role ) {
					$collection[ 'access' ][ 'roles' ][] = $role;
				}
			}
			
			if ( $collection[ 'twitter' ][ 'consumer_key' ] !== self::$config[ 'collections' ][ $id ][ 'twitter' ][ 'consumer_key' ] or $collection[ 'twitter' ][ 'consumer_secret' ] !== self::$config[ 'collections' ][ $id ][ 'twitter' ][ 'consumer_secret' ] ) {
				$collection[ 'twitter' ][ 'oauth_token' ] = $collection[ 'twitter' ][ 'oauth_secret' ] = '';
			} elseif ( $collection[ 'twitter' ][ 'oauth_token' ] and $collection[ 'twitter' ][ 'oauth_secret' ] ) {
				$collection[ 'twitter' ][ 'request_token' ] = $collection[ 'twitter' ][ 'request_secret' ] = '';
			}
			
			$collection[ 'commerce' ][ 'prints' ]                   = ( isset( $_POST[ 'webcomic_commerce_prints' ] ) and $collection[ 'commerce' ][ 'business' ] );
			$collection[ 'commerce' ][ 'originals' ]                = ( isset( $_POST[ 'webcomic_commerce_originals' ] ) and $collection[ 'commerce' ][ 'business' ] );
			$collection[ 'commerce' ][ 'total' ][ 'domestic' ]      = round( $collection[ 'commerce' ][ 'price' ][ 'domestic' ] + $collection[ 'commerce' ][ 'shipping' ][ 'domestic' ], 2 );
			$collection[ 'commerce' ][ 'total' ][ 'international' ] = round( $collection[ 'commerce' ][ 'price' ][ 'international' ] + $collection[ 'commerce' ][ 'shipping' ][ 'international' ], 2 );
			$collection[ 'commerce' ][ 'total' ][ 'original' ]      = round( $collection[ 'commerce' ][ 'price' ][ 'original' ] + $collection[ 'commerce' ][ 'shipping' ][ 'original' ], 2 );
			
			if ( !empty( $_POST[ 'webcomic_transcripts_languages' ] ) and !in_array( '!', $_POST[ 'webcomic_transcripts_languages' ] ) ) {
				$collection[ 'transcripts' ][ 'languages' ] = array();
				
				foreach ( $_POST[ 'webcomic_transcripts_languages' ] as $language ) {
					$collection[ 'transcripts' ][ 'languages' ][] = $language;
				}
			}
			
			self::$config[ 'collections' ][ $id ] = $collection;
		}
		
		if ( !$new and !$bulk and ( isset( $_POST[ 'webcomic_general' ] ) or isset( $_POST[ 'webcomic_collection' ] ) ) ) {
			add_settings_error( 'webcomic-options', 'settings-saved', __( 'Settings saved.', 'webcomic' ), 'updated' );
		}
		
		return ( isset( $_POST[ 'webcomic_general' ] ) or isset( $_POST[ 'webcomic_collection' ] ) ) ? self::$config : $options;
	}
	
	/** Update size information when media options are saved.
	 * 
	 * @uses Webcomic::$config
	 */
	private function save_sizes() {
		if ( isset( $_POST[ 'webcomic_media_sizes' ], $_POST[ 'option_page' ], $_POST[ 'action' ] ) and 'media' === $_POST[ 'option_page' ] and 'update' === $_POST[ 'action' ] and wp_verify_nonce( $_POST[ 'webcomic_media_sizes' ], 'webcomic_media_sizes' ) ) {
			if ( $size = sanitize_title( $_POST[ 'webcomic_new_size' ] ) ) {
				if ( 'thumb' === $size or 'thumbnail' === $size or 'medium' === $size or 'large' === $size or 'post-thumbnail' === $size ) {
					wp_die( sprintf( __( 'The name <q>%s</q> is reserved by WordPress.', 'webcomic' ), $size ), __( 'Error | Webcomic', 'webcomic' ) );
				} elseif ( in_array( $size, get_intermediate_image_sizes() ) ) {
					wp_die( sprintf( __( 'A size with the name <q>%s</q> already exists.', 'webcomic' ), $size ), __( 'Error | Webcomic', 'webcomic' ) );
				} else {
					self::$config[ 'sizes' ][ $size ] = array(
						'width'  => intval( $_POST[ 'webcomic_new_size_width' ] ),
						'height' => intval( $_POST[ 'webcomic_new_size_height' ] ),
						'crop'   => isset( $_POST[ 'webcomic_new_size_crop' ] )
					);
				}
			}
			
			if ( !empty( $_POST[ 'webcomic_size' ] ) ) {
				foreach ( $_POST[ 'webcomic_size' ] as $k => $v ) {
					self::$config[ 'sizes' ][ $k ] = array(
						'width'  => intval( $v[ 'width' ] ),
						'height' => intval( $v[ 'height' ] ),
						'crop'   => isset( $v[ 'crop' ] )
					);
				}
			}
			
			if ( $_POST[ 'webcomic_bulk_size' ] and isset( $_POST[ 'webcomic_sizes' ] ) ) {
				foreach ( $_POST[ 'webcomic_sizes' ] as $size ) {
					unset( self::$config[ 'sizes' ][ $size ] );
				}
			}
			
			update_option( 'webcomic_options', self::$config );
		}
	}
	
	/** Render a settings page.
	 * 
	 * @uses Webcomic::$config
	 * @uses Webcomic::$version
	 */
	public function page() {
		$page = empty( $_GET[ 'post_type' ] ) ? 'webcomic-options' : "{$_GET[ 'post_type' ]}-options";
		?>
		<div class="wrap" >
			<div id="icon-options-general" class="icon32" data-webcomic-admin-url="<?php echo admin_url(); ?>"></div>
			<h2><?php echo get_admin_page_title(); if ( 'webcomic-options' !== $page ) { ?><a href="#" class="add-new-h2" title="<?php esc_attr_e( 'Collection ID', 'webcomic' ); ?>"><?php echo $_GET[ 'post_type' ]; ?></a><?php } ?></h2>
			<form action="options.php" method="post"<?php echo 'webcomic' !== $page ? ' enctype="multipart/form-data"' : ''; ?>>
				<?php
					settings_fields( 'webcomic-options' );
					do_settings_sections( $page );
					
					echo 'webcomic-options' === $page ? '<input type="hidden" name="webcomic_general" value="1">' : sprintf( '<input type="hidden" name="webcomic_collection" value="%s">', $_GET[ 'post_type' ] );
				?>
				<p class="submit">
					<?php
						submit_button( '', 'primary', '', false );
						
						if ( 'webcomic-options' === $page ) {
							printf( '<span class="alignright">%s</span>', sprintf( __( 'Thank you for using <a href="%1$s" target="_blank">Webcomic %2$s</a>', 'webcomic' ), 'http://webcomic.nu', self::$version ) );
						}
					?>
				</p>
			</form>
		</div>
		<?php
	}
	
	/** Generic settings section callback.
	 * 
	 * Most sections don't include a description, but if permalinks are
	 * set to Default we need to warn users that the permalink URL's
	 * won't actually work.
	 */
	public function section( $args ) {
		if ( preg_match( '/^webcomic\d+-permalinks$/', $args[ 'id' ] ) and !get_option( 'permalink_structure' ) ) {
			echo '<p>', sprintf( __( "These URL's won't work unless you <a href='%s'>change the permalink setting</a> to something other than <em>Default</em>.", 'webcomic' ), admin_url( 'options-permalink.php' ) ), '</p>';
		}
	}
	
	/** Handle collection poster image updating.
	 * 
	 * @param integer $id ID of the selected image.
	 * @param string $collection Collection the poster is for.
	 */
	public static function ajax_collection_image( $id, $collection ) {
		if ( $id ) {
			printf( '<a href="%s">%s</a><br>',
				esc_url( add_query_arg( array( 'post' => $id, 'action' => 'edit' ), admin_url( 'post.php' ) ) ),
				wp_get_attachment_image( $id )
			);
		}
		
		printf( '<input type="hidden" name="webcomic_image" value="%s"><a class="button webcomic-collection-image" data-title="%s" data-update="%s">%s</a>',
			$id,
			sprintf( __( 'Choose a Poster for %s', 'webcomic' ), esc_attr( self::$config[ 'collections' ][ $collection ][ 'name' ] ) ),
			__( 'Update', 'webcomic' ),
			$id ? __( 'Change', 'webcomic' ) : __( 'Select', 'webcomic' )
		);
		
		if ( $id ) {
			printf( ' <a class="button webcomic-collection-image-x">%s</a>', __( 'Remove', 'webcomic' ) );
		}
	}
	
	/** Handle dynamic slug previews.
	 * 
	 * @param string $slug New slug.
	 * @param string $preview ID of the element theupdated slug will be loaded into for preview.
	 * @param string $collection Collection the slug belongs to.
	 */
	public static function ajax_slug_preview( $slug, $preview, $collection ) {
		$slug     = explode( '/', $slug );
		$tokens   = array( '%year%', '%monthnum%', '%day%', '%hour%', '%minute%', '%second%', '%post_id%', '%author%', "%{$collection}_storyline%" );
		$fallback = substr( $preview, 16 );
		
		foreach ( $slug as $k => $v ) {
			if ( ( 'webcomic' === $fallback and in_array( $v, $tokens ) ) or $v = sanitize_title( $v ) ) {
				$slug[ $k ] = $v;
			} else {
				unset( $slug[ $k ] );
			}
		}
		
		$slug = join( '/', $slug );
		$slug = $slug ? $slug : self::$config[ 'collections' ][ $collection ][ 'slugs' ][ $fallback ];
		
		echo json_encode( array(
			'slug'      => $slug,
			'container' => $preview
		) );
	}
	
	/** Handle dynamic commerce defaults.
	 * 
	 * @param string $email Email to validate.
	 */
	public static function ajax_commerce_defaults( $email ) {
		echo json_encode( array( 'clear' => filter_var( $email, FILTER_VALIDATE_EMAIL ) ) );
	}
	
	/** Handle dynamic Twitter authorization updates.
	 * 
	 * @param string $consumer_key Twitter application consumer key.
	 * @param string $consumer_secret Twitter application consumer secret.
	 * @param string $collection Collection the Twitter credentials belong to.
	 */
	public static function ajax_twitter_account( $consumer_key, $consumer_secret, $collection ) {
		if ( !class_exists( 'TwitterOAuth' ) ) {
			require_once self::$dir . '-/library/twitter.php';
		}
		
		if ( $consumer_key and $consumer_secret ) {
			$oauth       = new TwitterOAuth( $consumer_key, $consumer_secret, self::$config[ 'collections' ][ $collection ][ 'twitter' ][ 'oauth_token' ], self::$config[ 'collections' ][ $collection ][ 'twitter' ][ 'oauth_secret' ] );
			$credentials = $oauth->get( 'account/verify_credentials' );
			
			self::$config[ 'collections' ][ $collection ][ 'twitter' ][ 'consumer_key' ]    = $consumer_key;
			self::$config[ 'collections' ][ $collection ][ 'twitter' ][ 'consumer_secret' ] = $consumer_secret;
			
			update_option( 'webcomic_options', self::$config );
			
			if ( isset( $credentials->screen_name ) ) {
				printf( '<a href="http://twitter.com/%s" target="_blank"><b>@%s</b></a> <a href="https://twitter.com/settings/applications" target="_blank" class="button">%s</a>',
					$credentials->screen_name,
					$credentials->screen_name,
					__( 'Revoke Access', 'webcomic' )
				);
			} else {
				$oauth   = new TwitterOAuth( $consumer_key, $consumer_secret );
				$request = $oauth->getRequestToken( add_query_arg( array( 'webcomic_twitter_oauth' => true, 'webcomic_collection' => $collection ), get_site_url() ) );
				
				if ( isset( $request[ 'Failed to validate oauth signature and token' ] ) ) {
					_e( 'Validation error. Please ensure your <a href="https://dev.twitter.com/apps/new" target="_blank">Twitter Application</a> <b>consumer key</b> and <b>consumer secret</b> are entered correctly.', 'webcomic' );
				} else {
					self::$config[ 'collections' ][ $collection ][ 'twitter' ][ 'request_token' ]   = isset( $request[ 'oauth_token' ] ) ? $request[ 'oauth_token' ] : '';
					self::$config[ 'collections' ][ $collection ][ 'twitter' ][ 'request_secret' ]  = isset( $request[ 'oauth_token_secret' ] ) ? $request[ 'oauth_token_secret' ] : '';
					
					update_option( 'webcomic_options', self::$config );
					
					printf( '%s<a href="%s"><img src="%s-/img/twitter.png" alt="%s"></a>',
						( self::$config[ 'collections' ][ $collection ][ 'twitter' ][ 'oauth_token' ] and self::$config[ 'collections' ][ $collection ][ 'twitter' ][ 'oauth_secret' ]  ) ? __( '<p class="description">Your credentials could not be verified.</p>', 'webcomic' ) : '',
						$oauth->getAuthorizeURL( $request ),
						self::$url,
						__( 'Sign in with Twitter', 'webcomic' )
					);
				}
			}
		} else {
			echo '<span class="description">', __( 'Please enter your <a href="https://dev.twitter.com/apps/new" target="_blank">Twitter Application</a> <b>consumer key</b> and <b>consumer secret</b> below.', 'webcomic' ), '</span>';
		}
	}
}
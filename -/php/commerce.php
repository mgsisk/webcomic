<?php
/** Contains the WebcomicCommerce class.
 * 
 * @package Webcomic
 */

/** Handle the IPN log tool.
 * 
 * @package Webcomic
 */
class WebcomicCommerce extends Webcomic {
	/** Register hooks.
	 * 
	 * @uses WebcomicCommerce::admin_init()
	 * @uses WebcomicCommerce::admin_menu()
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}
	
	/** Empty the current ipn log file.
	 * 
	 * @uses Webcomic::$dir
	 * @hook admin_init
	 */
	public function admin_init() {
		global $wpdb;
		
		if ( isset( $_POST[ 'webcomic_commerce' ], $_POST[ 'empty_log' ] ) and wp_verify_nonce( $_POST[ 'webcomic_commerce' ], 'webcomic_commerce' ) ) {
			$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'webcomic_commerce'" );
		}
	}
	
	/** Register submenu page for ipn log viewer.
	 * 
	 * @uses WebcomicCommerce::page()
	 * @hook admin_menu
	 */
	public function admin_menu() {
		add_submenu_page( 'tools.php', __( 'Webcomic Commerce', 'webcomic' ), __( 'Webcomic Commerce', 'webcomic' ), 'manage_options', 'webcomic-commerce', array( $this, 'page' ) );
	}
	
	/** Render the commerce tool page. */
	public function page() { 
		$transactions = get_posts( array( 'post_type' => 'webcomic_commerce', 'numberposts' => -1, 'post_status' => 'any' ) );
		?>
		<div class="wrap">
			<div id="icon-tools" class="icon32"></div>
			<h2><?php echo get_admin_page_title(); ?></h2>
			<br>
			<table class="wp-list-table widefat fixed">
				<thead>
					<tr>
						<th><?php _e( 'Transaction', 'webcomic' ); ?></th>
						<th><?php _e( 'Item', 'webcomic' ); ?></th>
						<th><?php _e( 'Message', 'webcomic' ); ?></th>
						<th class="column-date"><?php _e( 'Date', 'webcomic' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
						if ( $transactions ) {
							global $post;
							
							$i = 0;
							
							foreach ( $transactions as $post ) {
								setup_postdata( $post );
								
								$error = get_post_meta( get_the_ID(), 'webcomic_commerce_error', true ) ? ' style="color:#bc0b0b;font-weight:bold"' : '';
					?>
					<tr<?php echo $i % 2 ? '' : ' class="alternate"'; ?>>
						<td<?php echo $error; ?>><?php the_title(); ?></td>
						<td<?php echo $error; ?>><?php the_content(); ?></td>
						<td<?php echo $error; ?>><?php the_excerpt(); ?></td>
						<td<?php echo $error; ?>><?php echo '<abbr title="', get_the_time( __( 'Y/m/d g:i:s A', 'webcomic' ) ), '">', get_the_time( __( 'Y/m/d', 'webcomic' ) ), '</abbr>'; ?></td>
					</tr>
					<?php $i++; } } else { ?>
					<tr>
						<td colspan="4" class="alternate"><p><?php _e( "No commerce activity has been logged.", 'webcomic' ); ?></p></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<?php if ( $transactions ) { ?>
			<form method="post" style="float:right">
				<?php
					wp_nonce_field( 'webcomic_commerce', 'webcomic_commerce' );
					submit_button( __( 'Empty Log', 'webcomic' ), 'primary', 'empty_log' );
				?>
			</form>
			<?php } ?>
		</div>
		<?php
	}
}
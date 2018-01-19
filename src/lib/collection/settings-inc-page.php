<?php
/**
 * Collection settings page
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

if ( ! isset( $args ) ) {
	return;
}

?>

<div class="wrap" >
	<h1 class="wp-heading-inline"><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<a href="<?php echo esc_attr( $args['new_link'] ); ?>" class="page-title-action"><?php esc_html_e( 'New Collection', 'webcomic' ); ?></a>
	<hr class="wp-header-end">
	<?php settings_errors(); ?>
	<p><?php esc_html_e( 'Refer to the Help tab at the top of the screen for information about the settings on this page.', 'webcomic' ); ?></p>
	<form action="options.php" method="post">
		<?php
		settings_fields( $args['collection'] );

		do_settings_sections( "{$args['collection']}_settings" );

		submit_button();
		?>
	</form>
</div>

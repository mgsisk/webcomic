<?php
/**
 * Settings page
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Plugin;

?>

<div class="wrap" >
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form action="options.php" method="post">
		<?php
		settings_fields( 'webcomic' );

		do_settings_sections( 'webcomic_options' );

		submit_button();
		?>
	</form>
</div>

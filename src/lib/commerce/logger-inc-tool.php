<?php
/**
 * Comic IPN logger tool box
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Commerce;

?>

<div class="card">
	<h2><?php esc_html_e( 'Webcomic IPN Log', 'webcomic' ); ?></h2>
	<p>
		<?php
		printf(
			// Translators: Hyperlink to the Webcomic IPN Log tool.
			esc_html__( "Use the %s tool to view Instant Payment Notification's logged by Webcomic.", 'webcomic' ),
			sprintf(
				'<a href="%s">%s</a>',
				esc_url(
					add_query_arg(
						[
							'page' => 'webcomic_commerce_logger',
						], admin_url( 'tools.php' )
					)
				),
				esc_html__( 'Webcomic IPN Log', 'webcomic' )
			)
		);
		?>
	</p>
</div>

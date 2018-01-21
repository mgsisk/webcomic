<?php
/**
 * Comic generator tool box
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

?>

<div class="card">
	<h2><?php esc_html_e( 'Webcomic Generator', 'webcomic' ); ?></h2>
	<p>
		<?php
		printf(
			// Translators: Hyperlink to the Webcomic Generator tool.
			esc_html__( "If you have a lot of uploaded media that you'd like to automatically generate comics for, use the %s tool.", 'webcomic' ),
			sprintf(
				'<a href="%s">%s</a>',
				esc_url(
					add_query_arg(
						[
							'page' => 'webcomic_generator',
						], admin_url( 'tools.php' )
					)
				),
				esc_html__( 'Webcomic Generator', 'webcomic' )
			)
		);
		?>
	</p>
</div>

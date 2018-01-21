<?php
/**
 * Media matcher tool box
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

?>

<div class="card">
	<h2><?php esc_html_e( 'Webcomic Matcher', 'webcomic' ); ?></h2>
	<p>
		<?php
		printf(
			// Translators: Hyperlink to the Webcomic Matcher tool.
			esc_html__( "If you have a lot of uploaded media that you'd like to automatically assign to existing comics, use the %s tool.", 'webcomic' ),
			sprintf(
				'<a href="%s">%s</a>',
				esc_url(
					add_query_arg(
						[
							'page' => 'webcomic_matcher',
						], admin_url( 'tools.php' )
					)
				),
				esc_html__( 'Webcomic Matcher', 'webcomic' )
			)
		);
		?>
	</p>
</div>

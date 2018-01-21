<?php
/**
 * Details settings help
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

if ( ! isset( $args ) ) {
	return;
}

?>

<p></p>
<table class="widefat fixed striped">
	<tbody>
		<?php foreach ( $args['option'] as $key => $value ) : ?>
			<tr>
				<td><?php echo esc_html( $key ); ?></td>
				<td>
					<?php
					// @codingStandardsIgnoreLine WordPress.PHP.DevelopmentFunctions - We're purposely using var_export().
					echo esc_html( var_export( $value, true ) );
					?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

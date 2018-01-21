<?php
/**
 * Buffer field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Alert;

if ( ! isset( $args ) ) {
	return;
}

?>

<input type="hidden" name="<?php echo esc_attr( $args['label_for'] ); ?>[comics][]">
<input type="hidden" name="<?php echo esc_attr( $args['label_for'] ); ?>[emails][]">
<table class="widefat striped webcomic-records">
	<thead>
		<tr>
			<td class="manage-column" scope="col"><?php esc_html_e( 'Comics Left', 'webcomic' ); ?></td>
			<td class="manage-column" scope="col"><?php esc_html_e( 'Send Alert To', 'webcomic' ); ?></td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><input type="number" name="<?php echo esc_attr( $args['label_for'] ); ?>[comics][]" min="1" class="small-text"></td>
			<td><input type="text" name="<?php echo esc_attr( $args['label_for'] ); ?>[emails][]" class="large-text"></td>
		</tr>
		<?php foreach ( $args['option'] as $comics => $emails ) : ?>
			<tr>
				<td><input type="number" name="<?php echo esc_attr( $args['label_for'] ); ?>[comics][]" value="<?php echo esc_attr( $comics ); ?>" min="1" class="small-text"></td>
				<td><input type="text" name="<?php echo esc_attr( $args['label_for'] ); ?>[emails][]" value="<?php echo esc_attr( $emails ); ?>" class="large-text"></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

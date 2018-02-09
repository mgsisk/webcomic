<?php
/**
 * Referrers field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Restrict;

if ( ! isset( $args ) ) {
	return;
}

?>

<input type="hidden" name="<?php echo esc_attr( $args['label_for'] ); ?>[]">
<table class="widefat striped webcomic-records">
	<thead>
		<tr>
			<td class="manage-column" scope="col"><?php esc_html_e( 'Web Address', 'webcomic' ); ?></td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><input type="text" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" class="large-text"></td>
		</tr>
		<?php foreach ( $args['option'] as $referrer ) : ?>
			<tr>
				<td><input type="text" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="<?php echo esc_attr( $referrer ); ?>" class="large-text"></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<p class="description"><?php esc_html_e( 'The web addresses a user must be referred from to view new comics.', 'webcomic' ); ?></p>

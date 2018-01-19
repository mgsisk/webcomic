<?php
/**
 * Deprecated settings section
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Compat;

if ( ! isset( $args ) ) {
	return;
}

wp_nonce_field( $args['compat'], $args['compat'] );

?>

<p><?php esc_html_e( "This Webcomic feature is deprecated. Previously created image sizes are listed here &ndash; and will continue to work &ndash; but can't be modified, and new ones can't be created. Checked sizes will be deleted when changes are saved, and this section will dissapear if all sizes are deleted.", 'webcomic' ); ?></p>
<input type="hidden" name="webcomic[compat][image_size_delete][]">
<table class="widefat fixed striped">
	<thead>
		<tr>
			<td scope="col" class="manag-column check-column"><input type="checkbox"></td>
			<th scope="col" class="manage-column"><?php esc_html_e( 'Name', 'webcomic' ); ?></th>
			<th scope="col" class="manage-column"><?php esc_html_e( 'Width', 'webcomic' ); ?></th>
			<th scope="col" class="manage-column"><?php esc_html_e( 'Height', 'webcomic' ); ?></th>
			<th scope="col" class="manage-column"><?php esc_html_e( 'Crop', 'webcomic' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $args['option'] as $size => $attr ) : ?>
			<tr>
				<th scope="row" class="check-column"><input type="checkbox" id="image_size_delete_<?php echo esc_attr( $size ); ?>" name="webcomic[compat][image_size_delete][]" value="<?php echo esc_attr( $size ); ?>"></th>
				<td><label for="image_size_delete_<?php echo esc_attr( $size ); ?>"><?php echo esc_html( $size ); ?></label></td>
				<td><?php echo esc_html( $attr['width'] ); ?></td>
				<td><?php echo esc_html( $attr['width'] ); ?></td>
				<td>
					<?php if ( $attr['crop'] ) : ?>
						<span class="dashicons dashicons-yes">
							<span class="screen-reader-text"><?php esc_html_e( 'Yes', 'webcomic' ); ?></span>
						</span>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

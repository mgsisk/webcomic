<?php
/**
 * Prints field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Commerce;

if ( ! isset( $args ) ) {
	return;
}

?>

<fieldset>
	<input type="hidden" name="<?php echo esc_attr( $args['label_for'] ); ?>">
	<label>
		<input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked( $args['option'] ); ?>>
		<span><?php esc_html_e( 'Use a shopping cart for print sales', 'webcomic' ); ?></span>
	</label><br>
</fieldset>
<input type="hidden" name="<?php echo esc_attr( $args['label_prints'] ); ?>[default][]">
<input type="hidden" name="<?php echo esc_attr( $args['label_prints'] ); ?>[name][]">
<input type="hidden" name="<?php echo esc_attr( $args['label_prints'] ); ?>[slug][]">
<input type="hidden" name="<?php echo esc_attr( $args['label_prints'] ); ?>[price][]">
<input type="hidden" name="<?php echo esc_attr( $args['label_prints'] ); ?>[stock][]">
<table class="widefat striped webcomic-records">
	<thead>
		<tr>
			<td class="manage-column check-column" scope="col">
				<span class="screen-reader-text"><?php esc_html_e( 'Default', 'webcomic' ); ?></span>
				<input type="checkbox">
			</td>
			<td class="manage-column" scope="col"><?php esc_html_e( 'Name', 'webcomic' ); ?></td>
			<td class="manage-column" scope="col"><?php esc_html_e( 'Slug', 'webcomic' ); ?></td>
			<td class="manage-column" scope="col"><?php esc_html_e( 'Price', 'webcomic' ); ?></td>
			<td class="manage-column" scope="col"><?php esc_html_e( 'Stock', 'webcomic' ); ?></td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th scope="row" class="check-column">
				<input type="hidden" name="<?php echo esc_attr( $args['label_prints'] ); ?>[default][]">
				<input type="checkbox" name="<?php echo esc_attr( $args['label_prints'] ); ?>[default][]" value="1" checked>
			</th>
			<td><input type="text" name="<?php echo esc_attr( $args['label_prints'] ); ?>[name][]"></td>
			<td><input type="text" name="<?php echo esc_attr( $args['label_prints'] ); ?>[slug][]"></td>
			<td><input type="number" name="<?php echo esc_attr( $args['label_prints'] ); ?>[price][]" min="1" step="0.01" value="1" class="small-text"></td>
			<td><input type="number" name="<?php echo esc_attr( $args['label_prints'] ); ?>[stock][]" min="0" value="0" class="small-text"></td>
		</tr>
		<?php foreach ( $args['option_prints'] as $key => $print ) : ?>
			<tr>
				<th scope="row" class="check-column">
					<input type="hidden" name="<?php echo esc_attr( $args['label_prints'] ); ?>[default][]">
					<input type="checkbox" name="<?php echo esc_attr( $args['label_prints'] ); ?>[default][]" value="1" <?php checked( $print['default'] ); ?>>
				</th>
				<td><input type="text" name="<?php echo esc_attr( $args['label_prints'] ); ?>[name][]" value="<?php echo esc_attr( $print['name'] ); ?>"></td>
				<td><input type="text" name="<?php echo esc_attr( $args['label_prints'] ); ?>[slug][]" value="<?php echo esc_attr( $key ); ?>"></td>
				<td><input type="number" name="<?php echo esc_attr( $args['label_prints'] ); ?>[price][]" value="<?php echo esc_attr( $print['price'] ); ?>" min="1" step="0.01" class="small-text"></td>
				<td><input type="number" name="<?php echo esc_attr( $args['label_prints'] ); ?>[stock][]" value="<?php echo esc_attr( $print['stock'] ); ?>" min="0" class="small-text"></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<p class="description"><?php esc_html_e( 'The types of prints available for sale. Checked prints will be available by default on new comics.', 'webcomic' ); ?></p>

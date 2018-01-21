<?php
/**
 * Prints metabox
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Commerce\MetaBox;

if ( ! isset( $args ) ) {
	return;
}

wp_nonce_field( $args['nonce'], $args['nonce'] );

if ( ! $args['prints'] ) :
	$url = add_query_arg(
		[
			'post_type' => get_post_type(),
			'page'      => get_post_type() . '_options',
		], admin_url( 'edit.php' )
	);
?>
	<p><a href="<?php echo esc_url( $url ); ?>" class="button"><?php esc_html_e( 'Manage Prints', 'webcomic' ); ?></a></p>
	<input type="hidden" name="webcomic_commerce_prints[]">
	<input type="hidden" name="webcomic_commerce_prints_adjust[]">
<?php
	return;
endif;
?>

<div class="webcomic-commerce-container">
	<input type="hidden" name="webcomic_commerce_prints[]">
	<input type="hidden" name="webcomic_commerce_prints_adjust[]">
	<table class="widefat striped">
		<thead>
			<tr>
				<td class="manage-column check-column"><input type="checkbox" <?php checked( $args['all_prints'] ); ?>></td>
				<td class="manage-column" scope="col"><?php esc_html_e( 'Type', 'webcomic' ); ?></td>
				<td class="manage-column" scope="col"><?php esc_html_e( 'Adjustment', 'webcomic' ); ?></td>
				<td class="manage-column" scope="col"><?php esc_html_e( 'Price', 'webcomic' ); ?></td>
				<td class="manage-column" scope="col"><?php esc_html_e( 'Sold / Stock', 'webcomic' ); ?></td>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ( $args['prints'] as $key => $print ) :
				$sold         = (int) get_post_meta( get_the_ID(), "webcomic_commerce_prints_sold_{$key}", true );
				$adjust       = (int) get_post_meta( get_the_ID(), "webcomic_commerce_prints_adjust_{$key}", true );
				$checkbox     = str_replace( '%s', $key, $args['label_adjust'] );
				$label_adjust = str_replace( '_prints[', '_prints_adjust[', $checkbox );
			?>
				<tr>
					<th class="check-column">
						<?php if ( ! $print['stock'] || $sold < $print['stock'] ) : ?>
							<input type="checkbox" id="<?php echo esc_attr( $checkbox ); ?>" name="webcomic_commerce_prints[]" value="<?php echo esc_attr( $key ); ?>" <?php checked( in_array( $key, $args['option'], true ) ); ?>>
						<?php endif; ?>
					</th>
					<td><label for="<?php echo esc_attr( $checkbox ); ?>"><?php echo esc_html( $print['name'] ); ?></label></td>
					<td>
						<label class="webcomic_commerce_print_price <?php echo esc_attr( $key ); ?>">
							<input type="number" id="<?php echo esc_attr( $label_adjust ); ?>" name="<?php echo esc_attr( $label_adjust ); ?>" value="<?php echo esc_attr( $adjust ); ?>" min="-100" class="small-text" data-price="<?php echo esc_attr( $print['price'] ); ?>">%
						</label>
					</td>
					<td>
						<label for="<?php echo esc_attr( $label_adjust ); ?>">
							<output><?php echo esc_html( get_webcomic_print_price( $key ) ); ?></output>
							<?php echo esc_html( $args['currency'] ); ?>
						</label>
					</td>
					<td>
						<label for="<?php echo esc_attr( $checkbox ); ?>">
							<?php
							$stock = $print['stock'];

							if ( ! $stock ) {
								$stock = '&infin;';
							}

							// Translators: 1: Number of prints sold. 2: Number of prints available.
							printf( esc_html__( '%1$s / %2$s', 'webcomic' ), (int) get_post_meta( get_the_ID(), "webcomic_commerce_prints_sold_{$key}", true ), esc_html( $stock ) );
							?>
						</label>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

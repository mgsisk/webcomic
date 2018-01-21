<?php
/**
 * Prints quick edit field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Commerce\MetaBox;

if ( ! isset( $args ) ) {
	return;
}

wp_nonce_field( $args['nonce'], $args['nonce'] );

?>

<fieldset class="inline-edit-col-left">
	<div class="inline-edit-col">
		<span class="title">
			<?php
			$title = __( 'Prints', 'webcomic' );


			if ( $args['bulk'] ) :
				$title = __( 'Prints to Add', 'webcomic' );
			endif;

			echo esc_html( $title );
			?>
		</span>
		<ul class="cat-checklist category-checklist webcomic-commerce-prints-checklist">
			<?php foreach ( $args['prints'] as $key => $print ) : ?>
				<li>
					<label>
						<input type="checkbox" name="webcomic_commerce_prints[]" value="<?php echo esc_attr( $key ); ?>"> <?php echo esc_html( $print['name'] ); ?>
					</label>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<input type="hidden" name="webcomic_commerce_prints[]">
	<input type="hidden" name="webcomic_commerce_prints_quick_edit" value="1">
	<?php if ( $args['bulk'] ) : ?>
		<input type="hidden" name="webcomic_commerce_prints_bulk" value="1">
	<?php endif; ?>
</fieldset>

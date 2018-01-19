<?php
/**
 * Collection quick edit field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\MetaBox;

if ( ! isset( $args ) ) {
	return;
}

wp_nonce_field( $args['nonce'], $args['nonce'] );

?>

<fieldset class="inline-edit-col-left">
	<div class="inline-edit-col">
		<label>
			<span class="title"><?php esc_html_e( 'Collection', 'webcomic' ); ?></span>
			<input type="hidden" name="webcomic_page_collection_quick_edit" value="1">
			<select name="webcomic_page_collection">
				<?php if ( $args['bulk'] ) : ?>
					<option value="no-change"><?php esc_html_e( '&mdash; No Change &mdash;', 'webcomic' ); ?></option>
				<?php endif; ?>
				<option value=""><?php esc_html_e( '(no collection)', 'webcomic' ); ?></option>
				<?php foreach ( webcomic( 'option.collections' ) as $collection ) : ?>
					<option value="<?php echo esc_attr( $collection ); ?>"><?php echo esc_html( get_webcomic_collection_title( $collection ) ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>
	</div>
</fieldset>

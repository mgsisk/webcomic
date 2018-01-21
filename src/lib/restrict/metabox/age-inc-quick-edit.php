<?php
/**
 * Age quick edit field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Restrict\MetaBox;

if ( ! isset( $args ) ) {
	return;
}

wp_nonce_field( $args['nonce'], $args['nonce'] );

?>

<fieldset class="inline-edit-col-left">
	<div class="inline-edit-col">
		<label class="alignleft">
			<span class="title"><?php esc_html_e( 'Min Age', 'webcomic' ); ?></span>
			<input type="number" name="webcomic_restrict_age" value="0" min="0" class="small-text">
		</label>
		<input type="hidden" name="webcomic_restrict_age_quick_edit" value="1">
		<?php if ( $args['bulk'] ) : ?>
			<em class="alignleft inline-edit-or">&nbsp;<?php echo esc_html_e( '&ndash;OR&ndash;', 'webcomic' ); ?></em>
			<label class="alignleft">
				<input type="checkbox" name="webcomic_restrict_age" value="no-change" checked>
				<span class="checkbox-title"><?php esc_html_e( 'No Change', 'webcomic' ); ?></span>
			</label>
		<?php endif; ?>
	</div>
</fieldset>

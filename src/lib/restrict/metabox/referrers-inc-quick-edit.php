<?php
/**
 * Referrers quick edit field
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
		<label>
			<span class="howto">
				<?php echo esc_html( $args['title'] ); ?>
			</span>
			<textarea name="webcomic_restrict_referrers" class="large-text"></textarea>
		</label>
		<input type="hidden" name="webcomic_restrict_referrers_quick_edit" value="1">
		<?php if ( $args['bulk'] ) : ?>
			<input type="hidden" name="webcomic_restrict_referrers_bulk" value="1">
		<?php endif; ?>
	</div>
</fieldset>

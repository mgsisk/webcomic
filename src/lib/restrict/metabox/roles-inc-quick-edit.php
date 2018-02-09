<?php
/**
 * Role quick edit field
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
		<span class="title">
			<?php echo esc_html( $args['title'] ); ?>
		</span>
		<ul class="cat-checklist category-checklist webcomic-restrict-roles-checklist">
			<li>
				<label>
					<input type="checkbox" name="webcomic_restrict_roles[]" value="~loggedin~"> <?php esc_html_e( 'Any registered user', 'webcomic' ); ?>
				</label>
			</li>
			<?php foreach ( get_editable_roles() as $key => $role ) : ?>
				<li>
					<label>
						<input type="checkbox" name="webcomic_restrict_roles[]" value="<?php echo esc_attr( $key ); ?>"> <?php echo esc_html( $role['name'] ); ?>
					</label>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<input type="hidden" name="webcomic_restrict_roles[]">
	<input type="hidden" name="webcomic_restrict_roles_quick_edit" value="1">
	<?php if ( $args['bulk'] ) : ?>
		<input type="hidden" name="webcomic_restrict_roles_bulk" value="1">
	<?php endif; ?>
</fieldset>

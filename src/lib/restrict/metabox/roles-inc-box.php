<?php
/**
 * Roles meta box
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Restrict\MetaBox;

if ( ! isset( $args ) ) {
	return;
}

wp_nonce_field( $args['nonce'], $args['nonce'] );

?>

<p class="post-attributes-label-wrapper">
	<label for="webcomic_restrict_roles_loggedin" class="post-attributes-label"><?php esc_html_e( 'Accessible Roles', 'webcomic' ); ?></label>
</p>
<p>
	<input type="hidden" name="webcomic_restrict_roles[]">
	<label>
		<input type="checkbox" id="webcomic_restrict_roles_loggedin" name="webcomic_restrict_roles[]" value="~loggedin~" <?php checked( in_array( '~loggedin~', $args['option_roles'], true ) ); ?>> <?php esc_html_e( 'Any registered user', 'webcomic' ); ?>
	</label><br>
	<?php foreach ( get_editable_roles() as $key => $role ) : ?>
		<label>
			<input type="checkbox" name="webcomic_restrict_roles[]" value="<?php echo esc_attr( $key ); ?>" <?php checked( in_array( $key, $args['option_roles'], true ) ); ?>> <?php echo esc_html( $role['name'] ); ?>
		</label><br>
	<?php endforeach; ?>
</p>
<p class="post-attributes-label-wrapper">
	<label class="post-attributes-label"><?php esc_html_e( 'Role-Restricted Media', 'webcomic' ); ?></label>
</p>
<div data-input="webcomic_restrict_roles_media" data-webcomic-media-manager></div>
<input type="hidden" name="webcomic_restrict_roles_media" value="<?php echo esc_attr( $args['option_media'] ); ?>">

<?php
/**
 * Roles setting field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Restrict;

if ( ! isset( $args ) ) {
	return;
}

?>

<fieldset>
	<input type="hidden" name="<?php echo esc_attr( $args['label_for'] ); ?>[]">
	<label>
		<input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="~loggedin~" <?php checked( in_array( '~loggedin~', $args['option'], true ) ); ?>>
		<span><?php esc_html_e( 'Any registered user', 'webcomic' ); ?></span>
	</label><br>
	<?php foreach ( get_editable_roles() as $key => $role ) : ?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="<?php echo esc_attr( $key ); ?>" <?php checked( in_array( $key, $args['option'], true ) ); ?>>
			<span><?php echo esc_html( $role['name'] ); ?></span>
		</label><br>
	<?php endforeach; ?>
</fieldset>
<p class="description"><?php esc_html_e( 'The user roles allowed to view new comics.', 'webcomic' ); ?></p>

<?php
/**
 * Permissions setting field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe;

if ( ! isset( $args ) ) {
	return;
}

?>

<fieldset>
	<label>
		<input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked( in_array( $args['option'], [ 'name_email', 'loggedin' ], true ) ); ?>>
		<span><?php esc_html_e( 'Transcript authors must', 'webcomic' ); ?></span>
	</label>
	<select name="<?php echo esc_attr( $args['label_option'] ); ?>">
		<option value="loggedin" <?php selected( $args['option'], 'loggedin' ); ?>><?php esc_html_e( 'be registered and logged in', 'webcomic' ); ?></option>
		<option value="name_email" <?php selected( $args['option'], 'name_email' ); ?>><?php esc_html_e( 'fill out name and email', 'webcomic' ); ?></option>
	</select>
	<br>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( $args['label_publish_check'] ); ?>" value="1" <?php checked( in_array( $args['option_publish'], [ 'name_email', 'loggedin' ], true ) ); ?>>
		<span><?php esc_html_e( 'Publish transcripts from authors that', 'webcomic' ); ?></span>
	</label>
	<select name="<?php echo esc_attr( $args['label_publish'] ); ?>">
		<option value="loggedin" <?php selected( $args['option_publish'], 'loggedin' ); ?>><?php esc_html_e( 'are registered and logged in', 'webcomic' ); ?></option>
		<option value="name_email" <?php selected( $args['option_publish'], 'name_email' ); ?>><?php esc_html_e( 'fill out name and email', 'webcomic' ); ?></option>
	</select>
	<br>
</fieldset>

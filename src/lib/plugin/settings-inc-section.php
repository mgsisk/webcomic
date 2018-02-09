<?php
/**
 * Settings section
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Plugin;

if ( ! isset( $args ) ) {
	return;
}

?>

<?php if ( $args['uninstall'] ) : ?>
<div class='notice notice-warning is-dismissible'><p><?php echo esc_html( $args['uninstall'] ); ?></p></div>
<?php endif; ?>
<p><?php esc_html_e( 'Refer to the Help tab at the top of the screen for information about the settings on this page.', 'webcomic' ); ?></p>
<input type="hidden" name="<?php echo esc_attr( $args['label_for'] ); ?>[]">
<table class="wp-list-table widefat plugins">
	<thead>
		<tr>
			<td scope="col" class="check-column"><label class="screen-reader-text" for="cb-select-all-1"><?php esc_html__( 'Select All', 'webcomic' ); ?></label><input id="cb-select-all-1" type="checkbox" <?php checked( 9 === count( $args['option'] ) ); ?>></td>
			<th scope="col" class="column-primary"><?php esc_html_e( 'Component', 'webcomic' ); ?></td>
			<th scope="col"><?php esc_html_e( 'Description', 'webcomic' ); ?></td>
		</tr>
	</thead>
	<tbody id="the-list">

		<tr class="<?php echo esc_attr( $args['css_class'][ (bool) checked( in_array( 'alert', $args['option'], true ), true, false ) ] ); ?>">
			<th class="check-column"><input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>_alert" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="alert" <?php checked( in_array( 'alert', $args['option'], true ) ); ?>></th>
			<td class="plugin-title">
				<label for="<?php echo esc_attr( $args['label_for'] ); ?>_alert">
					<strong><?php esc_html_e( 'Alert', 'webcomic' ); ?></strong>
					<div class="row-actions visible"></div>
				</label>
			</td>
			<td class="desc">
				<div class="plugin-description">
					<p><?php esc_html_e( 'Alert adds settings for creating buffer and hiatus email alerts.', 'webcomic' ); ?></p>
				</div>
			</td>
		</tr>

		<tr class="<?php echo esc_attr( $args['css_class'][ (bool) checked( in_array( 'character', $args['option'], true ), true, false ) ] ); ?>">
			<th class="check-column"><input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>_character" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="character" <?php checked( in_array( 'character', $args['option'], true ) ); ?>></th>
			<td class="plugin-title">
				<label for="<?php echo esc_attr( $args['label_for'] ); ?>_character">
					<strong><?php esc_html_e( 'Character', 'webcomic' ); ?></strong>
				</label>
			</td>
			<td class="desc">
				<div class="plugin-description">
					<p><?php esc_html_e( 'Character allows you to tag character appearances and use character-based archives.', 'webcomic' ); ?></p>
				</div>
			</td>
		</tr>

		<tr class="<?php echo esc_attr( $args['css_class'][ (bool) checked( in_array( 'commerce', $args['option'], true ), true, false ) ] ); ?>">
			<th class="check-column"><input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>_commerce" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="commerce" <?php checked( in_array( 'commerce', $args['option'], true ) ); ?>></th>
			<td class="plugin-title">
				<label for="<?php echo esc_attr( $args['label_for'] ); ?>_commerce"><strong><?php esc_html_e( 'Commerce', 'webcomic' ); ?></strong></label>
			</td>
			<td class="desc">
				<div class="plugin-description">
					<p><?php esc_html_e( 'Commerce adds PayPal-based print selling and donation features.', 'webcomic' ); ?></p>
				</div>
			</td>
		</tr>

		<tr class="<?php echo esc_attr( $args['css_class'][ (bool) checked( in_array( 'compat', $args['option'], true ), true, false ) ] ); ?>">
			<th class="check-column"><input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>_compat" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="compat" <?php checked( in_array( 'compat', $args['option'], true ) ); ?>></th>
			<td class="plugin-title">
				<label for="<?php echo esc_attr( $args['label_for'] ); ?>_compat"><strong><?php esc_html_e( 'Compat', 'webcomic' ); ?></strong></label>
			</td>
			<td class="desc">
				<div class="plugin-description">
					<p><?php esc_html_e( 'Compat provides an array of features that ease the transition from older versions of Webcomic.', 'webcomic' ); ?></p>
				</div>
			</td>
		</tr>

		<tr class="<?php echo esc_attr( $args['css_class'][ (bool) checked( in_array( 'restrict', $args['option'], true ), true, false ) ] ); ?>">
			<th class="check-column"><input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>_restrict" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="restrict" <?php checked( in_array( 'restrict', $args['option'], true ) ); ?>></th>
			<td class="plugin-title">
				<label for="<?php echo esc_attr( $args['label_for'] ); ?>_restrict"><strong><?php esc_html_e( 'Restrict', 'webcomic' ); ?></strong></label>
			</td>
			<td class="desc">
				<div class="plugin-description">
					<p><?php esc_html_e( 'Restrict adds features for restricting access to comics based on age, referrer, or user role.', 'webcomic' ); ?></p>
				</div>
			</td>
		</tr>

		<tr class="<?php echo esc_attr( $args['css_class'][ (bool) checked( in_array( 'storyline', $args['option'], true ), true, false ) ] ); ?>">
			<th class="check-column"><input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>_storyline" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="storyline" <?php checked( in_array( 'storyline', $args['option'], true ) ); ?>></th>
			<td class="plugin-title">
				<label for="<?php echo esc_attr( $args['label_for'] ); ?>_storyline"><strong><?php esc_html_e( 'Storyline', 'webcomic' ); ?></strong></label>
			</td>
			<td class="desc">
				<div class="plugin-description">
					<p><?php esc_html_e( 'Storyline allows you to organize comics by storyline and use storyline-based archives.', 'webcomic' ); ?></p>
				</div>
			</td>
		</tr>

		<tr class="<?php echo esc_attr( $args['css_class'][ (bool) checked( in_array( 'transcribe', $args['option'], true ), true, false ) ] ); ?>">
			<th class="check-column"><input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>_transcribe" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="transcribe" <?php checked( in_array( 'transcribe', $args['option'], true ) ); ?>></th>
			<td class="plugin-title">
				<label for="<?php echo esc_attr( $args['label_for'] ); ?>_transcribe"><strong><?php esc_html_e( 'Transcribe', 'webcomic' ); ?></strong></label>
			</td>
			<td class="desc">
				<div class="plugin-description">
					<p><?php esc_html_e( 'Transcribe adds comic transcription features for SEO-enhancing text alternatives to your comics.', 'webcomic' ); ?></p>
				</div>
			</td>
		</tr>

		<tr class="<?php echo esc_attr( $args['css_class'][ (bool) checked( in_array( 'twitter', $args['option'], true ), true, false ) ] ); ?>">
			<th class="check-column"><input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>_twitter" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="twitter" <?php checked( in_array( 'twitter', $args['option'], true ) ); ?>></th>
			<td class="plugin-title">
				<label for="<?php echo esc_attr( $args['label_for'] ); ?>_twitter"><strong><?php esc_html_e( 'Twitter', 'webcomic' ); ?></strong></label>
			</td>
			<td class="desc">
				<div class="plugin-description">
					<p><?php esc_html_e( 'Twitter adds settings for updating your Twitter account status when publishing comics.', 'webcomic' ); ?></p>
				</div>
			</td>
		</tr>
	</tbody>
</table>

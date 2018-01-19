<?php
/**
 * Authors meta box
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Transcribe\MetaBox;

if ( ! isset( $args ) ) {
	return;
}

wp_nonce_field( $args['nonce'], $args['nonce'] );

?>

<input type="hidden" name="webcomic_transcript_authors[name][]">
<input type="hidden" name="webcomic_transcript_authors[email][]">
<input type="hidden" name="webcomic_transcript_authors[url][]">
<input type="hidden" name="webcomic_transcript_authors[time][]">
<input type="hidden" name="webcomic_transcript_authors[ip][]">
<div class="webcomic-authors">
	<table class="widefat striped webcomic-records">
		<thead>
			<tr>
				<td class="manage-column" scope="col"><?php esc_html_e( 'Details', 'webcomic' ); ?></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<label>
						<span><?php esc_html_e( 'Name', 'webcomic' ); ?></span>
						<input type="text" name="webcomic_transcript_authors[name][]" value="<?php echo esc_attr( $args['user_name'] ); ?>">
					</label><br>
					<label>
						<span><?php esc_html_e( 'Email', 'webcomic' ); ?></span>
						<input type="email" name="webcomic_transcript_authors[email][]" value="<?php echo esc_attr( $args['user_email'] ); ?>">
					</label><br>
					<label>
						<span><?php esc_html_e( 'URL', 'webcomic' ); ?></span>
						<input type="url" name="webcomic_transcript_authors[url][]" value="<?php echo esc_attr( $args['user_url'] ); ?>">
					</label><br>
					<label>
						<span><?php esc_html_e( 'Date', 'webcomic' ); ?></span>
						<input type="datetime-local" name="webcomic_transcript_authors[time][]">
					</label><br>
					<label>
						<span><?php esc_html_e( 'IP', 'webcomic' ); ?></span>
						<input type="text" name="webcomic_transcript_authors[ip][]" value="<?php echo esc_attr( webcomic( 'GLOBALS._SERVER.REMOTE_ADDR' ) ); ?>" readonly>
					</label>
				</td>
			</tr>
			<?php foreach ( $args['option'] as $author ) : ?>
				<tr>
					<td>
						<label>
							<span><?php esc_html_e( 'Name', 'webcomic' ); ?></span>
							<input type="text" name="webcomic_transcript_authors[name][]" value="<?php echo esc_attr( $author['name'] ); ?>">
						</label><br>
						<label>
							<span><?php esc_html_e( 'Email', 'webcomic' ); ?></span>
							<input type="email" name="webcomic_transcript_authors[email][]" value="<?php echo esc_attr( $author['email'] ); ?>">
						</label><br>
						<label>
							<span><?php esc_html_e( 'URL', 'webcomic' ); ?></span>
							<input type="url" name="webcomic_transcript_authors[url][]" value="<?php echo esc_attr( $author['url'] ); ?>">
						</label><br>
						<label>
							<span><?php esc_html_e( 'Date', 'webcomic' ); ?></span>
							<input type="datetime-local" name="webcomic_transcript_authors[time][]" step="1" value="<?php echo esc_attr( str_replace( ' ', 'T', $author['time'] ) ); ?>">
						</label><br>
						<label>
							<span><?php esc_html_e( 'IP', 'webcomic' ); ?></span>
							<input type="text" name="webcomic_transcript_authors[ip][]" value="<?php echo esc_attr( $author['ip'] ); ?>" readonly>
						</label>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

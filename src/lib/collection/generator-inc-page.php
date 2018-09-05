<?php
/**
 * Comic generator
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection;

if ( ! isset( $args ) ) {
	return;
}

?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<div id="col-container" class="wp-clearfix">
		<div id="col-left">
			<div class="col-wrap">
				<form method="post" id="webcomic_generator" class="form-wrap">
					<?php
					do_settings_sections( 'webcomic_generator' );

					submit_button( __( 'Generate Comics', 'webcomic' ), 'primary', 'webcomic_generator[generate]' );
					?>
				</form>
			</div>
		</div>
		<div id="col-right">
			<div class="col-wrap">
				<table class="widefat fixed striped media">
					<thead>
						<td scope="col" class="manage-column check-column"><input type="checkbox"></td>
						<th scope="col" class="manage-column"><?php esc_html_e( 'Media', 'webcomic' ); ?></th>
					</thead>
					<tbody>
						<?php
						$count = 0;
						$media = get_posts(
							[
								'post_type'      => 'attachment',
								'post_status'    => 'inherit',
								'post_mime_type' => 'image',
								'posts_per_page' => -1,
								'meta_query'     => [
									[
										'key'     => 'webcomic_post',
										'compare' => 'NOT EXISTS',
									],
								],
							]
						);

						foreach ( $media as $image ) :
							$count++;
							?>
							<tr data-id="<?php echo esc_attr( $image->ID ); ?>">
								<th scope="row" class="check-column"><input type="checkbox" id="media-<?php echo esc_attr( $image->ID ); ?>" name="webcomic_generate[]" value="<?php echo esc_attr( $image->ID ); ?>" form="webcomic_generator" <?php checked( in_array( $image->ID, $args['media'], true ) ); ?>></th>
								<td class="title column-title column-primary">
									<label for="media-<?php echo esc_attr( $image->ID ); ?>">
										<strong class="has-media-icon">
											<span class="media-icon image-icon"><?php echo wp_get_attachment_image( $image->ID, 'medium' ); ?></span>
											<?php echo esc_html( get_the_title( $image->ID ) ); ?>
										</strong>
										<p class="filename"><?php echo esc_html( basename( get_attached_file( $image->ID ) ) ); ?></p>
									</label>
								</td>
							</tr>
							<?php
						endforeach;

						if ( ! $count ) :
							?>
							<tr>
								<td colspan="2"><?php esc_html_e( 'No orphaned media found.', 'webcomic' ); ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

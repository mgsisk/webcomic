<?php
/**
 * Comic media matcher
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
				<form method="post" id="webcomic_matcher" class="form-wrap">
					<?php
					do_settings_sections( 'webcomic_matcher' );

					submit_button( __( 'Find Matches', 'webcomic' ) );
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
						<th scope="col" class="manage-column"><?php esc_html_e( 'Comic', 'webcomic' ); ?></th>
					</thead>
					<tbody>
						<?php
						$match = [];
						$posts = [];
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

						if ( $args['collection'] && $media ) {
							$posts = get_posts(
								[
									'post_type'  => $args['collection'],
									'meta_query' => [
										[
											'key'     => 'webcomic_media',
											'compare' => 'NOT EXISTS',
										],
									],
								]
							);
						}

						foreach ( $posts as $post ) {
							$post_field = $post->{$args['match_post']};

							if ( 'post_date' === $args['match_post'] ) {
								$post_field = mysql2date( $args['post_date'], $post_field );
							} elseif ( 'post_custom' === $args['match_post'] ) {
								$post_field = $post->{$args['post_custom']};

								if ( ! is_string( $post_field ) ) {
									$post_field = '';
								}
							}

							if ( ! $post_field ) {
								continue;
							}

							foreach ( $media as $image ) {
								$media_field = $image->{$args['match_media']};

								if ( 'post_date' === $args['match_media'] ) {
									$media_field = mysql2date( $args['post_date'], $media_field );
								} elseif ( 'guid' === $args['match_media'] ) {
									$media_field = basename( $media_field );
								}

								if ( $args['exact_match'] && $post_field === $media_field ) {
									$match[] = "{$post->ID}-{$image->ID}";
								} elseif ( ! $args['exact_match'] && ( false !== strpos( $post_field, $media_field ) || false !== strpos( $media_field, $post_field ) ) ) {
									$match[] = "{$post->ID}-{$image->ID}";
								}
							}
						}

						foreach ( $match as $ids ) :
							list( $post, $media ) = explode( '-', $ids );
						?>
						<tr>
							<th scope="row" class="check-column"><input type="checkbox" id="match-<?php echo esc_attr( $ids ); ?>" name="webcomic_match[]" value="<?php echo esc_attr( $ids ); ?>" form="webcomic_matcher"></th>
							<td class="title column-title column-primary">
								<label for="match-<?php echo esc_attr( $ids ); ?>">
									<strong class="has-media-icon">
										<span class="media-icon image-icon"><?php echo wp_get_attachment_image( $media, 'medium' ); ?></span>
										<?php echo esc_html( get_the_title( $media ) ); ?>
									</strong>
									<p class="filename"><?php echo esc_html( basename( get_attached_file( $media ) ) ); ?></p>
								</label>
							</td>
							<td class="title column-title column-primary">
								<label for="match-<?php echo esc_attr( $ids ); ?>">
									<strong><?php echo esc_html( get_the_title( $post ) ); ?></strong>
									<p><?php echo esc_html( get_post_status_object( get_post_status( $post ) )->label ); ?></p>
								</label>
							</td>
						</tr>
						<?php
						endforeach;

						if ( $args['collection'] && ! $match ) :
						?>
						<tr>
							<td colspan="3"><?php esc_html_e( 'No matches found.', 'webcomic' ); ?></td>
						</tr>
						<?php elseif ( ! $match ) : ?>
						<tr>
							<td colspan="3"><?php esc_html_e( 'Click Find Matches to search for media/comic matches.', 'webcomic' ); ?></td>
						</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<br>
				<?php
				if ( $match ) {
					submit_button(
						__( 'Save Selected Matches', 'webcomic' ), 'primary', 'webcomic_matcher[save]', true, [
							'form' => 'webcomic_matcher',
						]
					);
				}
				?>
			</div>
		</div>
	</div>
</div>

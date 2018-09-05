<?php
/**
 * Comic IPN logger
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Commerce;

if ( ! isset( $args ) ) {
	return;
}

?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form method="post" id="webcomic_commerce_logger">
		<?php if ( $args['posts'] ) : ?>
		<div class="tablenav top">
			<?php echo $args['submit']; // WPCS: xss ok. ?>
		</div>
		<?php endif; ?>
	</form>
	<table class="widefat fixed striped">
		<thead>
			<th scope="col" class="manage-column"><?php esc_html_e( 'Transaction', 'webcomic' ); ?></th>
			<th scope="col" class="manage-column"><?php esc_html_e( 'Item', 'webcomic' ); ?></th>
			<th scope="col" class="manage-column"><?php esc_html_e( 'Message', 'webcomic' ); ?></th>
			<th scope="col" class="manage-column"><?php esc_html_e( 'Date', 'webcomic' ); ?></th>
		</thead>
		<tbody>
			<?php
			foreach ( $args['posts'] as $post ) :
				$class   = '';
				$content = get_post_field( 'post_content', $post );

				if ( false !== strpos( $content, 'ERROR' ) ) {
					$class = 'error';
				}
				?>
				<tr class="<?php echo esc_attr( $class ); ?>">
					<td><?php echo esc_html( get_the_title( $post ) ); ?></td>
					<td><?php echo esc_html( get_the_excerpt( $post ) ); ?></td>
					<td><?php echo wp_kses_post( $content ); ?></td>
					<td>
						<?php
						// Translators: 1: Post publish date. 2: Post publish time.
						echo esc_html( sprintf( __( '%1$s @ %2$s', 'webcomic' ), get_the_date( '', $post ), get_the_time( '', $post ) ) );
						?>
					</td>
				</tr>
				<?php
			endforeach;

			if ( ! $args['posts'] ) :
				?>
				<tr>
					<td colspan="4"><?php esc_html_e( 'No instant payment notifications found.', 'webcomic' ); ?></td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
	<?php if ( $args['posts'] ) : ?>
	<div class="tablenav bottom">
		<?php echo $args['submit']; // WPCS: xss ok. ?>
	</div>
	<?php endif; ?>
</div>

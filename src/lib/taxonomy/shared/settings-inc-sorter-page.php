<?php
/**
 * Taxonomy sorter
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Taxonomy\Shared;

if ( ! isset( $args ) ) {
	return;
}

?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<div id="col-container" class="wp-clearfix">
		<div id="col-left">
			<div class="col-wrap">
				<p><?php echo esc_html( $args['help'] ); ?></p>
				<form method="post" id="webcomic_sorter" class="form-wrap">
					<?php wp_nonce_field( $args['nonce'], $args['nonce'] ); ?>
				</form>
				<p>
					<?php
					if ( wp_count_terms( $args['taxonomy'] ) ) {
						submit_button(
							__( 'Save Changes', 'webcomic' ), 'primary', 'webcomic_sorter[sort]', false, [
								'form' => 'webcomic_sorter',
							]
						);
					}
					?>
					<a href="<?php echo esc_attr( $args['admin_url'] ); ?>" class="button"><?php esc_html_e( 'Back', 'webcomic' ); ?></a>
				</p>
			</div>
		</div>
		<div id="col-right">
			<div class="col-wrap">
				<?php
				webcomic_terms_list(
					[
						'format'     => "<ol {$args['hierarchical']}>{{}}</ol>",
						'hide_empty' => false,
						'orderby'    => 'meta_value_num',
						'meta_query' => [
							'relation' => 'OR',
							[
								'key'     => 'webcomic_order',
								'compare' => 'EXISTS',
							],
							[
								'key'     => 'webcomic_order',
								'compare' => 'NOT EXISTS',
							],
						],
						'taxonomy'   => $args['taxonomy'],
						'walker'     => 'Mgsisk\Webcomic\Taxonomy\Walker\TermSorter',
					]
				);
				?>
			</div>
		</div>
	</div>
</div>

<?php
/**
 * Behavior field
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Taxonomy\Shared;

if ( ! isset( $args ) ) {
	return;
}

?>

<fieldset>
	<input type="hidden" name="<?php echo esc_attr( $args['label_for'] ); ?>">
	<input type="hidden" name="<?php echo esc_attr( $args['label_skip'] ); ?>">
	<input type="hidden" name="<?php echo esc_attr( $args['label_sort'] ); ?>">
	<input type="hidden" name="<?php echo esc_attr( $args['label_crossovers'] ); ?>">
	<label>
		<input type="checkbox" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="1" <?php checked( $args['option'] ); ?>>
		<span><?php esc_html_e( 'Allow hierarchical organization', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( $args['label_skip'] ); ?>" value="1" <?php checked( $args['option_skip'] ); ?>>
		<span><?php esc_html_e( 'Skip redundant terms while browsing', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" id="<?php echo esc_attr( $args['label_sort'] ); ?>" name="<?php echo esc_attr( $args['label_sort'] ); ?>" value="1" <?php checked( $args['option_sort'] ); ?>>
		<span><?php esc_html_e( 'Sort terms in custom order by default', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" id="<?php echo esc_attr( $args['label_crossovers'] ); ?>" name="<?php echo esc_attr( $args['label_crossovers'] ); ?>" value="1" <?php checked( $args['option_crossovers'] ); ?>>
		<span><?php esc_html_e( 'Include crossover comics on archive pages', 'webcomic' ); ?></span>
	</label><br>
</fieldset>

<?php
/**
 * Supports fields
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Settings;

if ( ! isset( $args ) ) {
	return;
}

?>

<fieldset class="check-column">
	<label><strong><?php esc_html_e( 'Features', 'webcomic' ); ?></strong></label><br>
	<input type="hidden" name="<?php echo esc_attr( $args['label_for'] ); ?>[]">
	<label>
		<input type="checkbox" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="author" <?php checked( in_array( 'author', $args['option'], true ) ); ?>>
		<span><?php esc_html_e( 'Author', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="comments" <?php checked( in_array( 'comments', $args['option'], true ) ); ?>>
		<span><?php esc_html_e( 'Comments', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="custom-fields" <?php checked( in_array( 'custom-fields', $args['option'], true ) ); ?>>
		<span><?php esc_html_e( 'Custom Fields', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="editor" <?php checked( in_array( 'editor', $args['option'], true ) ); ?>>
		<span><?php esc_html_e( 'Editor', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="excerpt" <?php checked( in_array( 'excerpt', $args['option'], true ) ); ?>>
		<span><?php esc_html_e( 'Excerpt', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="thumbnail" <?php checked( in_array( 'thumbnail', $args['option'], true ) ); ?>>
		<span><?php esc_html_e( 'Featured Image', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="post-formats" <?php checked( in_array( 'post-formats', $args['option'], true ) ); ?>>
		<span><?php esc_html_e( 'Post Formats', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="revisions" <?php checked( in_array( 'revisions', $args['option'], true ) ); ?>>
		<span><?php esc_html_e( 'Revisions', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="trackbacks" <?php checked( in_array( 'trackbacks', $args['option'], true ) ); ?>>
		<span><?php esc_html_e( 'Trackbacks', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( $args['label_for'] ); ?>[]" value="title" <?php checked( in_array( 'title', $args['option'], true ) ); ?>>
		<span><?php esc_html_e( 'Title', 'webcomic' ); ?></span>
	</label><br>
</fieldset>

<fieldset class="check-column">
	<label><strong><?php esc_html_e( 'Taxonomies', 'webcomic' ); ?></strong></label><br>
	<input type="hidden" name="<?php echo esc_attr( $args['label_tax'] ); ?>[]">
	<?php
	foreach ( $args['taxonomies'] as $taxonomy ) :
		?>
		<label>
			<input type="checkbox" name="<?php echo esc_attr( $args['label_tax'] ); ?>[]" value="<?php echo esc_attr( $taxonomy->name ); ?>" <?php checked( in_array( $taxonomy->name, $args['option_tax'], true ) ); ?>>
			<span><?php echo esc_html( $taxonomy->labels->name ); ?></span>
		</label><br>
	<?php endforeach; ?>
</fieldset>

<fieldset class="check-column">
	<label><strong><?php esc_html_e( 'Widget Areas', 'webcomic' ); ?></strong></label><br>
	<input type="hidden" name="<?php echo esc_attr( $args['label_area'] ); ?>[]">
	<label>
		<input type="checkbox" name="<?php echo esc_attr( $args['label_area'] ); ?>[]" value="infinite" <?php checked( in_array( 'infinite', $args['option_area'], true ) ); ?>>
		<span><?php esc_html_e( 'Infinite', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( $args['label_area'] ); ?>[]" value="media" <?php checked( in_array( 'media', $args['option_area'], true ) ); ?>>
		<span><?php esc_html_e( 'Media', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( $args['label_area'] ); ?>[]" value="meta" <?php checked( in_array( 'meta', $args['option_area'], true ) ); ?>>
		<span><?php esc_html_e( 'Meta', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( $args['label_area'] ); ?>[]" value="navigation" <?php checked( in_array( 'navigation', $args['option_area'], true ) ); ?>>
		<span><?php esc_html_e( 'Navigation', 'webcomic' ); ?></span>
	</label><br>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( $args['label_area'] ); ?>[]" value="syndicate" <?php checked( in_array( 'syndicate', $args['option_area'], true ) ); ?>>
		<span><?php esc_html_e( 'Syndication', 'webcomic' ); ?></span>
	</label><br>
</fieldset>

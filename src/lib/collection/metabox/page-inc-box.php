<?php
/**
 * Collection and template meta box
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\MetaBox;

if ( ! isset( $args ) ) {
	return;
}

wp_nonce_field( $args['nonce'], $args['nonce'] );

?>

<input type="hidden" name="<?php echo esc_attr( $args['label_content'] ); ?>">
<input type="hidden" name="<?php echo esc_attr( $args['label_meta'] ); ?>">
<input type="hidden" name="<?php echo esc_attr( $args['label_comments'] ); ?>">
<p class="post-attributes-label-wrapper">
	<label class="post-attributes-label" for="webcomic_page_collection"><?php esc_html_e( 'Collection', 'webcomic' ); ?></label>
</p>
<p>
	<select id="<?php echo esc_attr( $args['label_collection'] ); ?>" name="<?php echo esc_attr( $args['label_collection'] ); ?>">
		<option value=""><?php esc_html_e( '(no collection)', 'webcomic' ); ?></option>
		<?php foreach ( webcomic( 'option.collections' ) as $collection ) : ?>
			<option value="<?php echo esc_attr( $collection ); ?>" <?php selected( $collection, $args['option_collection'] ); ?>><?php echo esc_html( get_webcomic_collection_title( $collection ) ); ?></option>
		<?php endforeach; ?>
	</select>
</p>
<p class="post-attributes-label-wrapper">
	<label for="<?php echo esc_attr( $args['label_template'] ); ?>" class="post-attributes-label"><?php esc_html_e( 'Template', 'webcomic' ); ?></label>
</p>
<p>
	<select id="<?php echo esc_attr( $args['label_template'] ); ?>" name="<?php echo esc_attr( $args['label_template'] ); ?>">
		<option value=""><?php esc_html_e( 'None', 'webcomic' ); ?></option>
		<option value="landing" <?php selected( (bool) get_post_meta( get_the_ID(), 'webcomic_integrate_landing_page_order', true ) ); ?>><?php esc_html_e( 'Landing Page', 'webcomic' ); ?></option>
		<option value="infinite" <?php selected( (bool) get_post_meta( get_the_ID(), 'webcomic_integrate_infinite_order', true ) ); ?>><?php esc_html_e( 'Infinite Scroll', 'webcomic' ); ?></option>
	</select>
</p>
<div class="webcomic-template-landing">
	<p>
		<select id="<?php echo esc_attr( $args['label_order'] ); ?>" name="<?php echo esc_attr( $args['label_order'] ); ?>">
			<optgroup label="<?php esc_attr_e( 'Show&hellip;', 'webcomic' ); ?>">
				<option value="asc"><?php esc_html_e( 'First comic', 'webcomic' ); ?></option>
				<option value="desc" <?php selected( 'desc', $args['option_order'] ); ?>><?php esc_html_e( 'Last comic', 'webcomic' ); ?></option>
			</optgroup>
		</select>
	</p>
	<p>
		<label class="selectit">
			<input type="checkbox" name="<?php echo esc_attr( $args['label_content'] ); ?>" value="1" <?php checked( $args['option_content'] ); ?>>
			<?php esc_html_e( 'Show post content', 'webcomic' ); ?>
		</label><br>
		<label class="selectit">
			<input type="checkbox" name="<?php echo esc_attr( $args['label_meta'] ); ?>" value="1" <?php checked( $args['option_meta'] ); ?>>
			<?php esc_html_e( 'Show meta data', 'webcomic' ); ?>
		</label><br>
		<label class="selectit">
			<input type="checkbox" name="<?php echo esc_attr( $args['label_comments'] ); ?>" value="1" <?php checked( $args['option_comments'] ); ?>>
			<?php esc_html_e( 'Show comments', 'webcomic' ); ?>
		</label><br>
	</p>
</div>
<div class="webcomic-template-infinite">
	<p>
		<select id="<?php echo esc_attr( $args['label_infinite'] ); ?>" name="<?php echo esc_attr( $args['label_infinite'] ); ?>">
			<optgroup label="<?php esc_attr_e( 'Start with&hellip;', 'webcomic' ); ?>">
				<option value="asc"><?php esc_html_e( 'First comic', 'webcomic' ); ?></option>
				<option value="desc" <?php selected( 'desc', $args['option_infinite'] ); ?>><?php esc_html_e( 'Last comic', 'webcomic' ); ?></option>
			</optgroup>
		</select>
	</p>
</div>

<p>
	<?php
	// Translators: Customizer link.
	printf( esc_html__( 'Templates require %s.', 'webcomic' ), '<a href="' . esc_url( $args['customize_url'] ) . '">' . esc_html__( 'theme integration', 'webcomic' ) . '</a>' );
	?>
</p>

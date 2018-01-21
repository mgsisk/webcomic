<?php
/**
 * Theme settings section
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Settings;

if ( ! isset( $args ) ) {
	return;
}

?>

<div></div>
<div class="theme-browser rendered">
	<div class="themes wp-clearfix">
			<div class="theme<?php echo esc_attr( $args['active'] ); ?>">
				<label>
					<div class="theme-screenshot"></div>
					<h2 class="theme-name"><input type="radio" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="" <?php checked( '', $args['option'] ); ?>><?php esc_html_e( 'Active Theme', 'webcomic' ); ?></h2>
					<div class="theme-actions">
						<a href="<?php echo esc_attr( wp_customize_url( get_stylesheet() ) ); ?>" class="button button-primary load-customize hide-if-no-customize"><?php esc_html_e( 'Customize', 'webcomic' ); ?></a>
					</div>
				</label>
			</div>
		<?php
		foreach ( $args['themes'] as $theme ) :
			$value  = "{$theme->template}|{$theme->stylesheet}";
			$active = '';

			if ( $value === $args['option'] ) {
				$active = ' active';
			}
		?>
			<div class="theme<?php echo esc_attr( $active ); ?>">
				<label>
					<div class="theme-screenshot"><img src="<?php echo esc_attr( $theme->get_screenshot() ); ?>" alt="<?php echo esc_attr( $theme->name ); ?>"></div>
					<h2 class="theme-name"><input type="radio" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( $value, $args['option'] ); ?>><?php echo esc_html( $theme->name ); ?></h2>
					<div class="theme-actions">
						<a href="<?php echo esc_attr( wp_customize_url( $theme->stylesheet ) ); ?>" class="button button-primary load-customize no-save hide-if-no-customize" data-stylesheet="<?php echo esc_attr( $theme->stylesheet ); ?>"><?php esc_html_e( 'Customize', 'webcomic' ); ?></a>
					</div>
				</label>
			</div>
		<?php endforeach; ?>
	</div>
</div>

<?php
/**
 * Theme settings functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Collection\Settings;

use WP_Theme;

/**
 * Add delete settings hooks.
 *
 * @return void
 */
function theme() {
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_register_theme_scripts' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_section_theme' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_delete_theme_customize_transient' );
	add_filter( 'admin_notices', __NAMESPACE__ . '\hook_inkblot_component_alert', 0 ); // NOTE For compatibility with Inkblot 4.
	add_filter( 'admin_enqueue_scripts', __NAMESPACE__ . '\hook_enqueue_theme_scripts' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_help_theme' );
	add_filter( 'switch_theme', __NAMESPACE__ . '\hook_reset_site_theme', 10, 3 );
	add_filter( 'customize_controls_print_footer_scripts', __NAMESPACE__ . '\hook_update_customizer_buttons', 99 );
	add_filter( 'wp_ajax_webcomic_customize_theme', __NAMESPACE__ . '\hook_set_theme_customize_transient' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_theme' );
	}
}

/**
 * Register theme settings javascript.
 *
 * @return void
 */
function hook_register_theme_scripts() {
	wp_register_script(
		__NAMESPACE__ . 'ThemeJS',
		plugins_url( 'srv/collection/settings-section-theme.js', webcomic( 'file' ) ),
		[],
		webcomic( 'option.version' ),
		true
	);
}

/**
 * Add the theme settings section.
 *
 * @return void
 */
function hook_add_section_theme() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_section(
			"{$collection}_theme",
			'<span class="dashicons dashicons-admin-appearance"></span> ' . esc_html__( 'Theme', 'webcomic' ),
			function() use ( $collection ) {
				$args = [
					'file'      => __DIR__ . '/theme-inc-section.php',
					'option'    => webcomic( "option.{$collection}.theme" ),
					'active'    => ' active',
					'themes'    => wp_get_themes(
						[
							'allowed' => true,
						]
					),
					'label_for' => "{$collection}[theme]",
				];

				if ( $args['option'] ) {
					$args['active'] = '';
				}

				require $args['file'];
			},
			"{$collection}_settings"
		);
	}
}

/**
 * Delete the customizer transient.
 *
 * @return void
 */
function hook_delete_theme_customize_transient() {
	if ( wp_doing_ajax() || preg_match( '/webcomic\d+/', wp_get_referer() ) ) {
		return;
	}

	delete_transient( 'webcomic_customize_theme' );
}

/**
 * Add a component alert message if Inkblot 4 is the collection's active theme.
 *
 * @return void
 */
function hook_inkblot_component_alert() {
	$match = [];

	if ( ! preg_match( '/^(webcomic\d+)_page_webcomic\d+_options$/', get_current_screen()->id, $match ) ) {
		return;
	}

	$theme = wp_get_theme();

	if ( 'inkblot' === strtolower( $theme->name ) ) {
		return;
	}

	$collection_theme = explode( '|', webcomic( "option.{$match[1]}.theme" ) );

	if ( 2 !== count( $collection_theme ) || ! in_array( 'inkblot', $collection_theme, true ) ) {
		return;
	}

	$theme = wp_get_theme( $collection_theme[0] );

	if ( 'inkblot' !== strtolower( $theme->name ) ) {
		$theme = wp_get_theme( $collection_theme[1] );
	}

	if ( 'inkblot' !== strtolower( $theme->name ) || '4' !== $theme->version[0] ) {
		return;
	}

	$missing = array_diff( [ 'character', 'collection', 'commerce', 'compat', 'restrict', 'storyline', 'transcribe' ], webcomic( 'option.components' ) );

	if ( ! $missing ) {
		return;
	}

	// Translators: 1: Theme name. 2: Theme major version. 3: Disabled plugin components.
	webcomic_notice( sprintf( __( '%1$s %2$s requires these disabled Webcomic components: %3$s', 'webcomic' ), $theme->name, $theme->version[0], ucwords( implode( ', ', $missing ) ) ), 'warning' );
}

/**
 * Enqueue theme settings javascript.
 *
 * @return void
 */
function hook_enqueue_theme_scripts() {
	if ( ! preg_match( '/^webcomic\d+_page_webcomic\d+_options$/', get_current_screen()->id ) ) {
		return;
	}

	wp_enqueue_script( __NAMESPACE__ . 'ThemeJS' );
}

/**
 * Add the theme help tab.
 *
 * @return void
 */
function hook_add_help_theme() {
	$screen = get_current_screen();

	if ( ! preg_match( '/^webcomic\d+_page_webcomic\d+_options/', $screen->id ) ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'theme',
			'title'    => __( 'Theme', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/theme-inc-help.php';
			},
		]
	);
}

/**
 * Reset the active site theme.
 *
 * @param string   $name The name of the new theme.
 * @param WP_Theme $new The new theme object.
 * @param WP_Theme $old The old theme object.
 * @return void
 */
function hook_reset_site_theme( string $name, WP_Theme $new, WP_Theme $old ) {
	$customizing = get_transient( 'webcomic_customize_theme' );

	if ( ! $customizing || $customizing !== $new->stylesheet ) {
		return;
	}

	switch_theme( $old->stylesheet );
}

/**
 * Update customizer button labels while customizing collection themes.
 *
 * @return void
 */
function hook_update_customizer_buttons() {
	$label = __( 'Save Changes', 'webcomic' );

	echo '
		<script>
			_wpCustomizeControlsL10n.save     = "' . esc_js( $label ) . '"
			_wpCustomizeControlsL10n.activate = "' . esc_js( $label ) . '"
		</script>';
}

/**
 * Set the theme customizer transient.
 *
 * @return void
 */
function hook_set_theme_customize_transient() {
	set_transient( 'webcomic_customize_theme', webcomic( 'GLOBALS._REQUEST.theme' ) );
}

/* ===== Collection Hooks =================================================== */

/**
 * Sanitize the theme field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_theme( array $options ) : array {
	$options['theme'] = implode( '|', array_map( 'sanitize_file_name', explode( '|', $options['theme'] ) ) );

	return $options;
}

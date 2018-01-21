<?php
/**
 * Commerce settings functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Commerce;

use WP_Screen;

/**
 * Add settings hooks.
 *
 * @return void
 */
function settings() {
	add_filter( 'webcomic_activate_commerce', __NAMESPACE__ . '\hook_activate' );
	add_filter( 'webcomic_deactivate_commerce', __NAMESPACE__ . '\hook_deactivate' );
	add_filter( 'webcomic_collection_allowed_options', __NAMESPACE__ . '\hook_add_allowed_options' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_settings_section' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_business' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_currency' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_donation' );
	add_filter( 'admin_init', __NAMESPACE__ . '\hook_add_field_prints' );
	add_filter( 'current_screen', __NAMESPACE__ . '\hook_enqueue_record_scripts' );
	add_filter( 'admin_head', __NAMESPACE__ . '\hook_add_help_tab' );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_business' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_currency' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_donation' );
		add_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_prints' );
	}
}

/**
 * Activate the commerce component.
 *
 * @return void
 */
function hook_activate() {
	set_transient( 'webcomic_flush_rewrite_rules', true, 1 );

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		if ( null !== webcomic( "option.{$collection}.commerce_business" ) ) {
			continue;
		}

		update_option(
			$collection, [
				'commerce_business' => '',
				'commerce_cart'     => false,
				'commerce_currency' => 'USD',
				'commerce_donation' => 0.00,
				'commerce_prints'   => [],
			]
		);
	}
}

/**
 * Deactivate the commerce component.
 *
 * @return void
 */
function hook_deactivate() {
	set_transient( 'webcomic_flush_rewrite_rules', true, 1 );

	if ( ! webcomic( 'option.uninstall' ) ) {
		return;
	}

	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_filter(
			"sanitize_option_{$collection}", function( array $options ) use ( $collection ) {
				remove_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_business' );
				remove_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_currency' );
				remove_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_donation' );
				remove_filter( "sanitize_option_{$collection}", __NAMESPACE__ . '\hook_sanitize_field_prints' );

				unset(
					$options['commerce_business'],
					$options['commerce_cart'],
					$options['commerce_currency'],
					$options['commerce_donation'],
					$options['commerce_prints']
				);

				return $options;
			}, 99
		);

		update_option(
			$collection, [
				'commerce_business' => '',
				'commerce_cart'     => false,
				'commerce_currency' => 'USD',
				'commerce_donation' => 0.00,
				'commerce_prints'   => [],
			]
		);
	}

	$table = webcomic( 'GLOBALS.wpdb' )->options;

	webcomic( 'GLOBALS.wpdb' )->query( "DELETE from {$table} where option_name like 'widget_mgsisk_webcomic_commerce_%'" );
}

/**
 * Add the commerce allowed collection options.
 *
 * @param array $allowed The allowed options.
 * @return array
 */
function hook_add_allowed_options( array $allowed ) : array {
	return array_merge( $allowed, [ 'commerce_business', 'commerce_cart', 'commerce_currency', 'commerce_donation', 'commerce_prints' ] );
}

/**
 * Add the settings section.
 *
 * @return void
 */
function hook_add_settings_section() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_section(
			"{$collection}_commerce",
			'<span class="dashicons dashicons-money"></span> ' . esc_html__( 'Commerce', 'webcomic' ),
			function() {
				echo '<div></div>';
			},
			"{$collection}_settings"
		);
	}
}

/**
 * Add the business setting field.
 *
 * @return void
 */
function hook_add_field_business() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_business",
			__( 'Business Email', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_commerce", [
				'file'      => __DIR__ . '/settings-inc-field-business.php',
				'option'    => webcomic( "option.{$collection}.commerce_business" ),
				'label_for' => "{$collection}[commerce_business]",
				'paypal'    => '<a href="https://paypal.com/" target="_blank">' . esc_html__( 'PayPal account', 'webcomic' ) . '</a>',
			]
		);
	}
}

/**
 * Add the currency setting field.
 *
 * @return void
 */
function hook_add_field_currency() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_currency",
			__( 'Currency', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_commerce", [
				'file'       => __DIR__ . '/settings-inc-field-currency.php',
				'option'     => webcomic( "option.{$collection}.commerce_currency" ),
				'label_for'  => "{$collection}[commerce_currency]",
				'currencies' => [
					'AUD' => __( 'Australian Dollar', 'webcomic' ),
					'BRL' => __( 'Brazilian Real', 'webcomic' ),
					'CAD' => __( 'Canadian Dollar', 'webcomic' ),
					'CHF' => __( 'Swiss Franc', 'webcomic' ),
					'CZK' => __( 'Czech Koruna', 'webcomic' ),
					'DKK' => __( 'Danish Krone', 'webcomic' ),
					'EUR' => __( 'Euro', 'webcomic' ),
					'GBP' => __( 'Pound Sterling', 'webcomic' ),
					'HKD' => __( 'Hong Kong Dollar', 'webcomic' ),
					'HUF' => __( 'Hungarian Forint', 'webcomic' ),
					'ILS' => __( 'Israeli New Sheqel', 'webcomic' ),
					'JPY' => __( 'Japanese Yen', 'webcomic' ),
					'MXN' => __( 'Mexican Peso', 'webcomic' ),
					'MYR' => __( 'Malaysian Ringgit', 'webcomic' ),
					'NOK' => __( 'Norwegian Krone', 'webcomic' ),
					'NZD' => __( 'New Zealand Dollar', 'webcomic' ),
					'PHP' => __( 'Philippine Peso', 'webcomic' ),
					'PLN' => __( 'Polish Zloty', 'webcomic' ),
					'RUB' => __( 'Russian Ruble', 'webcomic' ),
					'SEK' => __( 'Swedish Krona', 'webcomic' ),
					'SGD' => __( 'Singapore Dollar', 'webcomic' ),
					'THB' => __( 'Thai Baht', 'webcomic' ),
					'TWD' => __( 'Taiwan New Dollar', 'webcomic' ),
					'USD' => __( 'U.S. Dollar', 'webcomic' ),
				],
			]
		);
	} // End foreach().
}

/**
 * Add the donation setting field.
 *
 * @return void
 */
function hook_add_field_donation() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_donation",
			__( 'Donation', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_commerce", [
				'file'      => __DIR__ . '/settings-inc-field-donation.php',
				'option'    => webcomic( "option.{$collection}.commerce_donation" ),
				'label_for' => "{$collection}[commerce_donation]",
			]
		);
	}
}

/**
 * Add the prints setting field.
 *
 * @return void
 */
function hook_add_field_prints() {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		add_settings_field(
			"{$collection}_prints",
			__( 'Prints', 'webcomic' ),
			function( $args ) {
				require $args['file'];
			},
			"{$collection}_settings",
			"{$collection}_commerce", [
				'file'          => __DIR__ . '/settings-inc-field-prints.php',
				'option'        => webcomic( "option.{$collection}.commerce_cart" ),
				'option_prints' => webcomic( "option.{$collection}.commerce_prints" ),
				'label_for'     => "{$collection}[commerce_cart]",
				'label_prints'  => "{$collection}[commerce_prints]",
			]
		);
	}
}

/**
 * Enqueue the table record scripts.
 *
 * @param WP_Screen $screen The current screen.
 * @return void
 */
function hook_enqueue_record_scripts( WP_Screen $screen ) {
	if ( ! preg_match( '/^webcomic\d+_page_webcomic\d+_options$/', $screen->id ) ) {
		return;
	}

	add_filter( 'webcomic_enqueue_table_record', '__return_true' );
}

/**
 * Add the help tab.
 *
 * @return void
 */
function hook_add_help_tab() {
	$screen = get_current_screen();

	if ( ! preg_match( '/^webcomic\d+_page_webcomic\d+_options$/', $screen->id ) ) {
		return;
	}

	$screen->add_help_tab(
		[
			'id'       => 'commerce',
			'title'    => __( 'Commerce', 'webcomic' ),
			'callback' => function() {
				require __DIR__ . '/settings-inc-help.php';
			},
		]
	);
}

/* ===== Collection Hooks =================================================== */

/**
 * Sanitize the commerce_business field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_business( array $options ) : array {
	$options['commerce_business'] = sanitize_email( $options['commerce_business'] );

	return $options;
}

/**
 * Sanitize the commerce_currency field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_currency( array $options ) : array {
	$currencies = [ 'AUD', 'BRL', 'CAD', 'CHF', 'CZK', 'DKK', 'EUR', 'GBP', 'HKD', 'HUF', 'ILS', 'JPY', 'MXN', 'MYR', 'NOK', 'NZD', 'PHP', 'PLN', 'RUB', 'SEK', 'SGD', 'THB', 'TWD', 'USD' ];

	if ( ! in_array( $options['commerce_currency'], $currencies, true ) ) {
		$options['commerce_currency'] = 'USD';
	}

	return $options;
}

/**
 * Sanitize the commerce_business field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_donation( array $options ) : array {
	$options['commerce_donation'] = round( abs( (float) $options['commerce_donation'] ), 2 );

	return $options;
}

/**
 * Sanitize the commerce_prints field.
 *
 * @param array $options The submitted options.
 * @return array
 */
function hook_sanitize_field_prints( array $options ) : array {
	$options['commerce_cart'] = (bool) $options['commerce_cart'];

	if ( isset( $options['commerce_prints']['name'] ) && empty( $options['commerce_prints']['name']['name'] ) ) {
		$options['commerce_prints'] = util_get_field_prints( $options['commerce_prints'] );
	}

	$prints = [];

	foreach ( $options['commerce_prints'] as $key => $print ) {
		$name    = sanitize_text_field( $print['name'] );
		$slug    = sanitize_title( $key );
		$price   = round( abs( (float) $print['price'] ), 2 );
		$stock   = abs( (int) $print['stock'] );
		$default = (bool) $print['default'];

		if ( ! $name || ! $price ) {
			continue;
		} elseif ( ! $slug ) {
			$slug = sanitize_title( $name );
		}

		$prints[ $slug ] = [
			'name'    => $name,
			'slug'    => $slug,
			'price'   => $price,
			'stock'   => $stock,
			'default' => $default,
		];
	}

	$options['commerce_prints'] = $prints;

	ksort( $options['commerce_prints'] );

	$old_prints = array_diff( array_keys( (array) webcomic( "option.{$options['id']}.commerce_prints" ) ), array_keys( $options['commerce_prints'] ) );

	foreach ( $old_prints as $key ) {
		delete_metadata( 'post', 0, 'webcomic_commerce_prints', $key, true );
		delete_metadata( 'post', 0, "webcomic_commerce_prints_sold_{$key}", '', true );
		delete_metadata( 'post', 0, "webcomic_commerce_prints_adjust_{$key}", '', true );
	}

	return $options;
}

/* ===== Utility Functions ================================================== */

/**
 * Convert prints settings field data into the expected option structure.
 *
 * @param array $field The print settings field data to convert.
 * @return array
 * @internal For hook_sanitize_field_prints().
 */
function util_get_field_prints( array $field ) : array {
	$prints = [];

	foreach ( array_keys( $field['name'] ) as $key ) {
		$slug = $field['slug'][ $key ];

		if ( ! $slug ) {
			$slug = sanitize_title( $field['name'][ $key ] );
		}

		if ( ! empty( $field['default'][ $key + 1 ] ) ) {
			array_splice( $field['default'], $key - 1, 1 );
		}

		$prints[ $slug ] = [
			'name'    => $field['name'][ $key ],
			'slug'    => $field['slug'][ $key ],
			'price'   => $field['price'][ $key ],
			'stock'   => $field['stock'][ $key ],
			'default' => $field['default'][ $key ],
		];
	}

	return $prints;
}

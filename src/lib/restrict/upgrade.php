<?php
/**
 * Restrict upgrade functionality
 *
 * @package Webcomic
 */

namespace Mgsisk\Webcomic\Restrict;

/**
 * Add restrict referrer options.
 *
 * @param string $version The version being upgraded.
 * @return void
 */
function v5_1_0( string $version ) {
	foreach ( webcomic( 'option.collections' ) as $collection ) {
		if ( null === webcomic( "option.{$collection}.restrict_age" ) ) {
			continue;
		}

		$options                             = webcomic( "option.{$collection}" );
		$options['restrict_referrers']       = [];
		$options['restrict_referrers_media'] = 0;

		update_option( $collection, $options );
	}
}

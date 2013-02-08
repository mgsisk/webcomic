<?php
/** Default age-restricted template.
 * 
 * Strictly speaking this isn't an integration template. This
 * template loads whenever a reader reaches an age-restricted
 * webcomic they can't access and an appropriate template can't be
 * found in the current theme.
 * 
 * @package Webcomic
 * @uses verify_webcomic_age()
 */

wp_die( is_null( verify_webcomic_age() ) ? sprintf( __( 'Please verify your age to view this content:%s', 'webcomic' ), sprintf( '<form method="post"><label>%s <input type="date" name="webcomic_birthday"></label><input type="submit"></form>', __( 'Birthday', 'webcomic' ) ) ) : __( "You don't have permission to view this content.", 'webcomic' ), __( 'Restricted Content | Webcomic', 'webcomic' ), 401 );
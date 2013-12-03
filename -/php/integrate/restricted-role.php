<?php
/**
 * Default role-restricted template.
 * 
 * Strictly speaking this isn't an integration template. This
 * template loads whenever a reader reaches a role-restricted
 * webcomic they can't access and an appropriate template can't be
 * found in the current theme.
 * 
 * @package Webcomic
 */

wp_die( is_user_logged_in() ? __( "You don't have permission to view this content.", "webcomic" ) : sprintf( __( "Please <a href='%s'>log in</a> to view this content.", "webcomic" ), wp_login_url( $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "REQUEST_URI" ] ) ), __( "Restricted Content | Webcomic", "webcomic" ), 401 );
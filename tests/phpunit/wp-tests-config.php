<?php
/**
 * WordPress configuration for unit testing
 *
 * @package Webcomic
 */

define( 'ABSPATH', dirname( __DIR__, 2 ) . '/vendor/johnpbloch/wordpress-core/' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_HOST', 'local.test' );
define( 'DB_NAME', 'wp' );
define( 'DB_PASSWORD', 'root' );
define( 'DB_USER', 'root' );
define( 'WP_DEBUG', true );
define( 'WP_DEFAULT_THEME', 'default' );
define( 'WP_PHP_BINARY', 'php' );
define( 'WP_TESTS_DOMAIN', 'local.test' );
define( 'WP_TESTS_EMAIL', 'admin@local.test' );
define( 'WP_TESTS_TITLE', 'local.test' );

$table_prefix = 'wptests_';

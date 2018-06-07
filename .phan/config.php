<?php
/**
 * Phan configuration
 *
 * @package Webcomic
 */

return [
	'directory_list'                  => [
		'src',
    'docs',
		'tests',
		'vendor/johnpbloch/wordpress-core',
		'vendor/phpunit/phpunit',
	],
	'exclude_analysis_directory_list' => [
		'vendor/',
		'tests/phpunit/data/',
		'tests/phpunit/includes/',
	],
	'plugins'                         => [
		'.phan/plugins/AlwaysReturnPlugin.php',
		'.phan/plugins/DollarDollarPlugin.php',
	],
	'enable_class_alias_support'      => true,
	'skip_slow_php_options_warning'   => true,
];

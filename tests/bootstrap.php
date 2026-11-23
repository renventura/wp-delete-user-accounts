<?php
/**
 * PHPUnit bootstrap: loads the WordPress test suite with this plugin active.
 *
 * @package WP Delete User Accounts
 */

$root = dirname( __DIR__ );

require $root . '/vendor/autoload.php';

putenv( 'WP_PHPUNIT__TESTS_CONFIG=' . __DIR__ . '/wp-tests-config.php' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_putenv -- Read by wp-phpunit.

$tests_dir = getenv( 'WP_PHPUNIT__DIR' ) ? getenv( 'WP_PHPUNIT__DIR' ) : $root . '/vendor/wp-phpunit/wp-phpunit';

require $tests_dir . '/includes/functions.php';

tests_add_filter(
	'muplugins_loaded',
	static function () use ( $root ) {
		require $root . '/wp-delete-user-accounts.php';
	}
);

require $tests_dir . '/includes/bootstrap.php';

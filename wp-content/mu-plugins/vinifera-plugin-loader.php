<?php
/**
 * Plugin Name: Vinifera Plugin Loader
 * Description: Loads Vinifera support plugins and Contact Form 7 as must-use plugins.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$plugins = [
	WP_PLUGIN_DIR . '/contact-form-7/wp-contact-form-7.php',
	WP_PLUGIN_DIR . '/vinifera-cf7-bootstrap/vinifera-cf7-bootstrap.php',
	WP_PLUGIN_DIR . '/vinifera-map-block/vinifera-map-block.php',
];

foreach ( $plugins as $plugin_file ) {
	if ( file_exists( $plugin_file ) ) {
		require_once $plugin_file;
	}
}

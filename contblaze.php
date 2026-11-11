<?php
/**
 * ContBlaze — The AI Engine for Content Growth.
 *
 * @package           ContBlaze
 * @author            Dulal Shikdar
 * @copyright         2026 Dulal Shikdar
 * @license           Proprietary
 *
 * @wordpress-plugin
 * Plugin Name:       ContBlaze
 * Plugin URI:        https://contblaze.com
 * Description:       The AI Engine for Content Growth — AI-powered content creation, optimization, and management inside WordPress.
 * Version:           0.1.0
 * Requires at least: 6.4
 * Requires PHP:      8.3
 * Author:            Dulal Shikdar
 * Author URI:        https://contblaze.com
 * Text Domain:       contblaze
 * Domain Path:       /languages
 */

namespace ContBlaze;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * Compatibility gate — plain PHP, before any namespaced code loads.
 * PHP 8.3 syntax would fatal on older runtimes, so bail with a notice instead.
 */
if ( version_compare( PHP_VERSION, '8.3.0', '<' ) || version_compare( get_bloginfo( 'version' ), '6.4', '<' ) ) {
	add_action(
		'admin_notices',
		static function () {
			printf(
				'<div class="notice notice-error"><p>%s</p></div>',
				esc_html__( 'ContBlaze requires PHP 8.3+ and WordPress 6.4+. The plugin has been deactivated.', 'contblaze' )
			);
		}
	);
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	deactivate_plugins( plugin_basename( __FILE__ ), false, is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) );
	return;
}

define( 'CONTBLAZE_VERSION', '0.1.0' );
define( 'CONTBLAZE_FILE', __FILE__ );
define( 'CONTBLAZE_DIR', plugin_dir_path( __FILE__ ) );
define( 'CONTBLAZE_URL', plugin_dir_url( __FILE__ ) );
define( 'CONTBLAZE_MIN_PHP', '8.3' );
define( 'CONTBLAZE_MIN_WP', '6.4' );

require_once CONTBLAZE_DIR . 'inc/class-autoloader.php';
Autoloader::register();

/**
 * Boot the plugin once all plugins are loaded, so modules can hook in.
 */
add_action(
	'plugins_loaded',
	static function () {
		Plugin::instance()->init();
	}
);

/**
 * Activation: store version, set defaults, flush nothing yet (no rewrite rules used).
 */
register_activation_hook(
	__FILE__,
	static function () {
		Plugin::activate();
	}
);

/**
 * Deactivation: clean transient state. No data is removed — reactivation is safe.
 */
register_deactivation_hook(
	__FILE__,
	static function () {
		Plugin::deactivate();
	}
);

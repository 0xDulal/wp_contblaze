<?php
/**
 * ContBlaze core plugin class: instance management, init, activation, deactivation.
 *
 * @package ContBlaze
 */

namespace ContBlaze;

defined( 'ABSPATH' ) || exit;

/**
 * Core plugin singleton.
 */
final class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	private static ?Plugin $instance = null;

	/**
	 * Admin controller.
	 *
	 * @var Admin|null
	 */
	private ?Admin $admin = null;

	/**
	 * Get the singleton instance.
	 */
	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Wire up hooks. Called once on plugins_loaded; idempotent.
	 */
	public function init(): void {
		if ( null !== $this->admin ) {
			return; // Already initialized — never fire contblaze_init twice.
		}

		$this->load_textdomain();

		$this->admin = new Admin();
		$this->admin->register();

		/**
		 * Fires after ContBlaze core has initialized. Modules hook here to
		 * self-register (assets, admin pages, REST routes).
		 *
		 * @param Plugin $plugin Core plugin instance.
		 */
		do_action( 'contblaze_init', $this );
	}

	/**
	 * Load the plugin text domain.
	 */
	private function load_textdomain(): void {
		load_plugin_textdomain( 'contblaze', false, dirname( plugin_basename( CONTBLAZE_FILE ) ) . '/languages' );
	}

	/**
	 * Activation: record version. Future upgrade routines compare this
	 * against CONTBLAZE_VERSION on plugins_loaded.
	 */
	public static function activate(): void {
		if ( ! get_option( 'contblaze_version' ) ) {
			add_option( 'contblaze_version', CONTBLAZE_VERSION );
		} else {
			update_option( 'contblaze_version', CONTBLAZE_VERSION );
		}

		/**
		 * Fires on plugin activation, after the version option is stored.
		 */
		do_action( 'contblaze_activated' );
	}

	/**
	 * Deactivation: clear transient/cache state only. Stored data (options,
	 * post meta, tables) is kept so reactivation is lossless.
	 */
	public static function deactivate(): void {
		delete_transient( 'contblaze_cache_version' );

		/**
		 * Fires on plugin deactivation.
		 */
		do_action( 'contblaze_deactivated' );
	}
}

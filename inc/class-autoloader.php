<?php
/**
 * ContBlaze PSR-4-style autoloader.
 *
 * Maps the ContBlaze\ namespace to /inc/ and /modules/.
 * Expected paths: ContBlaze\Plugin            -> inc/class-plugin.php
 *                 ContBlaze\Admin             -> inc/class-admin.php
 *                 ContBlaze\Modules\Writer\Writer -> modules/writer/class-writer.php
 *
 * Deliberately dependency-free: no Composer. Lazy-loaded per class.
 * ponytail: if a future module needs deep sub-namespaces, prefer Composer's
 * ClassLoader over growing this class.
 *
 * @package ContBlaze
 */

namespace ContBlaze;

defined( 'ABSPATH' ) || exit;

/**
 * PSR-4-style autoloader for ContBlaze classes.
 */
final class Autoloader {

	/**
	 * Register the autoloader with SPL.
	 */
	public static function register(): void {
		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	/**
	 * Load a ContBlaze\ class from its namespace path.
	 *
	 * Modules do not implement this yet, but the mapping is in place so the
	 * ten planned modules can self-register without touching the loader.
	 *
	 * @param string $class Fully-qualified class name.
	 */
	public static function autoload( string $class ): void {
		if ( ! str_starts_with( $class, __NAMESPACE__ . '\\' ) ) {
			return;
		}

		$path = substr( $class, strlen( __NAMESPACE__ ) + 1 ); // Strip "ContBlaze\".

		// Module classes (ContBlaze\Modules\<Module>\...): modules/<module>/class-<name>.php.
		if ( str_starts_with( $path, 'Modules\\' ) ) {
			$path = substr( $path, strlen( 'Modules\\' ) ); // Strip the "Modules\" prefix.
			$parts   = explode( '\\', strtolower( $path ) );
			$name    = array_pop( $parts );
			$dir     = implode( '/', $parts );
			$file    = CONTBLAZE_DIR . 'modules/' . ( $dir ? $dir . '/' : '' ) . 'class-' . $name . '.php';
		} else {
			// Core classes: inc/class-<name>.php.
			$file = CONTBLAZE_DIR . 'inc/class-' . strtolower( $path ) . '.php';
		}

		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}
}

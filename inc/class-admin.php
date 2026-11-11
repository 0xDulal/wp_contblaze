<?php
/**
 * ContBlaze admin: menu registration and dashboard placeholder page.
 *
 * @package ContBlaze
 */

namespace ContBlaze;

defined( 'ABSPATH' ) || exit;

/**
 * Admin menu and dashboard.
 */
final class Admin {

	/**
	 * Capability required for all ContBlaze admin screens.
	 *
	 * @var string
	 */
	public const CAPABILITY = 'manage_options';

	/**
	 * Register admin hooks.
	 */
	public function register(): void {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Register the top-level ContBlaze menu and the dashboard subpage.
	 */
	public function register_menu(): void {
		add_menu_page(
			__( 'ContBlaze', 'contblaze' ),
			__( 'ContBlaze', 'contblaze' ),
			self::CAPABILITY,
			'contblaze',
			array( $this, 'render_dashboard' ),
			'dashicons-superhero-alt',
			58
		);

		add_submenu_page(
			'contblaze',
			__( 'Dashboard', 'contblaze' ),
			__( 'Dashboard', 'contblaze' ),
			self::CAPABILITY,
			'contblaze',
			array( $this, 'render_dashboard' ) // Reuses the top-level slug: no duplicate menu entry.
		);
	}

	/**
	 * Enqueue admin styles on ContBlaze screens only.
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 */
	public function enqueue_assets( string $hook_suffix ): void {
		if ( ! str_contains( $hook_suffix, 'contblaze' ) ) {
			return;
		}

		wp_enqueue_style(
			'contblaze-admin',
			CONTBLAZE_URL . 'assets/css/admin.css',
			array(),
			CONTBLAZE_VERSION
		);
	}

	/**
	 * Render the dashboard placeholder.
	 *
	 * Capability is enforced here as defense-in-depth: WordPress already gates
	 * page access via add_menu_page(), but the render callback must not trust that.
	 */
	public function render_dashboard(): void {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'contblaze' ), 403 );
		}

		$modules = array(
			__( 'AI Writer', 'contblaze' ),
			__( 'SEO Assistant', 'contblaze' ),
			__( 'Content Planner', 'contblaze' ),
			__( 'AI Repurposing', 'contblaze' ),
			__( 'Auto Publisher', 'contblaze' ),
			__( 'Bulk Generator', 'contblaze' ),
			__( 'Content Refresh', 'contblaze' ),
			__( 'Brand Voice', 'contblaze' ),
			__( 'Content Workflow', 'contblaze' ),
			__( 'Content Analytics', 'contblaze' ),
		);
		?>
		<div class="wrap contblaze-dashboard">
			<h1><?php esc_html_e( 'ContBlaze', 'contblaze' ); ?></h1>
			<p class="contblaze-tagline"><?php esc_html_e( 'The AI Engine for Content Growth.', 'contblaze' ); ?></p>

			<div class="notice notice-info inline">
				<p>
					<?php
					printf(
						/* translators: 1: plugin version, 2: status */
						esc_html__( 'Version %1$s — Status: %2$s', 'contblaze' ),
						esc_html( CONTBLAZE_VERSION ),
						esc_html__( 'Environment setup complete. Modules coming soon.', 'contblaze' )
					);
					?>
				</p>
			</div>

			<h2><?php esc_html_e( 'Planned Modules', 'contblaze' ); ?></h2>
			<div class="contblaze-module-grid">
				<?php foreach ( $modules as $module ) : ?>
					<div class="contblaze-module-card">
						<h3><?php echo esc_html( $module ); ?></h3>
						<p><?php esc_html_e( 'Coming soon.', 'contblaze' ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}

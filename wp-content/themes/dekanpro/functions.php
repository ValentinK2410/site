<?php //phpcs:ignore
/**
 * Theme functions and definitions.
 *
 * @package DekanPro
 * @author DekanPro
 * @since   1.0.0
 */

/**
 * Main Dekanpro class.
 *
 * @since 1.0.0
 */
final class Dekanpro {

	/**
	 * Theme options
	 *
	 * @since 1.0.0
	 * @var object
	 */
	public $options;

	/**
	 * Theme fonts
	 *
	 * @since 1.0.0
	 * @var object
	 */
	public $fonts;

	/**
	 * Theme icons
	 *
	 * @since 1.0.0
	 * @var object
	 */
	public $icons;

	/**
	 * Theme customizer
	 *
	 * @since 1.0.0
	 * @var object
	 */
	public $customizer;

	/**
	 * Theme admin
	 *
	 * @since 1.0.0
	 * @var object
	 */
	public $admin;

	/**
	 * Singleton instance of the class.
	 *
	 * @since 1.0.0
	 * @var object
	 */
	private static $instance;
	/**
	 * Theme version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $version = '1.0.27';
	/**
	 * Main Dekanpro Instance.
	 *
	 * Insures that only one instance of Dekanpro exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since 1.0.0
	 * @return Dekanpro
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Dekanpro ) ) {
			self::$instance = new Dekanpro();
			self::$instance->constants();
			self::$instance->includes();
			self::$instance->objects();
			// Hook now that all of the Dekanpro stuff is loaded.
			do_action( 'dekanpro_loaded' );
		}
		return self::$instance;
	}

	/**
	 * Setup constants.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function constants() {
		if ( ! defined( 'DEKANPRO_THEME_VERSION' ) ) {
			define( 'DEKANPRO_THEME_VERSION', $this->version );
		}
		if ( ! defined( 'DEKANPRO_THEME_URI' ) ) {
			define( 'DEKANPRO_THEME_URI', get_parent_theme_file_uri() );
		}
		if ( ! defined( 'DEKANPRO_THEME_PATH' ) ) {
			define( 'DEKANPRO_THEME_PATH', get_parent_theme_file_path() );
		}
	}
	/**
	 * Include files.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function includes() {
		require_once DEKANPRO_THEME_PATH . '/inc/common.php';
		require_once DEKANPRO_THEME_PATH . '/inc/helpers.php';
		require_once DEKANPRO_THEME_PATH . '/inc/widgets.php';
		require_once DEKANPRO_THEME_PATH . '/inc/template-tags.php';
		require_once DEKANPRO_THEME_PATH . '/inc/template-parts.php';
		require_once DEKANPRO_THEME_PATH . '/inc/icon-functions.php';
		require_once DEKANPRO_THEME_PATH . '/inc/breadcrumbs.php';
		require_once DEKANPRO_THEME_PATH . '/inc/class-dekanpro-dynamic-styles.php';
		// Core.
		require_once DEKANPRO_THEME_PATH . '/inc/core/class-dekanpro-options.php';
		require_once DEKANPRO_THEME_PATH . '/inc/core/class-dekanpro-enqueue-scripts.php';
		require_once DEKANPRO_THEME_PATH . '/inc/core/class-dekanpro-fonts.php';
		require_once DEKANPRO_THEME_PATH . '/inc/core/class-dekanpro-theme-setup.php';
		// Compatibility.
		require_once DEKANPRO_THEME_PATH . '/inc/compatibility/woocommerce/class-dekanpro-woocommerce.php';
		require_once DEKANPRO_THEME_PATH . '/inc/compatibility/socialsnap/class-dekanpro-socialsnap.php';
		require_once DEKANPRO_THEME_PATH . '/inc/compatibility/class-dekanpro-wpforms.php';
		require_once DEKANPRO_THEME_PATH . '/inc/compatibility/class-dekanpro-jetpack.php';
		require_once DEKANPRO_THEME_PATH . '/inc/compatibility/class-dekanpro-beaver-themer.php';
		require_once DEKANPRO_THEME_PATH . '/inc/compatibility/class-dekanpro-elementor.php';
		require_once DEKANPRO_THEME_PATH . '/inc/compatibility/class-dekanpro-elementor-pro.php';
		require_once DEKANPRO_THEME_PATH . '/inc/compatibility/class-dekanpro-hfe.php';

		if ( is_admin() ) {
			require_once DEKANPRO_THEME_PATH . '/inc/utilities/class-dekanpro-plugin-utilities.php';
			require_once DEKANPRO_THEME_PATH . '/inc/admin/class-dekanpro-admin.php';

		}
		new Dekanpro_Enqueue_Scripts();
		// Customizer.
		require_once DEKANPRO_THEME_PATH . '/inc/customizer/class-dekanpro-customizer.php';
		require_once DEKANPRO_THEME_PATH . '/inc/customizer/customizer-callbacks.php';
		require_once DEKANPRO_THEME_PATH . '/inc/customizer/class-dekanpro-section-ordering.php';
	}
	/**
	 * Setup objects to be used throughout the theme.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function objects() {

		dekanpro()->options    = new Dekanpro_Options();
		dekanpro()->fonts      = new Dekanpro_Fonts();
		dekanpro()->icons      = new Dekanpro_Icons();
		dekanpro()->customizer = new Dekanpro_Customizer();
		if ( is_admin() ) {
			dekanpro()->admin = new Dekanpro_Admin();
		}
	}
}

/**
 * The function which returns the one Dekanpro instance.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $dekanpro = dekanpro(); ?>
 *
 * @since 1.0.0
 * @return object
 */
function dekanpro() {
	return Dekanpro::instance();
}

dekanpro();


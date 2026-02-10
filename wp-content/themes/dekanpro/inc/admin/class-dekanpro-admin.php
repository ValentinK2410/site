<?php
/**
 * Admin class.
 *
 * This class ties together all admin classes.
 *
 * @package     Dekanpro
 * @author      DekanPro
 * @since       1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Dekanpro_Admin' ) ) :

	/**
	 * Admin Class
	 */
	class Dekanpro_Admin {

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			/**
			 * Include admin files.
			 */
			$this->includes();

			/**
			 * Load admin assets.
			 */
			add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );

			/**
			 * Add filters for WordPress header and footer text.
			 */
			add_filter( 'update_footer', array( $this, 'filter_update_footer' ), 50 );
			add_filter( 'admin_footer_text', array( $this, 'filter_admin_footer_text' ), 50 );

			/**
			 * Admin page header.
			 */
			add_action( 'in_admin_header', array( $this, 'admin_header' ), 100 );

			/**
			 * Admin page footer.
			 */
			add_action( 'in_admin_footer', array( $this, 'admin_footer' ), 100 );

			/**
			 * Add notices.
			 */
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );

			/**
			 * After admin loaded
			 */
			do_action( 'dekanpro_admin_loaded' );
		}

		/**
		 * Includes files.
		 *
		 * @since 1.0.0
		 */
		private function includes() {

			/**
			 * Include helper functions.
			 */
			require_once DEKANPRO_THEME_PATH . '/inc/admin/helpers.php'; // phpcs:ignore

			/**
			 * Include Dekanpro welcome page.
			 */
			require_once DEKANPRO_THEME_PATH . '/inc/admin/class-dekanpro-dashboard.php'; // phpcs:ignore

			/**
			 * Guten block
			 */
			// require_once DEKANPRO_THEME_PATH . '/inc/admin/guten-block.php'; // phpcs:ignore

			/**
			 * Include Dekanpro meta boxes.
			 */
			require_once DEKANPRO_THEME_PATH . '/inc/admin/metabox/class-dekanpro-meta-boxes.php'; // phpcs:ignore

		}

		/**
		 * Load our required assets on admin pages.
		 *
		 * @since 1.0.0
		 * @param string $hook it holds the information about the current page.
		 */
		public function load_assets( $hook ) {
			/**
			 * Do not enqueue if we are not on one of our pages.
			 */
			if ( ! dekanpro_is_admin_page( $hook ) ) {
				return;
			}

			// Script debug.
			$prefix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'dev/' : '';
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			/**
			 * Enqueue admin pages stylesheet.
			 */
			wp_enqueue_style(
				'dekanpro-admin-styles',
				DEKANPRO_THEME_URI . '/inc/admin/assets/css/dekanpro-admin' . $suffix . '.css',
				false,
				DEKANPRO_THEME_VERSION
			);

			/**
			 * Enqueue admin pages script.
			 */
			wp_enqueue_script(
				'dekanpro-admin-script',
				DEKANPRO_THEME_URI . '/inc/admin/assets/js/' . $prefix . 'dekanpro-admin' . $suffix . '.js',
				array( 'jquery', 'wp-util', 'updates' ),
				DEKANPRO_THEME_VERSION,
				true
			);

			/**
			 * Localize admin strings.
			 */
			$texts = array(
				'install'               => esc_html__( 'Install', 'dekanpro' ),
				'install-inprogress'    => esc_html__( 'Installing...', 'dekanpro' ),
				'activate-inprogress'   => esc_html__( 'Activating...', 'dekanpro' ),
				'deactivate-inprogress' => esc_html__( 'Deactivating...', 'dekanpro' ),
				'active'                => esc_html__( 'Active', 'dekanpro' ),
				'retry'                 => esc_html__( 'Retry', 'dekanpro' ),
				'please_wait'           => esc_html__( 'Please Wait...', 'dekanpro' ),
				'importing'             => esc_html__( 'Importing... Please Wait...', 'dekanpro' ),
				'currently_processing'  => esc_html__( 'Currently processing: ', 'dekanpro' ),
				'import'                => esc_html__( 'Import', 'dekanpro' ),
				'import_demo'           => esc_html__( 'Import Demo', 'dekanpro' ),
				'importing_notice'      => esc_html__( 'The demo importer is still working. Closing this window may result in failed import.', 'dekanpro' ),
				'import_complete'       => esc_html__( 'Import Complete!', 'dekanpro' ),
				'import_complete_desc'  => esc_html__( 'The demo has been imported.', 'dekanpro' ) . ' <a href="' . esc_url( get_home_url() ) . '">' . esc_html__( 'Visit site.', 'dekanpro' ) . '</a>',
			);

			$strings = array(
				'ajaxurl'       => admin_url( 'admin-ajax.php' ),
				'wpnonce'       => wp_create_nonce( 'dekanpro_nonce' ),
				'texts'         => $texts,
				'color_pallete' => array( '#F43676', '#06cca6', '#2c2e3a', '#e4e7ec', '#f0b849', '#ffffff', '#000000' ),
			);

			$strings = apply_filters( 'dekanpro_admin_strings', $strings );

			wp_localize_script( 'dekanpro-admin-script', 'hester_strings', $strings );
		}

		/**
		 * Filters WordPress footer right text to hide all text.
		 *
		 * @since 1.0.0
		 * @param string $text Text that we're going to replace.
		 */
		public function filter_update_footer( $text ) {

			$base = get_current_screen()->base;

			/**
			 * Only do this if we are on one of our plugin pages.
			 */
			if ( dekanpro_is_admin_page( $base ) ) {
				return apply_filters( 'dekanpro_footer_version', esc_html__( 'DekanPro Theme', 'dekanpro' ) . ' ' . DEKANPRO_THEME_VERSION );
			} else {
				return $text;
			}
		}

		/**
		 * Filter WordPress footer left text to display our text.
		 *
		 * @since 1.0.0
		 * @param string $text Text that we're going to replace.
		 */
		public function filter_admin_footer_text( $text ) {

			if ( dekanpro_is_admin_page() ) {
				return;
			}

			return $text;
		}

		/**
		 * Outputs the page admin header.
		 *
		 * @since 1.0.0
		 */
		public function admin_header() {

			$base = get_current_screen()->base;

			if ( ! dekanpro_is_admin_page( $base ) ) {
				return;
			}
			?>

			<div id="hester-header">
				<div class="hester-container">

					<a href="<?php echo esc_url( admin_url( 'admin.php?page=dekanpro-dashboard' ) ); ?>" class="hester-logo">
						<img src="<?php echo esc_url( DEKANPRO_THEME_URI . '/assets/images/dekanpro-logo.svg' ); ?>" alt="<?php echo esc_html( 'Dekanpro' ); ?>" />
					</a>

					<span class="hester-header-action">
						<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Customize', 'dekanpro' ); ?></a>
						<a href="<?php echo esc_url( 'https://dekan.pro/' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Сайт', 'dekanpro' ); ?></a>
					</span>

				</div>
			</div><!-- END #hester-header -->
			<?php
		}

		/**
		 * Outputs the page admin footer.
		 *
		 * @since 1.0.0
		 */
		public function admin_footer() {

			$base = get_current_screen()->base;

			if ( ! dekanpro_is_admin_page( $base ) || dekanpro_is_admin_page( $base, 'hester_wizard' ) ) {
				return;
			}
			?>
			<div id="hester-footer">
			<ul>
				<li><a href="<?php echo esc_url( 'https://dekan.pro/' ); ?>" target="_blank" rel="noopener noreferrer"><span><?php esc_html_e( 'Сайт DekanPro', 'dekanpro' ); ?></span></span></a></li>
				<li><a href="<?php echo esc_url( 'https://wordpress.org/support/theme/dekanpro/reviews/#new-post' ); ?>" target="_blank" rel="noopener noreferrer"><span class="dashicons dashicons-heart" aria-hidden="true"></span><span><?php esc_html_e( 'Leave a Review', 'dekanpro' ); ?></span></a></li>
			</ul>
			</div><!-- END #hester-footer -->

			<?php
		}

		/**
		 * Admin Notices
		 *
		 * @since 1.0.0
		 */
		public function admin_notices() {

			$screen = get_current_screen();

			// Display on Dashboard, Themes and Dekanpro admin pages.
			if ( ! in_array( $screen->base, array( 'dashboard', 'themes' ), true ) && ! dekanpro_is_admin_page() ) {
				return;
			}

			// Display if not dismissed and not on Dekanpro plugins page.
			if ( ! dekanpro_is_notice_dismissed( 'dekanpro_notice_recommended-plugins' ) && ! dekanpro_is_admin_page( false, 'dekanpro-plugins' ) ) {

				$plugins = dekanpro_plugin_utilities()->get_recommended_plugins();
				$plugins = dekanpro_plugin_utilities()->get_deactivated_plugins( $plugins );

				$plugin_list = '';

				if ( is_array( $plugins ) && ! empty( $plugins ) ) {

					foreach ( $plugins as $slug => $plugin ) {

						$url = admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . esc_attr( $slug ) . '&TB_iframe=true&width=990&height=500' );

						$plugin_list .= '<a href="' . esc_url( $url ) . '" class="thickbox">' . esc_html( $plugin['name'] ) . '</a>, ';
					}

					wp_enqueue_script( 'plugin-install' );
					add_thickbox();

					$plugin_list = trim( $plugin_list, ', ' );

					/* translators: %1$s <strong> tag, %2$s </strong> tag */
					$message = sprintf( wp_kses( __( 'DekanPro theme recommends the following plugins: %1$s.', 'dekanpro' ), dekanpro_get_allowed_html_tags() ), $plugin_list );

					$navigation_items = dekanpro_dashboard()->get_navigation_items();

					dekanpro_print_notice(
						array(
							'type'        => 'info',
							'message'     => $message,
							'message_id'  => 'recommended-plugins',
							'expires'     => 7 * 24 * 60 * 60,
							'action_link' => $navigation_items['plugins']['url'],
							'action_text' => esc_html__( 'Install Now', 'dekanpro' ),
						)
					);
				}
			}

		}
	}
endif;

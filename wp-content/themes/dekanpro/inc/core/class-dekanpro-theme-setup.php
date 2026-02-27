<?php
/**
 * DekanPro Theme Setup Class.
 *
 * @package  Dekanpro
 * @author   DekanPro
 * @since    1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Dekanpro_Theme_Setup' ) ) :

	/**
	 * Dekanpro Options Class.
	 */
	class Dekanpro_Theme_Setup {

		/**
		 * Singleton instance of the class.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private static $instance;

		/**
		 * Main Dekanpro_Theme_Setup Instance.
		 *
		 * @since 1.0.0
		 * @return Dekanpro_Theme_Setup
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Dekanpro_Theme_Setup ) ) {
				self::$instance = new Dekanpro_Theme_Setup();
			}
			return self::$instance;
		}

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Add theme supports.
			add_action( 'after_setup_theme', array( $this, 'setup' ), 10 );

			// Content width.
			add_action( 'wp', array( $this, 'content_width' ) );

			// Убираем сообщение об ошибке Interactivity API из вывода.
			add_action( 'template_redirect', array( $this, 'strip_directive_error' ), 0 );
			add_action( 'wp_footer', array( $this, 'strip_directive_error_js' ), 999 );
		}

		/**
		 * Sets up theme defaults and registers support for various WordPress features.
		 *
		 * Note that this function is hooked into the after_setup_theme hook, which
		 * runs before the init hook. The init hook is too late for some features, such
		 * as indicating support for post thumbnails.
		 *
		 * @since 1.0.0
		 */
		public function setup() {

			// Make the theme available for translation.
			load_theme_textdomain( 'dekanpro', DEKANPRO_THEME_PATH . '/languages' );

			// Add default posts and comments RSS feed links to head.
			add_theme_support( 'automatic-feed-links' );

			// Add theme support for Post Thumbnails and image sizes.
			add_theme_support( 'post-thumbnails' );

			// Add theme support for various Post Formats.
			add_theme_support(
				'post-formats',
				array(
					'gallery',
					'image',
					'link',
					'quote',
					'video',
					'audio',
					'status',
					'aside',
				)
			);

			// Add title output.
			add_theme_support( 'title-tag' );

			// Add wide image support.
			add_theme_support( 'align-wide' );

			// Responsive embeds support.
			add_theme_support( 'responsive-embeds' );

			// Add support for core block visual styles.
			add_theme_support( 'wp-block-styles' );

			// add custom spacing support.
			add_theme_support( 'custom-spacing' );

			// Selective Refresh for Customizer.
			add_theme_support( 'customize-selective-refresh-widgets' );

			// Excerpt support for pages.
			add_post_type_support( 'page', 'excerpt' );

			// Register Navigation menu.
			register_nav_menus(
				array(
					'dekanpro-primary' => esc_html__( 'Primary Navigation', 'dekanpro' ),
					'primary'          => esc_html__( 'Main Menu (primary)', 'dekanpro' ),
				)
			);

			// Add theme support for Custom Logo.
			add_theme_support(
				'custom-logo',
				apply_filters(
					'dekanpro_custom_logo_args',
					array(
						'width'       => 200,
						'height'      => 40,
						'flex-height' => true,
						'flex-width'  => true,
					)
				)
			);

			// Add theme support for Custom Background.
			add_theme_support(
				'custom-background',
				apply_filters(
					'dekanpro_custom_background_args',
					array(
						'default-color' => '#F9F9FF',
						'default-size'  => 'fit',
					)
				)
			);

			// Enable HTML5 markup.
			add_theme_support(
				'html5',
				array(
					'search-form',
					'gallery',
					'caption',
					'script',
					'style',
				)
			);

			add_theme_support(
				'custom-header',
				apply_filters(
					'dekanpro_custom_header_args',
					array(
						'default-image' => '',
						'width'         => 1920,
						'height'        => 250,
						'flex-height'   => true,
						'header-text'   => false,
					)
				)
			);

			add_theme_support(
				'starter-content',
				array(
					'widgets' => array(
						'dekanpro-footer-1' => array(
							'search',
						),
						'dekanpro-footer-2' => array(
							'categories',
						),
						'dekanpro-footer-3' => array(
							'archives',
						),
						'dekanpro-footer-4' => array(
							'meta',
						),
					),
				)
			);

			// Add editor style.
			$dekanpro_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			add_editor_style( 'assets/css/editor-style' . $dekanpro_suffix . '.css' );

			do_action( 'dekanpro_after_setup_theme' );
		}

		/**
		 * Set the content width in pixels, based on the theme's design and stylesheet.
		 *
		 * @global int $content_width
		 * @since 1.0.0
		 */
		public function content_width() {
			global $content_width;

			if ( ! isset( $content_width ) ) {
				$content_width = apply_filters( 'dekanpro_content_width', intval( dekanpro_option( 'container_width' ) ) - 100 ); // phpcs:ignore
			}
		}

		/**
		 * Убирает сообщение об ошибке Interactivity API из HTML-вывода.
		 *
		 * @since 1.0.0
		 */
		public function strip_directive_error() {
			ob_start(
				function ( $html ) {
					return str_replace( '[an error occurred while processing the directive]', '', $html );
				}
			);
		}

		/**
		 * Убирает сообщение об ошибке Interactivity API через JS (на случай клиентского рендеринга).
		 *
		 * @since 1.0.0
		 */
		public function strip_directive_error_js() {
			?>
			<script>
			(function(){
				var walk = function(node) {
					if (node.nodeType === 3) {
						if (node.textContent.indexOf('an error occurred while processing the directive') !== -1) {
							node.textContent = node.textContent.replace(/\[?an error occurred while processing the directive\]?/g, '');
						}
						return;
					}
					for (var i = 0; i < node.childNodes.length; i++) {
						walk(node.childNodes[i]);
					}
				};
				if (document.body) walk(document.body);
				document.addEventListener('DOMContentLoaded', function() { walk(document.body); });
			})();
			</script>
			<?php
		}
	}

endif;

/**
 * The function which returns the one Dekanpro_Options instance.
 *
 * @since 1.0.0
 * @return object
 */
function dekanpro_theme_setup() {
	return Dekanpro_Theme_Setup::instance();
}

dekanpro_theme_setup();

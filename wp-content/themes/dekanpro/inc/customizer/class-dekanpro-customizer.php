<?php
/**
 * Dekanpro Customizer class
 *
 * @package     Dekanpro
 * @author      Peregrine Themes
 * @since       1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Dekanpro_Customizer' ) ) :
	/**
	 * Dekanpro Customizer class
	 */
	class Dekanpro_Customizer {

		/**
		 * Singleton instance of the class.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private static $instance;

		/**
		 * Customizer options.
		 *
		 * @since 1.0.0
		 * @var Array
		 */
		private static $options;

		/**
		 * Main Dekanpro_Customizer Instance.
		 *
		 * @since 1.0.0
		 * @return Dekanpro_Customizer
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Dekanpro_Customizer ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Loads our Customizer custom controls.
			add_action( 'customize_register', array( $this, 'load_custom_controls' ) );

			// Loads our Customizer helper functions.
			add_action( 'customize_register', array( $this, 'load_customizer_helpers' ) );

			// Loads our Customizer widgets classes.
			add_action( 'customize_register', array( $this, 'load_customizer_widgets' ) );

			// Tweak inbuilt sections.
			add_action( 'customize_register', array( $this, 'customizer_tweak' ), 11 );

			// Registers our Customizer options.
			add_action( 'after_setup_theme', array( $this, 'register_options' ) );

			// Registers our Customizer options.
			add_action( 'customize_register', array( $this, 'register_options_new' ) );

			// Loads our Customizer controls assets.
			add_action( 'customize_controls_enqueue_scripts', array( $this, 'load_assets' ), 10 );

			// Enqueues our Customizer preview assets.
			add_action( 'customize_preview_init', array( $this, 'load_preview_assets' ) );

			// Add available top bar widgets panel.
			add_action( 'customize_controls_print_footer_scripts', array( $this, 'dekanpro_customizer_widgets' ) );
			add_action( 'customize_controls_print_footer_scripts', array( 'Dekanpro_Customizer_Control', 'template_units' ) );
		}

		/**
		 * Loads our Customizer custom controls.
		 *
		 * @since 1.0.0
		 * @param WP_Customize_Manager $customizer Instance of WP_Customize_Manager class.
		 */
		public function load_custom_controls( $customizer ) {

			// Directory where each custom control is located.
			$path = DEKANPRO_THEME_PATH . '/inc/customizer/controls/';

			// Require base control class.
			require $path . '/class-dekanpro-customizer-control.php'; // phpcs:ignore

			$controls = $this->get_custom_controls();

			// Load custom controls classes.
			foreach ( $controls as $control => $class ) {
				$control_path = $path . '/' . $control . '/class-dekanpro-customizer-control-' . $control . '.php';
				if ( file_exists( $control_path ) ) {
					require_once $control_path; // phpcs:ignore
					$customizer->register_control_type( $class );
				}
			}
		}

		/**
		 * Loads Customizer helper functions and sanitization callbacks.
		 *
		 * @since 1.0.0
		 */
		public function load_customizer_helpers() {
			require DEKANPRO_THEME_PATH . '/inc/customizer/customizer-helpers.php'; // phpcs:ignore
			require_once DEKANPRO_THEME_PATH . '/inc/customizer/customizer-callbacks.php'; // phpcs:ignore
			require DEKANPRO_THEME_PATH . '/inc/customizer/customizer-partials.php'; // phpcs:ignore
			require DEKANPRO_THEME_PATH . '/inc/customizer/ui/plugin-install-helper/class-dekanpro-customizer-plugin-install-helper.php'; // phpcs:ignore
		}

		/**
		 * Loads Customizer widgets classes.
		 *
		 * @since 1.0.0
		 */
		public function load_customizer_widgets() {

			$widgets = dekanpro_get_customizer_widgets();

			require DEKANPRO_THEME_PATH . '/inc/customizer/widgets/class-dekanpro-customizer-widget.php'; // phpcs:ignore

			foreach ( $widgets as $id => $class ) {

				$path = DEKANPRO_THEME_PATH . '/inc/customizer/widgets/class-dekanpro-customizer-widget-' . $id . '.php';

				if ( file_exists( $path ) ) {
					require $path; // phpcs:ignore
				}
			}
		}

		/**
		 * Move inbuilt panels into our sections.
		 *
		 * @since 1.0.0
		 * @param WP_Customize_Manager $customizer Instance of WP_Customize_Manager class.
		 */
		public static function customizer_tweak( $customizer ) {

			// Site Identity to Logo.
			$customizer->get_section( 'title_tagline' )->priority = 2;
			$customizer->get_section( 'title_tagline' )->title    = esc_html__( 'Logos &amp; Site Title', 'dekanpro' );

			// Custom logo.
			$customizer->get_control( 'custom_logo' )->description = esc_html__( 'Upload your logo image here.', 'dekanpro' );
			$customizer->get_control( 'custom_logo' )->priority    = 10;
			$customizer->get_setting( 'custom_logo' )->transport   = 'postMessage';

			// Add selective refresh partial for Custom Logo.
			$customizer->selective_refresh->add_partial(
				'custom_logo',
				array(
					'selector'            => '.dekanpro-logo',
					'render_callback'     => 'dekanpro_logo',
					'container_inclusive' => false,
					'fallback_refresh'    => true,
				)
			);

			// Site title.
			$customizer->get_setting( 'blogname' )->transport   = 'postMessage';
			$customizer->get_control( 'blogname' )->description = esc_html__( 'Enter the name of your site here.', 'dekanpro' );
			$customizer->get_control( 'blogname' )->priority    = 60;

			// Site description.
			$customizer->get_setting( 'blogdescription' )->transport   = 'postMessage';
			$customizer->get_control( 'blogdescription' )->description = esc_html__( 'A tagline is a short phrase, or sentence, used to convey the essence of the site.', 'dekanpro' );
			$customizer->get_control( 'blogdescription' )->priority    = 70;

			// Site icon.
			$customizer->get_control( 'site_icon' )->priority = 90;

			// Site Background.
			$background_fields = array(
				'background_color',
				'background_image',
				'background_preset',
				'background_position',
				'background_size',
				'background_repeat',
				'background_attachment',
				'background_image',
			);

			foreach ( $background_fields as $field ) {
				$customizer->get_control( $field )->section  = 'dekanpro_section_colors';
				$customizer->get_control( $field )->priority = 50;
			}

		}

		/**
		 * Registers our Customizer options.
		 *
		 * @since 1.0.0
		 */
		public function register_options() {

			// Directory where each individual section is located.
			$path = DEKANPRO_THEME_PATH . '/inc/customizer/settings/class-dekanpro-customizer-';

			/**
			 * Customizer sections.
			 */
			apply_filters(
				'dekanpro_cusomizer_settings',
				$sections = array(
					'sections',
					'colors',
					'category-colors',
					'typography',
					'layout',
					'top-bar',
					'main-header',
					'ticker',
					'hero',
					'advertisement',
					'featured-links',
					'pyml',
					'page-header',
					'logo',
					'single-post',
					'blog-page',
					'main-footer',
					'copyright-settings',
					'misc',
					'sticky-header',
					'sidebar',
					'breadcrumbs',
					'pro-features',
					'buttons',
				)
			);

			foreach ( $sections as $section ) {
				if ( file_exists( $path . $section . '.php' ) ) {
					require_once $path . $section . '.php'; // phpcs:ignore
				}
			}
		}

		/**
		 * Registers our Customizer options.
		 *
		 * @since 1.0.0
		 *
		 * @param WP_Customize_Manager $customizer instance of WP_Customize_Manager.
		 *
		 * @return void
		 */
		public function register_options_new( $customizer ) {

			$options = $this->get_customizer_options();
			if ( isset( $options['panel'] ) && ! empty( $options['panel'] ) ) {
				foreach ( $options['panel'] as $id => $args ) {
					$this->add_panel( $id, $args, $customizer );
				}
			}

			if ( isset( $options['section'] ) && ! empty( $options['section'] ) ) {
				foreach ( $options['section'] as $id => $args ) {
					$this->add_section( $id, $args, $customizer );
				}
			}

			if ( isset( $options['setting'] ) && ! empty( $options['setting'] ) ) {
				foreach ( $options['setting'] as $id => $args ) {

					$this->add_setting( $id, $args, $customizer );
					$this->add_control( $id, $args['control'], $customizer );
				}
			}
		}

		/**
		 * Filter and return Customizer options.
		 *
		 * @since 1.0.0
		 *
		 * @return Array Customizer options for registering Sections/Panels/Controls.
		 */
		public function get_customizer_options() {
			if ( ! is_null( self::$options ) ) {
				return self::$options;
			}

			return apply_filters( 'dekanpro_customizer_options', array() );
		}

		/**
		 * Register Customizer Panel
		 *
		 * @param string $id Panel id.
		 * @param Array  $args Panel settings.
		 * @param [type] $customizer instance of WP_Customize_Manager.
		 * @return void
		 */
		private function add_panel( $id, $args, $customizer ) {
			$class = dekanpro_get_prop( $args, 'class', 'WP_Customize_Panel' );

			$customizer->add_panel( new $class( $customizer, $id, $args ) );
		}

		/**
		 * Register Customizer Section.
		 *
		 * @since 1.0.0
		 *
		 * @param string               $id Section id.
		 * @param Array                $args Section settings.
		 * @param WP_Customize_Manager $customizer instance of WP_Customize_Manager.
		 *
		 * @return void
		 */
		private function add_section( $id, $args, $customizer ) {
			$class = dekanpro_get_prop( $args, 'class', 'WP_Customize_Section' );
			$customizer->add_section( new $class( $customizer, $id, $args ) );
		}

		/**
		 * Register Customizer Control.
		 *
		 * @since 1.0.0
		 *
		 * @param string               $id Control id.
		 * @param Array                $args Control settings.
		 * @param WP_Customize_Manager $customizer instance of WP_Customize_Manager.
		 *
		 * @return void
		 */
		private function add_control( $id, $args, $customizer ) {

			if ( isset( $args['class'] ) ) {
				$class = $args['class'];
			} else {
				$class = $this->get_control_class( dekanpro_get_prop( $args, 'type' ) );
			}
			$args['setting'] = $id;
			if ( false !== $class ) {
				$customizer->add_control( new $class( $customizer, $id, $args ) );
			} else {
				$customizer->add_control( $id, $args );
			}
		}

		/**
		 * Register Customizer Setting.
		 *
		 * @since 1.0.0
		 * @param string               $id Control setting id.
		 * @param Array                $setting Settings.
		 * @param WP_Customize_Manager $customizer instance of WP_Customize_Manager.
		 *
		 * @return void
		 */
		private function add_setting( $id, $setting, $customizer ) {
			$setting = wp_parse_args( $setting, $this->get_customizer_defaults( 'setting' ) );

			$customizer->add_setting(
				$id,
				array(
					'default'           => dekanpro()->options->get_default( $id ),
					'type'              => dekanpro_get_prop( $setting, 'type' ),
					'transport'         => dekanpro_get_prop( $setting, 'transport' ),
					'sanitize_callback' => dekanpro_get_prop( $setting, 'sanitize_callback', 'dekanpro_no_sanitize' ),
				)
			);

			$partial = dekanpro_get_prop( $setting, 'partial', false );

			if ( $partial && isset( $customizer->selective_refresh ) ) {

				$customizer->selective_refresh->add_partial(
					$id,
					array(
						'selector'            => dekanpro_get_prop( $partial, 'selector' ),
						'container_inclusive' => dekanpro_get_prop( $partial, 'container_inclusive' ),
						'render_callback'     => dekanpro_get_prop( $partial, 'render_callback' ),
						'fallback_refresh'    => dekanpro_get_prop( $partial, 'fallback_refresh' ),
					)
				);
			}
		}

		/**
		 * Return custom controls.
		 *
		 * @since 1.0.0
		 *
		 * @return Array custom control slugs & classnames.
		 */
		private function get_custom_controls() {
			return apply_filters(
				'dekanpro_custom_customizer_controls',
				array(
					'toggle'              => 'Dekanpro_Customizer_Control_Toggle',
					'select'              => 'Dekanpro_Customizer_Control_Select',
					'heading'             => 'Dekanpro_Customizer_Control_Heading',
					'color'               => 'Dekanpro_Customizer_Control_Color',
					'range'               => 'Dekanpro_Customizer_Control_Range',
					'spacing'             => 'Dekanpro_Customizer_Control_Spacing',
					'widget'              => 'Dekanpro_Customizer_Control_Widget',
					'radio-image'         => 'Dekanpro_Customizer_Control_Radio_Image',
					'background'          => 'Dekanpro_Customizer_Control_Background',
					'text'                => 'Dekanpro_Customizer_Control_Text',
					'textarea'            => 'Dekanpro_Customizer_Control_Textarea',
					'typography'          => 'Dekanpro_Customizer_Control_Typography',
					'button'              => 'Dekanpro_Customizer_Control_Button',
					'sortable'            => 'Dekanpro_Customizer_Control_Sortable',
					'info'                => 'Dekanpro_Customizer_Control_Info',
					'pro'                 => 'Dekanpro_Customizer_Control_Pro',
					'design-options'      => 'Dekanpro_Customizer_Control_Design_Options',
					'alignment'           => 'Dekanpro_Customizer_Control_Alignment',
					'checkbox-group'      => 'Dekanpro_Customizer_Control_Checkbox_Group',
					'repeater'            => 'Dekanpro_Customizer_Control_Repeater',
					'editor'              => 'Dekanpro_Customizer_Control_Editor',
					'section-pro'    	  => 'Dekanpro_Customizer_Control_Section_Pro',
					'generic-notice'      => 'Dekanpro_Customizer_Control_Generic_Notice',
					'gallery'             => 'Dekanpro_Customizer_Control_Gallery',
					'datetime'            => 'Dekanpro_Customizer_Control_Datetime',
					'section-group-title' => 'Dekanpro_Customizer_Control_Section_Group_Title',
				)
			);
		}

		/**
		 * Return default values for customizer parts.
		 *
		 * @param  String $type setting or control.
		 * @return Array  default values for the Customizer Configurations.
		 */
		private function get_customizer_defaults( $type ) {

			$defaults = array();

			switch ( $type ) {
				case 'setting':
					$defaults = array(
						'type'      => 'theme_mod',
						'transport' => 'refresh',
					);
					break;

				case 'control':
					$defaults = array();
					break;

				default:
					break;
			}

			return apply_filters(
				'dekanpro_customizer_configuration_defaults',
				$defaults,
				$type
			);
		}

		/**
		 * Get custom control classname.
		 *
		 * @since 1.0.0
		 *
		 * @param string $type Control ID.
		 *
		 * @return string Control classname.
		 */
		private function get_control_class( $type ) {

			if ( false !== strpos( $type, 'dekanpro-' ) ) {

				$controls = $this->get_custom_controls();
				$type     = trim( str_replace( 'dekanpro-', '', $type ) );
				if ( isset( $controls[ $type ] ) ) {
					return $controls[ $type ];
				}
			}

			return false;
		}

		/**
		 * Loads our own Customizer assets.
		 *
		 * @since 1.0.0
		 */
		public function load_assets() {

			// Script debug.
			$dekanpro_dir    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'dev/' : '';
			$dekanpro_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			/**
			 * Enqueue our Customizer styles.
			 */
			wp_enqueue_style(
				'dekanpro-customizer-styles',
				DEKANPRO_THEME_URI . '/inc/customizer/assets/css/dekanpro-customizer' . $dekanpro_suffix . '.css',
				false,
				DEKANPRO_THEME_VERSION
			);

			/**
			 * Enqueue our Customizer controls script.
			 */
			wp_enqueue_script(
				'dekanpro-customizer-js',
				DEKANPRO_THEME_URI . '/inc/customizer/assets/js/' . $dekanpro_dir . 'customize-controls' . $dekanpro_suffix . '.js',
				array( 'wp-color-picker', 'jquery', 'customize-base' ),
				DEKANPRO_THEME_VERSION,
				true
			);

			/**
			 * Enqueue Customizer controls dependency script.
			 */
			wp_enqueue_script(
				'dekanpro-control-dependency-js',
				DEKANPRO_THEME_URI . '/inc/customizer/assets/js/' . $dekanpro_dir . 'customize-dependency' . $dekanpro_suffix . '.js',
				array( 'jquery' ),
				DEKANPRO_THEME_VERSION,
				true
			);

			/**
			 * Localize JS variables
			 */
			$dekanpro_customizer_localized = array(
				'ajaxurl'                 => admin_url( 'admin-ajax.php' ),
				'wpnonce'                 => wp_create_nonce( 'dekanpro_customizer' ),
				'color_palette'           => array( '#ffffff', '#000000', '#e4e7ec', '#F43676', '#f7b40b', '#e04b43', '#30373e', '#8a63d4' ),
				'preview_url_for_section' => $this->get_preview_urls_for_section(),
				'strings'                 => array(
					'selectCategory' => esc_html__( 'Select a category', 'dekanpro' ),
				),
			);

			/**
			 * Allow customizer localized vars to be filtered.
			 */
			$dekanpro_customizer_localized = apply_filters( 'dekanpro_customizer_localized', $dekanpro_customizer_localized );

			wp_localize_script(
				'dekanpro-customizer-js',
				'dekanpro_customizer_localized',
				$dekanpro_customizer_localized
			);
		}

		/**
		 * Loads customizer preview assets
		 *
		 * @since 1.0.0
		 */
		public function load_preview_assets() {

			// Script debug.
			$dekanpro_dir    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'dev/' : '';
			$dekanpro_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$version         = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : DEKANPRO_THEME_VERSION;

			wp_enqueue_script(
				'dekanpro-customizer-preview-js',
				DEKANPRO_THEME_URI . '/inc/customizer/assets/js/' . $dekanpro_dir . 'customize-preview' . $dekanpro_suffix . '.js',
				array( 'customize-preview', 'customize-selective-refresh', 'jquery' ),
				$version,
				true
			);

			// Enqueue Customizer preview styles.
			wp_enqueue_style(
				'dekanpro-customizer-preview-styles',
				DEKANPRO_THEME_URI . '/inc/customizer/assets/css/dekanpro-customizer-preview' . $dekanpro_suffix . '.css',
				false,
				DEKANPRO_THEME_VERSION
			);

			/**
			 * Localize JS variables.
			 */
			$dekanpro_customizer_localized = array(
				'default_system_font' => dekanpro()->fonts->get_default_system_font(),
				'fonts'               => dekanpro()->fonts->get_fonts(),
				'google_fonts_url'    => '//fonts.googleapis.com',
				'google_font_weights' => '100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i',
			);

			/**
			 * Allow customizer localized vars to be filtered.
			 */
			$dekanpro_customizer_localized = apply_filters( 'dekanpro_customize_preview_localized', $dekanpro_customizer_localized );

			wp_localize_script(
				'dekanpro-customizer-preview-js',
				'dekanpro_customizer_preview',
				$dekanpro_customizer_localized
			);
		}

		/**
		 * Print the html template used to render the add top bar widgets frame.
		 *
		 * @since 1.0.0
		 */
		public function dekanpro_customizer_widgets() {

			// Get customizer widgets.
			$widgets = dekanpro_get_customizer_widgets();

			// Check if any available widgets exist.
			if ( ! is_array( $widgets ) || empty( $widgets ) ) {
				return;
			}
			?>
									<div id="dekanpro-available-widgets">

										<div class="dekanpro-widget-caption">
											<h3></h3>
											<a href="#" class="dekanpro-close-widgets-panel"></a>
										</div><!-- END #dekanpro-available-widgets-caption -->

										<div id="dekanpro-available-widgets-list">

											<?php foreach ( $widgets as $id => $classname ) { ?>
												<?php $widget = new $classname(); ?>

												<div id="dekanpro-widget-tpl-<?php echo esc_attr( $widget->id_base ); ?>" data-widget-id="<?php echo esc_attr( $widget->id_base ); ?>" class="dekanpro-widget">
													<?php $widget->template(); ?>
												</div>

											<?php } ?>

										</div><!-- END #dekanpro-available-widgets-list -->
									</div>
						<?php
		}

		/**
		 * Get preview URL for a section. The URL will load when the section is opened.
		 *
		 * @return string
		 */
		public function get_preview_urls_for_section() {

			$return = array();

			// Preview a random single post for Single Post section.
			$posts = get_posts(
				array(
					'post_type'      => 'post',
					'posts_per_page' => 1,
					'orderby'        => 'rand',
				)
			);

			if ( count( $posts ) ) {
				$return['dekanpro_section_blog_single_post'] = get_permalink( $posts[0] );
			}

			// Preview blog page.
			$return['dekanpro_section_blog_page'] = dekanpro_get_blog_url();

			return $return;
		}
	}
endif;

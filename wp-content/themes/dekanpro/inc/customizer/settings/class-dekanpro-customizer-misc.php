<?php
/**
 * Dekanpro Misc section in Customizer.
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

if ( ! class_exists( 'Dekanpro_Customizer_Misc' ) ) :
	/**
	 * Dekanpro Misc section in Customizer.
	 */
	class Dekanpro_Customizer_Misc {

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			/**
			 * Registers our custom options in Customizer.
			 */
			add_filter( 'dekanpro_customizer_options', array( $this, 'register_options' ) );
		}

		/**
		 * Registers our custom options in Customizer.
		 *
		 * @since 1.0.0
		 * @param array $options Array of customizer options.
		 */
		public function register_options( $options ) {

			// Section.
			$options['section']['dekanpro_section_misc'] = array(
				'title'    => esc_html__( 'Misc Settings', 'dekanpro' ),
				'panel'    => 'dekanpro_panel_general',
				'priority' => 60,
			);

			// Schema toggle.
			$options['setting']['dekanpro_enable_schema'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'        => 'dekanpro-toggle',
					'label'       => esc_html__( 'Schema Markup', 'dekanpro' ),
					'description' => esc_html__( 'Add structured data to your content.', 'dekanpro' ),
					'section'     => 'dekanpro_section_misc',
				),
			);

			// Custom form styles.
			$options['setting']['dekanpro_custom_input_style'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'        => 'dekanpro-toggle',
					'label'       => esc_html__( 'Custom Form Styles', 'dekanpro' ),
					'description' => esc_html__( 'Custom design for checkboxes and radio buttons.', 'dekanpro' ),
					'section'     => 'dekanpro_section_misc',
				),
			);

			// Enable/Disable Page Preloader.
			$options['setting']['dekanpro_preloader'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'        => 'dekanpro-toggle',
					'label'       => esc_html__( 'Enable Page Preloader', 'dekanpro' ),
					'description' => esc_html__( 'Show animation until page is fully loaded.', 'dekanpro' ),
					'section'     => 'dekanpro_section_misc',
				),
			);

			// Enable/Disable Scroll Top.
			$options['setting']['dekanpro_scroll_top'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'        => 'dekanpro-toggle',
					'label'       => esc_html__( 'Enable Scroll Top Button', 'dekanpro' ),
					'description' => esc_html__( 'A sticky button that allows users to easily return to the top of a page.', 'dekanpro' ),
					'section'     => 'dekanpro_section_misc',
				),
			);

			// Enable/Disable Cursor Dot.
			$options['setting']['dekanpro_enable_cursor_dot'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'        => 'dekanpro-toggle',
					'label'       => esc_html__( 'Enable Cursor Dot', 'dekanpro' ),
					'description' => esc_html__( 'A cursor dot effect show on desktop size mode only with work on mouse.', 'dekanpro' ),
					'section'     => 'dekanpro_section_misc',
				),
			);

			return $options;
		}
	}
endif;
new Dekanpro_Customizer_Misc();

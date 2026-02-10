<?php
/**
 * Dekanpro Base Colors section in Customizer.
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

if ( ! class_exists( 'Dekanpro_Customizer_Colors' ) ) :
	/**
	 * Dekanpro Colors section in Customizer.
	 */
	class Dekanpro_Customizer_Colors {

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
			$options['section']['dekanpro_section_colors'] = array(
				'title'    => esc_html__( 'Base Colors', 'dekanpro' ),
				'panel'    => 'dekanpro_panel_general',
				'priority' => 20,
			);

			// Accent color.
			$options['setting']['dekanpro_accent_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_color',
				'control'           => array(
					'type'        => 'dekanpro-color',
					'label'       => esc_html__( 'Accent Color', 'dekanpro' ),
					'description' => esc_html__( 'The accent color is used subtly throughout your site, to call attention to key elements.', 'dekanpro' ),
					'section'     => 'dekanpro_section_colors',
					'priority'    => 10,
					'opacity'     => false,
				),
			);

			// Dark mode
			$options['setting']['dekanpro_dark_mode'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'        => 'dekanpro-toggle',
					'label'       => esc_html__( 'Dark mode', 'dekanpro' ),
					'description' => esc_html__( 'Enable dark mode.', 'dekanpro' ),
					'section'     => 'dekanpro_section_colors',
					'priority'    => 11,
				),
			);

			// Body Animation
			$options['setting']['dekanpro_body_animation'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_select',
				'control'           => array(
					'type'        => 'dekanpro-select',
					'label'       => esc_html__( 'Body Animation', 'dekanpro' ),
					'description' => esc_html__( 'Choose Body Animation.', 'dekanpro' ),
					'section'     => 'dekanpro_section_colors',
					'priority'    => 12,
					'choices'     => array(
						'0' => esc_html__( 'None', 'dekanpro' ),
						'1' => esc_html__( 'Glassmorphism', 'dekanpro' ),
					),
				),
			);

			// Body background heading.
			$options['setting']['dekanpro_body_background_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'     => 'dekanpro-heading',
					'priority' => 40,
					'label'    => esc_html__( 'Body Background', 'dekanpro' ),
					'section'  => 'dekanpro_section_colors',
					'toggle'   => false,
				),
			);

			return $options;
		}

	}
endif;
new Dekanpro_Customizer_Colors();

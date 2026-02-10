<?php
/**
 * Dekanpro Logo section in Customizer.
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

if ( ! class_exists( 'Dekanpro_Customizer_Logo' ) ) :
	/**
	 * Dekanpro Logo section in Customizer.
	 */
	class Dekanpro_Customizer_Logo {

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

			// Logo Max Height.
			$options['setting']['dekanpro_logo_max_height'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_responsive',
				'control'           => array(
					'type'        => 'dekanpro-range',
					'label'       => esc_html__( 'Logo Height', 'dekanpro' ),
					'description' => esc_html__( 'Maximum logo image height.', 'dekanpro' ),
					'section'     => 'title_tagline',
					'priority'    => 30,
					'min'         => 0,
					'max'         => 1000,
					'step'        => 10,
					'unit'        => 'px',
					'responsive'  => true,
					'required'    => array(
						array(
							'control'  => 'custom_logo',
							'value'    => false,
							'operator' => '!=',
						),
					),
				),
			);

			// Logo margin.
			$options['setting']['dekanpro_logo_margin'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_responsive',
				'control'           => array(
					'type'        => 'dekanpro-spacing',
					'label'       => esc_html__( 'Logo Margin', 'dekanpro' ),
					'description' => esc_html__( 'Specify spacing around logo. Negative values are allowed.', 'dekanpro' ),
					'section'     => 'title_tagline',
					'settings'    => 'dekanpro_logo_margin',
					'priority'    => 40,
					'choices'     => array(
						'top'    => esc_html__( 'Top', 'dekanpro' ),
						'right'  => esc_html__( 'Right', 'dekanpro' ),
						'bottom' => esc_html__( 'Bottom', 'dekanpro' ),
						'left'   => esc_html__( 'Left', 'dekanpro' ),
					),
					'responsive'  => true,
					'unit'        => array(
						'px',
					),
				),
			);

			// Show tagline.
			$options['setting']['dekanpro_display_tagline'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'     => 'dekanpro-toggle',
					'label'    => esc_html__( 'Display Tagline', 'dekanpro' ),
					'section'  => 'title_tagline',
					'settings' => 'dekanpro_display_tagline',
					'priority' => 80,
				),
				'partial'           => array(
					'selector'            => '.dekanpro-logo',
					'render_callback'     => 'dekanpro_logo',
					'container_inclusive' => false,
					'fallback_refresh'    => true,
				),
			);

			// Site Identity heading.
			$options['setting']['dekanpro_logo_heading_site_identity'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'     => 'dekanpro-heading',
					'label'    => esc_html__( 'Site Identity', 'dekanpro' ),
					'section'  => 'title_tagline',
					'settings' => 'dekanpro_logo_heading_site_identity',
					'priority' => 50,
					'toggle'   => false,
				),
			);

			// Logo typography heading.
			$options['setting']['dekanpro_typography_logo_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'     => 'dekanpro-heading',
					'label'    => esc_html__( 'Typography', 'dekanpro' ),
					'section'  => 'title_tagline',
					'priority' => 100,
					'required' => array(
						array(
							'control'  => 'custom_logo',
							'value'    => false,
							'operator' => '==',
						),
					),
				),
			);

			// Site title font size.
			$options['setting']['dekanpro_logo_text_font_size'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_responsive',
				'control'           => array(
					'type'       => 'dekanpro-range',
					'label'      => esc_html__( 'Site Title Font Size', 'dekanpro' ),
					'section'    => 'title_tagline',
					'priority'   => 100,
					'min'        => 8,
					'max'        => 30,
					'step'       => 1,
					'responsive' => true,
					'unit'       => array(
						array(
							'id'   => 'px',
							'name' => 'px',
							'min'  => 8,
							'max'  => 90,
							'step' => 1,
						),
						array(
							'id'   => 'em',
							'name' => 'em',
							'min'  => 0.5,
							'max'  => 5,
							'step' => 0.01,
						),
						array(
							'id'   => 'rem',
							'name' => 'rem',
							'min'  => 0.5,
							'max'  => 5,
							'step' => 0.01,
						),
					),
					'required'   => array(
						array(
							'control'  => 'custom_logo',
							'value'    => false,
							'operator' => '==',
						),
						array(
							'control'  => 'dekanpro_typography_logo_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			return $options;
		}
	}
endif;
new Dekanpro_Customizer_Logo();

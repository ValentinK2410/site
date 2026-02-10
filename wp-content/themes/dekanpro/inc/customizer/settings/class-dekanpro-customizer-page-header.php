<?php
/**
 * Dekanpro Page Title Settings section in Customizer.
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

if ( ! class_exists( 'Dekanpro_Customizer_Page_Header' ) ) :
	/**
	 * Dekanpro Page Title Settings section in Customizer.
	 */
	class Dekanpro_Customizer_Page_Header {

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

			// Page Title Section.
			$options['section']['dekanpro_section_page_header'] = array(
				'title'    => esc_html__( 'Page Header', 'dekanpro' ),
				'panel'    => 'dekanpro_panel_header',
				'priority' => 60,
			);

			// Page Header enable.
			$options['setting']['dekanpro_page_header_enable'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'    => 'dekanpro-toggle',
					'label'   => esc_html__( 'Enable Page Header', 'dekanpro' ),
					'section' => 'dekanpro_section_page_header',
				),
			);

			// Spacing.
			$options['setting']['dekanpro_page_header_spacing'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_responsive',
				'control'           => array(
					'type'        => 'dekanpro-spacing',
					'label'       => esc_html__( 'Page Title Spacing', 'dekanpro' ),
					'description' => esc_html__( 'Specify Page Title top and bottom padding.', 'dekanpro' ),
					'section'     => 'dekanpro_section_page_header',
					'choices'     => array(
						'top'    => esc_html__( 'Top', 'dekanpro' ),
						'bottom' => esc_html__( 'Bottom', 'dekanpro' ),
					),
					'responsive'  => true,
					'unit'        => array(
						'px',
					),
					'required'    => array(
						array(
							'control'  => 'dekanpro_page_header_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Page Header design options heading.
			$options['setting']['dekanpro_page_header_heading_design'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'     => 'dekanpro-heading',
					'label'    => esc_html__( 'Design Options', 'dekanpro' ),
					'section'  => 'dekanpro_section_page_header',
					'required' => array(
						array(
							'control'  => 'dekanpro_page_header_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Page Header background design.
			$options['setting']['dekanpro_page_header_background'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_design_options',
				'control'           => array(
					'type'     => 'dekanpro-design-options',
					'label'    => esc_html__( 'Background', 'dekanpro' ),
					'section'  => 'dekanpro_section_page_header',
					'display'  => array(
						'background' => array(
							'color'    => esc_html__( 'Solid Color', 'dekanpro' ),
							'gradient' => esc_html__( 'Gradient', 'dekanpro' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'dekanpro_page_header_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'dekanpro_page_header_heading_design',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Page Header Text Color.
			$options['setting']['dekanpro_page_header_text_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_design_options',
				'control'           => array(
					'type'     => 'dekanpro-design-options',
					'label'    => esc_html__( 'Font Color', 'dekanpro' ),
					'section'  => 'dekanpro_section_page_header',
					'display'  => array(
						'color' => array(
							'text-color'       => esc_html__( 'Text Color', 'dekanpro' ),
							'link-color'       => esc_html__( 'Link Color', 'dekanpro' ),
							'link-hover-color' => esc_html__( 'Link Hover Color', 'dekanpro' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'dekanpro_page_header_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'dekanpro_page_header_heading_design',
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
new Dekanpro_Customizer_Page_Header();

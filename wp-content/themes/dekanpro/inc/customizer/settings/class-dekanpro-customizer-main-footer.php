<?php
/**
 * Dekanpro Main Footer section in Customizer.
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

if ( ! class_exists( 'Dekanpro_Customizer_Main_Footer' ) ) :
	/**
	 * Dekanpro Main Footer section in Customizer.
	 */
	class Dekanpro_Customizer_Main_Footer {

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
			$options['section']['dekanpro_section_main_footer'] = array(
				'title'    => esc_html__( 'Main Footer', 'dekanpro' ),
				'panel'    => 'dekanpro_panel_footer',
				'priority' => 20,
			);

			// Enable Footer.
			$options['setting']['dekanpro_enable_footer'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'    => 'dekanpro-toggle',
					'label'   => esc_html__( 'Enable Main Footer', 'dekanpro' ),
					'section' => 'dekanpro_section_main_footer',
				),
			);

			// Footer Layout.
			$options['setting']['dekanpro_footer_layout'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_select',
				'control'           => array(
					'type'        => 'dekanpro-radio-image',
					'label'       => esc_html__( 'Column Layout', 'dekanpro' ),
					'description' => esc_html__( 'Choose your site&rsquo;s footer column layout.', 'dekanpro' ),
					'section'     => 'dekanpro_section_main_footer',
					'choices'     => array(
						'layout-2' => array(
							'image' => DEKANPRO_THEME_URI . '/inc/customizer/assets/images/footer-layout-2.svg',
							'title' => esc_html__( '1/3 + 1/3 + 1/3', 'dekanpro' ),
						),
						'layout-8' => array(
							'image' => DEKANPRO_THEME_URI . '/inc/customizer/assets/images/footer-layout-8.svg',
							'title' => esc_html__( '1', 'dekanpro' ),
						),
					),
					'required'    => array(
						array(
							'control'  => 'dekanpro_enable_footer',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#dekanpro-footer-widgets',
					'render_callback'     => 'dekanpro_footer_widgets',
					'container_inclusive' => false,
					'fallback_refresh'    => true,
				),
			);

			// Center footer widgets..
			$options['setting']['dekanpro_footer_widgets_align_center'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'     => 'dekanpro-toggle',
					'label'    => esc_html__( 'Center Widget Content', 'dekanpro' ),
					'section'  => 'dekanpro_section_main_footer',
					'required' => array(
						array(
							'control'  => 'dekanpro_enable_footer',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#dekanpro-footer-widgets',
					'render_callback'     => 'dekanpro_footer_widgets',
					'container_inclusive' => false,
					'fallback_refresh'    => true,
				),
			);

			// Footer Design Options heading.
			$options['setting']['dekanpro_footer_heading_design_options'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'     => 'dekanpro-heading',
					'label'    => esc_html__( 'Design Options', 'dekanpro' ),
					'section'  => 'dekanpro_section_main_footer',
					'required' => array(
						array(
							'control'  => 'dekanpro_enable_footer',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Footer Background.
			$options['setting']['dekanpro_footer_background'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_design_options',
				'control'           => array(
					'type'     => 'dekanpro-design-options',
					'label'    => esc_html__( 'Background', 'dekanpro' ),
					'section'  => 'dekanpro_section_main_footer',
					'display'  => array(
						'background' => array(
							'color'    => esc_html__( 'Solid Color', 'dekanpro' ),
							'gradient' => esc_html__( 'Gradient', 'dekanpro' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'dekanpro_enable_footer',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'dekanpro_footer_heading_design_options',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Footer Text Color.
			$options['setting']['dekanpro_footer_text_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_design_options',
				'control'           => array(
					'type'     => 'dekanpro-design-options',
					'label'    => esc_html__( 'Font Color', 'dekanpro' ),
					'section'  => 'dekanpro_section_main_footer',
					'display'  => array(
						'color' => array(
							'text-color'         => esc_html__( 'Text Color', 'dekanpro' ),
							'link-color'         => esc_html__( 'Link Color', 'dekanpro' ),
							'link-hover-color'   => esc_html__( 'Link Hover Color', 'dekanpro' ),
							'widget-title-color' => esc_html__( 'Widget Title Color', 'dekanpro' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'dekanpro_enable_footer',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'dekanpro_footer_heading_design_options',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Footer Border.
			$options['setting']['dekanpro_footer_border'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_design_options',
				'control'           => array(
					'type'     => 'dekanpro-design-options',
					'label'    => esc_html__( 'Border', 'dekanpro' ),
					'section'  => 'dekanpro_section_main_footer',
					'display'  => array(
						'border' => array(
							'style'     => esc_html__( 'Style', 'dekanpro' ),
							'color'     => esc_html__( 'Color', 'dekanpro' ),
							'width'     => esc_html__( 'Width (px)', 'dekanpro' ),
							'positions' => array(
								'top'    => esc_html__( 'Top', 'dekanpro' ),
								'bottom' => esc_html__( 'Bottom', 'dekanpro' ),
							),
						),
					),
					'required' => array(
						array(
							'control'  => 'dekanpro_enable_footer',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'dekanpro_footer_heading_design_options',
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
new Dekanpro_Customizer_Main_Footer();

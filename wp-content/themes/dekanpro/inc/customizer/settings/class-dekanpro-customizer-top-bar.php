<?php
/**
 * Dekanpro Top Bar Settings section in Customizer.
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

if ( ! class_exists( 'Dekanpro_Customizer_Top_Bar' ) ) :
	/**
	 * Dekanpro Top Bar Settings section in Customizer.
	 */
	class Dekanpro_Customizer_Top_Bar {

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
			$options['section']['dekanpro_section_top_bar'] = array(
				'title'    => esc_html__( 'Top Bar', 'dekanpro' ),
				'panel'    => 'dekanpro_panel_header',
				'priority' => 10,
			);

			// Enable Top Bar.
			$options['setting']['dekanpro_top_bar_enable'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'        => 'dekanpro-toggle',
					'label'       => esc_html__( 'Enable Top Bar', 'dekanpro' ),
					'description' => esc_html__( 'Top Bar is a section with widgets located above Main Header area.', 'dekanpro' ),
					'section'     => 'dekanpro_section_top_bar',
				),
			);

			// Top Bar widgets heading.
			$options['setting']['dekanpro_top_bar_heading_widgets'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'        => 'dekanpro-heading',
					'label'       => esc_html__( 'Top Bar Widgets', 'dekanpro' ),
					'description' => esc_html__( 'Click the Add Widget button to add available widgets to your Top Bar.', 'dekanpro' ),
					'section'     => 'dekanpro_section_top_bar',
					'required'    => array(
						array(
							'control'  => 'dekanpro_top_bar_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Top Bar widgets.
			$options['setting']['dekanpro_top_bar_widgets'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_widget',
				'control'           => array(
					'type'       => 'dekanpro-widget',
					'label'      => esc_html__( 'Top Bar Widgets', 'dekanpro' ),
					'section'    => 'dekanpro_section_top_bar',
					'widgets'    => array(
						'text'    => array(
							'max_uses' => 2,
						),
						'nav'     => array(
							'max_uses' => 1,
						),
					),
					'locations'  => array(
						'left'  => esc_html__( 'Left', 'dekanpro' ),
						'right' => esc_html__( 'Right', 'dekanpro' ),
					),
					'visibility' => array(
						'all'                => esc_html__( 'Show on All Devices', 'dekanpro' ),
						'hide-mobile'        => esc_html__( 'Hide on Mobile', 'dekanpro' ),
						'hide-tablet'        => esc_html__( 'Hide on Tablet', 'dekanpro' ),
						'hide-mobile-tablet' => esc_html__( 'Hide on Mobile and Tablet', 'dekanpro' ),
					),
					'required'   => array(
						array(
							'control'  => 'dekanpro_top_bar_heading_widgets',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'dekanpro_top_bar_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#dekanpro-topbar',
					'render_callback'     => 'dekanpro_topbar_output',
					'container_inclusive' => true,
					'fallback_refresh'    => true,
				),
			);

			// Top Bar design options heading.
			$options['setting']['dekanpro_top_bar_heading_design_options'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'     => 'dekanpro-heading',
					'label'    => esc_html__( 'Design Options', 'dekanpro' ),
					'section'  => 'dekanpro_section_top_bar',
					'required' => array(
						array(
							'control'  => 'dekanpro_top_bar_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Top Bar Background.
			$options['setting']['dekanpro_top_bar_background'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_design_options',
				'control'           => array(
					'type'     => 'dekanpro-design-options',
					'label'    => esc_html__( 'Background', 'dekanpro' ),
					'section'  => 'dekanpro_section_top_bar',
					'display'  => array(
						'background' => array(
							'color'    => esc_html__( 'Solid Color', 'dekanpro' ),
							'gradient' => esc_html__( 'Gradient', 'dekanpro' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'dekanpro_top_bar_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'dekanpro_top_bar_heading_design_options',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Top Bar Text Color.
			$options['setting']['dekanpro_top_bar_text_color'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_design_options',
				'control'           => array(
					'type'     => 'dekanpro-design-options',
					'label'    => esc_html__( 'Font Color', 'dekanpro' ),
					'section'  => 'dekanpro_section_top_bar',
					'display'  => array(
						'color' => array(
							'text-color'       => esc_html__( 'Text Color', 'dekanpro' ),
							'link-color'       => esc_html__( 'Link Color', 'dekanpro' ),
							'link-hover-color' => esc_html__( 'Link Hover Color', 'dekanpro' ),
						),
					),
					'required' => array(
						array(
							'control'  => 'dekanpro_top_bar_enable',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'dekanpro_top_bar_heading_design_options',
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
new Dekanpro_Customizer_Top_Bar();

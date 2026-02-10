<?php
/**
 * Dekanpro Copyright Bar section in Customizer.
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

if ( ! class_exists( 'Dekanpro_Customizer_Copyright_Settings' ) ) :
	/**
	 * Dekanpro Copyright Bar section in Customizer.
	 */
	class Dekanpro_Customizer_Copyright_Settings {

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Registers our custom options in Customizer.
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
			$options['section']['dekanpro_section_copyright_bar'] = array(
				'title'    => esc_html__( 'Copyright Bar', 'dekanpro' ),
				'priority' => 30,
				'panel'    => 'dekanpro_panel_footer',
			);

			// Enable Copyright Bar.
			$options['setting']['dekanpro_enable_copyright'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'    => 'dekanpro-toggle',
					'label'   => esc_html__( 'Enable Copyright Bar', 'dekanpro' ),
					'section' => 'dekanpro_section_copyright_bar',
				),
			);

			// Copyright Layout.
			$options['setting']['dekanpro_copyright_layout'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_select',
				'control'           => array(
					'type'        => 'dekanpro-radio-image',
					'section'     => 'dekanpro_section_copyright_bar',
					'label'       => esc_html__( 'Copyright Layout', 'dekanpro' ),
					'description' => esc_html__( 'Choose your site&rsquo;s copyright widgets layout.', 'dekanpro' ),
					'choices'     => array(
						'layout-1' => array(
							'image' => DEKANPRO_THEME_URI . '/inc/customizer/assets/images/copyright-layout-1.svg',
							'title' => esc_html__( 'Centered', 'dekanpro' ),
						),
					),
					'required'    => array(
						array(
							'control'  => 'dekanpro_enable_copyright',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Copyright widgets heading.
			$options['setting']['dekanpro_copyright_heading_widgets'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'        => 'dekanpro-heading',
					'section'     => 'dekanpro_section_copyright_bar',
					'label'       => esc_html__( 'Copyright Bar Widgets', 'dekanpro' ),
					'description' => esc_html__( 'Click the Add Widget button to add available widgets to your Copyright Bar.', 'dekanpro' ),
					'required'    => array(
						array(
							'control'  => 'dekanpro_enable_copyright',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Copyright widgets.
			$options['setting']['dekanpro_copyright_widgets'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_widget',
				'control'           => array(
					'type'       => 'dekanpro-widget',
					'section'    => 'dekanpro_section_copyright_bar',
					'label'      => esc_html__( 'Copyright Bar Widgets', 'dekanpro' ),
					'widgets'    => array(
						'text'    => array(
							'max_uses' => 1,
						),
						'nav'     => array(
							'menu_location' => apply_filters( 'dekanpro_footer_menu_location', 'dekanpro-footer' ),
							'max_uses'      => 1,
						),
						'socials' => array(
							'max_uses' => 1,
							'styles'   => array(
								'minimal' => esc_html__( 'Minimal', 'dekanpro' ),
								'rounded' => esc_html__( 'Rounded', 'dekanpro' ),
							),
						),
					),
					'locations'  => array(
						'start' => esc_html__( 'Start', 'dekanpro' ),
						'end'   => esc_html__( 'End', 'dekanpro' ),
					),
					'visibility' => array(
						'all'                => esc_html__( 'Show on All Devices', 'dekanpro' ),
						'hide-mobile'        => esc_html__( 'Hide on Mobile', 'dekanpro' ),
						'hide-tablet'        => esc_html__( 'Hide on Tablet', 'dekanpro' ),
						'hide-mobile-tablet' => esc_html__( 'Hide on Mobile and Tablet', 'dekanpro' ),
					),
					'required'   => array(
						array(
							'control'  => 'dekanpro_copyright_heading_widgets',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'dekanpro_enable_copyright',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#dekanpro-copyright',
					'render_callback'     => 'dekanpro_copyright_bar_output',
					'container_inclusive' => true,
					'fallback_refresh'    => true,
				),
			);

			return $options;
		}

	}
endif;
new Dekanpro_Customizer_Copyright_Settings();

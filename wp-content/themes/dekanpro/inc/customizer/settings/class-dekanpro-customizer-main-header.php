<?php
/**
 * Dekanpro Main Header Settings section in Customizer.
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

if ( ! class_exists( 'Dekanpro_Customizer_Main_Header' ) ) :
	/**
	 * Dekanpro Main Header section in Customizer.
	 */
	class Dekanpro_Customizer_Main_Header {

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

			// Main Header Section.
			$options['section']['dekanpro_section_main_header'] = array(
				'title'    => esc_html__( 'Main Header', 'dekanpro' ),
				'panel'    => 'dekanpro_panel_header',
				'priority' => 20,
			);

			// Header Layout.
			$options['setting']['dekanpro_header_layout'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_select',
				'control'           => array(
					'type'        => 'dekanpro-radio-image',
					'label'       => esc_html__( 'Header Layout', 'dekanpro' ),
					'description' => esc_html__( 'Pre-defined positions of header elements, such as logo and navigation.', 'dekanpro' ),
					'section'     => 'dekanpro_section_main_header',
					'priority'    => 5,
					'choices'     => array(
						'layout-1' => array(
							'image' => DEKANPRO_THEME_URI . '/inc/customizer/assets/images/header-layout-1.svg',
							'title' => esc_html__( 'Header 1', 'dekanpro' ),
						),
					),
				),
			);

			// Header widgets heading.
			$options['setting']['dekanpro_header_heading_widgets'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'        => 'dekanpro-heading',
					'label'       => esc_html__( 'Header Widgets', 'dekanpro' ),
					'description' => esc_html__( 'Click the "Add Widget" button to add available widgets to your Header. Click the down arrow icon to expand widget options.', 'dekanpro' ),
					'section'     => 'dekanpro_section_main_header',
					'space'       => true,
				),
			);

			// Header widgets.
			$options['setting']['dekanpro_header_widgets'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_widget',
				'control'           => array(
					'type'       => 'dekanpro-widget',
					'label'      => esc_html__( 'Header Widgets', 'dekanpro' ),
					'section'    => 'dekanpro_section_main_header',
					'widgets'    => apply_filters(
						'dekanpro_main_header_widgets',
						array(
							'search'   => array(
								'max_uses' => 1,
							),
							'darkmode' => array(
								'max_uses' => 1,
							),
							'button'   => array(
								'max_uses' => 1,
							),
							'socials'  => array(
								'max_uses' => 1,
								'styles'   => array(
									'rounded-fill'   => esc_html__( 'Rounded Fill', 'dekanpro' ),
									'rounded-border' => esc_html__( 'Rounded Border', 'dekanpro' ),
								),
							),
						)
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
							'control'  => 'dekanpro_header_heading_widgets',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#dekanpro-header',
					'render_callback'     => 'dekanpro_header_content_output',
					'container_inclusive' => false,
					'fallback_refresh'    => true,
				),
			);

			return $options;
		}
	}
endif;
new Dekanpro_Customizer_Main_Header();

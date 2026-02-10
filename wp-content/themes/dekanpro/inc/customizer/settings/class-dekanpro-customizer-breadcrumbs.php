<?php
/**
 * Dekanpro Breadcrumbs Settings section in Customizer.
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

if ( ! class_exists( 'Dekanpro_Customizer_Breadcrumbs' ) ) :
	/**
	 * Dekanpro Breadcrumbs Settings section in Customizer.
	 */
	class Dekanpro_Customizer_Breadcrumbs {

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

			// Main Navigation Section.
			$options['section']['dekanpro_section_breadcrumbs'] = array(
				'title'    => esc_html__( 'Breadcrumbs', 'dekanpro' ),
				'panel'    => 'dekanpro_panel_header',
				'priority' => 70,
			);

			// Breadcrumbs.
			$options['setting']['dekanpro_breadcrumbs_enable'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'    => 'dekanpro-toggle',
					'label'   => esc_html__( 'Enable Breadcrumbs', 'dekanpro' ),
					'section' => 'dekanpro_section_breadcrumbs',
				),
			);

			// Hide breadcrumbs on.
			$options['setting']['dekanpro_breadcrumbs_hide_on'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_no_sanitize',
				'control'           => array(
					'type'        => 'dekanpro-checkbox-group',
					'label'       => esc_html__( 'Disable On: ', 'dekanpro' ),
					'description' => esc_html__( 'Choose on which pages you want to disable breadcrumbs. ', 'dekanpro' ),
					'section'     => 'dekanpro_section_breadcrumbs',
					'choices'     => dekanpro_get_display_choices(),
					'required'    => array(
						array(
							'control'  => 'dekanpro_breadcrumbs_enable',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Spacing.
			$options['setting']['dekanpro_breadcrumbs_spacing'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_responsive',
				'control'           => array(
					'type'        => 'dekanpro-spacing',
					'label'       => esc_html__( 'Spacing', 'dekanpro' ),
					'description' => esc_html__( 'Specify top and bottom padding.', 'dekanpro' ),
					'section'     => 'dekanpro_section_breadcrumbs',
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
							'control'  => 'dekanpro_breadcrumbs_enable',
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
new Dekanpro_Customizer_Breadcrumbs();

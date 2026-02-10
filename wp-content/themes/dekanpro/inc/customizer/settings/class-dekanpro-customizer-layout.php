<?php
/**
 * Dekanpro Layout section in Customizer.
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

if ( ! class_exists( 'Dekanpro_Customizer_Layout' ) ) :
	/**
	 * Dekanpro Layout section in Customizer.
	 */
	class Dekanpro_Customizer_Layout {

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
			$options['section']['dekanpro_layout_section'] = array(
				'title'    => esc_html__( 'Layout', 'dekanpro' ),
				'panel'    => 'dekanpro_panel_general',
				'priority' => 10,
			);

			// Site layout.
			$options['setting']['dekanpro_site_layout'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_select',
				'control'           => array(
					'type'        => 'dekanpro-select',
					'section'     => 'dekanpro_layout_section',
					'label'       => esc_html__( 'Site Layout', 'dekanpro' ),
					'description' => esc_html__( 'Choose your site&rsquo;s main layout.', 'dekanpro' ),
					'choices'     => array(
						'fw-contained' => esc_html__( 'Full Width: Contained', 'dekanpro' ),
						'fw-stretched' => esc_html__( 'Full Width: Stretched', 'dekanpro' ),
					),
				),
			);

			return $options;
		}
	}
endif;
new Dekanpro_Customizer_Layout();

<?php
/**
 * Dekanpro Sticky Header Settings section in Customizer.
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

if ( ! class_exists( 'Dekanpro_Customizer_Sticky_Header' ) ) :
	/**
	 * Dekanpro Sticky Header section in Customizer.
	 */
	class Dekanpro_Customizer_Sticky_Header {

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

			// Sticky Header Section.
			$options['section']['dekanpro_section_sticky_header'] = array(
				'title'    => esc_html__( 'Sticky Header', 'dekanpro' ),
				'panel'    => 'dekanpro_panel_header',
				'priority' => 80,
			);

			// Enable Transparent Header.
			$options['setting']['dekanpro_sticky_header'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'    => 'dekanpro-toggle',
					'label'   => esc_html__( 'Enable Sticky Header', 'dekanpro' ),
					'section' => 'dekanpro_section_sticky_header',
				),
			);

			return $options;
		}
	}
endif;
new Dekanpro_Customizer_Sticky_Header();

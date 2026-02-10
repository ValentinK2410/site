<?php
/**
 * Buttons section in Customizer » General Settings.
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

if ( ! class_exists( 'Dekanpro_Customizer_Buttons' ) ) :
	/**
	 * Buttons section in Customizer » General Settings.
	 */
	class Dekanpro_Customizer_Buttons {

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

			$theme = wp_get_theme();
			// Upsell section
			$options['section']['dekanpro_section_upsell_button'] = array(
				'class'    => 'Dekanpro_Customizer_Control_Section_Pro',
				'title'    => esc_html__( 'Need more features?', 'dekanpro' ),
				'pro_url'  => esc_url_raw( 'https://dekan.pro/' ),
				'pro_text' => esc_html__( 'Upgrade to pro', 'dekanpro' ),
				'priority' => 200,
			);

			$options['setting']['dekanpro_section_upsell_heading'] = array(
				'control' => array(
					'type'    => 'hidden',
					'section' => 'dekanpro_section_upsell_button',
				),
			);
			// Docs link
			$options['section']['dekanpro_section_docs_button'] = array(
				'class'    => 'Dekanpro_Customizer_Control_Section_Pro',
				'title'    => esc_html__( 'Need Help?', 'dekanpro' ),
				'pro_url'  => esc_url_raw( 'https://dekan.pro/' ),
				'pro_text' => esc_html__( 'See the Articles', 'dekanpro' ),
				'priority' => 200,
			);

			$options['setting']['dekanpro_section_docs_heading'] = array(
				'control' => array(
					'type'    => 'hidden',
					'section' => 'dekanpro_section_docs_button',
				),
			);

			return $options;
		}
	}
endif;
new Dekanpro_Customizer_Buttons();

<?php
/**
 * Dekanpro Customizer sections and panels.
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

if ( ! class_exists( 'Dekanpro_Customizer_Sections' ) ) :
	/**
	 * Dekanpro Customizer sections and panels.
	 */
	class Dekanpro_Customizer_Sections {

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			/**
			 * Registers our custom panels in Customizer.
			 */
			add_filter( 'dekanpro_customizer_options', array( $this, 'register_panel' ) );
		}

		/**
		 * Registers our custom options in Customizer.
		 *
		 * @since 1.0.0
		 * @param array $options Array of customizer options.
		 */
		public function register_panel( $options ) {

			// Title - General Options
			$options['section']['dekanpro_section_general_group'] = array(
				'class'    => 'Dekanpro_Customizer_Control_Section_Group_Title',
				'title'    => esc_html__( 'General Options', 'dekanpro' ),
				'priority' => 1,
			);

			// General panel.
			$options['panel']['dekanpro_panel_general'] = array(
				'title'    => esc_html__( 'General Settings', 'dekanpro' ),
				'priority' => 2,
			);

			// Header panel.
			$options['panel']['dekanpro_panel_header'] = array(
				'title'    => esc_html__( 'Header', 'dekanpro' ),
				'priority' => 3,
			);

			// Footer panel.
			$options['panel']['dekanpro_panel_footer'] = array(
				'title'    => esc_html__( 'Footer', 'dekanpro' ),
				'priority' => 3,
			);

			// Blog settings.
			$options['panel']['dekanpro_panel_blog'] = array(
				'title'    => esc_html__( 'Blog', 'dekanpro' ),
				'priority' => 3,
			);

			// Title - Extra Options
			$options['section']['dekanpro_section_extra_group'] = array(
				'class'    => 'Dekanpro_Customizer_Control_Section_Group_Title',
				'title'    => esc_html__( 'Extra Options', 'dekanpro' ),
				'priority' => 4,
			);

			// Title - Core
			$options['section']['dekanpro_section_core_group'] = array(
				'class'    => 'Dekanpro_Customizer_Control_Section_Group_Title',
				'title'    => esc_html__( 'Core', 'dekanpro' ),
				'priority' => 7,
			);

			return $options;
		}
	}
endif;
new Dekanpro_Customizer_Sections();

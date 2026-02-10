<?php
/**
 * Dekanpro Category Colors section in Customizer.
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

if ( ! class_exists( 'Dekanpro_Customizer_Category_Colors' ) ) :
	/**
	 * Dekanpro Colors section in Customizer.
	 */
	class Dekanpro_Customizer_Category_Colors {

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
			$options['section']['dekanpro_section_category_colors'] = array(
				'title'    => esc_html__( 'Post Category Colors', 'dekanpro' ),
				'panel'    => 'dekanpro_panel_general',
				'priority' => 21,
			);

			// Category color.
			$categories = get_categories( array( 'hide_empty' => 1 ) );
			foreach ( $categories as $category ) {
				$options['setting'][ 'dekanpro_category_color_' . esc_attr( $category->term_id ) ] = array(
					'transport'         => 'refresh',
					'sanitize_callback' => 'dekanpro_sanitize_color',
					'control'           => array(
						'type'     => 'dekanpro-color',
						'label'    => sprintf( esc_html__( '%1$s Color', 'dekanpro' ), esc_html( $category->name ) ),
						'section'  => 'dekanpro_section_category_colors',
						'priority' => 10,
						'opacity'  => false,
					),
				);
			}

			return $options;
		}

	}
endif;
new Dekanpro_Customizer_Category_Colors();

<?php
/**
 * Dekanpro Advertisement Section Settings in Customizer.
 *
 * @package     DekanPro
 * @author      Peregrine Themes
 * @since       1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Dekanpro_Customizer_Advertisement' ) ) :
	/**
	 * Dekanpro Page Title Settings section in Customizer.
	 */
	class Dekanpro_Customizer_Advertisement {

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

			// Advertisement Section.
			$options['section']['dekanpro_section_advertisement'] = array(
				'title'    => esc_html__( 'Advertisements', 'dekanpro' ),
				'priority' => 4,
			);

			// Advertisement widgets.
			$options['setting']['dekanpro_ad_widgets'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_widget',
				'control'           => array(
					'type'       => 'dekanpro-widget',
					'label'      => esc_html__( 'Advertisement Widgets', 'dekanpro' ),
					'section'    => 'dekanpro_section_advertisement',
					'widgets'    => apply_filters(
						'dekanpro_main_ad_widgets',
						array(
							'advertisements' => array(
								'max_uses'      => 2,
								'display_areas' => array(
									'before_header'        => esc_html__( 'Before Header', 'dekanpro' ),
									'after_header'         => esc_html__( 'After Header', 'dekanpro' ),
									'before_post_archive'  => esc_html__( 'Before post archive', 'dekanpro' ),
									'random_post_archives' => esc_html__( 'Random post archives', 'dekanpro' ),
									'before_post_content'  => esc_html__( 'Before post content', 'dekanpro' ),
									'after_post_content'   => esc_html__( 'After post content', 'dekanpro' ),
									'before_footer'        => esc_html__( 'Before footer', 'dekanpro' ),
									'after_footer'         => esc_html__( 'After footer', 'dekanpro' ),
								),
							),
						)
					),
					'visibility' => array(
						'all'                => esc_html__( 'Show on All Devices', 'dekanpro' ),
						'hide-mobile'        => esc_html__( 'Hide on Mobile', 'dekanpro' ),
						'hide-tablet'        => esc_html__( 'Hide on Tablet', 'dekanpro' ),
						'hide-mobile-tablet' => esc_html__( 'Hide on Mobile and Tablet', 'dekanpro' ),
					),
				),
			);
			return $options;
		}
	}
endif;
new Dekanpro_Customizer_Advertisement();

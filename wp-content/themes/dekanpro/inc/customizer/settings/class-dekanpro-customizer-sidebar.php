<?php
/**
 * Dekanpro Sidebar section in Customizer.
 *
 * @package DekanPro
 * @author Peregrine Themes
 * @since   1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Dekanpro_Customizer_Sidebar' ) ) :

	/**
	 * Dekanpro Sidebar section in Customizer.
	 */
	class Dekanpro_Customizer_Sidebar {

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
			$options['section']['dekanpro_section_sidebar'] = array(
				'title'    => esc_html__( 'Sidebar', 'dekanpro' ),
				'priority' => 3,
			);

			// Default sidebar position.
			$options['setting']['dekanpro_sidebar_position'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_select',
				'control'           => array(
					'type'        => 'dekanpro-select',
					'section'     => 'dekanpro_section_sidebar',
					'label'       => esc_html__( 'Default Position', 'dekanpro' ),
					'description' => esc_html__( 'Choose default sidebar position layout. You can change this setting per page via metabox settings.', 'dekanpro' ),
					'choices'     => array(
						'no-sidebar'    => esc_html__( 'No Sidebar', 'dekanpro' ),
						'left-sidebar'  => esc_html__( 'Left Sidebar', 'dekanpro' ),
						'right-sidebar' => esc_html__( 'Right Sidebar', 'dekanpro' ),
					),
				),
			);

			// Single post sidebar position.
			$options['setting']['dekanpro_single_post_sidebar_position'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_select',
				'control'           => array(
					'type'        => 'dekanpro-select',
					'label'       => esc_html__( 'Single Post', 'dekanpro' ),
					'description' => esc_html__( 'Choose default sidebar position layout for single posts. You can change this setting per post via metabox settings.', 'dekanpro' ),
					'section'     => 'dekanpro_section_sidebar',
					'choices'     => array(
						'default'       => esc_html__( 'Default', 'dekanpro' ),
						'no-sidebar'    => esc_html__( 'No Sidebar', 'dekanpro' ),
						'left-sidebar'  => esc_html__( 'Left Sidebar', 'dekanpro' ),
						'right-sidebar' => esc_html__( 'Right Sidebar', 'dekanpro' ),
					),
				),
			);

			// Single page sidebar position.
			$options['setting']['dekanpro_single_page_sidebar_position'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_select',
				'control'           => array(
					'type'        => 'dekanpro-select',
					'label'       => esc_html__( 'Page', 'dekanpro' ),
					'description' => esc_html__( 'Choose default sidebar position layout for pages. You can change this setting per page via metabox settings.', 'dekanpro' ),
					'section'     => 'dekanpro_section_sidebar',
					'choices'     => array(
						'default'       => esc_html__( 'Default', 'dekanpro' ),
						'no-sidebar'    => esc_html__( 'No Sidebar', 'dekanpro' ),
						'left-sidebar'  => esc_html__( 'Left Sidebar', 'dekanpro' ),
						'right-sidebar' => esc_html__( 'Right Sidebar', 'dekanpro' ),
					),
				),
			);

			// Archive sidebar position.
			$options['setting']['dekanpro_archive_sidebar_position'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_select',
				'control'           => array(
					'type'        => 'dekanpro-select',
					'label'       => esc_html__( 'Archives & Search', 'dekanpro' ),
					'description' => esc_html__( 'Choose default sidebar position layout for archives and search results.', 'dekanpro' ),
					'section'     => 'dekanpro_section_sidebar',
					'choices'     => array(
						'default'       => esc_html__( 'Default', 'dekanpro' ),
						'no-sidebar'    => esc_html__( 'No Sidebar', 'dekanpro' ),
						'left-sidebar'  => esc_html__( 'Left Sidebar', 'dekanpro' ),
						'right-sidebar' => esc_html__( 'Right Sidebar', 'dekanpro' ),
					),
				),
			);

			// Sidebar options heading.
			$options['setting']['dekanpro_sidebar_options_heading'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'    => 'dekanpro-heading',
					'label'   => esc_html__( 'Options', 'dekanpro' ),
					'section' => 'dekanpro_section_sidebar',
				),
			);

			// Sidebar width.
			$options['setting']['dekanpro_sidebar_width'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_range',
				'control'           => array(
					'type'        => 'dekanpro-range',
					'section'     => 'dekanpro_section_sidebar',
					'label'       => esc_html__( 'Sidebar Width', 'dekanpro' ),
					'description' => esc_html__( 'Change your sidebar width.', 'dekanpro' ),
					'min'         => 15,
					'max'         => 50,
					'step'        => 1,
					'unit'        => '%',
					'required'    => array(
						array(
							'control'  => 'dekanpro_sidebar_options_heading',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Sticky sidebar.
			$options['setting']['dekanpro_sidebar_sticky'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_select',
				'control'           => array(
					'type'        => 'dekanpro-select',
					'section'     => 'dekanpro_section_sidebar',
					'label'       => esc_html__( 'Sticky Sidebar', 'dekanpro' ),
					'description' => esc_html__( 'Stick sidebar when scrolling.', 'dekanpro' ),
					'choices'     => array(
						''        => esc_html__( 'Disable', 'dekanpro' ),
						'sidebar' => esc_html__( 'Stick first widget', 'dekanpro' ),
					),
					'required'    => array(
						array(
							'control'  => 'dekanpro_sidebar_options_heading',
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

new Dekanpro_Customizer_Sidebar();

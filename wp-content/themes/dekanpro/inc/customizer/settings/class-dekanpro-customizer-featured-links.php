<?php
/**
 * Dekanpro Featured Links Section Settings section in Customizer.
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

if ( ! class_exists( 'Dekanpro_Customizer_Featured_Links' ) ) :
	/**
	 * Dekanpro Page Title Settings section in Customizer.
	 */
	class Dekanpro_Customizer_Featured_Links {

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

			// Featured links Section.
			$options['section']['dekanpro_section_featured_links'] = array(
				'title'    => esc_html__( 'Featured Items', 'dekanpro' ),
				'priority' => 4,
			);

			// Featured links enable.
			$options['setting']['dekanpro_enable_featured_links'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'    => 'dekanpro-toggle',
					'section' => 'dekanpro_section_featured_links',
					'label'   => esc_html__( 'Enable featured items section', 'dekanpro' ),
				),
			);

			// Title.
			$options['setting']['dekanpro_featured_links_title'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
				'control'           => array(
					'type'     => 'dekanpro-text',
					'section'  => 'dekanpro_section_featured_links',
					'label'    => esc_html__( 'Title', 'dekanpro' ),
					'required' => array(
						array(
							'control'  => 'dekanpro_enable_featured_links',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			$options['setting']['dekanpro_featured_links'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_repeater_sanitize',
				'control'           => array(
					'type'         => 'dekanpro-repeater',
					'label'        => esc_html__( 'Featured Items', 'dekanpro' ),
					'section'      => 'dekanpro_section_featured_links',
					'item_name'    => esc_html__( 'Featured Link', 'dekanpro' ),
					'title_format' => esc_html__( '[live_title]', 'dekanpro' ), // [live_title]
					'add_text'     => esc_html__( 'Add new Feature', 'dekanpro' ),
					'max_item'     => 3, // 3 Maximum item can add,
					'limited_msg'  => wp_kses_post( __( 'Свяжитесь с <a target="_blank" href="https://dekan.pro/">DekanPro</a> для добавления дополнительных функций!', 'dekanpro' ) ),
					'fields'       => array(
						'link'  => array(
							'title' => esc_html__( 'Select feature link', 'dekanpro' ),
							'type'  => 'link',
						),

						'image' => array(
							'title' => esc_html__( 'Image', 'dekanpro' ),
							'type'  => 'media',
						),
					),
					'required'     => array(
						array(
							'control'  => 'dekanpro_enable_featured_links',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#featured_links',
					'render_callback'     => 'dekanpro_blog_featured_links',
					'container_inclusive' => true,
					'fallback_refresh'    => true,
				),
			);

			// Featured links display on.
			$options['setting']['dekanpro_featured_links_enable_on'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_no_sanitize',
				'control'           => array(
					'type'        => 'dekanpro-checkbox-group',
					'label'       => esc_html__( 'Enable On: ', 'dekanpro' ),
					'description' => esc_html__( 'Choose on which pages you want to enable Featured links. ', 'dekanpro' ),
					'section'     => 'dekanpro_section_featured_links',
					'choices'     => array(
						'home'       => array(
							'title' => esc_html__( 'Home Page', 'dekanpro' ),
						),
						'posts_page' => array(
							'title' => esc_html__( 'Blog / Posts Page', 'dekanpro' ),
						),
					),
					'required'    => array(
						array(
							'control'  => 'dekanpro_enable_featured_links',
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
new Dekanpro_Customizer_Featured_Links();

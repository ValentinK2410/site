<?php
/**
 * Dekanpro Ticker section in Customizer.
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

if ( ! class_exists( 'Dekanpro_Customizer_Ticker' ) ) :
	/**
	 * Dekanpro Ticker section in Customizer.
	 */
	class Dekanpro_Customizer_Ticker {

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
			// Ticker News Section.
			$options['section']['dekanpro_section_ticker'] = array(
				'title'    => esc_html__( 'Ticker News', 'dekanpro' ),
				'priority' => 4,
			);

			// Ticker News enable.
			$options['setting']['dekanpro_enable_ticker'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'    => 'dekanpro-toggle',
					'section' => 'dekanpro_section_ticker',
					'label'   => esc_html__( 'Enable Ticker News Section', 'dekanpro' ),
				),
			);

			// Title.
			$options['setting']['dekanpro_ticker_title'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
				'control'           => array(
					'type'     => 'dekanpro-text',
					'section'  => 'dekanpro_section_ticker',
					'label'    => esc_html__( 'Title', 'dekanpro' ),
					'required' => array(
						array(
							'control'  => 'dekanpro_enable_ticker',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Ticker News display on.
			$options['setting']['dekanpro_ticker_enable_on'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_no_sanitize',
				'control'           => array(
					'type'        => 'dekanpro-checkbox-group',
					'label'       => esc_html__( 'Enable On: ', 'dekanpro' ),
					'description' => esc_html__( 'Choose on which pages you want to enable Ticker News. ', 'dekanpro' ),
					'section'     => 'dekanpro_section_ticker',
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
							'control'  => 'dekanpro_enable_ticker',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Post Settings heading.
			$options['setting']['dekanpro_ticker_posts'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'     => 'dekanpro-heading',
					'section'  => 'dekanpro_section_ticker',
					'label'    => esc_html__( 'Post Settings', 'dekanpro' ),
					'required' => array(
						array(
							'control'  => 'dekanpro_enable_ticker',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Post count.
			$options['setting']['dekanpro_ticker_post_number'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_range',
				'control'           => array(
					'type'        => 'dekanpro-range',
					'section'     => 'dekanpro_section_ticker',
					'label'       => esc_html__( 'Post Number', 'dekanpro' ),
					'description' => esc_html__( 'Set the number of visible posts.', 'dekanpro' ),
					'min'         => 1,
					'max'         => 500,
					'step'        => 1,
					'unit'        => '',
					'required'    => array(
						array(
							'control'  => 'dekanpro_enable_ticker',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'dekanpro_ticker_posts',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Post category.
			$options['setting']['dekanpro_ticker_category'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_select',
				'control'           => array(
					'type'        => 'dekanpro-select',
					'section'     => 'dekanpro_section_ticker',
					'label'       => esc_html__( 'Category', 'dekanpro' ),
					'description' => esc_html__( 'Display posts from selected category only. Leave empty to include all.', 'dekanpro' ),
					'is_select2'  => true,
					'data_source' => 'category',
					'multiple'    => true,
					'required'    => array(
						array(
							'control'  => 'dekanpro_enable_ticker',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'dekanpro_ticker_posts',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Ticker Slider Elements.
			$options['setting']['dekanpro_ticker_elements'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_sortable',
				'control'           => array(
					'type'        => 'dekanpro-sortable',
					'section'     => 'dekanpro_section_ticker',
					'label'       => esc_html__( 'Post Elements', 'dekanpro' ),
					'description' => esc_html__( 'Set order and visibility for post elements.', 'dekanpro' ),
					'sortable'    => false,
					'choices'     => array(
						// 'thumbnail' => esc_html__( 'Thumbnail', 'dekanpro' ),
						'meta' => esc_html__( 'Post Details', 'dekanpro' ),
					),
					'required'    => array(
						array(
							'control'  => 'dekanpro_enable_ticker',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'dekanpro_ticker_posts',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#ticker',
					'render_callback'     => 'dekanpro_blog_ticker',
					'container_inclusive' => true,
					'fallback_refresh'    => true,
				),
			);

			return $options;
		}
	}
endif;
new Dekanpro_Customizer_Ticker();

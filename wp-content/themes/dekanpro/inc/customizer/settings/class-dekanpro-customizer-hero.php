<?php
/**
 * Dekanpro Hero Section Settings section in Customizer.
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

if ( ! class_exists( 'Dekanpro_Customizer_Hero' ) ) :
	/**
	 * Dekanpro Page Title Settings section in Customizer.
	 */
	class Dekanpro_Customizer_Hero {

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

			// Hero Section.
			$options['section']['dekanpro_section_hero'] = array(
				'title'    => esc_html__( 'Hero', 'dekanpro' ),
				'priority' => 4,
			);

			// Hero enable.
			$options['setting']['dekanpro_enable_hero'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'    => 'dekanpro-toggle',
					'section' => 'dekanpro_section_hero',
					'label'   => esc_html__( 'Enable Hero Section', 'dekanpro' ),
				),
			);

			// Hero display on.
			$options['setting']['dekanpro_hero_enable_on'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_no_sanitize',
				'control'           => array(
					'type'        => 'dekanpro-checkbox-group',
					'label'       => esc_html__( 'Enable On: ', 'dekanpro' ),
					'description' => esc_html__( 'Choose on which pages you want to enable Hero. ', 'dekanpro' ),
					'section'     => 'dekanpro_section_hero',
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
							'control'  => 'dekanpro_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Hero Type.
			$options['setting']['dekanpro_hero_type'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_select',
				'control'           => array(
					'type'        => 'dekanpro-select',
					'section'     => 'dekanpro_section_hero',
					'label'       => esc_html__( 'Type', 'dekanpro' ),
					'description' => esc_html__( 'Choose hero style type.', 'dekanpro' ),
					'choices'     => array(
						'horizontal-slider' => esc_html__( 'Slider Horizontal', 'dekanpro' ),
					),
					'required'    => array(
						array(
							'control'  => 'dekanpro_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Post Settings heading.
			$options['setting']['dekanpro_hero_slider_posts'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'     => 'dekanpro-heading',
					'section'  => 'dekanpro_section_hero',
					'label'    => esc_html__( 'Post Settings', 'dekanpro' ),
					'required' => array(
						array(
							'control'  => 'dekanpro_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Post count.
			$options['setting']['dekanpro_hero_slider_post_number'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_range',
				'control'           => array(
					'type'        => 'dekanpro-range',
					'section'     => 'dekanpro_section_hero',
					'label'       => esc_html__( 'Post Number', 'dekanpro' ),
					'description' => esc_html__( 'Set the number of visible posts.', 'dekanpro' ),
					'min'         => 1,
					'max'         => 50,
					'step'        => 1,
					'unit'        => '',
					'required'    => array(
						array(
							'control'  => 'dekanpro_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'dekanpro_hero_slider_posts',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#hero',
					'render_callback'     => 'dekanpro_blog_hero',
					'container_inclusive' => true,
					'fallback_refresh'    => true,
				),
			);

			// Post category.
			$options['setting']['dekanpro_hero_slider_category'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_select',
				'control'           => array(
					'type'        => 'dekanpro-select',
					'section'     => 'dekanpro_section_hero',
					'label'       => esc_html__( 'Category', 'dekanpro' ),
					'description' => esc_html__( 'Display posts from selected category only. Leave empty to include all.', 'dekanpro' ),
					'is_select2'  => true,
					'data_source' => 'category',
					'multiple'    => true,
					'required'    => array(
						array(
							'control'  => 'dekanpro_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'dekanpro_hero_slider_posts',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Hero Slider heading.
			$options['setting']['dekanpro_hero_slider'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'     => 'dekanpro-heading',
					'section'  => 'dekanpro_section_hero',
					'label'    => esc_html__( 'Style', 'dekanpro' ),
					'required' => array(
						array(
							'control'  => 'dekanpro_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Hero Slider Elements.
			$options['setting']['dekanpro_hero_slider_elements'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_sortable',
				'control'           => array(
					'type'        => 'dekanpro-sortable',
					'section'     => 'dekanpro_section_hero',
					'label'       => esc_html__( 'Post Elements', 'dekanpro' ),
					'description' => esc_html__( 'Set order and visibility for post elements.', 'dekanpro' ),
					'sortable'    => false,
					'choices'     => array(
						'category'  => esc_html__( 'Categories', 'dekanpro' ),
						'meta'      => esc_html__( 'Post Details', 'dekanpro' ),
						'read_more' => esc_html__( 'Continue Reading', 'dekanpro' ),
					),
					'required'    => array(
						array(
							'control'  => 'dekanpro_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'dekanpro_hero_slider',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#hero',
					'render_callback'     => 'dekanpro_blog_hero',
					'container_inclusive' => true,
					'fallback_refresh'    => true,
				),
			);

			// Hero Slider Meta/Post Details.
			$options['setting']['dekanpro_hero_entry_meta_elements'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_sortable',
				'control'           => array(
					'type'        => 'dekanpro-sortable',
					'section'     => 'dekanpro_section_hero',
					'label'       => esc_html__( 'Post Meta', 'dekanpro' ),
					'description' => esc_html__( 'Set order and visibility for post meta details.', 'dekanpro' ),
					'choices'     => array(
						'author'   => esc_html__( 'Author', 'dekanpro' ),
						'date'     => esc_html__( 'Publish Date', 'dekanpro' ),
						'comments' => esc_html__( 'Comments', 'dekanpro' ),
					),
					'required'    => array(
						array(
							'control'  => 'dekanpro_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'dekanpro_hero_slider',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#hero',
					'render_callback'     => 'dekanpro_blog_hero',
					'container_inclusive' => true,
					'fallback_refresh'    => true,
				),
			);

			// Continue Reading.
			$options['setting']['dekanpro_hero_slider_read_more'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_text_field',
				'control'           => array(
					'type'        => 'dekanpro-text',
					'section'     => 'dekanpro_section_hero',
					'label'       => esc_html__( 'Continue Reading', 'dekanpro' ),
					'description' => esc_html__( 'Change Continue Reading Text.', 'dekanpro' ),
					'required'    => array(
						array(
							'control'  => 'dekanpro_enable_hero',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'dekanpro_hero_slider',
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
new Dekanpro_Customizer_Hero();

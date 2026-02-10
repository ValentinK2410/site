<?php
/**
 * Dekanpro PYML section in Customizer.
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

if ( ! class_exists( 'Dekanpro_Customizer_PYML' ) ) :
	/**
	 * Dekanpro PYML section in Customizer.
	 */
	class Dekanpro_Customizer_PYML {

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
			// Posts You Might Like Section.
			$options['section']['dekanpro_section_pyml'] = array(
				'title'    => esc_html__( 'Posts You Might Like', 'dekanpro' ),
				'priority' => 5,
			);

			// Posts You Might Like enable.
			$options['setting']['dekanpro_enable_pyml'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'    => 'dekanpro-toggle',
					'section' => 'dekanpro_section_pyml',
					'label'   => esc_html__( 'Enable Posts You Might Like Section', 'dekanpro' ),
				),
			);

			// Title.
			$options['setting']['dekanpro_pyml_title'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'sanitize_text_field',
				'control'           => array(
					'type'     => 'dekanpro-text',
					'section'  => 'dekanpro_section_pyml',
					'label'    => esc_html__( 'Title', 'dekanpro' ),
					'required' => array(
						array(
							'control'  => 'dekanpro_enable_pyml',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Posts You Might Like display on.
			$options['setting']['dekanpro_pyml_enable_on'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_no_sanitize',
				'control'           => array(
					'type'        => 'dekanpro-checkbox-group',
					'label'       => esc_html__( 'Enable On: ', 'dekanpro' ),
					'description' => esc_html__( 'Choose on which pages you want to enable Posts You Might Like. ', 'dekanpro' ),
					'section'     => 'dekanpro_section_pyml',
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
							'control'  => 'dekanpro_enable_pyml',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// PYML heading.
			$options['setting']['dekanpro_pyml_style'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'     => 'dekanpro-heading',
					'section'  => 'dekanpro_section_pyml',
					'label'    => esc_html__( 'Style', 'dekanpro' ),
					'required' => array(
						array(
							'control'  => 'dekanpro_enable_pyml',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// PYML Elements.
			$options['setting']['dekanpro_pyml_elements'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_sortable',
				'control'           => array(
					'type'        => 'dekanpro-sortable',
					'section'     => 'dekanpro_section_pyml',
					'label'       => esc_html__( 'Post Elements', 'dekanpro' ),
					'description' => esc_html__( 'Set order and visibility for post elements.', 'dekanpro' ),
					'sortable'    => false,
					'choices'     => array(
						'category' => esc_html__( 'Categories', 'dekanpro' ),
						'meta'     => esc_html__( 'Post Details', 'dekanpro' ),
					),
					'required'    => array(
						array(
							'control'  => 'dekanpro_enable_pyml',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'dekanpro_pyml_style',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#pyml',
					'render_callback'     => 'dekanpro_blog_pyml',
					'container_inclusive' => true,
					'fallback_refresh'    => true,
				),
			);

			// Post Settings heading.
			$options['setting']['dekanpro_pyml_posts'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'     => 'dekanpro-heading',
					'section'  => 'dekanpro_section_pyml',
					'label'    => esc_html__( 'Post Settings', 'dekanpro' ),
					'required' => array(
						array(
							'control'  => 'dekanpro_enable_pyml',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
			);

			// Post count.
			$options['setting']['dekanpro_pyml_post_number'] = array(
				'transport'         => 'postMessage',
				'sanitize_callback' => 'dekanpro_sanitize_range',
				'control'           => array(
					'type'        => 'dekanpro-range',
					'section'     => 'dekanpro_section_pyml',
					'label'       => esc_html__( 'Post Number', 'dekanpro' ),
					'description' => esc_html__( 'Set the number of visible posts.', 'dekanpro' ),
					'min'         => 1,
					'max'         => 4,
					'step'        => 1,
					'unit'        => '',
					'required'    => array(
						array(
							'control'  => 'dekanpro_enable_pyml',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'dekanpro_pyml_posts',
							'value'    => true,
							'operator' => '==',
						),
					),
				),
				'partial'           => array(
					'selector'            => '#pyml',
					'render_callback'     => 'dekanpro_blog_pyml',
					'container_inclusive' => true,
					'fallback_refresh'    => true,
				),
			);

			// Post category.
			$options['setting']['dekanpro_pyml_category'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_select',
				'control'           => array(
					'type'        => 'dekanpro-select',
					'section'     => 'dekanpro_section_pyml',
					'label'       => esc_html__( 'Category', 'dekanpro' ),
					'description' => esc_html__( 'Display posts from selected category only. Leave empty to include all.', 'dekanpro' ),
					'is_select2'  => true,
					'data_source' => 'category',
					'multiple'    => true,
					'required'    => array(
						array(
							'control'  => 'dekanpro_enable_pyml',
							'value'    => true,
							'operator' => '==',
						),
						array(
							'control'  => 'dekanpro_pyml_posts',
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
new Dekanpro_Customizer_PYML();

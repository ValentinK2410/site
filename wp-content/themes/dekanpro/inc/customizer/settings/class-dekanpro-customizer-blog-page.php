<?php
/**
 * Dekanpro Blog » Blog Page / Archive section in Customizer.
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

if ( ! class_exists( 'Dekanpro_Customizer_Blog_Page' ) ) :
	/**
	 * Dekanpro Blog » Blog Page / Archive section in Customizer.
	 */
	class Dekanpro_Customizer_Blog_Page {

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
			$options['section']['dekanpro_section_blog_page'] = array(
				'title' => esc_html__( 'Blog Page / Archive', 'dekanpro' ),
				'panel' => 'dekanpro_panel_blog',
			);

			// Layout.
			$options['setting']['dekanpro_blog_layout'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_select',
				'control'           => array(
					'type'        => 'dekanpro-select',
					'label'       => esc_html__( 'Layout', 'dekanpro' ),
					'description' => esc_html__( 'Choose blog layout.', 'dekanpro' ),
					'section'     => 'dekanpro_section_blog_page',
					'choices'     => array(
						'blog-horizontal' => esc_html__( 'Horizontal', 'dekanpro' ),
					),
				),
			);

			$_image_sizes = dekanpro_get_image_sizes();
			$size_choices = array();

			if ( ! empty( $_image_sizes ) ) {
				foreach ( $_image_sizes as $key => $value ) {
					$name = ucwords( str_replace( array( '-', '_' ), ' ', $key ) );

					$size_choices[ $key ] = $name;

					if ( $value['width'] || $value['height'] ) {
						$size_choices[ $key ] .= ' (' . $value['width'] . 'x' . $value['height'] . ')';
					}
				}
			}

			// Featured Image Size.
			$options['setting']['dekanpro_blog_image_size'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_select',
				'control'           => array(
					'type'    => 'dekanpro-select',
					'label'   => esc_html__( 'Featured Image Size', 'dekanpro' ),
					'section' => 'dekanpro_section_blog_page',
					'choices' => $size_choices,
				),
			);

			// Read more.
			$options['setting']['dekanpro_blog_read_more'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_text_field',
				'control'           => array(
					'type'        => 'dekanpro-text',
					'section'     => 'dekanpro_section_blog_page',
					'label'       => esc_html__( 'Read More', 'dekanpro' ),
					'description' => esc_html__( 'Change Read More Text.', 'dekanpro' ),
				),
			);

			// Meta/Post Details Layout.
			$options['setting']['dekanpro_blog_entry_meta_elements'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_sortable',
				'control'           => array(
					'type'        => 'dekanpro-sortable',
					'section'     => 'dekanpro_section_blog_page',
					'label'       => esc_html__( 'Post Meta', 'dekanpro' ),
					'description' => esc_html__( 'Set order and visibility for post meta details.', 'dekanpro' ),
					'choices'     => array(
						'author'   => esc_html__( 'Author', 'dekanpro' ),
						'date'     => esc_html__( 'Publish Date', 'dekanpro' ),
						'comments' => esc_html__( 'Comments', 'dekanpro' ),
						'tag'      => esc_html__( 'Tags', 'dekanpro' ),
					),
				),
			);

			// Post Categories.
			$options['setting']['dekanpro_blog_horizontal_post_categories'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'        => 'dekanpro-toggle',
					'label'       => esc_html__( 'Show Post Categories', 'dekanpro' ),
					'description' => esc_html__( 'A list of categories the post belongs to. Displayed above post title.', 'dekanpro' ),
					'section'     => 'dekanpro_section_blog_page',
					'required'    => array(
						array(
							'control'  => 'dekanpro_blog_layout',
							'value'    => 'blog-horizontal',
							'operator' => '==',
						),
					),
				),
			);

			// Read More Button.
			$options['setting']['dekanpro_blog_horizontal_read_more'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'     => 'dekanpro-toggle',
					'label'    => esc_html__( 'Show Read More Button', 'dekanpro' ),
					'section'  => 'dekanpro_section_blog_page',
					'required' => array(
						array(
							'control'  => 'dekanpro_blog_layout',
							'value'    => 'blog-horizontal',
							'operator' => '==',
						),
					),
				),
			);

			// Meta Author image.
			$options['setting']['dekanpro_entry_meta_icons'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'    => 'dekanpro-toggle',
					'section' => 'dekanpro_section_blog_page',
					'label'   => esc_html__( 'Show avatar and icons in post meta', 'dekanpro' ),
				),
			);

			// Excerpt Length.
			$options['setting']['dekanpro_excerpt_length'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_range',
				'control'           => array(
					'type'        => 'dekanpro-range',
					'section'     => 'dekanpro_section_blog_page',
					'label'       => esc_html__( 'Excerpt Length', 'dekanpro' ),
					'description' => esc_html__( 'Number of words displayed in the excerpt.', 'dekanpro' ),
					'min'         => 0,
					'max'         => 100,
					'step'        => 1,
					'unit'        => '',
					'responsive'  => false,
				),
			);

			// Excerpt more.
			$options['setting']['dekanpro_excerpt_more'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_text_field',
				'control'           => array(
					'type'        => 'dekanpro-text',
					'section'     => 'dekanpro_section_blog_page',
					'label'       => esc_html__( 'Excerpt More', 'dekanpro' ),
					'description' => esc_html__( 'What to append to excerpt if the text is cut.', 'dekanpro' ),
				),
			);

			return $options;
		}
	}
endif;

new Dekanpro_Customizer_Blog_Page();

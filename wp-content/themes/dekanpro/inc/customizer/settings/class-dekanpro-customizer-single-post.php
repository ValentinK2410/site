<?php
/**
 * Dekanpro Blog - Single Post section in Customizer.
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

if ( ! class_exists( 'Dekanpro_Customizer_Single_Post' ) ) :
	/**
	 * Dekanpro Blog - Single Post section in Customizer.
	 */
	class Dekanpro_Customizer_Single_Post {

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
			$options['section']['dekanpro_section_blog_single_post'] = array(
				'title'    => esc_html__( 'Single Post', 'dekanpro' ),
				'panel'    => 'dekanpro_panel_blog',
				'priority' => 20,
			);

			$options['setting']['dekanpro_single_post_elements'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_sortable',
				'control'           => array(
					'type'        => 'dekanpro-sortable',
					'section'     => 'dekanpro_section_blog_single_post',
					'label'       => esc_html__( 'Post Elements', 'dekanpro' ),
					'description' => esc_html__( 'Set visibility of post elements.', 'dekanpro' ),
					'sortable'    => false,
					'choices'     => array(
						'thumb'          => esc_html__( 'Featured Image', 'dekanpro' ),
						'category'       => esc_html__( 'Post Categories', 'dekanpro' ),
						'tags'           => esc_html__( 'Post Tags', 'dekanpro' ),
						'last-updated'   => esc_html__( 'Last Updated Date', 'dekanpro' ),
						'about-author'   => esc_html__( 'About Author Box', 'dekanpro' ),
						'prev-next-post' => esc_html__( 'Next/Prev Post Links', 'dekanpro' ),
					),
				),
			);

			// Meta/Post Details Layout.
			$options['setting']['dekanpro_single_post_meta_elements'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_sortable',
				'control'           => array(
					'type'        => 'dekanpro-sortable',
					'label'       => esc_html__( 'Post Meta', 'dekanpro' ),
					'description' => esc_html__( 'Set order and visibility for post meta details.', 'dekanpro' ),
					'section'     => 'dekanpro_section_blog_single_post',
					'choices'     => array(
						'author'   => esc_html__( 'Author', 'dekanpro' ),
						'date'     => esc_html__( 'Publish Date', 'dekanpro' ),
						'comments' => esc_html__( 'Comments', 'dekanpro' ),
						'category' => esc_html__( 'Categories', 'dekanpro' ),
					),
				),
			);

			// Meta icons.
			$options['setting']['dekanpro_single_entry_meta_icons'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'    => 'dekanpro-toggle',
					'section' => 'dekanpro_section_blog_single_post',
					'label'   => esc_html__( 'Show avatar and icons in post meta', 'dekanpro' ),
				),
			);

			// Toggle Comments.
			$options['setting']['dekanpro_single_toggle_comments'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'dekanpro_sanitize_toggle',
				'control'           => array(
					'type'        => 'dekanpro-toggle',
					'label'       => esc_html__( 'Show Toggle Comments', 'dekanpro' ),
					'description' => esc_html__( 'Hide comments and comment form behind a toggle button. ', 'dekanpro' ),
					'section'     => 'dekanpro_section_blog_single_post',
				),
			);

			return $options;
		}
	}
endif;
new Dekanpro_Customizer_Single_Post();

<?php

/**
 * Dekanpro Options Class.
 *
 * @package  Dekanpro
 * @author   Peregrine Themes
 * @since    1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Dekanpro_Options' ) ) :

	/**
	 * Dekanpro Options Class.
	 */
	class Dekanpro_Options {

		/**
		 * Singleton instance of the class.
		 *
		 * @since 1.0.0
		 * @var object
		 */
		private static $instance;

		/**
		 * Options variable.
		 *
		 * @since 1.0.0
		 * @var mixed $options
		 */
		private static $options;

		/**
		 * Main Dekanpro_Options Instance.
		 *
		 * @since 1.0.0
		 * @return Dekanpro_Options
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Dekanpro_Options ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Refresh options.
			add_action( 'after_setup_theme', array( $this, 'refresh' ) );
		}

		/**
		 * Set default option values.
		 *
		 * @since  1.0.0
		 * @return array Default values.
		 */
		public function get_defaults() {

			$categories                        = get_categories( array( 'hide_empty' => 1 ) );
			$dekanpro_categories_color_options = array();
			foreach ( $categories as $category ) {
				$dekanpro_categories_color_options[ 'dekanpro_category_color_' . $category->term_id ] = '#F43676';
			}

			$defaults = array(

				/**
				 * General Settings.
				 */

				// Layout.
				'dekanpro_site_layout'                     => 'fw-contained',
				'dekanpro_container_width'                 => 1480,

				// Base Colors.
				'dekanpro_accent_color'                    => '#F43676',
				'dekanpro_dark_mode'                       => false,
				'dekanpro_body_animation'                  => '1',
				'dekanpro_content_text_color'              => '#002050',
				'dekanpro_headings_color'                  => '#302D55',
				'dekanpro_content_link_hover_color'        => '#302D55',
				'dekanpro_body_background_heading'         => true,
				'dekanpro_content_background_heading'      => true,
				'dekanpro_boxed_content_background_color'  => '#FFFFFF',
				'dekanpro_scroll_top_visibility'           => 'all',

				// Base Typography.
				'dekanpro_html_base_font_size'             => array(
					'desktop' => 62.5,
					'tablet'  => 53,
					'mobile'  => 50,
				),
				'dekanpro_font_smoothing'                  => true,
				'dekanpro_typography_body_heading'         => false,
				'dekanpro_typography_headings_heading'     => false,
				'dekanpro_body_font'                       => dekanpro_typography_defaults(
					array(
						'font-family'         => 'Be Vietnam Pro',
						'font-weight'         => 400,
						'font-size-desktop'   => '1.7',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.75',
					)
				),
				'dekanpro_headings_font'                   => dekanpro_typography_defaults(
					array(
						'font-family'     => 'Be Vietnam Pro',
						'font-weight'     => 700,
						'font-style'      => 'normal',
						'text-transform'  => 'none',
						'text-decoration' => 'none',
					)
				),
				'dekanpro_h1_font'                         => dekanpro_typography_defaults(
					array(
						'font-weight'         => 700,
						'font-size-desktop'   => '4',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.4',
					)
				),
				'dekanpro_h2_font'                         => dekanpro_typography_defaults(
					array(
						'font-weight'         => 700,
						'font-size-desktop'   => '3.6',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.4',
					)
				),
				'dekanpro_h3_font'                         => dekanpro_typography_defaults(
					array(
						'font-weight'         => 700,
						'font-size-desktop'   => '2.8',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.4',
					)
				),
				'dekanpro_h4_font'                         => dekanpro_typography_defaults(
					array(
						'font-weight'         => 700,
						'font-size-desktop'   => '2.4',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.4',
					)
				),
				'dekanpro_h5_font'                         => dekanpro_typography_defaults(
					array(
						'font-weight'         => 700,
						'font-size-desktop'   => '2',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.4',
					)
				),
				'dekanpro_h6_font'                         => dekanpro_typography_defaults(
					array(
						'font-weight'         => 600,
						'font-size-desktop'   => '1.8',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.72',
					)
				),
				'dekanpro_heading_em_font'                 => dekanpro_typography_defaults(
					array(
						'font-family' => 'Playfair Display',
						'font-weight' => 'inherit',
						'font-style'  => 'italic',
					)
				),
				'dekanpro_section_heading_style'           => '1',
				'dekanpro_footer_widget_title_font_size'   => array(
					'desktop' => 2,
					'unit'    => 'rem',
				),

				// Primary Button.
				'dekanpro_primary_button_heading'          => false,
				'dekanpro_primary_button_bg_color'         => '',
				'dekanpro_primary_button_hover_bg_color'   => '',
				'dekanpro_primary_button_text_color'       => '#fff',
				'dekanpro_primary_button_hover_text_color' => '#fff',
				'dekanpro_primary_button_border_radius'    => array(
					'top-left'     => '0.8',
					'top-right'    => '0.8',
					'bottom-right' => '0.8',
					'bottom-left'  => '0.8',
					'unit'         => 'rem',
				),
				'dekanpro_primary_button_border_width'     => 0.1,
				'dekanpro_primary_button_border_color'     => 'rgba(0, 0, 0, 0.12)',
				'dekanpro_primary_button_hover_border_color' => 'rgba(0, 0, 0, 0.12)',
				'dekanpro_primary_button_typography'       => dekanpro_typography_defaults(
					array(
						'font-family'         => 'Be Vietnam Pro',
						'font-weight'         => 500,
						'font-size-desktop'   => '1.8',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '',
					)
				),

				// Secondary Button.
				'dekanpro_secondary_button_heading'        => false,
				'dekanpro_secondary_button_bg_color'       => '#302D55',
				'dekanpro_secondary_button_hover_bg_color' => '#002050',
				'dekanpro_secondary_button_text_color'     => '#FFFFFF',
				'dekanpro_secondary_button_hover_text_color' => '#FFFFFF',
				'dekanpro_secondary_button_border_radius'  => array(
					'top-left'     => '',
					'top-right'    => '',
					'bottom-right' => '',
					'bottom-left'  => '',
					'unit'         => 'rem',
				),
				'dekanpro_secondary_button_border_width'   => .1,
				'dekanpro_secondary_button_border_color'   => 'rgba(0, 0, 0, 0.12)',
				'dekanpro_secondary_button_hover_border_color' => 'rgba(0, 0, 0, 0.12)',
				'dekanpro_secondary_button_typography'     => dekanpro_typography_defaults(
					array(
						'font-family'         => 'Be Vietnam Pro',
						'font-weight'         => 500,
						'font-size-desktop'   => '1.8',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.6',
					)
				),

				// Text button.
				'dekanpro_text_button_heading'             => false,
				'dekanpro_text_button_text_color'          => '#302D55',
				'dekanpro_text_button_hover_text_color'    => '',
				'dekanpro_text_button_typography'          => dekanpro_typography_defaults(
					array(
						'font-family'         => 'Be Vietnam Pro',
						'font-weight'         => 500,
						'font-size-desktop'   => '1.6',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.5',
					)
				),

				// Misc Settings.
				'dekanpro_enable_schema'                   => true,
				'dekanpro_custom_input_style'              => true,
				'dekanpro_preloader_heading'               => false,
				'dekanpro_preloader'                       => false,
				'dekanpro_preloader_style'                 => '1',
				'dekanpro_preloader_visibility'            => 'all',
				'dekanpro_scroll_top_heading'              => false,
				'dekanpro_scroll_top'                      => true,
				'dekanpro_scroll_top_visibility'           => 'all',
				'dekanpro_cursor_dot_heading'              => false,
				'dekanpro_cursor_dot'                      => false,

				/**
				 * Logos & Site Title.
				 */
				'dekanpro_logo_default_retina'             => '',
				'dekanpro_logo_max_height'                 => array(
					'desktop' => 45,
				),
				'dekanpro_logo_margin'                     => array(
					'desktop' => array(
						'top'    => 27,
						'right'  => 10,
						'bottom' => 27,
						'left'   => 10,
					),
					'tablet'  => array(
						'top'    => 25,
						'right'  => 1,
						'bottom' => 25,
						'left'   => 0,
					),
					'mobile'  => array(
						'top'    => '',
						'right'  => '',
						'bottom' => '',
						'left'   => '',
					),
					'unit'    => 'px',
				),
				'dekanpro_display_tagline'                 => false,
				'dekanpro_logo_heading_site_identity'      => true,
				'dekanpro_typography_logo_heading'         => false,
				'dekanpro_logo_text_font_size'             => array(
					'desktop' => 3,
					'unit'    => 'rem',
				),

				/**
				 * Header.
				 */

				// Top Bar.
				'dekanpro_top_bar_enable'                  => false,
				'dekanpro_top_bar_container_width'         => 'content-width',
				'dekanpro_top_bar_visibility'              => 'all',
				'dekanpro_top_bar_heading_widgets'         => true,
				'dekanpro_top_bar_widgets'                 => array(
					array(
						'classname' => 'dekanpro_customizer_widget_text',
						'type'      => 'text',
						'values'    => array(
							'content'    => wp_kses( '<i class="far fa-calendar-alt fa-lg dekanpro-icon"></i><strong><span id="dekanpro-date"></span> - <span id="dekanpro-time"></span></strong>', dekanpro_get_allowed_html_tags() ),
							'location'   => 'left',
							'visibility' => 'all',
						),
					),
					array(
						'classname' => 'dekanpro_customizer_widget_text',
						'type'      => 'text',
						'values'    => array(
							'content'    => wp_kses( '<i class="far fa-location-arrow fa-lg dekanpro-icon"></i> Subscribe to our dekanproter & never miss our best posts. <a href="#"><strong>Subscribe Now!</strong></a>', dekanpro_get_allowed_html_tags() ),
							'location'   => 'right',
							'visibility' => 'all',
						),
					),
				),
				'dekanpro_top_bar_widgets_separator'       => 'regular',
				'dekanpro_top_bar_heading_design_options'  => false,
				'dekanpro_top_bar_background'              => dekanpro_design_options_defaults(
					array(
						'background' => array(
							'color'    => array(
								'background-color' => 'rgba(247,229,183,0.35)',
							),
							'gradient' => array(
								'gradient-color-1' => 'rgba(247,229,183,0.35)',
								'gradient-color-2' => 'rgba(226,181,181,0.39)',
							),
						),
					)
				),
				'dekanpro_top_bar_text_color'              => dekanpro_design_options_defaults(
					array(
						'color' => array(
							'text-color'       => '#002050',
							'link-color'       => '#302D55',
							'link-hover-color' => '#F43676',
						),
					)
				),
				'dekanpro_top_bar_border'                  => dekanpro_design_options_defaults(
					array(
						'border' => array(
							'border-top-width' => '',
							'border-style'     => 'solid',
							'border-color'     => '',
							'separator-color'  => '#cccccc',
						),
					)
				),

				// Main Header.
				'dekanpro_header_layout'                   => 'layout-1',

				'dekanpro_header_container_width'          => 'content-width',
				'dekanpro_header_heading_widgets'          => true,
				'dekanpro_header_widgets'                  => array(
					array(
						'classname' => 'dekanpro_customizer_widget_socials',
						'type'      => 'socials',
						'values'    => array(
							'style'      => 'rounded-border',
							'size'       => 'standard',
							'location'   => 'left',
							'visibility' => 'hide-mobile-tablet',
						),
					),
					array(
						'classname' => 'dekanpro_customizer_widget_darkmode',
						'type'      => 'darkmode',
						'values'    => array(
							'style'      => 'rounded-border',
							'location'   => 'right',
							'visibility' => 'hide-mobile-tablet',
						),
					),
					array(
						'classname' => 'dekanpro_customizer_widget_search',
						'type'      => 'search',
						'values'    => array(
							'style'      => 'rounded-fill',
							'location'   => 'right',
							'visibility' => 'hide-mobile-tablet',
						),
					),
					array(
						'classname' => 'dekanpro_customizer_widget_button',
						'type'      => 'button',
						'values'    => array(
							'text'       => '<i class="far fa-bell mr-1 dekanpro-icon"></i> Subscribe',
							'url'        => '#',
							'class'      => 'btn-small',
							'target'     => '_self',
							'location'   => 'right',
							'visibility' => 'hide-mobile-tablet',
						),
					),
				),

				// Ad Widget
				'dekanpro_ad_widgets'                      => array(
					array(
						'classname' => 'dekanpro_customizer_widget_advertisements',
						'type'      => 'advertisements',
					),
				),

				'dekanpro_header_widgets_separator'        => 'none',
				'dekanpro_header_heading_design_options'   => false,
				'dekanpro_header_background'               => dekanpro_design_options_defaults(
					array(
						'background' => array(
							'color'    => array(
								'background-color' => '#FFFFFF',
							),
							'gradient' => array(),
							'image'    => array(),
						),
					)
				),
				'dekanpro_header_border'                   => dekanpro_design_options_defaults(
					array(
						'border' => array(
							'border-bottom-width' => 1,
							'border-color'        => 'rgba(185, 185, 185, 0.4)',
							'separator-color'     => '#cccccc',
						),
					)
				),
				'dekanpro_header_text_color'               => dekanpro_design_options_defaults(
					array(
						'color' => array(
							'text-color' => '#66717f',
							'link-color' => '#131315',
						),
					)
				),

				// Header navigation widgets
				'dekanpro_header_navigation_heading_widgets' => true,
				'dekanpro_header_navigation_widgets'       => array(),

				// Transparent Header.
				'dekanpro_tsp_header'                      => false,
				'dekanpro_tsp_header_disable_on'           => array(
					'404',
					'posts_page',
					'archive',
					'search',
				),

				// Sticky Header.
				'dekanpro_sticky_header'                   => false,
				'dekanpro_sticky_header_hide_on'           => array( '' ),

				// Main Navigation.
				'dekanpro_main_nav_heading_animation'      => false,
				'dekanpro_main_nav_hover_animation'        => 'underline',
				'dekanpro_main_nav_heading_sub_menus'      => true,
				'dekanpro_main_nav_sub_indicators'         => true,
				'dekanpro_main_nav_heading_mobile_menu'    => false,
				'dekanpro_main_nav_mobile_breakpoint'      => 960,
				'dekanpro_main_nav_mobile_label'           => '',
				'dekanpro_nav_design_options'              => false,
				'dekanpro_main_nav_background'             => dekanpro_design_options_defaults(
					array(
						'background' => array(
							'color'    => array(
								'background-color' => '#FFFFFF',
							),
							'gradient' => array(),
						),
					)
				),
				'dekanpro_main_nav_border'                 => dekanpro_design_options_defaults(
					array(
						'border' => array(
							'border-top-width'    => 1,
							'border-bottom-width' => 0,
							'border-style'        => 'solid',
							'border-color'        => 'rgba(185, 185, 185, 0.4)',
						),
					)
				),
				'dekanpro_main_nav_font_color'             => dekanpro_design_options_defaults(
					array(
						'color' => array(),
					)
				),
				'dekanpro_typography_main_nav_heading'     => false,
				'dekanpro_main_nav_font'                   => dekanpro_typography_defaults(
					array(
						'font-family'         => 'Inter Tight',
						'font-weight'         => 600,
						'font-size-desktop'   => '1.7',
						'font-size-unit'      => 'rem',
						'line-height-desktop' => '1.5',
					)
				),

				// Page Header.
				'dekanpro_page_header_enable'              => true,
				'dekanpro_page_header_alignment'           => 'left',
				'dekanpro_page_header_spacing'             => array(
					'desktop' => array(
						'top'    => 30,
						'bottom' => 30,
					),
					'tablet'  => array(
						'top'    => '',
						'bottom' => '',
					),
					'mobile'  => array(
						'top'    => '',
						'bottom' => '',
					),
					'unit'    => 'px',
				),
				'dekanpro_page_header_background'          => dekanpro_design_options_defaults(
					array(
						'background' => array(
							'color'    => array( 'background-color' => 'rgba(244,54,118,0.1)' ),
							'gradient' => array(),
							'image'    => array(),
						),
					)
				),
				'dekanpro_page_header_text_color'          => dekanpro_design_options_defaults(
					array(
						'color' => array(),
					)
				),
				'dekanpro_page_header_border'              => dekanpro_design_options_defaults(
					array(
						'border' => array(
							'border-bottom-width' => 1,
							'border-style'        => 'solid',
							'border-color'        => 'rgba(0,0,0,.062)',
						),
					)
				),
				'dekanpro_typography_page_header'          => false,
				'dekanpro_page_header_font_size'           => array(
					'desktop' => 2.6,
					'unit'    => 'rem',
				),

				// Breadcrumbs.
				'dekanpro_breadcrumbs_enable'              => true,
				'dekanpro_breadcrumbs_hide_on'             => array( 'home' ),
				'dekanpro_breadcrumbs_position'            => 'in-page-header',
				'dekanpro_breadcrumbs_alignment'           => 'left',
				'dekanpro_breadcrumbs_spacing'             => array(
					'desktop' => array(
						'top'    => 15,
						'bottom' => 15,
					),
					'tablet'  => array(
						'top'    => '',
						'bottom' => '',
					),
					'mobile'  => array(
						'top'    => '',
						'bottom' => '',
					),
					'unit'    => 'px',
				),
				'dekanpro_breadcrumbs_heading_design'      => false,
				'dekanpro_breadcrumbs_background'          => dekanpro_design_options_defaults(
					array(
						'background' => array(
							'color'    => array(),
							'gradient' => array(),
							'image'    => array(),
						),
					)
				),
				'dekanpro_breadcrumbs_text_color'          => dekanpro_design_options_defaults(
					array(
						'color' => array(),
					)
				),
				'dekanpro_breadcrumbs_border'              => dekanpro_design_options_defaults(
					array(
						'border' => array(
							'border-top-width'    => 0,
							'border-bottom-width' => 0,
							'border-color'        => '',
							'border-style'        => 'solid',
						),
					)
				),

				/**
				 * Hero.
				 */
				'dekanpro_enable_hero'                     => true,
				'dekanpro_hero_type'                       => 'horizontal-slider',
				'dekanpro_hero_slider_align'			   => 'center',
				'dekanpro_hero_enable_on'                  => array( 'home' ),
				'dekanpro_hero_slider'                     => false,
				'dekanpro_hero_slider_orderby'             => 'date-desc',
				'dekanpro_hero_slider_title_font_size'     => array(
					'desktop' => 2.4,
					'unit'    => 'rem',
				),
				'dekanpro_hero_slider_elements'            => array(
					'category'  => true,
					'meta'      => true,
					'read_more' => true,
				),
				'dekanpro_hero_entry_meta_elements'        => array(
					'author'   => true,
					'date'     => true,
					'comments' => false,
				),
				'dekanpro_hero_slider_posts'               => false,
				'dekanpro_hero_slider_post_number'         => 6,
				'dekanpro_hero_slider_category'            => array(),
				'dekanpro_hero_slider_read_more'           => esc_html__( 'Continue Reading', 'dekanpro' ),

				/**
				 * Featured Links
				 */
				'dekanpro_enable_featured_links'           => false,
				'dekanpro_featured_links_title'            => esc_html__( 'Today Best Trending Topics', 'dekanpro' ),
				'dekanpro_featured_links_enable_on'        => array( 'home' ),
				'dekanpro_featured_links_style'            => false,
				'dekanpro_featured_links_type'             => 'one',
				'dekanpro_featured_links_title_type'       => '1',
				'dekanpro_featured_links_card_border'      => true,
				'dekanpro_featured_links_card_shadow'      => true,
				'dekanpro_featured_links'                  => apply_filters(
					'dekanpro_featured_links_default',
					array(
						array(
							'link'  => '',
							'image' => array(),
						),
						array(
							'link'  => '',
							'image' => array(),
						),
						array(
							'link'  => '',
							'image' => array(),
						),
					),
				),

				/**
				 * PYML
				 */
				'dekanpro_enable_pyml'                     => true,
				'dekanpro_pyml_title'                      => esc_html__( 'You May Have Missed', 'dekanpro' ),
				'dekanpro_pyml_enable_on'                  => array( 'home' ),
				'dekanpro_pyml_style'                      => false,
				'dekanpro_pyml_type'                       => '1',
				'dekanpro_pyml_orderby'                    => 'date-desc',
				'dekanpro_pyml_card_border'                => true,
				'dekanpro_pyml_card_shadow'                => true,
				'dekanpro_pyml_elements'                   => array(
					'category' => true,
					'meta'     => true,
				),
				'dekanpro_pyml_posts'                      => true,
				'dekanpro_pyml_post_number'                => 4,
				'dekanpro_pyml_post_title_font_size'       => array(
					'desktop' => 2,
					'unit'    => 'rem',
				),
				'dekanpro_pyml_category'                   => array(),

				/**
				 * Ticker Slider
				 */
				'dekanpro_enable_ticker'                   => true,
				'dekanpro_ticker_title'                    => esc_html__( 'Top Stories', 'dekanpro' ),
				'dekanpro_ticker_enable_on'                => array( 'home' ),
				'dekanpro_ticker_type'                     => 'one-ticker',
				'dekanpro_ticker_elements'                 => array(
					'meta' => true,
				),
				'dekanpro_ticker_posts'                    => false,
				'dekanpro_ticker_post_number'              => 100,
				'dekanpro_ticker_category'                 => array(),

				/**
				 * Blog.
				 */

				// Blog Page / Archive.
				'dekanpro_blog_entry_elements'             => array(
					'thumbnail'      => true,
					'header'         => true,
					'meta'           => true,
					'summary'        => true,
					'summary-footer' => true,
				),
				'dekanpro_blog_entry_meta_elements'        => array(
					'author'   => true,
					'date'     => true,
					'category' => false,
					'tag'      => false,
					'comments' => false,
				),
				'dekanpro_related_posts'                   => false,
				'dekanpro_related_posts_enable'            => false,
				'dekanpro_related_posts_heading'           => esc_html__( 'Related posts', 'dekanpro' ),
				'dekanpro_related_post_number'             => 3,
				'dekanpro_related_posts_column'            => 4,
				'dekanpro_entry_meta_icons'                => true,
				'dekanpro_excerpt_length'                  => 30,
				'dekanpro_excerpt_more'                    => '&hellip;',
				'dekanpro_blog_layout'                     => 'blog-horizontal',
				'dekanpro_blog_image_wrap'                 => true,
				'dekanpro_blog_zig_zag'                    => false,
				'dekanpro_blog_masonry'                    => false,
				'dekanpro_blog_layout_column'              => 6,
				'dekanpro_blog_image_position'             => 'left',
				'dekanpro_blog_image_size'                 => 'large',
				'dekanpro_blog_card_border'                => true,
				'dekanpro_blog_card_shadow'                => true,
				'dekanpro_blog_heading'                    => '',
				'dekanpro_blog_read_more'                  => esc_html__( 'Read More', 'dekanpro' ),
				'dekanpro_blog_horizontal_post_categories' => true,
				'dekanpro_blog_horizontal_read_more'       => false,

				// Single Post.
				'dekanpro_single_post_layout_heading'      => false,
				'dekanpro_single_title_position'           => 'in-content',
				'dekanpro_single_title_alignment'          => 'left',
				'dekanpro_single_title_spacing'            => array(
					'desktop' => array(
						'top'    => 152,
						'bottom' => 100,
					),
					'tablet'  => array(
						'top'    => 90,
						'bottom' => 55,
					),
					'mobile'  => array(
						'top'    => '',
						'bottom' => '',
					),
					'unit'    => 'px',
				),
				'dekanpro_single_content_width'            => 'wide',
				'dekanpro_single_narrow_container_width'   => 700,
				'dekanpro_single_post_elements_heading'    => false,
				'dekanpro_single_post_meta_elements'       => array(
					'author'   => true,
					'date'     => true,
					'comments' => true,
					'category' => false,
				),
				'dekanpro_single_post_thumb'               => true,
				'dekanpro_single_post_categories'          => true,
				'dekanpro_single_post_tags'                => true,
				'dekanpro_single_last_updated'             => true,
				'dekanpro_single_about_author'             => true,
				'dekanpro_single_post_next_prev'           => true,
				'dekanpro_single_post_elements'            => array(
					'thumb'          => true,
					'category'       => true,
					'tags'           => true,
					'last-updated'   => true,
					'about-author'   => true,
					'prev-next-post' => true,
				),
				'dekanpro_single_toggle_comments'          => false,
				'dekanpro_single_entry_meta_icons'         => true,
				'dekanpro_typography_single_post_heading'  => false,
				'dekanpro_single_content_font_size'        => array(
					'desktop' => '1.6',
					'unit'    => 'rem',
				),

				/**
				 * Sidebar.
				 */

				'dekanpro_sidebar_position'                => 'right-sidebar',
				'dekanpro_single_post_sidebar_position'    => 'default',
				'dekanpro_single_page_sidebar_position'    => 'default',
				'dekanpro_archive_sidebar_position'        => 'default',
				'dekanpro_sidebar_options_heading'         => false,
				'dekanpro_sidebar_style'                   => '2',
				'dekanpro_sidebar_width'                   => 30,
				'dekanpro_sidebar_sticky'                  => 'sidebar',
				'dekanpro_typography_sidebar_heading'      => false,
				'dekanpro_sidebar_widget_title_font_size'  => array(
					'desktop' => 2.4,
					'unit'    => 'rem',
				),

				/**
				 * Footer.
				 */

				// Copyright.
				'dekanpro_enable_copyright'                => true,
				'dekanpro_copyright_layout'                => 'layout-1',
				'dekanpro_copyright_separator'             => 'contained-separator',
				'dekanpro_copyright_visibility'            => 'all',
				'dekanpro_copyright_heading_widgets'       => true,
				'dekanpro_copyright_widgets'               => array(
					array(
						'classname' => 'dekanpro_customizer_widget_text',
						'type'      => 'text',
						'values'    => array(
							'content'    => wp_kses( 'Copyright {{the_year}} &mdash; <b>{{site_title}}</b>. All rights reserved. <b>{{theme_link}}</b>', dekanpro_get_allowed_html_tags() ),
							// 'content'    => esc_html__( '', 'dekanpro' ),
							'location'   => 'start',
							'visibility' => 'all',
						),
					),
				),
				'dekanpro_copyright_heading_design_options' => false,
				'dekanpro_copyright_background'            => dekanpro_design_options_defaults(
					array(
						'background' => array(
							'color'    => array(
								'background-color' => '',
							),
							'gradient' => array(),
						),
					)
				),
				'dekanpro_copyright_text_color'            => dekanpro_design_options_defaults(
					array(
						'color' => array(
							'text-color'       => '#d9d9d9',
							'link-color'       => '#ffffff',
							'link-hover-color' => '#F43676',
						),
					)
				),

				// Main Footer.
				'dekanpro_enable_footer'                   => true,
				'dekanpro_footer_layout'                   => 'layout-2',
				'dekanpro_footer_widgets_align_center'     => false,
				'dekanpro_footer_visibility'               => 'all',
				'dekanpro_footer_widget_heading_style'     => '0',
				'dekanpro_footer_heading_design_options'   => false,
				'dekanpro_footer_background'               => dekanpro_design_options_defaults(
					array(
						'background' => array(
							'color'    => array(
								'background-color' => '#302d55',
							),
							'gradient' => array(),
							'image'    => array(),
						),
					)
				),
				'dekanpro_footer_text_color'               => dekanpro_design_options_defaults(
					array(
						'color' => array(
							'text-color'         => '#d9d9d9',
							'link-color'         => '#d9d9d9',
							'link-hover-color'   => '#F43676',
							'widget-title-color' => '#ffffff',
						),
					)
				),
				'dekanpro_footer_border'                   => dekanpro_design_options_defaults(
					array(
						'border' => array(
							'border-top-width'    => 1,
							'border-bottom-width' => 0,
							'border-color'        => 'rgba(255,255,255,0.1)',
							'border-style'        => 'solid',
						),
					)
				),
				'dekanpro_typography_main_footer_heading'  => false,
			);

			$defaults = array_merge( $defaults, $dekanpro_categories_color_options );

			$defaults = apply_filters( 'dekanpro_default_option_values', $defaults );
			return $defaults;
		}

		/**
		 * Get the options from static array()
		 *
		 * @since  1.0.0
		 * @return array    Return array of theme options.
		 */
		public function get_options() {
			return self::$options;
		}

		/**
		 * Get the options from static array().
		 *
		 * @since  1.0.0
		 * @param string $id Options jet to get.
		 * @return array Return array of theme options.
		 */
		public function get( $id ) {
			$value = isset( self::$options[ $id ] ) ? self::$options[ $id ] : self::get_default( $id );
			$value = apply_filters("theme_mod_{$id}", $value); // phpcs:ignore
			return $value;
		}

		/**
		 * Set option.
		 *
		 * @since  1.0.0
		 * @param string $id Option key.
		 * @param any    $value Option value.
		 * @return void
		 */
		public function set( $id, $value ) {
			set_theme_mod( $id, $value );
			self::$options[ $id ] = $value;
		}

		/**
		 * Refresh options.
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function refresh() {
			self::$options = wp_parse_args(
				get_theme_mods(),
				self::get_defaults()
			);
		}

		/**
		 * Returns the default value for option.
		 *
		 * @since  1.0.0
		 * @param  string $id Option ID.
		 * @return mixed      Default option value.
		 */
		public function get_default( $id ) {
			$defaults = self::get_defaults();
			return isset( $defaults[ $id ] ) ? $defaults[ $id ] : false;
		}
	}

endif;

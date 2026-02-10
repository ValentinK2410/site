<?php
/**
 * DekanPro Pro Features section in Customizer.
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

if ( ! class_exists( 'Dekanpro_Customizer_Pro_Features' ) ) :
	/**
	 * Dekanpro PYML section in Customizer.
	 */
	class Dekanpro_Customizer_Pro_Features {

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
			// Pro features section
			$options['section']['dekanpro_section_dekanpro_pro'] = array(
				'title'    => esc_html__( 'View Pro Features', 'dekanpro' ),
				'priority' => 0,
			);

			$options['setting']['dekanpro_section_dekanpro_pro_features'] = array(
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_text_field',
				'control'           => array(
					'type'       => 'dekanpro-pro',
					'section'    => 'dekanpro_section_dekanpro_pro',
					'screenshot' => apply_filters( 'dekanpro_pro_theme_screenshot', esc_url( get_template_directory_uri() ) . '/assets/images/dekanpro-lapi.webp' ),
					'features'   => apply_filters(
						'dekanpro_pro_theme_features',
						array(
							esc_html_x( 'All starter sites included', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Advance header layout options', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Advance FrontPage slider layouts', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Unlimited \'Advertisement\' widgets', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Option to ad AdSesne code in advertisement widget', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Body and H1 to H6 typography options', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Primary, seconday and text buttons color and typography options', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Post advance features', 'pro feature' , 'dekanpro' ),
							esc_html_x( '\'Post Like ❤️\' feature', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Ajax load more posts', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Infinite load posts', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Unlimited \'Featured links\' + some additional features', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Meta category options', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Site layouts options e.g. Boxed, Framed etc', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Archive layout options', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Advance color scheme', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Author widgets', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Title design settings', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Masonry grid & multi post options', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Full width Post/Page options', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Single Post/Page layout options', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Footer advance features', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Footer widgets options', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Call to action / Pre-Footer', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Site width manage options', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Parallax footer', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Site pre-loader', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'SEO Meta', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'AMP compatibility', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Coming soon/Maintenance mode option', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Regular premium updates', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'Quick support', 'pro feature' , 'dekanpro' ),
							esc_html_x( 'And much more...', 'pro feature' , 'dekanpro' ),
						)
					),
				),
			);

			return $options;
		}

	}
endif;
new Dekanpro_Customizer_Pro_Features();

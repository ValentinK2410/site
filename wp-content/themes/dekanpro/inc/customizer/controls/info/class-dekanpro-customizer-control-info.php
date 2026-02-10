<?php
/**
 * Dekanpro Customizer info control class.
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

if ( ! class_exists( 'Dekanpro_Customizer_Control_Info' ) ) :
	/**
	 * Dekanpro Customizer info control class.
	 */
	class Dekanpro_Customizer_Control_Info extends Dekanpro_Customizer_Control {

		/**
		 * The control type.
		 *
		 * @var string
		 */
		public $type = 'dekanpro-info';

		/**
		 * Custom URL.
		 *
		 * @since  1.0.0
		 * @var    string
		 */
		public $url = '';

		/**
		 * Link target.
		 *
		 * @since  1.0.0
		 * @var    string
		 */
		public $target = '_blank';

		/**
		 * Enqueue control related scripts/styles.
		 *
		 * @access public
		 */
		public function enqueue() {

			// Script debug.
			$dekanpro_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// Control type.
			$dekanpro_type = str_replace( 'dekanpro-', '', $this->type );

			/**
			 * Enqueue control stylesheet
			 */
			wp_enqueue_style(
				'dekanpro-' . $dekanpro_type . '-control-style',
				DEKANPRO_THEME_URI . '/inc/customizer/controls/' . $dekanpro_type . '/' . $dekanpro_type . $dekanpro_suffix . '.css',
				false,
				DEKANPRO_THEME_VERSION,
				'all'
			);
		}

		/**
		 * Refresh the parameters passed to the JavaScript via JSON.
		 *
		 * @see WP_Customize_Control::to_json()
		 */
		public function to_json() {
			parent::to_json();

			$this->json['url']    = $this->url;
			$this->json['target'] = $this->target;
		}

		/**
		 * An Underscore (JS) template for this control's content (but not its container).
		 *
		 * Class variables for this control class are available in the `data` JS object;
		 * export custom variables by overriding {@see WP_Customize_Control::to_json()}.
		 *
		 * @see WP_Customize_Control::print_template()
		 */
		protected function content_template() {
			?>
			<div class="dekanpro-info-wrapper dekanpro-control-wrapper">

				<# if ( data.label ) { #>
					<span class="dekanpro-control-heading customize-control-title dekanpro-field">{{{ data.label }}}</span>
				<# } #>

				<# if ( data.description ) { #>
					<div class="description customize-control-description dekanpro-field dekanpro-info-description">{{{ data.description }}}</div>
				<# } #>

				<a href="{{ data.url }}" class="button button-primary" target="{{ data.target }}" rel="noopener noreferrer"><?php esc_html_e( 'Learn More', 'dekanpro' ); ?></a>

			</div><!-- END .dekanpro-control-wrapper -->
			<?php
		}

	}
endif;

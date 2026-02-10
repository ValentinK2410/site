<?php
/**
 * Dekanpro Customizer custom color control class.
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

if ( ! class_exists( 'Dekanpro_Customizer_Control_Color' ) ) :
	/**
	 * Dekanpro Customizer custom color control class.
	 */
	class Dekanpro_Customizer_Control_Color extends Dekanpro_Customizer_Control {

		/**
		 * The control type.
		 *
		 * @var string
		 */
		public $type = 'dekanpro-color';

		/**
		 * Add support for showing the opacity value on the slider handle.
		 *
		 * @var boolean
		 */
		public $opacity;

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

			// Enqueue WordPress color picker styles.
			wp_enqueue_style( 'wp-color-picker' );

			// Enqueue control stylesheet.
			wp_enqueue_style(
				'dekanpro-' . $dekanpro_type . '-control-style',
				DEKANPRO_THEME_URI . '/inc/customizer/controls/' . $dekanpro_type . '/' . $dekanpro_type . $dekanpro_suffix . '.css',
				false,
				DEKANPRO_THEME_VERSION,
				'all'
			);

			// Enqueue our control script.
			wp_enqueue_script(
				'dekanpro-' . $dekanpro_type . '-js',
				DEKANPRO_THEME_URI . '/inc/customizer/controls/' . $dekanpro_type . '/' . $dekanpro_type . $dekanpro_suffix . '.js',
				array( 'jquery', 'customize-base', 'wp-color-picker' ),
				DEKANPRO_THEME_VERSION,
				true
			);
		}

		/**
		 * Refresh the parameters passed to the JavaScript via JSON.
		 *
		 * @see WP_Customize_Control::to_json()
		 */
		public function to_json() {
			parent::to_json();

			$this->json['opacity'] = ( false === $this->opacity || 'false' === $this->opacity ) ? 'false' : 'true';
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
			<div class="dekanpro-color-wrapper dekanpro-control-wrapper">

				<# if ( data.label ) { #>
					<div class="customize-control-title">
						<span>{{{ data.label }}}</span>

						<# if ( data.description ) { #>
							<i class="dekanpro-info-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle">
									<circle cx="12" cy="12" r="10"></circle>
									<path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
									<line x1="12" y1="17" x2="12" y2="17"></line>
								</svg>
								<span class="dekanpro-tooltip">{{{ data.description }}}</span>
							</i>
						<# } #>

					</div>
				<# } #>

				<div>
					<input class="dekanpro-color-control" type="text" value="{{ data.value }}" data-show-opacity="{{ data.opacity }}" data-default-color="{{ data.default }}" />
				</div>

			</div><!-- END .dekanpro-toggle-wrapper -->
			<?php
		}

	}
endif;

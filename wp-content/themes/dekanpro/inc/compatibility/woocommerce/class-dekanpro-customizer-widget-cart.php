<?php
/**
 * Dekanpro Customizer widgets class.
 *
 * @package DekanPro
 * @author Peregrine Themes
 * @since   1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Dekanpro_Customizer_Widget_Cart' ) ) :

	/**
	 * Dekanpro Customizer widget class
	 */
	class Dekanpro_Customizer_Widget_Cart extends Dekanpro_Customizer_Widget {

		/**
		 * Primary class constructor.
		 *
		 * @since 1.0.0
		 * @param array $args An array of the values for this widget.
		 */
		public function __construct( $args = array() ) {

			parent::__construct( $args );

			$this->name        = esc_html__( 'Cart', 'dekanpro' );
			$this->description = esc_html__( 'Displays WooCommerce cart.', 'dekanpro' );
			$this->icon        = 'dashicons dashicons-cart';
			$this->type        = 'cart';

			$this->styles = isset( $args['styles'] ) ? $args['styles'] : array(
				'minimal'        => 'Minimal',
				'rounded-fill'   => 'Rounded Fill',
				'rounded-border' => 'Rounded Border',
			);
		}

		/**
		 * Displays the form for this widget on the Widgets page of the WP Admin area.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function form() {

			if ( ! empty( $this->styles ) ) {
				$style_value = isset( $this->values['style'] ) ? $this->values['style'] : ''; // Ensured default value if not set.
				?>
				<p class="dekanpro-widget-cart-style">
					<label for="widget-cart-<?php echo esc_attr( $this->id ); ?>-<?php echo esc_attr( $this->number ); ?>-style">
						<?php esc_html_e( 'Style', 'dekanpro' ); ?>:
					</label>
					<select id="widget-cart-<?php echo esc_attr( $this->id ); ?>-<?php echo esc_attr( $this->number ); ?>-style" name="widget-cart[<?php echo esc_attr( $this->number ); ?>][style]" data-option-name="style">
						<?php foreach ( $this->styles as $key => $value ) { ?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $style_value ); ?>>
								<?php echo esc_html( $value ); ?>
							</option>
						<?php } ?>
					</select>
				</p>
				<?php
			}
		}
	}
endif;

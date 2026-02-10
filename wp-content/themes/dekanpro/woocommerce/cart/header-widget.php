<?php
/**
 * Header Cart Widget.
 *
 * @package DekanPro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$dekanpro_cart_count = WC()->cart->get_cart_contents_count();
$dekanpro_cart_icon  = apply_filters( 'dekanpro_wc_cart_widget_icon', 'shopping-cart-2' );

$dekanpro_header_widgets = dekanpro_option( 'header_widgets' );
$style_for_cart          = '';

foreach ( $dekanpro_header_widgets as $widget ) {
	// Check if the widget type is 'cart'
	if ( $widget['type'] === 'cart' ) {
		// Check if 'style' key exists and then access the 'style' from the 'values' array
		if ( isset( $widget['values']['style'] ) ) {
			$style_for_cart = $widget['values']['style'];
		} else {
			// Optionally handle the case where 'style' does not exist
			// For example, you could assign a default style
			$style_for_cart = 'default-style'; // This is just an example, adjust as needed
		}
		break; // Stop the loop if the cart widget is found
	}
}

?>
<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="dekanpro-cart <?php echo esc_attr( $style_for_cart ); ?>">
	<?php echo dekanpro()->icons->get_svg( $dekanpro_cart_icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<?php if ( $dekanpro_cart_count > 0 ) { ?>
		<span class="dekanpro-cart-count"><?php echo esc_html( $dekanpro_cart_count ); ?></span>
	<?php } ?>
</a>

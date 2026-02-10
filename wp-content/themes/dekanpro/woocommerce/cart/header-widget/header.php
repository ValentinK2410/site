<?php
/**
 * Header Cart Widget dropdown header.
 *
 * @package DekanPro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$dekanpro_cart_count    = WC()->cart->get_cart_contents_count();
$dekanpro_cart_subtotal = WC()->cart->get_cart_subtotal();

?>
<div class="wc-cart-widget-header">
	<span class="dekanpro-cart-count">
		<?php
		/* translators: %s: the number of cart items; */
		echo wp_kses_post( sprintf( _n( '%s item', '%s items', $dekanpro_cart_count, 'dekanpro' ), $dekanpro_cart_count ) );
		?>
	</span>

	<span class="dekanpro-cart-subtotal">
		<?php
		/* translators: %s is the cart subtotal. */
		echo wp_kses_post( sprintf( __( 'Subtotal: %s', 'dekanpro' ), '<span>' . $dekanpro_cart_subtotal . '</span>' ) );
		?>
	</span>
</div>

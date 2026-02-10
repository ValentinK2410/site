<?php
/**
 * Header Cart Widget cart & checkout buttons.
 *
 * @package DekanPro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="dekanpro-cart-buttons">
	<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="dekanpro-btn btn-text-1" role="button">
		<span><?php esc_html_e( 'View Cart', 'dekanpro' ); ?></span>
	</a>

	<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="dekanpro-btn btn-fw" role="button">
		<span><?php esc_html_e( 'Checkout', 'dekanpro' ); ?></span>
	</a>
</div>

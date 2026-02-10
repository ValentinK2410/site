<?php
/**
 * Header Cart Widget empty cart.
 *
 * @package DekanPro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="dekanpro-empty-cart">
	<?php echo dekanpro()->icons->get_svg( 'shopping-empty', array( 'aria-hidden' => 'true' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<p><?php esc_html_e( 'No products in the cart.', 'dekanpro' ); ?></p>
</div>

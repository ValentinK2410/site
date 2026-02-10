<?php
/**
 * The template for displaying scroll to top button.
 *
 * @package     Dekanpro
 * @author      Peregrine Themes
 * @since       1.0.0
 */

?>

<a href="#" id="dekanpro-scroll-top" class="dekanpro-smooth-scroll" title="<?php esc_attr_e( 'Scroll to Top', 'dekanpro' ); ?>" <?php dekanpro_scroll_top_classes(); ?>>
	<span class="dekanpro-scroll-icon" aria-hidden="true">
		<?php echo dekanpro()->icons->get_svg( 'arrow-up', array( 'class' => 'top-icon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php echo dekanpro()->icons->get_svg( 'arrow-up' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</span>
	<span class="screen-reader-text"><?php esc_html_e( 'Scroll to Top', 'dekanpro' ); ?></span>
</a><!-- END #dekanpro-scroll-to-top -->

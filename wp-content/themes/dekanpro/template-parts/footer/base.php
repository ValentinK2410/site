<?php
/**
 * The template for displaying theme footer.
 *
 * @package     Dekanpro
 * @author      Peregrine Themes
 * @since       1.0.0
 */

?>

<?php do_action( 'dekanpro_before_footer' ); ?>
<div id="dekanpro-footer" <?php dekanpro_footer_classes(); ?>>
	<div class="dekanpro-container">
		<div class="dekanpro-flex-row" id="dekanpro-footer-widgets">

			<?php dekanpro_footer_widgets(); ?>

		</div><!-- END .dekanpro-flex-row -->
	</div><!-- END .dekanpro-container -->
</div><!-- END #dekanpro-footer -->
<?php do_action( 'dekanpro_after_footer' ); ?>

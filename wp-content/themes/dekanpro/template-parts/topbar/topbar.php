<?php
/**
 * The template for displaying theme top bar.
 *
 * @see https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package DekanPro
 * @author Peregrine Themes
 * @since   1.0.0
 */

?>

<?php do_action( 'dekanpro_before_topbar' ); ?>
<div id="dekanpro-topbar" <?php dekanpro_top_bar_classes(); ?>>
	<div class="dekanpro-container">
		<div class="dekanpro-flex-row">
			<div class="col-md flex-basis-auto start-sm"><?php do_action( 'dekanpro_topbar_widgets', 'left' ); ?></div>
			<div class="col-md flex-basis-auto end-sm"><?php do_action( 'dekanpro_topbar_widgets', 'right' ); ?></div>
		</div>
	</div>
</div><!-- END #dekanpro-topbar -->
<?php do_action( 'dekanpro_after_topbar' ); ?>

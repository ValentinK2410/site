<?php
/**
 * The template for displaying theme copyright bar.
 *
 * @package     Dekanpro
 * @author      DekanPro
 * @since       1.0.0
 */

?>

<?php do_action( 'dekanpro_before_copyright' ); ?>
<div id="dekanpro-copyright" <?php dekanpro_copyright_classes(); ?>>
	<div class="dekanpro-container">
		<div class="dekanpro-flex-row">

			<div class="col-xs-12 center-xs col-md flex-basis-auto start-md"><?php do_action( 'dekanpro_copyright_widgets', 'start' ); ?></div>
			<div class="col-xs-12 center-xs col-md flex-basis-auto end-md"><?php do_action( 'dekanpro_copyright_widgets', 'end' ); ?></div>

		</div><!-- END .dekanpro-flex-row -->
	</div>
</div><!-- END #dekanpro-copyright -->
<?php do_action( 'dekanpro_after_copyright' ); ?>

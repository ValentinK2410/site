<?php
/**
 * The template for displaying header layout 1.
 *
 * @package DekanPro
 * @author DekanPro
 * @since   1.0.0
 */

?>

<div class="dekanpro-container dekanpro-header-container">

	<?php
	dekanpro_header_logo_template();
	?>

	<span class="dekanpro-header-element dekanpro-mobile-nav">
		<?php dekanpro_hamburger( dekanpro_option( 'main_nav_mobile_label' ), 'dekanpro-primary-nav' ); ?>
	</span>

	<?php
	dekanpro_main_navigation_template();
	do_action( 'dekanpro_header_widget_location', array( 'left', 'right' ) );
	?>

</div><!-- END .dekanpro-container -->

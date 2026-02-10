<?php
/**
 * The template for displaying page preloader.
 *
 * @see https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package DekanPro
 * @author DekanPro
 * @since   1.0.0
 */

?>

<div id="dekanpro-preloader"<?php dekanpro_preloader_classes(); ?>>
	<?php get_template_part( 'template-parts/preloader/preloader', dekanpro_option( 'preloader_style' ) ); ?>
</div><!-- END #dekanpro-preloader -->

<?php
/**
 * The base template for displaying theme header area.
 *
 * @see https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package     Dekanpro
 * @author      Peregrine Themes
 * @since       1.0.0
 */

?>
<?php do_action( 'dekanpro_before_header' ); ?>
<div id="dekanpro-header" <?php dekanpro_header_classes(); ?>>
	<?php do_action( 'dekanpro_header_content' ); ?>
</div><!-- END #dekanpro-header -->
<?php do_action( 'dekanpro_after_header' ); ?>

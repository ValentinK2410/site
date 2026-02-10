<?php
/**
 * The template for displaying search results pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package     Dekanpro
 * @author      Peregrine Themes
 * @since       1.0.0
 */

?>

<?php get_header(); ?>

<?php do_action( 'dekanpro_before_container' ); ?>

<div class="dekanpro-container">

	<?php do_action( 'dekanpro_before_content_area', 'before_post_archive' ); ?>

	<div id="primary" class="content-area">

		<?php do_action( 'dekanpro_before_content' ); ?>

		<main id="content" class="site-content" role="main"<?php dekanpro_schema_markup( 'main' ); ?>>

			<?php do_action( 'dekanpro_content_search' ); ?>

		</main><!-- #content .site-content -->

		<?php do_action( 'dekanpro_after_content' ); ?>

	</div><!-- #primary .content-area -->

	<?php do_action( 'dekanpro_sidebar' ); ?>

	<?php do_action( 'dekanpro_after_content_area' ); ?>

</div><!-- END .dekanpro-container -->

<?php do_action( 'dekanpro_after_container' ); ?>

<?php
get_footer();

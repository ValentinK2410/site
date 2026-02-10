<?php
/**
 * Template part for displaying content of Dekanpro Canvas [Fullwidth] page template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package DekanPro
 * @author Peregrine Themes
 * @since   1.0.0
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?><?php dekanpro_schema_markup( 'article' ); ?>>
	<div class="entry-content dekanpro-entry dekanpro-fullwidth-entry">
		<?php
		do_action( 'dekanpro_before_page_content' );

		the_content();

		do_action( 'dekanpro_after_page_content' );
		?>
	</div><!-- END .entry-content -->
</article><!-- #post-<?php the_ID(); ?> -->

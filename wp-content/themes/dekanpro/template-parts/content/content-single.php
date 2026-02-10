<?php
/**
 * Template for Single post
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package DekanPro
 * @author DekanPro
 * @since   1.0.0
 */

?>

<?php do_action( 'dekanpro_before_article' ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'dekanpro-article' ); ?><?php dekanpro_schema_markup( 'article' ); ?>>

	<?php
	if ( 'quote' === get_post_format() ) {
		get_template_part( 'template-parts/entry/format/media', 'quote' );
	}

	$dekanpro_single_post_elements = dekanpro_get_single_post_elements();

	if ( ! empty( $dekanpro_single_post_elements ) ) {
		foreach ( $dekanpro_single_post_elements as $dekanpro_element ) {

			if ( 'content' === $dekanpro_element ) {
				do_action( 'dekanpro_before_single_content', 'before_post_content' );
				get_template_part( 'template-parts/entry/entry', $dekanpro_element );
				do_action( 'dekanpro_after_single_content', 'after_post_content' );
			} else {
				get_template_part( 'template-parts/entry/entry', $dekanpro_element );
			}
		}
	}
	?>

</article><!-- #post-<?php the_ID(); ?> -->

<?php do_action( 'dekanpro_after_article' ); ?>

<?php
/**
 * Template part for displaying post in post listing.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package     Dekanpro
 * @author      DekanPro
 * @since       1.0.0
 */

?>

<?php do_action( 'dekanpro_before_article' ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'dekanpro-article dp-reveal' ); ?><?php dekanpro_schema_markup( 'article' ); ?>>

	<?php
	$dekanpro_blog_entry_format = get_post_format();

	if ( 'quote' === $dekanpro_blog_entry_format ) {
		get_template_part( 'template-parts/entry/format/media', $dekanpro_blog_entry_format );
	} else {

		$dekanpro_blog_entry_elements = dekanpro_get_blog_entry_elements();

		if ( ! empty( $dekanpro_blog_entry_elements ) ) {
			foreach ( $dekanpro_blog_entry_elements as $dekanpro_element ) {
				get_template_part( 'template-parts/entry/entry', $dekanpro_element );
			}
		}
	}
	?>

</article><!-- #post-<?php the_ID(); ?> -->

<?php do_action( 'dekanpro_after_article' ); ?>

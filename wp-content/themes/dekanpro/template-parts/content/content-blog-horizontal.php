<?php
/**
 * Template part for displaying blog post - horizontal.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package     Dekanpro
 * @author      DekanPro
 * @since       1.0.0
 */
$class_no_media = ! has_post_thumbnail() ? 'no-entry-media' : '';
?>

<?php do_action( 'dekanpro_before_article' ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( array( 'dekanpro-article', esc_attr( $class_no_media ) ) ); ?><?php dekanpro_schema_markup( 'article' ); ?>>

	<?php
	$dekanpro_blog_entry_format = get_post_format();

	if ( 'quote' === $dekanpro_blog_entry_format ) {
		get_template_part( 'template-parts/entry/format/media', $dekanpro_blog_entry_format );
	} else {

		$dekanpro_classes     = array();
		$dekanpro_classes[]   = 'dekanpro-blog-entry-wrapper';
		$dekanpro_thumb_align = dekanpro_option( 'blog_image_position' );
		$dekanpro_thumb_align = apply_filters( 'dekanpro_horizontal_blog_image_position', $dekanpro_thumb_align );
		$dekanpro_classes[]   = 'dekanpro-thumb-' . $dekanpro_thumb_align;
		$dekanpro_classes     = implode( ' ', $dekanpro_classes );
		?>

		<div class="<?php echo esc_attr( $dekanpro_classes ); ?>">
			<?php get_template_part( 'template-parts/entry/entry-thumbnail' ); ?>

			<div class="dekanpro-entry-content-wrapper">

				<?php
				if ( dekanpro_option( 'blog_horizontal_post_categories' ) ) {
					get_template_part( 'template-parts/entry/entry-category' );
				}

				get_template_part( 'template-parts/entry/entry-header' );
				get_template_part( 'template-parts/entry/entry-summary' );


				if ( dekanpro_option( 'blog_horizontal_read_more' ) ) {
					get_template_part( 'template-parts/entry/entry-summary-footer' );
				}

				get_template_part( 'template-parts/entry/entry-meta' );
				?>
			</div>
		</div>

	<?php } ?>

</article><!-- #post-<?php the_ID(); ?> -->

<?php do_action( 'dekanpro_after_article' ); ?>

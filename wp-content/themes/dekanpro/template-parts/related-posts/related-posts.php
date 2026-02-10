<?php
/**
 * The template for displaying Related posts on post details page.
 *
 * @package     Dekanpro
 * @author      Peregrine Themes
 * @since       1.0.0
 */


// Setup Related posts.

if ( ! dekanpro_option( 'related_posts_enable' ) ) {
	return;
}
$numbre_of_posts = dekanpro_option( 'related_post_number' );
$numbre_of_posts = $numbre_of_posts ? $numbre_of_posts : 3;
$dekanpro_args   = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => $numbre_of_posts, // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
	'orderby'             => 'date',
	'ignore_sticky_posts' => true,
	'category__in'        => wp_get_post_categories( get_the_ID() ),
	'post__not_in'        => array( get_the_ID() ),
	'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		array(
			'taxonomy' => 'post_format',
			'field'    => 'slug',
			'terms'    => array( 'post-format-quote' ),
			'operator' => 'NOT IN',
		),
	),
);

$dekanpro_args = apply_filters( 'dekanpro_related_posts_query_args', $dekanpro_args );

$dekanpro_posts = new WP_Query( $dekanpro_args );

// No posts found.
if ( ! $dekanpro_posts->have_posts() ) {
	return;
}

$dekanpro_related_posts_items_html = '';
$col                               = dekanpro_option( 'related_posts_column' );
while ( $dekanpro_posts->have_posts() ) :
	$dekanpro_posts->the_post();

	// Post items HTML markup.
	ob_start();
	?>

	<div class="col-md-<?php echo esc_attr( $col ); ?> col-sm-6 col-xs-12">
		<div class="dekanpro-post-item style-1 end rounded">
			<div class="dekanpro-post-thumb">
				<a href="<?php echo esc_url( dekanpro_entry_get_permalink() ); ?>" tabindex="0"></a>
				<div class="inner"><?php the_post_thumbnail( get_the_ID(), 'full' ); ?></div>
			</div><!-- END .dekanpro-post-thumb -->
			<div class="dekanpro-post-content">
							
				<div class="post-category">
					<?php dekanpro_entry_meta_category( ' ', false, apply_filters( 'dekanpro_pyml_category_limit', 3 ) ); ?>
				</div>

				<?php get_template_part( 'template-parts/entry/entry-header' ); ?>

				<div class="entry-meta">
					<div class="entry-meta-elements">
						<?php
						dekanpro_entry_meta_author();
						?>
					</div>
				</div><!-- END .entry-meta -->

			</div><!-- END .dekanpro-post-content -->			
		</div><!-- END .dekanpro-post-item -->
	</div>
	<?php
	$dekanpro_related_posts_items_html .= ob_get_clean();
endwhile;

// Restore original Post Data.
wp_reset_postdata();

// Title.
$dekanpro_related_posts_title = dekanpro_option( 'related_posts_heading' );

?>
<div id="related_posts" class="mt-5">
	<div class="dekanpro-rp slider-overlay-1 <?php echo esc_attr( $dekanpro_classes ); ?>">
		<div class="dekanpro-rp-container">
			<div class="dekanpro-flex-row">
				<div class="col-xs-12">
					<div class="dekanpro-card-items">
						<div class="h4 widget-title">
							<?php if ( $dekanpro_related_posts_title ) : ?>
								<?php echo esc_html( $dekanpro_related_posts_title ); ?>
							<?php endif; ?>
						</div>
						<div class="dekanpro-flex-row gy-4">
							<?php echo $dekanpro_related_posts_items_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					</div>
				</div>
			</div><!-- END .dekanpro-card-items -->
		</div>
	</div><!-- END .dekanpro-rp -->
</div><!-- END #related_posts -->

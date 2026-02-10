<?php
/**
 * The template for displaying PYML Slider.
 *
 * @package     Dekanpro
 * @author      DekanPro
 * @since       1.0.0
 */


// Setup PYML posts.
$dekanpro_pyml_orderby = dekanpro_option( 'pyml_orderby' );
$dekanpro_pyml_order   = explode( '-', $dekanpro_pyml_orderby );

$dekanpro_args = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => dekanpro_option( 'pyml_post_number' ), // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
	'order'               => $dekanpro_pyml_order[1],
	'orderby'             => $dekanpro_pyml_order[0],
	'ignore_sticky_posts' => true,
	'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		array(
			'taxonomy' => 'post_format',
			'field'    => 'slug',
			'terms'    => array( 'post-format-quote' ),
			'operator' => 'NOT IN',
		),
	),
);

$dekanpro_pyml_categories = dekanpro_option( 'pyml_category' );

if ( ! empty( $dekanpro_pyml_categories ) ) {
	$dekanpro_args['category_name'] = implode( ', ', $dekanpro_pyml_categories );
}

$dekanpro_args = apply_filters( 'dekanpro_pyml_query_args', $dekanpro_args );

$dekanpro_posts = new WP_Query( $dekanpro_args );

// No posts found.
if ( ! $dekanpro_posts->have_posts() ) {
	return;
}

// $dekanpro_pyml_bgs_html   = '';
$dekanpro_pyml_items_html = '';

$dekanpro_pyml_elements = (array) dekanpro_option( 'pyml_elements' );

$dekanpro_posts_per_page = 'col-md-' . ceil( esc_attr( 12 / $dekanpro_args['posts_per_page'] ) ) . ' col-sm-6 col-xs-12';

while ( $dekanpro_posts->have_posts() ) :
	$dekanpro_posts->the_post();

	// Post items HTML markup.
	ob_start();
	?>
	<div class="<?php echo esc_attr( $dekanpro_posts_per_page ); ?>">
		<div class="dekanpro-post-item style-1 end rounded">
			<div class="dekanpro-post-thumb">
				<a href="<?php echo esc_url( dekanpro_entry_get_permalink() ); ?>" tabindex="0"></a>
				<div class="inner"><?php the_post_thumbnail( get_the_ID(), 'full' ); ?></div>
			</div><!-- END .dekanpro-post-thumb -->
			<div class="dekanpro-post-content">
							
				<?php if ( isset( $dekanpro_pyml_elements['category'] ) && $dekanpro_pyml_elements['category'] ) { ?>
					<div class="post-category">
						<?php dekanpro_entry_meta_category( ' ', false, apply_filters( 'dekanpro_pyml_category_limit', 3 ) ); ?>
					</div>
				<?php } ?>

				<?php get_template_part( 'template-parts/entry/entry-header' ); ?>

				<?php if ( isset( $dekanpro_pyml_elements['meta'] ) && $dekanpro_pyml_elements['meta'] ) { ?>
					<div class="entry-meta">
						<div class="entry-meta-elements">
							<?php
							dekanpro_entry_meta_author();

							dekanpro_entry_meta_date(
								array(
									'show_modified'   => false,
									'published_label' => '',
								)
							);
							?>
						</div>
					</div><!-- END .entry-meta -->
				<?php } ?>

			</div><!-- END .dekanpro-post-content -->			
		</div><!-- END .dekanpro-post-item -->
	</div>
	<?php
	$dekanpro_pyml_items_html .= ob_get_clean();
endwhile;

// Restore original Post Data.
wp_reset_postdata();

// Container.
$dekanpro_pyml_container = dekanpro_option( 'pyml_container' );
$dekanpro_pyml_container = 'full-width' === $dekanpro_pyml_container ? 'dekanpro-container dekanpro-container__wide' : 'dekanpro-container';

// Title.
$dekanpro_pyml_title = dekanpro_option( 'pyml_title' );

// Classes.
$dekanpro_classes  = '';
$dekanpro_classes .= dekanpro_option( 'pyml_card_border' ) ? ' dekanpro-card__boxed' : '';
$dekanpro_classes .= dekanpro_option( 'pyml_card_shadow' ) ? ' dekanpro-card-shadow' : '';

?>

<div class="dekanpro-pyml slider-overlay-1 <?php echo esc_attr( $dekanpro_classes ); ?>">
	<div class="dekanpro-pyml-container <?php echo esc_attr( $dekanpro_pyml_container ); ?>">
		<div class="dekanpro-flex-row">
			<div class="col-xs-12">
				<div class="dekanpro-card-items">
					<div class="h4 widget-title">
						<?php if ( $dekanpro_pyml_title ) : ?>
						<span><?php echo esc_html( $dekanpro_pyml_title ); ?></span>
						<?php endif; ?>
					</div>
					<div class="dekanpro-flex-row gy-4">
						<?php echo wp_kses_post( $dekanpro_pyml_items_html ); ?>
					</div>
				</div>
			</div>
		</div><!-- END .dekanpro-card-items -->
	</div>
</div><!-- END .dekanpro-pyml -->

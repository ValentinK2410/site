<?php
/**
 * The template for displaying Ticker Slider.
 *
 * @package     Dekanpro
 * @author      DekanPro
 * @since       1.0.0
 */


// Setup Ticker posts.
$dekanpro_args = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => dekanpro_option( 'ticker_post_number' ), // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
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

$dekanpro_ticker_categories = dekanpro_option( 'ticker_category' );

if ( ! empty( $dekanpro_ticker_categories ) ) {
	$dekanpro_args['category_name'] = implode( ', ', $dekanpro_ticker_categories );
}

$dekanpro_args = apply_filters( 'dekanpro_ticker_query_args', $dekanpro_args );

$dekanpro_posts = new WP_Query( $dekanpro_args );

// No posts found.
if ( ! $dekanpro_posts->have_posts() ) {
	return;
}

$dekanpro_ticker_items_html = '';

$dekanpro_ticker_elements = (array) dekanpro_option( 'ticker_elements' );

$dekanpro_ticker_type = dekanpro_option( 'ticker_type' );

$dekanpro_ticker_slide = $dekanpro_ticker_type === 'one-ticker' ? 'ticker-item' : '';

while ( $dekanpro_posts->have_posts() ) :
	$dekanpro_posts->the_post();

	// Post items HTML markup.
	ob_start();
	?>
	<div class="<?php echo esc_attr( $dekanpro_ticker_slide ); ?>">
		<div class="ticker-slide-item">

			<?php if ( has_post_thumbnail() ) { ?>
			<div class="ticker-slider-backgrounds">
				<a href="<?php echo esc_url( dekanpro_entry_get_permalink() ); ?>">
					<?php the_post_thumbnail( 'thumbnail' ); ?>
				</a>
			</div><!-- END .ticker-slider-items -->
			<?php } ?>

			<div class="slide-inner">				

				<?php if ( get_the_title() ) { ?>
					<h6><a href="<?php echo esc_url( dekanpro_entry_get_permalink() ); ?>"><?php the_title(); ?></a></h6>
				<?php } ?>

				<?php if ( isset( $dekanpro_ticker_elements['meta'] ) && $dekanpro_ticker_elements['meta'] ) { ?>
					<div class="entry-meta">
						<div class="entry-meta-elements">
							<?php
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

			</div><!-- END .slide-inner -->
		</div><!-- END .ticker-slide-item -->
	</div><!-- END .swiper-slide -->
	<?php
	$dekanpro_ticker_items_html .= ob_get_clean();
endwhile;

// Restore original Post Data.
wp_reset_postdata();

$dekanpro_ticker_title = dekanpro_option( 'ticker_title' );

?>

<div class="dekanpro-ticker <?php echo esc_attr( $dekanpro_ticker_type ); ?>">
	<div class="dekanpro-ticker-container dekanpro-container">
		<div class="dekanpro-flex-row">
			<div class="col-xs-12">
				<div class="dekanpro-card-items">
					<?php if ( $dekanpro_ticker_title ) : ?>
					<div class="h4 widget-title">
						<?php echo esc_html( $dekanpro_ticker_title ); ?>
					</div>
					<?php endif; ?>
					<?php
						$dekanpro_ticker_direction = 'left';
						$dekanpro_ticker_dir       = 'ltr';
					if ( is_rtl() ) {
						$dekanpro_ticker_direction = 'right';
						$dekanpro_ticker_dir       = 'ltr';
					}
					?>
					<?php if ( 'one-ticker' === $dekanpro_ticker_type ) : ?>
					<div class="ticker-slider-box">
						<div class="ticker-slider-wrap" direction="<?php echo esc_attr( $dekanpro_ticker_direction ); ?>" dir="<?php echo esc_attr( $dekanpro_ticker_dir ); ?>">
							<?php echo wp_kses_post( $dekanpro_ticker_items_html ); ?>
						</div>
					</div>
					<div class="ticker-slider-controls">
						<button class="ticker-slider-pause"><i class="fas fa-pause"></i></button>						
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div><!-- END .ticker-slider-items -->
	</div>
</div><!-- END .dekanpro-ticker -->

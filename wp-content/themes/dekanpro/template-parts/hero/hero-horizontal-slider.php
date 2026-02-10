<?php
/**
 * The template for displaying Hero Horizontal Slider.
 *
 * @package     Dekanpro
 * @author      DekanPro
 * @since       1.0.0
 */


// Setup Hero posts.
$dekanpro_hero_slider_orderby = dekanpro_option( 'hero_slider_orderby' );
$dekanpro_hero_slider_order   = explode( '-', $dekanpro_hero_slider_orderby );

$dekanpro_args = array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => dekanpro_option( 'hero_slider_post_number' ), // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
	'order'               => $dekanpro_hero_slider_order[1],
	'orderby'             => $dekanpro_hero_slider_order[0],
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

$dekanpro_hero_categories = dekanpro_option( 'hero_slider_category' );

if ( ! empty( $dekanpro_hero_categories ) ) {
	$dekanpro_args['category_name'] = implode( ', ', $dekanpro_hero_categories );
}

$dekanpro_args = apply_filters( 'dekanpro_hero_slider_query_args', $dekanpro_args );

$dekanpro_posts = new WP_Query( $dekanpro_args );

// No posts found.
if ( ! $dekanpro_posts->have_posts() ) {
	return;
}

$dekanpro_hero_items_html = '';

$dekanpro_hero_elements       = (array) dekanpro_option( 'hero_slider_elements' );
$dekanpro_hero_readmore       = isset( $dekanpro_hero_elements['read_more'] ) && $dekanpro_hero_elements['read_more'] ? ' dekanpro-hero-readmore' : '';
$dekanpro_hero_read_more_text = dekanpro_option( 'hero_slider_read_more' );

while ( $dekanpro_posts->have_posts() ) :
	$dekanpro_posts->the_post();

	// Post items HTML markup.
	ob_start();

	?>
	<div class="swiper-slide">
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'dekanpro-article' ); ?><?php dekanpro_schema_markup( 'article' ); ?>>
			<div class="dekanpro-blog-entry-wrapper dekanpro-thumb-hero dekanpro-thumb-left">
				<div class="post-thumb entry-media thumbnail">
					<a href="<?php echo esc_url( dekanpro_entry_get_permalink() ); ?>" class="entry-image-link">
						<?php the_post_thumbnail( get_the_ID(), 'full' ); ?>
					</a>
				</div>
				<div class="dekanpro-entry-content-wrapper">

				<?php if ( isset( $dekanpro_hero_elements['category'] ) && $dekanpro_hero_elements['category'] ) { ?>
					<div class="post-category">
						<?php dekanpro_entry_meta_category( ' ', false, apply_filters( 'dekanpro_hero_horizontal_category_limit', 3 ) ); ?>
					</div>
				<?php } ?>

				<?php if ( get_the_title() ) { ?>
				<header class="entry-header">
					<h4 class="entry-title"><a href="<?php echo esc_url( dekanpro_entry_get_permalink() ); ?>"><?php the_title(); ?></a></h4>
				</header>
				<?php } ?>

				<?php get_template_part( 'template-parts/entry/entry-summary' ); ?>

				<?php if ( $dekanpro_hero_readmore ) { ?>
					<footer class="entry-footer">
						<a href="<?php echo esc_url( dekanpro_entry_get_permalink() ); ?>" class="dekanpro-btn btn-text-1" role="button"><span><?php echo esc_html( $dekanpro_hero_read_more_text ); ?></span></a>
					</footer>
				<?php } ?>

				<?php if ( isset( $dekanpro_hero_elements['meta'] ) && $dekanpro_hero_elements['meta'] ) { ?>
					<?php
						get_template_part( 'template-parts/entry/entry', 'meta', array( 'dekanpro_meta_callback' => 'dekanpro_get_hero_entry_meta_elements' ) );
					?>
					<!-- END .entry-meta -->
				<?php } ?>

			</div><!-- END .slide-inner -->
		</article><!-- END article -->
	</div>
	<?php
	$dekanpro_hero_items_html .= ob_get_clean();
endwhile;

// Restore original Post Data.
wp_reset_postdata();

// Hero container. {"delay": 8000, "disableOnInteraction": false}

?>
<div class="dekanpro-hero-slider dekanpro-blog-horizontal">
	<div class="dekanpro-horizontal-slider">

		<div class="dekanpro-hero-container dekanpro-container">
			<div class="dekanpro-flex-row">
				<div class="col-xs-12">
					<div class="dekanpro-swiper swiper" data-swiper-options='{
						"spaceBetween": 24,
						"slidesPerView": 1,
						"breakpoints": {
							"0": {
								"spaceBetween": 16
							},
							"768": {
								"spaceBetween": 16
							},
							"1200": {
								"spaceBetween": 24
							}
						},
						"loop": true,
						"autoHeight": true,
						"autoplay": {"delay": 12000, "disableOnInteraction": false},
						"speed": 1000,
						"navigation": {"nextEl": ".hero-next", "prevEl": ".hero-prev"}
					}'>
						<div class="swiper-wrapper">
							<?php echo wp_kses( $dekanpro_hero_items_html, dekanpro_get_allowed_html_tags() ); ?> 
						</div>
						<div class="swiper-button-next hero-next"></div>
						<div class="swiper-button-prev hero-prev"></div>
					</div>
				</div>
			</div>
		</div>

		<div class="dekanpro-spinner visible">
			<div></div>
			<div></div>
		</div>
	</div>
</div><!-- END .dekanpro-hero-slider -->

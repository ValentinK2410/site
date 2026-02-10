<?php

/**
 * Template parts.
 *
 * @package     Dekanpro
 * @author      Peregrine Themes
 * @since       1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds the meta tag to the site header.
 *
 * @since 1.0.0
 */
function dekanpro_meta_viewport() {
	echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
}
add_action( 'wp_head', 'dekanpro_meta_viewport', 1 );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 *
 * @since 1.0.0
 */
function dekanpro_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'dekanpro_pingback_header' );

/**
 * Adds the meta tag for website accent color.
 *
 * @since 1.0.0
 */
function dekanpro_meta_theme_color() {

	$color = dekanpro_option( 'accent_color' );

	if ( $color ) {
		printf( '<meta name="theme-color" content="%s">', esc_attr( $color ) );
	}
}
add_action( 'wp_head', 'dekanpro_meta_theme_color' );

/**
 * Outputs the theme top bar area.
 *
 * @since 1.0.0
 */
function dekanpro_topbar_output() {

	if ( ! dekanpro_is_top_bar_displayed() ) {
		return;
	}

	get_template_part( 'template-parts/topbar/topbar' );
}
add_action( 'dekanpro_header', 'dekanpro_topbar_output', 10 );

/**
 * Outputs the top bar widgets.
 *
 * @since 1.0.0
 * @param string $location Widget location in top bar.
 */
function dekanpro_topbar_widgets_output( $location ) {

	do_action( 'dekanpro_top_bar_widgets_before_' . $location );

	$dekanpro_top_bar_widgets = dekanpro_option( 'top_bar_widgets' );

	if ( is_array( $dekanpro_top_bar_widgets ) && ! empty( $dekanpro_top_bar_widgets ) ) {
		foreach ( $dekanpro_top_bar_widgets as $widget ) {

			if ( ! isset( $widget['values'] ) ) {
				continue;
			}

			if ( $location !== $widget['values']['location'] ) {
				continue;
			}

			if ( function_exists( 'dekanpro_top_bar_widget_' . $widget['type'] ) ) {

				$classes   = array();
				$classes[] = 'dekanpro-topbar-widget__' . esc_attr( $widget['type'] );
				$classes[] = 'dekanpro-topbar-widget';

				if ( isset( $widget['values']['visibility'] ) && $widget['values']['visibility'] ) {
					$classes[] = 'dekanpro-' . esc_attr( $widget['values']['visibility'] );
				}

				$classes = apply_filters( 'dekanpro_topbar_widget_classes', $classes, $widget );
				$classes = trim( implode( ' ', $classes ) );

				printf( '<div class="%s">', esc_attr( $classes ) );
				call_user_func( 'dekanpro_top_bar_widget_' . $widget['type'], $widget['values'] );
				printf( '</div><!-- END .dekanpro-topbar-widget -->' );
			}
		}
	}

	do_action( 'dekanpro_top_bar_widgets_after_' . $location );
}
add_action( 'dekanpro_topbar_widgets', 'dekanpro_topbar_widgets_output' );

/**
 * Outputs the theme header area.
 *
 * @since 1.0.0
 */
function dekanpro_header_output() {

	if ( ! dekanpro_is_header_displayed() ) {
		return;
	}

	get_template_part( 'template-parts/header/base' );
}
add_action( 'dekanpro_header', 'dekanpro_header_output', 20 );

/**
 * Outputs the header widgets in Header Widget Locations.
 *
 * @since 1.0.0
 * @param string $locations Widget location.
 */
function dekanpro_header_widgets( $locations ) {

	$locations   = (array) $locations;
	$all_widgets = (array) dekanpro_option( 'header_widgets' );

	dekanpro_header_widget_output( $locations, $all_widgets );
}
add_action( 'dekanpro_header_widget_location', 'dekanpro_header_widgets', 1 );

/**
 * Outputs the header widgets in Header Navigation Widget Locations.
 *
 * @since 1.0.0
 * @param string $locations Widget location.
 */
function dekanpro_header_navigation_widgets( $locations ) {

	$locations   = (array) $locations;
	$all_widgets = (array) dekanpro_option( 'header_navigation_widgets' );

	dekanpro_header_widget_output( $locations, $all_widgets );
}
add_action( 'dekanpro_header_navigation_widget_location', 'dekanpro_header_navigation_widgets', 1 );

/**
 * Outputs the content of theme header.
 *
 * @since 1.0.0
 */
function dekanpro_header_content_output() {

	// Get the selected header layout from Customizer.
	$header_layout = dekanpro_option( 'header_layout' );

	?>
	<div id="dekanpro-header-inner">
		<?php

		// Load header layout template.
		get_template_part( 'template-parts/header/header', $header_layout );

		?>
	</div><!-- END #dekanpro-header-inner -->
	<?php
}
add_action( 'dekanpro_header_content', 'dekanpro_header_content_output' );

/**
 * Outputs the main footer area.
 *
 * @since 1.0.0
 */
function dekanpro_footer_output() {

	if ( ! dekanpro_is_footer_displayed() ) {
		return;
	}

	get_template_part( 'template-parts/footer/base' );
}
add_action( 'dekanpro_footer', 'dekanpro_footer_output', 20 );

/**
 * Outputs the copyright area.
 *
 * @since 1.0.0
 */
function dekanpro_copyright_bar_output() {

	if ( ! dekanpro_is_copyright_bar_displayed() ) {
		return;
	}

	get_template_part( 'template-parts/footer/copyright/copyright' );
}
add_action( 'dekanpro_footer', 'dekanpro_copyright_bar_output', 30 );

/**
 * Outputs the copyright widgets.
 *
 * @since 1.0.0
 * @param string $location Widget location in copyright.
 */
function dekanpro_copyright_widgets_output( $location ) {

	do_action( 'dekanpro_copyright_widgets_before_' . $location );

	$dekanpro_widgets = dekanpro_option( 'copyright_widgets' );

	if ( is_array( $dekanpro_widgets ) && ! empty( $dekanpro_widgets ) ) {
		foreach ( $dekanpro_widgets as $widget ) {

			if ( ! isset( $widget['values'] ) ) {
				continue;
			}

			if ( isset( $widget['values'], $widget['values']['location'] ) && $location !== $widget['values']['location'] ) {
				continue;
			}

			if ( function_exists( 'dekanpro_copyright_widget_' . $widget['type'] ) ) {

				$classes   = array();
				$classes[] = 'dekanpro-copyright-widget__' . esc_attr( $widget['type'] );
				$classes[] = 'dekanpro-copyright-widget';

				if ( isset( $widget['values']['visibility'] ) && $widget['values']['visibility'] ) {
					$classes[] = 'dekanpro-' . esc_attr( $widget['values']['visibility'] );
				}

				$classes = apply_filters( 'dekanpro_copyright_widget_classes', $classes, $widget );
				$classes = trim( implode( ' ', $classes ) );

				printf( '<div class="%s">', esc_attr( $classes ) );
				call_user_func( 'dekanpro_copyright_widget_' . $widget['type'], $widget['values'] );
				printf( '</div><!-- END .dekanpro-copyright-widget -->' );
			}
		}
	}

	do_action( 'dekanpro_copyright_widgets_after_' . $location );
}
add_action( 'dekanpro_copyright_widgets', 'dekanpro_copyright_widgets_output' );

/**
 * Outputs the theme sidebar area.
 *
 * @since 1.0.0
 */
function dekanpro_sidebar_output() {

	if ( dekanpro_is_sidebar_displayed() ) {
		get_sidebar();
	}
}
add_action( 'dekanpro_sidebar', 'dekanpro_sidebar_output' );

/**
 * Outputs the back to top button.
 *
 * @since 1.0.0
 */
function dekanpro_back_to_top_output() {

	if ( ! dekanpro_option( 'scroll_top' ) ) {
		return;
	}

	get_template_part( 'template-parts/misc/back-to-top' );
}
add_action( 'dekanpro_after_page_wrapper', 'dekanpro_back_to_top_output' );

/**
 * Outputs the cursor dot.
 *
 * @since 1.0.0
 */
function dekanpro_cursor_dot_output() {

	if ( ! dekanpro_option( 'enable_cursor_dot' ) ) {
		return;
	}

	get_template_part( 'template-parts/misc/cursor-dot' );
}
add_action( 'dekanpro_after_page_wrapper', 'dekanpro_cursor_dot_output' );

/**
 * Outputs the theme page content.
 *
 * @since 1.0.0
 */
function dekanpro_page_header_template() {

	do_action( 'dekanpro_before_page_header' );

	if ( dekanpro_is_page_header_displayed() ) {
		if ( is_singular( 'post' ) ) {
			get_template_part( 'template-parts/header-page-title-single' );
		} else {
			get_template_part( 'template-parts/header-page-title' );
		}
	}

	do_action( 'dekanpro_after_page_header' );
}
add_action( 'dekanpro_page_header', 'dekanpro_page_header_template' );


/**
 * Outputs the theme Ticker News content.
 *
 * @since 1.0.0
 */
function dekanpro_blog_ticker() {

	if ( ! dekanpro_is_ticker_displayed() ) {
		return;
	}

	do_action( 'dekanpro_before_ticker' );

	// Enqueue Dekanpro Marquee script.
	if ( 'one-ticker' === dekanpro_option( 'ticker_type' ) ) {
		wp_enqueue_script( 'dekanpro-marquee' );
	}

	?>
	<div id="ticker">
		<?php get_template_part( 'template-parts/ticker/ticker' ); ?>
	</div><!-- END #ticker -->
	<?php

	do_action( 'dekanpro_after_ticker' );
}
add_action( 'dekanpro_after_masthead', 'dekanpro_blog_ticker', 29 );


/**
 * Outputs the theme blog hero content.
 *
 * @since 1.0.0
 */
function dekanpro_blog_hero() {

	if ( ! dekanpro_is_hero_displayed() ) {
		return;
	}

	// Hero type.
	$hero_type = dekanpro_option( 'hero_type' );

	do_action( 'dekanpro_before_hero' );

	// Enqueue Dekanpro Slider script.
	wp_enqueue_script( 'dekanpro-slider' );

	?>
	<div id="hero">
		<?php
			get_template_part( 'template-parts/hero/hero', $hero_type );
		?>
	</div><!-- END #hero -->
	<?php

	do_action( 'dekanpro_after_hero' );
}
add_action( 'dekanpro_after_masthead', 'dekanpro_blog_hero', 30 );


/**
 * Outputs the theme Blog Featured Links content.
 *
 * @since 1.0.0
 */
function dekanpro_blog_featured_links() {

	if ( ! dekanpro_is_featured_links_displayed() ) {
		return;
	}

	// Featured links type.
	$dekanpro_featured_links_type = dekanpro_option( 'featured_links_type' );

	$dekanpro_featured_links = dekanpro_option( 'featured_links' );

	// No items found.
	if ( ! $dekanpro_featured_links ) {
		return;
	}

	$features = array();

	foreach ( $dekanpro_featured_links as $dekanpro_featured_link ) {
		$features[] = array(
			'link'  => $dekanpro_featured_link['link'],
			'image' => $dekanpro_featured_link['image'],
		);
	}

	do_action( 'dekanpro_before_featured_links' );

	?>
	<div id="featured_links">
		<?php get_template_part( 'template-parts/featured-links/featured-links', $dekanpro_featured_links_type, array( 'features' => $features ) ); ?>
	</div><!-- END #featured_links -->
	<?php

	do_action( 'dekanpro_after_featured_links' );
}
add_action( 'dekanpro_after_masthead', 'dekanpro_blog_featured_links', 31 );


/**
 * Outputs the theme Blog PYML content.
 *
 * @since 1.0.0
 */
function dekanpro_blog_pyml() {

	if ( ! dekanpro_is_pyml_displayed() ) {
		return;
	}

	$pyml_type = dekanpro_option( 'pyml_type' );

	do_action( 'dekanpro_before_pyml' );

	?>
	<div id="pyml">
		<?php get_template_part( 'template-parts/pyml/pyml', $pyml_type ); ?>
	</div><!-- END #pyml -->
	<?php

	do_action( 'dekanpro_after_pyml' );
}
add_action( 'dekanpro_after_container', 'dekanpro_blog_pyml', 32 );


/**
 * Outputs the theme Body Animation.
 *
 * @since 1.0.0
 */
function dekanpro_body_animation() {

	$body_animation_option = dekanpro_option( 'body_animation' );

	if ( '0' === $body_animation_option ) {
		return;
	}

	do_action( 'dekanpro_before_body_animation' );
	?>
	<?php if ( '1' === $body_animation_option ) : ?>
	<div class="dekanpro-glassmorphism">
		<span class="block one"></span>
		<span class="block two"></span>
	</div>
		<?php
	endif;
	do_action( 'dekanpro_after_body_animation' );
}
add_action( 'dekanpro_main_end', 'dekanpro_body_animation', 33 );

function dekanpro_blog_heading_content() {

	if ( $blog_heading = dekanpro_option( 'blog_heading' ) ) {
		echo '<div id="dekanpro-blog-heading">';
		echo wp_kses( $blog_heading, dekanpro_get_allowed_html_tags() );
		echo '</div>';
	}
}
add_action( 'dekanpro_blog_heading', 'dekanpro_blog_heading_content' );

/**
 * Outputs the queried articles.
 *
 * @since 1.0.0
 */
function dekanpro_content() {
	global $wp_query;
	$dekanpro_blog_layout        = dekanpro_option( 'blog_masonry' ) ? 'masonries' : '';
	$dekanpro_blog_layout_column = 12;

	if ( dekanpro_option( 'blog_layout' ) != 'blog-horizontal' ) :
		$dekanpro_blog_layout_column = dekanpro_option( 'blog_layout_column' );
	endif;

	if ( have_posts() ) :

		if ( is_home() ) {
			do_action( 'dekanpro_blog_heading' );
		}
		echo '<div class="dekanpro-flex-row g-4 ' . $dekanpro_blog_layout . '">';

		$ads_info = dekanpro_algorithm_to_push_ads_in_archive();
		$count    = 0;
		while ( have_posts() ) :
			the_post();

			if ( is_array( $ads_info ) && ! is_null( $ads_info['ads_to_render'] ) ) :
				if ( in_array( $wp_query->current_post, $ads_info['random_numbers'] ) ) :
					echo '<div class="col-md-' . $dekanpro_blog_layout_column . ' col-sm-' . $dekanpro_blog_layout_column . ' col-xs-12">';
					dekanpro_random_post_archive_advertisement_part( is_array( $ads_info['ads_to_render'] ) ? $ads_info['ads_to_render'][ $count ] : $ads_info['ads_to_render'] );
					echo '</div>';
					$count++;
				endif;
			endif;

			echo '<div class="col-md-' . $dekanpro_blog_layout_column . ' col-sm-' . $dekanpro_blog_layout_column . ' col-xs-12">';
			get_template_part( 'template-parts/content/content', dekanpro_get_article_feed_layout() );
			echo '</div>';
		endwhile;
		echo '</div>';
		dekanpro_pagination();

	else :
		get_template_part( 'template-parts/content/content', 'none' );
	endif;
}
add_action( 'dekanpro_content', 'dekanpro_content' );
add_action( 'dekanpro_content_archive', 'dekanpro_content' );
add_action( 'dekanpro_content_search', 'dekanpro_content' );

/**
 * Outputs the theme single content.
 *
 * @since 1.0.0
 */
function dekanpro_content_singular() {

	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();

			if ( is_singular( 'post' ) ) {
				do_action( 'dekanpro_content_single' );
			} else {
				do_action( 'dekanpro_content_page' );
			}

		endwhile;
	else :
		get_template_part( 'template-parts/content/content', 'none' );
	endif;
}
add_action( 'dekanpro_content_singular', 'dekanpro_content_singular' );


/**
 * Outputs the theme 404 page content.
 *
 * @since 1.0.0
 */
function dekanpro_404_page_content() {

	get_template_part( 'template-parts/content/content', '404' );
}
add_action( 'dekanpro_content_404', 'dekanpro_404_page_content' );

/**
 * Outputs the theme page content.
 *
 * @since 1.0.0
 */
function dekanpro_content_page() {

	get_template_part( 'template-parts/content/content', 'page' );
}
add_action( 'dekanpro_content_page', 'dekanpro_content_page' );

/**
 * Outputs the theme single post content.
 *
 * @since 1.0.0
 */
function dekanpro_content_single() {

	get_template_part( 'template-parts/content/content', 'single' );
}
add_action( 'dekanpro_content_single', 'dekanpro_content_single' );

/**
 * Outputs the comments template.
 *
 * @since 1.0.0
 */
function dekanpro_output_related_posts() {

	if ( 'post' == get_post_type() ) {
		get_template_part( 'template-parts/related-posts/related', 'posts' );
	}
}
add_action( 'dekanpro_after_singular', 'dekanpro_output_related_posts' );

/**
 * Outputs the comments template.
 *
 * @since 1.0.0
 */
function dekanpro_output_comments() {
	comments_template();
}
add_action( 'dekanpro_after_singular', 'dekanpro_output_comments' );

/**
 * Outputs the theme archive page info.
 *
 * @since 1.0.0
 */
function dekanpro_archive_info() {

	// Author info.
	if ( is_author() ) {
		get_template_part( 'template-parts/entry/entry', 'about-author' );
	}
}
add_action( 'dekanpro_before_content', 'dekanpro_archive_info' );

/**
 * Outputs more posts button to author description box.
 *
 * @since 1.0.0
 */
function dekanpro_add_author_posts_button() {
	if ( ! is_author() ) {
		get_template_part( 'template-parts/entry/entry', 'author-posts-button' );
	}
}
add_action( 'dekanpro_entry_after_author_description', 'dekanpro_add_author_posts_button' );

/**
 * Outputs Comments Toggle button.
 *
 * @since 1.0.0
 */
function dekanpro_comments_toggle() {

	if ( dekanpro_comments_toggle_displayed() ) {
		get_template_part( 'template-parts/entry/entry-show-comments' );
	}
}
add_action( 'dekanpro_before_comments', 'dekanpro_comments_toggle' );

/**
 * Outputs Page Preloader.
 *
 * @since 1.0.0
 */
function dekanpro_preloader() {

	if ( ! dekanpro_is_preloader_displayed() ) {
		return;
	}

	get_template_part( 'template-parts/preloader/base' );
}
add_action( 'dekanpro_before_page_wrapper', 'dekanpro_preloader' );

/**
 * Outputs breadcrumbs after header.
 *
 * @since  1.0.0
 * @return void
 */
function dekanpro_breadcrumb_after_header_output() {

	if ( 'below-header' === dekanpro_option( 'breadcrumbs_position' ) && dekanpro_has_breadcrumbs() ) {

		$alignment = 'dekanpro-text-align-' . dekanpro_option( 'breadcrumbs_alignment' );

		$args = array(
			'container_before' => '<div class="dekanpro-breadcrumbs"><div class="dekanpro-container ' . $alignment . '">',
			'container_after'  => '</div></div>',
		);

		dekanpro_breadcrumb( $args );
	}
}
add_action( 'dekanpro_main_start', 'dekanpro_breadcrumb_after_header_output' );

/**
 * Outputs breadcumbs in page header.
 *
 * @since  1.0.0
 * @return void
 */
function dekanpro_breadcrumb_page_header_output() {

	if ( dekanpro_page_header_has_breadcrumbs() ) {

		if ( is_singular( 'post' ) ) {
			$args = array(
				'container_before' => '<div class="dekanpro-container dekanpro-breadcrumbs">',
				'container_after'  => '</div>',
			);
		} else {
			$args = array(
				'container_before' => '<div class="dekanpro-breadcrumbs">',
				'container_after'  => '</div>',
			);
		}

		dekanpro_breadcrumb( $args );
	}
}
add_action( 'dekanpro_page_header_end', 'dekanpro_breadcrumb_page_header_output' );

/**
 * Output the main navigation template.
 */
function dekanpro_main_navigation_template() {
	get_template_part( 'template-parts/header/navigation' );
}

/**
 * Output the Header logo template.
 */
function dekanpro_header_logo_template() {
	get_template_part( 'template-parts/header/logo' );
}

function dekanpro_about_button() {
	$button_widgets = dekanpro_option( 'about_widgets' );

	if ( empty( $button_widgets ) ) {
		return;
	}
	foreach ( $button_widgets as $widget ) {
		call_user_func( 'dekanpro_about_widget_' . $widget['type'], $widget['values'] );
	}
}

function dekanpro_cta_widgets() {
	$widgets = dekanpro_option( 'cta_widgets' );

	if ( empty( $widgets ) ) {
		return;
	}
	foreach ( $widgets as $widget ) {
		call_user_func( 'dekanpro_cta_widget_' . $widget['type'], $widget['values'] );
	}
}

function dekanpro_advertisement_part( $arg = '' ) {

	if ( $arg === '' ) {
		return;
	}

	$ad_widgets = dekanpro_option( 'ad_widgets' );

	// get all array elements from $ad_widgets in which 'display_area' key has value $arg = 'before_post_content'
	$arr_widgets = array_filter(
		$ad_widgets,
		function( $widget ) use ( $arg ) {
			return isset( $widget['values']['display_area'] ) && in_array( $arg, $widget['values']['display_area'] );
		}
	);

	if ( ! empty( $arr_widgets ) ) :
		foreach ( $arr_widgets as $widget ) {
			if ( function_exists( 'dekanpro_ad_widget_' . $widget['type'] ) ) {
				$classes   = array();
				$classes[] = 'dekanpro-ad-widget__' . esc_attr( $widget['type'] );
				$classes[] = 'dekanpro-ad-widget';

				if ( isset( $widget['values']['visibility'] ) && $widget['values']['visibility'] ) {
					$classes[] = 'dekanpro-' . esc_attr( $widget['values']['visibility'] );
				}

				$classes = apply_filters( 'dekanpro_ad_widget_classes', $classes, $widget );
				$classes = trim( implode( ' ', $classes ) );

				printf( '<div class="%s">', esc_attr( $classes ) );
				call_user_func( 'dekanpro_ad_widget_' . $widget['type'], $widget['values'] );
				printf( '</div>' );
			}
		}
	endif;

}
add_action( 'dekanpro_before_single_content', 'dekanpro_advertisement_part', 10, 1 );
add_action( 'dekanpro_after_single_content', 'dekanpro_advertisement_part', 10, 1 );
add_action( 'dekanpro_before_masthead', 'dekanpro_advertisement_part', 10, 1 );
add_action( 'dekanpro_after_masthead', 'dekanpro_advertisement_part', 10, 1 );
add_action( 'dekanpro_before_colophon', 'dekanpro_advertisement_part', 10, 1 );
add_action( 'dekanpro_after_colophon', 'dekanpro_advertisement_part', 10, 1 );
add_action( 'dekanpro_header_4_ad', 'dekanpro_advertisement_part', 10, 1 );
add_action( 'dekanpro_before_content_area', 'dekanpro_advertisement_part', 10, 1 );

<?php
/**
 * Template part for displaying entry thumbnail (featured image).
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
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

// Get default post media.
$dekanpro_media = dekanpro_get_post_media( '' );

if ( ! $dekanpro_media || post_password_required() ) {
	return;
}

$dekanpro_post_format = get_post_format();

// Wrap with link for non-singular pages.
if ( 'link' === $dekanpro_post_format || ! is_single( get_the_ID() ) ) {

	$dekanpro_icon = '';

	if ( is_sticky() ) {
		$dekanpro_icon = sprintf(
			'<span class="entry-media-icon is_sticky" title="%1$s" aria-hidden="true"><span class="entry-media-icon-wrapper">%2$s%3$s</span></span>',
			esc_attr__( 'Featured', 'dekanpro' ),
			dekanpro()->icons->get_svg(
				'pin',
				array(
					'class'       => 'top-icon',
					'aria-hidden' => 'true',
				)
			),
			dekanpro()->icons->get_svg( 'pin', array( 'aria-hidden' => 'true' ) )
		);
	} elseif ( 'video' === $dekanpro_post_format ) {

		$dekanpro_icon = sprintf(
			'<span class="entry-media-icon" aria-hidden="true"><span class="entry-media-icon-wrapper">%1$s%2$s</span></span>',
			dekanpro()->icons->get_svg(
				'play-2',
				array(
					'class'       => 'top-icon',
					'aria-hidden' => 'true',
				)
			),
			dekanpro()->icons->get_svg( 'play-2', array( 'aria-hidden' => 'true' ) )
		);
	} elseif ( 'link' === $dekanpro_post_format ) {
		$dekanpro_icon = sprintf(
			'<span class="entry-media-icon" title="%1$s" aria-hidden="true"><span class="entry-media-icon-wrapper">%2$s%3$s</span></span>',
			esc_url( dekanpro_entry_get_permalink() ),
			dekanpro()->icons->get_svg(
				'external-link',
				array(
					'class'       => 'top-icon',
					'aria-hidden' => 'true',
				)
			),
			dekanpro()->icons->get_svg( 'external-link', array( 'aria-hidden' => 'true' ) )
		);
	}

	$dekanpro_icon = apply_filters( 'dekanpro_post_format_media_icon', $dekanpro_icon, $dekanpro_post_format );

	$dekanpro_media = sprintf(
		'<a href="%1$s" class="entry-image-link">%2$s%3$s</a>',
		esc_url( dekanpro_entry_get_permalink() ),
		$dekanpro_media,
		$dekanpro_icon
	);
}

$dekanpro_media = apply_filters( 'dekanpro_post_thumbnail', $dekanpro_media );

// Print the post thumbnail.
echo wp_kses(
	sprintf(
		'<div class="post-thumb entry-media thumbnail">%1$s</div>',
		$dekanpro_media
	),
	dekanpro_get_allowed_html_tags()
);

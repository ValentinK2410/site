<?php
/**
 * Template part for displaying page featured image.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package DekanPro
 * @author Peregrine Themes
 * @since   1.0.0
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

$dekanpro_media = apply_filters( 'dekanpro_post_thumbnail', $dekanpro_media, get_the_ID() );

$dekanpro_classes = array( 'post-thumb', 'entry-media', 'thumbnail' );

$dekanpro_classes = apply_filters( 'dekanpro_post_thumbnail_wrapper_classes', $dekanpro_classes, get_the_ID() );
$dekanpro_classes = trim( implode( ' ', array_unique( $dekanpro_classes ) ) );

// Print the post thumbnail.
echo wp_kses_post(
	sprintf(
		'<div class="%2$s">%1$s</div>',
		$dekanpro_media,
		esc_attr( $dekanpro_classes )
	)
);

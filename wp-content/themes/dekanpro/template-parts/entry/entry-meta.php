<?php
/**
 * Template part for displaying entry meta info.
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

/**
 * Only show meta tags for posts.
 */
if ( ! in_array( get_post_type(), (array) apply_filters( 'dekanpro_entry_meta_post_type', array( 'post' ) ), true ) ) {
	return;
}

do_action( 'dekanpro_before_entry_meta' );

// Get meta items to be displayed.
$dekanpro_meta_elements = dekanpro_get_entry_meta_elements();

if ( isset( $args['dekanpro_meta_callback'] ) ) {
	$dekanpro_meta_elements = call_user_func( $args['dekanpro_meta_callback'] );
}

if ( ! empty( $dekanpro_meta_elements ) ) {

	echo '<div class="entry-meta"><div class="entry-meta-elements">';

	do_action( 'dekanpro_before_entry_meta_elements' );

	// Loop through meta items.
	foreach ( $dekanpro_meta_elements as $dekanpro_meta_item ) {

		// Call a template tag function.
		if ( function_exists( 'dekanpro_entry_meta_' . $dekanpro_meta_item ) ) {
			call_user_func( 'dekanpro_entry_meta_' . $dekanpro_meta_item );
		}
	}

	// Add edit post link.
	$dekanpro_edit_icon = dekanpro()->icons->get_meta_icon( 'edit', dekanpro()->icons->get_svg( 'edit-3', array( 'aria-hidden' => 'true' ) ) );

	dekanpro_edit_post_link(
		sprintf(
			wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers */
				$dekanpro_edit_icon . __( 'Edit <span class="screen-reader-text">%s</span>', 'dekanpro' ),
				dekanpro_get_allowed_html_tags()
			),
			get_the_title()
		),
		'<span class="edit-link">',
		'</span>'
	);

	do_action( 'dekanpro_after_entry_meta_elements' );

	echo '</div></div>';
}

do_action( 'dekanpro_after_entry_meta' );

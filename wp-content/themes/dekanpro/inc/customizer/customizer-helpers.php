<?php
/**
 * Dekanpro Customizer helper functions.
 *
 * @package     Dekanpro
 * @author      DekanPro
 * @since       1.0.0
 */

/**
 * Do not allow direct script access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns array of available widgets.
 *
 * @since 1.0.0
 * @return array, $widgets array of available widgets.
 */
function dekanpro_get_customizer_widgets() {

	$widgets = array(
		'text'           => 'Dekanpro_Customizer_Widget_Text',
		'advertisements' => 'Dekanpro_Customizer_Widget_Advertisements',
		'nav'            => 'Dekanpro_Customizer_Widget_Nav',
		'socials'        => 'Dekanpro_Customizer_Widget_Socials',
		'search'         => 'Dekanpro_Customizer_Widget_Search',
		'darkmode'       => 'Dekanpro_Customizer_Widget_Darkmode',
		'button'         => 'Dekanpro_Customizer_Widget_Button',
	);

	return apply_filters( 'dekanpro_customizer_widgets', $widgets );
}

/**
 * Get choices for "Hide on" customizer options.
 *
 * @since  1.0.0
 * @return array
 */
function dekanpro_get_display_choices() {

	// Default options.
	$return = array(
		'home'       => array(
			'title' => esc_html__( 'Home Page', 'dekanpro' ),
		),
		'posts_page' => array(
			'title' => esc_html__( 'Blog / Posts Page', 'dekanpro' ),
		),
		'search'     => array(
			'title' => esc_html__( 'Search', 'dekanpro' ),
		),
		'archive'    => array(
			'title' => esc_html__( 'Archive', 'dekanpro' ),
			'desc'  => esc_html__( 'Dynamic pages such as categories, tags, custom taxonomies...', 'dekanpro' ),
		),
		'post'       => array(
			'title' => esc_html__( 'Single Post', 'dekanpro' ),
		),
		'page'       => array(
			'title' => esc_html__( 'Single Page', 'dekanpro' ),
		),
	);

	// Get additionally registered post types.
	$post_types = get_post_types(
		array(
			'public'   => true,
			'_builtin' => false,
		),
		'objects'
	);

	if ( is_array( $post_types ) && ! empty( $post_types ) ) {
		foreach ( $post_types as $slug => $post_type ) {
			$return[ $slug ] = array(
				'title' => $post_type->label,
			);
		}
	}

	return apply_filters( 'dekanpro_display_choices', $return );
}

/**
 * Get device choices for "Display on" customizer options.
 *
 * @since  1.0.0
 * @return array
 */
function dekanpro_get_device_choices() {

	// Default options.
	$return = array(
		'desktop' => array(
			'title' => esc_html__( 'Hide On Desktop', 'dekanpro' ),
		),
		'tablet'  => array(
			'title' => esc_html__( 'Hide On Tablet', 'dekanpro' ),
		),
		'mobile'  => array(
			'title' => esc_html__( 'Hide On Mobile', 'dekanpro' ),
		),
	);

	return apply_filters( 'dekanpro_device_choices', $return );
}

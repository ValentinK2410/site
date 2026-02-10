<?php
/**
 * Template part for displaying media of the entry.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
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

$dekanpro_post_format = get_post_format();

if ( is_single() ) {
	$dekanpro_post_format = '';
}

do_action( 'dekanpro_before_entry_thumbnail' );

get_template_part( 'template-parts/entry/format/media', $dekanpro_post_format );

do_action( 'dekanpro_after_entry_thumbnail' );

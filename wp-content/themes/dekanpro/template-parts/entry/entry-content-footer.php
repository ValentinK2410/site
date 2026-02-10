<?php
/**
 * Template part for displaying entry tags.
 *
 * @package     Dekanpro
 * @author      DekanPro
 * @since       1.0.0
 */

$dekanpro_entry_elements    = dekanpro_option( 'single_post_elements' );
$dekanpro_entry_footer_tags = isset( $dekanpro_entry_elements['tags'] ) && $dekanpro_entry_elements['tags'] && has_tag();
$dekanpro_entry_footer_date = isset( $dekanpro_entry_elements['last-updated'] ) && $dekanpro_entry_elements['last-updated'] && get_the_time( 'U' ) !== get_the_modified_time( 'U' );

$dekanpro_entry_footer_tags = apply_filters( 'dekanpro_display_entry_footer_tags', $dekanpro_entry_footer_tags );
$dekanpro_entry_footer_date = apply_filters( 'dekanpro_display_entry_footer_date', $dekanpro_entry_footer_date );

// Nothing is enabled, don't display the div.
if ( ! $dekanpro_entry_footer_tags && ! $dekanpro_entry_footer_date ) {
	return;
}
?>

<?php do_action( 'dekanpro_before_entry_footer' ); ?>

<div class="entry-footer">

	<?php
	// Post Tags.
	if ( $dekanpro_entry_footer_tags ) {
		dekanpro_entry_meta_tag(
			'<div class="post-tags"><span class="cat-links">',
			'',
			'</span></div>',
			0,
			false
		);
	}

	// Last Updated Date.
	if ( $dekanpro_entry_footer_date ) {

		$dekanpro_before = '<span class="last-updated dekanpro-iflex-center">';

		if ( true === dekanpro_option( 'single_entry_meta_icons' ) ) {
			$dekanpro_before .= dekanpro()->icons->get_svg( 'edit-3' );
		}

		dekanpro_entry_meta_date(
			array(
				'show_published' => false,
				'show_modified'  => true,
				'before'         => $dekanpro_before,
				'after'          => '</span>',
			)
		);
	}
	?>

</div>

<?php do_action( 'dekanpro_after_entry_footer' ); ?>

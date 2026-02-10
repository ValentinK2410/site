<?php
/**
 * Template part for displaying page header for single post.
 *
 * @package DekanPro
 * @author Peregrine Themes
 * @since   1.0.0
 */

?>

<div <?php dekanpro_page_header_classes(); ?><?php dekanpro_page_header_atts(); ?>>

	<?php do_action( 'dekanpro_page_header_start' ); ?>

	<?php if ( 'in-page-header' === dekanpro_option( 'single_title_position' ) ) { ?>

		<div class="dekanpro-container">
			<div class="dekanpro-page-header-wrapper">

				<?php
				if ( dekanpro_single_post_displays( 'category' ) ) {
					get_template_part( 'template-parts/entry/entry', 'category' );
				}

				if ( dekanpro_page_header_has_title() ) {
					echo '<div class="dekanpro-page-header-title">';
					dekanpro_page_header_title();
					echo '</div>';
				}

				if ( dekanpro_has_entry_meta_elements() ) {
					get_template_part( 'template-parts/entry/entry', 'meta' );
				}
				?>

			</div>
		</div>

	<?php } ?>

	<?php do_action( 'dekanpro_page_header_end' ); ?>

</div>

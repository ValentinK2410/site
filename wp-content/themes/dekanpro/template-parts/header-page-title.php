<?php
/**
 * Template part for displaying page header.
 *
 * @package DekanPro
 * @author Peregrine Themes
 * @since   1.0.0
 */

?>

<div <?php dekanpro_page_header_classes(); ?><?php dekanpro_page_header_atts(); ?>>
	<div class="dekanpro-container">

	<?php do_action( 'dekanpro_page_header_start' ); ?>

	<?php if ( dekanpro_page_header_has_title() ) { ?>

		<div class="dekanpro-page-header-wrapper">

			<div class="dekanpro-page-header-title">
				<?php dekanpro_page_header_title(); ?>
			</div>

			<?php $dekanpro_description = apply_filters( 'dekanpro_page_header_description', dekanpro_get_the_description() ); ?>

			<?php if ( $dekanpro_description ) { ?>

				<div class="dekanpro-page-header-description">
					<?php echo wp_kses( $dekanpro_description, dekanpro_get_allowed_html_tags() ); ?>
				</div>

			<?php } ?>
		</div>

	<?php } ?>

	<?php do_action( 'dekanpro_page_header_end' ); ?>

	</div>
</div>

<?php
/**
 * The template for displaying Featured Links.
 *
 * @package     Dekanpro
 * @author      Peregrine Themes
 * @since       1.0.0
 */


$dekanpro_featured_links_title_type = dekanpro_option( 'featured_links_title_type' );
$dekanpro_featured_links_items_html = '';

$dekanpro_featured_column = 'col-md-4 col-sm-6 col-xs-12';
foreach ( $args['features'] as $key => $feature ) :

	// Post items HTML markup.
	ob_start();

	?>
	
	<div id="bloghsah-featured-item-<?php echo esc_attr( $key ); ?>" class="<?php echo esc_attr( $dekanpro_featured_column ); ?>">
		<div class="dekanpro-post-item style-1 center">
			<div class="dekanpro-post-thumb">
				<div class="inner bloghsah-featured-item-image">
					<?php
					if ( ! empty( $feature['image']['id'] ) ) :
						echo wp_get_attachment_image( $feature['image']['id'], 'large' );
					endif;
					?>
				</div>
			</div><!-- END .dekanpro-post-thumb-->
			<div class="dekanpro-post-content">

				<?php
				if ( ! empty( $feature['link'] ) ) :
					if ( '1' == $dekanpro_featured_links_title_type ) :
						printf( '<a href="%1$s" class="dekanpro-btn btn-small btn-white" title="%2$s" target="%3$s">%4$s</a>', esc_url_raw( $feature['link']['url'] ), esc_attr( $feature['link']['title'] ), esc_attr( $feature['link']['target'] ), esc_html( $feature['link']['title'] ) );
						?>
						<?php
					endif;
				endif;
				?>
			</div><!-- END .dekanpro-post-content -->
		</div><!-- END .dekanpro-post-item -->
	</div>
	<?php
	$dekanpro_featured_links_items_html .= ob_get_clean();
endforeach;

// Restore original Post Data.
wp_reset_postdata();

// Title.
$dekanpro_featured_links_title = dekanpro_option( 'featured_links_title' );

// Classes.
$dekanpro_classes  = '';
$dekanpro_classes .= dekanpro_option( 'featured_links_card_border' ) ? ' dekanpro-card__boxed' : '';
$dekanpro_classes .= dekanpro_option( 'featured_links_card_shadow' ) ? ' dekanpro-card-shadow' : '';

?>

<div class="dekanpro-featured featured-one slider-overlay-1 <?php echo esc_attr( $dekanpro_classes ); ?>">
	<div class="dekanpro-featured-container dekanpro-container">
		<div class="dekanpro-flex-row g-0">
			<div class="col-xs-12">
				<div class="dekanpro-card-items">
					<?php if ( $dekanpro_featured_links_title ) : ?>
					<div class="h4 widget-title">							
						<span><?php echo esc_html( $dekanpro_featured_links_title ); ?></span>
					</div>
					<?php endif; ?>
					<div class="dekanpro-flex-row gy-4">
						<?php echo wp_kses_post( $dekanpro_featured_links_items_html ); ?>
					</div>
				</div>
			</div>
		</div><!-- END .dekanpro-card-items -->
	</div>
</div><!-- END .dekanpro-featured -->

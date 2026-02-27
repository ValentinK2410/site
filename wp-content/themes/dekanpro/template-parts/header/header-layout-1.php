<?php
/**
 * The template for displaying header layout 1.
 *
 * @package DekanPro
 * @author DekanPro
 * @since   1.0.0
 */

?>

<div class="dekanpro-container dekanpro-header-container">

	<?php
	dekanpro_header_logo_template();
	?>

	<span class="dekanpro-header-element dekanpro-mobile-nav">
		<?php dekanpro_hamburger( dekanpro_option( 'main_nav_mobile_label' ), 'dekanpro-primary-nav' ); ?>
	</span>

	<?php
	dekanpro_main_navigation_template();
	do_action( 'dekanpro_header_widget_location', array( 'left', 'right' ) );
	?>
	<?php
	// Fallback: форма поиска в шапке, если виджет поиска не добавлен.
	$has_header_search = false;
	$header_widgets    = dekanpro_option( 'header_widgets' );
	if ( is_array( $header_widgets ) ) {
		foreach ( $header_widgets as $w ) {
			if ( isset( $w['type'] ) && 'search' === $w['type'] ) {
				$has_header_search = true;
				break;
			}
		}
	}
	if ( ! $has_header_search ) {
		?>
		<div class="dekanpro-header-search-fallback dekanpro-header-element">
			<?php get_search_form( array( 'aria_label' => __( 'Search for:', 'dekanpro' ) ) ); ?>
		</div>
		<?php
	}
	?>

</div><!-- END .dekanpro-container -->

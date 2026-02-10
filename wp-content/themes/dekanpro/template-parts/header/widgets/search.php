<?php
/**
 * The template for displaying theme header search widget.
 *
 * @package     Dekanpro
 * @author      DekanPro
 * @since       1.0.0
 */

$dekanpro_header_widgets = dekanpro_option( 'header_widgets' );
$style_for_search        = '';
foreach ( $dekanpro_header_widgets as $widget ) {
	// Check if the widget type is 'search'
	if ( $widget['type'] === 'search' ) {
		// Access the 'style' from the 'values' array
		$style_for_search = $widget['values']['style'] ?? 'rounded-fill';
		break; // Stop the loop if the search widget is found
	}
}

?>

<div aria-haspopup="true">
	<a href="#" class="dekanpro-search <?php echo esc_attr( $style_for_search ); ?>">
		<?php echo dekanpro()->icons->get_svg( 'search', array( 'aria-label' => esc_html__( 'Search', 'dekanpro' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</a><!-- END .dekanpro-search -->

	<div class="dekanpro-search-simple dekanpro-search-container dropdown-item">
		<?php
			get_search_form(
				array(
					'aria_label' => __( 'Search for:', 'dekanpro' ),
					'icon' => 'arrow'
				)
			);
		?>
	</div><!-- END .dekanpro-search-simple -->
</div>

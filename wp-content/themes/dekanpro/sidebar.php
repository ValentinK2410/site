<?php
/**
 * The template for displaying theme sidebar.
 *
 * @package     Dekanpro
 * @author      Peregrine Themes
 * @since       1.0.0
 */

if ( ! dekanpro_is_sidebar_displayed() ) {
	return;
}

$dekanpro_sidebar = dekanpro_get_sidebar();
?>

<aside id="secondary" class="widget-area dekanpro-sidebar-container"<?php dekanpro_schema_markup( 'sidebar' ); ?> role="complementary">

	<div class="dekanpro-sidebar-inner">
		<?php do_action( 'dekanpro_before_sidebar' ); ?>

		<?php
		if ( is_active_sidebar( $dekanpro_sidebar ) ) {

			dynamic_sidebar( $dekanpro_sidebar );

		} elseif ( current_user_can( 'edit_theme_options' ) ) {

			$dekanpro_sidebar_name = dekanpro_get_sidebar_name_by_id( $dekanpro_sidebar );
			?>
			<div class="dekanpro-sidebar-widget dekanpro-widget dekanpro-no-widget">

				<div class='h4 widget-title'><?php echo esc_html( $dekanpro_sidebar_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div> 

				<p class='no-widget-text'>
					<?php if ( is_customize_preview() ) { ?>
						<a href='#' class="dekanpro-set-widget" data-sidebar-id="<?php echo esc_attr( $dekanpro_sidebar ); ?>">
					<?php } else { ?>
						<a href='<?php echo esc_url( admin_url( 'widgets.php' ) ); ?>'>
					<?php } ?>
						<?php esc_html_e( 'Click here to assign a widget.', 'dekanpro' ); ?>
					</a>
				</p>
			</div>
			<?php
		}
		?>

		<?php do_action( 'dekanpro_after_sidebar' ); ?>
	</div>

</aside><!--#secondary .widget-area -->

<?php

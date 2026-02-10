<?php
/**
 * The template for displaying header navigation.
 *
 * @package     Dekanpro
 * @author      Peregrine Themes
 * @since       1.0.0
 */

?>

<nav class="site-navigation main-navigation dekanpro-primary-nav dekanpro-nav dekanpro-header-element" role="navigation"<?php dekanpro_schema_markup( 'site_navigation' ); ?> aria-label="<?php esc_attr_e( 'Site Navigation', 'dekanpro' ); ?>">

<?php

if ( has_nav_menu( 'dekanpro-primary' ) ) {
	wp_nav_menu(
		array(
			'theme_location' => 'dekanpro-primary',
			'menu_id'        => 'dekanpro-primary-nav',
			'container'      => '',
			'link_before'    => '<span>',
			'link_after'     => '</span>',
		)
	);
} else {
	wp_page_menu(
		array(
			'menu_class'  => 'dekanpro-primary-nav',
			'show_home'   => true,
			'container'   => 'ul',
			'before'      => '',
			'after'       => '',
			'link_before' => '<span>',
			'link_after'  => '</span>',
		)
	);
}

?>
</nav><!-- END .dekanpro-nav -->

<?php
/**
 * The template for displaying header navigation.
 *
 * @package     Dekanpro
 * @author      DekanPro
 * @since       1.0.0
 */

?>

<nav class="site-navigation main-navigation dekanpro-primary-nav dekanpro-nav dekanpro-header-element" role="navigation"<?php dekanpro_schema_markup( 'site_navigation' ); ?> aria-label="<?php esc_attr_e( 'Site Navigation', 'dekanpro' ); ?>">

<?php

$nav_location = has_nav_menu( 'primary' ) ? 'primary' : 'dekanpro-primary';
if ( has_nav_menu( 'primary' ) || has_nav_menu( 'dekanpro-primary' ) ) {
	wp_nav_menu(
		array(
			'theme_location' => $nav_location,
			'menu_id'        => 'dekanpro-primary-nav',
			'container'      => false,
			'menu_class'     => '',
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

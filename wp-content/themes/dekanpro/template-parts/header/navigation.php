<?php
/**
 * The template for displaying main navigation (Apple ac-localnav style).
 *
 * @package DekanPro
 * @author DekanPro
 * @since   1.0.0
 */

if ( ! class_exists( 'Dekanpro_AC_Local_Nav_Walker' ) ) {
	class Dekanpro_AC_Local_Nav_Walker extends Walker_Nav_Menu {
		public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
			$t        = ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) ? '' : "\t";
			$n        = ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) ? '' : "\n";
			$indent   = ( $depth ) ? str_repeat( $t, $depth ) : '';
			$classes  = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'menu-item-' . $item->ID;
			$classes[] = 'ac-ln-menu-item';
			$args     = apply_filters( 'nav_menu_item_args', $args, $item, $depth );
			$class_names = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
			$id_attr  = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
			$atts     = array(
				'title'  => ! empty( $item->attr_title ) ? $item->attr_title : '',
				'target' => ! empty( $item->target ) ? $item->target : '',
				'rel'    => ! empty( $item->xfn ) ? $item->xfn : '',
				'href'   => ! empty( $item->url ) ? $item->url : '',
				'class'  => 'ac-ln-menu-link',
			);
			$atts     = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
			$attributes = '';
			foreach ( $atts as $attr => $value ) {
				if ( ! empty( $value ) ) {
					$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
					$attributes .= ' ' . $attr . '="' . $value . '"';
				}
			}
			$title = apply_filters( 'the_title', $item->title, $item->ID );
			$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );
			$item_output  = ( $args->before ?? '' ) . '<a' . $attributes . '>' . ( $args->link_before ?? '' ) . '<span>' . $title . '</span>' . ( $args->link_after ?? '' ) . '</a>' . ( $args->after ?? '' );
			$output      .= $indent . '<li id="' . esc_attr( $id_attr ) . '" class="' . esc_attr( $class_names ) . '">' . apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		}
	}
}

$dekanpro_nav_menus = get_registered_nav_menus();
$dekanpro_has_nav   = has_nav_menu( 'primary' );
$dekanpro_home_url  = home_url( '/' );
$dekanpro_site_name = get_bloginfo( 'name' );
$dekanpro_search_url = home_url( '/' );
?>

<nav
	id="ac-localnav"
	class="site-navigation main-navigation dekanpro-primary-nav dekanpro-nav dekanpro-header-element ac-localnav ac-ln-allow-transitions"
	role="navigation"
	itemtype="https://schema.org/SiteNavigationElement"
	itemscope="itemscope"
	aria-label="<?php esc_attr_e( 'Site Navigation', 'dekanpro' ); ?>"
	aria-haspopup="true"
>
	<div class="ac-ln-wrapper">
		<div class="ac-ln-background" aria-hidden="true"></div>
		<div class="ac-ln-content">
			<span class="ac-ln-title">
				<a href="<?php echo esc_url( $dekanpro_home_url ); ?>" aria-current="<?php echo is_front_page() ? 'page' : 'false'; ?>">
					<?php esc_html_e( 'Свежие записи', 'dekanpro' ); ?>
				</a>
			</span>

			<div class="ac-ln-menu">
				<div class="ac-ln-actions">
					<?php if ( $dekanpro_has_nav && ! empty( $dekanpro_nav_menus ) ) : ?>
						<div class="ac-ln-menu-tray">
							<?php
							wp_nav_menu(
								array(
									'theme_location' => 'primary',
									'menu_id'        => 'dekanpro-primary-nav',
									'menu_class'     => 'ac-ln-menu-items',
									'container'      => false,
									'fallback_cb'    => false,
									'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
									'item_spacing'   => 'discard',
									'walker'         => new Dekanpro_AC_Local_Nav_Walker(),
								)
							);
							?>
						</div>
					<?php endif; ?>

					<div class="ac-ln-action ac-ln-action-button ac-ln-search-cta">
						<button type="button" class="ac-ln-search-trigger" id="ac-ln-search-open" aria-expanded="false" aria-controls="dekanpro-search-tray">
							<span class="ac-ln-search-trigger-label"><?php esc_html_e( 'Поиск', 'dekanpro' ); ?></span>
						</button>
						<button type="button" class="ac-ln-search-close" id="ac-ln-search-close" aria-label="<?php esc_attr_e( 'Close Search', 'dekanpro' ); ?>" style="display: none;">×</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="dekanpro-search-tray" class="ac-ln-search-tray search-tray" aria-hidden="true" inert style="max-height: 0;">
		<div class="search-wrapper">
			<form class="search-form" action="<?php echo esc_url( $dekanpro_search_url ); ?>" method="get" role="search">
				<div class="search-form-wrapper">
					<span class="icon icon-search" aria-hidden="true"></span>
					<input
						type="search"
						class="search-form-input"
						placeholder="<?php esc_attr_e( 'Поиск на сайте', 'dekanpro' ); ?>"
						name="s"
						value="<?php echo get_search_query(); ?>"
						aria-label="<?php esc_attr_e( 'Search', 'dekanpro' ); ?>"
						autocomplete="off"
					/>
					<button type="button" class="search-form-close" aria-label="<?php esc_attr_e( 'Close Search', 'dekanpro' ); ?>">×</button>
				</div>
			</form>
		</div>
	</div>
</nav>

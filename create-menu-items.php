<?php
/**
 * Создаёт минимальное меню: «Каталог» и «Добавить».
 * Удаляет все старые пункты меню.
 * Запуск: php create-menu-items.php (из корня WordPress)
 */

if ( php_sapi_name() !== 'cli' ) {
	die( 'Запускайте только из командной строки: php create-menu-items.php' );
}

require_once __DIR__ . '/wp-load.php';

$menu_name = 'Основное меню';

// Создаём рубрики при отсутствии.
$category_slugs = array(
	'zhivopis'    => 'Живопись',
	'poeziya'     => 'Поэзия',
	'tvorchestvo' => 'Творчество',
	'fotografii'  => 'Фотографии',
	'stati'       => 'Статьи',
);
foreach ( $category_slugs as $slug => $name ) {
	if ( ! get_term_by( 'slug', $slug, 'category' ) ) {
		$result = wp_insert_term( $name, 'category', array( 'slug' => $slug ) );
		if ( ! is_wp_error( $result ) ) {
			echo "Создана рубрика: {$name}\n";
		}
	}
}

// Ищем или создаём меню.
$menu = wp_get_nav_menu_object( $menu_name );
if ( ! $menu ) {
	$menu_id = wp_create_nav_menu( $menu_name );
	if ( is_wp_error( $menu_id ) ) {
		echo "Ошибка создания меню: " . $menu_id->get_error_message() . "\n";
		exit( 1 );
	}
	echo "Создано меню: {$menu_name}\n";
} else {
	$menu_id = $menu->term_id;
	echo "Меню «{$menu_name}» уже существует (ID: {$menu_id})\n";
}

// Удаляем ВСЕ старые пункты меню.
$existing_items = wp_get_nav_menu_items( $menu_id );
if ( $existing_items ) {
	foreach ( $existing_items as $item ) {
		wp_delete_post( $item->ID, true );
	}
	echo "Удалено старых пунктов: " . count( $existing_items ) . "\n";
}

// Добавляем только 2 пункта: «Каталог» и «Добавить».
$blog_url = home_url( '/' );
$page_for_posts = get_option( 'page_for_posts' );
if ( $page_for_posts ) {
	$blog_url = get_permalink( $page_for_posts );
}

wp_update_nav_menu_item( $menu_id, 0, array(
	'menu-item-title'    => 'Каталог',
	'menu-item-url'      => $blog_url,
	'menu-item-status'   => 'publish',
	'menu-item-position' => 1,
	'menu-item-type'     => 'custom',
) );
echo "Добавлен пункт: Каталог\n";

$submit_page = get_page_by_path( 'create' ) ?: get_page_by_path( 'dobavit-material' );
$submit_url  = $submit_page ? get_permalink( $submit_page ) : home_url( '/create/' );

wp_update_nav_menu_item( $menu_id, 0, array(
	'menu-item-title'    => 'Добавить',
	'menu-item-url'      => $submit_url,
	'menu-item-status'   => 'publish',
	'menu-item-position' => 2,
	'menu-item-type'     => 'custom',
) );
echo "Добавлен пункт: Добавить\n";

// Привязываем меню к локациям.
$locations = get_theme_mod( 'nav_menu_locations', array() );
$locations['primary']          = $menu_id;
$locations['dekanpro-primary'] = $menu_id;
set_theme_mod( 'nav_menu_locations', $locations );
echo "Меню привязано к локациям primary и dekanpro-primary.\n";
echo "Готово.\n";

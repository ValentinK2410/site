<?php
/**
 * Создаёт меню с пунктами: Живопись, Поэзия, Статьи.
 * Создаёт рубрики при отсутствии.
 * Запуск: php create-menu-items.php (из корня WordPress)
 */

if ( php_sapi_name() !== 'cli' ) {
	die( 'Запускайте только из командной строки: php create-menu-items.php' );
}

require_once __DIR__ . '/wp-load.php';

$menu_name   = 'Основное меню';
$menu_items  = array(
	array(
		'title' => 'Все рубрики',
		'url'   => '',
		'type'  => 'blog',
	),
	array(
		'title' => 'Живопись',
		'url'   => '',
		'type'  => 'category',
		'slug'  => 'zhivopis',
	),
	array(
		'title' => 'Поэзия',
		'url'   => '',
		'type'  => 'category',
		'slug'  => 'poeziya',
	),
	array(
		'title' => 'Творчество',
		'url'   => '',
		'type'  => 'category',
		'slug'  => 'tvorchestvo',
	),
	array(
		'title' => 'Фотографии',
		'url'   => '',
		'type'  => 'category',
		'slug'  => 'fotografii',
	),
	array(
		'title' => 'Статьи',
		'url'   => '',
		'type'  => 'category',
		'slug'  => 'stati',
	),
	array(
		'title' => 'Добавить материал',
		'url'   => '',
		'type'  => 'page',
		'slug'  => 'dobavit-material',
	),
);

// Создаём рубрики при отсутствии.
foreach ( $menu_items as $item ) {
	if ( 'category' !== $item['type'] ) {
		continue;
	}
	$term = get_term_by( 'slug', $item['slug'], 'category' );
	if ( ! $term ) {
		$result = wp_insert_term( $item['title'], 'category', array( 'slug' => $item['slug'] ) );
		if ( ! is_wp_error( $result ) ) {
			echo "Создана рубрика: {$item['title']}\n";
		} else {
			echo "Ошибка создания рубрики {$item['title']}: " . $result->get_error_message() . "\n";
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

// Определяем URL и добавляем/обновляем пункты.
$existing_items  = wp_get_nav_menu_items( $menu_id );
$existing_by_title = array();
if ( $existing_items ) {
	foreach ( $existing_items as $obj ) {
		$existing_by_title[ $obj->title ] = $obj;
	}
}
$position = $existing_items ? count( $existing_items ) : 0;

// Обновление пункта «Статьи»: отдельный раздел /category/stati/
$stati_term = get_term_by( 'slug', 'stati', 'category' );
if ( $stati_term && isset( $existing_by_title['Статьи'] ) ) {
	$obj = $existing_by_title['Статьи'];
	$new_url = get_category_link( $stati_term->term_id );
	if ( $obj->url !== $new_url ) {
		wp_update_nav_menu_item( $menu_id, $obj->ID, array(
			'menu-item-url' => $new_url,
		) );
		echo "Обновлён URL пункта «Статьи» → /category/stati/\n";
	}
}

$added_blog_first = false;
foreach ( $menu_items as $item ) {
	if ( in_array( $item['title'], array_keys( $existing_by_title ), true ) ) {
		echo "Пункт «{$item['title']}» уже есть, пропуск.\n";
		continue;
	}
	$url = '';
	if ( 'category' === $item['type'] ) {
		$term = get_term_by( 'slug', $item['slug'], 'category' );
		$url  = $term ? get_category_link( $term->term_id ) : home_url( '/category/' . $item['slug'] . '/' );
	} elseif ( 'blog' === $item['type'] ) {
		$page_for_posts = get_option( 'page_for_posts' );
		$url            = $page_for_posts ? get_permalink( $page_for_posts ) : home_url( '/' );
		// Сдвигаем все пункты, чтобы «Все рубрики» был первым
		if ( ! $added_blog_first && $existing_items ) {
			foreach ( $existing_items as $obj ) {
				wp_update_nav_menu_item( $menu_id, $obj->ID, array( 'menu-item-position' => $obj->menu_order + 1 ) );
			}
			$position = 0;
			$added_blog_first = true;
		}
	} elseif ( 'page' === $item['type'] ) {
		$page = get_page_by_path( $item['slug'] );
		if ( ! $page ) {
			$page = get_posts( array( 'post_type' => 'page', 'title' => $item['title'], 'numberposts' => 1 ) );
			$page = ! empty( $page ) ? $page[0] : null;
		}
		$url = $page ? get_permalink( $page ) : home_url( '/' . $item['slug'] . '/' );
	}

	wp_update_nav_menu_item(
		$menu_id,
		0,
		array(
			'menu-item-title'     => $item['title'],
			'menu-item-url'       => $url,
			'menu-item-status'    => 'publish',
			'menu-item-position'  => $position,
			'menu-item-type'      => 'custom',
		)
	);
	$position++;
	echo "Добавлен пункт: {$item['title']}\n";
}

// Привязываем меню к локации primary и dekanpro-primary.
$locations = get_theme_mod( 'nav_menu_locations', array() );
$locations['primary']          = $menu_id;
$locations['dekanpro-primary'] = $menu_id;
set_theme_mod( 'nav_menu_locations', $locations );
echo "Меню привязано к локациям primary и dekanpro-primary.\n";
echo "Готово.\n";

<?php
/**
 * Переносит все рубрики, кроме основных 5, в метки (теги).
 * Основные: Живопись, Поэзия, Статьи, Творчество, Фотографии.
 *
 * Запуск: php migrate-categories-to-tags.php (из корня WordPress)
 */

if ( php_sapi_name() !== 'cli' ) {
	die( 'Запускайте только из командной строки: php migrate-categories-to-tags.php' );
}

require_once __DIR__ . '/wp-load.php';

$allowed_slugs = array( 'zhivopis', 'poeziya', 'stati', 'tvorchestvo', 'fotografii' );
$default_cat  = (int) get_option( 'default_category' );

$categories = get_terms( array(
	'taxonomy'   => 'category',
	'hide_empty' => false,
	'exclude'    => array( $default_cat ),
) );

if ( is_wp_error( $categories ) ) {
	echo 'Ошибка: ' . $categories->get_error_message() . "\n";
	exit( 1 );
}

$migrated = 0;
$deleted  = 0;

foreach ( $categories as $term ) {
	if ( in_array( $term->slug, $allowed_slugs, true ) ) {
		continue;
	}

	$posts = get_posts( array(
		'post_type'      => 'post',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'tax_query'      => array(
			array(
				'taxonomy' => 'category',
				'field'    => 'term_id',
				'terms'    => $term->term_id,
			),
		),
	) );

	foreach ( $posts as $post_id ) {
		$post_tags = wp_get_post_tags( $post_id );
		$tag_names = wp_list_pluck( $post_tags, 'name' );
		if ( ! in_array( $term->name, $tag_names, true ) ) {
			$tag_names[] = $term->name;
		}
		wp_set_post_tags( $post_id, $tag_names );

		$post_cats = wp_get_post_categories( $post_id );
		$post_cats = array_diff( $post_cats, array( $term->term_id ) );
		$post_cats = array_values( array_map( 'intval', $post_cats ) );

		if ( empty( $post_cats ) ) {
			$parent_id = $term->parent;
			$fallback  = $default_cat;
			if ( $parent_id ) {
				$parent = get_term( $parent_id, 'category' );
				if ( $parent && ! is_wp_error( $parent ) && in_array( $parent->slug, $allowed_slugs, true ) ) {
					$fallback = $parent_id;
				}
			}
			$post_cats = array( $fallback );
		}
		wp_set_post_categories( $post_id, $post_cats );

		$migrated++;
	}

	$result = wp_delete_term( $term->term_id, 'category' );
	if ( ! is_wp_error( $result ) ) {
		$deleted++;
		echo "Рубрика «{$term->name}» перенесена в метки и удалена. Постов: " . count( $posts ) . "\n";
	} else {
		echo "Ошибка удаления «{$term->name}»: " . $result->get_error_message() . "\n";
	}
}

echo "\nГотово. Мигрировано постов: {$migrated}, удалено рубрик: {$deleted}\n";

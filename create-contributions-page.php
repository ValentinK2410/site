<?php
/**
 * Создаёт страницу «Добавить материал» со шорткодом и включает регистрацию пользователей.
 * Запуск: php create-contributions-page.php (из корня WordPress)
 */

if ( php_sapi_name() !== 'cli' ) {
	die( 'Запускайте только из командной строки' );
}

require_once __DIR__ . '/wp-load.php';

$page_title = 'Добавить материал';
$content    = '<h2>' . esc_html__( 'Добавить стихотворение, картину, фотографию или статью', 'dekanpro-contributions' ) . '</h2>
<p>' . esc_html__( 'Заполните форму ниже. Материал будет опубликован после модерации.', 'dekanpro-contributions' ) . '</p>
[dekanpro_submit]';

global $wpdb;
$existing = $wpdb->get_var( $wpdb->prepare(
	"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'page' AND post_title = %s AND post_status != 'trash' LIMIT 1",
	$page_title
) );

if ( $existing ) {
	echo "Страница «{$page_title}» уже существует (ID: {$existing})\n";
	echo "URL: " . get_permalink( $existing ) . "\n";
} else {
	$post_id = wp_insert_post( array(
		'post_title'   => $page_title,
		'post_name'    => 'dobavit-material',
		'post_content' => $content,
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_author'  => 1,
	) );
	if ( is_wp_error( $post_id ) ) {
		echo "Ошибка: " . $post_id->get_error_message() . "\n";
		exit( 1 );
	}
	echo "Страница создана. ID: {$post_id}\n";
	echo "URL: " . get_permalink( $post_id ) . "\n";
}

// Включаем регистрацию пользователей и роль Contributor по умолчанию.
update_option( 'users_can_register', 1 );
update_option( 'default_role', 'contributor' );
echo "Регистрация пользователей включена. Роль по умолчанию: contributor.\n";

// Активация плагина DekanPro Contributions.
$plugin = 'dekanpro-contributions/dekanpro-contributions.php';
if ( ! is_plugin_active( $plugin ) ) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	activate_plugin( $plugin );
	echo "Плагин DekanPro Contributions активирован.\n";
} else {
	echo "Плагин DekanPro Contributions уже активен.\n";
}
echo "Готово.\n";

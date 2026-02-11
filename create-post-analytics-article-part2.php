<?php
/**
 * Создаёт статью «Часть вторая: как добавить просмотры в пост».
 * Запуск: php create-post-analytics-article-part2.php (из корня WordPress)
 */

if ( php_sapi_name() !== 'cli' ) {
	die( 'Запускайте только из командной строки' );
}

require_once __DIR__ . '/wp-load.php';

$title = 'Часть вторая: как добавить просмотры в пост';

$content = '<h2>Введение</h2>
<p>В <a href="' . esc_url( home_url( '/kak-sozdat-plagin-analitiki-prosmotrov-zapisey-v-wordpress/' ) ) . '">первой части</a> мы создали плагин Post Analytics: трекинг просмотров, база данных, REST API и админка со статистикой. Теперь добавим отображение количества просмотров в карточках постов на главной странице и в архивах — значок с иконкой глаза и числом.</p>

<h2>Что нужно сделать</h2>
<ul>
<li>Функция получения количества просмотров для поста</li>
<li>Интеграция с темой: добавить «просмотры» в элементы меты</li>
<li>Функция вывода (иконка + число)</li>
<li>Стили для значка</li>
</ul>

<h2>Шаг 1: Функция get_view_count</h2>
<p>В классе <code>Post_Analytics_DB</code> добавляем статический метод <code>get_view_count($post_id, $days = 30)</code>. Он вызывает <code>get_post_stats()</code> и возвращает только <code>unique_views</code>. Это лёгкий способ получить число для вывода в шаблоне.</p>

<h2>Шаг 2: Интеграция с темой Dekanpro</h2>
<p>Тема Dekanpro выводит мету поста (автор, дата, категория) через массив элементов. Для каждого элемента вызывается функция <code>dekanpro_entry_meta_{$item}()</code>. Достаточно:</p>
<ol>
<li>Подключиться к фильтру <code>dekanpro_entry_meta_elements</code> и добавить в массив строку <code>views</code>.</li>
<li>Определить функцию <code>dekanpro_entry_meta_views()</code>, которая выводит иконку и число просмотров.</li>
</ol>
<p>Просмотры показываем только на главной, в архивах и поиске — в <code>add_views_to_meta_elements</code> проверяем <code>is_home() || is_archive() || is_search()</code>.</p>

<h2>Шаг 3: Вывод значка</h2>
<p>Функция <code>dekanpro_entry_meta_views()</code> получает ID поста через <code>get_the_ID()</code>, вызывает <code>Post_Analytics_DB::get_view_count($post_id, 30)</code>, выводит span с классом <code>entry-meta-views</code>, внутри — SVG-иконка глаза и число, отформатированное через <code>number_format_i18n()</code>. Даже при нуле просмотров значок отображается (показывает 0).</p>

<h2>Шаг 4: Стили</h2>
<p>Подключаем отдельный CSS-файл плагина только на главной, в архивах и поиске. Стили задают:</p>
<ul>
<li><code>display: inline-flex</code>, <code>align-items: center</code>, <code>gap: 4px</code> — иконка и число в ряд</li>
<li>Размер иконки, лёгкая прозрачность</li>
<li>Поддержка тёмной темы</li>
</ul>

<h2>Структура нового файла</h2>
<p>Класс <code>Post_Analytics_Frontend</code> в <code>includes/class-post-analytics-frontend.php</code>:</p>
<ul>
<li>Фильтр <code>dekanpro_entry_meta_elements</code> — добавить <code>views</code></li>
<li>Хук <code>init</code> (приоритет 20) — определить <code>dekanpro_entry_meta_views</code></li>
<li>Хук <code>wp_enqueue_scripts</code> — подключить CSS</li>
</ul>

<h2>Итог</h2>
<p>Плагин остаётся самодостаточным: не требует правок темы. Интеграция через фильтры и стандартные хуки WordPress. Значок просмотров появляется в каждой карточке поста на главной и в архивах автоматически.</p>';

global $wpdb;
$existing_id = $wpdb->get_var( $wpdb->prepare(
	"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'post' AND post_title = %s AND post_status != 'trash' LIMIT 1",
	$title
) );
if ( $existing_id ) {
	echo "Запись «$title» уже существует (ID: $existing_id)\n";
	echo "URL: " . get_permalink( $existing_id ) . "\n";
	exit( 0 );
}

$post_id = wp_insert_post( array(
	'post_title'   => $title,
	'post_content' => $content,
	'post_status'  => 'publish',
	'post_type'    => 'post',
	'post_author'  => 1,
) );

if ( is_wp_error( $post_id ) ) {
	echo "Ошибка: " . $post_id->get_error_message() . "\n";
	exit( 1 );
}

echo "Запись создана. ID: $post_id\n";
echo "URL: " . get_permalink( $post_id ) . "\n";

<?php
/**
 * Создаёт статью о плагине Post Analytics и добавляет термины в глоссарий.
 * Запуск: php create-post-analytics-article.php (из корня WordPress)
 */

if ( php_sapi_name() !== 'cli' ) {
	die( 'Запускайте только из командной строки: php create-post-analytics-article.php' );
}

require_once __DIR__ . '/wp-load.php';

if ( ! class_exists( 'Glossary_Tooltips' ) ) {
	echo "Плагин Glossary Tooltips не активен. Термины не будут добавлены.\n";
}

/**
 * Термины для глоссария (Post Analytics).
 */
function get_post_analytics_glossary_terms() {
	return array(
		array(
			'title'      => 'REST API',
			'definition' => 'Интерфейс программирования приложений на базе HTTP. Позволяет клиенту (браузеру, мобильному приложению) отправлять запросы к серверу и получать данные в формате JSON. В WordPress REST API доступен по умолчанию с версии 4.7.',
			'examples'   => "register_rest_route( 'my-plugin/v1', '/track', array( 'methods' => 'POST', 'callback' => 'handle_track' ) );" . "\n" . "URL: /wp-json/post-analytics/v1/track",
			'use_cases'  => "Для отправки данных с фронтенда без полной перезагрузки страницы." . "\n" . "AJAX-подобная функциональность с удобной структурой URL и маршрутов.",
			'aliases'    => 'REST API, REST, API',
		),
		array(
			'title'      => 'fetch',
			'definition' => 'Встроенный в браузер метод JavaScript для выполнения HTTP-запросов. Возвращает Promise. Современная замена XMLHttpRequest, используется для отправки данных на сервер.',
			'examples'   => "fetch( url, { method: 'POST', body: formData, headers: { 'X-WP-Nonce': nonce } } )" . "\n" . "keepalive: true — позволяет отправить данные даже при закрытии вкладки.",
			'use_cases'  => "Отправка аналитики при уходе со страницы (beforeunload)." . "\n" . "Любые асинхронные запросы к серверу из JavaScript.",
			'aliases'    => 'fetch, Fetch API',
		),
		array(
			'title'      => 'wp_localize_script',
			'definition' => 'Функция WordPress, передающая данные из PHP в подключённый JavaScript. Создаёт глобальную переменную с заданным именем. Используется для nonce, URL, ID записей и других значений.',
			'examples'   => "wp_localize_script( 'post-analytics', 'postAnalytics', array( 'postId' => 123, 'restUrl' => rest_url('...'), 'nonce' => wp_create_nonce('wp_rest') ) );",
			'use_cases'  => "Когда JavaScript нужны данные от сервера (ID поста, URL, nonce для REST API)." . "\n" . "Избегает жёстко прописанных значений в коде.",
			'aliases'    => 'wp_localize_script, wp localize script',
		),
		array(
			'title'      => 'register_rest_route',
			'definition' => 'Функция WordPress для регистрации маршрута REST API. Определяет URL, метод (GET, POST и т.д.), callback и валидацию параметров.',
			'examples'   => "register_rest_route( 'post-analytics/v1', '/track', array( 'methods' => 'POST', 'callback' => array(\$this, 'handle_track'), 'permission_callback' => '__return_true', 'args' => array(...) ) );",
			'use_cases'  => "Создание endpoint для приёма данных с фронтенда." . "\n" . "Стандартный способ добавить API в WordPress.",
			'aliases'    => 'register_rest_route, register rest route',
		),
		array(
			'title'      => 'dbDelta',
			'definition' => 'Функция WordPress для создания и обновления таблиц в базе данных. Анализирует существующую структуру и вносит только необходимые изменения. Находится в wp-admin/includes/upgrade.php.',
			'examples'   => "require_once ABSPATH . 'wp-admin/includes/upgrade.php';" . "\n" . "dbDelta( \"CREATE TABLE ... (id bigint, post_id bigint, ...) \$charset\" );",
			'use_cases'  => "При активации плагина — создание своей таблицы." . "\n" . "При обновлении плагина — добавление колонок или изменение структуры.",
			'aliases'    => 'dbDelta, db delta',
		),
		array(
			'title'      => 'User-Agent',
			'definition' => 'Строка в HTTP-запросе, идентифицирующая браузер, ОС и устройство. Передаётся в заголовке. Используется для определения платформы (iOS, Android, Windows) и типа устройства.',
			'examples'   => "\$_SERVER['HTTP_USER_AGENT'] — в PHP получить User-Agent." . "\n" . "navigator.userAgent — в JavaScript. Содержит, например: Mozilla/5.0 (Windows NT 10.0; Win64; x64)...",
			'use_cases'  => "Определение мобильного или десктопного трафика." . "\n" . "Уникальная идентификация посетителя в комбинации с IP.",
			'aliases'    => 'User-Agent, User Agent, user agent',
		),
		array(
			'title'      => 'трекинг',
			'definition' => 'Сбор данных о действиях пользователя на сайте: просмотры, прокрутка, время на странице, клики. Используется для аналитики и улучшения контента.',
			'examples'   => "Трекинг глубины прокрутки — сколько процентов страницы пользователь пролистал." . "\n" . "Трекинг времени — сколько секунд пользователь провёл на странице.",
			'use_cases'  => "Аналитика просмотров статей." . "\n" . "A/B тестирование, тепло-карты, поведенческие метрики.",
			'aliases'    => 'трекинг, tracking, аналитика',
		),
		array(
			'title'      => 'hash',
			'definition' => 'Результат криптографической функции, преобразующей данные в фиксированную строку. Одинаковые входные данные дают одинаковый hash. Используется для анонимной идентификации без хранения персональных данных.',
			'examples'   => "hash('sha256', ip + ua + date) — уникальный идентификатор посетителя за день." . "\n" . "MD5, SHA-256 — распространённые алгоритмы.",
			'use_cases'  => "Подсчёт уникальных посетителей без cookies." . "\n" . "Анонимизация IP и User-Agent.",
			'aliases'    => 'hash, хеш, хэш',
		),
		array(
			'title'      => 'beforeunload',
			'definition' => 'Событие браузера, срабатывающее при закрытии вкладки или переходе на другую страницу. Используется для сохранения данных перед уходом пользователя.',
			'examples'   => "window.addEventListener( 'beforeunload', function() { sendAnalytics(); } );" . "\n" . "В fetch нужно указать keepalive: true, иначе запрос может не успеть отправиться.",
			'use_cases'  => "Отправка аналитики при уходе со страницы." . "\n" . "Сохранение черновика, подтверждение выхода из формы.",
			'aliases'    => 'beforeunload',
		),
		array(
			'title'      => 'visibilitychange',
			'definition' => 'Событие API видимости страницы. Срабатывает при переключении вкладки (страница скрыта/показана) или сворачивании окна. document.visibilityState — «visible» или «hidden».',
			'examples'   => "document.addEventListener( 'visibilitychange', function() { if (document.visibilityState === 'hidden') sendData(); } );" . "\n" . "Переход на другую вкладку — страница «hidden».",
			'use_cases'  => "Отправка аналитики когда пользователь переключился на другую вкладку." . "\n" . "Пауза видео/анимации при скрытой вкладке.",
			'aliases'    => 'visibilitychange, Visibility API',
		),
		array(
			'title'      => 'wp_enqueue_script',
			'definition' => 'Функция WordPress для подключения JavaScript на фронтенде или в админке. Управляет зависимостями, версией и позицией загрузки (header/footer).',
			'examples'   => "wp_enqueue_script( 'post-analytics', PLUGIN_URL . 'assets/js/post-analytics.js', array(), '1.0.0', true );" . "\n" . "Пятый параметр true — загрузка в footer.",
			'use_cases'  => "Подключение скриптов плагина и темы." . "\n" . "Всегда вместо прямого тега <script>.",
			'aliases'    => 'wp_enqueue_script, wp enqueue script',
		),
		array(
			'title'      => 'is_singular',
			'definition' => 'Функция WordPress, проверяющая, отображается ли одиночная запись (post, page или указанный тип). Возвращает true на странице просмотра одной записи.',
			'examples'   => "if ( is_singular( 'post' ) ) { wp_enqueue_script( 'analytics', ... ); }" . "\n" . "is_singular() без аргумента — любая одиночная запись.",
			'use_cases'  => "Загружать скрипты только на страницах записей, а не в архивах и на главной." . "\n" . "Оптимизация — не грузить аналитику там, где она не нужна.",
			'aliases'    => 'is_singular, is singular',
		),
		array(
			'title'      => 'activation hook',
			'definition' => 'Хук WordPress, срабатывающий один раз при активации плагина. Используется для создания таблиц, настройки опций и другой первоначальной инициализации.',
			'examples'   => "register_activation_hook( __FILE__, array( \$this, 'activate' ) );" . "\n" . "В activate вызываем Post_Analytics_DB::create_table();",
			'use_cases'  => "Создание таблиц при первой активации." . "\n" . "Добавление дефолтных настроек, capabilities.",
			'aliases'    => 'activation hook, register_activation_hook, хук активации',
		),
	);
}

function add_glossary_terms() {
	$terms = get_post_analytics_glossary_terms();
	$created = 0;

	foreach ( $terms as $term_data ) {
		global $wpdb;
		$exists = $wpdb->get_var( $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'glossary_term' AND post_title = %s AND post_status != 'trash' LIMIT 1",
			$term_data['title']
		) );
		if ( $exists ) {
			continue;
		}
		$post_id = wp_insert_post( array(
			'post_type'   => 'glossary_term',
			'post_title'  => $term_data['title'],
			'post_status' => 'publish',
		) );
		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, '_glossary_definition', $term_data['definition'] );
			update_post_meta( $post_id, '_glossary_examples', $term_data['examples'] );
			update_post_meta( $post_id, '_glossary_use_cases', $term_data['use_cases'] );
			update_post_meta( $post_id, '_glossary_aliases', $term_data['aliases'] ?? '' );
			$created++;
			echo "Добавлен термин: " . $term_data['title'] . "\n";
		}
	}

	if ( $created > 0 && class_exists( 'Glossary_Tooltips' ) ) {
		Glossary_Tooltips::clear_cache();
	}
	return $created;
}

/**
 * Содержимое статьи о плагине Post Analytics.
 */
function get_post_analytics_article_content() {
	return '<h2>Введение</h2>
<p>Плагин <strong>Post Analytics</strong> собирает статистику просмотров записей блога: сколько уникальных пользователей просмотрело каждую запись, до какой части страницы они прокрутили, сколько времени провели на странице, с каких устройств и платформ заходили. В этой статье подробно описано, как создать такой плагин с нуля, чтобы любой пользователь мог повторить реализацию.</p>

<h2>Что умеет плагин</h2>
<ul>
<li>Подсчёт уникальных просмотров (по IP + User-Agent + дата)</li>
<li>Глубина прокрутки — до скольких процентов страницы пользователь долистал</li>
<li>Время на странице в секундах</li>
<li>Определение устройства: мобильный, планшет, десктоп</li>
<li>Определение платформы: iOS, Android, Windows, macOS, Linux</li>
<li>Админка со статистикой: популярность записей, среднее время, средняя и максимальная глубина прокрутки</li>
<li>Фильтр по периоду: 7, 14, 30, 90 дней</li>
</ul>

<h2>Структура плагина</h2>
<p>Создаём папку <code>wp-content/plugins/post-analytics/</code> со следующей структурой:</p>
<pre><code class="language-text">post-analytics/
├── post-analytics.php          # Точка входа
├── includes/
│   ├── class-post-analytics-db.php     # Работа с базой данных
│   ├── class-post-analytics-rest.php   # REST API endpoint
│   └── class-post-analytics-admin.php # Страница статистики в админке
└── assets/
    └── js/
        └── post-analytics.js    # Трекинг на фронтенде</code></pre>

<h2>Шаг 1: Главный файл плагина</h2>
<p>Файл <code>post-analytics.php</code> — точка входа. Здесь объявляем плагин, подключаем зависимости, регистрируем <strong>activation hook</strong> для создания таблицы при первой активации, подключаем скрипты через <code>wp_enqueue_script</code> только на одиночных записях (проверка <code>is_singular(\'post\')</code>). Через <code>wp_localize_script</code> передаём в JavaScript ID поста, URL REST API и nonce для авторизации запросов.</p>

<h2>Шаг 2: База данных</h2>
<p>Класс <code>Post_Analytics_DB</code> отвечает за хранение данных. Таблица создаётся функцией <code>dbDelta</code> при активации. Поля: <code>post_id</code>, <code>visitor_hash</code> (хеш от IP + User-Agent + дата для анонимной идентификации), <code>scroll_depth</code> (0–100%), <code>time_seconds</code>, <code>device</code>, <code>platform</code>, <code>created_at</code>. Методы: <code>insert_view()</code> — сохранить просмотр, <code>get_post_stats()</code> — статистика по одной записи, <code>get_all_stats()</code> — список всех записей с агрегированными данными.</p>

<h2>Шаг 3: REST API</h2>
<p>Класс <code>Post_Analytics_REST</code> регистрирует endpoint через <code>register_rest_route</code>: <code>POST /wp-json/post-analytics/v1/track</code>. Принимает параметры: <code>post_id</code>, <code>scroll_depth</code>, <code>time_seconds</code>, <code>device</code>, <code>platform</code>. Проверяет, что запись существует и опубликована. Формирует <code>visitor_hash</code> из IP, User-Agent и текущей даты — один посетитель в день считается один раз. Сохраняет данные через <code>Post_Analytics_DB::insert_view()</code>.</p>

<h2>Шаг 4: JavaScript-трекинг</h2>
<p>Скрипт <code>post-analytics.js</code> получает из <code>wp_localize_script</code> объект <code>postAnalytics</code> с <code>postId</code>, <code>restUrl</code>, <code>nonce</code>. Отслеживает:</p>
<ul>
<li><strong>Глубину прокрутки</strong> — при событии <code>scroll</code> и по таймеру раз в 500 мс запоминает максимальное значение (0–100%).</li>
<li><strong>Время на странице</strong> — от момента загрузки до ухода.</li>
<li><strong>Уход пользователя</strong> — события <code>beforeunload</code>, <code>pagehide</code> и <code>visibilitychange</code> (когда <code>document.visibilityState === \'hidden\'</code>, т.е. пользователь переключил вкладку).</li>
</ul>
<p>При уходе отправляет данные через <code>fetch</code> с <code>keepalive: true</code>, чтобы запрос успел уйти даже при закрытии вкладки. Определяет устройство по ширине окна (mobile &lt; 768px, tablet &lt; 1024px, desktop) и платформу по <code>navigator.userAgent</code>.</p>

<h2>Шаг 5: Админка</h2>
<p>Класс <code>Post_Analytics_Admin</code> добавляет пункт меню через <code>add_menu_page</code> (или <code>add_submenu_page</code> к существующему разделу). Страница выводит таблицу: заголовок записи, уникальные просмотры, всего просмотров, средняя и максимальная глубина прокрутки (%), среднее время (сек), разбивка по устройствам и платформам. Переключатель периода: 7, 14, 30, 90 дней. Если таблица в базе не создана (например, плагин был скопирован без активации), показывается кнопка «Создать таблицу» с проверкой nonce.</p>

<h2>Идентификация уникального посетителя</h2>
<p>Для подсчёта уникальных просмотров без cookies используем <strong>hash</strong> от комбинации IP, User-Agent и даты. Один и тот же пользователь в один день даёт один hash — считаем его одним уникальным посетителем. Данные не содержат персональной информации, только хеш.</p>

<h2>Термины и технологии</h2>
<p>В статье используются термины: <strong>REST API</strong>, <strong>fetch</strong>, <strong>wp_localize_script</strong>, <strong>register_rest_route</strong>, <strong>dbDelta</strong>, <strong>User-Agent</strong>, <strong>трекинг</strong>, <strong>hash</strong>, <strong>beforeunload</strong>, <strong>visibilitychange</strong>, <strong>wp_enqueue_script</strong>, <strong>is_singular</strong>, <strong>activation hook</strong>. Все они добавлены в глоссарий — при клике на термин откроется подсказка с пояснением.</p>

<h2>Итог</h2>
<p>Плагин Post Analytics — полноценный пример сбора поведенческой аналитики на WordPress: база данных для хранения, REST API для приёма данных с фронтенда, JavaScript-трекинг с учётом ухода пользователя, админка для просмотра статистики. Структуру можно расширить: добавить экспорт, графики, уведомления о популярных записях.</p>';
}

// === Выполнение ===

echo "=== Добавление терминов в глоссарий ===\n";
$terms_added = add_glossary_terms();
echo "Добавлено терминов: $terms_added\n\n";

$title = 'Как создать плагин аналитики просмотров записей в WordPress';
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

$content = get_post_analytics_article_content();
$post_id = wp_insert_post( array(
	'post_title'   => $title,
	'post_content' => $content,
	'post_status'  => 'publish',
	'post_type'    => 'post',
	'post_author'  => 1,
) );

if ( is_wp_error( $post_id ) ) {
	echo "Ошибка создания записи: " . $post_id->get_error_message() . "\n";
	exit( 1 );
}

$url = get_permalink( $post_id );
echo "=== Запись создана и опубликована ===\n";
echo "ID: $post_id\n";
echo "URL: $url\n";

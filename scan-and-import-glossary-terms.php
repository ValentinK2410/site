<?php
/**
 * Сканирование статей на технические термины и добавление недостающих в глоссарий.
 * Запуск: php scan-and-import-glossary-terms.php (из корня WordPress)
 */

if ( php_sapi_name() !== 'cli' ) {
	die( 'Запускайте только из командной строки' );
}

require_once __DIR__ . '/wp-load.php';

if ( ! class_exists( 'Glossary_Tooltips' ) ) {
	die( "Плагин Glossary Tooltips не активен.\n" );
}

/**
 * Извлечение текста из HTML.
 */
function extract_text_from_html( $html ) {
	$html = preg_replace( '/<script\b[^>]*>.*?<\/script>/is', ' ', $html );
	$html = preg_replace( '/<style\b[^>]*>.*?<\/style>/is', ' ', $html );
	$text = strip_tags( $html );
	$text = html_entity_decode( $text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	return $text;
}

/**
 * Паттерны для извлечения технических терминов.
 */
function extract_technical_terms( $text ) {
	$terms = array();
	$patterns = array(
		'/\b(wp_enqueue_script|wp_enqueue_style|wp_head|wp_footer)\b/i',
		'/\b(get_template_directory_uri|get_stylesheet_directory_uri)\b/i',
		'/\b(sanitize_title|sanitize_file_name|sanitize_text_field)\b/i',
		'/\b(register_post_type|get_posts|wp_insert_post)\b/i',
		'/\b(add_filter|add_action|apply_filters|do_action)\b/i',
		'/\b(Prism\.js|Prism\.|prism-tomorrow|prism-autoloader)\b/i',
		'/\b(functions\.php|php\.ini)\b/i',
		'/\b(wp-content|uploads|wp-admin)\b/i',
		'/\b(rsync|chown|chmod|chmod)\b/i',
		'/\b(PHP-FPM|Apache|mod_php)\b/i',
		'/\b(requestAnimationFrame|DOMContentLoaded)\b/i',
		'/\b(strtr|preg_replace|mb_strtoupper)\b/i',
		'/\b(Gutenberg|Custom Post Type|CPT)\b/i',
		'/\b(position:\s*fixed|position:\s*sticky)\b/i',
		'/\b(upload_max_filesize|post_max_size|upload_tmp_dir)\b/i',
	);
	foreach ( $patterns as $pattern ) {
		if ( preg_match_all( $pattern, $text, $m ) ) {
			foreach ( $m[1] as $t ) {
				$normalized = preg_replace( '/\s+/', ' ', trim( $t ) );
				if ( strlen( $normalized ) >= 2 ) {
					$terms[ $normalized ] = true;
				}
			}
		}
	}
	// Отдельные известные термины из статей
	$known = array( 'wp_enqueue_script', 'wp_enqueue_style', 'get_template_directory_uri', 'functions.php',
		'sanitize_title', 'sanitize_file_name', 'add_filter', 'register_post_type', 'Prism.js',
		'rsync', 'chown', 'chmod', 'php.ini', 'wp-content', 'uploads', 'PHP-FPM', 'Gutenberg',
		'requestAnimationFrame', 'DOMContentLoaded', 'strtr', 'preg_replace', 'upload_max_filesize',
		'post_max_size', 'upload_tmp_dir', 'приоритет', 'priority' );
	foreach ( $known as $t ) {
		if ( stripos( $text, $t ) !== false ) {
			$terms[ $t ] = true;
		}
	}
	return array_keys( $terms );
}

/**
 * Словарь недостающих терминов с определениями.
 */
function get_extra_glossary_terms() {
	return array(
		array(
			'title'      => 'wp_enqueue_script',
			'definition' => 'Функция WordPress для подключения JavaScript-файлов на фронтенде или в админке. Управляет зависимостями, версионированием и порядком загрузки скриптов.',
			'examples'   => "wp_enqueue_script( 'my-script', get_template_directory_uri() . '/assets/js/script.js', array( 'jquery' ), '1.0.0', true );" . "\n" . "Пятый параметр true — скрипт загружается в footer для ускорения отображения страницы.",
			'use_cases'  => "При добавлении своих JS-файлов в тему или плагин." . "\n" . "Всегда используйте вместо прямого <script> — WordPress избежит дублирования.",
			'aliases'    => 'wp_enqueue_script, wp enqueue script',
		),
		array(
			'title'      => 'wp_enqueue_style',
			'definition' => 'Функция WordPress для подключения CSS-файлов. Аналог wp_enqueue_script для стилей. Управляет зависимостями и порядком загрузки.',
			'examples'   => "wp_enqueue_style( 'my-theme', get_stylesheet_uri(), array(), '1.0' );" . "\n" . "wp_enqueue_style( 'prism-theme', 'https://cdn.../prism-tomorrow.min.css', array(), '1.29.0' );",
			'use_cases'  => "Для подключения стилей темы, плагина или библиотек (Prism, Bootstrap)." . "\n" . "Используется в хуке wp_enqueue_scripts.",
			'aliases'    => 'wp_enqueue_style, wp enqueue style',
		),
		array(
			'title'      => 'get_template_directory_uri',
			'definition' => 'Функция WordPress, возвращающая URL корневой папки активной темы. Используется для формирования путей к ресурсам (JS, CSS, изображениям).',
			'examples'   => "get_template_directory_uri() . '/assets/js/sticky-header.js'" . "\n" . "Результат: https://site.ru/wp-content/themes/dekanpro/assets/js/sticky-header.js",
			'use_cases'  => "При подключении скриптов и стилей в теме." . "\n" . "Для дочерней темы используйте get_stylesheet_directory_uri().",
			'aliases'    => 'get_template_directory_uri, get template directory uri',
		),
		array(
			'title'      => 'functions.php',
			'definition' => 'Главный файл темы WordPress для добавления своего PHP-кода. Здесь подключают скрипты, регистрируют меню, добавляют хуки и фильтры. Выполняется при каждой загрузке сайта.',
			'examples'   => "add_action( 'wp_enqueue_scripts', 'my_theme_scripts' );" . "\n" . "function my_theme_scripts() { wp_enqueue_script( ... ); }",
			'use_cases'  => "Любые кастомные доработки темы без создания плагина." . "\n" . "Внимание: при смене темы код в functions.php теряется.",
			'aliases'    => 'functions.php, functions php',
		),
		array(
			'title'      => 'sanitize_title',
			'definition' => 'Функция WordPress, преобразующая строку (заголовок) в slug для URL. Убирает спецсимволы, заменяет пробелы на дефисы, применяет транслитерацию.',
			'examples'   => "sanitize_title( 'Как сделать липкий сайдбар' );" . "\n" . "Результат: kak-sdelat-lipkiy-saydbar (если есть фильтр транслитерации)" . "\n" . "add_filter( 'sanitize_title', 'my_transliterate', 9 ); — изменить поведение до стандартной обработки.",
			'use_cases'  => "При создании slug из пользовательского ввода." . "\n" . "Плагины транслитерации подключаются к этому фильтру.",
			'aliases'    => 'sanitize_title, sanitize title',
		),
		array(
			'title'      => 'sanitize_file_name',
			'definition' => 'Функция WordPress для очистки имени загружаемого файла. Удаляет недопустимые символы, приводит к безопасному формату.',
			'examples'   => "add_filter( 'sanitize_file_name', 'my_transliterate', 9 );" . "\n" . "Файл «Документ.pdf» → document.pdf после транслитерации.",
			'use_cases'  => "Когда нужна транслитерация имён загружаемых файлов." . "\n" . "Работает аналогично sanitize_title.",
			'aliases'    => 'sanitize_file_name, sanitize file name',
		),
		array(
			'title'      => 'register_post_type',
			'definition' => 'Функция WordPress для регистрации пользовательского типа записей (Custom Post Type). Создаёт новый раздел в админке и свою структуру контента.',
			'examples'   => "register_post_type( 'glossary_term', array( 'public' => false, 'show_ui' => true, 'show_in_menu' => 'tools.php', 'supports' => array( 'title' ) ) );" . "\n" . "Тип glossary_term появляется в меню «Инструменты».",
			'use_cases'  => "Для портфолио, товаров, справочников, событий — любой структуры данных кроме стандартных записей и страниц." . "\n" . "Вызывать в хуке init.",
			'aliases'    => 'register_post_type, register post type',
		),
		array(
			'title'      => 'Prism.js',
			'definition' => 'Лёгкая библиотека для подсветки синтаксиса кода на веб-страницах. Разбивает код на токены и раскрашивает их по типу (ключевые слова, строки, комментарии).',
			'examples'   => "<pre><code class=\"language-php\">echo 'Hello';</code></pre>" . "\n" . "Prism автоматически подсветит код по классу language-php." . "\n" . "Тема Prism Tomorrow — тёмный фон с контрастными цветами.",
			'use_cases'  => "Для блогов и документации с примерами кода." . "\n" . "Поддерживает PHP, JavaScript, CSS, HTML и многие другие языки.",
			'aliases'    => 'Prism.js, Prism, prism',
		),
		array(
			'title'      => 'rsync',
			'definition' => 'Утилита Linux/Unix для синхронизации файлов между компьютерами или папками. Используется при деплое сайтов — копирует изменённые файлы на сервер.',
			'examples'   => "rsync -avz --exclude 'wp-content/uploads' ./ user@server:/path/to/site/" . "\n" . "Флаг -a сохраняет права и владельца, -v вывод, -z сжатие. После rsync файлы могут получить владельца разработчика вместо www-data.",
			'use_cases'  => "Деплой через SSH." . "\n" . "Инкрементальное резервное копирование.",
			'aliases'    => 'rsync',
		),
		array(
			'title'      => 'chown',
			'definition' => 'Команда Unix/Linux для смены владельца файлов и папок. После деплоя через rsync папка uploads может принадлежать разработчику — PHP не сможет писать. chown исправляет владельца.',
			'examples'   => "sudo chown -R www-root:www-root /var/www/site/wp-content/uploads" . "\n" . "-R рекурсивно для всех вложенных файлов. www-root — пользователь, под которым работает PHP-FPM.",
			'use_cases'  => "Исправление ошибки «Загруженный файл не удалось переместить»." . "\n" . "В скриптах деплоя после rsync.",
			'aliases'    => 'chown',
		),
		array(
			'title'      => 'chmod',
			'definition' => 'Команда Unix/Linux для изменения прав доступа к файлам. Задаёт, кто может читать, писать или выполнять. Для папок uploads обычно 775, для файлов 664.',
			'examples'   => "chmod 775 uploads" . "\n" . "find uploads -type d -exec chmod 775 {} \; — папки 775" . "\n" . "find uploads -type f -exec chmod 664 {} \; — файлы 664",
			'use_cases'  => "Настройка прав после деплоя или миграции." . "\n" . "Когда PHP не может записать в папку.",
			'aliases'    => 'chmod',
		),
		array(
			'title'      => 'php.ini',
			'definition' => 'Файл конфигурации PHP. Задаёт лимиты загрузки, размер памяти, временные папки и другие параметры. Ошибки загрузки файлов часто связаны с лимитами в php.ini.',
			'examples'   => "upload_max_filesize = 64M" . "\n" . "post_max_size = 64M" . "\n" . "upload_tmp_dir = /tmp — папка должна быть доступна для записи.",
			'use_cases'  => "Увеличение лимита загрузки." . "\n" . "Диагностика проблем с загрузкой файлов.",
			'aliases'    => 'php.ini, php ini',
		),
		array(
			'title'      => 'wp-content',
			'definition' => 'Главная папка пользовательского контента WordPress. Содержит themes, plugins, uploads. Файлы темы, плагинов и загрузок хранятся здесь.',
			'examples'   => "wp-content/themes/dekanpro — папка темы" . "\n" . "wp-content/plugins/glossary-tooltips — папка плагина" . "\n" . "wp-content/uploads/2026/02 — загруженные изображения по месяцам",
			'use_cases'  => "Когда говорим о структуре WordPress." . "\n" . "При исключении uploads из rsync: --exclude 'wp-content/uploads'",
			'aliases'    => 'wp-content, wp content',
		),
		array(
			'title'      => 'uploads',
			'definition' => 'Папка внутри wp-content для загруженных пользователем файлов (изображения, документы). Структура: uploads/ГГГГ/ММ. Не должна перетираться при деплое.',
			'examples'   => "wp-content/uploads/2026/02/image.jpg" . "\n" . "Ошибка «не удалось переместить» — часто из-за прав: папка принадлежит не www-data.",
			'use_cases'  => "Исключать из деплоя: rsync --exclude 'wp-content/uploads'." . "\n" . "После деплоя: chown -R www-root:www-root .../uploads",
			'aliases'    => 'uploads, загрузки',
		),
		array(
			'title'      => 'PHP-FPM',
			'definition' => 'Менеджер процессов PHP (FastCGI Process Manager). Обрабатывает PHP-запросы отдельно от веб-сервера. Пользователь PHP задаётся в конфиге пула (user = www-root).',
			'examples'   => "[dekan.pro]" . "\n" . "user = www-root" . "\n" . "group = www-root" . "\n" . "Файлы должны принадлежать этому пользователю, чтобы PHP мог писать.",
			'use_cases'  => "Понимание, под кем работает PHP на хостинге." . "\n" . "Настройка прав для загрузок.",
			'aliases'    => 'PHP-FPM, PHP FPM, php-fpm',
		),
		array(
			'title'      => 'Gutenberg',
			'definition' => 'Редактор блоков WordPress (с версии 5.0). Контент состоит из блоков: параграф, заголовок, изображение, код и т.д. Заменил классический редактор.',
			'examples'   => "Блок «Код» — для вставки кода с подсветкой. В настройках справа выбирается язык (PHP, JavaScript)." . "\n" . "WordPress автоматически добавляет class=\"language-php\" к тегу code.",
			'use_cases'  => "Когда говорим о редактировании записей в WordPress." . "\n" . "Для блоков кода используйте блок «Код» с выбором языка.",
			'aliases'    => 'Gutenberg, Гутенберг',
		),
		array(
			'title'      => 'requestAnimationFrame',
			'definition' => 'Метод браузера для оптимизации анимаций и обработки скролла. Вызывает callback перед следующей перерисовкой (обычно 60 раз в секунду). Предотвращает «тормоза» при прокрутке.',
			'examples'   => "let ticking = false;" . "\n" . "window.addEventListener('scroll', function() {" . "\n" . "    if (!ticking) { requestAnimationFrame(function() { handleScroll(); ticking = false; }); ticking = true; }" . "\n" . "}, { passive: true });",
			'use_cases'  => "Оптимизация обработчиков scroll и resize." . "\n" . "Throttling без потери кадров.",
			'aliases'    => 'requestAnimationFrame, requestAnimationFrame',
		),
		array(
			'title'      => 'DOMContentLoaded',
			'definition' => 'Событие браузера, срабатывающее когда HTML загружен и распарсен, без ожидания стилей, изображений и т.д. Раньше, чем load.',
			'examples'   => "document.addEventListener('DOMContentLoaded', initStickyElements);" . "\n" . "или if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init); } else { init(); }",
			'use_cases'  => "Запуск скриптов после готовности DOM." . "\n" . "Раньше использовали jQuery $(document).ready().",
			'aliases'    => 'DOMContentLoaded, DOM content loaded',
		),
		array(
			'title'      => 'strtr',
			'definition' => 'Функция PHP для замены символов по таблице соответствия. Самый быстрый способ транслитерации: strtr($text, array(\'а\' => \'a\', \'б\' => \'b\', ...)).',
			'examples'   => "\$title = strtr(\$title, \$table);" . "\n" . "«Привет» → «Privet» при правильной таблице." . "\n" . "Используется в плагинах транслитерации вместо preg_replace.",
			'use_cases'  => "Транслитерация кириллицы в латиницу." . "\n" . "Замена набора символов одним вызовом.",
			'aliases'    => 'strtr',
		),
		array(
			'title'      => 'preg_replace',
			'definition' => 'Функция PHP для замены по регулярному выражению. preg_replace(\'/pattern/\', \'replacement\', $string) — заменить все вхождения. Используется для очистки slug, имён файлов.',
			'examples'   => "preg_replace('/[^a-zA-Z0-9\\-_.]/', '-', \$title); — заменить недопустимые символы на дефис" . "\n" . "preg_replace('/-+/', '-', \$title); — схлопнуть несколько дефисов подряд.",
			'use_cases'  => "Очистка slug после транслитерации." . "\n" . "Любая замена по сложному шаблону.",
			'aliases'    => 'preg_replace, preg replace',
		),
		array(
			'title'      => 'приоритет',
			'definition' => 'Порядок выполнения функций, подписанных на один хук. Передаётся третьим аргументом в add_action и add_filter. Чем больше число — тем позже выполнится. По умолчанию 10.',
			'examples'   => "add_action( 'admin_menu', 'my_menu', 25 ); — выполнится позже, чем подписчики с 10" . "\n" . "add_filter( 'sanitize_title', 'transliterate', 9 ); — выполнится до стандартной обработки WordPress (10).",
			'use_cases'  => "Когда нужно выполнить код раньше или позже других плагинов." . "\n" . "Приоритет 9 — до стандартного, 11 и выше — после.",
			'aliases'    => 'приоритет, priority',
		),
		array(
			'title'      => 'upload_max_filesize',
			'definition' => 'Директива php.ini. Максимальный размер одного загружаемого файла в байтах (или M для мегабайт). Ограничивает загрузку изображений и документов через WordPress.',
			'examples'   => "upload_max_filesize = 64M" . "\n" . "post_max_size должен быть не меньше upload_max_filesize — иначе загрузка не дойдёт до PHP.",
			'use_cases'  => "Увеличение лимита для больших файлов." . "\n" . "Диагностика «файл слишком большой».",
			'aliases'    => 'upload_max_filesize, upload max filesize',
		),
		array(
			'title'      => 'post_max_size',
			'definition' => 'Директива php.ini. Максимальный размер данных POST-запроса. Должен быть не меньше upload_max_filesize, иначе загружаемый файл «отрежется» до попадания в PHP.',
			'examples'   => "post_max_size = 64M" . "\n" . "Если upload_max_filesize = 64M, то post_max_size минимум 64M.",
			'use_cases'  => "При ошибках загрузки — проверить оба параметра." . "\n" . "Часто забывают увеличить post_max_size.",
			'aliases'    => 'post_max_size, post max size',
		),
		array(
			'title'      => 'upload_tmp_dir',
			'definition' => 'Директива php.ini. Папка для временного хранения загружаемых файлов (обычно /tmp). PHP сохраняет файл сюда, затем WordPress перемещает в wp-content/uploads. Папка должна быть доступна для записи.',
			'examples'   => "upload_tmp_dir = /tmp" . "\n" . "Если /tmp заполнена или недоступна — загрузка не сработает.",
			'use_cases'  => "Диагностика «не удалось переместить»." . "\n" . "Проверка свободного места на диске.",
			'aliases'    => 'upload_tmp_dir, upload tmp dir',
		),
	);
}

$extra = get_extra_glossary_terms();

// Собираем контент из постов и docs
$all_text = '';
$posts = get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => -1 ) );
foreach ( $posts as $post ) {
	$all_text .= ' ' . extract_text_from_html( $post->post_content );
}
$docs_dir = __DIR__ . '/wp-content/themes/dekanpro/docs';
if ( is_dir( $docs_dir ) ) {
	foreach ( glob( $docs_dir . '/*.{html,txt}', GLOB_BRACE ) ?: array() as $f ) {
		$all_text .= ' ' . extract_text_from_html( file_get_contents( $f ) );
	}
}

$found_terms = extract_technical_terms( $all_text );
$existing = array();
$glossary_posts = get_posts( array( 'post_type' => 'glossary_term', 'post_status' => 'publish', 'posts_per_page' => -1 ) );
foreach ( $glossary_posts as $gp ) {
	$existing[ strtolower( trim( $gp->post_title ) ) ] = true;
	$aliases = get_post_meta( $gp->ID, '_glossary_aliases', true );
	if ( $aliases ) {
		foreach ( explode( ',', $aliases ) as $a ) {
			$existing[ strtolower( trim( $a ) ) ] = true;
		}
	}
}

$extra = get_extra_glossary_terms();
$to_add = array();
foreach ( $extra as $term_data ) {
	$title_lower = strtolower( $term_data['title'] );
	$aliases = isset( $term_data['aliases'] ) ? array_map( 'trim', explode( ',', $term_data['aliases'] ) ) : array();
	$any_exists = isset( $existing[ $title_lower ] );
	foreach ( $aliases as $a ) {
		if ( isset( $existing[ strtolower( $a ) ] ) ) {
			$any_exists = true;
			break;
		}
	}
	if ( ! $any_exists ) {
		$to_add[] = $term_data;
	}
}

$created = 0;
foreach ( $to_add as $term_data ) {
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
		echo "Добавлен: " . $term_data['title'] . "\n";
	}
}

Glossary_Tooltips::clear_cache();
echo "\nГотово. Добавлено терминов: $created\n";

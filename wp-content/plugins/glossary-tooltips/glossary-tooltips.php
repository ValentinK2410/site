<?php
/**
 * Plugin Name: Glossary Tooltips
 * Plugin URI: https://dekan.pro/
 * Description: Справочная система: технические термины в статьях раскрываются по клику с пояснениями и примерами.
 * Version: 1.0.2
 * Author: DekanPro
 * Text Domain: glossary-tooltips
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GLOSSARY_TOOLTIPS_VERSION', '1.0.2' );
define( 'GLOSSARY_TOOLTIPS_PATH', plugin_dir_path( __FILE__ ) );
define( 'GLOSSARY_TOOLTIPS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Главный класс плагина.
 */
final class Glossary_Tooltips {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'register_post_type' ), 20 );
		add_action( 'admin_menu', array( $this, 'add_help_submenu' ), 25 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_glossary_term', array( $this, 'save_term_meta' ), 10, 2 );

		add_filter( 'the_content', array( $this, 'filter_content' ), 20 );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'load-edit.php', array( $this, 'add_help_tab_on_list' ), 20 );
		add_action( 'load-post.php', array( $this, 'add_help_tab_on_edit' ), 20 );
		add_action( 'load-post-new.php', array( $this, 'add_help_tab_on_edit' ), 20 );
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'glossary-tooltips', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Регистрация Custom Post Type для терминов глоссария.
	 */
	public function register_post_type() {
		$labels = array(
			'name'               => 'Термины глоссария',
			'singular_name'      => 'Термин',
			'menu_name'          => 'Глоссарий',
			'add_new'            => 'Добавить термин',
			'add_new_item'       => 'Добавить новый термин',
			'edit_item'          => 'Редактировать термин',
			'new_item'           => 'Новый термин',
			'view_item'          => 'Просмотр термина',
			'search_items'       => 'Искать термины',
			'not_found'          => 'Терминов не найдено',
			'not_found_in_trash' => 'В корзине терминов не найдено',
		);

		$args = array(
			'labels'              => $labels,
			'public'              => false,
			'publicly_queryable'   => false,
			'show_ui'             => true,
			'show_in_menu'        => 'tools.php',
			'query_var'           => false,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => 58,
			'menu_icon'           => 'dashicons-book-alt',
			'supports'            => array( 'title' ),
		);

		register_post_type( 'glossary_term', $args );
	}

	/**
	 * Добавление страницы «Справка» в меню глоссария.
	 */
	public function add_help_submenu() {
		add_submenu_page(
			'tools.php',
			'Как пользоваться глоссарием',
			'Глоссарий: Справка',
			'read',
			'glossary-tooltips-help',
			array( $this, 'render_help_page' )
		);
	}

	/**
	 * Обработка импорта справочных терминов по запросу.
	 */
	private function maybe_import_default_terms() {
		if ( ! current_user_can( 'edit_posts' ) || ! isset( $_POST['glossary_action'] ) || 'import_defaults' !== $_POST['glossary_action'] ) {
			return;
		}
		if ( ! isset( $_POST['glossary_import_nonce'] ) || ! wp_verify_nonce( $_POST['glossary_import_nonce'], 'glossary_import_defaults' ) ) {
			return;
		}
		$created = $this->create_default_glossary_terms();
		add_action( 'admin_notices', function () use ( $created ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( sprintf( __( 'Добавлено терминов: %d.', 'glossary-tooltips' ), $created ) ) . '</p></div>';
		} );
	}

	/**
	 * Список справочных терминов для импорта.
	 */
	private function get_default_glossary_terms() {
		return array(
			array(
				'title'      => 'add_submenu_page',
				'definition' => 'Функция WordPress, которая добавляет подпункт в существующее меню админки. Используется, когда нужно создать новую страницу внутри раздела (например, «Инструменты», «Настройки»).',
				'examples'   => "Добавление страницы справки в «Инструменты»: add_submenu_page( 'tools.php', 'Справка', 'Справка', 'read', 'my-help', 'my_help_callback' );" . "\n" . "Добавление подпункта в «Настройки»: add_submenu_page( 'options-general.php', 'Мои настройки', 'Мои настройки', 'manage_options', 'my-settings', 'my_settings_callback' );",
				'use_cases'  => "Когда нужна страница справки, настроек плагина или утилиты внутри стандартного раздела WordPress." . "\n" . "Когда не хотите создавать отдельный пункт в главном меню, а предпочитаете разместить страницу в «Инструменты» или «Настройки».",
				'aliases'    => 'add_submenu_page, add submenu page',
			),
			array(
				'title'      => 'add_menu_page',
				'definition' => 'Функция WordPress, которая добавляет новый пункт в главное меню админки (слева). Создаёт отдельный раздел с собственной иконкой.',
				'examples'   => "add_menu_page( 'Мой раздел', 'Мой раздел', 'manage_options', 'my-plugin', 'my_callback', 'dashicons-admin-generic', 30 );" . "\n" . "Параметры: заголовок, текст в меню, права доступа, slug, функция вывода, иконка, позиция.",
				'use_cases'  => "Когда плагин или тема создают отдельный раздел с несколькими подстраницами." . "\n" . "Для панелей управления, кабинетов, отчётов и т.п.",
				'aliases'    => 'add_menu_page, add menu page',
			),
			array(
				'title'      => 'capability',
				'definition' => 'Право доступа в WordPress. Определяет, какие действия разрешены пользователю (редактировать записи, управлять настройками и т.д.). Страницы и меню показываются только тем, у кого есть нужное право.',
				'examples'   => "read — минимальное право, есть у всех авторизованных пользователей." . "\n" . "edit_posts — редактирование своих записей (авторы, редакторы, администраторы)." . "\n" . "manage_options — только администраторы, для страниц настроек." . "\n" . "В add_submenu_page: add_submenu_page( 'tools.php', 'Справка', 'Справка', 'read', ... );",
				'use_cases'  => "При добавлении страниц в админку — указать, кто может её видеть." . "\n" . "Чтобы скрыть чувствительные настройки от обычных редакторов, используйте manage_options.",
				'aliases'    => 'capability, права доступа, capability type',
			),
			array(
				'title'      => 'Custom Post Type',
				'definition' => 'Пользовательский тип записей в WordPress. Помимо стандартных «Записей» и «Страниц» можно создавать свои типы: портфолио, товары, термины глоссария и др. Каждый тип имеет свои экраны в админке.',
				'examples'   => "Регистрация: register_post_type( 'glossary_term', array( 'labels' => ..., 'public' => false, 'show_ui' => true, 'show_in_menu' => 'tools.php' ) );" . "\n" . "Тип «glossary_term» — для терминов глоссария, отображается в «Инструменты».",
				'use_cases'  => "Когда нужна структура данных, отличная от записей и страниц." . "\n" . "Для каталогов, справочников, событий, отзывов и т.п.",
				'aliases'    => 'CPT, Custom Post Type, пользовательский тип записей',
			),
			array(
				'title'      => 'slug',
				'definition' => 'Короткий идентификатор в URL. В WordPress используется для страниц, категорий, пунктов меню. Должен быть уникальным в рамках контекста. Обычно латиница, цифры и дефисы.',
				'examples'   => "Страница справки: glossary-tooltips-help → URL: /wp-admin/tools.php?page=glossary-tooltips-help" . "\n" . "Страница сайта: o-nas → URL: https://site.ru/o-nas/" . "\n" . "Категория: wordpress → URL: https://site.ru/category/wordpress/",
				'use_cases'   => "При создании страниц, категорий, типов записей — задать уникальный slug." . "\n" . "Slug влияет на URL, поэтому его лучше задавать сразу и не менять.",
				'aliases'    => 'slug, слаг',
			),
			array(
				'title'      => 'хук',
				'definition' => 'Точка «подключения» в WordPress. В определённый момент работы системы WordPress вызывает все функции, «подписанные» на этот хук. Позволяет расширять поведение без изменения ядра.',
				'examples'   => "add_action( 'admin_menu', 'my_function' ) — выполнит my_function при формировании меню админки." . "\n" . "add_action( 'init', 'my_function' ) — при инициализации WordPress." . "\n" . "add_filter( 'the_content', 'my_function' ) — изменить контент перед выводом.",
				'use_cases'  => "Чтобы выполнить свой код в нужный момент (при загрузке, сохранении, выводе и т.д.)." . "\n" . "Плагины и темы почти всегда используют хуки для интеграции с WordPress.",
				'aliases'    => 'хук, hook, add_action, add_filter',
			),
			array(
				'title'      => 'callback',
				'definition' => 'Функция, которая вызывается «позже» — когда произойдёт событие или по запросу. В WordPress передаётся как аргумент в add_action, add_submenu_page и аналогичные функции.',
				'examples'   => "add_submenu_page( 'tools.php', 'Справка', 'Справка', 'read', 'help', 'render_help_page' ); — render_help_page вызовется при открытии страницы." . "\n" . "add_action( 'admin_menu', array( \$this, 'add_menu' ) ); — метод add_menu вызовется при событии admin_menu.",
				'use_cases'  => "Когда нужно указать, какую функцию выполнить при наступлении события." . "\n" . "Используется везде: хуки, регистрация страниц, кнопки, AJAX.",
				'aliases'    => 'callback, колбэк, функция обратного вызова',
			),
			array(
				'title'      => 'add_help_tab',
				'definition' => 'Метод объекта экрана WordPress, который добавляет вкладку в контекстную справку (правый верхний угол экрана). Позволяет показать подсказку прямо на странице редактирования.',
				'examples'   => "\$screen = get_current_screen();" . "\n" . "\$screen->add_help_tab( array( 'id' => 'my-help', 'title' => 'Справка', 'content' => '<p>Текст подсказки</p>' ) );" . "\n" . "Вкладка появится рядом с «Помощь» в правом верхнем углу.",
				'use_cases'  => "Чтобы пользователь видел инструкцию без перехода на отдельную страницу." . "\n" . "На экранах создания и редактирования записей, настроек плагинов.",
				'aliases'    => 'add_help_tab, вкладка справки, help tab',
			),
			array(
				'title'      => 'get_current_screen',
				'definition' => 'Функция WordPress, возвращающая объект текущего экрана админки. Содержит информацию о странице: тип записи, id экрана и т.д. Используется для условной логики и добавления вкладок справки.',
				'examples'   => "\$screen = get_current_screen();" . "\n" . "if ( \$screen && 'glossary_term' === \$screen->post_type ) { /* мы на странице глоссария */ }" . "\n" . "\$screen->add_help_tab( ... );",
				'use_cases'  => "Когда нужно выполнить код только на определённых экранах админки." . "\n" . "Для добавления справки, скриптов или стилей точечно.",
				'aliases'    => 'get_current_screen, get current screen',
			),
			array(
				'title'      => 'add_action',
				'definition' => 'Функция WordPress для «подписки» на хук. Указывает: при каком событии и какую функцию вызвать. Основа расширения WordPress без изменения ядра.',
				'examples'   => "add_action( 'init', 'my_init' ); — вызвать my_init при инициализации." . "\n" . "add_action( 'admin_menu', 'my_menu', 25 ); — вызвать my_menu при формировании меню, приоритет 25." . "\n" . "add_action( 'save_post', 'my_save', 10, 2 ); — при сохранении записи, 2 аргумента.",
				'use_cases'  => "Чтобы ваш код выполнился в нужный момент." . "\n" . "Используется в каждом плагине и во многих темах.",
				'aliases'    => 'add_action, add_filter',
			),
			array(
				'title'      => 'add_filter',
				'definition' => 'Функция WordPress для «подписки» на фильтр. Похожа на add_action, но используется когда нужно изменить значение (контент, заголовок и т.д.) перед выводом или использованием. Функция должна вернуть изменённое значение.',
				'examples'   => "add_filter( 'the_content', 'my_content_filter' ); — изменить контент записи перед выводом." . "\n" . "add_filter( 'the_title', function( \$title ) { return \$title . ' — '; }, 10, 2 ); — добавить текст к заголовку.",
				'use_cases'  => "Когда нужно изменить данные (текст, HTML, массив) перед их использованием." . "\n" . "Для подсветки терминов в тексте, добавления виджетов, изменения ссылок и т.п.",
				'aliases'    => 'add_filter',
			),
			array(
				'title'      => 'admin_menu',
				'definition' => 'Хук WordPress, который срабатывает при формировании меню админки. Используется для добавления своих пунктов и подпунктов в левое меню wp-admin.',
				'examples'   => "add_action( 'admin_menu', 'register_my_pages' );" . "\n" . "function register_my_pages() {" . "\n" . "    add_submenu_page( 'tools.php', 'Моя страница', 'Моя страница', 'read', 'my-page', 'my_page_callback' );" . "\n" . "}",
				'use_cases'  => "Когда нужно добавить страницу в админку." . "\n" . "Практически обязателен для плагинов с настройками или справкой.",
				'aliases'    => 'admin_menu',
			),
			array(
				'title'      => 'post type',
				'definition' => 'Тип контента в WordPress. Стандартные: post (записи), page (страницы), attachment (вложения). Можно создавать свои типы через register_post_type — портфолио, товары, термины и т.д.',
				'examples'   => "post — записи блога, отображаются в «Записи»." . "\n" . "page — статические страницы (О сайте, Контакты)." . "\n" . "glossary_term — пользовательский тип для терминов глоссария.",
				'use_cases'  => "Когда говорим о том, какой вид контента обрабатываем." . "\n" . "В коде: get_posts( array( 'post_type' => 'glossary_term' ) ) — получить записи типа glossary_term.",
				'aliases'    => 'post type, тип записи',
			),
			array(
				'title'      => 'wp-admin',
				'definition' => 'Папка и URL админ-панели WordPress. По адресу /wp-admin/ открывается интерфейс управления сайтом: создание записей, настройки, плагины и т.д. Доступен только авторизованным пользователям.',
				'examples'   => "https://site.ru/wp-admin/ — вход в админку." . "\n" . "https://site.ru/wp-admin/tools.php — раздел «Инструменты»." . "\n" . "admin_url( 'tools.php' ) — получить полный URL в коде.",
				'use_cases'  => "Когда нужна ссылка на раздел админки." . "\n" . "В плагинах: редирект после активации, ссылки «Перейти в настройки».",
				'aliases'    => 'wp-admin, админка, админ-панель',
			),
			array(
				'title'      => 'nonce',
				'definition' => 'Одноразовый токен безопасности в WordPress. Проверяет, что запрос (форма, ссылка) сформирован вашим сайтом и не подделан. Защита от CSRF-атак.',
				'examples'   => "В форме: wp_nonce_field( 'my_action', 'my_nonce' );" . "\n" . "При проверке: wp_verify_nonce( \$_POST['my_nonce'], 'my_action' );" . "\n" . "В ссылке: wp_nonce_url( admin_url( 'admin.php?action=delete' ), 'delete_item' );",
				'use_cases'  => "Для всех форм и действий, изменяющих данные." . "\n" . "Обязательно при удалении, импорте, сохранении настроек из админки.",
				'aliases'    => 'nonce, нонс, wp_nonce_field, wp_verify_nonce',
			),
		);
	}

	/**
	 * Создание справочных терминов в глоссарии.
	 */
	private function create_default_glossary_terms() {
		$terms  = $this->get_default_glossary_terms();
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
			}
		}

		if ( $created > 0 ) {
			self::clear_cache();
		}
		return $created;
	}

	/**
	 * Публичный метод для вызова импорта из скрипта.
	 */
	public function do_import_default_terms() {
		return $this->create_default_glossary_terms();
	}

	/**
	 * Отображение страницы справки.
	 */
	public function render_help_page() {
		if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['glossary_action'] ) && 'import_defaults' === $_POST['glossary_action'] ) {
			$this->maybe_import_default_terms();
		}
		?>
		<div class="wrap glossary-help-wrap" style="max-width: 720px;">
			<h1><?php esc_html_e( 'Как пользоваться глоссарием', 'glossary-tooltips' ); ?></h1>

			<?php if ( current_user_can( 'edit_posts' ) ) : ?>
			<div style="margin-bottom: 1.5em;">
				<form method="post" style="display:inline;">
					<?php wp_nonce_field( 'glossary_import_defaults', 'glossary_import_nonce' ); ?>
					<input type="hidden" name="glossary_action" value="import_defaults" />
					<button type="submit" class="button button-secondary"><?php esc_html_e( 'Добавить справочные термины из статьи', 'glossary-tooltips' ); ?></button>
				</form>
				<span class="description"><?php esc_html_e( 'Создаст в глоссарии технические термины (add_submenu_page, хук, callback и др.) с пояснениями и примерами.', 'glossary-tooltips' ); ?></span>
			</div>
			<?php endif; ?>

			<div class="glossary-help-content" style="line-height: 1.7;">
				<h2><?php esc_html_e( 'Что это?', 'glossary-tooltips' ); ?></h2>
				<p><?php esc_html_e( 'Глоссарий — это справочная система для технических терминов в статьях. Когда читатель видит незнакомое слово (например, API, REST, PHP), он может кликнуть по нему — откроется окно с пояснением, примерами и рекомендациями по использованию.', 'glossary-tooltips' ); ?></p>

				<h2><?php esc_html_e( 'Как добавить термин?', 'glossary-tooltips' ); ?></h2>
				<ol style="margin-left: 1.5em;">
					<li><?php esc_html_e( 'Перейдите в Инструменты → Термины глоссария → Добавить термин.', 'glossary-tooltips' ); ?></li>
					<li><?php esc_html_e( 'В поле «Заголовок» введите сам термин (например: API, REST, хуки).', 'glossary-tooltips' ); ?></li>
					<li>
						<?php esc_html_e( 'Заполните блок «Содержание термина»:', 'glossary-tooltips' ); ?>
						<ul style="margin: 0.5em 0 0 1em;">
							<li><strong>Пояснение</strong> — <?php esc_html_e( 'краткое объяснение для пользователя. Что это такое и зачем нужно.', 'glossary-tooltips' ); ?></li>
							<li><strong>Примеры использования</strong> — <?php esc_html_e( 'где и как термин применяется. Каждый пример с новой строки.', 'glossary-tooltips' ); ?></li>
							<li><strong>В каких случаях использовать</strong> — <?php esc_html_e( 'когда уместно применять этот термин или концепцию.', 'glossary-tooltips' ); ?></li>
							<li><strong>Варианты написания (алиасы)</strong> — <?php esc_html_e( 'через запятую. Например: API, апи, эй-пи-ай. Эти варианты будут подсвечиваться так же, как основной термин.', 'glossary-tooltips' ); ?></li>
						</ul>
					</li>
					<li><?php esc_html_e( 'Нажмите «Опубликовать».', 'glossary-tooltips' ); ?></li>
				</ol>

				<h2><?php esc_html_e( 'Как это работает для читателя?', 'glossary-tooltips' ); ?></h2>
				<p><?php esc_html_e( 'В опубликованных статьях (записях блога) технические термины из глоссария автоматически подсвечиваются пунктирной линией. При клике или нажатии Enter/Space открывается всплывающее окно с:', 'glossary-tooltips' ); ?></p>
				<ul style="margin-left: 1.5em;">
					<li><?php esc_html_e( 'пояснением термина;', 'glossary-tooltips' ); ?></li>
					<li><?php esc_html_e( 'примерами использования;', 'glossary-tooltips' ); ?></li>
					<li><?php esc_html_e( 'рекомендациями, в каких случаях применять.', 'glossary-tooltips' ); ?></li>
				</ul>
				<p><?php esc_html_e( 'Термины внутри блоков кода (<code>&lt;code&gt;</code>, <code>&lt;pre&gt;</code>) не подсвечиваются.', 'glossary-tooltips' ); ?></p>

				<h2><?php esc_html_e( 'Советы', 'glossary-tooltips' ); ?></h2>
				<ul style="margin-left: 1.5em;">
					<li><?php esc_html_e( 'Длинные фразы обрабатываются первыми: если добавить «REST API» и «API» отдельно, в тексте «REST API» будет подсвечено целиком.', 'glossary-tooltips' ); ?></li>
					<li><?php esc_html_e( 'Используйте алиасы для разных написаний: API и апи — один термин, несколько вариантов отображения.', 'glossary-tooltips' ); ?></li>
					<li><?php esc_html_e( 'После добавления или изменения термина изменения видны сразу (кэш обновляется автоматически).', 'glossary-tooltips' ); ?></li>
				</ul>
			</div>
		</div>
		<?php
	}

	/**
	 * Мета-боксы для полей термина.
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'glossary_term_meta',
			'Содержание термина',
			array( $this, 'render_term_meta_box' ),
			'glossary_term',
			'normal'
		);
	}

	public function render_term_meta_box( $post ) {
		wp_nonce_field( 'glossary_term_meta', 'glossary_term_meta_nonce' );

		$definition   = get_post_meta( $post->ID, '_glossary_definition', true );
		$examples     = get_post_meta( $post->ID, '_glossary_examples', true );
		$use_cases    = get_post_meta( $post->ID, '_glossary_use_cases', true );
		$aliases      = get_post_meta( $post->ID, '_glossary_aliases', true );
		?>
		<table class="form-table">
			<tr>
				<th><label for="glossary_definition"><?php esc_html_e( 'Пояснение', 'glossary-tooltips' ); ?></label></th>
				<td>
					<textarea id="glossary_definition" name="glossary_definition" rows="4" class="large-text"><?php echo esc_textarea( $definition ); ?></textarea>
					<p class="description">Краткое объяснение термина для пользователя.</p>
				</td>
			</tr>
			<tr>
				<th><label for="glossary_examples"><?php esc_html_e( 'Примеры использования', 'glossary-tooltips' ); ?></label></th>
				<td>
					<textarea id="glossary_examples" name="glossary_examples" rows="5" class="large-text"><?php echo esc_textarea( $examples ); ?></textarea>
					<p class="description">Примеры: где и как используется. Каждый пример с новой строки.</p>
				</td>
			</tr>
			<tr>
				<th><label for="glossary_use_cases"><?php esc_html_e( 'В каких случаях использовать', 'glossary-tooltips' ); ?></label></th>
				<td>
					<textarea id="glossary_use_cases" name="glossary_use_cases" rows="4" class="large-text"><?php echo esc_textarea( $use_cases ); ?></textarea>
					<p class="description">Когда уместно применять этот термин/концепцию.</p>
				</td>
			</tr>
			<tr>
				<th><label for="glossary_aliases"><?php esc_html_e( 'Варианты написания (алиасы)', 'glossary-tooltips' ); ?></label></th>
				<td>
					<input type="text" id="glossary_aliases" name="glossary_aliases" value="<?php echo esc_attr( $aliases ); ?>" class="large-text" placeholder="например: API, апи, эй-пи-ай">
					<p class="description">Через запятую. Будут подсвечиваться так же, как основной термин.</p>
				</td>
			</tr>
		</table>
		<?php
	}

	public function save_term_meta( $post_id, $post ) {
		if ( ! isset( $_POST['glossary_term_meta_nonce'] ) || ! wp_verify_nonce( $_POST['glossary_term_meta_nonce'], 'glossary_term_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$fields = array( 'glossary_definition', 'glossary_examples', 'glossary_use_cases', 'glossary_aliases' );
		foreach ( $fields as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				update_post_meta( $post_id, '_' . $field, sanitize_textarea_field( $_POST[ $field ] ) );
			}
		}
	}

	/**
	 * Получение всех активных терминов для подсветки в контенте.
	 */
	private function get_terms_for_content() {
		$cache_key = 'glossary_terms_content';
		$cached    = get_transient( $cache_key );
		if ( false !== $cached ) {
			return $cached;
		}

		$posts = get_posts( array(
			'post_type'      => 'glossary_term',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		) );

		$terms = array();
		foreach ( $posts as $post ) {
			$term = $post->post_title;
			if ( empty( trim( $term ) ) ) {
				continue;
			}
			$aliases = get_post_meta( $post->ID, '_glossary_aliases', true );
			$variants = array_filter( array_map( 'trim', array_merge( array( $term ), $aliases ? explode( ',', $aliases ) : array() ) ) );
			$terms[] = array(
				'id'         => $post->ID,
				'term'      => $term,
				'variants'  => array_unique( $variants ),
				'definition' => get_post_meta( $post->ID, '_glossary_definition', true ),
				'examples'   => get_post_meta( $post->ID, '_glossary_examples', true ),
				'use_cases'  => get_post_meta( $post->ID, '_glossary_use_cases', true ),
			);
		}

		set_transient( $cache_key, $terms, HOUR_IN_SECONDS );
		return $terms;
	}

	/**
	 * Фильтр контента: оборачивает термины в span с data-атрибутами.
	 */
	public function filter_content( $content ) {
		if ( ! is_singular() ) {
			return $content;
		}

		$terms = $this->get_terms_for_content();
		if ( empty( $terms ) ) {
			return $content;
		}

		// Сохраняем содержимое <code>, <pre>, <script> — не подсвечиваем термины внутри.
		$placeholders = array();
		$content = preg_replace_callback( '/<(code|pre|script)[^>]*>.*?<\/\1>/is', function ( $m ) use ( &$placeholders ) {
			$key = '{{GLOSSARY_SKIP_' . count( $placeholders ) . '}}';
			$placeholders[ $key ] = $m[0];
			return $key;
		}, $content );

		// Сортируем по длине варианта (длинные первыми), чтобы "REST API" матчился раньше "API".
		$all_variants = array();
		foreach ( $terms as $t ) {
			foreach ( $t['variants'] as $v ) {
				if ( '' !== $v ) {
					$all_variants[] = array( 'pattern' => $v, 'data' => $t );
				}
			}
		}
		usort( $all_variants, function ( $a, $b ) {
			return mb_strlen( $b['pattern'] ) - mb_strlen( $a['pattern'] );
		} );

		$content = $this->wrap_terms_in_content( $content, $all_variants );

		// Восстанавливаем блоки кода.
		foreach ( $placeholders as $key => $original ) {
			$content = str_replace( $key, $original, $content );
		}

		return $content;
	}

	/**
	 * Оборачивает термины в контенте. Длинные фразы обрабатываются первыми,
	 * чтобы «REST API» не разбивался на «REST» + «API».
	 */
	private function wrap_terms_in_content( $content, $variants ) {
		$placeholders = array();

		foreach ( $variants as $i => $item ) {
			$pattern = '/(?<![a-zA-Zа-яА-ЯёЁ0-9_])(' . preg_quote( $item['pattern'], '/' ) . ')(?![a-zA-Zа-яА-ЯёЁ0-9_])/iu';
			$data   = $item['data'];

			$content = preg_replace_callback( $pattern, function ( $m ) use ( $data, $i, &$placeholders ) {
				$placeholder = '{{GLOSSARY_PLACEHOLDER_' . $i . '_' . uniqid() . '}}';
				$placeholders[ $placeholder ] = array(
					'text' => $m[1],
					'data' => $data,
				);
				return $placeholder;
			}, $content );
		}

		foreach ( $placeholders as $placeholder => $info ) {
			$data   = $info['data'];
			$text   = $info['text'];
			$attr_def   = esc_attr( wp_strip_all_tags( $data['definition'] ) );
			$attr_ex    = esc_attr( wp_strip_all_tags( $data['examples'] ) );
			$attr_cases = esc_attr( wp_strip_all_tags( $data['use_cases'] ) );
			$html = '<span class="glossary-term" data-glossary-id="' . esc_attr( $data['id'] ) . '" data-definition="' . $attr_def . '" data-examples="' . $attr_ex . '" data-use-cases="' . $attr_cases . '" tabindex="0" role="button">' . esc_html( $text ) . '</span>';
			$content = str_replace( $placeholder, $html, $content );
		}

		return $content;
	}

	/**
	 * Сброс кэша при сохранении термина.
	 */
	public static function clear_cache() {
		delete_transient( 'glossary_terms_content' );
	}

	public function enqueue_scripts() {
		if ( ! is_singular() ) {
			return;
		}

		// Prism.js для подсветки кода в попапе (если тема не подключила)
		wp_enqueue_style(
			'prism-theme',
			'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css',
			array(),
			'1.29.0'
		);
		wp_enqueue_script(
			'prism-core',
			'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js',
			array(),
			'1.29.0',
			true
		);
		wp_enqueue_script(
			'prism-autoloader',
			'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js',
			array( 'prism-core' ),
			'1.29.0',
			true
		);

		$terms_for_popup = array();
		foreach ( $this->get_terms_for_content() as $t ) {
			$terms_for_popup[] = array(
				'term'       => $t['term'],
				'variants'   => $t['variants'],
				'definition' => $t['definition'],
				'examples'   => $t['examples'],
				'use_cases'  => $t['use_cases'],
			);
		}

		wp_enqueue_style(
			'glossary-tooltips',
			GLOSSARY_TOOLTIPS_URL . 'assets/css/glossary-tooltips.css',
			array(),
			GLOSSARY_TOOLTIPS_VERSION
		);
		wp_enqueue_script(
			'glossary-tooltips',
			GLOSSARY_TOOLTIPS_URL . 'assets/js/glossary-tooltips.js',
			array( 'jquery', 'prism-autoloader' ),
			GLOSSARY_TOOLTIPS_VERSION,
			true
		);
		wp_localize_script( 'glossary-tooltips', 'glossaryTermsForPopup', $terms_for_popup );
	}

	public function add_help_tab_on_list() {
		if ( ! isset( $_GET['post_type'] ) || 'glossary_term' !== sanitize_key( $_GET['post_type'] ) ) {
			return;
		}
		$this->add_help_tab_to_screen();
	}

	public function add_help_tab_on_edit() {
		if ( isset( $_GET['post'] ) ) {
			$post = get_post( (int) $_GET['post'] );
		} elseif ( isset( $_GET['post_type'] ) ) {
			$post = (object) array( 'post_type' => sanitize_key( $_GET['post_type'] ) );
		} else {
			return;
		}
		if ( ! $post || 'glossary_term' !== $post->post_type ) {
			return;
		}
		$this->add_help_tab_to_screen();
	}

	private function add_help_tab_to_screen() {
		$screen = get_current_screen();
		if ( ! $screen || ! method_exists( $screen, 'add_help_tab' ) ) {
			return;
		}
		$help_url = admin_url( 'tools.php?page=glossary-tooltips-help' );
		$screen->add_help_tab( array(
			'id'      => 'glossary-tooltips-help',
			'title'   => __( 'Как пользоваться', 'glossary-tooltips' ),
			'content' => '<p><strong>' . __( 'Что такое глоссарий?', 'glossary-tooltips' ) . '</strong></p>
				<p>' . __( 'Глоссарий подсвечивает технические термины в статьях. При клике читатель видит пояснение, примеры и рекомендации.', 'glossary-tooltips' ) . '</p>
				<p><strong>' . __( 'Заполните все поля:', 'glossary-tooltips' ) . '</strong></p>
				<ul>
					<li><strong>' . __( 'Пояснение', 'glossary-tooltips' ) . '</strong> — ' . __( 'краткое объяснение термина', 'glossary-tooltips' ) . '</li>
					<li><strong>' . __( 'Примеры использования', 'glossary-tooltips' ) . '</strong> — ' . __( 'каждый пример с новой строки', 'glossary-tooltips' ) . '</li>
					<li><strong>' . __( 'В каких случаях использовать', 'glossary-tooltips' ) . '</strong> — ' . __( 'когда применять', 'glossary-tooltips' ) . '</li>
					<li><strong>' . __( 'Алиасы', 'glossary-tooltips' ) . '</strong> — ' . __( 'варианты через запятую (API, апи, эй-пи-ай)', 'glossary-tooltips' ) . '</li>
				</ul>
				<p><a href="' . esc_url( $help_url ) . '">' . __( 'Подробная справка', 'glossary-tooltips' ) . ' →</a></p>',
		) );
	}

	public function admin_enqueue_scripts( $hook ) {
		// Placeholder for future admin styles/scripts.
	}

	public static function on_activate() {
		Glossary_Tooltips::instance()->register_post_type();
		flush_rewrite_rules();
	}

	public static function on_deactivate() {
		flush_rewrite_rules();
		delete_transient( 'glossary_terms_content' );
	}
}

add_action( 'init', array( 'Glossary_Tooltips', 'instance' ) );
add_action( 'save_post_glossary_term', array( 'Glossary_Tooltips', 'clear_cache' ) );
register_activation_hook( __FILE__, array( 'Glossary_Tooltips', 'on_activate' ) );
register_deactivation_hook( __FILE__, array( 'Glossary_Tooltips', 'on_deactivate' ) );

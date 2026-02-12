<?php
/**
 * Plugin Name: DekanPro Post Analytics — Аналитика просмотров записей
 * Plugin URI: https://dekan.pro/
 * Description: Статистика просмотров: уникальные пользователи, глубина прокрутки, время на странице, устройства и платформы.
 * Version: 1.0.0
 * Author: DekanPro
 * Text Domain: post-analytics
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

// Защита от прямого доступа.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Константы плагина.
define( 'POST_ANALYTICS_VERSION', '1.0.0' );
define( 'POST_ANALYTICS_PATH', plugin_dir_path( __FILE__ ) );
define( 'POST_ANALYTICS_URL', plugin_dir_url( __FILE__ ) );
define( 'POST_ANALYTICS_TABLE', 'post_analytics' );

// Подключаем зависимости.
require_once POST_ANALYTICS_PATH . 'includes/class-post-analytics-db.php';
require_once POST_ANALYTICS_PATH . 'includes/class-post-analytics-rest.php';
require_once POST_ANALYTICS_PATH . 'includes/class-post-analytics-admin.php';
require_once POST_ANALYTICS_PATH . 'includes/class-post-analytics-frontend.php';

/**
 * Главный класс плагина Post Analytics.
 */
final class Post_Analytics {

	/** @var Post_Analytics|null Экземпляр класса (одиночка). */
	private static $instance = null;

	/**
	 * Возвращает единственный экземпляр класса.
	 *
	 * @return Post_Analytics
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Конструктор: регистрирует хуки и инициализирует компоненты.
	 */
	private function __construct() {
		// Хук активации — создание таблицы при первом включении плагина.
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		// Хук инициализации — загрузка переводов.
		add_action( 'init', array( $this, 'init' ) );
		// Подключение скрипта трекинга на фронтенде.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend' ) );

		// Инициализация REST API, админки и вывода просмотров на фронте.
		Post_Analytics_REST::instance();
		Post_Analytics_Admin::instance();
		Post_Analytics_Frontend::instance();
	}

	/**
	 * Инициализация: загрузка файлов переводов.
	 */
	public function init() {
		load_plugin_textdomain( 'post-analytics', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Вызывается при активации плагина — создаёт таблицу в БД.
	 */
	public function activate() {
		Post_Analytics_DB::create_table();
	}

	/**
	 * Подключает скрипт трекинга только на одиночных страницах записей.
	 */
	public function enqueue_frontend() {
		// Скрипт нужен только на странице просмотра одной записи.
		if ( ! is_singular( 'post' ) ) {
			return;
		}

		global $post;
		if ( ! $post || ! $post->ID ) {
			return;
		}

		// Подключаем JS-файл трекинга (в footer).
		wp_enqueue_script(
			'post-analytics',
			POST_ANALYTICS_URL . 'assets/js/post-analytics.js',
			array(),
			POST_ANALYTICS_VERSION,
			true
		);

		// Передаём в JS: ID поста, URL REST API и nonce для авторизации.
		wp_localize_script( 'post-analytics', 'postAnalytics', array(
			'postId'     => $post->ID,
			'restUrl'    => rest_url( 'post-analytics/v1/track' ),
			'nonce'      => wp_create_nonce( 'wp_rest' ),
		) );
	}
}

// Запуск плагина.
Post_Analytics::instance();

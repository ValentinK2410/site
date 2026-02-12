<?php
/**
 * Интеграция Post Analytics с темой: значок просмотров в карточках постов.
 *
 * @package Post_Analytics
 */

// Защита от прямого доступа.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Класс вывода просмотров на главной и в архивах.
 */
class Post_Analytics_Frontend {

	/** @var Post_Analytics_Frontend|null Экземпляр класса (одиночка). */
	private static $instance = null;

	/**
	 * Возвращает единственный экземпляр класса.
	 *
	 * @return Post_Analytics_Frontend
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Конструктор: подключает хуки к теме Dekanpro.
	 */
	private function __construct() {
		// Добавляем «просмотры» в элементы меты на главной и в архивах.
		add_filter( 'dekanpro_entry_meta_elements', array( $this, 'add_views_to_meta_elements' ) );
		// Регистрируем функцию вывода (тема вызывает dekanpro_entry_meta_views).
		add_action( 'init', array( $this, 'define_entry_meta_views' ), 20 );
		// Подключаем стили значка.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Модифицирует элементы меты: на одиночном посте — убираем автора и дату, добавляем просмотры.
	 *
	 * @param array $elements Массив имён элементов.
	 * @return array
	 */
	public function add_views_to_meta_elements( $elements ) {
		if ( ! is_array( $elements ) ) {
			return $elements;
		}
		if ( is_single() ) {
			// На странице поста: убираем автора и дату, оставляем только просмотры.
			$elements = array_diff( $elements, array( 'author', 'date' ) );
			$elements = array_values( $elements );
			$elements[] = 'views';
		} elseif ( is_home() || is_archive() || is_search() ) {
			// В списках (главная, категории, архивы): убираем дату, добавляем просмотры.
			$elements = array_diff( $elements, array( 'date' ) );
			$elements = array_values( $elements );
			$elements[] = 'views';
		}
		return $elements;
	}

	/**
	 * Определяет функцию dekanpro_entry_meta_views, которую вызывает тема.
	 */
	public function define_entry_meta_views() {
		if ( ! function_exists( 'dekanpro_entry_meta_views' ) ) {
			/**
			 * Выводит HTML с количеством просмотров (иконка + число).
			 */
			function dekanpro_entry_meta_views() {
				$post_id = get_the_ID();
				if ( ! $post_id || 'post' !== get_post_type( $post_id ) ) {
					return;
				}
				$count = class_exists( 'Post_Analytics_DB' ) ? Post_Analytics_DB::get_view_count( $post_id, 30 ) : 0;
				// Иконка глаза (SVG, Feather Icons style).
				$icon = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
				echo '<span class="post-views entry-meta-views">';
				echo wp_kses( $icon, array( 'svg' => array( 'xmlns' => true, 'width' => true, 'height' => true, 'viewbox' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'aria-hidden' => true ), 'path' => array( 'd' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ) ) );
				echo '<span class="views-count">' . esc_html( number_format_i18n( $count ) ) . '</span>';
				echo '</span>';
			}
		}
	}

	/**
	 * Подключает стили для значка просмотров (главная, архивы, одиночный пост).
	 */
	public function enqueue_styles() {
		if ( ! is_home() && ! is_archive() && ! is_search() && ! is_singular( 'post' ) ) {
			return;
		}
		wp_enqueue_style(
			'post-analytics-frontend',
			POST_ANALYTICS_URL . 'assets/css/post-analytics-frontend.css',
			array(),
			POST_ANALYTICS_VERSION
		);
	}
}

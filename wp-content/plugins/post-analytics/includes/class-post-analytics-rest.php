<?php
/**
 * REST API для приёма данных трекинга Post Analytics.
 *
 * @package Post_Analytics
 */

// Защита от прямого доступа.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Класс регистрации и обработки REST endpoint для трекинга просмотров.
 */
class Post_Analytics_REST {

	/** @var Post_Analytics_REST|null Экземпляр класса (одиночка). */
	private static $instance = null;

	/**
	 * Возвращает единственный экземпляр класса.
	 *
	 * @return Post_Analytics_REST
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Конструктор: подключает хук регистрации маршрутов.
	 */
	private function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Регистрирует маршрут POST /post-analytics/v1/track для приёма данных просмотра.
	 */
	public function register_routes() {
		register_rest_route( 'post-analytics/v1', '/track', array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => array( $this, 'handle_track' ),
			'permission_callback' => '__return_true',
			'args'                => array(
				'post_id'      => array(
					'required'          => true,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
				),
				'scroll_depth' => array(
					'default'           => 0,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
				),
				'time_seconds' => array(
					'default'           => 0,
					'type'              => 'integer',
					'sanitize_callback' => 'absint',
				),
				'device'       => array(
					'default' => 'desktop',
					'type'    => 'string',
				),
				'platform'     => array(
					'default' => '',
					'type'    => 'string',
				),
			),
		) );
	}

	/**
	 * Обрабатывает запрос на сохранение просмотра.
	 *
	 * @param WP_REST_Request $request Объект запроса с параметрами post_id, scroll_depth, time_seconds, device, platform.
	 * @return WP_REST_Response|WP_Error
	 */
	public function handle_track( $request ) {
		$post_id = $request->get_param( 'post_id' );
		$post    = get_post( $post_id );

		// Проверяем: запись должна существовать, быть типом post и опубликованной.
		if ( ! $post || 'post' !== $post->post_type || 'publish' !== $post->post_status ) {
			return new WP_Error( 'invalid_post', __( 'Invalid post.', 'post-analytics' ), array( 'status' => 400 ) );
		}

		$visitor_hash = $this->get_visitor_hash();
		$scroll_depth = $request->get_param( 'scroll_depth' );
		$time_seconds = $request->get_param( 'time_seconds' );
		$device       = sanitize_text_field( $request->get_param( 'device' ) );
		$platform     = sanitize_text_field( $request->get_param( 'platform' ) );

		// Допустимые значения устройства: mobile, tablet, desktop.
		if ( ! in_array( $device, array( 'mobile', 'tablet', 'desktop' ), true ) ) {
			$device = 'desktop';
		}

		$id = Post_Analytics_DB::insert_view( $post_id, $visitor_hash, $scroll_depth, $time_seconds, $device, $platform );

		if ( false === $id ) {
			return new WP_Error( 'db_error', __( 'Failed to save.', 'post-analytics' ), array( 'status' => 500 ) );
		}

		return new WP_REST_Response( array( 'success' => true ), 200 );
	}

	/**
	 * Формирует хеш посетителя по IP, User-Agent и дате (для подсчёта уникальных просмотров).
	 *
	 * @return string
	 */
	private function get_visitor_hash() {
		// IP: учитываем X-Forwarded-For если запрос идёт через прокси.
		$ip = '';
		if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
			$ip = explode( ',', $ip )[0];
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}
		// User-Agent — строка браузера/ОС.
		$ua = ! empty( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
		// Хеш: один посетитель в день = один уникальный просмотр.
		return hash( 'sha256', $ip . '|' . $ua . '|' . gmdate( 'Y-m-d' ) );
	}
}

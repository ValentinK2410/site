<?php
/**
 * Слой работы с базой данных для Post Analytics.
 *
 * @package Post_Analytics
 */

// Защита от прямого доступа.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Класс для работы с таблицей аналитики просмотров.
 */
class Post_Analytics_DB {

	/**
	 * Создаёт таблицу аналитики при активации плагина.
	 */
	public static function create_table() {
		global $wpdb;
		$table   = $wpdb->prefix . POST_ANALYTICS_TABLE;
		$charset = $wpdb->get_charset_collate();

		// Структура таблицы: ID, пост, хеш посетителя, глубина прокрутки, время, устройство, платформа.
		$sql = "CREATE TABLE IF NOT EXISTS $table (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			post_id bigint(20) unsigned NOT NULL,
			visitor_hash varchar(64) NOT NULL,
			scroll_depth tinyint(3) unsigned DEFAULT 0,
			time_seconds int(10) unsigned DEFAULT 0,
			device varchar(20) DEFAULT 'desktop',
			platform varchar(50) DEFAULT '',
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY post_id (post_id),
			KEY visitor_hash (visitor_hash),
			KEY created_at (created_at)
		) $charset;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		// Сохраняем версию структуры БД.
		update_option( 'post_analytics_db_version', POST_ANALYTICS_VERSION );
	}

	/**
	 * Возвращает полное имя таблицы.
	 *
	 * @return string
	 */
	public static function get_table() {
		global $wpdb;
		return $wpdb->prefix . POST_ANALYTICS_TABLE;
	}

	/**
	 * Сохраняет один просмотр в таблицу.
	 *
	 * @param int    $post_id     ID записи.
	 * @param string $visitor_hash Хеш посетителя (IP + User-Agent + дата).
	 * @param int    $scroll_depth Глубина прокрутки 0–100%.
	 * @param int    $time_seconds Время на странице в секундах.
	 * @param string $device      Тип устройства: mobile|tablet|desktop.
	 * @param string $platform    Платформа (iOS, Android, Windows и т.д.).
	 * @return int|false ID вставленной строки или false при ошибке.
	 */
	public static function insert_view( $post_id, $visitor_hash, $scroll_depth, $time_seconds, $device, $platform ) {
		global $wpdb;
		$table = self::get_table();

		// Валидация: scroll_depth 0–100%, device и platform обрезаем/очищаем.
		$result = $wpdb->insert(
			$table,
			array(
				'post_id'       => $post_id,
				'visitor_hash'  => $visitor_hash,
				'scroll_depth'  => min( 100, max( 0, (int) $scroll_depth ) ),
				'time_seconds'  => max( 0, (int) $time_seconds ),
				'device'        => sanitize_text_field( $device ),
				'platform'      => sanitize_text_field( substr( $platform, 0, 50 ) ),
			),
			array( '%d', '%s', '%d', '%d', '%s', '%s' )
		);

		return $result ? $wpdb->insert_id : false;
	}

	/**
	 * Возвращает агрегированную статистику по одной записи за указанный период.
	 *
	 * @param int $post_id ID записи.
	 * @param int $days    Количество дней для учёта (по умолчанию 30).
	 * @return array Массив с total_views, unique_views, avg_scroll, avg_time, max_scroll, devices, platforms.
	 */
	public static function get_post_stats( $post_id, $days = 30 ) {
		global $wpdb;
		$table = self::get_table();
		// Граница периода — N дней назад от текущего момента.
		$since = gmdate( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) );

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT visitor_hash, scroll_depth, time_seconds, device, platform
				FROM $table
				WHERE post_id = %d AND created_at >= %s",
				$post_id,
				$since
			),
			ARRAY_A
		);

		// Пустой результат — возвращаем нули.
		if ( empty( $rows ) ) {
			return array(
				'total_views'    => 0,
				'unique_views'   => 0,
				'avg_scroll'     => 0,
				'avg_time'       => 0,
				'max_scroll'     => 0,
				'devices'        => array(),
				'platforms'      => array(),
			);
		}

		// Агрегация: уникальные посетители, среднее и макс. прокрутка, время, разбивка по устройствам и платформам.
		$unique    = array_unique( wp_list_pluck( $rows, 'visitor_hash' ) );
		$scrolls   = array_filter( wp_list_pluck( $rows, 'scroll_depth' ) );
		$times     = array_filter( wp_list_pluck( $rows, 'time_seconds' ) );
		$devices   = array_count_values( array_column( $rows, 'device' ) );
		$platforms = array_filter( array_column( $rows, 'platform' ) );
		$platforms = array_count_values( $platforms );

		return array(
			'total_views'  => count( $rows ),
			'unique_views' => count( $unique ),
			'avg_scroll'   => ! empty( $scrolls ) ? round( array_sum( $scrolls ) / count( $scrolls ), 1 ) : 0,
			'avg_time'     => ! empty( $times ) ? round( array_sum( $times ) / count( $times ), 1 ) : 0,
			'max_scroll'   => ! empty( $scrolls ) ? max( $scrolls ) : 0,
			'devices'      => $devices,
			'platforms'    => $platforms,
		);
	}

	/**
	 * Возвращает список записей со статистикой, отсортированный по популярности.
	 *
	 * @param int $days   Период в днях.
	 * @param int $limit Максимальное количество записей в результате.
	 * @return array Массив записей с полями post_id, title, url, unique_views и др.
	 */
	public static function get_all_stats( $days = 30, $limit = 100 ) {
		global $wpdb;
		$table = self::get_table();
		$since = gmdate( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) );

		// Собираем ID постов, по которым есть данные за период (берём с запасом для фильтрации).
		$post_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT post_id FROM $table WHERE created_at >= %s ORDER BY post_id DESC LIMIT %d",
				$since,
				$limit * 2
			)
		);

		if ( empty( $post_ids ) ) {
			return array();
		}

		$results = array();
		foreach ( $post_ids as $post_id ) {
			$post = get_post( $post_id );
			// Пропускаем удалённые и записи не типа post.
			if ( ! $post || 'post' !== $post->post_type ) {
				continue;
			}
			$stats              = self::get_post_stats( $post_id, $days );
			$stats['post_id']   = $post_id;
			$stats['title']     = $post->post_title;
			$stats['url']       = get_permalink( $post_id );
			$results[]          = $stats;
		}

		// Сортировка по убыванию уникальных просмотров.
		usort( $results, function ( $a, $b ) {
			return $b['unique_views'] - $a['unique_views'];
		});

		return array_slice( $results, 0, $limit );
	}
}

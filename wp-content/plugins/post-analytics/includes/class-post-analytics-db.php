<?php
/**
 * Database layer for Post Analytics.
 *
 * @package Post_Analytics
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Post_Analytics_DB {

	/**
	 * Create the analytics table.
	 */
	public static function create_table() {
		global $wpdb;
		$table = $wpdb->prefix . POST_ANALYTICS_TABLE;
		$charset = $wpdb->get_charset_collate();

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

		update_option( 'post_analytics_db_version', POST_ANALYTICS_VERSION );
	}

	/**
	 * Get table name.
	 *
	 * @return string
	 */
	public static function get_table() {
		global $wpdb;
		return $wpdb->prefix . POST_ANALYTICS_TABLE;
	}

	/**
	 * Insert a view event.
	 *
	 * @param int    $post_id     Post ID.
	 * @param string $visitor_hash Visitor hash (IP + UA).
	 * @param int    $scroll_depth Scroll depth 0-100.
	 * @param int    $time_seconds Time on page in seconds.
	 * @param string $device      mobile|tablet|desktop.
	 * @param string $platform    Platform string.
	 * @return int|false
	 */
	public static function insert_view( $post_id, $visitor_hash, $scroll_depth, $time_seconds, $device, $platform ) {
		global $wpdb;
		$table = self::get_table();

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
	 * Get aggregated stats for a post.
	 *
	 * @param int $post_id Post ID.
	 * @param int $days    Number of days to include (default 30).
	 * @return array
	 */
	public static function get_post_stats( $post_id, $days = 30 ) {
		global $wpdb;
		$table = self::get_table();
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

		$unique = array_unique( wp_list_pluck( $rows, 'visitor_hash' ) );
		$scrolls = array_filter( wp_list_pluck( $rows, 'scroll_depth' ) );
		$times = array_filter( wp_list_pluck( $rows, 'time_seconds' ) );
		$devices = array_count_values( array_column( $rows, 'device' ) );
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
	 * Get all posts with stats, sorted by popularity.
	 *
	 * @param int $days   Days to include.
	 * @param int $limit Max posts.
	 * @return array
	 */
	public static function get_all_stats( $days = 30, $limit = 100 ) {
		global $wpdb;
		$table = self::get_table();
		$since = gmdate( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) );

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
			if ( ! $post || 'post' !== $post->post_type ) {
				continue;
			}
			$stats = self::get_post_stats( $post_id, $days );
			$stats['post_id'] = $post_id;
			$stats['title'] = $post->post_title;
			$stats['url'] = get_permalink( $post_id );
			$results[] = $stats;
		}

		usort( $results, function ( $a, $b ) {
			return $b['unique_views'] - $a['unique_views'];
		});

		return array_slice( $results, 0, $limit );
	}
}

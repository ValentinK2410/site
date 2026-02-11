<?php
/**
 * Plugin Name: Post Analytics — Аналитика просмотров записей
 * Plugin URI: https://dekan.pro/
 * Description: Статистика просмотров: уникальные пользователи, глубина прокрутки, время на странице, устройства и платформы.
 * Version: 1.0.0
 * Author: DekanPro
 * Text Domain: post-analytics
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'POST_ANALYTICS_VERSION', '1.0.0' );
define( 'POST_ANALYTICS_PATH', plugin_dir_path( __FILE__ ) );
define( 'POST_ANALYTICS_URL', plugin_dir_url( __FILE__ ) );
define( 'POST_ANALYTICS_TABLE', 'post_analytics' );

require_once POST_ANALYTICS_PATH . 'includes/class-post-analytics-db.php';
require_once POST_ANALYTICS_PATH . 'includes/class-post-analytics-rest.php';
require_once POST_ANALYTICS_PATH . 'includes/class-post-analytics-admin.php';

final class Post_Analytics {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend' ) );

		Post_Analytics_REST::instance();
		Post_Analytics_Admin::instance();
	}

	public function init() {
		load_plugin_textdomain( 'post-analytics', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	public function activate() {
		Post_Analytics_DB::create_table();
	}

	public function enqueue_frontend() {
		if ( ! is_singular( 'post' ) ) {
			return;
		}

		global $post;
		if ( ! $post || ! $post->ID ) {
			return;
		}

		wp_enqueue_script(
			'post-analytics',
			POST_ANALYTICS_URL . 'assets/js/post-analytics.js',
			array(),
			POST_ANALYTICS_VERSION,
			true
		);

		wp_localize_script( 'post-analytics', 'postAnalytics', array(
			'postId'     => $post->ID,
			'restUrl'    => rest_url( 'post-analytics/v1/track' ),
			'nonce'      => wp_create_nonce( 'wp_rest' ),
		) );
	}
}

Post_Analytics::instance();

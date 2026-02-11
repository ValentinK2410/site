<?php
/**
 * Админ-интерфейс для Post Analytics.
 *
 * @package Post_Analytics
 */

// Защита от прямого доступа.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Класс страницы статистики в админке WordPress.
 */
class Post_Analytics_Admin {

	/** @var Post_Analytics_Admin|null Экземпляр класса (одиночка). */
	private static $instance = null;

	/**
	 * Возвращает единственный экземпляр класса.
	 *
	 * @return Post_Analytics_Admin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Конструктор: добавляет пункт меню и обработчик создания таблицы.
	 */
	private function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		// Обработчик action для кнопки «Создать таблицу» (если плагин скопирован без активации).
		add_action( 'admin_post_post_analytics_create_table', array( $this, 'maybe_create_table' ) );
	}

	/**
	 * Добавляет пункт «Статистика записей» в боковое меню админки.
	 */
	public function add_menu() {
		add_menu_page(
			__( 'Статистика записей', 'post-analytics' ),
			__( 'Статистика записей', 'post-analytics' ),
			'edit_posts',
			'post-analytics',
			array( $this, 'render_page' ),
			'dashicons-chart-bar',
			25
		);
	}

	/**
	 * Создаёт таблицу по нажатию кнопки (проверка прав и nonce).
	 */
	public function maybe_create_table() {
		if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'post_analytics_create_table' ) ) {
			wp_die( esc_html__( 'Недостаточно прав.', 'post-analytics' ) );
		}
		Post_Analytics_DB::create_table();
		wp_safe_redirect( add_query_arg( 'pa_created', '1', admin_url( 'admin.php?page=post-analytics' ) ) );
		exit;
	}

	/**
	 * Выводит страницу статистики: таблица с просмотрами или форма настройки.
	 */
	public function render_page() {
		global $wpdb;
		$table        = Post_Analytics_DB::get_table();
		$table_exists = $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) === $table;

		// Если таблицы нет — показываем экран настройки с кнопкой создания.
		if ( ! $table_exists ) {
			$this->render_setup( $table );
			return;
		}

		// Период: 7, 14, 30 или 90 дней.
		$days = isset( $_GET['days'] ) ? absint( $_GET['days'] ) : 30;
		$days = in_array( $days, array( 7, 14, 30, 90 ), true ) ? $days : 30;

		$stats = Post_Analytics_DB::get_all_stats( $days, 50 );

		?>
		<div class="wrap post-analytics-wrap">
			<h1><?php esc_html_e( 'Статистика просмотров записей', 'post-analytics' ); ?></h1>

			<?php if ( isset( $_GET['pa_created'] ) ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Таблица создана.', 'post-analytics' ); ?></p></div>
			<?php endif; ?>

			<p>
				<?php esc_html_e( 'Период:', 'post-analytics' ); ?>
				<a href="<?php echo esc_url( add_query_arg( 'days', 7 ) ); ?>" <?php echo 7 === $days ? 'class="current"' : ''; ?>>7 <?php esc_html_e( 'дней', 'post-analytics' ); ?></a> |
				<a href="<?php echo esc_url( add_query_arg( 'days', 14 ) ); ?>" <?php echo 14 === $days ? 'class="current"' : ''; ?>>14</a> |
				<a href="<?php echo esc_url( add_query_arg( 'days', 30 ) ); ?>" <?php echo 30 === $days ? 'class="current"' : ''; ?>>30</a> |
				<a href="<?php echo esc_url( add_query_arg( 'days', 90 ) ); ?>" <?php echo 90 === $days ? 'class="current"' : ''; ?>>90</a>
			</p>

			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Запись', 'post-analytics' ); ?></th>
						<th><?php esc_html_e( 'Уникальные просмотры', 'post-analytics' ); ?></th>
						<th><?php esc_html_e( 'Всего просмотров', 'post-analytics' ); ?></th>
						<th><?php esc_html_e( 'Средняя глубина прокрутки %', 'post-analytics' ); ?></th>
						<th><?php esc_html_e( 'Макс. прокрутка %', 'post-analytics' ); ?></th>
						<th><?php esc_html_e( 'Среднее время (сек)', 'post-analytics' ); ?></th>
						<th><?php esc_html_e( 'Устройства', 'post-analytics' ); ?></th>
						<th><?php esc_html_e( 'Платформы', 'post-analytics' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $stats ) ) : ?>
						<tr><td colspan="8"><?php esc_html_e( 'Пока нет данных. Просматривайте записи на сайте.', 'post-analytics' ); ?></td></tr>
					<?php else : ?>
						<?php foreach ( $stats as $s ) : ?>
							<tr>
								<td>
									<strong><a href="<?php echo esc_url( $s['url'] ); ?>" target="_blank"><?php echo esc_html( $s['title'] ); ?></a></strong>
									<div class="row-actions"><a href="<?php echo esc_url( get_edit_post_link( $s['post_id'] ) ); ?>"><?php esc_html_e( 'Изменить', 'post-analytics' ); ?></a></div>
								</td>
								<td><?php echo esc_html( number_format_i18n( $s['unique_views'] ) ); ?></td>
								<td><?php echo esc_html( number_format_i18n( $s['total_views'] ) ); ?></td>
								<td><?php echo esc_html( $s['avg_scroll'] ); ?>%</td>
								<td><?php echo esc_html( $s['max_scroll'] ); ?>%</td>
								<td><?php echo esc_html( round( $s['avg_time'] ) ); ?></td>
								<td>
									<?php
									// Разбивка по устройствам: десктоп, мобильный, планшет.
									$dev   = $s['devices'];
									$parts = array();
									if ( ! empty( $dev['desktop'] ) ) $parts[] = 'Десктоп: ' . $dev['desktop'];
									if ( ! empty( $dev['mobile'] ) ) $parts[] = 'Мобильный: ' . $dev['mobile'];
									if ( ! empty( $dev['tablet'] ) ) $parts[] = 'Планшет: ' . $dev['tablet'];
									echo esc_html( implode( ', ', $parts ) ?: '—' );
									?>
								</td>
								<td>
									<?php
									// Разбивка по платформам: iOS, Android, Windows и т.д.
									$p    = $s['platforms'];
									$plat = array();
									foreach ( (array) $p as $name => $cnt ) {
										$plat[] = $name . ': ' . $cnt;
									}
									echo esc_html( $plat ? implode( ', ', $plat ) : '—' );
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Выводит экран настройки: сообщение и кнопка создания таблицы.
	 *
	 * @param string $table Имя таблицы (для отладки, в шаблоне не используется).
	 */
	private function render_setup( $table ) {
		$create_url = wp_nonce_url( admin_url( 'admin-post.php?action=post_analytics_create_table' ), 'post_analytics_create_table' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Статистика записей', 'post-analytics' ); ?></h1>
			<p><?php esc_html_e( 'Для работы плагина необходимо создать таблицу в базе данных.', 'post-analytics' ); ?></p>
			<p><a href="<?php echo esc_url( $create_url ); ?>" class="button button-primary"><?php esc_html_e( 'Создать таблицу', 'post-analytics' ); ?></a></p>
		</div>
		<?php
	}
}

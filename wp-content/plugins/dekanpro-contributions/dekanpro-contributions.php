<?php
/**
 * Plugin Name: DekanPro Contributions
 * Description: Позволяет пользователям добавлять стихи, картины, фотографии и статьи. Форма отправки на фронтенде.
 * Version: 1.0.0
 * Author: DekanPro
 * Text Domain: dekanpro-contributions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DEKANPRO_CONTRIBUTIONS_VERSION', '1.0.0' );
define( 'DEKANPRO_CONTRIBUTIONS_PATH', plugin_dir_path( __FILE__ ) );
define( 'DEKANPRO_CONTRIBUTIONS_URL', plugin_dir_url( __FILE__ ) );

/**
 * Плагин отправки материалов пользователями.
 */
final class Dekanpro_Contributions {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_shortcode( 'dekanpro_submit', array( $this, 'shortcode_form' ) );
	}

	public function init() {
		$this->handle_submission();
	}

	/**
	 * Обработка отправки формы.
	 */
	private function handle_submission() {
		if ( empty( $_POST['dekanpro_submit_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dekanpro_submit_nonce'] ) ), 'dekanpro_submit_post' ) ) {
			return;
		}
		if ( ! is_user_logged_in() ) {
			return;
		}

		$title   = isset( $_POST['dekanpro_title'] ) ? sanitize_text_field( wp_unslash( $_POST['dekanpro_title'] ) ) : '';
		$content = isset( $_POST['dekanpro_content'] ) ? wp_kses_post( wp_unslash( $_POST['dekanpro_content'] ) ) : '';
		$category = isset( $_POST['dekanpro_category'] ) ? absint( $_POST['dekanpro_category'] ) : 0;

		if ( empty( $title ) || empty( $content ) ) {
			return;
		}

		$post_status = 'pending';
		$post_data   = array(
			'post_title'   => $title,
			'post_content' => $content,
			'post_status'  => $post_status,
			'post_type'    => 'post',
			'post_author'  => get_current_user_id(),
		);

		if ( $category > 0 ) {
			$post_data['post_category'] = array( $category );
		}

		$post_id = wp_insert_post( $post_data );

		if ( is_wp_error( $post_id ) ) {
			return;
		}

		// Загрузка изображения.
		if ( ! empty( $_FILES['dekanpro_image']['name'] ) && ! $_FILES['dekanpro_image']['error'] ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';

			$file = $_FILES['dekanpro_image'];
			$allowed = array( 'image/jpeg', 'image/png', 'image/gif', 'image/webp' );
			$filetype = wp_check_filetype( $file['name'] );

			if ( in_array( $file['type'], $allowed, true ) ) {
				$upload = media_handle_upload( 'dekanpro_image', $post_id );
				if ( ! is_wp_error( $upload ) ) {
					set_post_thumbnail( $post_id, $upload );
				}
			}
		}

		wp_safe_redirect( add_query_arg( 'submitted', '1', wp_get_referer() ?: home_url( '/' ) ) );
		exit;
	}

	/**
	 * Шорткод формы отправки.
	 */
	public function shortcode_form() {
		if ( ! is_user_logged_in() ) {
			return $this->render_login_prompt();
		}

		$message = '';
		if ( isset( $_GET['submitted'] ) && '1' === $_GET['submitted'] ) {
			$message = '<p class="dekanpro-contrib-success">' . esc_html__( 'Спасибо! Ваш материал отправлен на модерацию и появится на сайте после проверки.', 'dekanpro-contributions' ) . '</p>';
		}

		$categories = get_terms( array(
			'taxonomy'   => 'category',
			'hide_empty' => false,
			'exclude'    => get_option( 'default_category' ),
		) );

		ob_start();
		?>
		<div class="dekanpro-contrib-form-wrapper">
			<?php echo $message; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<form class="dekanpro-contrib-form" method="post" enctype="multipart/form-data" action="">
				<?php wp_nonce_field( 'dekanpro_submit_post', 'dekanpro_submit_nonce' ); ?>

				<p class="form-row">
					<label for="dekanpro_title"><?php esc_html_e( 'Название', 'dekanpro-contributions' ); ?> <span class="required">*</span></label>
					<input type="text" id="dekanpro_title" name="dekanpro_title" required maxlength="200" value="">
				</p>

				<p class="form-row">
					<label for="dekanpro_category"><?php esc_html_e( 'Раздел', 'dekanpro-contributions' ); ?></label>
					<select id="dekanpro_category" name="dekanpro_category">
						<option value=""><?php esc_html_e( '— Выберите раздел —', 'dekanpro-contributions' ); ?></option>
						<?php
						foreach ( $categories as $cat ) {
							printf(
								'<option value="%d">%s</option>',
								(int) $cat->term_id,
								esc_html( $cat->name )
							);
						}
						?>
					</select>
				</p>

				<p class="form-row">
					<label for="dekanpro_content"><?php esc_html_e( 'Текст (стихотворение, описание, статья)', 'dekanpro-contributions' ); ?> <span class="required">*</span></label>
					<textarea id="dekanpro_content" name="dekanpro_content" rows="12" required></textarea>
				</p>

				<p class="form-row">
					<label for="dekanpro_image"><?php esc_html_e( 'Изображение (картина, фото)', 'dekanpro-contributions' ); ?></label>
					<input type="file" id="dekanpro_image" name="dekanpro_image" accept="image/jpeg,image/png,image/gif,image/webp">
					<span class="form-hint"><?php esc_html_e( 'JPG, PNG, GIF или WebP. Макс. 5 МБ.', 'dekanpro-contributions' ); ?></span>
				</p>

				<p class="form-row form-submit">
					<button type="submit" class="dekanpro-btn primary-button"><?php esc_html_e( 'Отправить на модерацию', 'dekanpro-contributions' ); ?></button>
				</p>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Блок «Войдите, чтобы добавить материал».
	 */
	private function render_login_prompt() {
		$login_url = wp_login_url( get_permalink() );
		$reg_url   = wp_registration_url();
		ob_start();
		?>
		<div class="dekanpro-contrib-login-prompt">
			<p><?php esc_html_e( 'Чтобы добавить стихотворение, картину, фотографию или статью — войдите или зарегистрируйтесь.', 'dekanpro-contributions' ); ?></p>
			<p class="contrib-actions">
				<a href="<?php echo esc_url( $login_url ); ?>" class="dekanpro-btn primary-button"><?php esc_html_e( 'Войти', 'dekanpro-contributions' ); ?></a>
				<?php if ( get_option( 'users_can_register' ) ) : ?>
					<a href="<?php echo esc_url( $reg_url ); ?>" class="dekanpro-btn"><?php esc_html_e( 'Регистрация', 'dekanpro-contributions' ); ?></a>
				<?php endif; ?>
			</p>
		</div>
		<?php
		return ob_get_clean();
	}

	public function enqueue_scripts() {
		global $post;
		if ( ! $post || ! is_singular() || ! has_shortcode( $post->post_content, 'dekanpro_submit' ) ) {
			return;
		}
		wp_enqueue_style(
			'dekanpro-contributions',
			DEKANPRO_CONTRIBUTIONS_URL . 'assets/contributions.css',
			array(),
			DEKANPRO_CONTRIBUTIONS_VERSION
		);
	}
}

Dekanpro_Contributions::instance();

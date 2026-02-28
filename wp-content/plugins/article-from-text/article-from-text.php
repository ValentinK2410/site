<?php
/**
 * Plugin Name: Article From Text
 * Description: Вставьте текст — плагин разберёт его на цитаты, оформит заголовки и создаст новую статью.
 * Version: 1.0.0
 * Author: DekanPro
 * Text Domain: article-from-text
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AFT_VERSION', '1.0.0' );
define( 'AFT_PATH', plugin_dir_path( __FILE__ ) );
define( 'AFT_URL', plugin_dir_url( __FILE__ ) );

final class Article_From_Text {

	private static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'handle_form_submit' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'wp_ajax_aft_preview', array( $this, 'ajax_preview' ) );
	}

	public function add_admin_menu() {
		add_posts_page(
			__( 'Статья из текста', 'article-from-text' ),
			__( 'Статья из текста', 'article-from-text' ),
			'edit_posts',
			'article-from-text',
			array( $this, 'render_admin_page' )
		);
	}

	public function enqueue_admin_assets( $hook ) {
		if ( strpos( $hook, 'article-from-text' ) === false ) {
			return;
		}
		wp_enqueue_style( 'aft-admin', AFT_URL . 'assets/admin.css', array(), AFT_VERSION );
		wp_enqueue_script( 'aft-admin', AFT_URL . 'assets/admin.js', array( 'jquery' ), AFT_VERSION, true );
		wp_localize_script( 'aft-admin', 'aftData', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'aft_preview' ),
		) );
	}

	public function handle_form_submit() {
		if ( empty( $_POST['aft_create_post_nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['aft_create_post_nonce'] ) ), 'aft_create_post' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		$raw_text = isset( $_POST['aft_raw_text'] ) ? wp_unslash( $_POST['aft_raw_text'] ) : '';
		$title    = isset( $_POST['aft_title'] ) ? sanitize_text_field( wp_unslash( $_POST['aft_title'] ) ) : '';

		if ( empty( trim( $raw_text ) ) ) {
			wp_safe_redirect( add_query_arg( array(
				'page'    => 'article-from-text',
				'aft_err' => 'empty',
			), admin_url( 'edit.php' ) ) );
			exit;
		}

		$content = $this->process_text( $raw_text );
		$title   = $title ?: $this->extract_title( $raw_text );

		$post_id = wp_insert_post( array(
			'post_title'   => $title,
			'post_content' => $content,
			'post_status'  => 'draft',
			'post_type'    => 'post',
			'post_author'  => get_current_user_id(),
		) );

		if ( is_wp_error( $post_id ) ) {
			wp_safe_redirect( add_query_arg( array(
				'page'    => 'article-from-text',
				'aft_err' => 'create',
			), admin_url( 'edit.php' ) ) );
			exit;
		}

		wp_safe_redirect( add_query_arg( array(
			'post'   => $post_id,
			'action' => 'edit',
			'aft_ok' => '1',
		), admin_url( 'post.php' ) ) );
		exit;
	}

	public function ajax_preview() {
		check_ajax_referer( 'aft_preview', 'nonce' );
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => 'Forbidden' ) );
		}
		$raw = isset( $_POST['text'] ) ? wp_unslash( $_POST['text'] ) : '';
		$html = $this->process_text( $raw );
		wp_send_json_success( array( 'html' => $html ) );
	}

	private function extract_title( $text ) {
		$lines = preg_split( '/\r\n|\r|\n/', trim( $text ), 2 );
		$first = trim( $lines[0] ?? '' );
		// Убираем markdown-заголовки для заголовка поста
		$first = preg_replace( '/^#+\s*/', '', $first );
		$first = preg_replace( '/^["\x{00AB}]\s*(.+?)\s*["\x{00BB}]$/u', '$1', $first );
		return $first ?: __( 'Без названия', 'article-from-text' );
	}

	/**
	 * Обработка текста: разбор на цитаты, заголовки, абзацы.
	 */
	private function process_text( $raw ) {
		$text  = trim( $raw );
		$lines = preg_split( '/\r\n|\r|\n/', $text );

		$blocks   = array();
		$current  = array();
		$in_quote = false;

		foreach ( $lines as $i => $line ) {
			$trimmed = trim( $line );

			// Пустая строка — конец блока
			if ( $trimmed === '' ) {
				if ( ! empty( $current ) ) {
					$blocks[] = $this->classify_block( $current );
					$current  = array();
				}
				$in_quote = false;
				continue;
			}

			// Markdown-заголовки
			if ( preg_match( '/^(#{1,6})\s+(.+)$/', $trimmed, $m ) ) {
				if ( ! empty( $current ) ) {
					$blocks[] = $this->classify_block( $current );
					$current  = array();
				}
				$level = strlen( $m[1] );
				$blocks[] = array(
					'type'  => 'heading',
					'level' => min( $level + 1, 3 ),
					'text'  => trim( $m[2] ),
				);
				continue;
			}

			// Цитата: строка целиком в кавычках «...» или "..."
			$is_full_quote = preg_match( '/^["\x{00AB}\x{201C}]\s*(.+?)\s*["\x{00BB}\x{201D}]$/us', $trimmed ) ||
				preg_match( '/^["\x{201C}](.+?)["\x{201D}]$/us', $trimmed );

			if ( $is_full_quote || $this->is_quoted_line( $trimmed ) ) {
				if ( ! empty( $current ) && empty( $current['quote'] ) ) {
					$blocks[] = $this->classify_block( $current );
					$current  = array();
				}
				$quote_text = $this->strip_quotes( $trimmed );
				if ( ! empty( $current['quote'] ) ) {
					$current['quote'][] = $quote_text;
				} else {
					$current = array( 'type' => 'blockquote', 'quote' => array( $quote_text ) );
				}
				$in_quote = true;
				continue;
			}

			// Продолжение цитаты (многострочная)
			if ( $in_quote && ! empty( $current['quote'] ) ) {
				$current['quote'][] = $trimmed;
				continue;
			}

			// Нумерованный заголовок: "1. Текст" или "1) Текст" — короткая строка
			if ( preg_match( '/^\d+[.\)]\s+(.+)$/', $trimmed, $m ) && mb_strlen( $trimmed ) < 80 ) {
				if ( ! empty( $current ) ) {
					$blocks[] = $this->classify_block( $current );
					$current  = array();
				}
				$blocks[] = array( 'type' => 'heading', 'level' => 2, 'text' => trim( $m[1] ) );
				continue;
			}

			// Короткая строка без точки в конце — возможный заголовок раздела (10–70 символов)
			if ( mb_strlen( $trimmed ) >= 10 && mb_strlen( $trimmed ) < 70 && ! preg_match( '/[.!?;]$/u', $trimmed ) ) {
				$next = trim( $lines[ $i + 1 ] ?? '' );
				// Если следующая строка пустая или длинный абзац с заглавной — вероятно заголовок
				if ( $next === '' || ( mb_strlen( $next ) > 40 && preg_match( '/^[А-ЯA-Z]/u', $next ) ) ) {
					if ( ! empty( $current ) ) {
						$blocks[] = $this->classify_block( $current );
						$current  = array();
					}
					$blocks[] = array( 'type' => 'heading', 'level' => 3, 'text' => $trimmed );
					continue;
				}
			}

			// Обычный абзац
			if ( ! empty( $current['quote'] ) ) {
				$blocks[] = $this->classify_block( $current );
				$current  = array();
				$in_quote = false;
			}
			$current[] = $trimmed;
		}

		if ( ! empty( $current ) ) {
			$blocks[] = $this->classify_block( $current );
		}

		return $this->blocks_to_html( $blocks );
	}

	private function is_quoted_line( $line ) {
		return preg_match( '/^["\x{00AB}\x{201C}].*["\x{00BB}\x{201D}]/us', $line ) ||
			preg_match( '/^".+?"$/u', $line );
	}

	private function strip_quotes( $line ) {
		return trim( preg_replace( '/^["\x{00AB}\x{201C}\x{2018}]\s*|\s*["\x{00BB}\x{201D}\x{2019}]$/u', '', $line ) );
	}

	private function classify_block( $block ) {
		if ( isset( $block['type'] ) && $block['type'] === 'blockquote' ) {
			return $block;
		}
		$text = is_array( $block ) ? implode( "\n", $block ) : (string) $block;
		return array( 'type' => 'paragraph', 'text' => $text );
	}

	private function blocks_to_html( $blocks ) {
		$out = array();
		foreach ( $blocks as $b ) {
			if ( $b['type'] === 'heading' ) {
				$tag = 'h' . (int) ( $b['level'] ?? 2 );
				$out[] = '<' . $tag . '>' . esc_html( $b['text'] ) . '</' . $tag . '>';
			} elseif ( $b['type'] === 'blockquote' ) {
				$lines = array_map( 'esc_html', (array) $b['quote'] );
				$out[] = '<blockquote><p>' . implode( '</p><p>', $lines ) . '</p></blockquote>';
			} else {
				$text = wp_kses_post( nl2br( $b['text'] ) );
				if ( strpos( $text, '<p>' ) !== 0 && strpos( $text, '<blockquote>' ) !== 0 ) {
					$text = '<p>' . $text . '</p>';
				}
				$out[] = $text;
			}
		}
		return implode( "\n\n", $out );
	}

	public function render_admin_page() {
		$err = isset( $_GET['aft_err'] ) ? sanitize_text_field( wp_unslash( $_GET['aft_err'] ) ) : '';
		?>
		<div class="wrap aft-wrap">
			<h1><?php esc_html_e( 'Статья из текста', 'article-from-text' ); ?></h1>
			<p class="aft-description">
				<?php esc_html_e( 'Вставьте сырой текст. Плагин выделит цитаты в кавычках, оформит заголовки и создаст черновик статьи.', 'article-from-text' ); ?>
			</p>

			<?php if ( $err === 'empty' ) : ?>
				<div class="notice notice-error"><p><?php esc_html_e( 'Введите текст.', 'article-from-text' ); ?></p></div>
			<?php elseif ( $err === 'create' ) : ?>
				<div class="notice notice-error"><p><?php esc_html_e( 'Не удалось создать запись.', 'article-from-text' ); ?></p></div>
			<?php endif; ?>

			<form method="post" action="" id="aft-form">
				<?php wp_nonce_field( 'aft_create_post', 'aft_create_post_nonce' ); ?>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="aft_title"><?php esc_html_e( 'Заголовок статьи', 'article-from-text' ); ?></label>
						</th>
						<td>
							<input type="text" name="aft_title" id="aft_title" class="large-text" placeholder="<?php esc_attr_e( 'Оставьте пустым — будет взят из первой строки', 'article-from-text' ); ?>">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="aft_raw_text"><?php esc_html_e( 'Текст', 'article-from-text' ); ?></label>
						</th>
						<td>
							<textarea name="aft_raw_text" id="aft_raw_text" class="large-text" rows="20" placeholder="<?php esc_attr_e( 'Вставьте текст сюда...', 'article-from-text' ); ?>"></textarea>
							<p class="description">
								<?php esc_html_e( 'Поддерживаются: Markdown-заголовки (# ## ###), цитаты в кавычках «…», нумерованные заголовки (1. Текст). Короткие строки без точки в конце — как подзаголовки.', 'article-from-text' ); ?>
							</p>
						</td>
					</tr>
				</table>
				<p class="submit">
					<button type="button" id="aft-preview-btn" class="button"><?php esc_html_e( 'Предпросмотр', 'article-from-text' ); ?></button>
					<button type="submit" name="aft_submit" class="button button-primary"><?php esc_html_e( 'Создать статью', 'article-from-text' ); ?></button>
				</p>
			</form>

			<div id="aft-preview" class="aft-preview-area" style="display:none;">
				<h2><?php esc_html_e( 'Предпросмотр', 'article-from-text' ); ?></h2>
				<div id="aft-preview-content" class="aft-preview-content"></div>
			</div>
		</div>
		<?php
	}
}

function article_from_text() {
	return Article_From_Text::instance();
}

add_action( 'plugins_loaded', 'article_from_text' );

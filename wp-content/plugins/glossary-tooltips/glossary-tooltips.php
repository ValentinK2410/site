<?php
/**
 * Plugin Name: Glossary Tooltips
 * Plugin URI: https://dekan.pro/
 * Description: Справочная система: технические термины в статьях раскрываются по клику с пояснениями и примерами.
 * Version: 1.0.0
 * Author: DekanPro
 * Text Domain: glossary-tooltips
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'GLOSSARY_TOOLTIPS_VERSION', '1.0.0' );
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
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_glossary_term', array( $this, 'save_term_meta' ), 10, 2 );

		add_filter( 'the_content', array( $this, 'filter_content' ), 20 );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
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
			'show_in_menu'        => true,
			'query_var'           => false,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => 25,
			'menu_icon'           => 'dashicons-book-alt',
			'supports'            => array( 'title' ),
		);

		register_post_type( 'glossary_term', $args );
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
		if ( ! is_singular( 'post' ) ) {
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
		if ( ! is_singular( 'post' ) ) {
			return;
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
			array( 'jquery' ),
			GLOSSARY_TOOLTIPS_VERSION,
			true
		);
	}

	public function admin_enqueue_scripts( $hook ) {
		if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
			return;
		}
		global $post;
		if ( ! $post || 'glossary_term' !== $post->post_type ) {
			return;
		}
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

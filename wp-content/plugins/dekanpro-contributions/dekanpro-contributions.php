<?php
/**
 * Plugin Name: DekanPro Contributions
 * Description: Позволяет любым посетителям (без регистрации) добавлять стихи, картины, фотографии и статьи. Все материалы проходят модерацию.
 * Version: 2.0.0
 * Author: DekanPro
 * Text Domain: dekanpro-contributions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DEKANPRO_CONTRIBUTIONS_VERSION', '2.0.0' );
define( 'DEKANPRO_CONTRIBUTIONS_PATH', plugin_dir_path( __FILE__ ) );
define( 'DEKANPRO_CONTRIBUTIONS_URL', plugin_dir_url( __FILE__ ) );

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
		add_filter( 'the_content', array( $this, 'append_location_meta' ), 15 );
		add_filter( 'dekanpro_author_display_name', array( $this, 'filter_author_display_name' ), 10, 3 );
		add_filter( 'the_author', array( $this, 'filter_author_name' ), 10 );
		add_action( 'wp_footer', array( $this, 'footer_script' ) );
	}

	public function init() {
		$this->handle_submission();
	}

	private function get_allowed_category_slugs() {
		return array( 'zhivopis', 'poeziya', 'tvorchestvo', 'fotografii', 'stati' );
	}

	private function handle_submission() {
		if ( empty( $_POST['dekanpro_submit_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['dekanpro_submit_nonce'] ) ), 'dekanpro_submit_post' ) ) {
			return;
		}

		// Honeypot anti-spam
		if ( ! empty( $_POST['dekanpro_website_url'] ) ) {
			return;
		}

		// Rate limiting via transient (1 submission per 60 seconds per IP)
		$ip_hash = md5( $_SERVER['REMOTE_ADDR'] ?? 'unknown' );
		$transient_key = 'dpcontrib_' . $ip_hash;
		if ( get_transient( $transient_key ) ) {
			return;
		}

		$title       = isset( $_POST['dekanpro_title'] ) ? sanitize_text_field( wp_unslash( $_POST['dekanpro_title'] ) ) : '';
		$content     = isset( $_POST['dekanpro_content'] ) ? wp_kses_post( wp_unslash( $_POST['dekanpro_content'] ) ) : '';
		$category    = isset( $_POST['dekanpro_category'] ) ? absint( $_POST['dekanpro_category'] ) : 0;
		$author_name = isset( $_POST['dekanpro_author_name'] ) ? sanitize_text_field( wp_unslash( $_POST['dekanpro_author_name'] ) ) : '';
		$author_email = isset( $_POST['dekanpro_author_email'] ) ? sanitize_email( wp_unslash( $_POST['dekanpro_author_email'] ) ) : '';
		$region      = isset( $_POST['dekanpro_region'] ) ? sanitize_text_field( wp_unslash( $_POST['dekanpro_region'] ) ) : '';
		$region_other = isset( $_POST['dekanpro_region_other'] ) ? sanitize_text_field( wp_unslash( $_POST['dekanpro_region_other'] ) ) : '';
		$city        = isset( $_POST['dekanpro_city'] ) ? sanitize_text_field( wp_unslash( $_POST['dekanpro_city'] ) ) : '';
		$settlement  = isset( $_POST['dekanpro_settlement'] ) ? sanitize_text_field( wp_unslash( $_POST['dekanpro_settlement'] ) ) : '';

		if ( empty( $title ) || empty( $content ) ) {
			return;
		}

		if ( empty( $author_name ) ) {
			$author_name = __( 'Гость', 'dekanpro-contributions' );
		}

		$post_author = is_user_logged_in() ? get_current_user_id() : 1;

		$post_data = array(
			'post_title'   => $title,
			'post_content' => $content,
			'post_status'  => 'pending',
			'post_type'    => 'post',
			'post_author'  => $post_author,
		);

		$allowed_slugs = $this->get_allowed_category_slugs();
		if ( $category > 0 ) {
			$term = get_term( $category, 'category' );
			if ( $term && ! is_wp_error( $term ) && in_array( $term->slug, $allowed_slugs, true ) ) {
				$post_data['post_category'] = array( $category );
			}
		}

		$post_id = wp_insert_post( $post_data );

		if ( is_wp_error( $post_id ) ) {
			return;
		}

		// Rate limit: 60 seconds
		set_transient( $transient_key, 1, 60 );

		// Upload image
		if ( ! empty( $_FILES['dekanpro_image']['name'] ) && ! $_FILES['dekanpro_image']['error'] ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';

			$file    = $_FILES['dekanpro_image'];
			$allowed = array( 'image/jpeg', 'image/png', 'image/gif', 'image/webp' );

			if ( in_array( $file['type'], $allowed, true ) && $file['size'] <= 5 * 1024 * 1024 ) {
				$upload = media_handle_upload( 'dekanpro_image', $post_id );
				if ( ! is_wp_error( $upload ) ) {
					set_post_thumbnail( $post_id, $upload );
				}
			}
		}

		// Save location
		$region_display = '';
		if ( 'other' === $region && $region_other ) {
			$region_display = $region_other;
		} elseif ( $region ) {
			$regions = $this->get_regions_list();
			$region_display = isset( $regions[ $region ] ) ? $regions[ $region ] : $region;
		}
		if ( $region_display ) {
			update_post_meta( $post_id, 'dekanpro_region', $region_display );
		}
		if ( $city ) {
			update_post_meta( $post_id, 'dekanpro_city', $city );
		}
		if ( $settlement ) {
			update_post_meta( $post_id, 'dekanpro_settlement', $settlement );
		}
		if ( $author_name ) {
			update_post_meta( $post_id, 'dekanpro_contributor_name', $author_name );
		}
		if ( $author_email ) {
			update_post_meta( $post_id, 'dekanpro_contributor_email', $author_email );
		}

		// Save guest IP for moderation reference
		if ( ! is_user_logged_in() ) {
			update_post_meta( $post_id, 'dekanpro_guest_ip', sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' ) );
		}

		wp_safe_redirect( add_query_arg( 'submitted', '1', wp_get_referer() ?: home_url( '/' ) ) );
		exit;
	}

	public function shortcode_form() {
		$message = '';
		if ( isset( $_GET['submitted'] ) && '1' === $_GET['submitted'] ) {
			$message = '<div class="dekanpro-contrib-success"><p>' . esc_html__( 'Спасибо! Ваш материал отправлен на модерацию и появится на сайте после проверки.', 'dekanpro-contributions' ) . '</p></div>';
		}

		$allowed_slugs = $this->get_allowed_category_slugs();
		$categories    = get_terms( array(
			'taxonomy'   => 'category',
			'hide_empty' => false,
			'slug__in'   => $allowed_slugs,
		) );
		usort( $categories, function( $a, $b ) use ( $allowed_slugs ) {
			return array_search( $a->slug, $allowed_slugs, true ) - array_search( $b->slug, $allowed_slugs, true );
		} );

		$is_logged_in = is_user_logged_in();
		$current_user = $is_logged_in ? wp_get_current_user() : null;

		ob_start();
		?>
		<div class="dekanpro-contrib-form-wrapper">
			<?php echo $message; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

			<div class="dekanpro-contrib-intro">
				<p><?php esc_html_e( 'Поделитесь своим творчеством — стихотворением, картиной, фотографией или статьёй. Регистрация не требуется. Все материалы проходят модерацию перед публикацией.', 'dekanpro-contributions' ); ?></p>
			</div>

			<form class="dekanpro-contrib-form" method="post" enctype="multipart/form-data" action="">
				<?php wp_nonce_field( 'dekanpro_submit_post', 'dekanpro_submit_nonce' ); ?>

				<!-- Honeypot -->
				<div style="position:absolute;left:-9999px;top:-9999px;" aria-hidden="true">
					<input type="text" name="dekanpro_website_url" value="" tabindex="-1" autocomplete="off">
				</div>

				<p class="form-row">
					<label for="dekanpro_author_name"><?php esc_html_e( 'Ваше имя', 'dekanpro-contributions' ); ?> <span class="required">*</span></label>
					<input type="text" id="dekanpro_author_name" name="dekanpro_author_name" required maxlength="150"
						value="<?php echo $is_logged_in && $current_user ? esc_attr( $current_user->display_name ) : ''; ?>"
						placeholder="<?php esc_attr_e( 'Имя или псевдоним', 'dekanpro-contributions' ); ?>">
				</p>

				<?php if ( ! $is_logged_in ) : ?>
				<p class="form-row">
					<label for="dekanpro_author_email"><?php esc_html_e( 'Email', 'dekanpro-contributions' ); ?></label>
					<input type="email" id="dekanpro_author_email" name="dekanpro_author_email" maxlength="200"
						placeholder="<?php esc_attr_e( 'Для уведомления о публикации (необязательно)', 'dekanpro-contributions' ); ?>">
				</p>
				<?php endif; ?>

				<p class="form-row">
					<label for="dekanpro_title"><?php esc_html_e( 'Название', 'dekanpro-contributions' ); ?> <span class="required">*</span></label>
					<input type="text" id="dekanpro_title" name="dekanpro_title" required maxlength="200" value="">
				</p>

				<p class="form-row">
					<label for="dekanpro_category"><?php esc_html_e( 'Раздел', 'dekanpro-contributions' ); ?></label>
					<select id="dekanpro_category" name="dekanpro_category">
						<option value=""><?php esc_html_e( '— Выберите раздел —', 'dekanpro-contributions' ); ?></option>
						<?php foreach ( $categories as $cat ) : ?>
							<option value="<?php echo (int) $cat->term_id; ?>"><?php echo esc_html( $cat->name ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>

				<div class="form-row-group form-row-group-location">
					<label class="group-label"><?php esc_html_e( 'Местоположение (по желанию)', 'dekanpro-contributions' ); ?></label>
					<p class="form-row">
						<label for="dekanpro_region"><?php esc_html_e( 'Область / регион', 'dekanpro-contributions' ); ?></label>
						<select id="dekanpro_region" name="dekanpro_region">
							<option value=""><?php esc_html_e( '— Не указывать —', 'dekanpro-contributions' ); ?></option>
							<?php foreach ( $this->get_regions_list() as $key => $label ) : ?>
								<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<p class="form-row dekanpro-region-other-row" style="display:none;">
						<label for="dekanpro_region_other"><?php esc_html_e( 'Укажите область', 'dekanpro-contributions' ); ?></label>
						<input type="text" id="dekanpro_region_other" name="dekanpro_region_other" maxlength="100">
					</p>
					<p class="form-row">
						<label for="dekanpro_city"><?php esc_html_e( 'Город', 'dekanpro-contributions' ); ?></label>
						<input type="text" id="dekanpro_city" name="dekanpro_city" maxlength="100" placeholder="<?php esc_attr_e( 'Например: Москва', 'dekanpro-contributions' ); ?>">
					</p>
					<p class="form-row">
						<label for="dekanpro_settlement"><?php esc_html_e( 'Посёлок / населённый пункт', 'dekanpro-contributions' ); ?></label>
						<input type="text" id="dekanpro_settlement" name="dekanpro_settlement" maxlength="100" placeholder="<?php esc_attr_e( 'Например: Красная Поляна', 'dekanpro-contributions' ); ?>">
					</p>
				</div>

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

				<p class="form-row form-notice">
					<small><?php esc_html_e( 'Отправляя материал, вы соглашаетесь с тем, что он будет опубликован после проверки модератором.', 'dekanpro-contributions' ); ?></small>
				</p>
			</form>
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

	public function footer_script() {
		global $post;
		if ( ! $post || ! is_singular() || ! has_shortcode( $post->post_content, 'dekanpro_submit' ) ) {
			return;
		}
		?>
		<script>
		document.addEventListener('DOMContentLoaded', function() {
			var sel = document.getElementById('dekanpro_region');
			var row = document.querySelector('.dekanpro-region-other-row');
			if (sel && row) {
				function toggle() { row.style.display = sel.value === 'other' ? 'block' : 'none'; }
				sel.addEventListener('change', toggle);
				toggle();
			}
		});
		</script>
		<?php
	}

	private function get_regions_list() {
		return array(
			'moscow'        => 'Москва',
			'spb'           => 'Санкт-Петербург',
			'mo'            => 'Московская область',
			'lo'            => 'Ленинградская область',
			'kr'            => 'Краснодарский край',
			'ro'            => 'Ростовская область',
			'so'            => 'Свердловская область',
			'novosibirsk'   => 'Новосибирская область',
			'nizhny'        => 'Нижегородская область',
			'samara'        => 'Самарская область',
			'chelyabinsk'   => 'Челябинская область',
			'bashkortostan' => 'Республика Башкортостан',
			'tatarstan'     => 'Республика Татарстан',
			'krasnoyarsk'   => 'Красноярский край',
			'perm'          => 'Пермский край',
			'volgograd'     => 'Волгоградская область',
			'voronezh'      => 'Воронежская область',
			'saratov'       => 'Саратовская область',
			'tyumen'        => 'Тюменская область',
			'omsk'          => 'Омская область',
			'kemerovo'      => 'Кемеровская область',
			'stavropol'     => 'Ставропольский край',
			'irkutsk'       => 'Иркутская область',
			'habarovsk'     => 'Хабаровский край',
			'primorsky'     => 'Приморский край',
			'sevastopol'    => 'Севастополь',
			'crimea'        => 'Республика Крым',
			'other'         => '— Другое —',
		);
	}

	public function filter_author_display_name( $display_name, $user_id, $post_id ) {
		if ( ! $post_id ) {
			return $display_name;
		}
		$contributor = get_post_meta( $post_id, 'dekanpro_contributor_name', true );
		return $contributor ? $contributor : $display_name;
	}

	public function filter_author_name( $display_name ) {
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return $display_name;
		}
		$contributor = get_post_meta( $post_id, 'dekanpro_contributor_name', true );
		return $contributor ? $contributor : $display_name;
	}

	public function append_location_meta( $content ) {
		if ( ! is_singular( 'post' ) ) {
			return $content;
		}
		$region     = get_post_meta( get_the_ID(), 'dekanpro_region', true );
		$city       = get_post_meta( get_the_ID(), 'dekanpro_city', true );
		$settlement = get_post_meta( get_the_ID(), 'dekanpro_settlement', true );
		if ( ! $region && ! $city && ! $settlement ) {
			return $content;
		}
		$parts    = array_filter( array( $region, $city, $settlement ) );
		$location = implode( ', ', $parts );
		$content .= '<p class="dekanpro-post-location"><span class="location-label">' . esc_html__( 'Местоположение:', 'dekanpro-contributions' ) . '</span> ' . esc_html( $location ) . '</p>';
		return $content;
	}
}

Dekanpro_Contributions::instance();

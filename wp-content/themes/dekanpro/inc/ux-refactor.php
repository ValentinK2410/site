<?php
/**
 * UX-улучшения: перелинковка, вовлечение, сбор контактов.
 *
 * @package DekanPro
 * @since 1.0.27
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Кнопки шаринга: VK, Telegram.
 */
function dekanpro_share_buttons_output() {
	if ( ! is_singular( 'post' ) || post_password_required() ) {
		return;
	}
	$url   = urlencode( get_permalink() );
	$title = urlencode( get_the_title() );
	$icons = function_exists( 'dekanpro' ) && isset( dekanpro()->icons ) ? dekanpro()->icons : null;
	?>
	<div class="dekanpro-share-buttons">
		<span class="share-label"><?php esc_html_e( 'Поделиться:', 'dekanpro' ); ?></span>
		<a class="share-btn share-vk" href="https://vk.com/share.php?url=<?php echo esc_attr( $url ); ?>&title=<?php echo esc_attr( $title ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Поделиться в VK', 'dekanpro' ); ?>">
			<?php if ( $icons ) : ?><span class="share-btn-icon"><?php echo $icons->get_svg( 'vkontakte', array( 'aria-hidden' => 'true' ) ); ?></span><?php endif; ?>
			<span class="share-btn-text">VK</span>
		</a>
		<a class="share-btn share-telegram" href="https://t.me/share/url?url=<?php echo esc_attr( $url ); ?>&text=<?php echo esc_attr( $title ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Поделиться в Telegram', 'dekanpro' ); ?>">
			<?php if ( $icons ) : ?><span class="share-btn-icon"><?php echo $icons->get_svg( 'telegram', array( 'aria-hidden' => 'true' ) ); ?></span><?php endif; ?>
			<span class="share-btn-text">Telegram</span>
		</a>
	</div>
	<?php
}
add_action( 'dekanpro_after_article', 'dekanpro_share_buttons_output', 5 );

/**
 * Похожие записи: случайный порядок (orderby rand) для разнообразия.
 */
function dekanpro_related_posts_orderby_rand( $args ) {
	$args['orderby'] = 'rand';
	return $args;
}
add_filter( 'dekanpro_related_posts_query_args', 'dekanpro_related_posts_orderby_rand' );

/**
 * Оглавление (TOC) для длинных статей по заголовкам h2/h3.
 */
function dekanpro_toc_output() {
	if ( ! is_singular( 'post' ) || post_password_required() ) {
		return;
	}
	$content = get_the_content();
	if ( empty( $content ) ) {
		return;
	}
	$headings = array();
	if ( preg_match_all( '/<h([23])[^>]*>([^<]+)<\/h\1>/', $content, $matches, PREG_SET_ORDER ) ) {
		$min_level = 2;
		foreach ( $matches as $i => $m ) {
			$level = (int) $m[1];
			$text  = trim( strip_tags( $m[2] ) );
			$id    = 'toc-' . ( $i + 1 );
			$headings[] = array(
				'level' => $level,
				'text'  => $text,
				'id'    => $id,
			);
			if ( $level < $min_level ) {
				$min_level = $level;
			}
		}
	}
	if ( count( $headings ) < 2 ) {
		return;
	}
	?>
	<nav class="dekanpro-toc" aria-label="<?php esc_attr_e( 'Оглавление', 'dekanpro' ); ?>">
		<h3 class="dekanpro-toc-title"><?php esc_html_e( 'Оглавление', 'dekanpro' ); ?></h3>
		<ul class="dekanpro-toc-list">
			<?php
			foreach ( $headings as $h ) {
				$slug = sanitize_title( $h['text'] );
				printf(
					'<li class="dekanpro-toc-item dekanpro-toc-h%d"><a href="#%s">%s</a></li>',
					$h['level'],
					esc_attr( $slug ),
					esc_html( $h['text'] )
				);
			}
			?>
		</ul>
	</nav>
	<?php
}
add_action( 'dekanpro_before_single_content', 'dekanpro_toc_output', 15 );

/**
 * Добавляет id к заголовкам h2/h3 в контенте для якорных ссылок оглавления.
 */
function dekanpro_add_heading_ids( $content ) {
	if ( ! is_singular( 'post' ) ) {
		return $content;
	}
	$content = preg_replace_callback(
		'/<h([23])([^>]*)>([^<]+)<\/h\1>/',
		function ( $m ) {
			$text = trim( strip_tags( $m[3] ) );
			$id   = sanitize_title( $text );
			return sprintf( '<h%1$s%2$s id="%3$s">%4$s</h%1$s>', $m[1], $m[2], $id, $m[3] );
		},
		$content
	);
	return $content;
}
add_filter( 'the_content', 'dekanpro_add_heading_ids', 5 );

/**
 * Кнопка «Добавить материал» на страницах рубрик.
 */
function dekanpro_contributions_cta() {
	if ( ! is_category() || ! class_exists( 'Dekanpro_Contributions' ) ) {
		return;
	}
	$page = get_page_by_path( 'dobavit-material' );
	if ( ! $page ) {
		return;
	}
	?>
	<div class="dekanpro-contrib-cta">
		<a href="<?php echo esc_url( get_permalink( $page ) ); ?>" class="dekanpro-btn primary-button">
			<?php esc_html_e( 'Добавить свой материал', 'dekanpro' ); ?>
		</a>
	</div>
	<?php
}
add_action( 'dekanpro_before_content', 'dekanpro_contributions_cta' );

/**
 * Добавляет класс для пункта «Выбрать регион и город».
 */
function dekanpro_region_selector_nav_class( $classes, $item ) {
	if ( $item->title === 'Выбрать регион и город' ) {
		$classes[] = 'dekanpro-region-trigger';
	}
	return $classes;
}
add_filter( 'nav_menu_css_class', 'dekanpro_region_selector_nav_class', 10, 2 );

/**
 * Список областей для выбора региона.
 */
function dekanpro_get_regions_list() {
	$regions = array(
		''             => '— Выберите регион —',
		'moscow'       => 'Москва',
		'spb'          => 'Санкт-Петербург',
		'mo'           => 'Московская область',
		'lo'           => 'Ленинградская область',
		'kr'           => 'Краснодарский край',
		'ro'           => 'Ростовская область',
		'so'           => 'Свердловская область',
		'novosibirsk'  => 'Новосибирская область',
		'nizhny'       => 'Нижегородская область',
		'samara'       => 'Самарская область',
		'chelyabinsk'  => 'Челябинская область',
		'bashkortostan'=> 'Республика Башкортостан',
		'tatarstan'    => 'Республика Татарстан',
		'krasnoyarsk'  => 'Красноярский край',
		'perm'         => 'Пермский край',
		'volgograd'    => 'Волгоградская область',
		'voronezh'     => 'Воронежская область',
		'saratov'      => 'Саратовская область',
		'tyumen'       => 'Тюменская область',
		'omsk'         => 'Омская область',
		'kemerovo'     => 'Кемеровская область',
		'stavropol'    => 'Ставропольский край',
		'irkutsk'      => 'Иркутская область',
		'habarovsk'    => 'Хабаровский край',
		'primorsky'    => 'Приморский край',
		'sevastopol'   => 'Севастополь',
		'crimea'       => 'Республика Крым',
		'other'        => 'Другое',
	);
	return apply_filters( 'dekanpro_regions_list', $regions );
}

/**
 * Вывод выпадающего блока выбора региона и города.
 */
function dekanpro_region_selector_output() {
	$regions = dekanpro_get_regions_list();
	?>
	<div id="dekanpro-region-selector" class="dekanpro-region-selector" aria-hidden="true">
		<div class="dekanpro-region-selector-inner">
			<h4 class="dekanpro-region-selector-title"><?php esc_html_e( 'Выбрать регион и город', 'dekanpro' ); ?></h4>
			<form class="dekanpro-region-form" method="get">
				<p class="dekanpro-region-row">
					<label for="dekanpro_region_select"><?php esc_html_e( 'Регион', 'dekanpro' ); ?></label>
					<select id="dekanpro_region_select" name="region">
						<?php foreach ( $regions as $key => $label ) : ?>
							<option value="<?php echo esc_attr( $key ); ?>"<?php selected( $key, 'saratov' ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>
				<p class="dekanpro-region-row">
					<label for="dekanpro_city_input"><?php esc_html_e( 'Город', 'dekanpro' ); ?></label>
					<input type="text" id="dekanpro_city_input" name="city" placeholder="<?php esc_attr_e( 'Например: Москва, Сочи', 'dekanpro' ); ?>">
				</p>
				<p class="dekanpro-region-actions">
					<button type="submit" class="dekanpro-btn primary-button"><?php esc_html_e( 'Применить', 'dekanpro' ); ?></button>
					<button type="button" class="dekanpro-btn dekanpro-region-clear"><?php esc_html_e( 'Сбросить', 'dekanpro' ); ?></button>
				</p>
			</form>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'dekanpro_region_selector_output' );

/**
 * Скрипт для открытия/закрытия селектора региона.
 */
function dekanpro_region_selector_script() {
	?>
	<script>
	(function() {
		var trigger = document.querySelector('.dekanpro-region-trigger a');
		var panel = document.getElementById('dekanpro-region-selector');
		if (!trigger || !panel) return;

		function initFormFromUrl() {
			var params = new URLSearchParams(window.location.search);
			var region = params.get('region') || 'saratov';
			var city = params.get('city') || '';
			var regionSel = panel.querySelector('[name="region"]');
			var cityInput = panel.querySelector('[name="city"]');
			if (regionSel) regionSel.value = region;
			if (cityInput) cityInput.value = city;
		}
		initFormFromUrl();

		trigger.addEventListener('click', function(e) {
			e.preventDefault();
			initFormFromUrl();
			panel.classList.toggle('is-open');
			panel.setAttribute('aria-hidden', panel.classList.contains('is-open') ? 'false' : 'true');
		});

		document.addEventListener('click', function(e) {
			if (panel.classList.contains('is-open') && !panel.contains(e.target) && !trigger.contains(e.target)) {
				panel.classList.remove('is-open');
				panel.setAttribute('aria-hidden', 'true');
			}
		});

		var form = panel.querySelector('.dekanpro-region-form');
		var clearBtn = panel.querySelector('.dekanpro-region-clear');
		if (form) {
			form.addEventListener('submit', function(e) {
				e.preventDefault();
				var region = form.querySelector('[name="region"]').value;
				var city = form.querySelector('[name="city"]').value;
				var params = new URLSearchParams();
				if (region) params.set('region', region);
				if (city) params.set('city', city);
				window.location.search = params.toString();
			});
		}
		if (clearBtn) {
			clearBtn.addEventListener('click', function() {
				window.location.href = window.location.pathname;
			});
		}
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'dekanpro_region_selector_script', 20 );

/**
 * Фильтрация постов по региону и городу.
 */
function dekanpro_filter_posts_by_region( $query ) {
	if ( ! $query->is_main_query() || is_admin() ) {
		return;
	}
	if ( ! $query->is_home() && ! $query->is_archive() && ! $query->is_search() ) {
		return;
	}
	$region = isset( $_GET['region'] ) ? sanitize_text_field( wp_unslash( $_GET['region'] ) ) : '';
	$city  = isset( $_GET['city'] ) ? sanitize_text_field( wp_unslash( $_GET['city'] ) ) : '';
	if ( empty( $region ) && empty( $city ) ) {
		return;
	}
	$meta_query = array( 'relation' => 'AND' );
	if ( $region && 'other' !== $region ) {
		$regions     = dekanpro_get_regions_list();
		$region_label = isset( $regions[ $region ] ) ? $regions[ $region ] : $region;
		$meta_query[] = array(
			'key'     => 'dekanpro_region',
			'value'   => $region_label,
			'compare' => '=',
		);
	}
	if ( $city ) {
		$meta_query[] = array(
			'key'     => 'dekanpro_city',
			'value'   => $city,
			'compare' => 'LIKE',
		);
	}
	$query->set( 'meta_query', $meta_query );
}
add_action( 'pre_get_posts', 'dekanpro_filter_posts_by_region' );

/**
 * Подписка: добавьте виджет «Текст» в сайдбар через Внешний вид → Виджеты
 * и вставьте код формы из Unisender, SendPulse и т.п. в режиме «Текст».
 * Сайдбар sidebar-1 уже зарегистрирован в inc/widgets.php.
 */

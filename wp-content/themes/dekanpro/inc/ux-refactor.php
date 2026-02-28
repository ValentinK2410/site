<?php
/**
 * UX-улучшения: перелинковка, вовлечение, сбор контактов.
 * Apple Newsroom style overrides.
 *
 * @package DekanPro
 * @since 1.0.27
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Apple Newsroom: скрыть автора в карточках, показывать только дату.
 */
function dekanpro_apple_newsroom_blog_meta( $elements ) {
	if ( is_singular() ) {
		return $elements;
	}
	return array( 'date' );
}
add_filter( 'dekanpro_entry_meta_elements', 'dekanpro_apple_newsroom_blog_meta', 999 );

/**
 * Apple Newsroom: в Hero тоже только дата, без автора.
 */
function dekanpro_apple_newsroom_hero_meta( $elements ) {
	return array( 'date' );
}
add_filter( 'dekanpro_hero_entry_meta_elements', 'dekanpro_apple_newsroom_hero_meta', 999 );

/**
 * Apple Newsroom: Hero-блок с названием сайта (как "Newsroom" у Apple).
 */
function dekanpro_apple_newsroom_hero_output() {
	if ( ! is_home() ) {
		return;
	}
	$site_name = get_bloginfo( 'name' );
	$tagline   = get_bloginfo( 'description' );
	?>
	<div class="dekanpro-newsroom-hero">
		<h1 class="dekanpro-newsroom-hero-title"><?php echo esc_html( $site_name ); ?></h1>
		<?php if ( $tagline ) : ?>
			<p class="dekanpro-newsroom-hero-tagline"><?php echo esc_html( $tagline ); ?></p>
		<?php endif; ?>
	</div>
	<?php
}
add_action( 'dekanpro_blog_heading', 'dekanpro_apple_newsroom_hero_output', 5 );

/**
 * Apple Newsroom: заголовок секции «Последние новости» на главной.
 */
function dekanpro_apple_newsroom_blog_heading( $value ) {
	if ( is_home() && ( empty( $value ) || ! is_string( $value ) ) ) {
		return '<h2 class="dekanpro-section-title">' . esc_html__( 'Последние новости', 'dekanpro' ) . '</h2>';
	}
	return $value;
}
add_filter( 'theme_mod_dekanpro_blog_heading', 'dekanpro_apple_newsroom_blog_heading' );

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
	$page = get_page_by_path( 'create' ) ?: get_page_by_path( 'dobavit-material' );
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

/* ============================================
   ГАЛЕРЕЯ С ФИЛЬТРАМИ ДЛЯ РУБРИК
   ============================================ */

/**
 * Подключаем скрипт и стили для галереи: на страницах рубрик и на странице каталога (блог/главная).
 */
function dekanpro_gallery_enqueue() {
	$show_gallery = is_category() || is_home();
	if ( ! $show_gallery ) {
		return;
	}
	wp_enqueue_script(
		'dekanpro-gallery',
		get_template_directory_uri() . '/assets/js/gallery-filter.js',
		array(),
		'1.0.2',
		true
	);
	$cat_id = is_category() ? get_queried_object_id() : 0;
	wp_localize_script( 'dekanpro-gallery', 'dekanproGallery', array(
		'ajaxurl'    => admin_url( 'admin-ajax.php' ),
		'nonce'      => wp_create_nonce( 'dekanpro_gallery' ),
		'category'   => $cat_id,
		'loading'    => __( 'Загрузка...', 'dekanpro' ),
		'no_results' => __( 'Ничего не найдено. Попробуйте изменить фильтры.', 'dekanpro' ),
		'load_more'  => __( 'Показать ещё', 'dekanpro' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'dekanpro_gallery_enqueue' );

/**
 * Панель фильтров перед контентом: на страницах рубрик и каталога (блог/главная).
 */
function dekanpro_gallery_filters_output() {
	$show_filters = is_category() || is_home();
	if ( ! $show_filters ) {
		return;
	}

	$tags = get_tags( array(
		'hide_empty' => true,
		'orderby'    => 'count',
		'order'      => 'DESC',
		'number'     => 30,
	) );
	$categories = is_home() ? get_categories( array( 'hide_empty' => true, 'orderby' => 'name' ) ) : array();
	?>
	<div class="dekanpro-gallery-filters" id="dekanpro-gallery-filters">
		<div class="gallery-filter-row">
			<div class="gallery-filter-search">
				<input type="text" id="gallery-search" placeholder="<?php esc_attr_e( 'Поиск по названию...', 'dekanpro' ); ?>" autocomplete="off">
			</div>
			<?php if ( ! empty( $categories ) ) : ?>
			<div class="gallery-filter-category">
				<select id="gallery-category">
					<option value=""><?php esc_html_e( 'Все рубрики', 'dekanpro' ); ?></option>
					<?php foreach ( $categories as $cat ) : ?>
						<option value="<?php echo esc_attr( $cat->term_id ); ?>"><?php echo esc_html( $cat->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<?php endif; ?>
			<div class="gallery-filter-sort">
				<select id="gallery-sort">
					<option value="date-desc"><?php esc_html_e( 'Сначала новые', 'dekanpro' ); ?></option>
					<option value="date-asc"><?php esc_html_e( 'Сначала старые', 'dekanpro' ); ?></option>
					<option value="title-asc"><?php esc_html_e( 'По названию А-Я', 'dekanpro' ); ?></option>
					<option value="title-desc"><?php esc_html_e( 'По названию Я-А', 'dekanpro' ); ?></option>
					<option value="popular"><?php esc_html_e( 'Популярные', 'dekanpro' ); ?></option>
				</select>
			</div>
			<div class="gallery-filter-view">
				<button type="button" class="gallery-view-btn active" data-view="grid" aria-label="<?php esc_attr_e( 'Сетка', 'dekanpro' ); ?>">
					<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="1" y="1" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="11" y="1" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="1" y="11" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="11" y="11" width="6" height="6" rx="1" stroke="currentColor" stroke-width="1.5"/></svg>
				</button>
				<button type="button" class="gallery-view-btn" data-view="list" aria-label="<?php esc_attr_e( 'Список', 'dekanpro' ); ?>">
					<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="1" y="2" width="16" height="3" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="1" y="8" width="16" height="3" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="1" y="14" width="16" height="3" rx="1" stroke="currentColor" stroke-width="1.5"/></svg>
				</button>
			</div>
		</div>
		<?php if ( ! empty( $tags ) ) : ?>
		<div class="gallery-filter-tags" id="gallery-tags">
			<button type="button" class="gallery-tag-btn active" data-tag=""><?php esc_html_e( 'Все', 'dekanpro' ); ?></button>
			<?php foreach ( $tags as $tag ) : ?>
				<button type="button" class="gallery-tag-btn" data-tag="<?php echo esc_attr( $tag->term_id ); ?>"><?php echo esc_html( $tag->name ); ?></button>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>
	<?php
}
add_action( 'dekanpro_before_content', 'dekanpro_gallery_filters_output', 20 );

/**
 * Рендерит одну карточку поста для галереи (используется и в PHP, и в AJAX).
 */
function dekanpro_render_gallery_card( $post_id ) {
	$thumb = get_the_post_thumbnail_url( $post_id, 'medium_large' );
	$title = get_the_title( $post_id );
	$link  = get_permalink( $post_id );
	$date  = get_the_date( 'j F Y', $post_id );
	$excerpt = get_the_excerpt( $post_id );
	if ( strlen( $excerpt ) > 120 ) {
		$excerpt = mb_substr( $excerpt, 0, 120 ) . '…';
	}
	$post_tags = get_the_tags( $post_id );
	?>
	<article class="gallery-card" data-id="<?php echo esc_attr( $post_id ); ?>">
		<?php if ( $thumb ) : ?>
		<a href="<?php echo esc_url( $link ); ?>" class="gallery-card-thumb">
			<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
		</a>
		<?php endif; ?>
		<div class="gallery-card-body">
			<?php if ( $post_tags ) : ?>
			<div class="gallery-card-tags">
				<?php foreach ( array_slice( $post_tags, 0, 3 ) as $tag ) : ?>
					<span class="gallery-card-tag"><?php echo esc_html( $tag->name ); ?></span>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
			<h3 class="gallery-card-title"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a></h3>
			<?php if ( $excerpt ) : ?>
			<p class="gallery-card-excerpt"><?php echo esc_html( $excerpt ); ?></p>
			<?php endif; ?>
			<div class="gallery-card-meta">
				<time datetime="<?php echo esc_attr( get_the_date( 'c', $post_id ) ); ?>"><?php echo esc_html( $date ); ?></time>
			</div>
		</div>
	</article>
	<?php
}

/**
 * AJAX-обработчик фильтрации галереи.
 */
function dekanpro_gallery_ajax() {
	check_ajax_referer( 'dekanpro_gallery', 'nonce' );

	$category = absint( $_POST['category'] ?? 0 );
	$search   = sanitize_text_field( wp_unslash( $_POST['search'] ?? '' ) );
	$tag      = absint( $_POST['tag'] ?? 0 );
	$sort     = sanitize_text_field( $_POST['sort'] ?? 'date-desc' );
	$page     = absint( $_POST['page'] ?? 1 );
	$per_page = 12;

	$args = array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => $per_page,
		'paged'          => $page,
	);

	if ( $category ) {
		$args['cat'] = $category;
	}

	if ( $search ) {
		$args['s'] = $search;
	}

	if ( $tag ) {
		$args['tag__in'] = array( $tag );
	}

	switch ( $sort ) {
		case 'date-asc':
			$args['orderby'] = 'date';
			$args['order']   = 'ASC';
			break;
		case 'title-asc':
			$args['orderby'] = 'title';
			$args['order']   = 'ASC';
			break;
		case 'title-desc':
			$args['orderby'] = 'title';
			$args['order']   = 'DESC';
			break;
		case 'popular':
			$args['orderby']  = 'comment_count';
			$args['order']    = 'DESC';
			break;
		default:
			$args['orderby'] = 'date';
			$args['order']   = 'DESC';
			break;
	}

	$query = new WP_Query( $args );

	ob_start();
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			dekanpro_render_gallery_card( get_the_ID() );
		}
		wp_reset_postdata();
	}
	$html = ob_get_clean();

	wp_send_json_success( array(
		'html'      => $html,
		'found'     => $query->found_posts,
		'max_pages' => $query->max_num_pages,
		'page'      => $page,
	) );
}
add_action( 'wp_ajax_dekanpro_gallery_filter', 'dekanpro_gallery_ajax' );
add_action( 'wp_ajax_nopriv_dekanpro_gallery_filter', 'dekanpro_gallery_ajax' );

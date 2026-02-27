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
 * Кнопки шаринга в соцсети (Twitter, VK, Telegram).
 */
function dekanpro_share_buttons_output() {
	if ( ! is_singular( 'post' ) || post_password_required() ) {
		return;
	}
	$url   = urlencode( get_permalink() );
	$title = urlencode( get_the_title() );
	?>
	<div class="dekanpro-share-buttons">
		<span class="share-label"><?php esc_html_e( 'Поделиться:', 'dekanpro' ); ?></span>
		<a class="share-btn share-twitter" href="https://twitter.com/intent/tweet?url=<?php echo esc_attr( $url ); ?>&text=<?php echo esc_attr( $title ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Twitter', 'dekanpro' ); ?>"><?php esc_html_e( 'Twitter', 'dekanpro' ); ?></a>
		<a class="share-btn share-vk" href="https://vk.com/share.php?url=<?php echo esc_attr( $url ); ?>&title=<?php echo esc_attr( $title ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'VK', 'dekanpro' ); ?>"><?php esc_html_e( 'VK', 'dekanpro' ); ?></a>
		<a class="share-btn share-telegram" href="https://t.me/share/url?url=<?php echo esc_attr( $url ); ?>&text=<?php echo esc_attr( $title ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Telegram', 'dekanpro' ); ?>"><?php esc_html_e( 'Telegram', 'dekanpro' ); ?></a>
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
 * Подписка: добавьте виджет «Текст» в сайдбар через Внешний вид → Виджеты
 * и вставьте код формы из Unisender, SendPulse и т.п. в режиме «Текст».
 * Сайдбар sidebar-1 уже зарегистрирован в inc/widgets.php.
 */

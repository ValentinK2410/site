/**
 * Post Analytics — трекинг просмотров.
 * Отправляет: глубину прокрутки, время на странице, устройство, платформу.
 */
( function() {
	'use strict';

	// Данные от wp_localize_script: postId, restUrl, nonce.
	if ( ! window.postAnalytics || ! window.postAnalytics.postId ) {
		return;
	}

	const config   = window.postAnalytics;
	let maxScroll  = 0;      // Максимальная достигнутая глубина прокрутки (0–100%).
	let startTime  = Date.now();
	let sent       = false;  // Отправили данные один раз — не дублируем.

	/**
	 * Определяет тип устройства по ширине окна.
	 * @returns {string} 'mobile' | 'tablet' | 'desktop'
	 */
	function getDevice() {
		const w = window.innerWidth;
		if ( w < 768 ) return 'mobile';
		if ( w < 1024 ) return 'tablet';
		return 'desktop';
	}

	/**
	 * Определяет платформу по User-Agent.
	 * @returns {string} iOS, Android, Windows, macOS, Linux или 'unknown'
	 */
	function getPlatform() {
		const ua = navigator.userAgent;
		if ( /iPhone|iPod|iPad/.test( ua ) ) return 'iOS';
		if ( /Android/.test( ua ) ) return 'Android';
		if ( /Windows/.test( ua ) ) return 'Windows';
		if ( /Mac OS/.test( ua ) ) return 'macOS';
		if ( /Linux/.test( ua ) ) return 'Linux';
		return navigator.platform || 'unknown';
	}

	/**
	 * Вычисляет текущую глубину прокрутки в процентах (0–100).
	 * @returns {number}
	 */
	function getScrollDepth() {
		const h = document.documentElement.scrollHeight - window.innerHeight;
		if ( h <= 0 ) return 100;
		const scrolled = window.scrollY || document.documentElement.scrollTop;
		return Math.round( ( scrolled / h ) * 100 );
	}

	/**
	 * Отправляет данные на сервер через REST API.
	 * @param {number} scrollDepth  Глубина прокрутки 0–100.
	 * @param {number} timeSeconds  Время на странице в секундах.
	 */
	function send( scrollDepth, timeSeconds ) {
		if ( sent ) return;
		sent = true;

		const body = new URLSearchParams( {
			post_id: config.postId,
			scroll_depth: scrollDepth,
			time_seconds: timeSeconds,
			device: getDevice(),
			platform: getPlatform(),
		} );

		// keepalive: true — запрос уйдёт даже при закрытии вкладки.
		fetch( config.restUrl, {
			method: 'POST',
			body: body,
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
				'X-WP-Nonce': config.nonce,
			},
			keepalive: true,
			credentials: 'same-origin',
		} ).catch( function() {} );
	}

	/** Обработчик прокрутки — обновляем maxScroll. */
	function onScroll() {
		const d = getScrollDepth();
		if ( d > maxScroll ) {
			maxScroll = d;
		}
	}

	/** При уходе со страницы — отправляем накопленные данные. */
	function onLeave() {
		const timeSeconds = Math.floor( ( Date.now() - startTime ) / 1000 );
		send( maxScroll, timeSeconds );
	}

	// Слушаем прокрутку (passive — не блокируем скролл).
	window.addEventListener( 'scroll', onScroll, { passive: true } );
	// События ухода: закрытие вкладки, переход по ссылке.
	window.addEventListener( 'beforeunload', onLeave );
	window.addEventListener( 'pagehide', onLeave );
	// Переключение вкладки (страница скрыта) — тоже считаем уходом.
	document.addEventListener( 'visibilitychange', function() {
		if ( document.visibilityState === 'hidden' ) {
			onLeave();
		}
	} );

	// Периодическая проверка прокрутки (на случай, если scroll не срабатывает).
	setInterval( function() {
		const d = getScrollDepth();
		if ( d > maxScroll ) {
			maxScroll = d;
		}
	}, 500 );

} )();

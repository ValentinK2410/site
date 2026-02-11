/**
 * Post Analytics — трекинг просмотров.
 * Отправляет: глубину прокрутки, время на странице, устройство, платформу.
 */
( function() {
	'use strict';

	if ( ! window.postAnalytics || ! window.postAnalytics.postId ) {
		return;
	}

	const config = window.postAnalytics;
	let maxScroll = 0;
	let startTime = Date.now();
	let sent = false;

	function getDevice() {
		const w = window.innerWidth;
		if ( w < 768 ) return 'mobile';
		if ( w < 1024 ) return 'tablet';
		return 'desktop';
	}

	function getPlatform() {
		const ua = navigator.userAgent;
		if ( /iPhone|iPod|iPad/.test( ua ) ) return 'iOS';
		if ( /Android/.test( ua ) ) return 'Android';
		if ( /Windows/.test( ua ) ) return 'Windows';
		if ( /Mac OS/.test( ua ) ) return 'macOS';
		if ( /Linux/.test( ua ) ) return 'Linux';
		return navigator.platform || 'unknown';
	}

	function getScrollDepth() {
		const h = document.documentElement.scrollHeight - window.innerHeight;
		if ( h <= 0 ) return 100;
		const scrolled = window.scrollY || document.documentElement.scrollTop;
		return Math.round( ( scrolled / h ) * 100 );
	}

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

	function onScroll() {
		const d = getScrollDepth();
		if ( d > maxScroll ) {
			maxScroll = d;
		}
	}

	function onLeave() {
		const timeSeconds = Math.floor( ( Date.now() - startTime ) / 1000 );
		send( maxScroll, timeSeconds );
	}

	window.addEventListener( 'scroll', onScroll, { passive: true } );
	window.addEventListener( 'beforeunload', onLeave );
	window.addEventListener( 'pagehide', onLeave );

	document.addEventListener( 'visibilitychange', function() {
		if ( document.visibilityState === 'hidden' ) {
			onLeave();
		}
	} );

	setInterval( function() {
		const d = getScrollDepth();
		if ( d > maxScroll ) {
			maxScroll = d;
		}
	}, 500 );

} )();

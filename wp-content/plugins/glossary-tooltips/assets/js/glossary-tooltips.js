/**
 * Glossary Tooltips - popup при клике на термин
 */
( function ( $ ) {
	'use strict';

	const $body = $( 'body' );
	let $popup = null;
	let $activeTerm = null;

	function createPopup() {
		if ( $popup && $popup.length ) {
			return $popup;
		}
		$popup = $( '<div class="glossary-popup" role="dialog" aria-labelledby="glossary-popup-title" aria-modal="true" hidden>' )
			.append(
				$( '<button type="button" class="glossary-popup-close" aria-label="Закрыть">&times;</button>' ),
				$( '<h3 class="glossary-popup-term" id="glossary-popup-title"></h3>' ),
				$( '<div class="glossary-popup-section glossary-popup-definition"></div>' ),
				$( '<div class="glossary-popup-section glossary-popup-examples"></div>' ),
				$( '<div class="glossary-popup-section glossary-popup-cases"></div>' )
			);
		$body.append( $popup );

		$popup.on( 'click', '.glossary-popup-close', closePopup );
		$popup.on( 'click', function ( e ) {
			if ( e.target === this ) {
				closePopup();
			}
		} );

		return $popup;
	}

	function showPopup( $term ) {
		$activeTerm = $term;
		const def = $term.attr( 'data-definition' ) || '';
		const ex  = $term.attr( 'data-examples' ) || '';
		const cases = $term.attr( 'data-use-cases' ) || '';

		$popup = createPopup();
		$popup.find( '.glossary-popup-term' ).text( $term.text().trim() );
		$popup.find( '.glossary-popup-definition' ).html( def ? '<p class="glossary-popup-label">Пояснение:</p><p>' + formatText( def ) + '</p>' : '' ).toggle( !! def );
		$popup.find( '.glossary-popup-examples' ).html( formatExamples( ex ) ).toggle( !! ex );
		$popup.find( '.glossary-popup-cases' ).html( formatCases( cases ) ).toggle( !! cases );

		$popup.removeAttr( 'hidden' ).css( { display: 'block', visibility: 'visible' } ).show();

		$( document ).off( 'keydown.glossary' ).on( 'keydown.glossary', function ( e ) {
			if ( e.key === 'Escape' ) {
				closePopup();
			}
		} );

		positionPopup( $term );
		$term.addClass( 'glossary-term-active' );
	}

	function formatText( text ) {
		if ( ! text ) return '';
		return text
			.replace( /&/g, '&amp;' )
			.replace( /</g, '&lt;' )
			.replace( />/g, '&gt;' )
			.replace( /\n/g, '<br>' );
	}

	function formatExamples( text ) {
		if ( ! text ) return '';
		const lines = text.split( '\n' ).filter( function ( s ) { return s.trim(); } );
		if ( ! lines.length ) return '';
		let html = '<p class="glossary-popup-label">Примеры использования:</p><ul>';
		lines.forEach( function ( line ) {
			html += '<li>' + formatText( line.trim() ) + '</li>';
		} );
		html += '</ul>';
		return html;
	}

	function formatUseCases( text ) {
		if ( ! text ) return '';
		const lines = text.split( '\n' ).filter( function ( s ) { return s.trim(); } );
		if ( ! lines.length ) return '';
		let html = '<p class="glossary-popup-label">В каких случаях:</p><ul>';
		lines.forEach( function ( line ) {
			html += '<li>' + formatText( line.trim() ) + '</li>';
		} );
		html += '</ul>';
		return html;
	}

	// Для "use cases" — тоже список или параграф
	function formatCases( text ) {
		if ( ! text ) return '';
		const lines = text.split( '\n' ).filter( function ( s ) { return s.trim(); } );
		if ( lines.length > 1 ) {
			let html = '<p class="glossary-popup-label">В каких случаях использовать:</p><ul>';
			lines.forEach( function ( line ) {
				html += '<li>' + formatText( line.trim() ) + '</li>';
			} );
			html += '</ul>';
			return html;
		}
		return '<p class="glossary-popup-label">В каких случаях использовать:</p><p>' + formatText( text ) + '</p>';
	}

	function positionPopup( $term ) {
		const termOffset = $term.offset();
		const termW      = $term.outerWidth();
		const termH      = $term.outerHeight();
		const popupW     = $popup.outerWidth();
		const popupH     = $popup.outerHeight();
		const winW       = $( window ).width();
		const winH       = $( window ).height();
		const scrollTop  = $( window ).scrollTop();
		const scrollLeft = $( window ).scrollLeft();
		const termTopVp  = termOffset.top - scrollTop;
		const termLeftVp = termOffset.left - scrollLeft;

		let left = termLeftVp + ( termW / 2 ) - ( popupW / 2 );
		let top  = termTopVp - popupH - 10;

		if ( left < 10 ) left = 10;
		if ( left + popupW > winW - 10 ) left = winW - popupW - 10;
		if ( top < 10 ) {
			top = termTopVp + termH + 10;
			$popup.addClass( 'glossary-popup-below' );
		} else {
			$popup.removeClass( 'glossary-popup-below' );
		}
		if ( top + popupH > winH - 10 ) {
			top = winH - popupH - 10;
		}

		$popup.css( { left: left + 'px', top: top + 'px' } );
	}

	function closePopup() {
		if ( $popup ) {
			$popup.attr( 'hidden', 'hidden' ).hide();
		}
		if ( $activeTerm ) {
			$activeTerm.removeClass( 'glossary-term-active' );
			$activeTerm = null;
		}
		$( document ).off( 'keydown.glossary' );
	}

	$( document ).on( 'click', '.glossary-term', function ( e ) {
		e.preventDefault();
		e.stopPropagation();
		const $term = $( this );
		if ( $popup && $popup.is( ':visible' ) && $term.is( $activeTerm ) ) {
			closePopup();
			return;
		}
		closePopup();
		showPopup( $term );
	} );

	$( document ).on( 'focus', '.glossary-term', function ( e ) {
		$( this ).on( 'keydown', function ( ev ) {
			if ( ev.key === 'Enter' || ev.key === ' ' ) {
				ev.preventDefault();
				$( this ).trigger( 'click' );
			}
		} );
	} );

} )( jQuery );

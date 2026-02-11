/**
 * Glossary Tooltips - popup при клике на термин.
 * Подсветка кода Prism, кросс-ссылки на другие термины, красочный дизайн.
 */
( function ( $ ) {
	'use strict';

	const $body = $( 'body' );
	let $popup = null;
	let $activeTerm = null;
	let termsData = [];

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
		$popup.on( 'click', '.glossary-popup-ref', function ( e ) {
			e.preventDefault();
			e.stopPropagation();
			const def = $( this ).attr( 'data-definition' ) || '';
			const ex  = $( this ).attr( 'data-examples' ) || '';
			const cases = $( this ).attr( 'data-use-cases' ) || '';
			const termText = $( this ).text().trim();
			showPopupFromData( termText, def, ex, cases, null );
		} );
		$popup.on( 'click', function ( e ) {
			if ( e.target === this ) {
				closePopup();
			}
		} );

		return $popup;
	}

	function escapeHtml( text ) {
		if ( ! text ) return '';
		return String( text )
			.replace( /&/g, '&amp;' )
			.replace( /</g, '&lt;' )
			.replace( />/g, '&gt;' );
	}

	function formatText( text ) {
		if ( ! text ) return '';
		return escapeHtml( text ).replace( /\n/g, '<br>' );
	}

	function looksLikeCode( line ) {
		const t = line.trim();
		return /\([^)]*\)|\$\w+|->|(add_|get_|wp_|array|if|function|register_)\w*\s*\(|;\s*$|^\s*}\s*$/.test( t ) || ( t.length > 0 && t.indexOf( "'" ) !== -1 && t.indexOf( '(' ) !== -1 );
	}

	function formatLine( line, currentTerm ) {
		const escaped = escapeHtml( line );
		if ( looksLikeCode( line ) ) {
			return '<pre class="glossary-popup-pre"><code class="language-php">' + escaped + '</code></pre>';
		}
		return '<p class="glossary-popup-text">' + injectGlossaryRefs( escaped, currentTerm ) + '</p>';
	}

	function attrEscape( s ) {
		return String( s || '' )
			.replace( /&/g, '&amp;' )
			.replace( /"/g, '&quot;' )
			.replace( /</g, '&lt;' )
			.replace( />/g, '&gt;' );
	}

	function injectGlossaryRefs( html, excludeTerm ) {
		if ( ! window.glossaryTermsForPopup || !html ) return html;
		let result = html;
		const sorted = [].concat( window.glossaryTermsForPopup ).sort( function ( a, b ) {
			const la = ( a.term && a.term.length ) || 0;
			const lb = ( b.term && b.term.length ) || 0;
			return lb - la;
		} );
		sorted.forEach( function ( t ) {
			const raw = t.variants || t.term;
			const variants = Array.isArray( raw ) ? raw : ( raw ? [ raw ] : [] );
			const skip = excludeTerm && Array.isArray( variants ) && variants.some( function ( v ) {
				return typeof v === 'string' && v.toLowerCase() === String( excludeTerm ).toLowerCase();
			} );
			if ( skip ) return;
			( Array.isArray( variants ) ? variants : [] ).forEach( function ( v ) {
				if ( typeof v !== 'string' || ! v || v.length < 2 ) return;
				const escaped = escapeHtml( v );
				const re = new RegExp( '(?<!["\'>\\w])(' + escaped.replace( /[.*+?^${}()|[\]\\]/g, '\\$&' ) + ')(?!["\'<\\w])', 'gi' );
				const dataDef = attrEscape( t.definition );
				const dataEx  = attrEscape( t.examples );
				const dataCases = attrEscape( t.use_cases );
				result = result.replace( re, '<span class="glossary-popup-ref" data-definition="' + dataDef + '" data-examples="' + dataEx + '" data-use-cases="' + dataCases + '" role="button" tabindex="0">$1</span>' );
			} );
		} );
		return result;
	}

	function formatExamples( text, currentTerm ) {
		if ( ! text ) return '';
		const lines = text.split( '\n' ).filter( function ( s ) { return s.trim(); } );
		if ( ! lines.length ) return '';
		let html = '<p class="glossary-popup-label glossary-popup-label-examples">Примеры использования</p>';
		lines.forEach( function ( line ) {
			html += formatLine( line.trim(), currentTerm );
		} );
		return html;
	}

	function formatCases( text, currentTerm ) {
		if ( ! text ) return '';
		const lines = text.split( '\n' ).filter( function ( s ) { return s.trim(); } );
		if ( ! lines.length ) return '';
		let html = '<p class="glossary-popup-label glossary-popup-label-cases">В каких случаях</p>';
		if ( lines.length > 1 ) {
			lines.forEach( function ( line ) {
				html += '<p class="glossary-popup-text">' + injectGlossaryRefs( escapeHtml( line.trim() ), currentTerm ) + '</p>';
			} );
		} else {
			html += '<p class="glossary-popup-text">' + injectGlossaryRefs( escapeHtml( text.trim() ).replace( /\n/g, '<br>' ), currentTerm ) + '</p>';
		}
		return html;
	}

	function showPopupFromData( termText, def, ex, cases, $anchorTerm ) {
		$popup = createPopup();
		$popup.find( '.glossary-popup-term' ).text( termText );
		$popup.find( '.glossary-popup-definition' ).html( def ? '<p class="glossary-popup-label glossary-popup-label-def">Пояснение</p><p class="glossary-popup-text">' + injectGlossaryRefs( formatText( def ), termText ) + '</p>' : '' ).toggle( !! def );
		$popup.find( '.glossary-popup-examples' ).html( formatExamples( ex, termText ) ).toggle( !! ex );
		$popup.find( '.glossary-popup-cases' ).html( formatCases( cases, termText ) ).toggle( !! cases );

		$popup.removeAttr( 'hidden' ).css( { display: 'block', visibility: 'visible' } ).show();

		$( document ).off( 'keydown.glossary' ).on( 'keydown.glossary', function ( e ) {
			if ( e.key === 'Escape' ) closePopup();
		} );

		if ( typeof Prism !== 'undefined' && Prism.highlightAll ) {
			Prism.highlightAllUnder( $popup[0] );
		}

		if ( $anchorTerm && $anchorTerm.length ) {
			positionPopup( $anchorTerm );
		} else {
			positionPopupNearCenter();
		}
	}

	function showPopup( $term ) {
		$activeTerm = $term;
		const def = $term.attr( 'data-definition' ) || '';
		const ex  = $term.attr( 'data-examples' ) || '';
		const cases = $term.attr( 'data-use-cases' ) || '';
		const termText = $term.text().trim();

		showPopupFromData( termText, def, ex, cases, $term );
		$term.addClass( 'glossary-term-active' );
	}

	function positionPopupNearCenter() {
		const winW = $( window ).width();
		const winH = $( window ).height();
		const scrollTop = $( window ).scrollTop();
		const popupW = $popup.outerWidth();
		const popupH = $popup.outerHeight();
		let left = Math.max( 10, ( winW - popupW ) / 2 );
		let top = Math.max( 10, scrollTop + ( winH - popupH ) / 2 );
		if ( top + popupH > scrollTop + winH - 10 ) top = scrollTop + winH - popupH - 10;
		$popup.css( { left: left + 'px', top: top + 'px' } );
	}

	function positionPopup( $term ) {
		const termOffset = $term.offset();
		const termW = $term.outerWidth();
		const termH = $term.outerHeight();
		const popupW = $popup.outerWidth();
		const popupH = $popup.outerHeight();
		const winW = $( window ).width();
		const winH = $( window ).height();
		const scrollTop = $( window ).scrollTop();
		const scrollLeft = $( window ).scrollLeft();
		const termTopVp = termOffset.top - scrollTop;
		const termLeftVp = termOffset.left - scrollLeft;

		let left = termLeftVp + ( termW / 2 ) - ( popupW / 2 );
		let top = termTopVp - popupH - 10;

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

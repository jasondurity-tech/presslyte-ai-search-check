( function () {
	'use strict';

	const config = window.PresslyteAISC;
	if ( ! config || ! config.labels || ! config.labels.findings ) {
		return;
	}

	const labels = config.labels;
	const findingLabels = labels.findings;
	let panel = null;
	let lastFocusedElement = null;

	function normalizeText( value ) {
		return String( value || '' ).replace( /\s+/g, ' ' ).trim();
	}

	function normalizedRole( element ) {
		return normalizeText( element.getAttribute( 'role' ) ).toLowerCase();
	}

	function isAriaHidden( element ) {
		let current = element;

		while ( current ) {
			if ( normalizeText( current.getAttribute( 'aria-hidden' ) ).toLowerCase() === 'true' ) {
				return true;
			}
			current = current.parentElement;
		}

		return false;
	}

	function descendantText( element ) {
		const parts = [];
		const walker = document.createTreeWalker( element, window.NodeFilter.SHOW_TEXT );

		while ( walker.nextNode() ) {
			let current = walker.currentNode.parentElement;
			let hidden = ! current;

			while ( current && ! hidden ) {
				const style = window.getComputedStyle( current );
				hidden = current.hidden
					|| normalizeText( current.getAttribute( 'aria-hidden' ) ).toLowerCase() === 'true'
					|| style.display === 'none'
					|| [ 'hidden', 'collapse' ].includes( style.visibility );

				if ( current === element ) {
					break;
				}
				current = current.parentElement;
			}

			if ( hidden ) {
				continue;
			}

			parts.push( walker.currentNode.nodeValue );
		}

		return normalizeText( parts.join( ' ' ) );
	}

	function isVisible( element ) {
		if ( ! element || ! element.getBoundingClientRect ) {
			return false;
		}

		const style = window.getComputedStyle( element );
		const rect = element.getBoundingClientRect();

		return style.display !== 'none' && style.visibility !== 'hidden' && rect.width > 0 && rect.height > 0;
	}

	function pageRoot() {
		const postId = Number.parseInt( config.postId, 10 );
		const selectors = [];

		if ( Number.isInteger( postId ) && postId > 0 ) {
			selectors.push( `#post-${ postId }`, `.post-${ postId }`, `[data-post-id="${ postId }"]` );
		}

		selectors.push( '.wp-block-post-content', '.entry-content', '[itemprop="articleBody"]', 'main', '[role="main"]', 'article' );

		for ( const selector of selectors ) {
			const element = Array.from( document.querySelectorAll( selector ) ).find( isVisible );
			if ( element ) {
				return { element, bodyFallback: false };
			}
		}

		return { element: document.body, bodyFallback: true };
	}

	function accessibleLinkLabel( link ) {
		const labelledBy = normalizeText( link.getAttribute( 'aria-labelledby' ) )
			.split( /\s+/ )
			.map( ( id ) => document.getElementById( id ) )
			.filter( Boolean )
			.map( ( element ) => normalizeText( element.textContent ) )
			.filter( Boolean )
			.join( ' ' );
		const ariaLabel = normalizeText( link.getAttribute( 'aria-label' ) );
		const visibleText = descendantText( link );
		const imageText = Array.from( link.querySelectorAll( 'img[alt]:not([aria-hidden="true"])' ) )
			.filter( ( image ) => ! isAriaHidden( image ) && ! [ 'presentation', 'none' ].includes( normalizedRole( image ) ) )
			.map( ( image ) => normalizeText( image.getAttribute( 'alt' ) ) )
			.filter( Boolean )
			.join( ' ' );
		const title = normalizeText( link.getAttribute( 'title' ) );

		return normalizeText( labelledBy || ariaLabel || [ visibleText, imageText ].filter( Boolean ).join( ' ' ) || title );
	}

	function finding( status, priority, title, detail, element ) {
		return { status, priority, title, detail, element: element || null };
	}

	function analyzePage( scope ) {
		const root = scope.element;
		const results = [];
		const noindexNames = new Set( [ 'robots', 'googlebot', 'bingbot' ] );
		const noindex = Array.from( document.querySelectorAll( 'meta[name][content]' ) ).find( ( meta ) => {
			const name = meta.getAttribute( 'name' ).trim().toLowerCase();
			const directives = meta.getAttribute( 'content' ).trim().toLowerCase().split( /[\s,;]+/ );
			return noindexNames.has( name ) && ( directives.includes( 'noindex' ) || directives.includes( 'none' ) );
		} );

		if ( noindex ) {
			results.push( finding( 'needs', 100, findingLabels.noindexBlockedTitle, findingLabels.noindexBlockedDetail ) );
		} else {
			results.push( finding( 'pass', 0, findingLabels.noindexPassTitle, findingLabels.noindexPassDetail ) );
		}

		const headingRoot = root.matches( '.wp-block-post-content, .entry-content, [itemprop="articleBody"]' )
			? root.closest( 'article, main, [role="main"]' ) || root
			: root;
		const headings = Array.from( headingRoot.querySelectorAll( 'h1, h2, h3, h4, h5, h6' ) ).filter( isVisible );
		const h1s = headings.filter( ( heading ) => heading.tagName === 'H1' );

		if ( h1s.length === 0 ) {
			results.push( finding( 'review', 80, findingLabels.noHeadingTitle, findingLabels.noHeadingDetail, root ) );
		} else if ( h1s.length > 1 ) {
			results.push( finding( 'review', 75, findingLabels.multipleHeadingTitle, findingLabels.multipleHeadingDetail, h1s[ 1 ] ) );
		} else {
			results.push( finding( 'pass', 0, findingLabels.headingPassTitle, findingLabels.headingPassDetail ) );
		}

		let previousLevel = 0;
		let skippedHeading = null;

		headings.some( ( heading ) => {
			const level = Number.parseInt( heading.tagName.slice( 1 ), 10 );
			if ( previousLevel && level > previousLevel + 1 ) {
				skippedHeading = heading;
				return true;
			}
			previousLevel = level;
			return false;
		} );

		if ( skippedHeading ) {
			results.push( finding( 'review', 70, findingLabels.headingSkipTitle, findingLabels.headingSkipDetail, skippedHeading ) );
		} else if ( headings.length > 0 ) {
			results.push( finding( 'pass', 0, findingLabels.headingOrderTitle, findingLabels.headingOrderDetail ) );
		}

		const images = Array.from( root.querySelectorAll( 'img' ) ).filter( isVisible );
		const missingAlt = images.find( ( image ) => ! image.hasAttribute( 'alt' ) && ! [ 'presentation', 'none' ].includes( normalizedRole( image ) ) );
		const failedImage = images.find( ( image ) => image.complete && image.naturalWidth === 0 );

		if ( missingAlt ) {
			results.push( finding( 'review', 65, findingLabels.missingAltTitle, findingLabels.missingAltDetail, missingAlt ) );
		} else if ( images.length > 0 ) {
			results.push( finding( 'pass', 0, findingLabels.altPassTitle, findingLabels.altPassDetail ) );
		}

		if ( failedImage ) {
			results.push( finding( 'needs', 90, findingLabels.brokenImageTitle, findingLabels.brokenImageDetail, failedImage ) );
		}

		const vagueLabels = new Set( [ '', 'click here', 'read more', 'learn more', 'more' ] );
		const vagueLink = Array.from( root.querySelectorAll( 'a[href]' ) ).filter( isVisible ).find( ( link ) => vagueLabels.has( normalizeText( accessibleLinkLabel( link ) ).toLowerCase() ) );

		if ( vagueLink ) {
			results.push( finding( 'review', 55, findingLabels.vagueLinkTitle, findingLabels.vagueLinkDetail, vagueLink ) );
		} else {
			results.push( finding( 'pass', 0, findingLabels.linkPassTitle, findingLabels.linkPassDetail ) );
		}

		const placeholderPattern = /\b(lorem ipsum|todo|tbd|placeholder text)\b|\[[a-z][a-z0-9_-]*(?:\s[^\]]*)?\]/i;
		const textElements = Array.from( root.querySelectorAll( 'p, li, h1, h2, h3, h4, h5, h6' ) ).filter( isVisible );
		const placeholder = textElements.find( ( element ) => placeholderPattern.test( element.textContent ) );

		if ( placeholder ) {
			results.push( finding( 'needs', 85, findingLabels.placeholderTitle, findingLabels.placeholderDetail, placeholder ) );
		} else {
			results.push( finding( 'pass', 0, findingLabels.placeholderPassTitle, findingLabels.placeholderPassDetail ) );
		}

		const longParagraph = Array.from( root.querySelectorAll( 'p' ) ).filter( isVisible ).find( ( paragraph ) => paragraph.textContent.trim().split( /\s+/ ).length > 120 );

		if ( longParagraph ) {
			results.push( finding( 'review', 45, findingLabels.longParagraphTitle, findingLabels.longParagraphDetail, longParagraph ) );
		}

		return results.sort( ( first, second ) => second.priority - first.priority );
	}

	function highlight( element ) {
		if ( ! element ) {
			return;
		}

		element.classList.add( 'presslyte-aisc-highlight' );
		const reduceMotion = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
		element.scrollIntoView( { behavior: reduceMotion ? 'auto' : 'smooth', block: 'center' } );
		window.setTimeout( () => element.classList.remove( 'presslyte-aisc-highlight' ), 2600 );
	}

	function resultCard( item ) {
		const card = document.createElement( item.element ? 'button' : 'div' );
		if ( item.element ) {
			card.type = 'button';
		}
		card.className = `presslyte-aisc-result presslyte-aisc-result--${ item.status }`;

		const status = document.createElement( 'span' );
		status.className = 'presslyte-aisc-result-status';
		status.textContent = statusLabel( item.status );

		const title = document.createElement( 'strong' );
		title.textContent = item.title;

		const detail = document.createElement( 'span' );
		detail.textContent = item.detail;

		card.append( status, title, detail );
		if ( item.element ) {
			card.addEventListener( 'click', () => highlight( item.element ) );
		}

		return card;
	}

	function closePanel() {
		if ( ! panel ) {
			return;
		}

		panel.remove();
		panel = null;
		if ( lastFocusedElement && lastFocusedElement.isConnected ) {
			lastFocusedElement.focus();
		}
		lastFocusedElement = null;
	}

	function statusLabel( status ) {
		if ( status === 'needs' ) {
			return labels.statusNeeds;
		}
		if ( status === 'review' ) {
			return labels.statusReview;
		}
		return labels.statusReady;
	}

	function actionLink( label, href ) {
		const link = document.createElement( 'a' );
		link.className = 'presslyte-aisc-action';
		link.href = href;
		link.textContent = label;
		return link;
	}

	function openPanel( trigger ) {
		if ( panel ) {
			closePanel();
			return;
		}

		lastFocusedElement = trigger;
		const scope = pageRoot();
		const results = analyzePage( scope );
		const priorities = results.filter( ( item ) => item.status !== 'pass' );
		const passed = results.filter( ( item ) => item.status === 'pass' );
		const overallStatus = results.some( ( item ) => item.status === 'needs' )
			? 'needs'
			: results.some( ( item ) => item.status === 'review' ) ? 'review' : 'pass';

		panel = document.createElement( 'aside' );
		panel.id = 'presslyte-ai-search-check-panel';
		panel.className = 'presslyte-aisc-panel';
		panel.setAttribute( 'role', 'dialog' );
		panel.setAttribute( 'aria-modal', 'false' );
		panel.setAttribute( 'aria-labelledby', 'presslyte-aisc-title' );
		panel.setAttribute( 'aria-describedby', 'presslyte-aisc-intro' );

		const header = document.createElement( 'header' );
		const brand = document.createElement( 'div' );
		brand.className = 'presslyte-aisc-brand';

		if ( config.iconUrl ) {
			const icon = document.createElement( 'img' );
			icon.className = 'presslyte-aisc-icon';
			icon.src = config.iconUrl;
			icon.alt = '';
			icon.width = 40;
			icon.height = 40;
			icon.decoding = 'async';
			brand.append( icon );
		}

		const heading = document.createElement( 'h2' );
		heading.id = 'presslyte-aisc-title';
		heading.textContent = labels.panelTitle;
		brand.append( heading );

		const close = document.createElement( 'button' );
		close.type = 'button';
		close.className = 'presslyte-aisc-close';
		close.setAttribute( 'aria-label', labels.close );
		close.textContent = '×';
		close.addEventListener( 'click', closePanel );
		header.append( brand, close );

		const intro = document.createElement( 'p' );
		intro.id = 'presslyte-aisc-intro';
		intro.className = 'presslyte-aisc-intro';
		intro.textContent = [ labels.panelIntro, scope.bodyFallback ? labels.bodyFallback : '' ].filter( Boolean ).join( ' ' );

		const summary = document.createElement( 'p' );
		summary.className = `presslyte-aisc-summary presslyte-aisc-summary--${ overallStatus }`;
		summary.textContent = statusLabel( overallStatus );

		const priorityHeading = document.createElement( 'h3' );
		priorityHeading.textContent = labels.priorityTitle;

		const priorityList = document.createElement( 'div' );
		priorityList.className = 'presslyte-aisc-list';
		if ( priorities.length ) {
			priorities.forEach( ( item ) => priorityList.append( resultCard( item ) ) );
		} else {
			const empty = document.createElement( 'p' );
			empty.className = 'presslyte-aisc-empty';
			empty.textContent = labels.noIssues;
			priorityList.append( empty );
		}

		const passedHeading = document.createElement( 'h3' );
		passedHeading.textContent = labels.passedTitle;

		const passedList = document.createElement( 'ul' );
		passedList.className = 'presslyte-aisc-passed';
		passed.forEach( ( item ) => {
			const row = document.createElement( 'li' );
			row.textContent = item.title;
			passedList.append( row );
		} );

		const actions = document.createElement( 'footer' );
		if ( config.editUrl ) {
			actions.append( actionLink( labels.edit, config.editUrl ) );
		}

		panel.append( header, summary, intro, priorityHeading, priorityList );
		if ( passed.length ) {
			panel.append( passedHeading, passedList );
		}
		if ( actions.children.length ) {
			panel.append( actions );
		}
		document.body.append( panel );
		close.focus();
	}

	document.addEventListener( 'click', ( event ) => {
		const trigger = event.target.closest( '#wp-admin-bar-presslyte-ai-search-check > a' );
		if ( ! trigger ) {
			return;
		}
		event.preventDefault();
		openPanel( trigger );
	} );

	document.addEventListener( 'keydown', ( event ) => {
		if ( event.key === 'Escape' && panel ) {
			closePanel();
		}
	} );
}() );

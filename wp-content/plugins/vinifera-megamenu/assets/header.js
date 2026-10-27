( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {

		/* ── Sticky header ──────────────────────────────────────── */
		var hdr = document.getElementById( 'site-header' );
		if ( hdr ) {
			window.addEventListener( 'scroll', function () {
				hdr.classList.toggle( 'is-sticky', window.scrollY > 60 );
			}, { passive: true } );
		}

		/* ── Mega menu: hover (desktop) + click (mobile) ────────── */
		var items = document.querySelectorAll( '.nav-item.has-mega' );
		items.forEach( function ( item ) {
			var trigger = item.querySelector( ':scope > a' );
			if ( ! trigger ) return;

			function openItem() {
				items.forEach( closeItem );
				item.classList.add( 'is-open' );
				trigger.setAttribute( 'aria-expanded', 'true' );
			}
			function closeItem() {
				item.classList.remove( 'is-open' );
				trigger.setAttribute( 'aria-expanded', 'false' );
			}

			item.addEventListener( 'mouseenter', function () {
				if ( window.innerWidth >= 1024 ) openItem();
			} );
			item.addEventListener( 'mouseleave', function () {
				if ( window.innerWidth >= 1024 ) closeItem();
			} );
			trigger.addEventListener( 'click', function ( e ) {
				if ( window.innerWidth < 1024 ) {
					e.preventDefault();
					item.classList.contains( 'is-open' ) ? closeItem() : openItem();
				}
			} );
		} );

		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' ) {
				items.forEach( function ( item ) {
					item.classList.remove( 'is-open' );
					var t = item.querySelector( ':scope > a' );
					if ( t ) t.setAttribute( 'aria-expanded', 'false' );
				} );
			}
		} );

		/* ── Mobile menu toggle ─────────────────────────────────── */
		var toggleBtn = document.querySelector( '.mobile-menu-toggle' );
		var nav       = document.getElementById( 'topnav' );
		if ( toggleBtn && nav ) {
			toggleBtn.addEventListener( 'click', function () {
				var expanded = toggleBtn.getAttribute( 'aria-expanded' ) === 'true';
				toggleBtn.setAttribute( 'aria-expanded', String( ! expanded ) );
				nav.classList.toggle( 'is-open' );
			} );
		}

		/* ── Search overlay ─────────────────────────────────────── */
		var searchToggle  = document.querySelector( '.search-toggle' );
		var overlay       = document.getElementById( 'search-overlay' );
		var overlayClose  = overlay ? overlay.querySelector( '.search-overlay__close' ) : null;
		var overlayInput  = overlay ? overlay.querySelector( '.search-overlay__input' ) : null;

		function openOverlay() {
			if ( ! overlay ) return;
			overlay.classList.add( 'is-active' );
			document.body.style.overflow = 'hidden';
			setTimeout( function () { if ( overlayInput ) overlayInput.focus(); }, 50 );
		}
		function closeOverlay() {
			if ( ! overlay ) return;
			overlay.classList.remove( 'is-active' );
			document.body.style.overflow = '';
		}

		if ( searchToggle ) searchToggle.addEventListener( 'click', openOverlay );
		if ( overlayClose ) overlayClose.addEventListener( 'click', closeOverlay );
		if ( overlay ) {
			overlay.addEventListener( 'click', function ( e ) {
				if ( e.target === overlay ) closeOverlay();
			} );
		}
		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' ) closeOverlay();
		} );

		/* ── WooCommerce: aggiorna contatore carrello ────────────── */
		function refreshCartCount() {
			if ( ! window.fetch ) return;
			fetch( '/wp-json/wc/store/v1/cart', { credentials: 'include' } )
				.then( function ( r ) { return r.json(); } )
				.then( function ( d ) {
					document.querySelectorAll( '.cart-count' ).forEach( function ( el ) {
						el.textContent = String( d.items_count || 0 );
					} );
				} )
				.catch( function () {} );
		}

		document.addEventListener( 'wc_cart_hash_changed', refreshCartCount );
		document.addEventListener( 'added_to_cart', refreshCartCount );
		document.addEventListener( 'removed_from_cart', refreshCartCount );

	} );
} )();

<?php
/**
 * Plugin Name: Sayweb Megamenu
 * Description: Custom mega menu block for the header, built around native Gutenberg blocks.
 * Version: 1.0.0
 * Author: Sayweb
 * Text Domain: sayweb-megamenu
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sayweb_megamenu_default_panel_html(): string {
	return '<div class="mega-grid">'
		. '<div class="mega-col">'
		. '<span class="mega-title">Regione</span>'
		. '<a href="#">Sicilia</a>'
		. '<a href="#">Puglia</a>'
		. '<a href="#">Campania</a>'
		. '<a href="#">Sardegna</a>'
		. '<a href="#" class="mega-all">Vedi tutti</a>'
		. '</div>'
		. '<div class="mega-col">'
		. '<span class="mega-title">Tipologia</span>'
		. '<a href="#">Valpolicella</a>'
		. '<a href="#">Nebbiolo</a>'
		. '<a href="#">Brunello</a>'
		. '<a href="#">Amarone</a>'
		. '<a href="#" class="mega-all">Vedi tutti</a>'
		. '</div>'
		. '<div class="mega-col">'
		. '<span class="mega-title">Abbinamento</span>'
		. '<a href="#">Carni bianche</a>'
		. '<a href="#">Vini da grigliata</a>'
		. '<a href="#">Carni rosse</a>'
		. '<a href="#">Formaggi</a>'
		. '<a href="#" class="mega-all">Vedi tutti</a>'
		. '</div>'
		. '</div>';
}

function sayweb_megamenu_parse_link_lines( string $lines ): array {
	$items = array();
	$rows  = preg_split( '/\r\n|\r|\n/', $lines );

	if ( ! is_array( $rows ) ) {
		return $items;
	}

	foreach ( $rows as $row ) {
		$row = trim( (string) $row );
		if ( '' === $row ) {
			continue;
		}

		$parts = explode( '|', $row, 2 );
		$label = trim( $parts[0] );
		$url   = isset( $parts[1] ) ? trim( $parts[1] ) : '#';

		if ( '' === $label ) {
			continue;
		}

		$items[] = array(
			'label' => $label,
			'url'   => '' !== $url ? $url : '#',
		);
	}

	return $items;
}

function sayweb_megamenu_render_column( string $title, string $items_raw ): string {
	$items = sayweb_megamenu_parse_link_lines( $items_raw );
	$html  = '<div class="mega-col"><span class="mega-title">' . esc_html( $title ) . '</span>';

	foreach ( $items as $item ) {
		$label = esc_html( (string) $item['label'] );
		$url   = esc_url( (string) $item['url'] );
		$html .= '<a href="' . $url . '">' . $label . '</a>';
	}

	$html .= '</div>';

	return $html;
}

function sayweb_megamenu_render_category( array $attributes = [], string $content = '' ): string {
	$defaults = array(
		'label'     => 'Rossi',
		'url'       => '#',
		'col1Title' => 'Regione',
		'col1Items' => "Sicilia|#\nPuglia|#\nCampania|#\nSardegna|#\nVedi tutti|#",
		'col2Title' => 'Tipologia',
		'col2Items' => "Valpolicella|#\nNebbiolo|#\nBrunello|#\nAmarone|#\nVedi tutti|#",
		'col3Title' => 'Abbinamento',
		'col3Items' => "Carni bianche|#\nVini da grigliata|#\nCarni rosse|#\nFormaggi|#\nVedi tutti|#",
	);

	$attributes = wp_parse_args( $attributes, $defaults );
	$label      = esc_html( (string) $attributes['label'] );
	$url        = esc_url( (string) $attributes['url'] );

	$panel_html = '<div class="mega-grid">'
		. sayweb_megamenu_render_column( (string) $attributes['col1Title'], (string) $attributes['col1Items'] )
		. sayweb_megamenu_render_column( (string) $attributes['col2Title'], (string) $attributes['col2Items'] )
		. sayweb_megamenu_render_column( (string) $attributes['col3Title'], (string) $attributes['col3Items'] )
		. '</div>';

	if ( trim( $panel_html ) === '' ) {
		$panel_html = sayweb_megamenu_default_panel_html();
	}

	return '<div class="nav-item has-mega"><a href="' . $url . '">' . $label . '</a><div class="mega"><div class="wrap">' . $panel_html . '</div></div></div>';
}

function sayweb_megamenu_render_nav_link( array $attributes = [] ): string {
	$defaults   = array( 'label' => 'Link', 'url' => '#' );
	$attributes = wp_parse_args( $attributes, $defaults );
	return '<a href="' . esc_url( (string) $attributes['url'] ) . '" class="nav-item">' . esc_html( (string) $attributes['label'] ) . '</a>';
}

function sayweb_megamenu_render_mobile_toggle(): string {
	return '<button class="mobile-menu-toggle" aria-label="Apri menu" aria-expanded="false" aria-controls="topnav">'
		. '<span></span><span></span><span></span>'
		. '</button>';
}

function sayweb_megamenu_render_header_search( array $attributes = [] ): string {
	$defaults   = array( 'placeholder' => 'Cerca vini, produttori, zone\u2026' );
	$attributes = wp_parse_args( $attributes, $defaults );
	$placeholder = esc_attr( (string) $attributes['placeholder'] );

	return '<button class="icobtn search-toggle" aria-label="Cerca" data-toggle="search">'
		. '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="16.5" y1="16.5" x2="21" y2="21"/></svg>'
		. '</button>'
		. '<div class="search-overlay" id="search-overlay" role="dialog" aria-modal="true" aria-label="Ricerca">'
		. '<div class="search-overlay__inner">'
		. '<button class="search-overlay__close" aria-label="Chiudi ricerca"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>'
		. '<form class="search-overlay__form" role="search" method="get" action="' . esc_url( home_url( '/' ) ) . '">'
		. '<input class="search-overlay__input" type="search" name="s" placeholder="' . $placeholder . '" autocomplete="off" aria-label="Cerca">'
		. '<button type="submit" aria-label="Avvia ricerca"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><line x1="16.5" y1="16.5" x2="21" y2="21"/></svg></button>'
		. '</form>'
		. '</div>'
		. '</div>';
}

function sayweb_megamenu_render_header_account( array $attributes = [] ): string {
	$defaults = array(
		'url'   => '',
		'label' => 'Il mio account',
	);
	$attributes = wp_parse_args( $attributes, $defaults );

	$url = ( '' !== (string) $attributes['url'] )
		? esc_url( (string) $attributes['url'] )
		: ( function_exists( 'wc_get_account_endpoint_url' )
			? esc_url( wc_get_account_endpoint_url( 'dashboard' ) )
			: '/my-account/' );
	$label = esc_attr( (string) $attributes['label'] );

	return '<a href="' . $url . '" class="icobtn" aria-label="' . $label . '">'
		. '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>'
		. '</a>';
}

function sayweb_megamenu_render_header_cart( array $attributes = [] ): string {
	$defaults = array( 'url' => '' );
	$attributes = wp_parse_args( $attributes, $defaults );

	$url = ( '' !== (string) $attributes['url'] )
		? esc_url( (string) $attributes['url'] )
		: ( function_exists( 'wc_get_cart_url' )
			? esc_url( wc_get_cart_url() )
			: '/carrello/' );
	$cart_count = ( function_exists( 'WC' ) && WC()->cart )
		? intval( WC()->cart->get_cart_contents_count() )
		: 0;

	return '<a href="' . $url . '" class="icobtn cart-btn" aria-label="Carrello">'
		. '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 7h12l-1.2 10a2 2 0 0 1-2 1.8H9.2a2 2 0 0 1-2-1.8L6 7z"/><path d="M9 7V5a3 3 0 0 1 6 0v2"/></svg>'
		. '<span class="cart-count" aria-live="polite">' . $cart_count . '</span>'
		. '</a>';
}

function sayweb_megamenu_render_header_whatsapp( array $attributes = [] ): string {
	$defaults = array(
		'waUrl'  => '/contattaci/#whatsapp',
		'waText' => 'Parla con Stefano',
	);
	$attributes = wp_parse_args( $attributes, $defaults );
	return '<a href="' . esc_url( (string) $attributes['waUrl'] ) . '" class="wa">'
		. esc_html( (string) $attributes['waText'] )
		. '</a>';
}

function sayweb_megamenu_render_header_actions( array $attributes = [] ): string {
	$defaults   = array(
		'waUrl'  => '/contattaci/#whatsapp',
		'waText' => 'Parla con Stefano',
	);
	$attributes = wp_parse_args( $attributes, $defaults );
	$wa_url     = esc_url( (string) $attributes['waUrl'] );
	$wa_text    = esc_html( (string) $attributes['waText'] );

	$account_url = function_exists( 'wc_get_account_endpoint_url' )
		? esc_url( wc_get_account_endpoint_url( 'dashboard' ) )
		: '/my-account/';
	$cart_url    = function_exists( 'wc_get_cart_url' )
		? esc_url( wc_get_cart_url() )
		: '/carrello/';
	$cart_count  = ( function_exists( 'WC' ) && WC()->cart )
		? intval( WC()->cart->get_cart_contents_count() )
		: 0;

	return '<div class="topactions">'
		. '<button class="icobtn search-toggle" aria-label="Cerca" data-toggle="search">'
		. '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="16.5" y1="16.5" x2="21" y2="21"/></svg>'
		. '</button>'
		. '<a href="' . $account_url . '" class="icobtn" aria-label="Il mio account">'
		. '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>'
		. '</a>'
		. '<a href="' . $cart_url . '" class="icobtn cart-btn" aria-label="Carrello">'
		. '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 7h12l-1.2 10a2 2 0 0 1-2 1.8H9.2a2 2 0 0 1-2-1.8L6 7z"/><path d="M9 7V5a3 3 0 0 1 6 0v2"/></svg>'
		. '<span class="cart-count" aria-live="polite">' . $cart_count . '</span>'
		. '</a>'
		. '<a href="' . $wa_url . '" class="wa">' . $wa_text . '</a>'
		. '</div>'
		. '<div class="search-overlay" id="search-overlay" role="dialog" aria-modal="true" aria-label="Ricerca">'
		. '<div class="search-overlay__inner">'
		. '<button class="search-overlay__close" aria-label="Chiudi ricerca"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>'
		. '<form class="search-overlay__form" role="search" method="get" action="' . esc_url( home_url( '/' ) ) . '">'
		. '<input class="search-overlay__input" type="search" name="s" placeholder="Cerca vini, produttori, zone…" autocomplete="off" aria-label="Cerca">'
		. '<button type="submit" aria-label="Avvia ricerca"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><line x1="16.5" y1="16.5" x2="21" y2="21"/></svg></button>'
		. '</form>'
		. '</div>'
		. '</div>';
}

function sayweb_megamenu_enqueue_frontend(): void {
	if ( is_admin() ) {
		return;
	}
	$plugin_url = plugin_dir_url( __FILE__ );
	$js_file    = plugin_dir_path( __FILE__ ) . 'assets/header.js';
	if ( file_exists( $js_file ) ) {
		wp_enqueue_script(
			'sayweb-header',
			$plugin_url . 'assets/header.js',
			array(),
			(string) filemtime( $js_file ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'sayweb_megamenu_enqueue_frontend' );

function sayweb_megamenu_register_blocks(): void {
	$plugin_dir = plugin_dir_path( __FILE__ );
	$plugin_url  = plugin_dir_url( __FILE__ );
	$editor_file = $plugin_dir . 'blocks/mega-menu/edit.js';

	if ( file_exists( $editor_file ) ) {
		wp_register_script(
			'sayweb-megamenu-editor',
			$plugin_url . 'blocks/mega-menu/edit.js',
			array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
			filemtime( $editor_file ),
			false
		);
	}

	register_block_type( 'vinifera/mega-menu-category', array(
		'api_version'     => 3,
		'render_callback' => 'sayweb_megamenu_render_category',
		'editor_script'   => 'sayweb-megamenu-editor',
		'attributes'      => array(
			'label'     => array( 'type' => 'string', 'default' => 'Rossi' ),
			'url'       => array( 'type' => 'string', 'default' => '#' ),
			'col1Title' => array( 'type' => 'string', 'default' => 'Regione' ),
			'col1Items' => array( 'type' => 'string', 'default' => "Sicilia|#\nPuglia|#\nCampania|#\nSardegna|#\nVedi tutti|#" ),
			'col2Title' => array( 'type' => 'string', 'default' => 'Tipologia' ),
			'col2Items' => array( 'type' => 'string', 'default' => "Valpolicella|#\nNebbiolo|#\nBrunello|#\nAmarone|#\nVedi tutti|#" ),
			'col3Title' => array( 'type' => 'string', 'default' => 'Abbinamento' ),
			'col3Items' => array( 'type' => 'string', 'default' => "Carni bianche|#\nVini da grigliata|#\nCarni rosse|#\nFormaggi|#\nVedi tutti|#" ),
		),
	) );

	register_block_type( 'vinifera/nav-link', array(
		'api_version'     => 3,
		'render_callback' => 'sayweb_megamenu_render_nav_link',
		'editor_script'   => 'sayweb-megamenu-editor',
		'attributes'      => array(
			'label' => array( 'type' => 'string', 'default' => 'Link' ),
			'url'   => array( 'type' => 'string', 'default' => '#' ),
		),
	) );

	register_block_type( 'vinifera/header-actions', array(
		'api_version'     => 3,
		'render_callback' => 'sayweb_megamenu_render_header_actions',
		'editor_script'   => 'sayweb-megamenu-editor',
		'attributes'      => array(
			'waUrl'  => array( 'type' => 'string', 'default' => '/contattaci/#whatsapp' ),
			'waText' => array( 'type' => 'string', 'default' => 'Parla con Stefano' ),
		),
	) );

	register_block_type( 'vinifera/mobile-toggle', array(
		'api_version'     => 3,
		'render_callback' => 'sayweb_megamenu_render_mobile_toggle',
		'editor_script'   => 'sayweb-megamenu-editor',
		'attributes'      => array(),
	) );

	register_block_type( 'vinifera/header-search', array(
		'api_version'     => 3,
		'render_callback' => 'sayweb_megamenu_render_header_search',
		'editor_script'   => 'sayweb-megamenu-editor',
		'attributes'      => array(
			'placeholder' => array( 'type' => 'string', 'default' => 'Cerca vini, produttori, zone\u2026' ),
		),
	) );

	register_block_type( 'vinifera/header-account', array(
		'api_version'     => 3,
		'render_callback' => 'sayweb_megamenu_render_header_account',
		'editor_script'   => 'sayweb-megamenu-editor',
		'attributes'      => array(
			'url'   => array( 'type' => 'string', 'default' => '' ),
			'label' => array( 'type' => 'string', 'default' => 'Il mio account' ),
		),
	) );

	register_block_type( 'vinifera/header-cart', array(
		'api_version'     => 3,
		'render_callback' => 'sayweb_megamenu_render_header_cart',
		'editor_script'   => 'sayweb-megamenu-editor',
		'attributes'      => array(
			'url' => array( 'type' => 'string', 'default' => '' ),
		),
	) );

	register_block_type( 'vinifera/header-whatsapp', array(
		'api_version'     => 3,
		'render_callback' => 'sayweb_megamenu_render_header_whatsapp',
		'editor_script'   => 'sayweb-megamenu-editor',
		'attributes'      => array(
			'waUrl'  => array( 'type' => 'string', 'default' => '/contattaci/#whatsapp' ),
			'waText' => array( 'type' => 'string', 'default' => 'Parla con Stefano' ),
		),
	) );
}
add_action( 'init', 'sayweb_megamenu_register_blocks' );

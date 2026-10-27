<?php
/**
 * Plugin Name: Vinifera Map Block
 * Description: Dynamic Google Maps block for Vinifera contact pages with stored API key.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'VINIFERA_MAP_BLOCK_LOADED' ) ) {
	return;
}
define( 'VINIFERA_MAP_BLOCK_LOADED', true );

const VINIFERA_MAPS_OPTION_API_KEY = 'vinifera_maps_api_key';

function vinifera_map_block_render( array $attributes = [] ): string {
	$attributes = wp_parse_args( $attributes, [
		'eyebrow' => 'Vinifera Enoteca',
		'title' => 'Largo Piave 10/11',
		'addressText' => '70010 Locorotondo (BA), nel centro storico della Valle d\'Itria.',
		'pinText' => 'Mappa segnaposto',
		'coordsLabel' => 'Coordinate',
		'latitude' => '40.7243',
		'longitude' => '17.3298',
		'zoom' => '16',
		'linkLabel' => 'Apri in Google Maps →',
		'linkUrl' => 'https://www.google.com/maps/search/?api=1&query=Largo+Piave+10%2F11+70010+Locorotondo',
	] );

	$api_key = trim( (string) get_option( VINIFERA_MAPS_OPTION_API_KEY, '' ) );
	$latitude = trim( (string) $attributes['latitude'] );
	$longitude = trim( (string) $attributes['longitude'] );
	$zoom = absint( $attributes['zoom'] );
	$zoom = $zoom ? $zoom : 16;

	$eyebrow = esc_html( $attributes['eyebrow'] );
	$title = esc_html( $attributes['title'] );
	$address_text = esc_html( $attributes['addressText'] );
	$pin_text = esc_html( $attributes['pinText'] );
	$coords_label = esc_html( $attributes['coordsLabel'] );
	$link_label = esc_html( $attributes['linkLabel'] );
	$link_url = esc_url( $attributes['linkUrl'] );
	$coords = esc_html( $latitude . ', ' . $longitude );

	if ( '' === $api_key ) {
		return '<div class="map-frame map-frame--missing"><div class="map-card"><span class="eyebrow olive">' . $eyebrow . '</span><strong>' . $title . '</strong><p>Inserisci la chiave API di Google Maps nelle impostazioni del plugin Vinifera Map Block.</p><p><small>' . $coords_label . ': ' . $coords . '</small></p></div></div>';
	}

	$embed_src = add_query_arg(
		[
			'key' => $api_key,
			'q' => $latitude . ',' . $longitude,
			'zoom' => $zoom,
		],
		'https://www.google.com/maps/embed/v1/place'
	);

	return '<div class="map-frame" data-lat="' . esc_attr( $latitude ) . '" data-lng="' . esc_attr( $longitude ) . '">
	<iframe class="map-embed" title="' . esc_attr( $title ) . '" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="' . esc_url( $embed_src ) . '"></iframe>
	<div class="map-road r1"></div>
	<div class="map-road r2"></div>
	<div class="map-road r3"></div>
	<div class="map-pin"><div class="map-pin-dot"></div></div>
	<span class="map-note">' . $pin_text . '</span>
	<div class="map-card">
		<span class="eyebrow olive">' . $eyebrow . '</span>
		<strong>' . $title . '</strong>
		<p>' . $address_text . '</p>
		<p><small>' . $coords_label . ': ' . $coords . '</small></p>
		<a href="' . $link_url . '" target="_blank" rel="noopener">' . $link_label . '</a>
	</div>
</div>';
}

function vinifera_map_block_register(): void {
	$editor_script_path = __DIR__ . '/editor.js';

	if ( file_exists( $editor_script_path ) ) {
		wp_register_script(
			'vinifera-map-block-editor',
			plugins_url( 'editor.js', __FILE__ ),
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render' ],
			filemtime( $editor_script_path ),
			true
		);
	}

	register_block_type( __DIR__, [
		'editor_script'   => 'vinifera-map-block-editor',
		'render_callback' => 'vinifera_map_block_render',
	] );
}
add_action( 'init', 'vinifera_map_block_register', 20 );

function vinifera_map_block_settings_page(): void {
	register_setting( 'vinifera_maps_settings', VINIFERA_MAPS_OPTION_API_KEY, [
		'type' => 'string',
		'sanitize_callback' => 'sanitize_text_field',
		'default' => '',
	] );

	add_settings_section(
		'vinifera_maps_section',
		'Google Maps',
		function () {
			echo '<p>Inserisci la chiave API per Google Maps Embed. Il blocco la usa per mostrare la mappa reale.</p>';
		},
		'vinifera-maps'
	);

	add_settings_field(
		VINIFERA_MAPS_OPTION_API_KEY,
		'API key',
		function () {
			$value = esc_attr( get_option( VINIFERA_MAPS_OPTION_API_KEY, '' ) );
			echo '<input type="password" class="regular-text" name="' . esc_attr( VINIFERA_MAPS_OPTION_API_KEY ) . '" value="' . $value . '" autocomplete="off" />';
			echo '<p class="description">La chiave resta salvata nelle impostazioni del plugin, non nel contenuto della pagina.</p>';
		},
		'vinifera-maps',
		'vinifera_maps_section'
	);
}
add_action( 'admin_init', 'vinifera_map_block_settings_page' );

function vinifera_map_block_menu(): void {
	add_options_page(
		'Vinifera Maps',
		'Vinifera Maps',
		'manage_options',
		'vinifera-maps',
		function () {
			echo '<div class="wrap"><h1>Vinifera Maps</h1><form method="post" action="options.php">';
			settings_fields( 'vinifera_maps_settings' );
			do_settings_sections( 'vinifera-maps' );
			submit_button();
			echo '</form></div>';
		}
	);
}
add_action( 'admin_menu', 'vinifera_map_block_menu' );

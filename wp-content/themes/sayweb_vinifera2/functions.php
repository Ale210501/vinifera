<?php
/**
 * Sayweb Vinifera theme setup.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Setup theme supports for block templates.
 */
function sayweb_vinifera_setup(): void {
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'woocommerce' );
	add_editor_style( 'style.css' );

	load_theme_textdomain( 'sayweb-vinifera', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'sayweb_vinifera_setup' );

/**
 * Register custom pattern categories.
 */
function sayweb_vinifera_register_pattern_categories(): void {
	register_block_pattern_category(
		'sayweb-vinifera',
		[ 'label' => __( 'Sayweb Vinifera', 'sayweb-vinifera' ) ]
	);
}
add_action( 'init', 'sayweb_vinifera_register_pattern_categories', 8 );

/**
 * Render callback for the custom hero visual block.
 */
function sayweb_vinifera_render_hero_visual( array $attributes = [] ): string {
	$defaults = [
		'mainImageUrl'    => 'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?auto=format&fit=crop&w=1200&q=80',
		'floaterImageUrl' => 'https://images.unsplash.com/photo-1485871981521-5b1fd3805eee?auto=format&fit=crop&w=900&q=80',
		'tapeValue'       => '10%',
		'tapeText'        => __( 'di sconto sul primo ordine', 'sayweb-vinifera' ),
	];

	$attributes = wp_parse_args( $attributes, $defaults );

	$main_url    = esc_url( (string) $attributes['mainImageUrl'] );
	$floater_url = esc_url( (string) $attributes['floaterImageUrl'] );
	$tape_value  = esc_html( (string) $attributes['tapeValue'] );
	$tape_text   = esc_html( (string) $attributes['tapeText'] );

	$wrapper_attributes = get_block_wrapper_attributes( [
		'class' => 'vinifera-hero-visual',
	] );

	return sprintf(
		'<div %1$s><div class="vinifera-hero-tape"><strong>%2$s</strong><span>%3$s</span></div><div class="vinifera-hero-img vinifera-hero-img-main"><img src="%4$s" alt="Interno enoteca" loading="lazy" decoding="async" /></div><div class="vinifera-hero-img vinifera-hero-img-floater"><img src="%5$s" alt="Degustazione vino" loading="lazy" decoding="async" /></div></div>',
		$wrapper_attributes,
		$tape_value,
		$tape_text,
		$main_url,
		$floater_url
	);
}

/**
 * Render callback for the hero tape badge (10% discount sticker).
 */
function sayweb_vinifera_render_hero_tape( array $attributes = [] ): string {
	$defaults = [
		'tapeValue' => '10%',
		'tapeText'  => __( 'di sconto sul primo ordine se ti iscrivi alla newsletter', 'sayweb-vinifera' ),
	];

	$attributes = wp_parse_args( $attributes, $defaults );

	$value = esc_html( (string) $attributes['tapeValue'] );
	$text  = esc_html( (string) $attributes['tapeText'] );

	$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => 'hero-tape' ] );

	return sprintf(
		'<div %1$s><span class="num">%2$s</span><p>%3$s</p></div>',
		$wrapper_attributes,
		$value,
		$text
	);
}

/**
 * Render callback for the header announcement block.
 */
function sayweb_vinifera_render_dynamic_header_banner( array $attributes = [] ): string {
	$defaults = [
		'announcePrefix'     => 'Spedizione gratuita oltre ',
		'announceHighlight1' => '100 EUR',
		'announceMiddle'     => ' in tutta Italia',
		'announceLocal'      => 'Consegna in giornata nei comuni limitrofi a Locorotondo',
		'announceSuffix'     => '',
		'announceHighlight2' => '600+ etichette selezionate',
	];

	$attributes = wp_parse_args( $attributes, $defaults );

	$announce_prefix = esc_html( (string) $attributes['announcePrefix'] );
	$announce_h1     = esc_html( (string) $attributes['announceHighlight1'] );
	$announce_middle = esc_html( (string) $attributes['announceMiddle'] );
	$announce_local  = esc_html( (string) $attributes['announceLocal'] );
	$announce_suffix = esc_html( (string) $attributes['announceSuffix'] );
	$announce_h2     = esc_html( (string) $attributes['announceHighlight2'] );

	return '
<div class="announce">
	<p>' . $announce_prefix . '<span>' . $announce_h1 . '</span>' . $announce_middle . '<span class="sep">•</span>' . $announce_local . $announce_suffix . '<span class="sep">•</span><span>' . $announce_h2 . '</span></p>
</div>
';
}

/**
 * Render callback for the header brand block.
 */
function sayweb_vinifera_render_dynamic_header_brand(): string {
	return '<a href="/" class="brand"><img src="vinifera-logo.png" alt="Vinifera Enoteca"></a>';
}

/**
 * Render callback for the header shell wrapper.
 */
function sayweb_vinifera_render_dynamic_header_shell( array $attributes = [], string $content = '' ): string {
	return '<header class="topbar"><div class="wrap">' . $content . '</div></header>';
}

/**
 * Render callback for the header nav wrapper.
 */
function sayweb_vinifera_render_dynamic_header_nav( array $attributes = [], string $content = '' ): string {
	return '<nav class="topnav">' . $content . '</nav>';
}

/**
 * Render callback for WooCommerce category filters.
 */
function sayweb_vinifera_render_category_filters(): string {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return '';
	}

	$blocks = [
		'<!-- wp:woocommerce/active-filters /-->',
		'<!-- wp:woocommerce/price-filter /-->',
	];

	if ( function_exists( 'wc_get_attribute_taxonomies' ) ) {
		$attribute_taxonomies = wc_get_attribute_taxonomies();

		if ( ! empty( $attribute_taxonomies ) ) {
			foreach ( $attribute_taxonomies as $attribute_taxonomy ) {
				$attribute_id = isset( $attribute_taxonomy->attribute_id ) ? (int) $attribute_taxonomy->attribute_id : 0;

				if ( $attribute_id <= 0 ) {
					continue;
				}

				$blocks[] = '<!-- wp:woocommerce/attribute-filter ' . wp_json_encode(
					[
						'attributeId' => $attribute_id,
						'queryType'   => 'or',
					]
				) . ' /-->';
			}
		}
	}

	$blocks[] = '<!-- wp:woocommerce/rating-filter /-->';
	$blocks[] = '<!-- wp:woocommerce/stock-filter /-->';

	return do_blocks( implode( "\n", $blocks ) );
}

/**
 * Render callback for the main header block.
 */
function sayweb_vinifera_render_dynamic_header( array $attributes = [] ): string {
	$defaults = [
		'rossiLabel'     => 'Rossi',
		'bianchiLabel'   => 'Bianchi',
		'spumantiLabel'  => 'Spumanti',
		'rosatiLabel'    => 'Rosati',
		'km0Label'       => 'Km 0',
		'foodLabel'      => 'Food locale',
		'aboutLabel'     => 'Chi siamo',
		'contactLabel'   => 'Contattaci',
		'waLabel'        => 'Parla con Stefano',
		'cartCount'      => '0',
	];

	$attributes = wp_parse_args( $attributes, $defaults );

	$rossi_label    = esc_html( (string) $attributes['rossiLabel'] );
	$bianchi_label  = esc_html( (string) $attributes['bianchiLabel'] );
	$spumanti_label = esc_html( (string) $attributes['spumantiLabel'] );
	$rosati_label   = esc_html( (string) $attributes['rosatiLabel'] );
	$km0_label      = esc_html( (string) $attributes['km0Label'] );
	$food_label     = esc_html( (string) $attributes['foodLabel'] );
	$about_label    = esc_html( (string) $attributes['aboutLabel'] );
	$contact_label  = esc_html( (string) $attributes['contactLabel'] );
	$wa_label       = esc_html( (string) $attributes['waLabel'] );
	$cart_count     = esc_html( (string) $attributes['cartCount'] );

	return '
<div class="header-main">
	<nav class="topnav">
		<div class="nav-item has-mega">
			<a href="#">' . $rossi_label . '</a>
			<div class="mega">
				<div class="wrap">
					<div class="mega-grid">
						<div class="mega-col">
							<span class="mega-title">Regione</span>
							<a href="#">Sicilia <span class="mega-badge">Trend</span></a>
							<a href="#">Puglia <span class="mega-badge">Trend</span></a>
							<a href="#">Campania <span class="mega-badge">Trend</span></a>
							<a href="#">Sardegna <span class="mega-badge">Trend</span></a>
							<a href="#">Veneto</a>
							<a href="#">Piemonte</a>
							<a href="#">Toscana</a>
							<a href="#">Trentino Alto Adige</a>
							<a href="#">Emilia Romagna</a>
							<a href="#">Lombardia</a>
							<a href="#">Friuli Venezia Giulia</a>
							<a href="#">Abruzzo</a>
							<a href="#">Valle d\'Aosta</a>
							<a href="#">Marche</a>
							<a href="#">Lazio</a>
							<a href="#" class="mega-all">Vedi tutti</a>
						</div>
						<div class="mega-col">
							<span class="mega-title">Tipologia</span>
							<a href="#">Valpolicella</a>
							<a href="#">Nebbiolo</a>
							<a href="#">Brunello</a>
							<a href="#">Amarone</a>
							<a href="#">Barolo</a>
							<a href="#">Bolgheri</a>
							<a href="#">Chianti Classico</a>
							<a href="#">Barbera</a>
							<a href="#">Lagrein</a>
							<a href="#">Sangiovese</a>
							<a href="#">Primitivo</a>
							<a href="#">Supertuscan</a>
							<a href="#">Montepulciano</a>
							<a href="#">Barbaresco</a>
							<a href="#" class="mega-all">Vedi tutti</a>
						</div>
						<div class="mega-col">
							<span class="mega-title">Abbinamento</span>
							<a href="#">Carni bianche</a>
							<a href="#">Vini da grigliata</a>
							<a href="#">Carni rosse</a>
							<a href="#">Formaggi</a>
							<a href="#">Salumi</a>
							<a href="#">Vini da dessert</a>
							<a href="#" class="mega-all">Vedi tutti</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="nav-item has-mega">
			<a href="#">' . $bianchi_label . '</a>
			<div class="mega">
				<div class="wrap">
					<div class="mega-grid">
						<div class="mega-col">
							<span class="mega-title">Regione</span>
							<a href="#">Sicilia <span class="mega-badge">Trend</span></a>
							<a href="#">Puglia <span class="mega-badge">Trend</span></a>
							<a href="#">Campania <span class="mega-badge">Trend</span></a>
							<a href="#">Friuli Venezia Giulia <span class="mega-badge">Trend</span></a>
							<a href="#">Trentino Alto Adige</a>
							<a href="#">Veneto</a>
							<a href="#">Piemonte</a>
							<a href="#">Toscana</a>
							<a href="#">Marche</a>
							<a href="#">Abruzzo</a>
							<a href="#">Lombardia</a>
							<a href="#">Sardegna</a>
							<a href="#">Lazio</a>
							<a href="#">Valle d\'Aosta</a>
							<a href="#">Emilia Romagna</a>
							<a href="#" class="mega-all">Vedi tutti</a>
						</div>
						<div class="mega-col">
							<span class="mega-title">Tipologia</span>
							<a href="#">Vermentino</a>
							<a href="#">Fiano</a>
							<a href="#">Greco di Tufo</a>
							<a href="#">Falanghina</a>
							<a href="#">Chardonnay</a>
							<a href="#">Sauvignon</a>
							<a href="#">Verdicchio</a>
							<a href="#">Gavi</a>
							<a href="#">Soave</a>
							<a href="#">Friulano</a>
							<a href="#">Pecorino</a>
							<a href="#">Ribolla Gialla</a>
							<a href="#">Grillo</a>
							<a href="#">Trebbiano</a>
							<a href="#" class="mega-all">Vedi tutti</a>
						</div>
						<div class="mega-col">
							<span class="mega-title">Abbinamento</span>
							<a href="#">Antipasti di mare</a>
							<a href="#">Pesce</a>
							<a href="#">Carni bianche</a>
							<a href="#">Formaggi freschi</a>
							<a href="#">Aperitivo</a>
							<a href="#">Vini da dessert</a>
							<a href="#" class="mega-all">Vedi tutti</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="nav-item has-mega">
			<a href="#">' . $spumanti_label . '</a>
			<div class="mega">
				<div class="wrap">
					<div class="mega-grid">
						<div class="mega-col">
							<span class="mega-title">Regione</span>
							<a href="#">Lombardia <span class="mega-badge">Trend</span></a>
							<a href="#">Trentino Alto Adige <span class="mega-badge">Trend</span></a>
							<a href="#">Veneto <span class="mega-badge">Trend</span></a>
							<a href="#">Champagne <span class="mega-badge">Trend</span></a>
							<a href="#">Piemonte</a>
							<a href="#">Emilia Romagna</a>
							<a href="#">Puglia</a>
							<a href="#">Sicilia</a>
							<a href="#">Friuli Venezia Giulia</a>
							<a href="#">Abruzzo</a>
							<a href="#">Marche</a>
							<a href="#">Sardegna</a>
							<a href="#" class="mega-all">Vedi tutti</a>
						</div>
						<div class="mega-col">
							<span class="mega-title">Tipologia</span>
							<a href="#">Franciacorta</a>
							<a href="#">Trento DOC</a>
							<a href="#">Prosecco</a>
							<a href="#">Champagne</a>
							<a href="#">Metodo Classico</a>
							<a href="#">Lambrusco</a>
							<a href="#">Asti Spumante</a>
							<a href="#">Pas Dose</a>
							<a href="#">Blanc de Blancs</a>
							<a href="#">Spumante Rose</a>
							<a href="#" class="mega-all">Vedi tutti</a>
						</div>
						<div class="mega-col">
							<span class="mega-title">Abbinamento</span>
							<a href="#">Aperitivo</a>
							<a href="#">Antipasti di mare</a>
							<a href="#">Fritti</a>
							<a href="#">Pesce</a>
							<a href="#">Brindisi e regalo</a>
							<a href="#">Vini da dessert</a>
							<a href="#" class="mega-all">Vedi tutti</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="nav-item has-mega">
			<a href="#">' . $rosati_label . '</a>
			<div class="mega">
				<div class="wrap">
					<div class="mega-grid">
						<div class="mega-col">
							<span class="mega-title">Regione</span>
							<a href="#">Puglia <span class="mega-badge">Trend</span></a>
							<a href="#">Abruzzo <span class="mega-badge">Trend</span></a>
							<a href="#">Sicilia <span class="mega-badge">Trend</span></a>
							<a href="#">Calabria <span class="mega-badge">Trend</span></a>
							<a href="#">Veneto</a>
							<a href="#">Toscana</a>
							<a href="#">Campania</a>
							<a href="#">Provenza</a>
							<a href="#">Lombardia</a>
							<a href="#">Sardegna</a>
							<a href="#" class="mega-all">Vedi tutti</a>
						</div>
						<div class="mega-col">
							<span class="mega-title">Tipologia</span>
							<a href="#">Cerasuolo d\'Abruzzo</a>
							<a href="#">Bardolino Chiaretto</a>
							<a href="#">Negroamaro Rosato</a>
							<a href="#">Salento Rosato</a>
							<a href="#">Provenza</a>
							<a href="#">Etna Rosato</a>
							<a href="#">Ciro Rosato</a>
							<a href="#">Bombino Nero</a>
							<a href="#" class="mega-all">Vedi tutti</a>
						</div>
						<div class="mega-col">
							<span class="mega-title">Abbinamento</span>
							<a href="#">Aperitivo</a>
							<a href="#">Pizza</a>
							<a href="#">Carni bianche</a>
							<a href="#">Pesce</a>
							<a href="#">Salumi</a>
							<a href="#">Formaggi freschi</a>
							<a href="#" class="mega-all">Vedi tutti</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<a href="#">' . $km0_label . '</a>
		<a href="#">' . $food_label . '</a>
		<a href="#">' . $about_label . '</a>
		<a href="#">' . $contact_label . '</a>
	</nav>
	<div class="topactions">
		<button class="icobtn" aria-label="Cerca">
			<svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"></circle><line x1="16.5" y1="16.5" x2="21" y2="21"></line></svg>
		</button>
		<button class="icobtn" aria-label="Account">
			<svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"></path></svg>
		</button>
		<button class="icobtn" aria-label="Carrello">
			<svg viewBox="0 0 24 24"><path d="M6 7h12l-1.2 10a2 2 0 0 1-2 1.8H9.2a2 2 0 0 1-2-1.8L6 7z"></path><path d="M9 7V5a3 3 0 0 1 6 0v2"></path></svg>
			<span class="cart-count">' . $cart_count . '</span>
		</button>
		<a href="#" class="wa">' . $wa_label . '</a>
	</div>
</div>';
}

/**
 * Render a single mega-menu column.
 */
function sayweb_vinifera_render_header_mega_column( string $title, string $items, int $featured_count = 0 ): string {
	$lines = preg_split( '/\r\n|\r|\n/', trim( $items ) );
	$lines = is_array( $lines ) ? array_values( array_filter( $lines, static function ( $line ) {
		return '' !== trim( (string) $line );
	} ) ) : [];

	$html = '<div class="mega-col"><span class="mega-title">' . esc_html( $title ) . '</span>';

	foreach ( $lines as $index => $line ) {
		$badge = $index < $featured_count ? ' <span class="mega-badge">Trend</span>' : '';
		$html .= '<a href="#">' . esc_html( (string) $line ) . $badge . '</a>';
	}

	$html .= '<a href="#" class="mega-all">Vedi tutti</a></div>';

	return $html;
}

/**
 * Render a mega menu category block.
 */
function sayweb_vinifera_render_header_mega_category( array $attributes = [], array $defaults = [] ): string {
	$attributes = wp_parse_args( $attributes, $defaults );

	$label = esc_html( (string) $attributes['label'] );

	return '<div class="nav-item has-mega"><a href="#">' . $label . '</a><div class="mega"><div class="wrap"><div class="mega-grid">'
		. sayweb_vinifera_render_header_mega_column(
			(string) $attributes['col1Title'],
			(string) $attributes['col1Items'],
			(int) ( $attributes['col1FeaturedCount'] ?? 0 )
		)
		. sayweb_vinifera_render_header_mega_column(
			(string) $attributes['col2Title'],
			(string) $attributes['col2Items'],
			(int) ( $attributes['col2FeaturedCount'] ?? 0 )
		)
		. sayweb_vinifera_render_header_mega_column(
			(string) $attributes['col3Title'],
			(string) $attributes['col3Items'],
			(int) ( $attributes['col3FeaturedCount'] ?? 0 )
		)
		. '</div></div></div></div>';
}

/**
 * Render the simple utility links block.
 */
function sayweb_vinifera_render_header_links( array $attributes = [] ): string {
	$defaults = [
		'km0Label'     => 'Km 0',
		'km0Url'       => '#',
		'foodLabel'    => 'Food locale',
		'foodUrl'      => '#',
		'aboutLabel'   => 'Chi siamo',
		'aboutUrl'     => '#',
		'contactLabel' => 'Contattaci',
		'contactUrl'   => '#',
	];

	$attributes = wp_parse_args( $attributes, $defaults );
	$km0_url     = esc_url( (string) $attributes['km0Url'] );
	$food_url    = esc_url( (string) $attributes['foodUrl'] );
	$about_url   = esc_url( (string) $attributes['aboutUrl'] );
	$contact_url = esc_url( (string) $attributes['contactUrl'] );

	return '<div class="nav-links">'
		. '<a href="' . $km0_url . '">' . esc_html( (string) $attributes['km0Label'] ) . '</a>'
		. '<a href="' . $food_url . '">' . esc_html( (string) $attributes['foodLabel'] ) . '</a>'
		. '<a href="' . $about_url . '">' . esc_html( (string) $attributes['aboutLabel'] ) . '</a>'
		. '<a href="' . $contact_url . '">' . esc_html( (string) $attributes['contactLabel'] ) . '</a>'
		. '</div>';
}

/**
 * Render the header actions block.
 */
function sayweb_vinifera_render_header_actions( array $attributes = [] ): string {
	$defaults = [
		'waLabel'   => 'Parla con Stefano',
		'waUrl'     => '#',
		'cartCount' => '0',
	];

	$attributes = wp_parse_args( $attributes, $defaults );
	$wa_label = esc_html( (string) $attributes['waLabel'] );
	$wa_url = esc_url( (string) $attributes['waUrl'] );
	$cart_count = esc_html( (string) $attributes['cartCount'] );

	return '
<div class="topactions">
	<button class="icobtn" aria-label="Cerca">
		<svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"></circle><line x1="16.5" y1="16.5" x2="21" y2="21"></line></svg>
	</button>
	<button class="icobtn" aria-label="Account">
		<svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"></path></svg>
	</button>
	<button class="icobtn" aria-label="Carrello">
		<svg viewBox="0 0 24 24"><path d="M6 7h12l-1.2 10a2 2 0 0 1-2 1.8H9.2a2 2 0 0 1-2-1.8L6 7z"></path><path d="M9 7V5a3 3 0 0 1 6 0v2"></path></svg>
		<span class="cart-count">' . $cart_count . '</span>
	</button>
	<a href="' . $wa_url . '" class="wa">' . $wa_label . '</a>
</div>';
}

/**
 * Render callback for dynamic footer block.
 */
function sayweb_vinifera_render_dynamic_footer( array $attributes = [] ): string {
	$defaults = [
		'brandDescription' => 'Enoteca con store fisico nel centro storico di Locorotondo e spedizione in tutta Italia. 600 etichette selezionate, food artigianale, degustazioni guidate. Dal 2020.',
		'addressLabel'     => 'Indirizzo',
		'addressText'      => "Via Eroi di Dogali 12, 70010\nLocorotondo (BA), Puglia",
		'hoursLabel'       => 'Orari',
		'hoursText'        => 'Lun-Dom · 10:00-23:00',
		'contactsLabel'    => 'Contatti',
		'contactsText'     => "+39 080 123 4567\ninfo@viniferaenoteca.it",
		'copyrightText'    => '© 2026 Vinifera Enoteca · P.IVA 0000000000',
		'legalText'        => 'Privacy · Cookie · Termini · Spedizioni e resi',
	];

	$attributes = wp_parse_args( $attributes, $defaults );

	$brand_description = esc_html( (string) $attributes['brandDescription'] );
	$address_label     = esc_html( (string) $attributes['addressLabel'] );
	$address_text      = nl2br( esc_html( (string) $attributes['addressText'] ) );
	$hours_label       = esc_html( (string) $attributes['hoursLabel'] );
	$hours_text        = esc_html( (string) $attributes['hoursText'] );
	$contacts_label    = esc_html( (string) $attributes['contactsLabel'] );
	$contacts_text     = nl2br( esc_html( (string) $attributes['contactsText'] ) );
	$copyright_text    = esc_html( (string) $attributes['copyrightText'] );
	$legal_text        = esc_html( (string) $attributes['legalText'] );

	return '
<footer class="footer">
	<div class="wrap">
		<div class="footer-top">
			<div class="footer-brand">
				<img src="vinifera-logo.png" alt="Vinifera Enoteca">
				<p>' . $brand_description . '</p>
				<div class="footer-social">
					<a href="#">IG</a>
					<a href="#">FB</a>
					<a href="#">TT</a>
					<a href="#">YT</a>
				</div>
			</div>

			<div class="footer-col">
				<h4>Lo shop</h4>
				<ul>
					<li><a href="#">Tutto il catalogo</a></li>
					<li><a href="#">Vini per regione</a></li>
					<li><a href="#">Wine box e regali</a></li>
					<li><a href="#">Food e abbinamenti</a></li>
					<li><a href="#">Promozioni</a></li>
				</ul>
			</div>
			<div class="footer-col">
				<h4>L\'enoteca</h4>
				<ul>
					<li><a href="#">Chi siamo</a></li>
					<li><a href="#">Esperienze e degustazioni</a></li>
					<li><a href="#">Personal Sommelier</a></li>
					<li><a href="#">Storie dal vicolo</a></li>
					<li><a href="#">Lavora con noi</a></li>
				</ul>
			</div>
			<div class="footer-col">
				<h4>Vieni a trovarci</h4>
				<p>
					<strong>' . $address_label . '</strong>
					' . $address_text . '
					<strong>' . $hours_label . '</strong>
					' . $hours_text . '
					<strong>' . $contacts_label . '</strong>
					' . $contacts_text . '
				</p>
			</div>
		</div>
		<div class="footer-bottom">
			<span>' . $copyright_text . '</span>
			<span>' . $legal_text . '</span>
		</div>
	</div>
</footer>';
}

/**
 * Render callback for contact form card block.
 */
function sayweb_vinifera_render_contact_form(): string {
	return '
<form class="cform" onsubmit="event.preventDefault()">
	<p class="eyebrow">Scrivici</p>
	<h2>Raccontaci <em>cosa ti serve</em></h2>
	<p>Compila il form e ti rispondiamo il prima possibile. Se hai fretta, WhatsApp resta la via piu rapida.</p>
	<div class="row">
		<div>
			<label for="nome">Nome</label>
			<input id="nome" type="text" placeholder="Il tuo nome" required>
		</div>
		<div>
			<label for="tel">Telefono</label>
			<input id="tel" type="tel" placeholder="Facoltativo">
		</div>
	</div>
	<label for="email">Email</label>
	<input id="email" type="email" placeholder="tua@email.it" required>
	<label for="oggetto">Di cosa si tratta</label>
	<select id="oggetto">
		<option>Un consiglio sul vino</option>
		<option>Un ordine o una disponibilita</option>
		<option>Cesti e regali</option>
		<option>Fornitura per ristoranti e locali</option>
		<option>Altro</option>
	</select>
	<label for="msg">Messaggio</label>
	<textarea id="msg" placeholder="Scrivici pure con parole tue" required></textarea>
	<div class="privacy">
		<input type="checkbox" id="priv" required>
		<label for="priv">Acconsento al trattamento dei dati come da Privacy Policy</label>
	</div>
	<button type="submit">Invia il messaggio →</button>
</form>';
}

/**
 * Render callback for contact map mock block.
 */
function sayweb_vinifera_render_contact_map(): string {
	return '
<div class="map-frame">
	<div class="map-road r1"></div>
	<div class="map-road r2"></div>
	<div class="map-road r3"></div>
	<div class="map-pin"><div class="map-pin-dot"></div></div>
	<span class="map-note">Mappa segnaposto</span>
	<div class="map-card">
		<span class="eyebrow olive">Vinifera Enoteca</span>
		<strong>Largo Piave 10/11</strong>
		<p>70010 Locorotondo (BA), nel centro storico della Valle d\'Itria.</p>
		<a href="https://www.google.com/maps/search/?api=1&amp;query=Largo+Piave+10%2F11+70010+Locorotondo" target="_blank" rel="noopener">Apri in Google Maps →</a>
	</div>
</div>';
}

/**
 * Render the Rossi mega-menu block.
 */
function sayweb_vinifera_render_dynamic_header_rossi( array $attributes = [] ): string {
	$defaults = [
		'label'             => 'Rossi',
		'col1Title'         => 'Regione',
		'col1Items'         => "Sicilia\nPuglia\nCampania\nSardegna\nVeneto\nPiemonte\nToscana\nTrentino Alto Adige\nEmilia Romagna\nLombardia\nFriuli Venezia Giulia\nAbruzzo\nValle d'Aosta\nMarche\nLazio",
		'col1FeaturedCount' => '4',
		'col2Title'         => 'Tipologia',
		'col2Items'         => "Valpolicella\nNebbiolo\nBrunello\nAmarone\nBarolo\nBolgheri\nChianti Classico\nBarbera\nLagrein\nSangiovese\nPrimitivo\nSupertuscan\nMontepulciano\nBarbaresco",
		'col2FeaturedCount' => '0',
		'col3Title'         => 'Abbinamento',
		'col3Items'         => "Carni bianche\nVini da grigliata\nCarni rosse\nFormaggi\nSalumi\nVini da dessert",
		'col3FeaturedCount' => '0',
	];

	return sayweb_vinifera_render_header_mega_category( $attributes, $defaults );
}

/**
 * Render the Bianchi mega-menu block.
 */
function sayweb_vinifera_render_dynamic_header_bianchi( array $attributes = [] ): string {
	$defaults = [
		'label'             => 'Bianchi',
		'col1Title'         => 'Regione',
		'col1Items'         => "Sicilia\nPuglia\nCampania\nFriuli Venezia Giulia\nTrentino Alto Adige\nVeneto\nPiemonte\nToscana\nMarche\nAbruzzo\nLombardia\nSardegna\nLazio\nValle d'Aosta\nEmilia Romagna",
		'col1FeaturedCount' => '4',
		'col2Title'         => 'Tipologia',
		'col2Items'         => "Vermentino\nFiano\nGreco di Tufo\nFalanghina\nChardonnay\nSauvignon\nVerdicchio\nGavi\nSoave\nFriulano\nPecorino\nRibolla Gialla\nGrillo\nTrebbiano",
		'col2FeaturedCount' => '0',
		'col3Title'         => 'Abbinamento',
		'col3Items'         => "Antipasti di mare\nPesce\nCarni bianche\nFormaggi freschi\nAperitivo\nVini da dessert",
		'col3FeaturedCount' => '0',
	];

	return sayweb_vinifera_render_header_mega_category( $attributes, $defaults );
}

/**
 * Render the Spumanti mega-menu block.
 */
function sayweb_vinifera_render_dynamic_header_spumanti( array $attributes = [] ): string {
	$defaults = [
		'label'             => 'Spumanti',
		'col1Title'         => 'Regione',
		'col1Items'         => "Lombardia\nTrentino Alto Adige\nVeneto\nChampagne\nPiemonte\nEmilia Romagna\nPuglia\nSicilia\nFriuli Venezia Giulia\nAbruzzo\nMarche\nSardegna",
		'col1FeaturedCount' => '4',
		'col2Title'         => 'Tipologia',
		'col2Items'         => "Franciacorta\nTrento DOC\nProsecco\nChampagne\nMetodo Classico\nLambrusco\nAsti Spumante\nPas Dose\nBlanc de Blancs\nSpumante Rose",
		'col2FeaturedCount' => '0',
		'col3Title'         => 'Abbinamento',
		'col3Items'         => "Aperitivo\nAntipasti di mare\nFritti\nPesce\nBrindisi e regalo\nVini da dessert",
		'col3FeaturedCount' => '0',
	];

	return sayweb_vinifera_render_header_mega_category( $attributes, $defaults );
}

/**
 * Render the Rosati mega-menu block.
 */
function sayweb_vinifera_render_dynamic_header_rosati( array $attributes = [] ): string {
	$defaults = [
		'label'             => 'Rosati',
		'col1Title'         => 'Regione',
		'col1Items'         => "Puglia\nAbruzzo\nSicilia\nCalabria\nVeneto\nToscana\nCampania\nProvenza\nLombardia\nSardegna",
		'col1FeaturedCount' => '4',
		'col2Title'         => 'Tipologia',
		'col2Items'         => "Cerasuolo d'Abruzzo\nBardolino Chiaretto\nNegroamaro Rosato\nSalento Rosato\nProvenza\nEtna Rosato\nCiro Rosato\nBombino Nero",
		'col2FeaturedCount' => '0',
		'col3Title'         => 'Abbinamento',
		'col3Items'         => "Aperitivo\nPizza\nCarni bianche\nPesce\nSalumi\nFormaggi freschi",
		'col3FeaturedCount' => '0',
	];

	return sayweb_vinifera_render_header_mega_category( $attributes, $defaults );
}

/**
 * Register custom blocks used by theme patterns.
 */
function sayweb_vinifera_register_blocks(): void {
	$blocks_dir = get_stylesheet_directory();
	$blocks_uri = get_stylesheet_directory_uri();

	$hero_visual_editor_file    = $blocks_dir . '/blocks/hero-visual/edit.js';
	$dynamic_layout_editor_file = $blocks_dir . '/blocks/dynamic-layout/edit.js';
	$hero_tape_editor_file      = $blocks_dir . '/blocks/hero-tape/edit.js';

	if ( file_exists( $hero_tape_editor_file ) ) {
		wp_register_script(
			'sayweb-vinifera-hero-tape-editor',
			$blocks_uri . '/blocks/hero-tape/edit.js',
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-server-side-render' ],
			filemtime( $hero_tape_editor_file ),
			false
		);
	}

	if ( file_exists( $hero_visual_editor_file ) ) {
		wp_register_script(
			'sayweb-vinifera-hero-visual-editor',
			$blocks_uri . '/blocks/hero-visual/edit.js',
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-i18n' ],
			filemtime( $hero_visual_editor_file ),
			false
		);
	}

	if ( file_exists( $dynamic_layout_editor_file ) ) {
		wp_register_script(
			'sayweb-vinifera-dynamic-layout-editor',
			$blocks_uri . '/blocks/dynamic-layout/edit.js',
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-server-side-render' ],
			filemtime( $dynamic_layout_editor_file ),
			false
		);
	}

	register_block_type( 'sayweb-vinifera/hero-visual', [
		'api_version'     => 3,
		'render_callback' => 'sayweb_vinifera_render_hero_visual',
		'editor_script'   => 'sayweb-vinifera-hero-visual-editor',
		'attributes'      => [
			'mainImageUrl'    => [ 'type' => 'string' ],
			'floaterImageUrl' => [ 'type' => 'string' ],
			'tapeValue'       => [ 'type' => 'string' ],
			'tapeText'        => [ 'type' => 'string' ],
		],
	] );

	register_block_type( 'sayweb-vinifera/hero-tape', [
		'api_version'     => 3,
		'render_callback' => 'sayweb_vinifera_render_hero_tape',
		'editor_script'   => 'sayweb-vinifera-hero-tape-editor',
		'attributes'      => [
			'tapeValue' => [ 'type' => 'string', 'default' => '10%' ],
			'tapeText'  => [ 'type' => 'string', 'default' => 'di sconto sul primo ordine se ti iscrivi alla newsletter' ],
		],
	] );

	register_block_type( 'sayweb-vinifera/dynamic-header-banner', [
		'api_version'     => 3,
		'render_callback' => 'sayweb_vinifera_render_dynamic_header_banner',
		'editor_script'   => 'sayweb-vinifera-dynamic-layout-editor',
		'attributes'      => [
			'announcePrefix'     => [ 'type' => 'string', 'default' => 'Spedizione gratuita oltre ' ],
			'announceHighlight1' => [ 'type' => 'string', 'default' => '100 EUR' ],
			'announceMiddle'     => [ 'type' => 'string', 'default' => ' in tutta Italia' ],
			'announceLocal'      => [ 'type' => 'string', 'default' => 'Consegna in giornata nei comuni limitrofi a Locorotondo' ],
			'announceSuffix'     => [ 'type' => 'string', 'default' => '' ],
			'announceHighlight2' => [ 'type' => 'string', 'default' => '600+ etichette selezionate' ],
		],
	] );

	register_block_type( 'sayweb-vinifera/dynamic-header-brand', [
		'api_version'     => 3,
		'render_callback' => 'sayweb_vinifera_render_dynamic_header_brand',
		'editor_script'   => 'sayweb-vinifera-dynamic-layout-editor',
		'attributes'      => [
			'brandUrl' => [ 'type' => 'string', 'default' => '/' ],
		],
	] );

	register_block_type( 'sayweb-vinifera/dynamic-header-rossi', [
		'api_version'     => 3,
		'render_callback' => 'sayweb_vinifera_render_dynamic_header_rossi',
		'editor_script'   => 'sayweb-vinifera-dynamic-layout-editor',
		'attributes'      => [
			'label'             => [ 'type' => 'string', 'default' => 'Rossi' ],
			'col1Title'         => [ 'type' => 'string', 'default' => 'Regione' ],
			'col1Items'         => [ 'type' => 'string', 'default' => "Sicilia\nPuglia\nCampania\nSardegna\nVeneto\nPiemonte\nToscana\nTrentino Alto Adige\nEmilia Romagna\nLombardia\nFriuli Venezia Giulia\nAbruzzo\nValle d'Aosta\nMarche\nLazio" ],
			'col1FeaturedCount' => [ 'type' => 'string', 'default' => '4' ],
			'col2Title'         => [ 'type' => 'string', 'default' => 'Tipologia' ],
			'col2Items'         => [ 'type' => 'string', 'default' => "Valpolicella\nNebbiolo\nBrunello\nAmarone\nBarolo\nBolgheri\nChianti Classico\nBarbera\nLagrein\nSangiovese\nPrimitivo\nSupertuscan\nMontepulciano\nBarbaresco" ],
			'col2FeaturedCount' => [ 'type' => 'string', 'default' => '0' ],
			'col3Title'         => [ 'type' => 'string', 'default' => 'Abbinamento' ],
			'col3Items'         => [ 'type' => 'string', 'default' => "Carni bianche\nVini da grigliata\nCarni rosse\nFormaggi\nSalumi\nVini da dessert" ],
			'col3FeaturedCount' => [ 'type' => 'string', 'default' => '0' ],
		],
	] );

	register_block_type( 'sayweb-vinifera/dynamic-header-shell', [
		'api_version'     => 3,
		'render_callback' => 'sayweb_vinifera_render_dynamic_header_shell',
		'editor_script'   => 'sayweb-vinifera-dynamic-layout-editor',
	] );

	register_block_type( 'sayweb-vinifera/dynamic-header-nav', [
		'api_version'     => 3,
		'render_callback' => 'sayweb_vinifera_render_dynamic_header_nav',
		'editor_script'   => 'sayweb-vinifera-dynamic-layout-editor',
	] );

	register_block_type( 'sayweb-vinifera/dynamic-header-bianchi', [
		'api_version'     => 3,
		'render_callback' => 'sayweb_vinifera_render_dynamic_header_bianchi',
		'editor_script'   => 'sayweb-vinifera-dynamic-layout-editor',
		'attributes'      => [
			'label'               => [ 'type' => 'string', 'default' => 'Bianchi' ],
			'col1Title'           => [ 'type' => 'string', 'default' => 'Regione' ],
			'col1Items'           => [ 'type' => 'string', 'default' => "Sicilia\nPuglia\nCampania\nFriuli Venezia Giulia\nTrentino Alto Adige\nVeneto\nPiemonte\nToscana\nMarche\nAbruzzo\nLombardia\nSardegna\nLazio\nValle d'Aosta\nEmilia Romagna" ],
			'col1FeaturedCount'   => [ 'type' => 'string', 'default' => '4' ],
			'col2Title'           => [ 'type' => 'string', 'default' => 'Tipologia' ],
			'col2Items'           => [ 'type' => 'string', 'default' => "Vermentino\nFiano\nGreco di Tufo\nFalanghina\nChardonnay\nSauvignon\nVerdicchio\nGavi\nSoave\nFriulano\nPecorino\nRibolla Gialla\nGrillo\nTrebbiano" ],
			'col2FeaturedCount'   => [ 'type' => 'string', 'default' => '0' ],
			'col3Title'           => [ 'type' => 'string', 'default' => 'Abbinamento' ],
			'col3Items'           => [ 'type' => 'string', 'default' => "Antipasti di mare\nPesce\nCarni bianche\nFormaggi freschi\nAperitivo\nVini da dessert" ],
			'col3FeaturedCount'   => [ 'type' => 'string', 'default' => '0' ],
		],
	] );

	register_block_type( 'sayweb-vinifera/dynamic-header-spumanti', [
		'api_version'     => 3,
		'render_callback' => 'sayweb_vinifera_render_dynamic_header_spumanti',
		'editor_script'   => 'sayweb-vinifera-dynamic-layout-editor',
		'attributes'      => [
			'label'               => [ 'type' => 'string', 'default' => 'Spumanti' ],
			'col1Title'           => [ 'type' => 'string', 'default' => 'Regione' ],
			'col1Items'           => [ 'type' => 'string', 'default' => "Lombardia\nTrentino Alto Adige\nVeneto\nChampagne\nPiemonte\nEmilia Romagna\nPuglia\nSicilia\nFriuli Venezia Giulia\nAbruzzo\nMarche\nSardegna" ],
			'col1FeaturedCount'   => [ 'type' => 'string', 'default' => '4' ],
			'col2Title'           => [ 'type' => 'string', 'default' => 'Tipologia' ],
			'col2Items'           => [ 'type' => 'string', 'default' => "Franciacorta\nTrento DOC\nProsecco\nChampagne\nMetodo Classico\nLambrusco\nAsti Spumante\nPas Dose\nBlanc de Blancs\nSpumante Rose" ],
			'col2FeaturedCount'   => [ 'type' => 'string', 'default' => '0' ],
			'col3Title'           => [ 'type' => 'string', 'default' => 'Abbinamento' ],
			'col3Items'           => [ 'type' => 'string', 'default' => "Aperitivo\nAntipasti di mare\nFritti\nPesce\nBrindisi e regalo\nVini da dessert" ],
			'col3FeaturedCount'   => [ 'type' => 'string', 'default' => '0' ],
		],
	] );

	register_block_type( 'sayweb-vinifera/dynamic-header-rosati', [
		'api_version'     => 3,
		'render_callback' => 'sayweb_vinifera_render_dynamic_header_rosati',
		'editor_script'   => 'sayweb-vinifera-dynamic-layout-editor',
		'attributes'      => [
			'label'               => [ 'type' => 'string', 'default' => 'Rosati' ],
			'col1Title'           => [ 'type' => 'string', 'default' => 'Regione' ],
			'col1Items'           => [ 'type' => 'string', 'default' => "Puglia\nAbruzzo\nSicilia\nCalabria\nVeneto\nToscana\nCampania\nProvenza\nLombardia\nSardegna" ],
			'col1FeaturedCount'   => [ 'type' => 'string', 'default' => '4' ],
			'col2Title'           => [ 'type' => 'string', 'default' => 'Tipologia' ],
			'col2Items'           => [ 'type' => 'string', 'default' => "Cerasuolo d'Abruzzo\nBardolino Chiaretto\nNegroamaro Rosato\nSalento Rosato\nProvenza\nEtna Rosato\nCiro Rosato\nBombino Nero" ],
			'col2FeaturedCount'   => [ 'type' => 'string', 'default' => '0' ],
			'col3Title'           => [ 'type' => 'string', 'default' => 'Abbinamento' ],
			'col3Items'           => [ 'type' => 'string', 'default' => "Aperitivo\nPizza\nCarni bianche\nPesce\nSalumi\nFormaggi freschi" ],
			'col3FeaturedCount'   => [ 'type' => 'string', 'default' => '0' ],
		],
	] );

	register_block_type( 'sayweb-vinifera/dynamic-header-links', [
		'api_version'     => 3,
		'render_callback' => 'sayweb_vinifera_render_header_links',
		'editor_script'   => 'sayweb-vinifera-dynamic-layout-editor',
		'attributes'      => [
			'km0Label'     => [ 'type' => 'string', 'default' => 'Km 0' ],
			'km0Url'       => [ 'type' => 'string', 'default' => '#' ],
			'foodLabel'    => [ 'type' => 'string', 'default' => 'Food locale' ],
			'foodUrl'      => [ 'type' => 'string', 'default' => '#' ],
			'aboutLabel'   => [ 'type' => 'string', 'default' => 'Chi siamo' ],
			'aboutUrl'     => [ 'type' => 'string', 'default' => '#' ],
			'contactLabel' => [ 'type' => 'string', 'default' => 'Contattaci' ],
			'contactUrl'   => [ 'type' => 'string', 'default' => '#' ],
		],
	] );

	register_block_type( 'sayweb-vinifera/dynamic-header-actions', [
		'api_version'     => 3,
		'render_callback' => 'sayweb_vinifera_render_header_actions',
		'editor_script'   => 'sayweb-vinifera-dynamic-layout-editor',
		'attributes'      => [
			'waLabel'   => [ 'type' => 'string', 'default' => 'Parla con Stefano' ],
			'waUrl'     => [ 'type' => 'string', 'default' => '#' ],
			'cartCount' => [ 'type' => 'string', 'default' => '0' ],
		],
	] );

	register_block_type( 'sayweb-vinifera/dynamic-header-banner', [
		'api_version'     => 3,
		'render_callback' => 'sayweb_vinifera_render_dynamic_header_banner',
		'editor_script'   => 'sayweb-vinifera-dynamic-layout-editor',
		'attributes'      => [
			'announcePrefix'     => [ 'type' => 'string', 'default' => 'Spedizione gratuita oltre ' ],
			'announceHighlight1' => [ 'type' => 'string', 'default' => '100 EUR' ],
			'announceMiddle'     => [ 'type' => 'string', 'default' => ' in tutta Italia' ],
			'announceLocal'      => [ 'type' => 'string', 'default' => 'Consegna in giornata nei comuni limitrofi a Locorotondo' ],
			'announceSuffix'     => [ 'type' => 'string', 'default' => '' ],
			'announceHighlight2' => [ 'type' => 'string', 'default' => '600+ etichette selezionate' ],
		],
	] );

	register_block_type( 'sayweb-vinifera/dynamic-header', [
		'api_version'     => 3,
		'render_callback' => 'sayweb_vinifera_render_dynamic_header',
		'editor_script'   => 'sayweb-vinifera-dynamic-layout-editor',
		'attributes'      => [
			'rossiLabel'     => [ 'type' => 'string', 'default' => 'Rossi' ],
			'bianchiLabel'   => [ 'type' => 'string', 'default' => 'Bianchi' ],
			'spumantiLabel'  => [ 'type' => 'string', 'default' => 'Spumanti' ],
			'rosatiLabel'    => [ 'type' => 'string', 'default' => 'Rosati' ],
			'km0Label'       => [ 'type' => 'string', 'default' => 'Km 0' ],
			'foodLabel'      => [ 'type' => 'string', 'default' => 'Food locale' ],
			'aboutLabel'     => [ 'type' => 'string', 'default' => 'Chi siamo' ],
			'contactLabel'   => [ 'type' => 'string', 'default' => 'Contattaci' ],
			'waLabel'        => [ 'type' => 'string', 'default' => 'Parla con Stefano' ],
			'cartCount'      => [ 'type' => 'string', 'default' => '0' ],
		],
	] );

	$registry = WP_Block_Type_Registry::get_instance();

	if ( ! $registry->is_registered( 'sayweb-vinifera/contact-form' ) ) {
		register_block_type( 'sayweb-vinifera/contact-form', [
			'api_version'     => 3,
			'render_callback' => 'sayweb_vinifera_render_contact_form',
			'editor_script'   => 'sayweb-vinifera-dynamic-layout-editor',
		] );
	}

	if ( ! $registry->is_registered( 'sayweb-vinifera/contact-map' ) ) {
		register_block_type( 'sayweb-vinifera/contact-map', [
			'api_version'     => 3,
			'render_callback' => 'sayweb_vinifera_render_contact_map',
			'editor_script'   => 'sayweb-vinifera-dynamic-layout-editor',
		] );
	}

	register_block_type( 'sayweb-vinifera/category-filters', [
		'api_version'     => 3,
		'render_callback' => 'sayweb_vinifera_render_category_filters',
	] );

	register_block_type( 'sayweb-vinifera/dynamic-footer', [
		'api_version'     => 3,
		'render_callback' => 'sayweb_vinifera_render_dynamic_footer',
		'editor_script'   => 'sayweb-vinifera-dynamic-layout-editor',
		'attributes'      => [
			'brandDescription' => [ 'type' => 'string', 'default' => 'Enoteca con store fisico nel centro storico di Locorotondo e spedizione in tutta Italia. 600 etichette selezionate, food artigianale, degustazioni guidate. Dal 2020.' ],
			'addressLabel'     => [ 'type' => 'string', 'default' => 'Indirizzo' ],
			'addressText'      => [ 'type' => 'string', 'default' => "Via Eroi di Dogali 12, 70010\nLocorotondo (BA), Puglia" ],
			'hoursLabel'       => [ 'type' => 'string', 'default' => 'Orari' ],
			'hoursText'        => [ 'type' => 'string', 'default' => 'Lun-Dom - 10:00-23:00' ],
			'contactsLabel'    => [ 'type' => 'string', 'default' => 'Contatti' ],
			'contactsText'     => [ 'type' => 'string', 'default' => "+39 080 123 4567\ninfo@viniferaenoteca.it" ],
			'copyrightText'    => [ 'type' => 'string', 'default' => 'Copyright 2026 Vinifera Enoteca - P.IVA 0000000000' ],
			'legalText'        => [ 'type' => 'string', 'default' => 'Privacy - Cookie - Termini - Spedizioni e resi' ],
		],
	] );

}
add_action( 'init', 'sayweb_vinifera_register_blocks', 7 );

/**
 * Register theme patterns explicitly as a fallback to auto-discovery.
 */
function sayweb_vinifera_register_patterns(): void {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}

	$patterns = [
		'sayweb-vinifera/vinifera-hero'      => [
			'title' => __( 'Vinifera Hero', 'sayweb-vinifera' ),
			'file'  => get_stylesheet_directory() . '/patterns/vinifera-hero.php',
		],
		'sayweb-vinifera/vinifera-trust-bar' => [
			'title' => __( 'Vinifera Trust Bar', 'sayweb-vinifera' ),
			'file'  => get_stylesheet_directory() . '/patterns/vinifera-trust-bar.php',
		],
		'sayweb-vinifera/vinifera-home-posts' => [
			'title' => __( 'Vinifera Home Posts', 'sayweb-vinifera' ),
			'file'  => get_stylesheet_directory() . '/patterns/vinifera-home-posts.php',
		],
	];

	$registry = class_exists( 'WP_Block_Patterns_Registry' ) ? WP_Block_Patterns_Registry::get_instance() : null;

	foreach ( $patterns as $slug => $pattern ) {
		if ( ! file_exists( $pattern['file'] ) ) {
			continue;
		}

		if ( $registry && method_exists( $registry, 'is_registered' ) && $registry->is_registered( $slug ) ) {
			continue;
		}

		$content = file_get_contents( $pattern['file'] );
		if ( ! is_string( $content ) || '' === trim( $content ) ) {
			continue;
		}

		// Remove the PHP header metadata block so only block markup is registered.
		$content = preg_replace( '/^<\?php[\s\S]*?\?>\s*/', '', $content );
		if ( ! is_string( $content ) || '' === trim( $content ) ) {
			continue;
		}

		register_block_pattern(
			$slug,
			[
				'title'      => $pattern['title'],
				'categories' => [ 'sayweb-vinifera' ],
				'inserter'   => true,
				'content'    => $content,
			]
		);
	}
}
add_action( 'init', 'sayweb_vinifera_register_patterns', 9 );

/**
 * Load parent and child styles.
 */
function sayweb_vinifera_enqueue_styles(): void {
	$theme = wp_get_theme();

	wp_enqueue_style(
		'sayweb-vinifera-parent-style',
		get_template_directory_uri() . '/style.css',
		[],
		$theme->parent() ? $theme->parent()->get( 'Version' ) : null
	);

	wp_enqueue_style(
		'sayweb-vinifera-style',
		get_stylesheet_uri(),
		[ 'sayweb-vinifera-parent-style' ],
		$theme->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'sayweb_vinifera_enqueue_styles' );

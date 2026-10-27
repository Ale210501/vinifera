<?php
/**
 * Plugin Name: Vinifera CF7 Bootstrap
 * Description: Creates and exposes a default Contact Form 7 form for Vinifera.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'VINIFERA_CF7_BOOTSTRAP_LOADED' ) ) {
	return;
}
define( 'VINIFERA_CF7_BOOTSTRAP_LOADED', true );

const VINIFERA_CF7_OPTION_FORM_ID = 'vinifera_cf7_contact_form_id';
const VINIFERA_CF7_FORM_TITLE     = 'Vinifera Contatti';

function vinifera_cf7_bootstrap_maybe_create_form(): void {
	if ( ! class_exists( 'WPCF7_ContactForm' ) ) {
		return;
	}

	$stored_id = absint( get_option( VINIFERA_CF7_OPTION_FORM_ID ) );
	if ( $stored_id && get_post_type( $stored_id ) === 'wpcf7_contact_form' ) {
		return;
	}

	$existing = WPCF7_ContactForm::find( [
		'post_status' => 'publish',
		'posts_per_page' => 1,
		'orderby' => 'ID',
		'order' => 'ASC',
		's' => VINIFERA_CF7_FORM_TITLE,
	] );

	if ( ! empty( $existing ) ) {
		$existing_id = $existing[0]->id();
		if ( $existing_id ) {
			update_option( VINIFERA_CF7_OPTION_FORM_ID, absint( $existing_id ), false );
			return;
		}
	}

	$form = WPCF7_ContactForm::get_template( [
		'title' => VINIFERA_CF7_FORM_TITLE,
		'locale' => determine_locale(),
	] );

	$form->set_title( VINIFERA_CF7_FORM_TITLE );
	$form->set_properties( [
		'form' => '<div class="row">
<div><label>Nome [text* your-name placeholder "Nome e cognome"]</label></div>
<div><label>Telefono [tel your-phone placeholder "Facoltativo"]</label></div>
</div>
<label>Email [email* your-email placeholder "tua@email.it"]</label>
<label>Oggetto [text your-subject placeholder "Di cosa si tratta"]</label>
<label>Messaggio [textarea* your-message placeholder "Scrivici pure con parole tue"]</label>
<div class="privacy">[acceptance your-privacy] Acconsento al trattamento dei dati come da Privacy Policy [/acceptance]</div>
[submit class:vinifera-cf7-submit "Invia il messaggio →"]',
		'mail' => [
			'recipient' => get_option( 'admin_email' ),
			'sender' => 'Vinifera <' . get_option( 'admin_email' ) . '>',
			'subject' => 'Nuovo contatto Vinifera - [your-subject]',
			'additional_headers' => 'Reply-To: [your-email]',
			'body' => "Nome: [your-name]\nEmail: [your-email]\nTelefono: [your-phone]\nOggetto: [your-subject]\n\nMessaggio:\n[your-message]",
			'exclude_blank' => false,
			'use_html' => false,
			'attachment' => '',
		],
		'mail_2' => [
			'enabled' => false,
		],
		'messages' => [],
		'additional_settings' => "",
	] );

	$created_id = $form->save();
	if ( $created_id ) {
		update_option( VINIFERA_CF7_OPTION_FORM_ID, absint( $created_id ), false );
	}
}
add_action( 'init', 'vinifera_cf7_bootstrap_maybe_create_form', 20 );

function vinifera_cf7_form_shortcode( $atts = [] ): string {
	$form_id = absint( get_option( VINIFERA_CF7_OPTION_FORM_ID ) );
	if ( ! $form_id ) {
		return '<p class="vinifera-cf7-missing">Contact Form 7 form not initialized yet.</p>';
	}

	return '<div class="vinifera-cf7-shell cform">' . do_shortcode( sprintf( '[contact-form-7 id="%d" html_class="vinifera-cf7-form"]', $form_id ) ) . '</div>';
}
add_shortcode( 'vinifera_contact_form', 'vinifera_cf7_form_shortcode' );

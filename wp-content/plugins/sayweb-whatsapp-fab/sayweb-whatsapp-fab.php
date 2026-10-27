<?php
/**
 * Plugin Name: Sayweb WhatsApp FAB
 * Description: Floating Action Button per WhatsApp con label e numero configurabili
 * Version: 1.0.0
 * Author: Sayweb
 * Text Domain: sayweb-whatsapp-fab
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

// Registra il menu impostazioni
add_action('admin_menu', function() {
    add_options_page(
        'WhatsApp FAB',
        'WhatsApp FAB',
        'manage_options',
        'sayweb-whatsapp-fab',
        'sayweb_whatsapp_fab_page'
    );
});

// Pagina impostazioni
function sayweb_whatsapp_fab_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    // Salva i dati se inviati
    if (isset($_POST['sayweb_whatsapp_fab_nonce']) && 
        wp_verify_nonce($_POST['sayweb_whatsapp_fab_nonce'], 'sayweb_whatsapp_fab_action')) {
        
        $label = sanitize_text_field($_POST['whatsapp_label'] ?? 'Parla con noi');
        $phone = sanitize_text_field($_POST['whatsapp_phone'] ?? '');
        
        update_option('sayweb_whatsapp_label', $label);
        update_option('sayweb_whatsapp_phone', $phone);
        
        echo '<div class="notice notice-success is-dismissible"><p>Impostazioni salvate!</p></div>';
    }

    $label = get_option('sayweb_whatsapp_label', 'Parla con Stefano');
    $phone = get_option('sayweb_whatsapp_phone', '');
    ?>
    <div class="wrap">
        <h1>WhatsApp FAB - Impostazioni</h1>
        
        <form method="POST">
            <?php wp_nonce_field('sayweb_whatsapp_fab_action', 'sayweb_whatsapp_fab_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="whatsapp_label">Label del bottone</label>
                    </th>
                    <td>
                        <input 
                            type="text" 
                            id="whatsapp_label" 
                            name="whatsapp_label" 
                            value="<?php echo esc_attr($label); ?>"
                            class="regular-text"
                            placeholder="Es. Parla con Stefano"
                        />
                        <p class="description">Testo che appare nel bottone WhatsApp</p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="whatsapp_phone">Numero WhatsApp</label>
                    </th>
                    <td>
                        <input 
                            type="tel" 
                            id="whatsapp_phone" 
                            name="whatsapp_phone" 
                            value="<?php echo esc_attr($phone); ?>"
                            class="regular-text"
                            placeholder="Es. +39 3XX XXXXXXX o 393XXXXXXX"
                        />
                        <p class="description">Numero WhatsApp con prefisso internazionale (es. +39 per Italia)</p>
                    </td>
                </tr>
            </table>
            
            <h3>Come usare</h3>
            <p>Usa lo <strong>shortcode</strong> nel template o nella pagina:</p>
            <code>[whatsapp_fab]</code>
            
            <p>Oppure nel codice PHP:</p>
            <code>echo do_shortcode('[whatsapp_fab]');</code>
            
            <h3>Anteprima</h3>
            <div style="padding: 20px; background: #f5f5f5; border-radius: 4px;">
                <?php echo do_shortcode('[whatsapp_fab]'); ?>
            </div>
            
            <hr />
            <?php submit_button('Salva impostazioni'); ?>
        </form>
    </div>
    <?php
}

// Registra lo shortcode
add_shortcode('whatsapp_fab', function() {
    $label = get_option('sayweb_whatsapp_label', 'Parla con Stefano');
    $phone = get_option('sayweb_whatsapp_phone', '');
    
    if (empty($phone)) {
        return '<p style="color: red;">⚠️ Numero WhatsApp non configurato. Vai su Impostazioni → WhatsApp FAB</p>';
    }
    
    // Pulisce il numero da spazi, puntini, trattini
    $phone_clean = preg_replace('/[^0-9+]/', '', $phone);
    
    // Se non ha il +, aggiungi +39 (default Italia)
    if (strpos($phone_clean, '+') === false) {
        $phone_clean = '+39' . ltrim($phone_clean, '0');
    }
    
    $whatsapp_url = 'https://wa.me/' . $phone_clean;
    
    $html = sprintf(
        '<a href="%s" class="wa-fab" target="_blank" rel="noopener noreferrer">%s</a>',
        esc_url($whatsapp_url),
        esc_html($label)
    );
    
    return $html;
});

// Blocco Gutenberg (opzionale)
add_action('init', function() {
    register_block_type('sayweb/whatsapp-fab', [
        'render_callback' => function($attributes) {
            return do_shortcode('[whatsapp_fab]');
        },
        'title' => 'WhatsApp FAB',
        'category' => 'common',
        'icon' => '💬',
    ]);
});

# Sayweb WhatsApp FAB

Plugin WordPress per un bottone galleggiante (FAB) WhatsApp configurabile.

## Caratteristiche

✅ Interfaccia di amministrazione per configurare label e numero WhatsApp  
✅ Shortcode `[whatsapp_fab]` per visualizzare il bottone  
✅ Blocco Gutenberg nativo per il Block Editor  
✅ Pulizia automatica del numero di telefono  
✅ Apertura chat WhatsApp in nuova scheda  
✅ Nessuna dipendenza esterna

## Installazione

1. Carica la cartella `sayweb-whatsapp-fab` in `wp-content/plugins/`
2. Attiva il plugin dal pannello WordPress
3. Vai su **Impostazioni → WhatsApp FAB**
4. Configura label e numero di WhatsApp

## Come usare

### Shortcode
Nel template, pagina o editor Gutenberg:
```
[whatsapp_fab]
```

### Nel codice PHP
```php
echo do_shortcode('[whatsapp_fab]');
```

### Blocco Gutenberg
Cerca "WhatsApp FAB" nel pannello di inserimento blocchi.

## Configurazione

Vai su **Impostazioni → WhatsApp FAB** per configurare:

- **Label del bottone**: Testo che appare nel bottone (es. "Parla con Stefano")
- **Numero WhatsApp**: Numero con prefisso internazionale (es. +39 3XX XXXXXXX)

## Formati numero accettati

- `+39 3XX XXXXXXX`
- `393XXXXXXX`
- `+39393XXXXXXX`
- Qualsiasi variante con spazi/trattini (vengono rimossi automaticamente)

## Note

- Il numero senza `+` sarà convertito automaticamente con il prefisso `+39` (Italia)
- Per altri paesi, usa il prefisso internazionale completo (es. `+1` per USA)
- Il bottone si apre in una nuova scheda

## Author

Sayweb

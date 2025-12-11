# Guida alla Risoluzione dei Problemi / Troubleshooting Guide

## 🇮🇹 ITALIANO

### Il plugin non elabora le immagini

Se hai attivato il plugin e inserito il token API ma le immagini non vengono elaborate, segui questi passaggi:

#### 1. Verifica le Impostazioni

1. Vai su **Impostazioni → Image AI Metadata**
2. Controlla che:
   - ✅ Il token API sia inserito correttamente (inizia con `hf_`)
   - ✅ L'opzione "Elaborazione automatica" sia **abilitata** (spunta attiva)
   - ✅ L'endpoint API sia corretto (quello predefinito va bene)

#### 2. Verifica il Token API

1. Vai su https://huggingface.co/settings/tokens
2. Controlla che il token:
   - ✅ Esista e sia valido
   - ✅ Abbia permessi di tipo "Read"
   - ✅ Non sia scaduto
3. Se necessario, genera un nuovo token e aggiornalo nelle impostazioni

#### 3. Testa Manualmente

1. Vai su **Media → Libreria**
2. Clicca su un'immagine esistente per modificarla
3. Nella colonna destra, cerca il box "Riconoscimento AI Immagine"
4. Clicca sul pulsante **"Rielabora con AI"**
5. Attendi alcuni secondi
6. Controlla se appare un messaggio di successo o errore

#### 4. Controlla i Log di WordPress

Se hai abilitato il debug di WordPress:

1. Aggiungi al file `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

2. Controlla il file `wp-content/debug.log` per eventuali errori

#### 5. Problemi Comuni e Soluzioni

**Problema**: "Il modello sta caricando, riprova tra 20 secondi"
- **Causa**: I modelli Hugging Face vengono caricati on-demand
- **Soluzione**: Attendi 20-30 secondi e ricarica una nuova immagine

**Problema**: Errore 401 o 403
- **Causa**: Token API non valido o scaduto
- **Soluzione**: Genera un nuovo token su Hugging Face

**Problema**: Errore di timeout
- **Causa**: Immagine troppo grande o connessione lenta
- **Soluzione**: 
  - Riduci la dimensione dell'immagine
  - Riprova più tardi
  - Controlla la connessione internet del server

**Problema**: Nessun errore ma metadati non aggiornati
- **Causa**: Elaborazione automatica potrebbe essere disabilitata
- **Soluzione**: Verifica che l'opzione sia abilitata nelle impostazioni

#### 6. Test del Token API

Usa lo script di test incluso:

```bash
# Nel terminale, vai nella cartella del plugin
cd wp-content/plugins/image-ai-metadata/

# Modifica test-api.php e inserisci il tuo token
nano test-api.php

# Esegui il test
php test-api.php
```

Se il test fallisce, il problema è con il token o la connessione API.

#### 7. Verifica Requisiti Server

Controlla che il server abbia:
- ✅ PHP 7.0 o superiore
- ✅ Estensione `curl` abilitata o `allow_url_fopen` attivo
- ✅ Connessione internet funzionante
- ✅ Timeout PHP di almeno 30 secondi

Puoi verificare con:
```php
<?php
phpinfo();
?>
```

#### 8. Limiti API Raggiunti

Account gratuito Hugging Face:
- ~1000 richieste al giorno
- ~50 richieste all'ora

Se hai raggiunto il limite:
- Attendi qualche ora
- Considera un account PRO se hai molte immagini

#### 9. Formato Immagine Non Supportato

Il plugin elabora solo:
- ✅ JPG/JPEG
- ✅ PNG
- ✅ WebP
- ✅ GIF

Altri formati potrebbero non funzionare.

#### 10. Controlla Permessi File

Il plugin deve poter leggere le immagini:
```bash
# Controlla permessi
ls -la wp-content/uploads/

# Se necessario, correggi
chmod -R 755 wp-content/uploads/
```

---

## 🇬🇧 ENGLISH

### Plugin doesn't process images

If you've activated the plugin and entered the API token but images aren't being processed, follow these steps:

#### 1. Verify Settings

1. Go to **Settings → Image AI Metadata**
2. Check that:
   - ✅ API token is entered correctly (starts with `hf_`)
   - ✅ "Automatic processing" option is **enabled** (checkbox checked)
   - ✅ API endpoint is correct (default is fine)

#### 2. Verify API Token

1. Go to https://huggingface.co/settings/tokens
2. Check that the token:
   - ✅ Exists and is valid
   - ✅ Has "Read" permissions
   - ✅ Hasn't expired
3. If needed, generate a new token and update in settings

#### 3. Test Manually

1. Go to **Media → Library**
2. Click on an existing image to edit it
3. In the right column, look for "AI Image Recognition" box
4. Click the **"Re-process with AI"** button
5. Wait a few seconds
6. Check if a success or error message appears

#### 4. Check WordPress Logs

If you've enabled WordPress debug:

1. Add to `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

2. Check `wp-content/debug.log` file for errors

#### 5. Common Problems and Solutions

**Problem**: "Model is loading, please retry in 20 seconds"
- **Cause**: Hugging Face models are loaded on-demand
- **Solution**: Wait 20-30 seconds and upload a new image

**Problem**: 401 or 403 error
- **Cause**: Invalid or expired API token
- **Solution**: Generate a new token on Hugging Face

**Problem**: Timeout error
- **Cause**: Image too large or slow connection
- **Solution**: 
  - Reduce image size
  - Try again later
  - Check server internet connection

**Problem**: No error but metadata not updated
- **Cause**: Automatic processing might be disabled
- **Solution**: Verify the option is enabled in settings

#### 6. API Token Test

Use the included test script:

```bash
# In terminal, go to plugin folder
cd wp-content/plugins/image-ai-metadata/

# Edit test-api.php and insert your token
nano test-api.php

# Run the test
php test-api.php
```

If the test fails, the problem is with the token or API connection.

#### 7. Check Server Requirements

Verify the server has:
- ✅ PHP 7.0 or higher
- ✅ `curl` extension enabled or `allow_url_fopen` active
- ✅ Working internet connection
- ✅ PHP timeout of at least 30 seconds

You can verify with:
```php
<?php
phpinfo();
?>
```

#### 8. API Rate Limits Reached

Free Hugging Face account:
- ~1000 requests per day
- ~50 requests per hour

If you've reached the limit:
- Wait a few hours
- Consider a PRO account if you have many images

#### 9. Unsupported Image Format

The plugin only processes:
- ✅ JPG/JPEG
- ✅ PNG
- ✅ WebP
- ✅ GIF

Other formats might not work.

#### 10. Check File Permissions

The plugin must be able to read images:
```bash
# Check permissions
ls -la wp-content/uploads/

# If needed, fix
chmod -R 755 wp-content/uploads/
```

---

## 🔍 Diagnostica Avanzata / Advanced Diagnostics

### Abilita il Logging Dettagliato / Enable Detailed Logging

Aggiungi temporaneamente al file `image-ai-metadata.php` dopo la riga 327:

```php
// Dopo: private function analyze_and_update_image($attachment_id) {
error_log('Image AI Metadata: Processing attachment ' . $attachment_id);
```

E dopo la riga 342:

```php
// Dopo: $description = $this->call_ai_api($image_path, $api_token);
if (is_wp_error($description)) {
    error_log('Image AI Metadata ERROR: ' . $description->get_error_message());
} else {
    error_log('Image AI Metadata SUCCESS: ' . $description);
}
```

Poi controlla `wp-content/debug.log` quando carichi un'immagine.

### Test di Connettività API / API Connectivity Test

Esegui questo test dal server WordPress:

```php
<?php
$token = 'IL_TUO_TOKEN_QUI';
$url = 'https://api-inference.huggingface.co/models/Salesforce/blip-image-captioning-large';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $token
));
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('path/to/test/image.jpg'));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n";
?>
```

### Verifica Formato Token / Verify Token Format

Il token deve:
- Iniziare con `hf_`
- Essere lungo ~37-40 caratteri
- Contenere solo lettere, numeri e underscore

Esempio valido: `hf_abcdefghijklmnopqrstuvwxyz123456`

---

## 📞 Hai ancora problemi? / Still having issues?

1. Controlla gli **Issues** su GitHub: https://github.com/fui86/Titoli-foto---Wordpress/issues
2. Apri un nuovo issue con:
   - Versione WordPress
   - Versione PHP
   - Messaggio di errore (se presente)
   - Screenshot delle impostazioni
   - Log di debug (senza token API visibile!)

---

## ✅ Checklist Veloce / Quick Checklist

Prima di chiedere supporto, verifica:

- [ ] Plugin attivato
- [ ] Token API inserito (inizia con `hf_`)
- [ ] Token valido su Hugging Face
- [ ] Elaborazione automatica abilitata
- [ ] Caricato un'immagine DOPO aver configurato il plugin
- [ ] Formato immagine supportato (JPG, PNG, WebP, GIF)
- [ ] WordPress debug abilitato
- [ ] Controllato debug.log per errori
- [ ] Testato elaborazione manuale
- [ ] Server ha connessione internet
- [ ] PHP >= 7.0
- [ ] Estensione curl abilitata

Se tutti i punti sono ✅ e il plugin non funziona ancora, apri un issue su GitHub.

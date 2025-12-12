# Guida all'installazione / Installation Guide

## 🇮🇹 Guida in Italiano

### Metodo 1: Installazione tramite file ZIP (Consigliato)

1. **Scarica il plugin**
   ```bash
   # Clona il repository
   git clone https://github.com/fui86/Titoli-foto---Wordpress.git
   
   # Oppure scarica come ZIP da GitHub
   # Click su "Code" → "Download ZIP"
   ```

2. **Crea un file ZIP del plugin**
   ```bash
   cd Titoli-foto---Wordpress
   zip -r image-ai-metadata.zip image-ai-metadata.php languages/ README.md USAGE.md EXAMPLES.md LICENSE .gitignore
   ```

3. **Installa in WordPress**
   - Accedi al pannello admin di WordPress
   - Vai su **Plugin** → **Aggiungi nuovo**
   - Clicca su **Carica plugin**
   - Seleziona il file `image-ai-metadata.zip`
   - Clicca su **Installa ora**
   - Clicca su **Attiva plugin**

### Metodo 2: Installazione manuale via FTP

1. **Scarica il plugin**
   - Scarica tutti i file dal repository

2. **Carica via FTP**
   - Connettiti al tuo server via FTP
   - Vai nella cartella `wp-content/plugins/`
   - Crea una nuova cartella chiamata `image-ai-metadata`
   - Carica tutti i file del plugin in questa cartella

3. **Attiva il plugin**
   - Vai sul pannello admin di WordPress
   - Vai su **Plugin** → **Plugin installati**
   - Trova "Image AI Metadata" e clicca **Attiva**

### Metodo 3: Installazione via SSH (per sviluppatori)

```bash
# Connettiti al server via SSH
ssh user@your-server.com

# Vai nella cartella dei plugin
cd /path/to/wordpress/wp-content/plugins/

# Clona il repository
git clone https://github.com/fui86/Titoli-foto---Wordpress.git image-ai-metadata

# Imposta i permessi corretti
chmod -R 755 image-ai-metadata/
chown -R www-data:www-data image-ai-metadata/
```

Poi attiva il plugin dal pannello admin di WordPress.

### Configurazione iniziale

1. **Crea un account Hugging Face** (se non ne hai uno)
   - Vai su https://huggingface.co/join
   - Compila il form di registrazione
   - Verifica la tua email

2. **Genera un API Token**
   - Accedi a https://huggingface.co/settings/tokens
   - Clicca su **New token**
   - Imposta:
     - **Name**: `WordPress Image AI`
     - **Role**: `read`
   - Clicca su **Generate a token**
   - **IMPORTANTE**: Copia il token subito! Non lo vedrai più.

3. **Configura il plugin in WordPress**
   - Vai su **Impostazioni** → **Image AI Metadata**
   - Incolla il token nel campo **Hugging Face API Token**
   - Lascia l'endpoint predefinito (o cambialo se preferisci un altro modello)
   - Abilita **Elaborazione automatica** (consigliato)
   - Clicca su **Salva impostazioni**

4. **Testa il plugin**
   - Vai su **Media** → **Aggiungi nuovo**
   - Carica un'immagine di test
   - Attendi qualche secondo
   - Vai su **Media** → **Libreria**
   - Clicca sull'immagine appena caricata
   - Verifica che i campi siano stati compilati

### Verifica dell'installazione

✅ **Plugin correttamente installato se**:
- Compare in **Plugin** → **Plugin installati**
- Compare una voce **Image AI Metadata** in **Impostazioni**
- Puoi vedere il meta box "Riconoscimento AI Immagine" quando modifichi un'immagine

❌ **Problemi comuni**:

**Plugin non compare nella lista**:
- Verifica che i file siano nella cartella corretta: `wp-content/plugins/image-ai-metadata/`
- Verifica i permessi dei file (dovrebbero essere leggibili dal server web)

**Errore "Plugin non ha un header valido"**:
- Assicurati che il file `image-ai-metadata.php` sia presente
- Verifica che non ci siano caratteri strani all'inizio del file

**Errore "Non puoi attivare questo plugin"**:
- Verifica la versione di PHP (minimo 7.0)
- Verifica la versione di WordPress (minimo 5.0)

---

## 🇬🇧 English Guide

### Method 1: Installation via ZIP file (Recommended)

1. **Download the plugin**
   ```bash
   # Clone the repository
   git clone https://github.com/fui86/Titoli-foto---Wordpress.git
   
   # Or download as ZIP from GitHub
   # Click "Code" → "Download ZIP"
   ```

2. **Create a plugin ZIP file**
   ```bash
   cd Titoli-foto---Wordpress
   zip -r image-ai-metadata.zip image-ai-metadata.php languages/ README.md USAGE.md EXAMPLES.md LICENSE .gitignore
   ```

3. **Install in WordPress**
   - Log in to WordPress admin panel
   - Go to **Plugins** → **Add New**
   - Click **Upload Plugin**
   - Select the `image-ai-metadata.zip` file
   - Click **Install Now**
   - Click **Activate Plugin**

### Method 2: Manual installation via FTP

1. **Download the plugin**
   - Download all files from the repository

2. **Upload via FTP**
   - Connect to your server via FTP
   - Navigate to `wp-content/plugins/`
   - Create a new folder called `image-ai-metadata`
   - Upload all plugin files to this folder

3. **Activate the plugin**
   - Go to WordPress admin panel
   - Go to **Plugins** → **Installed Plugins**
   - Find "Image AI Metadata" and click **Activate**

### Method 3: Installation via SSH (for developers)

```bash
# Connect to server via SSH
ssh user@your-server.com

# Navigate to plugins folder
cd /path/to/wordpress/wp-content/plugins/

# Clone the repository
git clone https://github.com/fui86/Titoli-foto---Wordpress.git image-ai-metadata

# Set correct permissions
chmod -R 755 image-ai-metadata/
chown -R www-data:www-data image-ai-metadata/
```

Then activate the plugin from WordPress admin panel.

### Initial Configuration

1. **Create a Hugging Face account** (if you don't have one)
   - Go to https://huggingface.co/join
   - Fill in the registration form
   - Verify your email

2. **Generate an API Token**
   - Log in to https://huggingface.co/settings/tokens
   - Click **New token**
   - Set:
     - **Name**: `WordPress Image AI`
     - **Role**: `read`
   - Click **Generate a token**
   - **IMPORTANT**: Copy the token immediately! You won't see it again.

3. **Configure the plugin in WordPress**
   - Go to **Settings** → **Image AI Metadata**
   - Paste the token in the **Hugging Face API Token** field
   - Leave the default endpoint (or change it if you prefer another model)
   - Enable **Automatic processing** (recommended)
   - Click **Save Settings**

4. **Test the plugin**
   - Go to **Media** → **Add New**
   - Upload a test image
   - Wait a few seconds
   - Go to **Media** → **Library**
   - Click on the just uploaded image
   - Verify that the fields have been populated

### Installation Verification

✅ **Plugin correctly installed if**:
- It appears in **Plugins** → **Installed Plugins**
- There's an **Image AI Metadata** entry in **Settings**
- You can see the "AI Image Recognition" meta box when editing an image

❌ **Common issues**:

**Plugin doesn't appear in the list**:
- Verify files are in the correct folder: `wp-content/plugins/image-ai-metadata/`
- Check file permissions (should be readable by web server)

**Error "Plugin doesn't have a valid header"**:
- Make sure the `image-ai-metadata.php` file is present
- Check there are no strange characters at the beginning of the file

**Error "You cannot activate this plugin"**:
- Check PHP version (minimum 7.0)
- Check WordPress version (minimum 5.0)

---

## 🔧 Advanced Configuration

### Multisite Installation

For WordPress Multisite:

1. **Network Activate** (optional):
   - Go to **Network Admin** → **Plugins**
   - Click **Network Activate** under the plugin

2. **Configure per site**:
   - Each site can have its own API token
   - Configure in each site's **Settings** → **Image AI Metadata**

### Custom Endpoints

You can use different AI models by changing the endpoint:

```
# BLIP Image Captioning (default - best quality)
https://api-inference.huggingface.co/models/Salesforce/blip-image-captioning-large

# BLIP Base (faster, good quality)
https://api-inference.huggingface.co/models/Salesforce/blip-image-captioning-base

# ViT GPT2 (fastest)
https://api-inference.huggingface.co/models/nlpconnect/vit-gpt2-image-captioning

# GIT Large (detailed descriptions)
https://api-inference.huggingface.co/models/microsoft/git-large-coco
```

### Environment Variables (for developers)

You can set the API token via environment variables:

```php
// In wp-config.php
define('IMAGE_AI_METADATA_API_TOKEN', 'hf_xxxxxxxxxxxxx');
```

The plugin will use this token if the option is not set in the database.

---

## 🧪 Testing the Installation

### Using the test script

A test script is provided to verify API connectivity:

```bash
# Edit test-api.php and add your API token
nano test-api.php

# Run the test
php test-api.php
```

Expected output:
```
=====================================
Image AI Metadata - API Test Script
=====================================

🔧 Configuration:
   Endpoint: https://api-inference.huggingface.co/...
   Token: hf_xxxxxxx...

📥 Downloading test image...
✓ Image downloaded: 123,456 bytes

🤖 Calling Hugging Face API...
✓ API Response received (HTTP 200)

=====================================
✅ SUCCESS!
=====================================

AI Generated Description:
   "a red car parked on a street"
```

---

## 📦 Requirements

### Minimum Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 7.0 or higher
- **PHP Extensions**: 
  - `curl` or `allow_url_fopen` enabled
  - `json`
- **Permissions**: Write access to `wp-content/plugins/`
- **Internet**: Connection to Hugging Face API

### Recommended Requirements

- **WordPress**: Latest version
- **PHP**: 7.4 or higher
- **Memory**: 128 MB (WordPress default)
- **Execution Time**: 30 seconds for API calls

### Server Configuration

Check your server configuration:

```php
<?php
// Create a file: check-requirements.php in your WordPress root

echo "PHP Version: " . phpversion() . "\n";
echo "cURL enabled: " . (function_exists('curl_init') ? 'Yes' : 'No') . "\n";
echo "allow_url_fopen: " . (ini_get('allow_url_fopen') ? 'Yes' : 'No') . "\n";
echo "JSON enabled: " . (function_exists('json_decode') ? 'Yes' : 'No') . "\n";
echo "Memory Limit: " . ini_get('memory_limit') . "\n";
echo "Max Execution Time: " . ini_get('max_execution_time') . " seconds\n";
```

---

## 🆘 Getting Help

If you encounter issues:

1. Check the [USAGE.md](USAGE.md) file for usage instructions
2. Check the [EXAMPLES.md](EXAMPLES.md) file for examples
3. Enable WordPress debug mode and check error logs
4. Open an issue on [GitHub](https://github.com/fui86/Titoli-foto---Wordpress/issues)

---

## 🔄 Updating the Plugin

### Via Git (for developers)

```bash
cd wp-content/plugins/image-ai-metadata/
git pull origin main
```

### Manual Update

1. Download the latest version
2. Deactivate the plugin (settings are preserved)
3. Delete the old plugin folder
4. Upload the new version
5. Activate the plugin again

**Note**: Your API token and settings will be preserved during updates.

---

## 🗑️ Uninstalling

### Clean Uninstall

1. Deactivate the plugin
2. Delete the plugin
3. (Optional) Clean up database:

```sql
-- Remove plugin options
DELETE FROM wp_options WHERE option_name LIKE 'image_ai_metadata_%';

-- Remove post meta
DELETE FROM wp_postmeta WHERE meta_key = '_image_ai_metadata_processed';
```

**Note**: This will remove all plugin data. Image metadata will remain.

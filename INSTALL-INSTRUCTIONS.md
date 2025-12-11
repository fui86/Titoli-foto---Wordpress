# Istruzioni di Installazione / Installation Instructions

## 🇮🇹 ITALIANO

### File Completo Pronto per WordPress

Il file **`image-ai-metadata.zip`** contiene tutto il necessario per installare il plugin su WordPress.

### Contenuto del ZIP (12 file)

```
image-ai-metadata/
├── image-ai-metadata.php         # File principale del plugin
├── languages/
│   └── image-ai-metadata.pot     # File di traduzione
├── README.md                     # Documentazione principale
├── QUICKSTART.md                 # Guida rapida (5 minuti)
├── USAGE.md                      # Guida dettagliata
├── EXAMPLES.md                   # Esempi pratici
├── INSTALLATION.md               # Istruzioni installazione
├── CONTRIBUTING.md               # Linee guida contribuzione
├── CHANGELOG.md                  # Storico versioni
└── LICENSE                       # Licenza GPL-2.0+
```

### Installazione - Metodo 1: Caricamento ZIP (CONSIGLIATO)

1. **Scarica il file**: `image-ai-metadata.zip` (31 KB)

2. **Accedi a WordPress**:
   - Vai sul tuo sito WordPress
   - Accedi come amministratore

3. **Carica il plugin**:
   - Vai su **Plugin** → **Aggiungi nuovo**
   - Clicca su **Carica plugin** (in alto)
   - Clicca su **Scegli file**
   - Seleziona `image-ai-metadata.zip`
   - Clicca su **Installa ora**

4. **Attiva il plugin**:
   - Dopo l'installazione, clicca su **Attiva plugin**
   - Oppure vai su **Plugin** → **Plugin installati** e attiva "Image AI Metadata"

5. **Configura il plugin**:
   - Vai su **Impostazioni** → **Image AI Metadata**
   - Inserisci il tuo token API di Hugging Face
   - Abilita l'elaborazione automatica
   - Clicca su **Salva impostazioni**

### Installazione - Metodo 2: Caricamento FTP

1. **Estrai il file ZIP**:
   - Estrai `image-ai-metadata.zip` sul tuo computer
   - Otterrai una cartella `image-ai-metadata/`

2. **Carica via FTP**:
   - Connettiti al tuo server via FTP (FileZilla, Cyberduck, ecc.)
   - Vai nella cartella `wp-content/plugins/`
   - Carica l'intera cartella `image-ai-metadata/`

3. **Attiva il plugin**:
   - Vai su WordPress admin → **Plugin** → **Plugin installati**
   - Trova "Image AI Metadata" e clicca **Attiva**

4. **Configura**:
   - Vai su **Impostazioni** → **Image AI Metadata**
   - Inserisci il token API
   - Salva le impostazioni

### Installazione - Metodo 3: SSH (Utenti Avanzati)

```bash
# Connettiti via SSH al server
ssh user@your-server.com

# Vai nella cartella dei plugin
cd /path/to/wordpress/wp-content/plugins/

# Scarica il file ZIP (se già caricato sul server)
# oppure caricalo via SFTP

# Estrai il file
unzip image-ai-metadata.zip

# Imposta i permessi corretti
chmod -R 755 image-ai-metadata/
chown -R www-data:www-data image-ai-metadata/

# Attiva il plugin da WordPress admin
```

### Configurazione Iniziale

1. **Ottieni un token API gratuito**:
   - Vai su https://huggingface.co/settings/tokens
   - Se non hai un account, registrati (gratuito)
   - Clicca **New token**
   - Nome: `WordPress`
   - Tipo: **Read**
   - Clicca **Generate a token**
   - **COPIA IL TOKEN** (apparirà solo una volta!)

2. **Inserisci il token in WordPress**:
   - Vai su **Impostazioni** → **Image AI Metadata**
   - Incolla il token nel campo **Hugging Face API Token**
   - Clicca **Salva impostazioni**

3. **Prova il plugin**:
   - Vai su **Media** → **Aggiungi nuovo**
   - Carica un'immagine di test
   - Attendi 5-10 secondi
   - Vai su **Media** → **Libreria**
   - Clicca sull'immagine
   - Verifica che i metadati siano stati compilati

### Verifica Installazione

✅ **Il plugin è installato correttamente se**:
- Compare in **Plugin** → **Plugin installati**
- Puoi vedere **Impostazioni** → **Image AI Metadata**
- Quando modifichi un'immagine, vedi il box "Riconoscimento AI Immagine"

### Requisiti Sistema

- WordPress 5.0 o superiore
- PHP 7.0 o superiore
- Estensione PHP `curl` o `allow_url_fopen` abilitato
- Connessione internet
- Account Hugging Face (gratuito)

### Risoluzione Problemi

**Il plugin non compare nella lista**:
- Verifica che il file ZIP sia stato estratto correttamente
- La struttura deve essere: `wp-content/plugins/image-ai-metadata/image-ai-metadata.php`

**Errore "Plugin non ha un header valido"**:
- Ri-estrai il file ZIP
- Verifica che `image-ai-metadata.php` sia presente
- Non modificare il file principale

**Non riesco a caricare il ZIP**:
- Il file potrebbe essere troppo grande per il limite di upload di WordPress
- Aumenta `upload_max_filesize` in `php.ini`
- Oppure usa il metodo FTP

---

## 🇬🇧 ENGLISH

### Complete WordPress-Ready File

The **`image-ai-metadata.zip`** file contains everything needed to install the plugin on WordPress.

### ZIP Contents (12 files)

```
image-ai-metadata/
├── image-ai-metadata.php         # Main plugin file
├── languages/
│   └── image-ai-metadata.pot     # Translation file
├── README.md                     # Main documentation
├── QUICKSTART.md                 # Quick guide (5 minutes)
├── USAGE.md                      # Detailed guide
├── EXAMPLES.md                   # Practical examples
├── INSTALLATION.md               # Installation instructions
├── CONTRIBUTING.md               # Contribution guidelines
├── CHANGELOG.md                  # Version history
└── LICENSE                       # GPL-2.0+ license
```

### Installation - Method 1: ZIP Upload (RECOMMENDED)

1. **Download the file**: `image-ai-metadata.zip` (31 KB)

2. **Log in to WordPress**:
   - Go to your WordPress site
   - Log in as administrator

3. **Upload the plugin**:
   - Go to **Plugins** → **Add New**
   - Click **Upload Plugin** (at the top)
   - Click **Choose File**
   - Select `image-ai-metadata.zip`
   - Click **Install Now**

4. **Activate the plugin**:
   - After installation, click **Activate Plugin**
   - Or go to **Plugins** → **Installed Plugins** and activate "Image AI Metadata"

5. **Configure the plugin**:
   - Go to **Settings** → **Image AI Metadata**
   - Enter your Hugging Face API token
   - Enable automatic processing
   - Click **Save Settings**

### Installation - Method 2: FTP Upload

1. **Extract the ZIP file**:
   - Extract `image-ai-metadata.zip` on your computer
   - You'll get an `image-ai-metadata/` folder

2. **Upload via FTP**:
   - Connect to your server via FTP (FileZilla, Cyberduck, etc.)
   - Navigate to `wp-content/plugins/`
   - Upload the entire `image-ai-metadata/` folder

3. **Activate the plugin**:
   - Go to WordPress admin → **Plugins** → **Installed Plugins**
   - Find "Image AI Metadata" and click **Activate**

4. **Configure**:
   - Go to **Settings** → **Image AI Metadata**
   - Enter the API token
   - Save settings

### Installation - Method 3: SSH (Advanced Users)

```bash
# Connect via SSH to the server
ssh user@your-server.com

# Navigate to plugins folder
cd /path/to/wordpress/wp-content/plugins/

# Download the ZIP file (if already uploaded to server)
# or upload via SFTP

# Extract the file
unzip image-ai-metadata.zip

# Set correct permissions
chmod -R 755 image-ai-metadata/
chown -R www-data:www-data image-ai-metadata/

# Activate plugin from WordPress admin
```

### Initial Configuration

1. **Get a free API token**:
   - Go to https://huggingface.co/settings/tokens
   - If you don't have an account, sign up (free)
   - Click **New token**
   - Name: `WordPress`
   - Type: **Read**
   - Click **Generate a token**
   - **COPY THE TOKEN** (you'll only see it once!)

2. **Enter the token in WordPress**:
   - Go to **Settings** → **Image AI Metadata**
   - Paste the token in the **Hugging Face API Token** field
   - Click **Save Settings**

3. **Test the plugin**:
   - Go to **Media** → **Add New**
   - Upload a test image
   - Wait 5-10 seconds
   - Go to **Media** → **Library**
   - Click on the image
   - Verify that metadata has been populated

### Installation Verification

✅ **Plugin is correctly installed if**:
- It appears in **Plugins** → **Installed Plugins**
- You can see **Settings** → **Image AI Metadata**
- When editing an image, you see the "AI Image Recognition" box

### System Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher
- PHP `curl` extension or `allow_url_fopen` enabled
- Internet connection
- Hugging Face account (free)

### Troubleshooting

**Plugin doesn't appear in list**:
- Verify the ZIP file was extracted correctly
- Structure should be: `wp-content/plugins/image-ai-metadata/image-ai-metadata.php`

**Error "Plugin does not have a valid header"**:
- Re-extract the ZIP file
- Verify `image-ai-metadata.php` is present
- Don't modify the main file

**Cannot upload ZIP**:
- File might be too large for WordPress upload limit
- Increase `upload_max_filesize` in `php.ini`
- Or use FTP method

---

## 📦 Package Details

- **File name**: `image-ai-metadata.zip`
- **Size**: 31 KB (compressed)
- **Format**: ZIP archive
- **WordPress compatible**: Yes ✓
- **Verified**: PHP syntax checked ✓
- **Complete**: All documentation included ✓

## 🎯 What's Included

| File | Description | Language |
|------|-------------|----------|
| image-ai-metadata.php | Main plugin (464 lines) | PHP |
| README.md | Main documentation | IT/EN |
| QUICKSTART.md | 5-minute setup guide | IT/EN |
| USAGE.md | Detailed usage | IT/EN |
| EXAMPLES.md | Practical examples | IT/EN |
| INSTALLATION.md | Installation guide | IT/EN |
| CONTRIBUTING.md | Contribution guide | EN |
| CHANGELOG.md | Version history | EN |
| LICENSE | GPL-2.0+ license | EN |
| languages/*.pot | Translation template | - |

## ✅ Quality Checks Performed

- [x] PHP syntax validated
- [x] ZIP structure verified
- [x] WordPress plugin format confirmed
- [x] All files included
- [x] Extraction tested
- [x] File permissions correct
- [x] Documentation complete

## 🚀 Ready to Use

The plugin is **production-ready** and can be installed immediately on any WordPress site (5.0+) with PHP 7.0+.

No additional files or dependencies required!

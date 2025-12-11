# Image AI Metadata - WordPress Plugin

Un plugin WordPress che utilizza l'intelligenza artificiale per riconoscere automaticamente il contenuto delle immagini e compilare i campi metadata (Testo alternativo, Titolo, Didascalia, Descrizione).

A WordPress plugin that uses artificial intelligence to automatically recognize image content and populate metadata fields (Alternative Text, Title, Caption, Description).

## 🇮🇹 Italiano

### Caratteristiche

- **Riconoscimento automatico delle immagini**: Usa modelli AI gratuiti di Hugging Face per analizzare le immagini
- **Compilazione automatica dei metadati**: Compila automaticamente:
  - Testo alternativo (Alt text)
  - Titolo
  - Didascalia (Caption)
  - Descrizione
- **Elaborazione automatica o manuale**: Scegli se elaborare le immagini automaticamente al caricamento o manualmente
- **Interfaccia intuitiva**: Configurazione semplice tramite pagina delle impostazioni
- **Gratuito**: Utilizza API gratuite di Hugging Face
- **Supporto multilingua**: Pronto per la traduzione

### Installazione

1. **Scarica il plugin**: Clona o scarica questo repository
2. **Carica su WordPress**: 
   - Carica la cartella del plugin in `/wp-content/plugins/`
   - Oppure carica il file ZIP tramite la dashboard di WordPress (Plugin → Aggiungi nuovo → Carica plugin)
3. **Attiva il plugin**: Vai su Plugin → Plugin installati e attiva "Image AI Metadata"

### Configurazione

1. **Ottieni un token API gratuito**:
   - Vai su [Hugging Face](https://huggingface.co/)
   - Crea un account gratuito
   - Vai su [Settings → Access Tokens](https://huggingface.co/settings/tokens)
   - Crea un nuovo token (tipo "Read")

2. **Configura il plugin**:
   - Vai su Impostazioni → Image AI Metadata
   - Incolla il tuo token API Hugging Face
   - Scegli se abilitare l'elaborazione automatica
   - Salva le impostazioni

3. **Usa il plugin**:
   - **Automatico**: Carica nuove immagini e verranno elaborate automaticamente
   - **Manuale**: Vai alla libreria media, modifica un'immagine e clicca "Rielabora con AI"

### Modelli AI Supportati

Il plugin usa di default il modello **BLIP Image Captioning** di Salesforce, che è eccellente per generare descrizioni accurate delle immagini. Puoi anche usare altri modelli cambiando l'endpoint API nelle impostazioni:

- `https://api-inference.huggingface.co/models/Salesforce/blip-image-captioning-large` (default)
- `https://api-inference.huggingface.co/models/nlpconnect/vit-gpt2-image-captioning`
- Altri modelli di image captioning disponibili su Hugging Face

### Requisiti

- WordPress 5.0 o superiore
- PHP 7.0 o superiore
- Account Hugging Face (gratuito)

---

## 🇬🇧 English

### Features

- **Automatic image recognition**: Uses free Hugging Face AI models to analyze images
- **Automatic metadata population**: Automatically fills:
  - Alternative text (Alt text)
  - Title
  - Caption
  - Description
- **Automatic or manual processing**: Choose to process images automatically on upload or manually
- **Intuitive interface**: Simple configuration via settings page
- **Free**: Uses free Hugging Face APIs
- **Multilingual support**: Translation ready

### Installation

1. **Download the plugin**: Clone or download this repository
2. **Upload to WordPress**:
   - Upload the plugin folder to `/wp-content/plugins/`
   - Or upload the ZIP file via WordPress dashboard (Plugins → Add New → Upload Plugin)
3. **Activate the plugin**: Go to Plugins → Installed Plugins and activate "Image AI Metadata"

### Configuration

1. **Get a free API token**:
   - Go to [Hugging Face](https://huggingface.co/)
   - Create a free account
   - Go to [Settings → Access Tokens](https://huggingface.co/settings/tokens)
   - Create a new token (type "Read")

2. **Configure the plugin**:
   - Go to Settings → Image AI Metadata
   - Paste your Hugging Face API token
   - Choose whether to enable automatic processing
   - Save settings

3. **Use the plugin**:
   - **Automatic**: Upload new images and they will be processed automatically
   - **Manual**: Go to media library, edit an image and click "Re-process with AI"

### Supported AI Models

The plugin uses the **BLIP Image Captioning** model from Salesforce by default, which is excellent for generating accurate image descriptions. You can also use other models by changing the API endpoint in settings:

- `https://api-inference.huggingface.co/models/Salesforce/blip-image-captioning-large` (default)
- `https://api-inference.huggingface.co/models/nlpconnect/vit-gpt2-image-captioning`
- Other image captioning models available on Hugging Face

### Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher
- Hugging Face account (free)

---

## 📸 Screenshots

### Settings Page
Configure your API token and processing options.

### Media Edit Page
Manual re-processing button with last processing timestamp.

---

## 🔧 Technical Details

### Architecture

- **Single file plugin**: Easy to install and maintain
- **WordPress best practices**: Uses WordPress hooks, filters, and APIs
- **Secure**: Nonce verification, capability checks, input sanitization
- **Error handling**: Comprehensive error messages and logging
- **Translation ready**: All strings are translatable

### API Integration

The plugin integrates with Hugging Face's Inference API, which provides free access to state-of-the-art AI models. The default model (BLIP) is specifically trained for image captioning and provides high-quality, natural language descriptions of images.

### How It Works

1. When an image is uploaded (or manually triggered), the plugin reads the image file
2. The image is sent to Hugging Face's API with your authentication token
3. The AI model analyzes the image and returns a description
4. The plugin updates all metadata fields with the generated description
5. A timestamp is stored to track when the image was last processed

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

---

## 📄 License

This plugin is licensed under the GPL-2.0+ License. See the plugin header for details.

---

## ⚠️ Disclaimer

This plugin uses external AI services (Hugging Face). Please review their [terms of service](https://huggingface.co/terms-of-service) and ensure compliance with your local regulations regarding AI-generated content.

---

## 📞 Support

For issues, questions, or contributions, please open an issue on the [GitHub repository](https://github.com/fui86/Titoli-foto---Wordpress).
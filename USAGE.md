# Guida all'uso / Usage Guide

## 🇮🇹 Guida in Italiano

### Passo 1: Installazione

1. Scarica il plugin dal repository
2. Vai su WordPress → Plugin → Aggiungi nuovo → Carica plugin
3. Seleziona il file ZIP del plugin
4. Clicca "Installa ora" e poi "Attiva plugin"

### Passo 2: Configurazione API

1. **Crea un account Hugging Face (gratuito)**:
   - Vai su https://huggingface.co/join
   - Completa la registrazione

2. **Genera il token API**:
   - Accedi al tuo account
   - Vai su https://huggingface.co/settings/tokens
   - Clicca "New token"
   - Nome: `WordPress Image AI`
   - Tipo: `Read`
   - Clicca "Generate"
   - **Copia il token** (lo vedrai solo una volta!)

3. **Configura WordPress**:
   - Vai su WordPress → Impostazioni → Image AI Metadata
   - Incolla il token nel campo "Hugging Face API Token"
   - Lascia l'endpoint predefinito (BLIP) o scegli un altro modello
   - Abilita "Elaborazione automatica" se vuoi che le immagini vengano elaborate al caricamento
   - Clicca "Salva impostazioni"

### Passo 3: Uso del plugin

#### Elaborazione Automatica (consigliata)

1. Vai su Media → Aggiungi nuovo
2. Carica una o più immagini
3. Il plugin le elaborerà automaticamente in background
4. Vai su Media → Libreria per vedere i metadati compilati

#### Elaborazione Manuale

1. Vai su Media → Libreria
2. Clicca su un'immagine per modificarla
3. Nella colonna destra, cerca "Riconoscimento AI Immagine"
4. Clicca "Rielabora con AI"
5. Attendi qualche secondo
6. I metadati verranno aggiornati automaticamente

### Cosa fa il plugin?

Il plugin analizza ogni immagine usando AI e compila automaticamente:

- **Testo alternativo (Alt text)**: Importante per l'accessibilità e la SEO
- **Titolo**: Il titolo dell'immagine nella libreria media
- **Didascalia**: Testo mostrato sotto l'immagine (dipende dal tema)
- **Descrizione**: Descrizione dettagliata nell'editor media

### Esempio pratico

**Immagine caricata**: Una foto di un gatto arancione su un divano

**Risultato AI**:
- Alt text: "an orange cat sitting on a couch"
- Titolo: "An orange cat sitting on a couch"
- Didascalia: "an orange cat sitting on a couch"
- Descrizione: "an orange cat sitting on a couch"

Puoi sempre modificare manualmente questi campi dopo l'elaborazione!

### Risoluzione problemi

**Il plugin non elabora le immagini**:
- Verifica di aver inserito il token API corretto
- Controlla che l'elaborazione automatica sia abilitata
- Prova a rielaborare manualmente un'immagine

**Errore "Token API non configurato"**:
- Vai su Impostazioni → Image AI Metadata
- Inserisci il token API di Hugging Face
- Salva le impostazioni

**Errore API 401 o 403**:
- Il token API potrebbe essere scaduto o non valido
- Genera un nuovo token su Hugging Face
- Aggiorna il token nelle impostazioni

**Le descrizioni sono in inglese**:
- I modelli AI di Hugging Face generano principalmente descrizioni in inglese
- Puoi modificare manualmente le descrizioni dopo l'elaborazione
- In futuro potrebbero essere disponibili modelli multilingua

### Modelli AI alternativi

Puoi cambiare il modello AI modificando l'endpoint nelle impostazioni:

**BLIP Image Captioning (consigliato)**:
```
https://api-inference.huggingface.co/models/Salesforce/blip-image-captioning-large
```
- Migliore qualità
- Descrizioni dettagliate
- Più lento

**ViT GPT2 Image Captioning**:
```
https://api-inference.huggingface.co/models/nlpconnect/vit-gpt2-image-captioning
```
- Veloce
- Descrizioni più semplici
- Buono per grandi volumi

---

## 🇬🇧 English Guide

### Step 1: Installation

1. Download the plugin from the repository
2. Go to WordPress → Plugins → Add New → Upload Plugin
3. Select the plugin ZIP file
4. Click "Install Now" and then "Activate Plugin"

### Step 2: API Configuration

1. **Create a Hugging Face account (free)**:
   - Go to https://huggingface.co/join
   - Complete registration

2. **Generate API token**:
   - Log in to your account
   - Go to https://huggingface.co/settings/tokens
   - Click "New token"
   - Name: `WordPress Image AI`
   - Type: `Read`
   - Click "Generate"
   - **Copy the token** (you'll only see it once!)

3. **Configure WordPress**:
   - Go to WordPress → Settings → Image AI Metadata
   - Paste the token in "Hugging Face API Token" field
   - Leave the default endpoint (BLIP) or choose another model
   - Enable "Automatic processing" if you want images processed on upload
   - Click "Save Settings"

### Step 3: Using the plugin

#### Automatic Processing (recommended)

1. Go to Media → Add New
2. Upload one or more images
3. The plugin will process them automatically in the background
4. Go to Media → Library to see the populated metadata

#### Manual Processing

1. Go to Media → Library
2. Click on an image to edit it
3. In the right column, look for "AI Image Recognition"
4. Click "Re-process with AI"
5. Wait a few seconds
6. Metadata will be updated automatically

### What does the plugin do?

The plugin analyzes each image using AI and automatically fills:

- **Alternative text (Alt text)**: Important for accessibility and SEO
- **Title**: The image title in the media library
- **Caption**: Text displayed below the image (theme-dependent)
- **Description**: Detailed description in the media editor

### Practical Example

**Uploaded image**: A photo of an orange cat on a couch

**AI Result**:
- Alt text: "an orange cat sitting on a couch"
- Title: "An orange cat sitting on a couch"
- Caption: "an orange cat sitting on a couch"
- Description: "an orange cat sitting on a couch"

You can always manually edit these fields after processing!

### Troubleshooting

**Plugin doesn't process images**:
- Verify you've entered the correct API token
- Check that automatic processing is enabled
- Try manually re-processing an image

**Error "API token not configured"**:
- Go to Settings → Image AI Metadata
- Enter your Hugging Face API token
- Save settings

**API error 401 or 403**:
- API token might be expired or invalid
- Generate a new token on Hugging Face
- Update the token in settings

**Descriptions are in English**:
- Hugging Face AI models primarily generate English descriptions
- You can manually edit descriptions after processing
- Multilingual models may be available in the future

### Alternative AI Models

You can change the AI model by modifying the endpoint in settings:

**BLIP Image Captioning (recommended)**:
```
https://api-inference.huggingface.co/models/Salesforce/blip-image-captioning-large
```
- Best quality
- Detailed descriptions
- Slower

**ViT GPT2 Image Captioning**:
```
https://api-inference.huggingface.co/models/nlpconnect/vit-gpt2-image-captioning
```
- Fast
- Simpler descriptions
- Good for large volumes

---

## 🔐 Privacy & Security

- Images are sent to Hugging Face's servers for processing
- No images are stored permanently on Hugging Face
- API calls are made server-to-server (not from user browsers)
- Your API token is stored securely in WordPress database
- Review Hugging Face's privacy policy: https://huggingface.co/privacy

---

## 💡 Tips & Best Practices

1. **Test with a few images first** before processing your entire library
2. **Review AI-generated descriptions** - they're usually good but may need tweaking
3. **Keep your API token secure** - don't share it publicly
4. **Use meaningful filenames** - helps the AI understand context
5. **Manual override** - always review important images manually
6. **Backup first** - backup your database before mass processing

---

## 📊 Limitations

- **API Rate Limits**: Free Hugging Face accounts have rate limits
- **Processing Time**: Each image takes 2-10 seconds to process
- **Language**: Descriptions are primarily in English
- **Accuracy**: AI is good but not perfect - always review critical content
- **Image Size**: Very large images may take longer to process

---

## 🆘 Support

Need help? 
- Check the README.md for detailed documentation
- Open an issue on GitHub: https://github.com/fui86/Titoli-foto---Wordpress/issues
- Review Hugging Face documentation: https://huggingface.co/docs

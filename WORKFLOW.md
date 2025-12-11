# Plugin Workflow / Flusso di lavoro del plugin

## 🔄 How It Works / Come funziona

### Visual Workflow

```
┌─────────────────────────────────────────────────────────────┐
│                    USER UPLOADS IMAGE                       │
│               L'utente carica un'immagine                   │
└─────────────────────┬───────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│              WORDPRESS MEDIA LIBRARY                        │
│            Libreria media di WordPress                      │
│                                                             │
│  ┌──────────────────────────────────────────────────┐     │
│  │  Image uploaded to /wp-content/uploads/          │     │
│  │  Immagine caricata in /wp-content/uploads/       │     │
│  └──────────────────────────────────────────────────┘     │
└─────────────────────┬───────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│            PLUGIN INTERCEPTS UPLOAD                         │
│         Il plugin intercetta il caricamento                 │
│                                                             │
│  Hook: add_attachment                                       │
│  Check: Is auto-process enabled?                            │
│  Check: È abilitata l'elaborazione automatica?              │
└─────────────────────┬───────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│              READ IMAGE FILE                                │
│             Leggi file immagine                             │
│                                                             │
│  get_attached_file($attachment_id)                          │
│  file_get_contents($image_path)                             │
└─────────────────────┬───────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│          SEND TO HUGGING FACE API                           │
│        Invia all'API di Hugging Face                        │
│                                                             │
│  POST https://api-inference.huggingface.co/...             │
│  Headers: Authorization: Bearer {token}                     │
│  Body: Binary image data                                    │
└─────────────────────┬───────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│            AI ANALYZES IMAGE                                │
│          L'AI analizza l'immagine                           │
│                                                             │
│  Model: BLIP Image Captioning                               │
│  Process: Computer vision + NLP                             │
│  Output: Natural language description                       │
└─────────────────────┬───────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│         RECEIVE AI DESCRIPTION                              │
│        Ricevi descrizione dall'AI                           │
│                                                             │
│  Example: "a red car parked on a street"                   │
│  Esempio: "a red car parked on a street"                   │
└─────────────────────┬───────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│       UPDATE WORDPRESS METADATA                             │
│      Aggiorna i metadati di WordPress                       │
│                                                             │
│  ✓ Alt text: "a red car parked on a street"                │
│  ✓ Title: "A red car parked on a street"                   │
│  ✓ Caption: "a red car parked on a street"                 │
│  ✓ Description: "a red car parked on a street"             │
│  ✓ Timestamp: 1702291234                                    │
└─────────────────────┬───────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│                    DONE! ✅                                  │
│                   Fatto! ✅                                  │
│                                                             │
│  Image is now accessible and SEO-friendly                   │
│  L'immagine è ora accessibile e ottimizzata SEO             │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔀 Alternative Flows / Flussi alternativi

### Manual Processing / Elaborazione manuale

```
User edits image in Media Library
   │
   ▼
Clicks "Re-process with AI" button
   │
   ▼
Same AI processing flow
   │
   ▼
Updated metadata
   │
   ▼
Success notification
```

### Error Handling / Gestione errori

```
API call fails
   │
   ▼
Error detected
   │
   ├─→ Invalid token → Show error message
   │
   ├─→ Network error → Show error message
   │
   ├─→ Model loading → Suggest retry
   │
   └─→ Other error → Log and notify user
```

---

## 📊 Data Flow / Flusso dati

### Input → Processing → Output

```
INPUT                    PROCESSING              OUTPUT
┌──────────────┐        ┌──────────┐        ┌─────────────┐
│              │        │          │        │             │
│  Image File  │───────▶│   API    │───────▶│ Description │
│              │        │          │        │             │
└──────────────┘        └──────────┘        └─────────────┘
     │                       │                     │
     │ Binary data          │ AI Model            │ Text
     │ JPG/PNG/WebP         │ BLIP/ViT-GPT2      │ English
     │ Up to 10MB           │ 2-10 seconds       │ UTF-8
     │                       │                     │
     ▼                       ▼                     ▼
┌──────────────┐        ┌──────────┐        ┌─────────────┐
│ WordPress    │        │ Hugging  │        │ WordPress   │
│ Media        │        │ Face     │        │ Database    │
│ Upload       │        │ Cloud    │        │ (wp_posts)  │
└──────────────┘        └──────────┘        └─────────────┘
```

---

## 🎛️ Configuration Flow / Flusso di configurazione

### Settings Page / Pagina impostazioni

```
┌─────────────────────────────────────────────────┐
│        Settings → Image AI Metadata             │
│      Impostazioni → Image AI Metadata           │
└───────────────────┬─────────────────────────────┘
                    │
     ┌──────────────┴──────────────┐
     │                             │
     ▼                             ▼
┌─────────┐                  ┌──────────┐
│   API   │                  │  Options │
│  Token  │                  │ Opzioni  │
└────┬────┘                  └────┬─────┘
     │                            │
     │  Saved to:                 │  Saved to:
     │  wp_options                │  wp_options
     │  ├─ api_token              │  ├─ auto_process
     │  └─ api_endpoint           │  └─ (checkbox)
     │                            │
     ▼                            ▼
┌─────────────────────────────────────┐
│      Used during processing         │
│    Usato durante l'elaborazione     │
└─────────────────────────────────────┘
```

---

## 🔐 Security Flow / Flusso di sicurezza

### Request Validation / Validazione richieste

```
User Action
   │
   ▼
┌─────────────────┐
│ Check Nonce     │ ←── WordPress nonce verification
└────┬────────────┘
     │ ✓ Valid
     ▼
┌─────────────────┐
│ Check Capability│ ←── manage_options / upload_files
└────┬────────────┘
     │ ✓ Authorized
     ▼
┌─────────────────┐
│ Sanitize Input  │ ←── sanitize_text_field, absint
└────┬────────────┘
     │ ✓ Clean
     ▼
┌─────────────────┐
│ Process Request │
└────┬────────────┘
     │
     ▼
┌─────────────────┐
│ Escape Output   │ ←── esc_html, esc_attr, esc_url
└─────────────────┘
```

---

## 💾 Database Schema / Schema database

### Tables Used / Tabelle utilizzate

```
wp_options (Plugin Settings)
├── image_ai_metadata_api_token      (text)
├── image_ai_metadata_auto_process   (bool)
└── image_ai_metadata_api_endpoint   (text)

wp_posts (Image Data)
├── ID                               (int)
├── post_title                       (text) ← AI Title
├── post_excerpt                     (text) ← AI Caption
├── post_content                     (text) ← AI Description
└── post_type = 'attachment'

wp_postmeta (Additional Data)
├── post_id                          (int)
├── meta_key = '_wp_attachment_image_alt' (text) ← AI Alt Text
└── meta_key = '_image_ai_metadata_processed' (timestamp)
```

---

## 🌐 API Communication / Comunicazione API

### Request Structure / Struttura richiesta

```http
POST /models/Salesforce/blip-image-captioning-large HTTP/1.1
Host: api-inference.huggingface.co
Authorization: Bearer hf_xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
Content-Type: application/octet-stream
Content-Length: 123456

[Binary image data]
```

### Response Structure / Struttura risposta

```json
[
  {
    "generated_text": "a red car parked on a street"
  }
]
```

### Error Response / Risposta errore

```json
{
  "error": "Model is currently loading",
  "estimated_time": 20.0
}
```

---

## 🔄 State Diagram / Diagramma stati

```
┌─────────────┐
│   Plugin    │
│  Installed  │
└──────┬──────┘
       │
       ▼
┌─────────────┐     No token      ┌──────────────┐
│  Activated  │───────────────────▶│ Need Config  │
└──────┬──────┘                    └──────────────┘
       │                                   │
       │ Token configured                  │ User adds token
       ▼                                   │
┌─────────────┐◀──────────────────────────┘
│    Ready    │
└──────┬──────┘
       │
       ├─────────────────┬─────────────────┐
       │                 │                 │
       ▼                 ▼                 ▼
┌─────────────┐   ┌──────────┐    ┌──────────┐
│ Processing  │   │  Idle    │    │  Error   │
│  Images     │   │          │    │  State   │
└──────┬──────┘   └──────────┘    └────┬─────┘
       │                                │
       │ Success                        │ Retry
       ▼                                │
┌─────────────┐                         │
│  Complete   │◀────────────────────────┘
└─────────────┘
```

---

## 🎯 Use Case: Photo Upload / Caso d'uso: Caricamento foto

### Scenario / Scenario

**User**: Photographer uploading 20 wedding photos  
**Utente**: Fotografo che carica 20 foto di matrimonio

### Timeline / Cronologia

```
00:00 - User selects 20 images
        L'utente seleziona 20 immagini

00:05 - Upload starts (WordPress)
        Inizia il caricamento (WordPress)

00:15 - All images uploaded
        Tutte le immagini caricate

00:15 - Plugin starts processing image #1
        Il plugin inizia a elaborare l'immagine #1

00:20 - Image #1 complete (metadata updated)
        Immagine #1 completata (metadati aggiornati)

00:20 - Plugin starts processing image #2
        Il plugin inizia a elaborare l'immagine #2

... (continues for all 20 images)

03:00 - All images processed
        Tutte le immagini elaborate

03:00 - User can view metadata in Media Library
        L'utente può vedere i metadati nella libreria media
```

### Result / Risultato

✅ 20 images with complete metadata  
✅ 20 immagini con metadati completi

- Alt text for accessibility
- Titles for organization
- Captions for display
- Descriptions for context

**Time saved**: ~30 minutes of manual entry!  
**Tempo risparmiato**: ~30 minuti di inserimento manuale!

---

## 🎨 UI Flow / Flusso interfaccia

### Settings Page Journey / Percorso pagina impostazioni

```
WordPress Admin Dashboard
         │
         ▼
    Settings Menu
         │
         ▼
  Image AI Metadata ◀─────┐
         │                │
         ▼                │
  Configuration Form      │
    ├─ API Token          │
    ├─ Endpoint           │
    └─ Auto-process       │
         │                │
         ▼                │
    Save Settings         │
         │                │
         ├─ Success ──────┘
         │
         └─ Error → Show message
```

### Media Library Journey / Percorso libreria media

```
Media Library
      │
      ├─ Upload New ────────┐
      │                     │
      │                     ▼
      │              Auto-process
      │                     │
      │                     ▼
      │              Metadata filled
      │
      └─ Edit Existing ────┐
                           │
                           ▼
                    Meta Box visible
                           │
                           ▼
                    "Re-process" button
                           │
                           ▼
                    Manual processing
                           │
                           ▼
                    Metadata updated
```

---

## 🔧 Developer Hooks / Hook per sviluppatori

### Available Filters / Filtri disponibili

```php
// None currently - intentional design choice
// Nessuno al momento - scelta progettuale intenzionale
```

### Available Actions / Azioni disponibili

```php
// Plugin uses WordPress core actions
// Il plugin usa le azioni core di WordPress

add_action('add_attachment', 'process_new_image');
add_action('admin_post_image_ai_metadata_process', 'handle_manual_process');
```

### Extensibility / Estensibilità

Future versions may add:
```php
// Example future hooks
apply_filters('image_ai_metadata_description', $description, $attachment_id);
do_action('image_ai_metadata_processed', $attachment_id, $description);
```

---

## 📈 Performance Metrics / Metriche prestazioni

### Typical Performance / Prestazioni tipiche

```
Image Upload:         1-5 seconds (WordPress)
API Call:             2-10 seconds (AI processing)
Metadata Update:      < 1 second (Database)
─────────────────────────────────────────────
Total Time per Image: 3-16 seconds
```

### Optimization Tips / Suggerimenti ottimizzazione

```
✓ Use smaller images when possible
✓ Process during low-traffic hours
✓ Consider batch processing limits
✓ Monitor API rate limits
✓ Cache results (plugin does this automatically)
```

---

## 🎓 Learning Resources / Risorse didattiche

### For Plugin Users / Per gli utenti del plugin

1. Start with QUICKSTART.md (5 minutes)
2. Read README.md (overview)
3. Check EXAMPLES.md (real scenarios)
4. Refer to USAGE.md (detailed guide)

### For Developers / Per gli sviluppatori

1. Read PROJECT-SUMMARY.md (architecture)
2. Check CONTRIBUTING.md (standards)
3. Review image-ai-metadata.php (code)
4. Test with test-api.php (API verification)

---

## 📞 Support Flow / Flusso supporto

```
Issue occurs
    │
    ▼
Check documentation
    │
    ├─ QUICKSTART.md
    ├─ README.md
    ├─ USAGE.md
    └─ EXAMPLES.md
    │
    ▼ Not resolved?
    │
    ▼
Check existing GitHub issues
    │
    ▼ Not found?
    │
    ▼
Open new GitHub issue
    │
    ├─ Bug report
    ├─ Feature request
    └─ Question
```

---

**Visual workflow complete! / Flusso visuale completato!** 🎨

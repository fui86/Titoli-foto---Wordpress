# Quick Start Guide / Guida Rapida

Get started with Image AI Metadata in 5 minutes! / Inizia con Image AI Metadata in 5 minuti!

---

## 🔽 DOWNLOAD / SCARICA

### **[⬇️ Scarica image-ai-metadata.zip (31 KB)](https://github.com/fui86/Titoli-foto---Wordpress/raw/copilot/add-wordpress-image-ai-plugin/image-ai-metadata.zip)**

### **[⬇️ Download image-ai-metadata.zip (31 KB)](https://github.com/fui86/Titoli-foto---Wordpress/raw/copilot/add-wordpress-image-ai-plugin/image-ai-metadata.zip)**

---

## 🇮🇹 Guida Rapida in Italiano

### 1️⃣ Installa il Plugin (2 minuti)

**Opzione A - Caricamento ZIP:**
1. [⬇️ Scarica image-ai-metadata.zip](https://github.com/fui86/Titoli-foto---Wordpress/raw/copilot/add-wordpress-image-ai-plugin/image-ai-metadata.zip)
2. In WordPress, vai su **Plugin → Aggiungi nuovo → Carica plugin**
3. Seleziona il file ZIP
4. Clicca **Installa ora** → **Attiva**

**Opzione B - FTP:**
1. Carica la cartella del plugin in `/wp-content/plugins/image-ai-metadata/`
2. In WordPress, vai su **Plugin** e attiva "Image AI Metadata"

### 2️⃣ Ottieni il Token API (1 minuto)

1. Vai su https://huggingface.co/settings/tokens
2. Se non hai un account, registrati gratuitamente
3. Clicca **New token**
4. Nome: `WordPress`
5. Tipo: **Read**
6. Clicca **Generate**
7. **COPIA IL TOKEN** (lo vedi solo ora!)

### 3️⃣ Configura il Plugin (1 minuto)

1. In WordPress, vai su **Impostazioni → Image AI Metadata**
2. Incolla il token nel campo **Hugging Face API Token**
3. Lascia tutto il resto come predefinito
4. Abilita **Elaborazione automatica** ✓
5. Clicca **Salva impostazioni**

### 4️⃣ Testa il Plugin (1 minuto)

1. Vai su **Media → Aggiungi nuovo**
2. Carica una foto (qualsiasi foto)
3. Attendi 5-10 secondi
4. Vai su **Media → Libreria**
5. Clicca sulla foto appena caricata
6. 🎉 **Guarda i metadati compilati!**

### ✅ Fatto!

Il plugin ora elaborerà automaticamente tutte le nuove immagini che carichi!

### 💡 Suggerimenti

- **Rielaborazione manuale**: Modifica un'immagine e clicca "Rielabora con AI"
- **Descrizioni in inglese**: I modelli AI generano in inglese, puoi modificare manualmente
- **Personalizzazione**: Rivedi sempre le descrizioni per contenuti importanti

### ❓ Problemi?

- **Non funziona?** Verifica il token API nelle impostazioni
- **Errore API?** Il modello potrebbe essere in caricamento, riprova dopo 30 secondi
- **Altro?** Controlla [USAGE.md](USAGE.md) o apri un issue su GitHub

---

## 🇬🇧 Quick Start in English

### 1️⃣ Install the Plugin (2 minutes)

**Option A - ZIP Upload:**
1. [⬇️ Download image-ai-metadata.zip](https://github.com/fui86/Titoli-foto---Wordpress/raw/copilot/add-wordpress-image-ai-plugin/image-ai-metadata.zip)
2. In WordPress, go to **Plugins → Add New → Upload Plugin**
3. Select the ZIP file
4. Click **Install Now** → **Activate**

**Option B - FTP:**
1. Upload the plugin folder to `/wp-content/plugins/image-ai-metadata/`
2. In WordPress, go to **Plugins** and activate "Image AI Metadata"

### 2️⃣ Get API Token (1 minute)

1. Go to https://huggingface.co/settings/tokens
2. If you don't have an account, sign up for free
3. Click **New token**
4. Name: `WordPress`
5. Type: **Read**
6. Click **Generate**
7. **COPY THE TOKEN** (you'll only see it once!)

### 3️⃣ Configure the Plugin (1 minute)

1. In WordPress, go to **Settings → Image AI Metadata**
2. Paste the token in **Hugging Face API Token** field
3. Leave everything else as default
4. Enable **Automatic processing** ✓
5. Click **Save Settings**

### 4️⃣ Test the Plugin (1 minute)

1. Go to **Media → Add New**
2. Upload a photo (any photo)
3. Wait 5-10 seconds
4. Go to **Media → Library**
5. Click on the just uploaded photo
6. 🎉 **See the populated metadata!**

### ✅ Done!

The plugin will now automatically process all new images you upload!

### 💡 Tips

- **Manual re-processing**: Edit an image and click "Re-process with AI"
- **English descriptions**: AI models generate in English, you can edit manually
- **Customization**: Always review descriptions for important content

### ❓ Issues?

- **Not working?** Check API token in settings
- **API error?** Model might be loading, try again after 30 seconds
- **Other?** Check [USAGE.md](USAGE.md) or open an issue on GitHub

---

## 📋 Checklist

Before you start, make sure you have:

- [ ] WordPress 5.0 or higher
- [ ] PHP 7.0 or higher
- [ ] Admin access to WordPress
- [ ] Free Hugging Face account
- [ ] 5 minutes to set up

## 🎯 What Happens After Setup

Once configured, the plugin will:

✅ Automatically analyze new images when uploaded  
✅ Generate descriptive text using AI  
✅ Fill in Alt text (for accessibility and SEO)  
✅ Fill in Title (for media library)  
✅ Fill in Caption (displayed with image)  
✅ Fill in Description (detailed info)  
✅ Track when images were processed  
✅ Allow manual re-processing anytime  

## 🚀 Next Steps

After setup:

1. **Upload test images** - Try different types of images
2. **Review results** - Check if descriptions are accurate
3. **Customize as needed** - Edit any descriptions manually
4. **Read full docs** - See [README.md](README.md) for all features
5. **Share feedback** - Let us know how it works for you!

## 📸 What Works Best

**Great results with:**
- Photos of objects (car, food, furniture)
- Nature scenes (mountains, beaches, forests)
- Animals (cats, dogs, birds)
- Simple compositions
- Clear, well-lit images

**May need manual review:**
- Abstract art
- Text-heavy images
- Complex scenes
- People (generic descriptions for privacy)
- Technical diagrams

## 🔒 Privacy Note

- Images are sent to Hugging Face for processing
- Not stored permanently on their servers
- Processed server-to-server (secure)
- Your token is stored in your WordPress database
- Review [Hugging Face Privacy Policy](https://huggingface.co/privacy)

## 💰 Cost

**Completely FREE!**
- Free Hugging Face account
- Free API access (with rate limits)
- ~1000 images per day on free tier
- No credit card required
- Open source plugin

## 🌟 Rate Limits (Free Account)

- ~1000 requests per day
- ~50 requests per hour
- If you need more, consider Hugging Face PRO

## 📞 Support

Need help?
- 📖 [Full Documentation](README.md)
- 💬 [GitHub Issues](https://github.com/fui86/Titoli-foto---Wordpress/issues)
- 📧 Contact repository maintainer

---

**Ready? Let's get started! / Pronti? Iniziamo!** 🚀

# 🔧 Soluzione Errore HTTP 410 - Modello Non Disponibile

## 🚨 Problema

Quando il plugin mostra questo errore:

```
✗ ERROR - Errore API (codice 410): [lungo codice HTML/JavaScript]
```

**Significato**: HTTP 410 significa "Gone" - il modello AI che stai usando non è più disponibile su Hugging Face.

## ✅ Soluzione Rapida (2 Minuti)

### Passo 1: Vai alle Impostazioni
1. Nel pannello WordPress, vai su **Impostazioni → Image AI Metadata**
2. Scorri fino al campo **"Endpoint API"**

### Passo 2: Cambia Modello
Sostituisci l'URL attuale con UNO di questi modelli alternativi (copia e incolla):

**🥇 CONSIGLIATO - BLIP Base (più veloce e stabile)**:
```
https://api-inference.huggingface.co/models/Salesforce/blip-image-captioning-base
```

**🥈 Alternativa - ViT-GPT2**:
```
https://api-inference.huggingface.co/models/nlpconnect/vit-gpt2-image-captioning
```

**🥉 Alternativa - Microsoft GIT**:
```
https://api-inference.huggingface.co/models/microsoft/git-base
```

### Passo 3: Salva e Testa
1. Clicca **"Salva modifiche"**
2. Vai su **Media → Elaborazione Bulk AI**
3. Clicca **"Scansiona Immagini"**
4. Clicca **"Inizia Elaborazione"**

## 📊 Confronto Modelli

| Modello | Velocità | Qualità | Stato |
|---------|----------|---------|-------|
| BLIP Large | Media | Alta | ❌ Non disponibile (410) |
| BLIP Base | Veloce | Buona | ✅ Funzionante |
| ViT-GPT2 | Media | Buona | ✅ Funzionante |
| Microsoft GIT | Veloce | Buona | ✅ Funzionante |

## 🔍 Come Verificare che Funziona

Dopo aver cambiato l'endpoint, nel log di debug vedrai:

✅ **Prima** (errore):
```
[15:22:36] ✗ ERROR - saxofonista.webp - Errore API (codice 410): [HTML...]
```

✅ **Dopo** (successo):
```
[15:22:36] ✓ SUCCESS - saxofonista.webp - Alt text: "a person playing a saxophone"
```

## 💡 Perché Succede?

- Hugging Face aggiorna/rimuove periodicamente i modelli
- I modelli vecchi vengono deprecati
- HTTP 410 significa che il modello è stato rimosso definitivamente
- Devi usare un modello alternativo

## 📝 Note Tecniche

**Il plugin ora gestisce automaticamente gli errori HTTP**:

- **410**: Messaggio chiaro + suggerimento modelli alternativi
- **403/401**: Problema con il token API
- **404**: Endpoint non trovato
- **429**: Limite richieste raggiunto
- **500/502/503**: Server Hugging Face non disponibile

**Nessun codice HTML/JavaScript** viene più mostrato negli errori!

## 🆘 Serve Aiuto?

Se continui ad avere problemi:

1. **Verifica il token API** è valido su https://huggingface.co/settings/tokens
2. **Controlla i log** nella console di debug
3. **Leggi TROUBLESHOOTING.md** per diagnostica completa
4. **Apri un issue** su GitHub con il log completo

---

## 🎯 Quick Fix (English)

### The Problem
HTTP 410 error means the AI model you're using is no longer available on Hugging Face.

### The Solution
Go to **Settings → Image AI Metadata** and replace the API Endpoint with:
```
https://api-inference.huggingface.co/models/Salesforce/blip-image-captioning-base
```

Save and test. Done! ✅

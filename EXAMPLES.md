# Esempi di utilizzo / Usage Examples

## 🖼️ Esempi di riconoscimento immagini

### Esempio 1: Foto di paesaggio

**Input**: Foto di montagne innevate con un lago

**Output AI**:
```
Alt text: a lake surrounded by mountains with snow on them
Titolo: A lake surrounded by mountains with snow on them
Didascalia: a lake surrounded by mountains with snow on them
Descrizione: a lake surrounded by mountains with snow on them
```

### Esempio 2: Foto di animali

**Input**: Foto di un cane golden retriever

**Output AI**:
```
Alt text: a golden retriever dog sitting in the grass
Titolo: A golden retriever dog sitting in the grass
Didascalia: a golden retriever dog sitting in the grass
Descrizione: a golden retriever dog sitting in the grass
```

### Esempio 3: Foto di oggetti

**Input**: Foto di una tazza di caffè su una scrivania

**Output AI**:
```
Alt text: a cup of coffee on a wooden table
Titolo: A cup of coffee on a wooden table
Didascalia: a cup of coffee on a wooden table
Descrizione: a cup of coffee on a wooden table
```

### Esempio 4: Foto di persone (generica)

**Input**: Foto di persone che camminano in città

**Output AI**:
```
Alt text: people walking on a city street
Titolo: People walking on a city street
Didascalia: people walking on a city street
Descrizione: people walking on a city street
```

### Esempio 5: Foto di cibo

**Input**: Foto di una pizza margherita

**Output AI**:
```
Alt text: a pizza with cheese and tomatoes on it
Titolo: A pizza with cheese and tomatoes on it
Didascalia: a pizza with cheese and tomatoes on it
Descrizione: a pizza with cheese and tomatoes on it
```

---

## 🔧 Esempi di configurazione

### Configurazione base (consigliata)

```
API Token: hf_xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
Endpoint: https://api-inference.huggingface.co/models/Salesforce/blip-image-captioning-large
Elaborazione automatica: ✓ Abilitata
```

### Configurazione per sito multilingua

```
API Token: hf_xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
Endpoint: https://api-inference.huggingface.co/models/Salesforce/blip-image-captioning-large
Elaborazione automatica: ✗ Disabilitata
```

*Nota: Con elaborazione automatica disabilitata, puoi tradurre manualmente le descrizioni prima di pubblicare.*

### Configurazione per sito ad alto traffico

```
API Token: hf_xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
Endpoint: https://api-inference.huggingface.co/models/nlpconnect/vit-gpt2-image-captioning
Elaborazione automatica: ✓ Abilitata
```

*Nota: Il modello ViT-GPT2 è più veloce per elaborare grandi volumi di immagini.*

---

## 📝 Casi d'uso reali

### Blog fotografico

**Scenario**: Un fotografo carica 50 foto di un matrimonio.

**Soluzione**:
1. Abilita l'elaborazione automatica
2. Carica tutte le 50 foto in batch
3. Il plugin elabora automaticamente tutte le foto
4. Rivedi e affina manualmente solo le foto più importanti
5. Pubblica la galleria

**Beneficio**: Risparmio del 90% del tempo nella compilazione dei metadati.

### E-commerce

**Scenario**: Un negozio online con centinaia di prodotti.

**Soluzione**:
1. Carica le foto dei prodotti
2. Il plugin genera descrizioni automatiche
3. Modifica le descrizioni per aggiungere dettagli specifici del prodotto
4. Mantieni l'alt text AI per la SEO

**Beneficio**: Base solida per SEO + descrizioni accurate per l'accessibilità.

### Sito di notizie

**Scenario**: Redazione che pubblica 20-30 articoli al giorno con immagini.

**Soluzione**:
1. Giornalisti caricano immagini degli articoli
2. Plugin genera alt text automaticamente
3. Redattori aggiungono contesto specifico alla didascalia
4. Alt text rimane accurato per accessibilità

**Beneficio**: Conformità automatica agli standard di accessibilità.

### Portfolio artistico

**Scenario**: Artista con portfolio di opere d'arte.

**Soluzione**:
1. Disabilita elaborazione automatica
2. Carica opere d'arte
3. Usa il pulsante "Rielabora con AI" per ogni opera
4. Personalizza titolo e descrizione con interpretazione artistica
5. Mantieni l'alt text AI per descrizione oggettiva

**Beneficio**: Equilibrio tra descrizione oggettiva e interpretazione artistica.

---

## 🎯 Best practices

### SEO e accessibilità

```html
<!-- Prima del plugin -->
<img src="foto123.jpg" alt="" />

<!-- Dopo il plugin -->
<img src="foto123.jpg" alt="a beautiful sunset over the ocean with orange and pink sky" />
```

**Benefici SEO**:
- I motori di ricerca comprendono il contenuto dell'immagine
- Migliora il ranking per ricerche di immagini
- Fornisce contesto per l'indicizzazione della pagina

**Benefici accessibilità**:
- Screen reader descrivono l'immagine agli utenti non vedenti
- Conforme alle linee guida WCAG 2.1
- Migliora l'esperienza utente per tutti

### Ottimizzazione descrizioni

**AI genera**: "a red car parked on the street"

**Ottimizzazione manuale per e-commerce**:
```
Alt text: red sports car Ferrari 488 GTB parked on city street
Titolo: Ferrari 488 GTB - Vista Laterale
Didascalia: Ferrari 488 GTB rossa in perfette condizioni
Descrizione: Supercar Ferrari 488 GTB di colore rosso, motore V8 3.9L, 
condizioni eccellenti, fotografata su strada urbana. Ideale per 
appassionati di auto sportive italiane.
```

### Gestione delle eccezioni

**Foto astratte o artistiche**:
- L'AI potrebbe non generare descrizioni accurate
- Rivedi e modifica manualmente
- Mantieni comunque un alt text descrittivo

**Foto con testo/loghi**:
- L'AI descrive l'immagine ma non legge il testo
- Aggiungi manualmente il testo presente nell'immagine
- Importante per accessibilità

**Grafici e diagrammi**:
- L'AI fornisce descrizione visiva generale
- Aggiungi manualmente i dati specifici del grafico
- Considera di aggiungere i dati anche nel testo della pagina

---

## 🌍 Supporto multilingua

### Workflow per siti multilingua

1. **Carica immagine** → Plugin genera descrizione in inglese
2. **Traduci manualmente** → Traduci la descrizione nella tua lingua
3. **Usa plugin WPML/Polylang** → Assegna traduzioni alle versioni linguistiche

### Esempio: Italiano

**AI (inglese)**: "a cat sitting on a window sill"

**Traduzione manuale**:
```
Alt text: un gatto seduto sul davanzale della finestra
Titolo: Gatto sul davanzale
Didascalia: Un gatto domestico si riposa sul davanzale
Descrizione: Fotografia di un gatto domestico che si riposa comodamente 
su un davanzale, godendosi la luce naturale che entra dalla finestra.
```

---

## 📊 Performance e limiti

### Tempi di elaborazione tipici

- Immagine piccola (< 500KB): 2-5 secondi
- Immagine media (500KB - 2MB): 5-8 secondi
- Immagine grande (> 2MB): 8-15 secondi

### Rate limits Hugging Face (account gratuito)

- ~1000 richieste al giorno
- ~50 richieste all'ora
- Se superi i limiti, attendi o considera un account a pagamento

### Consigli per grandi volumi

1. **Elabora in batch piccoli**: 10-20 immagini alla volta
2. **Usa orari di basso traffico**: Notte o weekend
3. **Prioritizza le immagini**: Elabora prima quelle più importanti
4. **Considera account premium**: Per siti con migliaia di immagini

---

## 🐛 Debug e troubleshooting

### Test del plugin

```php
// Testa l'API manualmente (aggiungi a functions.php temporaneamente)
function test_image_ai_api() {
    $token = get_option('image_ai_metadata_api_token');
    $endpoint = get_option('image_ai_metadata_api_endpoint');
    
    // Usa un'immagine di test
    $image_url = 'https://huggingface.co/datasets/huggingface/documentation-images/resolve/main/transformers/tasks/car.jpg';
    
    $response = wp_remote_post($endpoint, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $token,
        ),
        'body' => file_get_contents($image_url),
        'timeout' => 30
    ));
    
    error_log('API Response: ' . print_r($response, true));
}
add_action('init', 'test_image_ai_api');
```

### Log degli errori

Controlla i log di WordPress in `wp-content/debug.log` (se WP_DEBUG è abilitato):

```php
// Aggiungi a wp-config.php per debug
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

---

## 💬 FAQ

**Q: Le descrizioni sono sempre in inglese?**  
A: Sì, i modelli attuali generano principalmente in inglese. Puoi tradurre manualmente o attendere modelli multilingua.

**Q: Posso usare il plugin offline?**  
A: No, richiede connessione internet per comunicare con l'API Hugging Face.

**Q: Il plugin funziona con WooCommerce?**  
A: Sì, funziona con qualsiasi immagine caricata nella libreria media di WordPress.

**Q: Posso usare un mio modello AI?**  
A: Sì, se il tuo modello è compatibile con l'API Inference di Hugging Face.

**Q: Il plugin salva immagini su server esterni?**  
A: No, le immagini sono inviate solo temporaneamente per l'elaborazione e non vengono salvate.

---

Per altre domande, apri un issue su GitHub!

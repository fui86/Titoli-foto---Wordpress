<?php
/**
 * Plugin Name: Image AI Metadata
 * Plugin URI: https://github.com/fui86/Titoli-foto---Wordpress
 * Description: Riconosce automaticamente il contenuto delle immagini usando AI e compila i campi Testo alternativo, Titolo, Didascalia e Descrizione.
 * Version: 1.0.0
 * Author: fui86
 * Text Domain: image-ai-metadata
 * Domain Path: /languages
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('IMAGE_AI_METADATA_VERSION', '1.0.0');
define('IMAGE_AI_METADATA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('IMAGE_AI_METADATA_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main plugin class
 */
class Image_AI_Metadata {
    
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * Get the singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        // Load text domain for translations
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        
        // Add settings page
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        
        // Process images on upload
        add_action('add_attachment', array($this, 'process_new_image'), 10, 1);
        
        // Add meta box to media edit page
        add_action('add_meta_boxes_attachment', array($this, 'add_meta_box'));
        
        // Handle manual processing
        add_action('admin_post_image_ai_metadata_process', array($this, 'handle_manual_process'));
        
        // Add admin styles and scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        
        // AJAX handlers for bulk processing
        add_action('wp_ajax_image_ai_get_images', array($this, 'ajax_get_images'));
        add_action('wp_ajax_image_ai_process_image', array($this, 'ajax_process_image'));
    }
    
    /**
     * Load plugin text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain('image-ai-metadata', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Settings page
        add_options_page(
            __('Image AI Metadata Settings', 'image-ai-metadata'),
            __('Image AI Metadata', 'image-ai-metadata'),
            'manage_options',
            'image-ai-metadata',
            array($this, 'render_settings_page')
        );
        
        // Bulk processing page under Media menu
        add_media_page(
            __('Elaborazione Bulk AI', 'image-ai-metadata'),
            __('Elaborazione Bulk AI', 'image-ai-metadata'),
            'upload_files',
            'image-ai-bulk-process',
            array($this, 'render_bulk_process_page')
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('image_ai_metadata_options', 'image_ai_metadata_api_token');
        register_setting('image_ai_metadata_options', 'image_ai_metadata_auto_process');
        register_setting('image_ai_metadata_options', 'image_ai_metadata_api_endpoint');
        
        add_settings_section(
            'image_ai_metadata_main',
            __('Configurazione API', 'image-ai-metadata'),
            array($this, 'render_settings_section'),
            'image-ai-metadata'
        );
        
        add_settings_field(
            'image_ai_metadata_api_token',
            __('Hugging Face API Token', 'image-ai-metadata'),
            array($this, 'render_api_token_field'),
            'image-ai-metadata',
            'image_ai_metadata_main'
        );
        
        add_settings_field(
            'image_ai_metadata_api_endpoint',
            __('Endpoint API (opzionale)', 'image-ai-metadata'),
            array($this, 'render_api_endpoint_field'),
            'image-ai-metadata',
            'image_ai_metadata_main'
        );
        
        add_settings_field(
            'image_ai_metadata_auto_process',
            __('Elaborazione automatica', 'image-ai-metadata'),
            array($this, 'render_auto_process_field'),
            'image-ai-metadata',
            'image_ai_metadata_main'
        );
    }
    
    /**
     * Render settings section
     */
    public function render_settings_section() {
        echo '<p>' . __('Configura le impostazioni per il riconoscimento automatico delle immagini tramite AI.', 'image-ai-metadata') . '</p>';
        echo '<p>' . sprintf(
            __('Ottieni un token API gratuito da <a href="%s" target="_blank">Hugging Face</a>.', 'image-ai-metadata'),
            'https://huggingface.co/settings/tokens'
        ) . '</p>';
    }
    
    /**
     * Render API token field
     */
    public function render_api_token_field() {
        $value = get_option('image_ai_metadata_api_token', '');
        echo '<input type="text" name="image_ai_metadata_api_token" value="' . esc_attr($value) . '" size="50" />';
        echo '<p class="description">' . __('Il token API di Hugging Face per accedere al servizio di riconoscimento immagini.', 'image-ai-metadata') . '</p>';
    }
    
    /**
     * Render API endpoint field
     */
    public function render_api_endpoint_field() {
        $value = get_option('image_ai_metadata_api_endpoint', 'https://api-inference.huggingface.co/models/Salesforce/blip-image-captioning-large');
        echo '<input type="text" name="image_ai_metadata_api_endpoint" value="' . esc_attr($value) . '" size="80" />';
        echo '<p class="description">' . __('Endpoint API per il modello di riconoscimento immagini (predefinito: BLIP Image Captioning).', 'image-ai-metadata') . '</p>';
    }
    
    /**
     * Render auto process field
     */
    public function render_auto_process_field() {
        $value = get_option('image_ai_metadata_auto_process', '1');
        echo '<label><input type="checkbox" name="image_ai_metadata_auto_process" value="1" ' . checked($value, '1', false) . ' />';
        echo ' ' . __('Elabora automaticamente le immagini al caricamento', 'image-ai-metadata') . '</label>';
        echo '<p class="description">' . __('Se disabilitato, dovrai elaborare manualmente ogni immagine dalla pagina di modifica.', 'image-ai-metadata') . '</p>';
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Show saved message
        if (isset($_GET['settings-updated'])) {
            add_settings_error(
                'image_ai_metadata_messages',
                'image_ai_metadata_message',
                __('Impostazioni salvate', 'image-ai-metadata'),
                'updated'
            );
        }
        
        settings_errors('image_ai_metadata_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('image_ai_metadata_options');
                do_settings_sections('image-ai-metadata');
                submit_button(__('Salva impostazioni', 'image-ai-metadata'));
                ?>
            </form>
            
            <hr>
            
            <h2><?php _e('Come funziona', 'image-ai-metadata'); ?></h2>
            <ol>
                <li><?php _e('Registrati gratuitamente su Hugging Face e ottieni un token API', 'image-ai-metadata'); ?></li>
                <li><?php _e('Inserisci il token API nel campo sopra e salva', 'image-ai-metadata'); ?></li>
                <li><?php _e('Carica le tue immagini nella libreria media di WordPress', 'image-ai-metadata'); ?></li>
                <li><?php _e('Il plugin analizzerà automaticamente le immagini e compilerà i metadati', 'image-ai-metadata'); ?></li>
            </ol>
            
            <h2><?php _e('Modelli AI supportati', 'image-ai-metadata'); ?></h2>
            <ul>
                <li><strong>BLIP Image Captioning</strong> (predefinito) - Generazione di descrizioni dettagliate</li>
                <li><strong>ViT GPT2 Image Captioning</strong> - Alternativa per descrizioni più creative</li>
            </ul>
            
            <hr>
            
            <h2><?php _e('Diagnostica', 'image-ai-metadata'); ?></h2>
            <div style="background: #f0f0f1; padding: 15px; border-left: 4px solid #72aee6;">
                <h3><?php _e('Stato Configurazione', 'image-ai-metadata'); ?></h3>
                <ul>
                    <li>
                        <strong><?php _e('Token API:', 'image-ai-metadata'); ?></strong>
                        <?php 
                        $token = get_option('image_ai_metadata_api_token');
                        if (!empty($token)) {
                            echo ' <span style="color: green;">✓ ' . __('Configurato', 'image-ai-metadata') . '</span>';
                            echo ' (' . substr($token, 0, 7) . '...)';
                        } else {
                            echo ' <span style="color: red;">✗ ' . __('Non configurato', 'image-ai-metadata') . '</span>';
                        }
                        ?>
                    </li>
                    <li>
                        <strong><?php _e('Elaborazione automatica:', 'image-ai-metadata'); ?></strong>
                        <?php 
                        $auto = get_option('image_ai_metadata_auto_process', '1');
                        if ($auto === '1') {
                            echo ' <span style="color: green;">✓ ' . __('Abilitata', 'image-ai-metadata') . '</span>';
                        } else {
                            echo ' <span style="color: orange;">⚠ ' . __('Disabilitata', 'image-ai-metadata') . '</span>';
                        }
                        ?>
                    </li>
                    <li>
                        <strong>PHP Version:</strong> <?php echo PHP_VERSION; ?>
                        <?php if (version_compare(PHP_VERSION, '7.0.0', '>=')) {
                            echo ' <span style="color: green;">✓</span>';
                        } else {
                            echo ' <span style="color: red;">✗ (minimo 7.0)</span>';
                        } ?>
                    </li>
                    <li>
                        <strong>cURL:</strong>
                        <?php if (function_exists('curl_init')) {
                            echo ' <span style="color: green;">✓ ' . __('Disponibile', 'image-ai-metadata') . '</span>';
                        } else {
                            echo ' <span style="color: red;">✗ ' . __('Non disponibile', 'image-ai-metadata') . '</span>';
                        } ?>
                    </li>
                    <li>
                        <strong>allow_url_fopen:</strong>
                        <?php if (ini_get('allow_url_fopen')) {
                            echo ' <span style="color: green;">✓ ' . __('Abilitato', 'image-ai-metadata') . '</span>';
                        } else {
                            echo ' <span style="color: orange;">⚠ ' . __('Disabilitato', 'image-ai-metadata') . '</span>';
                        } ?>
                    </li>
                </ul>
                
                <h3><?php _e('Problemi comuni', 'image-ai-metadata'); ?></h3>
                <ul>
                    <li>✓ <strong><?php _e('Token non valido:', 'image-ai-metadata'); ?></strong> <?php _e('Verifica su Hugging Face che il token sia attivo', 'image-ai-metadata'); ?></li>
                    <li>✓ <strong><?php _e('Modello in caricamento:', 'image-ai-metadata'); ?></strong> <?php _e('Attendi 20-30 secondi e riprova', 'image-ai-metadata'); ?></li>
                    <li>✓ <strong><?php _e('Nessun metadata:', 'image-ai-metadata'); ?></strong> <?php _e('Verifica che l\'elaborazione automatica sia abilitata', 'image-ai-metadata'); ?></li>
                </ul>
                
                <p>
                    <strong><?php _e('Guida completa:', 'image-ai-metadata'); ?></strong>
                    <a href="https://github.com/fui86/Titoli-foto---Wordpress/blob/copilot/add-wordpress-image-ai-plugin/TROUBLESHOOTING.md" target="_blank">
                        <?php _e('Leggi la guida alla risoluzione dei problemi', 'image-ai-metadata'); ?> →
                    </a>
                </p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Process new image on upload
     */
    public function process_new_image($attachment_id) {
        // Check if auto-process is enabled
        if (get_option('image_ai_metadata_auto_process', '1') !== '1') {
            return;
        }
        
        // Check if it's an image
        if (!wp_attachment_is_image($attachment_id)) {
            return;
        }
        
        // Process the image
        $result = $this->analyze_and_update_image($attachment_id);
        
        // Log errors if debug is enabled
        if (is_wp_error($result) && defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Image AI Metadata Error (ID: ' . $attachment_id . '): ' . $result->get_error_message());
        }
    }
    
    /**
     * Add meta box to media edit page
     */
    public function add_meta_box($post) {
        if (wp_attachment_is_image($post->ID)) {
            add_meta_box(
                'image-ai-metadata-box',
                __('Riconoscimento AI Immagine', 'image-ai-metadata'),
                array($this, 'render_meta_box'),
                'attachment',
                'side',
                'default'
            );
        }
    }
    
    /**
     * Render meta box
     */
    public function render_meta_box($post) {
        $last_processed = get_post_meta($post->ID, '_image_ai_metadata_processed', true);
        
        if ($last_processed) {
            echo '<p>' . sprintf(
                __('Ultima elaborazione: %s', 'image-ai-metadata'),
                date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $last_processed)
            ) . '</p>';
        }
        
        echo '<p>';
        echo '<button type="button" class="button button-primary" onclick="document.getElementById(\'image-ai-metadata-form\').submit();">';
        echo __('Rielabora con AI', 'image-ai-metadata');
        echo '</button>';
        echo '</p>';
        
        echo '<form id="image-ai-metadata-form" method="post" action="' . esc_url(admin_url('admin-post.php')) . '" style="display:none;">';
        echo '<input type="hidden" name="action" value="image_ai_metadata_process" />';
        echo '<input type="hidden" name="attachment_id" value="' . absint($post->ID) . '" />';
        wp_nonce_field('image_ai_metadata_process', 'image_ai_metadata_nonce');
        echo '</form>';
        
        echo '<p class="description">' . __('Usa questo pulsante per rielaborare l\'immagine con l\'AI e aggiornare i metadati.', 'image-ai-metadata') . '</p>';
    }
    
    /**
     * Handle manual processing request
     */
    public function handle_manual_process() {
        // Verify nonce
        if (!isset($_POST['image_ai_metadata_nonce']) || !wp_verify_nonce($_POST['image_ai_metadata_nonce'], 'image_ai_metadata_process')) {
            wp_die(__('Errore di sicurezza', 'image-ai-metadata'));
        }
        
        // Check permissions
        if (!current_user_can('upload_files')) {
            wp_die(__('Non hai i permessi per eseguire questa azione', 'image-ai-metadata'));
        }
        
        $attachment_id = isset($_POST['attachment_id']) ? intval($_POST['attachment_id']) : 0;
        
        if ($attachment_id && wp_attachment_is_image($attachment_id)) {
            $result = $this->analyze_and_update_image($attachment_id);
            
            if (is_wp_error($result)) {
                wp_redirect(add_query_arg(array(
                    'post' => $attachment_id,
                    'action' => 'edit',
                    'image_ai_error' => urlencode($result->get_error_message())
                ), admin_url('post.php')));
            } else {
                wp_redirect(add_query_arg(array(
                    'post' => $attachment_id,
                    'action' => 'edit',
                    'image_ai_success' => '1'
                ), admin_url('post.php')));
            }
        } else {
            wp_die(__('ID allegato non valido', 'image-ai-metadata'));
        }
        
        exit;
    }
    
    /**
     * Analyze image using AI and update metadata
     */
    private function analyze_and_update_image($attachment_id) {
        $api_token = get_option('image_ai_metadata_api_token');
        
        if (empty($api_token)) {
            return new WP_Error('no_api_token', __('Token API non configurato. Configura il plugin nelle impostazioni.', 'image-ai-metadata'));
        }
        
        // Get image file path
        $image_path = get_attached_file($attachment_id);
        
        if (!file_exists($image_path)) {
            return new WP_Error('file_not_found', __('File immagine non trovato.', 'image-ai-metadata'));
        }
        
        // Call AI API
        $description = $this->call_ai_api($image_path, $api_token);
        
        if (is_wp_error($description)) {
            return $description;
        }
        
        // Update image metadata
        $this->update_image_metadata($attachment_id, $description);
        
        // Mark as processed
        update_post_meta($attachment_id, '_image_ai_metadata_processed', time());
        
        return true;
    }
    
    /**
     * Call AI API to analyze image
     */
    private function call_ai_api($image_path, $api_token) {
        $endpoint = get_option('image_ai_metadata_api_endpoint', 'https://api-inference.huggingface.co/models/Salesforce/blip-image-captioning-large');
        
        // Read image file
        $image_data = file_get_contents($image_path);
        
        if ($image_data === false) {
            return new WP_Error('read_error', __('Errore nella lettura del file immagine.', 'image-ai-metadata'));
        }
        
        // Prepare API request
        $response = wp_remote_post($endpoint, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_token,
                'Content-Type' => 'application/octet-stream'
            ),
            'body' => $image_data,
            'timeout' => 30
        ));
        
        if (is_wp_error($response)) {
            return new WP_Error('api_error', sprintf(
                __('Errore nella chiamata API: %s', 'image-ai-metadata'),
                $response->get_error_message()
            ));
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        if ($response_code !== 200) {
            return new WP_Error('api_error', sprintf(
                __('Errore API (codice %d): %s', 'image-ai-metadata'),
                $response_code,
                $body
            ));
        }
        
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('json_error', __('Errore nella decodifica della risposta JSON.', 'image-ai-metadata'));
        }
        
        // Extract description from response
        if (isset($data[0]['generated_text'])) {
            return sanitize_text_field($data[0]['generated_text']);
        }
        
        return new WP_Error('invalid_response', __('Risposta API non valida.', 'image-ai-metadata'));
    }
    
    /**
     * Update image metadata with AI description
     */
    private function update_image_metadata($attachment_id, $description) {
        // Capitalize first letter for title
        $title = ucfirst($description);
        
        // Update post data
        wp_update_post(array(
            'ID' => $attachment_id,
            'post_title' => $title,
            'post_excerpt' => $description, // Caption
            'post_content' => $description  // Description
        ));
        
        // Update alt text
        update_post_meta($attachment_id, '_wp_attachment_image_alt', $description);
    }
    
    /**
     * Enqueue admin styles and scripts
     */
    public function enqueue_admin_styles($hook) {
        if ($hook === 'post.php' || $hook === 'upload.php') {
            // Add admin notice for success/error messages
            add_action('admin_notices', array($this, 'show_admin_notices'));
        }
        
        // Enqueue scripts for bulk processing page
        if ($hook === 'media_page_image-ai-bulk-process') {
            wp_enqueue_style('image-ai-bulk-style', false);
            wp_add_inline_style('image-ai-bulk-style', $this->get_bulk_page_css());
            
            wp_enqueue_script('image-ai-bulk-script', false, array('jquery'), IMAGE_AI_METADATA_VERSION, true);
            wp_add_inline_script('image-ai-bulk-script', $this->get_bulk_page_js());
            
            wp_localize_script('image-ai-bulk-script', 'imageAIBulk', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('image_ai_bulk_nonce'),
                'strings' => array(
                    'processing' => __('Elaborazione in corso...', 'image-ai-metadata'),
                    'completed' => __('Completato', 'image-ai-metadata'),
                    'failed' => __('Fallito', 'image-ai-metadata'),
                    'success' => __('Successo', 'image-ai-metadata'),
                    'error' => __('Errore', 'image-ai-metadata'),
                )
            ));
        }
    }
    
    /**
     * Show admin notices
     */
    public function show_admin_notices() {
        if (isset($_GET['image_ai_success'])) {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p>' . __('Immagine elaborata con successo! I metadati sono stati aggiornati.', 'image-ai-metadata') . '</p>';
            echo '</div>';
        }
        
        if (isset($_GET['image_ai_error'])) {
            echo '<div class="notice notice-error is-dismissible">';
            echo '<p>' . sprintf(__('Errore: %s', 'image-ai-metadata'), urldecode($_GET['image_ai_error'])) . '</p>';
            echo '</div>';
        }
    }
    
    /**
     * Render bulk processing page
     */
    public function render_bulk_process_page() {
        if (!current_user_can('upload_files')) {
            wp_die(__('Non hai i permessi per accedere a questa pagina.', 'image-ai-metadata'));
        }
        
        $api_token = get_option('image_ai_metadata_api_token');
        ?>
        <div class="wrap">
            <h1><?php _e('Elaborazione Bulk AI - Immagini', 'image-ai-metadata'); ?></h1>
            
            <?php if (empty($api_token)): ?>
                <div class="notice notice-error">
                    <p>
                        <strong><?php _e('Token API non configurato!', 'image-ai-metadata'); ?></strong><br>
                        <?php printf(
                            __('Vai su <a href="%s">Impostazioni → Image AI Metadata</a> per configurare il token API.', 'image-ai-metadata'),
                            admin_url('options-general.php?page=image-ai-metadata')
                        ); ?>
                    </p>
                </div>
            <?php else: ?>
                
                <div class="bulk-controls">
                    <h2><?php _e('Seleziona Immagini da Elaborare', 'image-ai-metadata'); ?></h2>
                    
                    <div class="filter-options">
                        <label>
                            <input type="radio" name="filter_type" value="all" checked>
                            <strong><?php _e('Tutte le immagini', 'image-ai-metadata'); ?></strong>
                            <span class="description"><?php _e('Elabora tutte le immagini nella libreria media', 'image-ai-metadata'); ?></span>
                        </label>
                        <br><br>
                        <label>
                            <input type="radio" name="filter_type" value="unprocessed">
                            <strong><?php _e('Solo immagini non processate', 'image-ai-metadata'); ?></strong>
                            <span class="description"><?php _e('Elabora solo le immagini mai processate dall\'AI', 'image-ai-metadata'); ?></span>
                        </label>
                    </div>
                    
                    <p>
                        <button id="btn-scan-images" class="button button-primary button-large">
                            <span class="dashicons dashicons-search"></span>
                            <?php _e('Scansiona Immagini', 'image-ai-metadata'); ?>
                        </button>
                    </p>
                </div>
                
                <div id="images-found" style="display:none;">
                    <h3><?php _e('Immagini Trovate', 'image-ai-metadata'); ?>: <span id="images-count">0</span></h3>
                    <p>
                        <button id="btn-start-processing" class="button button-primary button-large">
                            <span class="dashicons dashicons-update"></span>
                            <?php _e('Inizia Elaborazione', 'image-ai-metadata'); ?>
                        </button>
                        <button id="btn-stop-processing" class="button button-secondary" style="display:none;">
                            <span class="dashicons dashicons-no"></span>
                            <?php _e('Ferma Elaborazione', 'image-ai-metadata'); ?>
                        </button>
                    </p>
                    
                    <div class="progress-bar-container">
                        <div class="progress-bar">
                            <div class="progress-bar-fill" id="progress-bar-fill"></div>
                        </div>
                        <div class="progress-text">
                            <span id="progress-text"><?php _e('Pronto per iniziare', 'image-ai-metadata'); ?></span>
                            <span id="progress-percent">0%</span>
                        </div>
                    </div>
                </div>
                
                <div id="debug-log">
                    <h3>
                        <span class="dashicons dashicons-admin-tools"></span>
                        <?php _e('Log di Debug', 'image-ai-metadata'); ?>
                    </h3>
                    <div id="debug-output"></div>
                    <p>
                        <button id="btn-clear-log" class="button button-small">
                            <?php _e('Pulisci Log', 'image-ai-metadata'); ?>
                        </button>
                        <button id="btn-copy-log" class="button button-small">
                            <?php _e('Copia Log', 'image-ai-metadata'); ?>
                        </button>
                    </p>
                </div>
                
                <div id="results-summary" style="display:none;">
                    <h3><?php _e('Riepilogo Elaborazione', 'image-ai-metadata'); ?></h3>
                    <div class="results-grid">
                        <div class="result-box result-success">
                            <div class="result-number" id="count-success">0</div>
                            <div class="result-label"><?php _e('Successo', 'image-ai-metadata'); ?></div>
                        </div>
                        <div class="result-box result-failed">
                            <div class="result-number" id="count-failed">0</div>
                            <div class="result-label"><?php _e('Falliti', 'image-ai-metadata'); ?></div>
                        </div>
                        <div class="result-box result-total">
                            <div class="result-number" id="count-total">0</div>
                            <div class="result-label"><?php _e('Totale', 'image-ai-metadata'); ?></div>
                        </div>
                    </div>
                </div>
                
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * AJAX: Get images to process
     */
    public function ajax_get_images() {
        check_ajax_referer('image_ai_bulk_nonce', 'nonce');
        
        if (!current_user_can('upload_files')) {
            wp_send_json_error(array('message' => __('Permessi insufficienti', 'image-ai-metadata')));
        }
        
        $filter_type = isset($_POST['filter_type']) ? sanitize_text_field($_POST['filter_type']) : 'all';
        
        $args = array(
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'post_status' => 'inherit',
            'posts_per_page' => -1,
            'fields' => 'ids',
        );
        
        if ($filter_type === 'unprocessed') {
            $args['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key' => '_image_ai_metadata_processed',
                    'compare' => 'NOT EXISTS',
                ),
                array(
                    'key' => '_image_ai_metadata_processed',
                    'value' => '',
                    'compare' => '=',
                ),
            );
        }
        
        $images = get_posts($args);
        
        wp_send_json_success(array(
            'count' => count($images),
            'images' => $images,
        ));
    }
    
    /**
     * AJAX: Process single image
     */
    public function ajax_process_image() {
        check_ajax_referer('image_ai_bulk_nonce', 'nonce');
        
        if (!current_user_can('upload_files')) {
            wp_send_json_error(array('message' => __('Permessi insufficienti', 'image-ai-metadata')));
        }
        
        $attachment_id = isset($_POST['attachment_id']) ? intval($_POST['attachment_id']) : 0;
        
        if (!$attachment_id || !wp_attachment_is_image($attachment_id)) {
            wp_send_json_error(array(
                'message' => __('ID immagine non valido', 'image-ai-metadata'),
                'attachment_id' => $attachment_id,
            ));
        }
        
        // Get image info for debug
        $image_file = get_attached_file($attachment_id);
        $image_url = wp_get_attachment_url($attachment_id);
        $image_title = get_the_title($attachment_id);
        
        // Process the image
        $result = $this->analyze_and_update_image($attachment_id);
        
        if (is_wp_error($result)) {
            wp_send_json_error(array(
                'message' => $result->get_error_message(),
                'attachment_id' => $attachment_id,
                'image_title' => $image_title,
                'image_url' => $image_url,
                'image_file' => basename($image_file),
            ));
        }
        
        // Get updated metadata to show in response
        $alt_text = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
        
        wp_send_json_success(array(
            'message' => __('Elaborato con successo', 'image-ai-metadata'),
            'attachment_id' => $attachment_id,
            'image_title' => get_the_title($attachment_id),
            'image_url' => $image_url,
            'image_file' => basename($image_file),
            'alt_text' => $alt_text,
        ));
    }
    
    /**
     * Get CSS for bulk processing page
     */
    private function get_bulk_page_css() {
        return "
            .bulk-controls {
                background: #fff;
                border: 1px solid #ccd0d4;
                padding: 20px;
                margin: 20px 0;
                box-shadow: 0 1px 1px rgba(0,0,0,.04);
            }
            .filter-options label {
                display: block;
                padding: 10px;
                background: #f8f9fa;
                border-left: 3px solid #2271b1;
                margin: 5px 0;
            }
            .filter-options label:hover {
                background: #f0f0f1;
            }
            .filter-options .description {
                display: block;
                font-size: 13px;
                color: #646970;
                margin-top: 5px;
            }
            .progress-bar-container {
                background: #fff;
                border: 1px solid #ccd0d4;
                padding: 20px;
                margin: 20px 0;
            }
            .progress-bar {
                width: 100%;
                height: 30px;
                background: #f0f0f1;
                border-radius: 3px;
                overflow: hidden;
                margin-bottom: 10px;
            }
            .progress-bar-fill {
                height: 100%;
                background: linear-gradient(90deg, #2271b1 0%, #135e96 100%);
                width: 0%;
                transition: width 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: bold;
            }
            .progress-text {
                display: flex;
                justify-content: space-between;
                font-size: 14px;
            }
            #debug-log {
                background: #1e1e1e;
                color: #d4d4d4;
                padding: 20px;
                margin: 20px 0;
                border-radius: 3px;
                font-family: 'Courier New', monospace;
            }
            #debug-log h3 {
                color: #fff;
                margin-top: 0;
            }
            #debug-output {
                background: #000;
                padding: 15px;
                border-radius: 3px;
                max-height: 400px;
                overflow-y: auto;
                font-size: 13px;
                line-height: 1.6;
            }
            .log-entry {
                margin: 5px 0;
                padding: 5px;
                border-left: 3px solid #646970;
            }
            .log-entry.log-info {
                border-left-color: #2271b1;
                color: #4cc9f0;
            }
            .log-entry.log-success {
                border-left-color: #00a32a;
                color: #7ed321;
            }
            .log-entry.log-error {
                border-left-color: #d63638;
                color: #ff6b6b;
            }
            .log-entry.log-warning {
                border-left-color: #dba617;
                color: #f5c842;
            }
            .log-timestamp {
                color: #858585;
                font-size: 11px;
                margin-right: 10px;
            }
            .results-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
                margin-top: 20px;
            }
            .result-box {
                background: #fff;
                border: 1px solid #ccd0d4;
                padding: 20px;
                text-align: center;
                border-radius: 3px;
            }
            .result-box.result-success {
                border-left: 4px solid #00a32a;
            }
            .result-box.result-failed {
                border-left: 4px solid #d63638;
            }
            .result-box.result-total {
                border-left: 4px solid #2271b1;
            }
            .result-number {
                font-size: 48px;
                font-weight: bold;
                margin-bottom: 10px;
            }
            .result-success .result-number {
                color: #00a32a;
            }
            .result-failed .result-number {
                color: #d63638;
            }
            .result-total .result-number {
                color: #2271b1;
            }
            .result-label {
                font-size: 14px;
                color: #646970;
                text-transform: uppercase;
            }
            .button .dashicons {
                line-height: 28px;
            }
            #images-found {
                background: #fff;
                border: 1px solid #ccd0d4;
                padding: 20px;
                margin: 20px 0;
            }
        ";
    }
    
    /**
     * Get JavaScript for bulk processing page
     */
    private function get_bulk_page_js() {
        return "
            jQuery(document).ready(function($) {
                console.log('Image AI Bulk Processing: Script loaded');
                console.log('AJAX URL:', imageAIBulk.ajaxurl);
                console.log('Nonce:', imageAIBulk.nonce);
                
                var imagesToProcess = [];
                var currentIndex = 0;
                var successCount = 0;
                var failedCount = 0;
                var isProcessing = false;
                
                function addLog(message, type) {
                    type = type || 'info';
                    var timestamp = new Date().toLocaleTimeString();
                    var logEntry = $('<div class=\"log-entry log-' + type + '\"></div>')
                        .html('<span class=\"log-timestamp\">[' + timestamp + ']</span>' + message);
                    $('#debug-output').append(logEntry);
                    $('#debug-output').scrollTop($('#debug-output')[0].scrollHeight);
                    console.log('[' + type.toUpperCase() + '] ' + message);
                }
                
                // Add initial log message
                addLog('Sistema di debug inizializzato. Pronto per elaborare le immagini.', 'info');
                
                $('#btn-scan-images').on('click', function() {
                    var filterType = $('input[name=\"filter_type\"]:checked').val();
                    $(this).prop('disabled', true).html('<span class=\"dashicons dashicons-update spin\"></span> Scansione...');
                    
                    addLog('Inizio scansione immagini (filtro: ' + filterType + ')...', 'info');
                    addLog('Chiamata AJAX a: ' + imageAIBulk.ajaxurl, 'info');
                    
                    $.ajax({
                        url: imageAIBulk.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'image_ai_get_images',
                            nonce: imageAIBulk.nonce,
                            filter_type: filterType
                        },
                        success: function(response) {
                            addLog('Risposta ricevuta dal server', 'info');
                            if (response.success) {
                                imagesToProcess = response.data.images;
                                $('#images-count').text(response.data.count);
                                $('#images-found').fadeIn();
                                $('#count-total').text(response.data.count);
                                addLog('Trovate ' + response.data.count + ' immagini da elaborare.', 'success');
                            } else {
                                addLog('Errore: ' + (response.data.message || 'Errore sconosciuto'), 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            addLog('ERRORE AJAX: ' + status + ' - ' + error, 'error');
                            addLog('Status Code: ' + xhr.status, 'error');
                            addLog('Response Text: ' + xhr.responseText.substring(0, 200), 'error');
                        },
                        complete: function() {
                            $('#btn-scan-images').prop('disabled', false).html('<span class=\"dashicons dashicons-search\"></span> Scansiona Immagini');
                        }
                    });
                });
                
                $('#btn-start-processing').on('click', function() {
                    if (imagesToProcess.length === 0) {
                        addLog('Nessuna immagine da elaborare!', 'warning');
                        return;
                    }
                    
                    isProcessing = true;
                    currentIndex = 0;
                    successCount = 0;
                    failedCount = 0;
                    
                    $(this).hide();
                    $('#btn-stop-processing').show();
                    $('#results-summary').fadeIn();
                    
                    addLog('=================================', 'info');
                    addLog('INIZIO ELABORAZIONE BULK', 'info');
                    addLog('Totale immagini: ' + imagesToProcess.length, 'info');
                    addLog('=================================', 'info');
                    
                    processNextImage();
                });
                
                $('#btn-stop-processing').on('click', function() {
                    isProcessing = false;
                    $(this).hide();
                    $('#btn-start-processing').show();
                    addLog('Elaborazione fermata dall\'utente.', 'warning');
                });
                
                function processNextImage() {
                    if (!isProcessing || currentIndex >= imagesToProcess.length) {
                        completeProcessing();
                        return;
                    }
                    
                    var attachmentId = imagesToProcess[currentIndex];
                    var progress = Math.round((currentIndex / imagesToProcess.length) * 100);
                    
                    $('#progress-bar-fill').css('width', progress + '%');
                    $('#progress-percent').text(progress + '%');
                    $('#progress-text').text('Elaborazione immagine ' + (currentIndex + 1) + ' di ' + imagesToProcess.length);
                    
                    addLog('Elaborazione immagine ID: ' + attachmentId + '...', 'info');
                    
                    $.post(imageAIBulk.ajaxurl, {
                        action: 'image_ai_process_image',
                        nonce: imageAIBulk.nonce,
                        attachment_id: attachmentId
                    }, function(response) {
                        if (response.success) {
                            successCount++;
                            $('#count-success').text(successCount);
                            addLog('✓ SUCCESS - ' + response.data.image_file + ' - Alt text: \"' + response.data.alt_text + '\"', 'success');
                        } else {
                            failedCount++;
                            $('#count-failed').text(failedCount);
                            addLog('✗ ERROR - ' + (response.data.image_file || 'Unknown') + ' - ' + response.data.message, 'error');
                        }
                        
                        currentIndex++;
                        setTimeout(processNextImage, 500);
                    }).fail(function(xhr, status, error) {
                        failedCount++;
                        $('#count-failed').text(failedCount);
                        addLog('✗ AJAX ERROR - Image ID ' + attachmentId + ' - ' + error, 'error');
                        currentIndex++;
                        setTimeout(processNextImage, 500);
                    });
                }
                
                function completeProcessing() {
                    isProcessing = false;
                    $('#btn-stop-processing').hide();
                    $('#btn-start-processing').show();
                    $('#progress-bar-fill').css('width', '100%');
                    $('#progress-percent').text('100%');
                    $('#progress-text').text('Elaborazione completata!');
                    
                    addLog('=================================', 'info');
                    addLog('ELABORAZIONE COMPLETATA', 'success');
                    addLog('Successo: ' + successCount, 'success');
                    addLog('Falliti: ' + failedCount, 'error');
                    addLog('Totale: ' + imagesToProcess.length, 'info');
                    addLog('=================================', 'info');
                }
                
                $('#btn-clear-log').on('click', function() {
                    $('#debug-output').empty();
                    addLog('Log pulito.', 'info');
                });
                
                $('#btn-copy-log').on('click', function() {
                    var logText = $('#debug-output').text();
                    navigator.clipboard.writeText(logText).then(function() {
                        addLog('Log copiato negli appunti!', 'success');
                    });
                });
                
                // Add spin animation for loading icons
                var style = $('<style>.dashicons.spin { animation: spin 1s linear infinite; } @keyframes spin { to { transform: rotate(360deg); } }</style>');
                $('head').append(style);
            });
        ";
    }
}

// Initialize the plugin
function image_ai_metadata_init() {
    return Image_AI_Metadata::get_instance();
}

add_action('plugins_loaded', 'image_ai_metadata_init');

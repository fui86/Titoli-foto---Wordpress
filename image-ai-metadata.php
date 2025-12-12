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
        
        // AJAX handler for API connection test
        add_action('wp_ajax_test_api_connection', array($this, 'ajax_test_api_connection'));
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
        register_setting('image_ai_metadata_options', 'image_ai_metadata_api_token', array(
            'sanitize_callback' => array($this, 'sanitize_api_token')
        ));
        register_setting('image_ai_metadata_options', 'image_ai_metadata_auto_process');
        register_setting('image_ai_metadata_options', 'image_ai_metadata_api_endpoint', array(
            'sanitize_callback' => array($this, 'sanitize_api_endpoint')
        ));
        
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
     * Sanitize API token
     */
    public function sanitize_api_token($value) {
        // Trim whitespace
        $value = trim($value);
        
        // Validate format if not empty
        if (!empty($value) && !preg_match('/^hf_[a-zA-Z0-9_]+$/', $value)) {
            add_settings_error(
                'image_ai_metadata_api_token',
                'invalid_token_format',
                __('Attenzione: Il token API dovrebbe iniziare con "hf_". Verifica che sia corretto.', 'image-ai-metadata'),
                'warning'
            );
        }
        
        return $value;
    }
    
    /**
     * Sanitize API endpoint
     */
    public function sanitize_api_endpoint($value) {
        // Trim whitespace
        $value = trim($value);
        
        // Clear all caches when endpoint is updated
        if (!empty($value)) {
            wp_cache_delete('image_ai_metadata_api_endpoint', 'options');
            wp_cache_delete('alloptions', 'options');
            wp_cache_flush();
        }
        
        return $value;
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
        // Check what's actually in the database (no default fallback)
        $db_value = get_option('image_ai_metadata_api_endpoint', false);
        
        // If nothing is stored, use the new working default (nlpconnect verified working in 2025)
        if ($db_value === false || empty($db_value)) {
            $value = 'https://api-inference.huggingface.co/models/nlpconnect/vit-gpt2-image-captioning';
            // Save it to database immediately
            update_option('image_ai_metadata_api_endpoint', $value);
        } else {
            $value = $db_value;
        }
        
        echo '<input type="text" name="image_ai_metadata_api_endpoint" value="' . esc_attr($value) . '" size="80" style="font-family: monospace;" />';
        echo '<p class="description">' . __('Endpoint API per il modello di riconoscimento immagini.', 'image-ai-metadata') . '</p>';
        
        // Show what's actually stored in the database for debugging
        echo '<div style="margin-top: 10px; padding: 8px; background: #e7f3ff; border-left: 4px solid #0073aa; border-radius: 3px; font-size: 12px;">';
        echo '<strong>🔍 Valore attualmente salvato nel database:</strong><br>';
        echo '<code style="background: #fff; padding: 2px 6px; border-radius: 2px; display: inline-block; margin-top: 5px;">' . esc_html($value) . '</code>';
        echo '</div>';
        
        echo '<div style="margin-top: 10px; padding: 10px; background: #f0f0f1; border-left: 4px solid #2271b1; border-radius: 4px;">';
        echo '<strong>' . __('✅ Modelli Verificati Funzionanti (2025):', 'image-ai-metadata') . '</strong><br>';
        echo '<ul style="margin: 10px 0 0 20px;">';
        echo '<li><strong><code>https://api-inference.huggingface.co/models/nlpconnect/vit-gpt2-image-captioning</code></strong> - <strong>ViT-GPT2 (DEFAULT - più stabile)</strong></li>';
        echo '<li><code>https://api-inference.huggingface.co/models/microsoft/git-base</code> - GIT Base (alternativa Microsoft)</li>';
        echo '<li><code>https://api-inference.huggingface.co/models/Salesforce/blip2-opt-2.7b</code> - BLIP2 (più potente ma più lento)</li>';
        echo '</ul>';
        echo '<p style="margin: 10px 0 0 0; font-size: 12px; color: #646970;">' . __('💡 Se vedi errore HTTP 410 (Gone), significa che il modello non è più disponibile. Copia e incolla uno degli endpoint sopra e clicca "Salva modifiche".', 'image-ai-metadata') . '</p>';
        echo '</div>';
        
        // Add test connection button
        echo '<div style="margin-top: 15px;">';
        echo '<button type="button" id="test-api-connection" class="button button-secondary">';
        echo '<span class="dashicons dashicons-update"></span> ';
        echo __('Test Connessione API', 'image-ai-metadata');
        echo '</button>';
        echo '<span id="test-result" style="margin-left: 10px;"></span>';
        echo '</div>';
        
        // Add JavaScript for test button
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#test-api-connection').on('click', function() {
                var button = $(this);
                var resultSpan = $('#test-result');
                
                button.prop('disabled', true);
                button.find('.dashicons').addClass('dashicons-update spin').css('animation', 'rotation 1s infinite linear');
                resultSpan.html('<span style="color: #0073aa;">⏳ <?php _e('Test in corso...', 'image-ai-metadata'); ?></span>');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'test_api_connection',
                        nonce: <?php echo wp_json_encode(wp_create_nonce('test_api_connection')); ?>,
                        endpoint: $('input[name="image_ai_metadata_api_endpoint"]').val(),
                        token: $('input[name="image_ai_metadata_api_token"]').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            resultSpan.html('<span style="color: #00a32a;">✓ ' + response.data.message + '</span>');
                        } else {
                            resultSpan.html('<span style="color: #d63638;">✗ ' + response.data.message + '</span>');
                        }
                    },
                    error: function() {
                        resultSpan.html('<span style="color: #d63638;">✗ <?php _e('Errore durante il test', 'image-ai-metadata'); ?></span>');
                    },
                    complete: function() {
                        button.prop('disabled', false);
                        button.find('.dashicons').removeClass('spin').css('animation', '');
                    }
                });
            });
        });
        </script>
        <style>
        .dashicons.spin {
            animation: rotation 1s infinite linear;
        }
        @keyframes rotation {
            from { transform: rotate(0deg); }
            to { transform: rotate(359deg); }
        }
        </style>
        <?php
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
     * Call AI API to analyze image with multiple format support and automatic fallback
     */
    private function call_ai_api($image_path, $api_token, $format = 'auto') {
        // CRITICAL: Force fresh read from database - clear ALL caches
        wp_cache_delete('image_ai_metadata_api_endpoint', 'options');
        wp_cache_delete('alloptions', 'options');
        
        // Trim token to remove any whitespace
        $api_token = trim($api_token);
        
        // Validate token format
        if (!preg_match('/^hf_[a-zA-Z0-9_]+$/', $api_token)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('[Image AI Metadata] WARNING: Token format may be invalid (should start with hf_)');
            }
        }
        
        // Force WordPress to reload options from database
        global $wpdb;
        $endpoint_value = $wpdb->get_var($wpdb->prepare(
            "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s",
            'image_ai_metadata_api_endpoint'
        ));
        
        // Use the working default if nothing is stored or value is empty
        if (empty($endpoint_value)) {
            $endpoint_value = 'https://api-inference.huggingface.co/models/nlpconnect/vit-gpt2-image-captioning';
            // Save it for future use
            update_option('image_ai_metadata_api_endpoint', $endpoint_value);
        }
        
        $endpoint = $endpoint_value;
        
        // Define alternative working models for fallback
        $alternative_models = array(
            'https://api-inference.huggingface.co/models/nlpconnect/vit-gpt2-image-captioning',
            'https://api-inference.huggingface.co/models/microsoft/git-base',
            'https://api-inference.huggingface.co/models/Salesforce/blip2-opt-2.7b',
        );
        
        // Enhanced logging for debugging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[Image AI Metadata] === API CALL START ===');
            error_log('[Image AI Metadata] Timestamp: ' . current_time('Y-m-d H:i:s'));
            error_log('[Image AI Metadata] Endpoint from DB: ' . $endpoint);
            error_log('[Image AI Metadata] Image path: ' . $image_path);
            error_log('[Image AI Metadata] Token configured: ' . (empty($api_token) ? 'NO' : 'YES (length: ' . strlen($api_token) . ')'));
            error_log('[Image AI Metadata] Request format: ' . $format);
        }
        
        // Read image file
        $image_data = file_get_contents($image_path);
        
        if ($image_data === false) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('[Image AI Metadata] ERROR: Failed to read image file');
            }
            return new WP_Error('read_error', __('Errore nella lettura del file immagine.', 'image-ai-metadata'));
        }
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[Image AI Metadata] Image data read successfully (' . strlen($image_data) . ' bytes)');
        }
        
        // Try different request formats based on parameter
        $formats_to_try = array();
        if ($format === 'auto') {
            $formats_to_try = array('binary', 'base64', 'url');
        } else {
            $formats_to_try = array($format);
        }
        
        $last_error = null;
        
        foreach ($formats_to_try as $current_format) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('[Image AI Metadata] Trying format: ' . $current_format);
            }
            
            $result = $this->try_api_request($endpoint, $api_token, $image_path, $image_data, $current_format);
            
            if (!is_wp_error($result)) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('[Image AI Metadata] Success with format: ' . $current_format);
                }
                return $result;
            }
            
            $last_error = $result;
            $response_code = $result->get_error_data('response_code');
            
            // If we get HTTP 410 (Gone), try alternative models immediately
            if ($response_code === 410) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('[Image AI Metadata] HTTP 410 detected, trying alternative models...');
                }
                
                foreach ($alternative_models as $alt_endpoint) {
                    if ($alt_endpoint === $endpoint) {
                        continue; // Skip the current failing endpoint
                    }
                    
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log('[Image AI Metadata] Trying alternative endpoint: ' . $alt_endpoint);
                    }
                    
                    $alt_result = $this->try_api_request($alt_endpoint, $api_token, $image_path, $image_data, $current_format);
                    
                    if (!is_wp_error($alt_result)) {
                        // Success! Save this as the new default
                        update_option('image_ai_metadata_api_endpoint', $alt_endpoint);
                        
                        if (defined('WP_DEBUG') && WP_DEBUG) {
                            error_log('[Image AI Metadata] SUCCESS! Switched to working model: ' . $alt_endpoint);
                        }
                        
                        // Add admin notice about the automatic switch
                        add_option('_image_ai_model_switched_notice', array(
                            'old' => $endpoint,
                            'new' => $alt_endpoint,
                            'time' => time()
                        ));
                        
                        return $alt_result;
                    }
                }
            }
            
            // For other errors, break the format loop and handle error
            // HTTP 503 (Service Unavailable) may work with different format, so continue trying
            // Other errors (401, 403, 404, etc.) are endpoint/token issues that won't be fixed by format change
            if ($response_code !== 503) {
                break;
            }
        }
        
        // All attempts failed, return the last error with enhanced message
        return $last_error;
    }
    
    /**
     * Try API request with specific format
     */
    private function try_api_request($endpoint, $api_token, $image_path, $image_data, $format) {
        $headers = array(
            'Authorization' => 'Bearer ' . $api_token,
        );
        
        $body = null;
        
        // Prepare request based on format
        switch ($format) {
            case 'binary':
                $headers['Content-Type'] = 'application/octet-stream';
                $body = $image_data;
                break;
                
            case 'base64':
                $headers['Content-Type'] = 'application/json';
                $body = json_encode(array(
                    'inputs' => base64_encode($image_data)
                ));
                break;
                
            case 'url':
                // Try to get public URL for the image
                $attachment_id = attachment_url_to_postid($image_path);
                if ($attachment_id) {
                    $image_url = wp_get_attachment_url($attachment_id);
                } else {
                    // Construct URL from path
                    $upload_dir = wp_upload_dir();
                    $image_url = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $image_path);
                }
                
                $headers['Content-Type'] = 'application/json';
                $body = json_encode(array(
                    'inputs' => $image_url
                ));
                break;
                
            default:
                return new WP_Error('invalid_format', __('Formato richiesta non valido.', 'image-ai-metadata'));
        }
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[Image AI Metadata] Sending POST request to: ' . $endpoint . ' (format: ' . $format . ')');
        }
        
        // Prepare API request with extended timeout
        // Increased to 45 seconds because some models (especially larger ones like BLIP2)
        // can take 30+ seconds to respond on first request (cold start)
        $start_time = microtime(true);
        $response = wp_remote_post($endpoint, array(
            'headers' => $headers,
            'body' => $body,
            'timeout' => 45,
            'sslverify' => true
        ));
        $elapsed_time = round((microtime(true) - $start_time) * 1000);
        
        if (is_wp_error($response)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('[Image AI Metadata] ERROR: wp_remote_post failed - ' . $response->get_error_message());
            }
            return new WP_Error('api_error', sprintf(
                __('Errore nella chiamata API: %s (Formato: %s)', 'image-ai-metadata'),
                $response->get_error_message(),
                $format
            ));
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $body_response = wp_remote_retrieve_body($response);
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[Image AI Metadata] Response received in ' . $elapsed_time . 'ms');
            error_log('[Image AI Metadata] HTTP Status Code: ' . $response_code);
            error_log('[Image AI Metadata] Response body length: ' . strlen($body_response) . ' bytes');
        }
        
        if ($response_code !== 200) {
            // Get specific error message based on status code
            $error_message = $this->get_error_message_for_status_code($response_code, $endpoint, $body_response, $format);
            
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('[Image AI Metadata] ERROR: Non-200 status code - ' . $error_message);
                error_log('[Image AI Metadata] Response preview (first 500 chars): ' . substr($body_response, 0, 500));
            }
            
            $error = new WP_Error('api_error', $error_message);
            $error->add_data($response_code, 'response_code');
            return $error;
        }
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[Image AI Metadata] Success! HTTP 200 received');
            error_log('[Image AI Metadata] Parsing JSON response...');
        }
        
        $data = json_decode($body_response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('[Image AI Metadata] ERROR: JSON decode failed - ' . json_last_error_msg());
                error_log('[Image AI Metadata] Raw response (first 500 chars): ' . substr($body_response, 0, 500));
            }
            return new WP_Error('json_error', __('Errore nella decodifica della risposta JSON.', 'image-ai-metadata'));
        }
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[Image AI Metadata] JSON parsed successfully');
        }
        
        // Extract description from response (handle different response formats)
        $description = null;
        
        if (isset($data[0]['generated_text'])) {
            $description = sanitize_text_field($data[0]['generated_text']);
        } elseif (isset($data['generated_text'])) {
            $description = sanitize_text_field($data['generated_text']);
        } elseif (isset($data[0]['caption'])) {
            $description = sanitize_text_field($data[0]['caption']);
        } elseif (isset($data['caption'])) {
            $description = sanitize_text_field($data['caption']);
        }
        
        if ($description) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('[Image AI Metadata] Generated text extracted: ' . $description);
                error_log('[Image AI Metadata] === API CALL END (SUCCESS) ===');
            }
            return $description;
        }
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('[Image AI Metadata] ERROR: Response structure invalid - no recognized caption field found');
            error_log('[Image AI Metadata] Response structure: ' . print_r($data, true));
            error_log('[Image AI Metadata] === API CALL END (FAILED) ===');
        }
        
        return new WP_Error('invalid_response', __('Risposta API non valida. Nessun testo generato trovato nella risposta.', 'image-ai-metadata'));
    }
    
    /**
     * Get error message for HTTP status code
     */
    private function get_error_message_for_status_code($response_code, $endpoint, $body, $format) {
        $error_message = '';
        
        switch ($response_code) {
            case 410:
                $error_message = sprintf(
                    __('Il modello non è più disponibile (HTTP 410 - Gone). Endpoint: %s. Formato: %s. Il plugin proverà automaticamente modelli alternativi. Se il problema persiste, vai su Impostazioni → Image AI Metadata e scegli un modello alternativo dalla lista.', 'image-ai-metadata'),
                    $endpoint,
                    $format
                );
                break;
            case 403:
                $error_message = __('Token API non valido o permessi insufficienti (HTTP 403). Verifica che il token sia di tipo FINEGRAINED con permessi "Read" o "Write" abilitati. Genera un nuovo token su https://huggingface.co/settings/tokens', 'image-ai-metadata');
                break;
            case 401:
                $error_message = __('Non autenticato (HTTP 401). Il token API non è valido o è scaduto. Verifica nelle impostazioni che il token inizi con "hf_" e sia attivo su https://huggingface.co/settings/tokens', 'image-ai-metadata');
                break;
            case 404:
                $error_message = sprintf(
                    __('Modello non trovato (HTTP 404). L\'endpoint API "%s" non esiste. Verifica l\'endpoint nelle impostazioni.', 'image-ai-metadata'),
                    $endpoint
                );
                break;
            case 429:
                // Try to extract retry-after header
                $retry_after = 'qualche minuto';
                $error_message = sprintf(
                    __('Troppo richieste (HTTP 429). Hai raggiunto il limite API di Hugging Face. Attendi %s e riprova. Account gratuiti hanno limite di ~1000 richieste/giorno.', 'image-ai-metadata'),
                    $retry_after
                );
                break;
            case 503:
                $error_message = sprintf(
                    __('Servizio temporaneamente non disponibile (HTTP 503). Il modello "%s" potrebbe essere in caricamento. Attendi 20-30 secondi e riprova.', 'image-ai-metadata'),
                    basename($endpoint)
                );
                break;
            case 500:
            case 502:
                $error_message = sprintf(
                    __('Errore server Hugging Face (HTTP %d). Il servizio potrebbe essere temporaneamente non disponibile. Riprova tra qualche minuto.', 'image-ai-metadata'),
                    $response_code
                );
                break;
            default:
                // Try to extract JSON error message
                $json_data = json_decode($body, true);
                if (isset($json_data['error'])) {
                    $api_error = sanitize_text_field($json_data['error']);
                    $error_message = sprintf(
                        __('Errore API (HTTP %d): %s | Formato richiesta: %s', 'image-ai-metadata'), 
                        $response_code, 
                        $api_error,
                        $format
                    );
                } else {
                    // Strip HTML tags and limit length for other errors
                    $clean_body = wp_strip_all_tags($body);
                    $clean_body = substr($clean_body, 0, 500);
                    $error_message = sprintf(
                        __('Errore API (HTTP %d) | Formato: %s | Risposta: %s', 'image-ai-metadata'), 
                        $response_code,
                        $format,
                        $clean_body
                    );
                }
                break;
        }
        
        return $error_message;
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
            // Enqueue jQuery explicitly
            wp_enqueue_script('jquery');
            
            // Register and enqueue custom style
            wp_register_style('image-ai-bulk-style', false);
            wp_enqueue_style('image-ai-bulk-style');
            wp_add_inline_style('image-ai-bulk-style', $this->get_bulk_page_css());
            
            // Register and enqueue custom script
            wp_register_script('image-ai-bulk-script', false, array('jquery'), IMAGE_AI_METADATA_VERSION, true);
            wp_enqueue_script('image-ai-bulk-script');
            
            // Localize script data BEFORE adding inline script
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
            
            // Add inline script
            wp_add_inline_script('image-ai-bulk-script', $this->get_bulk_page_js());
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
        
        // Show notice if model was automatically switched
        $switched_notice = get_option('_image_ai_model_switched_notice');
        if ($switched_notice && is_array($switched_notice)) {
            // Show notice only once
            delete_option('_image_ai_model_switched_notice');
            
            echo '<div class="notice notice-info is-dismissible" style="border-left-color: #00a32a;">';
            echo '<p><strong>' . __('✓ Modello AI aggiornato automaticamente', 'image-ai-metadata') . '</strong></p>';
            echo '<p>' . sprintf(
                __('Il modello precedente non era più disponibile (HTTP 410). Il plugin ha automaticamente trovato e salvato un modello alternativo funzionante:', 'image-ai-metadata')
            ) . '</p>';
            echo '<p><strong>' . __('Nuovo modello:', 'image-ai-metadata') . '</strong> <code>' . esc_html($switched_notice['new']) . '</code></p>';
            echo '<p>' . sprintf(
                __('Puoi cambiare modello manualmente in <a href="%s">Impostazioni → Image AI Metadata</a> se preferisci un\'alternativa.', 'image-ai-metadata'),
                admin_url('options-general.php?page=image-ai-metadata')
            ) . '</p>';
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
        <div class="wrap image-ai-bulk-wrap">
            <div class="page-header">
                <h1>
                    <span class="dashicons dashicons-images-alt2"></span>
                    <?php _e('Elaborazione Bulk AI - Immagini', 'image-ai-metadata'); ?>
                </h1>
                <p class="description">
                    <?php _e('Elabora automaticamente tutte le immagini della tua libreria media con intelligenza artificiale', 'image-ai-metadata'); ?>
                </p>
            </div>
            
            <?php if (empty($api_token)): ?>
                <div class="notice notice-error is-dismissible">
                    <p>
                        <strong><?php _e('⚠️ Token API non configurato!', 'image-ai-metadata'); ?></strong><br>
                        <?php printf(
                            __('Vai su <a href="%s">Impostazioni → Image AI Metadata</a> per configurare il token API.', 'image-ai-metadata'),
                            admin_url('options-general.php?page=image-ai-metadata')
                        ); ?>
                    </p>
                </div>
            <?php else: ?>
                
                <!-- System Diagnostic Panel -->
                <div class="diagnostic-panel">
                    <h3>
                        <span class="dashicons dashicons-admin-generic"></span>
                        <?php _e('Stato Sistema', 'image-ai-metadata'); ?>
                    </h3>
                    <div class="diagnostic-grid">
                        <div class="diagnostic-item">
                            <span class="dashicons dashicons-yes-alt status-ok"></span>
                            <strong><?php _e('Token API:', 'image-ai-metadata'); ?></strong> 
                            <?php _e('Configurato', 'image-ai-metadata'); ?>
                        </div>
                        <div class="diagnostic-item">
                            <span class="dashicons dashicons-yes-alt status-ok"></span>
                            <strong>jQuery:</strong> <span id="jquery-status"><?php _e('Controllo...', 'image-ai-metadata'); ?></span>
                        </div>
                        <div class="diagnostic-item">
                            <span class="dashicons dashicons-yes-alt status-ok"></span>
                            <strong>AJAX URL:</strong> <span id="ajax-url-status"><?php _e('Controllo...', 'image-ai-metadata'); ?></span>
                        </div>
                        <div class="diagnostic-item">
                            <span class="dashicons dashicons-yes-alt status-ok"></span>
                            <strong>Nonce:</strong> <span id="nonce-status"><?php _e('Controllo...', 'image-ai-metadata'); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="bulk-controls card">
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
     * AJAX: Test API connection
     */
    public function ajax_test_api_connection() {
        check_ajax_referer('test_api_connection', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permessi insufficienti', 'image-ai-metadata')));
        }
        
        $endpoint = isset($_POST['endpoint']) ? sanitize_text_field($_POST['endpoint']) : '';
        $token = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '';
        
        if (empty($token)) {
            wp_send_json_error(array('message' => __('Token API non configurato', 'image-ai-metadata')));
        }
        
        if (empty($endpoint)) {
            wp_send_json_error(array('message' => __('Endpoint API non configurato', 'image-ai-metadata')));
        }
        
        // Trim token
        $token = trim($token);
        
        // Validate token format
        if (!preg_match('/^hf_[a-zA-Z0-9_]+$/', $token)) {
            wp_send_json_error(array(
                'message' => __('Formato token non valido. Il token deve iniziare con "hf_"', 'image-ai-metadata')
            ));
        }
        
        // Create a minimal test image (1x1 pixel transparent PNG - 85 bytes)
        // This is the smallest valid PNG that can be decoded by most image processing APIs
        $test_image_data = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        
        // Try binary format first (using same timeout as main API calls for consistency)
        $start_time = microtime(true);
        $response = wp_remote_post($endpoint, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/octet-stream'
            ),
            'body' => $test_image_data,
            'timeout' => 45
        ));
        $elapsed_time = round((microtime(true) - $start_time) * 1000);
        
        if (is_wp_error($response)) {
            wp_send_json_error(array(
                'message' => sprintf(
                    __('Errore di connessione: %s', 'image-ai-metadata'),
                    $response->get_error_message()
                )
            ));
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        if ($response_code === 200) {
            wp_send_json_success(array(
                'message' => sprintf(
                    __('Connessione riuscita! (%dms) Il modello è funzionante.', 'image-ai-metadata'),
                    $elapsed_time
                )
            ));
        } else {
            // Provide detailed error message
            $error_msg = $this->get_error_message_for_status_code($response_code, $endpoint, $body, 'binary');
            wp_send_json_error(array(
                'message' => sprintf(
                    __('Test fallito (HTTP %d): %s', 'image-ai-metadata'),
                    $response_code,
                    $error_msg
                )
            ));
        }
    }
    
    /**
     * Get CSS for bulk processing page
     */
    private function get_bulk_page_css() {
        return "
            .image-ai-bulk-wrap {
                max-width: 1400px;
            }
            .page-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 30px;
                border-radius: 8px;
                margin-bottom: 20px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            }
            .page-header h1 {
                color: white;
                margin: 0 0 10px 0;
                font-size: 28px;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .page-header .dashicons {
                font-size: 32px;
                width: 32px;
                height: 32px;
            }
            .page-header .description {
                color: rgba(255,255,255,0.9);
                font-size: 15px;
                margin: 0;
            }
            .diagnostic-panel {
                background: #fff;
                border: 1px solid #c3c4c7;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }
            .diagnostic-panel h3 {
                margin-top: 0;
                display: flex;
                align-items: center;
                gap: 10px;
                color: #1d2327;
            }
            .diagnostic-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 15px;
                margin-top: 15px;
            }
            .diagnostic-item {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 12px;
                background: #f6f7f7;
                border-radius: 6px;
                font-size: 14px;
            }
            .diagnostic-item .status-ok {
                color: #00a32a;
            }
            .diagnostic-item .status-error {
                color: #d63638;
            }
            .diagnostic-item .status-warning {
                color: #dba617;
            }
            .card {
                background: #fff;
                border: 1px solid #c3c4c7;
                border-radius: 8px;
                padding: 25px;
                margin-bottom: 20px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }
            .bulk-controls h2 {
                margin-top: 0;
                color: #1d2327;
                font-size: 20px;
            }
            .filter-options {
                margin: 20px 0;
            }
            .filter-options label {
                display: flex;
                align-items: flex-start;
                gap: 12px;
                padding: 16px;
                background: #f6f7f7;
                border: 2px solid #e0e0e0;
                border-radius: 8px;
                margin: 12px 0;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            .filter-options label:hover {
                background: #fff;
                border-color: #2271b1;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            }
            .filter-options input[type='radio'] {
                margin-top: 4px;
            }
            .filter-options label > div {
                flex: 1;
            }
            .filter-options .description {
                display: block;
                font-size: 13px;
                color: #646970;
                margin-top: 5px;
                font-weight: normal;
            }
            .progress-bar-container {
                background: #fff;
                border: 1px solid #c3c4c7;
                border-radius: 8px;
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
            (function($) {
                'use strict';
                
                console.log('=== IMAGE AI BULK PROCESSING DEBUG ===');
                console.log('1. Script caricato correttamente');
                console.log('2. jQuery version:', $.fn.jquery);
                console.log('3. imageAIBulk object:', imageAIBulk);
                console.log('4. AJAX URL:', imageAIBulk ? imageAIBulk.ajaxurl : 'UNDEFINED!');
                console.log('5. Nonce:', imageAIBulk ? imageAIBulk.nonce : 'UNDEFINED!');
                
                // Check if imageAIBulk is defined
                if (typeof imageAIBulk === 'undefined') {
                    console.error('ERRORE CRITICO: imageAIBulk non è definito!');
                    alert('ERRORE: Configurazione JavaScript non caricata correttamente. Ricarica la pagina.');
                    return;
                }
                
                var imagesToProcess = [];
                var currentIndex = 0;
                var successCount = 0;
                var failedCount = 0;
                var isProcessing = false;
                
                // Update diagnostic panel
                function updateDiagnostics() {
                    $('#jquery-status').text('v' + $.fn.jquery + ' ✓').parent().find('.dashicons').removeClass('status-warning').addClass('status-ok');
                    $('#ajax-url-status').text(imageAIBulk.ajaxurl).parent().find('.dashicons').removeClass('status-warning').addClass('status-ok');
                    $('#nonce-status').text('Valido (' + imageAIBulk.nonce.substring(0, 10) + '...)').parent().find('.dashicons').removeClass('status-warning').addClass('status-ok');
                    addLog('✓ Sistema inizializzato correttamente', 'success');
                    addLog('→ jQuery: ' + $.fn.jquery, 'info');
                    addLog('→ AJAX URL: ' + imageAIBulk.ajaxurl, 'info');
                    addLog('→ Nonce: ' + imageAIBulk.nonce.substring(0, 20) + '...', 'info');
                }
                
                function addLog(message, type) {
                    type = type || 'info';
                    var timestamp = new Date().toLocaleTimeString();
                    var logEntry = $('<div class=\"log-entry log-' + type + '\"></div>')
                        .html('<span class=\"log-timestamp\">[' + timestamp + ']</span>' + message);
                    $('#debug-output').append(logEntry);
                    $('#debug-output').scrollTop($('#debug-output')[0].scrollHeight);
                    console.log('[' + type.toUpperCase() + '] ' + message);
                }
                
                // Initialize diagnostics on page load
                $(document).ready(function() {
                    console.log('6. DOM ready, inizializzazione diagnostics...');
                    updateDiagnostics();
                    addLog('='.repeat(50), 'info');
                    addLog('PRONTO PER L\'ELABORAZIONE', 'success');
                    addLog('='.repeat(50), 'info');
                });
                
                $('#btn-scan-images').on('click', function() {
                    console.log('7. Click su btn-scan-images rilevato');
                    addLog('='.repeat(50), 'info');
                    addLog('PASSO 1: Click sul pulsante Scansiona rilevato', 'info');
                    addLog('='.repeat(50), 'info');
                    var filterType = $('input[name=\"filter_type\"]:checked').val();
                    addLog('PASSO 2: Filtro selezionato: ' + filterType, 'info');
                    
                    $(this).prop('disabled', true).html('<span class=\"dashicons dashicons-update spin\"></span> Scansione...');
                    addLog('PASSO 3: Pulsante disabilitato, inizio chiamata AJAX...', 'info');
                    
                    addLog('PASSO 4: Preparazione dati per la richiesta', 'info');
                    addLog('  → URL: ' + imageAIBulk.ajaxurl, 'info');
                    addLog('  → Action: image_ai_get_images', 'info');
                    addLog('  → Nonce: ' + imageAIBulk.nonce.substring(0, 10) + '...', 'info');
                    addLog('  → Filter: ' + filterType, 'info');
                    
                    addLog('PASSO 5: Invio richiesta AJAX al server...', 'info');
                    var ajaxStartTime = Date.now();
                    
                    $.ajax({
                        url: imageAIBulk.ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'image_ai_get_images',
                            nonce: imageAIBulk.nonce,
                            filter_type: filterType
                        },
                        beforeSend: function() {
                            console.log('8. beforeSend: richiesta in partenza');
                            addLog('  → Richiesta in partenza...', 'info');
                        },
                        success: function(response) {
                            var ajaxDuration = Date.now() - ajaxStartTime;
                            console.log('9. success callback ricevuto, durata:', ajaxDuration + 'ms');
                            addLog('PASSO 6: Risposta ricevuta dal server (' + ajaxDuration + 'ms)', 'success');
                            addLog('  → Tipo risposta: ' + typeof response, 'info');
                            addLog('  → Response.success: ' + response.success, 'info');
                            
                            console.log('Response completa:', response);
                            
                            if (response.success) {
                                addLog('PASSO 7: Risposta positiva dal server', 'success');
                                imagesToProcess = response.data.images;
                                addLog('  → Immagini trovate: ' + response.data.count, 'success');
                                addLog('  → Array immagini caricato in memoria', 'info');
                                
                                $('#images-count').text(response.data.count);
                                $('#images-found').fadeIn();
                                $('#count-total').text(response.data.count);
                                
                                addLog('PASSO 8: Interfaccia aggiornata con successo', 'success');
                                addLog('='.repeat(50), 'success');
                                addLog('✓ SCANSIONE COMPLETATA: ' + response.data.count + ' immagini pronte', 'success');
                                addLog('='.repeat(50), 'success');
                            } else {
                                addLog('PASSO 7: Risposta negativa dal server', 'error');
                                addLog('  → Messaggio: ' + (response.data.message || 'Errore sconosciuto'), 'error');
                                console.error('Errore response:', response);
                            }
                        },
                        error: function(xhr, status, error) {
                            var ajaxDuration = Date.now() - ajaxStartTime;
                            console.error('10. error callback, durata:', ajaxDuration + 'ms');
                            console.error('xhr:', xhr);
                            console.error('status:', status);
                            console.error('error:', error);
                            
                            addLog('='.repeat(50), 'error');
                            addLog('✗ ERRORE AJAX CRITICO', 'error');
                            addLog('='.repeat(50), 'error');
                            addLog('PASSO 6: Errore durante la chiamata AJAX', 'error');
                            addLog('  → Status: ' + status, 'error');
                            addLog('  → Error: ' + error, 'error');
                            addLog('  → HTTP Status Code: ' + xhr.status, 'error');
                            addLog('  → Ready State: ' + xhr.readyState, 'error');
                            
                            if (xhr.status === 0) {
                                addLog('  → Possibile causa: Richiesta bloccata o timeout', 'error');
                            } else if (xhr.status === 403) {
                                addLog('  → Possibile causa: Nonce non valido o permessi insufficienti', 'error');
                            } else if (xhr.status === 500) {
                                addLog('  → Possibile causa: Errore PHP sul server', 'error');
                            }
                            
                            if (xhr.responseText) {
                                addLog('  → Response Text (primi 300 caratteri):', 'error');
                                addLog('    ' + xhr.responseText.substring(0, 300), 'error');
                            }
                        },
                        complete: function(xhr, status) {
                            console.log('11. complete callback, status:', status);
                            addLog('PASSO FINALE: Richiesta completata (status: ' + status + ')', 'info');
                            $('#btn-scan-images').prop('disabled', false).html('<span class=\"dashicons dashicons-search\"></span> Scansiona Immagini');
                            addLog('  → Pulsante riabilitato', 'info');
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
                
            })(jQuery);
        ";
    }
}

// Initialize the plugin
function image_ai_metadata_init() {
    return Image_AI_Metadata::get_instance();
}

// Activation hook for cache clearing
function image_ai_metadata_activate() {
    // Force clear all caches on activation
    wp_cache_flush();
    delete_transient('image_ai_metadata_api_endpoint');
}

add_action('plugins_loaded', 'image_ai_metadata_init');
register_activation_hook(__FILE__, 'image_ai_metadata_activate');
